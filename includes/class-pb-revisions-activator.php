<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/lukaiser
 * @since      1.0.0
 *
 * @package    Pb_Revisions
 * @subpackage Pb_Revisions/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Pb_Revisions
 * @subpackage Pb_Revisions/includes
 * @author     Lukas Kaiser <reg@lukaiser.com>
 */
class Pb_Revisions_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate($networkwide) {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-pb-revisions-public.php';
		Pb_Revisions_Public::register_tables();

		if (function_exists('is_multisite') && is_multisite()) {
			// check if it is a network activation - if so, run the activation function for each blog id
			if ($networkwide) {
				// Get all blog ids
				$site_ids = get_sites( array( 'fields' => 'ids', 'network_id' => get_current_network_id() ) );
				foreach ( $site_ids as $site_id ) {
					switch_to_blog( $site_id );
					self::activate_one();
					restore_current_blog();
				}
				return;
			}   
		} 
		self::activate_one();  
	}

	/**
	 * Activate plugin for one blog
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private static function activate_one(){
		global $wpdb;
		// Detect the charset and collation of the database.
		$charset_collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
		  if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		  }
		  if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE $wpdb->collate";
		  }
		}

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$sql_version = file_get_contents(plugin_dir_path( dirname( __FILE__ ) ) . 'includes/sql/pb-revisions-version-1.0.0.sql');

		$vars_version = array(
			'{$table_name}' => $wpdb->pb_revisions_version,
			'{$pb_revisions_chapter}' => $wpdb->pb_revisions_chapter,
			'{$charset_collate}'      => $charset_collate
		);
		  
		$sql_version = strtr($sql_version, $vars_version);

		dbDelta($sql_version);

		$sql_chapter = file_get_contents(plugin_dir_path( dirname( __FILE__ ) ) . 'includes/sql/pb-revisions-chapter-1.0.0.sql');

		$vars_chapter = array(
			'{$table_name}' => $wpdb->pb_revisions_chapter,
			'{$charset_collate}'      => $charset_collate
		);
		  
		$sql_chapter = strtr($sql_chapter, $vars_chapter);

		dbDelta($sql_chapter);
		add_option( 'pb_revisions_db_version', '1.0.0' );
	}

	/**
	 * Activate new blog
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param	 int	$blog_id	The blog id of the new blog
	 */
	public static function activate_new_blog($blog_id){
		if (is_plugin_active_for_network('pressbooks-revisions/pressbooks-revisions.php')) {
			switch_to_blog( $blog_id );
			self::activate_one();
			restore_current_blog();
		}
	}
}
