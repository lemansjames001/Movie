<?php
if (!class_exists('WPSE_Taxonomy_Terms_Sheet')) {

	class WPSE_Taxonomy_Terms_Sheet extends WPSE_Sheet_Factory {

		function __construct() {
			$allowed_columns = array();

			// @todo Add allowed columns for free version
			if (!wpsett_fs()->can_use_premium_code__premium_only()) {
				$allowed_columns = array(
				);
			}

			parent::__construct(array(
				'fs_object' => wpsett_fs(),
				'post_type' => array($this, 'get_taxonomies_and_labels'),
				'post_type_label' => '',
				'serialized_columns' => array(), // column keys
				'register_default_taxonomy_columns' => false,
				'bootstrap_class' => 'WPSE_Taxonomy_Terms_Spreadsheet_Bootstrap',
				'columns' => array($this, 'get_columns'),
				'allowed_columns' => $allowed_columns,
				'remove_columns' => array(
				), // column keys
			));

			add_filter('vg_sheet_editor/provider/default_provider_key', array($this, 'set_default_provider_for_taxonomies'), 10, 2);

			add_filter('vg_sheet_editor/provider/term/update_item_meta', array($this, 'filter_cell_data_for_saving'), 10, 3);
			add_filter('vg_sheet_editor/provider/term/get_item_meta', array($this, 'filter_cell_data_for_readings'), 10, 5);
			add_filter('vg_sheet_editor/provider/term/get_item_data', array($this, 'filter_cell_data_for_readings'), 10, 6);
			add_filter('vg_sheet_editor/handsontable/custom_args', array($this, 'enable_row_sorting'), 10, 2);
			add_action('vg_sheet_editor/after_enqueue_assets', array($this, 'register_assets'));
			add_action('wp_ajax_woocommerce_term_ordering', array($this, 'woocommerce_term_ordering'), 1);
			add_filter('vg_sheet_editor/columns/blacklisted_columns', array($this, 'blacklist_private_columns'), 10, 2);
			add_filter('vg_sheet_editor/import/find_post_id', array($this, 'find_existing_term_by_slug_for_import'), 10, 6);
			add_action('vg_sheet_editor/import/before_existing_wp_check_message', array($this, 'add_wp_check_message_for_import'));
			add_filter('vg_sheet_editor/import/wp_check/available_columns_options', array($this, 'filter_wp_check_options_for_import'), 10, 2);
			add_filter('vg_sheet_editor/welcome_url', array($this, 'filter_welcome_url'));
			add_action('vg_sheet_editor/filters/after_fields', array($this, 'add_filters_fields'), 10, 2);
			add_filter('vg_sheet_editor/load_rows/wp_query_args', array($this, 'filter_posts'), 10, 2);
			add_filter('terms_clauses', array($this, 'search_by_multiple_parents'), 10, 3);


			if (wpsett_fs()->can_use_premium_code__premium_only()) {
				// Register toolbar button to enable the display of variations and create variations
				add_action('vg_sheet_editor/editor/before_init', array(
					$this,
					'register_toolbar_items__premium_only'
				));
				add_action('wp_ajax_vgse_merge_terms', array($this, 'merge_terms__premium_only'));
			}
		}

		function merge_terms__premium_only() {
			global $wpdb;
			$data = VGSE()->helpers->clean_data($_REQUEST);
			if (empty($data['nonce']) || empty($data['post_type']) || !wp_verify_nonce($data['nonce'], 'bep-nonce') || !VGSE()->helpers->user_can_edit_post_type($data['post_type']) || empty($data['vgse_terms_source'])) {
				wp_send_json_error(array('message' => __('Request not allowed. Try again later.', VGSE()->textname)));
			}
			if (empty($data['final_term']) || !is_string($data['final_term'])) {
				wp_send_json_error(array('message' => __('Please select the term that you want to keep.', VGSE()->textname)));
			}
			$post_type = sanitize_text_field($data['post_type']);
			$final_term = get_term_by('slug', sanitize_text_field($data['final_term']), $post_type);
			$final_term_id = $final_term->term_id;

			// Disable post actions to prevent conflicts with other plugins
			VGSE()->helpers->remove_all_post_actions($post_type);

			if ($data['vgse_terms_source'] === 'individual') {

				if (empty($data['terms_to_remove'])) {
					wp_send_json_error(array('message' => __('Please select the terms to remove.', VGSE()->textname)));
				}

				$terms_slugs_to_remove = array_map('sanitize_text_field', $data['terms_to_remove']);
				$terms_to_remove = array();
				foreach ($terms_slugs_to_remove as $slug) {
					$term = get_term_by('slug', $slug, $post_type);
					if (is_object($term)) {
						$terms_to_remove[] = $term->term_id;
					}
				}
			} elseif ($data['vgse_terms_source'] === 'search') {

				$get_rows_args = apply_filters('vg_sheet_editor/terms/merge/search_query/get_rows_args', array(
					'nonce' => wp_create_nonce('bep-nonce'),
					'post_type' => $post_type,
					'filters' => $_REQUEST['filters'],
					'paged' => 1,
					'wpse_source' => 'merge_terms',
				));
				$base_query = VGSE()->helpers->prepare_query_params_for_retrieving_rows($get_rows_args, $get_rows_args);
				$base_query = apply_filters('vg_sheet_editor/terms/merge/posts_query', $base_query, $data);

				$base_query['fields'] = 'ids';
				$base_query['wpse_force_not_hierarchical'] = true;
				// When we search by keyword we use the post__in query arg, and 
				// the provider returns all the posts from the post__in ignoring the pagination, 
				// so we pass this flag to force the use of pagination
				$base_query['wpse_force_pagination'] = true;
				$per_page = (!empty(VGSE()->options) && !empty(VGSE()->options['be_posts_per_page_save']) ) ? (int) VGSE()->options['be_posts_per_page_save'] / 2 : 3;
				$base_query['posts_per_page'] = ( $per_page < 3 ) ? 3 : (int) $per_page;
				$editor = VGSE()->helpers->get_provider_editor($post_type);
				VGSE()->current_provider = $editor->provider;
				$query = $editor->provider->get_items($base_query);
				$terms_to_remove = $query->posts;
			}
			if ((int) $data['page'] === 1 && empty($terms_to_remove)) {
				wp_send_json_error(array('message' => __('Terms to remove not found.', VGSE()->textname)));
			}

			if (!empty($terms_to_remove)) {
				$this->_merge_terms__premium_only($terms_to_remove, $final_term_id, $post_type);
			}
			wp_send_json_success(array(
				'message' => sprintf(__('%s terms merged.', VGSE()->textname), count($terms_to_remove)),
				'deleted' => array_values(array_diff(array_unique($terms_to_remove), array($final_term_id))),
				'query' => current_user_can('manage_options') ? $base_query : null
			));
		}

		function _merge_terms__premium_only($terms_to_remove, $term_id_to_keep, $taxonomy) {


			if (!$term_id_to_keep || is_wp_error($term_id_to_keep)) {
				return false;
			}

			foreach ($terms_to_remove as $term_to_remove) {
				$term_id = $term_to_remove;
				if (!$term_id || $term_id == $term_id_to_keep) {
					continue;
				}

				$ret = wp_delete_term($term_id, $taxonomy, array('default' => $term_id_to_keep, 'force_default' => true));
				if (is_wp_error($ret)) {
					continue;
				}
			}

			return true;
		}

		/**
		 * Register toolbar item
		 */
		function register_toolbar_items__premium_only($editor) {

			if (!taxonomy_exists($editor->args['provider'])) {
				return;
			}
			$editor->args['toolbars']->register_item('merge_terms', array(
				'type' => 'button', // html | switch | button
				'content' => __('Merge terms', VGSE()->textname),
				'id' => 'merge-terms',
				'help_tooltip' => __('Combine terms into one and automatically reassign the posts to use the final term.', VGSE()->textname),
				'extra_html_attributes' => 'data-remodal-target="merge-terms-modal"',
				'footer_callback' => array($this, 'render_merge_terms_modal__premium_only')
					), $editor->args['provider']);
		}

		/**
		 * Render modal for merging terms
		 * @param str $post_type
		 * @return null
		 */
		function render_merge_terms_modal__premium_only($post_type) {
			$nonce = wp_create_nonce('bep-nonce');
			include vgse_taxonomy_terms()->plugin_dir . '/views/merge-terms-modal.php';
		}

		function search_by_multiple_parents($pieces, $taxonomies, $args) {

			// Check if our custom argument, 'wpse_term_parents' is set, if not, bail
			if (!isset($args['wpse_term_parents']) || !is_array($args['wpse_term_parents'])
			) {
				return $pieces;
			}

			// If  'wpse_term_parents' is set, make sure that 'parent' and 'child_of' is not set
			if ($args['parent'] || $args['child_of']
			) {
				return $pieces;
			}

			// Validate the array as an array of integers
			$parents = array_map('intval', $args['wpse_term_parents']);

			// Loop through $parents and set the WHERE clause accordingly
			$where = [];
			foreach ($parents as $parent) {
				// Make sure $parent is not 0, if so, skip and continue
				if (0 === $parent) {
					continue;
				}

				$where[] = " tt.parent = '$parent'";
			}

			if (!$where) {
				return $pieces;
			}

			$where_string = implode(' OR ', $where);
			$pieces['where'] .= " AND ( $where_string ) ";

			return $pieces;
		}

		/**
		 * Apply filters to wp-query args
		 * @param array $query_args
		 * @param array $data
		 * @return array
		 */
		function filter_posts($query_args, $data) {
			if (!empty($data['filters'])) {
				$filters = WP_Sheet_Editor_Filters::get_instance()->get_raw_filters($data);

				if (!empty($filters['parent_term_keyword'])) {
					$terms_by_keyword = get_terms(array(
						'hide_empty' => false,
						'update_term_meta_cache' => false,
						'name__like' => $filters['parent_term_keyword'],
						'fields' => 'ids'
					));
					$query_args['wpse_term_parents'] = $terms_by_keyword;
				}
			}

			return $query_args;
		}

		function add_filters_fields($current_post_type, $filters) {
			if (!taxonomy_exists($current_post_type)) {
				return;
			}
			if (is_taxonomy_hierarchical($current_post_type)) {
				?>
				<li>
					<label><?php _e('Parent keyword', VGSE()->textname); ?> <a href="#" class="tipso" data-tipso="<?php _e('We will display all the categories below parent that contains this keyword', VGSE()->textname); ?>">( ? )</a></label>
					<input type="text" name="parent_term_keyword" />							
				</li>
				<?php
			}
		}

		function filter_welcome_url($url) {
			$url = admin_url('admin.php?page=wpsett_welcome_page');
			return $url;
		}

		function filter_wp_check_options_for_import($columns, $taxonomy) {

			if (!taxonomy_exists($taxonomy)) {
				return $columns;
			}
			$columns = array(
				'ID' => $columns['ID'],
				'slug' => $columns['slug']
			);
			return $columns;
		}

		function add_wp_check_message_for_import($taxonomy) {

			if (!taxonomy_exists($taxonomy)) {
				return;
			}
			?>
			<style>.field-find-existing-columns .wp-check-message { display: none; }</style>
			<p class="wp-custom-check-message"><?php _e('We find items that have the same SLUG in the CSV and the WP Field.<br>Please select the CSV column that contains the slug.<br>You must import the slug column if you want to update existing categories, items without slug will be created as new.', vgse_taxonomy_terms()->textname); ?></p>
			<?php
		}

		function find_existing_term_by_slug_for_import($term_id, $row, $taxonomy, $meta_query, $writing_type, $check_wp_fields) {
			if (taxonomy_exists($taxonomy)) {
				$default_term_id = PHP_INT_MAX;
				if (!empty($row['ID']) && in_array('ID', $check_wp_fields)) {
					$term_id = ( term_exists((int) $row['ID'], $taxonomy) ) ? (int) $row['ID'] : null;
				} else {
					if (!empty($row['old_slug']) && in_array('old_slug', $check_wp_fields)) {
						$slug = $row['old_slug'];
					} elseif (!empty($row['slug']) && in_array('slug', $check_wp_fields)) {
						$slug = $row['slug'];
					}

					if (!empty($slug)) {
						$term = get_term_by('slug', $slug, $taxonomy);

						if ($term && !is_wp_error($term)) {
							$term_id = $term->term_id;
						}
					}
				}
				if (!$term_id) {
					$term_id = $default_term_id;
				}
			}
			return $term_id;
		}

		function blacklist_private_columns($blacklisted_fields, $provider) {
			if (!in_array($provider, $this->post_type)) {
				return $blacklisted_fields;
			}
//			We have allowed the product_count_xxx" term meta because WooCommerce uses this as usage count
//			so we need this for the searches to delete unused tags and categories, 
//			to prevent confusions we are blacklisting the core count column
//			$blacklisted_fields[] = '^product_count_';
			if (in_array($provider, array('product_cat', 'product_tag'), true)) {
				$blacklisted_fields[] = '^count$';
			}

			return $blacklisted_fields;
		}

		// WooCommerce returns 0 even on success, so we must return 
		// something to avoid showing the automatic ajax error notification
		// that sheet editor shows
		function woocommerce_term_ordering() {
			echo 1;
		}

		/**
		 * Register frontend assets
		 */
		function register_assets() {
			wp_enqueue_script('wpse-taxonomy-terms-js', plugins_url('/assets/js/init.js', vgse_taxonomy_terms()->args['main_plugin_file']), array(), VGSE()->version, false);
			wp_localize_script('wpse-taxonomy-terms-js', 'wpse_tt_data', array(
				'sort_icon_url' => plugins_url('/assets/imgs/sort-icon.png', vgse_taxonomy_terms()->args['main_plugin_file'])
			));
		}

		function enable_row_sorting($handsontable_args, $provider) {
			if (function_exists('WC') && ( strstr($provider, 'pa_') || in_array($provider, apply_filters('woocommerce_sortable_taxonomies', array('product_cat'))) )) {
				$handsontable_args['manualRowMove'] = true;
			}
			return $handsontable_args;
		}

		function get_taxonomies_and_labels() {

			if (wpsett_fs()->can_use_premium_code__premium_only()) {
				$taxonomies = array_merge(get_taxonomies(array(
					'public' => true,
					'show_ui' => true,
					'_builtin' => true,
								), 'objects'), get_taxonomies(array(
					'show_ui' => true,
					'_builtin' => false,
								), 'objects'));
				$out = array(
					'post_types' => array_values(wp_list_pluck($taxonomies, 'name')),
					'labels' => array_values(wp_list_pluck($taxonomies, 'label')),
				);
			} else {
				$out = array(
					'post_types' => array('category', 'post_tag'),
					'labels' => array('Blog categories', 'Blog tags'),
				);
			}

			return $out;
		}

		function set_default_provider_for_taxonomies($provider_class_key, $provider) {
			if (taxonomy_exists($provider)) {
				$provider_class_key = 'term';
			}
			return $provider_class_key;
		}

		function filter_cell_data_for_readings($value, $id, $key, $single, $context, $item = null) {
			if ($context !== 'read' || ( $item && !in_array($item->taxonomy, $this->post_type))) {
				return $value;
			}
			if ($key === 'parent' && $value) {
				$term = VGSE()->helpers->get_current_provider()->get_item($value);
				$value = $term->name;
			}
			if ($key === 'count') {
				$value = (int) $value;
			}

			return $value;
		}

		function filter_cell_data_for_saving($new_value, $id, $key) {
			if (get_post_type($id) !== $this->post_type) {
				return $new_value;
			}

			if ($key === 'taxonomy_term_files' && is_array($new_value)) {
				$new_value = $new_value;
			}

			return $new_value;
		}

		function get_columns() {
			
		}

	}

	$GLOBALS['wpse_taxonomy_terms_sheet'] = new WPSE_Taxonomy_Terms_Sheet();
}