<?php

/**
 * Revision Chapter
 *
 * The model of a revision chapter
 *
 * @since      1.0.0
 * @package    Pb_Revisions
 * @subpackage Pb_Revisions/includes/models
 * @author     Lukas Kaiser <reg@lukaiser.com>
 */

namespace PBRevisions\includes\models;
class Chapter {

	/**
	 * The version
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      Pb_Revisions_Version    $version    The version
	 */
	public $version;

	/**
	 * The revision chapter ID
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      int    $ID    The revision chapter ID
	 */
	public $ID;

	/**
	 * The chapter ID
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      int    $chapter    The chapter ID
	 */
	public $chapter;

	/**
	 * The chapter content of the last version
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $content_old    The chapter content of the last version
	 */
	public $content_old;

	/**
	 * The chapter content of the current version
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $content_new    The chapter content of the current version
	 */
	public $content_new;

	/**
	 * The chapter title of the last version
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $title_old    The chapter title of the last version
	 */
	public $title_old;

	/**
	 * The chapter title of the new version
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $title_new    The chapter title of the new version
	 */
	public $title_new;

	/**
	 * The chapter status of the last version
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $status_old    The chapter status of the last version
	 */
	public $status_old;

	/**
	 * The chapter status of the new version
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $status_new    The chapter status of the new version
	 */
	public $status_new;

	/**
	 * The chapter order of the last version
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      int    $order_old    The chapter order of the last version
	 */
	public $order_old;

	/**
	 * The chapter order of the new version
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      int    $order_new    The chapter order of the new version
	 */
	public $order_new;

	/**
	 * The chapter type of the last version
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $type_old    The chapter type of the last version
	 */
	public $type_old;

	/**
	 * The chapter type of the new version
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $type_new    The chapter type of the new version
	 */
	public $type_new;

	/**
	 * A comment for the title
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $title_comment    A comment for the title
	 */
	public $title_comment;

	/**
	 * Comments for conntent changes
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $comments    Comments for conntent changes
	 */
	public $comments;

	/**
	 * A hash representing the new conntend present when saving the draft
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $content_draft_hash    A hash representing the new conntend present when saving the draft
	 */
	protected $content_draft_hash;

	/**
	 * The ID of the prev chapter
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      int    $prev_chapter_ID    The ID of the prev chapter
	 */
	public $prev_chapter_ID;

	/**
	 * The ID of the next chapter
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      int    $next_chapter_ID    The ID of the next chapter
	 */
	public $next_chapter_ID;

	/**
	 * Define the chapter
	 *
	 * @since      1.0.0
	 * @param      Pb_Revisions_Version	$version	The version
	 * @param      int		$ID					The revision chapter ID
	 * @param	   int		$chapter			The chapter ID
	 */
	public function __construct($version, $ID, $chapter) {
		$this->version = $version;
		$this->ID = $ID;
		$this->chapter = $chapter;
		$this->comments = array();
	}

	/**
	 * Set the values of the old version
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param      string	$content			The chapter content
	 * @param      string	$title				The chapter title
	 * @param      string	$status				The chapter status
	 * @param      int		$order				The chapter menu order
	 * @param      string	$type				The chapter type
	 */
	public function set_old_values($content, $title, $status, $order, $type) {
		$this->content_old = $content;
		$this->title_old = $title;
		$this->status_old = $status;
		$this->order_old = $order;
		$this->type_old = $type;
	}

	/**
	 * Set the values of the new version
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param      string	$content			The chapter content
	 * @param      string	$title				The chapter title
	 * @param      string	$status				The chapter status
	 * @param      int		$order				The chapter menu order
	 * @param      string	$type				The chapter type
	 */
	public function set_new_values($content, $title, $status, $order, $type) {
		$this->content_new = $content;
		$this->title_new = $title;
		$this->status_new = $status;
		$this->order_new = $order;
		$this->type_new = $type;
	}

	/**
	 * Set comments
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param      string	$title_comment			Title comment
	 * @param      array	$comments				Comments
	 * @param      string	$content_draft_hash		A hash representing the new conntend present when saving the draft
	 */
	public function set_comments($title_comment, $comments, $content_draft_hash) {
		$this->title_comment = $title_comment;
		$this->comments = is_array($comments) ? $comments : array();
		$this->content_draft_hash = $content_draft_hash;
	}

	/**
	 * Set neighbors
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param      int	$prev			ID of previous chapter
	 * @param      int	$next			ID of next chapter
	 */
	public function set_neighbors($prev, $next) {
		$this->prev_chapter_ID = $prev;
		$this->next_chapter_ID = $next;
	}

	/**
	 * Old visilbe in the web?
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return	 boolean
	 */
	public function web_statuts_old(){
		return $this->status_old == "publish" || $this->status_old == "web-only";
	}

	/**
	 * New visilbe in the web?
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return	 boolean
	 */
	public function web_statuts_new(){
		return $this->status_new == "publish" || $this->status_new == "web-only";
	}

	/**
	 * Old visilbe in the export?
	 *
	 * @since    1.1.0
	 * @access   public
	 * @return	 boolean
	 */
	public function export_status_old(){
		return $this->status_old == "publish" || $this->status_old == "private";
	}

	/**
	 * New visilbe in the export?
	 *
	 * @since    1.1.0
	 * @access   public
	 * @return	 boolean
	 */
	public function export_status_new(){
		return $this->status_new == "publish" || $this->status_new == "private";
	}

	/**
	 * Was the chapter added since the last version?
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return	 boolean
	 */
	public function is_added(){
		return is_null($this->status_old) || (!$this->web_statuts_old() && !$this->export_status_old());
	}

	/**
	 * Was the chapter deleted since the last version?
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return	 boolean
	 */
	public function is_deleted(){
		return is_null($this->status_new) || (!$this->web_statuts_new() && !$this->export_status_new());
	}

	/**
	 * Did the content change since the last version?
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return	 boolean
	 */
	public function contend_changed(){
		return $this->content_old != $this->content_new;
	}

	/**
	 * Did the title change since the last version?
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return	 boolean
	 */
	public function title_changed(){
		return $this->title_old != $this->title_new;
	}

	/**
	 * Did the export status change since the last version?
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return	 boolean
	 */
	public function status_changed(){
		return $this->status_old != $this->status_new;
	}

	/**
	 * Did the web status change since the last version?
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return	 boolean
	 */
	public function web_status_changed(){
		return $this->web_statuts_old() != $this->web_statuts_new();
	}

	/**
	 * Did the export status change since the last version?
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return	 boolean
	 */
	public function export_status_changed(){
		return $this->export_status_old() != $this->export_status_new();
	}

	/**
	 * Did anything change since the last version?
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return	 boolean
	 */
	public function anything_changed(){
		return $this->is_added() || $this->is_deleted() || $this->contend_changed() || $this->title_changed() || $this->status_changed() || $this->export_status_changed();
	}

	/**
	 * Did the header change?
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return	 boolean
	 */
	public function header_changed(){
		return $this->is_added() || $this->is_deleted() || $this->title_changed() || $this->status_changed() || $this->export_status_changed();
	}

	/**
	 * Did the new content change since the last draft?
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return	 boolean
	 */
	public function contend_new_changed_since_draft(){
		if(is_null($this->content_draft_hash)) return false;
		return $this->content_draft_hash != $this->contend_new_hash();
	}

	/**
	 * A hast of the new content
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return	 string
	 */
	public function contend_new_hash(){
		return hash("md5", $this->content_new.'---'.$this->title_new.'---'.$this->status_new.'---'.($this->export_status_new() ? "true" : "false"));
	}

	/**
	 * Get Title
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return	 string
	 */
	public function title(){
		return empty($this->title_new) ? $this->title_old : $this->title_new;
	}
}
