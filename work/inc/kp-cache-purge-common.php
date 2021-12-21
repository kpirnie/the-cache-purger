<?php
/** 
 * Cache Purger Common
 * 
 * This file contains the plugins common functionality
 * 
 * @since 7.3
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
     * @since 7.3
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package The Cache Purger
     *  
    */
    class KP_Cache_Purge_Common {

        /** 
         * get_options
         * 
         * Public method pull to the options
         * 
         * @since 7.3
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
         * @since 7.3
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
         * @since 7.3
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
         * @since 7.3
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

                // our query for all public posts
                $_qry = new WP_Query( array( 'post_type' => $_post_types, 'posts_per_page' => -1, 'post_status' => 'publish' ) );

                // fire up the recordset
                $_rs = $_qry -> get_posts( );

                // make sure we have a recordset
                if( $_rs ) {

                    // loop and and populate the URL array with the posts permalinks
                    foreach( $_rs as $_post ) {

                        // add it to the return array
                        $_ret[] = get_permalink( $_post -> ID );

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
         * @since 7.3
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
                'post' => array( 'save_post', 'trashed_post' ),
                'page' => array( 'save_post', 'trashed_post' ),
                'cpt' => array( 'save_post', 'trashed_post' ),
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
         * @since 7.3
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
                $_message = '[' . current_time( 'mysql' ) . ']: ' . $_msg . PHP_EOL;

                // unfortunately we cannot use wp's builtin filesystem hanlders for this
                // the put_contents method only writes/overwrites contents, and does not append
                // we need this to append the content

                // append the message to the purge log file
                file_put_contents( $_path, $_message, FILE_APPEND | LOCK_EX );
                
            }
            
        }

        /** 
         * arr_or_empty
         * 
         * Public method to return either an array or an empty array
         * 
         * @since 7.3
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
