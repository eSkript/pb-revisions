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
	'textarea_rows' => 10
);
?>
<form action="<?php echo esc_url($form_url) ?>" method="POST">
<div class="pbr-header">
	<div class="pbr-header__content">
		<div class="pdr-footer__grow"></div>
		<div>
			<h1><?php printf(__('Version %s', 'pb-revisions'), esc_html($data['version']->number));?></h1>
		</div>
		<div class="pdr-footer__grow"></div>
		<div class="pbr-header__align-center">
			<button type="submit" class="button<?php echo !$draft ? ' button-primary' :'' ?>" name="pb_revisions_action" value="save_version"><?php _e('Save and Exit', 'pb-revisions');?></button>
			<?php if($draft){?>
				<button type="submit" class="button button-primary" name="pb_revisions_action" value="publish_version"  onclick="if ( !confirm('<?php esc_attr_e( 'Are you sure you want to publish this version? There is no way back!', 'pb-revisions' ); ?>' ) ) { return false }"><?php _e('Publish', 'pb-revisions');?></button>
			<?php } ?>
		</div>
	</div>
</div>
<div class="wrap">
		<input type="hidden" name="pb_revisions_version" value="<?php echo esc_attr($data['version']->ID)?>">
		
		<h2><?php _e('Summary', 'pb-revisions');?></h2>
		<?php echo wp_editor( $data['version']->comment, 'pb_revisions_comment', $settings ); ?>
		<h2><?php _e('Chapters', 'pb-revisions');?></h2>
		<div class="pb_revisions_version__chapters">
		<?php foreach ( $data['chapters'] as $chapter ) : ?>
			<div class="pb_revisions_version__chapter">
			<h3><?php echo esc_html($chapter->title());?> <a class="button pb_revisions_version__edit_button" href="<?php echo esc_url($chapter_url.$chapter->chapter)?>"><?php _e('Edit', 'pb-revisions');?></a></h3>
			<?php pb_revision_the_content($chapter->title_comment)?>
			<?php foreach ( $chapter->comments as $comment ) : ?>
				<?php pb_revision_the_content($comment)?>
			<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
		</div>
		<?php if(!$data['has_saved_chapters'] && !empty($data['first_chapter'])){
					$next_chapter_url = get_admin_url( get_current_blog_id(), "/admin.php?page=pb_revisions&pb_revisions_view=chapter_diff&pb_revisions_version={$data['version']->ID}&pb_revisions_chapter={$data['first_chapter']->chapter}" );	
				?>
				<p>
					<button type="submit" formaction="<?php echo esc_url($next_chapter_url) ?>" class="button" name="pb_revisions_action" value="save_version">
						<?php _e('Add chapter comments', 'pb-revisions');?>
					</button>
				</p>
			<?php } ?>
</div>

<?php if($data['has_saved_chapters'] && !empty($data['chapters'])){?>

<div class="pbr-footer">
	<div class="pbr-footer__content">
		<div class="pdr-footer__grow"></div>
		<div class="pbr_go_to_chapter_list">
			<a href="#" id="pbr_go_to_chapter_list__toggle_button">
						<?php _e('Go to', 'pb-revisions');?>
			</a>
			<div id="pbr_go_to_chapter_list__list" class="pbr_go_to_chapter_list__list" style="display: none;">
				<?php foreach ( $data['allChapters'] as $list_chapter ) : ?>
						<button type="submit" formaction="<?php echo esc_url($chapter_url.$list_chapter->chapter)?>" class="button pbr_go_to_chapter_list__chapter_button" name="pb_revisions_action" value="save_chapter">
								<?php esc_html_e($list_chapter->title());?>
						</button>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="pdr-footer__grow"></div>
	</div>
</div>

<?php } ?>

</form>