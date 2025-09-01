<?php
/** 
 * API module
 * 
 * This file contains the api purge methods
 * 
 * @since 8.1
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
     * @since 8.1
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
         * @since 8.1
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

            // get the cloudflare token
            $_cf_token = ( $_opt -> service_api_keys['cloudflare_token'] ) ?? null;

            // get the cloudflare zone
            $_cf_zone = ( $_opt -> service_api_keys['cloudflare_zone'] ) ?? null;

            // get the sucuri key
            $_sucuri_key = ( $_opt -> service_api_keys['sucuri_key'] ) ?? null;
            
            // get the sucuri secret
            $_sucuri_secret = ( $_opt -> service_api_keys['sucuri_secret'] ) ?? null;

            // get the fastly token
            $_fastly_token = ( $_opt -> service_api_keys['fastly_token'] ) ?? null;

            // get the fastly service id
            $_fastly_service_id = ( $_opt -> service_api_keys['fastly_service_id'] ) ?? null;

            // make sure we have keys for cloudflare
            if( $_cf_token && $_cf_zone ) {

                // cloudflare
                $this -> purge_api_cloudflare( $_cf_token, $_cf_zone );

            }

            // make sure we have keys for sucuri
            if( $_sucuri_key && $_cf_zone ) {

                // sucuri
                $this -> purge_api_sucuri( $_sucuri_key, $_sucuri_secret );

            }

            // make sure we have a token and service id for fastly
            if( $_fastly_token && $_fastly_service_id ) {

                // fastly purge
                $this -> purge_api_fastly( $_fastly_token, $_fastly_service_id );

            }

            // we dont need the option anymore so dump it
            unset( $_opt );

        }

        /** 
         * purge_api_fastly
         * 
         * This method attempts to purge the fastly cdn caches
         * 
         * @since 8.1
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @param string $_token The fastly cdn token
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_api_fastly( string $_token = '', string $_service_id = '' ) : void {

            // make sure it actually exists
            if( $_token && $_service_id ) {

                // start log for it
                KPCPC::write_log( "\t\tFASTLY CDN PURGE");

                // setup the config
                $_conf = Fastly\Configuration::getDefaultConfiguration( ) -> setApiToken( sanitize_text_field( $_token ) );

                // hold the api instance
                $_api = new Fastly\Api\PurgeApi( null, $_conf );
                
                // now hold the service ID option
                $_opt = array(
                    'service_id' => sanitize_text_field( $_service_id ),
                );

                // rty and catch an exception
                try {

                    // purge all!
                    $_api -> purgeAll( $_opt );

                    // we're golden
                    KPCPC::write_log( "\t\t\tSUCCESS");

                // whoops there was an issue
                } catch (Exception $e) {

                    // log the issue
                    KPCPC::write_log( "\t\t\tFAILED - " . $e -> getMessage( ) );

                }

            }

        }

        /** 
         * purge_api_sucuri
         * 
         * This method attempts to purge the sucuri cache configured
         * 
         * @since 8.1
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @param string $_key The sucuri api key
         * @param string $_secret The sucuri api secret
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_api_sucuri( string $_key = '', string $_secret ='' ) : void {

            // make sure they both exist
            if( $_key && $_secret ) {

                // start log for it
                KPCPC::write_log( "\t\tSUCURI PURGE");

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
                    KPCPC::write_log( "\t\t\tSUCCESS");

                } else {

                    // it actually failed, so log it
                    KPCPC::write_log( "\t\t\tFAILED - " . trim( preg_replace( '/\s+/', ' ', $_req ) ) );

                }

            }

        }

        /** 
         * purge_cloudflare
         * 
         * This method attempts to purge the cloudflare cache configured
         * 
         * @since 8.1
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @param string $_token The Cloudflare token
         * @param string $_zone The Cloudflare zone
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_api_cloudflare( string $_token = '', string $_zone ='' ) : void {

            // make sure we have all required fields
            if( $_token && $_zone ) {

                // start log for it
                KPCPC::write_log( "\t\tCLOUDFLARE PURGE");

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
                        KPCPC::write_log( "\t\t\tFAILED - " . $_json['errors'][0]['message'] );

                    } else {

                        // log it
                        KPCPC::write_log( "\t\t\tSUCCESS");

                    }

                } else {

                    // log it
                    KPCPC::write_log( "\t\t\tFAILED - EMPTY RESPONSE, CHECK CLOUDFLARE LOGS" );

                }

            }

        }

    }

}
