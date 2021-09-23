<?php
/*
Plugin Name: Blizhost CloudCache Purge
Plugin URI: https://www.blizhost.com.br/
Description: Automatically empty your site cache when a post is published or when content is modified. And it improves CloudCache compatibility.
Version: 4.0.1
Author: Blizhost
Author URI: https://www.blizhost.com.br/
License: http://www.apache.org/licenses/LICENSE-2.0
Text Domain: blizhost-cache-purge
Network: true
Domain Path: /lang/

Copyright 2017-2020: Blizhost (email: contato@blizhost.com), Mika A. Epstein (email: ipstenu@halfelf.org) - based on version 4.0.2

*/

/**
 * Blizhost Purge CloudCache Class
 *
 * @since 2.0
 */

class BlizCloudCachePurger {
	protected $purgeUrls = array();
	public $p_version = '4.0.1'; // Version

	/**
	 * Init
	 *
	 * @since 2.0
	 * @access public
	 */
	public function __construct( ) {
		
		$this->http_x_server = isset($_SERVER['HTTP_X_SERVER']) ? $_SERVER['HTTP_X_SERVER'] : '';
		
		// Load custom Blizhost font style
		function load_blizhost_logo_adminbar() {
			if (is_user_logged_in()) {
				wp_register_style( 'blizhost_logo_adminbar_css', plugin_dir_url( __FILE__ ) . 'font/style.css', false, '1.2' );
				wp_enqueue_style( 'blizhost_logo_adminbar_css' );
			}
		}
		add_action( 'admin_enqueue_scripts', 'load_blizhost_logo_adminbar' );
		add_action( 'wp_enqueue_scripts', 'load_blizhost_logo_adminbar' ); 
		
		add_filter( 'admin_init', array( &$this, 'blizhost_donotcache' ) );
		add_filter( 'template_include', array( &$this, 'donotcache_filter' ) );
		
		// Automatic Background Updates - Users may not like this
		//add_filter( 'auto_update_core', '__return_true' );
		//add_filter( 'auto_update_plugin', '__return_true' );
		//add_filter( 'auto_core_update_send_email', '__return_false' );
		
		// Remove version of WordPress from header for security reasons
		remove_action( 'wp_head', 'wp_generator' );
		
		// Remove version of WordPress from rss for security reasons
		add_filter( 'the_generator', '__return_empty_string' );
		
		// Remove version of Slider Revolution for security reasons
		add_filter( 'revslider_meta_generator', '__return_empty_string' );
		
		// Hash version from scripts and styles for security reasons
		function remove_ver_scripts_styles($src) {
			if (strpos($src, $_SERVER['HTTP_HOST']) === false) {
				return $src;
			}
			elseif (strpos($src, 'ver=')) {
				preg_match('~ver=([0-9.-_]+)~', $src, $get_ver);
				$hash_ver = preg_replace('/\d+/u', '', md5($get_ver[1]));
				$hash_ver = substr($hash_ver, 0, 5);
				$src = str_replace('ver='.$get_ver[1], 'ver='.$hash_ver, $src);
			}
			return $src;
		}
		add_filter( 'style_loader_src', 'remove_ver_scripts_styles', 9999);
		add_filter( 'script_loader_src', 'remove_ver_scripts_styles', 9999);
		
		add_action( 'init', array( &$this, 'init' ) );
		add_action( 'activity_box_end', array( $this, 'blizccache_rightnow' ), 100 );
		
		// WordPress Image CDN
		add_action( 'admin_notices' , array( &$this, 'needJetpackMessage')); // Explains you need Jetpack-connected plugin to use this feature
		add_action( 'admin_init', array( &$this, 'needJetpackMessage_dismissed'));
		add_action( 'init', array( &$this, 'cdn_imageurl_buffer' ) ); // Replaces images with final html output
		
		// CloudFlare Flexible SSL - since 3.1
		$HttpsServerOpts = array( 'HTTP_CF_VISITOR', 'HTTP_X_FORWARDED_PROTO' );
		foreach( $HttpsServerOpts as $sOpt ) {

			if ( isset( $_SERVER[ $sOpt ] ) && ( strpos( $_SERVER[ $sOpt ], 'https' ) !== false ) ) {
				$_SERVER[ 'HTTPS' ] = 'on';
				break;
			}
		}
		if ( is_admin() ) {
			add_action( 'admin_init', array( $this, 'keepsPluginAtLoadPosition') );
		}
		
	}

	/**
	 * Plugin Init
	 *
	 * @since 1.0
	 * @access public
	 */
	public function init() {
		global $blog_id;

		// load language
		load_plugin_textdomain( 'blizhost-cache-purge', '', dirname( plugin_basename( __FILE__ ) ) . '/lang' );
		
		// Warning: No Pretty Permalinks!
		if ( '' == get_option( 'permalink_structure' ) && current_user_can('manage_options') ) {
			add_action( 'admin_notices' , array( $this, 'blizprettyPermalinksMessage'));
			return;
		}
		
		// get my events
		$events = $this->blizgetRegisterEvents();
		$noIDevents = $this->getNoIDEvents();

		// make sure we have events and they're in an array
		if ( !empty( $events ) && !empty( $noIDevents ) ) {

			// Force it to be an array, in case someone's stupid
			$events = (array) $events;
			$noIDevents = (array) $noIDevents;

			// Add the action for each event
			foreach ( $events as $event) {
				if ( in_array($event, $noIDevents ) ) {
					// These events have no post ID and, thus, will perform a full purge
					add_action( $event, array($this, 'purgeNoID') );
				} else {
					add_action( $event, array($this, 'blizpurgePost'), 10, 2 );
				}
			}
		}
		
		add_action( 'shutdown', array($this, 'blizexecutePurge') );

		// Success: Admin notice when purging
		if ( isset($_GET['bliz_flush_all']) && check_admin_referer('bliz-flush-all') ) {
			add_action( 'admin_notices' , array( $this, 'blizpurgeMessage'));
		}

		// Checking user permissions for who can and cannot use the admin button
		if (
			// SingleSite - admins can always purge
			( !is_multisite() && current_user_can('activate_plugins') ) ||
			// Multisite - Network Admin can always purge
			current_user_can('manage_network') ||
			// Multisite - Site admins can purge UNLESS it's a subfolder install and we're on site #1
			( is_multisite() && current_user_can('activate_plugins') && ( SUBDOMAIN_INSTALL || ( !SUBDOMAIN_INSTALL && ( BLOG_ID_CURRENT_SITE != $blog_id ) ) ) )
			) {
				add_action( 'admin_bar_menu', array( $this, 'blizccache_rightnow_adminbar' ), 100 );
		}

	}

	/**
	 * Set do not cache header
	 *
	 * @since 3.6
	 */
	function blizhost_donotcache() {
		// Does not cache in CloudCache if DONOTCACHEPAGE is TRUE or if the request is for an administrative page - Blizhost VCL
		if(defined('DONOTCACHEPAGE') || defined('DOING_CRON') || is_admin() || $GLOBALS['pagenow'] === 'wp-login.php') {
			// Do not set header if header has already been sent or if the request is ajax
			if (!headers_sent() && !defined('DOING_AJAX') && strpos($this->http_x_server, 'blizhost') !== false) {
				// Do not set header if is single or front page
				if (!is_home() && !is_front_page() && !is_single() && $_SERVER['REQUEST_URI'] != '/') {
					header("BypassCloudCache: TRUE");if(!$_SERVER['HTTP_X_BYPASSCCACHE']){exit;}
				}
			}
		}
	}

	/**
	 * Set do not cache header by template_include
	 * Is the latest possible action prior to output
	 * Required to get DONOTCACHEPAGE in some cases
	 *
	 * @since 3.6
	 */
	function donotcache_filter($template) {
		$this->blizhost_donotcache();
		return $template;
	}

	/**
	 * Purge Message
	 * Informs of a succcessful purge
	 *
	 * @since 2.0
	 */
	function blizpurgeMessage() {
		echo "<div id='message' class='notice notice-success fade is-dismissible'><p><strong>".__('All CloudCache has been purged!', 'blizhost-cache-purge')."</strong></p></div>";
	}

	/**
	 * Permalinks Message
	 * Explains you need Pretty Permalinks on to use this plugin
	 *
	 * @since 2.0
	 */
	function blizprettyPermalinksMessage() {
		echo "<div id='message' class='error'><p>" . sprintf( __( 'Blizhost CloudCache Purge requires you to use custom permalinks. Please go to the <a href="%1$s">Permalinks Options Page</a> to configure them.', 'blizhost-cache-purge' ), admin_url( 'options-permalink.php' ) ) . "</p></div>";
	}

	/**
	 * WordPress Image CDN Message
	 * Explains you need Jetpack-connected plugin to use this feature
	 *
	 * @since 3.9.4
	 */
	function needJetpackMessage() {
		if ( !get_user_meta( get_current_user_id(), 'needJetpackMessage_dismissed' ) && !class_exists('Jetpack') ) {
			echo sprintf( "<div id='message' class='notice notice-warning'><p>" . sprintf( __( 'Blizhost CloudCache Purge requires <a href="https://wordpress.org/plugins/jetpack/" target="_blank">Jetpack-connected</a> plugin to use WordPress Image CDN. Please install and connect this plugin to automatically use this feature. <a href="%1$s">Do not show again.</a>', 'blizhost-cache-purge' ), '?needjp=0' ) ) . "</p></div>";
		}
	}
	
	/**
	 * Save if needJetpackMessage has been dismissed to not display again
	 */
	function needJetpackMessage_dismissed() {
		if ( isset( $_GET['needjp'] ) )
			add_user_meta( get_current_user_id(), 'needJetpackMessage_dismissed', 'true', true );
	}

	/**
	 * The Home URL
	 * Get the Home URL and allow it to be filterable
	 * This is for domain mapping plugins that, for some reason, don't filter
	 * on their own (including WPMU, Ron's, and so on).
	 *
	 * @since 4.0
	 */
	static public function the_home_url(){
		$home_url = apply_filters( 'vhp_home_url', home_url() );
		return $home_url;
	}

	/**
	 * CloudCache Purge Button in the Admin Bar
	 *
	 * @since 2.0
	 */
	function blizccache_rightnow_adminbar($admin_bar){
		$admin_bar->add_menu( array(
			'id'	=> 'bliz-purge-ccache-cache',
			'title' => '<span class="ab-icon blizicon-symbol_16px" style="font-size: 18px;margin-top: 3px;font-family: \'blizhost_logo\'!important;speak: none;font-style: normal;font-weight: normal;font-variant: normal;text-transform: none;line-height: 1;-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;"></span>' . __('Blizhost CloudCache','blizhost-cache-purge'),
		));
		
		$admin_bar->add_menu( array(
			'parent' => 'bliz-purge-ccache-cache',
			'id'	=> 'bliz-purge-ccache-cache-all',
			'title' => __('Purge Entire Cache','blizhost-cache-purge'),
			'href'  => wp_nonce_url( add_query_arg('bliz_flush_all', 1), 'bliz-flush-all'),
			'meta'  => array(
				'title' => __('Purge Entire Cache (All Pages)','blizhost-cache-purge'),
			),
		));
	}

	/**
	 * CloudCache Right Now Information
	 * This information is put on the Dashboard 'Right now' widget
	 *
	 * @since 1.0
	 */
	function blizccache_rightnow() {
		global $blog_id;
		$url = wp_nonce_url(add_query_arg('bliz_flush_all', 1), 'bliz-flush-all');
		$intro = sprintf( __('<a href="%1$s" target="_blank">Blizhost CloudCache Purge</a> automatically clears the cache for Blizhost customers when a post is created or changed.', 'blizhost-cache-purge' ), 'https://wordpress.org/plugins/blizhost-cache-purge/' );
		$button =  __('Press the button below to force a cleanup of the entire cache:', 'blizhost-cache-purge' );
		$button .= '</p><p><span class="button"><a href="'.$url.'"><strong>';
		$button .= __('Purge CloudCache', 'blizhost-cache-purge' );
		$button .= '</strong></a></span>';
		$nobutton =  __('You do not have permission to purge the cache for the whole site. Please contact your administrator.', 'blizhost-cache-purge' );

		if (
			// SingleSite - admins can always purge
			( !is_multisite() && current_user_can('activate_plugins') ) ||
			// Multisite - Network Admin can always purge
			current_user_can('manage_network') ||
			// Multisite - Site admins can purge UNLESS it's a subfolder install and we're on site #1
			( is_multisite() && !current_user_can('manage_network') && ( SUBDOMAIN_INSTALL || ( !SUBDOMAIN_INSTALL && ( BLOG_ID_CURRENT_SITE != $blog_id ) ) ) )
		) {
			$text = $intro.' '.$button;
		} else {
			$text = $intro.' '.$nobutton;
		}
		echo "<p class='ccache-rightnow'>$text</p>\n";
	}

	// CloudFlare Flexible SSL functions - 1.2.2
	/**
	 * Sets this plugin to be the first loaded of all the plugins.
	 */
	public function keepsPluginAtLoadPosition() {
		$sBaseFile = plugin_basename( __FILE__ );
		$nLoadPosition = $this->getAcPluginLoadPosition( $sBaseFile );
		if ( $nLoadPosition > 1 ) {
			$this->setAcPluginLoadPosition( $sBaseFile, 0 );
		}
	}

	/**
	 * @param string $sPluginFile
	 * @return int
	 */
	public function getAcPluginLoadPosition( $sPluginFile ) {
		$sOptKey = is_multisite() ? 'active_sitewide_plugins' : 'active_plugins';
		$aActive = get_option( $sOptKey );
		$nPosition = -1;
		if ( is_array( $aActive ) ) {
			$nPosition = array_search( $sPluginFile, $aActive );
			if ( $nPosition === false ) {
				$nPosition = -1;
			}
		}
		return $nPosition;
	}

	/**
	 * @param string $sPluginFile
	 * @param int $nDesiredPosition
	 */
	public function setAcPluginLoadPosition( $sPluginFile, $nDesiredPosition = 0 ) {

		$aActive = $this->setValueToPosition( get_option( 'active_plugins' ), $sPluginFile, $nDesiredPosition );
		update_option( 'active_plugins', $aActive );

		if ( is_multisite() ) {
			$aActive = $this->setValueToPosition( get_option( 'active_sitewide_plugins' ), $sPluginFile, $nDesiredPosition );
			update_option( 'active_sitewide_plugins', $aActive );
		}
	}

	/**
	 * @param array $aSubjectArray
	 * @param mixed $mValue
	 * @param int $nDesiredPosition
	 * @return array
	 */
	public function setValueToPosition( $aSubjectArray, $mValue, $nDesiredPosition ) {

		if ( $nDesiredPosition < 0 || !is_array( $aSubjectArray ) ) {
			return $aSubjectArray;
		}

		$nMaxPossiblePosition = count( $aSubjectArray ) - 1;
		if ( $nDesiredPosition > $nMaxPossiblePosition ) {
			$nDesiredPosition = $nMaxPossiblePosition;
		}

		$nPosition = array_search( $mValue, $aSubjectArray );
		if ( $nPosition !== false && $nPosition != $nDesiredPosition ) {

			// remove existing and reset index
			unset( $aSubjectArray[ $nPosition ] );
			$aSubjectArray = array_values( $aSubjectArray );

			// insert and update
			// http://stackoverflow.com/questions/3797239/insert-new-item-in-array-on-any-position-in-php
			array_splice( $aSubjectArray, $nDesiredPosition, 0, $mValue );
		}

		return $aSubjectArray;
	}
	//
	
	// WordPress Image CDN functions
	/**
	 * Replaces image urls
	 * to use the free WordPress Image CDN
	 *
	 * @since 3.9.1
	 */
	function cdn_imageurl($content) {
		// Prepare url for regex
		$url = preg_replace("/(http:\/\/|https:\/\/)/i", "", get_site_url()); // It covers some url variations
		$url = str_replace("www.", "", $url);
		$url = str_replace("/", "\/", $url);
		$url = str_replace(".", "\.", $url);
		
		// Find the images that matches the boundaries of the CDN
		$content = preg_replace_callback("@(?i)<img ((?!<|>).)*(src *= *[\"']((https?:\/\/)(www.)?".$url."((?![\"']).)*\/wp-content\/uploads\/((?![\"']).)*\.(gif|jpe?g|png))[\"'])((?!<|>).)*>@", array( &$this, 'cdn_imageurl_replace' ),$content);
		$content = preg_replace_callback("@(?i)<article ((?!<|>).)*(data-image *= *[\"']((https?:\/\/)(www.)?".$url."((?![\"']).)*\/wp-content\/uploads\/((?![\"']).)*\.(gif|jpe?g|png))[\"'])((?!<|>).)*>@", array( &$this, 'cdn_imageurl_replace' ),$content);
		$content = preg_replace_callback("@(?i)<(div|span) ((?!<|>).)*(background-image: *url *\( *[\"']?((https?:\/\/)(www.)?".$url."((?![\"']).)*\/wp-content\/uploads\/((?![\"']).)*\.(gif|jpe?g|png))[\"']?\))((?!<|>).)*>@", array( &$this, 'cdn_imageurl_replace_bg' ),$content);
		return $content;
	}
	 
	function cdn_imageurl_replace($matches) {
		// Do not proceed if src is empty
		if(!$matches[3]) { return $matches[0]; }
	
		// Prepare url for regex
		$url = preg_replace("/(http:\/\/|https:\/\/)/i", "", get_site_url()); // It covers some url variations
		$url = str_replace("www.", "", $url);
		$url = str_replace("/", "\/", $url);
		$url = str_replace(".", "\.", $url);
		
		// Find the images that matches the boundaries of the CDN (child)
		$matches = preg_replace_callback("@(?i)((src|data-image|data-orig-file|data-medium-file|data-large-file|srcset) *= *[\"']((https?:\/\/)(www.)?".$url."((?![\"']).)*\/wp-content\/uploads\/((?![\"']).)*)[\"'])@", array( &$this, 'cdn_imageurl_replace_child' ),$matches);
		return $matches[0];
	}
	 
	function cdn_imageurl_replace_bg($matches) {
		// Do not proceed if src is empty
		if(!$matches[4]) { return $matches[0]; }
	
		// Prepare url for regex
		$url = preg_replace("/(http:\/\/|https:\/\/)/i", "", get_site_url()); // It covers some url variations
		$url = str_replace("www.", "", $url);
		$url = str_replace("/", "\/", $url);
		$url = str_replace(".", "\.", $url);
		
		// Find the images that matches the boundaries of the CDN (child)
		$matches = preg_replace_callback("@(?i)((background-image:) *url *\( *[\"']?((https?:\/\/)(www.)?".$url."((?![\"']).)*\/wp-content\/uploads\/((?![\"']).)*)[\"']?) *\)@", array( &$this, 'cdn_imageurl_replace_child' ),$matches);
		return $matches[0];
	}
	
	function cdn_imageurl_replace_child($matches) {
		// Do not proceed if src is empty
		if(!$matches[3]) { return $matches[0]; }
		
		// Generates cdn subdomain number
		$url = parse_url(get_site_url());
		$host = str_replace("www.", "", $url['host']);
		mt_srand(abs(crc32($host)));
		$static_rand = mt_rand(0,2);
		mt_srand(); // this resets everything that relies on this, like array_rand() and shuffle()
		
		// Replaces the protocol with cdn url
		$wp = 'i'.$static_rand.'.wp.com/';
		$url_replace = preg_replace("/(http:\/\/)/i", "http://".$wp, $matches[3]);
		$url_replace = preg_replace("/(https:\/\/)/i", "https://".$wp, $url_replace);
		$replace = str_replace($matches[3],$url_replace,$matches[0]);
		return $replace;
	}
	
	function cdn_imageurl_buffer() {
		// Check if Jetpack/Photon is active
		if((defined('DISABLE_WP_CDN') && DISABLE_WP_CDN === true) || (class_exists('Jetpack') && method_exists('Jetpack','get_active_modules') && in_array('photon',Jetpack::get_active_modules())) || strpos($this->http_x_server, 'blizhost') === false || (!class_exists('Jetpack') && strpos($this->http_x_server, 'blizhost') === false)) {
			return;
		}
		
		// These filters should not run on administrative pages
		if(defined('DOING_AJAX') || defined('DOING_CRON') || is_admin() || $GLOBALS['pagenow'] === 'wp-login.php') {
			return;
		}
		
		// Checks for cache plugins enabled and does not run ob_start() due to compatibility issues
		if(defined('WP_CACHE') && WP_CACHE === true) {
			// is_plugin_active function only exists on administrative pages, this fix the problem
			if ( !function_exists( 'is_plugin_active' ) ) {
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
			}
			
			// Use the super-cache plugin buffer output
			if(is_plugin_active( 'wp-super-cache/wp-cache.php' )) {
				add_filter('wp_cache_ob_callback_filter', array($this, 'cdn_imageurl'), 999);
			}else{
				// Otherwise it was not possible to use the buffer, so let's use some filters that return the html
				// this is required to avoid compatibility issues with cache plugins and keep a minimal functionality
				add_filter('the_content', array( &$this, 'cdn_imageurl' ),9999);
				add_filter('post_thumbnail_html', array( &$this, 'cdn_imageurl' ),9999);
			}
		}else{
			// Source: https://stackoverflow.com/questions/772510/wordpress-filter-to-modify-final-html-output
			ob_start();
			
			add_action('shutdown', function() {
				$final = '';
				
				// We'll need to get the number of ob levels we're in, so that we can iterate over each, collecting
				// that buffer's output into the final output.
				$levels = ob_get_level();
			
				for ($i = 0; $i < $levels; $i++) {
					$final .= ob_get_clean();
				}
				
				// Apply any filters to the final output
				$final = $this->cdn_imageurl($final);
			
				// Displays the final output
				echo $final;
			}, 0);
		}
	}
	//

	/**
	 * Registered Events
	 * These are when the purge is triggered
	 *
	 * @since 1.0
	 * @access protected
	 */
	protected function blizgetRegisterEvents() {

		// Define registered purge events
		$actions = array(
			'switch_theme',						// After a theme is changed
			'autoptimize_action_cachepurged',	// Compat with https://wordpress.org/plugins/autoptimize/
			'save_post',							// Save a post
			'deleted_post',						// Delete a post
			'trashed_post',						// Empty Trashed post
			'edit_post',							// Edit a post - includes leaving comments
			'delete_attachment',					// Delete an attachment - includes re-uploading
		);

		// send back the actions array, filtered
		// @param array $actions the actions that trigger the purge event
		return apply_filters( 'ccache_http_purge_events', $actions );
	}

	/**
	 * Events that have no post IDs
	 * These are when a full purge is triggered
	 *
	 * @since 3.9
	 * @access protected
	 */
	protected function getNoIDEvents() {

		// Define registered purge events
		$actions = array(
			'switch_theme',						// After a theme is changed
			'autoptimize_action_cachepurged,'	// Compat with https://wordpress.org/plugins/autoptimize/
		);

		// send back the actions array, filtered
		// @param array $actions the actions that trigger the purge event
		// DEVELOPERS! USE THIS SPARINGLY! YOU'RE A GREAT BIG ?? IF YOU USE IT FLAGRANTLY
		// Remember to add your action to this AND ccache_http_purge_events due to shenanigans
		return apply_filters( 'ccache_http_purge_events_full', $actions );
	}

	/**
	 * Execute Purge
	 * Run the purge command for the URLs. Calls $this->purgeUrl for each URL
	 *
	 * @since 1.0
	 * @access protected
	 */
	public function blizexecutePurge() {
		$purgeUrls = array_unique( $this->purgeUrls );

		// Get correct http protocol from Blizhost CloudCache
		$http_proto = (!empty($_SERVER['HTTPS'])) ? "https://" : "http://";
		
		if ( empty($purgeUrls) ) {
			if ( isset($_GET['bliz_flush_all']) && check_admin_referer('bliz-flush-all') ) {
				$this->blizpurgeUrl( $http_proto . $_SERVER['HTTP_HOST'] .'/?bliz-regex' ); // Clears the main domain
			}
		} else {
			foreach($purgeUrls as $url) {
				$this->blizpurgeUrl($url);
			}
		}
	}

	/**
	 * Purge URL
	 * Parse the URL for proxy proxies
	 *
	 * @since 1.0
	 * @param array $url the url to be purged
	 * @access protected
	 */
	public function blizpurgeUrl($url) {
		$p = parse_url($url);
		
		// Do not send requests if the site is not hosted at Blizhost
		if(strpos($this->http_x_server, 'blizhost') === false){
			return FALSE;
		}

		if ( isset($p['query']) && ( $p['query'] == 'bliz-regex' ) ) {
			$pregex = '.*';
			$ccache_x_purgemethod = 'regex';
		} else {
			$pregex = '';
			$ccache_x_purgemethod = 'default';
		}

		if (isset($p['path'] ) ) {
			$path = $p['path'];
		} else {
			$path = '';
		}

		$blizpurgeme = $path.$pregex;
		
		if (!empty($p['query']) && $p['query'] != 'bliz-regex') {
			$blizpurgeme .= '?' . $p['query'];
		}

		// Get customer informations and API key or generates a new one
		$dir = $_SERVER['DOCUMENT_ROOT'];
		$dir_exp = explode("/", $dir);

		$directory = $dir_exp[1];
		$user = $dir_exp[2];

		$du_api = '/' . $directory . '/' . $user . '/.blizhost-api';
		if (@file_exists($du_api)) {
			$api_key = @file_get_contents($du_api,"r");
		}else{
			$api_uniqid = md5(uniqid(rand(), true));
			if(@file_put_contents($du_api, $api_uniqid)){
				$api_key = $api_uniqid;
			}
		}
		
		// Get plugin version
		if (is_admin()) {
			$plugin_data = get_plugin_data( __FILE__ );
			$plugin_version = $plugin_data['Version'];
		}else{
			$plugin_version = $this->p_version;
		}
		
		// Blizhost cleanup CloudCache after check if the API key and others informations are correct
		wp_remote_request('http://cloudcache-api.blizhost.com.br/purge.php', array(		   
			'method' => 'POST',
			'body' =>  array( 
				'url' => $blizpurgeme, 
				'method' => $ccache_x_purgemethod,
				'user' => $user,
				'host' => $p['host'],
				'api_key' => $api_key,
				'plugin_version' => $plugin_version
			)
		));
		
		do_action('after_purge_url', $url, $blizpurgeme, $response);
	}

	/**
	 * Purge - No IDs
	 * Flush the whole cache
	 *
	 * @since 3.9
	 * @access private
	 */
	public function purgeNoID( $postId ) {
		$listofurls = array();

		array_push($listofurls, $this->the_home_url().'/?bliz-regex' );
	
		// Now flush all the URLs we've collected provided the array isn't empty
		if ( !empty($listofurls) ) {
			foreach ($listofurls as $url) {
				array_push($this->purgeUrls, $url ) ;
			}
		}
	}

	/**
	 * Purge Post
	 * Flush the post
	 *
	 * @since 1.0
	 * @param array $postId the ID of the post to be purged
	 * @access public
	 */
	public function blizpurgePost( $postId ) {
		
		// If this is a valid post we want to purge the post, 
		// the home page and any associated tags and categories

		$validPostStatus = array("publish", "trash");
		$thisPostStatus  = get_post_status($postId);

		// array to collect all our URLs
		$listofurls = array();

		if( get_permalink($postId) == true && in_array($thisPostStatus, $validPostStatus) ) {
			// If this is a post with a permalink AND it's published or trashed, 
			// we're going to add a ton of things to flush.
			
			// Category purge based on Donnacha's work in WP Super Cache
			$categories = get_the_category($postId);
			if ( $categories ) {
				foreach ($categories as $cat) {
					array_push($listofurls, get_category_link( $cat->term_id ) );
				}
			}
			// Tag purge based on Donnacha's work in WP Super Cache
			$tags = get_the_tags($postId);
			if ( $tags ) {
				foreach ($tags as $tag) {
					array_push($listofurls, get_tag_link( $tag->term_id ) );
				}
			}

			// Author URL
			array_push($listofurls,
				get_author_posts_url( get_post_field( 'post_author', $postId ) ),
				get_author_feed_link( get_post_field( 'post_author', $postId ) )
			);

			// Archives and their feeds
			$archiveurls = array();
			if ( get_post_type_archive_link( get_post_type( $postId ) ) == true ) {
				array_push($listofurls,
					get_post_type_archive_link( get_post_type( $postId ) ),
					get_post_type_archive_feed_link( get_post_type( $postId ) )
				);
			}

			// Post URL
			array_push( $listofurls, get_permalink($postId) );

			// Also clean URL for trashed post.
			if ( $thisPostStatus == "trash" ) {
				$trashpost = get_permalink($postId);
				$trashpost = str_replace("__trashed", "", $trashpost);
				array_push( $listofurls, $trashpost, $trashpost.'feed/' );
			}
			
			// Add in AMP permalink if Automattic's AMP is installed
			if ( function_exists('amp_get_permalink') ) {
				array_push( $listofurls, amp_get_permalink($postId) );
			}
			
			// Regular AMP url for posts
			array_push( $listofurls, get_permalink($postId).'amp/' );
			
			// Feeds
			array_push($listofurls,
				get_bloginfo_rss('rdf_url') ,
				get_bloginfo_rss('rss_url') ,
				get_bloginfo_rss('rss2_url'),
				get_bloginfo_rss('atom_url'),
				get_bloginfo_rss('comments_rss2_url'),
				get_post_comments_feed_link($postId)
			);
			
			// Sitemaps - Blizhost
			array_push($listofurls, site_url() .'/sitemap?bliz-regex' );
			
			// Pagination - Blizhost
			array_push($listofurls, site_url() .'/page/?bliz-regex' );
			
			// Home Page and (if used) posts page
			array_push( $listofurls, $this->the_home_url().'/' );
			if ( get_option('show_on_front') == 'page' ) {
				// Ensure we have a page_for_posts setting to avoid empty URL
				if ( get_option('page_for_posts') ) {
					array_push( $listofurls, get_permalink( get_option('page_for_posts') ) );
				}
			}
		} else {
			// We're not sure how we got here, but bail instead of processing anything else.
			return;
		}
		
		// Now flush all the URLs we've collected provided the array isn't empty
		if ( !empty($listofurls) ) {
			foreach ($listofurls as $url) {
				array_push($this->purgeUrls, $url ) ;
			}
		}

		// Filter to add or remove urls to the array of purged urls
		// @param array $purgeUrls the urls (paths) to be purged
		// @param int $postId the id of the new/edited post
		$this->purgeUrls = apply_filters( 'bliz_purge_urls', $this->purgeUrls, $postId );
	}

}

$purger = new BlizCloudCachePurger();

/**
 * Purge CloudCache via WP-CLI
 *
 * @since 3.8
 */
if ( defined('WP_CLI') && WP_CLI ) {
	include( 'wp-cli.php' );
}