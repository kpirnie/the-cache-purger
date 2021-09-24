<?php
/** 
 * Cache Purger Processor
 * 
 * This file does all the processing for the purges
 * 
 * @since 7.3
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
     * @since 7.3
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package The Cache Purger
     * 
     * @property object $options The options for the purging
     * @property object $actions The actions to be purged in
     * 
    */
    class KP_Cache_Purge_Processor {

        // hold our internal options property
        private object $options;

        // hold our internal actions property
        private object $actions;

        // fire us up
        public function __construct( ) {

            // set the options
            $this -> options = KPCPC::get_options( );

            // set the actions 
            $this -> actions = KPCPC::get_actions( );

        }

        // clean us up --- probably not necessary, but whatever...
        public function __destruct( ) { 

            // release our properties
            unset( $this -> options, $this -> actions );

        }

        /** 
         * process
         * 
         * Public method attempting to process the purging
         * 
         * @since 7.3
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

                /** SETUP OUR OPTIONS */

                // on menu
                $_on_menu = filter_var( ( $this -> options -> on_menu ) ?? false, FILTER_VALIDATE_BOOLEAN );

                // on post
                $_on_post = filter_var( ( $this -> options -> on_post ) ?? false, FILTER_VALIDATE_BOOLEAN );

                // on post exclude
                $_on_post_exclude = ( $this -> options -> on_post_exclude ) ?? array( );

                // on page
                $_on_page = filter_var( ( $this -> options -> on_page ) ?? false, FILTER_VALIDATE_BOOLEAN );

                // on page exclude
                $_on_page_exclude = ( $this -> options -> on_page_exclude ) ?? array( );

                // on cpt
                $_on_cpt = filter_var( ( $this -> options -> on_cpt ) ?? false, FILTER_VALIDATE_BOOLEAN );

                // on cpt exclude
                $_on_cpt_exclude = ( $this -> options -> on_cpt_exclude ) ?? array( );

                // on taxonomy
                $_on_taxonomy = filter_var( ( $this -> options -> on_taxonomy ) ?? false, FILTER_VALIDATE_BOOLEAN );

                // on category
                $_on_category = filter_var( ( $this -> options -> on_category ) ?? false, FILTER_VALIDATE_BOOLEAN );

                // on widget
                $_on_widget = filter_var( ( $this -> options -> on_widget ) ?? false, FILTER_VALIDATE_BOOLEAN );

                // on woo
                $_on_woo = filter_var( ( $this -> options -> on_woo ) ?? false, FILTER_VALIDATE_BOOLEAN );

                // on form
                $_on_form = filter_var( ( $this -> options -> on_form ) ?? false, FILTER_VALIDATE_BOOLEAN );

                // on form exclude
                $_on_form_exclude = ( $this -> options -> on_form_exclude ) ?? array( );

                // on acf
                $_on_acf = filter_var( ( $this -> options -> on_acf ) ?? false, FILTER_VALIDATE_BOOLEAN );

                // on acf exclude
                $_on_acf_exclude = ( $this -> options -> on_acf_exclude ) ?? array( );

                

            }

            echo '<textarea style="width:100%" rows="40">';
            var_dump( $this -> options );
            echo '</textarea>';
            exit;

        }




    }

}
