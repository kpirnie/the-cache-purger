<?php
/** 
 * PAGESPEED module
 * 
 * This file contains the pagespeed purge methods
 * 
 * @since 7.4
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package The Cache Purger
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// check if this trait already exists
if( ! trait_exists( 'PAGESPEED' ) ) {

    /**
     * Trait PHP
     *
     * This trait contains the pagespeed purge methods
     *
     * @since 7.4
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package The Cache Purger
     *
    */
    trait PAGESPEED {

        /** 
         * purge_pagespeed_caches
         * 
         * This method attempts to purge the PageSpeed Mod caches
         * 
         * @since 7.4
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        private function purge_pagespeed_caches( ) : void {

            // implement hook
            do_action( 'tcp_pre_pagespeed_purge' );

            // hold possible pagespeed headers
            $_ps_headers = array( 'x-mod-pagespeed', 'x-page-speed' );

            // get the remote URL's headers only
            $_res = wp_get_http_headers( site_url( ) );

            // check if there's an error
            if ( is_wp_error( $_res ) ) {
                
                // there was.  this means we cannot purge this cache, so dump out of the method
                return;
            }

            // cast it as an array, and reset the index
            // NOTE: we have to do this, because the WP Dev page lies, this is not returned as an array, it's returned as a object(Requests_Utility_CaseInsensitiveDictionary)
            $_headers = array_values( ( array ) $_res );

            // check that our header return is actually an array & check if our pagespeed headers are in the response headers
            if( is_array( $_headers[0] ) && ! in_array( $_ps_headers, $_headers[0] ) ) {

                // the are not, this means the pagespeed module is not installed on the server, and we can dump out of this method
                return;

            }

            // hold a "is cloudflare" flag
            $_is_cf = ( isset( $_headers[0]['server'] ) && ( strpos( $_headers[0]['server'], 'cloudflare' ) !== false ) );

            // hold our default request arguments
            $_args = array(
                'method' => 'PURGE',
                'redirection' => 0,
            );

            // get the server IP
            $_server_ip = filter_input( INPUT_SERVER, 'SERVER_ADDR', FILTER_VALIDATE_IP );

            // get our URL list
            $_urls = KPCPC::get_urls( );

            // make sure we have some returned
            if( $_urls ) {

                // loop them
                foreach( $_urls as $_url ) {

                    // if it is run through cloudflare
                    if( $_is_cf ) {

                        // parse the url
                        $_link = wp_parse_url( $_url );

                        // rebuild the URL
                        $_url = $_link['scheme'] . '://' . $_server_ip . $_link['path'];

                        // set some more arguments
                        $_args['redirection'] = 5; // -L
                        $_args['sslverify'] = false; // -k
                        $_args['headers'] = 'host: ' . $_link['host'];

                    }

                    // make the remote PURGE request
                    wp_remote_request( $_url, $_args );

                }

                // log the purge
                KPCPC::write_log( "\tPAGESPEED PURGE" );

            }

            // implement hook
            do_action( 'tcp_post_pagespeed_purge' );

        }

    }

}
