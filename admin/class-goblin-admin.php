<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.goblincompute.com/
 * @since      1.0.0
 *
 * @package    Goblin
 * @subpackage Goblin/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Goblin
 * @subpackage Goblin/admin
 * @author     Goblin <bsousaf@gmail.com>
 */
class Goblin_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $error_message;

	private $options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->options = get_option( 'goblin_settings' );

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Goblin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Goblin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/goblin-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Goblin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Goblin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/goblin-admin.js', array( 'jquery' ), $this->version, true );

	}



	/*
	 * Register the settings
	 */
	
	public function goblin_register_settings(){
	    //this will save the option in the wp_options table as 'goblin_settings'
	    //the third parameter is a function that will validate your input values
	    register_setting('goblin_settings', 'goblin_settings', array($this,'goblin_settings_validate'));
	}

	public function goblin_settings_validate($args){
	    //$args will contain the values posted in your settings form, you can validate them as no spaces allowed, no special chars allowed or validate emails etc.
	    if(!isset($args['goblin_wallet']) || empty($args['goblin_wallet'])){
	        //add a settings error because the email is invalid and make the form field blank, so that the user can enter again
	        $args['goblin_wallet'] = '';
	    	add_settings_error('goblin_settings', 'goblin_invalid_wallet', 'Please enter a valid Monero wallet!', $type = 'error');   
	    }

	    if(!isset($args['goblin_secret']) || empty($args['goblin_secret'])){
	        //add a settings error because the email is invalid and make the form field blank, so that the user can enter again
	        $args['goblin_secret'] = '';
	    	add_settings_error('goblin_settings', 'goblin_invalid_secret', 'Please enter a valid secret folder!', $type = 'error');   
	    }

	    //make sure you return the args
	    return $args;
	}

	//Display the validation errors and update messages
	/*
	 * Admin notices
	 */
	
	public function goblin_admin_notices(){
	   settings_errors();
	}

	public function goblin_display_error() {
		add_action( 'admin_notices', array($this, 'error_notice') );
	}


	public function goblin_setup_menu(){
	        add_menu_page( 'Goblin Settings', 'Goblin', 'manage_options', 'goblin-plugin', array($this,'goblin_dashboard_init') );
	}
	 
	public function goblin_dashboard_init(){
	        ?>
		        <div class="dashgoblin">
		        <h1>Goblin Settings</h1>

			        <?php
				        $goblin_handle_post = $this->goblin_handle_post();

				        settings_fields( 'goblin_settings' );
				        do_settings_sections( __FILE__ );

				        $this->options = get_option( 'goblin_settings' );
				        
				        $this->get_token_form();
				        $this->get_wallet_display();
				        $this->get_enable_form();
					?>

				</div>
			<?php
	}


	public function get_enable_form() {
		?>
		<form method="post" id="enable_goblin" class="<?php echo (!empty($this->options['goblin_token']) ? '' : 'hidden'); ?>">

	    	<h2>Embed settings</h2>
		    <table>
		    	<tr>
		                <th scope="row">Enable Goblin site-wide</th>
		                <td>
		                    <fieldset>
		                        <label>
		                            <input name="goblin_settings[goblin_enabled]" type="checkbox" id="goblin_enabled" value="<?php echo (!empty($this->options['goblin_enabled']) ? $this->options['goblin_enabled'] : 'true'); ?>" <?php echo ($this->options['goblin_enabled'] === true ? 'checked' : ''); ?> />

		                            <input name="goblin_settings[goblin_token]" type="hidden" id="goblin_token" value="<?php echo $this->options['goblin_token']; ?>" />
		                            <input name="goblin_settings[goblin_wallet]" type="hidden" id="goblin_wallet" value="<?php echo $this->options['goblin_wallet']; ?>" />
		                            <input name="goblin_toggle" type="hidden" id="goblin_toggle" value="1" />
		                            
		                        </label>
		                    </fieldset>
		                </td>
		            </tr>
		    </table>
	    	<?php submit_button('Save'); ?>
	    </form>
	    <?php
	}

	public function get_wallet_display() {
		?>
		<div id="wallet_display" class="<?php echo (!empty($this->options['goblin_wallet']) ? '' : 'hidden'); ?>">
						<h2>Basic Setup</h2>
					    <table>
					    	<tr>
				                <th scope="row">Monero Address</th>
				                <td>
				                    <fieldset>
				                        <label>
				                            <input name="goblin_settings[goblin_wallet]" type="text" id="goblin_wallet" value="<?php echo $this->options['goblin_wallet']; ?>" disabled />
				                        </label>
				                    </fieldset>

				                    
				                </td>
				                <td><a href="#" class="change_wallet">Change wallet address</a></td>
					        </tr>
					    </table>


					    <table id="token_display" class="<?php echo (!empty($this->options['goblin_token']) ? '' : 'hidden'); ?>">
					    	<tr>
					                <th scope="row">Token</th>
					                <td>
					                    <fieldset>
					                        <label>
					                            <input name="goblin_settings[goblin_token]" type="text" id="goblin_token" value="<?php echo $this->options['goblin_token']; ?>" disabled />
					                        </label>
					                    </fieldset>

					                    
					                </td>
					            </tr>
					    </table>
				    </div>
		<?php
	}

	public function get_token_form() {
		?>
			<form method="post" id="token_form" class="<?php echo (!$this->options['goblin_wallet'] ? '' : 'hidden'); ?>">

	        	<?php
		        
		        	$default_wallet = '';
		        	$default_secret = get_bloginfo('url').'/goblin-secret/';

		        ?>

		        
		        <h2>Generate Token</h2>
		        <table class="form-table">
		            <tr>
		                <th scope="row">Monero Address</th>
		                <td>
		                    <fieldset>
		                        <label>
		                            <input name="goblin_settings[goblin_wallet]" type="text" id="goblin_wallet" value="<?php echo (isset($this->options['goblin_wallet']) && $this->options['goblin_wallet'] != '') ? $this->options['goblin_wallet'] : $default_wallet; ?>"/>

		                            <input name="goblin_settings[goblin_secret]" type="hidden" id="goblin_secret" value="<?php echo (isset($this->options['goblin_secret']) && $this->options['goblin_wallet'] != '') ? $this->options['goblin_secret'] : $default_secret; ?>" />
		                            <br />
		                            <span class="description">Please enter a valid Monero address.</span>
		                        </label>
		                    </fieldset>

		                    
		                </td>
		            </tr>
		        </table>
		        <?php submit_button('Get token'); ?>

		        <?php echo (!$this->options['goblin_wallet'] ? '' : '<p><a href="#" class="cancel_wallet">Cancel</a></p>'); ?>
		    </form>
		<?php
	}


	public function goblin_handle_post(){
        /*

		POST https://api.goblincompute.com/v0/token
		Content-Type: application/json

		{
		  "address": "49JyyzdZ1t6VYbMSL6cjAQccdYQhkQyAjGHCoC8Crz5D1zx33eWxMQrZxDKmsRFAKqQMDo785hQ8KckkdkDNYcJuTDPtt4z",
		  "base_url": "https://example.com/white-rabbit-horse-staple"
		}

        */
		$this->options = get_option( 'goblin_settings' );
        
        $error = null;

        $data_array = array(
        	'address' => '',
        	'base_url' => get_bloginfo('url').'/goblin-secret/',
        	'enabled' => false
        );

        //var_dump($_POST['goblin_settings']['goblin_wallet']);

        if(isset($_POST['goblin_settings']['goblin_wallet'])){ 
                $data_array['address'] = $_POST['goblin_settings']['goblin_wallet'];

                // change site-wide flag as well
                if(isset($_POST['goblin_settings']['goblin_enabled'])) {
                	$data_array['enabled'] = filter_var($_POST['goblin_settings']['goblin_enabled'], FILTER_VALIDATE_BOOLEAN);
                }
        } else {
        	return false;
        }

        if(!isset($_POST['goblin_toggle'])):
	        $url = 'https://api.goblincompute.com/v0/token';
	        $data = wp_remote_post($url, array(
			    'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
			    'body'        => json_encode($data_array),
			    'method'      => 'POST',
			    'data_format' => 'body',
			));

	        
			$data = json_decode($data['body']);

			$pattern = '/^error/i';
			$haserror = @preg_match($pattern, $data, $matches, PREG_OFFSET_CAPTURE);

			if($haserror) {
				$this->error_message = $data;
				$this->error_notice();
				return $data;
			}
    	endif;



        update_option( 'goblin_settings', array(
        	'goblin_token' => (isset($_POST['goblin_toggle']) ? $this->options['goblin_token'] : $data->token),
        	'goblin_wallet' => $data_array['address'],
        	'goblin_secret' => $data_array['base_url'],
        	'goblin_enabled' => $data_array['enabled']
        	) 
        );
    }

    public function error_notice() {
	    ?>
	    <div class="error notice is-dismissible">
	        <p><?php echo $this->error_message; ?></p>
	        <button type="button" class="notice-dismiss">
				<span class="screen-reader-text">Dismiss this notice.</span>
			</button>
	    </div>
	    <?php
	}
}
