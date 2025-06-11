<?php
/** 
 * WORDPRESS module
 * 
 * This file contains the wordpress purge methods
 * 
 * @since 8.1
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package The Cache Purger
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// check if this trait already exists
if( ! trait_exists( 'WORDPRESS' ) ) {

    /**
     * Trait WORDPRESS
     *
     * This trait contains the wordpress purge methods
     *
     * @since 8.1
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package The Cache Purger
     *
    */
    trait WORDPRESS {

        // setup the wordpress cache modules to be purged
        protected $_wordpress_caches = array(
            'purge_wp_object',
            'purge_wp_options',
            'purge_wp_transients',
            'purge_wp_global_object',
        );

        /** 
         * purge_wordpress_caches
         * 
         * This method attempts to utilize the purge methods 
         * builtin to Wordpress
         * 
         * @since 8.1
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        private function purge_wordpress_caches( ) : void {

            // throw a hook here
            do_action( 'tcp_pre_wp_purge' );

            // log it
            KPCPC::write_log( "\tWORDPRESS PURGE" );

            // loop over the slug array
            foreach( $this -> _wordpress_caches as $_wordpress ) {

                // fire up the method to do the purge
                $this -> { $_wordpress }( );

            }
            
            // release the array
            unset( $this -> _wordpress_caches );
            
            // throw a hook here
            do_action( 'tcp_post_wp_purge' );

        }

        /** 
         * purge_wp_object
         * 
         * This method attempts to utilize the purge the 
         * wordpress object caches
         * 
         * @since 8.1
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_wp_object( ) : void {

            // try to clear Wordpress's built-in object cache
            wp_cache_flush( );

            // log the purge
            KPCPC::write_log( "\t\tWP Object Cache" );

        }

        /** 
         * purge_wp_options
         * 
         * This method attempts to utilize the purge the 
         * wordpress option caches
         * 
         * @since 8.1
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_wp_options( ) : void {

            // now try to delete the wp object cache
            if( function_exists( 'wp_cache_delete' ) ) {

                // clear the plugin object cache
                wp_cache_delete( 'uninstall_plugins', 'options' );

                // clear the options object cache
                wp_cache_delete( 'alloptions', 'options' );

                // clear the rest of the object cache
                wp_cache_delete( 'notoptions', 'options' );

                // clear the rest of the object cache for the parent site in a multisite install
                wp_cache_delete( $this -> site_id . '-notoptions', 'site-options' );

                // clear the plugin object cache for the parent site in a multisite install
                wp_cache_delete( $this -> site_id . '-active_sitewide_plugins', 'site-options' );

                // log the purge
                KPCPC::write_log( "\t\tWP Option Cache" );
            }

        }

        /** 
         * purge_wp_transients
         * 
         * This method attempts to utilize the purge the 
         * wordpress transient caches
         * 
         * @since 8.1
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_wp_transients( ) : void {

            // get our WPDB global
            global $wpdb;
            
            // delete the transients
            $wpdb -> query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE '_transient_%'" );

            // log the purge
            KPCPC::write_log( "\t\tWP Transient Cache" );

        }

        /** 
         * purge_wp_global_object
         * 
         * This method attempts to utilize the purge the 
         * wordpress global object caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_wp_global_object( ) : void {

            // probably overkill, but let's fire off the rest of the builtin cache flushing mechanisms
            global $wp_object_cache;

            // try to flush the object cache
            $wp_object_cache -> flush( 0 );

            // log the purge
            KPCPC::write_log( "\t\tWP Global Object Cache" );

        }

    }

}
