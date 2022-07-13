<?php
/** 
 * PLUGIN module
 * 
 * This file contains the plugin purge methods
 * 
 * @since 7.4
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package The Cache Purger
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// check if this trait already exists
if( ! trait_exists( 'PLUGIN' ) ) {

    /**
     * Trait PLUGIN
     *
     * This trait contains the plugin purge methods
     *
     * @since 7.4
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package The Cache Purger
     *
    */
    trait PLUGIN {

        // hold the internal list of plugins we have methods for
        protected $_plugin_caches = array(
            'purge_plugin_cloudflare',
            'purge_plugin_sucuri',
            'purge_plugin_siteground',
            'purge_plugin_nginxhelper',
            'purge_plugin_lightspeed',
            'purge_plugin_cachify',
            'purge_plugin_autoptimize',
            'purge_plugin_fastvelocity',
            'purge_plugin_wprocket',
            'purge_plugin_swift',
            'purge_plugin_comet',
            'purge_plugin_hummingbird',
            'purge_plugin_wpfastest',
            'purge_plugin_wpfastest2',
            'purge_plugin_wpsupercache',
            'purge_plugin_w3totalcache',
            'purge_plugin_hypercache',
            'purge_plugin_wpotimize',
            'purge_plugin_wpoptimize2',
            'purge_plugin_cacheenabler',
            'purge_plugin_elementor',
            'purge_plugin_divi',
            'purge_plugin_wprestcache',
            'purge_plugin_nitropack',
        );

        /** 
         * purge_plugin_caches
         * 
         * This method attempts to utilize the purge methods 
         * of the most common caching plugins
         * 
         * @since 7.4
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        private function purge_plugin_caches( ) : void {

            // throw a hook here
            do_action( 'tcp_pre_plugin_purge' );

            // log it
            KPCPC::write_log( "\tPLUGIN PURGE" );

            // loop over the slug array
            foreach( $this -> _plugin_caches as $_plugin ) {

                // fire up the method to do the purge
                $this -> { $_plugin }( );

            }
            
            // release the array
            unset( $this -> _plugin_caches );

            // throw a hook here
            do_action( 'tcp_post_plugin_purge' );

        }

        /** 
         * purge_plugin_cloudflare
         * 
         * This method attempts to utilize the purge methods
         * of Cloudflare plugin to purge it's caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_plugin_cloudflare( ) : void {

            // Cloudflare - even though it's not a host
            if ( class_exists( '\CF\WordPress\Hooks' ) ) {

                // Initiliaze Hooks class which contains WordPress hook functions from Cloudflare plugin.
                $_cf_hooks = new \CF\WordPress\Hooks( );
                
                // If we have an instantiated class.
                if ( $_cf_hooks ) {
                
                    // Purge all cache.
                    $_cf_hooks -> purgeCacheEverything( );
                
                }

                // clean up
                unset( $_cf_hooks );

                // log the purge
                KPCPC::write_log( "\t\tCloudflare Cache" );
            }

        }

        /** 
         * purge_plugin_sucuri
         * 
         * This method attempts to utilize the purge methods
         * of Sucuri plugin to purge it's caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_plugin_sucuri( ) : void {

            // Sucuri - even though it's not a host, we'll need to rely on the sucuri plugin being installed
            if( class_exists( 'SucuriScanFirewall' ) ) {

                // get the sucuri api key
                $_key = SucuriScanFirewall::getKey( );

                // fireoff the cache clearing ajax method
                SucuriScanFirewall::clearCache( $_key );

                // log the purge
                KPCPC::write_log( "\t\tSucuri Cache" );

            }

        }

        /** 
         * purge_plugin_siteground
         * 
         * This method attempts to utilize the purge methods
         * of Siteground plugin to purge it's caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_plugin_siteground( ) : void {

            // SG Optimizer.
            if ( class_exists( 'SiteGround_Optimizer\Supercacher\Supercacher' ) ) {

                // clear siteground cache
                SiteGround_Optimizer\Supercacher\Supercacher::purge_cache( );

                // log the purge
                KPCPC::write_log( "\t\tSiteGround Cache" );

            }

        }

        /** 
         * purge_plugin_nginxhelper
         * 
         * This method attempts to utilize the purge methods
         * of nGinx Helper plugin to purge it's caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_plugin_nginxhelper( ) : void {

            // Nginx helper Plugin (Gridpane and others)
            if ( class_exists( 'Nginx_Helper' ) ) {

                // clear nginx helper cache
                do_action( 'rt_nginx_helper_purge_all' );

                // log the purge
                KPCPC::write_log( "\t\tNginx Helper Cache" );
            }
            
        }

        /** 
         * purge_plugin_lightspeed
         * 
         * This method attempts to utilize the purge methods
         * of Lightspeed plugin to purge it's caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_plugin_lightspeed( ) : void {

            // LiteSpeed Cache.
            if ( class_exists( 'LiteSpeed_Cache_Purge' ) ) {

                // litespeed
                LiteSpeed_Cache_Purge::all( );

                // just in case
                do_action( 'litespeed_purge_all' );

                // log the purge
                KPCPC::write_log( "\t\tLiteSpeed Cache" );
            }

        }

        /** 
         * purge_plugin_cachify
         * 
         * This method attempts to utilize the purge methods
         * of Cachify plugin to purge it's caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_plugin_cachify( ) : void {

            // Clear Cachify Cache
            if ( has_action('cachify_flush_cache') ) {

                // clear cachify
                do_action( 'cachify_flush_cache' );

                // log the purge
                KPCPC::write_log( "\t\tCachify Cache" );
            }

        }

        /** 
         * purge_plugin_autoptimize
         * 
         * This method attempts to utilize the purge methods
         * of Autoptimize plugin to purge it's caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_plugin_autoptimize( ) : void {

            // Autoptimize
            if( class_exists( 'autoptimizeCache' ) ) {

                // autoptimize
                autoptimizeCache::clearall_actionless( );

                // try this too
                autoptimizeCache::clearall( );

                // log the purge
                KPCPC::write_log( "\t\tAutoptimize Cache" );
            }

        }

        /** 
         * purge_plugin_fastvelocity
         * 
         * This method attempts to utilize the purge methods
         * of Fast Velocity plugin to purge it's caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_plugin_fastvelocity( ) : void {

            // Fast Velocity Minify
            if( function_exists( 'fvm_purge_all' ) ) {

                // fmv purge
                fvm_purge_all( );

                // log the purge
                KPCPC::write_log( "\t\tFast Velocity Minify Cache" );
            }

        }

        /** 
         * purge_plugin_wprocket
         * 
         * This method attempts to utilize the purge methods
         * of WP Rocket plugin to purge it's caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_plugin_wprocket( ) : void {

            // WPRocket
            if( function_exists( 'rocket_clean_domain' ) ) {

                // wp rocker cache
                rocket_clean_domain( );

                // log the purge
                KPCPC::write_log( "\t\tWPRocket Cache" );

            }

        }

        /** 
         * purge_plugin_swift
         * 
         * This method attempts to utilize the purge methods
         * of Swift plugin to purge it's caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_plugin_swift( ) : void {
            
            // Swift Performance
            if( class_exists( 'Swift_Performance_Cache' ) ) {

                // swift cache
                Swift_Performance_Cache::clear_all_cache( );

                // log the purge
                KPCPC::write_log( "\t\tSwift Performance Cache" );

            }

        }

        /** 
         * purge_plugin_comet
         * 
         * This method attempts to utilize the purge methods
         * of Comet Cache plugin to purge it's caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_plugin_comet( ) : void {

            // Comet Cache.
            if ( class_exists( 'comet_cache' ) ) {

                // clear it
                comet_cache::clear( );

                // log the purge
                KPCPC::write_log( "\t\tComet Cache" );
            }            

        }

        /** 
         * purge_plugin_hummingbird
         * 
         * This method attempts to utilize the purge methods
         * of Hummingbird Cache plugin to purge it's caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_plugin_hummingbird( ) : void {

            // Hummingbird.
            if ( class_exists( 'Hummingbird\Core\Filesystem' ) ) {

                // I would use Hummingbird\WP_Hummingbird::flush_cache( true, false ) instead, but it's disabling the page cache option in Hummingbird settings.
                Hummingbird\Core\Filesystem::instance( ) -> clean_up( );

                // just in case
                do_action( 'wphb_clear_page_cache' );

                // log the purge
                KPCPC::write_log( "\t\tHummingbird Cache" );
            }

        }

        /** 
         * purge_plugin_wpfastest
         * 
         * This method attempts to utilize the purge methods
         * of WP Fastest Cache plugin to purge it's caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_plugin_wpfastest( ) : void {

            // WP Fastest Cache
            if( class_exists( 'WpFastestCache' ) ) {
            
                // fire up the class
                $wpfc = new WpFastestCache( );
                
                // purge the cache
                $wpfc -> deleteCache( );

                // log the purge
                KPCPC::write_log( "\t\tWP Fastest Cache" );
            }

        }

        /** 
         * purge_plugin_wpfastest2
         * 
         * This method attempts to utilize the purge methods
         * of WP Fastest Cache (newer) plugin to purge it's caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_plugin_wpfastest2( ) : void {

            // WP Fastest Cache 2
            if ( isset( $GLOBALS['wp_fastest_cache'] ) && method_exists( $GLOBALS['wp_fastest_cache'], 'deleteCache' ) ) {
            
                // delete the caches    
                $GLOBALS['wp_fastest_cache'] -> deleteCache( );

                // log the purge
                KPCPC::write_log( "\t\tWP Fastest 2 Cache" );
            
            }

        }

        /** 
         * purge_plugin_wpsupercache
         * 
         * This method attempts to utilize the purge methods
         * of WP Super Cache plugin to purge it's caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_plugin_wpsupercache( ) : void {

            // WP Super Cache
            if( function_exists( 'wp_cache_clear_cache' ) ) {

                // check if we're multisite
                if( is_multisite( ) ) {

                    // we are so utilize the cache clearing for it
                    wp_cache_clear_cache( $this -> site_id );
                
                } else {
                    
                    // we're not
                    wp_cache_clear_cache( );
                
                }

                // log the purge
                KPCPC::write_log( "\t\tWP Super Cache" );
            }

        }

        /** 
         * purge_plugin_w3totalcache
         * 
         * This method attempts to utilize the purge methods
         * of W3 Total Cache plugin to purge it's caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_plugin_w3totalcache( ) : void {

            // W3 Total Cache
            if( function_exists( 'w3tc_flush_all' ) ) {

                // flush
                w3tc_flush_all( );

                // just in case
                do_action( 'w3tc_flush_posts' );

                // log the purge
                KPCPC::write_log( "\t\tW3 Total Cache" );
            }

        }

        /** 
         * purge_plugin_hypercache
         * 
         * This method attempts to utilize the purge methods
         * of Hyper Cache plugin to purge it's caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_plugin_hypercache( ) : void {

            // Hyper Cache
            if( class_exists( 'HyperCache' ) ) {
            
                // fire it up
                $hypercache = new HyperCache( );
                
                // clean the cache
                $hypercache -> clean( );

                // log the purge
                KPCPC::write_log( "\t\tHyper Cache" );
            }

        }

        /** 
         * purge_plugin_wpoptimize
         * 
         * This method attempts to utilize the purge methods
         * of WP Optimize plugin to purge it's caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_plugin_wpotimize( ) : void {

            // WP Optimize
            if( function_exists( 'wpo_cache_flush' ) ) {

                // flush the cache
                wpo_cache_flush( );

                // log the purge
                KPCPC::write_log( "\t\tWP Optimize Cache" );

            }

            // WP-Optimize
            if ( class_exists( 'WP_Optimize' ) && defined( 'WPO_PLUGIN_MAIN_PATH' ) ) {
                
                // check for the page cache
                if( is_callable( array( 'WP_Optimize', 'get_page_cache' ) ) && is_callable( array( WP_Optimize( ) -> get_page_cache( ), 'purge' ) ) ) {
                    
                    // purge
                    WP_Optimize( ) -> get_page_cache( ) -> purge( );
                }

                // log the purge
                KPCPC::write_log( "\t\tWP Optimize (try 2) Cache" );

            }

        }

        /** 
         * purge_plugin_wpoptimize2
         * 
         * This method attempts to utilize the purge methods
         * of WP Optimize (newer version) plugin to purge it's caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_plugin_wpoptimize2( ) : void {

            // WP-Optimize 2
            if ( class_exists( 'WP_Optimize_Cache_Commands' ) ) {

                // fir eup the class
                $_wpo_cc = new WP_Optimize_Cache_Commands( );

                // purge the caches
                $_wpo_cc -> purge_page_cache( );

                // clean up
                unset( $_wpo_cc );

                // log the purge
                KPCPC::write_log( "\t\tWP Optimize 2 Cache" );
            }

            // WP-Optimize minification files have a different cache.
            if ( class_exists( 'WP_Optimize_Minify_Cache_Functions' ) ) {

                // purge the caches
                WP_Optimize_Minify_Cache_Functions::purge( );

                // log the purge
                KPCPC::write_log( "\t\tWP Optimize Minify Cache" );

            }

        }

        /** 
         * purge_plugin_cacheenabler
         * 
         * This method attempts to utilize the purge methods
         * of Cache Enabler plugin to purge it's caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_plugin_cacheenabler( ) {

            // Cache Enabler
            if( class_exists( 'Cache_Enabler' ) ) {
                
                // clear it all out
                Cache_Enabler::clear_total_cache( );

                // just in case
                do_action( 'ce_clear_cache' );

                // log the purge
                KPCPC::write_log( "\t\tCache Enabler Cache" );

            }

        }

        /** 
         * purge_plugin_elementor
         * 
         * This method attempts to utilize the purge methods
         * of Elementor plugin to purge it's caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_plugin_elementor( ) : void {

            // Elementor
            if( did_action( 'elementor/loaded' ) ) {

                // Automatically purge and regenerate the Elementor CSS cache
                \Elementor\Plugin::instance( ) -> files_manager -> clear_cache( );

                // log the purge
                KPCPC::write_log( "\t\tElementor Cache" );

            }

        }

        /** 
         * purge_plugin_divi
         * 
         * This method attempts to utilize the purge methods
         * of Divi plugin to purge it's caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_plugin_divi( ) : void {

            // Divi
            if( defined( 'ET_CORE_CACHE_DIR' ) ) {

                // clear the Divi caches
                ET_Core_PageResource::remove_static_resources( 'all', 'all', true );

                // clear the ET cache folder as well
                $_et_cache = ET_CORE_CACHE_DIR;

                // let's utilize wordpress's filesystem global
                global $wp_filesystem;

                // if we do not have the global yet
                if( empty( $wp_filesystem ) ) {

                    // require the file
                    require_once ABSPATH . '/wp-admin/includes/file.php';

                    // initialize the wordpress filesystem
                    WP_Filesystem( );

                }

                // clear the files from the cache path.  This should take care of the rest
                if( @is_readable ( $_et_cache ) ) {

                    // get a list of the files/folders in the cache path
                    $_files = glob( $_et_cache . '*' );

                    // loop over them
                    foreach( $_files as $_file ) {

                        // if the location is readable
                        if( @is_readable( $_file ) ) {

                            // if it's a directory
                            if( $wp_filesystem -> is_dir( $_file ) ) {

                                // try to delete it recursively
                                $wp_filesystem -> delete( $_file, true, 'd' );

                                // for my own OCDness, let's then recreate the path
                                $wp_filesystem -> mkdir( $_file );

                            // otherwise it's a file
                            } else {

                                // try to delete it
                                $wp_filesystem -> delete( $_file, false, 'f' );

                            }

                        }

                    }

                }

                // log the purge
                KPCPC::write_log( "\t\tDivi Cache & Divi File Cache" );

            }

        }

        /** 
         * purge_plugin_wprestcache
         * 
         * This method attempts to utilize the purge methods
         * of WP Rest Cache plugin to purge it's caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_plugin_wprestcache( ) : void {

            // WP REST Cache
            if( class_exists( 'WP_Rest_Cache_Plugin\\Includes\\Caching\\Caching' ) ) {

                // fire up the database global
                global $wpdb;

                // get the table names
                $_cache_tbl = ( defined( 'WP_Rest_Cache_Plugin\\Includes\\Caching\\Caching::TABLE_CACHES' ) ) ? WP_Rest_Cache_Plugin\Includes\Caching\Caching::TABLE_CACHES : 'wrc_caches';
                $_cache_rel_tbl = ( defined( 'WP_Rest_Cache_Plugin\\Includes\\Caching\\Caching::TABLE_RELATIONS' ) ) ? WP_Rest_Cache_Plugin\Includes\Caching\Caching::TABLE_RELATIONS : 'wrc_relations';

                // now append the table prefix
                $_cache_tbl = sprintf( '%s%s', $wpdb -> prefix, $_cache_tbl );
                $_cache_rel_tbl = sprintf( '%s%s', $wpdb -> prefix, $_cache_rel_tbl );

                // truncate the relationship table
                $wpdb->query( "TRUNCATE `{$_cache_rel_tbl}`;" );

                // truncate the cache table
                $wpdb->query( "TRUNCATE `{$_cache_tbl}`;" );

                // log the purge
                KPCPC::write_log( "\t\tWP REST Cache" );

            }

        }

        /** 
         * purge_plugin_nitropack
         * 
         * This method attempts to utilize the purge methods
         * of nitropack plugin to purge it's caches
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_plugin_nitropack( ) : void {

            // NitroPack Cache
            if( class_exists( 'NitroPack\\SDK\\NitroPack' ) ) {

                // get the nitro site config
                $_sc = nitropack_get_site_config( );

                // fire it up and try to clear the proxy cache
                if ( $_sc && null !== $_nitro = get_nitropack_sdk( $_sc["siteId"], $_sc["siteSecret"] ) ) {
                    
                    // try to clear the proxy caches    
                    $_nitro -> purgeProxyCache( );
                
                }

                // purge the nitro local 
                nitropack_sdk_purge_local( );

                // delete the nitro backlog
                nitropack_sdk_delete_backlog( );

                // one more final try to purge nitro cache
                nitropack_sdk_purge( NULL, NULL, '' );

                // log the purge
                KPCPC::write_log( "\t\tNitroPack  Cache" );

            }

        }

    }

}
