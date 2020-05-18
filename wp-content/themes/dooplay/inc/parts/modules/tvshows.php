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
$sldr = doo_is_true('tvmodcontrol','slider');
$auto = doo_is_true('tvmodcontrol','autopl');
$orde = cs_get_option('tvmodorderby','date');
$ordr = cs_get_option('tvmodorder','DESC');
$pitm = cs_get_option('tvitems','10');
$titl = cs_get_option('tvtitle','TV Shows');
$pmlk = get_post_type_archive_link('tvshows');
$totl = doo_total_count('tvshows');
$eowl = ($sldr == true) ? 'id="dt-tvshows" ' : false;

// Compose Query
$query = array(
	'post_type' => array('tvshows'),
	'showposts' => $pitm,
	'orderby' 	=> $orde,
	'order' 	=> $ordr
);

// End Data
?>
<header>
	<h2><?php echo $titl; ?></h2>
	<?php if($sldr == true && !$auto) { ?>
	<div class="nav_items_module">
	  <a class="btn prev4"><i class="icon-caret-left"></i></a>
	  <a class="btn next4"><i class="icon-caret-right"></i></a>
	</div>
	<?php } ?>
	<span></span>
</header>
<div id="tvload" class="load_modules"><?php _d('Loading..'); ?></div>
<div <?php echo $eowl; ?>class="items itemphoto_img01">

<a href="/tag/the-fast-and-the-furious/"><img class="imgxx2" src="/wp-content/uploads/photo_img01/fast__furious_poster.jpg" alt="ดูหนังออนไลน์ Fast & Furious"><h5 class="dataimgxx2">ดูหนัง Fast & Furious</h5></a>
<a href="/tag/iron-man/"><img class="imgxx2" src="/wp-content/uploads/photo_img01/iron_man_poster.jpg" alt="ดูหนังออนไลน์ Iron man"><h5 class="dataimgxx2">ดูหนัง Iron man</h5></a>
<a href="/tag/james-bond-007/"><img class="imgxx2" src="/wp-content/uploads/photo_img01/jame_bond007_poster.jpg" alt="ดูหนังออนไลน์ Jame Bond 007"><h5 class="dataimgxx2">ดูหนัง Jame Bond 007</h5></a>
<a href="/tag/john-wick/"><img class="imgxx2" src="/wp-content/uploads/photo_img01/john_wick_poster.jpg" alt="ดูหนังออนไลน์ John Wick"><h5 class="dataimgxx2">ดูหนัง John Wick</h5></a>
<a href="/tag/mission-impossible/"><img class="imgxx2" src="/wp-content/uploads/photo_img01/mi_poster.jpg" alt="ดูหนังออนไลน์ Mission Impossible"><h5 class="dataimgxx2">ดูหนัง Mission Impossible</h5></a>
<a href="/tag/อเวนเจอร์ส/"><img class="imgxx2" src="/wp-content/uploads/photo_img01/poster_avenger.jpg" alt="ดูหนังออนไลน์ Avenger"><h5 class="dataimgxx2">ดูหนัง Avenger</h5></a>
<a href="/tag/spider-man/"><img class="imgxx2" src="/wp-content/uploads/photo_img01/spiderman_poster.jpg" alt="ดูหนังออนไลน์ Spiderman"><h5 class="dataimgxx2">ดูหนัง Spiderman</h5></a>
<a href="/tag/the-lord-of-the-rings/"><img class="imgxx2" src="/wp-content/uploads/photo_img01/the_lord_of_the_ring_poster.jpg" alt="ดูหนังออนไลน์ Thelord of thering"><h5 class="dataimgxx2">ดูหนัง Thelord of thering</h5></a>
<a href="/tag/transformers/"><img class="imgxx2" src="/wp-content/uploads/photo_img01/transformer_poster.jpg" alt="ดูหนังออนไลน์ Transformer"><h5 class="dataimgxx2">ดูหนัง Transformer</h5></a>
<a href=""><img class="imgxx2" src="/wp-content/uploads/photo_img01/toy-story-poster.jpg" alt="ดูหนังออนไลน์ Toy story"><h5 class="dataimgxx2">ดูหนัง Toy story</h5></a>
<a href="/tag/the-matrix/"><img class="imgxx2" src="/wp-content/uploads/photo_img01/the-matrix-poster.jpg" alt="ดูหนังออนไลน์ The Matrix"><h5 class="dataimgxx2">ดูหนัง The Matrix</h5></a>
<a href="/tag/the-conjuring/"><img class="imgxx2" src="/wp-content/uploads/photo_img01/the-conjuring-poster.jpg" alt="ดูหนังออนไลน์ The conjuring"><h5 class="dataimgxx2">ดูหนัง The conjuring</h5></a>
<a href="/tag/star-wars/"><img class="imgxx2" src="/wp-content/uploads/photo_img01/starwars-poster.jpg" alt="ดูหนังออนไลน์ Star wars"><h5 class="dataimgxx2">ดูหนัง Star wars</h5></a>
<a href="/tag/marvel/"><img class="imgxx2" src="/wp-content/uploads/photo_img01/mavel-poster.jpg" alt="ดูหนังออนไลน์ Mavel"><h5 class="dataimgxx2">ดูหนัง Mavel</h5></a>
<a href="/tag/ยิปมัน/"><img class="imgxx2" src="/wp-content/uploads/photo_img01/ip-poster.jpg" alt="ดูหนังออนไลน์ Ipman"><h5 class="dataimgxx2">ดูหนัง Ipman</h5></a>
<a href="/tag/harrypotter/"><img class="imgxx2" src="/wp-content/uploads/photo_img01/harry-potter-poster.jpg" alt="ดูหนังออนไลน์ Harry potter"><h5 class="dataimgxx2">ดูหนัง Harry potter</h5></a>
<a href="/tag/หนังdc/"><img class="imgxx2" src="/wp-content/uploads/photo_img01/dc-poster.jpg" alt="ดูหนังออนไลน์ DC Universe"><h5 class="dataimgxx2">ดูหนัง DC Universe</h5></a>
<a href="/tag/racing/"><img class="imgxx2" src="/wp-content/uploads/photo_img01/car-poster.jpg" alt="ดูหนังออนไลน์ แข่งรถ"><h5 class="dataimgxx2">ดูหนัง แข่งรถ</h5></a>
<a href=""><img class="imgxx2" ></h5></a>
<a href=""><img class="imgxx2" ></h5></a>
</div>
