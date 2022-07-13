<?php
/** 
 * HOSTING module
 * 
 * This file contains the hosting purge methods
 * 
 * @since 7.4
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package The Cache Purger
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// check if this trait already exists
if( ! trait_exists( 'HOSTING' ) ) {

    /**
     * Trait HOSTING
     *
     * This trait contains the hosting purge methods
     *
     * @since 7.4
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package The Cache Purger
     *
    */
    trait HOSTING {

        protected $_hosting_caches = array(
            'purge_hosting_wpengine',
            'purge_hosting_kinsta',
            'purge_hosting_godaddy',
            'purge_hosting_bluehost',
            'purge_hosting_cloudways',
            'purge_hosting_pantheon',
            'purge_hosting_siteground',
            'purge_hosting_runcloud',
        );

        /** 
         * purge_hosting_caches
         * 
         * This method attempts to utilize the purge methods 
         * of the most common hosting environments
         * 
         * @since 7.4
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        private function purge_hosting_caches( ) : void {

            // throw a hook here
            do_action( 'tcp_pre_hosting_purge' );

            // log the purge
            KPCPC::write_log( "\tHOSTING PURGE" );

            // loop over the slug array
            foreach( $this -> _hosting_caches as $_host ) {

                // fire up the method to do the purge
                $this -> { $_host }( );

            }

            // clean em up
            unset( $_hosting_caches );

            // throw a hook here
            do_action( 'tcp_post_hosting_purge' );

        }

        /** 
         * purge_hosting_wpengine
         * 
         * This method attempts to utilize the purge the 
         * wpengine caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_hosting_wpengine( ) : void {

            // WPEngine
            if( class_exists( 'WpeCommon' ) ) {

                // clear memcached
                if ( method_exists( 'WpeCommon', 'purge_memcached' ) ) {
                    WpeCommon::purge_memcached( );
                }

                // clear cdn
                if ( method_exists( 'WpeCommon', 'clear_maxcdn_cache' ) ) {
                    WpeCommon::clear_maxcdn_cache( );
                }

                // clear varnish
                if ( method_exists( 'WpeCommon', 'purge_varnish_cache' ) ) {
                    WpeCommon::purge_varnish_cache( );
                }

                // clear object cache
                if ( is_callable( 'WpeCommon::instance' ) && $_inst = WpeCommon::instance( ) ) {
                    method_exists( $_inst, 'purge_object_cache' ) ? $_inst -> purge_object_cache( ) : '';
                }

                // log the purge
                KPCPC::write_log( "\t\tWPEngine Cache" );

            }

        }

        /** 
         * purge_hosting_kinsta
         * 
         * This method attempts to utilize the purge the 
         * kinsta caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_hosting_kinsta( ) : void {

            // Kinsta Cache.
            if( class_exists( 'Kinsta\Cache' ) ) {

                // $kinsta_cache object already created by Kinsta cache.php file.
                global $kinsta_cache;

                // try to purge the full page caches
                $kinsta_cache -> kinsta_cache_purge -> purge_complete_full_page_cache( );

                // try to purg the rest
                $kinsta_cache -> kinsta_cache_purge -> purge_complete_caches( );

                // log the purge
                KPCPC::write_log( "\t\tKinsta Cache" );

            }

        }

        /** 
         * purge_hosting_godaddy
         * 
         * This method attempts to utilize the purge the 
         * godaddy caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_hosting_godaddy( ) : void {

            // GoDaddy
            if( class_exists( '\WPaaS\Cache' ) ) {

                // purge and ban the GoDaddy Cache
                remove_action( 'shutdown', [ '\WPaaS\Cache', 'purge' ], PHP_INT_MAX );
                add_action( 'shutdown', [ '\WPaaS\Cache', 'ban' ], PHP_INT_MAX );

                // log the purge
                KPCPC::write_log( "\t\tGoDaddy Cache" );

            }

        }

        /** 
         * purge_hosting_bluehost
         * 
         * This method attempts to utilize the purge the 
         * bluehost caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_hosting_bluehost( ) : void {

            // Bluehost
            if ( class_exists( 'Endurance_Page_Cache' ) ) {
                
                // try it
                do_action( 'epc_purge' );

                // log the purge
                KPCPC::write_log( "\t\tBlueHost Cache" );
                
            }

        }

        /** 
         * purge_hosting_cloudways
         * 
         * This method attempts to utilize the purge the 
         * cloudways caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_hosting_cloudways( ) : void {

            // Cloudways
            if ( class_exists( 'Breeze_Admin' ) ) {

                // fire up the class
                $_ba = new Breeze_Admin( );

                // try to clear it
                $_ba -> breeze_clear_all_cache( );

                // log the purge
                KPCPC::write_log( "\t\tCloudways Cache" );
            }

        }

        /** 
         * purge_hosting_pantheon
         * 
         * This method attempts to utilize the purge the 
         * pantheon caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_hosting_pantheon( ) : void {

            // Pantheon
            if( function_exists( 'pantheon_clear_edge_all' ) ) {

                // purge all caches
                pantheon_clear_edge_all( );

                // log the purge
                KPCPC::write_log( "\t\tPantheon Cache" );

            }

        }

        /** 
         * purge_hosting_siteground
         * 
         * This method attempts to utilize the purge the 
         * siteground caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_hosting_siteground( ) : void {

            // Siteground
            if ( isset( $GLOBALS['sg_cachepress_supercacher'] ) ) {

                // grab it's globa;
                global $sg_cachepress_supercacher;
    
                // make sure it exists
                if ( is_object( $sg_cachepress_supercacher ) && method_exists( $sg_cachepress_supercacher, 'purge_cache' ) ) {
                    
                    // purge
                    $sg_cachepress_supercacher->purge_cache( true );
                }

                // log the purge
                KPCPC::write_log( "\t\tSiteground Cache" );
    
            // otherwise
            } else if ( function_exists( 'sg_cachepress_purge_cache' ) ) {
                
                // purge
                sg_cachepress_purge_cache( );

                // log the purge
                KPCPC::wite_log( "\t\tSiteground Cache" );
            }

        }

        /** 
         * purge_hosting_runcloud
         * 
         * This method attempts to utilize the purge the 
         * runcloud caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_hosting_runcloud( ) : void {

            // RunCloud - this will only work if the RunCloud Hub is installed
            if( class_exists( 'RunCloud_Hub' ) ) {

                // if the site is a multisite
                if( is_multisite( ) ) {
                    
                    // purge all sites caches
                    @RunCloud_Hub::purge_cache_all_sites( );
                
                // we're not
                } else {

                    // purge the sites caches
                    @RunCloud_Hub::purge_cache_all();
                }

                // log the purge
                KPCPC::write_log( "\t\tRunCloud Cache" );

            }

        }

    }

}
