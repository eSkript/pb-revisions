<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$form_url = get_admin_url( get_current_blog_id(), '/admin.php?page=pb_revisions' );
$chapter_url = get_admin_url( get_current_blog_id(), "/admin.php?page=pb_revisions&pb_revisions_view=chapter_diff&pb_revisions_version={$data['version']->ID}&pb_revisions_chapter=" );
$draft = $data['version']->draft;
function pb_revision_the_content($content){
	$content = apply_filters( 'the_content', $content );
    echo wp_kses_post(str_replace( ']]>', ']]&gt;', $content ));
}
$settings = array(
	'textarea_rows' => 6
);
?>
<div class="wrap">
	<form action="<?php echo esc_url($form_url) ?>" method="POST">
		<input type="hidden" name="pb_revisions_version" value="<?php echo esc_attr($data['version']->ID)?>">
		<h1><?php printf(__('Summary Version %s', 'pb-revisions'), esc_html($data['version']->number));?></h1>
		<?php _e('<p>Write here a short summary of the changes since the last version.</p>', 'pb-revisions');?>
		<?php echo wp_editor( $data['version']->comment, 'pb_revisions_comment', $settings ); ?>
		<?php foreach ( $data['chapters'] as $chapter ) : ?>
			<h3><?php printf(__('Chapter: %s', 'pb-revisions'), esc_html($chapter->title()));?> <a href="<?php echo esc_url($chapter_url.$chapter->chapter)?>"><?php _e('Edit', 'pb-revisions');?></a></h3>
			<?php pb_revision_the_content($chapter->title_comment)?>
			<?php foreach ( $chapter->comments as $comment ) : ?>
				<?php pb_revision_the_content($comment)?>
			<?php endforeach; ?>
		<?php endforeach; ?>
		<?php if(!$data['has_saved_chapters'] && !empty($data['first_chapter'])){
			$next_chapter_url = get_admin_url( get_current_blog_id(), "/admin.php?page=pb_revisions&pb_revisions_view=chapter_diff&pb_revisions_version={$data['version']->ID}&pb_revisions_chapter={$data['first_chapter']->chapter}" );	
		?>
			<button type="submit" formaction="<?php echo esc_url($next_chapter_url) ?>" class="button button-hero" name="pb_revisions_action" value="save_version">
				<?php _e('Add chapter comments', 'pb-revisions');?>
			</button>
		<?php } ?>
		<button type="submit" class="button button-hero" name="pb_revisions_action" value="nothing"><?php _e('Save and Exit', 'pb-revisions');?></button>
		<?php if($draft){?>
			<button type="submit" class="button button-hero button-primary" name="pb_revisions_action" value="publish_version"  onclick="if ( !confirm('<?php esc_attr_e( 'Are you sure you want to publish this version? There is no way back!', 'pb_revisions' ); ?>' ) ) { return false }"><?php _e('Publish', 'pb-revisions');?></button>
		<?php } ?>
	</form>
</div>