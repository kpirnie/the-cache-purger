<?php
/** 
 * Common Functionality
 * 
 * Setup the common functionality for the plugin
 * 
 * @since 7.4
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
        
    // check the PHP version, and deny if lower than 7.4
    if ( version_compare( PHP_VERSION, '7.4', '<=' ) ) {

        // it is, so throw and error message and exit
        wp_die( __( '<h1>PHP To Low</h1><p>Due to the nature of this plugin, it cannot be run on lower versions of PHP.</p><p>Please contact your hosting provider to upgrade your site to at least version 7.4.</p>', 'the-cache-purger' ), 
            __( 'Cannot Activate: PHP To Low', 'the-cache-purger' ),
            array(
                'back_link' => true,
            ) );

    }

    // check if we tried to network activate this plugin
    if( is_multisite( ) && $_network ) {

        // we did, so... throw an error message and exit
        wp_die( 
            __( '<h1>Cannot Network Activate</h1><p>Due to the nature of this plugin, it cannot be network activated.</p><p>Please go back, and activate inside your subsites.</p>', 'the-cache-purger' ), 
            __( 'Cannot Network Activate', 'the-cache-purger' ),
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

    // let's see if we're in CLI or not
    if( ! defined( 'WP_CLI' ) ) {

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
                wp_register_style( 'kpcp_css', plugins_url( '/assets/css/style.css?_=' . time( ), TCP_PATH . '/' . TCP_FILENAME ), null, null );

            } else {
                
                // we're not, queue up the minified assets
                wp_register_style( 'kpcp_css', plugins_url( '/assets/css/style.min.css?_=' . time( ), TCP_PATH . '/' . TCP_FILENAME ), null, null );

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
                        <p><?php _e( "<p>The cache purge has initialized.</p><p>The majority is run in the background, so please wait around 2 minutes for it to complete.</p>", 'the-cache-purger' ); ?></p>
                    </div>
                    <?php

                }, PHP_INT_MAX );

            }

            // get the querystring for purging the log
            $_do_log_purge = filter_var( ( isset( $_GET['the_log_purge'] ) ) ? sanitize_text_field( $_GET['the_log_purge'] ) : false, FILTER_VALIDATE_BOOLEAN );

            /// make sure we are actually purging the log
            if( $_do_log_purge ) {

                // get the logs path
                $_l_path = ABSPATH . 'wp-content/purge.log';

                // unfortunately we cannot utilize wordpress's built-in file methods, but let's clear the log
                file_put_contents( $_l_path, '', LOCK_EX );

            }

        }, PHP_INT_MAX );

        // hook into the wordpress initialization
        add_action( 'init', function( ) : void {

            // get our options
            $_opts = KPCPC::get_options( );

            // set if it's allowed 
            $_allowed = filter_var( ( $_opts -> cron_schedule_allowed ) ?? false, FILTER_VALIDATE_BOOLEAN );
            $_l_allowed = filter_var( ( $_opts -> should_log ) ?? false, FILTER_VALIDATE_BOOLEAN );
            $_lp_allowed = filter_var( ( $_opts -> cron_log_purge_allowed ) ?? false, FILTER_VALIDATE_BOOLEAN );

            // if it is
            if( $_allowed ) {

                // setup our action and create the job for it
                add_action( 'kpcpc_the_purge', function( ) : void {
            
                    // run the purge!
                    $_cp = new KP_Cache_Purge( );

                    // purge
                    $_cp -> kp_do_purge( );

                    // log the purge
                    KPCPC::write_log( "CRONJOB Cache Cleared" );

                    // clean it up
                    unset( $_cp );
                    
                } );

                // make sure we're only scheduling this once
                if( ! wp_next_scheduled( 'kpcpc_the_purge' ) ) {

                    // get our schedule options
                    $_bi_schedule = ( $_opts -> cron_schedule_builtin ) ?? 'hourly';

                    // schedule the event
                    wp_schedule_event( time( ), $_bi_schedule, 'kpcpc_the_purge' );  
                
                }

            }

            // if the log is enabled and if the log purging is allowed, and allowed to be on a schedule
            if( $_l_allowed && $_lp_allowed ) {

                // setup the action to be performed
                add_action( 'kpcpc_the_log_purge', function( ) : void {

                    // get the logs path
                    $_l_path = ABSPATH . 'wp-content/purge.log';

                    // unfortunately we cannot utilize wordpress's built-in file methods, but let's clear the log
                    file_put_contents( $_l_path, '', LOCK_EX );

                } );

                // make sure we're only scheduling this once
                if( ! wp_next_scheduled( 'kpcpc_the_log_purge' ) ) {

                    // get our schedule options
                    $_bi_schedule = ( $_opts -> cron_log_purge_schedule ) ?? 'weekly';

                    // schedule the event
                    wp_schedule_event( time( ), $_bi_schedule, 'kpcpc_the_log_purge' );  
                
                }

            }

            // see if we're purging
            $_is_purging = filter_var( ( get_transient( 'is_doing_cache_purge' ) ) ?? false, FILTER_VALIDATE_BOOLEAN );

            // if we are purging
            if( $_is_purging ) {

                // create a hook for the clearing to occurr in 
                add_action( 'kptcp_long_purge', function( ) : void {

                    // fire up the cache purge
                    $_cp = new KP_Cache_Purge( );

                    // run the long runner
                    $_cp -> kp_do_long_purge( );

                    // clean up
                    unset( $_cp );

                } );

                // check if the long purge task already exists
                if( ! wp_next_scheduled( 'kptcp_long_purge' ) ){

                    // schedule it to run once
                    wp_schedule_single_event( time( ) + 5, 'kptcp_long_purge' );

                    // now spawn the task to run
                    spawn_cron( );

                }

            }

        }, PHP_INT_MAX );
        
        // fire up the processor class here.  Inside it are the proper hooks where the purging will take place
        $_processor = new KP_Cache_Purge_Processor( );

        // run the processing
        $_processor -> process( );

        // clean up
        unset( $_processor );

    // we've hit the CLI, let's make sure we're only processing based on that
    } else {

        // just fire up the main CLI class
        new KP_Cache_Purge_CLI( );

    }

}
