<?php
/** 
 * Documentation
 * 
 * The plugin documentation
 * 
 * @since 8.1
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package The Cache Purger
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

?>

<article class="kptcp-doc-content" id="kpcp_top">
    <header>
        <h1><?php _e( 'The Cache Purger Documentation' ); ?></h1>
    </header>
    <main>
        <h2 id="kpcp_desc"><?php _e( 'Description' ); ?></h2>
        <p class="kpcp_nav"><a href="#kpcp_top"><?php _e( 'TOP' ); ?></a> | <a href="#kpcp_desc"><?php _e( 'DESCRIPTION' ); ?></a> | <a href="#kpcp_features"><?php _e( 'FEATURES' ); ?></a> | <a href="#kpcp_settings"><?php _e( 'SETTINGS' ); ?></a> | <a href="#kpcp_api_settings"><?php _e( 'API/SERVER SETTINGS' ); ?></a> | <a href="#kpcp_cron_settings"><?php _e( 'CRON Action SETTINGS' ); ?></a> | <a href="#kpcp_cron_settings"><?php _e( 'CRON Action SETTINGS' ); ?></a> | <a href="#kpcp_in_the_works"><?php _e( 'IN THE WORKS' ); ?></a></p>
        <p><?php _e( 'This plugin attempts to purge all server-side caching methods' ); ?>.</p>
        <p><?php _e( 'This includes the most common caching plugins, some hosting based caches, most server based caches, built-in Wordpress object caches, and even simple file based caches' ); ?></p>
        <p><?php _e( 'Just configure what you want to purge on, and the plugin will take care of the rest' ); ?>.</p>
        <p><?php _e( 'We have also included a CLI cache purger.  Shell into your install and run the following command:' ); ?> <code>wp the_cache purge</code>. <?php _e( 'The normal CLI flags apply, and if you are in a multisite, you must include the --url flag.' ); ?></p>
        <h2 id="kpcp_features"><?php _e( 'Features' ); ?></h2>
        <p class="kpcp_nav"><a href="#kpcp_top"><?php _e( 'TOP' ); ?></a> | <a href="#kpcp_desc"><?php _e( 'DESCRIPTION' ); ?></a> | <a href="#kpcp_features"><?php _e( 'FEATURES' ); ?></a> | <a href="#kpcp_settings"><?php _e( 'SETTINGS' ); ?></a> | <a href="#kpcp_api_settings"><?php _e( 'API/SERVER SETTINGS' ); ?></a> | <a href="#kpcp_cron_settings"><?php _e( 'CRON Action SETTINGS' ); ?></a> | <a href="#kpcp_in_the_works"><?php _e( 'IN THE WORKS' ); ?></a></p>
        <h3><?php _e( 'Built in automatic cache purging for the following caches' ); ?></h3>
        <ul>
            <li><strong><?php _e( 'Plugins/Themes' ); ?></strong>
                <ul>
                    <li><?php _e( 'Flying Press' ); ?>, <?php _e( 'SiteGround Optimizer' ); ?>, <?php _e( 'Nginx Helper' ); ?>, <?php _e( 'LiteSpeed Cache' ); ?>, <?php _e( 'Cachify' ); ?>, <?php _e( 'Autoptimize' ); ?>, <?php _e( 'Fast Velocity Minify' ); ?>, <?php _e( 'WP Rocket' ); ?>, <?php _e( 'Swift Performance' ); ?>, <?php _e( 'Comet Cache' ); ?>, <?php _e( 'Hummingbird' ); ?>, <?php _e( 'WP Fastest Cache' ); ?>, <?php _e( 'WP Super Cache' ); ?>, <?php _e( 'W3 Total Cache' ); ?>, <?php _e( 'Hyper Cache' ); ?>, <?php _e( 'WP Optimize' ); ?>, <?php _e( 'Cache Enabler' ); ?>, <?php _e( 'Divi' ); ?>, <?php _e( 'Elementor' ); ?></li>
                </ul>
            </li>
            <li><strong><?php _e( 'Hosting / CDN' ); ?></strong>
                <ul>
                    <li><?php _e( 'WPEngine' ); ?>, <?php _e( 'SpinupWP' ); ?>, <?php _e( 'Kinsta' ); ?>, <?php _e( 'GoDaddy' ); ?>, <?php _e( 'Pantheon' ); ?>, <?php _e( 'CloudFlare' ); ?>, <?php _e( 'Sucuri' ); ?>, <?php _e( 'RunCloud' ); ?>, <?php _e( 'Siteground' ); ?>, <?php _e( 'Bluehost' ); ?>, <?php _e( 'Breezeway' ); ?></li>
                    <li><em><?php _e( 'Some of these are dependant on separate plugins.  Please see your provider if it is necessary, or already included' ); ?></em></li>
                </ul>
            </li>
            <li><strong><?php _e( 'Server Based' ); ?></strong>
                <ul>
                    <li><?php _e( 'Fastly CDN' ); ?>, <?php _e( 'PHP FPM' ); ?>, <?php _e( 'Zend Opcache' ); ?>, <?php _e( 'APC and APCU' ); ?>, <?php _e( 'WinCache' ); ?>, <?php _e( 'Pagespeed Module' ); ?>, <?php _e( 'Memcache' ); ?>, <?php _e( 'Memcached' ); ?>, <?php _e( 'Redis' ); ?>, <?php _e( 'nGinx' ); ?>, <?php _e( 'Static File Caches' ); ?></li>
                </ul>
            </li>
            <li><strong><?php _e( 'Wordpress Built-In' ); ?></strong> <?php _e( 'object caching and persistent object caching' ); ?></li>
        </ul>
        
        <h3><?php _e( 'Purges are configurable in the settings, and include the following saves/updates/trashes:' ); ?></h3>
        <ul>
            <li><?php _e( 'Posts' ); ?>, <?php _e( 'Pages' ); ?>, <?php _e( 'Custom Post Types' ); ?>, <?php _e( 'Categories' ); ?>, <?php _e( 'Taxonomies' ); ?>, <?php _e( 'Widgets' ); ?>, <?php _e( 'Menus' ); ?>, <?php _e( 'Plugins' ); ?>, <?php _e( 'Updates' ); ?>, <?php _e( 'Settings & Options' ); ?>, <?php _e( 'GravityForms' ); ?> (<em><?php _e( 'if installed and activated' ); ?></em>), <?php _e( 'Advanced Custom Fields' ); ?> (<em><?php _e( 'if installed and activated' ); ?></em>), <?php _e( 'WooCommerce Settings' ); ?> (<em><?php _e( 'if installed and activated' ); ?></em>)</li>
        </ul>
        <h2 id="kpcp_settings"><?php _e( 'Settings' ); ?></h2>
        <p class="kpcp_nav"><a href="#kpcp_top"><?php _e( 'TOP' ); ?></a> | <a href="#kpcp_desc"><?php _e( 'DESCRIPTION' ); ?></a> | <a href="#kpcp_features"><?php _e( 'FEATURES' ); ?></a> | <a href="#kpcp_settings"><?php _e( 'SETTINGS' ); ?></a> | <a href="#kpcp_api_settings"><?php _e( 'API/SERVER SETTINGS' ); ?></a> | <a href="#kpcp_cron_settings"><?php _e( 'CRON Action SETTINGS' ); ?></a> | <a href="#kpcp_in_the_works"><?php _e( 'IN THE WORKS' ); ?></a></p>
        <ul>
            <li>
                <strong><?php _e( 'Log Purge Actions?' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:' ); ?> <code>should_log</code></li>
                    <li><?php _e( 'Do you want to log purge actions?  The log file will be located here:' ); ?> <code><?php _e( ABSPATH . 'wp-content/purge.log' ); ?></code></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Purge on Menu?' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:' ); ?> <code>on_menu</code></li>
                    <li><?php _e( 'This will attempt to purge all caches for every menu update, save, or delete.' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Purge on Post?' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:' ); ?> <code>on_post</code></li>
                    <li><?php _e( 'This will attempt to purge all caches for every post update, save, or delete.' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Ignored Posts' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:' ); ?> <code>on_post_exclude</code></li>
                    <li><?php _e( 'Select the posts you wish to ignore from the cache purge actions.' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Purge on Page?' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:' ); ?> <code>on_page</code></li>
                    <li><?php _e( 'This will attempt to purge all caches for every page update, save, or delete.' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Ignored Pages' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:' ); ?> <code>on_page_exclude</code></li>
                    <li><?php _e( 'Select the pages you wish to ignore from the cache purge actions.' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Purge on CPT?' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:' ); ?> <code>on_cpt</code></li>
                    <li><?php _e( 'This will attempt to purge all caches for every custom post type update, save, or delete.' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Ignore CPT' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:' ); ?> <code>on_cpt_exclude</code></li>
                    <li><?php _e( 'Select the custom post types you wish to ignore from the cache purge actions.' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Purge on Term/Taxonomy?' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:' ); ?> <code>on_taxonomy</code></li>
                    <li><?php _e( 'This will attempt to purge all caches for every taxonomy/term update, save, or delete.' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Purge on Category?' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:' ); ?> <code>on_category</code></li>
                    <li><?php _e( 'This will attempt to purge all caches for every category update, save, or delete.' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Purge on Widget?' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:' ); ?> <code>on_widget</code></li>
                    <li><?php _e( 'This will attempt to purge all caches for every widget update, save, or removal.' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Purge on Customizer?' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:' ); ?> <code>on_customizer</code></li>
                    <li><?php _e( 'This will attempt to purge all caches for every customizer update or save.' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Purge on GravityForms?' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:' ); ?> <code>on_form</code></li>
                    <li><?php _e( 'This will attempt to purge all caches for every form update, save, or delete.' ); ?></li>
                    <li><em><?php _e( 'This option is only available if you have GravityForms installed and active on your site.' ); ?></em></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Ignore Forms' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:' ); ?> <code>on_form_exclude</code></li>
                    <li><?php _e( 'Select the forms you wish to ignore from the cache purge actions.' ); ?></li>
                    <li><em><?php _e( 'This option is only available if you have GravityForms installed and active on your site.' ); ?></em></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Purge on ACF?' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:' ); ?> <code>on_acf</code></li>
                    <li><?php _e( 'This will attempt to purge all caches for every "advanced custom field" group update, save, or delete.' ); ?></li>
                    <li><em><?php _e( 'This option is only available if you have Advanced Custom Fields installed and active on your site.' ); ?></em></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Ignore Field Group' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:' ); ?> <code>on_acf_exclude</code></li>
                    <li><?php _e( 'Select the field groups you wish to ignore from the cache purge actions.' ); ?></li>
                    <li><em><?php _e( 'This option is only available if you have Advanced Custom Fields installed and active on your site.' ); ?></em></li>
                </ul>
            </li>
        </ul>
        <h2 id="kpcp_api_settings"><?php _e( 'API/SERVER Settings' ); ?></h2>
        <p class="kpcp_nav"><a href="#kpcp_top"><?php _e( 'TOP' ); ?></a> | <a href="#kpcp_desc"><?php _e( 'DESCRIPTION' ); ?></a> | <a href="#kpcp_features"><?php _e( 'FEATURES' ); ?></a> | <a href="#kpcp_settings"><?php _e( 'SETTINGS' ); ?></a> | <a href="#kpcp_api_settings"><?php _e( 'API/SERVER SETTINGS' ); ?></a> | <a href="#kpcp_cron_settings"><?php _e( 'CRON Action SETTINGS' ); ?></a> | <a href="#kpcp_in_the_works"><?php _e( 'IN THE WORKS' ); ?></a></p>
        <ul>
            <li>
                <strong><?php _e( 'Remote Redis Server' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:' ); ?> <code>remote_redis</code></li>
                    <li><?php _e( 'Do you want to configure Redis servers to be purged?' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Redis Servers - Server' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:' ); ?> <code>remote_redis_servers['remote_redis_server']</code></li>
                    <li><?php _e( 'Insert the servers IP address.' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Redis Servers - Port' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:' ); ?> <code>remote_redis_servers['remote_redis_port']</code></li>
                    <li><?php _e( 'Insert the servers port.' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Remote Memcache Server' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:' ); ?> <code>remote_memcache</code></li>
                    <li><?php _e( 'Do you want to configure Memcache servers to be purged?' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Memcache Servers - Server' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:' ); ?> <code>remote_memcache_servers['remote_memcache_server']</code></li>
                    <li><?php _e( 'Insert the servers IP address.' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Memcache Servers - Port' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:' ); ?> <code>remote_memcache_servers['remote_memcache_port']</code></li>
                    <li><?php _e( 'Insert the servers port.' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Remote Memcached Server' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:' ); ?> <code>remote_memcached</code></li>
                    <li><?php _e( 'Do you want to configure Memcached servers to be purged?' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Memcached Servers - Server' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:' ); ?> <code>remote_memcached_servers['remote_memcached_server']</code></li>
                    <li><?php _e( 'Insert the servers IP address.' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Memcached Servers - Port' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:' ); ?> <code>remote_memcached_servers['remote_memcached_port']</code></li>
                    <li><?php _e( 'Insert the servers port.' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Service API Keys' ); ?></strong>
                <ul>
                    <li>
                        <strong><?php _e( 'Cloudflare Token' ); ?></strong>
                        <ul>
                            <li><?php _e( 'Option(s) Name:' ); ?> <code>service_api_keys['cloudflare_token']</code></li>
                            <li><?php _e( 'Enter your Cloudflare API Token. If you do not have one, you can create one here: <a href="https://dash.cloudflare.com/profile/api-tokens" target="_blank">https://dash.cloudflare.com/profile/api-tokens</a><br /><strong>NOTE: </strong>This is stored in plain-text.' ); ?></li>
                        </ul>
                    </li>
                    <li>
                        <strong><?php _e( 'Cloudflare Zone' ); ?></strong>
                        <ul>
                            <li><?php _e( 'Option(s) Name:' ); ?> <code>service_api_keys['cloudflare_zone']</code></li>
                            <li><?php _e( 'Enter your Cloudflare Zone ID. You can find this by clicking into your websites overview in your account: <a href="https://dash.cloudflare.com/" target="_blank">https://dash.cloudflare.com/</a><br /><strong>NOTE: </strong>This is stored in plain-text.' ); ?></li>
                        </ul>
                    </li>
                    <li>
                        <strong><?php _e( 'Sucuri Key' ); ?></strong>
                        <ul>
                            <li><?php _e( 'Option(s) Name:' ); ?> <code>service_api_keys['sucuri_key']</code></li>
                            <li><?php _e( 'Enter your Sucuri API Key. If you do not have one, you can find it in your site\'s Firewall here: <a href="https://waf.sucuri.net/" target="_blank">https://waf.sucuri.net/</a>. Click into your site, then Settings, then API.<br /><strong>NOTE: </strong>This is stored in plain-text.' ); ?></li>
                        </ul>
                    </li>
                    <li>
                        <strong><?php _e( 'Sucuri Secret' ); ?></strong>
                        <ul>
                            <li><?php _e( 'Option(s) Name:' ); ?> <code>service_api_keys['sucuri_secret']</code></li>
                            <li><?php _e( 'Enter your Sucuri API Secret. If you do not have one, you can find it in your site\'s Firewall here: <a href="https://waf.sucuri.net/" target="_blank">https://waf.sucuri.net/</a>. Click into your site, then Settings, then API.<br /><strong>NOTE: </strong>This is stored in plain-text.' ); ?></li>
                        </ul>
                    </li>
                </ul>
            </li>

        </ul>
        <h2 id="kpcp_cron_settings"><?php _e( 'CRON Action Settings' ); ?></h2>
        <p class="kpcp_nav"><a href="#kpcp_top"><?php _e( 'TOP' ); ?></a> | <a href="#kpcp_desc"><?php _e( 'DESCRIPTION' ); ?></a> | <a href="#kpcp_features"><?php _e( 'FEATURES' ); ?></a> | <a href="#kpcp_settings"><?php _e( 'SETTINGS' ); ?></a> | <a href="#kpcp_api_settings"><?php _e( 'API/SERVER SETTINGS' ); ?></a> | <a href="#kpcp_cron_settings"><?php _e( 'CRON Action SETTINGS' ); ?></a> | <a href="#kpcp_in_the_works"><?php _e( 'IN THE WORKS' ); ?></a></p>
        <ul>
            <li>
                <strong><?php _e( 'Allow Scheduled Purges?' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:' ); ?> <code>cron_schedule_allowed</code></li>
                    <li><?php _e( 'Should the cached be purged based on a Wordpress Cron schedule?' ); ?></li>
                </ul>
            </li>
            <li>
                <strong><?php _e( 'Built-In Schedule' ); ?></strong>
                <ul>
                    <li><?php _e( 'Option(s) Name:' ); ?> <code>cron_schedule_builtin</code></li>
                    <li><?php _e( 'Select a built-in schedule to purge the caches on.' ); ?></li>
                </ul>
            </li>
        </ul>
        <h2 id="kpcp_in_the_works"><?php _e( 'In The Works' ); ?></h2>
        <p class="kpcp_nav"><a href="#kpcp_top"><?php _e( 'TOP' ); ?></a> | <a href="#kpcp_desc"><?php _e( 'DESCRIPTION' ); ?></a> | <a href="#kpcp_features"><?php _e( 'FEATURES' ); ?></a> | <a href="#kpcp_settings"><?php _e( 'SETTINGS' ); ?></a> | <a href="#kpcp_api_settings"><?php _e( 'API/SERVER SETTINGS' ); ?></a> | <a href="#kpcp_cron_settings"><?php _e( 'CRON Action SETTINGS' ); ?></a> | <a href="#kpcp_in_the_works"><?php _e( 'IN THE WORKS' ); ?></a></p>
        <ul>
            <li><?php _e( 'WooCommerce Product Updates (<em>and exclusions</em>)' ); ?></li>
            <li><?php _e( 'WooCommerce Order Updates' ); ?></li>
            <li><?php _e( 'More Plugin References' ); ?></li>
            <li><?php _e( 'More Hosting References' ); ?></li>
        </ul>
    </main>
    <footer></footer>
</article>
