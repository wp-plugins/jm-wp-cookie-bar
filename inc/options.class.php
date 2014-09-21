<?php

if ( ! defined( 'WPCB_VERSION' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}

if( !class_exists('WPCB_Tool_Page') ) {

    class WPCB_Tool_Page{

        public $textdomain = 'jm-wpcb';
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
                    __('Cookie Bar', $this->textdomain),
                    __('Cookie Bar', $this->textdomain), 
                    'manage_options', 
                   strtolower( __CLASS__ ), 
                    array($this, 'admin_page')
            );

         register_setting( 'jm-wpcb', 'jm_wpcb' );

       }


       public function admin_page()
       {

        $opts = $this->get_options();
        ?>
        <div class="wrap">
        <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
        <?php if ( isset( $_GET['settings-updated'] ) ) echo "<div class='updated'><p>".__('Settings saved.')."</p></div>"; ?>

           <form class="jm-wpcb-form" method="POST" action="options.php">
            <?php settings_fields('jm-wpcb'); ?>
                <fieldset>
                    <legend><?php _e('Options'); ?></legend> 
                    <p>
                        <label for="closeClass"><?php _e('Which CSS class do you want to add to style the close button? (default is .wpcb-close-btn)', $this->textdomain); ?> :</label><br />
                        <input id="closeClass" type="text" name="jm_wpcb[closeClass]" size="50" value="<?php echo sanitize_html_class($opts['closeClass']); ?>" />
                    </p>
                    <p>
                        <label for="cookieBarExpire"><?php _e('Set the cookie expiration time? (default is 365 days)', $this->textdomain); ?> :</label><br />
                        <input id="cookieBarExpire" type="number" name="jm_wpcb[cookieBarExpire]" min="365" max="400" value="<?php echo (int) $opts['cookieBarExpire']; ?>" />
                    </p>                    
                    <p>
                        <label for="cookieBarText"><?php _e('The text to be shown. (120 chars at most)', $this->textdomain); ?> :</label><br />
                        <textarea id="cookieBarText" maxlenght="120" type="text" rows="8" cols="100" name="jm_wpcb[cookieBarText]"><?php echo esc_textarea(strip_tags($opts['cookieBarText'])); ?></textarea>
                    </p>
                    <p>
                        <label for="ccookieRulesUrl"><?php _e('URL for your cookie rules', $this->textdomain); ?> :</label><br />
                        <input id="cookieRulesUrl" type="url" size="100" name="jm_wpcb[cookieRulesUrl]" value="<?php echo esc_url($opts['cookieRulesUrl']); ?>"/>
                    </p>
                    <p>
                        <label for="cookieBarPosition"><?php _e('Which position?', $this->textdomain); ?> :</label>
                        <select class="styled-select" id="cookieBarPosition" name="jm_wpcb[cookieBarPosition]">
                            <option value="top" <?php echo $opts['cookieBarPosition'] == 'top' ? 'selected="selected"' : ''; ?> ><?php _e('top of the page', $this->textdomain); ?></option>
                            <option value="bottom" <?php echo $opts['cookieBarPosition'] == 'bottom' ? 'selected="selected"' : ''; ?> ><?php _e('bottom of the page', $this->textdomain); ?></option>
                        </select>
                        <br /><em>(<?php _e('Default is top of the page', $this->textdomain); ?>)</em>
                    </p>
                     <p>
                        <label for="cookieBarStyle"><?php _e('Include basic styles?', $this->textdomain); ?> :</label>
                        <select class="styled-select" id="cookieBarStyle" name="jm_wpcb[cookieBarStyle]">
                            <option value="yes" <?php echo $opts['cookieBarStyle'] == 'yes' ? 'selected="selected"' : ''; ?> ><?php _e('yes', $this->textdomain); ?></option>
                            <option value="no" <?php echo $opts['cookieBarStyle'] == 'no' ? 'selected="selected"' : ''; ?> ><?php _e('no', $this->textdomain); ?></option>
                        </select>
                        <br /><em>(<?php _e('Default is yes. Be careful, this could mess up with bar position so do not forget to add required styles in your own stylesheet.', $this->textdomain); ?>)</em>
                    </p>
                    <?php submit_button(null, 'primary', '_submit'); ?>
                </fieldset>     
            </form>          
        </div>
        <?php
       }

        /*
        * OPTIONS TREATMENT
        */

        // Process options when submitted
        protected function sanitize($options) 
        {
            return array_merge($this->get_options(), $this->sanitize_options($options));
        }

        // Sanitize options
        protected function sanitize_options($options) 
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
        protected function get_options() 
        {
            $options = get_option( 'jm_wpcb' );
            return array_merge($this->get_default_options(), $this->sanitize_options($options));
        }

        public function get_default_options() 
        {
            return array(
            'closeClass' => 'wpcb-close-btn',
            'cookieBarPosition' => 'top',
            'cookieBarStyle' => 'yes',
            'cookieRulesUrl' => '',
            'cookieBarText' => __('Cookies help us deliver our services. By using our services, you agree to our use of cookies.', $this->textdomain),
            'cookieBarExpire' => '365'
            );
        }

    }

}

$WPCB_Tool_Page = WPCB_Tool_Page::GetInstance();
$WPCB_Tool_Page->init();