<?php
if ( ! defined( 'WPCB_VERSION' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}


if( ! class_exists('WPCB_Init') ) 
{

    class WPCB_Init
    {

    	protected $WPCB_Init;
        protected static $instance;


        static function GetInstance()
        {
          
            if (!isset(self::$instance))
            {
              self::$instance = new self();
            }

            return self::$instance;
        }

		//On activation
		public static function on_activation() 
		{

			$opts = get_option( 'jm_wpcb' );

			if ( !is_array($opts) )
				update_option( 'jm_wpcb', self::get_default_options() );
		}

		//
		public static function activate() 
		{
			if( !is_multisite() ) {
				
				self::on_activation();
			
			} else {
			    // For regular options.
				global $wpdb;
				$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );
				foreach ( $blog_ids as $blog_id ) 
				{
					switch_to_blog( $blog_id );
					self::on_activation();
					restore_current_blog();
				}
			
			}
			
		}

		/*
		* Default options
		*/
		public static function get_default_options() 
        {
            return array(
            'closeClass' => 'wpcb-close-btn',
            'cookieBarPosition' => 'bottom',
            'cookieBarStyle' => 'yes',
            'cookieRulesUrl' => '',
            'cookieBarText' => __('Cookies help us deliver our services. By using our services, you agree to our use of cookies.', WPCB_SLUG),
            'cookieBarExpire' => '365'
            );
        }
	}
}