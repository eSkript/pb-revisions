<?php

/**
 * Revisions Store
 *
 * The model store helping loading and saving models
 *
 * @since      1.0.0
 * @package    Pb_Revisions
 * @subpackage Pb_Revisions/includes
 * @author     Lukas Kaiser <reg@lukaiser.com>
 */
namespace PBRevisions\includes;

class Store {

    /**
	 * The Version Option Parameter
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version_option    The Version Option Parameter
	 */
    protected $version_option = "PB-Revisions-Version";
    
    /**
	 * The Version Export Option Parameter
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $export_version_option    The Version Export Option Parameter
	 */
	protected $export_version_option = "PB-Revisions-Export-Version";
	
	/**
	 * Get Versions
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return      array       Array of versions
	 */
	public function get_versions() {
		global $wpdb;
		$versions = $wpdb->get_results( 
			"
			SELECT * 
			FROM $wpdb->pb_revisions_version
			ORDER BY ID;
			"
		);
		$prev_number = null;
		return array_map(function($version) use (&$prev_number){
			$out = new \PBRevisions\includes\models\Version($version->author, $version->ID, $version->date, $version->number, $prev_number, $version->type, boolval($version->draft), $version->comment);
			$prev_number = $version->number;
			return $out;
		}, $versions);
	}
	
	/**
	 * Get Versions up to
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param	 string $version_number	The version number up to you want the output
	 * @return      array       Array of versions
	 */
	public function get_versions_up_to($version_number) {
		global $wpdb;
		if(empty($version_number)){
			$versions = $wpdb->get_results(
				"
				SELECT * 
				FROM $wpdb->pb_revisions_version
				ORDER BY ID;
				"
			);
		}else{
			$versions = $wpdb->get_results(
				$wpdb->prepare(
					"
					SELECT * 
					FROM $wpdb->pb_revisions_version
					WHERE ID <= (SELECT ID FROM $wpdb->pb_revisions_version WHERE number LIKE %s)
					ORDER BY ID;
					",
					$version_number
				)
			);
		}
		$prev_number = null;
		return array_map(function($version) use (&$prev_number){
			$out = new \PBRevisions\includes\models\Version($version->author, $version->ID, $version->date, $version->number, $prev_number, $version->type, boolval($version->draft), $version->comment);
			$prev_number = $version->number;
			return $out;
		}, $versions);
	}
	
	/**
	 * Get Version by number
	 *
	 * @since    1.0.0
	 * @access   public
     * @param       string  $version_di         The version id                    
	 * @return      Pb_Revisions_Version    The version
	 */
	public function get_version($version_id) {
		global $wpdb;
		$versions = $wpdb->get_results(
			$wpdb->prepare(
				"
				SELECT * 
				FROM $wpdb->pb_revisions_version
				WHERE ID <= %d
				ORDER BY ID DESC
				LIMIT 2;
				",
				$version_id
			)
		);
		$version = $versions[0];
		$version_prev = array_key_exists(1, $versions) ? $versions[1]->number : Null;
		return new \PBRevisions\includes\models\Version($version->author, $version->ID, $version->date, $version->number, $version_prev, $version->type, boolval($version->draft), $version->comment);
    }
    
    /**
	 * Get Version by number
	 *
	 * @since    1.0.0
	 * @access   public
     * @param       string  $version_number         The version number                    
	 * @return      Pb_Revisions_Version    The version
	 */
	public function get_version_by_number($version_number) {
		global $wpdb;
		$versions = $wpdb->get_results(
			$wpdb->prepare(
				"
				SELECT * 
				FROM $wpdb->pb_revisions_version
				WHERE ID <= (SELECT ID FROM $wpdb->pb_revisions_version WHERE number LIKE %s)
				ORDER BY ID DESC
				LIMIT 2;
				",
				$version_number
			)
		);
		$version = $versions[0];
		$version_prev = array_key_exists(1, $versions) ? $versions[1]->number : Null;
		return new \PBRevisions\includes\models\Version($version->author, $version->ID, $version->date, $version->number, $version_prev, $version->type, boolval($version->draft), $version->comment);
	}
	
	/**
	 * Number of Versions
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return      int       Number of versions
	 */
	public function number_of_versions() {
		global $wpdb;
		return $wpdb->get_var( 
			"
			SELECT COUNT(*) 
			FROM $wpdb->pb_revisions_version;
			"
		);
	}

	/**
	 * Has draft Version?
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return      int       Number of versions
	 */
	public function has_draft_version() {
		global $wpdb;
		return $wpdb->get_var( 
			"
			SELECT COUNT(*) 
			FROM $wpdb->pb_revisions_version
			WHERE draft = 1;
			"
		) > 0;
	}

	/**
	 * Get Draft Version
	 *
	 * @since    1.0.0
	 * @access   public                    
	 * @return      Pb_Revisions_Version    The version
	 */
	public function get_draft_version() {
		global $wpdb;
		$versions = $wpdb->get_results(
				"
				SELECT * 
				FROM $wpdb->pb_revisions_version
				WHERE ID <= (SELECT ID FROM $wpdb->pb_revisions_version WHERE draft = 1 LIMIT 1)
				ORDER BY ID DESC
				LIMIT 2;
				"
		);

		if(count($versions) < 2){
			return false;
		}

		$version = $versions[0];
		$version_prev = array_key_exists(1, $versions) ? $versions[1]->number : Null;
		return new \PBRevisions\includes\models\Version($version->author, $version->ID, $version->date, $version->number, $version_prev, $version->type, boolval($version->draft), $version->comment);
	}

	/**
	 * Get Last Version
	 *
	 * @since    1.0.0
	 * @access   public                    
	 * @return      Pb_Revisions_Version    The version
	 */
	public function get_last_version() {
		global $wpdb;
		$versions = $wpdb->get_results(
				"
				SELECT * 
				FROM $wpdb->pb_revisions_version
				ORDER BY ID DESC
				LIMIT 2;
				"
		);

		$version = $versions[0];
		$version_prev = array_key_exists(1, $versions) ? $versions[1]->number : Null;
		return new \PBRevisions\includes\models\Version($version->author, $version->ID, $version->date, $version->number, $version_prev, $version->type, boolval($version->draft), $version->comment);
	}
    
    /**
	 * Save Version
	 *
	 * @since    1.0.0
	 * @access   public
     * @param       Pb_Revisions_Version  $version  The version
	 * @return	The insert id                  
	 */
	public function save_version($version) {
		global $wpdb;
		$effect = $wpdb->replace( 
			$wpdb->pb_revisions_version, 
			array( 
				'ID' => $version->ID,
				'date' => $version->date, 
				'number' => $version->number,
				'type' => $version->type,
				'author' => $version->author,
				'draft' => $version->draft,
				'comment' => $version->comment
			), 
			array( 
				'%d',
				'%s', 
				'%s',
				'%s',
				'%d',
				'%d',
				'%s'
			) 
		);
		if($effect){
			return $wpdb->insert_id;
		}else{
			//TODO Error
		}
		
	}
	
	/**
	 * Publish Version
	 *
	 * @since    1.0.0
	 * @access   public
     * @param       Pb_Revisions_Version  $version  The version                    
	 */
	public function publish_version($version) {
		global $wpdb;
		$version->prepare_publishing();
		$this->save_version($version);

		$posts = $wpdb->prefix.'posts';
		$posts_new = $wpdb->prefix.'posts_v'.str_replace(".", "_", $version->number);
		$postmeta = $wpdb->prefix.'postmeta';
		$postmeta_new = $wpdb->prefix.'postmeta_v'.str_replace(".", "_", $version->number);
		$sql = "
				SET SQL_MODE='ALLOW_INVALID_DATES';
				CREATE TABLE $posts_new LIKE $posts; 
				INSERT $posts_new SELECT * FROM $posts;
				SET SQL_MODE='ALLOW_INVALID_DATES';
				CREATE TABLE $postmeta_new LIKE $postmeta; 
				INSERT $postmeta_new SELECT * FROM $postmeta
			";

		$sqls = explode(';', $sql);
		foreach($sqls as $s){
			$wpdb->query($s);
			if($wpdb->last_error != '') {
				$error = new WP_Error("dberror", __("Database query error"), $wpdb->last_error);
				return $error;
			}
		}
	}
	
	/**
	 * Delete Version
	 *
	 * @since    1.0.0
	 * @access   public
     * @param       Pb_Revisions_Version  $version  The version                    
	 */
	public function delete_version($version) {
		global $wpdb;
		$wpdb->delete( $wpdb->pb_revisions_version, array( 'ID' => $version->ID ), array( '%d' ) );
		$wpdb->delete( $wpdb->pb_revisions_chapter, array( 'version' => $version->ID ), array( '%d' ) );
	}

    /**
	 * Get Versions with Chapters up to
	 * 
	 * Get all versions up to a certain number with its chapters
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param	 string $version_number	The version number up to you want the output
	 * @return      array       Array of versions
	 */
	public function get_versions_with_chapters_up_to($version_number) {
		global $wpdb;
		$versions = $this->get_versions_up_to($version_number);
		foreach ( $versions as $version ){
			$chapters = $this->get_chapters($version);
			$version->set_chapters($chapters);
		}
		return $versions;
	}
    
    /**
	 * Get Chapters of Version
	 *
	 * @since    1.0.0
	 * @access   public
     * @param       Pb_Revisions_Version  $version  The version
	 * @return      array       Array of chapters
	 */
	public function get_chapters($version) {
		global $wpdb;

		if(!empty($version->prev_number)){		
			$sql= file_get_contents(plugin_dir_path( dirname( __FILE__ ) ) . 'includes/sql/selectRevisionChapters.sql');
		}else{
			$sql= file_get_contents(plugin_dir_path( dirname( __FILE__ ) ) . 'includes/sql/selectRevisionChaptersWithoutOld.sql');
		}
		  
		$sql = $this->replace_chapter_tables_strings($sql, $version);

		$chapters = $wpdb->get_results(
			$wpdb->prepare(
				$sql,
				$version->ID
			)
		);

		return array_map(function($c) use ($version){
			$out = new \PBRevisions\includes\models\Chapter($version, $c->ID, $c->chapter);
			if(!empty($version->prev_number)){
				$out->set_old_values($c->post_content_old, $c->post_title_old, $c->post_status_old, $c->pb_export_old=="on", $c->menu_order_old, $c->post_type_old);
			}
			$out->set_new_values($c->post_content_new, $c->post_title_new, $c->post_status_new, $c->pb_export_new=="on", $c->menu_order_new, $c->post_type_new);
			$out->set_comments($c->title_comment, unserialize($c->comments), $c->content_draft_hash);
			return $out;
		}, $chapters);
	}
	
	/**
	 * Get Chapter
	 *
	 * @since    1.0.0
	 * @access   public
     * @param       int  $chapter_number  The position
	 * @param       Pb_Revisions_Version  $version  The version
	 * @return      Pb_Revisions_Chapter       The chapter
	 */
	public function get_chapter($chapter_number, $version) {
		global $wpdb;
		if(empty($version->prev_number)){
			return false;
		}
		$sql = file_get_contents(plugin_dir_path( dirname( __FILE__ ) ) . 'includes/sql/selectChanges.sql');
		$sql = $this->replace_chapter_tables_strings($sql, $version);
		$sql = $wpdb->prepare(
			$sql,
			$version->ID,
			$chapter_number
		);

		$sqls = explode(';', $sql);
		$lastElement = end($sqls);
		foreach($sqls as $s){
			if($s == $lastElement){
				$chapters = $wpdb->get_results($s);
			}else{
				$wpdb->query($s);
			}
			if($wpdb->last_error != '') {
				echo $wpdb->last_error;
				$error = new WP_Error("dberror", __("Database query error"), $wpdb->last_error);
				return $error;
			}
		}
		
		$prev = null;
		$next = null;
		if(count($chapters) == 3 && $chapters[1]->chapter == $chapter_number){
			$c = $chapters[1];
			$prev = $chapters[0]->chapter;
			$next = $chapters[2]->chapter;
		}else if(count($chapters) == 2 && $chapters[1]->chapter == $chapter_number){
			$c = $chapters[1];
			$prev = $chapters[0]->chapter;
		}else if(count($chapters) == 2 && ($chapters[0]->chapter == $chapter_number || $chapter_number<0)){
			$c = $chapters[0];
			$next = $chapters[1]->chapter;
		}else if(count($chapters) == 1 && ($chapters[0]->chapter == $chapter_number || $chapter_number<0)){
			$c = $chapters[0];
		}else{
			return false;
		}
		$out = new \PBRevisions\includes\models\Chapter($version, $c->ID, $c->chapter);
		$out->set_old_values($c->post_content_old, $c->post_title_old, $c->post_status_old, $c->pb_export_old=="on", $c->menu_order_old, $c->post_type_old);
		$out->set_new_values($c->post_content_new, $c->post_title_new, $c->post_status_new, $c->pb_export_new=="on", $c->menu_order_new, $c->post_type_new);
		$out->set_comments($c->title_comment, unserialize($c->comments), $c->content_draft_hash);
		$out->set_neighbors($prev, $next);
		return $out;
	}
	
	/**
	 * Has saved chapters?
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param	Pb_Revisions_Version  $version  The version
	 * @return      boolean       Has saved chpaters?
	 */
	public function has_version_saved_chapters($version) {
		global $wpdb;
		return $wpdb->get_var(
			$wpdb->prepare(
				"
				SELECT COUNT(*) 
				FROM $wpdb->pb_revisions_chapter
				WHERE version = %d;
				",
				$version->ID
			)
			
		) > 0;
	}

    /**
	 * Save Chapter
	 *
	 * @since    1.0.0
	 * @access   public
     * @param       Pb_Revisions_Chapter  $chapter  The chapter                    
	 */
	public function save_chapter($chapter) {
		global $wpdb;
		$wpdb->replace( 
			$wpdb->pb_revisions_chapter, 
			array( 
				'ID' => $chapter->ID,
				'version' => $chapter->version->ID, 
				'chapter' => $chapter->chapter,
				'content_draft_hash' => $chapter->contend_new_hash(),
				'title_comment' => $chapter->title_comment,
				'comments' => serialize($chapter->comments)
			), 
			array( 
				'%d',
				'%d', 
				'%d',
				'%s',
				'%s',
				'%s'
			) 
		);
	}
	
	/**
	 * Delete Chapter
	 *
	 * @since    1.0.0
	 * @access   public
     * @param       Pb_Revisions_Chapter  $chapter  The chapter                    
	 */
	public function delete_chapter($chapter) {
		global $wpdb;
		$wpdb->delete( $wpdb->pb_revisions_chapter, array( 'ID' => $chapter->ID ), array( '%d' ) );
	}

    /**
	 * Get active version number
	 *
	 * @since    1.0.0
	 * @access   public               
	 * @return      string    The version number
	 */
	public function get_active_version_number() {
		return get_option($this->version_option, false);
    }

    /**
	 * Get active version
	 *
	 * @since    1.0.0
	 * @access   public               
	 * @return      Pb_Revisions_Version    The version
	 */
	public function get_active_version() {
		$version = $this->get_active_version_number();
		if(!$version) return false;
		return $this->get_version_by_number($version);
    }

    /**
	 * Save active version number
	 *
	 * @since    1.0.0
	 * @access   public               
	 * @param      string $number    The version number
	 */
	public function save_active_version_number($number) {
		update_option($this->version_option, $number, false);
    }

    /**
	 * Save active version
	 *
	 * @since    1.0.0
	 * @access   public               
	 * @param      Pb_Revisions_Version $version    The version
	 */
	public function save_active_version($version) {
        $this->save_active_version_number($version->number);
    }

    /**
	 * Get active export version number
	 *
	 * @since    1.0.0
	 * @access   public               
	 * @return      string    The version number
	 */
	public function get_active_export_version_number() {
		return get_option($this->export_version_option, false);
    }

    /**
	 * Get active export version
	 *
	 * @since    1.0.0
	 * @access   public               
	 * @return      Pb_Revisions_Version    The version
	 */
	public function get_active_export_version() {
		$version = $this->get_active_export_version_number();
		if(!$version) return false;
		return $this->get_version_by_number($version);
    }

    /**
	 * Save active export version number
	 *
	 * @since    1.0.0
	 * @access   public               
	 * @param      string $number    The version number
	 */
	public function save_active_export_version_number($number) {
		update_option($this->export_version_option, $number, false);
    }

    /**
	 * Save active export version
	 *
	 * @since    1.0.0
	 * @access   public               
	 * @param      Pb_Revisions_Version $version    The version
	 */
	public function save_active_export_version($version) {
        $this->save_active_export_version_number($version->number);
	}

	/**
	 * Get the posts table name
	 *
	 * @since    1.0.0
	 * @param	string	The version number         
	 * @return	string	The posts table name
	 */
	public function posts_table_name($version=false) {
		global $wpdb;
		if(!empty($version)){
			return $wpdb->prefix.'posts_v'.str_replace(".", "_", $version);
		}else{
			return $wpdb->prefix.'posts';
		}
	}
	
	/**
	 * Get the postmeta table name
	 *
	 * @since    1.0.0
	 * @param	string	The version number         
	 * @return	string	The postmeta table name
	 */
	public function postmeta_table_name($version=false) {
		global $wpdb;
		if(!empty($version)){
			return $wpdb->prefix.'postmeta_v'.str_replace(".", "_", $version);
		}else{
			return $wpdb->prefix.'postmeta';
		}
    }
	
	/**
	 * Replace strings for chapter tables in SQL
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param	string	The Sql String         
	 * @param      Pb_Revisions_Version $version    The version
	 * @return	array	An array with the replace strings
	 */
	private function replace_chapter_tables_strings($sql, $version) {
		global $wpdb;
        if($version->draft){
			$posts = $this->posts_table_name();
			$postmeta = $this->postmeta_table_name();
		}else{
			$posts = $this->posts_table_name($version->number);
			$postmeta = $this->postmeta_table_name($version->number);
		}
		$posts_prev = $this->posts_table_name($version->prev_number);
		$postmeta_prev = $this->postmeta_table_name($version->prev_number);

		$vars_chapter = array(
			'{$posts}' 		=> $posts,
			'{$postmeta}'	=> $postmeta,
			'{$posts_prev}' 		=> $posts_prev,
			'{$postmeta_prev}'	=> $postmeta_prev,
			'{$pb_revisions_chapter}' => $wpdb->pb_revisions_chapter
		);

		return strtr($sql, $vars_chapter);
    }
}
