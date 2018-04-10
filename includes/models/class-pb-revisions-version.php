<?php

/**
 * Revision Version
 *
 * The model of a revision version
 *
 * @since      1.0.0
 * @package    Pb_Revisions
 * @subpackage Pb_Revisions/includes/models
 * @author     Lukas Kaiser <reg@lukaiser.com>
 */

namespace PBRevisions\includes\models;
class Version {

	/**
	 * The Version ID
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      int    $ID    The chapter ID
	 */
	public $ID;

	/**
	 * The creation date
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      int    $date    The creation date
	 */
	public $date;

	/**
	 * The version number
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $number    The version number
	 */
	public $number;

	/**
	 * The prev version number
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $prev_number    The prev version number
	 */
	public $prev_number;

	/**
	 * The version type
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $type    The version type
	 */
	public $type;

	/**
	 * The author
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      int    $author    The author of the version
	 */
	public $author;

	/**
	 * Is it a draft?
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      boolean    $draft    Is it a draft?
	 */
	public $draft = true;


	/**
	 * Comment for conntent changes
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $comment    Comment for conntent changes
	 */
	public $comment;

	/**
	 * Chapters
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $chapters    The Chapters of this version
	 */
	public $chapters;

	/**
	 * Define the version
	 *
	 * @since    1.0.0
	 * @param      int	    $author				The version author
	 * @param      int		$ID					The version ID
	 * @param      int	    $date			    The version date
	 * @param      string	$number				The version number
	 * @param      string	$prev_number		The prev version number
     * @param      string   $type               The version type
	 * @param      boolean	$draft      		Is this version a draft?
	 * @param      string	$comment			The version comment
	 */
	public function __construct($author, $ID=null, $date=null, $number=null, $prev_number=null, $type=null, $draft=true, $comment=null) {
        $this->author = $author;
        $this->ID = $ID;
        $this->date = $date;
		$this->number = $number;
		$this->prev_number = $prev_number;
        $this->type = $type;
        $this->draft = $draft;
        $this->comment = $comment;
	}

	/**
	 * Set Version One
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function set_version_one() {
		$this->type = "major";
		$this->number = "1.0.0";
	}

	/**
	 * Set the number by last number
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param      string	$prev_number		The previous number
	 * @param      string	$type				The version type
	 */
	public function set_number_by_last($prev_number = null, $type = null) {
		if(is_null($type)){
            $type = $this->type;
        }else{
            $this->type = $type;
		}
		
		if(is_null($prev_number)){
            $prev_number = $this->prev_number;
        }else{
            $this->prev_number = $prev_number;
        }

        $this->number = self::get_next_number($prev_number, $type);
	}

	/**
	 * Set the chapters
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param      string	$chapters		The chapters
	 */
	public function set_chapters($chapters) {
		$this->chapters = $chapters;
	}

	/**
	 * Prepare Publishing
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function prepare_publishing() {
		$this->draft = false;
		$this->date = current_time( 'mysql', 1 );
	}

	/**
	 * Set the number by last number
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param	string	$number		The number
	 * @param      string	$type				The version type
	 * @return	string	The new number
	 */
	public static function get_next_number($number, $type) {

        $split_n = explode(".", $number);

        if("major" == $type){
            $split_n[0] = 1+(int)$split_n[0];
        }else if("minor" == $type){
            $split_n[1] = 1+(int)$split_n[1];
        }else if("patch" == $type){
            $split_n[2] = 1+(int)$split_n[2];
        }else{
            throw new Exception("Version Type $type is unknown");
        }

        return implode(".", $split_n);
	}
}
