<?php

/*

* -------------------------------------------------------------------------------------

* @author: Doothemes

* @author URI: https://doothemes.com/

* @aopyright: (c) 2018 Doothemes. All rights reserved

* -------------------------------------------------------------------------------------

*

* @since 2.2.3

*

*/



// All Postmeta

$classlinks = new DooLinks;

$postmeta = doo_postmeta_movies($post->ID);

$adsingle = doo_compose_ad('_dooplay_adsingle');

// Movies Meta data

$trailer = doo_isset($postmeta,'youtube_id');

$pviews  = doo_isset($postmeta,'dt_views_count');

$player  = doo_isset($postmeta,'players');

$player  = maybe_unserialize($player);

$images  = doo_isset($postmeta,'imagenes');

$tviews  = ($pviews) ? sprintf( __d('%s Views'), $pviews) : __d('0 Views');

//  Image

$dynamicbg  = dbmovies_get_rand_image($images);

// Options

$player_ads = doo_compose_ad('_dooplay_adplayer');

$player_wht = cs_get_option('playsize','regular');



// Dynamic Background

if(cs_get_option('dynamicbg') == true) { ?>

<style>

#dt_contenedor {

    background-image: url(<?php echo $dynamicbg; ?>);

    background-repeat: no-repeat;

    background-attachment: fixed;

    background-size: cover;

    background-position: 50% 0%;

}

</style>

<?php } ?>





<!-- Big Player -->

<?php DooPlayer::viewer_big($player_wht, $player_ads, $dynamicbg); ?>



<!-- Start Single -->

<div id="single" class="dtsingle">



    <!-- Edit link response Ajax -->

    <div id="edit_link"></div>



    <!-- Start Post -->

    <?php if(have_posts()) :while (have_posts()) : the_post(); doo_set_views($post->ID); ?>

    <div class="content">



        <!-- Regular Player and Player Options -->

        <?php DooPlayer::viewer($post->ID, 'movie', $player, $trailer, $player_wht, $tviews, $player_ads, $dynamicbg); ?>



        <!-- Head movie Info -->

        <div class="sheader">

        	<div class="poster">

        		<img src="<?php echo dbmovies_get_poster($post->ID); ?>" alt="<?php the_title(); ?>">

        	</div>

        	<div class="data">

        		<h1><?php the_title(); ?></h1>

        		<div class="extra">

        		<?php

                // Movie Meta Info

                if($d = doo_isset($postmeta,'tagline')) echo "<span class='tagline'>{$d}</span>";

        		if($d = doo_isset($postmeta,'release_date')) echo "<span class='date'>".doo_date_compose($d,false)."</span>";

        		if($d = doo_isset($postmeta,'Country')) echo "<span class='country'>{$d}</span>";

        		if($d = doo_isset($postmeta,'runtime')) echo "<span class='runtime'>{$d} ".__d('Min.')."</span>";

        		if($d = doo_isset($postmeta,'Rated')) echo "<span class='C{$d} rated'>{$d}</span>";

                // end..

                ?>

        		</div>

        		<?php echo do_shortcode('[starstruck_shortcode]'); ?>

        		<div class="sgeneros">

        		<?php echo get_the_term_list($post->ID, 'genres', '', '', ''); ?>

        		</div>

        	</div>

        </div>



        <!-- Movie Tab single -->

        <div class="single_tabs">

            <?php if(is_user_logged_in() && doo_is_true('permits','eusr')){ ?>

        	<div class="user_control">

        		<?php dt_list_button($post->ID); dt_views_button($post->ID); ?>

        	</div>

            <?php } ?>

        	<ul id="section" class="smenu idTabs">

            	<li><a id="main_ali" href="#info"><?php _d('ข้อมูลหนัง'); ?></a></li>

            	<?php if(doo_here_links($post->ID)) echo '<li><a href="#linksx">'.__d('Links').'</a></li>'; ?>

                <li><a href="#cast"><?php _d('Cast'); ?></a></li>

                <li id="report_li"><a href="#report"><?php _d('เเจ้งหนังเสีย'); ?></a></li>

        	</ul>

        </div>



        <!-- Single Post Ad -->

        <?php if($adsingle) echo '<div class="module_single_ads">'.$adsingle.'</div>'; ?>



        <!-- Report video Error -->

        <div id="report" class="sbox">

            <?php get_template_part('inc/parts/single/report-video'); ?>

        </div>



        <!-- Movie more info -->

        <div id="info" class="sbox">

            <h2><?php _d('เรื่องย่อ'); ?></h2>

            <div itemprop="description" class="wp-content">

                <?php the_content(); ?>

           
            
                <?php the_tags('<ul class="wp-tags colortagx"><li>','</li><li>','</li></ul>'); ?>

                <?php dbmovies_get_images($images); ?>

            </div>

            <?php if($d = doo_isset($postmeta, 'original_title')) { ?>

            <div class="custom_fields">

                <b class="variante"><?php _d('Original title'); ?></b>

                <span class="valor"><?php echo $d; ?></span>

            </div>

            <?php } if($d = doo_isset($postmeta, 'imdbRating')) { ?>

            <div class="custom_fields">

        	    <b class="variante"><?php _d('IMDb Rating'); ?></b>

        	    <span class="valor">

        		    <b id="repimdb"><?php echo '<strong>'.$d.'</strong> '; if($votes = doo_isset($postmeta, 'imdbVotes')) echo sprintf( __d('%s votes'), doo_format_number($votes) ); ?></b>

        	        <?php if(current_user_can('administrator')) { ?><a data-id="<?php echo $post->ID; ?>" data-imdb="<?php echo doo_isset($postmeta, 'ids'); ?>" id="update_imdb_rating"><?php _d('Update Rating'); ?></a><?php } ?>

        	    </span>

            </div>

            <?php } if($d = doo_isset($postmeta, 'vote_average')) { ?>

            <div class="custom_fields">

                <b class="variante"><?php _d('TMDb Rating'); ?></b>

                <span class="valor"><?php echo '<strong>'.$d.'</strong> '; if($votes = doo_isset($postmeta, 'vote_count')) echo sprintf( __d('%s votes'), number_format($votes) ); ?></span>

            </div>

            <?php } ?>

        </div>



        <!-- Movie Cast -->

        <div id="cast" class="sbox fixidtab">

            <h2><?php _d('Director'); ?></h2>

            <div class="persons">

            	<?php doo_director(doo_isset($postmeta,'dt_dir'), "img", true); ?>

            </div>

            <h2><?php _d('Cast'); ?></h2>

            <div class="persons">

            	<?php doo_cast(doo_isset($postmeta,'dt_cast'), "img", true); ?>

            </div>

        </div>



        <!-- Movie Links -->

        <?php if(DOO_THEME_DOWNLOAD_MOD) get_template_part('inc/parts/single/links'); ?>



        <!-- Movie Social Links -->

         <!-- doo_social_sharelink($post->ID); ?> -->



        <!-- Movie Related content -->

         <!-- if(DOO_THEME_RELATED) get_template_part('inc/parts/single/relacionados'); ?> -->



        <!-- Movie comments -->

         <!-- get_template_part('inc/parts/comments'); ?> -->



        <!-- Movie breadcrumb -->

        <!-- doo_breadcrumb( $post->ID, 'movies', __d('Movies'), 'breadcrumb_bottom' ); ?> -->



    </div>

    <!-- End Post-->

    <?php endwhile; endif; ?>



    <!-- Movie Sidebar -->

    <div class="sidebar scrolling">

    	<?php dynamic_sidebar('sidebar-movies'); ?>

      

    </div>

    <!-- End Sidebar -->


 <!-- Footer -->
</div>
	
	<div class="contentxxx">
    
    <p>ดูหนังออนไลน์ <?php the_tags(); ?> เรื่อง <strong class="movietitle"><?php the_title(); ?></strong></P>
    <?php the_content(); ?>
    <p>หนังดีหนังเด่นที่ทุกคนพากันติดตามเป็นจำนวนมาก ซึ่งหนังเรื่องนี้ถือได้ว่าเป็นภาพยนตร์เรื่องดังไปทั่วโลก หากใครพลาดที่จะรับชมภายในโรงภาพยนตร์ ในตอนนี้หากใครอยากจะติดตามรับชม ดูหนัง</p><?php the_tags(); ?>
    <p>สามารถติดตามรับชมในรูปแบบ หนังออนไลน์ ได้ที่เว็บไซต์  <a href="https://moviebkk.com/">MovieBKK</a> การติดตามรับชม ดูหนัง ภายในเว็บไซต์ MovieBKK นั้น ทุกคนสามารถรับชมกันได้แบบฟรี ๆ ไม่ต้องเสียค่าใช้จ่าย แถมการติดตามรับชมในแต่ละครั้งคุณก็จะได้อรรถรสเหมือน ๆ กันได้เข้าไปดูในโรงภาพยนตร์อีกเช่นเดียวกัน เนื่องจากไม่มีโฆษณาคั่นตัวหนังให้ต้องวุ่นวายใจ คุณจะสามารถดูหนัง </p>
    <br>
    <p><strong  class="movietitle"><?php the_title(); ?> ดูหนังเรื่องนี้แบบฟรี ๆ ได้แล้วที่นี่</strong></p><br>
    <p>คุณสามารถ ดูหนังออนไลน์ <?php the_title(); ?> ได้แบบฟรี ๆ โดยที่ไม่ต้องสมัครเป็นสมาชิกหรือเสียค่าใช้จ่ายแต่อย่างใด แต่คุณจะสามารถรับชม หนังออนไลน์ เด่น ๆ ดัง ๆ ได้ทุกเรื่องที่เว็บไซต์  <a href="https://moviebkk.com/">MovieBKK</a> โดยตรง ซึ่ง เว็บ <a href="https://moviebkk.com/">MovieBKK</a> เปิดให้ทุกคนสามารถเข้ามารับชมหนังดังระดับโลก หนังชนโรง หนังใหม่ ล่าสุด ซึ่งมีให้ทุกคนได้ติดตามรับชมกันแบบทุกเรื่อง ทุกแนว ทุกประเภท ซึ่งทุกคนจะสามารถดูหนังได้ตามใจชอบ ใครมีเรื่องไหนในดวงใจเพียงแค่ค้นหาบนหน้าเว็บไซต์ หากพบเจอหนังคุณก็จะสามารถรับชมได้ทันที ฟรี ตลอด 24 ชั่วโมง</p>
	<br>
    <p><strong class="movietitle">ดูหนังออนไลน์ เรื่อง <?php the_title(); ?>  ได้ต่อเนื่อง ไร้โฆษณาคั่นกลาง</strong></p>
    <br>
    <p>
    สำหรับท่านใดที่อยากจะ ดูหนัง ดังอย่างเรื่องนี้ วันนี้คุณสามารถดูหนังเรื่องนี้ได้แบบฟรี ๆ ผ่านเว็บไซต์ <a href="https://moviebkk.com/">MovieBKK</a> โดยตรง ซึ่งทุกคนจะสามารถ ดูหนังออนไลน์ เรื่องนี้ได้โดยที่ไม่ต้องเสียค่าใช้จ่ายแต่อย่างใด แถมในระหว่างที่คุณกำลังดูหนังเรื่องนี้อยู่นั้น จะไม่มีโฆษณาคั่นกลาง นั่นหมายความว่า คุณจะได้อารมณ์เหมือนกับได้ดูหนังยาวต่อเนื่องตั้งแต่ต้นจนจบเหมือนกับที่ดูภาพยนตร์ในโรงภาพยนตร์โดยตรงนั่นเอง  และหลังจากที่คุณดูหนังเรื่องนี้จบแล้ว หากอยากจะติดตามดูหนังดัง หนังชนโรงเรื่องอื่น ๆ ก็ยังคงสามารถติดตามเช็คหนังเรื่องอื่น ๆ ภายในเว็บไซต์ เพื่อที่จะทำให้คุณสามารถกดดู หนังออนไลน์ เรื่องอื่น ๆ ได้ต่อไปนั่นเอง
    </p>
    <p>
    เพราะฉะนั้น หากใครยังไม่ได้ดูหนังดังเรื่อง <?php the_title(); ?> ในโรงภาพยนตร์ตามเวลาฉาย วันนี้คุณสามารถติดตาม ดูหนังออนไลน์ เรื่องนี้ได้แบบฟรี ๆ กันแล้ว เฉพาะที่ เว็บไซต์<a href="https://moviebkk.com/">MovieBKK</a> โดยตรง การ ดูหนังฟรี ที่เว็บไซต์แห่งนี้ คุณไม่จำเป็นจะต้องเสียค่าสมัครเป็นสมาชิก และไม่จำเป็นจะต้องเสียค่าใช้จ่ายเลยแม้แต่บาทเดียว แต่คุณจะสามารถติดตามดู หนังออนไลน์ เรื่องเด่น ๆ ดัง ๆ ที่เคยฉายในโรงภาพยนตร์มาก่อนหน้านี้ หรือแม้กระทั่ง ดูหนังชนโรง คุณก็สามารถดูได้แบบฟรี ๆ ทุกเรื่องที่ เว็บ <a href="https://moviebkk.com/">MovieBKK.com</a> โดยตรง ซึ่งทาง <a href="https://moviebkk.com/">MovieBKK.com</a> มีความประสงค์อยากจะให้ทุกท่านมีความสุขและสนุกไปกับหนังเรื่องโปรดที่คุณอยากจะติดตามรับชมโดยตรงนั่นเอง สำหรับท่านใดที่สนใจอย่าลืมไปดูหนังดี ๆ ในดวงใจกันแบบฟรี ๆ
    </p>
    
</div>

<!-- End Single -->

