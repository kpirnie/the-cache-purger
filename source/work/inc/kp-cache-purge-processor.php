<?php
/** 
 * Cache Purger Processor
 * 
 * This file does all the processing for the purges
 * 
 * @since 7.4
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package The Cache Purger
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// check if this class already exists
if( ! class_exists( 'KP_Cache_Purge_Processor' ) ) {

    /** 
     * Class KP_Cache_Purge_Processor
     * 
     * Class for processing the purges
     * 
     * @since 7.4
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package The Cache Purger
     * 
     * @property object $options The options for the purging
     * @property array $actions The actions to be purged in
     * 
    */
    class KP_Cache_Purge_Processor {

        // hold our internal options property
        private $options;

        // hold our internal actions property
        private $actions;

        // fire us up
        public function __construct( ) {

            // throw an action here
            do_action( 'tcp_pre_purge' );

            // set the options
            $this -> options = KPCPC::get_options( );

            // set the actions 
            $this -> actions = KPCPC::get_actions( );

        }

        // clean us up --- probably not necessary, but whatever...
        public function __destruct( ) { 

            // release our properties
            unset( $this -> options, $this -> actions );

            // throw an action here
            do_action( 'tcp_pre_purge' );

        }

        /** 
         * process
         * 
         * Public method attempting to process the purging
         * 
         * @since 7.4
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        public function process( ) : void {
            
            // make sure we have the actions before we do anything further
            if( $this -> actions ) {

                // on menu
                $_on_menu = filter_var( ( $this -> options -> on_menu ) ?? false, FILTER_VALIDATE_BOOLEAN );

                // on post
                $_on_post = filter_var( ( $this -> options -> on_post ) ?? false, FILTER_VALIDATE_BOOLEAN );

                // on post exclude
                $_on_post_exclude = KPCPC::arr_or_empty( ( $this -> options -> on_post_exclude ) ?? null );

                // on page
                $_on_page = filter_var( ( $this -> options -> on_page ) ?? false, FILTER_VALIDATE_BOOLEAN );

                // on page exclude
                $_on_page_exclude = KPCPC::arr_or_empty( ( $this -> options -> on_page_exclude ) ?? null  );

                // on cpt
                $_on_cpt = filter_var( ( $this -> options -> on_cpt ) ?? false, FILTER_VALIDATE_BOOLEAN );

                // on cpt exclude
                $_on_cpt_exclude = KPCPC::arr_or_empty( ( $this -> options -> on_cpt_exclude ) ?? null  );

                // on taxonomy
                $_on_taxonomy = filter_var( ( $this -> options -> on_taxonomy ) ?? false, FILTER_VALIDATE_BOOLEAN );

                // on category
                $_on_category = filter_var( ( $this -> options -> on_category ) ?? false, FILTER_VALIDATE_BOOLEAN );

                // on widget
                $_on_widget = filter_var( ( $this -> options -> on_widget ) ?? false, FILTER_VALIDATE_BOOLEAN );

                // on customizer
                $_on_customizer = filter_var( ( $this -> options -> on_customizer ) ?? false, FILTER_VALIDATE_BOOLEAN );

                // on woo
                $_on_woo = filter_var( ( $this -> options -> on_woo ) ?? false, FILTER_VALIDATE_BOOLEAN );

                // on form
                $_on_form = filter_var( ( $this -> options -> on_form ) ?? false, FILTER_VALIDATE_BOOLEAN );

                // on form exclude
                $_on_form_exclude = KPCPC::arr_or_empty( ( $this -> options -> on_form_exclude ) ?? null );

                // on acf
                $_on_acf = filter_var( ( $this -> options -> on_acf ) ?? false, FILTER_VALIDATE_BOOLEAN );

                // on acf exclude
                $_on_acf_exclude = KPCPC::arr_or_empty( ( $this -> options -> on_acf_exclude ) ?? null );

                // loop over the actions objects
                foreach( $this -> actions as $_cat => $_actions ) {


                    // if we need to process on menu
                    if( ( $_cat == 'menu' ) && $_on_menu ) {

                        // process the actions to purge on
                        $this -> purge_on_action( 'menu', $_actions );

                    }

                    // if we need to process on post
                    if( ( $_cat == 'post' ) && $_on_post ) {

                        // exclusions: $_on_post_exclude
                        $this -> purge_on_action( 'post', $_actions, $_on_post_exclude );

                    }

                    // if we need to process on page
                    if( ( $_cat == 'page' ) && $_on_page ) {

                        // exclusions: $_on_page_exclude
                        $this -> purge_on_action( 'page', $_actions, $_on_page_exclude );

                    }

                    // if we need to process on cpt
                    if( ( $_cat == 'cpt' ) && $_on_cpt ) {

                        // exclusions: $_on_cpt_exclude
                        $this -> purge_on_action( 'cpt', $_actions, $_on_cpt_exclude );

                    }

                    // if we need to process on tax
                    if( ( $_cat == 'tax' ) && $_on_taxonomy ) {

                        // purge on taxonomies
                        $this -> purge_on_action( 'tax', $_actions );

                    }

                    // if we need to process on cat
                    if( ( $_cat == 'cat' ) && $_on_category ) {

                        // purge on categories
                        $this -> purge_on_action( 'cat', $_actions );

                    }

                    // if we need to process on widget
                    if( ( $_cat == 'widget' ) && $_on_widget ) {

                        // purge on widgets
                        $this -> purge_on_action( 'widget', $_actions );

                    }

                    // if we need to process on customizer
                    if( ( $_cat == 'customizer' ) && $_on_customizer ) {

                        // purge on customizer
                        $this -> purge_on_action( 'customizer', $_actions );

                    }

                    // if we need to process on gf
                    if( ( $_cat == 'gf' ) && $_on_form ) {

                        // exclusions: $_on_form_exclude
                        $this -> purge_on_action( 'gf', $_actions, $_on_form_exclude );

                    }

                    // if we need to process on acf
                    if( ( $_cat == 'acf' ) && $_on_acf ) {

                        // exclusions: $_on_acf_exclude
                        $this -> purge_on_action( 'acf', $_actions, $_on_acf_exclude );

                    }

                    // we need to process on settings
                    if( $_cat == 'settings' ) {

                        // purge on settings
                        $this -> purge_on_action( 'settings', $_actions );
                        
                    }

                    // we need to process on plugin
                    if( $_cat == 'plugin' ) {

                        // purge on plugin
                        $this -> purge_on_action( 'plugin', $_actions );
                        
                    }

                    // we need to process on updates
                    if( $_cat == 'updates' ) {

                        // purge on updates
                        $this -> purge_on_action( 'updates', $_actions );
                        
                    }

                }

            }

        }

        /** 
         * purge_on_action
         * 
         * Private method to purge on the action
         * and check for the exclusions
         * 
         * @since 7.4
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @param string $_type The category type we're running this on
         * @param array $_actions The WP actions we need to run it on
         * @param array $_the_exclusions The exclusions array
         * 
         * @return void This method does not return anything
         * 
        */
        private function purge_on_action( string $_type, array $_actions, array $_the_exclusions = array( ) ) : void {

            // make sure we actually have actions to run on
            if( $_actions ) {

                // loop over them
                foreach( $_actions as $_action ) {

                    // if we're processing gravity forms
                    if( $_type === 'gf' ) {

                        // if we're saving or adding a form
                        if( $_action == 'gform_after_save_form' ) {

                            // hook into the action
                            add_action( $_action, function( $_form, $_is_new ) use( $_the_exclusions ) : void {

                                // if the form ID is not in the exclusions
                                if( ! in_array( $_form['id'], $_the_exclusions ) ) {

                                    // fire up the purge class
                                    $_cp = new KP_Cache_Purge( );

                                    // purge the caches
                                    $_cp -> kp_do_purge( );

                                    // log the purge
                                    KPCPC::write_log( "\t" );
                                    KPCPC::write_log( "\tACTION PURGE" );
                                    KPCPC::write_log( "\t\tgf Cache Cleared on: gform_after_save_form" );
                                    KPCPC::write_log( "\t\tID: " . $_form['ID'] );

                                    // clean it up
                                    unset( $_cp );

                                }

                            }, PHP_INT_MAX, 2 );

                        }

                        // if we're trashing a form
                        if( $_action == 'gform_post_form_trashed' ) {

                            // hook into the action
                            add_action( $_action, function( $_id ) use( $_the_exclusions ) : void {

                                // if the form ID is not in the exclusions
                                if( ! in_array( $_id, $_the_exclusions ) ) {

                                    // fire up the purge class
                                    $_cp = new KP_Cache_Purge( );

                                    // purge the caches
                                    $_cp -> kp_do_purge( );

                                    // log the purge
                                    KPCPC::write_log( "\t" );
                                    KPCPC::write_log( "\tACTION PURGE" );
                                    KPCPC::write_log( "\t\tgf Cache Cleared on: gform_post_form_trashed" );
                                    KPCPC::write_log( "\t\tID: $_id" );

                                    // clean it up
                                    unset( $_cp );

                                }

                            }, PHP_INT_MAX, 1 );

                        }

                    // if we're processing ACF Field Groups
                    } elseif( $_type === 'acf' ) {

                        // hook into the action
                        add_action( $_action, function( $_fg ) use( $_action, $_the_exclusions ) : void {

                            // if the field group ID is not in the exclusions
                            if( ! in_array( $_fg['ID'], $_the_exclusions ) ) {

                                // fire up the purge class
                                $_cp = new KP_Cache_Purge( );

                                // purge the caches
                                $_cp -> kp_do_purge( );

                                // log the purge
                                KPCPC::write_log( "\t" );
                                KPCPC::write_log( "\tACTION PURGE" );
                                KPCPC::write_log( "\t\tacf Cache Cleared on: $_action" );
                                KPCPC::write_log( "\t\tID: " . $_fg['ID'] );

                                // clean it up
                                unset( $_cp );

                            }

                        }, PHP_INT_MAX, 1 );

                    // if we're processing a post or page
                    } elseif( in_array( $_type, array( 'post', 'page' ) ) ) {

                        // process the post
                        $this -> process_post( $_action, $_type, $_the_exclusions );
                        
                    } elseif( $_type === 'cpt' ) {

                        // get our CPTs
                        $_cpts = KPCPC::get_post_types_for_select( );

                        // make sure we have a return
                        if( $_cpts ) {

                            // loop them
                            foreach( $_cpts as $_cpt => $_name ) {

                                // check if we're not none and not excluded
                                if( $_cpt != 'none' && ! in_array( $_cpt, $_the_exclusions ) ) {

                                    // process the post
                                    $this -> process_post( $_action, $_cpt, $_the_exclusions );

                                }

                            }

                        }
                        
                    } elseif( $_type === 'menu' ) {

                        // process the purge
                        $this -> process_other( $_action, 'menu' );

                    } elseif( $_type === 'tax' ) {

                        // process the purge
                        $this -> process_other( $_action, 'tax' );

                    } elseif( $_type === 'cat' ) {

                        // process the purge
                        $this -> process_other( $_action, 'cat' );

                    } elseif( $_type === 'widget' ) {

                        // process the purge
                        $this -> process_other( $_action, 'widget' );

                    } elseif( $_type === 'customizer' ) {

                        // process the purge
                        $this -> process_other( $_action, 'customizer' );

                    } elseif( $_type === 'settings' ) {

                        // process the purge
                        $this -> process_other( $_action, 'settings' );

                    } elseif( $_type === 'plugin' ) {

                        // process the purge
                        $this -> process_other( $_action, 'plugin' );

                    } elseif( $_type === 'updates' ) {

                        // process the purge
                        $this -> process_other( $_action, 'updates' );

                    }

                }

            }

        }

        /** 
         * process_post
         * 
         * Private method for processing the post
         * 
         * @since 7.4
         * @access public
         * @static
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @param string $_type The category type we're running this on
         * @param array $_action The WP action we need to run it on
         * @param array $_the_exclusions The exclusions array
         * 
         * @return void This method does not return anything
         * 
        */
        private function process_post( string $_action, string $_type, array $_the_exclusions ) : void {

            // if the action is save
            if( $_action === 'save_post' ) {

                // hook into the actions in the highest priority
                add_action( 'save_post', function( $_id, $_post, $_update ) use( $_type, $_the_exclusions ) : void {

                    // if this is a revision
                    if( wp_is_post_revision( $_id ) ) {

                        // we dont need this to run, so just return
                        return;

                    }

                    // if this is an autosave
                    if( wp_is_post_autosave( $_id ) ) {

                        // we dont need this to run, so just return
                        return;

                    }

                    // if this is an autosave check 2
                    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {

                        // we dont need this to run, so just return
                        return;

                    }

                    // make sure this isn't fired on the rest request
                    if( defined( 'REST_REQUEST' ) && REST_REQUEST ) {

                        // we dont need this to run, so just return
                        return;
                    }

                    // make sure this isn't fired when saving a post as trash
                    if ( 'trash' === $_post -> post_status ) {

                        // we dont need this to run, so just return
                        return;

                    }

                    // if the posts ID is in the exclusions
                    if( in_array( $_id, $_the_exclusions ) ) {

                        // we don't need this to run, so just return
                        return;

                    // otherwise, go for it
                    } else {

                        // check our post type against the type
                        if( $_post -> post_type == $_type ) {

                            // fire up the purge class
                            $_cp = new KP_Cache_Purge( );

                            // purge the caches
                            $_cp -> kp_do_purge( );

                            // log the purge
                            KPCPC::write_log( "\t" );
                            KPCPC::write_log( "\tACTION PURGE" );
                            KPCPC::write_log( "\t\t$_type Cache Cleared on: save_post" );
                            KPCPC::write_log( "\t\tID: $_id" );

                            // clean it up
                            unset( $_cp );  

                        }
                        
                    }

                }, PHP_INT_MAX, 3 );

            // otherwise it's trashed
            } else {

                // hook into the actions in the highest priority
                add_action( 'trashed_post', function( $_id ) use( $_type, $_the_exclusions ) : void {

                    // if the posts ID is in the exclusions
                    if( in_array( $_id, $_the_exclusions ) ) {

                        // we don't need this to run, so just return
                        return;

                    // otherwise, go for it
                    } else {

                        // get the post type
                        $_post_type = get_post_type( $_id );

                        // check the current post type against the type
                        if( $_post_type == $_type ) {

                            // fire up the purge class
                            $_cp = new KP_Cache_Purge( );

                            // purge the caches
                            $_cp -> kp_do_purge( );

                            // log the purge
                            KPCPC::write_log( "\t" );
                            KPCPC::write_log( "\tACTION PURGE" );
                            KPCPC::write_log( "\t\t$_type Cache Cleared on: trashed_post" );
                            KPCPC::write_log( "\t\tID: $_id" );

                            // clean it up
                            unset( $_cp );  

                        }

                    }

                }, PHP_INT_MAX, 1 );

            }

        }

        /** 
         * process_other
         * 
         * Private method for processing other cache flushing necessities
         * 
         * @since 7.4
         * @access public
         * @static
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @param string $_action The WP actions we need to run it on
         * @param string $_type The type we are processing on
         * 
         * @return void This method does not return anything
         * 
        */
        private function process_other( string $_action, string $_type ) : void {

            // hook into the necessary action at the latest possible moments
            add_action( $_action, function( ) use( $_type, $_action ) : void {

                // fire up the purge class
                $_cp = new KP_Cache_Purge( );

                // purge the caches
                $_cp -> kp_do_purge( );

                // log the purge
                KPCPC::write_log( "\t" );
                KPCPC::write_log( "\tACTION PURGE" );
                KPCPC::write_log( "\t\t$_type Cache Cleared on: $_action" );

                // clean it up
                unset( $_cp );                  

            }, PHP_INT_MAX );

        }

        /** 
         * get_current_post
         * 
         * Private method for getting the current poost
         * 
         * @since 7.4
         * @access public
         * @static
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return WP_Post Returns a nullable WP_post object for the current post being browsed
         * 
        */
        private function get_current_post( ) : ?WP_Post {

            // let's get our post global
            global $post;

            // if it's empty, let's try to retrieve it based on the post querystring
            if ( empty( $post ) && is_array( $_GET ) ) {

                $post = get_post( ( $_GET['post'] ) ?? null );
            }

            // Optional: get an empty post object from the post_type
            if ( empty( $post ) && is_array( $_GET ) ) {

                // default standard class object
                $object = new stdClass( );
                
                // set the objects post type
                $object -> post_type = ( $_GET['post_type'] ) ?? null;
                
                // return a new WP Post object
                $post = WP_Post( $object );
            }

            // now... if our post object is still empty, let's try to figure it out via the URL
            if ( empty( $post ) ) {

                // grab our WP global
                global $wp;

                // try to get the current URL
                $current_url = home_url( add_query_arg( array( ), $wp -> request ) );

                // try to get the post ID, from the URL
                $current_post_id = url_to_postid( $current_url );

                // get the post
                $post = get_post( $current_post_id );
            }

            // one more check if post is still empty.  generally this means we're on the configured blog page
            if ( empty( $post ) ) {

                // get the post
                $post = get_post( ( int ) get_option( 'page_for_posts' ) );
            }

            // return the WP Post object
            return $post;

        }

    }

}
