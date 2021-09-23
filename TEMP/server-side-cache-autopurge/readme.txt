=== Server-Side Cache AutoPurge ===
Contributors: suresupport
Tags: cache, caching, fast, flush, purge, wp-cache, performance
Requires at least: 4.8
Tested up to: 5.8
Requires PHP: 5.4
Stable tag: 1.0.1
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Purge server-side cache automatically after making website changes. Optimized for servers managed by SureSupport.

== Description ==

This plugin automatically purges the server-side cache after you make a website change such as updating a post.

The automatic purge functionality works for websites hosted on servers managed by SureSupport and using the "Server-side Caching" feature available in the hosting Control Panel.

== Installation ==

You install this plugin just as any other WordPress plugin.

== Configuration ==

The plugin does not require any additional configuration. It works out of the box. Just make sure you have the "Server-side caching" feature enabled in your hosting Control Panel.

= WP-CLI =

You can clear the server-side cache with the following WP-CLI command:

`wp surecache purge`

== Frequently Asked Questions ==

= Can I enable/disable server-side caching using the plugin? =

No. You can do this only via your hosting Control Panel > Server-side caching.

= Can I manually purge the server-side cache using the plugin? =

Yes, you can. When the plugin is installed and activated, you will see a "Purge Server-Side Cache" button at the top left side of the Dashboard. Clicking it will purge the server-side cache.

You can also purge the cache with the following WP-CLI command:

`wp surecache purge`

= I am experiencing issues with the plugin. What do I do? =

You can try to resolve the problem by purging the cache, deactivating the plugin, or disabling server-side caching from your Control Panel.

== Changelog ==

= 1.0 =
* Initial release.
