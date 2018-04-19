<?php

/**
 * The controller the version to display
 *
 * @link       https://github.com/lukaiser
 * @since      1.0.0
 *
 * @package    Pb_Revisions
 * @subpackage Pb_Revisions/includes
 */

/**
 * The controller for the menu page
 *
 * Controlles the whole flow of the revisons admin section
 *
 * @package    Pb_Revisions
 * @subpackage Pb_Revisions/includes
 * @author     Lukas Kaiser <reg@lukaiser.com>
 */

namespace PBRevisions\includes;

class Version_Controller {
    /**
	 * Should a Revisioned Version be shown
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return	boolean
	 */
	public static function show_revisioned_version(){
		global $wp;
		if(is_admin() && isset($_POST['export_formats'])) return true;
		if(self::is_export_download()) return true;
		if(self::is_export_deletion()) return true;
		if(is_admin()) return false;
		if(isset($wp) && is_array($wp->query_vars) && array_key_exists( 'preview', $wp->query_vars ) && self::can_user_see_working_version()) return false;
		if(self::can_user_see_working_version() && get_user_meta( get_current_user_id(), "pb_revisions_show_working_version", true )) return false;
		return true;
    }
    
	/**
	 * Is Export
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return	boolean
	 */
	public static function is_export(){
		if(is_admin() && isset($_POST['export_formats']) && current_user_can( "edit_posts" )) return true;
		global $wp;
		$exporter = new \Pressbooks\Modules\Export\WordPress\Wxr(array());
		$timestamp = absint( @$_REQUEST['timestamp'] );
		$hashkey = @$_REQUEST['hashkey'];
		if(isset($wp) && is_array($wp->query_vars) && array_key_exists( 'format', $wp->query_vars ) && $exporter->verifyNonce( $timestamp, $hashkey )) return true;
		return false;
	}

	/**
	 * Is Export Download in Admin
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return	boolean
	 */
	public static function is_export_download(){
		return (is_admin() &&
		   isset($_GET['page']) &&
		   $_GET['page'] == "pb_export" &&
		   ! empty( $_GET['download_export_file'] ) &&
		   isset($_GET['download_export_version']) &&
		   current_user_can( "edit_posts" ));
	}

	/**
	 * Is Export Deletion
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return	boolean
	 */
	public static function is_export_deletion(){
		return (is_admin() &&
		   isset($_GET['page']) &&
		   $_GET['page'] == "pb_export" &&
		   isset( $_POST['delete_export_file'] ) &&
		   isset($_POST['delete_export_version']) &&
		   current_user_can( "edit_posts" ));
	}

	/**
	 * Is user alowed to see working version
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return	boolean
	 */
	public static function can_user_see_working_version(){
		global $post;
		return (current_user_can( "edit_posts" ) || 
					(is_single() && 
					 isset($post) &&
					 current_user_can('edit_post', $post->ID)
					)
				);
	}

	/**
	 * Version to show
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return	boolean
	 */
	public static function version_to_show(){
		if(self::show_revisioned_version()){
            $store = new \PBRevisions\includes\Store();
			if(self::is_export()){
				return $store->get_active_export_version_number();
			}else{
				return $store->get_active_version_number();
			}
		}else{
			return false;
		}
	}
}