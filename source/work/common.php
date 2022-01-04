<?php
/** 
 * Common Functionality
 * 
 * Setup the common functionality for the plugin
 * 
 * @since 7.3
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Kevin's Framework
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// set the plugins path
$_pi_path = TCP_PATH . '/the-cache-purger.php';

// Plugin Activation
register_activation_hook( $_pi_path, function( ) : void {
        
    // check the PHP version, and deny if lower than 7.3
    if ( version_compare( PHP_VERSION, '7.3', '<=' ) ) {

        // it is, so throw and error message and exit
        wp_die( __( '<h1>PHP To Low</h1><p>Due to the nature of this plugin, it cannot be run on lower versions of PHP.</p><p>Please contact your hosting provider to upgrade your site to at least version 7.3.</p>', 'the-cache-purger' ), 
            __( 'Cannot Activate: PHP To Low', 'the-cache-purger' ),
            array(
                'back_link' => true,
            ) );

    }

} );

// Plugin De-Activation
register_deactivation_hook( $_pi_path, function( ) : void {

    // nothing to do here because we want to be able to keep settings on deactivate

} );

// let's make sure the plugin is activated
if( in_array( TCP_DIRNAME . '/' . TCP_FILENAME, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    // setup our autoload
    spl_autoload_register( function( $_cls )  : void {

        // reformat the class name to match the file name for inclusion
        $_class = strtolower( str_ireplace( '_', '-', $_cls ) );

        // pull in our classes based on the file path
        $_path = TCP_PATH . '/work/inc/' . $_class . '.php';

        // check if it's our field framework
        if( $_cls === 'KPTCP' ) {

            // setup the proper path
            $_path = TCP_PATH . '/vendor/custom-fields/classes/setup.class.php';

        }
        
        // if the file exists
        if( @is_readable( $_path ) ) {

            // include it once
            include $_path;
        }

    } );

    // set us up a class alias for the common class
    class_alias( 'KP_Cache_Purge_Common', 'KPCPC' );

    // hook into the plugins loaded action
    add_action( 'plugins_loaded', function( ) : void {

        // initialize the field framework
        KPTCP::init( );

    }, PHP_INT_MAX );
    
    // hook into the custom fields loaded
    add_action( 'kptcp_loaded', function( ) : void {

        // fire up the admin class
        $_cp_admin = new KP_Cache_Purge_Admin( );

        // do it!
        $_cp_admin -> kpcp_admin( );

        // clean it up
        unset( $_cp_admin );

    }, PHP_INT_MAX );

    // hack in some styling
    add_action( 'admin_enqueue_scripts', function( ) : void {

        // check if we're in debug more or not
        if( defined( 'WP_DEBUG' ) && WP_DEBUG ) {

            // we are, so queue up our unminified assets
            wp_register_style( 'kpcp_css', plugins_url( '/assets/css/style.css', TCP_PATH . '/' . TCP_FILENAME ), null, null );

        } else {

            // we're not, queue up the minified assets
            wp_register_style( 'kpcp_css', plugins_url( '/assets/css/style.min.css', TCP_PATH . '/' . TCP_FILENAME ), null, null );

        }

        // enqueue it
        wp_enqueue_style( 'kpcp_css' );
        
    }, PHP_INT_MAX );

    // hook into the admin_init
    add_action( 'admin_init', function( ) : void {

        // get the querystring for the purge
        $_do_purge = filter_var( ( isset( $_GET['the_purge'] ) ) ? sanitize_text_field( $_GET['the_purge'] ) : false, FILTER_VALIDATE_BOOLEAN );
      
        // if it's true
        if( $_do_purge ) {

            // setup the cache purger
            $_cp = new KP_Cache_Purge( );

            // purge
            $_cp -> kp_do_purge( );

            // log the purge
            KPCPC::write_log( "Manual Cache Cleared" );

            // clean it up
            unset( $_cp );

            // show an admin message
            add_action( 'admin_notices', function( ) :void {

                ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php _e( "The cache purge has finished.", 'the-cache-purger' ); ?></p>
                </div>
                <?php

            }, PHP_INT_MAX );

        }

    }, PHP_INT_MAX );

    // fire up the processor class here.  Inside it are the proper hooks where the purging will take place
    $_processor = new KP_Cache_Purge_Processor( );

    // run the processing
    $_processor -> process( );

    // clean up
    unset( $_processor );

}
