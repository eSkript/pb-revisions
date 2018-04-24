<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$form_url = get_admin_url( get_current_blog_id(), '/admin.php?page=pb_revisions&noheader=true' )
?>
<div class="wrap">
	<h1><?php _e('What type of revision do you want to add?', 'pb-revisions');?></h1>
	<div class="select_version">
		<div class="select_version__versions">
			<h2><?php echo esc_html($data['major_number']); ?></h2>
			<h1><?php _e('Major', 'pb-revisions');?></h1>
			<img src="<?php echo PB_REVISIONS_PLUGIN_URL.'admin/images/Major.svg'?>" width="100%">
			<?php _e('<p>Did you reorganized the book, changed the way the book is structured and how the content is presentend?</p>
			<p>Then you should create a Major Revision.</p>', 'pb-revisions');?>
			<form class="select_version__create_form" action="<?php echo esc_url($form_url) ?>" method="POST">
				<input type="hidden" name="pb_revisions_type" value="major">
				<button type="submit" class="button button-hero button-primary generate" name="pb_revisions_action" value="create_version"><?php printf(__('Create %s', 'pb-revisions'), esc_html($data['major_number']));?></button>
			</form>
		</div>
		<div class="select_version__versions">
			<h2><?php echo esc_html($data['minor_number']); ?></h2>
			<h1><?php _e('Minor', 'pb-revisions');?></h1>
			<img src="<?php echo PB_REVISIONS_PLUGIN_URL.'admin/images/Minor.svg'?>" width="100%">
			<?php _e('<p>Did you add some additional content within the existing structure?</p>
			<p>Then you should create a Minor Revision.</p>', 'pb-revisions');?>
			<form class="select_version__create_form" action="<?php echo esc_url($form_url) ?>" method="POST">
				<input type="hidden" name="pb_revisions_type" value="minor">
				<button type="submit" class="button button-hero button-primary generate" name="pb_revisions_action" value="create_version"><?php printf(__('Create %s', 'pb-revisions'), esc_html($data['minor_number']));?></button>
			</form>
		</div>
		<div class="select_version__versions">
			<h2><?php echo esc_html($data['patch_number']); ?></h2>
			<h1><?php _e('Patch', 'pb-revisions');?></h1>
			<img src="<?php echo PB_REVISIONS_PLUGIN_URL.'admin/images/Patch.svg'?>" width="100%">
			<?php _e('<p>Did you correct some errors, clarifie some parts or just remove some typos?</p>
			<p>Then you should create a Patch Revision.</p>', 'pb-revisions');?>
			<form class="select_version__create_form" action="<?php echo esc_url($form_url) ?>" method="POST">
				<input type="hidden" name="pb_revisions_type" value="patch">
				<button type="submit" class="button button-hero button-primary generate" name="pb_revisions_action" value="create_version"><?php printf(__('Create %s', 'pb-revisions'), esc_html($data['patch_number']));?></button>
			</form>
		</div>
	</div>
</div>