<?php

/**
 * Export
 *
 * Functions helping with Export
 *
 * @since      1.0.0
 * @package    Pb_Revisions
 * @subpackage Pb_Revisions/includes
 * @author     Lukas Kaiser <reg@lukaiser.com>
 */
namespace PBRevisions\includes;

class Export {
    /**
     * Scan the exports directory, return the files grouped into intervals of 3 minutes, newest first.
     *
     * @param string $dir fullpath to the Exports folder. (optional)
     * @return array
     */
    static function group_exports() {

        $dir = \Pressbooks\Modules\Export\Export::getExportFolder();

        $ignored = array( '.', '..', '.svn', '.git', '.htaccess' );
        
        $store = new \PBRevisions\includes\Store();
        $av = $store->get_active_version_number();

        $public_files = self::latest_exports_of_version($dir.$av);

        $sections = array();
        $working = \Pressbooks\Utility\group_exports($dir);
        $version = false;
        foreach($working as $date => $section){
            $files = array();
            foreach($section as $file){
                $files[] = array(
                    'file' => $file,
                    'public' => ($av == $version && in_array($file, $public_files))
                );
            }
            $sections[$date] = array(
                'files' => $files,
                'version' => 'working',
                'folder' => '',
                'date' => $date
            );
        }

        foreach ( scandir( $dir ) as $version ) {
            if ( in_array( $version, $ignored ) || !is_dir($dir . $version) ) { continue;
            }
            $version_sections = \Pressbooks\Utility\group_exports($dir . $version);
            foreach($version_sections as $date => $section){
                $files = array();
                foreach($section as $file){
                    $files[] = array(
                        'file' => $file,
                        'public' => ($av == $version && in_array($file, $public_files))
                    );
                }
                $sections[$date] = array(
                    'files' => $files,
                    'version' => $version,
                    'folder' => $version,
                    'date' => $date
                );
            }
        }
        
        krsort($sections);

        return $sections;
    }

    /**
     * Scan the export directory, return latest of each file type
     *
     * @return array
     */
    static function latest_exports_of_version($dir) {

        if ( ! file_exists( $dir ) ) {
            return array();
        }
        
        /**
         * @since 1.0.0
         * Add custom export formats to the latest exports filetype mapping array.
         *
         * For example, here's how one might add a hypothetical Word export format:
         *
         * add_filter( 'pb_latest_export_filetypes', function ( $filetypes ) {
         * 	$filetypes['word'] = '.docx';
         *	return $filetypes;
        * } );
        *
        */
        $filetypes = apply_filters( 'pb_latest_export_filetypes', array(
            'epub3' => '._3.epub',
            'epub' => '.epub',
            'pdf' => '.pdf',
                'print-pdf' => '._print.pdf',
            'mobi' => '.mobi',
            'icml' => '.icml',
            'xhtml' => '.html',
            'wxr' => '.xml',
            'vanillawxr' => '._vanilla.xml',
            'mpdf' => '._oss.pdf',
            'odf' => '.odt',
        ) );

        $files = array();

        // group by extension, sort by date newest first
        foreach ( \Pressbooks\Utility\scandir_by_date( $dir ) as $file ) {
            // only interested in the part of filename starting with the timestamp
            if(preg_match( '/-\d{10,11}(.*)/', $file, $matches )){

                // grab the first captured parenthisized subpattern
                $ext = $matches[1];

                $files[ $ext ][] = $file;
            }
        }

        // get only one of the latest of each type
        $latest = array();

        foreach ( $filetypes as $type => $ext ) {
            if ( array_key_exists( $ext, $files ) ) {
                $latest[ $type ] = $files[ $ext ][0];
            }
        }
        // @TODO filter these results against user prefs

        return $latest;
    }
}