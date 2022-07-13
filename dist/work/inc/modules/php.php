<?php
/** 
 * PHP module
 * 
 * This file contains the php purge methods
 * 
 * @since 7.4
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package The Cache Purger
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// check if this trait already exists
if( ! trait_exists( 'PHP' ) ) {

    /**
     * Trait PHP
     *
     * This trait contains the php purge methods
     *
     * @since 7.4
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package The Cache Purger
     *
    */
    trait PHP {

        // hold our php cache slugs
        protected $_php_caches = array(
            'purge_php_wincache',
            'purge_php_opcache',
            'purge_php_apc',
            'purge_php_xcache',
        );

        /** 
         * purge_php_caches
         * 
         * This method attempts to purge php based caches
         * if they exist; wincache, opcache, apc and apcu
         * 
         * @since 7.4
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        private function purge_php_caches( ) : void {

            // implement hook
            do_action( 'tcp_pre_php_purge' );

            // log it
            KPCPC::write_log( "\tPHP PURGE" );

            // loop over the slug array
            foreach( $this -> _php_caches as $_php ) {

                // fire up the method to do the purge
                $this -> { $_php }( );

            }

            // release the array
            unset( $this -> _php_caches );

            // implement hook
            do_action( 'tcp_post_php_purge' );

        }

        /** 
         * purge_php_wincache
         * 
         * This method attempts to utilize the purge the 
         * php wincaches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_php_wincache( ) : void {

            // if we're on a windows server
            if( function_exists( 'wincache_ucache_get' ) ) {

                // clear it
                wincache_ucache_clear( );

                // log the purge
                KPCPC::write_log( "\t\tPHP Win Cache" );

            }

        }

        /** 
         * purge_php_opcache
         * 
         * This method attempts to utilize the purge the 
         * php Zend opcaches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_php_opcache( ) : void {

            // check if the Zend Opcache is available
            if( extension_loaded( 'Zend OPcache' ) ) {

                // get the status, silence the errors if any
                $_status = @opcache_get_status( );

                // make sure it's enabled
                if( isset( $_status["opcache_enabled"] ) ) {

                    // attempt to reset it
                    opcache_reset( );

                    // now try to clear the php file cache
                    foreach( $_status['scripts'] as $_k => $_v ) {

                        // set the directories
                        $dirs[dirname( $_k )][basename( $_k )] = $_v;
                        
                        // invalidate it
                        opcache_invalidate( $_v['full_path'] , $force = true );

                    }

                    // log the purge
                    KPCPC::write_log( "\t\tPHP Zend OpCache" );

                }

            }

        }

        /** 
         * purge_php_apc
         * 
         * This method attempts to utilize the purge the 
         * php apc caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_php_apc( ) : void {

            // check if the APC extension is enabled
            if( extension_loaded( 'apc' ) ) {

                // try to clear it's opcache
                apc_clear_cache( 'opcode' );

                // try to clear it's user cache
                apc_clear_cache( 'user' );

                // log the purge
                KPCPC::write_log( "\t\tPHP APC Cache" );

            }

        }

        /** 
         * purge_php_xcache
         * 
         * This method attempts to utilize the purge the 
         * php xcache
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_php_xcache( ) : void {

            // check if the xcache extension is enabled
            if( extension_loaded( 'xcache' ) ) {

                // make sure there is no auth enabled for it
                if( ! ini_get( 'xcache.admin.enable_auth' ) ) {

                    // purge it
                    xcache_clear_cache( XC_TYPE_PHP );

                    // log the purge
                    KPCPC::write_log( "\t\tPHP XCACHE Cache" );

                }

            }

        }

    }

}
