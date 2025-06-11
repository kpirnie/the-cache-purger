<?php
/** 
 * FILE module
 * 
 * This file contains the file purge methods
 * 
 * @since 8.1
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package The Cache Purger
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// check if this trait already exists
if( ! trait_exists( 'FILE' ) ) {

    /**
     * Trait FILE
     *
     * This trait contains the file purge methods
     *
     * @since 8.1
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package The Cache Purger
     *
    */
    trait FILE {

        /** 
         * purge_file_caches
         * 
         * This method attempts to delete the file based caches
         * 
         * @since 8.1
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        private function purge_file_caches( ) : void {

            // implement hook
            do_action( 'tcp_pre_file_purge' );

            // hold our built cache path variable
            $_cache_path = '';

            // if the WPCACHE path is set
            if( defined( 'WPCACHEHOME' ) ) {

                // set the cache path to it
                $_cache_path = WPCACHEHOME;

            // otherwise, attempt to build one
            } else {

                // set it
                $_cache_path = ABSPATH . 'wp-content/cache/';

            }

            // log it
            KPCPC::write_log( "\tFILE PURGE" );

            // fire up our internal deleter
            $this -> full_delete( $_cache_path );

            // log the path cleared
            KPCPC::write_log( "\t\tPath: " . $_cache_path );
            
            // implement hook
            do_action( 'tcp_post_file_purge' );

        }

        /** 
         * full_delete
         * 
         * This method does the actual file or folder removal
         * also works recursively
         * 
         * @since 8.1
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        private function full_delete( string $_path ) : void {

            // make sure the path exists
            if ( file_exists( $_path ) ) {

                // setup the path iterators
                $_it = new RecursiveDirectoryIterator( $_path, FilesystemIterator::SKIP_DOTS ); // ensures no . or .. files
                $_it = new RecursiveIteratorIterator( $_it, RecursiveIteratorIterator::CHILD_FIRST );
                
                // loop over them
                foreach( $_it as $_file ) {
                    
                    // check if it's a directory
                    if ( $_file -> isDir( ) ) {

                        // attempt to remove it
                        @rmdir( $_file -> getPathname( ) );
                    
                    // it's actually a file    
                    } else {
                    
                        // try to delete it
                        @unlink( $_file -> getPathname( ) );
                    
                    }
                
                }
                
                // now try to dump the parent
                @rmdir( $_path );

            }

        }

    }

}
