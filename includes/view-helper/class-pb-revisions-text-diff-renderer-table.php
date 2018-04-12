<?php
/**
 * Line Text Diff View Helper Table Renderer
 *
 * This class helps the text diff view helper to render a text diff.
 *
 * @since      1.0.0
 * @package    PbRevisions
 * @subpackage PbRevisions/includes/view_helper
 * @author     Lukas Kaiser <reg@lukaiser.com>
 * @uses WP_Text_Diff_Renderer_Table Extends
 */

namespace PBRevisions\includes\view_helper;

if ( ! class_exists( 'WP_Text_Diff_Renderer_Table', false ) )
            require( ABSPATH . WPINC . '/wp-diff.php' );

class Text_Diff_Renderer_Table extends \WP_Text_Diff_Renderer_Table {

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
	 * The current editor number
	 *
	 * @var int
	 * @since 1.0.0
	 */
    public $editor_number = 0;

	/**
	 * Constructor - Call parent constructor with params array.
	 *
	 * This will set class properties based on the key value pairs in the array.
	 *
	 * @since 2.6.0
	 *
	 * @param array $params
     * @param   array   $editors_content     The editor settings
     * @param   string  $editors_name        The name of the editor
     * @param   array   $editors_settings    The editor settings
	 */
	public function __construct( $params = array(), $editors_content, $editors_name, $editors_settings ) {
        parent::__construct( $params );
        $this->editors_content = $editors_content;
        $this->editors_name = $editors_name;
        $this->editors_settings = $editors_settings;
	}

    /**
	 * @ignore
	 *
	 * @param string $line HTML-escape the value.
	 * @return string
	 */
	public function changedLine( $line ) {
		return "<td class='pb_rev_diff_cell pb_rev_diff_cell__changed'>{$line}</td>";
	}

	/**
	 * @ignore
	 *
	 * @param string $line HTML-escape the value.
	 * @return string
	 */
	public function addedLine( $line ) {
		return "<tr><td class='pb_rev_diff_cell'>&nbsp;</td><td class='pb_rev_diff_cell pb_rev_diff_cell__added'>{$line}</td>".$this->editor()."</tr>\n";

	}

	/**
	 * @ignore
	 *
	 * @param string $line HTML-escape the value.
	 * @return string
	 */
	public function deletedLine( $line ) {
		return "<tr><td class='pb_rev_diff_cell pb_rev_diff_cell__removed'>{$line}</td><td class='pb_rev_diff_cell'>&nbsp;</td>".$this->editor()."</tr>\n";
	}

	/**
	 * @ignore
	 *
	 * @param string $line HTML-escape the value.
	 * @return string
	 */
	public function contextLine( $line ) {
		return "<tr><td colspan='2' class='pb_rev_diff_cell pb_rev_diff_cell__context'>{$line}</td><td></td></tr>\n";
	}

	/**
	 * @ignore
	 *
	 * @return string
	 */
	public function emptyLine() {
		return '<td class="pb_rev_diff_cell">&nbsp;</td>';
    }
    
    /**
	 * @ignore
	 *
	 * @return string
	 */
	public function editor() {
        $editor_number = $this->editor_number;
        $this->editor_number ++;
        $content = isset($this->editors_content[$editor_number]) ? $this->editors_content[$editor_number] : "";
        ob_start();
	    wp_editor($content , $this->editors_name."-".$editor_number, $this->editors_settings );
		return '<td class="pb_rev_diff_editor_cell">'.ob_get_clean().'</td>';
	}

	/**
	 * @ignore
	 *
	 * @param array $lines
	 * @param bool $encode
	 * @return string
	 */
	public function _added( $lines, $encode = true ) {
		$r = '';
		foreach ( $lines as $line ) {
			if ( $encode ) {
				$processed_line = htmlspecialchars( $line );

				/**
				 * Contextually filters a diffed line.
				 *
				 * Filters TextDiff processing of diffed line. By default, diffs are processed with
				 * htmlspecialchars. Use this filter to remove or change the processing. Passes a context
				 * indicating if the line is added, deleted or unchanged.
				 *
				 * @since 4.1.0
				 *
				 * @param String $processed_line The processed diffed line.
				 * @param String $line           The unprocessed diffed line.
				 * @param string null            The line context. Values are 'added', 'deleted' or 'unchanged'.
				 */
				$line = apply_filters( 'process_text_diff_html', $processed_line, $line, 'added' );
			}
			$r .= $this->addedLine( $line );
		}
		return $r;
	}

	/**
	 * @ignore
	 *
	 * @param array $lines
	 * @param bool $encode
	 * @return string
	 */
	public function _deleted( $lines, $encode = true ) {
		$r = '';
		foreach ( $lines as $line ) {
			if ( $encode ) {
				$processed_line = htmlspecialchars( $line );

				/** This filter is documented in wp-includes/wp-diff.php */
				$line = apply_filters( 'process_text_diff_html', $processed_line, $line, 'deleted' );
			}
			$r .= $this->deletedLine( $line );
		}
		return $r;
	}

	/**
	 * @ignore
	 *
	 * @param array $lines
	 * @param bool $encode
	 * @return string
	 */
	public function _context( $lines, $encode = true ) {
		$r = '';
		foreach ( $lines as $line ) {
			if ( $encode ) {
				$processed_line = htmlspecialchars( $line );

				/** This filter is documented in wp-includes/wp-diff.php */
				$line = apply_filters( 'process_text_diff_html', $processed_line, $line, 'unchanged' );
			}
			$r .= $this->contextLine( $line );
		}
		return $r;
	}

	/**
	 * Process changed lines to do word-by-word diffs for extra highlighting.
	 *
	 * (TRAC style) sometimes these lines can actually be deleted or added rows.
	 * We do additional processing to figure that out
	 *
	 * @since 1.0.0
	 *
	 * @param array $orig
	 * @param array $final
	 * @return string
	 */
	public function _changed( $orig, $final ) {
        $r = '';
        
		// Does the aforementioned additional processing
		// *_matches tell what rows are "the same" in orig and final. Those pairs will be diffed to get word changes
		//	match is numeric: an index in other column
		//	match is 'X': no match. It is a new row
		// *_rows are column vectors for the orig column and the final column.
		//	row >= 0: an indix of the $orig or $final array
		//	row  < 0: a blank row for that column
		list($orig_matches, $final_matches, $orig_rows, $final_rows) = $this->interleave_changed_lines( $orig, $final );

		// These will hold the word changes as determined by an inline diff
		$orig_diffs  = array();
		$final_diffs = array();

		// Compute word diffs for each matched pair using the inline diff
		foreach ( $orig_matches as $o => $f ) {
			if ( is_numeric( $o ) && is_numeric( $f ) ) {
				$text_diff = new \Text_Diff( 'auto', array( array( $orig[ $o ] ), array( $final[ $f ] ) ) );
				$renderer  = new $this->inline_diff_renderer;
				$diff      = $renderer->render( $text_diff );

				// If they're too different, don't include any <ins> or <dels>
				if ( preg_match_all( '!(<ins>.*?</ins>|<del>.*?</del>)!', $diff, $diff_matches ) ) {
					// length of all text between <ins> or <del>
					$stripped_matches = strlen( strip_tags( join( ' ', $diff_matches[0] ) ) );
					// since we count lengith of text between <ins> or <del> (instead of picking just one),
					//	we double the length of chars not in those tags.
					$stripped_diff = strlen( strip_tags( $diff ) ) * 2 - $stripped_matches;
					$diff_ratio    = $stripped_matches / $stripped_diff;
					if ( $diff_ratio > $this->_diff_threshold ) {
						continue; // Too different. Don't save diffs.
					}
				}

				// Un-inline the diffs by removing del or ins
				$orig_diffs[ $o ]  = preg_replace( '|<ins>.*?</ins>|', '', $diff );
				$final_diffs[ $f ] = preg_replace( '|<del>.*?</del>|', '', $diff );
			}
		}

		foreach ( array_keys( $orig_rows ) as $row ) {
			// Both columns have blanks. Ignore them.
			if ( $orig_rows[ $row ] < 0 && $final_rows[ $row ] < 0 ) {
				continue;
			}

			// If we have a word based diff, use it. Otherwise, use the normal line.
			if ( isset( $orig_diffs[ $orig_rows[ $row ] ] ) ) {
				$orig_line = $orig_diffs[ $orig_rows[ $row ] ];
			} elseif ( isset( $orig[ $orig_rows[ $row ] ] ) ) {
				$orig_line = htmlspecialchars( $orig[ $orig_rows[ $row ] ] );
			} else {
				$orig_line = '';
			}

			if ( isset( $final_diffs[ $final_rows[ $row ] ] ) ) {
                $total = false;
				$final_line = $final_diffs[ $final_rows[ $row ] ];
			} elseif ( isset( $final[ $final_rows[ $row ] ] ) ) {
                $total = true;
				$final_line = htmlspecialchars( $final[ $final_rows[ $row ] ] );
			} else {
				$final_line = '';
			}

			if ( $orig_rows[ $row ] < 0 || empty($orig_line) ) { // Orig is blank. This is really an added row.
				$r .= $this->_added( array( $final_line ), false );
			} elseif ( $final_rows[ $row ] < 0 || empty($final_line) ) { // Final is blank. This is really a deleted row.
				$r .= $this->_deleted( array( $orig_line ), false );
			} elseif ( $total ) { // A true changed row.
				$r .= '<tr><td class="pb_rev_diff_cell pb_rev_diff_cell__removed">'.$orig_line.'</td><td class="pb_rev_diff_cell pb_rev_diff_cell__added">'.$final_line.'</td>' .$this->editor()."</tr>\n";
			} else { // A true changed row.
				$r .= '<tr>' . $this->changedLine( $orig_line ) . $this->changedLine( $final_line ) .$this->editor()."</tr>\n";
			}
		}

		return $r;
	}

	
}