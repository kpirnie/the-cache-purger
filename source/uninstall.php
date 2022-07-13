<?php
/** 
 * Uninstall
 * 
 * Process the uninstalling of this plugin
 * 
 * @since 7.4
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package The Cache Purger
 * 
*/

// make sure we're actually supposed to be doing this
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) || ! WP_UNINSTALL_PLUGIN ||
	dirname( WP_UNINSTALL_PLUGIN ) != dirname( plugin_basename( __FILE__ ) ) ) {
	exit;
}

// remove our settings
unregister_setting( 'kpcp_settings', 'kpcp_settings' );

// delete the option
delete_option( 'kpcp_settings' );
