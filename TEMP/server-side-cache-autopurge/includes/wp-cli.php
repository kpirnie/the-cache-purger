<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( ! defined( 'WP_CLI' ) ) {
	return;
}

/**
 * Provides functionality to manage the Server-Side Cache feature.
 */
class WP_CLI_SureCache_AutoPurge_Command extends WP_CLI_Command {
	private $plugin = null;

	public function __construct() {
		$this->plugin = SureCache_AutoPurge::getInstance();
	}

	/**
	 * Purges the Server-Side Cache.
	 *
	 * This command can be used to purge the contents of the Server-Side Cache.
	 *
	 * @param $argv
	 */
	public function purge( $argv ) {
		if ( $this->plugin->addAll()->commit() ) {
			WP_CLI::success( __( 'The Server-Side Cache was purged successfully.', 'surecache-autopurge' ) );

		} else {
			WP_CLI::error( __( 'The Server-Side Cache could not be purged!', 'surecache-autopurge' ) );
		}
	}
}

WP_CLI::add_command( 'surecache', 'WP_CLI_SureCache_AutoPurge_Command' );
