<?php
/** 
 * Documentation
 * 
 * The plugin documentation
 * 
 * @since 7.4
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package The Cache Purger
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

?>

<article class="kptcp-doc-content" id="kpcp_top">
    <header>
        <h1><?php _e( 'The Cache Purger Documentation', 'the-cache-purger' ); ?></h1>
    </header>
    <main>
        <h2 id="kpcp_desc"><?php _e( 'Description', 'the-cache-purger' ); ?></h2>
        <p class="kpcp_nav"><a href="#kpcp_top"><?php _e( 'TOP', 'the-cache-purger' ); ?></a> | <a href="#kpcp_desc"><?php _e( 'DESCRIPTION', 'the-cache-purger' ); ?></a> | <a href="#kpcp_features"><?php _e( 'FEATURES', 'the-cache-purger' ); ?></a> | <a href="#kpcp_settings"><?php _e( 'SETTINGS', 'the-cache-purger' ); ?></a> | <a href="#kpcp_api_settings"><?php _e( 'API/SERVER SETTINGS', 'the-cache-purger' ); ?></a> | <a href="#kpcp_cron_settings"><?php _e( 'CRON Action SETTINGS', 'the-cache-purger' ); ?></a> | <a href="#kpcp_cron_settings"><?php _e( 'CRON Action SETTINGS', 'the-cache-purger' ); ?></a> | <a href="#kpcp_in_the_works"><?php _e( 'IN THE WORKS', 'the-cache-purger' ); ?></a></p>
        <p><?php _e( 'This plugin attempts to purge all server-side caching methods', 'the-cache-purger' ); ?>.</p>
        <p><?php _e( 'This includes the most common caching plugins, some hosting based caches, most server based caches, built-in Wordpress object caches, and even simple file based caches', 'the-cache-purger' ); ?></p>
        <p><?php _e( 'Just configure what you want to purge on, and the plugin will take care of the rest', 'the-cache-purger' ); ?>.</p>
        <p><?php _e( 'We have also included a CLI cache purger.  Shell into your install and run the following command:', 'the-cache-purger' ); ?> <code>wp the_cache purge</code>. <?php _e( 'The normal CLI flags apply, and if you are in a multisite, you must include the --url flag.', 'the-cache-purger' ); ?></p>
        <h2 id="kpcp_features"><?php _e( 'Features', 'the-cache-purger' ); ?></h2>
        <p class="kpcp_nav"><a href="#kpcp_top"><?php _e( 'TOP', 'the-cache-purger' ); ?></a> | <a href="#kpcp_desc"><?php _e( 'DESCRIPTION', 'the-cache-purger' ); ?></a> | <a href="#kpcp_features"><?php _e( 'FEATURES', 'the-cache-purger' ); ?></a> | <a href="#kpcp_settings"><?php _e( 'SETTINGS', 'the-cache-purger' ); ?></a> | <a href="#kpcp_api_settings"><?php _e( 'API/SERVER SETTINGS', 'the-cache-purger' ); ?></a> | <a href="#kpcp_cron_settings"><?php _e( 'CRON Action SETTINGS', 'the-cache-purger' ); ?></a> | <a href="#kpcp_in_the_works"><?php _e( 'IN THE WORKS', 'the-cache-purger' ); ?></a></p>
        <h3><?php _e( 'Built in automatic cache purging for the following caches', 'the-cache-purger' ); ?></h3>
        <ul>
            <li><strong><?php _e( 'Plugins/Themes', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'SiteGround Optimizer', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'Nginx Helper', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'LiteSpeed Cache', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'Cachify', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'Autoptimize', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'Fast Velocity Minify', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'WP Rocket', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'Swift Performance', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'Comet Cache', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'Hummingbird', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'WP Fastest Cache', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'WP Super Cache', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'W3 Total Cache', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'Hyper Cache', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'WP Optimize', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'Cache Enabler', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'Divi', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'Elementor', 'the-cache-purger' ); ?></li>
                </ul>
            </li>
            <li><strong><?php _e( 'Hosting / CDN', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'WPEngine', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'Kinsta', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'GoDaddy', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'Pantheon', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'CloudFlare', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'Sucuri', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'RunCloud', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'Siteground', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'Bluehost', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'Breezeway', 'the-cache-purger' ); ?></li>
                    <li><em><?php _e( 'Some of these are dependant on separate plugins.  Please see your provider if it is necessary, or already included', 'the-cache-purger' ); ?></em></li>
                </ul>
            </li>
            <li><strong><?php _e( 'Server Based', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'PHP FPM', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'Zend Opcache', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'APC and APCU', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'WinCache', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'Pagespeed Module', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'Memcache', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'Memcached', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'Redis', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'nGinx', 'the-cache-purger' ); ?></li>
                    <li><?php _e( 'Static File Caches', 'the-cache-purger' ); ?></li>
                </ul>
            </li>
            <li><strong><?php _e( 'Wordpress Built-In', 'the-cache-purger' ); ?></strong> <?php _e( 'object caching and persistent object caching', 'the-cache-purger' ); ?></li>
        </ul>
        
        <h3><?php _e( 'Purges are configurable in the settings, and include the following saves/updates/trashes:', 'the-cache-purger' ); ?></h3>
        <ul>
            <li><?php _e( 'Posts', 'the-cache-purger' ); ?></li>
            <li><?php _e( 'Pages', 'the-cache-purger' ); ?></li>
            <li><?php _e( 'Custom Post Types', 'the-cache-purger' ); ?></li>
            <li><?php _e( 'Categories', 'the-cache-purger' ); ?></li>
            <li><?php _e( 'Taxonomies', 'the-cache-purger' ); ?></li>
            <li><?php _e( 'Widgets', 'the-cache-purger' ); ?></li>
            <li><?php _e( 'Menus', 'the-cache-purger' ); ?></li>
            <li><?php _e( 'Plugins', 'the-cache-purger' ); ?></li>
            <li><?php _e( 'Updates', 'the-cache-purger' ); ?></li>
            <li><?php _e( 'Settings & Options', 'the-cache-purger' ); ?></li>
            <li><?php _e( 'GravityForms', 'the-cache-purger' ); ?> (<em><?php _e( 'if installed and activated', 'the-cache-purger' ); ?></em>)</li>
            <li><?php _e( 'Advanced Custom Fields', 'the-cache-purger' ); ?> (<em><?php _e( 'if installed and activated', 'the-cache-purger' ); ?></em>)</li>
            <li><?php _e( 'WooCommerce Settings', 'the-cache-purger' ); ?> (<em><?php _e( 'if installed and activated', 'the-cache-purger' ); ?></em>)</li>
        </ul>
        <h2 id="kpcp_settings"><?php _e( 'Settings', 'the-cache-purger' ); ?></h2>
        <p class="kpcp_nav"><a href="#kpcp_top"><?php _e( 'TOP', 'the-cache-purger' ); ?></a> | <a href="#kpcp_desc"><?php _e( 'DESCRIPTION', 'the-cache-purger' ); ?></a> | <a href="#kpcp_features"><?php _e( 'FEATURES', 'the-cache-purger' ); ?></a> | <a href="#kpcp_settings"><?php _e( 'SETTINGS', 'the-cache-purger' ); ?></a> | <a href="#kpcp_api_settings"><?php _e( 'API/SERVER SETTINGS', 'the-cache-purger' ); ?></a> | <a href="#kpcp_cron_settings"><?php _e( 'CRON Action SETTINGS', 'the-cache-purger' ); ?></a> | <a href="#kpcp_in_the_works"><?php _e( 'IN THE WORKS', 'the-cache-purger' ); ?></a></p>
        <ul>
            <li>
                <strong><?php _e( 'Log Purge Actions?', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>should_log</code></li>
                    <li><?php _e( 'Do you want to log purge actions?  The log file will be located here:', 'the-cache-purger' ); ?> <code><?php _e( ABSPATH . 'wp-content/purge.log' ); ?></code></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Purge on Menu?', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>on_menu</code></li>
                    <li><?php _e( 'This will attempt to purge all caches for every menu update, save, or delete.', 'the-cache-purger' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Purge on Post?', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>on_post</code></li>
                    <li><?php _e( 'This will attempt to purge all caches for every post update, save, or delete.', 'the-cache-purger' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Ignored Posts', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>on_post_exclude</code></li>
                    <li><?php _e( 'Select the posts you wish to ignore from the cache purge actions.', 'the-cache-purger' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Purge on Page?', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>on_page</code></li>
                    <li><?php _e( 'This will attempt to purge all caches for every page update, save, or delete.', 'the-cache-purger' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Ignored Pages', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>on_page_exclude</code></li>
                    <li><?php _e( 'Select the pages you wish to ignore from the cache purge actions.', 'the-cache-purger' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Purge on CPT?', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>on_cpt</code></li>
                    <li><?php _e( 'This will attempt to purge all caches for every custom post type update, save, or delete.', 'the-cache-purger' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Ignore CPT', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>on_cpt_exclude</code></li>
                    <li><?php _e( 'Select the custom post types you wish to ignore from the cache purge actions.', 'the-cache-purger' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Purge on Term/Taxonomy?', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>on_taxonomy</code></li>
                    <li><?php _e( 'This will attempt to purge all caches for every taxonomy/term update, save, or delete.', 'the-cache-purger' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Purge on Category?', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>on_category</code></li>
                    <li><?php _e( 'This will attempt to purge all caches for every category update, save, or delete.', 'the-cache-purger' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Purge on Widget?', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>on_widget</code></li>
                    <li><?php _e( 'This will attempt to purge all caches for every widget update, save, or removal.', 'the-cache-purger' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Purge on Customizer?', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>on_customizer</code></li>
                    <li><?php _e( 'This will attempt to purge all caches for every customizer update or save.', 'the-cache-purger' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Purge on GravityForms?', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>on_form</code></li>
                    <li><?php _e( 'This will attempt to purge all caches for every form update, save, or delete.', 'the-cache-purger' ); ?></li>
                    <li><em><?php _e( 'This option is only available if you have GravityForms installed and active on your site.', 'the-cache-purger' ); ?></em></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Ignore Forms', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>on_form_exclude</code></li>
                    <li><?php _e( 'Select the forms you wish to ignore from the cache purge actions.', 'the-cache-purger' ); ?></li>
                    <li><em><?php _e( 'This option is only available if you have GravityForms installed and active on your site.', 'the-cache-purger' ); ?></em></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Purge on ACF?', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>on_acf</code></li>
                    <li><?php _e( 'This will attempt to purge all caches for every "advanced custom field" group update, save, or delete.', 'the-cache-purger' ); ?></li>
                    <li><em><?php _e( 'This option is only available if you have Advanced Custom Fields installed and active on your site.', 'the-cache-purger' ); ?></em></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Ignore Field Group', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>on_acf_exclude</code></li>
                    <li><?php _e( 'Select the field groups you wish to ignore from the cache purge actions.', 'the-cache-purger' ); ?></li>
                    <li><em><?php _e( 'This option is only available if you have Advanced Custom Fields installed and active on your site.', 'the-cache-purger' ); ?></em></li>
                </ul>
            </li>
        </ul>
        <h2 id="kpcp_api_settings"><?php _e( 'API/SERVER Settings', 'the-cache-purger' ); ?></h2>
        <p class="kpcp_nav"><a href="#kpcp_top"><?php _e( 'TOP', 'the-cache-purger' ); ?></a> | <a href="#kpcp_desc"><?php _e( 'DESCRIPTION', 'the-cache-purger' ); ?></a> | <a href="#kpcp_features"><?php _e( 'FEATURES', 'the-cache-purger' ); ?></a> | <a href="#kpcp_settings"><?php _e( 'SETTINGS', 'the-cache-purger' ); ?></a> | <a href="#kpcp_api_settings"><?php _e( 'API/SERVER SETTINGS', 'the-cache-purger' ); ?></a> | <a href="#kpcp_cron_settings"><?php _e( 'CRON Action SETTINGS', 'the-cache-purger' ); ?></a> | <a href="#kpcp_in_the_works"><?php _e( 'IN THE WORKS', 'the-cache-purger' ); ?></a></p>
        <ul>
            <li>
                <strong><?php _e( 'Remote Redis Server', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>remote_redis</code></li>
                    <li><?php _e( 'Do you want to configure Redis servers to be purged?', 'the-cache-purger' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Redis Servers - Server', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>remote_redis_servers['remote_redis_server']</code></li>
                    <li><?php _e( 'Insert the servers IP address.', 'the-cache-purger' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Redis Servers - Port', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>remote_redis_servers['remote_redis_port']</code></li>
                    <li><?php _e( 'Insert the servers port.', 'the-cache-purger' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Remote Memcache Server', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>remote_memcache</code></li>
                    <li><?php _e( 'Do you want to configure Memcache servers to be purged?', 'the-cache-purger' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Memcache Servers - Server', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>remote_memcache_servers['remote_memcache_server']</code></li>
                    <li><?php _e( 'Insert the servers IP address.', 'the-cache-purger' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Memcache Servers - Port', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>remote_memcache_servers['remote_memcache_port']</code></li>
                    <li><?php _e( 'Insert the servers port.', 'the-cache-purger' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Remote Memcached Server', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>remote_memcached</code></li>
                    <li><?php _e( 'Do you want to configure Memcached servers to be purged?', 'the-cache-purger' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Memcached Servers - Server', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>remote_memcached_servers['remote_memcached_server']</code></li>
                    <li><?php _e( 'Insert the servers IP address.', 'the-cache-purger' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Memcached Servers - Port', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>remote_memcached_servers['remote_memcached_port']</code></li>
                    <li><?php _e( 'Insert the servers port.', 'the-cache-purger' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Service API Keys', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li>
                        <strong><?php _e( 'Cloudflare Token', 'the-cache-purger' ); ?></strong>
                        <ul>
                            <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>service_api_keys['cloudflare_token']</code></li>
                            <li><?php _e( 'Enter your Cloudflare API Token. If you do not have one, you can create one here: <a href="https://dash.cloudflare.com/profile/api-tokens" target="_blank">https://dash.cloudflare.com/profile/api-tokens</a><br /><strong>NOTE: </strong>This is stored in plain-text.', 'the-cache-purger' ); ?></li>
                        </ul>
                    </li>
                    <li>
                        <strong><?php _e( 'Cloudflare Zone', 'the-cache-purger' ); ?></strong>
                        <ul>
                            <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>service_api_keys['cloudflare_zone']</code></li>
                            <li><?php _e( 'Enter your Cloudflare Zone ID. You can find this by clicking into your websites overview in your account: <a href="https://dash.cloudflare.com/" target="_blank">https://dash.cloudflare.com/</a><br /><strong>NOTE: </strong>This is stored in plain-text.', 'the-cache-purger' ); ?></li>
                        </ul>
                    </li>
                    <li>
                        <strong><?php _e( 'Sucuri Key', 'the-cache-purger' ); ?></strong>
                        <ul>
                            <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>service_api_keys['sucuri_key']</code></li>
                            <li><?php _e( 'Enter your Sucuri API Key. If you do not have one, you can find it in your site\'s Firewall here: <a href="https://waf.sucuri.net/" target="_blank">https://waf.sucuri.net/</a>. Click into your site, then Settings, then API.<br /><strong>NOTE: </strong>This is stored in plain-text.', 'the-cache-purger' ); ?></li>
                        </ul>
                    </li>
                    <li>
                        <strong><?php _e( 'Sucuri Secret', 'the-cache-purger' ); ?></strong>
                        <ul>
                            <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>service_api_keys['sucuri_secret']</code></li>
                            <li><?php _e( 'Enter your Sucuri API Secret. If you do not have one, you can find it in your site\'s Firewall here: <a href="https://waf.sucuri.net/" target="_blank">https://waf.sucuri.net/</a>. Click into your site, then Settings, then API.<br /><strong>NOTE: </strong>This is stored in plain-text.', 'the-cache-purger' ); ?></li>
                        </ul>
                    </li>
                </ul>
            </li>

        </ul>
        <h2 id="kpcp_cron_settings"><?php _e( 'CRON Action Settings', 'the-cache-purger' ); ?></h2>
        <p class="kpcp_nav"><a href="#kpcp_top"><?php _e( 'TOP', 'the-cache-purger' ); ?></a> | <a href="#kpcp_desc"><?php _e( 'DESCRIPTION', 'the-cache-purger' ); ?></a> | <a href="#kpcp_features"><?php _e( 'FEATURES', 'the-cache-purger' ); ?></a> | <a href="#kpcp_settings"><?php _e( 'SETTINGS', 'the-cache-purger' ); ?></a> | <a href="#kpcp_api_settings"><?php _e( 'API/SERVER SETTINGS', 'the-cache-purger' ); ?></a> | <a href="#kpcp_cron_settings"><?php _e( 'CRON Action SETTINGS', 'the-cache-purger' ); ?></a> | <a href="#kpcp_in_the_works"><?php _e( 'IN THE WORKS', 'the-cache-purger' ); ?></a></p>
        <ul>
            <li>
                <strong><?php _e( 'Allow Scheduled Purges?', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>cron_schedule_allowed</code></li>
                    <li><?php _e( 'Should the cached be purged based on a Wordpress Cron schedule?', 'the-cache-purger' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Built-In Schedule', 'the-cache-purger' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:', 'the-cache-purger' ); ?> <code>cron_schedule_builtin</code></li>
                    <li><?php _e( 'Select a built-in schedule to purge the caches on.', 'the-cache-purger' ); ?></li>
                </ul>
            </li>
        </ul>
        <h2 id="kpcp_in_the_works"><?php _e( 'In The Works', 'the-cache-purger' ); ?></h2>
        <p class="kpcp_nav"><a href="#kpcp_top"><?php _e( 'TOP', 'the-cache-purger' ); ?></a> | <a href="#kpcp_desc"><?php _e( 'DESCRIPTION', 'the-cache-purger' ); ?></a> | <a href="#kpcp_features"><?php _e( 'FEATURES', 'the-cache-purger' ); ?></a> | <a href="#kpcp_settings"><?php _e( 'SETTINGS', 'the-cache-purger' ); ?></a> | <a href="#kpcp_api_settings"><?php _e( 'API/SERVER SETTINGS', 'the-cache-purger' ); ?></a> | <a href="#kpcp_cron_settings"><?php _e( 'CRON Action SETTINGS', 'the-cache-purger' ); ?></a> | <a href="#kpcp_in_the_works"><?php _e( 'IN THE WORKS', 'the-cache-purger' ); ?></a></p>
        <ul>
            <li><?php _e( 'WooCommerce Product Updates (<em>and exclusions</em>)', 'the-cache-purger' ); ?></li>
            <li><?php _e( 'WooCommerce Order Updates', 'the-cache-purger' ); ?></li>
            <li><?php _e( 'More Plugin References', 'the-cache-purger' ); ?></li>
            <li><?php _e( 'More Hosting References', 'the-cache-purger' ); ?></li>
        </ul>
    </main>
    <footer></footer>
</article>
