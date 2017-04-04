<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Piereg_API_Manager_Example_MENU{

	//private $api_manager_example_key;
	private $piereg_api_manager_key_class;

	// Load admin menu
	public function __construct() {
		$this->piereg_api_manager_key_class = new Api_Manager_Example_Key();

		add_action( 'admin_init', array( $this, 'load_settings' ) );
	}

	// Register settings
	public function load_settings() {
		global $piereg_api_manager;

		register_setting( 'api_manager_example', 'api_manager_example', array( $this, 'validate_options' ) );
		
		// API Key
		add_settings_section( 'api_key', __( 'License Information', $piereg_api_manager->piereg_text_domain ), array( $this, 'wc_am_api_key_text' ), 'api_manager_example_dashboard' );
		add_settings_field( 'api_key', __( 'License Key', $piereg_api_manager->piereg_text_domain ), array( $this, 'wc_am_api_key_field' ), 'api_manager_example_dashboard', 'api_key' );
		add_settings_field( 'api_email', __( 'License email', $piereg_api_manager->piereg_text_domain ), array( $this, 'wc_am_api_email_field' ), 'api_manager_example_dashboard', 'api_key' );

		// Activation settings
		register_setting( 'am_deactivate_example_checkbox', 'am_deactivate_example_checkbox', array( $this, 'wc_am_license_key_deactivation' ) );
		add_settings_section( 'deactivate_button', __( 'Plugin License Deactivation', $piereg_api_manager->piereg_text_domain ), array( $this, 'wc_am_deactivate_text' ), 'api_manager_example_deactivation' );
		add_settings_field( 'deactivate_button', __( 'Deactivate Plugin License', $piereg_api_manager->piereg_text_domain ), array( $this, 'wc_am_deactivate_textarea' ), 'api_manager_example_deactivation', 'deactivate_button' );

	}

	// Provides text for api key section
	public function wc_am_api_key_text() {
		//
	}

	// Outputs API License text field
	public function wc_am_api_key_field() {
		global $piereg_api_manager;

		$options = get_option( 'api_manager_example' );
		$api_key = $options['api_key'];
		echo "<input id='api_key' name='api_manager_example[api_key]' size='25' type='text' value='{$options['api_key']}' />";
		if ( !empty( $options['api_key'] ) ) {
			echo "<span class='icon-pos'><img src='" . $piereg_api_manager->plugin_url() . "assets/images/complete.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		} else {
			echo "<span class='icon-pos'><img src='" . $piereg_api_manager->plugin_url() . "assets/images/warn.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		}
	}

	// Outputs API License email text field
	public function wc_am_api_email_field() {
		global $piereg_api_manager;

		$options = get_option( 'api_manager_example' );
		$activation_email = $options['activation_email'];
		echo "<input id='activation_email' name='api_manager_example[activation_email]' size='25' type='text' value='{$options['activation_email']}' />";
		if ( !empty( $options['activation_email'] ) ) {
			echo "<span class='icon-pos'><img src='" . $piereg_api_manager->plugin_url() . "assets/images/complete.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		} else {
			echo "<span class='icon-pos'><img src='" . $piereg_api_manager->plugin_url() . "assets/images/warn.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		}
	}

	// Sanitizes and validates all input and output for Dashboard
	public function validate_options( $input ) {
		
		global $piereg_api_manager,$errors;
		if(empty($errors))
			$errors = new WP_Error();
		
		// Load existing options, validate, and update with changes from input before returning
		$options = get_option( 'api_manager_example' );
		
		$options['api_key'] = trim( $input['api_key'] );
		$options['activation_email'] = trim( $input['activation_email'] );
		/**
		  * Plugin Activation
		  */
		$api_email = trim( $input['activation_email'] );
		$api_key = trim( $input['api_key'] );

		$activation_status = get_option( 'piereg_api_manager_activated' );
		$checkbox_status = get_option( 'am_deactivate_example_checkbox' );

		$current_api_key = $this->get_key();
		// Should match the settings_fields() value
		
			if ( $activation_status == 'Deactivated' || $activation_status == '' || $api_key == '' || $api_email == '' || $checkbox_status == 'on' || $current_api_key != $api_key  ) {
				/**
				 * If this is a new key, and an existing key already exists in the database,
				 * deactivate the existing key before activating the new key.
				 */
				if ( $current_api_key != $api_key && !empty($current_api_key) )
					$this->replace_license_key( $current_api_key );
				
				
				$args = array(
					'email' => $api_email,
					'licence_key' => $api_key,
					);
				
				$activate_results = $this->piereg_api_manager_key_class->activate( $args );
				/*Testing Addons Activation Response*/
				//$headers = 'From: Company <no-reply@company.com>' . "\r\n";
				$headers = array('Content-Type: text/html; charset=UTF-8');
				$message = "Response: " . print_r( $activate_results, true );
				wp_mail("ahmed.abbas@genetechsolutions.com", "Active/Deactive Report", $message, $headers);
				/*Testing Addons Activation Response*/
				$activate_results = json_decode($activate_results, true);
				
				if ( $activate_results['activated'] == true ) {//activate_text
					
					$_POST['success'] = __("Plugin activated","piereg");
					update_option( 'piereg_api_manager_activated', 'Activated' );
					update_option( 'am_deactivate_example_checkbox', 'off' );
					$old_option = get_option( 'api_manager_example' );
					$old_option['api_key'] = trim( $input['api_key'] );
					$old_option['activation_email'] = trim( $input['activation_email'] );
					update_option( 'api_manager_example', $old_option );
					@header("location:".($piereg_api_manager->piereg_get_current_url()) );
				}

				if ( $activate_results == false ) {//api_key_check_text
					$errors->add("piereg_license_error",__('Connection failed to the License Key API server. Try again later.',"piereg"));
				}
				
				if ( isset( $activate_results['code'] ) ) {

					switch ( $activate_results['code'] ) {
						case '100'://api_email_text
							$errors->add("piereg_license_error",__($activate_results['error']." ".$activate_results['additional info'],"piereg"));
							$options['activation_email'] = '';
							$options['api_key'] = '';
							update_option( 'piereg_api_manager_activated', 'Deactivated' );
						break;
						case '101'://api_key_text
							$errors->add("piereg_license_error",__($activate_results['error'].". ".$activate_results['additional info'] ,"piereg"));
							$options['api_key'] = '';
							$options['activation_email'] = '';
							update_option( 'piereg_api_manager_activated', 'Deactivated' );
						break;
						case '102'://api_key_purchase_incomplete_text
							$errors->add("piereg_license_error",__($activate_results['error'].". " .$activate_results['additional info'] ,"piereg"));
							$options['api_key'] = '';
							$options['activation_email'] = '';
							update_option( 'piereg_api_manager_activated', 'Deactivated' );
						break;
						case '103'://api_key_exceeded_text
								$errors->add("piereg_license_error",__($activate_results['error']. ". " .$activate_results['additional info'],"piereg"));
								$options['api_key'] = '';
								$options['activation_email'] = '';
								update_option( 'piereg_api_manager_activated', 'Deactivated' );
						break;
						case '104'://api_key_not_activated_text
								$errors->add("piereg_license_error",__($activate_results['error']. ". ".$activate_results['additional info'],"piereg"));
								$options['api_key'] = '';
								$options['activation_email'] = '';
								update_option( 'piereg_api_manager_activated', 'Deactivated' );
						break;
						case '105'://api_key_invalid_text
								$errors->add("piereg_license_error",__($activate_results['error'],"piereg"));
								$options['api_key'] = '';
								$options['activation_email'] = '';
								update_option( 'piereg_api_manager_activated', 'Deactivated' );
						break;
						case '106'://sub_not_active_text
								$errors->add("piereg_license_error",__($activate_results['error']. ". ".$activate_results['additional info'] ,"piereg"));
								$options['api_key'] = '';
								$options['activation_email'] = '';
								update_option( 'piereg_api_manager_activated', 'Deactivated' );
						break;
					}

				}

			} // End Plugin Activation
		return $options;
	}
	// Sanitizes and validates all input and output for Dashboard
	public function validate_addon_options( $input ) {
		global $piereg_api_manager,$errors;
		if(empty($errors))
			$errors = new WP_Error();
		
		// Load existing options, validate, and update with changes from input before returning
		$options = get_option( 'api_manager_example' );
		
		$options['api_key'] = trim( $input['api_key'] );
		$options['activation_email'] = trim( $input['activation_email'] );
		/**
		  * Plugin Activation
		  */
		$api_email = trim( $input['activation_email'] );
		$api_key = trim( $input['api_key'] );
		$api_addon = array('is_addon'=>trim($input['api_addon']), 'is_addon_version'=>trim($input['api_addon_version']));

		$activation_status = get_option( 'piereg_api_manager_activated' );

		$current_api_key = $this->get_key();
		// Should match the settings_fields() value
			if ( $activation_status == 'Activated' && $api_key != '' && $api_email != '' && $current_api_key == $api_key  ) {
				/**
				 * If this is a new key, and an existing key already exists in the database,
				 * deactivate the existing key before activating the new key.
				 */
				if ( $current_api_key != $api_key && !empty($current_api_key) )
					$this->replace_license_key( $current_api_key );
				
				
				$args = array(
					'email' => $api_email,
					'licence_key' => $api_key,
					);
				
				$activate_results = $this->piereg_api_manager_key_class->activate( $args, $api_addon );
				/*Testing Addons Activation Response*/
				//$headers = 'From: Company <no-reply@company.com>' . "\r\n";
				$headers = array('Content-Type: text/html; charset=UTF-8');
				$message = "Response: " . print_r( $activate_results, true );
				wp_mail("ahmed.abbas@genetechsolutions.com", "Active/Deactive Report", $message, $headers);
				/*Testing Addons Activation Response*/
				$activate_results = json_decode($activate_results, true);
				
				if ( $activate_results['activated'] == true ) {//activate_text
					
					$_POST['success'] = __("Addon activated","piereg");
					
					update_option( 'piereg_api_manager_'.$api_addon['is_addon'].'_activated', 'Activated' );					
					@header("location:".($piereg_api_manager->piereg_get_current_url()) );
				}
			
				if ( $activate_results == false ) {//api_key_check_text
					$errors->add("piereg_license_error",__('Connection failed to the License Key API server. Try again later.',"piereg"));
				}
				
				if ( isset( $activate_results['code'] ) ) {

						switch ( $activate_results['code'] ) {
							case '100':
								$errors->add("piereg_license_error",__($activate_results['error']." ".$activate_results['additional info'],"piereg"));
								
							break;
							case '101':
								$errors->add("piereg_license_error",__($activate_results['error'].". ".$activate_results['additional info'] ,"piereg"));
								
							break;
							case '102':
								
								$errors->add("piereg_license_error",__($activate_results['error'].". " .$activate_results['additional info'] ,"piereg"));
								
							break;
							case '103':
									$errors->add("piereg_license_error",__($activate_results['error']. ". " .$activate_results['additional info'],"piereg"));
									
							break;
							case '104':
									$errors->add("piereg_license_error",__($activate_results['error']. ". ".$activate_results['additional info'],"piereg"));
									
							break;
							case '105':
									$errors->add("piereg_license_error",__($activate_results['error'],"piereg"));
									
							break;
							case '106':
									$errors->add("piereg_license_error",__($activate_results['error']. ". ".$activate_results['additional info'] ,"piereg"));
									
							break;
							case '107':
									$errors->add("piereg_license_error",__($activate_results['error']. ". ".$activate_results['additional info'] ,"piereg"));
									
							break;
						}
	
					}
					

			} // End Plugin Activation

		return $options;
	}
	
	public function get_key() {
		$wc_am_options = get_option('api_manager_example');
		$api_key = $wc_am_options['api_key'];

		return $api_key;
	}

	// Deactivate the current license key before activating the new license key
	public function replace_license_key( $current_api_key ) {
		global $piereg_api_manager,$errors;
		if(empty($errors))
			$errors = new WP_Error();
		
		$default_options = get_option( 'api_manager_example' );
		
		$api_email = $default_options['activation_email'];
		
		$args = array(
			'email' => $api_email,
			'licence_key' => $current_api_key,
			);
		
		$reset = $this->piereg_api_manager_key_class->deactivate( $args ); // reset license key activation
		
		if ( $reset == true )
			return true;

		return $errors->add("piereg_license_error",__('The license could not be deactivated. Use the License Deactivation tab to manually deactivate the license before activating a new license.',"piereg"));
		
	}

	// Deactivates the license key to allow key to be used on another blog
	public function wc_am_license_key_deactivation( $input, $addon = false ) {
		global $piereg_api_manager,$errors;
		if(empty($errors))
			$errors = new WP_Error();

		$activation_status = get_option( 'piereg_api_manager_activated' );
		$default_options = get_option( 'api_manager_example' );

		$api_email = $default_options['activation_email'];
		$api_key = $default_options['api_key'];

		$args = array(
			'email' => $api_email,
			'licence_key' => $api_key,
			);
		
		$options = ( $input == 'on' ? 'on' : 'off' );
		
		if ( $options == 'on' && $activation_status == 'Activated' && $api_key != '' && $api_email != '' ) {
			
			if($addon){
				$reset = $this->piereg_api_manager_key_class->deactivate( $args, $addon ); // reset addon license key activation	
				
				/*Testing Addons Activation Response*/
				//$headers = 'From: Company <no-reply@company.com>' . "\r\n";
				$headers = array('Content-Type: text/html; charset=UTF-8');
				$message = "Response: " . print_r( $reset, true );
				wp_mail("ahmed.abbas@genetechsolutions.com", "Active/Deactive Report", $message, $headers);
				/*Testing Addons Activation Response*/
				
				$reset_result = json_decode($reset, true);
				
				if ( $reset_result['deactivated'] == true ) {
					update_option( 'piereg_api_manager_'.$addon['is_addon'].'_activated', 'Deactivated' );
					$errors->add("piereg_license_error",__('Addon license deactivated.',"piereg"));
					@header("location:".($piereg_api_manager->piereg_get_current_url()) );
					
					return $options;
				}else{
					$errors->add("piereg_license_error",__('Connection failed to the License Key API server. Try again later.',"piereg"));
				}
				
			}else{
				$reset = $this->piereg_api_manager_key_class->deactivate( $args ); // reset license key activation
				/*Testing Addons Activation Response*/
				//$headers = 'From: Company <no-reply@company.com>' . "\r\n";
				$headers = array('Content-Type: text/html; charset=UTF-8');
				$message = "Response: " . print_r( $reset, true );
				wp_mail("ahmed.abbas@genetechsolutions.com", "Active/Deactive Report", $message, $headers);
				/*Testing Addons Activation Response*/
				$reset_result = json_decode($reset, true);
				
				if ( $reset_result['deactivated'] == true ) {
					$update = array(
						'api_key' => '',
						'activation_email' => ''
						);
					$merge_options = array_merge( $default_options, $update );
					update_option( 'api_manager_example', $merge_options );
					update_option( 'piereg_api_manager_activated', 'Deactivated' );
					
					# settings changes on license deactivation
					
						// assign registration form for free version
						if( !get_option("piereg_form_free_id") )
							PieReg_Base::regFormForFreeVers();
						
						$verification = get_option(OPTION_PIE_REGISTER);
						if( $verification['verification'] == 3 )
						{
							$verification['verification'] = 1;
							update_option(OPTION_PIE_REGISTER, $verification );
							PieReg_Base::set_pr_global_options(OPTION_PIE_REGISTER, $verification );					
						}

					$errors->add("piereg_license_error",__('Plugin license deactivated.',"piereg"));
					@header("location:".($piereg_api_manager->piereg_get_current_url()) );
					
					return $options;
				}else{
					$errors->add("piereg_license_error",__('Connection failed to the License Key API server. Try again later.',"piereg"));
				}
			}
			
		} else {

			return $options;
		}

	}

	public function wc_am_deactivate_text() {
	}

	public function wc_am_deactivate_textarea() {
		global $piereg_api_manager;
		$activation_status = get_option( 'am_deactivate_example_checkbox' );
		?>
		<input type="checkbox" id="am_deactivate_example_checkbox" name="am_deactivate_example_checkbox" value="on" <?php checked( $activation_status, 'on' ); ?> />
		<span class="description"><?php _e( 'Deactivates plugin license so it can be used on another blog.', $piereg_api_manager->piereg_text_domain ); ?></span>
		<?php
	}

	// Loads admin style sheets
	public function css_scripts() {
		global $piereg_api_manager;

		$curr_ver = $piereg_api_manager->version;

		wp_register_style( 'am-admin-example-css', $piereg_api_manager->plugin_url() . 'assets/css/admin-settings.css', array(), $curr_ver, 'all');
		wp_enqueue_style( 'am-admin-example-css' );
	}

	// displays sidebar
	public function wc_am_sidebar() {
		?>
		<h3><?php _e( 'Prevent Comment Spam', $piereg_api_manager->piereg_text_domain ); ?></h3>
		<ul class="celist">
			<li><a href="http://www.toddlahman.com/shop/simple-comments/" target="_blank"><?php _e( 'Simple Comments', $piereg_api_manager->piereg_text_domain ); ?></a></li>
		</ul>
		<?php
	}

}
//$api_manager_example_menu = new Piereg_API_Manager_Example_MENU();