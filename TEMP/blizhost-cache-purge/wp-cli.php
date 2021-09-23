<?php

if (!defined('ABSPATH')) {
    die();
}

// Bail if WP-CLI is not present
if ( !defined( 'WP_CLI' ) ) return;

/**
 * Purges CloudCache
 */
class WP_CLI_BlizCCache_Purge_Command extends WP_CLI_Command {

	private $wildcard = false;

	public function __construct() {
		$this->ccache_purge = new BlizCCachePurger();
	}
	
    /**
     * Forces a full CloudCache Purge of the entire site (provided
     * regex is supported).
     * 
     * ## EXAMPLES
     * 
     *		wp ccache purge
     *
     *		wp ccache purge http://example.com/wp-content/themes/twentyeleventy/style.css
     *
	 *		wp ccache purge "/wp-content/themes/twentysixty/style.css"
	 *
     *		wp ccache purge http://example.com/wp-content/themes/ --wildcard
     *
     *		wp ccache purge "/wp-content/themes/" --wildcard
     *
     */
	
	function purge( $args , $assoc_args ) {	
		
		$wp_version = get_bloginfo( 'version' );
		$cli_version = WP_CLI_VERSION;
		
		// Set the URL/path
		if ( !empty($args) ) { list( $url ) = $args; }

		// If wildcard is set, or the URL argument is empty
		// then treat this as a full purge
		$pregex = $wild = '';
		if ( isset( $assoc_args['wildcard'] ) || empty($url) ) {
			$pregex = '/?bliz-regex';
			$wild = ".*";
		}

		wp_create_nonce('ccache-purge-cli');

		// Make sure the URL is a URL:
		if ( !empty($url) ) {
			$url = $this->ccache_purge->the_home_url() . esc_url( $url );
		} else {
			$url = $this->ccache_purge->the_home_url();
		}
		
		if ( version_compare( $wp_version, '4.6', '>=' ) && ( version_compare( $cli_version, '0.25.0', '<' ) || version_compare( $cli_version, '0.25.0-alpha', 'eq' ) ) ) {
			
			WP_CLI::log( sprintf( 'This plugin does not work on WP 4.6 and up, unless WP-CLI is version 0.25.0 or greater. You\'re using WP-CLI %s and WordPress %s.', $cli_version, $wp_version ) );
			WP_CLI::log( 'To flush your cache, please run the following command:');
			WP_CLI::log( sprintf( '$ curl -X PURGE "%s"' , $url.$wild ) );
			WP_CLI::error( 'CloudCache must be purged manually.' );
		}

		$this->ccache_purge->purgeUrl( $url.$pregex );

		WP_CLI::success( 'The CloudCache was purged.' );
	}

}

WP_CLI::add_command( 'ccache', 'WP_CLI_BlizCCache_Purge_Command' );