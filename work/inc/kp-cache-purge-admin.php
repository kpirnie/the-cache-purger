<?php
/** 
 * Cache Purger Admin
 * 
 * This file contains cache purging settings and admin pages
 * 
 * @since 7.3
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package The Cache Purger
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// check if this class already exists
if( ! class_exists( 'KP_Cache_Purge_Admin' ) ) {

    /** 
     * Class KP_Cache_Purge_Admin
     * 
     * Class for building out our settings and admin pages
     * 
     * @since 7.3
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package The Cache Purger
     *  
    */
    class KP_Cache_Purge_Admin {

        /** 
         * kpcp_admin
         * 
         * Public method pull together the settings and admin pages
         * 
         * @since 7.3
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        public function kpcp_admin( ) : void {

            // make sure our field framework actually exists
            if( class_exists( 'KPF' ) ) {

                // hold our settings id
                $_cp_settings_id = 'kpcp_settings';

                // hold the manual purge ID
                $_cp_manual_purge = 'kpcp_manual_purge';

                // create the main options page
                KPF::createOptions( $_cp_settings_id, array(
                    'menu_title' => __( 'The Cache Purge' ),
                    'menu_slug'  => 'kpcp_settings',
                    'menu_capability' => 'activate_plugins',
                    'menu_icon' => 'dashicons-layout',
                    'show_in_network' => false,
                    'show_reset_all' => false,
                    'show_reset_section' => false,  
                    'show_bar_menu' => false, 
                    'sticky_header' => false,  
                    'ajax_save' => false,           
                    'framework_title' => __( 'The Cache Purger <small>by Kevin C. Pirnie</small>' ),
                    'footer_credit' => __( '<a href="https://kevinpirnie.com/" target="_blank">Kevin C. Pirnie</a>' )
                ) );

                // Settings
                KPF::createSection( $_cp_settings_id, 
                    array(
                        'title'  => __( 'Settings' ),
                        'fields' => $this -> kpcp_settings( ),
                    )
                );

                // Documentation
                KPF::createSection( $_cp_settings_id, 
                    array(
                        'title'  => __( 'Documentation' ),
                        'fields' => array(
                            array(
                                'type' => 'content',
                                'content' => $this -> kpcp_docs( ),
                            )
                        ),
                    )
                );

                // Export/Import Settings
                KPF::createSection( $_cp_settings_id, 
                    array(
                        'title'  => __( 'Export/Import Settings' ),
                        'fields' => array(
                            array(
                                'type' => 'backup',
                            ),
                        ),
                    )
                );

                // on settings save, clear cache if we are configured to do so
                add_action( 'kpf_' . $_cp_settings_id . '_save_after', function( ) use( $_cp_settings_id ) : void {

                    // setup the cache purger
                    $_cp = new KP_Cache_Purge( );

                    // purge
                    $_cp -> kp_do_purge( );

                    // log the purge
                    KPCPC::write_log( "Settings Cache Cleared on: " . 'kpf_' . $_cp_settings_id . '_save_after' );

                    // clean it up
                    unset( $_cp );

                }, PHP_INT_MAX );

                // add a button to the admin bar for purging manually
                // for this we'll hook directly into the admin menu bar
                add_action( 'admin_bar_menu', function( $_admin_bar ) : void {

                    // set the arguments for this admin bar menu item
                    $_args = array (
                        'id' => 'book',
                        'title' => '<span class="ab-icon dashicons-layout"></span> ' . __( 'Master Cache Purge' ),
                        'href' => admin_url( 'admin.php?page=kpcp_settings&the_purge=true' ),
                        'meta' => array( 'title' => _( 'Click here to purge all of your caches.' ) ),
                    );
                
                    // add the node with the arguments above
                    $_admin_bar -> add_node( $_args );

                }, PHP_INT_MAX );

            }

        }

        /** 
         * kpcp_settings
         * 
         * Private method pull together the settings fields
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return array Returns an array representing the settings fields
         * 
        */
        private function kpcp_settings( ) : array {

            // hold the returnable array
            $_ret = array( );

            // hold the temp array
            $_tmp = array( );

            // return the array of fields
            $_ret = array(

                // log the purge actions
                array(
                    'id' => 'should_log',
                    'type' => 'switcher',
                    'title' => __( 'Log Purge Actions?' ),
                    'desc' => __( 'This will attempt to write a log of all purge actions performed.<br />The file location is: <code>' . ABSPATH . 'wp-content/purge.log</code>' ),
                    'default' => false,
                ),

                // purge on menu
                array(
                    'id' => 'on_menu',
                    'type' => 'switcher',
                    'title' => __( 'Purge on Menu Save/Delete?' ),
                    'desc' => __( 'This will attempt to purge all caches for every menu update, save, or delete.' ),
                    'default' => false,
                ),
                
                // purge on post
                array(
                    'id' => 'on_post',
                    'type' => 'switcher',
                    'title' => __( 'Purge on Post Save/Delete?' ),
                    'desc' => __( 'This will attempt to purge all caches for every post update, save, or delete.' ),
                    'default' => false,
                ),

                // post exclusions
                array(
                    'id' => 'on_post_exclude',
                    'type' => 'select',
                    'multiple' => true,
                    'title' => __( 'Excluded Posts' ),
                    'placeholder' => __( 'Please select the exclusions...' ),
                    'desc' => __( 'Posts to exclude from the purger.' ),
                    'options' => KPCPC::get_posts_for_select( 'posts' ),
                    'default' => 0,
                    'dependency' => array( 'on_post', '==', true ),
                ),

                // purge on page
                array(
                    'id' => 'on_page',
                    'type' => 'switcher',
                    'title' => __( 'Purge on Page Save/Delete?' ),
                    'desc' => __( 'This will attempt to purge all caches for every page update, save, or delete.' ),
                    'default' => false,
                ),

                // page exclusions
                array(
                    'id' => 'on_page_exclude',
                    'type' => 'select',
                    'multiple' => true,
                    'title' => __( 'Excluded Pages' ),
                    'placeholder' => __( 'Please select the exclusions...' ),
                    'desc' => __( 'Pages to exclude from the purger.' ),
                    'options' => KPCPC::get_posts_for_select( 'pages' ),
                    'default' => 0,
                    'dependency' => array( 'on_page', '==', true ),
                ),

                // purge on CPT
                array(
                    'id' => 'on_cpt',
                    'type' => 'switcher',
                    'title' => __( 'Purge on Custom Post Type Save/Delete?' ),
                    'desc' => __( 'This will attempt to purge all caches for every custom post type update, save, or delete.' ),
                    'default' => false,
                ),

                // cpt exclusions
                array(
                    'id' => 'on_cpt_exclude',
                    'type' => 'select',
                    'multiple' => true,
                    'title' => __( 'Excluded CPTs' ),
                    'placeholder' => __( 'Please select the exclusions...' ),
                    'desc' => __( 'CPTs to exclude from the purger.' ),
                    'options' => KPCPC::get_post_types_for_select( ),
                    'default' => 0,
                    'dependency' => array( 'on_cpt', '==', true ),
                ),

                // purge on taxonomy
                array(
                    'id' => 'on_taxonomy',
                    'type' => 'switcher',
                    'title' => __( 'Purge on Taxonomy/Term Save/Delete?' ),
                    'desc' => __( 'This will attempt to purge all caches for every taxonomy/term update, save, or delete.' ),
                    'default' => false,
                ),

                // purge on category
                array(
                    'id' => 'on_category',
                    'type' => 'switcher',
                    'title' => __( 'Purge on Category Save/Delete?' ),
                    'desc' => __( 'This will attempt to purge all caches for every category update, save, or delete.' ),
                    'default' => false,
                ),

                // purge on widget
                array(
                    'id' => 'on_widget',
                    'type' => 'switcher',
                    'title' => __( 'Purge on Widget Save/Delete?' ),
                    'desc' => __( 'This will attempt to purge all caches for every widget update, save, or delete.' ),
                    'default' => false,
                ),

                // purge on customizer
                array(
                    'id' => 'on_customizer',
                    'type' => 'switcher',
                    'title' => __( 'Purge on Customizer Save?' ),
                    'desc' => __( 'This will attempt to purge all caches for every customizer update or save.' ),
                    'default' => false,
                ),
                
            );

            // if gravity forms is installed and activated
            if( class_exists( 'GFAPI' ) ) {

                // purge on form field
                $_tmp[] = array(
                        'id' => 'on_form',
                        'type' => 'switcher',
                        'title' => __( 'Purge on Form Save/Delete?' ),
                        'desc' => __( 'This will attempt to purge all caches for every form update, save, or delete.' ),
                        'default' => false,
                    );

                // for exclusions
                $_tmp[] = array(
                    'id' => 'on_form_exclude',
                    'type' => 'select',
                    'multiple' => true,
                    'title' => __( 'Excluded Forms' ),
                    'placeholder' => __( 'Please select the exclusions...' ),
                    'desc' => __( 'Forms to exclude from the purger.' ),
                    'options' => $this -> get_our_forms( ),
                    'default' => 0,
                    'dependency' => array( 'on_form', '==', true ),
                );

            }

            // if ACF is installed and activated
            if( class_exists('ACF') ) {

                // purge on form field
                $_tmp[] = array(
                    'id' => 'on_acf',
                    'type' => 'switcher',
                    'title' => __( 'Purge on ACF Save/Delete?' ),
                    'desc' => __( 'This will attempt to purge all caches for every "advanced custom field" group update, save, or delete.' ),
                    'default' => false,
                );

                // for exclusions
                $_tmp[] = array(
                    'id' => 'on_acf_exclude',
                    'type' => 'select',
                    'multiple' => true,
                    'title' => __( 'Excluded Field Groups' ),
                    'placeholder' => __( 'Please select the exclusions...' ),
                    'desc' => __( 'Field Groups to exclude from the purger.' ),
                    'options' => $this -> get_our_field_groups( ),
                    'default' => 0,
                    'dependency' => array( 'on_acf', '==', true ),
                );

            }

            // return the merged arrays
            return array_merge( $_ret, $_tmp );

        }

        /** 
         * kpcp_docs
         * 
         * Private method pull together the documentation
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return string Returns the string content of the documentation file
         * 
        */
        private function kpcp_docs( ) : string {

            // hold the return
            $_ret = '';

            // setup the path
            $_path = TCP_PATH . "/work/doc.php";

            // if the file exists
            if( @is_readable( $_path ) ) {

                // start the output buffer
                ob_start( );
                
                // include the doc file
                include $_path;
                
                // include the documentation
                $_ret = ob_get_contents();
                
                // clean and end the output buffer
                ob_end_clean( );

            }

            // return it
            return $_ret;

        }


        /** 
         * get_forms
         * 
         * Private method pull all forms
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return array Returns an array representing the forms created for the site
         * 
        */
        private function get_our_forms( ) : array {

            // setup a returnable array
            $_ret = array( );

            // populate the NONE
            $_ret[0] = __( ' -- None -- ' );

            // get all forms
            $_forms = GFAPI::get_forms( );

            // if there are some
            if( $_forms ) {

                // get a count
                $_fCt = count( $_forms );

                // loop over them
                for( $_i = 0; $_i < $_fCt; ++$_i ) {

                    // setup the return array
                    $_ret[$_forms[$_i]['id']] = $_forms[$_i]['title'];

                }

            }

            // return
            return $_ret;

        }

        /** 
         * get_field_groups
         * 
         * Private method pull all ACF field groups
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return array Returns an array representing the field groups created for the site
         * 
        */
        private function get_our_field_groups( ) : array {

            // setup a returnable array
            $_ret = array( );

            // populate the NONE
            $_ret[0] = __( ' -- None -- ' );

            // get all field groups
            $_fgs = acf_get_field_groups( );

            // make sure we have a return
            if( $_fgs ) {

                // get a count
                $_fCt = count( $_fgs );

                // loop over them
                for( $_i = 0; $_i < $_fCt; ++$_i ) {

                    // add to the array
                    $_ret[$_fgs[$_i]['ID']] = $_fgs[$_i]['title'];

                }

            }

            // return
            return $_ret;

        }

    }

}
