<?php
/** 
 * Cache Purger
 * 
 * This file contains cache purging methods
 * 
 * @since 7.3
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package The Cache Purger
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// check if this class already exists
if( ! class_exists( 'KP_Cache_Purge' ) ) {

    /** 
     * Class KP_Cache_Purge
     * 
     * Class for attempting to purge all caches
     * 
     * @since 7.3
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package The Cache Purger
     * 
     * @property int $site_id Holds the current site ID if the site is a multisite
     * 
    */
    class KP_Cache_Purge {

        // internal class properties
        private int $site_id;

        /** We're going to use this to populate our class properties */
        public function __construct( ) {

            // get the current site ID
            $this -> site_id = get_current_blog_id( );

            // log
            KPCPC::write_log( "------------------------------------" );
            KPCPC::write_log( "STARTING THE PURGE" );
            KPCPC::write_log( "------------------------------------" );

        }

        // clean us up --- probably not necessary, but whatever...
        public function __destruct( ) { 

            // release our properties
            unset( $this -> site_id );

            // log
            KPCPC::write_log( "------------------------------------" );
            KPCPC::write_log( "ENDING THE PURGE" );
            KPCPC::write_log( "------------------------------------\n" );
        }

        /** 
         * kp_do_purge
         * 
         * Public method attempting to purge the sites caches
         * 
         * @since 7.3
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        public function kp_do_purge( ) : void {

            // log the purge
            KPCPC::write_log( "\tHOSTING PURGE" );
            // let's take care of the hosting caches first
            $this -> purge_hosting_caches( );

            // log the purge
            KPCPC::write_log( "\tPLUGIN PURGE" );
            // now we'll try plugin purges
            $this -> purge_plugin_caches( );

            // log the purge
            KPCPC::write_log( "\tWP PURGE" );
            // now we'll try Wordpress's internal cache purges
            $this -> purge_wordpress_caches( );

            // log the purge
            KPCPC::write_log( "\tPHP PURGE" );
            // now we'll try to purge php based caches
            $this -> purge_php_caches( );

            // log the purge
            KPCPC::write_log( "\tPAGESPEED PURGE" );
            // now we'll try to purge pagespeed mod caches
            $this -> purge_pagespeed_caches( );

            // log the purge
            KPCPC::write_log( "\tNGINX PURGE" );
            // now we'll try to purge nginx caches
            $this -> purge_nginx_caches( );

            // log the purge
            KPCPC::write_log( "\tFILE PURGE" );
            // let's attempt to clear out file based caches
            $this -> purge_file_caches( );

            // log the purge
            KPCPC::write_log( "\tAPI/SERVER PURGE" );
            // let's attempt to purge the api and server based caches
            $this -> purge_remote_apiserver_caches( );

        }

        /** 
         * purge_remote_apiserver_caches
         * 
         * This method attempts to utilize the purge methods 
         * of the configured remote caches
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        private function purge_remote_apiserver_caches( ) : void {

            // get our options 
            $_opt = KPCPC::get_options( );

            // purge redis
            $this -> purge_redis( $_opt );

            // purge memcached
            $this -> purge_memcached( $_opt );
            
            // purge memcache
            $this -> purge_memcache( $_opt );

            // cloudflare
            $this -> purge_cloudflare( $_opt );

            // sucuri
            $this -> purge_sucuri( $_opt );

        }

        /** 
         * purge_sucuri
         * 
         * This method attempts to purge the sucuri cache configured
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @param object $_opt The options object
         * 
         * @return void This method does not return anything
         * 
        */
        private function purge_sucuri( object $_opt ) : void {

            // get the key
            $_key = ( $_opt -> service_api_keys['sucuri_key'] ) ?? null;
            
            // get the secret
            $_secret = ( $_opt -> service_api_keys['sucuri_secret'] ) ?? null;

            // make sure they both exist
            if( $_key && $_secret ) {

                // create the request URL
                $_url = sprintf(
                    'https://waf.sucuri.net/api?k=%s&s=%s&a=clearcache',
                    sanitize_text_field( $_key ),
                    sanitize_text_field( $_secret )
                );

                // all we need to do is perform the request
                wp_safe_remote_get( $_url, array(
                    'headers' => array(
                        'timeout' => 30,
                    ),
                ) );

                // log it
                KPCPC::write_log( "\t\tSUCURI PURGE - SUCCESS");

            }

        }

        /** 
         * purge_cloudflare
         * 
         * This method attempts to purge the cloudflare cache configured
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @param object $_opt The options object
         * 
         * @return void This method does not return anything
         * 
        */
        private function purge_cloudflare( object $_opt ) : void {
            
            // get the cloudflare token
            $_token = ( $_opt -> service_api_keys['cloudflare_token'] ) ?? null;
            
            // get the cloudflare zone
            $_zone = ( $_opt -> service_api_keys['cloudflare_zone'] ) ?? null;

            // make sure we have all required fields
            if( $_token && $_zone ) {

                // setup our arguments
                $_args = array(
                    'headers' => array(
                        'timeout' => 30,
                        'Authorization' => "Bearer " . sanitize_text_field( $_token ),
                        'Content-Type' => 'application/json',
                    ),
                    'body' => json_encode( array( 'purge_everything' => true ) ),
                );

                // setup the URL
                $_url = sprintf(
                    'https://api.cloudflare.com/client/v4/zones/%s/purge_cache',
                    sanitize_text_field( $_zone )
                );

                // utilize wordpress's built-in remote post
                $_req = wp_safe_remote_post( $_url, $_args );

                // get the responses body
                $_resp = wp_remote_retrieve_body( $_req );

                // if there is a response
                if( ! empty( $_resp ) ) {

                    // decode the json
                    $_json = json_decode( $_resp, true );

                    // if it was not successful
                    if( ! $_json['success'] ) {

                        // log it
                        KPCPC::write_log( "\t\tCLOUDFLARE PURGE - " . $_json['errors'][0]['message'] );

                    } else {

                        // log it
                        KPCPC::write_log( "\t\tCLOUDFLARE PURGE - SUCCESS");

                    }

                }

            }

        }

        /** 
         * purge_redis
         * 
         * This method attempts to purge the redis servers configured
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @param object $_opt The options object
         * 
         * @return void This method does not return anything
         * 
        */
        private function purge_redis( object $_opt ) : void {

            // redis
            $_allow_redis = filter_var( ( $_opt -> remote_redis ) ?? false, FILTER_VALIDATE_BOOLEAN );

            // if we are doing the remote redis
            if( $_allow_redis ) {

                if( class_exists( 'Redis' ) ) {

                    // fire up the redis class
                    $_redis = new Redis( );

                    // get the configured servers
                    $_servers = ( $_opt -> remote_redis_servers ) ?? array( );

                    // make sure we have some
                    if( ! empty( $_servers ) ) {

                        // loop them
                        foreach( $_servers as $_server ) {

                            // try to trap an exception
                            try {

                                // connect
                                $_redis -> connect( $_server['remote_redis_server'], $_server['remote_redis_port'] );

                                // now flush
                                $_redis -> flushAll( );

                                // now close the connection
                                $_redis -> close( );

                            } catch ( Exception $e ) {
                                // do nothing... php will ignore and continue 
                            }

                        }

                    }

                    // clean it up
                    unset( $_redis );

                }

            }

        }

        /** 
         * purge_memcache
         * 
         * This method attempts to purge the memcache servers configured
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @param object $_opt The options object
         * 
         * @return void This method does not return anything
         * 
        */
        private function purge_memcache( object $_opt ) : void {

            // memcached
            $_allow_memcache = filter_var( ( $_opt -> remote_memcache ) ?? false, FILTER_VALIDATE_BOOLEAN );
            
            // if we are doing the remote memcached
            if( $_allow_memcache ) {

                // make sure the Memcached module is installed for PHP
                if( class_exists( 'Memcache' ) ) {

                    // get the configured memcache servers
                    $_servers = ( $_opt -> remote_memcache_servers ) ?? array( );

                    // make sure this exists
                    if( ! empty( $_servers ) ) {

                        // fire it up
                        $_mc = new Memcache( );

                        // loop them
                        foreach( $_servers as $_server ) {

                            // add the server
                            $_mc -> addServer( $_server['remote_memcache_server'], $_server['remote_memcache_port'] );

                            // now flush it
                            $_mc -> flush( );

                        }

                        // clean it up 
                        unset( $_mc );

                    }

                }

            }

        }

        /** 
         * purge_memcached
         * 
         * This method attempts to purge the memcached servers configured
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @param object $_opt The options object
         * 
         * @return void This method does not return anything
         * 
        */
        private function purge_memcached( object $_opt ) : void {

            // memcached
            $_allow_memcached = filter_var( ( $_opt -> remote_memcached ) ?? false, FILTER_VALIDATE_BOOLEAN );
            
            // if we are doing the remote memcached
            if( $_allow_memcached ) {

                // make sure the Memcached module is installed for PHP
                if( class_exists( 'Memcached' ) ) {

                    // get the configured memcached servers
                    $_servers = ( $_opt -> remote_memcached_servers ) ?? array( );

                    // make sure this exists
                    if( ! empty( $_servers ) ) {

                        // fire it up
                        $_mc = new Memcached( );

                        // loop them
                        foreach( $_servers as $_server ) {

                            // add the server
                            $_mc -> addServer( $_server['remote_memcached_server'], $_server['remote_memcached_port'] );

                            // now flush it
                            $_mc -> flush( );

                        }

                        // clean it up 
                        unset( $_mc );

                    }

                }

            }

        }

        /** 
         * purge_hosting_caches
         * 
         * This method attempts to utilize the purge methods 
         * of the most common hosting environments
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        private function purge_hosting_caches( ) : void {

            // throw a hook here
            do_action( 'tcp_pre_hosting_purge' );

            // WPEngine
            if( class_exists( 'WpeCommon' ) ) {

                // clear memcached
                if ( method_exists( 'WpeCommon', 'purge_memcached' ) ) {
                    WpeCommon::purge_memcached( );
                }

                // clear cdn
                if ( method_exists( 'WpeCommon', 'clear_maxcdn_cache' ) ) {
                    WpeCommon::clear_maxcdn_cache( );
                }

                // clear varnish
                if ( method_exists( 'WpeCommon', 'purge_varnish_cache' ) ) {
                    WpeCommon::purge_varnish_cache( );
                }

                // log the purge
                KPCPC::write_log( "\t\tWPEngine Cache" );

            }

            // Kinsta Cache.
            if( class_exists( 'Kinsta\Cache' ) ) {

                // $kinsta_cache object already created by Kinsta cache.php file.
                global $kinsta_cache;
                $kinsta_cache->kinsta_cache_purge -> purge_complete_full_page_cache( );

                // log the purge
                KPCPC::write_log( "\t\tKinsta Cache" );

            }

            // GoDaddy
            if( class_exists( '\WPaaS\Cache' ) ) {

                // purge and ban the GoDaddy Cache
                remove_action( 'shutdown', [ '\WPaaS\Cache', 'purge' ], PHP_INT_MAX );
                add_action( 'shutdown', [ '\WPaaS\Cache', 'ban' ], PHP_INT_MAX );

                // log the purge
                KPCPC::write_log( "\t\tGoDaddy Cache" );

            }

            // Media Temple


            // Pantheon
            if( function_exists( 'pantheon_clear_edge_all' ) ) {

                // purge all caches
                pantheon_clear_edge_all( );

                // log the purge
                KPCPC::write_log( "\t\tPantheon Cache" );

            }

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

            // RunCloud - this will only work if the RunCloud Hub is installed
            if( class_exists( 'RunCloud_Hub' ) ) {

                // if the site is a multisite
                if( is_multisite( ) ) {
                    
                    // purge all sites caches
                    @RunCloud_Hub::purge_cache_all_sites( );
                
                // we're not
                } else {

                    // purge the sites caches
                    @RunCloud_Hub::purge_cache_all();
                }

                // log the purge
                KPCPC::write_log( "\t\tRunCloud Cache" );

            }

            // throw a hook here
            do_action( 'tcp_post_hosting_purge' );

        }

        /** 
         * purge_plugin_caches
         * 
         * This method attempts to utilize the purge methods 
         * of the most common caching plugins
         * 
         * @since 7.3
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
                if( is_multisite( ) ) {
                    wp_cache_clear_cache( $_site_id );
                } else {
                    
                    wp_cache_clear_cache( );
                }

                // log the purge
                KPCPC::write_log( "\t\tWP Super Cache" );
            }

            // W3 Total Cache
            if( function_exists( 'w3tc_flush_all' ) ) {
                w3tc_flush_all( );

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

                // log the purge
                KPCPC::write_log( "\t\tCache Enabler Cache" );
            }

            // throw a hook here
            do_action( 'tcp_post_plugin_purge' );

        }

        /** 
         * purge_wordpress_caches
         * 
         * This method attempts to utilize the purge methods 
         * builtin to Wordpress
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        private function purge_wordpress_caches( ) : void {

            // throw a hook here
            do_action( 'tcp_pre_wp_purge' );

            // get our WPDB global
            global $wpdb;

            // try to clear Wordpress's built-in object cache
            wp_cache_flush( );

            // log the purge
            KPCPC::write_log( "\t\tWP Cache" );

            // now try to delete the wp object cache
            if( function_exists( 'wp_cache_delete' ) ) {

                // clear the plugin object cache
                wp_cache_delete( 'uninstall_plugins', 'options' );

                // clear the options object cache
                wp_cache_delete( 'alloptions', 'options' );

                // clear the rest of the object cache
                wp_cache_delete( 'notoptions', 'options' );

                // clear the rest of the object cache for the parent site in a multisite install
                wp_cache_delete( $this -> site_id . '-notoptions', 'site-options' );

                // clear the plugin object cache for the parent site in a multisite install
                wp_cache_delete( $this -> site_id . '-active_sitewide_plugins', 'site-options' );

                // log the purge
                KPCPC::write_log( "\t\tWP Object Cache" );
            }

            // delete the transients
            $wpdb -> query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE '_transient_files_%'" );

            // log the purge
            KPCPC::write_log( "\t\tWP Transient Cache" );

            // probably overkill, but let's fire off the rest of the builtin cache flushing mechanisms
            global $wp_object_cache;

            // try to flush the object cache
            $wp_object_cache -> flush( 0 );

            // log the purge
            KPCPC::write_log( "\t\tWP Object 2 Cache" );

            // throw a hook here
            do_action( 'tcp_post_wp_purge' );

        }

        /** 
         * purge_php_caches
         * 
         * This method attempts to purge php based caches
         * if they exist; wincache, opcache, apc and apcu
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        private function purge_php_caches( ) : void {

            // implement hook
            do_action( 'tcp_pre_php_purge' );

            // if we're on a windows server
            if( function_exists( 'wincache_ucache_get' ) ) {

                // clear it
                wincache_ucache_clear( );

                // log the purge
                KPCPC::write_log( "\t\tPHP Win Cache" );

            }

            // check if the Zend Opcache is available
            if( extension_loaded( 'Zend OPcache' ) ) {

                // get the status
                $_status = opcache_get_status( );

                // make sure it's enabled
                if( isset( $_status["opcache_enabled"] ) ) {

                    // attempt to reset it
                    opcache_reset( );

                    // now try to clear the php file cache
                    foreach( $_status['scripts'] as $_k => $_v ) {

                        // set the directories
                        $dirs[dirname( $_k )][basename( $_k )] = $_v;
                        
                        // invalidate it
                        opcache_invalidate( $_v['full_path'] , $force = true );

                    }

                    // log the purge
                    KPCPC::write_log( "\t\tPHP Zend OpCache" );

                }

            }

            // check if the APC extension is enabled
            if( extension_loaded( 'apc' ) ) {

                // try to clear it's opcache
                apc_clear_cache( 'opcode' );

                // try to clear it's user cache
                apc_clear_cache( 'user' );

                // log the purge
                KPCPC::write_log( "\t\tPHP APC Cache" );

            }

            // implement hook
            do_action( 'tcp_post_php_purge' );

        }

        /** 
         * purge_pagespeed_caches
         * 
         * This method attempts to purge the PageSpeed Mod caches
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        private function purge_pagespeed_caches( ) : void {

            // implement hook
            do_action( 'tcp_pre_pagespeed_purge' );

            // hold possible pagespeed headers
            $_ps_headers = array( 'x-mod-pagespeed', 'x-page-speed' );

            // make a remote request to the site
		    $_res = wp_remote_request( site_url( ) );

            // check if there's an error
            if ( is_wp_error( $_res ) ) {
                
                // there was.  this means we cannot purge this cache, so dump out of the method
                return;
            }

            // we made it this far, check for the headers from the response
            $_res_headers = wp_remote_retrieve_headers( $_res );

            // cast it as an array, and reset the index
            // NOTE: we have to do this, because the WP Dev page lies, this is not returned as an array, it's returned as a object(Requests_Utility_CaseInsensitiveDictionary)
            $_headers = array_values( ( array ) $_res_headers );

            // check if our pagespeed headers are in the response headers
            if( ! in_array( $_ps_headers, $_headers ) ) {

                // the are not, this means the pagespeed module is not installed on the server, and we can dump out of this method
                return;

            }

            // hold a "is cloudflare" flag
            $_is_cf = ( isset( $_res_headers['server'] ) && ( strpos( $_res_headers['server'], 'cloudflare' ) !== false ) );

            // hold our default request arguments
            $_args = array(
                'method' => 'PURGE',
                'redirection' => 0,
            );

            // get the server IP
            $_server_ip = filter_input( INPUT_SERVER, 'SERVER_ADDR', FILTER_VALIDATE_IP );

            // get our URL list
            $_urls = KPCPC::get_urls( );

            // make sure we have some returned
            if( $_urls ) {

                // loop them
                foreach( $_urls as $_url ) {

                    // if it is run through cloudflare
                    if( $_is_cf ) {

                        // parse the url
                        $_link = wp_parse_url( $_url );

                        // rebuild the URL
                        $_url = $_link['scheme'] . '://' . $_server_ip . $_link['path'];

                        // set some more arguments
                        $_args['redirection'] = 5; // -L
                        $_args['sslverify'] = false; // -k
                        $_args['headers'] = 'host: ' . $_link['host'];

                    }

                    // make the remote PURGE request
                    wp_remote_request( $_url, $_args );

                }

                // log the purge
                KPCPC::write_log( "\t\tPagespeed Cache" );

            }

            // implement hook
            do_action( 'tcp_post_pagespeed_purge' );

        }

        /** 
         * purge_nginx_caches
         * 
         * This method attempts to purge nginx based caches
         * if they exist
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        private function purge_nginx_caches( ) : void {

            // implement hook
            do_shortcode( 'tcp_pre_nginx_purge' );

            // let's utilize wordpress's filesystem global
            global $wp_filesystem;

            // if we do not have the global yet
            if( empty( $wp_filesystem ) ) {

                // require the file
                require_once ABSPATH . '/wp-admin/includes/file.php';

                // initialize the wordpress filesystem
                WP_Filesystem( );

            }

            // hold the cache location, we'll need to try a few locations
            $_cache_path = '';

            // check the /etc/nginx location
            if( @is_readable( '/etc/nginx/cache/' ) ) {

                // set the cache path
                $_cache_path = '/etc/nginx/cache/';
 
            // check the /var/cache location
            } elseif( @is_readable( '/var/cache/nginx/') ) {

                // set the cache path
                $_cache_path = '/var/cache/nginx/';
 
            // check the /var/cache location for runcloud
            } elseif( @is_readable( '/var/cache/nginx-rc/') ) {

                // set the cache path
                $_cache_path = '/var/cache/nginx-rc/';

            // check the /var/cache location for cpanel
            } elseif( @is_readable( '/var/cache/ea-nginx/proxy/') ) {

                // set the cache path
                $_cache_path = '/var/cache/ea-nginx/proxy/';
            
            // check the /var/nginx-cache location inmotion
            } elseif( @is_readable( '/var/nginx-cache/') ) {

                // set the cache path
                $_cache_path = '/var/nginx-cache/';

            }

            // get our URL list
            $_pages = KPCPC::get_urls( );

            // make sure we have pages
            if( $_pages ) {

                // loop over the page array
                foreach( $_pages as $_page ) {

                    // parse the URL
                    $_url = parse_url( $_page );

                    // get the scheme
                    $_scheme = $_url['scheme'];

                    // get the host
                    $_host = $_url['host'];

                    // the uri
                    $_requesturi = $_url['path'];

                    // hash it
                    $_hash = md5( $_scheme . 'GET' . $_host . $_requesturi );

                    // now setup the full path
                    $_path = $_cache_path . substr( $_hash, -1 ) . '/' . substr( $_hash, -3 ,2 ) . '/' . $_hash;

                    // if it's a directory
                    if( $wp_filesystem -> is_dir( $_path ) ) {

                        // try to delete it recursively
                        $wp_filesystem -> delete( $_path, true, 'd' );

                        // for my own OCDness, let's then recreate it
                        $wp_filesystem -> mkdir( $_path );

                    // otherwise it's a file
                    } else {

                        // try to delete it
                        $wp_filesystem -> delete( $_path, false, 'f' );

                    }
                    
                }

                // log the purge
                KPCPC::write_log( "\t\tNginx Cache" );

            }

            // implement hook
            do_action( 'tcp_post_nginx_purge' );

        }

        /** 
         * purge_file_caches
         * 
         * This method attempts to delete the file based caches
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        private function purge_file_caches( ) : void {

            // implement hook
            do_action( 'tcp_pre_file_purge' );

            // let's utilize wordpress's filesystem global
            global $wp_filesystem;

            // if we do not have the global yet
            if( empty( $wp_filesystem ) ) {

                // require the file
                require_once ABSPATH . '/wp-admin/includes/file.php';

                // initialize the wordpress filesystem
                WP_Filesystem( );

            }

            // hold our built cache path variable
            $_cache_path = '';

            // if the WPCACHE path is set
            if( defined( 'WPCACHEHOME' ) ) {

                // set the cache path to it
                $_cache_path = WPCACHEHOME;

            // otherwise, attempt to build one
            } else {

                // set it
                $_cache_path = ABSPATH . 'wp-content/cache/';

            }
            
            // clear the files from the cache path.  This should take care of the rest
            if( @is_readable ( $_cache_path ) ) {

                // get a list of the files/folders in the cache path
                $_files = glob( $_cache_path . '*' );

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

                // log the purge
                KPCPC::write_log( "\t\tFile Cache" );

            }

            // implement hook
            do_action( 'tcp_post_file_purge' );

        }

    }

}
