<?php
$export_delete_url = wp_nonce_url( get_admin_url( get_current_blog_id(), '/admin.php?page=pb_export' ), 'pb-delete-export' );
$download_url_prefix = get_admin_url( get_current_blog_id(), '/admin.php?page=pb_export&download_export_file=' );
$timezone_string = get_blog_option( 1, 'timezone_string' );
$date_format = get_blog_option( 1, 'date_format', 'F j, Y' );
$time_format = get_blog_option( 1, 'time_format', 'g:i a' );

if ( $timezone_string ) {
	date_default_timezone_set( $timezone_string );
} else {
	date_default_timezone_set( 'America/Montreal' );
}
$c = 0; // start counter
	$files = \PBRevisions\includes\Export::group_exports();
	foreach ( $files as $exports ) {
		if($exports['version'] == "working"){
			$version_title = "Working Version";
		}else{
			$version_title = "Version: ". $exports['version'];
		}
		
		// Echo files to screen
		if ( 0 == $c ) { ?>
		<div class="export-files latest">
		<div class="export-files__date"><?php printf( _x( '%1$s at %2$s', 'Date and time string, e.g. "January 1, 2016 at 12:00pm', 'pressbooks' ), date( $date_format, $exports['date'] ), date( $time_format, $exports['date'] ) ); ?></div>
		<h2><?php _e( 'Latest Export', 'pressbooks' ); ?> - <?php echo $version_title;?></h2>
		
	<?php } elseif ( $c > 0 ) { ?>
		<div class="export-files">
		<div class="export-files__date"><?php printf( _x( '%1$s at %2$s', 'Date and time string, e.g. "January 1, 2016 at 12:00pm', 'pressbooks' ), date( $date_format, $exports['date'] ), date( $time_format, $exports['date'] ) ); ?></div>
		<h3><?php echo $version_title;?></h3>
		
	<?php }
foreach ( $exports['files'] as $file ) {
	$file_extension = substr( strrchr( $file['file'], '.' ), 1 );
	switch ( $file_extension ) {
		case 'epub':
			$pre_suffix = strstr( $file['file'], '._3.epub' );
			break;
		case 'pdf':
			$pre_suffix = strstr( $file['file'], '._print.pdf' );
			break;
		case 'xml':
			$pre_suffix = strstr( $file['file'], '._vanilla.xml' );
			break;
		default:
			$pre_suffix = false;
	}
	if ( 'html' == $file_extension ) {
		$file_class = 'xhtml';
	} elseif ( 'xml' == $file_extension && '._vanilla.xml' == $pre_suffix ) {
		$file_class = 'vanillawxr';
	} elseif ( 'xml' == $file_extension && false == $pre_suffix ) {
		$file_class = 'wxr';
	} elseif ( 'epub' == $file_extension && '._3.epub' == $pre_suffix ) {
		$file_class = 'epub3';
	} elseif ( 'pdf' == $file_extension && '._print.pdf' == $pre_suffix ) {
		$file_class = 'print-pdf';
	} else {
		/**
		 * @since 3.9.8
		 * Map custom export format file extensions to their CSS class.
		 *
		 * For example, here's how one might set the CSS class for a .docx file:
		 *
		 * add_filter( 'pb_get_export_file_class', function ( $file_extension ) {
		 * 	if ( 'docx' == $file_extension ) {
		 *		return 'word';
		 * 	}
		 *	return $file_extension;
		 * } );
		 *
		 */
		$file_class = apply_filters( 'pb_get_export_file_class', $file_extension );
	}

	$public_class = ($file['public']) ? " public": '';
?>
	<form class="export-file" action="<?php echo $export_delete_url; ?>" method="post">
		<input type="hidden" name="filename" value="<?php echo $file['file']; ?>" />
		<input type="hidden" name="delete_export_version" value="<?php echo $exports['folder']?>" />
		<input type="hidden" name="delete_export_file" value="true" />
		<div class="export-file-container">
	<a class="export-file" href="<?php echo ( $download_url_prefix . $file['file'].'&download_export_version='.$exports['folder'] ); ?>"><span class="export-file-icon <?php echo ( 0 == $c ? 'large' : 'small' ); ?> <?php echo $file_class; echo $public_class; ?>" title="<?php echo esc_attr( $file['file'] ); ?>"></span></a>
	<div class="file-actions">
		<a href="<?php echo ( $download_url_prefix . $file['file'].'&download_export_version='.$exports['folder'] ); ?>"><span class="dashicons dashicons-download"></span></a>
		<button class="delete" type="submit" name="submit" src="" value="Delete" onclick="if ( !confirm('<?php esc_attr_e( 'Are you sure you want to delete this?', 'pressbooks' ); ?>' ) ) { return false }"><span class="dashicons dashicons-trash"></span></button>
	</div>
		</div>
	</form>
	<?php } ?>
	</div>
	<?php
		++$c;
	} ?>