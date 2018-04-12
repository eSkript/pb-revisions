<?php

/**
 * Line Text Diff View Helper
 *
 * This view helper helps to style the text diff
 *
 * @since      1.0.0
 * @package    PbRevisions
 * @subpackage PbRevisions/includes/view_helper
 * @author     Lukas Kaiser <reg@lukaiser.com>
 */

namespace PBRevisions\includes\view_helper;
class Text_Diff {

	/**
	 * Inline display helper object name.
	 *
	 * @var string
	 * @since 1.0.0
	 */
    protected $inline_diff_renderer = '\WP_Text_Diff_Renderer_inline';
    
    /**
	 * Threshold for when a diff should be saved or omitted.
	 *
	 * @var float
	 * @since 1.0.0
	 */
    protected $_diff_threshold = 0.6;
    
    /**
	 * Was the chapter added?
	 *
	 * @var boolean
	 * @since 1.0.0
	 */
    protected $chapter_added = false;

    /**
	 * Was the chapter deleted?
	 *
	 * @var boolean
	 * @since 1.0.0
	 */
    protected $chapter_deleted = false;

    /**
	 * The content of the editors
	 *
	 * @var array
	 * @since 1.0.0
	 */
    protected $editors_content;

    /**
	 * The name of the editors
	 *
	 * @var string
	 * @since 1.0.0
	 */
    protected $editors_name;

    /**
	 * The settings of the editors
	 *
	 * @var array
	 * @since 1.0.0
	 */
    protected $editors_settings;

	/**
	 * Define the helper
	 *
	 * @since    1.0.0
     * @param   boolean $chapter_added  Was the chapter added?
     * @param   boolean $chapter_deleted    Was the chapter deleted?
     * @param   array   $editors_content     The editor settings
     * @param   string  $editors_name        The name of the editor
     * @param   array   $editors_settings    The editor settings
	 */
	public function __construct($chapter_added, $chapter_deleted, $editors_content, $editors_name, $editors_settings) {
        $this->chapter_added = $chapter_added;
        $this->chapter_deleted = $chapter_deleted;
        $this->editors_content = $editors_content;
        $this->editors_name = $editors_name;
        $this->editors_settings = $editors_settings;
	}

	/**
	 * Render a Line
	 *
	 * @since    1.0.0
	 * @access   public
     * @var     string  $old    The old string
     * @var     string  $new    The new string
	 */
	public function render_line($old, $new) {

        $old  = htmlspecialchars(normalize_whitespace($old));
        $new = htmlspecialchars(normalize_whitespace($new));

        if($this->chapter_added){
            echo "<td class=\"pb_rev_diff_cell\"></td>";
            echo "<td class=\"pb_rev_diff_cell pb_rev_diff_cell__added\">{$new}</td>";
            return;
        }
        if($this->chapter_deleted){
            echo "<td class=\"pb_rev_diff_cell pb_rev_diff_cell__removed\">{$old}</td>";
            echo "<td class=\"pb_rev_diff_cell\"></td>";
            return;
        }

        if($old == $new){
            echo "<td class=\"pb_rev_diff_cell\">{$old}</td>";
            echo "<td class=\"pb_rev_diff_cell\">{$new}</td>";
            return;
        }

        if ( ! class_exists( 'WP_Text_Diff_Renderer_Table', false ) )
            require( ABSPATH . WPINC . '/wp-diff.php' );

		$text_diff = new \Text_Diff( 'auto', array( array( $old ), array( $new ) ) );
		$renderer  = new $this->inline_diff_renderer;
        $diff      = $renderer->render( $text_diff );

        $total = true;
        $out_old = $old;
        $out_new = $new;
        // If they're too different, don't include any <ins> or <dels>
        if ( preg_match_all( '!(<ins>.*?</ins>|<del>.*?</del>)!', $diff, $diff_matches ) ) {
            // length of all text between <ins> or <del>
            $stripped_matches = strlen( strip_tags( join( ' ', $diff_matches[0] ) ) );
            // since we count lengith of text between <ins> or <del> (instead of picking just one),
            //	we double the length of chars not in those tags.
            $stripped_diff = strlen( strip_tags( $diff ) ) * 2 - $stripped_matches;
            $diff_ratio    = $stripped_matches / $stripped_diff;
            if ( $diff_ratio <= $this->_diff_threshold ) {
                $total = false;
                $out_old  = preg_replace( '|<ins>.*?</ins>|', '', $diff );
				$out_new = preg_replace( '|<del>.*?</del>|', '', $diff );
            }
        }

        if($total){
            $class_old = ' pb_rev_diff_cell__removed';
            $class_new = ' pb_rev_diff_cell__added';
        }else{
            $class_old = ' pb_rev_diff_cell__changed';
            $class_new = ' pb_rev_diff_cell__changed';
        }
        echo "<td class=\"pb_rev_diff_cell{$class_old}\">{$out_old}</td>";
        echo "<td class=\"pb_rev_diff_cell{$class_new}\">{$out_new}</td>";
    }
    
    /**
	 * Render Content
	 *
	 * @since    1.0.0
	 * @access   public
     * @var     string  $old    The old string
     * @var     string  $new    The new string
	 */
	public function render_content($old, $new) {
        $left_string  = $this->chapter_added? '' : normalize_whitespace($old);
        $right_string = $this->chapter_deleted? '' : normalize_whitespace($new);

        $left_lines  = explode("\n", $left_string);
        $right_lines = explode("\n", $right_string);

        if($old == $new){
            foreach($right_lines as $line){
                echo "<tr><td class='pb_rev_diff_cell pb_rev_diff_cell__context' colspan='2'>{$line}</td><td></td></tr>";
            }
        }

        if ( ! class_exists( 'WP_Text_Diff_Renderer_Table', false ) )
            require( ABSPATH . WPINC . '/wp-diff.php' );

        $text_diff = new \Text_Diff($left_lines, $right_lines);
        $renderer  = new Text_Diff_Renderer_Table(array(), $this->editors_content, $this->editors_name, $this->editors_settings );
        echo $renderer->render($text_diff);
        $editor_number = $renderer->editor_number;
        if(is_array($this->editors_content)){
            $r_content_a = array_filter($this->editors_content, function($k) use ($editor_number) {
                            return $k >= $editor_number;
                        }, ARRAY_FILTER_USE_KEY);
            if(count($r_content_a) > 0){
                $r_content = implode("\n", $r_content_a);
                echo '<tr><td></td><td></td><td class="pb_rev_diff_editor_cell pb_rev_diff_editor_cell__to_much">';
                echo '<span class="dashicons dashicons-warning"></span> These comments are not associated with any paragraph. Please move them to the right place.';
                wp_editor($r_content , $this->editors_name."-r", $this->editors_settings );
                echo "<input type='hidden' name='{$this->editors_name}-r-orig' value='".htmlspecialchars($r_content)."'";
                echo '</td></tr>';
            }
        }
        //TODO more comments then editors
	}
}
