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

                // hold out settings id
                $_cp_settings_id = 'kpcp_settings';

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
                    'footer_credit' => null
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
                        'fields' => $this -> kpcp_docs( ),
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

                // add a button to the admin bar for purging manually


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

            // return the array of fields
            return array(
                
                // purge on post
                array(
                    'id' => 'kpcp_purge_on_post',
                    'type' => 'switcher',
                    'title' => __( 'Purge on Post Save?' ),
                    'desc' => __( 'This will attempt to purge all caches for every post update or save.' ),
                    'default' => false,
                ),

                // purge on page
                array(
                    'id' => 'kpcp_purge_on_page',
                    'type' => 'switcher',
                    'title' => __( 'Purge on Page Save?' ),
                    'desc' => __( 'This will attempt to purge all caches for every page update or save.' ),
                    'default' => false,
                ),

                // purge on CPT


                // purge on taxonomy


                // purge on category


                // purge on GF Form


                // purge on ACF


            );

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
         * @return array Returns an array representing the documentation fields
         * 
        */
        private function kpcp_docs( ) : array {

            return array(
                array(
                    'type' => 'content',
                    'content' => '<h1>THE DOCS</h1>',
                ),
            );

        }

    }

}
