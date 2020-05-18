<?php
/*
* -------------------------------------------------------------------------------------
* @author: Doothemes
* @author URI: https://doothemes.com/
* @aopyright: (c) 2018 Doothemes. All rights reserved
* -------------------------------------------------------------------------------------
*
* @since 2.1.8
*
*/

// Compose data MODULE
$sldr = doo_is_true('moviemodcontrol','slider');
$auto = doo_is_true('moviemodcontrol','autopl');
$orde = cs_get_option('moviemodorderby','date');
$ordr = cs_get_option('moviemodorder','DESC');
$pitm = "10";
$titl = "หนังใหม่ชนโรง";
$pmlk = "https://moviebkk.com/release/2020/";
$eowl = ($sldr == true) ? 'id="dt-movies" ' : false;
$year = "2020";

// Compose Query
$query = array(
	'post_type' => array('movies'),
	'showposts' => $pitm,
	'orderby' 	=> $orde,
	'order' 	=> $ordr,
	'dtyear'    => $year
);

// End Data
?>
<header>
	<h2><?php echo $titl; ?></h2>
	<?php if($sldr == true && !$auto) { ?>
	<div class="nav_items_module">
	  <a class="btn prev3"><i class="icon-caret-left"></i></a>
	  <a class="btn next3"><i class="icon-caret-right"></i></a>
	</div>
	<?php } ?>
	<span class="textseeall"> <a href="<?php echo $pmlk; ?>" class="see-all"><?php _d('ดูทั้งหมด'); ?></a></span>
</header>
<div <?php echo $eowl; ?>class="items">
	<?php query_posts($query);  while(have_posts()){ the_post(); get_template_part('inc/parts/item');  } wp_reset_query(); ?>
</div>

