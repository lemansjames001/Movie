<?php if(!defined('ABSPATH')) die;
/**
 * @package TMDB Updater > Application
 * @author Erick Meza
 * @copyright 2020 Doothemes and Dbmvs.com
 * @link https://doothemes.com/
 * @version 1.0.0
 */

class TMDbUpater extends DbmoviesTMDb{

    /**
     * @version 1.0
     * @since 1.0
     */
    function __construct(){
        add_action('admin_menu', array(&$this, 'register_sub_menu') );
        add_action('admin_enqueue_scripts', array($this,'scripts'), 20);
        add_action('wp_ajax_tmdb_metaupdater', array(&$this,'metaupdater'));
        add_action('wp_ajax_tmdb_calculator', array(&$this,'calculator'));
        add_action('wp_ajax_tmdb_reset', array(&$this,'reset'));
    }

    /**
     * @version 1.0
     * @since 1.0
     */
    public function scripts(){
        wp_enqueue_style('tmdb-updater-plugin', TMDB_PLUGIN_URL.'assets/app'.$this->minify().'.css', array(), '1.0', 'all' );
        wp_enqueue_script('tmdb-updater-plugin', TMDB_PLUGIN_URL.'assets/app'.$this->minify().'.js', array('jquery'), '1.0', false );
        wp_localize_script('tmdb-updater-plugin', 'tmdbupdater', array(
            'url' => admin_url('admin-ajax.php','relative'),
            'processing' => __('Processing','tmdb'),
            'completed' => __('Completed Process','tmdb')
        ));
    }

    /**
     * @version 1.0
     * @since 1.0
     */
    public function metaupdater(){
        // Set Response
        $response = array('success' => false);
        if(current_user_can('administrator')){
            // Post data
            $page  = isset($_REQUEST['page']) ? $_REQUEST['page'] : false;
            $total = isset($_REQUEST['total']) ? $_REQUEST['total'] : false;
            // Update Pager
            if($page == $total){
                update_option('_tmdb_updater_page','1');
                delete_option('_tmdb_counter_posts');
            } else {
                update_option('_tmdb_updater_page', $page);
            }
            // Compose Response
            if($page){
                $response = DbmoviesTMDb::updater($page);
            }
        }
        // Return
        wp_send_json(apply_filters('tmdb_metaupdater', $response, $page));
    }


    /**
     * @version 1.0
     * @since 1.0
     */
    public function calculator(){
        // Set Response
        $number   = 0;
        $response = array('success' => false);
        $nonce    = isset($_POST['nonce']) ? $_POST['nonce'] : false;
        if(current_user_can('administrator') && wp_verify_nonce($nonce,'tmdb_calculator_processes')){
            // Delete Option
            delete_option('_tmdb_counter_posts');
            // Calculator
            $number = DbmoviesTMDb::calculate_processes();
            // Verify Number
            if($number){
                $response = array(
                    'success'   => true,
                    'processes' => $number
                );
            }
        }
        // Return
        wp_send_json(apply_filters('tmdb_calculator', $response, $number));
    }

    /**
     * @version 1.0
     * @since 1.0
     */
    public function reset(){
        $response = array('success' => false);
        $nonce    = isset($_POST['nonce']) ? $_POST['nonce'] : false;
        if(current_user_can('administrator') && wp_verify_nonce($nonce,'tmdb_reset_processes')){
            update_option('_tmdb_updater_page','1');
            delete_option('_tmdb_counter_posts');
            $response = array(
                'success' => true
            );
        }
        // Return
        wp_send_json(apply_filters('tmdb_reset', $response, $nonce));
    }

    /**
     * @version 1.0
     * @since 1.0
     */
    public function register_sub_menu(){
        add_submenu_page(
            'tools.php',
            __('TMDb Updater','tmdb'),
            __('TMDb Updater','tmdb'),
            'manage_options',
            'dbmvs-updater',
            array(&$this,'admin_template')
        );
    }

    /**
     * @version 1.0
     * @since 1.0
     */
    public function admin_template(){
        // Get Page
        $current_page = get_option('_tmdb_updater_page','1');
        $count_tposts = get_option('_tmdb_counter_posts','0');
        $progress     = ($count_tposts > 1) ? round(($current_page/$count_tposts*100), 0) : 0;
        $button_text  = ($current_page == 1) ? __('Start update','tmdb') : __('Continue updating','tmdb');
        // Load Template
        include_once(TMDB_PLUGIN_DIR.'includes/tool.php');
    }

    /**
     * @since 1.0
     * @version 1.0
     */
    private function minify(){
        return (apply_filters('tmdb_dev_mode', false ) || WP_DEBUG ) ? '' : '.min';
    }

}

new TMDbUpater;
