<?php
/*
Plugin Name: WP Blog and Widget
Plugin URL: https://www.wponlinesupport.com/plugins/
Text Domain: wp-blog-and-widgets
Domain Path: /languages/
Description: Display Blog on your website with list and in grid view. Also work with Gutenberg shortcode block.
Version: 1.8.1
Author: WP OnlineSupport
Author URI: https://www.wponlinesupport.com/
Contributors: WP OnlineSupport
*/

if( !defined( 'WPBW_VERSION' ) ) {
	define( 'WPBW_VERSION', '1.8.1' ); // Version of plugin
}
if( !defined( 'WPBAW_DIR' ) ) {
	define( 'WPBAW_DIR', dirname( __FILE__ ) ); // Plugin dir
}
if( !defined( 'WPBAW_URL' ) ) {
	define( 'WPBAW_URL', plugin_dir_url( __FILE__ ) ); // Plugin url
}
if( !defined( 'WPBAW_PLUGIN_BASENAME' ) ) {
	define( 'WPBAW_PLUGIN_BASENAME', plugin_basename( __FILE__ ) ); // Plugin base name
}
if( !defined( 'WPBAW_POST_TYPE' ) ) {
	define( 'WPBAW_POST_TYPE', 'blog_post' ); // Plugin post type
}
if( !defined( 'WPBAW_CAT' ) ) {
	define( 'WPBAW_CAT', 'blog-category' ); // Plugin category name
}

register_activation_hook( __FILE__, 'freeBlog_install_premium_version' );
function freeBlog_install_premium_version(){
if( is_plugin_active('wp-blog-and-widgets-pro/wp-blog-and-widgets.php') ){
	 add_action('update_option_active_plugins', 'freeBlog_deactivate_premium_version');
	}
}
function freeBlog_deactivate_premium_version(){
   deactivate_plugins('wp-blog-and-widgets-pro/wp-blog-and-widgets.php',true);
}
add_action( 'admin_notices', 'freeBlog_rpfs_admin_notice');
function freeBlog_rpfs_admin_notice() {

	global $pagenow;

	$dir = ABSPATH . 'wp-content/plugins/wp-blog-and-widgets-pro/wp-blog-and-widgets.php';
	$notice_link        = add_query_arg( array('message' => 'wpbawh-plugin-notice'), admin_url('plugins.php') );
	$notice_transient   = get_transient( 'wpbawh_install_notice' );

	if( $notice_transient == false && $pagenow == 'plugins.php' && file_exists( $dir ) && current_user_can( 'install_plugins' ) ) {        
		echo '<div class="updated notice" style="position:relative;">
			<p>
				<strong>'.sprintf( __('Thank you for activating %s', 'wp-blog-and-widgets'), 'WP Blog and Widget').'</strong>.<br/>
				'.sprintf( __('It looks like you had PRO version %s of this plugin activated. To avoid conflicts the extra version has been deactivated and we recommend you delete it.', 'wp-blog-and-widgets'), '<strong>(<em>WP Blog and Widget Pro</em>)</strong>' ).'
			</p>
			<a href="'.esc_url( $notice_link ).'" class="notice-dismiss" style="text-decoration:none;"></a>
		</div>';
	}
}

add_action('plugins_loaded', 'wpbaw_blog_load_textdomain');
function wpbaw_blog_load_textdomain() {
	load_plugin_textdomain( 'wp-blog-and-widgets', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}
// Initialization function
add_action('init', 'wpbaw_blog_init');
function wpbaw_blog_init() {
  // Create new News custom post type
	$wpbaw_blog_labels = array(
	'name'                 => _x('Blog', 'wp-blog-and-widgets'),
	'singular_name'        => _x('blog', 'wp-blog-and-widgets'),
	'add_new'              => _x('Add Blog', 'wp-blog-and-widgets'),
	'add_new_item'         => __('Add New Blog', 'wp-blog-and-widgets'),
	'edit_item'            => __('Edit Blog', 'wp-blog-and-widgets'),
	'new_item'             => __('New Blog', 'wp-blog-and-widgets'),
	'view_item'            => __('View Blog', 'wp-blog-and-widgets'),
	'search_items'         => __('Search Blog', 'wp-blog-and-widgets'),
	'not_found'            =>  __('No Blog Items found', 'wp-blog-and-widgets'),
	'not_found_in_trash'   => __('No Blog Items found in Trash', 'wp-blog-and-widgets'), 
	'_builtin'             =>  false, 
	'parent_item_colon'    => '',    
	'menu_name'              => _x('Blog', 'wp-blog-and-widgets')
  );
  $wpbaw_blog_args = array(
	'labels'              => $wpbaw_blog_labels,
	'public'              => true,
	'publicly_queryable'  => true,
	'exclude_from_search' => false,
	'show_ui'             => true,
	'show_in_menu'        => true, 
	'query_var'           => true,
	'rewrite'             => array( 
							'slug' => 'blog-post',
							'with_front' => false
							),
	'capability_type'     => 'post',
	'has_archive'         => true,
	'hierarchical'        => false,
	'menu_position'       => 5,
	'menu_icon'   		  => 'dashicons-feedback',
	'supports'            => array('title','editor','thumbnail','excerpt','comments','author'),
	'show_in_rest'		  => true,
	'taxonomies'          => array('post_tag')
  );
 
  register_post_type( 'blog_post', apply_filters( 'wpbaw_blog_registered_post_type_args', $wpbaw_blog_args ) );
}
/* Register Taxonomy */
add_action( 'init', 'wpbaw_blog_taxonomies');
function wpbaw_blog_taxonomies() {
	$labels = array(
		'name'              => _x( 'Category', 'wp-blog-and-widgets' ),
		'singular_name'     => _x( 'Category', 'wp-blog-and-widgets' ),
		'search_items'      => __( 'Search Category', 'wp-blog-and-widgets' ),
		'all_items'         => __( 'All Category', 'wp-blog-and-widgets' ),
		'parent_item'       => __( 'Parent Category', 'wp-blog-and-widgets' ),
		'parent_item_colon' => __( 'Parent Category:', 'wp-blog-and-widgets' ),
		'edit_item'         => __( 'Edit Category', 'wp-blog-and-widgets' ),
		'update_item'       => __( 'Update Category', 'wp-blog-and-widgets' ),
		'add_new_item'      => __( 'Add New Category', 'wp-blog-and-widgets' ),
		'new_item_name'     => __( 'New Category Name', 'wp-blog-and-widgets' ),
		'menu_name'         => __( 'Blog Category', 'wp-blog-and-widgets' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'show_in_rest'		=> true,
		'rewrite'           => array( 'slug' => 'blog-category' ),
	);

	register_taxonomy( 'blog-category', array( 'blog_post' ), $args );
}

function wpbaw_blog_rewrite_flush() {  
		wpbaw_blog_init();  
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'wpbaw_blog_rewrite_flush' );
add_action( 'wp_enqueue_scripts','wpbaw_blog_css_script' );
	function wpbaw_blog_css_script() {
		wp_enqueue_style( 'cssblog',  plugin_dir_url( __FILE__ ). 'css/styleblog.css', array(), WPBW_VERSION );
	}

/* Added Widgets */	
require_once( 'blog-widgets.php' );


/* Page short code [blog limit="10"] */

function get_wpbaw_blog( $atts, $content = null ){
			// setup the query
			extract(shortcode_atts(array(
		"limit"                 => 10,
		"category"              => '',
		"grid"                  => 0,
		"show_date"             => 'true',
		"show_category_name"    => 'true',
		"show_author"           => 'true',
		"show_content"          => 'true',
		"show_full_content"     => 'false',
		"content_words_limit"   => 20,
		"pagination_type"       => 'numeric',
		"order"					=> 'DESC',
		"orderby"				=> 'date',
	), $atts));
	
	// Define variables 
	
	$posts_per_page 	= !empty($limit) 					? $limit 						: 10;	
	$cat 				= (!empty($category)) 				? explode(',', $category) 		: '';	
	$gridcol 			= !empty($grid) 					? $grid 						: 0;
	$showDate 			= ( $show_date == 'false' ) 		? 'false' 						: 'true';
	$showCategory 		= ( $show_category_name == 'false' )? 'false' 						: 'true';	
	$showContent 		= ( $show_content == 'false' ) 		? 'false' 						: 'true';
	$showAuthor   		= ( $show_author == 'false' ) 		? 'false' 						: 'true';
	$showFullContent   	= ( $show_full_content == 'false' ) ? 'false' 						: 'true';	
	$words_limit 		= (!empty($content_words_limit)) 	? $content_words_limit 			: 20;
	$pagination_type   	= ( $pagination_type == 'numeric' ) ? 'numeric' 					: 'next-prev';	
	$order 				= ( strtolower( $order ) == 'asc' ) 	? 'ASC' 						: 'DESC';
	$orderby 			= !empty( $orderby )					? $orderby 						: 'date';


	ob_start();
	
	global $paged;
	if(is_home() || is_front_page()) {
		  $paged = get_query_var('page');
	} else {
		 $paged = get_query_var('paged');
	}
	
	$post_type 		= 'blog_post';	
				 
	$args = array ( 
		'post_type'      => $post_type, 
		'orderby'        => $orderby, 
		'order'          => $order,
		'posts_per_page' => $posts_per_page,   
		'paged'          => $paged,
	);
	if($cat != ""){
		$args['tax_query'] = array( 
			array( 
				'taxonomy' => 'blog-category', 
				'field' => 'term_id', 
				'terms' => $cat
			)
		 );
	}        
	
	$query = new WP_Query($args);
	
	global $post;
	$post_count = $query->post_count;
	$count = 0; ?>
	<div class="blogfree-plugin blog-clearfix">
	<?php    
	if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();        
		$count++;
		$terms = get_the_terms( $post->ID, 'blog-category' );
		$news_links = array();
		
		if($terms){

			foreach ( $terms as $term ) {
				$term_link = get_term_link( $term );
				$news_links[] = '<a href="' . esc_url( $term_link ) . '">'.$term->name.'</a>';
			}
		}
		
		$cate_name = join( ", ", $news_links );
		$css_class="wpbaw-blog-class";
		
		if ( ( is_numeric( $grid ) && ( $grid > 0 ) && ( 0 == ($count - 1) % $grid ) ) || 1 == $count ) { $css_class .= ' wpbaw-first'; }
		if ( ( is_numeric( $grid ) && ( $grid > 0 ) && ( 0 == $count % $grid ) ) || $post_count == $count ) { $css_class .= ' wpbaw-last'; }
		if($showDate == 'true'){ $date_class = "has-date";}else{$date_class = "has-no-date";} ?>
			
		<div id="post-<?php the_ID(); ?>" class="blog type-blog blog-clearfix <?php echo (has_post_thumbnail()) ? "has-thumb" : "no-thumb";?> blog-col-<?php echo $gridcol.' '.$css_class.' '.$date_class; ?>">			
			<div class="blog-inner-wrap-view blog-clearfix">
			<?php if ( has_post_thumbnail()) { ?>                
				<div class="blog-thumb">                
					<?php if($gridcol == '1'){ ?>        				
						<div class="grid-blog-thumb">        				    
							<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('url'); ?></a>
						</div>
					<?php } else if($gridcol > '2') { ?>    					
						<div class="grid-blog-thumb">    				        
							<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large'); ?></a>
						</div>
					<?php } else if($gridcol == '0') { ?>        			
						<div class="grid-blog-thumb">
							<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large'); ?></a>
						</div>
					<?php } else { ?>    			
						<div class="grid-blog-thumb">
							<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large'); ?></a>
						</div>
					<?php } ?>
				</div>
			<?php } ?>			
			<div class="blog-content">
				<?php if($gridcol == '1') {                    
					if($showDate == 'true'){?>    				
						<div class="date-post">
							<h2><span><?php echo get_the_date('j'); ?></span></h2>
							<p><?php echo get_the_date('M y'); ?></p>
						</div>
					 <?php } ?>
				<?php } else { ?>    				
					<div class="grid-category-post">                       
						<?php echo ($showCategory == 'true' && $cate_name != '') ? $cate_name : ""?>
					</div>    			
					<?php if($showAuthor == 'true' || $showDate == 'true'){ ?>            			
						<div class="blog-author">                			
							<?php if($showAuthor == 'true') {?>                    		
								<span>
									<?php esc_html_e( 'By', 'wp-blog-and-widgets' ); ?> <?php the_author(); ?>
								</span>
							<?php }?>
							<?php echo ($showAuthor == 'true' && $showDate == 'true') ? '/' : '' ?>
							<?php echo ($showDate == "true")? get_the_date() : "" ;?>
						</div>
					<?php } 
				}?>
				<div class="post-content-text">        			
					<?php if($gridcol == '1'){ ?>        			    
						<div class="grid-1-date">        				
							<?php echo ($showDate == "true")? get_the_date() : "" ;?>
						</div>
					<?php } ?>        			
					<?php the_title( sprintf( '<h3 class="blog-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' ); ?>        			    
					<?php if($gridcol == '1'){ ?>        				
						<div class="blog-cat">                        
						  <?php if($showAuthor == 'true') { ?> <span class="grid-1-author"><?php _e( 'By', 'wp-blog-and-widgets' ); ?> <?php the_author(); ?></span>  <?php } echo ($showAuthor == 'true' && $showCategory == 'true') ? '/' : '' ?> <?php if($showCategory == 'true') { echo $cate_name; } ?>
						</div>
					<?php }?>                    
					<?php if($showContent == 'true'){ ?>              			
						<div class="blog-content-excerpt">                			
							<?php  if($showFullContent == "false" ) {                			    
								$excerpt = get_the_content();?>                                
								<p class="blog-short-content"><?php echo blog_limit_words( $post->ID, $excerpt, $words_limit, '...'); ?></p>                            
								<a href="<?php the_permalink(); ?>" class="blog-more-link"> <?php _e( 'Read More', 'wp-blog-and-widgets' ); ?></a>	
							<?php } else {                 			
								the_content();
							} ?>
						</div>
					<?php }?>
				</div>
			</div>
			</div>
		</div><!-- #post-## -->
	<?php endwhile; 
	endif; ?>
	</div>
	<div class="blog_pagination blog-clearfix">        
		<?php if($pagination_type == 'numeric'){        
			echo wpbaw_pagination( array( 'paged' => $paged , 'total' => $query->max_num_pages ) );        } else { ?>            
			<div class="button-blog-p"><?php next_posts_link( ' Next >>', $query->max_num_pages ); ?></div>            
			<div class="button-blog-n"><?php previous_posts_link( '<< Previous' ); ?> </div>
		<?php } ?>
	</div><?php
	
	wp_reset_postdata(); 
	return ob_get_clean();
}

add_shortcode('blog','get_wpbaw_blog');	

/* Home short code [recent_blog_post limit="10"] */

function get_wpbaw_homeblog( $atts, $content = null ){
	
	// setup the query
	extract(shortcode_atts(array(
		"limit" 					=> 10,	
		"category"					=> '',
		"grid" 						=> '0',
		"show_date" 				=> 'true',
		"show_author" 				=> 'true',
		"show_category_name" 		=> 'true',
		"show_content" 				=> 'true',
		"content_words_limit" 		=> 20,
		"order"						=> 'DESC',
		"orderby"					=> 'date',
	), $atts));
	
	// Define variables 
	
	$posts_per_page 	= !empty($limit) 					? $limit 						: 10;	
	$cat 				= (!empty($category)) 				? explode(',', $category) 		: '';	
	$gridcol 			= !empty($grid) 					? $grid 						: 0;
	$showDate 			= ( $show_date == 'false' ) 		? 'false' 						: 'true';
	$showCategory 		= ( $show_category_name == 'false' )? 'false' 						: 'true';	
	$showContent 		= ( $show_content == 'false' ) 		? 'false' 						: 'true';
	$showAuthor   		= ( $show_author == 'false' ) 		? 'false' 						: 'true';
	$words_limit 		= (!empty($content_words_limit)) 	? $content_words_limit 			: 20;	
	$order 				= ( strtolower( $order ) == 'asc' ) 	? 'ASC' 						: 'DESC';
	$orderby 			= !empty( $orderby )					? $orderby 						: 'date';

	ob_start();
	
	$post_type 		= 'blog_post';				 
		
	$args = array ( 
		'post_type'      => $post_type, 
		'orderby'        => $orderby, 
		'order'          => $order,
		'posts_per_page' => $posts_per_page,
	); 
	
	if($cat != ""){
		$args['tax_query'] = array( 
			array( 
				'taxonomy'  => 'blog-category',
				'field'     => 'id', 
				'terms'     => $cat
			) 
		);
	}
		
	$query = new WP_Query($args);
		
	global $post;
	$post_count = $query->post_count;
	$count = 0; ?>
	<div class="blogfree-plugin blog-clearfix">    
	<?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();
	 
		$count++;
		$terms = get_the_terms( $post->ID, 'blog-category' );
		$news_links = array();
		
		if($terms){

			foreach ( $terms as $term ) {
				$term_link = get_term_link( $term );
				$news_links[] = '<a href="' . esc_url( $term_link ) . '">'.$term->name.'</a>';
			}
		}
		
		$cate_name = join( ", ", $news_links );
		
		$css_class="wpbaw-blog-class";

		if ( ( is_numeric( $grid ) && ( $grid > 0 ) && ( 0 == ($count - 1) % $grid ) ) || 1 == $count ) { $css_class .= ' wpbaw-first'; }
		if ( ( is_numeric( $grid ) && ( $grid > 0 ) && ( 0 == $count % $grid ) ) || $post_count == $count ) { $css_class .= ' wpbaw-last'; }
		if($showDate == 'true'){ $date_class = "has-date";}else{$date_class = "has-no-date";} ?>
	
		<div id="post-<?php the_ID(); ?>" class="blog type-blog <?php echo (has_post_thumbnail()) ? "has-thumb" : "no-thumb";?> blog-col-<?php echo $gridcol.' '.$css_class.' '.$date_class; ?>">
			<div class="blog-inner-wrap-view blog-clearfix">
			<?php if ( has_post_thumbnail())  {?>
				<div class="blog-thumb">
					
					<?php if($gridcol == '1'){ ?>
						<div class="grid-blog-thumb">
							<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('url'); ?></a>
						</div>
					<?php } else if($gridcol > '2') { ?>
						<div class="grid-blog-thumb">
							<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large'); ?></a>
						</div>
					<?php }else if($gridcol == '0') { ?>
						<div class="grid-blog-thumb">
							 <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large'); ?></a>
						</div>
					<?php }else{ ?>
						<div class="grid-blog-thumb">
							<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large'); ?></a>
						</div>
					<?php } ?>
				</div>
			<?php }?>
			
			<div class="blog-content">
				<?php if($gridcol == '1') {                    
					if($showDate == 'true'){ ?>        				
						<div class="date-post">            			
							<h2><span><?php echo get_the_date('j'); ?></span></h2>            			
							<p><?php echo get_the_date('M y'); ?></p>
						</div>
					<?php }?>
				<?php } else { ?>    				
					<div class="grid-category-post">                        
						<?php echo ($showCategory == 'true' && $cate_name != '') ? $cate_name : ""?>
					</div>        			
					<?php if($showAuthor == 'true' || $showDate == 'true'){ ?>
						<div class="blog-author">                			
							<?php if($showAuthor == 'true') {?>                			
								<span>
									<?php esc_html_e( 'By', 'wp-blog-and-widgets' ); ?> <?php the_author(); ?>
								</span>
							<?php }?>
							<?php echo ($showAuthor == 'true' && $showDate == 'true') ? '/' : '' ?>
							<?php echo ($showDate == "true")? get_the_date() : "" ;?>
						</div><?php
					} 
				} ?>

				<div class="post-content-text">        			
					<?php if($gridcol == '1'){ ?>            		
						<div class="grid-1-date">
							<?php echo ($showDate == "true")? get_the_date() : "" ;?>
						</div>
					<?php } ?>
					<?php the_title( sprintf( '<h3 class="blog-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' );	?>        			    
					<?php if($gridcol == '1'){ ?>            		
						<div class="blog-cat">                    
							<?php if($showAuthor == 'true') { ?> <span class="grid-1-author"><?php _e( 'By', 'wp-blog-and-widgets' ); ?> <?php the_author(); ?></span>  <?php } echo ($showAuthor == 'true' && $showCategory == 'true') ? '/' : '' ?> <?php if($showCategory == 'true') { echo $cate_name; } ?>
						</div>
					<?php }?>        			
					<?php if($showContent == 'true'){ ?>            			
						<div class="blog-content-excerpt">            			    
							<?php $excerpt = get_the_excerpt(); ?>                        
							<p class="blog-short-content"><?php echo blog_limit_words($post->ID, $excerpt, $words_limit, '...'); ?></p>                       
							<a href="<?php the_permalink(); ?>" class="blog-more-link"> <?php _e( 'Read More', 'wp-blog-and-widgets' ); ?></a>	
						</div><!-- .entry-content -->
					<?php }?>
				</div>
			</div>
			</div>
		</div><!-- #post-## -->
	<?php  endwhile;
	endif; ?>
	</div>
	<?php
	wp_reset_postdata(); 
				
	return ob_get_clean();
	}
add_shortcode('recent_blog_post','get_wpbaw_homeblog');


function blog_limit_words( $post_id = null, $content = '', $word_length = '55', $more = '...' ) {
	
	$has_excerpt  = false;
	$word_length    = !empty($word_length) ? $word_length : '55';

	// If post id is passed
	if( !empty($post_id) ) {
		if (has_excerpt($post_id)) {

			$has_excerpt    = true;
			$content        = get_the_excerpt();

		} else {
			$content = !empty($content) ? $content : get_the_content();
		}
	}

	if( !empty($content) && (!$has_excerpt) ) {
		$content = strip_shortcodes( $content ); // Strip shortcodes
		$content = wp_trim_words( $content, $word_length, $more );
	}

	return $content;
}

function spblog_display_tags( $query ) {
	if( is_tag() && $query->is_main_query() ) {       
	   $post_types = array( 'post', 'blog_post' );
		$query->set( 'post_type', $post_types );
	}
}
add_filter( 'pre_get_posts', 'spblog_display_tags' );


// Manage Category Shortcode Columns

add_filter("manage_blog-category_custom_column", 'blog_category_columns', 10, 3);
add_filter("manage_edit-blog-category_columns", 'blog_category_manage_columns'); 
function blog_category_manage_columns($theme_columns) {
	$new_columns = array(
			'cb' => '<input type="checkbox" />',
			'name' => __('Name'),
			'blog_shortcode' => __( 'Blog Category Shortcode', 'wp-blog-and-widgets' ),
			'slug' => __('Slug'),
			'posts' => __('Posts')
			);
	return $new_columns;
}

function blog_category_columns($out, $column_name, $theme_id) {
	$theme = get_term($theme_id, 'blog-category');
	switch ($column_name) {      

		case 'title':
			echo get_the_title();
		break;
		case 'blog_shortcode':        

			 echo '[blog category="' . $theme_id. '"]';
			  echo '[recent_blog_post category="' . $theme_id. '"]';
		break;

		default:
			break;
	}
	return $out;
}

function wpbaw_pagination($args = array()){
	
	$big = 999999999; // need an unlikely integer

	$paging = apply_filters('wpbaw_blog_paging_args', array(
					'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
					'format'    => '?paged=%#%',
					'current'   => max( 1, $args['paged'] ),
					'total'     => $args['total'],
					'prev_next' => true,
					'prev_text' => __('« Previous', 'wp-blog-and-widgets'),
					'next_text' => __('Next »', 'wp-blog-and-widgets'),
				));
	
	echo paginate_links($paging);
}

// How it work file, Load admin files
if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	require_once( WPBAW_DIR . '/admin/wpbaw-how-it-work.php' );
}

//include Admin Class file
require_once( WPBAW_DIR . '/admin/class-wpbaw-admin.php' );

/* Plugin Wpos Analytics Data Starts */
function wpos_analytics_anl22_load() {

	require_once dirname( __FILE__ ) . '/wpos-analytics/wpos-analytics.php';

	$wpos_analytics = wpos_anylc_init_module( array(
							'id'			=> 22,
							'file'			=> plugin_basename( __FILE__ ),
							'name'			=> 'WP Blog and Widget',
							'slug'			=> 'wp-blog-and-widget',
							'type'			=> 'plugin',
							'menu'			=> 'edit.php?post_type=blog_post',
							'text_domain'	=> 'wp-blog-and-widgets',
							'promotion'		=> array( 
													'bundle' => array(
																'name'	=> 'Download FREE 50+ Plugins, 10+ Themes and Dashboard Plugin',
																'desc'	=> 'Download FREE 50+ Plugins, 10+ Themes and Dashboard Plugin',
																'file'	=> 'https://www.wponlinesupport.com/latest/wpos-free-50-plugins-plus-12-themes.zip'
															)
													),
							'offers'		=> array(
													'trial_premium' => array(
														'image'	=> 'http://analytics.wponlinesupport.com/?anylc_img=22',
														'link'	=> 'http://analytics.wponlinesupport.com/?anylc_redirect=22',
														'desc'	=> 'Or start using the plugin from admin menu',
													)
												),
						));

	return $wpos_analytics;
}

// Init Analytics
wpos_analytics_anl22_load();
/* Plugin Wpos Analytics Data Ends */