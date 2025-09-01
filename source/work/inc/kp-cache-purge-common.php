<?php
/** 
 * Cache Purger Common
 * 
 * This file contains the plugins common functionality
 * 
 * @since 8.1
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package The Cache Purger
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// check if this class already exists
if( ! class_exists( 'KP_Cache_Purge_Common' ) ) {

    /** 
     * Class KP_Cache_Purge_Common
     * 
     * Class for building out the common functionality for the plugin
     * 
     * @since 8.1
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package The Cache Purger
     *  
    */
    class KP_Cache_Purge_Common {

        /** 
         * initialize_plugin
         * 
         * Initialize the plugin
         * 
         * @since 8.1
         * @access public
         * @static
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void Returns nothing
         * 
        */
        public static function initialize_plugin( ) : void {

            // once the plugins are loaded
            add_action( 'plugins_loaded', function( ) {

                // initialize the field framework
                KPTCP::init( );

            }, PHP_INT_MAX );

            // hack in some styling
            add_action( 'admin_enqueue_scripts', function( ) : void {

                // we are, so queue up our unminified assets
                wp_register_style( 'kpcp_css', plugins_url( '/assets/css/style.css?_=' . time( ), TCP_PATH . '/' . TCP_FILENAME ), null, null );

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
                            <p><?php _e( "<p>The cache purge has initialized.</p><p>The majority is run in the background, so please wait around 2 minutes for it to complete.</p>" ); ?></p>
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

                // initialize the field framework
                KPTCP::init( );

                // get our options
                $_opts = KPCPC::get_options( );

                // get our wp cron info
                $_cron_info = wp_get_schedules( );

                // set if it's allowed 
                $_allowed = filter_var( ( $_opts -> cron_schedule_allowed ) ?? false, FILTER_VALIDATE_BOOLEAN );
                $_l_allowed = filter_var( ( $_opts -> should_log ) ?? false, FILTER_VALIDATE_BOOLEAN );
                $_lp_allowed = filter_var( ( $_opts -> cron_log_purge_allowed ) ?? false, FILTER_VALIDATE_BOOLEAN );

                // if it is
                if( $_allowed ) {

                    // setup our action and create the job for it
                    add_action( 'kpcpc_the_purge', [__CLASS__, 'do_the_actual_purge'] );

                    // make sure we're only scheduling this once
                    if( ! as_has_scheduled_action( 'kpcpc_the_purge' ) ) {

                        // throw a hook here
                        do_action( 'tcp_cron_cache_purge' );

                        // get our schedule options
                        $_bi_schedule = ( $_opts -> cron_schedule_builtin ) ?? 'hourly';

                        // schedule the event
                        as_schedule_recurring_action( time( ), $_cron_info[ $_bi_schedule ]['interval'], 'kpcpc_the_purge' );

                    }

                }

                // if the log is enabled and if the log purging is allowed, and allowed to be on a schedule
                if( $_l_allowed && $_lp_allowed ) {

                    // setup the action to be performed
                    add_action( 'kpcpc_the_log_purge', [__CLASS__, 'do_log_purge'] );

                    // make sure we're only scheduling this once
                    if( ! as_has_scheduled_action( 'kpcpc_the_log_purge' ) ) {

                        // throw a hook here
                        do_action( 'tcp_cron_log_purge' );

                        // get our schedule options
                        $_bi_schedule = ( $_opts -> cron_log_purge_schedule ) ?? 'weekly';

                        // schedule the event
                        as_schedule_recurring_action( time( ), $_cron_info[ $_bi_schedule ]['interval'], 'kpcpc_the_log_purge' );

                    }

                }

                // see if we're purging
                $_is_purging = filter_var( ( get_transient( 'is_doing_cache_purge' ) ) ?? false, FILTER_VALIDATE_BOOLEAN );

                // if we are purging
                if( $_is_purging ) {

                    // create a hook for the clearing to occurr in 
                    add_action( 'kptcp_long_purge', [__CLASS__, 'do_the_long_purge'] );

                    // check if the long purge task already exists
                    if( ! as_next_scheduled_action( 'kptcp_long_purge' ) ) {

                        // throw a hook here
                        do_action( 'tcp_long_cache_purge' );

                        // schedule it to run once as soon as possible
                        as_schedule_single_action( time( ) + 5, 'kptcp_long_purge' );

                    }

                }

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
            
            // we'll need a message in wp-admin for PHP 8 compatibility
            add_action( 'admin_notices', function( ) : void {

                // if the site is under PHP 8.1
                if ( version_compare( PHP_VERSION, '8.1', '<=' ) ) {

                    // show this notice
                    ?>
                    <div class="notice notice-info is-dismissible">
                        <p><?php _e( "<h3>PHP Upgrade Notice</h3><p>To maintain optimal security standards, this will be the final version that supports PHP versions lower than 8.1. Your site must be upgraded in order to update the plugin to future versions.</p><p>Please see here for up to date PHP version information: <a href='https://www.php.net/supported-versions.php' target='_blank'>https://www.php.net/supported-versions.php</a></p>" ); ?></p>
                    </div>
                <?php
                }
            }, PHP_INT_MAX );

            // fire up the processor class here.  Inside it are the proper hooks where the actual purging will take place
            $_processor = new KP_Cache_Purge_Processor( );

            // run the processing
            $_processor -> process( );

            // clean up
            unset( $_processor );

        }

        /** 
         * do_the_long_purge
         * 
         * Perform the long purging
         * 
         * @since 8.1
         * @access public
         * @static
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void Returns nothing
         * 
        */
        public static function do_the_long_purge( ) : void {

            // fire up the cache purge
            $_cp = new KP_Cache_Purge( );

            // run the long runner
            $_cp -> kp_do_long_purge( );

            // clean up
            unset( $_cp );

        }

        /** 
         * do_log_purge
         * 
         * Clean the log
         * 
         * @since 8.1
         * @access public
         * @static
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void Returns nothing
         * 
        */
        public static function do_log_purge( ) : void {

            // get the logs path
            $_l_path = ABSPATH . 'wp-content/purge.log';

            // unfortunately we cannot utilize wordpress's built-in file methods, but let's clear the log
            file_put_contents( $_l_path, '', LOCK_EX );

        }

        /** 
         * do_the_actual_purge
         * 
         * Perform the actual purging
         * 
         * @since 8.1
         * @access public
         * @static
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void Returns nothing
         * 
        */
        public static function do_the_actual_purge( ) : void {

            // run the purge!
            $_cp = new KP_Cache_Purge( );

            // purge
            $_cp -> kp_do_purge( );

            // log the purge
            KPCPC::write_log( "CRONJOB Cache Cleared" );

            // clean it up
            unset( $_cp );

        }


        /** 
         * get_options
         * 
         * Public method pull to the options
         * 
         * @since 8.1
         * @access public
         * @static
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return object Returns the nullable object for all options
         * 
        */
        public static function get_options( ) : ?object {

            // get the options
            $_option = get_option( 'kpcp_settings' );

            // try to convert the value to an array
            $_opts = maybe_unserialize( $_option );

            // return it or null
            return ( object ) $_opts;
        }

        /** 
         * get_post_types_for_select
         * 
         * Public method pull to gather all public post types for a select box
         * 
         * @since 8.1
         * @access public
         * @static
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return array Returns an array of posts types
         * 
        */
        public static function get_post_types_for_select( ) : array {

            // setup a return array
            $_ret = array( );

            // the first item needs to be NONE
            $_ret['none'] = __( ' -- None -- ' );

            // see if we've already got this in cache
            $_post_types = wp_cache_get( 'kpcp_post_types', 'kpcp_post_types' );

            // check if we're already cached
            if( $_post_types ) {

                // we are, so return the object
                return $_post_types;

            // we aren't cached yet
            } else {

                // get the inherent post types
                $_the_pts = get_post_types( array( '_builtin' => false, 'public' => true, ), 'names' );

                // check if we have any
                if( empty( $_the_pts ) ) {

                    // we don't yet because they aren't registered as of this point, we'll need to try to get them from the existing posts

                    // fire up the wpdb global
                    global $wpdb;

                    // run a query to get all post types
                    $_pts = $wpdb -> get_results( "SELECT DISTINCT post_type FROM $wpdb->posts WHERE post_status = 'publish' AND post_type NOT IN ( 'nav_menu_item', 'page', 'post', 'acf-field-group', 'attachment', 'custom_css', 'customize_changeset' ) ORDER BY post_type ASC;", ARRAY_A );

                    // make sure we have some
                    if( $_pts ) {

                        // loop over them
                        foreach( $_pts as $_pt ) {

                            // add the post type to the returnable array
                            $_ret[ $_pt['post_type'] ] = ucwords( __( str_replace( '_', ' ', $_pt['post_type'] ) ) );

                        }

                    }

                } else {

                    // loop over them
                    foreach( $_the_pts as $_pt ) {

                            // add the post type to the returnable array
                            $_ret[ $_pt ] = ucwords( __( str_replace( '_', ' ', $_pt ) ) );

                    }

                }

                // set the array to the cache for 1 hour
                wp_cache_add( 'kpcp_post_types', $_ret, 'kpcp_post_types', HOUR_IN_SECONDS );

            }

            // return the array
            return ( is_array( $_ret ) ) ? $_ret : array( );
            
        }

        /** 
         * get_posts_for_select
         * 
         * Public method pull to gather all posts for a select box
         * 
         * @since 8.1
         * @access public
         * @static
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @param string $_type The type of posts we are going to pull
         * 
         * @return array Returns an array of posts
         * 
        */
        public static function get_posts_for_select( string $_type = 'posts' ) : array {

            // setup a return array
            $_ret = array( );

            // the first item needs to be NONE
            $_ret[0] = __( ' -- None -- ' );

            // see if we've already got this in cache
            $_posts = wp_cache_get( "kpcp_posts_$_type", "kpcp_posts_$_type" );

            // we do
            if( $_posts ) {

                // return the merged arrays
                return array_merge( $_ret, $_posts );

            // we don't
            } else {

                // hold the query variable
                $_qry = null;

                // hold the recordset variable
                $_rs = null;

                // if it's posts we want
                if( $_type == 'posts' ) {

                    // query for our posts
                    $_qry = new WP_Query( array( 'posts_per_page' => -1, 'orderby' => 'menu_order', 'order' => 'ASC', 'post_status' => 'publish', 'post_type' => 'post' ) );
                    
                    // the recordset
                    $_rs = $_qry -> get_posts( );

                }

                // if it's pages we want
                if( $_type == 'pages' ) {

                    // query for our pages
                    $_qry = new WP_Query( array( 'posts_per_page' => -1, 'orderby' => 'menu_order', 'order' => 'ASC', 'post_status' => 'publish', 'post_type' => 'page' ) );
                    
                    // the recordset
                    $_rs = $_qry -> get_posts( );

                }

                // if it's CPTs we want
                if( $_type == 'cpts' ) {

                    // get our publicly queryable post types that are not builtin... ie... CPTs
                    $_pts = get_post_types( array( '_builtin' => false, 'public' => true, 'publicly_queryable' => true, ), 'names', 'and' );

                    // make sure this is not empty
                    if( ! empty( $_pts ) ) {

                        // query for our cpts
                        $_qry = new WP_Query( array( 'posts_per_page' => -1, 'orderby' => 'menu_order', 'order' => 'ASC', 'post_status' => 'publish', 'post_type' => $_pts ) );
                        
                        // the recordset
                        $_rs = $_qry -> get_posts( );

                    }

                    // clean up the post types
                    unset( $_pts );

                }

                // make sure there is something for our recordset
                if( $_rs ) {

                    // loop over the recordset
                    foreach( $_rs as $_post ) {

                        // add the id as an array index, and the title to the return array
                        $_ret[$_post -> ID] = __( $_post -> post_title );

                    }

                    // cache the return for 1 hour
                    wp_cache_add( "kpcp_posts_$_type", $_ret, "kpcp_posts_$_type", HOUR_IN_SECONDS );

                }

            }

            // return the array
            return $_ret;

        }
        
        /** 
         * get_urls
         * 
         * Public method pull to gather all public URLs
         * 
         * @since 8.1
         * @access public
         * @static
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return array Returns an array of URLs
         * 
        */
        public static function get_urls( ) : array {

            // see if we've already got this in cache
            $_urls = wp_cache_get( "kpcp_urls", "kpcp_urls" );

            // if we have it in our cache already
            if( $_urls ) {

                // return it
                return $_urls;

            // we don't    
            } else {

                // hold our return array
                $_ret = array( );

                // all we're interested in is publicly accessible posts/pages
                $_post_types = get_post_types( array( 'public' => true, ), 'names' );

                // setup some arguments
                $_args = array( 
                    'no_found_rows' => true, 
                    'update_post_meta_cache' => false, 
                    'update_post_term_cache' => false, 
                    'fields' => 'ids',
                    'post_type' => $_post_types, 
                    'posts_per_page' => -1, 
                    'post_status' => 'publish',
                );

                // our query for all public posts
                $_qry = new WP_Query( $_args );

                // fire up the recordset
                $_rs = $_qry -> get_posts( );

                // make sure we have a recordset
                if( $_rs ) {

                    // loop and and populate the URL array with the posts permalinks
                    foreach( $_rs as $_id ) {

                        // add it to the return array
                        $_ret[] = get_permalink( $_id );

                    }

                }

                // cache it for 1 hour
                wp_cache_add( "kpcp_urls", $_ret, "kpcp_urls", HOUR_IN_SECONDS );

                // return the array
                return $_ret;
                
            }

        }

        /** 
         * get_actions
         * 
         * Public method pull to gather all actions we need to hook into 
         * for each section the cache is configured to be purged for
         * 
         * @since 8.1
         * @access public
         * @static
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return array Returns an array of categorized action objects
         * 
        */
        public static function get_actions( ) : array {

            // return the array
            return array(
                'menu' => array( 'wp_update_nav_menu', 'wp_delete_nav_menu' ),
                'post' => array( 'save_post', 'edit_post', 'trashed_post' ),
                'page' => array( 'save_post', 'edit_post', 'trashed_post' ),
                'cpt' => array( 'save_post', 'edit_post', 'trashed_post' ),
                'tax' => array( 'saved_term', 'delete_term' ),
                'cat' => array( 'saved_category', 'delete_category' ),
                'widget' => array( 'wp_ajax_save-widget', 'wp_ajax_widgets-order', 'sidebar_admin_setup' ),
                'customizer' => array( 'customize_save_after' ),
                'gf' => array( 'gform_after_save_form', 'gform_post_form_trashed' ),
                'acf' => array( 'acf/update_field_group', 'acf/trash_field_group' ),
                'settings' => array( 'woocommerce_settings_saved', 'pre_set_transient_settings_errors' ), // no matter what, we'll purge on this
                'plugin' => array( 'activated_plugin', 'deactivated_plugin' ), // no matter what, we'll purge on this
                'updates' => array( 'upgrader_process_complete', '_core_updated_successfully' ), // no matter what, we'll purge on this
            );

        }

        /** 
         * write_log
         * 
         * Public method to write to a cache purge log
         * 
         * @since 8.1
         * @access public
         * @static
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        public static function write_log( string $_msg ) : void {

            // get our setting
            $_should_log = filter_var( ( KPCPC::get_options( ) -> should_log ) ?? false, FILTER_VALIDATE_BOOLEAN );

            // let's make sure we should be logging
            if( $_should_log ) {
                
                // set a path to hold the purge log
                $_path = ABSPATH . 'wp-content/purge.log';

                // I want to append a timestamp to the message
                $_message = '[' . current_time( 'mysql' ) . ']: ' . __( $_msg ) . PHP_EOL;

                // unfortunately we cannot use wp's builtin filesystem hanlders for this
                // the put_contents method only writes/overwrites contents, and does not append
                // we need this to append the content

                // append the message to the purge log file
                file_put_contents( $_path, $_message, FILE_APPEND | LOCK_EX );
                
            }
            
        }

        /** 
         * write_error
         * 
         * Public method to write to a exception log
         * 
         * @since 8.1
         * @access public
         * @static
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        public static function write_error( string $_msg ) : void {

            // set a path to hold the exception log
            $_path = ABSPATH . 'wp-content/purge-exceptions.log';

            // I want to append a timestamp to the message
            $_message = '[' . current_time( 'mysql' ) . ']: ' . __( $_msg ) . PHP_EOL;

            // unfortunately we cannot use wp's builtin filesystem hanlders for this
            // the put_contents method only writes/overwrites contents, and does not append
            // we need this to append the content

            // append the message to the purge log file
            file_put_contents( $_path, $_message, FILE_APPEND | LOCK_EX );

        }

        /** 
         * arr_or_empty
         * 
         * Public method to return either an array or an empty array
         * 
         * @since 8.1
         * @access public
         * @static
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @param var $_val The value to check
         * 
         * @return array Returns either the array or an empty one
         * 
        */
        public static function arr_or_empty( $_val ) : array {

            // return either the array or an empty array
            return ( is_array( $_val ) && ! empty( $_val ) ) ? $_val : array( );

        }

    }

}
