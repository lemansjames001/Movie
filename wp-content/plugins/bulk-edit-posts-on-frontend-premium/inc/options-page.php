<?php

if (!function_exists('vgse_frontend_options_init')) {
	add_action('vg_sheet_editor/after_init', 'vgse_frontend_options_init', 20);

	function vgse_frontend_options_init() {

		if (class_exists('WPSE_Frontend_Sheets_Options_Page')) {
			return;
		}

		class WPSE_Frontend_Sheets_Options_Page extends WP_Sheet_Editor_Redux_Setup {

			public function setArguments() {
				parent::setArguments();
				$this->args['page_parent'] = vgse_frontend_editor()->main_admin_page_slug;
				$this->args['page_slug'] = vgse_frontend_editor()->main_admin_page_slug . '_options';

				add_filter('redux/options/' . VGSE()->options_key . '/sections', array($this, 'add_options'));
			}

			public function add_options($sections) {
				$new_sections = array(
					'icon' => 'el-icon-cogs',
					'title' => __('Frontend Spreadsheets', vgse_frontend_editor()->textname),
					'fields' => array(
						array(
							'id' => 'frontend_login_message',
							'type' => 'editor',
							'title' => __('Login message', vgse_frontend_editor()->textname),
							'default' => __('You need to login to view this page.', vgse_frontend_editor()->textname),
							'desc' => __('This will be displayed when the current user is not logged in and tries to see a spreadsheet page. We will display a login form after your message.', vgse_frontend_editor()->textname),
						),
						array(
							'id' => 'hide_admin_bar_frontend',
							'type' => 'switch',
							'title' => __('Hide admin bar on the frontend', vgse_frontend_editor()->textname),
							'desc' => __('By default WordPress shows a black bar at the top of the page when a logged in user views a frontend page. The bar lets you access the wp-admin, log out, edit the current page, etc. If you enable this option we will hide that bar and you can use the shortcode: [vg_display_logout_link] to display the logout link.', vgse_frontend_editor()->textname),
							'default' => true,
						),
						array(
							'id' => 'frontend_logo',
							'type' => 'media',
							'url' => true,
							'title' => __('Logo', vgse_frontend_editor()->textname),
							'desc' => __('This logo will be displayed above the spreadsheet in the frontend', vgse_frontend_editor()->textname),
						),
						array(
							'id' => 'frontend_menu',
							'type' => 'select',
							'title' => __('Menu', vgse_frontend_editor()->textname),
							'desc' => __('This menu will be displayed at the top right section above the spreadsheet.', vgse_frontend_editor()->textname),
							'data' => 'menus'
						),
						array(
							'id' => 'frontend_main_color',
							'type' => 'color',
							'title' => __('Main Color', vgse_frontend_editor()->textname),
							'subtitle' => __('This color will be used as background for the header and footer.', vgse_frontend_editor()->textname),
							'default' => '#FFFFFF',
							'validate' => 'color',
						),
						array(
							'id' => 'frontend_links_color',
							'type' => 'color',
							'title' => __('Links Color', vgse_frontend_editor()->textname),
							'subtitle' => __('This color will be used for the menu links, it should be the opposite of the background color. i.e. dark background with light text, or light background with dark text', vgse_frontend_editor()->textname),
							'default' => '#000',
							'validate' => 'color',
						)
					)
				);
				return array_merge(array($new_sections), $sections);
			}

		}

		new WPSE_Frontend_Sheets_Options_Page();
	}

}