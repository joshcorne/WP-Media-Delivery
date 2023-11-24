<?php

/**
 * This file contains the zipping functionality.
 *
 * @see        https://github.com/RalfAlbert/gallery-zip
 * @link       https://joshcorne.co.uk
 * @since      1.0.0
 *
 * @package    Media_Delivery
 * @subpackage Media_Delivery/public/partials
 */

class Zipper {
    /**
     * Internal error array
     * @var array
     */
    public $errors = array();

    /**
     * Name of the cache directory
     * @var string
     */
    public $cache_dir = '';
 
    /**
     * Constructor
     * - checks (and create) the cache directory
     */
    public function __construct( $cache_dir_prepend ) { 
        $this->cache_dir = WP_CONTENT_DIR . '/' . trim( $cache_dir_prepend ) . '-cache/';

        if ( ! is_dir( $this->cache_dir ) ) {
            wp_mkdir_p( $this->cache_dir );
        }
    }
 
    /**
     * Add message to internal error array
     * @param	string	$msg	Message to add
     * @return	boolean			Returns true if a message was set, else false
     */
    protected function add_error( $msg = '' ) {
        array_push( $this->errors, $msg );
        return ! empty( $msg );
    }
 
    /**
     * Converts absolute path to an url-path. Use WP_CONTENT_DIR/URL as base path
     * @param	string	$path	Path to be converted
     * @return	string			Converted path
     */
    public function to_url( $path ) {
        // We do not need to add cache_dir to content_url, it is in $path
        // WP_CONTENT_DIR - "[This] should not be used directly by plugins"
        return str_replace( WP_CONTENT_DIR, content_url( ), $path );
    }
 
    /**
     * Create a zip-file with name and path defined in target from a given file list
     * @param	string	$target		Name and path of the zip file
     * @param	string	$file_list	Array with pathes and filenames
     * @return	bool				True on success, false on error
     */
    public function zip_files( $target, $file_list ) {
        if ( ! is_array( $file_list ) )
            $file_list = (array) $file_list;
 
        if ( class_exists( 'ZipArchive' ) ) {
            return $this->ziparchive( $target, $file_list );
        } else {
            return $this->pclzip( $target, $file_list );
        }
    }
 
    /**
     * Zipping files with ZipArchive
     * @param	string	$target		Name and path of the zip file
     * @param	string	$file_list	Array with pathes and filenames
     * @return	bool				True on success, false on error
     */
    protected function ziparchive( $target, $file_list ) {
        $zip = new \ZipArchive();

        if ( ! $zip->open( $target, \ZIPARCHIVE::CREATE ) ) {
            $this->add_error( "Could not create temporary zip archive {$target}" );
            return;
        }

        foreach ( $file_list as $file ) {
            if ( file_exists( $file ) && is_readable( $file ) )
                $zip->addFile( $file, basename( $file ) );
        }

        $zip->close();

        return $zip;
    }
 
    /**
     * Zipping files with PclZip
     * @param	string	$target		Name and path of the zip file
     * @param	string	$file_list	Array with pathes and filenames
     * @return	bool				True on success, false on error
     */
    protected function pclzip( $target, $file_list ) {
        if ( ! class_exists( 'PclZip' ) )
            require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';
 
        $archive = new \PclZip( $target );
 
        $zip = $archive->create( $file_list );
        if ( 0 == $zip ) {
            $this->add_error( $archive->errorInfo( true ) );
            return;
        }
 
        return $zip;
    }
    
    /**
     * Create a zip file from list with images
     * @param	string	$zipname	Name of the zip-file (no path, no extension. just name)
     * @param	array	$images		Array with full pathes to images
     * @return	string				URL to the zip-file on success, empty string on error
     */
    public function zip_images( $zip_name, $images ) {
        $zip_name = preg_replace( '/\.zip$/is', '', $zip_name ) . '.zip';
        $target  = $this->cache_dir . ltrim( $zip_name, '/' );

        if ( ! file_exists( $target ) )
            $this->zip_files( $target, $images );

        $zip['url'] = $this->to_url( $target );
        $zip['filename'] = $target;

        return $zip;
    }
}