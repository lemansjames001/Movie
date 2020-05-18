<?php
$ajaxNonce = wp_create_nonce("YrmNonce");

$pagenum = isset( $_GET['yrm-pagenum'] ) ? absint( $_GET['yrm-pagenum'] ) : 1;
global $wpdb;
$limit = YRM_NUMBER_PAGES; // number of rows in page
$offset = ($pagenum - 1) * $limit;
$total = $wpdb->get_var("SELECT COUNT(`id`) FROM {$wpdb->prefix}expm_maker");
$numOfPages = ceil($total/$limit);
$results = ReadMoreData::getAllDataByLimit($offset, $limit);
?>
<div class="ycf-bootstrap-wrapper">
<div class="wrap">
	<h2 class="add-new-buttons"><?php _e('Read More', YRM_LANG); ?><a href="<?php echo admin_url();?>admin.php?page=addNew" class="add-new-h2"><?php echo _e('Add New', YRM_LANG); ?></a></h2>
</div>
<div class="expm-wrapper">
    <div class="yrm-export-import-wrapper">
        <img src="<?php echo YRM_IMG_URL.'ajax.gif'; ?>" class="yrm-spinner yrm-hide-content">
        
        <input type="button" class="yrm-exprot-button button" value="<?php echo  _e('Export', YRM_LANG)?>">
        <input type="button" class="yrm-import-button button" data-ajaxNonce="<?php echo $ajaxNonce; ?>" value="<?php echo  _e('Import', YRM_LANG)?>">
    </div>
	<?php if(YRM_PKG == YRM_FREE_PKG): ?>
		<div class="main-view-upgrade main-upgreade-wrapper">
            <a href="<?php echo YRM_PRO_URL; ?>" target="_blank">
                <button class="yrm-upgrade-button-red">
                    <b class="h2">Upgrade</b><br><span class="h5">to PRO version</span>
                </button>
            </a>
		</div>
	<?php endif;?>
	<table class="table table-bordered expm-table">
		<tr>
			<td>Id</td>
			<td><?php _e('Title', YRM_LANG)?></td>
			<td><?php _e('Enabled', YRM_LANG)?></td>
			<td><?php _e('Type', YRM_LANG)?></td>
			<td><?php _e('Options', YRM_LANG)?></td>
		</tr>

		<?php if(empty($results)) { ?>
			<tr>
				<td colspan="4"><?php _e('No Data', YRM_LANG)?></td>
			</tr>
		<?php }
		else {
			foreach ($results as $result) { ?>
                <?php
                    $id = (int)$result['id'];
				    $title = esc_attr($result['expm-title']);
				    $type = esc_attr($result['type']);

                ?>
				<tr>
					<td><?php echo $id; ?></td>
                    <td><a href="<?php echo admin_url()."admin.php?page=button&type=".$type."&readMoreId=".$id.""?>"><?php echo $title; ?></a></td>
					<td>
                        <?php $isChecked = (ReadMore::isActiveReadMore($id) ? 'checked': ''); ?>
                        <div class="yrm-switch-wrapper">
                            <label class="yrm-switch">
                                <input type="checkbox" name="yrm-status-switch" data-id="<?= $id; ?>" class="yrm-accordion-checkbox yrm-status-switch" <?php echo $isChecked;?>>
                                <span class="yrm-slider yrm-round"></span>
                            </label>
                        </div>
                    </td>
					<td><?php echo ReadMoreAdminHelper::getTitleFromType($type); ?></td>
					<td>
						<a class="yrm-crud yrm-edit glyphicon glyphicon-edit" href="<?php echo admin_url()."admin.php?page=button&yrm_type=".$type."&readMoreId=".$id.""?>"></a>
						<a class="yrm-crud yrm-delete-link glyphicon glyphicon-remove" data-id="<?php echo $id;?>" href="<?php echo admin_url()."admin-post.php?action=delete_readmore&readMoreId=".$id.""?>"></a>
                        <a class="yrm-crud yrm-clone-link glyphicon glyphicon-duplicate" href="<?php echo admin_url();?>admin-post.php?action=read_more_clone&id=<?php echo $id; ?>" ></a>
					</td>
				</tr>
		<?php } ?>

		<?php } ?>
		<tr>
			<td>Id</td>
			<td><?php _e('Title', YRM_LANG)?></td>
			<td><?php _e('Enabled', YRM_LANG)?></td>
			<td><?php _e('Type', YRM_LANG)?></td>
			<td><?php _e('Options', YRM_LANG)?></td>
		</tr>
	</table>
    <?php
        $page_links = paginate_links( array(
            'base' => add_query_arg( 'yrm-pagenum', '%#%' ),
            'format' => '',
            'prev_text' => __( '&laquo;', 'text-domain' ),
            'next_text' => __( '&raquo;', 'text-domain' ),
            'total' => $numOfPages,
            'current' => $pagenum
        ) );

        if ( $page_links ) {
            echo '<div class="yrm-tablenav"><div class="yrm-tablenav-pages">' . $page_links . '</div></div>';
        }
    ?>
</div>
</div>