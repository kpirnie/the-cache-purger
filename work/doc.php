<?php
/** 
 * Documentation
 * 
 * The plugin documentation
 * 
 * @since 7.3
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Kevin's Framework
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

?>

<article class="kpfw-doc-content" id="kpcp_top">
    <header>
        <h1>The Cache Purger Documentation</h1>
    </header>
    <main>
        <h2 id="kpcp_desc">Description</h2>
        <p class="kpcp_nav"><a href="#kpcp_top">TOP</a> | <a href="#kpcp_desc">DESCRIPTION</a> | <a href="#kpcp_features">FEATURES</a> | <a href="#kpcp_settings">SETTINGS</a> | <a href="#kpcp_in_the_works">IN THE WORKS</a></p>
        <p>This plugin attempts to purge all server-side caching methods.</p>
        <p>This includes the most common caching plugins, some hosting based caches, most server based caches, built-in Wordpress object caches, and even simple file based caches</p>
        <p>Just configure what you want to purge on, and the plugin will take care of the rest.</p>
        <h2 id="kpcp_features">Features</h2>
        <p class="kpcp_nav"><a href="#kpcp_top">TOP</a> | <a href="#kpcp_desc">DESCRIPTION</a> | <a href="#kpcp_features">FEATURES</a> | <a href="#kpcp_settings">SETTINGS</a> | <a href="#kpcp_in_the_works">IN THE WORKS</a></p>
        <h3>Built in automatic cache purging for the following caches</h3>
        <ul>
            <li><strong>Plugins</strong>
                <ul>
                    <li>SiteGround Optimizer</li>
                    <li>Nginx Helper</li>
                    <li>LiteSpeed Cache</li>
                    <li>Cachify, Autoptimize</li>
                    <li>Fast Velocity Minify</li>
                    <li>WP Rocket</li>
                    <li>Swift Performance</li>
                    <li>Comet Cache</li>
                    <li>Hummingbird</li>
                    <li>WP Fastest Cache</li>
                    <li>WP Super Cache</li>
                    <li>W3 Total Cache</li>
                    <li>Hyper Cache</li>
                    <li>WP Optimize</li>
                    <li>Cache Enabler</li>
                </ul>
            </li>
            <li><strong>Hosting / CDN</strong>
                <ul>
                    <li>WPEngine</li>
                    <li>Kinsta</li>
                    <li>GoDaddy Managed Wordpress</li>
                    <li>Pantheon</li>
                    <li>CloudFlare</li>
                    <li>Sucuri</li>
                    <li>RunCloud</li>
                    <li><em>Some of these are dependant on separate plugins.  Please see your provider if it is necessary, or already included</em></li>
                </ul>
            </li>
            <li><strong>Server Based</strong>
                <ul>
                    <li>PHP FPM</li>
                    <li>Zend Opcache</li>
                    <li>APC and APCU</li>
                    <li>WinCache</li>
                    <li>Pagespeed Module</li>
                    <li>nGinx</li>
                    <li>Static File Caches</li>
                </ul>
            </li>
            <li><strong>Wordpress Built-In</strong> object caching and persistent object caching</li>
        </ul>
        
        <h3>Purges are configurable in the settings, and include the following saves/updates/trashes:</h3>
        <ul>
            <li>Posts</li>
            <li>Pages</li>
            <li>Custom Post Types</li>
            <li>Categories</li>
            <li>Taxonomies</li>
            <li>Widgets</li>
            <li>Menus</li>
            <li>Plugins</li>
            <li>Updates</li>
            <li>Settings & Options</li>
            <li>GravityForms (<em>if installed and activated</em>)</li>
            <li>Advanced Custom Fields (<em>if installed and activated</em>)</li>
            <li>WooCommerce Settings (<em>if installed and activated</em>)</li>
        </ul>
        <h2 id="kpcp_settings">Settings</h2>
        <p class="kpcp_nav"><a href="#kpcp_top">TOP</a> | <a href="#kpcp_desc">DESCRIPTION</a> | <a href="#kpcp_features">FEATURES</a> | <a href="#kpcp_settings">SETTINGS</a> | <a href="#kpcp_in_the_works">IN THE WORKS</a></p>
        <ul>
            <li>
                <strong>Log Purge Actions?</strong>
                <ul>
                    <li>Option(s) Name: <code>should_log</code></li>
                    <li>Do you want to log purge actions?  The log file will be located here: <code><?php _e( ABSPATH . 'wp-content/purge.log' ); ?></code></li>
                </ul>
            </li>
            <li>
                <strong>Purge on Menu?</strong>
                <ul>
                    <li>Option(s) Name: <code>on_menu</code></li>
                    <li>This will attempt to purge all caches for every menu update, save, or delete.</li>
                </ul>
            </li>
            <li>
                <strong>Purge on Post?</strong>
                <ul>
                    <li>Option(s) Name: <code>on_post</code></li>
                    <li>This will attempt to purge all caches for every post update, save, or delete.</li>
                </ul>
            </li>
            <li>
                <strong>Excluded Posts</strong>
                <ul>
                    <li>Option(s) Name: <code>on_post_exclude</code></li>
                    <li>Select the posts you wish to exclude from the cache purge actions.</li>
                </ul>
            </li>
            <li>
                <strong>Purge on Page?</strong>
                <ul>
                    <li>Option(s) Name: <code>on_page</code></li>
                    <li>This will attempt to purge all caches for every page update, save, or delete.</li>
                </ul>
            </li>
            <li>
                <strong>Excluded Pages</strong>
                <ul>
                    <li>Option(s) Name: <code>on_page_exclude</code></li>
                    <li>Select the pages you wish to exclude from the cache purge actions.</li>
                </ul>
            </li>
            <li>
                <strong>Purge on CPT?</strong>
                <ul>
                    <li>Option(s) Name: <code>on_cpt</code></li>
                    <li>This will attempt to purge all caches for every custom post type update, save, or delete.</li>
                </ul>
            </li>
            <li>
                <strong>Exclude CPT</strong>
                <ul>
                    <li>Option(s) Name: <code>on_cpt_exclude</code></li>
                    <li>Select the custom post types you wish to exclude from the cache purge actions.</li>
                </ul>
            </li>
            <li>
                <strong>Purge on Term/Taxonomy?</strong>
                <ul>
                    <li>Option(s) Name: <code>on_taxonomy</code></li>
                    <li>This will attempt to purge all caches for every taxonomy/term update, save, or delete.</li>
                </ul>
            </li>
            <li>
                <strong>Purge on Category?</strong>
                <ul>
                    <li>Option(s) Name: <code>on_category</code></li>
                    <li>This will attempt to purge all caches for every category update, save, or delete.</li>
                </ul>
            </li>
            <li>
                <strong>Purge on Widget?</strong>
                <ul>
                    <li>Option(s) Name: <code>on_widget</code></li>
                    <li>This will attempt to purge all caches for every widget update, save, or removal.</li>
                </ul>
            </li>
            <li>
                <strong>Purge on Customizer?</strong>
                <ul>
                    <li>Option(s) Name: <code>on_customizer</code></li>
                    <li>This will attempt to purge all caches for every customizer update or save.</li>
                </ul>
            </li>
            <li>
                <strong>Purge on GravityForms?</strong>
                <ul>
                    <li>Option(s) Name: <code>on_form</code></li>
                    <li>This will attempt to purge all caches for every form update, save, or delete.</li>
                    <li><em>This option is only available if you have GravityForms installed and active on your site.</em></li>
                </ul>
            </li>
            <li>
                <strong>Exclude Forms</strong>
                <ul>
                    <li>Option(s) Name: <code>on_form_exclude</code></li>
                    <li>Select the forms you wish to exclude from the cache purge actions.</li>
                    <li><em>This option is only available if you have GravityForms installed and active on your site.</em></li>
                </ul>
            </li>
            <li>
                <strong>Purge on ACF?</strong>
                <ul>
                    <li>Option(s) Name: <code>on_acf</code></li>
                    <li>This will attempt to purge all caches for every "advanced custom field" group update, save, or delete.</li>
                    <li><em>This option is only available if you have Advanced Custom Fields installed and active on your site.</em></li>
                </ul>
            </li>
            <li>
                <strong>Exclude Field Group</strong>
                <ul>
                    <li>Option(s) Name: <code>on_acf_exclude</code></li>
                    <li>Select the field groups you wish to exclude from the cache purge actions.</li>
                    <li><em>This option is only available if you have Advanced Custom Fields installed and active on your site.</em></li>
                </ul>
            </li>
        </ul>
        <h2 id="kpcp_in_the_works">In The Works</h2>
        <p class="kpcp_nav"><a href="#kpcp_top">TOP</a> | <a href="#kpcp_desc">DESCRIPTION</a> | <a href="#kpcp_features">FEATURES</a> | <a href="#kpcp_settings">SETTINGS</a> | <a href="#kpcp_in_the_works">IN THE WORKS</a></p>
        <ul>
            <li>WooCommerce Product Updates (<em>and exclusions</em>)</li>
            <li>WooCommerce Order Updates</li>
            <li>More Plugin References</li>
            <li>More Hosting References</li>
        </ul>
    </main>
    <footer></footer>
</article>
