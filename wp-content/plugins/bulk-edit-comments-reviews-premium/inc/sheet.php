<?php
if (!class_exists('WPSE_Comments_Sheet')) {

	class WPSE_Comments_Sheet extends WPSE_Sheet_Factory {

		var $key = 'comments';

		function __construct() {
			$allowed_columns = array();

			// @todo Add allowed columns for free version
			if (!wpsecr_fs()->can_use_premium_code__premium_only()) {
				$allowed_columns = array(
				);
			}

			parent::__construct(array(
				'fs_object' => wpsecr_fs(),
				'post_type' => array('comments'),
				'post_type_label' => array(__('Comments')),
				'serialized_columns' => array(), // column keys
				'register_default_taxonomy_columns' => false,
				'bootstrap_class' => 'WPSE_Comments_Spreadsheet_Bootstrap',
				'columns' => array($this, 'get_columns'),
				'allowed_columns' => $allowed_columns,
				'remove_columns' => array(
				), // column keys
			));

			add_filter('vg_sheet_editor/provider/default_provider_key', array($this, 'set_default_provider_for_comments'), 10, 2);
			add_filter('vg_sheet_editor/import/find_post_id', array($this, 'find_existing_comment_by_slug_for_import'), 10, 6);
			add_action('vg_sheet_editor/import/before_existing_wp_check_message', array($this, 'add_wp_check_message_for_import'));
			add_filter('vg_sheet_editor/import/wp_check/available_columns_options', array($this, 'filter_wp_check_options_for_import'), 10, 2);
			add_action('vg_sheet_editor/filters/after_fields', array($this, 'add_filters_fields'), 10, 2);
			add_filter('vg_sheet_editor/load_rows/wp_query_args', array($this, 'filter_posts'), 10, 2);

			add_action('admin_menu', array($this, 'register_menu'));
		}

		function register_menu() {
			add_comments_page(__('Bulk edit', vgse_comments()->textname), __('Bulk edit comments', vgse_comments()->textname), 'edit_posts', VGSE()->helpers->get_editor_url($this->key), null);
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

				if (!empty($filters['comments_post_type'])) {
					$query_args['comments_post_type'] = $filters['comments_post_type'];
				}
				if (!empty($filters['comment_status'])) {
					$query_args['comment_status'] = array_filter($filters['comment_status']);
				}
				if (!empty($filters['comment_type'])) {
					$query_args['type'] = sanitize_text_field($filters['comment_type']);
				}
				if (!empty($filters['comments_author'])) {
					$query_args['comments_author'] = array_filter(array_map('intval', $filters['comments_author']));
				}
			}

			return $query_args;
		}

		function add_filters_fields($current_post_type, $filters) {
			global $wpdb;
			if ($current_post_type !== $this->key) {
				return;
			}
			$post_types_with_comments = get_post_types_by_support('comments');
			?>
			<li>
				<label><?php _e('Post type', VGSE()->textname); ?> <a href="#" class="tipso" data-tipso="<?php _e('Find comments created for all posts in a post type. I.e. Find comments left on posts, or reviews left on products', VGSE()->textname); ?>">( ? )</a></label>
				<select name="comments_post_type">
					<option value="">--</option>
					<?php foreach ($post_types_with_comments as $post_type) {
						?>
						<option value="<?php echo $post_type; ?>"><?php echo $post_type; ?></option>
					<?php }
					?>
				</select>
			</li>
			<li>
				<label><?php _e('Comment status', VGSE()->textname); ?></label>
				<select name="comment_status[]" multiple data-placeholder="<?php _e('Select...', VGSE()->textname); ?>" class="select2">
					<option value=""><?php _e('All'); ?></option>
					<option value="hold"><?php _e('Pending'); ?></option>
					<option value="approve"><?php _e('Approved'); ?></option>
				</select>
			</li>
			<li>
				<label><?php _e('Post author', VGSE()->textname); ?> <a href="#" class="tipso" data-tipso="<?php esc_attr_e('Example: Find comments left on posts created by an author or reviews left on the events published by a specific user', VGSE()->textname); ?>">( ? )</a></label>
				<select name="comments_author[]" multiple data-placeholder="<?php _e('Select...', VGSE()->textname); ?>" class="select2">
					<option value=""><?php _e('All'); ?></option>
					<?php
					$authors = VGSE()->data_helpers->get_authors_list(null, true);
					if (!empty($authors) && is_array($authors)) {
						foreach ($authors as $item => $value) {
							echo '<option value="' . $item . '" ';
							echo '>' . $value . '</option>';
						}
					}
					?>
				</select>
			</li>
			<li>
				<label><?php _e('Comment type', VGSE()->textname); ?></label>
				<select name="comment_type">
					<option value=""><?php _e('Comment'); ?></option>
					<?php
					$types = $wpdb->get_col("SELECT comment_type FROM $wpdb->comments WHERE comment_type != '' GROUP BY comment_type");
					if (!empty($types) && is_array($types)) {
						foreach ($types as $type) {
							?>
							<option value="<?php echo esc_attr($type); ?>"><?php echo esc_html($type); ?></option>
							<?php
						}
					}
					?>
				</select>
			</li>
			<?php
		}

		function filter_wp_check_options_for_import($columns, $post_type_key) {

			if ($post_type_key !== $this->key) {
				return $columns;
			}
			$columns = array(
				'ID' => $columns['ID'],
			);
			return $columns;
		}

		function add_wp_check_message_for_import($post_type_key) {

			if ($post_type_key !== $this->key) {
				return;
			}
			?>
			<style>.field-find-existing-columns .wp-check-message { display: none; }</style>
			<p class="wp-custom-check-message"><?php _e('We find items that have the same ID in the CSV and the WP Field.<br>Please select the CSV column that contains the comment ID.', vgse_comments()->textname); ?></p>
			<?php
		}

		function find_existing_comment_by_slug_for_import($comment_id, $row, $post_type_key, $meta_query, $writing_type, $check_wp_fields) {
			if ($post_type_key === $this->key && !empty($row['ID'])) {
				$comment_id = (int) $row['ID'];
			}
			return $comment_id;
		}

		function set_default_provider_for_comments($provider_class_key, $provider) {
			if ($provider === 'comments') {
				$provider_class_key = 'comments';
			}
			return $provider_class_key;
		}

		function get_columns() {
			
		}

	}

	$GLOBALS['wpse_comments_sheet'] = new WPSE_Comments_Sheet();
}