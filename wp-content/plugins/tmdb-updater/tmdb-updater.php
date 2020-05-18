<?php if(!defined('ABSPATH')) die;
/**
 * Plugin Name: TMDb Updater
 * Plugin URI: http://doothemes.com/items/dooplay/
 * Author: Doothemes
 * Author URI: http://doothemes.com/
 * Version: 1.0.1
 * Description: This tool will allow you to correct and update metadata for Dbmovies content.
 * Text Domain: tmdb
 * Domain Path: /languages
 *
 */

// Main Constants
defined('TMDB_PLUGIN_DIR') or define('TMDB_PLUGIN_DIR', plugin_dir_path(__FILE__));
defined('TMDB_PLUGIN_URL') or define('TMDB_PLUGIN_URL', plugin_dir_url(__FILE__));

// TMDb Constants
defined('TMDB_API_URL') or define('TMDB_API_URL','https://api.themoviedb.org');
defined('TMDB_API_KEY') or define('TMDB_API_KEY','05902896074695709d7763505bb88b4d');
defined('TMDB_API_LNG') or define('TMDB_API_LNG','en-US');

// Load Application
if(!class_exists('TMDbUpater')){
    // Load Text domain
    load_textdomain('tmdb', TMDB_PLUGIN_DIR.'languages/tmdb-'.get_locale().'.mo');
    // All files
    include_once(TMDB_PLUGIN_DIR.'includes/classes/tmdb.php');
    include_once(TMDB_PLUGIN_DIR.'includes/classes/dbmovies.php');
    include_once(TMDB_PLUGIN_DIR.'includes/app.php');
} else {
    add_action('admin_notices', 'require_doothemes_item');
}

// Requiere DooPlay
function require_doothemes_item(){
    $out_html  = '<div class="notice notice-warning is-dismissible"><p>';
    $out_html .= __('This tool is only compatible with Dbmovies','tmdb');
    $out_html .= '</p></div>';
    echo $out_html;
}
// End Application
