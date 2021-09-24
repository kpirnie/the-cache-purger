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
        


    }

}
