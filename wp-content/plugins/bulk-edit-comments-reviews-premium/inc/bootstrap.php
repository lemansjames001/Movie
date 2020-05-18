<?php
if (!class_exists('WPSE_Comments_Spreadsheet_Bootstrap')) {

	class WPSE_Comments_Spreadsheet_Bootstrap extends WP_Sheet_Editor_Bootstrap {

		function render_quick_access() {
			$screen = get_current_screen();
			// We get the taxonomy from $_GET instead of the function parameter to make it
			// compatible with the parent's method which doesn't accept parameters
			if (empty($screen) || $screen->base !== 'edit-comments') {
				return;
			}
			?>
			<script>jQuery(window).on('load', function () {
					jQuery('.wp-heading-inline').last().after('<a href=<?php echo json_encode(VGSE()->helpers->get_editor_url('comments')); ?> class="page-title-action"><?php _e('Open in a Spreadsheet', VGSE()->textname); ?></a>');
				});</script>
			<?php
		}

		function get_post_title_for_column($post, $cell_key, $cell_args) {
			$value = get_the_title($post->comment_post_ID) . ' (' . get_post_type($post->comment_post_ID) . ')';
			return $value;
		}

		function get_post_url_for_column($post, $cell_key, $cell_args) {
			$value = get_permalink($post->comment_post_ID);
			return $value;
		}

		function _register_columns() {
			global $wpdb;
			$post_type = 'comments';
			$this->columns->register_item('ID', $post_type, array(
				'data_type' => 'post_data', //String (post_data,post_meta|meta_data)	
				'unformatted' => array('data' => 'ID', 'renderer' => 'html', 'readOnly' => true), //Array (Valores admitidos por el plugin de handsontable)
				'column_width' => 75, //int (Ancho de la columna)
				'title' => __('ID', vgse_comments()->textname), //String (Titulo de la columna)
				'type' => '', // String (Es para saber si será un boton que abre popup, si no dejar vacio) boton_tiny|boton_gallery|boton_gallery_multiple|(vacio)
				'supports_formulas' => false,
				'allow_to_hide' => false,
				'allow_to_save' => false,
				'allow_to_rename' => false,
				'formatted' => array('data' => 'ID', 'renderer' => 'html', 'readOnly' => true),
			));
			$this->columns->register_item('comment_author', $post_type, array(
				'data_type' => 'post_data', //String (post_data,post_meta|meta_data)	
				'column_width' => 180, //int (Ancho de la columna)
				'title' => __('Author', vgse_comments()->textname), //String (Titulo de la columna)
				'supports_formulas' => true,
			));
			$this->columns->register_item('comment_author_email', $post_type, array(
				'data_type' => 'post_data', //String (post_data,post_meta|meta_data)	
				'column_width' => 180, //int (Ancho de la columna)
				'title' => __('Author email', vgse_comments()->textname), //String (Titulo de la columna)
				'supports_formulas' => true,
			));
			$this->columns->register_item('comment_author_url', $post_type, array(
				'data_type' => 'post_data', //String (post_data,post_meta|meta_data)	
				'column_width' => 180, //int (Ancho de la columna)
				'title' => __('Author URL', vgse_comments()->textname), //String (Titulo de la columna)
				'supports_formulas' => true,
			));
			$this->columns->register_item('comment_author_IP', $post_type, array(
				'data_type' => 'post_data', //String (post_data,post_meta|meta_data)	
				'column_width' => 180, //int (Ancho de la columna)
				'title' => __('Author IP', vgse_comments()->textname), //String (Titulo de la columna)
				'supports_formulas' => true,
			));
			$this->columns->register_item('comment_content', $post_type, array(
				'data_type' => 'post_data', //String (post_data,post_meta|meta_data)	
				'column_width' => 180, //int (Ancho de la columna)
				'title' => __('Content', vgse_comments()->textname), //String (Titulo de la columna)
				'supports_formulas' => true,
				'formatted' => array(
					'renderer' => 'wp_tinymce',
					'wpse_template_key' => 'tinymce_cell_template'
				),
			));
			$this->columns->register_item('comment_date', $post_type, array(
				'data_type' => 'post_data', //String (post_data,post_meta|meta_data)	
				'column_width' => 180, //int (Ancho de la columna)
				'title' => __('Date', vgse_comments()->textname), //String (Titulo de la columna)
				'supports_formulas' => true,
				'formatted' => array('type' => 'date', 'dateFormat' => 'YYYY-MM-DD HH:mm:ss', 'correctFormat' => true, 'defaultDate' => date('Y-m-d H:i:s'), 'datePickerConfig' => array('firstDay' => 0, 'showWeekNumber' => true, 'numberOfMonths' => 1)),
			));
			$this->columns->register_item('comment_post_ID', $post_type, array(
				'data_type' => 'post_data', //String (post_data,post_meta|meta_data)	
				'column_width' => 100, //int (Ancho de la columna)
				'title' => __('Post ID', vgse_comments()->textname), //String (Titulo de la columna)
				'supports_formulas' => true,
			));
			$this->columns->register_item('comment_post_title', $post_type, array(
				'data_type' => 'post_data', //String (post_data,post_meta|meta_data)	
				'column_width' => 320, //int (Ancho de la columna)
				'title' => __('Post Title', vgse_comments()->textname), //String (Titulo de la columna)
				'supports_formulas' => false,
				'allow_to_save' => false,
				'is_locked' => true,
				'get_value_callback' => array($this, 'get_post_title_for_column'),
			));

			$this->columns->register_item('view_post', $post_type, array(
				'data_type' => 'post_data',
				'unformatted' => array('data' => 'view_post', 'renderer' => 'wp_external_button', 'readOnly' => true),
				'column_width' => 115,
				'title' => __('View Post', VGSE()->textname),
				'supports_formulas' => false,
				'type' => 'external_button',
				'formatted' => array('data' => 'view_post', 'renderer' => 'wp_external_button', 'readOnly' => true),
				'allow_to_hide' => true,
				'allow_to_save' => false,
				'allow_to_rename' => true,
				'get_value_callback' => array($this, 'get_post_url_for_column'),
			));
			$this->columns->register_item('comment_karma', $post_type, array(
				'data_type' => 'post_data', //String (post_data,post_meta|meta_data)	
				'column_width' => 120, //int (Ancho de la columna)
				'title' => __('Karma', vgse_comments()->textname), //String (Titulo de la columna)
				'supports_formulas' => true,
			));
			$this->columns->register_item('comment_approved', $post_type, array(
				'data_type' => 'post_data', //String (post_data,post_meta|meta_data)	
				'column_width' => 120, //int (Ancho de la columna)
				'title' => __('Status', vgse_comments()->textname), //String (Titulo de la columna)
				'supports_formulas' => true,
				'supports_sql_formulas' => false,
				'formatted' => array('editor' => 'select', 'selectOptions' => array(
						'0' => __('Pending moderation', vgse_comments()->textname),
						'1' => __('Approved', vgse_comments()->textname),
						'spam' => __('Spam', vgse_comments()->textname),
						'trash' => __('Trash', vgse_comments()->textname),
						'delete' => __('Delete completely', vgse_comments()->textname),
					)),
			));
			$this->columns->register_item('comment_agent', $post_type, array(
				'data_type' => 'post_data', //String (post_data,post_meta|meta_data)	
				'column_width' => 180, //int (Ancho de la columna)
				'title' => __('Browser of the user', vgse_comments()->textname), //String (Titulo de la columna)
				'supports_formulas' => true,
			));
			$types = array_merge(array('comment'), $wpdb->get_col("SELECT comment_type FROM $wpdb->comments WHERE comment_type != '' GROUP BY comment_type"));
			$this->columns->register_item('comment_type', $post_type, array(
				'data_type' => 'post_data', //String (post_data,post_meta|meta_data)	
				'column_width' => 120, //int (Ancho de la columna)
				'title' => __('Type', vgse_comments()->textname), //String (Titulo de la columna)
				'supports_formulas' => true,
				'formatted' => array('editor' => 'select', 'selectOptions' => $types),
			));
			$this->columns->register_item('comment_parent', $post_type, array(
				'data_type' => 'post_data', //String (post_data,post_meta|meta_data)	
				'column_width' => 120, //int (Ancho de la columna)
				'title' => __('Parent comment', vgse_comments()->textname), //String (Titulo de la columna)
				'supports_formulas' => true,
			));
			$this->columns->register_item('user_id', $post_type, array(
				'data_type' => 'post_data', //String (post_data,post_meta|meta_data)	
				'column_width' => 180, //int (Ancho de la columna)
				'title' => __('User ID of the Author', vgse_comments()->textname), //String (Titulo de la columna)
				'supports_formulas' => true,
			));
		}

	}

}