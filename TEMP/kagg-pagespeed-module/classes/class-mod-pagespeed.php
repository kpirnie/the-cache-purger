<?php
/**
 * Mod_PageSpeed class file.
 *
 * @package kagg-pagespeed-module
 */

/**
 * Class Mod_PageSpeed.
 */
class Mod_PageSpeed {

	/**
	 * Plugin options.
	 *
	 * @var array
	 */
	private $options;

	/**
	 * Admin screen id.
	 */
	const SCREEN_ID = 'settings_page_mod-pagespeed';

	/**
	 * Option page.
	 */
	const PAGE = 'mod-pagespeed';

	/**
	 * Ajax action name.
	 */
	const ACTION = 'mod-pagespeed-action';

	/**
	 * Admin script handle.
	 */
	const HANDLE = 'mod-pagespeed-admin';

	/**
	 * Mod_PageSpeed constructor.
	 */
	public function __construct() {
		$this->options = get_option( 'mod_pagespeed_settings' );

		add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
		add_filter(
			'plugin_action_links_' . plugin_basename( MOD_PAGESPEED_FILE ),
			[ $this, 'add_settings_link' ],
			10,
			2
		);
		add_action( 'admin_init', [ $this, 'setup_fields' ] );

		add_action( 'plugins_loaded', [ $this, 'load_textdomain' ], 100 );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
		add_action( 'wp_ajax_mod_pagespeed', [ $this, 'ajax_action' ] );
		add_action( 'parse_request', [ $this, 'mod_pagespeed_arg' ], 20 );
	}

	/**
	 * Add settings page to the menu.
	 */
	public function add_settings_page() {
		$page_title = __( 'PageSpeed', 'kagg-pagespeed-module' );
		$menu_title = __( 'PageSpeed', 'kagg-pagespeed-module' );
		$capability = 'manage_options';
		$slug       = self::PAGE;
		$callback   = [ $this, 'mod_pagespeed_settings_page' ];
		$icon       = MOD_PAGESPEED_URL . '/images/icon-16x16.png';
		$icon       = '<img class="ps-menu-image" src="' . $icon . '">';
		$menu_title = $icon . '<span class="ps-menu-title">' . $menu_title . '</span>';
		add_options_page( $page_title, $menu_title, $capability, $slug, $callback );
	}

	/**
	 * Options page.
	 */
	public function mod_pagespeed_settings_page() {
		?>
		<div class="wrap">
			<h2 id="title">
				<?php
				// Admin panel title.
				echo( esc_html( __( 'PageSpeed Plugin Options', 'kagg-pagespeed-module' ) ) );
				?>
			</h2>

			<form action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" method="POST">
				<?php
				settings_fields( 'mod_pagespeed_group' ); // Hidden protection fields.
				do_settings_sections( self::PAGE ); // Sections with options.
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Setup options fields.
	 */
	public function setup_fields() {
		add_settings_section(
			'purge_section',
			__( 'Purge Cache', 'kagg-pagespeed-module' ),
			[ $this, 'mod_pagespeed_purge_section' ],
			self::PAGE
		);
		add_settings_section(
			'development_section',
			__( 'Development Mode', 'kagg-pagespeed-module' ),
			[ $this, 'mod_pagespeed_development_section' ],
			self::PAGE
		);
	}

	/**
	 * Purge Cache section.
	 */
	public function mod_pagespeed_purge_section() {
		$title       = __( 'Purge Styles', 'kagg-pagespeed-module' );
		$text        = __( 'Clear cached version of current WordPress theme style.css file.<br><br>This is useful when styles were changed.', 'kagg-pagespeed-module' );
		$button_text = __( 'Purge Styles', 'kagg-pagespeed-module' );
		$this->card_section( $title, $text, $button_text, 'purge_styles' );

		$title       = __( 'Purge Entire Cache', 'kagg-pagespeed-module' );
		$text        = __( 'Clear entire PageSpeed cache on site. This action fetches fresh versions of all pages, images, and scripts on your web site.<br><br>Please note that PageSpeed module will take some time to re-create cache after several page visits.', 'kagg-pagespeed-module' );
		$button_text = __( 'Purge Entire Cache', 'kagg-pagespeed-module' );
		$this->card_section( $title, $text, $button_text, 'purge_entire_cache' );
	}

	/**
	 * Output card option section
	 *
	 * @param string $title       Card title.
	 * @param string $text        Card text.
	 * @param string $button_text Button text.
	 * @param string $button_id   Button id.
	 */
	private function card_section( $title, $text, $button_text, $button_id ) {
		?>
		<section class="ps-card">
			<div class="ps-card-section">
				<div class="ps-card-content">
					<h3 class="ps-card-title"><?php echo esc_html( $title ); ?></h3>
					<p><?php echo wp_kses_post( wpautop( $text ) ); ?></p>
				</div>
				<div class="ps-card-control">
					<button id="<?php echo esc_attr( $button_id ); ?>" type="button" class="ps-btn">
						<?php echo esc_html( $button_text ); ?>
					</button>
				</div>
			</div>
		</section>
		<?php
	}

	/**
	 * Development Mode section.
	 */
	public function mod_pagespeed_development_section() {
		$title    = __( 'Development Mode', 'kagg-pagespeed-module' );
		$text     = __( 'When development mode is on, all PageSpeed cache is bypassed.<br><br>This is done by adding ?ModPagespeed=off agrument to every site url.', 'kagg-pagespeed-module' );
		$dev_mode = $this->options['dev_mode'];
		if ( 'true' === $dev_mode ) {
			$active  = 'active';
			$checked = 'checked=checked';
		} else {
			$active  = '';
			$checked = '';
		}
		?>
		<section class="ps-card">
			<div class="ps-card-section">
				<div class="ps-card-content">
					<h3 class="ps-card-title"><?php echo esc_html( $title ); ?></h3>
					<p><?php echo wp_kses_post( wpautop( $text ) ); ?></p>
				</div>
				<div class="ps-card-control">
					<label class="ps-toggle <?php echo esc_attr( $active ); ?>">
						<input id="dev_mode" type="checkbox" <?php echo esc_attr( $checked ); ?> class="ps-checkbox">
					</label>
				</div>
			</div>
		</section>
		<?php
		echo '<div id="ps-success"></div>';
		echo '<div id="ps-error"></div>';
	}

	/**
	 * Load plugin text domain.
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'kagg-pagespeed-module',
			false,
			dirname( plugin_basename( MOD_PAGESPEED_FILE ) ) . '/languages/'
		);
	}

	/**
	 * Enqueue plugin scripts.
	 */
	public function admin_enqueue_scripts() {
		if ( ! is_admin() ) {
			return;
		}

		wp_enqueue_style(
			self::HANDLE,
			MOD_PAGESPEED_URL . '/css/mod-pagespeed-admin.css',
			[],
			MOD_PAGESPEED_VERSION
		);

		if ( ! $this->is_options_screen() ) {
			return;
		}

		wp_enqueue_script(
			self::HANDLE,
			MOD_PAGESPEED_URL . '/js/mod-pagespeed-admin.js',
			[ 'jquery' ],
			MOD_PAGESPEED_VERSION,
			true
		);
		wp_localize_script(
			self::HANDLE,
			'mod_pagespeed',
			[
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( self::ACTION ),
			]
		);
	}

	/**
	 * Is current admin screen the plugin options screen.
	 *
	 * @return bool
	 */
	private function is_options_screen() {
		$current_screen = get_current_screen();

		return $current_screen && ( 'options' === $current_screen->id || self::SCREEN_ID === $current_screen->id );
	}

	/**
	 * Process ajax request.
	 */
	public function ajax_action() {
		if (
		! wp_verify_nonce(
			filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_STRING ),
			self::ACTION
		)
		) {
			wp_send_json_error( __( 'Bad nonce!', 'kagg-pagespeed-module' ) );
		}

		$id = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_STRING );

		switch ( $id ) {
			case 'purge_styles':
				$link = get_stylesheet_uri();
				$this->purge_link( $link );
				break;
			case 'purge_entire_cache':
				$link = site_url() . '/*.*';
				$this->purge_link( $link );
				break;
			case 'dev_mode':
				$checked = filter_input( INPUT_POST, 'checked', FILTER_SANITIZE_STRING );

				$this->options['dev_mode'] = $checked;
				if ( 'true' === $checked ) {
					$mode = __( 'Development mode is on', 'kagg-pagespeed-module' );
				} else {
					$mode = __( 'Development mode is off', 'kagg-pagespeed-module' );
				}
				update_option( 'mod_pagespeed_settings', $this->options );
				wp_send_json_success( $mode );
				break;
			default:
				wp_send_json_error( __( 'Unknown error', 'kagg-pagespeed-module' ) );
		}
	}

	/**
	 * Purge cache for $link.
	 *
	 * @param string $link a link to file or * to be purged.
	 */
	private function purge_link( $link ) {
		$pagespeed_headers = [ 'x-mod-pagespeed', 'x-page-speed' ];

		$result = wp_remote_request( site_url() );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_message() . ' - ' . $link );
		}

		foreach ( $pagespeed_headers as $header ) {
			$x_page_speed = wp_remote_retrieve_header( $result, $header );
			if ( '' !== $x_page_speed ) {
				break;
			}
		}

		if ( '' === $x_page_speed ) {
			wp_send_json_error(
				__( 'PageSpeed Module is not installed on your server. Plugin is useless.', 'kagg-pagespeed-module' )
			);
		}

		$cf        = false;
		$server_ip = '';
		$server    = wp_remote_retrieve_header( $result, 'server' );
		if ( false !== strpos( $server, 'cloudflare' ) ) {
			// Site is behind Cloudflare.
			$cf        = true;
			$server_ip = filter_input( INPUT_SERVER, 'SERVER_ADDR', FILTER_VALIDATE_IP );
		}

		// Normal request looks like: curl -X PURGE -L 'http://domain.org/*.*'
		// Request for Cloudflare looks like: curl -X PURGE -H 'host: domain.org' -L -k $server_ip/*.* .
		$url  = $link;
		$args = [
			'method'      => 'PURGE',
			'redirection' => 0,
		];
		if ( $cf ) {
			$link_array          = wp_parse_url( $link );
			$url                 = $link_array['scheme'] . '://' . $server_ip . $link_array['path'];
			$args['redirection'] = 5; // -L
			$args['sslverify']   = false; // -k
			$args['headers']     = 'host: ' . $link_array['host'];
		};

		$result = wp_remote_request( $url, $args );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_message() . ' - ' . $link );
		}
		if ( 200 === $result['response']['code'] ) {
			wp_send_json_success( esc_html( wp_remote_retrieve_body( $result ) ) . ' - ' . $link );
		} else {
			wp_send_json_error( wp_remote_retrieve_response_message( $result ) . ' - ' . $link );
		}
	}

	/**
	 * For any site url, add or remove ?ModPagespeed argument.
	 */
	public function mod_pagespeed_arg() {
		if ( wp_doing_ajax() || is_admin() || $this->is_rest() ) {
			return;
		}

		$dev_mode = $this->options['dev_mode'];

		// It is impossible to set nonce for any WordPress url.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		isset( $_GET['ModPagespeed'] ) ?
			$mod_pagespeed = filter_input( INPUT_GET, 'ModPagespeed', FILTER_SANITIZE_STRING ) :
			$mod_pagespeed = '';
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		if ( ( 'off' === $mod_pagespeed ) && ( 'true' === $dev_mode ) ) {
			return;
		}

		$url = null;

		if ( $mod_pagespeed ) {
			$url = remove_query_arg( 'ModPagespeed' );
		}

		if ( 'true' === $dev_mode ) {
			$url = add_query_arg( [ 'ModPagespeed' => 'off' ] );
		}

		if ( null !== $url ) {
			wp_safe_redirect( $url, 301 );
			exit();
		}
	}

	/**
	 * Check if it is a REST request
	 *
	 * @return bool
	 */
	private function is_rest() {
		return defined( 'REST_REQUEST' ) && REST_REQUEST;
	}

	/**
	 * Add link to plugin setting page on plugins page.
	 *
	 * @param array $links Plugin links.
	 *
	 * @return array|mixed Plugin links
	 */
	public function add_settings_link( $links ) {
		$action_links = [
			'settings' =>
				'<a href="' . admin_url( 'options-general.php?page=' . self::PAGE ) . '" aria-label="' .
				esc_attr__( 'View PageSpeed Module settings', 'kagg-pagespeed-module' ) . '">' .
				esc_html__( 'Settings', 'kagg-pagespeed-module' ) . '</a>',
		];

		return array_merge( $action_links, $links );
	}
}
