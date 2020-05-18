<?php
$currentExtensions = YrmConfig::extensions();
$extensionsResult = ReadMoreAdminHelper::separateToActiveAndNotActive($currentExtensions);
?>
<?php if(YRM_PKG == YRM_FREE_PKG): ?>
	<div style="margin-top: 5px">
		<a href="<?php echo YRM_PRO_URL; ?>" target="_blank">
			<button class="yrm-upgrade-button-red">
				<b class="h2">Upgrade</b><br><span class="h5">to PRO version</span>
			</button>
		</a>
	</div>
<?php endif;?>
<div class="ycf-bootstrap-wrapper">
	<h2>Add New Read More Type</h2>
	<div class="product-banner" onclick="location.href = '<?php echo admin_url();?>admin.php?page=button&yrm_type=button'">
		<div class="yrm-types yrm-button"></div>
		<div class="yrm-type-view-footer">
			<span class="yrm-promotion-title"><?php _e('Button', YRM_LANG);?></span>
		</div>
	</div>
	<div class="product-banner" onclick="location.href = '<?php echo admin_url();?>admin.php?page=button&yrm_type=inline'">
		<div class="yrm-types yrm-inline"></div>
		<div class="yrm-type-view-footer">
			<span class="yrm-promotion-title"><?php _e('Inline', YRM_LANG);?></span>
		</div>
	</div>
    <?php foreach ($extensionsResult['active'] as $extension): ?>
        <div class="product-banner" onclick="location.href = '<?php echo admin_url();?>admin.php?page=button&yrm_type=<?php echo esc_attr($extension['shortKey']);?>'">
            <div class="yrm-types yrm-<?php echo esc_attr($extension['shortKey']);?>"></div>
            <div class="yrm-type-view-footer">
                <span class="yrm-promotion-title"><?php _e($extension['boxTitle'], YRM_LANG);?></span>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="product-banner" onclick="location.href = '<?php echo admin_url();?>admin.php?page=button&yrm_type=link'">
        <div class="yrm-types yrm-link"></div>
        <div class="yrm-type-view-footer">
            <span class="yrm-promotion-title"><?php _e('Link button', YRM_LANG);?></span>
        </div>
    </div>
    <div class="product-banner" onclick="location.href = '<?php echo admin_url();?>admin.php?page=button&yrm_type=alink'">
        <div class="yrm-types yrm-alink"></div>
        <div class="yrm-type-view-footer">
            <span class="yrm-promotion-title"><?php _e('Link', YRM_LANG);?></span>
        </div>
    </div>
	<?php if(YRM_PKG > YRM_SILVER_PKG): ?>
		<div class="product-banner" onclick="location.href = '<?php echo admin_url();?>admin.php?page=button&yrm_type=popup'">
			<div class="yrm-types yrm-popup"></div>
			<div class="yrm-type-view-footer">
				<span class="yrm-promotion-title"><?php _e('Button & popup', YRM_LANG);?></span>
			</div>
		</div>
        <div class="product-banner" onclick="location.href = '<?php echo admin_url();?>admin.php?page=button&yrm_type=inlinePopup'">
			<div class="yrm-types yrm-inline-popup"></div>
			<div class="yrm-type-view-footer">
				<span class="yrm-promotion-title"><?php _e('Inline & popup', YRM_LANG);?></span>
			</div>
		</div>
	<?php endif?>
	<?php if(YRM_PKG == YRM_FREE_PKG): ?>
		<a class="product-banner" href="<?php echo YRM_PRO_URL; ?>" target="_blank">
			<div class="yrm-types yrm-popup type-banner-pro">
				<p class="yrm-type-title-pro">PRO Features</p>
			</div>
			<div class="yrm-type-view-footer">
				<span class="yrm-promotion-title"><?php _e('Button & popup', YRM_LANG);?></span>
                <span class="yrm-play-promotion-video" data-href="<?php echo YRM_POPUP_VIDEO; ?>"></span>
			</div>
		</a>
        <a class="product-banner" href="<?php echo YRM_PRO_URL; ?>" target="_blank">
			<div class="yrm-types yrm-inline-popup type-banner-pro">
				<p class="yrm-type-title-pro">PRO Features</p>
			</div>
			<div class="yrm-type-view-footer">
				<span class="yrm-promotion-title"><?php _e('Inline & popup', YRM_LANG);?></span>
                <span class="yrm-play-promotion-video" data-href="<?php echo YRM_POPUP_VIDEO; ?>"></span>
			</div>
		</a>
	<?php endif?>
</div>
<?php

if(!empty($extensionsResult['passive'])) : ?>
<div class="yrm-add-new-extensions-wrapper">
	<span class="yrm-add-new-extensions">
		Extensions
	</span>
</div>
    <?php foreach ($extensionsResult['passive'] as $extension): ?>
        <div class="product-banner" onclick="location.href = '<?php echo YRM_PRO_URL.'#yrm-'.esc_attr($extension['shortKey']); ?>'">
            <div class="yrm-<?php echo esc_attr($extension['shortKey']);?>">
                <div class="yrm-types type-banner-pro">
                    <p class="yrm-ext-title-pro"><?php _e('PRO EXTENSION', YRM_LANG);?></p>
                </div>
            </div>
            <div class="yrm-type-view-footer">
                <span class="yrm-promotion-title"><?php _e($extension['boxTitle'], YRM_LANG);?></span>
                <?php if(!empty($extension['videoURL'])): ?>
                <span class="yrm-play-promotion-video" data-href="<?php echo esc_attr($extension['videoURL']); ?>"></span>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
<div class="yrm-add-new-extensions-wrapper">
	<span class="yrm-add-new-extensions">
		More plugins
	</span>
</div>
<div class="yrm-add-new-plugins">
	<?php require_once(dirname(__FILE__).'/morePlugins.php')?>
</div>

