=== The Cache Purger ===
Contributors: kevp75
Donate link: https://paypal.me/kevinpirnie
Tags: remote cache, caching, purge cache, cache purging
Requires at least: 5.0
Tested up to: 5.8.1
Requires PHP: 7.3
Stable tag: 0.3.98
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
* Hosting / CDN
    * WPEngine, Kinsta, GoDaddy Managed Wordpress, Pantheon, CloudFlare, Sucuri, RunCloud
        * Some of these are dependant on separate plugins.  Please see your provider if it is necessary, or already included
* Server Based
    * PHP FPM, Zend Opcache, APC and APCU, WinCache, Pagespeed Module, nGinx, Static File Caches
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

Even if you don't, there are still some server based caches that need to be purged that this plugin takes care of.

== Screenshots ==
1. Settings 1
2. Settings 2
3. Settings 3 - Showing GravityForms Activated
4. Settings 4 - Showing ACF Activated
5. Documentation
6. Logged Purged Object

== Changelog ==
= 0.3.98 =
Build purging methods
Build out settings
Build out documentation
Build out export/import settings

= 0.1.01 =
* INITIAL
