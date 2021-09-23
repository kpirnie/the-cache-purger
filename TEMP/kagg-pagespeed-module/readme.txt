=== PageSpeed Module ===
Contributors: kaggdesign
Donate link: https://kagg.eu/en/
Tags: PageSpeed Module, Mod PageSpeed, mod_pagespeed, Apache, Nginx, cache
Requires at least: 4.4
Tested up to: 5.7
Requires PHP: 5.6
Stable tag: 1.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

PageSpeed Module plugin supports WordPress installation under Apache or Nginx with PageSpeed Module. PageSpeed Module is an open-source modules for Apache and Nginx created by Google to help Make the Web Faster by rewriting web pages to reduce latency and bandwidth.

== Description ==

PageSpeed Module plugin allows purge caches created by Apache or Nginx Module and turn on development mode for WordPress site, bypassing PageSpeed cache.

Plugin has options page in the site console, with relevant buttons and controls. Please see screenshots.

Plugin requires PageSpeed Module to be installed with your Apache or Nginx web server. If PageSpeed Module is not installed, plugin does nothing.

== Installation ==

= Minimum Requirements =

* PHP version 5.6 or greater (PHP 7.0 or greater is recommended)
* MySQL version 5.0 or greater (MySQL 5.6 or greater is recommended)
* PageSpeed Module for Apache or Nginx
* In Apache config, the following directives must present:
ModPagespeedEnableCachePurge on
ModPagespeedPurgeMethod PURGE
* In Nginx config, the following directives must present:
pagespeed EnableCachePurge on;
pagespeed PurgeMethod PURGE;

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t need to leave your web browser. To do an automatic install of PageSpeed Module plugin, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type “PageSpeed Module” and click Search Plugins. Once you’ve found our plugin you can view details about it such as the point release, rating and description. Most importantly of course, you can install it by simply clicking “Install Now”.

= Manual installation =

The manual installation method involves downloading our plugin and uploading it to your webserver via your favourite FTP application. The WordPress codex contains [instructions on how to do this here](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

= Updating =

Automatic updates should work like a charm; as always though, ensure you backup your site just in case.

== Frequently Asked Questions ==

= Where can I get support or talk to other users? =

If you get stuck, you can ask for help in the [PageSpeed Module Plugin Forum](https://wordpress.org/support/plugin/pagespeed-module).

== Screenshots ==

1. The PageSpeed Module settings panel.

== Changelog ==

= 1.4 =
* Tested with WordPress 5.7

= 1.3.1 =
* Fixed bug with REST requests in Development mode.

= 1.3 =
* Tested with WordPress 5.6
* Admin scripts and styles are launched on the plugin settings page only.

= 1.2 =
* Tested with WordPress 5.5

= 1.1.6 =
* Tested with WordPress 5.4

= 1.1.5 =
* Tested with WordPress 5.2
* Minimal php version bumped up to 5.6

= 1.1.4 =
* Fixed bug with some Apache servers.
* Tested with WordPress 5.2

= 1.1.3 =
* Tested with WordPress 5.1

= 1.1.2 =
* Tested with WordPress 5.0

= 1.1.1 =
* Fixed format of PURGE request for Cloudflare

= 1.1 =
* Added detection if PageSpeed Module is installed on server
* Added Cloudflare support
* Added settings link on plugin page

= 1.0.1 =
* Translation update.

= 1.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.1 =
1.0.1 is a translation update.
