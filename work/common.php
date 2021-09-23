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
        wp_die( __( '<h1>PHP To Low</h1><p>Due to the nature of this plugin, it cannot be run on lower versions of PHP.</p><p>Please contact your hosting provider to upgrade your site to at least version 7.3.</p>' ), 
            __( 'Cannot Activate: PHP To Low' ),
            array(
                'back_link' => true,
            ) );

    }

} );

// Plugin De-Activation
register_deactivation_hook( $_pi_path, function( ) : void {



} );

// let's make sure the plugin is activated
if( in_array( TCP_DIRNAME . '/' . TCP_FILENAME, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    // setup our autoload
    spl_autoload_register( function( $_cls )  : void {

        // reformat the class name to match the file name for inclusion
        $_class = strtolower( str_ireplace( '_', '-', $_cls ) );

        // pull in our classes based on the file path
        $_path = TCP_PATH . '/work/inc/' . $_class . '.php';

        // if the file exists
        if( is_readable( $_path ) ) {

            // include it once
            include $_path;
        }

    } );   

    // let's see if the main framework class exists
    if( ! class_exists( 'KPF' ) ) {

        // it does not, include the setup class
        require_once plugin_dir_path( TCP_PATH . '/' . TCP_FILENAME ) .'/vendor/custom-fields/classes/setup.class.php';
        
    }
    
    // hook into the custom fields loaded
    add_action( 'kpf_loaded', function( ) : void {

        // fire up the admin class
        $_cp_admin = new KP_Cache_Purge_Admin( );

        // do it!
        $_cp_admin -> kpcp_admin( );

        // clean it up
        unset( $_cp_admin );

    }, PHP_INT_MAX );

    // hack in some styling
    add_action( 'admin_enqueue_scripts', function( ) : void {

        // register the stylesheet
        wp_register_style( 'kpcp_css', plugins_url( '/assets/style.css', TCP_PATH . '/' . TCP_FILENAME ), null, null );

        // enqueue it
        wp_enqueue_style( 'kpcp_css' );
        
    }, PHP_INT_MAX );

}
