<?php if(!defined('ABSPATH')) die;
/**
 * @package TMDB Updater > Classes > Dbmovies
 * @author Erick Meza
 * @copyright 2020 Doothemes and Dbmvs.com
 * @link https://doothemes.com/
 * @version 1.0.0
 */

class DbmoviesTMDb extends TMDbAPI{

    /**
     * @version 1.0
     * @since 1.0
     */
    public static function updater($page = 1){
        // Set page
        // Get Post data
        $post = self::get_post_id($page);
        // Compose data
        $post_id   = isset($post[0]['ID']) ? $post[0]['ID'] : false;
        $post_type = isset($post[0]['post_type']) ? $post[0]['post_type'] : false;
        // Get Remote data
        return self::get_content($post_id, $post_type);
    }

    /**
     * @version 1.0
     * @since 1.0
     */
    public static function calculate_processes(){
        // get total posts
        $total_posts = get_option('_tmdb_counter_posts');
        // Verify
        if(!$total_posts){
            global $wpdb;
            $total_posts = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type in ('movies','tvshows','seasons','episodes')");
            update_option('_tmdb_counter_posts',$total_posts);
        }
        // Get counter
		return $total_posts;
    }

    /**
     * @version 1.0
     * @since 1.0
     */
    private static function get_post_id($page){
        global $wpdb;
        $perpage = 1;
        $sql  = "SELECT ID, post_type FROM $wpdb->posts ";
        $sql .= "WHERE post_type in ('movies','tvshows','seasons','episodes') ";
        $sql .= "AND post_status = 'publish' ORDER BY ID DESC ";
        $sql .= "LIMIT {$perpage} OFFSET ".($page - 1) * $perpage;
        $query = $wpdb->get_results($sql, 'ARRAY_A');
        return $query;
    }
}
