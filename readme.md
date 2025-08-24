# The Cache Purger

A comprehensive WordPress plugin that automatically purges all server-side caching methods when your website content changes. This plugin eliminates the need to manually clear caches across multiple systems by providing a unified solution for cache management.

## Description

The Cache Purger attempts to purge all server-side caching methods including the most common caching plugins, hosting-based caches, server-based caches, built-in WordPress object caches, and simple file-based caches. When you update content on your WordPress site, this plugin automatically clears all relevant caches to ensure visitors see the latest version immediately.

## Features

### Comprehensive Cache Support

**Caching Plugins**
- SiteGround Optimizer
- Nginx Helper
- LiteSpeed Cache
- Cachify
- Autoptimize
- Fast Velocity Minify
- WP Rocket
- Swift Performance
- Comet Cache
- Hummingbird
- WP Fastest Cache
- WP Super Cache
- W3 Total Cache
- Hyper Cache
- WP Optimize
- Cache Enabler
- NitroPack
- Flying Press
- Elementor (CSS Auto-Regenerate)
- Divi Cache
- Breeze

**Hosting & CDN Providers**
- WPEngine
- Kinsta
- GoDaddy Managed WordPress
- Pantheon
- Bluehost
- Cloudways
- SiteGround
- RunCloud
- Fastly CDN
- SpinUpWP
- Cloudflare (Direct API)
- Sucuri (Direct API)

*Note: Some hosting integrations may depend on separate plugins or configurations.*

**Server-Based Caches**
- PHP FPM
- Zend Opcache
- APC and APCU
- WinCache
- PageSpeed Module
- Nginx
- Static File Caches
- Redis (with database ID and authentication support)
- Memcache
- Memcached
- Varnish
- XCache

**WordPress Built-in**
- WordPress object caching
- Persistent object caching
- WP REST Cache

### Automated Purging Triggers

Cache purging is configurable and triggers on the following actions:
- **Content Updates**: Posts, Pages, Custom Post Types
- **Taxonomy Changes**: Categories, Tags, Custom Taxonomies  
- **Site Structure**: Widgets, Menus
- **System Changes**: Plugin updates, Settings & Options changes
- **Plugin Integration**: 
  - Gravity Forms (if installed)
  - Advanced Custom Fields (if installed) 
  - WooCommerce Settings (if installed)

### Additional Features

- **Manual Purging**: Clear caches on-demand from the admin interface
- **WP-CLI Support**: Clear caches via command line interface
- **Background Processing**: Uses WooCommerce Action Scheduler for efficient processing
- **Logging**: Optional logging of purge activities with cron-based log clearing
- **Selective Purging**: Choose which types of caches to purge
- **API Integration**: Direct integration with CDN and security services
- **Translation Ready**: Fully internationalized with text domain `the-cache-purger`

## Installation

### Method 1: WordPress Plugin Repository
1. Navigate to **Plugins** > **Add New** in your WordPress dashboard
2. Search for "The Cache Purger"
3. Click **Install Now** and then **Activate**

### Method 2: Manual Installation
1. Download the plugin zip file
2. Upload and extract to `/wp-content/plugins/the-cache-purger/`
3. Activate the plugin through the **Plugins** menu in WordPress

### Method 3: Upload via Admin
1. Download the plugin zip file
2. Go to **Plugins** > **Add New** > **Upload Plugin**
3. Choose the zip file and click **Install Now**
4. Activate the plugin

## Configuration

After activation, configure the plugin through the WordPress admin:

1. Navigate to the plugin settings page
2. **General Settings**: Configure which events trigger cache purging
3. **Cache Types**: Select which caching systems to purge
4. **API Settings**: Configure credentials for CDN and remote cache services
5. **Logging**: Enable/disable activity logging
6. **Manual Purge**: Use the master purge button for immediate cache clearing

### API Configuration

For services requiring API credentials:
- **Cloudflare**: Enter your API key and zone information
- **Sucuri**: Provide API credentials 
- **Fastly CDN**: Configure API key and service ID
- **Redis**: Set database ID, authentication, and key prefix
- **Remote Memcache/Memcached**: Configure connection details

## Usage

### Automatic Purging
Once configured, the plugin automatically purges relevant caches when:
- You publish or update posts/pages
- You modify widgets or menus
- You change plugin settings
- Supported plugins (Gravity Forms, ACF, WooCommerce) are updated

### Manual Purging
- Use the **Master Purge** button in the plugin settings
- Purge individual cache types as needed
- Monitor purge activity through the logging interface

### WP-CLI Usage
Clear all caches via command line:

```bash
wp the_cache purge
```

**Additional CLI Options:**
- For root users: `wp the_cache purge --allow-root`
- For multisite networks: `wp the_cache purge --url=https://yoursite.com`

*Note: CLI functionality was removed in version 2.1.36 but may be reinstated in future versions.*

## Requirements

- **WordPress**: 5.6 or higher
- **PHP**: 8.1 or higher (8.4 compatible)
- **WordPress Version Tested**: Up to 6.9

## Frequently Asked Questions

### Why would I need this plugin?
If you need to manually clear server-side caches every time you modify your site, or want to automate cache clearing, this plugin is for you. It provides a "set it and forget it" solution for cache management.

### Does it work with my hosting provider?
The plugin supports most major WordPress hosting providers. Check the features list above for your specific host. Some integrations may require additional plugins or configurations.

### Can I choose which caches to purge?
Yes! The plugin includes settings to select which types of caches should be purged, giving you granular control over the purging process.

### What about performance impact?
The plugin uses WooCommerce's Action Scheduler to handle purging in the background, minimizing impact on your site's performance. Long-running purge operations are handled via WordPress cron jobs.

### Does it support multisite?
Yes, the plugin is compatible with WordPress multisite installations.

## Important Notes

### NitroPack Integration
NitroPack has its own methodology for clearing and managing most server-based caches. As a result, we cannot log items that NitroPack removes, overwrites, manages, or flushes during its purge processes.

### PHP Dynamic Properties Notice
The Fastly CDN integration may generate PHP 8 notices about Dynamic Properties. This is a known issue with the Fastly library that will be resolved when Fastly updates their code.

### Performance Considerations
- Long purge operations are moved to background processing to avoid admin interface slowdowns
- Cron-based scheduling prevents performance issues during high-traffic periods
- Logging can be disabled to improve performance if detailed purge tracking isn't needed

## Changelog Highlights

### Version 2.1.36 (Latest)
- ⬆️ PHP 8.1 minimum requirement
- 🔄 Updated vendor libraries
- ➖ Removed CLI clearing functionality
- 🏗️ Restructured common functionality for better performance
- ➕ Added Redis prefix/key support
- 🐛 Fixed Redis database flush issues

### Version 2.1.01
- ✅ WordPress Core 6.9 compatibility
- ✅ PHP 8.4 compatibility verified
- ➕ Added Redis database ID and authentication support
- 🔄 Updated Fastly CDN and Action Scheduler libraries
- ⚠️ Added notice for PHP 8.0 and lower versions

### Version 2.0.11
- ➕ Added SpinUpWP support
- 🔄 Updated documentation and libraries
- 🐛 Fixed Action Scheduler background job calls

### Version 1.9.89
- ➕ Added Fastly CDN purge support
- 🔄 Replaced WordPress cron with WooCommerce Action Scheduler
- ➕ Added new hooks: `tcp_cron_cache_purge`, `tcp_cron_log_purge`, `tcp_long_cache_purge`

### Version 1.9.27
- ➕ Added Flying Press plugin support
- ➕ New setting for selecting cache types to purge

For complete changelog, see the plugin's WordPress.org page.

## Hooks & Filters

The plugin provides numerous action hooks for developers:

**General Hooks**
- `tcp_pre_purge` - Fires before any purging begins
- `tcp_post_purge` - Fires after all purging is complete

**Specific Cache Type Hooks**
- `tcp_pre_hosting_purge` / `tcp_post_hosting_purge`
- `tcp_pre_plugin_purge` / `tcp_post_plugin_purge`  
- `tcp_pre_wp_purge` / `tcp_post_wp_purge`
- `tcp_pre_php_purge` / `tcp_post_php_purge`
- `tcp_pre_pagespeed_purge` / `tcp_post_pagespeed_purge`
- `tcp_pre_nginx_purge` / `tcp_post_nginx_purge`
- `tcp_pre_file_purge` / `tcp_post_file_purge`

**Cron Hooks**
- `tcp_cron_cache_purge` - Fires when cron cache purge is scheduled
- `tcp_cron_log_purge` - Fires when cron log purge is scheduled
- `tcp_long_cache_purge` - Fires when long cache purge is executed

## Support

### Community Support
For general support questions, please visit the [WordPress.org plugin support forum](https://wordpress.org/support/plugin/the-cache-purger/).

### Professional Support  
For complex configurations or professional assistance, contact the developer at: https://kevp.us/contact

## Contributing

The Cache Purger is open source software. Contributions are welcome!

- **Browse the code**: [Plugin Trac](https://plugins.trac.wordpress.org/browser/the-cache-purger/)
- **SVN Repository**: [The Cache Purger SVN](https://plugins.svn.wordpress.org/the-cache-purger/)
- **Development Log**: [RSS Feed](https://plugins.trac.wordpress.org/log/the-cache-purger/?limit=100&mode=stop_on_copy&format=rss)

### Translation
Help translate the plugin into your language at [WordPress Translate](https://translate.wordpress.org/projects/wp-plugins/the-cache-purger).

## License

This plugin is licensed under the GPL v2 or later.

---

**Performance Tip**: This plugin works best when you configure only the cache types you actually use. Unnecessary purging operations can be disabled in the settings to optimize performance.

**Compatibility Note**: While this plugin attempts to support as many caching systems as possible, some hosting providers may have unique implementations. If you experience issues, contact your hosting provider to confirm cache clearing methods and any required additional plugins.