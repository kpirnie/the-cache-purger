<?php
/** 
 * Cache Purger CLI
 * 
 * This file contains the plugins CLI functionality
 * 
 * @since 7.4
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package The Cache Purger
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// check if this class already exists
if( ! class_exists( 'KP_Cache_Purge_CLI' ) ) {

    /** 
     * Class KP_Cache_Purge_CLI
     * 
     * Class for building out the CLI functionality for the plugin
     * 
     * @since 7.4
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package The Cache Purger
     *  
    */
    class KP_Cache_Purge_CLI {

        // fire up the class
        public function __construct( ) {

            // add the actual command
            WP_CLI::add_command( 'the_cache purge', function( ) : void {

                // we're going to need to includ some class files here
                include TCP_PATH . "/work/inc/kp-cache-purge-common.php";
                
                // set us up a class alias for the common class
                class_alias( 'KP_Cache_Purge_Common', 'KPCPC' );
                include TCP_PATH . "/work/inc/kp-cache-purge-processor.php";
                include TCP_PATH . "/work/inc/kp-cache-purge.php";

                // show a warning message that this is starting
                WP_CLI::warning( __( 'The Purge is Commencing!', 'the-cache-purger' ) );

                // run the purge!
                $_cp = new KP_Cache_Purge( );

                // purge
                $_cp -> kp_do_purge( );

                // log the purge
                KPCPC::write_log( "CLI Cache Cleared" );

                // clean it up
                unset( $_cp );

                // show a success message that it is all set
                WP_CLI::success( __( 'The Purge has finished!', 'the-cache-purger' ) );

            } );

        }

    }

}
