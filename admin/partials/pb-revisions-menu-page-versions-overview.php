<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$form_url = get_admin_url( get_current_blog_id(), '/admin.php?page=pb_revisions' );
$date_format = get_option( 'date_format' );
?>
<div class="wrap">
<h1><?php _e('Revisions', 'pb-revisions');?></h1>
<?php do_action( 'admin_notices' );?>
<p>
	<span class="dashicons pbricons-revision"></span> <?php printf(__('Active Version: %s', 'pb-revisions'), esc_html($data['active_version'] ? $data['active_version']->number : ""));?>
</p>
<table class="wp-list-table widefat fixed" cellspacing="0">
	<thead>
		<tr>
			<th><?php _e('Date', 'pb-revisions');?></th>
			<th><?php _e('Version', 'pb-revisions');?></th>
			<th><?php _e('Author', 'pb-revisions');?></th>
			<th><?php _e('Active', 'pb-revisions');?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $data['versions'] as $version ) : ?>
			<tr class="<?php echo ($data['active_version'] == $version) ? 'version_table__active_version' : ''; ?>">
				<td><?php echo esc_html($version->draft ? __('Draft', 'pb-revisions') : get_date_from_gmt($version->date, $date_format));?></td>
				<td><?php echo esc_html($version->number)?></td>
				<td><?php echo esc_html(get_userdata( $version->author )->display_name)?></td>
				<td>
					<?php if($data['active_version'] == $version) {?>
						<span class="dashicons pbricons-revision__white"></span> <?php _e('Active', 'pb-revisions');?> 
					<?php }else if(!$version->draft){?>
						<form action="<?php echo esc_url($form_url) ?>" method="POST">
							<input type="hidden" name="pb_revisions_version" value="<?php echo esc_attr($version->ID)?>">
							<button type="submit" class="button generate" name="pb_revisions_action" value="activate_version"><?php _e('Activate', 'pb-revisions');?></button>
						</form>
					<?php }?>
				</td>
				<td class="action column-action">
					<?php if($version->number != '1.0.0') {
								$version_id = $version->ID;
								$edit_url = get_admin_url( get_current_blog_id(), "/admin.php?page=pb_revisions&pb_revisions_view=version_summary&pb_revisions_version={$version_id}" );?>
						<a class="button generate" href=<?php echo esc_url($edit_url);?>><?php _e('Edit', 'pb-revisions');?></a>
					<?php }?>
					<?php if($version->draft) {?>
						<form class="inline_form" action="<?php echo esc_url($form_url) ?>" method="POST">
							<button type="submit" class="button generate" name="pb_revisions_action" value="delete_draft" onclick="if ( !confirm('<?php esc_attr_e( 'Are you sure you want to delete this?', 'pressbooks' ); ?>' ) ) { return false }"><?php _e('Delete', 'pb-revisions');?></button>
						</form>
					<?php }?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<p>
<?php if($data['has_draft']) {
	$version_id = $data['has_draft']->ID;
	$edit_draft_url = get_admin_url( get_current_blog_id(), "/admin.php?page=pb_revisions&pb_revisions_view=version_summary&pb_revisions_version={$version_id}" );
?>
	<a class="button button-hero button-primary generate right" href=<?php echo esc_url($edit_draft_url);?>><?php _e('Edit Draft', 'pb-revisions');?></a>
<?php }else{
	$create_version_url = get_admin_url( get_current_blog_id(), "/admin.php?page=pb_revisions&pb_revisions_view=create_version" );
?>
	<a class="button button-hero button-primary generate right" href=<?php echo esc_url($create_version_url);?>><?php _e('Create Version from Current State', 'pb-revisions');?></a>
<?php }?>
</p>
</div>