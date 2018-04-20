<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$form_url = get_admin_url( get_current_blog_id(), '/admin.php?page=pb_revisions' );
$chapter_url = get_admin_url( get_current_blog_id(), "/admin.php?page=pb_revisions&pb_revisions_view=chapter_diff&pb_revisions_version={$data['chapter']->version->ID}&pb_revisions_chapter={$data['chapter']->chapter}" );
if($data['chapter']->next_chapter_ID){
	$next_url = get_admin_url( get_current_blog_id(), "/admin.php?page=pb_revisions&pb_revisions_view=chapter_diff&pb_revisions_version={$data['chapter']->version->ID}&pb_revisions_chapter={$data['chapter']->next_chapter_ID}" );
	$has_next = true;
}else{
	$next_url = get_admin_url( get_current_blog_id(), "/admin.php?page=pb_revisions&pb_revisions_view=version_review&pb_revisions_version={$data['chapter']->version->ID}" );
	$has_next = false;
}
if($data['chapter']->prev_chapter_ID){
	$prev_url = get_admin_url( get_current_blog_id(), "/admin.php?page=pb_revisions&pb_revisions_view=chapter_diff&pb_revisions_version={$data['chapter']->version->ID}&pb_revisions_chapter={$data['chapter']->prev_chapter_ID}" );
	$has_prev = true;
}else{
	$prev_url = get_admin_url( get_current_blog_id(), "/admin.php?page=pb_revisions&pb_revisions_view=version_summary&pb_revisions_version={$data['chapter']->version->ID}" );
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
<div class="wrap">
	<form action="<?php echo esc_url($form_url) ?>" method="POST">
		<input type="hidden" name="pb_revisions_chapter" value="<?php echo esc_attr($data['chapter']->chapter)?>">
		<input type="hidden" name="pb_revisions_version" value="<?php echo esc_attr($data['chapter']->version->ID)?>">
		<?php if(!$data['chapter']->anything_changed()){?>
			<div class="notice">
				<h3>Unnecessary comments!</h3>
				<p>
					This chapter doesn't have any changes compared to the last version but does have change comments.
					We recomend you to delete the comments.
				</p>
				<button type="submit" formaction="<?php echo esc_url($next_url) ?>" class="button button-hero button-primary" name="pb_revisions_action" value="delete_chapter">
					Delete the comments!
				</button>
			</div>
		<?php } ?>
		<?php if($data['chapter']->contend_new_changed_since_draft() && $data['chapter']->anything_changed()){?>
			<div class="notice">
				<h3>Chapter changed!</h3>
				<p>
					This chapter has changed since you last edited the comments of it.
					The comments might be outdated. Please check them.
				</p>
				<button type="submit" formaction="<?php echo esc_url($chapter_url) ?>" class="button" name="pb_revisions_action" value="force_save_chapter">
					Discard
				</button>
			</div>
		<?php } ?>
		
		<h1>Chapter: <?php echo esc_html($data['chapter']->title()) ?></h1>

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
					<th>Old</th>
					<th>New</th>
					<th>Add your comments:</th>
				</tr>
			</thead>
			<tbody>
				<tr class="pb_rev_diff_row">
					<?php $text_diff->render_line($data['chapter']->title_old, $data['chapter']->title_new);?>
					<td rowspan="3"><?php echo wp_editor( $data['chapter']->title_comment, 'pb_revisions_title_comment', $settings ); ?></td>
				</tr>
				<tr class="pb_rev_diff_row">
					<td class="pb_rev_diff_cell">
						<span class="dashicons dashicons-admin-site"></span> Web Book:
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
						<span class="dashicons dashicons-admin-site"></span> Web Book:
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
						<span class="dashicons dashicons-migrate"></span> Export:
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
						<span class="dashicons dashicons-migrate"></span> Export:
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


		<button type="submit" formaction="<?php echo esc_url($prev_url) ?>" class="button button-hero" name="pb_revisions_action" value="save_chapter">
			<?php if($has_prev){?>
				Previous
			<?php } else { ?>
				Back to Summary
			<?php } ?>
		</button>
		<button type="submit" class="button button-hero" name="pb_revisions_action" value="save_chapter">
			Save and Exit
		</button>
		<button type="submit" formaction="<?php echo esc_url($next_url) ?>" class="button button-hero <?php echo $data['chapter']->anything_changed() ? 'button-primary' : ''?>" name="pb_revisions_action" value="save_chapter">
			<?php if($has_next){?>
				Next
			<?php } else { ?>
				To Review
			<?php } ?>
		</button>
	</form>
</div>