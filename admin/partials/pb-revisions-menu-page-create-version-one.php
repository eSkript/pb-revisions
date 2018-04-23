<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$form_url = get_admin_url( get_current_blog_id(), '/admin.php?page=pb_revisions' )
?>
<div class="wrap">
<h1><?php _e('Version 1.0.0', 'pb-revisions');?></h1>
<?php _e('<p>Revisions enables you to track the changes in your eSkript and enables you to work on the next version of it while the readers still see the last.</p>
<p>Is your book ready for Version 1.0.0?<br/>
If so, publish it now!<br/>
But keep in mind, you can’t change a published version. If you find an error or want to add something, you have to create a new revision.</p>', 'pb-revisions');?>
<form action="<?php echo esc_url($form_url) ?>" method="POST">
<button type="submit" class="button button-hero button-primary generate" name="pb_revisions_action" value="publish_version_one"  onclick="if ( !confirm('<?php esc_attr_e( 'Are you sure you want to publish this version? There is no way back!', 'pb-revisions' ); ?>' ) ) { return false }"><?php _e('Publish Version 1.0.0', 'pb-revisions');?></button>
</form>
</div>