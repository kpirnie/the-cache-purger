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

            // let's take care of plugin purges first
            $this -> purge_plugin_caches( );

            // now we'll try Wordpress's internal cache purges
            $this -> purge_wordpress_caches( );

            // now we'll try to purge php based caches
            $this -> purge_php_caches( );

            // now we'll try to purge pagespeed mod caches
            $this -> purge_pagespeed_caches( );

            // now we'll try to purge nginx caches
            $this -> purge_nginx_caches( );

            // let's attempt to clear out file based caches
            $this -> purge_file_caches( );

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

            // WPEngine
            if ( class_exists( 'WpeCommon' ) ) {

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
            }

            // SG Optimizer.
            if ( class_exists( 'SiteGround_Optimizer\Supercacher\Supercacher' ) ) {
                SiteGround_Optimizer\Supercacher\Supercacher::purge_cache( );
            }

            // Kinsta Cache.
            if ( class_exists( 'Kinsta\Cache' ) ) {
                // $kinsta_cache object already created by Kinsta cache.php file.
                global $kinsta_cache;
                $kinsta_cache->kinsta_cache_purge -> purge_complete_full_page_cache( );
            }

            // Nginx helper Plugin (Gridpane and others)
            if ( class_exists( 'Nginx_Helper' ) ) {
                do_action( 'rt_nginx_helper_purge_all' );
            }

            // LiteSpeed Cache.
            if ( class_exists( 'LiteSpeed_Cache_Purge' ) ) {
                LiteSpeed_Cache_Purge::all( );
            }

            // Clear Cachify Cache
            if ( has_action('cachify_flush_cache') ) {
                do_action( 'cachify_flush_cache' );
            }

            // Autoptimize
            if( class_exists( 'autoptimizeCache' ) ) {
                autoptimizeCache::clearall_actionless( );
            }

            // Fast Velocity Minify
            if( function_exists( 'fvm_purge_all' ) ) {
                fvm_purge_all( );
            }

            // WPRocket
            if( function_exists( 'rocket_clean_domain' ) ) {
                rocket_clean_domain( );
            }

            // Swift Performance
            if( class_exists( 'Swift_Performance_Cache' ) ) {
                Swift_Performance_Cache::clear_all_cache( );
            }

            // Comet Cache.
            if ( class_exists( 'comet_cache' ) ) {
                comet_cache::clear( );
            }

            // Hummingbird.
            if ( class_exists( 'Hummingbird\Core\Filesystem' ) ) {

                // I would use Hummingbird\WP_Hummingbird::flush_cache( true, false ) instead, but it's disabling the page cache option in Hummingbird settings.
                Hummingbird\Core\Filesystem::instance( ) -> clean_up( );
            }

            // WP Fastest Cache
            if( class_exists( 'WpFastestCache' ) ) {
                $wpfc = new WpFastestCache( );
                $wpfc -> deleteCache( );
            }
        
            // WP Fastest Cache 2
            if ( isset( $GLOBALS['wp_fastest_cache'] ) && method_exists( $GLOBALS['wp_fastest_cache'], 'deleteCache' ) ) {
                $GLOBALS['wp_fastest_cache'] -> deleteCache( );
            }

            // WP Super Cache
            if( function_exists( 'wp_cache_clear_cache' ) ) {
                if( is_multisite( ) ) {
                    wp_cache_clear_cache( $_site_id );
                } else {
                    
                    wp_cache_clear_cache( );
                }
            }

            // W3 Total Cache
            if( function_exists( 'w3tc_flush_all' ) ) {
                w3tc_flush_all( );
            }

            // Hyper Cache
            if( class_exists( 'HyperCache' ) ) {
                $hypercache = new HyperCache( );
                $hypercache -> clean( );
            }

            // WP Optimize
            if( function_exists( 'wpo_cache_flush' ) ) {
                wpo_cache_flush( );
            }

            // WP-Optimize 2
            if ( class_exists( 'WP_Optimize_Cache_Commands' ) ) {

                // This function returns a response, so I'm assigning it to a variable to prevent unexpected output to the screen.
                $response = WP_Optimize_Cache_Commands::purge_page_cache( );
            }
            
            // WP-Optimize minification files have a different cache.
            if ( class_exists( 'WP_Optimize_Minify_Cache_Functions' ) ) {

                // This function returns a response, so I'm assigning it to a variable to prevent unexpected output to the screen.
                $response = WP_Optimize_Minify_Cache_Functions::purge( );
            }

            // Cache Enabler
            if( class_exists( 'Cache_Enabler' ) ) {
                Cache_Enabler::clear_total_cache( );
            }

            // Cloudflare
            if ( class_exists( '\CF\WordPress\Hooks' ) ) {

                // Initiliaze Hooks class which contains WordPress hook functions from Cloudflare plugin.
                $_cf_hooks = new \CF\WordPress\Hooks( );
                
                // If we have an instantiated class.
                if ( $_cf_hooks ) {
                
                    // Purge all cache.
                    $_cf_hooks -> purgeCacheEverything( );
                
                }
            }

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

            // get our WPDB global
            global $wpdb;

            // try to clear Wordpress's built-in object cache
            wp_cache_flush( );

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
            }

            // delete the transients
            $wpdb -> query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE '_transient_files_%'" );

            // probably overkill, but let's fire off the rest of the builtin cache flushing mechanisms
            global $wp_object_cache;

            // try to flush the object cache
            $wp_object_cache -> flush( 0 );

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

            // if we're on a windows server
            if( function_exists( 'wincache_ucache_get' ) ) {

                // clear it
                wincache_ucache_clear( );

            }

            // check if the Zend Opcache is available
            if( extension_loaded( 'Zend OPcache' ) ) {

                // get the status
                $_status = opcache_get_status( );

                // make sure it's enabled
                if( $_status["opcache_enabled"] ) {

                    // attempt to reset it
                    opcache_reset( );

                    // now try to clear the php file cache
                    foreach( $_status['scripts'] as $_k => $_v ) {

                        // set the directories
                        $dirs[dirname( $_k )][basename( $_k )] = $_v;
                        
                        // invalidate it
                        opcache_invalidate( $_v['full_path'] , $force = true );

                    }

                }

            }

            // check if the APC extension is enabled
            if( extension_loaded( 'apc' ) ) {

                // try to clear it's opcache
                apc_clear_cache( 'opcode' );

                // try to clear it's user cache
                apc_clear_cache( 'user' );

            }

        }

        /** 
         * purge_php_caches
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

            // hold possible pagespeed headers
            $_ps_headers = array( 'x-mod-pagespeed', 'x-page-speed' );

            // make a remote request to the site
		    $_res = wp_remote_request( site_url( ) );

            // check if there's an error
            if ( is_wp_error( $result ) ) {
                
                // there was.  this means we cannot purge this cache, so dump out of the method
                return;
            }

            // we made it this far, check for the headers from the response
            $_res_headers = wp_remote_retrieve_headers( $_res );

            // check if our pagespeed headers are in the response headers
            if( ! in_array( $_ps_headers, $_res_headers ) ) {

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

            }

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

            // let's utilize wordpress's filesystem global
            global $wp_filesystem;

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

            // hold a pages array
            $_pages = array( );

            // all we're concerned with is internal posts and pages, so let's use WP_Query to grab us an array of the URL's
            $_post_types = get_post_types( 
                array(
                    'public' => true,
                ), 
            'names' );

            // now run a query to get all posts with these post types and are published
            $_qry = new WP_Query( array( 'post_type' => $_post_types, 'posts_per_page' => -1, 'post_status' => 'publish' ) );

            // now make sure we get a post object for each item returned
            $_rs = $_qry -> get_posts( );

            // if we do not have anything throw an error
            if( $_rs ) {

                // loop over the return
                foreach( $_rs as $_post ) {

                    // we only need the permalink for this
                    $_pages[] = get_permalink( $_post -> ID );

                }

                // reset the query
                wp_reset_query( );
                
            }

            // we can dump the query here
            unset( $_rs, $_qry, $_post_types );

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

            // let's utilize wordpress's filesystem global
            global $wp_filesystem;

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

            }

        }

    }

}
