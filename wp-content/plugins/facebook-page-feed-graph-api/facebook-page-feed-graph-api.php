<?php
/**
 * Plugin Name: Mongoose Page Plugin
 * Plugin URI: https://mongoosemarketplace.com/downloads/facebook-page-plugin/
 * Description: The most popular way to display the Facebook Page Plugin on your WordPress website. Easy implementation using a shortcode or widget. Now available in 95 different languages
 * Version: 1.7.3
 * Author: Mongoose Marketplace
 * Author URI: https://mongoosemarketplace.com/
 * License: GPLv2
 * Text Domain: facebook-page-feed-graph-api
 
 * Copyright 2015-2019  Cameron Jones  (email : support@mongoosemarketplace.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
 */

defined( 'ABSPATH' ) or die();

class cameronjonesweb_facebook_page_plugin {

	public static $remove_donate_notice_key = 'facebook_page_plugin_donate_notice_ignore';

	public function __construct() {

		define( 'CJW_FBPP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		define( 'CJW_FBPP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		define( 'CJW_FBPP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		define( 'CJW_FBPP_PLUGIN_VER', '1.7.3' );
		define( 'CJW_FBPP_PLUGIN_DONATE_LINK', 'https://www.patreon.com/cameronjonesweb' );
		define( 'CJW_FBPP_PLUGIN_SURVEY_LINK', 'https://cameronjonesweb.typeform.com/to/BllbYm' );

		// Add all the hooks and actions.
		add_shortcode( 'facebook-page-plugin', array( $this, 'facebook_page_plugin' ) );
		add_filter( 'widget_text', 'do_shortcode' );
		add_action( 'wp_dashboard_setup', array( $this, 'facebook_page_plugin_dashboard_widget' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'facebook_page_plugin_admin_resources' ) );
		add_action( 'admin_init', array( $this, 'remove_donate_notice_nojs' ) );
		add_action( 'admin_menu', array( $this, 'facebook_page_plugin_landing_page_menu' ) );
		add_action( 'wp_ajax_facebook_page_plugin_latest_blog_posts_callback', array( $this, 'facebook_page_plugin_latest_blog_posts_callback' ) );
		add_action( 'activated_plugin', array( $this, 'facebook_page_plugin_activation_hook' ) );
		add_action( 'wp_ajax_facebook_page_plugin_remove_donate_notice', array( $this, 'remove_donate_notice' ) );
		add_filter( 'plugin_action_links_' . CJW_FBPP_PLUGIN_BASENAME, array( $this, 'plugin_action_links' ) );

	}


	// Filter functions.
	private static function dashboard_widget_capability() {

		$return = apply_filters( 'facebook_page_plugin_dashboard_widget_capability', 'edit_posts' );
		return $return;

	}


	private static function app_id() {

		$return = apply_filters( 'facebook_page_plugin_app_id', '846690882110183' );
		return $return;

	}


	// Admin functions.

	public static function donate_notice() {

		$return = NULL;

		if( current_user_can( 'administrator' ) ) {

			$user_id = get_current_user_id();

			if ( !get_user_meta( $user_id, self::$remove_donate_notice_key ) || get_user_meta( $user_id, self::$remove_donate_notice_key ) === false ) {

				$return .= '<div class="facebook-page-plugin-donate"><p>';

					$return .= __( 'Thank you for using the Mongoose Page Plugin. Please consider donating to support ongoing development. ', 'facebook-page-feed-graph-api' );

					$return .= '</p><p>';

					$return .= '<a href="' . CJW_FBPP_PLUGIN_DONATE_LINK . '" target="_blank" class="button button-secondary">' . __( 'Donate now', 'facebook-page-feed-graph-api' ) . '</a>';

					$return .= '<a href="?' . self::$remove_donate_notice_key . '=0" class="notice-dismiss facebook-page-plugin-donate-notice-dismiss" title="' . __( 'Dismiss this notice', 'facebook-page-feed-graph-api' ) . '"><span class="screen-reader-text">' . __( 'Dismiss this notice', 'facebook-page-feed-graph-api' ) . '.</span></a>';

				$return .= '</p></div>';

			}

		}

		return $return;

	}

	public static function remove_donate_notice() {

		$user_id = get_current_user_id();

		update_user_meta( $user_id, self::$remove_donate_notice_key, 'true', true );

		if( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

			wp_die();

		}

	}

	public function remove_donate_notice_nojs() {

		if ( isset( $_GET[self::$remove_donate_notice_key] ) && '0' == $_GET[self::$remove_donate_notice_key] ) {

			self::remove_donate_notice();

		}

	}


	// Add a link to support on plugins listing
	function plugin_action_links( $links ) {

		$links[] = sprintf(
			'<a href="https://wordpress.org/support/plugin/facebook-page-feed-graph-api" target="_blank">%1$s</a>',
			__( 'Support', 'facebook-page-feed-graph-api' )
		);
		return $links;
	}


	//Enqueue CSS and JS for admin
	public function facebook_page_plugin_admin_resources() {

		wp_enqueue_script( 'facebook-page-plugin-admin-scripts', CJW_FBPP_PLUGIN_URL . 'js/admin-global.js' );
		wp_enqueue_style( 'facebook-page-plugin-admin-styles', CJW_FBPP_PLUGIN_URL . 'css/admin-global.css' );

	}

	//Register the dashboard widget
	public function facebook_page_plugin_dashboard_widget() {

		if( current_user_can( self::dashboard_widget_capability() ) ) {
		
			wp_add_dashboard_widget( 'facebook-page-plugin-shortcode-generator', __( 'Mongoose Page Plugin Shortcode Generator', 'facebook-page-feed-graph-api' ), array( $this, 'facebook_page_plugin_dashboard_widget_callback' ) );

		}

	}

	//Load the dashboard widget
	function facebook_page_plugin_dashboard_widget_callback() {
		echo '<a name="cameronjonesweb_facebook_page_plugin_shortcode_generator"></a>';
		$generator = new cameronjonesweb_facebook_page_plugin_shortcode_generator();
		$generator->generate();
		
	}

	function facebook_page_plugin_landing_page_menu() {
		add_options_page( __( 'Mongoose Page Plugin by Mongoose Marketplace', 'facebook-page-feed-graph-api' ), 'Mongoose Page Plugin', 'install_plugins', 'facebook-page-plugin', array( $this, 'facebook_page_plugin_landing_page' ) );
	}

	function facebook_page_plugin_landing_page() {
		wp_enqueue_style( 'facebook-page-plugin-landing-page-css', CJW_FBPP_PLUGIN_URL . 'css/admin-landing-page.css', [], CJW_FBPP_PLUGIN_VER );
		wp_enqueue_style( 'facebook-page-plugin-google-fonts', 'https://fonts.googleapis.com/css?family=Rammetto+One|Paytone+One|Space+Mono:400|Muli:400,400i,700', [], CJW_FBPP_PLUGIN_VER );
		wp_enqueue_script( 'facebook-page-plugin-landing-page-js', CJW_FBPP_PLUGIN_URL . 'js/admin-landing-page.js', array( 'jquery' ), CJW_FBPP_PLUGIN_VER, true );
		include CJW_FBPP_PLUGIN_DIR . '/inc/landing-page.php';
	}

	function facebook_page_plugin_latest_blog_posts_callback() {
		$links = sprintf(
			'<p><a href="https://cameronjonesweb.com.au/blog/" target="_blank">%1$s</a> | <a href="https://mongoosemarketplace.com/news/" target="_blank">%2$s</a></p>',
			__( 'Developer\'s blog', 'facebook-page-feed-graph-api' ),
			__( 'Latest plugin news', 'facebook-page-feed-graph-api' )
		);
		wp_widget_rss_output( 'https://feed.rssunify.com/5b718c594e800/rss.xml', [ 'show_date' => 1 ] );
		wp_die( $links );
	}

	function facebook_page_plugin_activation_hook( $plugin ) {
		if( $plugin == CJW_FBPP_PLUGIN_BASENAME ) {
			exit( wp_redirect( admin_url( 'options-general.php?page=facebook-page-plugin' ) ) );
		}
	}


	// Client side stuff.
	function facebook_page_plugin_generate_wrapper_id() {
		return substr( str_shuffle( str_repeat( implode( '', array_merge( range( 'A', 'Z' ), range( 'a', 'z' ) ) ), 5 ) ), 0, 15 );
	}

	/**
	 * Parse shortcode
	 *
	 * @param array $filter Supplied shortcode attributes.
	 * @return string
	 */
	public function facebook_page_plugin( $filter ) {
		wp_enqueue_script( 'facebook-page-plugin-sdk', CJW_FBPP_PLUGIN_URL . 'js/sdk.js', array(), CJW_FBPP_PLUGIN_VER, true );
		wp_enqueue_script( 'facebook-page-plugin-responsive-script', CJW_FBPP_PLUGIN_URL . 'js/responsive.min.js', 'jquery', CJW_FBPP_PLUGIN_VER, true );
		$return = '';
		$a      = shortcode_atts(
			array(
				'href'            => null,
				'width'           => 340,
				'height'          => 130,
				'cover'           => null,
				'facepile'        => null,
				'posts'           => null,
				'tabs'            => array(),
				'language'        => get_bloginfo( 'language' ),
				'cta'             => null,
				'small'           => null,
				'adapt'           => null,
				'link'            => true,
				'linktext'        => null,
				'_implementation' => 'shortcode',
			),
			$filter
		);
		if ( isset( $a['href'] ) && ! empty( $a['href'] ) ) {
			$a['language'] = str_replace( '-', '_', $a['language'] );

			// Send the language as a parameter to the SDK.
			wp_localize_script( 'facebook-page-plugin-sdk', 'facebook_page_plugin_language', array( 'language' => esc_attr( $a['language'] ) ) );

			$return .= sprintf(
				'<div class="cameronjonesweb_facebook_page_plugin" data-version="%1$s" data-implementation="%2$s" id="%3$s">',
				esc_attr( CJW_FBPP_PLUGIN_VER ),
				esc_attr( $a['_implementation'] ),
				esc_attr( $this->facebook_page_plugin_generate_wrapper_id() )
			);
			$return .= sprintf(
				'<div id="fb-root"></div><script>(function(d, s, id) {var js, fjs = d.getElementsByTagName(s)[0];if (d.getElementById(id)) return;js = d.createElement(s); js.id = id;js.src = "//connect.facebook.net/' . $a['language'] . '/sdk.js#xfbml=1&version=v3.2&appId=%1$s";fjs.parentNode.insertBefore(js, fjs);	}(document, \'script\', \'facebook-jssdk\'));</script>',
				esc_attr( self::app_id() )
			);
			$return .= sprintf(
				'<div class="fb-page" data-href="https://facebook.com/%1$s" ',
				esc_attr( $a['href'] )
			);
			if ( isset( $a['width'] ) && ! empty( $a['width'] ) ) {
				$return .= sprintf(
					' data-width="%1$s" data-max-width="%1$s"',
					esc_attr( $a['width'] )
				);
			}
			if ( isset( $a['height'] ) && ! empty( $a['height'] ) ) {
				$return .= sprintf(
					' data-height="%1$s"',
					esc_attr( $a['height'] )
				);
			}
			if ( isset( $a['cover'] ) && ! empty( $a['cover'] ) ) {
				if ( 'false' == $a['cover'] ) {
					$return .= ' data-hide-cover="true"';
				} elseif ( 'true' == $a['cover'] ) {
					$return .= ' data-hide-cover="false"';
				}
			}
			if ( isset( $a['facepile'] ) && ! empty( $a['facepile'] ) ) {
				$return .= ' data-show-facepile="' . $a['facepile'] . '"';
			}
			if ( isset( $a['tabs'] ) && ! empty( $a['tabs'] ) ) {
				$return .= sprintf(
					' data-tabs="%1$s"',
					esc_attr( $a['tabs'] )
				);
			} elseif ( isset( $a['posts'] ) && ! empty( $a['posts'] ) ) {
				if ( 'true' == $a['posts'] ) {
					$return .= ' data-tabs="timeline"';
				} else {
					$return .= ' data-tabs="false"';
				}
			}
			if ( isset( $a['cta'] ) && ! empty( $a['cta'] ) ) {
				$return .= ' data-hide-cta="' . $a['cta'] . '"';
				$return .= sprintf(
					' data-hide-cta="%1$s"',
					esc_attr( $a['cta'] )
				);
			}
			if ( isset( $a['small'] ) && ! empty( $a['small'] ) ) {
				$return .= sprintf(
					' data-small-header="%1$s"',
					esc_attr( $a['small'] )
				);
			}
			if ( isset( $a['adapt'] ) && ! empty( $a['adapt'] ) ) {
				$return .= ' data-adapt-container-width="true"';
			} else {
				$return .= ' data-adapt-container-width="false"';
			}
			$return .= '><div class="fb-xfbml-parse-ignore">';
			if ( 'true' == $a['link'] ) {
				$return .= sprintf(
					'<blockquote cite="https://www.facebook.com/%1$s"><a href="https://www.facebook.com/%1$s">',
					esc_attr( $a['href'] )
				);
				if ( empty( $a['linktext'] ) ) {
					$return .= 'https://www.facebook.com/' . esc_attr( $a['href'] );
				} else {
					$return .= esc_html( $a['linktext'] );
				}
				$return .= '</a></blockquote>';
			}
			$return .= '</div></div></div>';
		}
		return $return;
	}

}

class cameronjonesweb_facebook_page_plugin_widget extends WP_Widget {
	
	private $facebookURLs = array( 'https://www.facebook.com/', 'https://facebook.com/', 'www.facebook.com/', 'facebook.com/', 'http://facebook.com/', 'http://www.facebook.com/' );
	private $settings;
	
	function __construct() {
		 $this->settings = new facebook_page_plugin_settings;
		parent::__construct( 'facebook_page_plugin_widget', __( 'Mongoose Page Plugin', 'facebook-page-feed-graph-api' ), array( 'description' => __( 'Generates a Facebook Page feed in your widget area', 'facebook-page-feed-graph-api' ), ) 	);
	}
	public function widget( $args, $instance ) {
		if( isset( $instance['title'] ) && !empty( $instance['title'] ) ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
		} else {
			$title = NULL;
		}
		if(isset($instance['href']) && !empty($instance['href'])){
			$href = $instance['href'];
			foreach($this->facebookURLs as $url){
				$href = str_replace($url, '', $href);
			}
		} else {
			$href = NULL;
		}
		if(isset($instance['width']) && !empty($instance['width'])){
			$width = $instance['width'];
		} else {
			$width = NULL;
		}
		if(isset($instance['height']) && !empty($instance['height'])){
			$height = $instance['height'];
		} else {
			$height = NULL;
		}
		if(isset($instance['cover']) && !empty($instance['cover'])){
			$cover = 'true';
		} else {
			$cover = 'false';
		}
		if(isset($instance['facepile']) && !empty($instance['facepile'])){
			$facepile = 'true';
		} else {
			$facepile = 'false';
		}
		if(isset($instance['tabs']) && !empty($instance['tabs'])){
			$tabs = $instance['tabs'];
		} else {
			$tabs = '';
		}
		if(isset($instance['cta']) && !empty($instance['cta'])){
			$cta = 'true';
		} else {
			$cta = 'false';
		}
		if(isset($instance['small']) && !empty($instance['small'])){
			$small = 'true';
		} else {
			$small = 'false';
		}
		if(isset($instance['adapt']) && !empty($instance['adapt'])){
			$adapt = 'true';
		} else {
			$adapt = 'false';
		}
		if(isset($instance['link']) && !empty($instance['link'])){
			$link = 'true';
		} else {
			$link = 'false';
		}
		if(isset($instance['linktext']) && !empty($instance['linktext'])){
			$linktext = $instance['linktext'];
		} else {
			$linktext = NULL;
		}
		if(isset($instance['language']) && !empty($instance['language'])){
			$language = $instance['language'];
		} else {
			$language = NULL;
		}
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		if( !empty($href )){
			$shortcode = '[facebook-page-plugin href="' . $href . '"';
			if( isset( $width ) && !empty( $width ) ){
				$shortcode .= ' width="' . $width . '"';
			}
			if( isset( $height ) && !empty( $height ) ){
				$shortcode .= ' height="' . $height . '"';
			}
			if( isset( $cover ) && !empty( $cover ) ){
				$shortcode .= ' cover="' . $cover . '"';
			}
			if( isset( $facepile ) && !empty( $facepile ) ){
				$shortcode .= ' facepile="' . $facepile . '"';
			}
			if( isset( $tabs ) && !empty( $tabs ) ){
				if( is_array( $tabs ) ) {
					$shortcode .= ' tabs="';
					for( $i = 0; $i < count( $tabs ); $i++ ) {
						$shortcode .= $tabs[$i];
						$shortcode .= ( $i != count( $tabs ) - 1 ? ',' : '' );
					}
					$shortcode .= '"';
				} else {
					$shortcode .= ' tabs="' . $tabs . '"';
				}
			}
			if( isset( $language ) && !empty( $language ) ){
				$shortcode .= ' language="' . $language . '"';
			}
			if( isset( $cta ) && !empty( $cta ) ){
				$shortcode .= ' cta="' . $cta . '"';
			}
			if( isset( $small ) && !empty( $small ) ){
				$shortcode .= ' small="' . $small . '"';
			}
			if( isset( $adapt ) && !empty( $adapt ) ){
				$shortcode .= ' adapt="' . $adapt . '"';
			}
			if( isset( $link ) && !empty( $link ) ){
				$shortcode .= ' link="' . $link . '"';
			}
			if( isset( $linktext ) && !empty( $linktext ) ){
				$shortcode .= ' linktext="' . $linktext . '"';
			}
			$shortcode .= ' _implementation="widget"';
			$shortcode .= ']';
			echo do_shortcode( $shortcode );
		}
		echo $args['after_widget'];
	} 
	public function form( $instance ) {

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'New title', 'facebook-page-feed-graph-api' );
		}
		if ( isset( $instance[ 'href' ] ) ) {
			$href = $instance[ 'href' ];
		} else {
			$href = '';
		}
		if ( isset( $instance[ 'width' ] ) ) {
			$width = $instance[ 'width' ];
		} else {
			$width = '';
		}
		if ( isset( $instance[ 'height' ] ) ) {
			$height = $instance[ 'height' ];
		} else {
			$height = '';
		}
		if ( isset( $instance[ 'cover' ] ) ) {
			$cover = $instance[ 'cover' ];
		} else {
			$cover = 'false';
		}
		if ( isset( $instance[ 'facepile' ] ) ) {
			$facepile = $instance[ 'facepile' ];
		} else {
			$facepile = 'false';
		}
		if ( isset( $instance[ 'tabs' ] ) ) {
			$tabs = $instance[ 'tabs' ];
		} else {
			$tabs = '';
		}
		if ( isset( $instance[ 'cta' ] ) ) {
			$cta = $instance[ 'cta' ];
		} else {
			$cta = 'false';
		}
		if ( isset( $instance[ 'small' ] ) ) {
			$small = $instance[ 'small' ];
		} else {
			$small = 'false';
		}
		if ( isset( $instance[ 'adapt' ] ) ) {
			$adapt = $instance[ 'adapt' ];
		} else {
			$adapt = 'true';
		}
		if ( isset( $instance[ 'link' ] ) ) {
			$link = $instance[ 'link' ];
		} else {
			$link = 'true';
		}
		if ( isset( $instance[ 'linktext' ] ) ) {
			$linktext = $instance[ 'linktext' ];
		} else {
			$linktext = '';
		}
		if ( isset( $instance[ 'language' ] ) ) {
			$language = $instance[ 'language' ];
		} else {
			$language = '';
		}

		$langs = $this->settings->get_locale_xml();

		echo cameronjonesweb_facebook_page_plugin::donate_notice();

		printf(
			'<p><label for="%1$s">%2$s</label><input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" /></p>',
			esc_attr( $this->get_field_id( 'title' ) ),
			esc_html__( 'Title:', 'facebook-page-feed-graph-api' ),
			esc_attr( $this->get_field_name( 'title' ) ),
			esc_attr( $title )
		);

		printf(
			'<p><label for="%1$s">%2$s</label><input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" /></p>',
			esc_attr( $this->get_field_id( 'href' ) ),
			esc_html__( 'Page URL:', 'facebook-page-feed-graph-api' ),
			esc_attr( $this->get_field_name( 'href' ) ),
			esc_attr( $href )
		);

		printf(
			'<p><label for="%1$s">%2$s</label><input class="widefat" id="%1$s" name="%3$s" type="number" min="180" max="500" value="%4$s" /></p>',
			esc_attr( $this->get_field_id( 'width' ) ),
			esc_html__( 'Width:', 'facebook-page-feed-graph-api' ),
			esc_attr( $this->get_field_name( 'width' ) ),
			esc_attr( $width )
		);

		printf(
			'<p><label for="%1$s">%2$s</label><input class="widefat" id="%1$s" name="%3$s" type="number" min="70" value="%4$s" /></p>',
			esc_attr( $this->get_field_id( 'height' ) ),
			esc_html__( 'Height:', 'facebook-page-feed-graph-api' ),
			esc_attr( $this->get_field_name( 'height' ) ),
			esc_attr( $height )
		);

		printf(
			'<p><label for="%1$s">%2$s</label> <input class="widefat" id="%1$s" name="%3$s" type="checkbox" value="true" %4$s /></p>',
			esc_attr( $this->get_field_id( 'cover' ) ),
			esc_html__( 'Cover Photo:', 'facebook-page-feed-graph-api' ),
			esc_attr( $this->get_field_name( 'cover' ) ),
			checked( esc_attr( $cover ), 'true', false )
		);

		printf(
			'<p><label for="%1$s">%2$s</label> <input class="widefat" id="%1$s" name="%3$s" type="checkbox" value="true" %4$s /></p>',
			esc_attr( $this->get_field_id( 'facepile' ) ),
			esc_html__( 'Show Facepile:', 'facebook-page-feed-graph-api' ),
			esc_attr( $this->get_field_name( 'facepile' ) ),
			checked( esc_attr( $facepile ), 'true', false )
		);

		echo '<p>';
		esc_html_e( 'Page Tabs:', 'facebook-page-feed-graph-api' );
		$cjw_fbpp_tabs = $this->settings->tabs();
		if ( ! empty( $cjw_fbpp_tabs ) ) {
			// First we should convert the string to an array as that's how it will be stored moving forward.
			if ( ! is_array( $tabs ) ) {
				$oldtabs = esc_attr( $tabs );
				$newtabs = explode( ',', $tabs );
				$tabs    = $newtabs;
			}
			foreach ( $cjw_fbpp_tabs as $tab ) {
				printf(
					'<br/><label><input type="checkbox" name="%1$s[%2$s]" %3$s /> %4$s</label>',
					esc_attr( $this->get_field_name( 'tabs' ) ),
					esc_attr( $tab ),
					in_array( $tab, $tabs, true ) ? 'checked' : '',
					esc_html( ucfirst( $tab ) )
				);
			}
		}
		echo '</p>';

		printf(
			'<p><label for="%1$s">%2$s</label> <input class="widefat" id="%1$s" name="%3$s" type="checkbox" value="true" %4$s /></p>',
			esc_attr( $this->get_field_id( 'cta' ) ),
			esc_html__( 'Hide Call To Action:', 'facebook-page-feed-graph-api' ),
			esc_attr( $this->get_field_name( 'cta' ) ),
			checked( esc_attr( $cta ), 'true', false )
		);

		printf(
			'<p><label for="%1$s">%2$s</label> <input class="widefat" id="%1$s" name="%3$s" type="checkbox" value="true" %4$s /></p>',
			esc_attr( $this->get_field_id( 'small' ) ),
			esc_html__( 'Small Header:', 'facebook-page-feed-graph-api' ),
			esc_attr( $this->get_field_name( 'small' ) ),
			checked( esc_attr( $small ), 'true', false )
		);

		printf(
			'<p><label for="%1$s">%2$s</label> <input class="widefat" id="%1$s" name="%3$s" type="checkbox" value="true" %4$s /></p>',
			esc_attr( $this->get_field_id( 'adapt' ) ),
			esc_html__( 'Adaptive Width:', 'facebook-page-feed-graph-api' ),
			esc_attr( $this->get_field_name( 'adapt' ) ),
			checked( esc_attr( $adapt ), 'true', false )
		);

		printf(
			'<p><label for="%1$s">%2$s</label> <input class="widefat" id="%1$s" name="%3$s" type="checkbox" value="true" %4$s /></p>',
			esc_attr( $this->get_field_id( 'link' ) ),
			esc_html__( 'Display link while loading:', 'facebook-page-feed-graph-api' ),
			esc_attr( $this->get_field_name( 'link' ) ),
			checked( esc_attr( $link ), 'true', false )
		);

		printf(
			'<p><label for="%1$s">%2$s</label><input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" /></p>',
			esc_attr( $this->get_field_id( 'linktext' ) ),
			esc_html__( 'Link text:', 'facebook-page-feed-graph-api' ),
			esc_attr( $this->get_field_name( 'linktext' ) ),
			esc_attr( $linktext )
		);

		printf(
			'<p><label for="%1$s">%2$s</label><input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" /></p>',
			esc_attr( $this->get_field_id( 'linktext' ) ),
			esc_html__( 'Link text:', 'facebook-page-feed-graph-api' ),
			esc_attr( $this->get_field_name( 'linktext' ) ),
			esc_attr( $linktext )
		);

		printf(
			'<p><label for="%1$s">%2$s</label><select class="widefat" id="%1$s" name="%3$s">%4$s</select></p>',
			esc_attr( $this->get_field_id( 'language' ) ),
			esc_html__( 'Language:', 'facebook-page-feed-graph-api' ),
			esc_attr( $this->get_field_name( 'language' ) ),
			call_user_func(
				function() use ( $langs, $language ) {
					$return = '<option value="">' . esc_html__( 'Site Language (default)', 'facebook-page-feed-graph-api' ) . '</option>';
					foreach ( $langs as $lang ) {
						$return .= sprintf(
							'<option value="%1$s" %2$s>%3$s</option>',
							esc_attr( $lang->standard->representation ),
							selected( esc_attr( $language ), $lang->standard->representation, false ),
							esc_html( $lang->englishName )
						);
					}
					return $return;
				}
			)
		);
	}
		
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		if ( ! empty( $new_instance['href'] ) ) {
			$href = strip_tags( $new_instance['href'] );
			$href = wp_http_validate_url( $href ) ? $href : 'https://facebook.com/' . $href;
			$instance['href'] = esc_url( $href );
		} else {
			$instance['href'] = '';
		}
		$instance['width'] = ( ! empty( $new_instance['width'] ) ) ? strip_tags( $new_instance['width'] ) : '';
		$instance['height'] = ( ! empty( $new_instance['height'] ) ) ? strip_tags( $new_instance['height'] ) : '';
		$instance['cover'] = ( ! empty( $new_instance['cover'] ) ) ? strip_tags( $new_instance['cover'] ) : '';
		$instance['facepile'] = ( ! empty( $new_instance['facepile'] ) ) ? strip_tags( $new_instance['facepile'] ) : '';
		if( !empty( $new_instance['tabs'] ) ) {
			if( is_array( $new_instance['tabs'] ) ) {
				foreach( $new_instance['tabs'] as $key => $var ) {
					$instance['tabs'][] = sanitize_text_field( $key );
				}
			}
		} else {
			$instance['tabs'] = '';
		}
		$instance['cta'] = ( ! empty( $new_instance['cta'] ) ) ? strip_tags( $new_instance['cta'] ) : '';
		$instance['small'] = ( ! empty( $new_instance['small'] ) ) ? strip_tags( $new_instance['small'] ) : '';
		$instance['adapt'] = ( ! empty( $new_instance['adapt'] ) ) ? strip_tags( $new_instance['adapt'] ) : '';
		$instance['link'] = ( ! empty( $new_instance['link'] ) ) ? strip_tags( $new_instance['link'] ) : '';
		$instance['linktext'] = ( ! empty( $new_instance['linktext'] ) ) ? strip_tags( $new_instance['linktext'] ) : '';
		$instance['language'] = ( ! empty( $new_instance['language'] ) ) ? strip_tags( $new_instance['language'] ) : '';
	return $instance;
	}

}

class cameronjonesweb_facebook_page_plugin_shortcode_generator {

	private $langs;
	private $settings;
	
	function __construct() {

		$this->settings = new facebook_page_plugin_settings;
		$this->langs = $this->settings->get_locale_xml();
	}

	function generate() {
		
		$return = NULL;

		$return .= cameronjonesweb_facebook_page_plugin::donate_notice();

		$return .= '<noscript>' . __( 'The shortcode generator requires JavaScript enabled', 'facebook-page-feed-graph-api' ) . '</noscript>';

		$return .= '<form>';
			$return .= '<p><label>' . __( 'Facebook Page URL', 'facebook-page-feed-graph-api' ) . ': <input type="url" id="fbpp-href" /></label></p>';
			$return .= '<p><label>' . __( 'Width (pixels)', 'facebook-page-feed-graph-api' ) . ': <input type="number" max="500" min="180" id="fbpp-width" /></label></p>';
			$return .= '<p><label>' . __( 'Height (pixels)', 'facebook-page-feed-graph-api' ) . ': <input type="number" min="70" id="fbpp-height" /></label></p>';
			$return .= '<p><label>' . __( 'Show Cover Photo', 'facebook-page-feed-graph-api' ) . ': <input type="checkbox" value="true" id="fbpp-cover" /></label></p>';
			$return .= '<p><label>' . __( 'Show Facepile', 'facebook-page-feed-graph-api' ) . ': <input type="checkbox" value="true" id="fbpp-facepile" /></label></p>';
			$return .= '<p><label>' . __( 'Page Tabs (formerly posts)', 'facebook-page-feed-graph-api' ) . ':';
			$settings = new facebook_page_plugin_settings;
			$CJW_FBPP_TABS = $settings->tabs();
			if( !empty( $CJW_FBPP_TABS ) ) {
				foreach( $CJW_FBPP_TABS as $tab ) {
					$return .= '<br/><label>';
						$return .= '<input type="checkbox" class="fbpp-tabs" name="' . $tab . '" /> ';
						$return .= __( ucfirst( $tab ), 'facebook-page-feed-graph-api' );
					$return .= '</label>';
				}
			 }
			$return .= '<p><label>' . __( 'Hide Call To Action', 'facebook-page-feed-graph-api' ) . ': <input type="checkbox" value="true" id="fbpp-cta" /></label></p>';
			$return .= '<p><label>' . __( 'Small Header', 'facebook-page-feed-graph-api' ) . ': <input type="checkbox" value="true" id="fbpp-small" /></label></p>';
			$return .= '<p><label>' . __( 'Adaptive Width', 'facebook-page-feed-graph-api' ) . ': <input type="checkbox" value="true" id="fbpp-adapt" checked /></label></p>';
			$return .= '<p><label>' . __( 'Display link while loading', 'facebook-page-feed-graph-api' ) . ': <input type="checkbox" value="true" id="fbpp-link" checked /></label></p>';
			$return .= '<p id="linktext-label"><label>' . __( 'Link text', 'facebook-page-feed-graph-api' ) . ': <input type="text" id="fbpp-linktext" /></label></p>';
			$return .= '<p><label>' . __( 'Language', 'facebook-page-feed-graph-api' ) . ': <select id="fbpp-lang"><option value="">' . __( 'Site Language', 'facebook-page-feed-graph-api' ) . '</option>';
			if(isset($this->langs) && !empty($this->langs)){
				foreach($this->langs as $lang){
					$return .= '<option value="' . $lang->standard->representation . '">' . __( $lang->englishName, 'facebook-page-feed-graph-api' ) . '</option>';
				}
			}
			$return .= '</select></label></p>';
			$return .= '<input type="text" readonly="readonly" id="facebook-page-plugin-shortcode-generator-output" onfocus="this.select()" />';
		$return .= '</form>';

		echo $return;
	}

}

class facebook_page_plugin_settings {

	public $tabs;

	function __construct() {
		$this->tabs = array( 'timeline', 'events', 'messages' );
	}

	function tabs() {
		return $this->tabs;
	}

	function get_locale_xml() {

		$admin_abspath = str_replace( site_url(), ABSPATH, admin_url() );

		include_once( $admin_abspath . '/includes/class-wp-filesystem-base.php' );
		include_once( $admin_abspath . '/includes/class-wp-filesystem-direct.php' );
		$wp_filesystem = new WP_Filesystem_Direct( null );

		try {
			//$xml = file_get_contents('https://www.facebook.com/translations/FacebookLocales.xml');
			//$xml = file_get_contents( CJW_FBPP_PLUGIN_URL ) . 'lang.xml');
			$lang_xml = $wp_filesystem->get_contents( CJW_FBPP_PLUGIN_DIR . '/lang.xml');
		} catch( Exception $ex ){
			$lang_xml = NULL;
		}

		if(isset($lang_xml) && !empty($lang_xml)){
			$langs = new SimpleXMLElement($lang_xml);
		} else {
			$langs = NULL;
		}

		return $langs;
	}

}

// Register the widget.
function facebook_page_plugin_load_widget() {
	register_widget( 'cameronjonesweb_facebook_page_plugin_widget' );
}
add_action( 'widgets_init', 'facebook_page_plugin_load_widget' );

$cameronjonesweb_facebook_page_plugin = new cameronjonesweb_facebook_page_plugin();
