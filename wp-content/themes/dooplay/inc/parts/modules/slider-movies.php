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
$pitm = cs_get_option('slideritems','10');
$orde = cs_get_option('slidermodorderby','date');
$ordr = cs_get_option('slidermodorder','DESC');
$titl = cs_get_option('xxx','ดูหนังออนไลน์ หนังใหม่ หนังชนโรง MovieBKK');

// Compose Query
$query = array(
	'post_type' => array('movies'),
	'showposts' => $pitm,
	'orderby' 	=> $orde,
	'order' 	=> $ordr
);

// End Data
?>
<header class="hdh1main">
	<h1 class="h1 main"><?php echo $titl; ?></h1>
	<?php if($sldr == true && !$auto) { ?>
	<?php } ?>
	
</header>
