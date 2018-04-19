<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$form_url = get_admin_url( get_current_blog_id(), '/admin.php?page=pb_revisions' );
$date_format = get_option( 'date_format' );
?>
<div class="wrap">
<h1>Revisions</h1>
<p>
	<span class="dashicons dashicons-admin-site"></span> Current Web Version: <?php echo $data['active_version'] ? $data['active_version']->number : "Working Version";?>
</p>
<table class="wp-list-table widefat fixed" cellspacing="0">
	<thead>
		<tr>
			<th>Date</th>
			<th>Version</th>
			<th>Author</th>
			<th>Web</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $data['versions'] as $version ) : ?>
			<tr>
				<td><?php echo $version->draft ? 'Draft' : get_date_from_gmt($version->date, $date_format);?></td>
				<td><?php echo $version->number?></td>
				<td><?php echo get_userdata( $version->author )->display_name?></td>
				<td>
					<?php if($data['active_version'] == $version) {?>
						Active
					<?php }else if(!$version->draft){?>
						<form action="<?php echo $form_url ?>" method="POST">
							<input type="hidden" name="pb_revisions_version" value="<?php echo $version->ID?>">
							<button type="submit" class="button generate" name="pb_revisions_action" value="activate_version">Activate</button>
						</form>
					<?php }?>
				</td>
				<td class="action column-action">
					<?php if($version->number != '1.0.0') {
								$version_id = $version->ID;
								$edit_url = get_admin_url( get_current_blog_id(), "/admin.php?page=pb_revisions&pb_revisions_view=version_summary&pb_revisions_version={$version_id}" );?>
						<a href=<?php echo $edit_url;?>>Edit</a>
					<?php }?>
					<?php if($version->draft) {?>
						<form action="<?php echo $form_url ?>" method="POST">
							<button type="submit" class="button generate" name="pb_revisions_action" value="delete_draft" onclick="if ( !confirm('<?php esc_attr_e( 'Are you sure you want to delete this?', 'pressbooks' ); ?>' ) ) { return false }"><span class="dashicons dashicons-trash"></span></button>
						</form>
					<?php }?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php if($data['has_draft']) {
	$version_id = $data['has_draft']->ID;
	$edit_draft_url = get_admin_url( get_current_blog_id(), "/admin.php?page=pb_revisions&pb_revisions_view=version_summary&pb_revisions_version={$version_id}" );
?>
	<a class="button button-hero button-primary generate" href=<?php echo $edit_draft_url;?>>Edit Draft</a>
<?php }else{
	$create_version_url = get_admin_url( get_current_blog_id(), "/admin.php?page=pb_revisions&pb_revisions_view=create_version" );
?>
	<a class="button button-hero button-primary generate" href=<?php echo $create_version_url;?>>Create Version from Current State</a>
<?php }?>
</div>