<?php
/** 
 * API module
 * 
 * This file contains the api purge methods
 * 
 * @since 7.4
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package The Cache Purger
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// check if this trait already exists
if( ! trait_exists( 'API' ) ) {

    /**
     * Trait FILE
     *
     * This trait contains the api purge methods
     *
     * @since 7.4
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package The Cache Purger
     *
    */
    trait API {

        /** 
         * purge_remote_apiserver_caches
         * 
         * This method attempts to utilize the purge methods 
         * of the configured remote caches
         * 
         * @since 7.4
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        private function purge_remote_apiserver_caches( ) : void {

            // get our options 
            $_opt = KPCPC::get_options( );

            // cloudflare
            $this -> purge_cloudflare( $_opt );

            // sucuri
            $this -> purge_sucuri( $_opt );

        }

        /** 
         * purge_sucuri
         * 
         * This method attempts to purge the sucuri cache configured
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @param object $_opt The options object
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_sucuri( object $_opt ) : void {

            // get the key
            $_key = ( $_opt -> service_api_keys['sucuri_key'] ) ?? null;
            
            // get the secret
            $_secret = ( $_opt -> service_api_keys['sucuri_secret'] ) ?? null;

            // make sure they both exist
            if( $_key && $_secret ) {

                // start log for it
                KPCPC::write_log( "\tSUCURI PURGE");

                // create the request URL
                $_url = sprintf(
                    'https://waf.sucuri.net/api?k=%s&s=%s&a=clearcache',
                    sanitize_text_field( $_key ),
                    sanitize_text_field( $_secret )
                );

                // fire up the wordpress file system
                $wp_filesystem = new WP_Filesystem_Direct( null );

                // get the contents of this request, since it's plain text
                $_req = $wp_filesystem -> get_contents( $_url );

                // check if the response contains an OK
                if( strpos( $_req, 'OK' ) !== false ) {

                    // log it
                    KPCPC::write_log( "\t\tSUCCESS");

                } else {

                    // it actually failed, so log it
                    KPCPC::write_log( "\t\tFAILED - " . trim( preg_replace( '/\s+/', ' ', $_req ) ) );

                }

            }

        }

        /** 
         * purge_cloudflare
         * 
         * This method attempts to purge the cloudflare cache configured
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @param object $_opt The options object
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_cloudflare( object $_opt ) : void {

            // get the cloudflare token
            $_token = ( $_opt -> service_api_keys['cloudflare_token'] ) ?? null;
            
            // get the cloudflare zone
            $_zone = ( $_opt -> service_api_keys['cloudflare_zone'] ) ?? null;

            // make sure we have all required fields
            if( $_token && $_zone ) {

                // start log for it
                KPCPC::write_log( "\tCLOUDFLARE PURGE");

                // setup our arguments
                $_args = array(
                    'headers' => array(
                        'timeout' => 5,
                        'blocking' => false,
                        'Authorization' => "Bearer " . sanitize_text_field( $_token ),
                        'Content-Type' => 'application/json',
                    ),
                    'body' => json_encode( array( 'purge_everything' => true ) ),
                );

                // setup the URL
                $_url = sprintf(
                    'https://api.cloudflare.com/client/v4/zones/%s/purge_cache',
                    sanitize_text_field( $_zone )
                );

                // utilize wordpress's built-in remote post
                $_req = wp_safe_remote_post( $_url, $_args );

                // get the responses body
                $_resp = wp_remote_retrieve_body( $_req );

                // if there is a response
                if( ! empty( $_resp ) ) {

                    // decode the json
                    $_json = json_decode( $_resp, true );

                    // if it was not successful
                    if( ! $_json['success'] ) {

                        // log it
                        KPCPC::write_log( "\t\tFAILED - " . $_json['errors'][0]['message'] );

                    } else {

                        // log it
                        KPCPC::write_log( "\t\tSUCCESS");

                    }

                } else {

                    // log it
                    KPCPC::write_log( "\t\tFAILED - EMPTY RESPONSE, CHECK CLOUDFLARE LOGS" );

                }

            }

        }

    }

}
