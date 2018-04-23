<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$summary_url = get_admin_url( get_current_blog_id(), "/admin.php?page=pb_revisions&pb_revisions_view=version_summary&pb_revisions_version={$data['version']->ID}" );
?>
<div class="wrap">
	<h1><?php _e('What type of comments do you want to add?', 'pb-revisions');?></h1>
	<div>
		<?php if(!empty($data['first_chapter'])){
			$next_chapter_url = get_admin_url( get_current_blog_id(), "/admin.php?page=pb_revisions&pb_revisions_view=chapter_diff&pb_revisions_version={$data['version']->ID}&pb_revisions_chapter={$data['first_chapter']->chapter}" );
		?>
			<a class="button button-hero button-primary generate right" href=<?php echo esc_url($next_chapter_url);?>><?php _e('Chapter Specific Comments', 'pb-revisions');?></a>
		<?php } ?>
		<a class="button button-hero button-primary generate right" href=<?php echo esc_url($summary_url);?>><?php _e('Just One Comment For Everything', 'pb-revisions');?></a>	
	</div>
</div>