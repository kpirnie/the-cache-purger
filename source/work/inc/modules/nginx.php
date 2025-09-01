<?php
/** 
 * NGINX module
 * 
 * This file contains the nginx purge methods
 * 
 * @since 8.1
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package The Cache Purger
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// check if this trait already exists
if( ! trait_exists( 'NGINX' ) ) {

    /**
     * Trait PHP
     *
     * This trait contains the nginx purge methods
     *
     * @since 8.1
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package The Cache Purger
     *
    */
    trait NGINX {

        /** 
         * purge_nginx_caches
         * 
         * This method attempts to purge nginx based caches
         * if they exist
         * 
         * @since 8.1
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        private function purge_nginx_caches( ) : void {

            // implement hook
            do_shortcode( 'tcp_pre_nginx_purge' );

            // hold the possible cache locations
            $_cache_paths = array(
                '/etc/nginx/cache/',
                '/var/cache/nginx/',
                '/var/cache/nginx-rc/',
                '/var/cache/ea-nginx/proxy/',
                '/var/nginx-cache/',
                '/var/nginx/cache/',
            );

            // loop over these paths
            foreach( $_cache_paths as $_path ) {

                // if it exists
                if( file_exists( $_path ) ) {

                    // log it
                    KPCPC::write_log( "\tNGINX PURGE" );
                    
                    // map the glob location of the cached files and delete them
                    array_map( 'unlink', glob( $_path . "*/*/*" ) );

                    // delete the parent
                    array_map( 'rmdir', glob( $_path . "*/*" ) );

                    // delete the grandparent
                    array_map( 'rmdir', glob( $_path . "*" ) );

                    // log the path cleared
                    KPCPC::write_log( "\t\t" . $_path . " Purged" );

                    // we found it, break the loop
                    break;

                }

            }
            
            // implement hook
            do_action( 'tcp_post_nginx_purge' );

        }

    }

}