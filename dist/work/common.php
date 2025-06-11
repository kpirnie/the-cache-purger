<?php
/** 
 * Common Functionality
 * 
 * Setup the common functionality for the plugin
 * 
 * @since 8.1
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package The Cache Purger
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// set the plugins path
$_pi_path = TCP_PATH . '/the-cache-purger.php';

// Plugin Activation
register_activation_hook( $_pi_path, function( $_network ) : void {
        
    // check the PHP version, and deny if lower than 8.1
    if ( version_compare( PHP_VERSION, '8.1', '<=' ) ) {

        // it is, so throw and error message and exit
        wp_die( __( '<h1>PHP To Low</h1><p>Due to the nature of this plugin, it cannot be run on lower versions of PHP.</p><p>Please contact your hosting provider to upgrade your site to at least version 8.1.</p>' ), 
            __( 'Cannot Activate: PHP To Low' ),
            array(
                'back_link' => true,
            ) );

    }

    // check if we tried to network activate this plugin
    if( is_multisite( ) && $_network ) {

        // we did, so... throw an error message and exit
        wp_die( 
            __( '<h1>Cannot Network Activate</h1><p>Due to the nature of this plugin, it cannot be network activated.</p><p>Please go back, and activate inside your subsites.</p>' ), 
            __( 'Cannot Network Activate' ),
            array(
                'back_link' => true,
            ) 
        );
    }

} );

// Plugin De-Activation
register_deactivation_hook( $_pi_path, function( ) : void {

    // nothing to do here because we want to be able to keep settings on deactivate

} );

// let's make sure the plugin is activated
if( in_array( TCP_DIRNAME . '/' . TCP_FILENAME, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    // include our autoloader
    include TCP_PATH . '/vendor/autoload.php';

    // action scheduler not loading workaround
    if ( ! class_exists( 'ActionScheduler', false ) || ! ActionScheduler::is_initialized( ) ) {

        // require the class
        require_once( TCP_PATH . '/vendor/woocommerce/action-scheduler/classes/abstracts/ActionScheduler.php' );

        // no initialize the library
        ActionScheduler::init( TCP_PATH . '/vendor/woocommerce/action-scheduler/action-scheduler.php' );

    }

    // set us up a class alias for the common class
    class_alias( 'KP_Cache_Purge_Common', 'KPCPC' );

    // initialize the plugin, controls all hooks necessary for both settings and cache clearing
    KPCPC::initialize_plugin( );

}
