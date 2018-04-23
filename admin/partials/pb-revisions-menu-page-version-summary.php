<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$form_url = get_admin_url( get_current_blog_id(), '/admin.php?page=pb_revisions' );
$has_frist_chapter = isset($data['first_chapter']) && $data['first_chapter'] !== false;
$has_saved_chapters = $data['has_saved_chapters'];
$draft = $data['version']->draft;
?>
<div class="wrap">
	<form action="<?php echo esc_url($form_url) ?>" method="POST">
		<input type="hidden" name="pb_revisions_version" value="<?php echo esc_attr($data['version']->ID)?>">
		<h1><?php printf(__('Summary Version %s', 'pb-revisions'), esc_html($data['version']->number));?></h1>
		<?php _e('<p>Write here a short summary of the changes since the last version. You can document specific changes like „Image 2.3.4 has been update to reflect ...“ or „Error ... has been corrected“ in the following pages.</p>', 'pb-revisions');?>
		<?php echo wp_editor( $data['version']->comment, 'pb_revisions_comment' ); ?>
		<button type="submit" class="button button-hero" name="pb_revisions_action" value="save_version"><?php _e('Save and Exit', 'pb-revisions');?></button>
		<?php if(!$has_saved_chapters && $draft){?>
			<button type="submit" class="button button-hero <?php echo $has_frist_chapter ? '' : 'button-primary '?>" name="pb_revisions_action" value="save_and_publish_version"  onclick="if ( !confirm('<?php esc_attr_e( 'Are you sure you want to publish this version? There is no way back!', 'pb_revisions' ); ?>' ) ) { return false }"><?php if($has_frist_chapter){ _e('Publish w/o chapter comments', 'pb-revisions'); }else{ _e('Publish', 'pb-revisions'); } ?></button>
		<?php } ?>
		<?php if($has_frist_chapter){
			$next_chapter_url = get_admin_url( get_current_blog_id(), "/admin.php?page=pb_revisions&pb_revisions_view=chapter_diff&pb_revisions_version={$data['version']->ID}&pb_revisions_chapter={$data['first_chapter']->chapter}" );
		?>
			<button type="submit" formaction="<?php echo esc_url($next_chapter_url) ?>" class="button button-hero button-primary" name="pb_revisions_action" value="save_version">
				<?php if($has_saved_chapters){?>
					<?php _e('Next', 'pb-revisions');?>
				<?php } else { ?>
					<?php _e('Add chapter comments', 'pb-revisions');?>
				<?php } ?>
			</button>
		<?php } ?>
	</form>
</div>