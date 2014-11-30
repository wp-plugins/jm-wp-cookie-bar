<?php
if ( ! defined( 'WPCB_VERSION' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}

if( !class_exists('WPCB_Tool_Page') ) {

    class WPCB_Tool_Page{

        protected $WPCB_screen_name;
        protected static $instance;

        static function GetInstance()
        {
          
            if (!isset(self::$instance))
            {
              self::$instance = new self();
            }

            return self::$instance;
        }

        public function init()
        {
            add_action('admin_menu', array( $this, 'add_menu_page') );
            
        }

       /*
        * ADMIN PAGE IN TOOL MENU
       */
       public function add_menu_page()
       {

        $this->WPCB_screen_name = 
            add_management_page( 
                    __('Cookie Bar', WPCB_SLUG),
                    __('Cookie Bar', WPCB_SLUG), 
                    'manage_options', 
                   strtolower( __CLASS__ ), 
                    array($this, 'admin_page')
            );

        register_setting( WPCB_SLUG, 'jm_wpcb' );

       }


       public function admin_page()
       {

        $opts = self::get_options();
        require( WPCB_DIR . 'views/settings.php' );
		
       }

        /*
        * OPTIONS TREATMENT
        */

        // Process options when submitted
        protected static function sanitize($options) 
        {
            return array_merge(self::get_options(), self::sanitize_options($options));
        }

        // Sanitize options
        protected static function sanitize_options($options) 
        {
            $new = array();

            if ( !is_array($options) )
            return $new;

            if ( isset($options['closeClass']) )
                $new['closeClass']          = sanitize_html_class( $options['closeClass'] );
            if ( isset($options['cookieBarExpire']) )
                $new['cookieBarExpire']     = (int) $options['cookieBarExpire'];
            if ( isset($options['cookieBarText']) )
                $new['cookieBarText']       = esc_attr(strip_tags( $options['cookieBarText'] ));
            if ( isset($options['cookieRulesUrl']) )
                $new['cookieRulesUrl']      = esc_url( $options['cookieRulesUrl'] );
            if ( isset($options['cookieBarPosition']) )
                $new['cookieBarPosition']   = $options['cookieBarPosition'];
            if ( isset($options['cookieBarStyle']) )
                $new['cookieBarStyle']      = $options['cookieBarStyle'];
            
            return $new;
        }


        // Retrieve and sanitize options
        protected static function get_options() 
        {
            $options = get_option( 'jm_wpcb' );
            return array_merge(WPCB_Init::get_default_options(), self::sanitize_options($options));
        }

    }

}