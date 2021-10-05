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

// let's pull in the Carbon Fields Namespaces we're going to need
use Carbon_Fields\Container;
use Carbon_Fields\Field;

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


        // fire us up
        public function __construct( ) {

            // include the field datastore serializer
            include_once TCP_PATH . '/vendor/field-serializer.php';

        }

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

            // create the settings page
			$_main = Container::make( 'theme_options', __( 'The Cache Purge' ) )
                -> set_page_menu_title( __( 'The Cache Purge' ) )
                -> set_page_file( 'kpcp_settings' )
                -> set_icon( 'dashicons-layout' )
                -> set_datastore( new KPCP_Serialized_Datastore( ) )
                -> where( 'current_user_role', 'IN', array( 'contributor', 'author', 'editor', 'administrator', 'super-admin' ) )
                -> add_fields( $this -> kpcp_settings( ) );

            // create the documentation page
			Container::make( 'theme_options', __( 'The Cache Purge Documentation' ) )
                -> set_page_menu_title( __( 'Documentation' ) )
                -> set_page_file( 'kpcp_documentation' )
                -> set_page_parent( $_main )
                -> where( 'current_user_role', 'IN', array( 'contributor', 'author', 'editor', 'administrator', 'super-admin' ) )
                -> add_fields( array(
                    Field::make( 'html', 'kpcp_doc', __( '' ) )
                        -> set_html( $this -> kpcp_docs( ) ),
                ) );

            // on settings save, clear cache if we are configured to do so
            add_filter( 'carbon_fields_theme_options_container_saved', function( ) : void {

                // setup the cache purger
                $_cp = new KP_Cache_Purge( );

                // purge
                $_cp -> kp_do_purge( );

                // log the purge
                KPCPC::write_log( "Settings Cache Cleared on: " . 'kpf_' . $_cp_settings_id . '_save_after' );

                // clean it up
                unset( $_cp );
        
            } );

            // hook into the current screen and change the settings button
			add_action( 'current_screen', function( ) : void { 

                // get the current screen
				global $current_screen;

                // if we're on the settings page
				if( $current_screen -> base == 'toplevel_page_kpcp_settings' ) {

					// change the save button content
					add_action( 'admin_footer', function( ) : void {

						// utilize jquery to replace the text in the button
						echo '<script type="text/javascript">jQuery(".button-large").val("Update Your Settings");</script><style type="text/css">.kpja-half-field {width:50% !important; flex:none !important;}.kpja-third-field {width:33% !important; flex:none !important;}</style>';
					} );

				}

				// if we're on the documentation page
				if( $current_screen -> base == 'the-cache-purge_page_kpcp_documentation' ) {

					// remove the metabox
					add_action( 'admin_footer', function( ) : void {

						// utilize jquery to replace the text in the button
						echo '<script type="text/javascript">jQuery("#postbox-container-1").remove( );</script><style type="text/css">.columns-2 {margin-right:0px !important;}</style>';
					} );

				}

            }, PHP_INT_MAX );

            // add a button to the admin bar for purging manually


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

                // should log
                Field::make( 'checkbox', 'should_log', __( 'Log Purge Actions?' ) )
                    -> set_option_value( 'yes' )
                    -> help_text( __( 'This will attempt to write a log of all purge actions performed.<br />The file location is: <code>' . ABSPATH . 'wp-content/purge.log</code>' ) ),

                // on menu
                Field::make( 'checkbox', 'on_menu', __( 'Purge on Menu Save/Delete?' ) )
                    -> set_option_value( 'yes' )
                    -> help_text( __( 'This will attempt to purge all caches for every menu update, save, or delete.' ) ),

                // on post
                Field::make( 'checkbox', 'on_post', __( 'Purge on Post Save/Delete?' ) )
                    -> set_option_value( 'yes' )
                    -> help_text( __( 'This will attempt to purge all caches for every post update, save, or delete.' ) ),

                // post exclusions
                Field::make( 'multiselect', 'on_post_exclude', __( 'Excluded Posts' ) )
                    -> help_text( __( 'Posts to exclude from the purger.' ) )
                    -> set_options( KPCPC::get_posts_for_select( 'posts' ) )
                    -> set_default_value( 0 )
                    -> set_conditional_logic( array( array( 'field' => 'on_post', 'value' => true, ) ) ),

                // on page
                Field::make( 'checkbox', 'on_page', __( 'Purge on Page Save/Delete?' ) )
                    -> set_option_value( 'yes' )
                    -> help_text( __( 'This will attempt to purge all caches for every page update, save, or delete.' ) ),

                // page exclusions
                Field::make( 'multiselect', 'on_page_exclude', __( 'Excluded Pages' ) )
                    -> help_text( __( 'Page to exclude from the purger.' ) )
                    -> set_options( KPCPC::get_posts_for_select( 'pages' ) )
                    -> set_default_value( 0 )
                    -> set_conditional_logic( array( array( 'field' => 'on_page', 'value' => true, ) ) ),

                // on cpt
                Field::make( 'checkbox', 'on_cpt', __( 'Purge on Custom Post Type Save/Delete?' ) )
                    -> set_option_value( 'yes' )
                    -> help_text( __( 'This will attempt to purge all caches for every custom post type update, save, or delete.' ) ),

                // cpt exclusions
                Field::make( 'multiselect', 'on_cpt_exclude', __( 'Excluded CPTs' ) )
                    -> help_text( __( 'CPTs to exclude from the purger.' ) )
                    -> set_options( KPCPC::get_post_types_for_select( ) )
                    -> set_default_value( 'none' )
                    -> set_conditional_logic( array( array( 'field' => 'on_cpt', 'value' => true, ) ) ),

                // on taxonomy
                Field::make( 'checkbox', 'on_taxonomy', __( 'Purge on Taxonomy/Term Save/Delete?' ) )
                    -> set_option_value( 'yes' )
                    -> help_text( __( 'This will attempt to purge all caches for every taxonomy/term update, save, or delete.' ) ),
                  
                // on category
                Field::make( 'checkbox', 'on_category', __( 'Purge on Category Save/Delete?' ) )
                    -> set_option_value( 'yes' )
                    -> help_text( __( 'This will attempt to purge all caches for every category update, save, or delete.' ) ),
                    
                // on widget
                Field::make( 'checkbox', 'on_widget', __( 'Purge on Widget Save/Delete?' ) )
                    -> set_option_value( 'yes' )
                    -> help_text( __( 'This will attempt to purge all caches for every widget update, save, or delete.' ) ),
                    
                // on customizer
                Field::make( 'checkbox', 'on_customizer', __( 'Purge on Customizer Save/Delete?' ) )
                    -> set_option_value( 'yes' )
                    -> help_text( __( 'This will attempt to purge all caches for every customizer update, save, or delete.' ) ),
                    
                
            );

            // if gravity forms is installed and activated
            if( class_exists( 'GFAPI' ) ) {

                // add the checkbox field to the temp array
                $_tmp[] = Field::make( 'checkbox', 'on_form', __( 'Purge on Form Save/Delete?' ) )
                    -> set_option_value( 'yes' )
                    -> help_text( __( 'This will attempt to purge all caches for every form update, save, or delete.' ) );

                // now we can add the exclusions field to the temp array
                $_tmp[] = Field::make( 'multiselect', 'on_form_exclude', __( 'Excluded Forms' ) )
                    -> help_text( __( 'Forms to exclude from the purger.' ) )
                    -> set_options( $this -> get_our_forms( ) )
                    -> set_default_value( 0 )
                    -> set_conditional_logic( array( array( 'field' => 'on_form', 'value' => true, ) ) );

            }

            // if ACF is installed and activated
            if( class_exists('ACF') ) {

                // add the checkbox field to the temp array
                $_tmp[] = Field::make( 'checkbox', 'on_acf', __( 'Purge on ACF Save/Delete?' ) )
                    -> set_option_value( 'yes' )
                    -> help_text( __( 'This will attempt to purge all caches for every "advanced custom field" group update, save, or delete.' ) );

                // now we can add the exclusions field to the temp array
                $_tmp[] = Field::make( 'multiselect', 'on_acf_exclude', __( 'Excluded Field Groups' ) )
                    -> help_text( __( 'Field Groups to exclude from the purger.' ) )
                    -> set_options( $this -> get_our_field_groups( ) )
                    -> set_default_value( 0 )
                    -> set_conditional_logic( array( array( 'field' => 'on_acf', 'value' => true, ) ) );

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
