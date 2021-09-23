<?php

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

/*
Plugin Name:  The Cache Purger
Plugin URI:   https://kevinpirnie.com
Description:  Plugin attemps to clear all plugin based and server based caches.
Version:      0.1.01
Author:       Kevin C Pirnie
License:      GPLv3
License URI:  https://www.gnu.org/licenses/gpl-3.0.html
*/

// setup the full page to this plugin
define( 'TCP_PATH', dirname( __FILE__ ) );

// setup the directory name
define( 'TCP_DIRNAME', basename( dirname( __FILE__ ) ) );

// setup the primary plugin file name
define( 'TCP_FILENAME', basename( __FILE__ ) );

// Include our "work"
require dirname( __FILE__ ) . '/work/common.php';

/*
// let's define the types of edits that should purge the cache
define( 'PURGE_ON', array(
    'post' => true, // posts
    'page' => true, // pages
    'cpt' => true, // custom post types
    'tax' => true, // taxonomies
    'cat' => true, // categories
    'gf' => true, // gravity forms
    'acf' => true, // advanced custom fields
) );

// END EDITTING

// include our class file
require WPMU_PLUGIN_DIR . '/emagine-cache-purger/emagine-cache-purger.php';

// fire up the purging class
$_purger = new KP_Cache_Purge( );

// if we are purging based on post saves
if( isset( PURGE_ON['post'] ) && PURGE_ON['post'] ) {

    // hook into the save_post action, and fire off in the highest priority
    add_action( 'save_post', function( $_id, $_post, $_update ) use ( $_purger ) : void {

        // make sure we are only saving a "post" post type
        if( $_post -> post_type === 'post' ) {

            // purge the cache
            $_purger -> kp_do_purge( );

        }

    }, PHP_INT_MAX, 3 );

}

// if we are purging based on page saves
if( isset( PURGE_ON['page'] ) && PURGE_ON['page'] ) {

    // hook into the save_post action, and fire off in the highest priority
    add_action( 'save_post', function( $_id, $_post, $_update ) use ( $_purger ) : void {

        // make sure we are only saving a "page" post type
        if( $_post -> post_type === 'page' ) {

            // purge the cache
            $_purger -> kp_do_purge( );

        }

    }, PHP_INT_MAX, 3 );

}

// if we are purging based on custom post type saves
if( isset( PURGE_ON['cpt'] ) && PURGE_ON['cpt'] ) {

    // get all public CPT's that are NOT builtin
    $_post_types = get_post_types( array( '_builtin ' => false, 'public' => true, 'publicly_queryable' => true, ), 'names', 'or' );

    // make sure there is a return
    if( $_post_types ) {

        // loop over them
        foreach( $_post_types as $_pt ) {

            // now hook into the save_post action for the CPT, and fire it off in the highest priority
            add_action( "save_post_$_cp", function( ) use ( $_purger ) : void {

                // PURGE!
                $_purger -> kp_do_purge( );

            }, PHP_INT_MAX );

        }

    }

}

// if we are purging based on taxonomy saves
if( isset( PURGE_ON['tax'] ) && PURGE_ON['tax'] ) {

    // hook into the saved_term action, and fire it off in the highest priority
    add_action( 'saved_term', function( ) use( $_purger ) : void {

        // PURGE
        $_purger -> kp_do_purge( );

    }, PHP_INT_MAX );

}

// if we are purging based on category saves
if( isset( PURGE_ON['cat'] ) && PURGE_ON['cat'] ) {

    // hook into the saved_category action, and fire it off in the highest priority
    add_action( 'saved_category', function( ) use( $_purger ) : void {

        // PURGE
        $_purger -> kp_do_purge( );

    }, PHP_INT_MAX );

}

// if we are purging based on gravity forms saves
if( isset( PURGE_ON['gf'] ) && PURGE_ON['gf'] ) {

    // make sure gravity forms is installed and activated
    if( class_exists( 'GFAPI' ) ) {

        // hook into the gform_after_save_form action, and fire it off in the highest priority
        add_action( 'gform_after_save_form', function( ) use( $_purger ) : void {

            // PURGE
            $_purger -> kp_do_purge( );

        }, PHP_INT_MAX );

    }

}

// if we are purging based on advanced custom fields saves
if( isset( PURGE_ON['acf'] ) && PURGE_ON['acf'] ) {

    // make sure ACF is installed and activated
    if( class_exists('ACF') ) {

        // hook into the update_field_group action, and fire it off in the highest priority
        add_action( 'acf/update_field_group', function( ) use( $_purger ) : void {

            // PURGE
            $_purger -> kp_do_purge( );

        }, PHP_INT_MAX );

    }

}

// clean up the purging class
unset( $_purger );

*/