<?php
/*
 * Plugin Name: Server-Side Cache AutoPurge
 * Description: Server-Side Cache Integration for WordPress.
 * Version: 1.0.1
 * Text Domain: surecache-autopurge
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

require_once __DIR__ . '/includes/purge-queue.php';

class SureCache_AutoPurge {
	const NAME = 'surecache-autopurge';

	private static $instance = null;
	private $queue = null;
	private $admin = null;

	/**
	 * Note that a bunch of these events may well be redundant and in all
	 * likelihood there will be duplicate URLs in the purge list.
	 *
	 * We take care of this on purge by removing duplicates.
	 */
	private $events = array(
		'edit_post'                   => 'addPost',
		'save_post'                   => 'addPost',
		'post_updated'                => 'addPost',
		'deleted_post'                => 'addPost',
		'trashed_post'                => 'addPost',
		'wp_trash_post'               => 'addPost',
		'add_attachment'              => 'addPost',
		'edit_attachment'             => 'addPost',
		'attachment_updated'          => 'addPost',
		'publish_phone'               => 'addPost',
		'clean_post_cache'            => 'addPost',
		'pingback_post'               => 'addComment',
		'comment_post'                => 'addComment',
		'edit_comment'                => 'addComment',
		'delete_comment'              => 'addComment',
		'wp_insert_comment'           => 'addComment',
		'wp_set_comment_status'       => 'addComment',
		'trackback_post'              => 'addComment', // trackback_id is a comment ID
		'wp_update_nav_menu'          => 'addAll',
		'switch_theme'                => 'addAll',
		'permalink_structure_changed' => 'addAll',
	);

	private function __construct() {
		$this->queue = new SureCache_AutoPurge_PurgeQueue();
		add_action( 'init', array( $this, 'init' ) );
	}

	public static function getInstance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function init() {
		load_plugin_textdomain( 'surecache-autopurge', false, 'surecache-autopurge/languages' );
		if ( is_admin() ) {
			require_once __DIR__ . '/includes/admin.php';
			$this->admin = new SureCache_AutoPurge_Admin();
		}

		foreach ( $this->events as $event => $callback ) {
			add_action( $event, array( $this, $callback ) );
		}

		// Special cases
		add_action( 'transition_post_status', array( $this, 'addTransitionPostStatus' ), PHP_INT_MAX, 3 );
		add_action( 'transition_comment_status', array( $this, 'addTransitionCommentStatus' ), PHP_INT_MAX, 3 );

		if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
			add_action( 'shutdown', array( $this, 'commit' ) );
		}
	}

	public function addTransitionPostStatus( $newStatus, $oldStatus, WP_Post $post ) {
		return $this->addPost( $post->ID );
	}

	/**
	 * Based on purge_post() from varnish-http-purge and vcaching plugins.
	 *
	 * Comments from the original implementations have been retained.
	 *
	 * Researching this part is actually the heavy lifting required for this
	 * plugin, so credit for that goes to wp-super-cache, varnish-http-purge,
	 * and vaching.
	 *
	 * @param int $postID
	 *
	 * @return $this
	 */
	public function addPost( $postID ) {
		$post = get_post( $postID );
		if ( ! $post instanceof WP_Post ) {
			return $this;
		}

		$post_type   = get_post_type( $post );
		$post_status = get_post_status( $post );

		if ( ! in_array( $post_status, array( 'publish', 'trash' ) ) ) {
			return $this;
		}

		$permalink = get_permalink( $post );

		if ( ! $permalink ) {
			return $this;
		}

		$this->queue->addURL( $permalink );

		/**
		 * Determine the route for the rest API
		 * This will need to be revisted if WP updates the version.
		 * Future me: Consider an array? 4.7-?? use v2, and then adapt from there?
		 */
		if ( version_compare( get_bloginfo( 'version' ), '4.7', '>=' ) ) {
			$rest_api_route = 'wp/v2';
		}

		/**
		 * JSON API Permalink for the post based on type
		 * We only want to do this if the rest_base exists
		 * But we apparently have to force it for posts and pages (seriously?)
		 */
		if ( isset( $rest_api_route ) ) {
			$post_type_object = get_post_type_object( $post_type );
			$rest_permalink   = false;
			if ( isset( $post_type_object->rest_base ) ) {
				$rest_permalink = get_rest_url() . $rest_api_route . '/' . $post_type_object->rest_base . '/' . $post->ID . '/';
			} elseif ( 'post' === $post_type ) {
				$rest_permalink = get_rest_url() . $rest_api_route . '/posts/' . $post->ID . '/';
			} elseif ( 'page' === $post_type ) {
				$rest_permalink = get_rest_url() . $rest_api_route . '/pages/' . $post->ID . '/';
			}

			if ( $rest_permalink ) {
				$this->queue->addURL( $rest_permalink );
			}
		}

		// Add in AMP permalink for offical WP AMP plugin:
		// https://wordpress.org/plugins/amp/
		if ( function_exists( 'amp_get_permalink' ) ) {
			$this->queue->addURL( amp_get_permalink( $post->ID ) );
		}

		// Regular AMP url for posts if ant of the following are active:
		// https://wordpress.org/plugins/accelerated-mobile-pages/
		if ( defined( 'AMPFORWP_AMP_QUERY_VAR' ) ) {
			$this->queue->addURL( $permalink . 'amp/' );
		}

		// Also clean URL for trashed post.
		if ( 'trash' === $post_status ) {
			$trashpost = str_replace( '__trashed', '', $permalink );
			$this->queue->addURL( $trashpost );
			$this->queue->addURL( $trashpost . 'feed/' );
		}

		// If the post is a post, we have more things to flush
		// Pages and Woo Things don't need all this.
		if ( $post_type && 'post' === $post_type ) {
			// Author URLs:
			$author_id = get_post_field( 'post_author', $post->ID );
			$this->queue->addURL( get_author_posts_url( $author_id ) );
			$this->queue->addURL( get_author_feed_link( $author_id ) );
			isset( $rest_api_route ) && $this->queue->addURL( get_rest_url() . $rest_api_route . '/users/' . $author_id . '/' );

			// Feeds:
			$this->queue->addURL( get_bloginfo_rss( 'rdf_url' ) );
			$this->queue->addURL( get_bloginfo_rss( 'rss_url' ) );
			$this->queue->addURL( get_bloginfo_rss( 'rss2_url' ) );
			$this->queue->addURL( get_bloginfo_rss( 'atom_url' ) );
			$this->queue->addURL( get_bloginfo_rss( 'comments_rss2_url' ) );
			$this->queue->addURL( get_post_comments_feed_link( $post->ID ) );
		}

		// Author URL
		$this->queue->addURL( get_author_posts_url( get_post_field( 'post_author', $post->ID ) ) );
		$this->queue->addURL( get_author_feed_link( get_post_field( 'post_author', $post->ID ) ) );

		// Category purge based on Donnacha's work in WP Super Cache.
		$categories = get_the_category( $post->ID );
		if ( $categories ) {
			foreach ( $categories as $cat ) {
				$this->queue->addURL( get_category_link( $cat->term_id ) );
				isset( $rest_api_route ) && $this->queue->addURL( get_rest_url() . $rest_api_route . '/categories/' . $cat->term_id . '/' );
			}
		}

		// Tag purge based on Donnacha's work in WP Super Cache.
		$tags = get_the_tags( $post->ID );
		if ( $tags ) {
			$tag_base = get_site_option( 'tag_base' );
			if ( '' === $tag_base ) {
				$tag_base = '/tag/';
			}
			foreach ( $tags as $tag ) {
				$this->queue->addURL( get_tag_link( $tag->term_id ) );
				isset( $rest_api_route ) && $this->queue->addURL( get_rest_url() . $rest_api_route . $tag_base . $tag->term_id . '/' );
			}
		}

		// Custom Taxonomies: Only show if the taxonomy is public.
		$taxonomies = get_post_taxonomies( $post->ID );
		if ( $taxonomies ) {
			foreach ( $taxonomies as $taxonomy ) {
				$features = (array) get_taxonomy( $taxonomy );
				if ( ! $features['public'] ) {
					continue;
				}
				$terms = wp_get_post_terms( $post->ID, $taxonomy );
				foreach ( $terms as $term ) {
					$this->queue->addURL( get_term_link( $term ) );
					isset( $rest_api_route ) && $this->queue->addURL( get_rest_url() . $rest_api_route . '/' . $term->taxonomy . '/' . $term->slug . '/' );
				}
			}
		}

		// Home Page and (if used) posts page
		$this->queue->addURL( home_url( '/' ) );
		$this->queue->addURL( get_rest_url() );
		// Ensure we have a page_for_posts setting to avoid empty URL.
		if ( 'page' === get_site_option( 'show_on_front' ) and get_site_option( 'page_for_posts' ) ) {
			$this->queue->addURL( get_permalink( get_site_option( 'page_for_posts' ) ) );
		}

		// Archives and their feeds.
		if ( $post_type && ! in_array( $post_type, array( 'post', 'page' ), true ) ) {
			$this->queue->addURL( get_post_type_archive_link( $post_type ) );
			$this->queue->addURL( get_post_type_archive_feed_link( $post_type ) );
			// Need to add in JSON?
		}

		return $this;
	}

	public function addTransitionCommentStatus( $newStatus, $oldStatus, WP_Comment $comment ) {
		return $this->addPost( $comment->comment_post_ID );
	}

	public function addComment( $commentID ) {
		$comment = get_comment( $commentID );

		return $this->addPost( $comment->comment_post_ID );
	}

	public function addAll() {
		$this->queue->addURL( home_url() . '/.*' );

		return $this;
	}

	public function commit() {
		return $this->queue->commit();
	}
}

SureCache_AutoPurge::getInstance();

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once __DIR__ . '/includes/wp-cli.php';
}
