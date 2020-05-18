<?php if(!defined('ABSPATH')) die;
/**
 * @package TMDB Updater > Classes > TMDbAPI
 * @author Erick Meza
 * @copyright 2020 Doothemes and Dbmvs.com
 * @link https://doothemes.com/
 * @version 1.0.0
 */

class TMDbAPI{

    /**
     * @version 1.0
     * @since 1.0
     */
    protected static $api_key;
    protected static $api_lng;
    protected static $api_tim;

    /**
     * @version 1.0
     * @since 1.0
     */
    public static function get_content($post_id, $post_type){
        // Get Options
        $options = get_option('_dbmovies_settings');
        // Compose options
        self::$api_tim = microtime(TRUE);
        self::$api_key = self::c_isset($options,' themoviedb', TMDB_API_KEY);
        self::$api_lng = self::c_isset($options, 'language', TMDB_API_LNG);
        // Set Response
        $response = array();
        // Verifications
        if(!empty($post_id) && !empty($post_type)){
            switch ($post_type) {
                case 'movies':
                    $response = self::movies($post_id);
                break;

                case 'tvshows':
                    $response = self::tvshows($post_id);
                break;

                case 'seasons':
                    $response = self::seasons($post_id);
                break;

                case 'episodes':
                    $response = self::episodes($post_id);
                break;

                default:
                    $response = array(
                        'success' => false,
                        'message' => __('Unknown error','tmdb')
                    );
                    break;
            }
        } else {
            $response = array(
                'success' => false,
                'message' => __('Incomplete data','tmdb')
            );
        }
        // The Response
        return $response;
    }

    /**
     * @version 1.0
     * @since 1.0
     */
    private static function c_isset($data = array(), $key = '', $default = null){
        return isset($data[$key]) ? $data[$key] : $default;
    }

    /**
     * @version 1.0
     * @since 1.0
     */
    private static function images($tmdb_images = array()){
        $images = '';
        if($tmdb_images){
            $image_count = 0;
            foreach($tmdb_images as $image) if($image_count < 10){
                if($image_count == 9){
                    $images.= self::c_isset($image,'file_path');
                }else{
                    $images.= self::c_isset($image,'file_path')."\n";
                }
                $image_count++;
            }
        }
        return $images;
    }

    /**
     * @version 1.0
     * @since 1.0
     */
    private static function arguments(){
        return array(
            'append_to_response'     => 'images,trailers',
            'include_image_language' => self::$api_lng.',null',
            'language'               => self::$api_lng,
            'api_key'                => self::$api_key
        );
    }

    /**
     * @version 1.0
     * @since 1.0
     */
    private static function remote($api = '', $args = array()){
        $sapi = esc_url_raw(add_query_arg($args, $api));
        $json = wp_remote_retrieve_body(wp_remote_get($sapi));
        return json_decode($json, true);
    }

    /**
     * @since 1.0
     * @version 1.0
     */
    private static function timexe($time = ''){
        $micro	= microtime(TRUE);
		return number_format($micro - $time, 2);
    }


    private static function successfully($post_id = '', $post_type = ''){
        return array(
            'success'   => true,
            'post_id'   => $post_id,
            'permalink' => get_permalink($post_id),
            'post_tile' => get_the_title($post_id),
            'post_type' => $post_type,
            'exetime'   => self::timexe(self::$api_tim)
        );
    }

    /**
     * @version 1.0
     * @since 1.0
     */
    private static function movies($post_id = ''){
        // Set Response
        $response = array();
        // Post Meta data
        $finder = get_post_meta($post_id, 'ids', true);
        // Verify ID finder
        if(!$finder){
            $finder = get_post_meta($post_id, 'idtmdb', true);
        }
        // Verify the Finder ID
        if(!empty($finder)){
            // TMDb Json data
            $json_tmdb = self::remote(TMDB_API_URL.'/3/movie/'.$finder, self::arguments());
            // verificate
            if(!self::c_isset($json_tmdb,'status_code')){
                // Set TMDb Metada
                $release   = self::c_isset($json_tmdb,'release_date');
                $ortitle   = self::c_isset($json_tmdb,'original_title');
                $poster    = self::c_isset($json_tmdb,'poster_path');
                $backdrop  = self::c_isset($json_tmdb,'backdrop_path');
                $average   = self::c_isset($json_tmdb,'vote_average');
                $votecount = self::c_isset($json_tmdb,'vote_count');
                $tagline   = self::c_isset($json_tmdb,'tagline');
                $runtime   = self::c_isset($json_tmdb,'runtime');
                $backdrops = isset($json_tmdb['images']['backdrops']) ? $json_tmdb['images']['backdrops'] : false;
                $trailers  = isset($json_tmdb['trailers']['youtube']) ? $json_tmdb['trailers']['youtube'] : false;
                // Set Images
                $images = self::images($backdrops);
                // Set Video Traiter
                $youtube = '';
                if($trailers){
                    foreach($trailers as $trailer){
                        $youtube .= '['.self::c_isset($trailer,'source').']';
                        break;
                    }
                }
                // Compose Postmeta
                $post_meta = array(
                    'dt_poster'      => $poster,
                    'dt_backdrop'    => $backdrop,
                    'imagenes'       => $images,
                    'youtube_id'     => $youtube,
                    'original_title' => $ortitle,
                    'release_date'   => $release,
                    'vote_average'   => $average,
                    'vote_count'     => $votecount,
                    'tagline'        => $tagline,
                    'runtime'        => $runtime
                );
                // Update Postmeta
                foreach($post_meta as $meta => $value){
                    if($meta == 'imagenes'){
                        if(!empty($value)) update_post_meta($post_id, $meta, esc_attr($value));
                    }else{
                        if(!empty($value)) add_post_meta($post_id, $meta, sanitize_text_field($value));
                    }
                }
                // The response
                $response = self::successfully( $post_id, __('movie','tmdb'));
            } else {
                $response = array(
                    'success' => false,
                    'message' => self::c_isset($json_tmdb,'status_message')
                );
            }
        } else {
            $response = array(
                'success' => false,
                'message' => __('Undefined TMDb ID','tmdb')
            );
        }
        // The response
        return $response;
    }

    /**
     * @version 1.0
     * @since 1.0
     */
    private static function tvshows($post_id = ''){
        // Set Response
        $response = array();
        // Post Meta data
        $finder = get_post_meta($post_id, 'ids', true);
        // Verify the Finder ID
        if(!empty($finder)){
            // TMDb Json data
            $json_tmdb = self::remote(TMDB_API_URL.'/3/tv/'.$finder, self::arguments());
            // verificate
            if(!self::c_isset($json_tmdb,'status_code')){
                // Get videos
                $json_tmdb_videos = self::remote(TMDB_API_URL.'/3/tv/'.$finder.'/videos', self::arguments());
                // Set TMDb Metada
                $orname     = self::c_isset($json_tmdb,'original_name');
                $firstdate  = self::c_isset($json_tmdb,'first_air_date');
                $lastdate   = self::c_isset($json_tmdb,'last_air_date');
                $epiruntime = self::c_isset($json_tmdb,'episode_run_time');
                $poster     = self::c_isset($json_tmdb,'poster_path');
                $backdrop   = self::c_isset($json_tmdb,'backdrop_path');
                $average    = self::c_isset($json_tmdb,'vote_average');
                $votecount  = self::c_isset($json_tmdb,'vote_count');
                $seasons    = self::c_isset($json_tmdb,'number_of_seasons');
                $episodes   = self::c_isset($json_tmdb,'number_of_episodes');
                $trailers   = self::c_isset($json_tmdb_videos,'results');
                $backdrops  = isset($json_tmdb['images']['backdrops']) ? $json_tmdb['images']['backdrops'] : false;
                // Set Images
                $images = self::images($backdrops);
                // Set Video Traiter
                $youtube = '';
                if($trailers){
                    foreach($trailers as $trailer){
                        $youtube .= '['.self::c_isset($trailer,'key').']';
                        break;
                    }
                }
                // Set Runtime
                $runtime = '';
                if($epiruntime){
                    foreach($epiruntime as $time){
                        $runtime .= $time;
                        break;
                    }
                }
                // Compose Postmeta
                $post_meta = array(
                    'imagenes'           => $images,
                    'youtube_id'         => $youtube,
                    'episode_run_time'   => $runtime,
                    'dt_poster'          => $poster,
                    'dt_backdrop'        => $backdrop,
                    'first_air_date'     => $firstdate,
                    'last_air_date'      => $lastdate,
                    'number_of_episodes' => $seasons,
                    'number_of_seasons'  => $episodes,
                    'original_name'      => $orname,
                    'imdbRating'         => $average,
                    'imdbVotes'          => $votecount,
                );
                // Update Postmeta
                foreach($post_meta as $meta => $value){
                    if($meta == 'imagenes'){
                        if(!empty($value)) update_post_meta($post_id, $meta, esc_attr($value));
                    }else{
                        if(!empty($value)) add_post_meta($post_id, $meta, sanitize_text_field($value));
                    }
                }
                // The response
                $response = self::successfully( $post_id, __('show','tmdb') );
            } else {
                $response = array(
                    'success' => false,
                    'message' => self::c_isset($json_tmdb,'status_message')
                );
            }
        } else {
            $response = array(
                'success' => false,
                'message' => __('Undefined TMDb ID','tmdb')
            );
        }
        // The response
        return $response;
    }

    /**
     * @version 1.0
     * @since 1.0
     */
    private static function seasons($post_id = ''){
        // Set Response
        $response = array();
        // Post Meta data
        $finder = get_post_meta($post_id,'ids',true);
        $season = get_post_meta($post_id,'temporada',true);
        // Verifications
        if(!empty($finder) && !empty($season)){
            // TMDb Json data
            $json_tmdb = self::remote(TMDB_API_URL.'/3/tv/'.$finder.'/season/'.$season, self::arguments());
            // verificate
            if(!self::c_isset($json_tmdb,'status_code')){
                // get Data
                $poster  = self::c_isset($json_tmdb,'poster_path');
                $airdate = self::c_isset($json_tmdb,'air_date');
                // Update postmeta
                if(!empty($poster)) add_post_meta($post_id, 'dt_poster', sanitize_text_field($poster), false);
                if(!empty($airdate)) add_post_meta($post_id, 'air_date', sanitize_text_field($airdate), false);
                // The response
                $response = self::successfully( $post_id, __('season','tmdb') );
            }else{
                $response = array(
                    'success' => false,
                    'message' => self::c_isset($json_tmdb,'status_message')
                );
            }
        } else {
            $response = array(
                'success' => false,
                'message' => __('Undefined data','tmdb')
            );
        }
        // The response
        return $response;
    }

    /**
     * @version 1.0
     * @since 1.0
     */
    private static function episodes($post_id = ''){
        // Set Response
        $response = array();
        // Post Meta data
        $finder  = get_post_meta($post_id,'ids',true);
        $season  = get_post_meta($post_id,'temporada',true);
        $episode = get_post_meta($post_id,'episodio',true);
        // Verifications
        if(!empty($finder) && !empty($season) && !empty($episode)){
            // TMDb Json data
            $json_tmdb = self::remote(TMDB_API_URL.'/3/tv/'.$finder.'/season/'.$season.'/episode/'.$episode, self::arguments());
            // verificate
            if(!self::c_isset($json_tmdb,'status_code')){
                $airdate   = self::c_isset($json_tmdb,'air_date');
                $backdrop  = self::c_isset($json_tmdb,'still_path');
                $backdrops = isset($json_tmdb['images']['stills']) ? $json_tmdb['images']['stills'] : false;
                // Compose Images
                $images = '';
                if($backdrops){
                    $image_count = 0;
                    foreach($backdrops as $image) if($image_count < 10){
                        if($image_count == 9){
                            $images.= self::c_isset($image,'file_path');
                        }else{
                            $images.= self::c_isset($image,'file_path')."\n";
                        }
                        $image_count++;
                    }
                }
                // Update postmeta
                if(!empty($backdrop)) add_post_meta($post_id, 'dt_backdrop', sanitize_text_field($backdrop), false);
                if(!empty($airdate)) add_post_meta($post_id, 'air_date', sanitize_text_field($airdate), false);
                if(!empty($images)) add_post_meta($post_id, 'imagenes', esc_attr($images), false);
                // The response
                $response = self::successfully( $post_id, __('episode','tmdb') );
            }else{
                $response = array(
                    'success' => false,
                    'message' => self::c_isset($json_tmdb,'status_message')
                );
            }
        } else {
            $response = array(
                'success' => false,
                'message' => __('Undefined data','tmdb')
            );
        }
        // The response
        return $response;
    }
}
