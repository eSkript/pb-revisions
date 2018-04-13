<?php

/**
 * The controller for the menu page
 *
 * @link       https://github.com/lukaiser
 * @since      1.0.0
 *
 * @package    Pb_Revisions
 * @subpackage Pb_Revisions/admin
 */

/**
 * The controller for the menu page
 *
 * Controlles the whole flow of the revisons admin section
 *
 * @package    Pb_Revisions
 * @subpackage Pb_Revisions/admin
 * @author     Lukas Kaiser <reg@lukaiser.com>
 */

namespace PBRevisions\admin;

class Menu_Page_Controller {
    /**
	 * The Store
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $store    The Store
	 */
    protected $store = "PB-Revisions-Version";


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct(  ) {
        $this->store = new \PBRevisions\includes\Store();
	}

	/**
	 * Prozess actions
	 *
	 * @since    1.0.0
	 */
	public function actions() {
        if(!$this->allowed()) return ;
        if ( isset($_GET['page']) && $_GET['page'] == 'pb_revisions' && isset($_POST['pb_revisions_action']) ) {
			if($_POST['pb_revisions_action'] == 'publish_version_one'){
				$this->action_publish_version_one();
			}
			if($this->store->number_of_versions() > 0){
				if($_POST['pb_revisions_action'] == 'activate_version'){
					$this->action_activate_version();
				}
				if($_POST['pb_revisions_action'] == 'activate_export_version'){
					$this->action_activate_export_version();
				}
				if($_POST['pb_revisions_action'] == 'delete_draft'){
					$this->action_delete_draft();
				}
				if($_POST['pb_revisions_action'] == 'create_version'){
					$this->action_create_version();
				}
				if($_POST['pb_revisions_action'] == 'save_version'){
					$this->action_save_version();
				}
				if($_POST['pb_revisions_action'] == 'save_and_publish_version'){
					$this->action_save_and_publish_version();
				}
				if($_POST['pb_revisions_action'] == 'publish_version'){
					$this->action_publish_version();
				}
				if($_POST['pb_revisions_action'] == 'delete_chapter'){
					$this->action_delete_chapter();
				}
				if($_POST['pb_revisions_action'] == 'save_chapter'){
					$this->action_save_chapter();
				}
				if($_POST['pb_revisions_action'] == 'force_save_chapter'){
					$this->action_save_chapter(true);
				}

				
			}
		}
	}
	
	/**
	 * Action Activate Version
	 *
	 * @since    1.0.0
	 */
	public function action_activate_version() {
		if(isset($_POST['pb_revisions_version'])){
			if($_POST['pb_revisions_version'] == 'working'){
				$this->store->save_active_version_number(null);
				return;
			}
			$version = $this->store->get_version((int)$_POST['pb_revisions_version']);
			if(isset($version) && !$version->draft){
				$this->store->save_active_version($version);
				return;
			}
		}
        //TODO if error
	}

	/**
	 * Action Activate Export Version
	 *
	 * @since    1.0.0
	 */
	public function action_activate_export_version() {
		if(isset($_POST['pb_revisions_version'])){
			if($_POST['pb_revisions_version'] == 'working'){
				$this->store->save_active_export_version_number(null);
				return;
			}
			$version = $this->store->get_version((int)$_POST['pb_revisions_version']);
			if(isset($version) && !$version->draft){
				$this->store->save_active_export_version($version);
			}
		}
        //TODO if error
	}

	/**
	 * Action Publish Version One
	 *
	 * @since    1.0.0
	 */
	public function action_publish_version_one() {
        if($this->store->number_of_versions() == 0){
			$v1 = new \PBRevisions\includes\models\Version(get_current_user_id());
			$v1->set_version_one();
			$this->store->publish_version($v1);
			$this->store->save_active_version($v1);
			$this->store->save_active_export_version($v1);
		}
	}

	/**
	 * Action Delete Draft
	 *
	 * @since    1.0.0
	 */
	public function action_delete_draft() {
		$draft = $this->store->get_draft_version();
		if(isset($draft)){
			$this->store->delete_version($draft);
		}
		//TODO Errors
	}

	/**
	 * Action Create Version
	 *
	 * @since    1.0.0
	 */
	public function action_create_version() {
        if(isset($_POST['pb_revisions_type']) && ($_POST['pb_revisions_type'] == 'major' || $_POST['pb_revisions_type'] == 'minor' ||$_POST['pb_revisions_type'] == 'patch')){
			$draft = $this->store->get_draft_version();
			if(empty($draft)){
				$last_version = $this->store->get_last_version();
				$v = new \PBRevisions\includes\models\Version(get_current_user_id());
				$v->set_number_by_last($last_version->number, $_POST['pb_revisions_type']);
				$version_id = $this->store->save_version($v);
				wp_redirect( get_admin_url( get_current_blog_id(), "/admin.php?page=pb_revisions&pb_revisions_view=version_summary&pb_revisions_version={$version_id}" ), 301 );
				exit;
			}
		}
		//TODO Error
	}

	/**
	 * Action Save Version
	 *
	 * @since    1.0.0
	 */
	public function action_save_version() {
        if(isset($_POST['pb_revisions_version']) && isset($_POST['pb_revisions_comment'])){
			$version = $this->store->get_version($_POST['pb_revisions_version']);
			if(isset($version)){
				$version->comment = wp_kses_post(stripslashes($_POST['pb_revisions_comment']));
				$this->store->save_version($version);
			}
		}
		//TODO Error
	}

	/**
	 * Action Save and Publish Version
	 *
	 * @since    1.0.0
	 */
	public function action_save_and_publish_version() {
        if(isset($_POST['pb_revisions_version']) && isset($_POST['pb_revisions_comment'])){
			$version = $this->store->get_version($_POST['pb_revisions_version']);
			if(isset($version) && $version->draft){
				$version->comment = wp_kses_post(stripslashes($_POST['pb_revisions_comment']));
				$this->store->publish_version($version);
				$this->store->save_active_version($version);
				$this->store->save_active_export_version($version);
			}
		}
		//TODO Error
	}

	/**
	 * Action Publish Version
	 *
	 * @since    1.0.0
	 */
	public function action_publish_version() {
        if(isset($_POST['pb_revisions_version'])){
			$version = $this->store->get_version($_POST['pb_revisions_version']);
			if(isset($version) && $version->draft){
				$this->store->publish_version($version);
				$this->store->save_active_version($version);
				$this->store->save_active_export_version($version);
			}
		}
		//TODO Error
	}

	/**
	 * Delete Chapter
	 *
	 * @since    1.0.0
	 */
	public function action_delete_chapter() {
        if(isset($_POST['pb_revisions_version']) && isset($_POST['pb_revisions_chapter'])){
			$version = $this->store->get_version($_POST['pb_revisions_version']);
			if(isset($version)){
				$chapter = $this->store->get_chapter($_POST['pb_revisions_chapter'], $version);
				if(isset($chapter) && isset($chapter->ID)){
					$this->store->delete_chapter($chapter);
				}
			}
		}
		//TODO Error
	}

	/**
	 * Save Chapter
	 *
	 * @since    1.0.0
	 * @param	boolean	$force	Should the chapter been saved even if there are no changes?
	 */
	public function action_save_chapter($force=false) {
		if(!isset($_POST['pb_revisions_version']) || !isset($_POST['pb_revisions_chapter']) || !isset($_POST['pb_revisions_title_comment']))
			return;

		$version = $this->store->get_version($_POST['pb_revisions_version']);
		if(empty($version))
			return;

		$chapter = $this->store->get_chapter($_POST['pb_revisions_chapter'], $version);
		if(empty($chapter))
			return;

		$new_comments = array();
		$bigest_id = -1;

		foreach($_POST as $k => $v){
			if(preg_match("/^pb_revisions_comments-(\d*)$/", $k, $matches)){
				if(!empty($v)){
					$new_comments[$matches[1]] = $v;
				}
				if((int)$matches[1] > $bigest_id)
					$bigest_id = (int)$matches[1];
			}
		}

		if(!$force && $_POST['pb_revisions_title_comment'] == $chapter->title_comment){
			if(!isset($_POST['pb_revisions_comments-r']) || !isset($_POST['pb_revisions_comments-r-orig'])){
				if($new_comments == $chapter->comments)
					return;
			}else{
				if($_POST['pb_revisions_comments-r'] == $_POST['pb_revisions_comments-r-orig']){
					$stript_comments = array_filter($chapter->comments, function($k) use ($bigest_id) {
						return $k <= $bigest_id;
					}, ARRAY_FILTER_USE_KEY);
					if($new_comments == $stript_comments)
						return;
				}
			}
		}

		if(!empty($_POST['pb_revisions_comments-r'])){
			$new_comments[$bigest_id+1] = $_POST['pb_revisions_comments-r'];
		}

		if(!empty($_POST['pb_revisions_title_comment']) || !empty($new_comments)){
			$chapter->comments = array_map ( 'wp_kses_post' , $new_comments );
			$chapter->title_comment = wp_kses_post($_POST['pb_revisions_title_comment']);
			$this->store->save_chapter($chapter);
		}else{
			if(isset($chapter->ID)){
				$this->store->delete_chapter($chapter);
			}
		}
		

		//TODO Error
	}

	/**
	 * Render the page
	 *
	 * @since    1.0.0
     * @return  string  The page html
	 */
	public function render() {
		if(!$this->allowed()) return "";
		if($this->store->number_of_versions() == 0){
			return $this->render_page('create_version_one');
		}
		if(isset($_GET['pb_revisions_view'])){
			if($_GET['pb_revisions_view'] == "create_version"){
				return $this->render_page_create_version();
			}
			if($_GET['pb_revisions_view'] == "version_summary"){
				return $this->render_page_version_summary();
			}
			if($_GET['pb_revisions_view'] == "chapter_diff"){
				return $this->render_page_chapter_diff();
			}
			if($_GET['pb_revisions_view'] == "version_review"){
				return $this->render_page_version_review();
			}
		}
		
		return $this->render_page_versions_overview();
	}

	/**
	 * Render Versions Overview
	 *
	 * @since    1.0.0
	 * @access   private
     * @return  string  The page html
	 */
	private function render_page_versions_overview() {
		$data = array(
			'versions' => $this->store->get_versions(),
			'active_version' => $this->store->get_active_version(),
			'active_export_version' => $this->store->get_active_export_version(),
			'has_draft' => $this->store->get_draft_version()
		);
		$this->render_page('versions_overview', $data);
	}

	/**
	 * Create Version
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function render_page_create_version() {
		if($this->store->has_draft_version()){
			//TODO error if draft exists
			return;
		}
		$last_version = $this->store->get_last_version();

		$data = array(
			'major_number' => $last_version::get_next_number($last_version->number, 'major'),
			'minor_number' => $last_version::get_next_number($last_version->number, 'minor'),
			'patch_number' => $last_version::get_next_number($last_version->number, 'patch')
		);
		$this->render_page('create_version', $data);
	}
	
	/**
	 * Version Summary
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function render_page_version_summary() {
		if(isset($_GET['pb_revisions_version'])){
			$version = $this->store->get_version($_GET['pb_revisions_version']);
			if(isset($version)){
				$data = array(
					'version' => $version,
					'first_chapter' => $this->store->get_chapter(-1, $version),
					'has_saved_chapters' => $this->store->has_version_saved_chapters($version)
				);
				$this->render_page('version_summary', $data);
			}
		}
		//TODO error if does not exist
    }
	
	/**
	 * Render Chapter Diff
	 *
	 * @since    1.0.0
	 * @access   private
     * @return  string  The page html
	 */
	private function render_page_chapter_diff() {
		if(isset($_GET['pb_revisions_chapter']) && isset($_GET['pb_revisions_version'])){
			$version = $this->store->get_version($_GET['pb_revisions_version']);
			if(isset($version)){
				$chapter = $this->store->get_chapter($_GET['pb_revisions_chapter'], $version);
				if(!empty($chapter)){
					$data = array(
						'chapter' => $chapter
					);
					return $this->render_page('chapter_diff', $data);
				}
			}
			
		}
		//TODO Error
	}
	
	/**
	 * Version Review
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function render_page_version_review() {
		if(isset($_GET['pb_revisions_version'])){
			$version = $this->store->get_version($_GET['pb_revisions_version']);
			if(isset($version)){
				$data = array(
					'version' => $version,
					'chapters' => $this->store->get_chapters($version)
				);
				$this->render_page('version_review', $data);
			}
		}
		//TODO errors
    }
	
	/**
	 * Render a page
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param	string	$page	The page name
	 * @param	array	$data	Data to render the page
	 */
	private function render_page($page, $data=null) {
		require( plugin_dir_path( __FILE__ ) . 'partials/pb-revisions-menu-page-'. str_replace( '_', '-', $page ) . '.php' );
    }
    
    /**
	 * Does the user have the rights to view the revisions section?
	 *
	 * @since    1.0.0
	 * @access   private
     * @return  boolean  If okay
	 */
	private function allowed() {
		return current_user_can('edit_posts');
	}

}
