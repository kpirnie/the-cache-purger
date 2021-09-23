=== Blizhost CloudCache Purge ===
Contributors: blizhost
Tags: blizhost, cloudcache, nginx, redis, host, cache, hospedagem
Requires at least: 4.0
Tested up to: 5.8
Requires PHP: 5.6
Stable tag: 4.0.1
License: http://www.apache.org/licenses/LICENSE-2.0

Automatically empty your site cache when a post is published or when content is modified. And it improves CloudCache compatibility.

== Description ==
<strong>Blizhost CloudCache Purge</strong> sends a request to the Blizhost servers clear the cache every time the content of your site is modified. This occurs when publishing, editing, commenting or deleting a page or post, and when changing themes.

Our plugin will also make your WordPress site <strong>completely compatible with CloudCache</strong>!

This plugin is <strong>exclusive</strong> to Blizhost customers, and will have no results if used outside of our hosting.

= What is CloudCache? =

CloudCache is an HTTP accelerator designed for <strong>high traffic</strong> websites. Your website is delivered directly from the RAM of the server, making loading <strong>300x more faster</strong> and dramatically <strong>increasing access capacity</strong> to the site.

In contrast to other web accelerators, such as Squid, which began life as a client-side cache, or Apache and nginx, which are primarily origin servers, CloudCache was designed as an HTTP accelerator. CloudCache is focused exclusively on HTTP, unlike other proxy servers that often support FTP, SMTP and other network protocols.

Technologies like CloudCache are used by <strong>high-profile, high-traffic websites</strong> including Wikipedia, online newspaper sites such as The New York Times, The Guardian, The Hindu, Corriere della Sera, social media and content sites such as Facebook, Twitter, Vimeo, and Tumblr. Among the Top 10 thousand sites in the web, around a tenth use this technology.

= Plugin details =

Our plugin in addition to making your site compatible with our system will clean your site cache automatically whenever you publish/update a post or page.

Not all pages are purged every time. When a post, page, or custom post type is edited, or a new comment is added, <em>only</em> the following pages will be purged:

* The front page
* The post/page edited
* Any categories or tags associated with the page
* The pagination pages

In addition, your entire cache will be purged on the following actions:

* <del>Changing permalinks</del>
* Changing themes
* Press the 'Purge CloudCache' button on the dashboard
* Press the 'Blizhost CloudCache > Purge Entire Cache' button on the toolbar

Please note: On a multisite network using subfolders, only the <strong>network admins</strong> can purge the main site.

== Installation ==
No WordPress configuration needed.

= Requirements =
* Pretty Permalinks enabled
* CloudCache enabled on your Blizhost account

= Languages =
* English
* Portuguese-BR

== Frequently Asked Questions ==

= Is CloudCache compatible with all WordPress plugins and themes? =

CloudCache is designed to be compatible with most plugins and themes, it works with the same basic principle of other caching systems, and many developers are concerned with creating cache-compatible plugins.

But unfortunately they are not all, a minority does not care about performance. Because most sites are small and do not get much traffic, this is not a concern for these developers.

So we recommend that you always use well-known plugins and themes.

After all, if you care about speed and performance, it's not a good idea to have a plugin on your site that is not suited for high-traffic sites!

= Why doesn't every page flush when I make a new post? =

The only pages that should purge are the post's page, the front page, categories, and tags.

When building out this plugin, there were a couple pathways on how best to handle purging caches and they boiled down to two: Decisions (the plugin purges what it purges when it purges) and Options (you decide what to purge, when and why). It's entirely possible to make this plugin purge everything, every time a 'trigger' happens, have it purge some things, or have it be so you can pick that purges.

= Why doesn't my cache purge when I edit my theme? =

Because the plugin only purges your <em>content</em> when you edit it. That means if you edit a page/post, or someone leaves a comment, it'll change. Otherwise, you have to purge the whole cache. The plugin will do this for you if you ''change'' your theme, but not when you edit your theme.

If you use Jetpack's CSS editor, it will purge the whole cache for your site on save.

= How do I manually purge the whole cache? =

Click the 'Purge CloudCache' button on the "Right Now" Dashboard (see the screenshot if you can't find it).

There's also a "Blizhost CloudCache > Purge Entire Cache" button on the admin toolbar.

= I don't see a button! =

If you're on a Multisite Network and you're on the primary site in the network, only the <em>network</em> admins can purge that site

On a subfolder network if you flush the site at `example.com`, then everything under that (like `example.com/site1` and `example.com/siten` and everything else) would also get flushed. That means that a purge on the main site purges the entire network.

In order to mitigate the destructive nature of that power, only the network admins can purge everything on the main site of a subfolder network.

= Can I use this with a proxy service like CloudFlare? =
Of course! Feel free to use with any proxy or CDN service.

= How do I disable the CloudCache? =

You can open a support ticket on the client panel requesting the removal of CloudCache. But it's important to note that this technology makes your site up to 300x faster and with support for high traffic.

== Changelog ==

= 4.0.1 =
* Remove version of Slider Revolution for security reasons

= 4.0.0 =
* Fixed bug that removes URL from styles and scripts due to hashed version security

= 3.9.9 =
* Remove version of WordPress from rss and header for security reasons
* Improve WordPress Security

= 3.9.8 =
* Fixed non-existent is_plugin_active function error

= 3.9.7 =
* Fixed compatibility issues with the super-cache plugin and similar

= 3.9.5 =
* Require Jetpack-connected plugin to use WordPress Image CDN feature

= 3.9.4 =
* Fixed bug where CDN did not work for http protocol
* Improved CDN url regex, filtering content through output buffer
* Added option where you can disable CDN by DISABLE_WP_CDN

= 3.9.3 =
* Improved http protocol of WordPress CDN for more compatibility

= 3.9.2 =
* Improved WordPress CDN

= 3.9.1 =
* WordPress CDN implemented

= 3.9 =
* Plugin renamed for better understanding of this tool
* Button on the admin toolbar has been improved

= 3.8 =
* Purge the pagination pages of cache when a post is created or edited

= 3.7 =
* Ignore "DONOTCACHEPAGE" on homepage and posts
* Fixed bug where it was not possible to clear the cache outside the administrative area in some situations
* Prevents requests sended to servers if the site is not hosted on Blizhost

= 3.6 =
* Fixed bug that "DONOTCACHEPAGE" was not defined under certain circumstances

= 3.5 =
* Fixed compatibility bug on sites hosted outside Blizhost (even without any effect)

= 3.4 =
* Several enhancements to make Blizhost CloudCache compatible with various Wordpress plugins and themes
* Now the CloudCache can be skipped at the backend level
* Added "DONOTCACHEPAGE" constant so plugins can specify pages that should not be cached
* Forced cache cleanup now works for the entire domain

= 3.3 =
* Improves plugin security when creating an API key if it does not exist
* Support for WP-CLI commands and PHP > 5.5
* Fix typo (on -> one)
* Correct permissions on Multisite
* Correct weird merge error
* Fix formatting in Changelog
* Allow filter for `home_url()`
* Added wp-cli commands to flush specific URLs and wildcards
* Requires wp-cli 0.25+ to work for WP 4.6+
* Update `purgePost()` to validate page_for_posts
* Add check for AMP
* Purge 'default' AMP URL as well

= 3.2 =
* Fix bug in permalinks notice
* Sends the plugin version in the request to the Blizhost server to avoid conflicts

= 3.1 =
* Fix infinite redirect loops when loading WordPress sites under CloudFlare

= 3.0 =
* Automatic purge of sitemaps implemented

= 2.9 =
* Checks if session was initialized before loading CSS

= 2.8 =
* Change purge notice so it can be dismissed
* Fix purging of deleted posts
* Fixing i18n which wasn't working and threw a error on sites without pretty permalinks

= 2.7 =
* Fixed Blizhost logo on admin bar

= 2.6 =
* Some language fixes
* Fixed font style on admin bar

= 2.5 =
* Minor fixes
* Some language fixes
* Blizhost logo changed from png to font style

= 2.4 =
* Retain query params on purge
* Do not use query part for regex purging
* Allow CloudCache IP to be filtered.
* Improve flushing for cases when there's no Post ID
* Add filter so other plugins can add events to trigger purge when they have no post ID
* Add compatibility with [Autoptimize](https://wordpress.org/plugins/autoptimize/) so it flushes CloudCache when you flush their cache

= 2.3 =
* Fixed language directory.
* New tags.
* Fixed Domain Path.

= 2.2 =
* Translated to Portuguese-BR.

= 2.1 =
* Fixed compatibilities issues.

= 2.0 =
* Several performance upgrades, security and new filters added.

= 1.0 =
* The code was clean, getting more consistent and adopting best code practices.

== Upgrade Notice ==

= 3.7 =
Versions prior to this will no longer be supported and will not clear the cache. Upgrade as soon as possible.

= 3.3 =
This version improves the security of the plugin. Upgrade as soon as possible.

== Screenshots ==

1. 'Purge CloudCache' button on the "Right Now" Dashboard
