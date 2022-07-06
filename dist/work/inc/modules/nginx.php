<?php
/** 
 * NGINX module
 * 
 * This file contains the nginx purge methods
 * 
 * @since 7.4
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
     * @since 7.4
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
         * @since 7.4
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

            // let's utilize wordpress's filesystem global
            global $wp_filesystem;

            // if we do not have the global yet
            if( empty( $wp_filesystem ) ) {

                // require the file
                require_once ABSPATH . '/wp-admin/includes/file.php';

                // initialize the wordpress filesystem
                WP_Filesystem( );

            }

            // hold the cache location, we'll need to try a few locations
            $_cache_path = '';

            // check the /etc/nginx location
            if( @is_readable( '/etc/nginx/cache/' ) ) {

                // set the cache path
                $_cache_path = '/etc/nginx/cache/';

            // check the /var/cache location
            } elseif( @is_readable( '/var/cache/nginx/') ) {

                // set the cache path
                $_cache_path = '/var/cache/nginx/';

            // check the /var/cache location for runcloud
            } elseif( @is_readable( '/var/cache/nginx-rc/') ) {

                // set the cache path
                $_cache_path = '/var/cache/nginx-rc/';

            // check the /var/cache location for cpanel
            } elseif( @is_readable( '/var/cache/ea-nginx/proxy/') ) {

                // set the cache path
                $_cache_path = '/var/cache/ea-nginx/proxy/';
            
            // check the /var/nginx-cache location inmotion
            } elseif( @is_readable( '/var/nginx-cache/') ) {

                // set the cache path
                $_cache_path = '/var/nginx-cache/';

            }

            // get our URL list
            $_pages = KPCPC::get_urls( );

            // make sure we have pages
            if( $_pages ) {

                // hold our index
                $_idx = 0;

                // loop
                do {
                    
                    // parse the URL
                    $_url = parse_url( $_pages[$_idx] );

                    // get the scheme
                    $_scheme = $_url['scheme'];

                    // get the host
                    $_host = $_url['host'];

                    // the uri
                    $_requesturi = $_url['path'];

                    // hash it
                    $_hash = md5( $_scheme . 'GET' . $_host . $_requesturi );

                    // now setup the full path
                    $_path = $_cache_path . substr( $_hash, -1 ) . '/' . substr( $_hash, -3 ,2 ) . '/' . $_hash;

                    // if it's a directory
                    if( $wp_filesystem -> is_dir( $_path ) ) {

                        // try to delete it recursively
                        $wp_filesystem -> delete( $_path, true, 'd' );

                    // otherwise it's a file
                    } else {

                        // try to delete it
                        $wp_filesystem -> delete( $_path, false, 'f' );

                    }
                    
                    // increment the index
                    ++$_idx;

                    // keep running while the index is less than the number of pages
                } while ( $_idx < count( $_pages ) );


                // log the purge
                KPCPC::write_log( "\tNGINX PURGE" );

            }

            // implement hook
            do_action( 'tcp_post_nginx_purge' );

        }

    }

}