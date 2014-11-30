<?php
/*
Plugin Name: JM WP Cookie Bar
Plugin URI: http://tweetpress.fr
Description: Because it's mandatory! You have to warn you users you use cookies for example with Google Analytics
Version: 1.5
Author: Julien Maury
Author URI: http://tweetpressfr.github.io
Text Domain: jm-wpcb
Domain Path: /languages
*/
/*
License: GPL v3

JM WP Cookie Bar
Copyright (C) 2014, Julien Maury - contact@tweetpress.fr

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


defined('ABSPATH') or die('No direct load!');

define( 'WPCB_VERSION', '1.5' );
define( 'WPCB_DIR', plugin_dir_path( __FILE__ )  );
define( 'WPCB_URL', plugin_dir_url( __FILE__ ) );
define( 'WPCB_CSS_URL', WPCB_URL. 'assets/css/' );
define( 'WPCB_JS_URL', WPCB_URL. 'assets/js/' );
define( 'WPCB_IMG_URL', WPCB_URL. 'assets/img/' );
define( 'WPCB_SLUG', 'jm-wpcb' );
define( 'WPCB_LANG_DIR', dirname( plugin_basename(__FILE__) ) . '/languages/');

/*
 Sources : https://www.youtube.com/watch?v=kuwnvCTIcf8  // great tutorial
 		   https://github.com/carhartl/jquery-cookie
*/

//Call modules
if( is_admin() ) {

	require( WPCB_DIR.'/classes/options.class.php' ); 
	require( WPCB_DIR.'/classes/init.class.php' );
} 

require( WPCB_DIR.'/classes/main.php' );  

// Early init
add_action('plugins_loaded','_wpcb_early_init');
function _wpcb_early_init() {

	if( is_admin() ) {

		$WPCB_Tool_Page = WPCB_Tool_Page::GetInstance();
		$WPCB_Init = WPCB_Init::GetInstance();
		$WPCB_Tool_Page->init(); 

	}

	// Language support
	load_plugin_textdomain( 'jm-wpcb', false, WPCB_LANG_DIR );
	
}

// On activation
register_activation_hook( __FILE__, array( 'WPCB_Init', 'activate' ) );