<?php
/** 
 * Cache Purger
 * 
 * This file contains cache purging methods
 * 
 * @since 7.4
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package The Cache Purger
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// check if this class already exists
if( ! class_exists( 'KP_Cache_Purge' ) ) {

    // include our modules to attach to our class
    foreach( new DirectoryIterator( TCP_PATH . '/work/inc/modules' ) as $_fi ) {
        
        // if the file is a dot file, skip it
        if( $_fi -> isDot( ) ) continue;
        
        // if the file is the index file, skip it
        if( $_fi -> getFilename( ) == 'index.php' ) continue;
        
        // include the file
        include TCP_PATH . '/work/inc/modules/' . $_fi -> getFilename( );

    }
    
    /** 
     * Class KP_Cache_Purge
     * 
     * Class for attempting to purge all caches
     * 
     * @since 7.4
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package The Cache Purger
     * 
     * @property int $site_id Holds the current site ID if the site is a multisite
     * 
    */
    class KP_Cache_Purge {
        Use HOSTING, PLUGIN, WORDPRESS, PHP, PAGESPEED,
        NGINX, FILE, MEMORY, API;

        // internal class properties
        private $site_id;

        /** We're going to use this to populate our class properties */
        public function __construct( ) {

            // get the current site ID
            $this -> site_id = get_current_blog_id( );

            // log
            KPCPC::write_log( "------------------------------------" );
            KPCPC::write_log( "STARTING THE PURGE" );
            KPCPC::write_log( "------------------------------------" );

        }

        // clean us up --- probably not necessary, but whatever...
        public function __destruct( ) { 

            // release our properties
            unset( $this -> site_id );

            // log
            KPCPC::write_log( "------------------------------------" );
            KPCPC::write_log( "ENDING THE PURGE" );
            KPCPC::write_log( "------------------------------------\n" );
        }

        /** 
         * kp_do_purge
         * 
         * Public method attempting to purge the sites caches
         * 
         * @since 7.4
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        public function kp_do_purge( ) : void {

            // let's take care of the hosting caches first
            $this -> purge_hosting_caches( );

            // now we'll try plugin purges
            $this -> purge_plugin_caches( );

            // now we'll try Wordpress's internal cache purges
            $this -> purge_wordpress_caches( );

            // now we'll try to purge php based caches
            $this -> purge_php_caches( );

            // now we'll try to purge pagespeed mod caches
            $this -> purge_pagespeed_caches( );

            // now we'll try to purge nginx caches
            $this -> purge_nginx_caches( );

            // let's attempt to clear out file based caches
            $this -> purge_file_caches( );

            // let's attempt to clear out memory/ram based caches
            $this -> purge_memory_caches( );

            // let's attempt to purge the api and server based caches
            $this -> purge_remote_apiserver_caches( );

        }

    }

}
