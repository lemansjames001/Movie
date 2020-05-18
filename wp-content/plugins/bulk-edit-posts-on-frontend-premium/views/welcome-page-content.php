<?php
$frontend_editor_instance = vgse_frontend_editor();

$frontend_editor_instance->auto_setup();
$first_editor_id = $frontend_editor_instance->_get_first_post();
$post_edit_url = admin_url('post.php?action=edit&post=' . $first_editor_id);
?>
<script>
	window.location.href = <?php echo json_encode($post_edit_url); ?>;
</script>
<?php
exit();
?>


<p><?php _e('Thank you for installing our plugin. You can start using it in 5 minutes. Please follow these steps:', $frontend_editor_instance->textname); ?></p>

<?php
// Disable core plugin welcome page.
add_option('vgse_welcome_redirect', 'no');

$steps = array();

$missing_plugins = array();


if ($first_editor_id) {
	$steps['use_shortcode'] = '<p>' . sprintf(__('Add this shortcode to a full-width page: [vg_sheet_editor editor_id="%s"] and it works automatically.', $frontend_editor_instance->textname), $first_editor_id) . '</p>';
	$steps['settings'] = '<p>' . sprintf(__('<a href="%s" target="_blank" class="button quick-settings-button">Quick Settings</a>', $frontend_editor_instance->textname), $post_edit_url) . '</p>';
} else {
	$steps['create_first_editor'] = '<p>' . sprintf(__('Fill the settings. <a href="%s" target="_blank" class="button">Click here</a>', $frontend_editor_instance->textname), admin_url('post-new.php?post_type=' . VGSE_EDITORS_POST_TYPE)) . '</p>';
}

$steps = apply_filters('vg_sheet_editor/frontend_editor/welcome_steps', $steps);

if (!empty($steps)) {
	echo '<ol class="steps">';
	foreach ($steps as $key => $step_content) {
		if (empty($step_content)) {
			continue;
		}
		?>
		<li class="<?php echo $key; ?>"><?php echo $step_content; ?></li>		
		<?php
	}

	echo '</ol>';
}
?>	
<style>
	.steps li {
		display: none;
	}
	.steps li:first-child {
		display: block;
	}
</style>