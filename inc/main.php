<?php
if ( ! defined( 'WPCB_VERSION' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}

// Scripts
add_action( 'wp_enqueue_scripts', '_wpcb_scripts' );
function _wpcb_scripts() {

	wp_register_style( 'wp-cookiebar-style', WPCB_CSS_URL.'basic-style.css' );
	
	$opts = get_option('jm_wpcb');
	if( $opts['cookieBarStyle'] == 'yes' )
		wp_enqueue_style( 'wp-cookiebar-style' );
	
	wp_enqueue_script( 'wp-cookiebar-tags', WPCB_JS_URL.'jquery.cookiebar.js', array('jquery'), null, false);

}

add_action( 'wp_footer', '_wpcb_add_cookiebar');
function _wpcb_add_cookiebar(){

	$opts  = get_option('jm_wpcb');
	$class = sanitize_html_class($opts['closeClass']);
	$text  = esc_textarea(strip_tags($opts['cookieBarText']));
	$last  = (int) $opts['cookieBarExpire'];
	$posi  = $opts['cookieBarPosition'];
	$url   = esc_url($opts['cookieRulesUrl']);

	$script = '
		<script>
			(function( $ ){
				// cookies
			    $(document).ready(function(){
			    	if( $.cookie("cookiebar") === undefined ){

			    		$( ".wpcb-cookie-bar" ).append( "<div id=\"cookie-message\" class=\"cookie-message\">'.$text.' <a id=\"cookie-rules\" class=\"cookie-rules\" href=\"'.$url.'\">'.__('Read more', 'jm-wpcb').'</a><div id=\"cookie-btn\" class=\"'.$class.'\">Ok</div></div>" );
			    		$( "#cookie-message" ).css( "'.$posi.'", "0"  );

			    		$( "#cookie-btn" ).click( function(e){
			    			e.preventDefault();
			    			$( "#cookie-message" ).fadeOut();
			    			$.cookie("cookiebar", "viewed", { expires: '.$last.' } );
			    		});
					}
			    });
			})(jQuery);
	  	</script>
	  	';

	 if ( wp_script_is( 'wp-cookiebar-tags', 'done' ) ) 
	 	echo $script;
 
}


// body class
add_filter( 'body_class', '_wpcb_class_names' );
function _wpcb_class_names( $classes ) {
	// add 'class-name' to the $classes array
	$classes[] = 'wpcb-cookie-bar';
	// return the $classes array
	return $classes;
}