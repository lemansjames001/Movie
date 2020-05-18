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
$pitm = "6";
$titl = cs_get_option('movietitle','Movies');
$pmlk = get_post_type_archive_link('movies');
$totl = doo_total_count('movies');
$eowl = ($sldr == true) ? 'id="dt-movies" ' : false;

// Compose Query
$query = array(
	'post_type' => array('movies'),
	'showposts' => $pitm,
	'orderby' 	=> $orde,
	'order' 	=> $ordr
);

// End Data
?>
<header>
	<h2><?php echo $titl; ?></h2>
	<div class="nav_items_module">
	  <a class="btn prev3"><i class="icon-caret-left"></i></a>
	  <a class="btn next3"><i class="icon-caret-right"></i></a>
	</div>
</header>
<div <?php echo $eowl; ?>class="items">
<div id="slider-movies" class="animation-1 slider">
<a href="/release/2020/"><img class="imgxx2" src="/wp-content/uploads/photo_img01/ph01.jpg" ></a>
<a href="/tag/marvel/"><img class="imgxx2" src="/wp-content/uploads/photo_img01/ph01.jpg" ></a>
<a href="/tag/the-fast-and-the-furious/"><img class="imgxx2" src="/wp-content/uploads/photo_img01/ph01.jpg" ></a>
<a href="https://moviebkk.com/tag/star-wars/"><img class="imgxx2" src="/wp-content/uploads/photo_img01/ph01.jpg" ></a>
<a href="https://moviebkk.com/tag/%e0%b8%ad%e0%b9%80%e0%b8%a7%e0%b8%99%e0%b9%80%e0%b8%88%e0%b8%ad%e0%b8%a3%e0%b9%8c%e0%b8%aa/"><img class="imgxx2" src="/wp-content/uploads/photo_img01/ph01.jpg" ></a>
<a href="/tag/harrypotter/"><img class="imgxx2" src="/wp-content/uploads/photo_img01/ph01.jpg" ></a>
</div>
</div>
