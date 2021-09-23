<?php

class SureCache_AutoPurge_Admin {
	const NAME = 'surecache-autopurge';

	private $plugin = null;

	public function __construct() {
		$this->plugin = SureCache_AutoPurge::getInstance();
		add_action( 'admin_bar_menu', array( $this, 'add_purge_button' ), PHP_INT_MAX );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_scripts' ) );
		add_action( 'admin_print_styles', array( $this, 'add_styles' ) );
		add_action( 'admin_bar_menu', array( $this, 'add_purge_button' ), PHP_INT_MAX );
		add_action( 'wp_ajax_surecache_autopurge_manual_purge', array( $this, 'purge' ) );
	}

	public function add_scripts() {
		wp_register_script( self::NAME, plugins_url( 'assets/js/admin.js', dirname( __FILE__ ) ) );
		wp_enqueue_script( self::NAME );
	}

	public function add_styles() {
		wp_register_style( self::NAME, plugins_url( 'assets/css/admin.css', dirname( __FILE__ ) ) );
		wp_enqueue_style( self::NAME );
	}

	public function add_purge_button( $wp_admin_bar ) {
		$wp_admin_bar->add_node( array(
			'id'    => 'surecache-autopurge-manual-purge',
			'title' => __( 'Purge Server-Side Cache', 'surecache-autopurge' ),
			'href'  => 'javascript:;',
			'meta'  => array( 'title' => __( 'Purge Server-Side Cache', 'surecache-autopurge' ) )
		) );

		add_action( 'admin_footer', array( $this, 'embed_wp_nonce' ) );
		add_action( 'admin_notices', array( $this, 'embed_admin_notices' ) );
	}

	public function embed_wp_nonce() {
		echo '<span id="' . self::NAME . '-purge-wp-nonce' . '" class="hidden">'
		     . wp_create_nonce( self::NAME . '-purge-wp-nonce' )
		     . '</span>';
	}

	public function embed_admin_notices() {
		echo '<div id="' . self::NAME . '-admin-notices' . '" class="hidden notice"></div>';
	}

	public function purge() {
		if ( wp_verify_nonce( $_POST['wp_nonce'], self::NAME . '-purge-wp-nonce' ) and $this->plugin->addAll()->commit() ) {
			echo json_encode( array(
				'success' => true,
				'message' => __( 'The Server-Side Cache was purged successfully.', 'surecache-autopurge' )
			) );

		} else {
			echo json_encode( array(
				'success' => false,
				'message' => __( 'The Server-Side Cache could not be purged!', 'surecache-autopurge' )
			) );
		}

		exit();
	}
}
