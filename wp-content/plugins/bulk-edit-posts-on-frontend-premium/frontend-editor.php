<?php
/*
  Plugin Name: WP Sheet Editor - Frontend Sheets (Premium)
  Description: Display spreadsheet editor on the frontend or custom admin pages, create custom spreadsheets as dashboards for apps.
  Version: 2.2.8
  Author:      WP Sheet Editor
  Author URI:  https://wpsheeteditor.com/?utm_source=wp-admin&utm_medium=plugins-list&utm_campaign=frontend
  Plugin URI: https://wpsheeteditor.com/extensions/frontend-spreadsheet-editor/?utm_source=wp-admin&utm_medium=plugins-list&utm_campaign=frontend
  License:     GPL2
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
  WC requires at least: 3.0
WC tested up to: 4.0
Text Domain: vg_sheet_editor_frontend
  Domain Path: /lang
  @fs_premium_only /modules/user-path/send-user-path.php, /modules/acf/, /modules/advanced-filters/, /modules/formulas/, /modules/custom-columns/, /modules/custom-post-types/, /modules/woocommerce/, /modules/universal-sheet/, /modules/yoast-seo/, /modules/wpml/, /modules/posts-templates/,  /plugins/, /inc/freemius-init-addon.php
 */

if (!defined('ABSPATH')) {
	exit;
}
if (function_exists('bepof_fs')) {
	bepof_fs()->set_basename(true, __FILE__);
}
if (!defined('VGSE_FRONTEND_EDITOR_DIR')) {
	define('VGSE_FRONTEND_EDITOR_DIR', __DIR__);
}
if (!defined('VGSE_EDITORS_POST_TYPE')) {
	define('VGSE_EDITORS_POST_TYPE', 'vgse_editors');
}

require 'vendor/vg-plugin-sdk/index.php';
require 'vendor/freemius/start.php';
require 'inc/freemius-init.php';
require 'inc/options-page.php';

if (bepof_fs()->can_use_premium_code__premium_only()) {
	if (!defined('VGSE_FRONTEND_IS_PREMIUM')) {
		define('VGSE_FRONTEND_IS_PREMIUM', true);
	}
}

if (!class_exists('WP_Sheet_Editor_Frontend_Editor')) {

	/**
	 * Filter rows in the spreadsheet editor.
	 */
	class WP_Sheet_Editor_Frontend_Editor {

		static private $instance = false;
		var $plugin_url = null;
		var $plugin_dir = null;
		var $current_editor_columns = null;
		var $shortcode_key = 'vg_sheet_editor';
		var $textname = 'vg_sheet_editor_frontend';
		var $buy_link = null;
		var $version = '2.2.8';
		var $settings = null;
		var $args = null;
		var $vg_plugin_sdk = null;
		var $sheets_bootstrap = null;
		var $main_admin_page_slug = null;
		var $frontend_template_key = 'vg-sheet-editor-frontend.php';

		private function __construct() {
			
		}

		function init_plugin_sdk() {

			$this->vg_plugin_sdk = new VG_Freemium_Plugin_SDK($this->args);
		}

		function auto_setup() {
			$flag_key = 'vg_sheet_editor_frontend_auto_setup';
			$already_setup = get_option($flag_key, 'no');

			if ($already_setup === 'yes') {
				return;
			}

			update_option($flag_key, 'yes');

			$default_post_type = 'post';

			wp_insert_post(array(
				'post_type' => VGSE_EDITORS_POST_TYPE,
				'post_title' => __('Edit posts', $this->textname),
				'post_status' => 'publish',
				'meta_input' => array(
					'vgse_post_type' => $default_post_type,
				),
			));
		}

		function _get_first_post() {
			$editors = new WP_Query(array(
				'post_type' => VGSE_EDITORS_POST_TYPE,
				'posts_per_page' => 1,
				'fields' => 'ids'
			));

			return ($editors->have_posts()) ? current($editors->posts) : false;
		}

		function get_upgrade_url() {
			$url = ( function_exists('bepof_fs') ) ? bepof_fs()->pricing_url(WP_FS__PERIOD_ANNUALLY, true, array(
						'licenses' => is_multisite() ? 'unlimited' : 1,
						'billing_cycle' => is_multisite() ? 'monthly' : WP_FS__PERIOD_ANNUALLY,
					)) : 'https://wpsheeteditor.com/buy-frontend-editor-wporg';
			return $url;
		}

		function notify_wrong_core_version() {
			$plugin_data = get_plugin_data(__FILE__, false, false);
			?>
			<div class="notice notice-error">
				<p><?php _e('Please update the WP Sheet Editor plugin and all its extensions to the latest version. The features of the plugin "' . $plugin_data['Name'] . '" will be disabled to prevent errors and they will be enabled automatically after you install the updates.', vgse_frontend_editor()->textname); ?></p>
			</div>
			<?php
		}

		function init() {

			require __DIR__ . '/modules/init.php';
			$this->modules_controller = new WP_Sheet_Editor_CORE_Modules_Init(__DIR__, bepof_fs());
			// We initialize the modules directly because the modules_init class uses the plugins_loaded hook
			// but this plugin initializes too late with the after_setup_theme
			$this->modules_controller->init();

			$this->plugin_url = plugins_url('/', __FILE__);
			$this->plugin_dir = __DIR__;
			$this->buy_link = $this->get_upgrade_url();

			$this->args = array(
				'main_plugin_file' => __FILE__,
				'show_welcome_page' => true,
				'welcome_page_file' => $this->plugin_dir . '/views/welcome-page-content.php',
				'logo' => plugins_url('/assets/imgs/logo-248x102.png', __FILE__),
				'buy_link' => $this->buy_link,
				'buy_link_text' => __('Try premium plugin for FREE', $this->textname),
				'plugin_name' => 'Frontend Sheet',
				'plugin_prefix' => 'vgsefe_',
				'show_whatsnew_page' => true,
				'whatsnew_pages_directory' => $this->plugin_dir . '/views/whats-new/',
				'plugin_version' => $this->version,
				'plugin_options' => $this->settings,
			);
			$this->main_admin_page_slug = $this->args['plugin_prefix'] . 'welcome_page';

			$this->init_plugin_sdk();

			$this->register_post_type();

			// Allow core editor on frontend
			add_filter('vg_sheet_editor/allowed_on_frontend', '__return_true');

			// After core has initialized
			add_action('vg_sheet_editor/initialized', array($this, 'after_core_init'));
			add_action('vg_sheet_editor/after_extensions_registered', array($this, 'after_full_core_init'));

			// Dont register the quick setup and other subpages, we'll
			// register them manually under the frontend sheets parent menu
			add_filter('vg_sheet_editor/register_admin_pages', '__return_false');

			// Fix. When we load the metabox settings, it used "post" as current provider showing post 
			// columns instead of the custom post type columns. We use this to set the provider 
			// from the post meta as current provider.
			add_filter('vg_sheet_editor/bootstrap/get_current_provider', array($this, 'set_provider_from_post_meta'));
			load_plugin_textdomain($this->textname, false, basename(dirname(__FILE__)) . '/lang/');

			add_action('admin_init', array($this, 'set_current_editor_settings_for_ajax_calls'));
		}

		function set_current_editor_settings_for_ajax_calls() {
			if (!wp_doing_ajax() || empty($_REQUEST['wpse_source_suffix'])) {
				return;
			}
			$editor_id = str_replace('_frontend_sheet', '', $_REQUEST['wpse_source_suffix']);
			if (!is_numeric($editor_id)) {
				return;
			}

			$this->set_current_editor_settings((int) $editor_id, 'shortcode');
		}

		function modify_js_data($args) {
			if (empty($this->current_editor_settings)) {
				return $args;
			}

			$args['wpse_source_suffix'] = '_frontend_sheet' . $this->current_editor_settings['editor_id'];
			return $args;
		}

		function allow_builtin_post_types($post_types) {
			// The frontend plugin allows to edit the same post types as the posts plugin
			// so we will remove those post types from the list of post types with own sheet
			// If we dont remove them here, the algorithm will exclude them and ask for an upgrade to edit those post types
			if (isset(VGSE()->bundles['custom_post_types']['post_types'])) {
				$post_types = array_diff($post_types, VGSE()->bundles['custom_post_types']['post_types']);
			}

			return $post_types;
		}

		function set_provider_from_post_meta($current_provider) {
			$post_id = null;
			$post = get_queried_object();
			if (is_admin() && !empty($_GET['post']) && get_post_type($_GET['post']) === VGSE_EDITORS_POST_TYPE) {
				$post_id = (int) $_GET['post'];
			} elseif (!is_admin() && $post && !empty($post->post_content) && strpos($post->post_content, '[vg_sheet_editor editor_id=') !== false) {
				$post_id = (int) preg_replace('/.*vg_sheet_editor editor_id="?(\d+)"?.*/s', '$1', $post->post_content);
			}
			if ($post_id) {
				$raw_current_provider = get_post_meta($post_id, 'vgse_post_type', true);
				if ($raw_current_provider) {
					$current_provider = $raw_current_provider;
				}
			}
			return $current_provider;
		}

		function remove_conflicting_css() {
			global $wp_styles, $wp_scripts;
			$post = get_queried_object();
			if (!empty($post) && isset($post->post_type) && $post->post_type === 'page' && $this->frontend_template_key == basename(get_post_meta($post->ID, '_wp_page_template', true))) {
				foreach ($wp_styles->registered as $index => $style) {
					if (!empty($style->src) && strpos($style->src, 'themes/') !== false) {
						unset($wp_styles->registered[$index]);
					}
				}
				foreach ($wp_scripts->registered as $index => $script) {
					if (!empty($script->src) && strpos($script->src, 'themes/') !== false) {
						unset($wp_scripts->registered[$index]);
					}
				}
			}
		}

		function render_page_template($template) {
			$post = get_post();
			$page_template = get_post_meta($post->ID, '_wp_page_template', true);
			if ($this->frontend_template_key == basename($page_template)) {
				$template = __DIR__ . '/views/frontend/page-template.php';
				wp_enqueue_style('vg-sheet-editor-frontend-styles', plugins_url('/assets/frontend/css/style.css', __FILE__));
			}


			return $template;
		}

		function register_page_template($templates) {
			$templates[$this->frontend_template_key] = 'Frontend Spreadsheet';
			return $templates;
		}

		function after_full_core_init() {

			if (!empty(VGSE()->options['hide_admin_bar_frontend']) && !is_admin() && !current_user_can('manage_options')) {
				add_filter('show_admin_bar', '__return_false');
			}
			add_filter('vg_sheet_editor/custom_post_types/get_post_types_with_own_sheet', array($this, 'allow_builtin_post_types'));

			// Initialize core sheets if the CORE plugin is not installed
			// If the CORE plugin is installed, the sheets are already initialized at this point
			if (!class_exists('WP_Sheet_Editor_Dist')) {
				$this->sheets_bootstrap = new WP_Sheet_Editor_Bootstrap(array(
					'enabled_post_types' => array_keys($this->get_allowed_post_types()),
					'register_admin_menus' => false,
				));
			}
		}

		function register_menu_page() {
			add_menu_page(
					$this->args['plugin_name'], $this->args['plugin_name'], 'manage_options', $this->main_admin_page_slug, array($this->vg_plugin_sdk, 'render_welcome_page'), plugins_url('/assets/imgs/icon-20x20.png', __FILE__)
			);
			if (bepof_fs()->can_use_premium_code__premium_only()) {
				add_submenu_page($this->main_admin_page_slug, __('All spreadsheets', vgse_frontend_editor()->textname), __('All spreadsheets', vgse_frontend_editor()->textname), 'manage_options', 'edit.php?post_type=vgse_editors');

				if (function_exists('vgse_custom_columns_init')) {
					add_submenu_page($this->main_admin_page_slug, __('Custom columns', vgse_frontend_editor()->textname), __('Custom columns', vgse_frontend_editor()->textname), 'manage_options', vgse_custom_columns_init()->key, array(vgse_custom_columns_init(), 'render_settings_page'));
				}
			}
		}

		function after_core_init() {


			if (version_compare(VGSE()->version, '2.5.2') < 0) {
				add_action('admin_notices', array($this, 'notify_wrong_core_version'));
				return;
			}


			add_action('admin_menu', array($this, 'register_menu_page'));


			add_filter('theme_page_templates', array($this, 'register_page_template'));
			add_filter('page_template', array($this, 'render_page_template'));
			add_action('wp_print_styles', array($this, 'remove_conflicting_css'), 99999999);

			// Register shortcode
			add_shortcode($this->shortcode_key, array($this, 'get_frontend_editor_html'));

			// Register metaboxes
			add_action('add_meta_boxes', array($this, 'register_meta_boxes'));
			add_action('save_post', array($this, 'save_meta_box'));

			// Enqueue metabox css and js
			add_action('admin_enqueue_scripts', array($this, 'enqueue_metabox_assets'), 10, 1);

			// Override core buy link with this plugin´s
			VGSE()->buy_link = $this->buy_link;

			// Disable columns visibility filter, we will set up our own filter
			if (class_exists('WP_Sheet_Editor_Columns_Visibility')) {
				remove_filter('vg_sheet_editor/columns/all_items', array('WP_Sheet_Editor_Columns_Visibility', 'filter_columns_for_visibility'), 9999);
				add_filter('vg_sheet_editor/columns/all_items', array($this, 'filter_columns_for_visibility'), 9999);
			}
		}

		function get_allowed_post_types() {
			$allowed_post_types = array(
				'post' => __('Posts', $this->textname),
				'page' => __('Pages', $this->textname),
			);
			if (bepof_fs()->can_use_premium_code__premium_only()) {
				$allowed_post_types = VGSE()->helpers->get_allowed_post_types();
			}

			return $allowed_post_types;
		}

		/**
		 * Enqueue metabox assets
		 * @global obj $post
		 * @param str $hook
		 */
		function enqueue_metabox_assets($hook) {

			global $post;

			if (($hook == 'post-new.php' || $hook == 'post.php') && VGSE_EDITORS_POST_TYPE === $post->post_type) {

				VGSE()->_register_styles();
				VGSE()->_register_scripts('post');

				if (class_exists('WP_Sheet_Editor_Columns_Visibility')) {
					$columns_visibility_module = WP_Sheet_Editor_Columns_Visibility::get_instance();
					$columns_visibility_module->enqueue_assets();
				}
			}
		}

		/**
		 * Register meta box(es).
		 */
		function register_meta_boxes() {
			add_meta_box('vgse-columns-visibility-metabox', __('Quick settings', $this->textname), array($this, 'render_settings_metabox'), VGSE_EDITORS_POST_TYPE);
		}

		/**
		 * Meta box display callback.
		 *
		 * @param WP_Post $post Current post object.
		 */
		function render_settings_metabox($post) {
			$allowed_post_types = $this->get_allowed_post_types();

			$post_type = get_post_meta($post->ID, 'vgse_post_type', true);

			if (empty($post_type) || !is_string($post_type) || !isset($allowed_post_types[$post_type])) {
				$post_type = '';
			}

			$sanitized_post_type = sanitize_text_field($post_type);
			$all_post_types = VGSE()->helpers->get_all_post_types();

			// Prepare post type selectors
			$post_type_selectors = array();
			if (!empty($all_post_types)) {
				foreach ($allowed_post_types as $post_type_key => $post_type_label) {
					$post_type_selectors[] = array(
						'key' => $post_type_key,
						'label' => $post_type_label,
						'allowed' => true,
					);
				}


				foreach ($all_post_types as $post_type_obj) {
					if (isset($allowed_post_types[$post_type_obj->name])) {
						continue;
					}

					$post_type_field = array(
						'key' => $post_type_obj->label,
						'label' => $post_type_obj->label,
						'allowed' => false,
					);
					if (bepof_fs()->can_use_premium_code__premium_only()) {
						$post_type_field['allowed'] = true;
					}

					$post_type_selectors[] = $post_type_field;
				}
			}
			if ($post_type) {
				$editor = VGSE()->helpers->get_provider_editor($post_type);
				if (empty($editor)) {
					$post_type = '';
				}
			}

			if ($post_type) {
				$frontend_page_id = $this->get_frontend_page_id(array(
					'spreadsheet_id' => $post->ID,
					'search_statuses' => array('publish', 'draft', 'pending')
				));
				$frontend_url = get_permalink($frontend_page_id);
				$all_toolbars = $editor->args['toolbars']->get_items();
				if (empty($all_toolbars) || !is_array($all_toolbars)) {
					$all_toolbars = array();
				}

				if (isset($all_toolbars[$post_type])) {
					$post_type_toolbars = $all_toolbars[$post_type];
				} else {
					$post_type_toolbars = array();
				}


				foreach ($post_type_toolbars as $toolbar_key => $toolbar_items) {
					if (empty($toolbar_items) || !is_string($toolbar_key) || !is_array($toolbar_items)) {
						unset($post_type_toolbars[$toolbar_key]);
					}

					$filtered_toolbar_items = wp_list_filter($toolbar_items, array(
						'allow_to_hide' => true,
						'allow_in_frontend' => true
					));
					$post_type_toolbars[$toolbar_key] = $filtered_toolbar_items;

					foreach ($post_type_toolbars[$toolbar_key] as $toolbar_item_key => $toolbar_item) {

						if (empty($toolbar_item) || !is_array($toolbar_item) || !isset($toolbar_item['key']) || empty($toolbar_item['label'])) {
							unset($post_type_toolbars[$toolbar_key][$toolbar_item_key]);
						}
					}
				}

				$current_toolbars = maybe_unserialize(get_post_meta($post->ID, 'vgse_toolbars', true));

				if (empty($current_toolbars) || !is_array($current_toolbars)) {
					$current_toolbars = array();
				}
				// Render the editor settings because some JS requires the texts and other info
				$editor_settings = $editor->get_editor_settings($post_type);
				?>
				<script>
					var vgse_editor_settings = <?php echo json_encode($editor_settings); ?>
				</script>
				<?php
			}
			$upgrade_label_suffix = sprintf(__(' <small>(Premium. <a href="%s" target="_blank">Try for Free for 7 Days</a>)</small>', $this->textname), VGSE()->get_buy_link('frontend-post-type-selector', $this->buy_link));

			// Columns visibility section
			if (class_exists('WP_Sheet_Editor_Columns_Visibility')) {
				$columns_visibility_module = WP_Sheet_Editor_Columns_Visibility::get_instance();
				$current_columns = maybe_unserialize(get_post_meta($post->ID, 'vgse_columns', true));
				if (!$current_columns) {
					$current_columns = array();
				}
				$column_visibility_options = null;
				if (!empty($current_columns)) {
					$column_visibility_options = array(
						$post_type => $current_columns
					);
				}
			}

			$this->set_current_editor_settings($post->ID, 'metabox');
			include __DIR__ . '/views/backend/metabox.php';
		}

		function set_current_editor_settings($editor_id, $context) {

			$post_type = get_post_meta($editor_id, 'vgse_post_type', true);
			$columns = maybe_unserialize(get_post_meta($editor_id, 'vgse_columns', true));
			$toolbars = maybe_unserialize(get_post_meta($editor_id, 'vgse_toolbars', true));

			$raw_toolbars = serialize($toolbars);
			if (strpos($raw_toolbars, "run_formula") === false) {
				add_filter('vg_sheet_editor/formulas/is_bulk_selector_column_allowed', '__return_false');
			}

			// Cache editor settings for later
			$this->current_editor_settings = array(
				'toolbars' => $toolbars,
				'columns' => $columns,
				'post_type' => $post_type,
				'editor_id' => $editor_id,
				'context' => $context
			);
		}

		function get_frontend_page_id($args = array()) {
			extract(wp_parse_args($args, array(
				'spreadsheet_id' => null,
				'auto_create' => false,
				'search_statuses' => array('publish')
			)));
			global $wpdb;

			$shortcode = '[vg_sheet_editor editor_id="' . $spreadsheet_id . '"]';
			$post_type = get_post_meta($spreadsheet_id, 'vgse_post_type', true);
			$page_id = (int) $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_type = 'page' AND post_status IN ('" . implode("','", $search_statuses) . "') AND post_content LIKE '%" . esc_sql($shortcode) . "%' ");
			if (!$page_id && $auto_create) {
				$page_id = wp_insert_post(array(
					'post_type' => 'page',
					'post_status' => 'publish',
					'post_title' => 'Edit ' . $post_type,
					'post_content' => $shortcode,
				));
				update_post_meta($page_id, '_wp_page_template', $this->frontend_template_key);
			}
			return $page_id;
		}

		/**
		 * Save meta box content.
		 *
		 * @param int $post_id Post ID
		 */
		function save_meta_box($post_id) {

			if (!isset($_POST['bep-nonce']) || !wp_verify_nonce($_POST['bep-nonce'], 'bep-nonce')) {
				return $post_id;
			}
			// cleanup data
			$data = VGSE()->helpers->clean_data($_POST);

			// Verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
			// to do anything
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return $post_id;
			}

			$post = get_post($post_id);

			if ($post->post_type !== VGSE_EDITORS_POST_TYPE) {
				return $post_id;
			}



			$allowed_post_types = $this->get_allowed_post_types();


			if (empty($data['vgse_post_type']) || !is_string($data['vgse_post_type']) || !isset($allowed_post_types[$data['vgse_post_type']])) {
				return;
			}
			update_post_meta($post_id, 'vgse_post_type', sanitize_text_field($data['vgse_post_type']));

			if (isset($data['vgse_columns_enabled_all_keys']) && class_exists('WP_Sheet_Editor_Columns_Visibility')) {
				// It's possible that zero columns are disabled, so we need to define these 
				// variables because they wont' come from the form
				if (empty($data['disallowed_columns_names'])) {
					$data['disallowed_columns_names'] = array();
				}
				if (empty($data['disallowed_columns'])) {
					$data['disallowed_columns'] = array();
				}
				update_post_meta($post_id, 'vgse_columns', array(
					'enabled' => array_combine($data['columns'], $data['columns_names']),
					'disabled' => array_combine($data['disallowed_columns'], $data['disallowed_columns_names']),
				));
			}

			if (isset($data['vgse_toolbar_item'])) {
				update_post_meta($post_id, 'vgse_toolbars', $data['vgse_toolbar_item']);
			}

			do_action('vg_sheet_editor/frontend/metabox/after_fields_saved', $post_id, $data, $allowed_post_types);
		}

		// Register Custom Post Type
		function register_post_type() {

			$labels = array(
				'name' => _x('Spreadsheets', 'Post Type General Name', $this->textname),
				'singular_name' => _x('Spreadsheet', 'Post Type Singular Name', $this->textname),
				'menu_name' => $this->args['plugin_name'],
				'name_admin_bar' => __('Post Type', $this->textname),
				'archives' => __('Spreadsheet Archives', $this->textname),
				'attributes' => __('Spreadsheet Attributes', $this->textname),
				'parent_item_colon' => __('Parent Spreadsheet:', $this->textname),
				'all_items' => __('All Spreadsheets', $this->textname),
				'add_new_item' => __('Add New Spreadsheet', $this->textname),
				'add_new' => __('Add New', $this->textname),
				'new_item' => __('New Spreadsheet', $this->textname),
				'edit_item' => __('Edit settings', $this->textname),
				'update_item' => __('Update settings', $this->textname),
				'view_item' => __('View Spreadsheet', $this->textname),
				'view_items' => __('View Spreadsheets', $this->textname),
				'search_items' => __('Search Spreadsheet', $this->textname),
				'not_found' => __('Not found', $this->textname),
				'not_found_in_trash' => __('Not found in Trash', $this->textname),
				'featured_image' => __('Featured Image', $this->textname),
				'set_featured_image' => __('Set featured image', $this->textname),
				'remove_featured_image' => __('Remove featured image', $this->textname),
				'use_featured_image' => __('Use as featured image', $this->textname),
				'insert_into_item' => __('Insert into item', $this->textname),
				'uploaded_to_this_item' => __('Uploaded to this item', $this->textname),
				'items_list' => __('Spreadsheets list', $this->textname),
				'items_list_navigation' => __('Spreadsheets list navigation', $this->textname),
				'filter_items_list' => __('Filter items list', $this->textname),
			);
			$args = array(
				'label' => $this->args['plugin_name'],
				'labels' => $labels,
				'supports' => array('title'),
				'hierarchical' => false,
				'public' => false,
				'show_ui' => true,
				'show_in_menu' => false,
				'menu_position' => 99,
				'show_in_admin_bar' => false,
				'show_in_nav_menus' => false,
				'can_export' => true,
				'has_archive' => false,
				'exclude_from_search' => true,
				'publicly_queryable' => false,
				'rewrite' => false,
				'capability_type' => 'page',
			);
			register_post_type(VGSE_EDITORS_POST_TYPE, $args);
		}

		/**
		 * Get frontend editor html
		 * @param array $atts
		 * @param str $content
		 * @return str
		 */
		function get_frontend_editor_html($atts = array(), $content = '') {

			$a = shortcode_atts(array(
				'editor_id' => '',
					), $atts);

			if (empty($a['editor_id']) || !function_exists('VGSE')) {
				return;
			}

			if (!is_user_logged_in()) {
				$login_message = (!empty(VGSE()->options['frontend_login_message']) ) ? wp_kses_post(wpautop(VGSE()->options['frontend_login_message'])) : '';
				$login_form = wp_login_form(array('echo' => false, 'redirect' => $_SERVER['REQUEST_URI']));

				ob_start();
				include 'views/frontend/log-in-message.php';
				return ob_get_clean();
			}

			// Allow plugins to do custom validation before showing the form and show custom error messages
			$error_message = apply_filters('vg_sheet_editor/frontend/get_editor_html_error', null, $a, $this);
			if (!empty($error_message)) {
				return $error_message;
			}


			$editor_id = (int) $a['editor_id'];
			$post_type = get_post_meta($editor_id, 'vgse_post_type', true);

			$allowed_post_types = $this->get_allowed_post_types();

			if (empty($post_type) || !is_string($post_type) || (!isset($allowed_post_types[$post_type]) && !in_array($post_type, $allowed_post_types) )) {
				return;
			}

			$this->set_current_editor_settings($editor_id, 'shortcode');

			// Only show columns that were explicitly enabled in the metabox, don't show new columns automatically
			if (!defined('WPSE_ONLY_EXPLICITLY_ENABLED_COLUMNS')) {
				define('WPSE_ONLY_EXPLICITLY_ENABLED_COLUMNS', true);
			}

			// Filter is_editor_page
			add_action('vg_sheet_editor/is_editor_page', '__return_true');
			add_filter('vg_sheet_editor/js_data', array($this, 'modify_js_data'));

			do_action('vg_sheet_editor/render_editor_js_settings');

			// Hide editor logo on frontend
			add_filter('vg_sheet_editor/editor_page/allow_display_logo', '__return_false');

			// Filter toolbar items based on shortcode settings
			add_filter('vg_sheet_editor/toolbar/get_items', array($this, 'filter_toolbar_items'));
			add_filter('vg_sheet_editor/formulas/available_columns_options', array($this, 'filter_formula_columns'));

			// Enqueue css and js on frontend
			VGSE()->_register_styles();
			wp_enqueue_media();
			VGSE()->_register_scripts($post_type);

			$editor = VGSE()->helpers->get_provider_editor($post_type);
			if (is_object($editor)) {
				$editor->remove_conflicting_assets();
			}

			// Get editor page
			$current_post_type = $post_type;
			ob_start();
			require VGSE_DIR . '/views/editor-page.php';

			// Enable the infinite scroll
			echo '<input type="checkbox" id="infinito" style="display: none;" checked/>';
			$content = ob_get_clean();

			add_action('wp_footer', array($this, 'enqueue_assets'));
			return $content;
		}

		function enqueue_assets() {
			wp_enqueue_style('vg-sheet-editor-frontend-css', plugins_url('/assets/frontend/css/general.css', __FILE__));
			wp_enqueue_script('vg-sheet-editor-frontend-js', plugins_url('/assets/frontend/js/init.js', __FILE__), array('jquery'), filemtime(__DIR__ . '/assets/frontend/js/init.js'), true);
		}

		function filter_formula_columns($columns) {
			if (!empty($columns) && $this->current_editor_columns['context'] === 'shortcode' && is_array($this->current_editor_settings['columns']) && !empty($this->current_editor_settings['columns']['disabled'])) {
				$columns = array_diff_key($columns, $this->current_editor_settings['columns']['disabled']);
			}
			return $columns;
		}

		/**
		 * Filter toolbar items based on shortcode settings
		 * @param array $items
		 * @return array
		 */
		function filter_toolbar_items($items) {

			if ($this->current_editor_columns['context'] === 'shortcode' || is_string($this->current_editor_settings['toolbars']) && $this->current_editor_settings['toolbars'] === 'all') {
				return $items;
			}
			if (empty($this->current_editor_settings['toolbars'])) {
				$this->current_editor_settings['toolbars'] = array();
			}
			foreach ($items[$this->current_editor_settings['post_type']] as $toolbar => $toolbar_items) {

				if (isset($this->current_editor_settings['toolbars'][$toolbar]) && is_string($this->current_editor_settings['toolbars'][$toolbar]) && $this->current_editor_settings['toolbars'][$toolbar] === 'all') {
					continue;
				}

				if (!isset($this->current_editor_settings['toolbars'][$toolbar])) {
					$this->current_editor_settings['toolbars'][$toolbar] = array();
				}

				foreach ($toolbar_items as $index => $item) {
					if (!in_array($item['key'], $this->current_editor_settings['toolbars'][$toolbar]) && $item['allow_to_hide']) {

						unset($items[$this->current_editor_settings['post_type']][$toolbar][$index]);
					} else {
						
					}
				}
			}

			return $items;
		}

		/**
		 * Filter column items based on shortcode settings
		 * @param array $columns
		 * @return array
		 */
		function filter_columns_for_visibility($columns) {

			if (empty($this->current_editor_settings)) {
				return WP_Sheet_Editor_Columns_Visibility::filter_columns_for_visibility($columns);
			}
			$options = null;
			if (!empty($this->current_editor_settings['columns'])) {
				$options = array(
					$this->current_editor_settings['post_type'] => $this->current_editor_settings['columns']
				);
			}

			$filtered = WP_Sheet_Editor_Columns_Visibility::filter_columns_for_visibility(array(
						$this->current_editor_settings['post_type'] => $columns[$this->current_editor_settings['post_type']]
							), $options);
			return $filtered;
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if (null == WP_Sheet_Editor_Frontend_Editor::$instance) {
				WP_Sheet_Editor_Frontend_Editor::$instance = new WP_Sheet_Editor_Frontend_Editor();
				WP_Sheet_Editor_Frontend_Editor::$instance->init();
			}
			return WP_Sheet_Editor_Frontend_Editor::$instance;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

	}

}

add_action('after_setup_theme', 'vgse_frontend_editor', 99);

if (!function_exists('vgse_frontend_editor')) {

	function vgse_frontend_editor() {
		return WP_Sheet_Editor_Frontend_Editor::get_instance();
	}

}

$directories = glob(__DIR__ . '/integrations/*', GLOB_ONLYDIR);

if (!empty($directories)) {
	$directories = array_map('basename', $directories);
	foreach ($directories as $directory) {
		$file = __DIR__ . "/integrations/$directory/$directory.php";
		if (file_exists($file)) {
			require_once $file;
		}
	}
}