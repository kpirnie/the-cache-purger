<?php

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

/*
Plugin Name:  The Cache Purger
Plugin URI:   https://kevinpirnie.com
Description:  Plugin attemps to clear all plugin based and server based caches.
Version:      0.8.09
Author:       Kevin C Pirnie
License:      GPLv3
License URI:  https://www.gnu.org/licenses/gpl-3.0.html
*/

// setup the full page to this plugin
define( 'TCP_PATH', dirname( __FILE__ ) );

// setup the directory name
define( 'TCP_DIRNAME', basename( dirname( __FILE__ ) ) );

// setup the primary plugin file name
define( 'TCP_FILENAME', basename( __FILE__ ) );

// Include our "work"
require TCP_PATH . '/work/common.php';
