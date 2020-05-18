<div id="vgse-wrapper">
	<a href="https://wpsheeteditor.com/?utm_source=wp-admin&utm_medium=pro-plugin&utm_campaign=frontend-sheets-metabox-logo" target="_blank"><img src="<?php echo $this->args['logo']; ?>" class="vg-logo"></a>
	<?php wp_nonce_field('bep-nonce', 'bep-nonce'); ?>

	<a class="button help-button" href="<?php echo VGSE()->get_support_links('contact_us', 'url', 'frontend-sheets-metabox-help'); ?>" target="_blank" ><i class="fa fa-envelope"></i> <?php _e('Need help?', vgse_frontend_editor()->textname); ?></a>   
	<a class="button" onclick="jQuery('.vgse-frontend-tutorial').slideToggle(); return false;"><i class="fa fa-play"></i> <?php _e('Watch tutorial', vgse_frontend_editor()->textname); ?></a>   
	<?php if (bepof_fs()->can_use_premium_code__premium_only()) { ?>
		<a class="button" href="<?php echo bepof_fs()->get_account_url(); ?>" target="_blank" ><i class="fa fa-user"></i> <?php _e('My account and license', vgse_frontend_editor()->textname); ?></a>
	<?php } ?>

	<iframe style="display: none;" class="vgse-frontend-tutorial" width="560" height="315" src="https://www.youtube.com/embed/kEovWuNImok?rel=0&amp;controls=0&amp;showinfo=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>

	<h3 class="wpse-toggle-head"><?php _e('1. What information do you want to edit on the frontend?', $this->textname); ?> <i class="fa fa-chevron-down"></i></h3>

	<div class="wpse-toggle-content active float-call-to-action">
		<?php
		if (!empty($post_type_selectors)) {
			foreach ($post_type_selectors as $post_type_selector) {
				$is_disabled = '';
				if (!$post_type_selector['allowed']) {
					$post_type_selector['label'] .= $upgrade_label_suffix;
					$is_disabled = 'disabled';
				}
				?>
				<label><input type="radio" <?php echo $is_disabled; ?> value="<?php echo esc_attr($post_type_selector['key']); ?>"  name="vgse_post_type" <?php checked($post_type_selector['key'], $sanitized_post_type); ?> /> <?php echo $post_type_selector['label']; ?></label><br/>
				<?php
			}
		}
		?>
		<br/>
		<button class="button button-primary"><?php _e('Save changes', $this->textname); ?></button>

	</div>
	<?php
	if (!$post_type) {
		?>
		<p><?php _e('Please select the post type and save changes. After you save changes you will be able to see the rest of the settings and instructions.', $this->textname); ?></p>
		<?php
		return;
	}

	$is_disabled = '';
	$label_suffix = '';

	if (!bepof_fs()->can_use_premium_code__premium_only()) {
		$is_disabled = 'disabled';
		$label_suffix = sprintf(__(' <small>(Premium. <a href="%s" target="_blank">Try for Free for 7 Days</a>)</small>', $this->textname), VGSE()->get_buy_link('frontend-toolbar-selector', $this->buy_link));
	}
	?>

	<h3 class="wpse-toggle-head"><?php _e('2. Setup page in the frontend', $this->textname); ?> <i class="fa fa-chevron-down"></i></h3>
	<!--<div class="wpse-toggle-content active">
		<p><?php _e('You need to set a logo in the settings page. Optionally you can change the background color, links color, and set a header menu.', $this->textname); ?></p>

		<a class="button" href="<?php echo admin_url('admin.php?page=vgsefe_welcome_page_options'); ?>" target="_blank"><i class="fa fa-cog"></i> <?php _e('Open Settings Page', $this->textname); ?></a> - <a class="button button-primary" href="<?php echo $frontend_url; ?>" target="_blank"><?php _e('Preview Frontend Editor', $this->textname); ?></a>

		<p><?php _e('When you finish this step you can start using the frontend editor. You can add the frontend page to a menu or share the link with your users.', $this->textname); ?></p>
	</div>-->

	<div class="wpse-toggle-content active">
		<p><?php printf(__('Add this shortcode to a full-width page: %s', $this->textname), '[vg_sheet_editor editor_id="' . $post->ID . '"]'); ?></p>
		<?php if ($frontend_url && $frontend_page_id) { ?>
			<p><?php printf(__('Page detected: This page contains the shortcode: <b>%s</b> (<a href="%s" target="_blank">Preview</a> - <a href="%s" target="_blank">Edit</a>)', $this->textname), get_the_title($frontend_page_id), $frontend_url, admin_url('post.php?action=edit&post=' . (int) $frontend_page_id)); ?></p>
		<?php } ?>
	</div>

	<h3 class="wpse-toggle-head"><?php _e('3. Available tools (optional)', $this->textname); ?> <i class="fa fa-chevron-down"></i></h3>
	<div class="wpse-toggle-content float-call-to-action">
		<?php
		foreach ($post_type_toolbars as $toolbar_key => $toolbar_items) {
			echo '<h4>' . esc_html($toolbar_key) . ' toolbar</h4>';

// In the free version we force the admin to display the "add new post" tool
			$toolbar_items_keys = wp_list_pluck($toolbar_items, 'key');
			if ($toolbar_key === 'primary' && in_array('add_rows', $toolbar_items_keys)) {
				$current_toolbars[$toolbar_key] = array('add_rows');
			}
			foreach ($toolbar_items as $toolbar_item) {
				// Child toolbar items can't be enabled/disabled in the metabox, only the parents
				if (!empty($toolbar_item['parent'])) {
					continue;
				}
				?> 
				<label><input type="checkbox" <?php echo $is_disabled; ?> value="<?php echo esc_attr($toolbar_item['key']); ?>"  name="vgse_toolbar_item[<?php echo esc_attr($toolbar_key); ?>][]" <?php checked(isset($current_toolbars[$toolbar_key]) && in_array($toolbar_item['key'], $current_toolbars[$toolbar_key])); ?> /> <?php echo esc_html(strip_tags($toolbar_item['label'])) . $label_suffix; ?></label><br/>
				<?php
			}
		}
		?>
		<br/>
		<button class="button button-primary"><?php _e('Save changes', $this->textname); ?></button>
	</div>
	<h3 class="wpse-toggle-head"><?php _e('4. Columns visibility and Custom Fields (optional)', $this->textname); ?> <i class="fa fa-chevron-down"></i></h3>
	<div class="wpse-toggle-content">
		<?php
		if (empty($column_visibility_options[$post_type]['enabled'])) {
			$column_visibility_options[$post_type]['enabled'] = array();
		}
		ob_start();
		$visible_columns = array_merge($column_visibility_options[$post_type]['enabled'], VGSE()->helpers->get_provider_columns($post_type));
		$columns_visibility_module->render_settings_modal($post_type, true, $column_visibility_options, null, $visible_columns);
		$columns_visibility_html = ob_get_clean();

		echo str_replace(array(
			'data-remodal-id="modal-columns-visibility" data-remodal-options="closeOnOutsideClick: false" class="remodal remodal',
			'<h3>Columns visibility</h3>'
				), array('class="', ''), $columns_visibility_html);
		?>
		<br/>
		<button class="button button-primary"><?php _e('Save changes', $this->textname); ?></button>
	</div>

	<div class="clear"></div>
	<h3 class="wpse-toggle-head"><?php _e('5. Learn more about security and user roles (optional)', $this->textname); ?> <i class="fa fa-chevron-down"></i></h3>
	<div class="wpse-toggle-content">

		<p><?php _e('The editor is available only for logged in users. Unknown users will see a login form automatically.', $this->textname); ?></p>

		<h3><?php _e('User roles', $this->textname); ?></h3>

		<ul>
			<li><?php _e('Subscriber role is not allowed to use the editor.', $this->textname); ?></li>
			<li><?php _e('Contributor role can view and edit their own posts only, but they can´t upload images.', $this->textname); ?></li>
			<li><?php _e('Author role can view and edit their own posts only, they can upload images.', $this->textname); ?></li>
			<li><?php _e('Editor role can view and edit all posts and pages.', $this->textname); ?></li>

			<?php
			if (bepof_fs()->can_use_premium_code__premium_only()) {
				if (function_exists('WC')) {
					echo __('<li>Shop manager role can view and edit WooCommerce products.</li>', $this->textname);
				}
			}
			?>

			<li><?php _e('Administrator role can view and edit everything.', $this->textname); ?></li>

		</ul>
	</div>
	<?php do_action('vg_sheet_editor/frontend/metabox/after_fields', $post); ?>

	<div class="clear"></div>
	<style>
		.modal-columns-visibility .vg-refresh-needed,
		.modal-columns-visibility .vgse-sorter .fa-refresh,
		.modal-columns-visibility .vgse-save-settings,
		.modal-columns-visibility .vgse-allow-save-settings,
		.modal-columns-visibility .remodal-confirm,
		.modal-columns-visibility .remodal-cancel,
		a.page-title-action
		{
			display: none !important;
		}
		.float-call-to-action small {
			position: absolute;
			left: 247px;
		}
		.modal-columns-visibility {
			overflow: auto;
		}
		<?php if (!bepof_fs()->can_use_premium_code__premium_only()) { ?>
			/*extra simple*/
			h2.hndle.ui-sortable-handle,
			#delete-action,
			#post-body #normal-sortables,
			#minor-publishing-actions,
			#misc-publishing-actions,
			#minor-publishing,
			#titlewrap {
				display: none;
			}

			div#vgse-columns-visibility-metabox {
				border: 0;
			} 

			h3.wpse-toggle-head {
				color: #333;
			} 
		<?php } ?>
	</style>
</div>
