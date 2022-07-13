<?php
/** 
 * FILE module
 * 
 * This file contains the file purge methods
 * 
 * @since 7.4
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
     * @since 7.4
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
         * @since 7.4
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

            // let's utilize wordpress's filesystem global
            global $wp_filesystem;

            // if we do not have the global yet
            if( empty( $wp_filesystem ) ) {

                // require the file
                require_once ABSPATH . '/wp-admin/includes/file.php';

                // initialize the wordpress filesystem
                WP_Filesystem( );

            }

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

            // if it exists
            if( file_exists( $_cache_path ) ) {
                    
                // map the glob location of the cached files and delete them
                array_map( 'unlink', glob( $_cache_path . "*/*/*" ) );

                // delete the parent
                array_map( 'rmdir', glob( $_cache_path . "*/*" ) );

                // delete the grandparent
                array_map( 'rmdir', glob( $_cache_path . "*" ) );

                // log the path cleared
                KPCPC::write_log( "\t\t" . $_cache_path . " Purged" );

            }
            
            // implement hook
            do_action( 'tcp_post_file_purge' );

        }

    }

}
