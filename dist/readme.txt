=== The Cache Purger ===
Contributors: kevp75
Donate link: https://paypal.me/kevinpirnie
Tags: remote cache, caching, purge cache, cache purging
Requires at least: 5.5
Tested up to: 6.0
Requires PHP: 7.4
Stable tag: 1.1.01
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Server-side cache purging plugin incorporating a slew of plugin, hosting, and general server based cache purges.

== Description ==

This plugin attempts to purge all server-side caching methods.  

This includes the most common caching plugins, some hosting based caches, most server based caches, built-in Wordpress object caches, 
and even simple file based caches.

== Features ==

Built in automatic cache purging for the following caches:

* Plugins
    * SiteGround Optimizer, Nginx Helper, LiteSpeed Cache, Cachify, Autoptimize, Fast Velocity Minify, WP Rocket, Swift Performance,
      Comet Cache, Hummingbird, WP Fastest Cache, WP Super Cache, W3 Total Cache, Hyper Cache, WP Optimize, Cache Enabler
    * Check the changelog below for new additions...
* Hosting / CDN
    * WPEngine, Kinsta, GoDaddy Managed Wordpress, Pantheon, CloudFlare, Sucuri, RunCloud
        * Some of these are dependant on separate plugins.  Please see your provider if it is necessary, or already included
        * Check the changelog below for new additions...
* Server Based
    * PHP FPM, Zend Opcache, APC and APCU, WinCache, Pagespeed Module, nGinx, Static File Caches, Redis, Memcache, Memcached
    * Check the changelog below for new additions...
* Wordpress Built-In object caching, and persistent object caching

Purges are configurable in the settings, and include the following saves/updates/trashes:
* Posts, Pages, Custom Post Types, Categories, Taxonomies, Widgets, Menus, Plugins, Updates, Settings & Options, 
  GravityForms (if installed and activated), Advanced Custom Fields (if installed and activated), 
  WooCommerce Settings (if installed and activated)

== Installation ==

1. Download the plugin, unzip it, and upload to your sites `/wp-content/plugins/` directory
    1. You can also upload it directly to your Plugins admin
	2. Or install it from the Wordpress Plugin Repository
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Why would I need this plugin? =

Do you need to manually clear your server-side caches everytime you modify your site?  If yes, then this is for you.  It automates this process for you.

Even if you don't, there are still some server based caches that need to be purged that this plugin attempts to take care of.

== Screenshots ==

1. Settings 1
2. Settings 2
3. Settings 3 - Showing GravityForms Activated
4. Settings 4 - Showing ACF Activated 
5. Logged Purged Object

== Changelog ==

= 1.1.01 =
* Test: Up to 6.0 compliant
* Test: Up to PHP 8.1 Compliant
* New: Plugin Icon =)
* Updated: Settings Field Framework
  * Added: Number field “min”, “max”, “step” options.
  * Updated: Google Web Fonts array added new fonts.
  * Updated: JS libraries (codemirror, leaflet, etc).
  * Improved: Group field “custom title and prefix” option (samples added).
  * Improved: Some JS and CSS coding.


= 1.0.27 =
* Feature: WP Cron based cache purging
  * including setting custom schedules
* Feature: CLI based purging
  * Command: `wp the_cache purge` 
* Fix: Sucuri purge logging
* Fix: Network activation check
* Fix: Master Purge showing on network admin
* Fix: Pagespeed purge performance issue
* Fix: OOM issue on WooCommerce site with lots of products
* Fix: Some hosting may require `opcache.restrict_api` to be configured. Disabled the warning on 
  servers where it is not
* Raise: PHP version requirement, WP tested up to, and minimum required WP version.
* Add: WP REST Cache Purge

= 0.8.88 =
* Feature: Translation ready
  * Text Domain: the-cache-purger
* Feature: now compiling everything
  * Put a check in to see if we're debugging or not to properly queue the necessary assets
* Fix: couple text items were not ready

= 0.8.09 =
* Add: Elementor CSS Auto-Regenerate
* Add: Divi Cache Purge
* Update: W3 Total Cache Clearing
* Update: WP Super Cache Clearing
* Update: Hummingbird Cache Clearing
* Update: Cache Enabler Clearing
* Update: Lightspeed Cache Clearing
* Update: Kinsta Cache Clearing
* Update: Autoptimize Cache Clearing
* Add: Siteground Hosting Cache Purge
* Add: Bluehost Hosting Cache Purge
* Add: Breezeway Hosting Cache Purge
* Update: WP Optimize Cache Clearing

= 0.7.22 =
* Fix: Strict typing for non-nullable object

= 0.7.21 =
* Fix: Issue on first config if no exceptions are selected

= 0.7.16 =
* Update: settings rewrite
  * Was conflicting with another plugin
* UI Fix: convert selectables to "chosen"
* Feature: Add API/Server settings
  * Implement: Remote Redis Clearing
  * Implement: Remote Memcache Clearing
  * Implement: Remote Memcached Clearing
  * Implement: Direct Cloudflare Clearing
  * Implement: Direct Sucuri Clearing

= 0.6.02 =
* Fixed: Admin bar menu show in for non-admin capabilities.
* Improved: Usage anywhere framework fields.
* Updated: JS libraries (codemirror, leaflet, etc).
* Improved: Some js and css coding.

= 0.5.57 =
* Verify: Core 5.9 Compatibility
* Fix: Array index set check
* Feature: Implement some new hooks
  * NOTE: The actions fire off before and after each related section.  They should be self-explanatory, so I will just list them all:
    * `tcp_pre_purge`, `tcp_post_purge`, `tcp_pre_hosting_purge`, `tcp_post_hosting_purge`, `tcp_pre_plugin_purge`, 
    `tcp_post_plugin_purge`, `tcp_pre_wp_purge`, `tcp_post_wp_purge`, `tcp_pre_php_purge`, `tcp_post_php_purge`,
    `tcp_pre_pagespeed_purge`, `tcp_post_pagespeed_purge`, `tcp_pre_nginx_purge`, `tcp_post_nginx_purge`, `tcp_pre_file_purge`,
    `tcp_post_file_purge`

= 0.4.37 =
* Fix: Tweak admin permissions
    * found an issue where a subsite admin could not administer
      the settings if the super-admin disabled Plugins in settings

= 0.4.15 = 
* Update: Settings
* New: Manual Cache Purge

= 0.3.98 =
* Build purging methods
* Build out settings
* Build out documentation
* Build out export/import settings

= 0.1.01 =
* INITIAL
