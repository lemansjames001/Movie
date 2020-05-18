<div class="wrap dooplaydb">
    <h1><?php _e('TMDb Updater','tmdb'); ?></h1>
    <h2><?php _e('This tool allows you to update and correct TMDb metadata on your website.','tmdb'); ?></h2>
    <p><?php _e('Before executing any process, we recommend that you obtain a backup copy of the database.','tmdb'); ?></p>
    <p><?php _e('We recommend deactivating this plugin after you finish using it.','tmdb'); ?></p>
    <div class="metabox-holder">
        <div class="postbox tmbd-box-updater">
            <h3><?php _e('TMDb Updater tool','tmdb'); ?> <span id="tmdb-processes" class="right"></span></h3>
            <div class="inside">
                <?php if(!empty($count_tposts)){ ?>
                <p><?php _e('We recommend completing the process to the end, if you need to pause the process, you can close this window and when it comes back it will continue.','tmdb'); ?></p>
                <p><a href="#" id="tmdb-progress-reset"  data-nonce="<?php echo wp_create_nonce('tmdb_reset_processes'); ?>"><strong><?php _e('Restart all processes','tmdb'); ?></strong></a></p>
                <div class="tmdb-progress">
                    <form id="tmdb-metaupdater" class="form">
                        <input id="tmdb-progress-submit" type="submit" class="button button-primary" value="<?php echo $button_text; ?>">
                        <input id="tmdb-progress-page" type="hidden" name="page" value="<?php echo $current_page; ?>">
                        <input id="tmdb-progress-total" type="hidden" name="total" value="<?php echo $count_tposts; ?>">
                        <input id="tmdb-progress-action" type="hidden" name="action" value="tmdb_metaupdater">
                    </form>
                    <div id="tmdb-progress-loader" class="loader"></div>
                    <div class="tmbd-updater-progress">
                        <div id="tmdb-progress-percentage" class="percentage"><?php echo $progress; ?>%</div>
                        <div class="bar">
                            <div id="tmdb-progress-line" class="progress-line" style="width:<?php echo $progress; ?>%"></div>
                        </div>
                    </div>
                </div>
                <?php } else { ?>
                <p><strong><?php _e('Notice:','tmdb'); ?></strong> <?php _e('Before starting we need to calculate the total number of processes.','tmdb'); ?></p>
                <div class="tmdb-progress">
                    <button id="tmdb-calculator" class="button button-primary" data-nonce="<?php echo wp_create_nonce('tmdb_calculator_processes'); ?>"><?php _e('Calculate processes now','tmdb'); ?></button>
                </div>
                <?php } ?>
            </div>
        </div>
        <div id="tmdb-updater-log" class="tmdb-updater-log">
            <ul><i id="tmdb-log-indicator"></i></ul>
        </div>
    </div>
</div>
