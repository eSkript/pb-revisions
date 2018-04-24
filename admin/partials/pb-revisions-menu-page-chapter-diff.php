<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$form_url = get_admin_url( get_current_blog_id(), '/admin.php?page=pb_revisions' );
$chapter_url = get_admin_url( get_current_blog_id(), "/admin.php?page=pb_revisions&pb_revisions_view=chapter_diff&pb_revisions_version={$data['chapter']->version->ID}&pb_revisions_chapter={$data['chapter']->chapter}" );
$other_chapter_url = get_admin_url( get_current_blog_id(), "/admin.php?page=pb_revisions&pb_revisions_view=chapter_diff&pb_revisions_version={$data['chapter']->version->ID}&pb_revisions_chapter=" );
$summary_url = get_admin_url( get_current_blog_id(), "/admin.php?page=pb_revisions&pb_revisions_view=version_summary&pb_revisions_version={$data['chapter']->version->ID}" );
if($data['chapter']->next_chapter_ID){
	$next_url = get_admin_url( get_current_blog_id(), "/admin.php?page=pb_revisions&pb_revisions_view=chapter_diff&pb_revisions_version={$data['chapter']->version->ID}&pb_revisions_chapter={$data['chapter']->next_chapter_ID}" );
	$has_next = true;
}else{
	$has_next = false;
}
if($data['chapter']->prev_chapter_ID){
	$prev_url = get_admin_url( get_current_blog_id(), "/admin.php?page=pb_revisions&pb_revisions_view=chapter_diff&pb_revisions_version={$data['chapter']->version->ID}&pb_revisions_chapter={$data['chapter']->prev_chapter_ID}" );
	$has_prev = true;
}else{
	$has_prev = false;
}
$settings = array(
	'media_buttons' => false,
	'textarea_rows' => 4,
	'quicktags' => false
);

add_filter( 'tiny_mce_before_init', 'mce_before_init_pb_diff', 10000 );
function mce_before_init_pb_diff( $init_array ) {
	$init_array['toolbar1'] = "bold,italic,underline,bullist,numlist,link,fullscreen";
	unset($init_array['toolbar2']);
	unset($init_array['toolbar3']);
	unset($init_array['toolbar4']);
	$init_array['statusbar'] = false;
	return $init_array;
}

$text_diff = new \PBRevisions\includes\view_helper\Text_Diff($data['chapter']->is_added(), $data['chapter']->is_deleted(), $data['chapter']->comments, "pb_revisions_comments", $settings);
?>
<form action="<?php echo esc_url($form_url) ?>" method="POST">
<div class="pbr-header">
	<div class="pbr-header__content">
		<div class="pbr-header__align-center">
			<button type="submit" formaction="<?php echo esc_url($summary_url) ?>" class="button" name="pb_revisions_action" value="save_chapter">
				<?php _e('To Summary', 'pb-revisions');?>
			</button>
		</div>
		<div class="pdr-footer__grow"></div>
		<div>
			<h1><?php esc_html_e($data['chapter']->title());?></h1>
		</div>
		<div class="pdr-footer__grow"></div>
		<div class="pbr-header__align-center">
			<button type="submit" class="button" name="pb_revisions_action" value="save_chapter"><?php _e('Save and Exit', 'pb-revisions');?></button>
		</div>
	</div>
</div>
<div class="wrap">
		<input type="hidden" name="pb_revisions_chapter" value="<?php echo esc_attr($data['chapter']->chapter)?>">
		<input type="hidden" name="pb_revisions_version" value="<?php echo esc_attr($data['chapter']->version->ID)?>">
		<?php if(!$data['chapter']->anything_changed()){?>
			<div class="notice">
				<h3><?php _e('Unnecessary comments!', 'pb-revisions');?></h3>
				<p>
					<?php _e("This chapter doesn't have any changes compared to the last version but does have change comments.
					We recomend you to delete the comments.", 'pb-revisions');?>
				</p>
				<button type="submit" formaction="<?php echo esc_url($next_url) ?>" class="button button-hero button-primary right" name="pb_revisions_action" value="delete_chapter">
					<?php _e('Delete the comments!', 'pb-revisions');?>
				</button>
				<p class="clear"></p>
			</div>
		<?php } ?>
		<?php if($data['chapter']->contend_new_changed_since_draft() && $data['chapter']->anything_changed()){?>
			<div class="notice">
				<h3><?php _e('Chapter changed!', 'pb-revisions');?></h3>
				<p>
					<?php _e('This chapter has changed since you last edited the comments of it. The comments might be outdated. Please check them.', 'pb-revisions');?>
				</p>
				<button type="submit" formaction="<?php echo esc_url($chapter_url) ?>" class="button right" name="pb_revisions_action" value="force_save_chapter">
					<?php _e('Discard', 'pb-revisions');?>
				</button>
				<p class="clear"></p>
			</div>
		<?php } ?>
		
		

		<table class="wp-list-table widefat fixed<?php
						if($data['chapter']->is_added()) echo " pb_rev_diff_table__added";
						if($data['chapter']->is_deleted()) echo " pb_rev_diff_table__removed";
					?>"" cellspacing="0">
			<colgroup>
				<col class="pb_rev_diff_col">
				<col class="pb_rev_diff_col<?php
						if($data['chapter']->is_added()) echo " pb_rev_diff_col__added";
						if($data['chapter']->is_deleted()) echo " pb_rev_diff_col__removed";
					?>">
				<col>
			</colgroup>
			<thead>
				<tr>
					<th><?php _e('Old', 'pb-revisions');?></th>
					<th><?php _e('New', 'pb-revisions');?></th>
					<th><?php _e('Add your comments:', 'pb-revisions');?></th>
				</tr>
			</thead>
			<tbody>
				<tr class="pb_rev_diff_row">
					<?php $text_diff->render_line($data['chapter']->title_old, $data['chapter']->title_new);?>
					<td rowspan="3"><?php echo wp_editor( $data['chapter']->title_comment, 'pb_revisions_title_comment', $settings ); ?></td>
				</tr>
				<tr class="pb_rev_diff_row">
					<td class="pb_rev_diff_cell">
						<span class="dashicons dashicons-admin-site"></span> <?php _e('Web Book:', 'pb-revisions');?>
						<?php if(!$data['chapter']->is_added()){?>
							<?php if($data['chapter']->web_statuts_old()){?>
								<span class="dashicons dashicons-yes"></span>
							<?php } else { ?>
								<span class="dashicons dashicons-no"></span>
							<?php } ?>
						<?php } ?>
					</td>
					<td class="pb_rev_diff_cell <?php
						if($data['chapter']->web_status_changed() && $data['chapter']->web_statuts_new()) echo "pb_rev_diff_cell__added";
						if($data['chapter']->web_status_changed() && !$data['chapter']->web_statuts_new()) echo "pb_rev_diff_cell__removed";
					?>">
						<span class="dashicons dashicons-admin-site"></span> <?php _e('Web Book:', 'pb-revisions');?>
						<?php if(!$data['chapter']->is_deleted()){?>
							<?php if($data['chapter']->web_statuts_new()){?>
								<span class="dashicons dashicons-yes"></span>
							<?php } else { ?>
								<span class="dashicons dashicons-no"></span>
							<?php } ?>
						<?php } ?>
					</td>
				</tr>
				<tr class="pb_rev_diff_row">
					<td class="pb_rev_diff_cell">
						<span class="dashicons dashicons-migrate"></span><?php _e('Export:', 'pb-revisions');?> 
						<?php if(!$data['chapter']->is_added()){?>
							<?php if($data['chapter']->export_status_old){?>
								<span class="dashicons dashicons-yes"></span>
							<?php } else { ?>
								<span class="dashicons dashicons-no"></span>
							<?php } ?>
						<?php } ?>
					</td>
					<td class="pb_rev_diff_cell <?php
						if($data['chapter']->export_status_changed() && $data['chapter']->export_status_new) echo "pb_rev_diff_cell__added";
						if($data['chapter']->export_status_changed() && !$data['chapter']->export_status_new) echo "pb_rev_diff_cell__removed";
					?>">
						<span class="dashicons dashicons-migrate"></span><?php _e('Export:', 'pb-revisions');?>
						<?php if(!$data['chapter']->is_deleted()){?>
							<?php if($data['chapter']->export_status_new){?>
								<span class="dashicons dashicons-yes"></span>
							<?php } else { ?>
								<span class="dashicons dashicons-no"></span>
							<?php } ?>
						<?php } ?>
					</td>
				</tr>
				<?php $text_diff->render_content($data['chapter']->content_old, $data['chapter']->content_new)?>
			</tbody>
		</table>

		
		
		

</div>

<div class="pbr-footer">
	<div class="pbr-footer__content">
		<div>
			<?php if($has_prev){?>
				<button type="submit" formaction="<?php echo esc_url($prev_url) ?>" class="button" name="pb_revisions_action" value="save_chapter">
						<?php _e('Previous', 'pb-revisions');?>
				</button>
			<?php } ?>
		</div>
		<div class="pdr-footer__grow"></div>
		<div class="pbr_go_to_chapter_list">
			<a href="#" id="pbr_go_to_chapter_list__toggle_button">
						<?php _e('Go to', 'pb-revisions');?>
			</a>
			<div id="pbr_go_to_chapter_list__list" class="pbr_go_to_chapter_list__list" style="display: none;">
				<?php foreach ( $data['chapters'] as $list_chapter ) : ?>
					<?php if($data['chapter']->chapter != $list_chapter->chapter){?>
						<button type="submit" formaction="<?php echo esc_url($other_chapter_url.$list_chapter->chapter)?>" class="button pbr_go_to_chapter_list__chapter_button" name="pb_revisions_action" value="save_chapter">
								<?php esc_html_e($list_chapter->title());?>
						</button>
					<?php }?>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="pdr-footer__grow"></div>
		<div>
			<?php if($has_next){?>
				<button type="submit" formaction="<?php echo esc_url($next_url) ?>" class="button <?php echo $data['chapter']->anything_changed() ? 'button-primary' : ''?>" name="pb_revisions_action" value="save_chapter">
						<?php _e('Next', 'pb-revisions');?>
				</button>
			<?php } else { ?>
				<button type="submit" formaction="<?php echo esc_url($summary_url) ?>" class="button button-primary" name="pb_revisions_action" value="save_chapter">
					<?php _e('To Summary', 'pb-revisions');?>
				</button>
			<?php } ?>
		</div>
	</div>
</div>

</form>