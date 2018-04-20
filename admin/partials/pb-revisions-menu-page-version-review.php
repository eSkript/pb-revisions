<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$form_url = get_admin_url( get_current_blog_id(), '/admin.php?page=pb_revisions' );
$summary_url = get_admin_url( get_current_blog_id(), "/admin.php?page=pb_revisions&pb_revisions_view=version_summary&pb_revisions_version={$data['version']->ID}" );
$chapter_url = get_admin_url( get_current_blog_id(), "/admin.php?page=pb_revisions&pb_revisions_view=chapter_diff&pb_revisions_version={$data['version']->ID}&pb_revisions_chapter=" );
$draft = $data['version']->draft;
function pb_revision_the_content($content){
	$content = apply_filters( 'the_content', $content );
    echo wp_kses_post(str_replace( ']]>', ']]&gt;', $content ));
}
?>
<div class="wrap">
	<form action="<?php echo esc_url($form_url) ?>" method="POST">
		<input type="hidden" name="pb_revisions_version" value="<?php echo esc_attr($data['version']->ID)?>">
		<h1>Version <?php echo esc_html($data['version']->number)?> Review</h1>
		<h2>Summary <a href="<?php echo esc_url($summary_url)?>">Edit</a></h2>
		<?php pb_revision_the_content($data['version']->comment)?>
		<?php foreach ( $data['chapters'] as $chapter ) : ?>
			<h3>Chapter: <?php echo esc_html($chapter->title())?> <a href="<?php echo esc_url($chapter_url.$chapter->chapter)?>">Edit</a></h3>
			<?php pb_revision_the_content($chapter->title_comment)?>
			<?php foreach ( $chapter->comments as $comment ) : ?>
				<?php pb_revision_the_content($comment)?>
			<?php endforeach; ?>
		<?php endforeach; ?>

		<button type="submit" class="button button-hero" name="pb_revisions_action" value="nothing">Save and Exit</button>
		<?php if($draft){?>
			<button type="submit" class="button button-hero button-primary" name="pb_revisions_action" value="publish_version"  onclick="if ( !confirm('<?php esc_attr_e( 'Are you sure you want to publish this version? There is no way back!', 'pb_revisions' ); ?>' ) ) { return false }">Publish</button>
		<?php } ?>
	</form>
</div>