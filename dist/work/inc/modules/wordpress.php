<?php
/** 
 * WORDPRESS module
 * 
 * This file contains the wordpress purge methods
 * 
 * @since 7.4
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
     * @since 7.4
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package The Cache Purger
     *
    */
    trait WORDPRESS {

        /** 
         * purge_wordpress_caches
         * 
         * This method attempts to utilize the purge methods 
         * builtin to Wordpress
         * 
         * @since 7.4
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

            // get our WPDB global
            global $wpdb;

            // try to clear Wordpress's built-in object cache
            wp_cache_flush( );

            // log the purge
            KPCPC::write_log( "\t\tWP Cache" );

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
                KPCPC::write_log( "\t\tWP Object Cache" );
            }

            // delete the transients
            $wpdb -> query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE '_transient_%'" );

            // log the purge
            KPCPC::write_log( "\t\tWP Transient Cache" );

            // probably overkill, but let's fire off the rest of the builtin cache flushing mechanisms
            global $wp_object_cache;

            // try to flush the object cache
            $wp_object_cache -> flush( 0 );

            // log the purge
            KPCPC::write_log( "\t\tWP Object 2 Cache" );

            // throw a hook here
            do_action( 'tcp_post_wp_purge' );

        }

    }

}
