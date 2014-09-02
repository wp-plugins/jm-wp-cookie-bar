<?php
/*
Plugin Name: JM WP Cookie Bar
Plugin URI: http://tweetpress.fr
Description: Because it's mandatory! You have to warn you users you use cookies for example with Google Analytics
Version: 1.1
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

define( 'WPCB_VERSION', '1.0' );
define( 'WPCB_DIR', plugin_dir_path( __FILE__ )  );
define( 'WPCB_URL', plugin_dir_url( __FILE__ ) );
define( 'WPCB_INC_DIR', trailingslashit( WPCB_DIR . 'inc') );
define( 'WPCB_CSS_URL', trailingslashit( WPCB_URL. 'css' ) );
define( 'WPCB_JS_URL', trailingslashit( WPCB_URL. 'js' ) );
define( 'WPCB_IMG_URL', trailingslashit( WPCB_URL. '/img' ) );
define( 'WPCB_LANG_DIR', dirname( plugin_basename(__FILE__) ) . '/languages/');

/*
 Sources : https://www.youtube.com/watch?v=kuwnvCTIcf8  // great tutorial
 		   https://github.com/carhartl/jquery-cookie
*/

//Call modules 
add_action('plugins_loaded','_WPCB_init');
function _wpcb_init() {

	if( is_admin() ) {
		require( WPCB_INC_DIR.'options.class.php' );  
	} else{
		require( WPCB_INC_DIR.'main.php' );  
	}
}

//
function _wpcb_get_default_options() {
    return array(
    'closeClass'     => 'wpcb-close-btn',
    'cookieBarPosition' => 'top',
    'cookieBarStyle' => 'yes',
    'cookieRulesUrl' => '',
    'cookieBarText'  => __('Cookies help us deliver our services. By using our services, you agree to our use of cookies.', 'jm-wpcb'),
    'cookieBarExpire' => '365'
    );
}

//On activation
function _wpcb_on_activation() {
	$opts = get_option( 'jm_wpcb' );
	if ( !is_array($opts) )
	update_option( 'jm_wpcb', _wpcb_get_default_options() );
}

register_activation_hook( __FILE__, '_wpcb_activate' );

function _wpcb_activate() {
	if( !is_multisite() ) {
		
		_wpcb_on_activation();
	
	} else {
	    // For regular options.
		global $wpdb;
		$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );
		foreach ( $blog_ids as $blog_id ) 
		{
			switch_to_blog( $blog_id );
			_wpcb_on_activation();
			restore_current_blog();
		}
	
	}
	
}

// Language support
add_action( 'init', '_wpcb_lang_init' );
function _wpcb_lang_init() {

	load_plugin_textdomain( 'jm-wpcb', false, WPCB_LANG_DIR );
	
}
