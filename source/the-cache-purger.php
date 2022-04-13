<?php

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

/*
Plugin Name:    The Cache Purger
Plugin URI:     https://kevinpirnie.com
Description:    Plugin attemps to clear all plugin based and server based caches.
Version:        1.1.01
Requires PHP:   7.4
Network:        false
Author:         Kevin C Pirnie
Text Domain:    the-cache-purger
License:        GPLv3
License URI:    https://www.gnu.org/licenses/gpl-3.0.html
*/

// setup the full page to this plugin
define( 'TCP_PATH', dirname( __FILE__ ) );

// setup the directory name
define( 'TCP_DIRNAME', basename( dirname( __FILE__ ) ) );

// setup the primary plugin file name
define( 'TCP_FILENAME', basename( __FILE__ ) );

// Include our "work"
require TCP_PATH . '/work/common.php';
