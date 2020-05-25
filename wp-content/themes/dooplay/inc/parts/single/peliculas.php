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

<h2 class="font1">ดูหนังออนไลน์ เรื่อง <?php the_title(); ?></h2> <?php the_content(); ?> <p class="font2">ดูหนังออนไลน์ ทีวีออนไลน์ ทีวีสด ดูบอลออนไลน์ <a href="https://moviebkk.com/">MovieBKK</a>
เรามีให้ทุกท่านได้รับชมกันแบบฟรีๆ ไม่เสียค่าสมาชิก อีกทั้งเรายังมี ช่องทีวีทั่วไป ช่องข่าว ช่องบันเทิงแล้วคุณจะไม่พลาด หนังสนุกๆ หนังใหม่ หนังชนโรง อีกต่อไป ดูหนังออนไลน์ <?php the_tags(); ?>  ลื่นไหลไม่มีสดุด พร้อมอัพเดท หนังใหม่ๆ ให้คุณได้เพลิดเพลินในทุกวันของคุณ</p><br>
 <h2 class="font1">ดูหนังออนไลน์ เรื่อง <?php the_title(); ?>ดูหนังออนไลน์ ทุกครั้งต้องนึกถึง  <a href="https://moviebkk.com/">ดูหนังออนไลน์</a> คมชัดสุงสุด ถึง4K อีกทั้งยังโหลดหนังได้ไว เราอัพเดท หนังใหม่ ให้คุณตลอด24ชั่วโมง
</h2><br>
<p class="font2">ดูหนังออนไลน์ ง่ายๆ อัพเดทหนังใหม่ทุกวันสามารถติดตามรับชมในรูปแบบ หนังออนไลน์ ได้ที่เว็บไซต์ <a href="https://moviebkk.com/">MovieBKK</a> การติดตามรับชม ดูหนัง <h2 class="font1">เรื่อง <?php the_title(); ?></h2> <p class="font2">ภายในเว็บไซต์ MovieBKK นั้น ทุกคนสามารถรับชมกันได้แบบฟรี ๆ ไม่ต้องเสียค่าใช้จ่าย แถมการติดตามรับชมในแต่ละครั้งคุณก็จะได้อรรถรสเหมือน ๆ กันได้เข้าไปดูในโรงภาพยนตร์อีกเช่นเดียวกัน เนื่องจากไม่มีโฆษณาคั่นตัวหนังให้ต้องวุ่นวายใจ คุณจะสามารถ</p><p class="font2"><?php the_tags(); ?></p> ช่องกีฬา อีกมากมายหลากหลายรายการวมไปถึงการถ่ายทอดสดกีฬา ฟุตบอลสด เราก็มีให้ท่านได้รับชมกันอย่างต่อเนื่อง โหลดหนังไว โฆษณาน้อยดูหนังออนไลน์ รองรับทุกอุปกรณ์ทั้งคอมพิวเตอร์ มือถือ Android IPhone แท็บเล็ต Smart tv และอุปกรณ์ทุกชนิด</p>   
<h2 class="font1">ดูหนังออนไลน์ <?php the_title(); ?> ที่ท่านชอบกันแบบฟรีๆ ไม่เสียค่าใช้จ่ายไม่มีโฆษณา</h2><br>
<p class="font2">อีกทั้งเรายังมี ช่องทีวีทั่วไป ช่องข่าว ช่องบันเทิง ช่องกีฬา อีกมากมายหลากหลายรายการวมไปถึงการ ถ่ายทอดสดกีฬา ฟุตบอลสด หนังสงคราม | War เรารวบรวมหนังทั้งเก่ามากกว่า 10 ปี ที่ได้รับรางวัล เราอัพเดท หนังใหม่ ให้คุณรวบรวบ หนังออนไลน์ ไว้ให้คุณมากมายทั้ง หนังจีน หนังไทย หนังต่างประเทศ หนังซับไทย ทีวีซีรี่ เเละอื่นๆอีกมากมาย ให้คุณได้รับชมอย่างสะดวกสบาย  <a href="https://moviebkk.com/">MovieBKK</a> ให้ทุกท่านได้รับชม</p>

</div>
<!-- End Single -->

