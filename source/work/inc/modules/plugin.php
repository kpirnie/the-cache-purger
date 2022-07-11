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

            // Cloudflare - even though it's not a host
            if ( class_exists( '\CF\WordPress\Hooks' ) ) {

                // Initiliaze Hooks class which contains WordPress hook functions from Cloudflare plugin.
                $_cf_hooks = new \CF\WordPress\Hooks( );
                
                // If we have an instantiated class.
                if ( $_cf_hooks ) {
                
                    // Purge all cache.
                    $_cf_hooks -> purgeCacheEverything( );
                
                }

                // log the purge
                KPCPC::write_log( "\t\tCloudflare Cache" );
            }

            // Sucuri - even though it's not a host, we'll need to rely on the sucuri plugin being installed
            if( class_exists( 'SucuriScanFirewall' ) ) {

                // get the sucuri api key
                $_key = SucuriScanFirewall::getKey( );

                // fireoff the cache clearing ajax method
                SucuriScanFirewall::clearCache( $_key );

                // log the purge
                KPCPC::write_log( "\t\tSucuri Cache" );

            }

            // SG Optimizer.
            if ( class_exists( 'SiteGround_Optimizer\Supercacher\Supercacher' ) ) {

                // clear siteground cache
                SiteGround_Optimizer\Supercacher\Supercacher::purge_cache( );

                // log the purge
                KPCPC::write_log( "\t\tSiteGround Cache" );

            }

            // Nginx helper Plugin (Gridpane and others)
            if ( class_exists( 'Nginx_Helper' ) ) {

                // clear nginx helper cache
                do_action( 'rt_nginx_helper_purge_all' );

                // log the purge
                KPCPC::write_log( "\t\tNginx Helper Cache" );
            }

            // LiteSpeed Cache.
            if ( class_exists( 'LiteSpeed_Cache_Purge' ) ) {

                // litespeed
                LiteSpeed_Cache_Purge::all( );

                // just in case
                do_action( 'litespeed_purge_all' );

                // log the purge
                KPCPC::write_log( "\t\tLiteSpeed Cache" );
            }

            // Clear Cachify Cache
            if ( has_action('cachify_flush_cache') ) {

                // clear cachify
                do_action( 'cachify_flush_cache' );

                // log the purge
                KPCPC::write_log( "\t\tCachify Cache" );
            }

            // Autoptimize
            if( class_exists( 'autoptimizeCache' ) ) {

                // autoptimize
                autoptimizeCache::clearall_actionless( );

                // try this too
                autoptimizeCache::clearall( );

                // log the purge
                KPCPC::write_log( "\t\tAutoptimize Cache" );
            }

            // Fast Velocity Minify
            if( function_exists( 'fvm_purge_all' ) ) {

                // fmv purge
                fvm_purge_all( );

                // log the purge
                KPCPC::write_log( "\t\tFast Velocity Minify Cache" );
            }

            // WPRocket
            if( function_exists( 'rocket_clean_domain' ) ) {

                // wp rocker cache
                rocket_clean_domain( );

                // log the purge
                KPCPC::write_log( "\t\tWPRocket Cache" );
            }

            // Swift Performance
            if( class_exists( 'Swift_Performance_Cache' ) ) {

                // swift cache
                Swift_Performance_Cache::clear_all_cache( );

                // log the purge
                KPCPC::write_log( "\t\tSwift Performance Cache" );

            }
    
            // Comet Cache.
            if ( class_exists( 'comet_cache' ) ) {
                comet_cache::clear( );

                // log the purge
                KPCPC::write_log( "\t\tComet Cache" );
            }

            // Hummingbird.
            if ( class_exists( 'Hummingbird\Core\Filesystem' ) ) {

                // I would use Hummingbird\WP_Hummingbird::flush_cache( true, false ) instead, but it's disabling the page cache option in Hummingbird settings.
                Hummingbird\Core\Filesystem::instance( ) -> clean_up( );

                // just in case
                do_action( 'wphb_clear_page_cache' );

                // log the purge
                KPCPC::write_log( "\t\tHummingbird Cache" );
            }

            // WP Fastest Cache
            if( class_exists( 'WpFastestCache' ) ) {
                $wpfc = new WpFastestCache( );
                $wpfc -> deleteCache( );

                // log the purge
                KPCPC::write_log( "\t\tWP Fastest Cache" );
            }
    
            // WP Fastest Cache 2
            if ( isset( $GLOBALS['wp_fastest_cache'] ) && method_exists( $GLOBALS['wp_fastest_cache'], 'deleteCache' ) ) {
                $GLOBALS['wp_fastest_cache'] -> deleteCache( );

                // log the purge
                KPCPC::write_log( "\t\tWP Fastest 2 Cache" );
            }

            // WP Super Cache
            if( function_exists( 'wp_cache_clear_cache' ) ) {

                // check if we're multisite
                if( is_multisite( ) ) {

                    // we are so utilize the cache clearing for it
                    wp_cache_clear_cache( $_site_id );
                } else {
                    
                    // we're not
                    wp_cache_clear_cache( );
                }

                // log the purge
                KPCPC::write_log( "\t\tWP Super Cache" );
            }

            // W3 Total Cache
            if( function_exists( 'w3tc_flush_all' ) ) {

                // flush
                w3tc_flush_all( );

                // just in case
                do_action( 'w3tc_flush_posts' );

                // log the purge
                KPCPC::write_log( "\t\tW3 Total Cache" );
            }

            // Hyper Cache
            if( class_exists( 'HyperCache' ) ) {
                $hypercache = new HyperCache( );
                $hypercache -> clean( );

                // log the purge
                KPCPC::write_log( "\t\tHyper Cache" );
            }

            // WP Optimize
            if( function_exists( 'wpo_cache_flush' ) ) {
                wpo_cache_flush( );

                // log the purge
                KPCPC::write_log( "\t\tWP Optimize Cache" );
            }

            // WP-Optimize
            if ( class_exists( 'WP_Optimize' ) && defined( 'WPO_PLUGIN_MAIN_PATH' ) ) {
                if( is_callable( array( 'WP_Optimize', 'get_page_cache' ) ) && is_callable( array( WP_Optimize( ) -> get_page_cache( ), 'purge' ) ) ) {
                    
                    // purge
                    WP_Optimize( ) -> get_page_cache( ) -> purge( );
                }

                // log the purge
                KPCPC::write_log( "\t\tWP Optimize (try 2) Cache" );
            }

            // WP-Optimize 2
            if ( class_exists( 'WP_Optimize_Cache_Commands' ) ) {

                // This function returns a response, so I'm assigning it to a variable to prevent unexpected output to the screen.
                $response = WP_Optimize_Cache_Commands::purge_page_cache( );

                // log the purge
                KPCPC::write_log( "\t\tWP Optimize 2 Cache" );
            }
    
            // WP-Optimize minification files have a different cache.
            if ( class_exists( 'WP_Optimize_Minify_Cache_Functions' ) ) {

                // This function returns a response, so I'm assigning it to a variable to prevent unexpected output to the screen.
                $response = WP_Optimize_Minify_Cache_Functions::purge( );

                // log the purge
                KPCPC::write_log( "\t\tWP Optimize Minify Cache" );
            }

            // Cache Enabler
            if( class_exists( 'Cache_Enabler' ) ) {
                Cache_Enabler::clear_total_cache( );

                // just in case
                do_action( 'ce_clear_cache' );

                // log the purge
                KPCPC::write_log( "\t\tCache Enabler Cache" );
            }

            // Elementor
            if( did_action( 'elementor/loaded' ) ) {

                // Automatically purge and regenerate the Elementor CSS cache
                \Elementor\Plugin::instance( ) -> files_manager -> clear_cache( );

                // log the purge
                KPCPC::write_log( "\t\tElementor Cache" );

            }

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

            // throw a hook here
            do_action( 'tcp_post_plugin_purge' );

        }

    }

}
