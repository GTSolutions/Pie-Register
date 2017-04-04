<?php
include("base_variables.php");
if( !class_exists('PieReg_Base') ){
	class PieReg_Base extends PieRegisterBaseVariables
	{
		/*
			*	Move Variable PieReg_Base to PieRegisterBaseVariables (01-09-2014)
		*/
		
		function __construct()
		{
			/*
				*	Execute Parent construct
			*/
			parent::__construct();
			
			$this->plugin_dir = dirname(dirname(__FILE__));
			$this->plugin_url = plugins_url() .'/'. basename(dirname(dirname(__FILE__))) .'/';
		}
		
		/*
			*	GET PR_GLOBAL_OPTIONS
			*	return PR global option
			*	
		*/
		function get_pr_global_options($option_name = NULL){
			switch($option_name){
				case OPTION_PIE_REGISTER:
					global $PR_GLOBAL_OPTIONS;
					$options = $PR_GLOBAL_OPTIONS;
				break;
				default:
					global $PR_GLOBAL_OPTIONS;
					$options = $PR_GLOBAL_OPTIONS;
				break;
			}
			return $options;
		}
		/*
			*	set PR global option and return true and false
		*/
		function set_pr_global_options($option_name = NULL, $value){
			switch($option_name){
				case OPTION_PIE_REGISTER:
					if(!empty($value))
					{
						global $PR_GLOBAL_OPTIONS;
						$PR_GLOBAL_OPTIONS = $value;
					}
				break;
				default:
					return false;
				break;
			}
			return true;
		}
		
		function getPieMeta()
		{
			global $wpdb;
			$this->user_table		= $wpdb->prefix . "users";
			$this->user_meta_table 	= $wpdb->prefix . "usermeta";
			$result	 = $wpdb->get_results( $wpdb->prepare("SELECT distinct(meta_key) FROM {$this->user_meta_table} WHERE `meta_key` like 'pie_%'", '') ); #WPS	
			if(isset($wpdb->last_error) && !empty($wpdb->last_error)){
				$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
			}
			
			if(sizeof($result) > 0)
			{
				return $result;
			}
			return false;
		}
		function replaceMetaKeys($text,$user_id)
		{
			if($result = $this->getPieMeta())
			{	
				foreach($result as $meta)
				{
					$key 		= "%".$meta->meta_key."%";
					$value		= get_user_meta($user_id, $meta->meta_key, true );
					$get_value = "";
					if(is_array($value)){
						foreach($value as $val){
							if(is_array($val)){
								if(array_filter($val))
								$get_value .= implode(", ",$val)."<br />";
							}else{
								$get_value = (is_array($value))? implode(", ",$value) : $value;
							}
						}
					}else{
						$get_value .= (!empty($value))? $value : "";
					}
					$text		= str_replace($key,$get_value,$text);
				}
			}
			return $text;
		}
		function getCurrentFields($id="")
		{
			if(((int)$id) != 0 and $id != "" )
			{
				$data 	= get_option("piereg_form_fields_".((int)$id));
			}
			else if(isset($_GET['form_id']) and ((int)$_GET['form_id']) != 0 and isset($_GET['form_name']) and $_GET['form_name']!= ""){
				
				$data 	= get_option("piereg_form_fields_".((int)$_GET['form_id']));
			}
			
			else{
				$data 	= get_option("pie_fields_default");
			}
			
			$data 	= maybe_unserialize($data );				
			
			if(!$data)
			{
				return false;		
			}
			return $data;			
		}
		function install_settings()
		{
			$this->activate_pieregister_license_key();
			
			/*Get old settings from options*/
			$old_options = get_option("pie_register_2");
			$new_options = get_option(OPTION_PIE_REGISTER);
			
			if($new_options == "" and $old_options != "")
			{
				update_option(OPTION_PIE_REGISTER,$old_options);
				unset($old_options);
				unset($new_options);
			}
						
			//Alternate Pages
			$get_pie_pages_from_db		= get_option("pie_pages");
			$piereg_registration_create_new_page = false;
			if(is_array($get_pie_pages_from_db))
			{
				$piereg_login 		= (isset($get_pie_pages_from_db[0]) and $get_pie_pages_from_db != "")? get_post_status($get_pie_pages_from_db[0]) : "null";
				$piereg_registrtion = (isset($get_pie_pages_from_db[1]) and $get_pie_pages_from_db != "")? get_post_status($get_pie_pages_from_db[1]) : "null";
				$piereg_forgot_pass = (isset($get_pie_pages_from_db[2]) and $get_pie_pages_from_db != "")? get_post_status($get_pie_pages_from_db[2]) : "null";
				$piereg_profile 	= (isset($get_pie_pages_from_db[3]) and $get_pie_pages_from_db != "")? get_post_status($get_pie_pages_from_db[3]) : "null";
			}
			
			$pie_pages = get_option("pie_pages");
			
			if(trim($piereg_login) != "publish")//Login
			{
				$_p = array();
				$_p['post_title'] 		= __("Login","piereg");
				$_p['post_content'] 	= "[pie_register_login]";
				$_p['post_status'] 		= 'publish';
				$_p['post_type'] 		= 'page';
				$_p['comment_status'] 	= 'closed';
				$_p['ping_status'] 		= 'closed';
				$login_page_id 			= wp_insert_post( $_p );
				$pie_pages[0]			= $login_page_id;
			}
			
			if(trim($piereg_registrtion) != "publish")//Registration
			{
				$_p = array();
				$_p['post_title'] 		= __("Registration","piereg");
				$_p['post_content'] 	= '[pie_register_form id="0" title="true" description="true" ]';
				$_p['post_status'] 		= 'publish';
				$_p['post_type'] 		= 'page';
				$_p['comment_status'] 	= 'closed';
				$_p['ping_status'] 		= 'closed';
				$reg_page_id 			= wp_insert_post( $_p );
				$pie_pages[1]			= $reg_page_id;
				$piereg_registration_create_new_page = true;
			}
				
			if(trim($piereg_forgot_pass) != "publish")//Forgot Password
			{
				$_p = array();
				$_p['post_title'] 		= __("Forgot Password","piereg");
				$_p['post_content'] 	= "[pie_register_forgot_password]";
				$_p['post_status'] 		= 'publish';
				$_p['post_type'] 		= 'page';
				$_p['comment_status'] 	= 'closed';
				$_p['ping_status'] 		= 'closed';
				$forPas_page_id	 		= wp_insert_post( $_p );
				$pie_pages[2]			= $forPas_page_id;
			}
			
			if(trim($piereg_profile) != "publish")//Profile Page
			{
				$_p = array();
				$_p['post_title'] 		= __("Profile","piereg");
				$_p['post_content'] 	= "[pie_register_profile]";
				$_p['post_status'] 		= 'publish';
				$_p['post_type'] 		= 'page';
				$_p['comment_status'] 	= 'closed';
				$_p['ping_status'] 		= 'closed';
				$Profile_page_id 		= wp_insert_post( $_p );
				$pie_pages[3]			= $Profile_page_id;
				update_option("Profile_page_id",$Profile_page_id);
			}
			update_option("pie_pages",$pie_pages);
			
			//Countries
			$country = array(__("Afghanistan","piereg"),__("Albania","piereg"),__("Algeria","piereg"),__("American Samoa","piereg"),__("Andorra","piereg"),__("Angola","piereg"),__("Antigua and Barbuda","piereg"),__("Argentina","piereg"),__("Armenia","piereg"),__("Australia","piereg"),__("Austria","piereg"),__("Azerbaijan","piereg"),__("Bahamas","piereg"),__("Bahrain","piereg"),__("Bangladesh","piereg"),__("Barbados","piereg"),__("Belarus","piereg"),__("Belgium","piereg"),__("Belize","piereg"),__("Benin","piereg"),__("Bermuda","piereg"),__("Bhutan","piereg"),__("Bolivia","piereg"),__("Bosnia and Herzegovina","piereg"),__("Botswana","piereg"),__("Brazil","piereg"),__("Brunei","piereg"),__("Bulgaria","piereg"),__("Burkina Faso","piereg"),__("Burundi","piereg"),__("Cambodia","piereg"),__("Cameroon","piereg"),__("Canada","piereg"),__("Cape Verde","piereg"),__("Central African Republic","piereg"),__("Chad","piereg"),__("Chile","piereg"),__("China","piereg"),__("Colombia","piereg"),__("Comoros","piereg"),__("Congo","piereg"),__("Costa Rica","piereg"),__("Côte d'Ivoire","piereg"),__("Croatia","piereg"),__("Cuba","piereg"),__("Cyprus","piereg"),__("Czech Republic","piereg"),__("Denmark","piereg"),__("Djibouti","piereg"),__("Dominica","piereg"),__("Dominican Republic","piereg"),__("East Timor","piereg"),__("Ecuador","piereg"),__("Egypt","piereg"),__("El Salvador","piereg"),__("Equatorial Guinea","piereg"),__("Eritrea","piereg"),__("Estonia","piereg"),__("Ethiopia","piereg"),__("Fiji","piereg"),__("Finland","piereg"),__("France","piereg"),__("Gabon","piereg"),__("Gambia","piereg"),__("Georgia","piereg"),__("Germany","piereg"),__("Ghana","piereg"),__("Greece","piereg"),__("Greenland","piereg"),__("Grenada","piereg"),__("Guam","piereg"),__("Guatemala","piereg"),__("Guinea","piereg"),__("Guinea-Bissau","piereg"),__("Guyana","piereg"),__("Haiti","piereg"),__("Honduras","piereg"),__("Hong Kong","piereg"),__("Hungary","piereg"),__("Iceland","piereg"),__("India","piereg"),__("Indonesia","piereg"),__("Iran","piereg"),__("Iraq","piereg"),__("Ireland","piereg"),__("Israel","piereg"),__("Italy","piereg"),__("Jamaica","piereg"),__("Japan","piereg"),__("Jordan","piereg"),__("Kazakhstan","piereg"),__("Kenya","piereg"),__("Kiribati","piereg"),__("North Korea","piereg"),__("South Korea","piereg"),__("Kuwait","piereg"),__("Kyrgyzstan","piereg"),__("Laos","piereg"),__("Latvia","piereg"),__("Lebanon","piereg"),__("Lesotho","piereg"),__("Liberia","piereg"),__("Libya","piereg"),__("Liechtenstein","piereg"),__("Lithuania","piereg"),__("Luxembourg","piereg"),__("Macedonia","piereg"),__("Madagascar","piereg"),__("Malawi","piereg"),__("Malaysia","piereg"),__("Maldives","piereg"),__("Mali","piereg"),__("Malta","piereg"),__("Marshall Islands","piereg"),__("Mauritania","piereg"),__("Mauritius","piereg"),__("Mexico","piereg"),__("Micronesia","piereg"),__("Moldova","piereg"),__("Monaco","piereg"),__("Mongolia","piereg"),__("Montenegro","piereg"),__("Morocco","piereg"),__("Mozambique","piereg"),__("Myanmar","piereg"),__("Namibia","piereg"),__("Nauru","piereg"),__("Nepal","piereg"),__("Netherlands","piereg"),__("New Zealand","piereg"),__("Nicaragua","piereg"),__("Niger","piereg"),__("Nigeria","piereg"),__("Norway","piereg"),__("Northern Mariana Islands","piereg"),__("Oman","piereg"),__("Pakistan","piereg"),__("Palau","piereg"),__("Palestine","piereg"),__("Panama","piereg"),__("Papua New Guinea","piereg"),__("Paraguay","piereg"),__("Peru","piereg"),__("Philippines","piereg"),__("Poland","piereg"),__("Portugal","piereg"),__("Puerto Rico","piereg"),__("Qatar","piereg"),__("Romania","piereg"),__("Russia","piereg"),__("Rwanda","piereg"),__("Saint Kitts and Nevis","piereg"),__("Saint Lucia","piereg"),__("Saint Vincent and the Grenadines","piereg"),__("Samoa","piereg"),__("San Marino","piereg"),__("Sao Tome and Principe","piereg"),__("Saudi Arabia","piereg"),__("Senegal","piereg"),__("Serbia and Montenegro","piereg"),__("Seychelles","piereg"),__("Sierra Leone","piereg"),__("Singapore","piereg"),__("Slovakia","piereg"),__("Slovenia","piereg"),__("Solomon Islands","piereg"),__("Somalia","piereg"),__("South Africa","piereg"),__("Spain","piereg"),__("Sri Lanka","piereg"),__("Sudan","piereg"),__("Sudan, South","piereg"),__("Suriname","piereg"),__("Swaziland","piereg"),__("Sweden","piereg"),__("Switzerland","piereg"),__("Syria","piereg"),__("Taiwan","piereg"),__("Tajikistan","piereg"),__("Tanzania","piereg"),__("Thailand","piereg"),__("Togo","piereg"),__("Tonga","piereg"),__("Trinidad and Tobago","piereg"),__("Tunisia","piereg"),__("Turkey","piereg"),__("Turkmenistan","piereg"),__("Tuvalu","piereg"),__("Uganda","piereg"),__("Ukraine","piereg"),__("United Arab Emirates","piereg"),__("United Kingdom","piereg"),__("United States","piereg"),__("Uruguay","piereg"),__("Uzbekistan","piereg"),__("Vanuatu","piereg"),__("Vatican City","piereg"),__("Venezuela","piereg"),__("Vietnam","piereg"),__("Virgin Islands, British","piereg"),__("Virgin Islands, U.S.","piereg"),__("Yemen","piereg"),__("Zambia","piereg"),__("Zimbabwe","piereg"));
			update_option("pie_countries",$country);	
			
			//USA States
			$us_states = array(__("Alabama","piereg"),__("Alaska","piereg"),__("Arizona","piereg"),__("Arkansas","piereg"),__("California","piereg"),__("Colorado","piereg"),__("Connecticut","piereg"),__("Delaware","piereg"),__("District of Columbia","piereg"),__("Florida","piereg"),__("Georgia","piereg"),__("Hawaii","piereg"),__("Idaho","piereg"),__("Illinois","piereg"),__("Indiana","piereg"),__("Iowa","piereg"),__("Kansas","piereg"),__("Kentucky","piereg"),__("Louisiana","piereg"),__("Maine","piereg"),__("Maryland","piereg"),__("Massachusetts","piereg"),__("Michigan","piereg"),__("Minnesota","piereg"),__("Mississippi","piereg"),__("Missouri","piereg"),__("Montana","piereg"),__("Nebraska","piereg"),__("Nevada","piereg"),__("New Hampshire", "piereg"),__("New Jersey", "piereg"),__("New Mexico", "piereg"),__("New York", "piereg"),__("North Carolina", "piereg"),__("North Dakota", "piereg"),__("Ohio","piereg"),__("Oklahoma","piereg"),__("Oregon","piereg"),__("Pennsylvania","piereg"),__("Rhode Island", "piereg"),__("South Carolina", "piereg"),__("South Dakota", "piereg"),__("Tennessee","piereg"),__("Texas","piereg"),__("Utah","piereg"),__("Vermont","piereg"),__("Virginia","piereg"),__("Washington","piereg"),__("West Virginia", "piereg"),__("Wisconsin","piereg"),__("Wyoming","piereg"),__("Armed Forces Americas","piereg"),__("Armed Forces Europe","piereg"),__("Armed Forces Pacific","piereg"));
			update_option("pie_us_states",$us_states);
					
			//Canada States
			$can_states = array(__("Alberta","piereg"),__("British Columbia","piereg"),__("Manitoba","piereg"),__("New Brunswick","piereg"),__("Newfoundland and Labrador","piereg"),__("Northwest Territories","piereg"),__("Nova Scotia","piereg"),__("Nunavut","piereg"),__("Ontario","piereg"),__("Prince Edward Island","piereg"),__("Quebec","piereg"),__("Saskatchewan","piereg"),__("Yukon","piereg"));
			update_option("pie_can_states",$can_states);
			
			
			//E-Mail TYpes
			$email_type = array(
								"default_template"							=> __("Your account is ready.","piereg"),
								"admin_verification"						=> __("Your account is being processed.","piereg"),
								"email_verification"						=> __("Email verification.","piereg"),
								"email_edit_verification"					=> __("Email address change verification.","piereg"),
								"current_email_verification"				=> __("Current Email address change verification.","piereg"),
								"email_thankyou"							=> __("Your account has been activated.","piereg"),
								"forgot_password_notification"				=> __("Password Reset Request.","piereg"),
								"pending_payment"							=> __("Overdue Payment.","piereg"),
								"payment_success"							=> __("Payment Processed.","piereg"),
								"payment_faild"								=> __("Payment Failed.","piereg"),
								"pending_payment_reminder"					=> __("Payment Pending.","piereg"),
								"email_verification_reminder"				=> __("Email Verification Reminder.","piereg"),
								"user_expiry_notice"						=> __("Final Email Verification Reminder.","piereg"),
								"user_temp_blocked_notice"					=> __("User Temporary Blocked Notice","piereg"),
								"user_renew_temp_blocked_account_notice"	=> __("Payment Failed.","piereg"),
								"user_perm_blocked_notice"					=> __("Final Reminder - Payment Failed.","piereg")
								);
			
			update_option("pie_user_email_types",$email_type);
			$current = get_option(OPTION_PIE_REGISTER);
			$update = $current;
			
			$update["paypal_butt_id"] = ($current["paypal_butt_id"])?$current["paypal_butt_id"]:"";
			$update["paypal_pdt"]     = ($current["paypal_pdt"])?$current["paypal_pdt"]:"";
			$update["paypal_sandbox"] = ($current["paypal_sandbox"])?$current["paypal_sandbox"]:"";
			$update["payment_success_msg"] 	= ($current["payment_success_msg"])?$current["payment_success_msg"]:__("Payment was successful.","piereg");
			$update["payment_faild_msg"] 	= ($current["payment_faild_msg"])?$current["payment_faild_msg"]:__("Payment failed.","piereg");
			$update["payment_renew_msg"] 	= ($current["payment_renew_msg"])?$current["payment_renew_msg"]:__("Account needs to be activated.","piereg");
			$update["payment_already_activate_msg"] 	= ($current["payment_already_activate_msg"])?$current["payment_already_activate_msg"]:__("Account is already active.","piereg");
			$update['enable_admin_notifications'] = ($current['enable_admin_notifications'])?$current['enable_admin_notifications']:1;
			$update['enable_paypal'] = ($current['enable_paypal'])?$current['enable_paypal']:0;
			$update['enable_blockedips'] = ($current['enable_blockedips'])?$current['enable_blockedips']:0;
			$update['enable_blockedusername'] = ($current['enable_blockedusername'])?$current['enable_blockedusername']:0;
			$update['enable_blockedemail'] = ($current['enable_blockedemail'])?$current['enable_blockedemail']:0;
			
			$update['admin_sendto_email'] 	= ($current['admin_sendto_email'])?$current['admin_sendto_email']:get_option( 'admin_email' );				
			$update['admin_from_name'] 		= ($current['admin_from_name'])?$current['admin_from_name']:"Administrator";
			$update['admin_from_email'] 	= ($current['admin_from_email'])?$current['admin_from_email']:get_option( 'admin_email' );
			$update['admin_to_email'] 		= ($current['admin_to_email'])?$current['admin_to_email']:get_option( 'admin_email' );
			$update['admin_bcc_email'] 		= ($current['admin_bcc_email'])?$current['admin_bcc_email']:get_option( 'admin_email' );
			$update['admin_subject_email'] 	= ($current['admin_subject_email'])?$current['admin_subject_email']:__("New User Registration","piereg");
			$update['admin_message_email_formate'] 			= ($current['admin_message_email_formate'])?$current['admin_message_email_formate']:1;
			$update['user_formate_email_default_template'] 	= ($current['user_formate_email_default_template'])?$current['user_formate_email_default_template']:1;
			$update['admin_message_email'] 		= ($current['admin_message_email'])?$current['admin_message_email']:'<p>Hello Admin,</p><p>A new user has been registered on your Website,. Details are given below:</p><p>Thanks</p><p>Team %blogname%</p>';
			$update['display_hints']			= ($current['display_hints'])?$current['display_hints']:0; // (1) - 090415
			$update['redirect_user']			= ($current['redirect_user'])?$current['redirect_user']:1;
			$update['subscriber_login']			= ($current['subscriber_login'])?$current['subscriber_login']:0;
			$update['login_form_in_website']	= ($current['login_form_in_website'])?$current['login_form_in_website']:1;
			$update['registration_in_website']	= ($current['registration_in_website'])?$current['registration_in_website']:1;
			$update['block_WP_profile']			= ($current['block_WP_profile'])?$current['block_WP_profile']:0;
			$update['allow_pr_edit_wplogin']	= ($current['allow_pr_edit_wplogin'])?$current['allow_pr_edit_wplogin']:0;
			$update['modify_avatars']			= ($current['modify_avatars'])?$current['modify_avatars']:0;
			$update['show_admin_bar']			= ($current['show_admin_bar'])?$current['show_admin_bar']:1;
			$update['block_wp_login']			= ($current['block_wp_login'])?$current['block_wp_login']:1;
			$update['alternate_login']			= $pie_pages[0];
			$update['alternate_register']		= $pie_pages[1];
			$update['alternate_forgotpass']		= $pie_pages[2];
			$update['alternate_profilepage']	= $pie_pages[3];
			
			////// Date Starting/Ending Variables////////////
			//////////////// Since 2.0.12 ///////////////////
			$update['piereg_startingDate']		= ($current['piereg_startingDate'])?$current['piereg_startingDate']:'1901';
			$update['piereg_endingDate']		= ($current['piereg_endingDate'])?$current['piereg_endingDate']:date_i18n("Y");
			
			$update['after_login']				= ($current['after_login'])?$current['after_login']:-1;
			$update['alternate_logout']			= ($current['alternate_logout'])?$current['alternate_logout']:-1;
			$update['alternate_logout_url']		= ($current['alternate_logout'])?$current['alternate_logout_url']:"";
			$update['outputcss'] 				= ($current['outputcss'])?$current['outputcss']:1;
			$update['outputjquery_ui'] 			= ($current['outputjquery_ui'])?$current['outputjquery_ui']:1;
			$update['login_after_register'] 	= ($current['login_after_register'])?$current['login_after_register']:0;
			
			$update['pass_strength_indicator_label']	= ($current['pass_strength_indicator_label'])? $current['pass_strength_indicator_label'] : "Strength Indicator";
			$update['pass_very_weak_label']				= ($current['pass_very_weak_label'])? $current['pass_very_weak_label'] : "Very weak";
			$update['pass_weak_label']					= ($current['pass_weak_label'])? $current['pass_weak_label'] : "Weak";
			$update['pass_medium_label']				= ($current['pass_medium_label'])? $current['pass_medium_label'] : "Medium";
			$update['pass_strong_label']				= ($current['pass_strong_label'])? $current['pass_strong_label'] : "Strong";
			$update['pass_mismatch_label']				= ($current['pass_mismatch_label'])? $current['pass_mismatch_label'] : "Mismatch";
			$update['pr_theme']							= ($current['pr_theme'])? $current['pr_theme'] : "0";
			
			/* Bot Settings */
			$update['restrict_bot_enabel'] 		= ($current['restrict_bot_enabel'])?$current['restrict_bot_enabel']:0;
			$update['restrict_bot_content']		= ($current['restrict_bot_content'])?$current['restrict_bot_content']:"bot\r\nia_archive\r\nslurp crawl\r\nspider\r\nYandex";
			$update['restrict_bot_content_message']		= ($current['restrict_bot_content_message'])?$current['restrict_bot_content_message']:"Restricted Post: You are not allowed to view the content of this Post";
			
			$update['outputhtml'] 				= ($current['outputhtml'])?$current['outputhtml']:1;
			$update['no_conflict']				= ($current['no_conflict'])?$current['no_conflict']:0;
			$update['currency'] 				= ($current['currency'])?$current['currency']:"USD";
			$update['verification'] 			= ($current['verification'])?$current['verification']:0;
			$update['email_edit_verification_step']	= ($current['email_edit_verification_step'])?$current['email_edit_verification_step']:1;
			
			$update['grace_period'] 			= ($current['grace_period'])?$current['grace_period']:0;
			$update['captcha_publc'] 			= ($current['captcha_publc'])?$current['captcha_publc']:"";
			$update['captcha_private'] 			= ($current['captcha_private'])?$current['captcha_private']:"";
			$update['paypal_button_id'] 		= ($current['paypal_button_id'])?$current['paypal_button_id']:"";
			$update['paypal_pdt_token'] 		= ($current['paypal_pdt_token'])?$current['paypal_pdt_token']:"";
			$update['custom_css'] 				= ($current['custom_css'])?$current['custom_css']:"";
			$update['tracking_code'] 			= ($current['tracking_code'])?$current['tracking_code']:"";
			$update['enable_invitation_codes'] 	= ($current['enable_invitation_codes'])?$current['enable_invitation_codes']:0;
			$update['invitation_codes'] 		= ($current['invitation_codes'])?$current['invitation_codes']:"";
			// Payment Setting 
			$update['payment_setting_amount']	= ($current['payment_setting_amount'])?$current['payment_setting_amount']:"10";
			//Role setting
			$update['pie_regis_set_user_role_'] = ($current['pie_regis_set_user_role_'])?$current['pie_regis_set_user_role_']:"subscriber";
			
			$update['custom_logo_url']					= ($current['custom_logo_url'])? $current['custom_logo_url'] : "";
			$update['reg_form_submission_time_enable']  = ($current['reg_form_submission_time_enable'])? $current['reg_form_submission_time_enable'] : "0";
			$update['reg_form_submission_time'] 		= ($current['reg_form_submission_time'])? $current['reg_form_submission_time'] : "0";
			$update['custom_logo_tooltip']				= ($current['custom_logo_tooltip'])? $current['custom_logo_tooltip'] : "";
			$update['custom_logo_link']					= ($current['custom_logo_link'])? $current['custom_logo_link'] : "";
			$update['show_custom_logo']					= ($current['show_custom_logo'])? $current['show_custom_logo'] : 1;
			// Login form
			$update['login_username_label']			= ($current['login_username_label'])? $current['login_username_label'] : "Username";
			$update['login_username_placeholder']	= ($current['login_username_placeholder'])? $current['login_username_placeholder'] : "";
			$update['login_password_label']			= ($current['login_password_label'])? $current['login_password_label'] : "Password";
			$update['login_password_placeholder']	= ($current['login_password_placeholder'])? $current['login_password_placeholder'] : "";
			$update['capthca_in_login_label']		= ($current['capthca_in_login_label'])? $current['capthca_in_login_label'] : "";
			$update['capthca_in_login']				= ($current['capthca_in_login'])? $current['capthca_in_login'] : "0";
			
			//New Settings 
			$update['captcha_in_login_value']				 = ($current['captcha_in_login_value']) ? $current['captcha_in_login_value'] : 0;
			$update['piereg_security_attempts_login_value']  = ($current['security_attempts_login']) ? $current['security_attempts_login'] : '0';
			$update['captcha_in_forgot_value']				 = ($current['capthca_in_forgot_pass']) ? $current['capthca_in_forgot_pass'] : 0;
			$update['piereg_security_attempts_forgot_value'] = ($current['piereg_security_attempts_forgot_value'])? $current['piereg_security_attempts_forgot_value'] : "0";
			
			//security_attempts_login
			$update['security_captcha_attempts_login']	= ($current['security_captcha_attempts_login'])? $current['security_captcha_attempts_login'] : "0";
			$update['security_captcha_login']			= ($current['security_captcha_login'])? $current['security_captcha_login'] : "2";
			$update['security_attempts_login']			= ($current['security_attempts_login'])? $current['security_attempts_login'] : "0";
			$update['security_attempts_login_time']		= ($current['security_attempts_login_time'])? $current['security_attempts_login_time'] : "1";
			
			// Forgot Password form
			$update['forgot_pass_username_label']		= ($current['forgot_pass_username_label'])? $current['forgot_pass_username_label'] : "Username or Email:";
			$update['forgot_pass_username_placeholder']	= ($current['forgot_pass_username_placeholder'])? $current['forgot_pass_username_placeholder'] : "";
			$update['forgot_pass_username_placeholder']	= ($current['forgot_pass_username_placeholder'])? $current['forgot_pass_username_placeholder'] : "";
			$update['capthca_in_forgot_pass_label']		= ($current['capthca_in_forgot_pass_label'])? $current['capthca_in_forgot_pass_label'] : "";
			$update['capthca_in_forgot_pass']			= ($current['capthca_in_forgot_pass'])? $current['capthca_in_forgot_pass'] : "0";
						
			$pie_user_email_types 	= get_option( 'pie_user_email_types');					
			foreach ($pie_user_email_types as $val=>$type) 
			{
				$update['enable_user_notifications'] = ($current['enable_user_notifications'])?$current['enable_user_notifications']:0;
				$update['user_from_name_'.$val] 	 = ($current['user_from_name_'.$val])?$current['user_from_name_'.$val]:"Admin";
				$update['user_from_email_'.$val] 	 = ($current['user_from_email_'.$val])?$current['user_from_email_'.$val]:get_option( 'admin_email' );
				$update['user_to_email_'.$val]	 	 = ($current['user_to_email_'.$val])?$current['user_to_email_'.$val]:get_option( 'admin_email' );
				$update['user_subject_email_'.$val]  = ($current['user_subject_email_'.$val])?$current['user_subject_email_'.$val]:$type;
				$update['user_formate_email_'.$val]  = ($current['user_formate_email_'.$val])?$current['user_formate_email_'.$val]:1;
			}
			$update['user_message_email_admin_verification']	 					= ($current['user_message_email_admin_verification'])?$current['user_message_email_admin_verification']: '<p>Dear %user_login%,</p><p>Thank You for registering with our website. </p><p>A site administrator will review your request. Once approved, you will be notified via email.</p><p>Best Wishes,</p><p>Team %blogname%</p>';
			$update['user_message_email_email_verification']			 			= ($current['user_message_email_email_verification'])?$current['user_message_email_email_verification']:'<p>Dear %user_login%,</p><p>Thank You for registering with our website. </p><p>Please use the link below to verify your email address. </p><p>%activationurl%</p><p>Best Wishes,</p><p>Team %blogname%</p>';
			$update['user_message_email_email_thankyou'] 							= ($current['user_message_email_email_thankyou'])?$current['user_message_email_email_thankyou']:'<p>Dear %user_login%,</p><p>Thank You for registering with our website. </p><p>You are ready to enjoy the benefits of our products and services by signing in to your personalized account.</p><p>Best Regards,</p><p>Team %blogname%</p>';
			
			$update['user_message_email_payment_success'] 							= ($current['user_message_email_payment_success'])?$current['user_message_email_payment_success']:'<p>Dear %user_login%,</p><p>Congratulations, your payment has been successfully processed. <br/>Please enjoy the benefits of your membership on %blogname% </p><p>Thank You,</p><p>Team %blogname%</p>';
			$update['user_message_email_payment_faild'] 							= ($current['user_message_email_payment_faild'])?$current['user_message_email_payment_faild']:'<p>Dear %user_login%,</p><p>Our last attempt to charge the membership payment for your account has failed. </p><p>You are requested to log in to your account at %blogname% to provide a different payment method, or contact your bank/credit-card company to resolve this issue.</p><p>Kind Regards,</p><p>Team %blogname%<br/></p>';
			$update['user_message_email_pending_payment'] 							= ($current['user_message_email_pending_payment'])?$current['user_message_email_pending_payment']:'<p>Dear %user_login%,</p><p>This is a reminder that membership payment is overdue for your account on %blogname%. Please process your payment immediately to keep membership previlages active. </p><p>Best Regards,</p><p>Team %blogname%</p>';
			$update['user_message_email_default_template'] 							= ($current['user_message_email_default_template'])?$current['user_message_email_default_template']: '<p>Dear %user_login%,</p><p>Thank You for registering with our website.</p><p>You are ready to enjoy the benefits of our products and services by signing in to your personalized account.</p><p>Best Regards,</p><p>Team %blogname%</p>';
			
			$update['user_message_email_pending_payment_reminder'] 					= ($current['user_message_email_pending_payment_reminder'])?$current['user_message_email_pending_payment_reminder']: '<p>Dear %user_login%,</p><p>We have noticed that you created an account on %blogname% a few days ago, but have not completed the payment. Please use the link below to complete the payment. <br/>Your account will be activated once the payment is received.</p><p>%pending_payment_url%</p><p>Best Regards,</p><p>Team %blogname%</p>';
			$update['user_message_email_email_verification_reminder']			 	= ($current['user_message_email_email_verification_reminder'])?$current['user_message_email_email_verification_reminder']: '<p>Dear %user_login%,</p><p>Thank You for registering with our website. </p><p>We noticed that you created an account on %blogname% but have not completed the email verification process. </p><p>Please use the link below to verify your email address. </p><p>%activationurl%</p><p>Best Wishes,</p><p>Team %blogname%</p>';
			$update['user_message_email_forgot_password_notification']				= ($current['user_message_email_forgot_password_notification'])?$current['user_message_email_forgot_password_notification']: '<p>Dear %user_login%,</p><p>We have received a request to reset your account password on %blogname%. Please use the link below to reset your password. If you did not request a new password, please ignore this email and the change will not be made.</p><p>( %reset_password_url% )</p><p>Best Regards,</p><p>Team %user_login%</p>';
			$update['user_message_email_user_expiry_notice'] 						= ($current['user_message_email_user_expiry_notice'])? $current['user_message_email_user_expiry_notice']: '<p>Dear %user_login%,</p><p>Thank You for registering with our website. </p><p>We noticed that you created an account on %blogname% but have not completed the email verification process. Failure to do so will result in your account being removed.</p><p>Please use the link below to verify your email address. </p><p>%activationurl%</p><p>Best Wishes,</p><p>Team %blogname%</p>';
			$update['user_message_email_user_temp_blocked_notice']					= ($current['user_message_email_user_temp_blocked_notice'])?$current['user_message_email_user_temp_blocked_notice'] :__("Now, You are temporary block at","piereg")." %blogname%";
			$update['user_message_email_user_renew_temp_blocked_account_notice']	= ($current['user_message_email_user_renew_temp_blocked_account_notice'])?$current['user_message_email_user_renew_temp_blocked_account_notice'] : '<p>Dear %user_login%,</p><p>Our last attempt to charge the membership payment for your account has failed. </p><p>You are requested to log in to your account at %blogname% to provide a different payment method, or contact your bank/credit-card company to resolve this issue. </p><p>Access to your account has been temporarily disabled until this issue is resolved.</p><p>Kind Regards,</p><p>Team %blogname%</p>';
			$update['user_message_email_user_perm_blocked_notice']					= ($current['user_message_email_user_perm_blocked_notice'])? $current['user_message_email_user_perm_blocked_notice']: '<p>Dear %user_login%,</p><p>Our last attempt to charge the membership payment for your account failed. </p><p>You are requested to log in to your account at %blogname% to provide a different payment method, or contact your bank/credit-card company to resolve this issue. </p><p>Failure to do so will result in your account being removed from %blogname%.</p><p>Kind Regards,</p><p>Team %blogname%</p>';
			$update['user_message_email_email_edit_verification']					= ($current['user_message_email_email_edit_verification'])?$current['user_message_email_email_edit_verification']: '<p>Hello %user_login%,</p><p>We have received a request to change the email address for your account on %blogname%.</p><p>Old Email Address: %user_email%<br/>New Email Address: %user_new_email%. </p><p>Please use the link below to complete this change.</p><p>(%reset_email_url%)</p><p>Thanks</p><p>%blogname%<br/></p>';
			$update['user_message_email_current_email_verification']				= ($current['user_message_email_current_email_verification'])?$current['user_message_email_current_email_verification']: '<p>Hello %user_login%,</p><p>We have received a request to change the email address for your account on %blogname%.</p><p>Old Email Address: %user_email%<br/>  New Email Address: %user_new_email%. </p><p>If you requested this change, please use the link below to complete the action. Otherwise please ignore this email and the change will not be made.</p><p>(%confirm_current_email_url%)</p><p>Thanks</p><p>%blogname%<br/></p>';
			
			update_option(OPTION_PIE_REGISTER, $update );
			
			$current_fields 	= maybe_unserialize(get_option( 'pie_fields' ));
			$fields 					= array();
			
			$fields['form']['label'] 				= ($current_fields['form']['label'])?$current_fields['form']['label']:__("Registration Form","piereg");
			$fields['form']['desc'] 				= ($current_fields['form']['desc'])?$current_fields['form']['desc']:__("Please fill in the form below to register.","piereg");
			$fields['form']['label_alignment'] 		= ($current_fields['form']['label_alignment'])?$current_fields['form']['label_alignment']:"left";
			$fields['form']['css']					= ($current_fields['form']['css'])?$current_fields['form']['css']:"";
			$fields['form']['type']					= ($current_fields['form']['type'])?$current_fields['form']['type']:"form";
			$fields['form']['meta']					= ($current_fields['form']['meta'])?$current_fields['form']['meta']:0;
			$fields['form']['reset']				= ($current_fields['form']['reset'])?$current_fields['form']['reset']:0;
			
			$fields[0]['label'] 		= ($current_fields[0]['label'])?$current_fields[0]['label']:__("Username","piereg");
			$fields[0]['type'] 			= ($current_fields[0]['type'])?$current_fields[0]['type']:"username";
			$fields[0]['id'] 			= ($current_fields[0]['id'])?$current_fields[0]['id']:0;
			$fields[0]['remove'] 		= ($current_fields[0]['remove'])?$current_fields[0]['remove']:0;
			$fields[0]['required'] 		= ($current_fields[0]['required'])?$current_fields[0]['required']:1;
			$fields[0]['desc'] 			= ($current_fields[0]['desc'])?$current_fields[0]['desc']:"";
			$fields[0]['length'] 		= ($current_fields[0]['length'])?$current_fields[0]['length']:"";
			$fields[0]['default_value'] = ($current_fields[0]['default_value'])?$current_fields[0]['default_value']:"";
			$fields[0]['placeholder'] 	= ($current_fields[0]['placeholder'])?$current_fields[0]['placeholder']:"";
			$fields[0]['css'] 			= ($current_fields[0]['css'])?$current_fields[0]['css']:""; 
			$fields[0]['meta']			= ($current_fields[0]['meta'])?$current_fields[0]['meta']:0;
			
			$fields[1]['label'] 			= ($current_fields[1]['label'])?$current_fields[1]['label']:__("Email","piereg");
			$fields[1]['label2'] 			= ($current_fields[1]['label2'])?$current_fields[1]['label2']:__("Confirm Email","piereg");
			$fields[1]['type'] 				= ($current_fields[1]['type'])?$current_fields[1]['type']:"email";
			$fields[1]['id'] 				= ($current_fields[1]['id'])?$current_fields[1]['id']:1;
			$fields[1]['remove'] 			= ($current_fields[1]['remove'])?$current_fields[1]['remove']:0;
			$fields[1]['required'] 			= ($current_fields[1]['required'])?$current_fields[1]['required']:1;
			$fields[1]['desc'] 				= ($current_fields[1]['desc'])?$current_fields[1]['desc']:"";
			$fields[1]['length'] 			= ($current_fields[1]['length'])?$current_fields[1]['length']:"";
			$fields[1]['default_value'] 	= ($current_fields[1]['default_value'])?$current_fields[1]['default_value']:"";
			$fields[1]['placeholder'] 		= ($current_fields[1]['placeholder'])?$current_fields[1]['placeholder']:"";
			$fields[1]['css'] 				= ($current_fields[1]['css'])?$current_fields[1]['css']:""; 
			$fields[1]['validation_rule'] 	= ($current_fields[1]['validation_rule'])?$current_fields[1]['validation_rule']:"email";
			$fields[1]['meta']				= ($current_fields[1]['meta'])?$current_fields[1]['meta']:0;
			
			$fields[2]['label'] 			= ($current_fields[2]['label'])?$current_fields[2]['label']:__("Password","piereg");
			$fields[2]['label2'] 			= ($current_fields[2]['label2'])?$current_fields[2]['label2']:__("Confirm Password","piereg");
			$fields[2]['type'] 				= ($current_fields[2]['type'])?$current_fields[2]['type']:"password";
			$fields[2]['id'] 				= ($current_fields[2]['id'])?$current_fields[2]['id']:2;
			$fields[2]['remove'] 			= ($current_fields[2]['remove'])?$current_fields[2]['remove']:0;
			$fields[2]['required'] 			= ($current_fields[2]['required'])?$current_fields[2]['required']:1;
			$fields[2]['desc'] 				= ($current_fields[2]['desc'])?$current_fields[2]['desc']:"";
			$fields[2]['length'] 			= ($current_fields[2]['length'])?$current_fields[2]['length']:"";
			$fields[2]['default_value'] 	= ($current_fields[2]['default_value'])?$current_fields[2]['default_value']:"";
			$fields[2]['placeholder'] 		= ($current_fields[2]['placeholder'])?$current_fields[2]['placeholder']:"";
			$fields[2]['css'] 				= ($current_fields[2]['css'])?$current_fields[2]['css']:""; 
			$fields[2]['validation_rule'] 	= ($current_fields[2]['validation_rule'])?$current_fields[2]['validation_rule']:""; 
			$fields[2]['meta']				= ($current_fields[2]['meta'])?$current_fields[2]['meta']:0;	
			$fields[2]['show_meter']		= ($current_fields[2]['show_meter'])?$current_fields[2]['show_meter']:1;		
						
			//Getting data from old plugins
			$num = 3;
			
			if ($current['firstname'] || $current ['lastname'])
			{
				$fields[$num]['type'] 			= "name";
				$fields[$num]['label'] 			= __("First Name","piereg");	
				$fields[$num]['label2'] 		= __("Last Name","piereg");
				$fields[$num]['field_name'] 	= "first_name";			
				$fields[$num]['id'] 			= $num;	
				$num++;		
			}
			
			if ($current['website'])
			{
				$fields[$num]['type'] 			= "default";
				$fields[$num]['label'] 			= __("Website","piereg");	
				$fields[$num]['field_name'] 	= "url";		
				$fields[$num]['id'] 			= $num;	
				$num++;		
			}
			if ($current ['aim'])
			{
				$fields[$num]['type'] 			= "default";
				$fields[$num]['label'] 			= __("AIM","piereg");
				$fields[$num]['field_name'] 	= "aim";			
				$fields[$num]['id'] 			= $num;	
				$num++;			
			}
			if ($current['yahoo'])
			{
				$fields[$num]['type'] 			= "default";
				$fields[$num]['label'] 			= __("Yahoo IM","piereg");
				$fields[$num]['field_name'] 	= "yim";			
				$fields[$num]['id'] 			= $num;	
				$num++;		
			}
			if ($current['jabber'])
			{
				$fields[$num]['type'] 			= "default";
				$fields[$num]['label'] 			= __("Jabber / Google Talk","piereg");
				$fields[$num]['field_name'] 	= "jabber";			
				$fields[$num]['id'] 			= $num;	
				$num++;		
			}
			if ($current['about'])
			{
				$fields[$num]['type'] 			= "default";
				$fields[$num]['label'] 			= __("About Yourself","piereg");	
				$fields[$num]['field_name'] 	= "description";			
				$fields[$num]['id'] 			= $num;	
				$num++;		
			}
			
			$piereg_custom = get_option( 'pie_register_custom' );
			if( is_array($piereg_custom ))
			{
				foreach( $piereg_custom as $k=>$v)
				{	
					
					if($v['fieldtype']=="select" || $v['fieldtype']=="checkbox" || $v['fieldtype']=="radio")//Populating values
					{
						$ops = explode(',',$v['extraoptions']);
						foreach( $ops as $op )
						{
							$fields[$num]['value'][] 	= $op;
							$fields[$num]['display'][] 	= $op;
						}
					}
					else
					{
						$fields[$num]['default_value'] 	= $v['extraoptions'];				
					}
					
					$fields[$num]['type'] 			= $v['fieldtype'];
					$fields[$num]['label'] 			= $v['label'];			
					$fields[$num]['id'] 			= $num;			
					$fields[$num]['required'] 		= $v['required'];
					
					if($fields[$num]['type']=="select")
					{
						$fields[$num]['type'] = "dropdown";	
					}
					
					if($fields[$num]['type']=="date")
					{
						$fields[$num]['date_type'] 	 	= "datepicker";
						$fields[$num]['date_format'] 	= $current["dateformat"];
						$fields[$num]['firstday'] 		= $current["firstday"];
						$fields[$num]['startdate'] 		= $current["startdate"];
						$fields[$num]['calyear'] 		= $current["calyear"];	
						$fields[$num]['calmonth'] 		= $current["calmonth"];				
					}
					
					$num++;
				}
			}
			
			$fields['submit']['message'] 			= __("Thank you for your registration","piereg");
			$fields['submit']['confirmation'] 		= "text";
			$fields['submit']['text'] 				= "Submit";
			$fields['submit']['reset']				= 0;
			$fields['submit']['reset_text'] 		= "Reset";
			$fields['submit']['type'] 				= "submit";
			$fields['submit']['meta']				= 0;
			$fields['submit']['redirect_url']		= "";
		
			
			update_option( 'pie_fields_default', $fields  );
			
			$structure 	= $this->getDefaultMeta();
			
					
			update_option( 'pie_fields_meta', $structure  );
			
			
			/*
				*	Get old form or create default form
			*/
			$created_form_id = $this->install_default_reg_form();
			
			//Alternate Pages
			$get_pie_pages_from_db		= get_option("pie_pages");
			if(is_array($get_pie_pages_from_db))
			{
				$piereg_registrtion = (isset($get_pie_pages_from_db[1]) and $get_pie_pages_from_db != "")? get_post_status($get_pie_pages_from_db[1]) : "null";
			}
			
			$pie_pages = get_option("pie_pages");
			
			if($piereg_registration_create_new_page && isset($pie_pages[1]) && !empty($pie_pages[1]) )//Registration
			{
				$_p = array();
				$_p['ID'] 				= intval($pie_pages[1]);
				$_p['post_title'] 		= __("Registration","piereg");
				$_p['post_content'] 	= '[pie_register_form id="' . ( (intval($created_form_id) > 0) ? intval($created_form_id) : "0" ) . '" title="true" description="true" ]';
				$_p['post_status'] 		= 'publish';
				$_p['post_type'] 		= 'page';
				$_p['comment_status'] 	= 'closed';
				$_p['ping_status'] 		= 'closed';
				wp_update_post( $_p );
			}
			
			
			global $wpdb;
			$prefix=$wpdb->prefix."pieregister_";
			$codetable=$prefix."code";
			$invitation_code_sql = "CREATE TABLE IF NOT EXISTS ".$codetable."(`id` INT( 5 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,`created` DATE NOT NULL ,`modified` DATE NOT NULL ,`name` TEXT NOT NULL ,`count` INT( 5 ) NOT NULL ,`status` INT( 2 ) NOT NULL ,`code_usage` INT( 5 ) NOT NULL) ENGINE = MYISAM ;";
			
			if(!$wpdb->query($invitation_code_sql))
			{
				$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
			}
			
			$status = $wpdb->get_results( $wpdb->prepare("SHOW COLUMNS FROM {$codetable}", '') ); #WPS
			if(isset($wpdb->last_error) && !empty($wpdb->last_error)){
				$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
			}
			$check = 0;
			foreach($status as $key=>$val)
			{
				if(trim(strtolower($val->Field)) == "code_usage")
				{
					$check = 1;
				}
				if(trim(strtolower($val->Field)) == "usage")
				{
					$check = 2;
				}
			}
			
			if($check === 2)
			{
				if(!$wpdb->query("ALTER TABLE ".$codetable." CHANGE `usage` `code_usage` int(11) NULL")){
					$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
				}				
			}
			
			if($check === 0)
			{
				if(!$wpdb->query("alter table ".$codetable." add column `code_usage` int(11) NULL")){
					$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
				}
			}
			
			
			$redirect_settings_table_name = $wpdb->prefix."pieregister_redirect_settings";
			$redirect_table_sql = "CREATE TABLE IF NOT EXISTS `".$redirect_settings_table_name."` (
									  `id` int(11) NOT NULL AUTO_INCREMENT,
									  `user_role` varchar(100) NOT NULL,
									  `logged_in_url` text NOT NULL,
									  `logged_in_page_id` int(11) NOT NULL,
									  `log_out_url` text NOT NULL,
									  `log_out_page_id` int(11) NOT NULL,
									  `status` bit(1) NOT NULL DEFAULT b'1',
									  PRIMARY KEY (`user_role`),
									  UNIQUE KEY `id` (`id`)
									) ENGINE=MYISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
			
			if(!$wpdb->query($redirect_table_sql))
			{
				$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
			}
			
			$lockdowns_table_name = $wpdb->prefix."pieregister_lockdowns";
			$lockdowns_table_sql = "CREATE TABLE IF NOT EXISTS `".$lockdowns_table_name."` (
									  `id` int(11) NOT NULL AUTO_INCREMENT,
									  `user_id` int(11) NOT NULL,
									  `login_attempt` int(11) NOT NULL,
									  `attempt_from` varchar(56) NOT NULL,
									  `is_security_captcha` tinyint(4) NOT NULL DEFAULT '0',
									  `attempt_time` datetime NOT NULL,
									  `release_time` datetime NOT NULL,
									  `user_ip` varchar(30) NOT NULL,
									  PRIMARY KEY (`id`)
									) ENGINE=MYISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
			";
			
			if(!$wpdb->query($lockdowns_table_sql))
			{
				$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
			}
			
			$lockdowns_all_columns = "SHOW COLUMNS FROM ".$lockdowns_table_name;
			$lockdowns_get_columns = $wpdb->get_results($lockdowns_all_columns);
			if(!in_array('attempt_from',$lockdowns_get_columns)){
				$lockdowns_add_column = "ALTER TABLE `".$lockdowns_table_name."` ADD attempt_from varchar(56) NOT NULL AFTER login_attempt";
				$wpdb->query($lockdowns_add_column);
			}
		
			/*
				*	Create Pie-Register Stats array If Do not Exist
			*/
			$piereg_stats = get_option(PIEREG_STATS_OPTION);
			
			$new_piereg_stats = array();
			$new_piereg_stats['login']['view'] = (isset($piereg_stats['login']['view'])?$piereg_stats['login']['view']:0);
			$new_piereg_stats['login']['used'] = (isset($piereg_stats['login']['used'])?$piereg_stats['login']['used']:0);
			
			$new_piereg_stats['forgot']['view'] = (isset($piereg_stats['forgot']['view'])?$piereg_stats['forgot']['view']:0);
			$new_piereg_stats['forgot']['used'] = (isset($piereg_stats['forgot']['view'])?$piereg_stats['forgot']['view']:0);
			
			$new_piereg_stats['register']['view'] = (isset($piereg_stats['register']['view'])?$piereg_stats['register']['view']:0);
			$new_piereg_stats['register']['used'] = (isset($piereg_stats['register']['used'])?$piereg_stats['register']['used']:0);
			
			update_option(PIEREG_STATS_OPTION,$new_piereg_stats);
			unset($new_piereg_stats);
			unset($piereg_stats);
			
			/*
				*	Save Currency name and array
			*/
			$this->piereg_save_currency();
			
			//Adding active meta to existing users
			 $blogusers = get_users();
			 foreach ($blogusers as $user) 
			 {
				update_user_meta( $user->ID, 'active', 1);
			 }
			 
			# updating pieregister db version 
			update_option('piereg_plugin_db_version',PIEREG_DB_VERSION);
			
		}
		function activate_pieregister_license_key(){
			global $wpdb;
			$old_pr_keys = get_option("api_manager_example");
			
			$global_options = array();
			$global_options['api_key'] = ( (isset($old_pr_keys['api_key']) && !empty($old_pr_keys['api_key'])) ? $old_pr_keys['api_key'] : "" );
			$global_options['activation_email'] = ( (isset($old_pr_keys['activation_email']) && !empty($old_pr_keys['activation_email'])) ? $old_pr_keys['activation_email'] : "" );
			
			if( empty($global_options['api_key']) || empty($old_pr_keys['activation_email']) ) :
				update_option( 'api_manager_example', $global_options );
				
				if( file_exists(PIEREG_DIR_NAME . '/classes/api/class-wc-api-manager-passwords.php') )
					require_once(PIEREG_DIR_NAME . '/classes/api/class-wc-api-manager-passwords.php');
		
				$API_Manager_Example_Password_Management = new API_Manager_Example_Password_Management();
				// Generate a unique installation $instance id
				$instance = $API_Manager_Example_Password_Management->generate_password( 12, false );
				
				$single_options = array(
					'piereg_api_manager_product_id' 			=> 'Pie-Register-pro',
					'piereg_api_manager_instance' 				=> $instance,
					'api_manager_example_deactivate_checkbox' 	=> 'on',
					'piereg_api_manager_activated' 				=> 'Deactivated',
					);
				foreach ( $single_options as $key => $value ) {
					update_option( $key, $value );
				}
				
				$curr_ver = get_option( $this->piereg_api_manager_version_name );
				// checks if the current plugin version is lower than the version being installed
				if ( version_compare( $this->version, $curr_ver, '>' ) ) {
					// update the version
					update_option( $this->piereg_api_manager_version_name, $this->version );
				}
			endif;
		}
		function install_default_reg_form(){
			$form_id = get_option("piereg_form_fields_id");
			$all_forms_info = $this->get_pr_forms_info();
			
			if(empty($form_id) || count($all_forms_info) == 0 )
			{
				$form_id = intval($form_id)+1;//increment form id
				update_option("piereg_form_fields_id",$form_id);//updated form id
				
				update_option('piereg_form_free_id', $form_id); // assignining reg for free ver
				
				$pie_fields = get_option("pie_fields");//get reg form
				$pie_fields = ( (empty($pie_fields)) ? get_option("pie_fields_default") : $pie_fields );//get default form
				
				
				// add membership field if paypal payment is ON.
				$current 				= get_option(OPTION_PIE_REGISTER);
				$pie_plugin_db_version 	= get_option('piereg_plugin_db_version');
				$pie_plugin_db_version 	= explode('.',$pie_plugin_db_version);		
				if($pie_plugin_db_version[0] == 2 && isset($current['enable_paypal'],$current['paypal_butt_id']) && ($current['enable_paypal'] == 1 && !empty($current['paypal_butt_id'])) )
				{
					$pie_fields		= maybe_unserialize($pie_fields);
					
					$pie_fields_submit = $pie_fields['submit'];
					unset($pie_fields['submit']);
					
					$int_m = count($pie_fields);
					
					$membership_field['id'] 					= $int_m;
					$membership_field['type'] 					= "pricing";
					$membership_field['label'] 					= "Membership";
					$membership_field['desc'] 					= ""; 
					$membership_field['allow_payment_gateways'] = array("PaypalStandard");
					$membership_field['validation_message'] 	= ""; 
					$membership_field['css'] 					= ""; 
					$membership_field['field_as'] 				= 1;
					
					array_push($pie_fields,$membership_field);
					$pie_fields['submit'] = $pie_fields_submit;
					
					file_put_contents('logfile.txt',print_r($pie_fields,1));
					if( !is_serialized( $pie_fields ) )
					{
						serialize($pie_fields);
					}
				}
				// add membership field if paypal payment is ON.
				
				
				if( is_serialized( $pie_fields ) ) {
					update_option("piereg_form_fields_".$form_id, $pie_fields );//install default form
					update_option("pie_fields_prev", $pie_fields ); 
				} else {
					update_option("piereg_form_fields_".$form_id, serialize($pie_fields) );//install default form
					update_option("pie_fields_prev", serialize($pie_fields) ); 
				}
				$_field['Id'] = $form_id;
				$_field['Title'] = ( (isset($pie_fields['form']['label']) && !empty($pie_fields['form']['label']) ) ? $pie_fields['form']['label'] : 'Registration Form' );
				$_field['Views'] = "0";
				$_field['Entries'] = "0";
				$_field['Status'] = "enable";
				update_option("piereg_form_field_option_".$form_id, $_field);
			}
			return $form_id;
		}
		function getDefaultMeta()
		{
			$structure = array();
			$structure["text"] = '<div class="fields_main"><div class="advance_options_fields"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" id="label_%d%" class="input_fields field_label"></div><div class="advance_fields"><label for="desc_%d%">'.__("Description","piereg").'</label><textarea name="field[%d%][desc]" id="desc_%d%" rows="8" cols="16"></textarea></div><div class="advance_fields"><label for="length_%d%">'.__("Length","piereg").'</label><input type="text" name="field[%d%][length]" id="length_%d%" class="input_fields character_fields field_length numeric"></div><div class="advance_fields"><label>'.__("Rules","piereg").'</label><input name="field[%d%][required]" id="required_%d%" value="%d%" type="checkbox" class="checkbox_fields"><label for="required_%d%" class="required">'.__("Required","piereg").'</label></div><div class="advance_fields"><label for="default_value_%d%">'.__("Default Value","piereg").'</label><input type="text" name="field[%d%][default_value]" id="default_value_%d%" class="input_fields field_default_value"></div><div class="advance_fields"><label for="placeholder_%d%">'.__("Placeholder","piereg").'</label><input type="text" name="field[%d%][placeholder]" id="placeholder_%d%" class="input_fields field_placeholder"></div><div class="advance_fields"><label for="validation_rule_%d%">'.__("Validation Rule","piereg").'</label><select name="field[%d%][validation_rule]" id="validation_rule_%d%"><option>'.__("None","piereg").'</option><option value="number">'.__("Number","piereg").'</option><option value="alphanumeric">'.__("Alphanumeric","piereg").'</option><option value="email">'.__("Email","piereg").'</option><option value="website">'.__("Website","piereg").'</option><option value="standard">'.__("USA Format","piereg").' (xxx) (xxx-xxxx)</option><option value="international">'.__("Phone International","piereg").' xxx-xxx-xxxx</option></select></div><div class="advance_fields"><label for="validation_message_%d%">'.__("Validation Message","piereg").'</label><input type="text" name="field[%d%][validation_message]" id="validation_message_%d%" class="input_fields"></div><div class="advance_fields"><label for="css_%d%">'.__("CSS Class Name","piereg").'</label><input type="text" name="field[%d%][css]" id="css_%d%" class="input_fields"></div><div class="advance_fields"><label for="show_in_profile_%d%">'.__("Show in Profile","piereg").'</label><select name="field[%d%][show_in_profile]" id="show_in_profile_%d%"  class="show_in_profile checkbox_fields"><option value="1" selected="selected">'.__("Yes","piereg").'</option><option value="0">'.__("No","piereg").'</option></select></div>'; 
			
			if( $this->piereg_pro_is_activate )
			{
				$structure["text"] .= '<div class="advance_fields"><label>'.__("Enable Conditional Logic","piereg").'</label><select name="field[%d%][conditional_logic]" data-conditional_area="conditional_area_%d%" id="conditional_logic_%d%"  class="checkbox_fields enabel_conditional_logic"><option value="1">'.__("Yes","piereg").'</option><option value="0" selected="selected" >'.__("No","piereg").'</option></select></div><div class="advance_fields" id="conditional_area_%d%" style="display:none;"><label for="field_status_%d%"></label><select name="field[%d%][field_status]" id="field_status_%d%"  class="field_status" style="width:auto;"><option value="1" selected="selected">'.__("Show","piereg").'</option><option value="0">'.__("Hide","piereg").'</option></select><span style="color:#fff;"> '.__("this field if","piereg").' </span><input type="hidden" name="field[%d%][selected_field]" id="selected_field_%d%" class="selected_field_input"><select data-selected_field="selected_field_%d%" class="selected_field piereg_all_field_dropdown" style="width:100px;"></select><select class="field_rule_operator_select" id="field_rule_operator" name="field[%d%][field_rule_operator]" style="width:auto;"><option selected="selected" value="==">equal</option><option value="!=">not equal</option><option value="empty">empty</option><option value="not_empty">not empty</option><option value=">">greater than</option><option value="<">less than</option><option value="contains">contains</option><option value="starts_with">starts with</option><option value="ends_with">ends with</option><option value="range">range</option></select><div class="wrap_cond_value"><input type="text" name="field[%d%][conditional_value]" id="conditional_value_%d%" class="input_fields conditional_value_input" placeholder="Enter Value"></div></div>';
			}
			$structure["text"] .= '</div></div>';
			
			$structure["username"] = '<input type="hidden" value="0" class="input_fields" name="field[%d%][meta]" id="meta_%d%"><div class="fields_main"><div class="advance_options_fields"><input type="hidden" value="1" name="field[%d%][required]"><input type="hidden" name="field[%d%][label]"><input type="hidden" name="field[%d%][validation_rule]"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" value="Username" id="label_%d%" class="input_fields field_label"></div><div class="advance_fields"><label for="desc_%d%">'.__("Description","piereg").'</label><textarea name="field[%d%][desc]" id="desc_%d%" rows="8" cols="16"></textarea></div><div class="advance_fields"><label for="placeholder_%d%">'.__("Placeholder","piereg").'</label><input type="text" name="field[%d%][placeholder]" id="placeholder_%d%" class="input_fields field_placeholder"></div><div class="advance_fields"><label for="validation_message_%d%">'.__("Validation Message","piereg").'</label><input type="text" name="field[%d%][validation_message]" id="validation_message_%d%" class="input_fields"></div><div class="advance_fields"><label for="css_%d%">'.__("CSS Class Name","piereg").'</label><input type="text" name="field[%d%][css]" id="css_%d%" class="input_fields"><input type="hidden" id="default_username"></div>';
			
			$structure["username"] .='</div></div>';
			
			$structure["honeypot"] = '<div class="fields_main"><div class="advance_options_fields"><input type="hidden" value="1" name="field[%d%][required]"><input type="hidden" id="default_honeypot"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" value="Honeypot" id="label_%d%" class="input_fields field_label"></div><div class="advance_fields"><label for="validation_message_%d%">'.__("Validation Message","piereg").'</label><input type="text" name="field[%d%][validation_message]" id="validation_message_%d%" class="input_fields" value="Spamming not allowed"></div></div></div>';
	
			$structure["default"] = '<div class="fields_main"><div class="advance_options_fields"><input type="hidden" class="input_fields" name="field[%d%][type]"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" id="label_%d%" class="input_fields field_label"></div></div></div>';
			
			$structure["aim"] = '<div class="fields_main"><div class="advance_options_fields"><input type="hidden" class="input_fields" name="field[%d%][type]"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" value="AIM" id="label_%d%" class="input_fields field_label"></div></div></div>';
			
			
			$structure["url"] = '<div class="fields_main"><div class="advance_options_fields"><input type="hidden" class="input_fields" name="field[%d%][type]"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" id="label_%d%" value="Website" class="input_fields field_label"></div></div></div>';
			
			
			$structure["yim"] = '<div class="fields_main"><div class="advance_options_fields"><input type="hidden" class="input_fields" name="field[%d%][type]"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" id="label_%d%" value="Yahoo IM" class="input_fields field_label"></div></div></div>';
			
			
			$structure["description"] = '<div class="fields_main"><div class="advance_options_fields"><input type="hidden" class="input_fields" name="field[%d%][type]"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" id="label_%d%" value="About Yourself" class="input_fields field_label"></div></div></div>';
			
			
			$structure["jabber"] = '<div class="fields_main"><div class="advance_options_fields"><input type="hidden" class="input_fields" name="field[%d%][type]"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" value="Jabber / Google Talk" id="label_%d%" class="input_fields field_label"></div></div></div>';
			
			$structure["password"] = '<div class="fields_main"><div class="advance_options_fields"><input type="hidden" value="1" name="field[%d%][required]"><input type="hidden" name="field[%d%][validation_rule]"><input type="hidden" value="0" class="input_fields" name="field[%d%][meta]" id="meta_%d%"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" value="Password" id="label_%d%" class="input_fields field_label"></div><div class="advance_fields"><label for="label2_%d%">'.__("Label2","piereg").'</label><input type="text" name="field[%d%][label2]" value="Confrim Password" id="label2_%d%" class="input_fields field_label"></div><div class="advance_fields"><label for="desc_%d%">'.__("Description","piereg").'</label><textarea name="field[%d%][desc]" id="desc_%d%" rows="8" cols="16"></textarea></div><div class="advance_fields"><label for="placeholder_%d%">Placeholder</label><input type="text" name="field[%d%][placeholder]" id="placeholder_%d%" class="input_fields field_placeholder"></div><div class="advance_fields"><label for="validation_message_%d%">'.__("Validation Message","piereg").'</label><input type="text" name="field[%d%][validation_message]" id="validation_message_%d%" class="input_fields"></div><div class="advance_fields"><label for="css_%d%">'.__("CSS Class Name","piereg").'</label><input type="text" name="field[%d%][css]" id="css_%d%" class="input_fields"></div><div class="advance_fields"><label for="show_meter_%d%">'.__("Show Strength Meter","piereg").'</label><select class="strength_meter show_meter checkbox_fields" name="field[%d%][show_meter]" id="show_meter_%d%"><option value="1" selected="selected">'.__("Yes","piereg").'</option><option value="0">'.__("No","piereg").'</option></select></div><div class="strength_labels_div"><div class="advance_fields"><label for="pass_strength_indicator_label_%d%">'.__("Strength Indicator",'piereg').'</label><input type="text" name="field[%d%][pass_strength_indicator_label]" id="pass_strength_indicator_label_%d%" class="input_fields" value="Strength Indicator" /></div><div class="advance_fields"><label for="pass_very_weak_label_%d%">'.__("Very Weak",'piereg').'</label><input type="text" name="field[%d%][pass_very_weak_label]" id="pass_very_weak_label_%d%" class="input_fields" value="Very Weak" /></div><div class="advance_fields"><label for="pass_weak_label_%d%">'.__("Weak",'piereg').'</label><input type="text" name="field[%d%][pass_weak_label]" id="pass_weak_label_%d%" class="input_fields" value="Weak" /></div><div class="advance_fields"><label for="pass_medium_label_%d%">'.__("Medium",'piereg').'</label><input type="text" name="field[%d%][pass_medium_label]" id="pass_medium_label_%d%" class="input_fields" value="Medium" /></div><div class="advance_fields"><label for="pass_strong_label_%d%">'.__("Strong",'piereg').'</label><input type="text" name="field[%d%][pass_strong_label]" id="pass_strong_label_%d%" class="input_fields" value="Strong" /></div><div class="advance_fields"><label for="pass_mismatch_label_%d%">'.__("Mismatch",'piereg').'</label><input type="text" name="field[%d%][pass_mismatch_label]" id="pass_mismatch_label_%d%" class="input_fields" value="Mismatch" /></div></div><div class="advance_fields"><label for="restrict_strength_%d%">'.__("Minimum Strength","piereg").'</label><select class="show_meter" name="field[%d%][restrict_strength]" id="restrict_strength_%d%"><option value="1" selected="selected">'.__("Very weak","piereg").'</option><option value="2">'.__("Weak","piereg").'</option><option value="3">'.__("Medium","piereg").'</option><option value="4">'.__("Strong","piereg").'</option></select></div><div class="advance_fields"><label for="strength_message_%d%">'.__("Strength Message","piereg").'</label><input type="text" name="field[%d%][strength_message]" id="strength_message_%d%" class="input_fields" value="Weak Password"></div>';
			
			
			$structure["password"] .= '</div></div>';
			
			$structure['email']	= '<input type="hidden" value="0" class="input_fields" name="field[%d%][meta]" id="meta_%d%"><div class="fields_main"><div class="advance_options_fields"><input type="hidden" value="1" name="field[%d%][required]"><input type="hidden" name="field[%d%][label]"><input type="hidden" name="field[%d%][validation_rule]"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" value="Email" id="label_%d%" class="input_fields field_label"></div><div class="advance_fields"><label for="label2_%d%">'.__("Label2","piereg").'</label><input type="text" name="field[%d%][label2]" value="Confrim Email" id="label2_%d%" class="input_fields field_label"></div><div class="advance_fields"><label for="desc_%d%">'.__("Description","piereg").'</label><textarea name="field[%d%][desc]" id="desc_%d%" rows="8" cols="16"></textarea></div><div class="advance_fields"><label for="confirm_email_%d%">'.__("Confirm Email","piereg").'</label><input name="field[%d%][confirm_email]" id="confirm_email" value="%d%" type="checkbox" class="checkbox_fields"></div><div class="advance_fields"><label for="placeholder_%d%">'.__("Placeholder","piereg").'</label><input type="text" name="field[%d%][placeholder]" id="placeholder_%d%" class="input_fields field_placeholder"></div><div class="advance_fields"><label for="validation_message_%d%">'.__("Validation Message","piereg").'</label><input type="text" name="field[%d%][validation_message]" id="validation_message_%d%" class="input_fields"></div><div class="advance_fields"><label for="css_%d%">'.__("CSS Class Name","piereg").'</label><input type="text" name="field[%d%][css]" id="css_%d%" class="input_fields"></div>';
			
			
			$structure['email']	.= '</div></div>';
			
			$structure["textarea"] = '<div class="fields_main"><div class="advance_options_fields"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" id="label_%d%" class="input_fields field_label"></div><div class="advance_fields"><label for="desc_%d%">'.__("Description","piereg").'</label><textarea name="field[%d%][desc]" id="desc_%d%" rows="8" cols="16"></textarea></div><div class="advance_fields"><label for="rows_%d%">'.__("Rows","piereg").'</label><input type="text" value="8" name="field[%d%][rows]" id="rows_%d%" class="input_fields character_fields field_rows numeric"></div><div class="advance_fields"><label for="cols_%d%">'.__("Columns","piereg").'</label><input type="text" value="73" name="field[%d%][cols]" id="cols_%d%" class="input_fields character_fields field_cols numeric"></div><div class="advance_fields"><label>'.__("Rules","piereg").'</label><input name="field[%d%][required]" id="required_%d%" value="%d%" type="checkbox" class="checkbox_fields"><label for="required_%d%" class="required">'.__("Required","piereg").'</label></div><div class="advance_fields"><label for="default_value_%d%">'.__("Default Value","piereg").'</label><input type="text" name="field[%d%][default_value]" id="default_value_%d%" class="input_fields field_default_value"></div><div class="advance_fields"><label for="placeholder_%d%">'.__("Placeholder","piereg").'</label><input type="text" name="field[%d%][placeholder]" id="placeholder_%d%" class="input_fields field_placeholder"></div><div class="advance_fields"><label for="validation_message_%d%">'.__("Validation Message","piereg").'</label><input type="text" name="field[%d%][validation_message]" id="validation_message_%d%" class="input_fields"></div><div class="advance_fields"><label for="css_%d%">'.__("CSS Class Name","piereg").'</label><input type="text" name="field[%d%][css]" id="css_%d%" class="input_fields"></div><div class="advance_fields"><label for="show_in_profile_%d%">'.__("Show in Profile","piereg").'</label><select class="show_in_profile" name="field[%d%][show_in_profile]" id="show_in_profile_%d%"  class="checkbox_fields"><option value="1" selected="selected">'.__("Yes","piereg").'</option><option value="0">'.__("No","piereg").'</option></select></div>';
						
			if( $this->piereg_pro_is_activate )
			{
				$structure["textarea"] .= '<div class="advance_fields"><label>'.__("Enable Conditional Logic","piereg").'</label><select name="field[%d%][conditional_logic]" data-conditional_area="conditional_area_%d%" id="conditional_logic_%d%"  class="checkbox_fields enabel_conditional_logic"><option value="1">'.__("Yes","piereg").'</option><option value="0" selected="selected" >'.__("No","piereg").'</option></select></div><div class="advance_fields" id="conditional_area_%d%" style="display:none;"><label for="field_status_%d%"></label><select name="field[%d%][field_status]" id="field_status_%d%"  class="field_status" style="width:auto;"><option value="1" selected="selected">'.__("Show","piereg").'</option><option value="0">'.__("Hide","piereg").'</option></select><span style="color:#fff;"> '.__("this field if","piereg").' </span><input type="hidden" name="field[%d%][selected_field]" id="selected_field_%d%" class="selected_field_input"><select data-selected_field="selected_field_%d%" class="selected_field piereg_all_field_dropdown" style="width:100px;"></select><select class="field_rule_operator_select" id="field_rule_operator" name="field[%d%][field_rule_operator]" style="width:auto;"><option selected="selected" value="==">equal</option><option value="!=">not equal</option><option value="empty">empty</option><option value="not_empty">not empty</option><option value=">">greater than</option><option value="<">less than</option><option value="contains">contains</option><option value="starts_with">starts with</option><option value="ends_with">ends with</option><option value="range">range</option></select><div class="wrap_cond_value"><input type="text" name="field[%d%][conditional_value]" id="conditional_value_%d%" class="input_fields conditional_value_input" placeholder="Enter Value"></div></div>';
			}
			
			$structure["textarea"] .= '</div></div>';
			
			
			$structure["dropdown"] = '<div class="fields_main"><div class="advance_options_fields"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" id="label_%d%" class="input_fields field_label"></div><div class="advance_fields"><label for="desc_%d%">'.__("Description","piereg").'</label><textarea name="field[%d%][desc]" id="desc_%d%" rows="8" cols="16"></textarea></div><div class="advance_fields multi_options sel_options_%d%"><label for="display_%d%">'.__("Display Value","piereg").'</label><input type="text" name="field[%d%][display][]" id="display_%d%" class="input_fields character_fields select_option_display"><label for="value_%d%">'.__("Value","piereg").'</label><input type="text" name="field[%d%][value][]" id="value_%d%" class="input_fields character_fields select_option_value"><label>'.__("Checked","piereg").'</label><input type="radio" value="0" id="check_%d%" name="field[%d%][selected][]" class="select_option_checked"><a style="color:white" href="javascript:;" onClick="addOptions(%d%,\'radio\',jQuery(this));">+</a><!--<a style="color:white;font-size: 13px;margin-left: 2px;" href="javascript:;" onclick="jQuery(this).parent().remove();">x</a>--></div><div class="advance_fields"><label>'.__("Rules","piereg").'</label><input name="field[%d%][required]" id="required_%d%" value="%d%" type="checkbox" class="checkbox_fields"><label for="required_%d%" class="required">'.__("Required","piereg").'</label></div><div class="advance_fields"><label for="validation_message_%d%">'.__("Validation Message","piereg").'</label><input type="text" name="field[%d%][validation_message]" id="validation_message_%d%" class="input_fields"></div><div class="advance_fields"><label for="css_%d%">'.__("CSS Class Name","piereg").'</label><input type="text" name="field[%d%][css]" id="css_%d%" class="input_fields"></div><div class="advance_fields"> <label for="list_type_%d%">'.__("List Type","piereg").'</label><select name="field[%d%][list_type]" id="list_type_%d%"><option>'.__("None","piereg").'</option><option value="country">'.__("Country","piereg").'</option><option value="us_states">'.__("US States","piereg").'</option><option value="can_states">'.__("Canada States","piereg").'</option> </select></div><div class="advance_fields"><label for="show_in_profile_%d%">'.__("Show in Profile","piereg").'</label><select class="show_in_profile" name="field[%d%][show_in_profile]" id="show_in_profile_%d%"  class="checkbox_fields"><option value="1" selected="selected">'.__("Yes","piereg").'</option><option value="0">'.__("No","piereg").'</option></select></div>';
			
			if( $this->piereg_pro_is_activate )
			{
				$structure["dropdown"] .= '<div class="advance_fields"><label>'.__("Enable Conditional Logic","piereg").'</label><select name="field[%d%][conditional_logic]" data-conditional_area="conditional_area_%d%" id="conditional_logic_%d%"  class="checkbox_fields enabel_conditional_logic"><option value="1">'.__("Yes","piereg").'</option><option value="0" selected="selected" >'.__("No","piereg").'</option></select></div><div class="advance_fields" id="conditional_area_%d%" style="display:none;"><label for="field_status_%d%"></label><select name="field[%d%][field_status]" id="field_status_%d%"  class="field_status" style="width:auto;"><option value="1" selected="selected">'.__("Show","piereg").'</option><option value="0">'.__("Hide","piereg").'</option></select><span style="color:#fff;"> '.__("this field if","piereg").' </span><input type="hidden" name="field[%d%][selected_field]" id="selected_field_%d%" class="selected_field_input"><select data-selected_field="selected_field_%d%" class="selected_field piereg_all_field_dropdown" style="width:100px;"></select><select class="field_rule_operator_select" id="field_rule_operator" name="field[%d%][field_rule_operator]" style="width:auto;"><option selected="selected" value="==">equal</option><option value="!=">not equal</option><option value="empty">empty</option><option value="not_empty">not empty</option><option value=">">greater than</option><option value="<">less than</option><option value="contains">contains</option><option value="starts_with">starts with</option><option value="ends_with">ends with</option><option value="range">range</option></select><div class="wrap_cond_value"><input type="text" name="field[%d%][conditional_value]" id="conditional_value_%d%" class="input_fields conditional_value_input" placeholder="Enter Value"></div></div>';
			}
			$structure["dropdown"] .= '</div></div>';
			
			
			$structure["multiselect"] = '<div class="fields_main"><div class="advance_options_fields"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" id="label_%d%" class="input_fields field_label"></div><div class="advance_fields"><label for="desc_%d%">'.__("Description","piereg").'</label><textarea name="field[%d%][desc]" id="desc_%d%" rows="8" cols="16"></textarea></div><div class="advance_fields multi_options sel_options_%d%"><label for="display_%d%">'.__("Display Value","piereg").'</label><input type="text" name="field[%d%][display][]" id="display_%d%" class="input_fields character_fields select_option_display"><label for="value_%d%">'.__("Value","piereg").'</label><input type="text" name="field[%d%][value][]" id="value_%d%" class="input_fields character_fields select_option_value"><label>'.__("Checked","piereg").'</label><input type="checkbox" value="0" id="check_%d%" name="field[%d%][selected][]" class="select_option_checked"><a style="color:white" href="javascript:;" onClick="addOptions(%d%,\'checkbox\',jQuery(this));">+</a><!--<a style="color:white;font-size: 13px;margin-left: 2px;" href="javascript:;" onclick="jQuery(this).parent().remove();">x</a>--></div><div class="advance_fields"><label>'.__("Rules","piereg").'</label><input name="field[%d%][required]" id="required_%d%" value="%d%" type="checkbox" class="checkbox_fields"><label for="required_%d%" class="required">'.__("Required","piereg").'</label></div><div class="advance_fields"><label for="validation_message_%d%">'.__("Validation Message","piereg").'</label><input type="text" name="field[%d%][validation_message]" id="validation_message_%d%" class="input_fields"></div><div class="advance_fields"><label for="css_%d%">'.__("CSS Class Name","piereg").'</label><input type="text" name="field[%d%][css]" id="css_%d%" class="input_fields"></div><div class="advance_fields"> <label for="list_type_%d%">'.__("List Type","piereg").'</label><select name="field[%d%][list_type]" id="list_type_%d%"><option>'.__("None","piereg").'</option><option value="country">'.__("Country","piereg").'</option><option value="us_states">'.__("US States","piereg").'</option><option value="can_states">'.__("Canada States","piereg").'</option></select></div><div class="advance_fields"><label for="show_in_profile_%d%">'.__("Show in Profile","piereg").'</label><select class="show_in_profile" name="field[%d%][show_in_profile]" id="show_in_profile_%d%"  class="checkbox_fields"><option value="1" selected="selected">'.__("Yes","piereg").'</option><option value="0">'.__("No","piereg").'</option></select></div>';
			
			
			$structure["multiselect"] .= '</div></div>';
						
			
			$structure["number"] = '<div class="fields_main"><div class="advance_options_fields"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" id="label_%d%" class="input_fields field_label"></div><div class="advance_fields"><label for="desc_%d%">'.__("Description","piereg").'</label><textarea name="field[%d%][desc]" id="desc_%d%" rows="8" cols="16"></textarea></div><div class="advance_fields"><label for="min_%d%">'.__("Min","piereg").'</label><input type="text" name="field[%d%][min]" id="min_%d%" class="input_fields character_fields  numeric"></div><div class="advance_fields"><label for="max_%d%">'.__("Max","piereg").'</label><input type="text" name="field[%d%][max]" id="max_%d%" class="input_fields character_fields  numeric"></div><div class="advance_fields"><label>'.__("Rules","piereg").'</label><input name="field[%d%][required]" id="required_%d%" value="%d%" type="checkbox" class="checkbox_fields"><label for="required_%d%" class="required">'.__("Required","piereg").'</label></div><div class="advance_fields"><label for="default_value_%d%">'.__("Default Value","piereg").'</label><input type="text" name="field[%d%][default_value]" id="default_value_%d%" class="input_fields field_default_value"></div><div class="advance_fields"><label for="placeholder_%d%">'.__("Placeholder","piereg").'</label><input type="text" name="field[%d%][placeholder]" id="placeholder_%d%" class="input_fields field_placeholder"></div><div class="advance_fields"><label for="validation_message_%d%">'.__("Validation Message","piereg").'</label><input type="text" name="field[%d%][validation_message]" id="validation_message_%d%" class="input_fields"></div><div class="advance_fields"><label for="css_%d%">'.__("CSS Class Name","piereg").'</label><input type="text" name="field[%d%][css]" id="css_%d%" class="input_fields"></div><div class="advance_fields"><label for="show_in_profile_%d%">'.__("Show in Profile","piereg").'</label><select name="field[%d%][show_in_profile]" id="show_in_profile_%d%"  class="show_in_profile checkbox_fields"><option value="1" selected="selected">'.__("Yes","piereg").'</option><option value="0">'.__("No","piereg").'</option></select></div>';
			
			if( $this->piereg_pro_is_activate )
			{
				$structure["number"] .= '<div class="advance_fields"><label>'.__("Enable Conditional Logic","piereg").'</label><select name="field[%d%][conditional_logic]" data-conditional_area="conditional_area_%d%" id="conditional_logic_%d%"  class="checkbox_fields enabel_conditional_logic"><option value="1">'.__("Yes","piereg").'</option><option value="0" selected="selected" >'.__("No","piereg").'</option></select></div><div class="advance_fields" id="conditional_area_%d%" style="display:none;"><label for="field_status_%d%"></label><select name="field[%d%][field_status]" id="field_status_%d%"  class="field_status" style="width:auto;"><option value="1" selected="selected">'.__("Show","piereg").'</option><option value="0">'.__("Hide","piereg").'</option></select><span style="color:#fff;"> '.__("this field if","piereg").' </span><input type="hidden" name="field[%d%][selected_field]" id="selected_field_%d%" class="selected_field_input"><select data-selected_field="selected_field_%d%" class="selected_field piereg_all_field_dropdown" style="width:100px;"></select><select class="field_rule_operator_select" id="field_rule_operator" name="field[%d%][field_rule_operator]" style="width:auto;"><option selected="selected" value="==">equal</option><option value="!=">not equal</option><option value="empty">empty</option><option value="not_empty">not empty</option><option value=">">greater than</option><option value="<">less than</option><option value="contains">contains</option><option value="starts_with">starts with</option><option value="ends_with">ends with</option><option value="range">range</option></select><div class="wrap_cond_value"><input type="text" name="field[%d%][conditional_value]" id="conditional_value_%d%" class="input_fields conditional_value_input" placeholder="Enter Value"></div></div>';
			}
			$structure["number"] .= '</div></div>';
			
			$structure["checkbox"] = '<div class="fields_main">  <div class="advance_options_fields"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" id="label_%d%" class="input_fields field_label"></div><div class="advance_fields"><label for="desc_%d%">'.__("Description","piereg").'</label><textarea name="field[%d%][desc]" id="desc_%d%" rows="8" cols="16"></textarea></div>  <div class="advance_fields multi_options sel_options_%d%"><label for="display_%d%">'.__("Display Value","piereg").'</label><input type="text" name="field[%d%][display][]" id="display_%d%" class="input_fields character_fields checkbox_option_display"><label for="value_%d%">'.__("Value","piereg").'</label><input type="text" name="field[%d%][value][]" id="value_%d%" class="input_fields character_fields checkbox_option_value"><label>'.__("Checked","piereg").'</label><input type="checkbox" value="0" id="check_%d%" name="field[%d%][selected][]" class="checkbox_option_checked"><a style="color:white" href="javascript:;" onClick="addOptions(%d%,\'checkbox\');">+</a></div><div class="advance_fields"><label>'.__("Rules","piereg").'</label><input name="field[%d%][required]" id="required_%d%" value="%d%" type="checkbox" class="checkbox_fields"><label for="required_%d%" class="required">'.__("Required","piereg").'</label></div><div class="advance_fields"><label for="validation_message_%d%">'.__("Validation Message","piereg").'</label><input type="text" name="field[%d%][validation_message]" id="validation_message_%d%" class="input_fields"></div><div class="advance_fields"><label for="css_%d%">'.__("CSS Class Name","piereg").'</label><input type="text" name="field[%d%][css]" id="css_%d%" class="input_fields"></div> <div class="advance_fields"><label for="show_in_profile_%d%">'.__("Show in Profile","piereg").'</label><select class="show_in_profile" name="field[%d%][show_in_profile]" id="show_in_profile_%d%"  class="checkbox_fields"><option value="1" selected="selected">'.__("Yes","piereg").'</option><option value="0">'.__("No","piereg").'</option></select></div> </div></div>';
			
			$structure["radio"] = '<div class="fields_main"><div class="advance_options_fields"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" id="label_%d%" class="input_fields field_label"></div><div class="advance_fields"><label for="desc_%d%">'.__("Description","piereg").'</label><textarea name="field[%d%][desc]" id="desc_%d%" rows="8" cols="16"></textarea></div><div class="advance_fields multi_options sel_options_%d%"><label for="display_%d%">'.__("Display Value","piereg").'</label><input type="text" name="field[%d%][display][]" id="display_%d%" class="input_fields character_fields radio_option_display"><label for="value_%d%">'.__("Value","piereg").'</label><input type="text" name="field[%d%][value][]" id="value_%d%" class="input_fields character_fields radio_option_value"><label>'.__("Checked","piereg").'</label><input type="radio" value="0" id="check_%d%" name="field[%d%][selected][]" class="radio_option_checked"><a style="color:white" href="javascript:;" onClick="addOptions(%d%,\'radio\');">+</a></div><div class="advance_fields"><label>'.__("Rules","piereg").'</label><input name="field[%d%][required]" id="required_%d%" value="%d%" type="checkbox" class="checkbox_fields"><label for="required_%d%" class="required">'.__("Required","piereg").'</label></div><div class="advance_fields"><label for="validation_message_%d%">'.__("Validation Message","piereg").'</label><input type="text" name="field[%d%][validation_message]" id="validation_message_%d%" class="input_fields"></div><div class="advance_fields"><label for="css_%d%">'.__("CSS Class Name","piereg").'</label><input type="text" name="field[%d%][css]" id="css_%d%" class="input_fields"></div><div class="advance_fields"><label for="show_in_profile_%d%">'.__("Show in Profile","piereg").'</label><select class="show_in_profile checkbox_fields" name="field[%d%][show_in_profile]" id="show_in_profile_%d%"><option value="1" selected="selected">'.__("Yes","piereg").'</option><option value="0">'.__("No","piereg").'</option></select></div></div></div>';
			
			$structure["html"] 	= '<input type="hidden" value="0" class="input_fields" name="field[%d%][meta]" id="meta_%d%"><div class="fields_main"><div class="advance_options_fields"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" id="label_%d%" class="input_fields field_label"></div><div class="advance_fields"><textarea rows="8" id="htmlbox_%d%" class="ckeditor" name="field[%d%][html]" cols="16"></textarea></div></div></div>';
			
			$structure["sectionbreak"] = '<input type="hidden" value="0" class="input_fields" name="field[%d%][meta]" id="meta_%d%"><div class="fields_main"><div class="advance_options_fields"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" id="label_%d%" class="input_fields field_label"></div>';
			
			$structure["sectionbreak"] .= '</div></div>';
			
			$structure["pagebreak"] 	= '<div class="fields_main"><div class="advance_options_fields"><input type="hidden" value="0" class="input_fields" name="field[%d%][meta]" id="meta_%d%"><div class="advance_fields"><label for="next_button_%d%">'.__("Next Button","piereg").'</label><div class="calendar_icon_type">  <input class="next_button" type="radio" id="next_button_%d%_text" name="field[%d%][next_button]" value="text" checked="checked">  <label for="next_button_%d%_text">'.__("Text","piereg").' </label>  <input class="next_button" type="radio" id="next_button_%d%_url" name="field[%d%][next_button]" value="url"><label for="next_button_%d%_url"> '.__("Image","piereg").'</label></div><div id="next_button_url_container_%d%" style="float:left;clear: both;display: none;">  <label for="next_button_%d%_url"> '.__("Image URL","piereg").': </label>  <input type="text" name="field[%d%][next_button_url]" class="input_fields" id="next_button_%d%_url"></div><div id="next_button_text_container_%d%" style="float:left;clear: both;">  <label for="next_button_%d%_text"> '.__("Text","piereg").': </label>  <input type="text" name="field[%d%][next_button_text]" value="Next" class="input_fields" id="next_button_%d%_text"></div></div><div class="advance_fields"><label for="prev_button_%d%">'.__("Previous Button","piereg").'</label><div class="calendar_icon_type">  <input class="prev_button" type="radio" id="prev_button_%d%_text" name="field[%d%][prev_button]" value="text" checked="checked">  <label for="prev_button_%d%_text">'.__("Text","piereg").' </label>  <input class="prev_button" type="radio" id="prev_button_%d%_url" name="field[%d%][prev_button]" value="url">  <label for="prev_button_%d%_url"> '.__("Image","piereg").'</label></div><div id="prev_button_url_container_%d%" style="float:left;clear: both;display: none;">  <label for="prev_button_%d%_url"> '.__("Image URL","piereg").': </label>  <input type="text" name="field[%d%][prev_button_url]" class="input_fields" id="prev_button_%d%_url"></div><div id="prev_button_text_container_%d%" style="float:left;clear: both;">  <label for="prev_button_%d%_text"> '.__("Text","piereg").': </label>  <input type="text" name="field[%d%][prev_button_text]" value="Previous" class="input_fields" id="prev_button_%d%_text"></div></div></div></div>';
						
			$structure['name']	= '<div class="fields_main"><div class="advance_options_fields"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" value="First Name" id="label_%d%" class="input_fields field_label"><input type="hidden" name="field[%d%][validation_rule]"></div><div class="advance_fields"><label for="label2_%d%">'.__("Label2","piereg").'</label><input type="text" name="field[%d%][label2]" value="Last Name" id="label2_%d%" class="input_fields field_label2"></div><div class="advance_fields"><label for="desc_%d%">'.__("Description","piereg").'</label><textarea name="field[%d%][desc]" id="desc_%d%" rows="8" cols="16"></textarea></div><div class="advance_fields"><label for="required_%d%">'.__("Rules","piereg").'</label><input name="field[%d%][required]" id="required_%d%" value="%d%" type="checkbox" class="checkbox_fields"><label for="required_%d%" class="required">'.__("Required","piereg").'</label></div><div class="advance_fields"><label for="validation_message_%d%">'.__("Validation Message","piereg").'</label><input type="text" name="field[%d%][validation_message]" id="validation_message_%d%" class="input_fields"></div><div class="advance_fields"><label for="css_%d%">'.__("CSS Class Name","piereg").'</label><input type="text" name="field[%d%][css]" id="css_%d%" class="input_fields"></div><div class="advance_fields"><label for="show_in_profile_%d%">'.__("Show in Profile","piereg").'</label><select name="field[%d%][show_in_profile]" id="show_in_profile_%d%"  class="show_in_profile checkbox_fields"><option value="1" selected="selected">'.__("Yes","piereg").'</option><option value="0">'.__("No","piereg").'</option></select></div>';
		
		if( $this->piereg_pro_is_activate )
		{
			$structure['name'] .= '<div class="advance_fields"><label>'.__("Enable Conditional Logic","piereg").'</label><select name="field[%d%][conditional_logic]" data-conditional_area="conditional_area_%d%" id="conditional_logic_%d%"  class="checkbox_fields enabel_conditional_logic"><option value="1">'.__("Yes","piereg").'</option><option value="0" selected="selected" >'.__("No","piereg").'</option></select></div><div class="advance_fields" id="conditional_area_%d%" style="display:none;"><label for="field_status_%d%"></label><select name="field[%d%][field_status]" id="field_status_%d%" class="field_status" style="width:auto;"><option value="1" selected="selected">'.__("Show","piereg").'</option><option value="0">'.__("Hide","piereg").'</option></select><span style="color:#fff;"> '.__("this field if","piereg").' </span><input type="hidden" name="field[%d%][selected_field]" id="selected_field_%d%" class="selected_field_input"><select data-selected_field="selected_field_%d%" class="selected_field piereg_all_field_dropdown" style="width:100px;"></select><select class="field_rule_operator_select" id="field_rule_operator" name="field[%d%][field_rule_operator]" style="width:auto;"><option selected="selected" value="==">equal</option><option value="!=">not equal</option><option value="empty">empty</option><option value="not_empty">not empty</option><option value=">">greater than</option><option value="<">less than</option><option value="contains">contains</option><option value="starts_with">starts with</option><option value="ends_with">ends with</option><option value="range">range</option></select><div class="wrap_cond_value"><input type="text" name="field[%d%][conditional_value]" id="conditional_value_%d%" class="input_fields conditional_value_input" placeholder="Enter Value"></div></div>';
		}
		
		$structure['name'] .= '</div></div>';
	
		$structure['time']	= '<div class="fields_main"><div class="advance_options_fields"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" id="label_%d%" class="input_fields field_label"></div><div class="advance_fields"> <label for="time_type_%d%">'.__("List Type","piereg").'</label><select class="time_format" name="field[%d%][time_type]" id="time_type_%d%"><option value="12">'.__("12 hour","piereg").'</option><option value="24">'.__("24 hour","piereg").'</option></select></div><div class="advance_fields"><label for="desc_%d%">'.__("Description","piereg").'</label><textarea name="field[%d%][desc]" id="desc_%d%" rows="8" cols="16"></textarea></div><div class="advance_fields"><label>'.__("Rules","piereg").'</label><input name="field[%d%][required]" id="required_%d%" value="%d%" type="checkbox" class="checkbox_fields"><label for="required_%d%" class="required">'.__("Required","piereg").'</label></div><div class="advance_fields"><label for="validation_message_%d%">'.__("Validation Message","piereg").'</label><input type="text" name="field[%d%][validation_message]" id="validation_message_%d%" class="input_fields"></div><div class="advance_fields"><label for="css_%d%">'.__("CSS Class Name","piereg").'</label><input type="text" name="field[%d%][css]" id="css_%d%" class="input_fields"></div><div class="advance_fields"><label for="show_in_profile_%d%">'.__("Show in Profile","piereg").'</label><select class="show_in_profile" name="field[%d%][show_in_profile]" id="show_in_profile_%d%"  class="checkbox_fields"><option value="1" selected="selected">'.__("Yes","piereg").'</option><option value="0">'.__("No","piereg").'</option></select></div></div></div>';
			
	
		
		$structure['website']	= '<div class="fields_main"><div class="advance_options_fields"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" id="label_%d%" class="input_fields field_label"></div><div class="advance_fields"><label for="desc_%d%">'.__("Description","piereg").'</label><textarea name="field[%d%][desc]" id="desc_%d%" rows="8" cols="16"></textarea></div><div class="advance_fields"><label>'.__("Rules","piereg").'</label><input name="field[%d%][required]" id="required_%d%" value="%d%" type="checkbox" class="checkbox_fields"><label for="required_%d%" class="required">'.__("Required","piereg").'</label></div><div class="advance_fields"><label for="validation_message_%d%">'.__("Validation Message","piereg").'</label><input type="text" name="field[%d%][validation_message]" id="validation_message_%d%" class="input_fields"></div><div class="advance_fields"><label for="css_%d%">'.__("CSS Class Name","piereg").'</label><input type="text" name="field[%d%][css]" id="css_%d%" class="input_fields"></div></div></div>';
		
		$structure['upload']	= '<div class="fields_main"><div class="advance_options_fields"><input type="hidden" class="input_fields" name="field[%d%][type]"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" id="label_%d%" class="input_fields field_label"></div><div class="advance_fields"><label for="file_types_%d%">'.__("File Types","piereg").'</label><input type="text" name="field[%d%][file_types]" id="file_types_%d%" class="input_fields"><a class="info" href="javascript:;">'.__("Separated with commas","piereg").' (i.e. jpg, gif, png, pdf)</a></div><div clss="advance_fields"><label for="desc_%d%">'.__("Description","piereg").'</label><textarea name="field[%d%][desc]" id="desc_%d%" rows="8" cols="16"></textarea></div><div class="advance_fields"><label>'.__("Rules","piereg").'</label><input name="field[%d%][required]" id="required_%d%" value="%d%" type="checkbox" class="checkbox_fields"><label for="required_%d%" class="required">'.__("Required","piereg").'</label></div><div class="advance_fields"><label for="validation_message_%d%">'.__("Validation Message","piereg").'</label><input type="text" name="field[%d%][validation_message]" id="validation_message_%d%" class="input_fields"></div><div class="advance_fields"><label for="css_%d%">'.__("CSS Class Name","piereg").'</label><input type="text" name="field[%d%][css]" id="css_%d%" class="input_fields"></div><div class="advance_fields"><label for="show_in_profile_%d%">'.__("Show in Profile","piereg").'</label><select class="show_in_profile" name="field[%d%][show_in_profile]" id="show_in_profile_%d%"  class="checkbox_fields"><option value="1" selected="selected">'.__("Yes","piereg").'</option><option value="0">'.__("No","piereg").'</option></select></div></div></div>';
		
		$structure['profile_pic'] = '<div class="fields_main"><div class="advance_options_fields"><input type="hidden" class="input_fields" name="field[%d%][type]"><input type="hidden" id="default_profile_pic"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" id="label_%d%" class="input_fields field_label"></div><div clss="advance_fields"><label for="desc_%d%">'.__("Description","piereg").'</label><textarea name="field[%d%][desc]" id="desc_%d%" rows="8" cols="16"></textarea></div><div class="advance_fields"><label>'.__("Rules","piereg").'</label><input name="field[%d%][required]" id="required_%d%" value="%d%" type="checkbox" class="checkbox_fields"><label for="required_%d%" class="required">'.__("Required","piereg").'</label></div><div class="advance_fields"><label for="validation_message_%d%">'.__("Validation Message","piereg").'</label><input type="text" name="field[%d%][validation_message]" id="validation_message_%d%" class="input_fields"></div><div class="advance_fields"><label for="css_%d%">'.__("CSS Class Name","piereg").'</label><input type="text" name="field[%d%][css]" id="css_%d%" class="input_fields"></div><div class="advance_fields"><label for="show_in_profile_%d%">'.__("Show in Profile","piereg").'</label><select class="show_in_profile" name="field[%d%][show_in_profile]" id="show_in_profile_%d%"  class="checkbox_fields"><option value="1" selected="selected">'.__("Yes","piereg").'</option><option value="0">'.__("No","piereg").'</option></select></div></div></div>';
		
		$structure['address']	= '<div class="fields_main"><div class="advance_options_fields"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" id="label_%d%" class="input_fields field_label"></div><div class="advance_fields"> <label for="address_type_%d%">'.__("List Type","piereg").'</label><select class="address_type" name="field[%d%][address_type]" id="address_type_%d%"><option value="International">'.__("International","piereg").'</option><option value="United States">'.__("United States","piereg").'</option><option value="Canada">'.__("Canada","piereg").'</option></select></div><div id="default_country_div_%d%" class="advance_fields"> <label for="default_country_%d%">'.__("Default Country","piereg").'</label><select class="default_country" name="field[%d%][default_country]" id="default_country_%d%"><option value="" selected="selected"></option><option value="Afghanistan">'.__("Afghanistan","piereg").'</option><option value="Albania">'.__("Albania","piereg").'</option><option value="Algeria">'.__("Algeria","piereg").'</option><option value="American Samoa">'.__("American Samoa","piereg").'</option><option value="Andorra">'.__("Andorra","piereg").'</option><option value="Angola">'.__("Angola","piereg").'</option><option value="Antigua and Barbuda">'.__("Antigua and Barbuda","piereg").'</option><option value="Argentina">'.__("Argentina","piereg").'</option><option value="Armenia">'.__("Armenia","piereg").'</option><option value="Australia">'.__("Australia","piereg").'</option><option value="Austria">'.__("Austria","piereg").'</option><option value="Azerbaijan">'.__("Azerbaijan","piereg").'</option><option value="Bahamas">'.__("Bahamas","piereg").'</option><option value="Bahrain">'.__("Bahrain","piereg").'</option><option value="Bangladesh">'.__("Bangladesh","piereg").'</option><option value="Barbados">'.__("Barbados","piereg").'</option><option value="Belarus">'.__("Belarus","piereg").'</option><option value="Belgium">'.__("Belgium","piereg").'</option><option value="Belize">'.__("Belize","piereg").'</option><option value="Benin">'.__("Benin","piereg").'</option><option value="Bermuda">'.__("Bermuda","piereg").'</option><option value="Bhutan">'.__("Bhutan","piereg").'</option><option value="Bolivia">'.__("Bolivia","piereg").'</option><option value="Bosnia and Herzegovina">'.__("Bosnia and Herzegovina","piereg").'</option><option value="Botswana">'.__("Botswana","piereg").'</option><option value="Brazil">'.__("Brazil","piereg").'</option><option value="Brunei">'.__("Brunei","piereg").'</option><option value="Bulgaria">'.__("Bulgaria","piereg").'</option><option value="Burkina Faso">'.__("Burkina Faso","piereg").'</option><option value="Burundi">'.__("Burundi","piereg").'</option><option value="Cambodia">'.__("Cambodia","piereg").'</option><option value="Cameroon">'.__("Cameroon","piereg").'</option><option value="Canada">'.__("Canada","piereg").'</option><option value="Cape Verde">'.__("Cape Verde","piereg").'</option><option value="Central African Republic">'.__("Central African Republic","piereg").'</option><option value="Chad">'.__("Chad","piereg").'</option><option value="Chile">'.__("Chile","piereg").'</option><option value="China">'.__("China","piereg").'</option><option value="Colombia">'.__("Colombia","piereg").'</option><option value="Comoros">'.__("Comoros","piereg").'</option><option value="Congo, Democratic Republic of the">'.__("Congo, Democratic Republic of the","piereg").'</option><option value="Congo, Republic of the">'.__("Congo, Republic of the","piereg").'</option><option value="Costa Rica">'.__("Costa Rica","piereg").'</option><option value="Côte d\'Ivoire">'.__("Côte d\'Ivoire","piereg").'</option><option value="Croatia">'.__("Croatia","piereg").'</option><option value="Cuba">'.__("Cuba","piereg").'</option><option value="Cyprus">'.__("Cyprus","piereg").'</option><option value="Czech Republic">'.__("Czech Republic","piereg").'</option><option value="Denmark">'.__("Denmark","piereg").'</option><option value="Djibouti">'.__("Djibouti","piereg").'</option><option value="Dominica">'.__("Dominica","piereg").'</option><option value="Dominican Republic">'.__("Dominican Republic","piereg").'</option><option value="East Timor">'.__("East Timor","piereg").'</option><option value="Ecuador">'.__("Ecuador","piereg").'</option><option value="Egypt">'.__("Egypt","piereg").'</option><option value="El Salvador">'.__("El Salvador","piereg").'</option><option value="Equatorial Guinea">'.__("Equatorial Guinea","piereg").'</option><option value="Eritrea">'.__("Eritrea","piereg").'</option><option value="Estonia">'.__("Estonia","piereg").'</option><option value="Ethiopia">'.__("Ethiopia","piereg").'</option><option value="Fiji">'.__("Fiji","piereg").'</option><option value="Finland">'.__("Finland","piereg").'</option><option value="France">'.__("France","piereg").'</option><option value="Gabon">'.__("Gabon","piereg").'</option><option value="Gambia">'.__("Gambia","piereg").'</option><option value="Georgia">'.__("Georgia","piereg").'</option><option value="Germany">'.__("Germany","piereg").'</option><option value="Ghana">'.__("Ghana","piereg").'</option><option value="Greece">'.__("Greece","piereg").'</option><option value="Greenland">'.__("Greenland","piereg").'</option><option value="Grenada">'.__("Grenada","piereg").'</option><option value="Guam">'.__("Guam","piereg").'</option><option value="Guatemala">'.__("Guatemala","piereg").'</option><option value="Guinea">'.__("Guinea","piereg").'</option><option value="Guinea-Bissau">'.__("Guinea-Bissau","piereg").'</option><option value="Guyana">'.__("Guyana","piereg").'</option><option value="Haiti">'.__("Haiti","piereg").'</option><option value="Honduras">'.__("Honduras","piereg").'</option><option value="Hong Kong">'.__("Hong Kong","piereg").'</option><option value="Hungary">'.__("Hungary","piereg").'</option><option value="Iceland">'.__("Iceland","piereg").'</option><option value="India">'.__("India","piereg").'</option><option value="Indonesia">'.__("Indonesia","piereg").'</option><option value="Iran">'.__("Iran","piereg").'</option><option value="Iraq">'.__("Iraq","piereg").'</option><option value="Ireland">'.__("Ireland","piereg").'</option><option value="Israel">'.__("Israel","piereg").'</option><option value="Italy">'.__("Italy","piereg").'</option><option value="Jamaica">'.__("Jamaica","piereg").'</option><option value="Japan">'.__("Japan","piereg").'</option><option value="Jordan">'.__("Jordan","piereg").'</option><option value="Kazakhstan">'.__("Kazakhstan","piereg").'</option><option value="Kenya">'.__("Kenya","piereg").'</option><option value="Kiribati">'.__("Kiribati","piereg").'</option><option value="North Korea">'.__("North Korea","piereg").'</option><option value="South Korea">'.__("South Korea","piereg").'</option><option value="Kuwait">'.__("Kuwait","piereg").'</option><option value="Kyrgyzstan">'.__("Kyrgyzstan","piereg").'</option><option value="Laos">'.__("Laos","piereg").'</option><option value="Latvia">'.__("Latvia","piereg").'</option><option value="Lebanon">'.__("Lebanon","piereg").'</option><option value="Lesotho">'.__("Lesotho","piereg").'</option><option value="Liberia">'.__("Liberia","piereg").'</option><option value="Libya">'.__("Libya","piereg").'</option><option value="Liechtenstein">'.__("Liechtenstein","piereg").'</option><option value="Lithuania">'.__("Lithuania","piereg").'</option><option value="Luxembourg">'.__("Luxembourg","piereg").'</option><option value="Macedonia">'.__("Macedonia","piereg").'</option><option value="Madagascar">'.__("Madagascar","piereg").'</option><option value="Malawi">'.__("Malawi","piereg").'</option><option value="Malaysia">'.__("Malaysia","piereg").'</option><option value="Maldives">'.__("Maldives","piereg").'</option><option value="Mali">'.__("Mali","piereg").'</option><option value="Malta">'.__("Malta","piereg").'</option><option value="Marshall Islands">'.__("Marshall Islands","piereg").'</option><option value="Mauritania">'.__("Mauritania","piereg").'</option><option value="Mauritius">'.__("Mauritius","piereg").'</option><option value="Mexico">'.__("Mexico","piereg").'</option><option value="Micronesia">'.__("Micronesia","piereg").'</option><option value="Moldova">'.__("Moldova","piereg").'</option><option value="Monaco">'.__("Monaco","piereg").'</option><option value="Mongolia">'.__("Mongolia","piereg").'</option><option value="Montenegro">'.__("Montenegro","piereg").'</option><option value="Morocco">'.__("Morocco","piereg").'</option><option value="Mozambique">'.__("Mozambique","piereg").'</option><option value="Myanmar">'.__("Myanmar","piereg").'</option><option value="Namibia">'.__("Namibia","piereg").'</option><option value="Nauru">'.__("Nauru","piereg").'</option><option value="Nepal">'.__("Nepal","piereg").'</option><option value="Netherlands">'.__("Netherlands","piereg").'</option><option value="New Zealand">'.__("New Zealand","piereg").'</option><option value="Nicaragua">'.__("Nicaragua","piereg").'</option><option value="Niger">'.__("Niger","piereg").'</option><option value="Nigeria">'.__("Nigeria","piereg").'</option><option value="Norway">'.__("Norway","piereg").'</option><option value="Northern Mariana Islands">'.__("Northern Mariana Islands","piereg").'</option><option value="Oman">'.__("Oman","piereg").'</option><option value="Pakistan">'.__("Pakistan","piereg").'</option><option value="Palau">'.__("Palau","piereg").'</option><option value="Palestine">'.__("Palestine","piereg").'</option><option value="Panama">'.__("Panama","piereg").'</option><option value="Papua New Guinea">'.__("Papua New Guinea","piereg").'</option><option value="Paraguay">'.__("Paraguay","piereg").'</option><option value="Peru">'.__("Peru","piereg").'</option><option value="Philippines">'.__("Philippines","piereg").'</option><option value="Poland">'.__("Poland","piereg").'</option><option value="Portugal">'.__("Portugal","piereg").'</option><option value="Puerto Rico">'.__("Puerto Rico","piereg").'</option><option value="Qatar">'.__("Qatar","piereg").'</option><option value="Romania">'.__("Romania","piereg").'</option><option value="Russia">'.__("Russia","piereg").'</option><option value="Rwanda">'.__("Rwanda","piereg").'</option><option value="Saint Kitts and Nevis">'.__("Saint Kitts and Nevis","piereg").'</option><option value="Saint Lucia">'.__("Saint Lucia","piereg").'</option><option value="Saint Vincent and the Grenadines">'.__("Saint Vincent and the Grenadines","piereg").'</option><option value="Samoa">'.__("Samoa","piereg").'</option><option value="San Marino">'.__("San Marino","piereg").'</option><option value="Sao Tome and Principe">'.__("Sao Tome and Principe","piereg").'</option><option value="Saudi Arabia">'.__("Saudi Arabia","piereg").'</option><option value="Senegal">'.__("Senegal","piereg").'</option><option value="Serbia and Montenegro">'.__("Serbia and Montenegro","piereg").'</option><option value="Seychelles">'.__("Seychelles","piereg").'</option><option value="Sierra Leone">'.__("Sierra Leone","piereg").'</option><option value="Singapore">'.__("Singapore","piereg").'</option><option value="Slovakia">'.__("Slovakia","piereg").'</option><option value="Slovenia">'.__("Slovenia","piereg").'</option><option value="Solomon Islands">'.__("Solomon Islands","piereg").'</option><option value="Somalia">'.__("Somalia","piereg").'</option><option value="South Africa">'.__("South Africa","piereg").'</option><option value="Spain">'.__("Spain","piereg").'</option><option value="Sri Lanka">'.__("Sri Lanka","piereg").'</option><option value="Sudan">'.__("Sudan","piereg").'</option><option value="Sudan, South">'.__("Sudan, South","piereg").'</option><option value="Suriname">'.__("Suriname","piereg").'</option><option value="Swaziland">'.__("Swaziland","piereg").'</option><option value="Sweden">'.__("Sweden","piereg").'</option><option value="Switzerland">'.__("Switzerland","piereg").'</option><option value="Syria">'.__("Syria","piereg").'</option><option value="Taiwan">'.__("Taiwan","piereg").'</option><option value="Tajikistan">'.__("Tajikistan","piereg").'</option><option value="Tanzania">'.__("Tanzania","piereg").'</option><option value="Thailand">'.__("Thailand","piereg").'</option><option value="Togo">'.__("Togo","piereg").'</option><option value="Tonga">'.__("Tonga","piereg").'</option><option value="Trinidad and Tobago">'.__("Trinidad and Tobago","piereg").'</option><option value="Tunisia">'.__("Tunisia","piereg").'</option><option value="Turkey">'.__("Turkey","piereg").'</option><option value="Turkmenistan">'.__("Turkmenistan","piereg").'</option><option value="Tuvalu">'.__("Tuvalu","piereg").'</option><option value="Uganda">'.__("Uganda","piereg").'</option><option value="Ukraine">'.__("Ukraine","piereg").'</option><option value="United Arab Emirates">'.__("United Arab Emirates","piereg").'</option><option value="United Kingdom">'.__("United Kingdom","piereg").'</option><option value="United States">'.__("United States","piereg").'</option><option value="Uruguay">'.__("Uruguay","piereg").'</option><option value="Uzbekistan">'.__("Uzbekistan","piereg").'</option><option value="Vanuatu">'.__("Vanuatu","piereg").'</option><option value="Vatican City">'.__("Vatican City","piereg").'</option><option value="Venezuela">'.__("Venezuela","piereg").'</option><option value="Vietnam">'.__("Vietnam","piereg").'</option><option value="Virgin Islands, British">'.__("Virgin Islands, British","piereg").'</option><option value="Virgin Islands, U.S.">'.__("Virgin Islands, U.S.","piereg").'</option><option value="Yemen">'.__("Yemen","piereg").'</option><option value="Zambia">'.__("Zambia","piereg").'</option><option value="Zimbabwe">'.__("Zimbabwe","piereg").'</option></select></div><div class="advance_fields"><label for="desc_%d%">'.__("Description","piereg").'</label><textarea name="field[%d%][desc]" id="desc_%d%" rows="8" cols="16"></textarea></div><div class="advance_fields"><label for="required_%d%">'.__("Rules","piereg").'</label><input name="field[%d%][required]" id="required_%d%" value="%d%" type="checkbox" class="checkbox_fields"><label for="required_%d%" class="required">'.__("Required","piereg").'</label></div><div class="advance_fields"><label for="hide_address2_%d%">'.__("Hide Address 2","piereg").'</label><input onChange="checkEvents(this,\'address_address2_%d%\')" name="field[%d%][hide_address2]" id="hide_address2_%d%" value="%d%" type="checkbox" class="checkbox_fields"><label for="hide_address2_%d%" class="required"></label></div><div class="advance_fields"><label for="hide_state_%d%">'.__("Hide State","piereg").'</label><input class="hide_state" name="field[%d%][hide_state]" id="hide_state_%d%" value="%d%" type="checkbox" class="checkbox_fields"><label for="hide_state_%d%" class="required"></label></div><div style="display:none;" id="default_state_div_%d%" class="advance_fields"><label for="default_state_%d%">'.__("Default State","piereg").'</label><select id="us_states_%d%" style="display:none;" class="default_state us_states_%d%" name="field[%d%][us_default_state]"><option value="" selected="selected"></option><option value="Alabama">'.__("Alabama","piereg").'</option><option value="Alaska">'.__("Alaska","piereg").'</option><option value="Arizona">'.__("Arizona","piereg").'</option><option value="Arkansas">'.__("Arkansas","piereg").'</option><option value="California">'.__("California","piereg").'</option><option value="Colorado">'.__("Colorado","piereg").'</option><option value="Connecticut">'.__("Connecticut","piereg").'</option><option value="Delaware">'.__("Delaware","piereg").'</option><option value="District of Columbia">'.__("District of Columbia","piereg").'</option><option value="Florida">'.__("Florida","piereg").'</option><option value="Georgia">'.__("Georgia","piereg").'</option><option value="Hawaii">'.__("Hawaii","piereg").'</option><option value="Idaho">'.__("Idaho","piereg").'</option><option value="Illinois">'.__("Illinois","piereg").'</option><option value="Indiana">'.__("Indiana","piereg").'</option><option value="Iowa">'.__("Iowa","piereg").'</option><option value="Kansas">'.__("Kansas","piereg").'</option><option value="Kentucky">'.__("Kentucky","piereg").'</option><option value="Louisiana">'.__("Louisiana","piereg").'</option><option value="Maine">'.__("Maine","piereg").'</option><option value="Maryland">'.__("Maryland","piereg").'</option><option value="Massachusetts">'.__("Massachusetts","piereg").'</option><option value="Michigan">'.__("Michigan","piereg").'</option><option value="Minnesota">'.__("Minnesota","piereg").'</option><option value="Mississippi">'.__("Mississippi","piereg").'</option><option value="Missouri">'.__("Missouri","piereg").'</option><option value="Montana">'.__("Montana","piereg").'</option><option value="Nebraska">'.__("Nebraska","piereg").'</option><option value="Nevada">'.__("Nevada","piereg").'</option><option value="New Hampshire">'.__("New Hampshire","piereg").'</option><option value="New Jersey">'.__("New Jersey","piereg").'</option><option value="New Mexico">'.__("New Mexico","piereg").'</option><option value="New York">'.__("New York","piereg").'</option><option value="North Carolina">'.__("North Carolina","piereg").'</option><option value="North Dakota">'.__("North Dakota","piereg").'</option><option value="Ohio">'.__("Ohio","piereg").'</option><option value="Oklahoma">'.__("Oklahoma","piereg").'</option><option value="Oregon">'.__("Oregon","piereg").'</option><option value="Pennsylvania">'.__("Pennsylvania","piereg").'</option><option value="Rhode Island">'.__("Rhode Island","piereg").'</option><option value="South Carolina">'.__("South Carolina","piereg").'</option><option value="South Dakota">'.__("South Dakota","piereg").'</option><option value="Tennessee">'.__("Tennessee","piereg").'</option><option value="Texas">'.__("Texas","piereg").'</option><option value="Utah">'.__("Utah","piereg").'</option><option value="Vermont">'.__("Vermont","piereg").'</option><option value="Virginia">'.__("Virginia","piereg").'</option><option value="Washington">'.__("Washington","piereg").'</option><option value="West Virginia">'.__("West Virginia","piereg").'</option><option value="Wisconsin">'.__("Wisconsin","piereg").'</option><option value="Wyoming">'.__("Wyoming","piereg").'</option><option value="Armed Forces Americas">'.__("Armed Forces Americas","piereg").'</option><option value="Armed Forces Europe">'.__("Armed Forces Europe","piereg").'</option><option value="Armed Forces Pacific">'.__("Armed Forces Pacific","piereg").'</option></select><select id="can_states_%d%" style="display:none;" class="default_state can_states_%d%" name="field[%d%][canada_default_state]"><option value="" selected="selected"></option><option value="Alberta">'.__("Alberta","piereg").'</option><option value="British Columbia">'.__("British Columbia","piereg").'</option><option value="Manitoba">'.__("Manitoba","piereg").'</option><option value="New Brunswick">'.__("New Brunswick","piereg").'</option><option value="Newfoundland &amp; Labrador">'.__("Newfoundland and Labrador","piereg").'</option><option value="Northwest Territories">'.__("Northwest Territories","piereg").'</option><option value="Nova Scotia">'.__("Nova Scotia","piereg").'</option><option value="Nunavut">'.__("Nunavut","piereg").'</option><option value="Ontario">'.__("Ontario","piereg").'</option><option value="Prince Edward Island">'.__("Prince Edward Island","piereg").'</option><option value="Quebec">'.__("Quebec","piereg").'</option><option value="Saskatchewan">'.__("Saskatchewan","piereg").'</option><option value="Yukon">'.__("Yukon","piereg").'</option></select></div><div class="advance_fields"><label for="validation_message_%d%">'.__("Validation Message","piereg").'</label><input type="text" name="field[%d%][validation_message]" id="validation_message_%d%" class="input_fields"></div><div class="advance_fields"><label for="css_%d%">'.__("CSS Class Name","piereg").'</label><input type="text" name="field[%d%][css]" id="css_%d%" class="input_fields"></div><div class="advance_fields"><label for="show_in_profile_%d%">'.__("Show in Profile","piereg").'</label><select class="show_in_profile" name="field[%d%][show_in_profile]" id="show_in_profile_%d%"  class="checkbox_fields"><option value="1" selected="selected">'.__("Yes","piereg").'</option><option value="0">'.__("No","piereg").'</option></select></div></div></div>';
			
			
			if( false )
			{
				// With Classic Recaptch which is deprecated since PR  ver 3.0 
				$structure['captcha']	= '<div class="fields_main"><div class="advance_options_fields"><input type="hidden" class="input_fields" name="field[%d%][type]"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" id="label_%d%" class="input_fields field_label"></div><div class="advance_fields piereg_recaptcha_skin"><label for="recaptcha_skin_%d%">'.__("Captcha Skin","piereg").'</label><select class="show_in_profile checkbox_fields" name="field[%d%][recaptcha_skin]" id="recaptcha_skin_%d%"><option value="red" selected="selected">'.__("Red","piereg").'</option><option value="white">'.__("White","piereg").'</option><option value="clean">'.__("Clean","piereg").'</option><option value="blackglass">'.__("Blackglass","piereg").'</option></select></div><div class="advance_fields piereg_recaptcha_type"><label for="recaptcha_type_%d%">'.__("Captcha Type","piereg").'</label><select name="field[%d%][recaptcha_type]" id="recaptcha_type_%d%"  class="show_in_profile checkbox_fields piereg_recaptcha_type"><option value="1" selected="selected">'.__("Classic ReCaptcha","piereg").'</option><option value="2">'.__("No Captcha ReCaptcha","piereg").'</option></select></div><div class="advance_fields piereg_recaptcha_note"><label><strong>'.__("Note","piereg").':</strong> '.__("Please make sure that Re-captcha keys are entered in Settings page").'.</label></div></div></div>';
			} 
			else 
			{
				$structure['captcha']	= '<div class="fields_main"><div class="advance_options_fields"><input type="hidden" class="input_fields" name="field[%d%][type]"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" id="label_%d%" class="input_fields field_label"></div><div class="advance_fields piereg_recaptcha_type"><input type="hidden" class="input_fields" name="field[%d%][recaptcha_type]" value="2"></div><div class="advance_fields piereg_recaptcha_note"><label><strong>'.__("Note","piereg").':</strong> '.__("Please make sure that Re-captcha keys are entered in Settings page").'.</label></div></div></div>';
			}
			
			$structure['math_captcha']	= '<div class="fields_main"><div class="advance_options_fields"><input type="hidden" value="1" name="field[%d%][required]"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" value="Math Captcha" id="label_%d%" class="input_fields field_label"></div><div class="advance_fields"><label for="desc_%d%">'.__("Description","piereg").'</label><textarea name="field[%d%][desc]" id="desc_%d%" rows="8" cols="16"></textarea></div><div class="advance_fields"><label for="placeholder_%d%">'.__("Placeholder","piereg").'</label><input type="text" name="field[%d%][placeholder]" id="placeholder_%d%" class="input_fields field_placeholder"></div><div class="advance_fields"><label for="validation_message_%d%">'.__("Validation Message","piereg").'</label><input type="text" name="field[%d%][validation_message]" id="validation_message_%d%" class="input_fields"></div><div class="advance_fields"><label for="css_%d%">'.__("CSS Class Name","piereg").'</label><input type="text" name="field[%d%][css]" id="css_%d%" class="input_fields"></div></div></div>';
			
			$structure['phone']	= '<div class="fields_main"><div class="advance_options_fields"><input type="hidden" class="input_fields" name="field[%d%][type]"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" id="label_%d%" class="input_fields field_label"></div><div class="advance_fields"><label for="desc_%d%">'.__("Description","piereg").'</label><textarea name="field[%d%][desc]" id="desc_%d%" rows="8" cols="16"></textarea></div><div class="advance_fields"><label for="required_%d%">'.__("Rules","piereg").'</label><input name="field[%d%][required]" id="required_%d%" value="%d%" type="checkbox" class="checkbox_fields"><label for="required_%d%" class="required">'.__("Required","piereg").'</label></div><div class="advance_fields"><label for="default_value_%d%">'.__("Default Value","piereg").'</label><input type="text" name="field[%d%][default_value]" id="default_value_%d%" class="input_fields field_default_value"></div><div class="advance_fields"> <label for="phone_format_%d%">'.__("Phone Format","piereg").'</label><select class="phone_format" name="field[%d%][phone_format]" id="phone_format_%d%"><option value="standard">'.__("USA Format","piereg").' (###) ###-####</option><option value="international">'.__("International","piereg").'</option></select></div><div class="advance_fields"><label for="validation_message_%d%">'.__("Validation Message","piereg").'</label><input type="text" name="field[%d%][validation_message]" id="validation_message_%d%" class="input_fields"></div><div class="advance_fields"><label for="css_%d%">'.__("CSS Class Name","piereg").'</label><input type="text" name="field[%d%][css]" id="css_%d%" class="input_fields"></div><div class="advance_fields"><label for="show_in_profile_%d%">'.__("Show in Profile","piereg").'</label><select class="show_in_profile" name="field[%d%][show_in_profile]" id="show_in_profile_%d%"  class="checkbox_fields"><option value="1" selected="selected">'.__("Yes","piereg").'</option><option value="0">'.__("No","piereg").'</option></select></div>';
			
			if( $this->piereg_pro_is_activate )
			{
				$structure['phone']	.= '<div class="advance_fields"><label>'.__("Enable Conditional Logic","piereg").'</label><select name="field[%d%][conditional_logic]" data-conditional_area="conditional_area_%d%" id="conditional_logic_%d%"  class="checkbox_fields enabel_conditional_logic"><option value="1">'.__("Yes","piereg").'</option><option value="0" selected="selected" >'.__("No","piereg").'</option></select></div><div class="advance_fields" id="conditional_area_%d%" style="display:none;"><label for="field_status_%d%"></label><select name="field[%d%][field_status]" id="field_status_%d%" class="field_status" style="width:auto;"><option value="1" selected="selected">'.__("Show","piereg").'</option><option value="0">'.__("Hide","piereg").'</option></select><span style="color:#fff;"> '.__("this field if","piereg").' </span><input type="hidden" name="field[%d%][selected_field]" id="selected_field_%d%" class="selected_field_input"><select data-selected_field="selected_field_%d%"  class="selected_field piereg_all_field_dropdown" style="width:100px;"></select><select class="field_rule_operator_select" id="field_rule_operator" name="field[%d%][field_rule_operator]" style="width:auto;"><option selected="selected" value="==">equal</option><option value="!=">not equal</option><option value="empty">empty</option><option value="not_empty">not empty</option><option value=">">greater than</option><option value="<">less than</option><option value="contains">contains</option><option value="starts_with">starts with</option><option value="ends_with">ends with</option><option value="range">range</option></select><div class="wrap_cond_value"><input type="text" name="field[%d%][conditional_value]" id="conditional_value_%d%" class="input_fields conditional_value_input" placeholder="Enter Value"></div></div>';
			}
			$structure['phone']	.= '</div></div>';
			
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			# [The function that control is_plugin_active is not loaded before code below. 20-04-2015]
			if( is_plugin_active("pie-register-twilio/pie-register-twilio.php") ){
				$structure['two_way_login_phone'] = '<div class="fields_main"><div class="advance_options_fields"><input type="hidden" value="international" class="input_fields" name="field[%d%][phone_format]"><input type="hidden" class="input_fields" name="field[%d%][type]"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" id="label_%d%" class="input_fields field_label" value="2Way Login Phone #"></div><div class="advance_fields"><label for="desc_%d%">'.__("Description","piereg").'</label><textarea name="field[%d%][desc]" id="desc_%d%" rows="8" cols="16">'.__("Please do not use +. \n e.g. 4155551212 (USA), 07400123456 (GB).","piereg").'</textarea></div><div class="advance_fields"><label for="required_%d%">'.__("Rules","piereg").'</label><input name="field[%d%][required]" id="required_%d%" value="%d%" type="checkbox" class="checkbox_fields"><label for="required_%d%" class="required">'.__("Required","piereg").'</label></div><div class="advance_fields"><label for="default_value_%d%">'.__("Default Value","piereg").'</label><input type="text" name="field[%d%][default_value]" id="default_value_%d%" class="input_fields field_default_value"></div><!--<div class="advance_fields"> <label for="phone_format_%d%">'.__("Phone Format","piereg").'</label><select class="phone_format" name="field[%d%][phone_format]" id="phone_format_%d%"><option value="standard">'.__("USA Format","piereg").' (###) ###-####</option><option value="international">'.__("International","piereg").'</option></select></div>--><div class="advance_fields"><label for="validation_message_%d%">'.__("Validation Message","piereg").'</label><input type="text" name="field[%d%][validation_message]" id="validation_message_%d%" class="input_fields"></div><div class="advance_fields"><label for="css_%d%">'.__("CSS Class Name","piereg").'</label><input type="text" name="field[%d%][css]" id="css_%d%" class="input_fields"></div><div class="advance_fields"><label for="show_in_profile_%d%">'.__("Show in Profile","piereg").'</label><select class="show_in_profile" name="field[%d%][show_in_profile]" id="show_in_profile_%d%"  class="checkbox_fields"><option value="1" selected="selected">'.__("Yes","piereg").'</option><option value="0">'.__("No","piereg").'</option></select></div>';
				if( $this->piereg_pro_is_activate )
				{
					$structure['two_way_login_phone'] .= '<div class="advance_fields"><label>'.__("Enable Conditional Logic","piereg").'</label><select name="field[%d%][conditional_logic]" data-conditional_area="conditional_area_%d%" id="conditional_logic_%d%"  class="checkbox_fields enabel_conditional_logic"><option value="1">'.__("Yes","piereg").'</option><option value="0" selected="selected" >'.__("No","piereg").'</option></select></div><div class="advance_fields" id="conditional_area_%d%" style="display:none;"><label for="field_status_%d%"></label><select class="field_status" name="field[%d%][field_status]" id="field_status_%d%" style="width:auto;"><option value="1" selected="selected">'.__("Show","piereg").'</option><option value="0">'.__("Hide","piereg").'</option></select><span style="color:#fff;"> '.__("this field if","piereg").' </span><input type="hidden" name="field[%d%][selected_field]" id="selected_field_%d%" class="selected_field_input"><select class="selected_field piereg_all_field_dropdown" data-selected_field="selected_field_%d%" style="width:100px;"></select><select class="field_rule_operator_select" id="field_rule_operator" name="field[%d%][field_rule_operator]" style="width:auto;"><option selected="selected" value="==">equal</option><option value="!=">not equal</option><option value="empty">empty</option><option value="not_empty">not empty</option><option value=">">greater than</option><option value="<">less than</option><option value="contains">contains</option><option value="starts_with">starts with</option><option value="ends_with">ends with</option><option value="range">range</option></select><div class="wrap_cond_value"><input type="text" name="field[%d%][conditional_value]" id="conditional_value_%d%" class="input_fields conditional_value_input" placeholder="Enter Value"></div><input type="hidden" id="default_two_way_login_phone"></div>';	
					
				}
				
				$structure['two_way_login_phone'] .= '</div></div>';
			}
			
			$structure['date']	= '<div class="fields_main"><div class="advance_options_fields"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" id="label_%d%" class="input_fields field_label"></div><div class="advance_fields"><label for="desc_%d%">'.__("Description","piereg").'</label><textarea name="field[%d%][desc]" id="desc_%d%" rows="8" cols="16"></textarea></div><div class="advance_fields"><label for="required_%d%">'.__("Rules","piereg").'</label><input name="field[%d%][required]" id="required_%d%" value="%d%" type="checkbox" class="checkbox_fields"><label for="required_%d%" class="required">'.__("Required","piereg").'</label></div><div class="advance_fields"> <label for="date_type_%d%">'.__("Date Format","piereg").'</label><select class="date_format" name="field[%d%][date_format]" id="date_format_%d%"><option value="mm/dd/yy">mm/dd/yy</option><option value="dd/mm/yy">dd/mm/yy</option><option value="dd-mm-yy">dd-mm-yy</option><option value="dd.mm.yy">dd.mm.yy</option><option value="yy/mm/dd">yy/mm/dd</option><option value="yy.mm.dd">yy.mm.dd</option></select></div><div class="advance_fields"> <label for="date_type_%d%">'.__("Date Input Type","piereg").'</label><select class="date_type" name="field[%d%][date_type]" id="date_type_%d%"><option value="datefield">'.__("Date Field","piereg").'</option><option value="datepicker">'.__("Date Picker","piereg").'</option><option value="datedropdown">'.__("Date Drop Down","piereg").'</option></select></div><div style="display:none;" id="icon_div_%d%" class="advance_fields"> <label for="date_type_%d%">&nbsp;</label><div class="calendar_icon_type"><input class="calendar_icon" type="radio" id="calendar_icon_%d%_none" name="field[%d%][calendar_icon]" value="none" checked="checked"><label for="calendar_icon_%d%_none"> '.__("No Icon","piereg").' </label>&nbsp;&nbsp;<input class="calendar_icon" type="radio" id="calendar_icon_%d%_calendar" name="field[%d%][calendar_icon]" value="calendar"><label for="calendar_icon_%d%_calendar"> '.__("Calendar Icon","piereg").' </label>&nbsp;&nbsp;<input class="calendar_icon" type="radio" id="calendar_icon_%d%_custom" name="field[%d%][calendar_icon]" value="custom"><label for="calendar_icon_%d%_custom"> '.__("Custom Icon","piereg").' </label></div><div id="icon_url_container_%d%" style="display: none;float:left;clear: both;">  <label for="cfield_calendar_icon_%d%_url"> '.__("Image URL","piereg").': </label>  <input type="text" class="input_fields" name="field[%d%][calendar_icon_url]" id="cfield_calendar_icon_%d%_url"></div></div><div class="advance_fields"><label for="validation_message_%d%">'.__("Validation Message","piereg").'</label><input type="text" name="field[%d%][validation_message]" id="validation_message_%d%" class="input_fields"></div><div class="advance_fields"><label for="css_%d%">'.__("CSS Class Name","piereg").'</label><input type="text" name="field[%d%][css]" id="css_%d%" class="input_fields"></div><div class="advance_fields"><label for="show_in_profile_%d%">'.__("Show in Profile","piereg").'</label><select class="show_in_profile checkbox_fields" name="field[%d%][show_in_profile]" id="show_in_profile_%d%"><option value="1" selected="selected">'.__("Yes","piereg").'</option><option value="0">'.__("No","piereg").'</option></select></div></div></div>';
			
			$structure['list'] = '<div class="fields_main"><div class="advance_options_fields"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" id="label_%d%" class="input_fields field_label"></div><div class="advance_fields"><label for="desc_%d%">'.__("Description","piereg").'</label><textarea name="field[%d%][desc]" id="desc_%d%" rows="8" cols="16"></textarea></div><div class="advance_fields"><label for="required_%d%">'.__("Rules","piereg").'</label><input name="field[%d%][required]" id="required_%d%" value="%d%" type="checkbox" class="checkbox_fields"><label for="required_%d%" class="required">'.__("Required","piereg").'</label></div><div class="advance_fields"><label for="rows_%d%">'.__("Rows","piereg").'</label><input type="text" value="1" name="field[%d%][rows]" id="rows_%d%" class="input_fields character_fields list_rows numeric greaterzero"></div><div class="advance_fields"><label for="cols_%d%">'.__("Columns","piereg").'</label><input type="text" value="1" name="field[%d%][cols]" id="cols_%d%" class="input_fields character_fields list_cols numeric greaterzero"></div><div class="advance_fields"><label for="validation_message_%d%">'.__("Validation Message","piereg").'</label><input type="text" name="field[%d%][validation_message]" id="validation_message_%d%" class="input_fields"></div><div class="advance_fields"><label for="css_%d%">'.__("CSS Class Name","piereg").'</label><input type="text" name="field[%d%][css]" id="css_%d%" class="input_fields"></div><div class="advance_fields"><label for="show_in_profile_%d%">'.__("Show in Profile","piereg").'</label><select name="field[%d%][show_in_profile]" id="show_in_profile_%d%"  class="show_in_profile checkbox_fields"><option value="1" selected="selected">'.__("Yes","piereg").'</option><option value="0">'.__("No","piereg").'</option></select></div></div></div>';

			$structure['hidden'] = '<div class="fields_main"><div class="advance_options_fields"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" id="label_%d%" class="input_fields field_label"></div><div class="advance_fields"><label for="default_value_%d%">'.__("Default Value","piereg").'</label><input type="text" name="field[%d%][default_value]" id="default_value_%d%" class="input_fields field_default_value"></div></div></div>';
			
			$structure['invitation'] = '<div class="fields_main"><div class="advance_options_fields"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" id="label_%d%" class="input_fields field_label"></div><div class="advance_fields"><label for="desc_%d%">'.__("Description","piereg").'</label><textarea name="field[%d%][desc]" id="desc_%d%" rows="8" cols="16"></textarea></div><div class="advance_fields"><label for="required_%d%">'.__("Rules","piereg").'</label><input name="field[%d%][required]" id="required_%d%" value="%d%" type="checkbox" class="checkbox_fields"><label for="required_%d%" class="required">'.__("Required","piereg").'</label></div><div class="advance_fields"><label for="placeholder_%d%">'.__("Placeholder","piereg").'</label><input type="text" name="field[%d%][placeholder]" id="placeholder_%d%" class="input_fields field_placeholder"></div><div class="advance_fields"><label for="validation_message_%d%">'.__("Validation Message","piereg").'</label><input type="text" name="field[%d%][validation_message]" id="validation_message_%d%" class="input_fields"></div><div class="advance_fields"><label for="css_%d%">'.__("CSS Class Name","piereg").'</label><input type="text" name="field[%d%][css]" id="css_%d%" class="input_fields"></div>';
			
			if( $this->piereg_pro_is_activate )
			{
				$structure['invitation'] .= '<div class="advance_fields"><label>'.__("Enable Conditional Logic","piereg").'</label><select name="field[%d%][conditional_logic]" data-conditional_area="conditional_area_%d%" id="conditional_logic_%d%"  class="checkbox_fields enabel_conditional_logic"><option value="1">'.__("Yes","piereg").'</option><option value="0" selected="selected" >'.__("No","piereg").'</option></select></div><div class="advance_fields" id="conditional_area_%d%" style="display:none;"><label for="field_status_%d%"></label><select name="field[%d%][field_status]" id="field_status_%d%"  class="field_status" style="width:auto;"><option value="1" selected="selected">'.__("Show","piereg").'</option><option value="0">'.__("Hide","piereg").'</option></select><span style="color:#fff;"> '.__("this field if","piereg").' </span><input type="hidden" name="field[%d%][selected_field]" id="selected_field_%d%" class="selected_field_input"><select data-selected_field="selected_field_%d%" class="selected_field piereg_all_field_dropdown" style="width:100px;"></select><select class="field_rule_operator_select" id="field_rule_operator" name="field[%d%][field_rule_operator]" style="width:auto;"><option selected="selected" value="==">equal</option><option value="!=">not equal</option><option value="empty">empty</option><option value="not_empty">not empty</option><option value=">">greater than</option><option value="<">less than</option><option value="contains">contains</option><option value="starts_with">starts with</option><option value="ends_with">ends with</option><option value="range">range</option></select><div class="wrap_cond_value"><input type="text" name="field[%d%][conditional_value]" id="conditional_value_%d%" class="input_fields conditional_value_input" placeholder="Enter Value"></div></div>';
			}
			$structure['invitation'] .= '<div class="advance_fields"><label for="show_in_profile_%d%">'.__("Show in Profile","piereg").'</label><select class="show_in_profile checkbox_fields" name="field[%d%][show_in_profile]" id="show_in_profile_%d%"><option value="1" selected="selected">'.__("Yes","piereg").'</option><option value="0">'.__("No","piereg").'</option></select></div></div></div>';
			
			global $wp_roles;
			$role = $wp_roles->roles;
			$user_role_option = "";
			$piereg_default_wp_usre_role = get_option("default_role");
			$piereg_selected_user_role = "";
			foreach($role as $key=>$value)
			{
				if($piereg_default_wp_usre_role == $key)
				{
					$piereg_selected_user_role = ' selected="selected" ';
				}
				$user_role_option .= '<option value="'.$key.'" '. $piereg_selected_user_role .' >'.$value['name'].'</option>';
				$piereg_selected_user_role = "";
			}
			
			$payment_gateways_html = "%payment_gateways_list_box%";
			
			$structure['pricing'] = '<div class="fields_main"><div class="advance_options_fields"><div class="advance_fields"><label for="label_%d%">'.__("Label","piereg").'</label><input type="text" name="field[%d%][label]" id="label_%d%" value="Membership" class="input_fields field_label"></div><div class="advance_fields"><label for="desc_%d%">'.__("Description","piereg").'</label><textarea name="field[%d%][desc]" id="desc_%d%" rows="8" cols="16"></textarea></div>';
			
			if( "not" == "allowed" ) {
			$structure['pricing'] .= '<div class="advance_fields dropdown_field_value sel_options_%d%"><div class="advance_fields dropdown_field_value"><label for="display_%d%" class="select_option_display">'.__("Display Value","piereg").'</label><input type="text" name="field[%d%][display][]" id="display_%d%" class="input_fields character_fields select_option_display"><label for="starting_price_%d%">'.__("Starting Price","piereg").'</label><input type="text" name="field[%d%][starting_price][]" id="starting_price_%d%" class="input_fields character_fields select_option_starting_price"><label for="for_%d%">'.__("For","piereg").'</label><input type="text" name="field[%d%][for][]" id="for_%d%" class="input_fields character_fields select_option_for"><select class="input_fields character_fields_mon select_option_for_period" name="field[%d%][for_period][]" id="for_period_%d%" ><option value="days">'.__("Days","piereg").'</option><option value="weeks">'.__("Weeks","piereg").'</option><option value="months">'.__("Months","piereg").'</option></select></div><div class="advance_fields dropdown_field_value"><label class="select_option_then_price" for="then_price_%d%">'.__("Then Price","piereg").'</label><input type="text" name="field[%d%][then_price][]" id="then_price_%d%" class="input_fields character_fields select_option_then_price"><label for="activation_cycle_%d%">'.__("Activation Cycle","piereg").'</label><select class="input_fields character_fields_sec select_option_activation_cycle" name="field[%d%][activation_cycle][]" id="activation_cycle_%d%" ><option value="-1">'.__("Use Default","piereg").'</option><option value="0">'.__("One Time","piereg").'</option><option value="7">'.__("Weekly","piereg").'</option><option value="30">'.__("Monthly","piereg").'</option><option value="182">'.__("Half Yearly","piereg").'</option><option value="273">'.__("Quarterly","piereg").'</option><option value="365">'.__("Yearly","piereg").'</option></select><label for="role_%d%">'.__("Role","piereg").'</label><select class="input_fields character_fields_sec select_option_role" name="field[%d%][role][]" id="role_%d%" >'.$user_role_option.'</select><label>'.__("Checked","piereg").'</label><input type="radio" value="0" id="check_%d%" name="field[%d%][selected][]" class="select_option_checked"><a style="color:white" href="javascript:;" onClick="addPricingOptions(%d%,\'radio\',jQuery(this));">+</a></div></div>';
			} // until multiple payment gateways release
			
			$structure['pricing'] .= '<div class="advance_fields"><label>'.__("Allow Payment Gateways","piereg").'</label>'.$payment_gateways_html.'</div><div class="advance_fields"><label for="validation_message_%d%">'.__("Validation Message","piereg").'</label><input type="text" name="field[%d%][validation_message]" id="validation_message_%d%" class="input_fields"></div><div class="advance_fields"><label for="css_%d%">'.__("CSS Class Name","piereg").'</label><input type="text" name="field[%d%][css]" id="css_%d%" class="input_fields"></div><div class="advance_fields"><label for="field_as_%d%">'.__("Field as","piereg").'</label><select class="show_in_profile piereg_field_as" name="field[%d%][field_as]" id="field_as_%d%"><option value="1">'.__("Dropdown","piereg").'</option><option value="0">'.__("Radio Button","piereg").'</option></select></div>';
			
			if( $this->piereg_pro_is_activate && "not" == "allowed" )
			{
				$structure['pricing'] .= '<div class="advance_fields"><label>'.__("Enable Conditional Logic","piereg").'</label><select name="field[%d%][conditional_logic]" data-conditional_area="conditional_area_%d%" id="conditional_logic_%d%"  class="enabel_conditional_logic"><option value="1">'.__("Yes","piereg").'</option><option value="0" selected="selected" >'.__("No","piereg").'</option></select></div><div class="advance_fields" id="conditional_area_%d%" style="display:none;"><label for="field_status_%d%"></label><select name="field[%d%][field_status]" id="field_status_%d%" class="field_status" style="width:auto;"><option value="1" selected="selected">'.__("Show","piereg").'</option><option value="0">'.__("Hide","piereg").'</option></select><span style="color:#fff;"> '.__("this field if","piereg").' </span><input type="hidden" name="field[%d%][selected_field]" id="selected_field_%d%" class="selected_field_input"><select data-selected_field="selected_field_%d%" class="selected_field piereg_all_field_dropdown" style="width:100px;"></select><select class="field_rule_operator_select" id="field_rule_operator" name="field[%d%][field_rule_operator]" style="width:auto;"><option selected="selected" value="==">equal</option><option value="!=">not equal</option><option value="empty">empty</option><option value="not_empty">not empty</option><option value=">">greater than</option><option value="<">less than</option><option value="contains">contains</option><option value="starts_with">starts with</option><option value="ends_with">ends with</option><option value="range">range</option></select><div class="wrap_cond_value"><input type="text" name="field[%d%][conditional_value]" id="conditional_value_%d%" class="input_fields conditional_value_input" placeholder="Enter Value"></div></div>';
			}
			$structure['pricing'] .= '</div></div>';
			
			return $structure;
		}
		function payment_gateways_list(){
			$pie_reg = get_option(OPTION_PIE_REGISTER);
			$payment_gateways_name_list = array();
			/* For Authorize.Net*/
			if ( (isset($pie_reg['enable_authorize_net'],$pie_reg['piereg_authorize_net_api_id']) && $pie_reg['enable_authorize_net'] == 1 and trim($pie_reg['piereg_authorize_net_api_id'])	 != "") ){
				$payment_gateways_name_list["authorizeNet"] = "Authorize.Net";
			}
			/*For 2CheckOut*/
			if( (isset($pie_reg['enable_2checkout'],$pie_reg['piereg_2checkout_api_id']) && $pie_reg['enable_2checkout'] == 1 and trim($pie_reg['piereg_2checkout_api_id']) != "") ){
				$payment_gateways_name_list["2checkout"] = "2CheckOut";
			}
			/*For Paypal (Pro)*/
			if( (isset($pie_reg['enable_PaypalPro'],$pie_reg['PaypalPro_username']) && $pie_reg['enable_PaypalPro'] == 1 and trim($pie_reg['PaypalPro_username']) != "") ){
				$payment_gateways_name_list["PaypalPro"] = "Paypal (Pro)";
			}
			/*For Paypal (Exp)*/
			if( (isset($pie_reg['enable_PaypalExp'],$pie_reg['PaypalExp_username']) && $pie_reg['enable_PaypalExp'] == 1 and trim($pie_reg['PaypalExp_username']) != "") ){
				$payment_gateways_name_list["PaypalExp"] = "Paypal (Exp)";
			}
			/*For Paypal (Standard)*/
			if( (isset($pie_reg['enable_paypal'],$pie_reg['paypal_butt_id']) && $pie_reg['enable_paypal'] == 1 and trim($pie_reg['paypal_butt_id']) != "") ){
				$payment_gateways_name_list["PaypalStandard"] = "Paypal (Standard)";
			}
			return $payment_gateways_name_list;
		}
		function deactivation_settings(){
			global $wpdb;
			$option = get_option(OPTION_PIE_REGISTER);
			do_action( 'pie_deactivation_base', $option );
			
			//$this->uninstall_settings();
		}
		function uninstall_settings()
		{
			do_action( 'pie_uninstall_base' );
			
			global $wpdb;
			$pie_pages = get_option("pie_pages",$pie_pages);
			
			if(is_array($pie_pages )) {
				foreach ($pie_pages as $page) {
					wp_delete_post($page, true);	
				}
			}
			
			$form_id = get_option("piereg_form_fields_id");			
			if(!empty($form_id))
			{
				for($a=1; $a<=$form_id; $a++)
				{
					delete_option("piereg_form_fields_".$a);
					delete_option("piereg_form_field_option_".$a);
				}
			}
			
			$form_on_free	= get_option("piereg_form_free_id");			
			if( !empty($form_id) && !$form_on_free )
			{
				for( $a=1; $a<=$form_on_free; $a++ )
				{
					delete_option("piereg_form_fields_".$a);
					delete_option("piereg_form_field_option_".$a);
				}
			}
			
			
			delete_option("piereg_form_fields_id");
			delete_option('piereg_form_free_id'); 
						
			delete_option('piereg_form_pricing_fields');
			delete_option('widget_pie_login_widget');
						
			delete_option('piereg_math_cpatcha_enable');
			delete_option('piereg_plugin_db_version');
			delete_option('pie_can_states');
			delete_option('pie_countries');
			delete_option('piereg_currency');
			delete_option('pie_fields');
			delete_option('pie_fields_default');
			delete_option('pie_fields_meta');
			delete_option('pie_register_2');
			delete_option('pie_register');
			delete_option('pie-register');
			delete_option('pie_register_2_active');
			delete_option('pie_register_2_key');
			delete_option('pieregister_restrict_widgets');
			delete_option('pieregister_stats_option');
			delete_option('pie_user_email_types');
			delete_option('pie_us_states');
			delete_option('pie_pages');
			
			$codetable = $wpdb->prefix."pieregister_code";
			if(!$wpdb->query("DROP TABLE IF EXISTS `".$codetable."`")){
				$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
			}
			$redirect_settings_table_name = $wpdb->prefix."pieregister_redirect_settings";
			if(!$wpdb->query("DROP TABLE IF EXISTS `".$redirect_settings_table_name."`")){
				$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
			}
			$lockdowns_table_name = $wpdb->prefix."pieregister_lockdowns";
			if(!$wpdb->query("DROP TABLE IF EXISTS `".$lockdowns_table_name."`")){
				$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
			}
			$this->uninstall_pieregister_license_key();
		}
		function uninstall_pieregister_license_key(){
			global $wpdb, $blog_id;

			$this->license_key_deactivation();
	
			// Remove options
			if ( is_multisite() ) {
	
				switch_to_blog( $blog_id );
	
				foreach ( array(
						'api_manager_example',
						'piereg_api_manager_product_id',
						'piereg_api_manager_instance',
						'api_manager_example_deactivate_checkbox',
						'piereg_api_manager_activated',
						'bf_version'
						) as $option) {
							delete_option( $option );
						}
	
				restore_current_blog();
	
			} else {
				foreach ( array(
						'api_manager_example',
						'piereg_api_manager_product_id',
						'piereg_api_manager_instance',
						'api_manager_example_deactivate_checkbox',
						'piereg_api_manager_activated'
						) as $option) {
							delete_option( $option );
						}
			}
		}
		/*
		 * Deactivates the license on the API server
		 * @return void
		*/
		public function license_key_deactivation() {
	
			$piereg_api_manager_key_class = new Api_Manager_Example_Key();
			$activation_status = get_option( 'piereg_api_manager_activated' );
			$default_options = get_option( 'api_manager_example' );	
			$api_email = $default_options['activation_email'];
			$api_key = $default_options['api_key'];
	
			$args = array(
				'email' => $api_email,
				'licence_key' => $api_key,
				);
	
			if ( $activation_status == 'Activated' && $api_key != '' && $api_email != '' ) {
				$piereg_api_manager_key_class->deactivate( $args ); // reset license key activation
			}
		}
		/**
		 * Check for software updates
		 */
		public function load_plugin_self_updater() {
			$options = get_option( 'api_manager_example' );
			$upgrade_url = $this->upgrade_url; // URL to access the Update API Manager.
			$plugin_name = untrailingslashit( plugin_basename( __FILE__ ) ); // same as plugin slug. if a theme use a theme name like 'twentyeleven'
			$product_id = get_option( 'piereg_api_manager_product_id' ); // Software Title
			$api_key = $options['api_key']; // API License Key
			$activation_email = $options['activation_email']; // License Email
			$renew_license_url = 'http://store.genetech.co/my-account/'; // URL to renew a license
			$instance = get_option( 'piereg_api_manager_instance' ); // Instance ID (unique to each blog activation)
			$domain = site_url(); // blog domain name
			$software_version = get_option( $this->piereg_api_manager_version_name ); // The software version
			$plugin_or_theme = 'plugin'; // 'theme' or 'plugin'
			
			// $this->piereg_text_domain is used to defined localization for translation
			new API_Manager_Example_Update_API_Check( $upgrade_url, $plugin_name, $product_id, $api_key, $activation_email, $renew_license_url, $instance, $domain, $software_version, $plugin_or_theme, $this->piereg_text_domain );
		}
		
		function getMetaKey($text)
		{
			return str_replace("-","_",sanitize_title($text));	
		}
		function filterEmail($text,$user,$user_pass="",$password_reset_key=false,$extra_variables = array())
		{
			if(!is_object($user))
			{
				
				if ( username_exists( $user ) ){
					$user = get_user_by('login', $user);
				}
				// Then, by e-mail address
				elseif( email_exists($user) ){
					$user = get_user_by('email', $user);
				}
				// Then, by user ID
				else{
					$user = new WP_User( intval($user) );
				}
			}
			if(!$user) return false;
			
			/*
				*	Define Variables
			*/
			$reset_email_key = "";//Reset Email Key
			$confirm_current_email_url = "";
			$user_last_date = "";//Add User Last Date
			
			/*
				*	Replace Array to Variables
			*/
			if(!empty($extra_variables))
				extract($extra_variables);
			
			$option = get_option(OPTION_PIE_REGISTER);
			
			$text					= $this->replaceMetaKeys($text,$user->ID);
			$user_login 			= stripslashes($user->data->user_login);
			$user_email 			= stripslashes($user->data->user_email);
			$blog_name 				= get_option("blogname"); 
			$site_url 				= get_option("siteurl");
			$blogname_url			= '<a href="'.get_option("siteurl").'">'.get_option("blogname").'</a>';
			$first_name				= get_user_meta( $user->ID, 'first_name' );
			$last_name				= get_user_meta( $user->ID, 'last_name' );
			$user_url				= $user->data->user_url;
			$user_aim				= get_user_meta( $user->ID, 'aim' );
			$user_yim				= get_user_meta( $user->ID, 'yim' );
			$user_jabber			= get_user_meta( $user->ID, 'jabber' );
			$user_biographical_nfo	= get_user_meta( $user->ID, 'description' );
			$invitation_code		= get_user_meta( $user->ID, 'invite_code' );
			$invitation_code		= (isset($invitation_code[0]) && is_array($invitation_code))? $invitation_code[0] : "";
			$user_ip				= $_SERVER['REMOTE_ADDR'];
			$hash 					= get_user_meta( $user->ID, 'hash', true );
			
			if(isset($hash))
				$activationurl = $this->pie_modify_custom_url($this->pie_login_url(),"action=activate").'&pie_id='.$user->data->user_login.'&activation_key='.((isset($hash))?$hash:"");
			else
				$activationurl = "";
			
			if($activationurl != ""){
				$activationurl			= '<a href="'.$activationurl.'" target="_blank">'.$activationurl.'</a>';
			}
			$all_field 				= $this->get_all_field($user->data->user_email);
			$user_registration_date = $user->data->user_registered;
			
			if($password_reset_key)
				$reset_password_url = $this->pie_modify_custom_url($this->pie_login_url(),"action=rp&key={$password_reset_key}&login={$user_login}");
			else
				$reset_password_url = "";
			
			if($reset_password_url != ""){
				$reset_password_url			= '<a href="'.$reset_password_url.'" target="_blank">'.$reset_password_url.'</a>';
			}	
			/*
				*	Add since 2.0.13
				*	User New Email
			*/
			$user_new_email = get_user_meta( $user->ID, 'new_email_address', true );
	
			/*
				*	Add since 2.0.13
				*	Email edit verification url
			*/
			$reset_email_url = "";
			if($reset_email_key)
				$reset_email_url = $this->get_page_uri($option['alternate_login'],"action=email_edit&key={$reset_email_key}&login={$user_login}");
			else
				$reset_email_url = "";
			if($reset_email_url != ""){
				$reset_email_url			= '<a href="'.$reset_email_url.'" target="_blank">'.$reset_email_url.'</a>';
			}	
			
			$confirm_current_email_url = "";
			if(isset($confirm_current_email_key))
				$confirm_current_email_url = $this->get_page_uri($option['alternate_login'],"action=current_email_verify&key={$confirm_current_email_key}&login={$user_login}");
			else
				$confirm_current_email_url = "";
			if($confirm_current_email_url != ""){
				$confirm_current_email_url			= '<a href="'.$confirm_current_email_url.'" target="_blank">'.$confirm_current_email_url.'</a>';
			}	
			
			/////////////// PAYMENT LINK ///////////
			$pending_payment_url = "";
			$register_type = get_user_meta($user->ID, 'register_type', true);
			if($register_type == "payment_verify"){
				$hash = md5( time() );
				update_user_meta( $user_id, 'hash', $hash );
				if($option['paypal_sandbox'] == "yes")
					$pending_payment_url = SSL_SAND_URL."?cmd=_s-xclick&hosted_button_id=".$option['paypal_butt_id']."&custom=".$hash."|".$user->ID."&bn=Genetech_SI_Custom";
				else
					$pending_payment_url = SSL_P_URL."?cmd=_s-xclick&hosted_button_id=".$option['paypal_butt_id']."&custom=".$hash."|".$user->ID."&bn=Genetech_SI_Custom";
				$pending_payment_url = '<a href="'.$pending_payment_url.'">'.$pending_payment_url.'</a>';
			}
			$user_pass = "********";
			//////////////////////////////////////
			$keys 	= array("%user_login%","%user_email%","%blogname%","%siteurl%","%activationurl%","%firstname%","%lastname%","%user_url%","%user_aim%","%user_yim%","%user_jabber%","%user_biographical_nfo%","%all_field%","%user_registration_date%","%reset_password_url%" ,"%invitation_code%","%pending_payment_url%","%blogname_url%","%user_ip%","%user_pass%","%user_new_email%","%reset_email_url%","%user_last_date%","%confirm_current_email_url%");
						
			$values = array($user_login ,$user_email,$blog_name, $site_url,$activationurl,$this->returnFormattedValue($first_name),$this->returnFormattedValue($last_name),$user_url,$this->returnFormattedValue($user_aim),$this->returnFormattedValue($user_yim),$this->returnFormattedValue($user_jabber),$this->returnFormattedValue($user_biographical_nfo), $all_field,$user_registration_date,$reset_password_url,$invitation_code,$pending_payment_url,$blogname_url,$user_ip,$user_pass,$user_new_email,$reset_email_url,$user_last_date,$confirm_current_email_url);
			
			$return_text = str_replace($keys,$values,$text);
			
				/////////////// CUSTOM FIELDS ///////////////
				$customfields = array();
				$user_form_id	= get_user_meta( $user->ID, 'user_registered_form_id', true);
				$form_fields	= unserialize( get_option("piereg_form_fields_" . $user_form_id ) );
				
				if( preg_match_all("'%pie_(.*?)%'si", $text, $customfields) )
				{
					
					
					foreach( $customfields[0] as $val )
					{
						$pie_field_slug			= str_replace( "%", "", $val );
						$pie_field_value		= get_user_meta( $user->ID, $pie_field_slug, true);
						$_type					= explode("_", $pie_field_slug);
						$_types_multi_val		= array('radio','checkbox','multiselect','dropdown');
						
						
						if( is_array($pie_field_value) || in_array( $_type[1], $_types_multi_val )  )
						{
														
							if( in_array( $_type[1], $_types_multi_val ) )
							{
								$field_data		= $form_fields[$_type[2]];
								
								if(is_array($field_data['value']))							
									$combined_array	= array_combine($field_data['value'], $field_data['display']);
								
								$corrected_value 	= array();
								
								for($a = 0 ; $a < sizeof($pie_field_value) ; $a++ )
								{
									if(isset($pie_field_value[$a]))
										$corrected_value[$a] = $combined_array[$pie_field_value[$a]];
								}
								
								$pie_field_value = implode(", ",$corrected_value);
				
								//var_dump($pie_field_value);
								//print_r($form_fields[$_type[2]]);
							}
							else 
							{
								$_params			= array(
															"_type" 	=> $_type[1],
															"_value" 	=> $pie_field_value
														);
								
								$pie_field_value	= $this->getValue( true, $_params );							
								#$pie_field_value 	= implode(", ", $pie_field_value);							
							}
							
						}
						
						$return_text 			= str_replace($val,$pie_field_value,$return_text);
					
					}
				}
			
			
			return $return_text;
		}
		
		function returnFormattedValue($array){
			if($array !== ''){
				if(is_array($array) && isset($array[0])){
					return $array[0];
				}else if(!is_array($array)){
					return $array;
				}
			}
			return '';
		}
		
		function get_all_field($user)
		{
			if(!is_object($user))
			{
				global $wpdb;
				$user_table = $wpdb->prefix."users";
				$user = $wpdb->get_results( $wpdb->prepare("SELECT `ID`, `user_login`, `user_nicename`, `user_email`, `user_registered` FROM `".$user_table."` WHERE `user_email` = %s", stripslashes(esc_sql( $user ) )) );
				if(isset($wpdb->last_error) && !empty($wpdb->last_error)){
					$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
				}
				$user = $user[0];
			}
			if($user)
			{
				$val = "<table>";
					foreach($user as $key=>$value)
					{
						if($key != "ID")
						{
							$val .= "<tr>
										<td>".$this->chnge_case($key)."</td>
										<td>".$value."</td>
									</tr>";
						}
					}
				$val .= "</table>";
			}
			else{
				$val = "";
			}
			return $val;
		}
		function chnge_case($key = "")
		{
			return @ucwords(strtolower(str_replace("_"," ",$key)));
		}
		function createDropdown($options,$sel = "")
		{
			$html = "";
			if(is_array($options))
			{
				for($a = 0 ;$a < sizeof($options);$a++)
				{
					$selected = "";
					if(isset($sel) && is_array($sel)){
						if(in_array($options[$a],$sel)){
							$selected = 'selected="selected"';
						}
					}else{
						if($options[$a]==$sel)
							$selected = 'selected="selected"';
					}
					$html .= '<option '.$selected.' value="'.$options[$a].'">'.$options[$a].'</option>';	
				}
			}
			return $html;
		}
		function codeTable()
		{
			global $wpdb;		
			return $wpdb->prefix."pieregister_code";			
		}
		function warnings()
		{ //Show warning if plugin is installed on a WordPress lower than 3.2
			global $wp_version;			
			//VERSION CONTROL
			if( $wp_version < 3.5 )
			echo "<div id='piereg-warning' class='updated fade-ff0000'><p><strong>".__('Pie-Register is only compatible with WordPress v3.5 and up. You are currently using WordPress.', 'piereg').$wp_version.". ".__("The plugin may not work as expected.","piereg")."</strong> </p></div>";
			
		}
		public function check_enable_social_site_method()// only check any Social Site method enable or not.
		{
			$pie_reg = get_option(OPTION_PIE_REGISTER);
			if(
			(isset($pie_reg['piereg_enable_facebook']) and $pie_reg['piereg_enable_facebook'] == 1 and trim($pie_reg['piereg_facebook_app_id']) != "") or
			(isset($pie_reg['piereg_enable_linkedin']) and $pie_reg['piereg_enable_linkedin'] == 1 and trim($pie_reg['piereg_linkedin_app_id']) != "")	or
			(isset($pie_reg['piereg_enable_google']) and $pie_reg['piereg_enable_google'] 	  == 1 )	or
			(isset($pie_reg['piereg_enable_yahoo']) and $pie_reg['piereg_enable_yahoo'] 	  == 1 )	or
			(isset($pie_reg['piereg_enable_twitter']) and $pie_reg['piereg_enable_twitter']   == 1 and trim($pie_reg['piereg_twitter_app_id'] ) != "") or
			(isset($pie_reg['piereg_enable_wordpress']) and $pie_reg['piereg_enable_wordpress']   == 1 and trim($pie_reg['piereg_wordpress_app_id'] ) != "")
			  )
			{
				return "true";
			}
			else
			{
				return "false";
			}
			
		}
		public function check_enable_payment_method()// only check any payment method enable or not.
		{
			$pie_reg = get_option(OPTION_PIE_REGISTER);
			if(
			   	/* For Authorize.Net*/
				(isset($pie_reg['enable_authorize_net'],$pie_reg['piereg_authorize_net_api_id']) && $pie_reg['enable_authorize_net'] == 1 and trim($pie_reg['piereg_authorize_net_api_id'])	 != "") or 
				/*For 2CheckOut*/
				(isset($pie_reg['enable_2checkout'],$pie_reg['piereg_2checkout_api_id']) && $pie_reg['enable_2checkout'] == 1 and trim($pie_reg['piereg_2checkout_api_id']) != "") or 
				/*For Paypal (Pro)*/
				(isset($pie_reg['enable_PaypalPro'],$pie_reg['PaypalPro_username']) && $pie_reg['enable_PaypalPro'] == 1 and trim($pie_reg['PaypalPro_username']) != "") or 
				/*For Paypal (Exp)*/
				(isset($pie_reg['enable_PaypalExp'],$pie_reg['PaypalExp_username']) && $pie_reg['enable_PaypalExp'] == 1 and trim($pie_reg['PaypalExp_username']) != "") or 
				/*Skrill_Username*/
				(isset($pie_reg['enable_Skrill'],$pie_reg['Skrill_email']) && $pie_reg['enable_Skrill'] == 1 and trim($pie_reg['Skrill_email']) != "") or 
				/*For Paypal (Standard)*/
				(isset($pie_reg['enable_paypal'],$pie_reg['paypal_butt_id']) && $pie_reg['enable_paypal'] == 1 and trim($pie_reg['paypal_butt_id']) != "") 
			  )
			{
				return "true";
			}
			else
			{
				return "false";
			}
		}
		function check_plugin_activation()
		{
			if(
				is_plugin_active("pie-register-2checkout/pie-register-2checkout.php")								or
				is_plugin_active("pie-register-authorize_dot_net/pie-register-authorize_dot_net.php")				or
				is_plugin_active("pie-register-skrill/pie-register-skrill.php")										or
				is_plugin_active("pie-register-paypal_pro/pie-register-PaypalPro.php")								or
				is_plugin_active("pie-register-paypal_exp/pie-register-PaypalExp.php")
			  )
			{
				return "true";
			}
			else{
				return "false";
			}
		}
		function check_payment_plugin_activation(){
			return $this->check_plugin_activation();
		}
		
		function piereg_default_settings()
		{
			$pie_pages_id = get_option("pie_pages");
			$update = get_option(OPTION_PIE_REGISTER);
			//E-Mail TYpes
			$email_type = array(
								"default_template"							=> __("Your account is ready.","piereg"),
								"admin_verification"						=> __("Your account is being processed.","piereg"),
								"email_verification"						=> __("Email verification.","piereg"),
								"email_thankyou"							=> __("Your account has been activated.","piereg"),
								"pending_payment"							=> __("Overdue Payment.","piereg"),
								"payment_success"							=> __("Payment Processed.","piereg"),
								"payment_faild"								=> __("Payment Failed.","piereg"),
								"pending_payment_reminder"					=> __("Payment Pending.","piereg"),
								"email_verification_reminder"				=> __("Email Verification Reminder.","piereg"),
								"forgot_password_notification"				=> __("Password Reset Request.","piereg")
								);
			
			add_option("pie_user_email_types",$email_type);
			
			// Truncate redirect roles table
			global $wpdb;
			$redirect_settings_table_name = $wpdb->prefix."pieregister_redirect_settings";
			$redirect_settings_sql = "TRUNCATE TABLE `".$redirect_settings_table_name."` ";
			
			if(!$wpdb->query($redirect_settings_sql))
			{
				$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
			}
			
			$update = get_option(OPTION_PIE_REGISTER);
			
			$update["paypal_butt_id"] = "";
			$update["paypal_pdt"]     = "";
			$update["paypal_sandbox"] = "";
			$update["payment_success_msg"] 			= __("Payment was successful.","piereg");
			$update["payment_faild_msg"] 			= __("Payment failed.","piereg");
			$update["payment_renew_msg"] 			= __("Account needs to be activated.","piereg");
			$update["payment_already_activate_msg"] = __("Account is already active.","piereg");
			$update['enable_admin_notifications'] = 1;
			$update['enable_paypal'] = 0;
			$update['enable_blockedips'] = 0;
			$update['enable_blockedusername'] = 0;
			$update['enable_blockedemail'] = 0;
			$update['admin_sendto_email'] 	= get_option( 'admin_email' );				
			$update['admin_from_name'] 		= "Administrator";
			$update['admin_from_email'] 	= get_option( 'admin_email' );
			$update['admin_to_email'] 		= get_option( 'admin_email' );
			$update['admin_bcc_email'] 		= get_option( 'admin_email' );
			$update['admin_subject_email'] 	= __("New User Registration","piereg");
			$update['admin_message_email_formate'] 	= 1;
			$update['admin_message_email'] 	= '<p>Hello Admin,</p><p>A new user has been registered on your Website,. Details are given below:</p><p>Thanks</p><p>Team %blogname%</p>';
			
			
			//UX_Basic_settings
			$update['display_hints']					= 0;
			$update['login_username_label']				= 'Username';
			$update['login_username_placeholder']		= '';
			$update['login_password_label']				= 'Password';
			$update['login_password_placeholder']		= '';
			$update['forgot_pass_username_label']		= 'Username or Email:';
			$update['forgot_pass_username_placeholder']	= '';
			
			
			$update['redirect_user']			= 1;
			$update['subscriber_login']			= 0;
			$update['allow_pr_edit_wplogin']	= 0;
			$update['block_WP_profile']			= 0;
			$update['modify_avatars']			= 0;
			$update['show_admin_bar']			= 1;
			$update['block_wp_login']			= 1;
			$update['alternate_login']			= $pie_pages_id[0];
			$update['alternate_register']		= $pie_pages_id[1];
			$update['alternate_forgotpass']		= $pie_pages_id[2];
			$update['alternate_profilepage']	= $pie_pages_id[3];
			
			$update['after_login']				= -1;
			
			//Captcha_login_form
			$update['captcha_in_login_value']		= 0;
			$update['captcha_in_login_attempts']		= 0;
			$update['capthca_in_login_label']		= '';
			$update['capthca_in_login']		= '2';
			$update['piereg_security_attempts_login_value']		= '0';
			$update['security_attempts_login_time']		= '1';
			$update['security_attempts_login']		= '2';
			
			//Captcha_forgot_form
			$update['captcha_in_forgot_value']		= 0;
			$update['capthca_in_forgot_pass_label']		= '';
			$update['capthca_in_forgot_pass']		= '2';
			$update['piereg_security_attempts_forgot_value']		= '0';
			$update['security_attempts_forgot_time']		= '1';
			$update['security_attempts_forgot']		= '2';
			
			//security_attempts_login
			$update['security_captcha_attempts_login']	= 0;
			$update['security_captcha_login']	= 2;
			$update['security_attempts_login']	= 0;
			$update['security_attempts_login_time']	= 1;
			
			
			$update['alternate_login_url']		= '';
			
			$update['alternate_logout']			= -1;
			$update['alternate_logout_url']		= '';
			$update['login_after_register'] 	= 0;
			/* Bot Settings */
			$update['restrict_bot_enabel']		= 0;
			$update['restrict_bot_content']		= "bot\r\nia_archive\r\nslurp crawl\r\nspider\r\nYandex";
			$update['restrict_bot_content_message']		= "Restricted Post: You are not allowed to view the content of this Post";
			
			$update['outputhtml'] 				= 1;
			$update['outputcss'] 				= 1;
			
			$update['pass_strength_indicator_label']	= "Strength Indicator";
			$update['pass_very_weak_label']				= "Very weak";
			$update['pass_weak_label']					= "Weak";
			$update['pass_medium_label']				= "Medium";
			$update['pass_strong_label']				= "Strong";
			$update['pass_mismatch_label']				= "Mismatch";
			$update['pr_theme']							= "0";
		
			
			$update['outputjquery_ui'] 			= 1;
			$update['no_conflict']				= 0;
			$update['currency'] 				= "USD";
			$update['verification'] 			= 0;
			$update['email_edit_verification_step'] = 1;
			$update['grace_period'] 			= 0;
			$update['captcha_publc'] 			= "";
			$update['captcha_private'] 			= "";
			$update['paypal_button_id'] 		= "";
			$update['paypal_pdt_token'] 		= "";
			$update['custom_css'] 				= "";
			$update['tracking_code'] 			= "";
			$update['enable_invitation_codes'] 	= 0;
			$update['invitation_codes'] 		= "";
			$update['reg_form_submission_time_enable'] = "0";
			$update['reg_form_submission_time'] = "0";
			$update['custom_logo_url']			= "";
					
			$update['custom_logo_tooltip']		= "";
			
			$update['custom_logo_link']			= "";
			$update['show_custom_logo']			= 1;
			
			$update['pie_regis_set_user_role_']	= "subscriber";
			
			
			$pie_user_email_types 	= get_option( 'pie_user_email_types'); 
					
			foreach ($pie_user_email_types as $val=>$type) 
			{
				$update['enable_user_notifications'] = 0;
				
				$update['user_from_name_'.$val] 	= "Admin";
				$update['user_from_email_'.$val] 	= get_option( 'admin_email' );
				$update['user_to_email_'.$val]	 	= get_option( 'admin_email' );
				$update['user_subject_email_'.$val] = $type;
				$update['user_formate_email_'.$val] = 1;
			}
	
			$update['user_message_email_admin_verification']	 					= '<p>Dear %user_login%,</p><p>Thank You for registering with our website. </p><p>A site administrator will review your request. Once approved, you will be notified via email.</p><p>Best Wishes,</p><p>Team %blogname%</p>';
			
			$update['user_message_email_email_verification']			 			= '<p>Dear %user_login%,</p><p>Thank You for registering with our website. </p><p>Please use the link below to verify your email address. </p><p>%activationurl%</p><p>Best Wishes,</p><p>Team %blogname%</p>';
			
			$update['user_message_email_email_thankyou'] 							= '<p>Dear %user_login%,</p><p>Thank You for registering with our website. </p><p>You are ready to enjoy the benefits of our products and services by signing in to your personalized account.</p><p>Best Regards,</p><p>Team %blogname%</p>';
			$update['user_message_email_payment_success'] 							= '<p>Dear %user_login%,</p><p>Congratulations, your payment has been successfully processed. <br/>Please enjoy the benefits of your membership on %blogname% </p><p>Thank You,</p><p>Team %blogname%</p>';
			
			$update['user_message_email_payment_faild'] 							= '<p>Dear %user_login%,</p><p>Our last attempt to charge the membership payment for your account has failed. </p><p>You are requested to log in to your account at %blogname% to provide a different payment method, or contact your bank/credit-card company to resolve this issue.</p><p>Kind Regards,</p><p>Team %blogname%<br/></p>';
			
			$update['user_message_email_pending_payment'] 							= '<p>Dear %user_login%,</p><p>This is a reminder that membership payment is overdue for your account on %blogname%. Please process your payment immediately to keep membership previlages active. </p><p>Best Regards,</p><p>Team %blogname%</p>';
			
			$update['user_message_email_default_template'] 							= '<p>Dear %user_login%,</p><p>Thank You for registering with our website.</p><p>You are ready to enjoy the benefits of our products and services by signing in to your personalized account.</p><p>Best Regards,</p><p>Team %blogname%</p>';
			
			$update['user_message_email_pending_payment_reminder'] 					='<p>Dear %user_login%,</p><p>We have noticed that you created an account on %blogname% a few days ago, but have not completed the payment. Please use the link below to complete the payment. <br/>Your account will be activated once the payment is received.</p><p>%pending_payment_url%</p><p>Best Regards,</p><p>Team %blogname%</p>';
			
			$update['user_message_email_email_verification_reminder']			 	= '<p>Dear %user_login%,</p><p>Thank You for registering with our website. </p><p>We noticed that you created an account on %blogname% but have not completed the email verification process. </p><p>Please use the link below to verify your email address. </p><p>%activationurl%</p><p>Best Wishes,</p><p>Team %blogname%</p>';
	
			$update['user_message_email_forgot_password_notification']				= '<p>Dear %user_login%,</p><p>We have received a request to reset your account password on %blogname%. Please use the link below to reset your password. If you did not request a new password, please ignore this email and the change will not be made.</p><p>( %reset_password_url% )</p><p>Best Regards,</p><p>Team %user_login%</p>';
			
			$update['user_message_email_email_edit_verification']		= '<p>Hello %user_login%,</p><p>We have received a request to change the email address for your account on %blogname%.</p><p>Old Email Address: %user_email%<br/>New Email Address: %user_new_email%. </p><p>Please use the link below to complete this change.</p><p>(%reset_email_url%)</p><p>Thanks</p><p>%blogname%<br/></p>';
			
			$update['user_message_email_current_email_verification']	= '<p>Hello %user_login%,</p><p>We have received a request to change the email address for your account on %blogname%.</p><p>Old Email Address: %user_email%<br/>  New Email Address: %user_new_email%. </p><p>If you requested this change, please use the link below to complete the action. Otherwise please ignore this email and the change will not be made.</p><p>(%confirm_current_email_url%)</p><p>Thanks</p><p>%blogname%<br/></p>';
			
			//Reset_all_pie_register_settings
			update_option(OPTION_PIE_REGISTER, $update );
			$this->set_pr_global_options( OPTION_PIE_REGISTER, $update );
		}
		function pie_registration_url($url=false)
		{
			$this->pr_get_WP_Rewrite();
			
			global $PR_GLOBAL_OPTIONS;
			$options = $PR_GLOBAL_OPTIONS;
			$pie_registration_url = get_permalink($options['alternate_register']);
			return ($pie_registration_url)? $pie_registration_url : wp_registration_url();
		}
		static function static_pie_registration_url($url=false)
		{
			if ( empty( $GLOBALS['wp_rewrite'] ) )
				$GLOBALS['wp_rewrite'] = new WP_Rewrite();
			
			global $PR_GLOBAL_OPTIONS;
			$options = $PR_GLOBAL_OPTIONS;
			$pie_registration_url = get_permalink($options['alternate_register']);
			return ($pie_registration_url)? $pie_registration_url : wp_registration_url();
		}
		function pie_login_url($url=false)
		{
			global $PR_GLOBAL_OPTIONS;
			$options = $PR_GLOBAL_OPTIONS;
			$this->pr_get_WP_Rewrite();
			
			$pie_login_url = $this->get_page_uri($options['alternate_login']);
			
			$redirect_to_url = explode("?redirect_to=",$url);
			
			if(isset($redirect_to_url[1]) && !empty($redirect_to_url[1])){
				$pie_login_url = $this->pie_modify_custom_url($pie_login_url,"redirect_to=".($redirect_to_url[1]) );
			}else{
				$current_page_uri = $this->get_current_permalink();
				$current_page_uri = ( (!empty($current_page_uri)) ? $current_page_uri : $this->piereg_get_current_url() );
				$pie_login_url = $this->pie_modify_custom_url($pie_login_url,"redirect_to=".($current_page_uri) );
			}
			
			return ( ($pie_login_url)? $pie_login_url : ( (!empty($url))?$url:wp_login_url() ) );
		}
		static function static_pie_login_url($url=false)
		{
			global $PR_GLOBAL_OPTIONS;
			$options = $PR_GLOBAL_OPTIONS;
			if ( empty( $GLOBALS['wp_rewrite'] ) )
				$GLOBALS['wp_rewrite'] = new WP_Rewrite();
			
			$pie_login_url = PieReg_Base::static_get_page_uri($options['alternate_login']);
			
			$redirect_to_url = explode("?redirect_to=",$url);
			
			if(isset($redirect_to_url[1]) && !empty($redirect_to_url[1])){
				$pie_login_url = PieReg_Base::static_pie_modify_custom_url($pie_login_url,"redirect_to=".(urlencode($redirect_to_url[1])) );
			}else{
				$current_page_uri = PieReg_Base::static_get_current_permalink();
				$current_page_uri = ( (!empty($current_page_uri)) ? $current_page_uri : $this->piereg_get_current_url() );
				$pie_login_url = PieReg_Base::static_pie_modify_custom_url($pie_login_url,"redirect_to=".(urlencode($current_page_uri)) );
			}
			
			return ( ($pie_login_url)? $pie_login_url : ( (!empty($url))?$url:wp_login_url() ) );
		}
		function pie_lostpassword_url($url=false)
		{
			$this->pr_get_WP_Rewrite();
			
			global $PR_GLOBAL_OPTIONS;
			$options = $PR_GLOBAL_OPTIONS;
			$pie_lostpass_url = get_permalink($options['alternate_forgotpass']);
			return ($pie_lostpass_url)? $pie_lostpass_url : wp_lostpassword_url();
		}
		static function static_pie_lostpassword_url($url=false)
		{
			if ( empty( $GLOBALS['wp_rewrite'] ) )
				$GLOBALS['wp_rewrite'] = new WP_Rewrite();
			
			global $PR_GLOBAL_OPTIONS;
			$options = $PR_GLOBAL_OPTIONS;
			$pie_lostpass_url = get_permalink($options['alternate_forgotpass']);
			return ($pie_lostpass_url)? $pie_lostpass_url : wp_lostpassword_url();
		}
		function piereg_logout_url($url,$redirect)
		{
			$options = $this->get_pr_global_options();
			$this->pr_get_WP_Rewrite();
			$this->piereg_get_wp_plugable_file();
				
			/*
			 *	Get after Log Out url by current user role
			 */
			$log_out_url 		= "";
			$log_out_page_id	= "";
			
			if( $this->piereg_pro_is_activate ) {
				global $wpdb,$current_user;
				$piereg_table_name=$wpdb->prefix."pieregister_redirect_settings";
				$current_user = wp_get_current_user();
				$current_user_roles = "'".implode("','",$current_user->roles)."'";
				$sql = "SELECT `log_out_url`,`log_out_page_id` FROM `$piereg_table_name` WHERE `user_role` IN({$current_user_roles}) LIMIT 1";
				$db_result = $wpdb->get_results( $sql ); #WPS_IN
				if(isset($wpdb->last_error) && !empty($wpdb->last_error)){
					$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
				}
				
				if($db_result){
					foreach($db_result as $db_result_val){
						if(!empty($db_result_val->log_out_url))
							$log_out_url = trim(urldecode($db_result_val->log_out_url));
						if(!empty($db_result_val->log_out_page_id))
							$log_out_page_id = intval($db_result_val->log_out_page_id);
					}
				}
			}
		
			if(!empty($log_out_url) && ($log_out_page_id == 0 || $log_out_page_id == "")){
				$redirect = trim($log_out_url);
			}
			elseif(!empty($log_out_page_id) && $log_out_page_id > 0){
				$redirect = $this->get_page_uri(intval($log_out_page_id));
			}
			elseif( $options['alternate_logout'] == 'url' && !empty($options['alternate_logout_url'])){
				$piereg_after_redirect_page = $options['alternate_logout_url'];
				$redirect = $piereg_after_redirect_page;
			}
			elseif( intval($options['alternate_logout']) > 0 && $options['alternate_logout'] != 'url'){
				$piereg_after_redirect_page = (intval($options['alternate_logout']) <= 0)? wp_logout_url() : $this->get_page_uri($options['alternate_logout']);
				$redirect = $piereg_after_redirect_page;
			}
			elseif(isset($_GET['redirect_to']) && $_GET['redirect_to'] != ""){
				$redirect = $_GET['redirect_to'];
			}
			
			if(empty($redirect))
				$redirect = home_url();
			
			$redirect = urlencode($redirect);
			$new_logout_url = home_url() . '/?piereg_logout_url=true&redirect_to=' . $redirect;
			return $new_logout_url;
		}
		static function static_piereg_logout_url($url,$redirect)
		{
			$options = $this->get_pr_global_options();
			if ( empty( $GLOBALS['wp_rewrite'] ) )
				$GLOBALS['wp_rewrite'] = new WP_Rewrite();
			$this->piereg_get_wp_plugable_file();
			
			$log_out_url 		= "";
			$log_out_page_id	= "";
			
			/*
				Get after Log Out url by current user role
			*/
			if( $this->piereg_pro_is_activate ) {
				global $wpdb,$current_user;
				$piereg_table_name=$wpdb->prefix."pieregister_redirect_settings";
				$current_user = wp_get_current_user();
				$current_user_roles = "'".implode("','",$current_user->roles)."'";
				$sql = "SELECT `log_out_url`,`log_out_page_id` FROM {$piereg_table_name} WHERE `user_role` IN({$current_user_roles}) LIMIT 1";
				$db_result = $wpdb->get_results( $wpdb->prepare($sql,'') ); #WPS_IN
				if(isset($wpdb->last_error) && !empty($wpdb->last_error)){
					$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
				}
				$log_out_url = "";
				$log_out_page_id = "";
				if($db_result){
					foreach($db_result as $db_result_val){
						if(!empty($db_result_val->log_out_url))
							$log_out_url = trim(urldecode($db_result_val->log_out_url));
						if(!empty($db_result_val->log_out_page_id))
							$log_out_page_id = intval($db_result_val->log_out_page_id);
					}
				}
			}
			
			if(!empty($log_out_url) && ($log_out_page_id == 0 || $log_out_page_id == "")){
				$redirect = trim($log_out_url);
			}
			elseif(!empty($log_out_page_id) && $log_out_page_id > 0){
				$redirect = $this->get_page_uri(intval($log_out_page_id));
			}
			elseif( $options['alternate_logout'] == 'url' && !empty($options['alternate_logout_url']) ){
				$piereg_after_redirect_page = $options['alternate_logout_url'];
				$redirect = $piereg_after_redirect_page;
			}
			elseif( $options['alternate_logout'] > 0 && $options['alternate_logout'] != 'url' ){
				$piereg_after_redirect_page = (intval($options['alternate_logout']) <= 0)? wp_logout_url() : $this->get_page_uri($options['alternate_logout']);
				$redirect = $piereg_after_redirect_page;
			}
			elseif(isset($_GET['redirect_to']) && $_GET['redirect_to'] != ""){
				$redirect = $_GET['redirect_to'];
			}
			
			if(empty($redirect))
				$redirect = home_url();
			
			$redirect = urlencode($redirect);
			$new_logout_url = home_url() . '/?piereg_logout_url=true&redirect_to=' . $redirect;
			return $new_logout_url;
		}
		function pie_modify_custom_url($get_url,$query_string=false){
			$get_url = trim($get_url);
			if(!$get_url) return false;
			
			if(strpos($get_url,"?"))
				$url = $get_url."&".$query_string;
			else
				$url = $get_url."?".$query_string;
				
			return $url;
		}
		static function static_pie_modify_custom_url($get_url,$query_string=false){
			$get_url = trim($get_url);
			if(!$get_url) return false;
			
			if(strpos($get_url,"?"))
				$url = $get_url."&".$query_string;
			else
				$url = $get_url."?".$query_string;
				
			return $url;
		}
		// get current URL
		function piereg_get_current_url($query_string = "") {
			$current_url  = 'http';
			$server_https = isset($_SERVER["HTTPS"]) ? $_SERVER["HTTPS"] : "";
			$server_name  = $_SERVER["SERVER_NAME"];
			$server_port  = $_SERVER["SERVER_PORT"];
			$request_uri  = $_SERVER["REQUEST_URI"];
			if ($server_https == "on") 
				$current_url .= "s";
			$current_url .= "://";
			if ($server_port != "80")
				$current_url .= $server_name . ":" . $server_port . $request_uri;
			else 
				$current_url .= $server_name . $request_uri;
			
			if(!empty($query_string))
				return $this->pie_modify_custom_url($current_url,$query_string);
			
			return $current_url;
		}
		function piereg_validate_files($file_info,$extantion_array = array())
		{
			$result = false;
			$extantion_array = array_map("trim",$extantion_array);
			$result = in_array(pathinfo($file_info,PATHINFO_EXTENSION),$extantion_array);
			$result = (trim($result))? $result : false;
			return $result;
		}
		
		function pie_profile_pictures_upload($user_id,$field,$field_slug){
			global $errors;
			$errors = new WP_Error();
			if( isset($_FILES[$field_slug]['name']) && $_FILES[$field_slug]['name'] != ''){
				////////////////////////////UPLOAD PROFILE PICTURE//////////////////////////////
				$allowedExts = array("gif", "GIF", "jpeg", "JPEG", "jpg", "JPG", "png", "PNG", "bmp", "BMP" );
				$result = $this->piereg_validate_files($_FILES[$field_slug]['name'],$allowedExts);
				if($result)
				{
					$temp = explode(".", $_FILES[$field_slug]["name"]);
					$extension = end($temp);
					$upload_dir = wp_upload_dir();
					$temp_dir = $upload_dir['basedir']."/piereg_users_files/".$user_id;
					wp_mkdir_p($temp_dir);
					$temp_file_name = "profile_pic_".abs( crc32( wp_generate_password( rand(7,12) ) ."_".time() ) ).".".$extension;
					$temp_file_url = $upload_dir['baseurl']."/piereg_users_files/".$user_id."/".$temp_file_name;
					if(!move_uploaded_file($_FILES[$field_slug]['tmp_name'],$temp_dir."/".$temp_file_name)){
						$errors->add( $field_slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_fail_to_upload_profile picture",__('Fail to upload profile picture.','piereg' )));
					}else{
						/*Upload Index.html file on User dir*/
						$this->upload_forbidden_html_file( $upload_dir['basedir']."/piereg_users_files" );
						/*Upload Index.html file on User dir*/
						$this->upload_forbidden_html_file( $temp_dir );
						
						$old_picture = get_user_meta($user_id,"pie_".$field_slug, true);
						if( !empty($old_picture) ){
							if( file_exists($temp_dir."/".basename( $old_picture )) ){
								unlink( $temp_dir."/".basename( $old_picture ) );
							}
						}
						update_user_meta($user_id,"pie_".$field_slug, $temp_file_url);
						$this->pie_success = 1;
					}
					
				}else{
					$errors->add( $slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_invalid_file_type_in_profile_picture",__('Invalid File Type In Profile Picture.','piereg' )));
					$this->pie_error = 1;
				}
			}else{
				update_user_meta($user_id,"pie_".$field_slug, "");
			}
	
		}
		function upload_forbidden_html_file($dir_name){
			if( !empty($dir_name) && !file_exists($dir_name."/index.html") ){
				$myfile = @fopen($dir_name."/index.html", "w");
				@fwrite( $myfile, "<html><head><title>Forbidden</title></head><body><h1>Forbidden</h1><p>You Don't have permission to access on this server</p></body></html>" );
				@fclose( $myfile );
			}
		}
		function pie_upload_files($user_id,$field,$field_slug){
			global $errors;
			$errors = new WP_Error();
			$result = false;
			if($_FILES[$field_slug]['name'] != ''){
				///////////////////UPLOAD ALL FILES//////////////////////////
				
				if($field['file_types'] != ""){
					$filter_string = stripcslashes($field['file_types']);
					$filter_array = explode(",",$filter_string);
					$result = $this->piereg_validate_files($_FILES[$field_slug]['name'],$filter_array);
					
					if($result){
						$temp = explode(".", $_FILES[$field_slug]["name"]);
						$extension = end($temp);
						$upload_dir = wp_upload_dir();
						$temp_dir = $upload_dir['basedir']."/piereg_users_files/".$user_id;
						wp_mkdir_p($temp_dir);
						$temp_file_name = "file_".abs( crc32( wp_generate_password( rand(7,12) ) ."_".time() ) ).".".$extension;
						$temp_file_url = $upload_dir['baseurl']."/piereg_users_files/".$user_id."/".$temp_file_name;
						if(!move_uploaded_file($_FILES[$field_slug]['tmp_name'],$temp_dir."/".$temp_file_name) && $required){
							$errors->add( $field_slug , '<strong>'.__(ucwords('error'),'piereg').'</strong>: '.apply_filters("piereg_Fail_to_upload_profile_picture",__('Fail to upload profile picture.','piereg' )));
						}else{
							/*Upload Index.html file on User dir*/
							$this->upload_forbidden_html_file( $upload_dir['basedir']."/piereg_users_files" );
							/*Upload Index.html file on User dir*/
							$this->upload_forbidden_html_file( $temp_dir );
							
							$old_file = get_user_meta($user_id,"pie_".$field_slug, true);
							if( !empty($old_file) ){
								if( file_exists($temp_dir."/".basename( $old_file )) ){
									unlink( $temp_dir."/".basename( $old_file ) );
								}
							}
							update_user_meta($user_id,"pie_".$field_slug, $temp_file_url);
						}
					}else{
						$errors->add( $field_slug , '<strong>'.__(ucwords('error'),'piereg').'</strong>: '.apply_filters("piereg_invalid_file",__('Invalid File.','piereg' )));
					}
				}
				elseif($field['file_types'] == ""){
					$temp = explode(".", $_FILES[$field_slug]["name"]);
					$extension = end($temp);
					$upload_dir = wp_upload_dir();
					$temp_dir = $upload_dir['basedir']."/piereg_users_files/".$user_id;
					wp_mkdir_p($temp_dir);
					$temp_file_name = "file_".abs( crc32( wp_generate_password( rand(7,12) ) ."_".time() ) ).".".$extension;
					$temp_file_url = $upload_dir['baseurl']."/piereg_users_files/".$user_id."/".$temp_file_name;
					if(!move_uploaded_file($_FILES[$field_slug]['tmp_name'],$temp_dir."/".$temp_file_name) && $required){
						$errors->add( $field_slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_fail_to_upload_profile_picture",__('Fail to upload profile picture.','piereg' )));
					}else{
						/*Upload Index.html file on User dir*/
						$this->upload_forbidden_html_file( $upload_dir['basedir']."/piereg_users_files" );
						/*Upload Index.html file on User dir*/
						$this->upload_forbidden_html_file( $temp_dir );
						
						$old_file = get_user_meta($user_id,"pie_".$field_slug, true);
						if( !empty($old_file) ){
							if( file_exists($temp_dir."/".basename( $old_file )) ){
								unlink( $temp_dir."/".basename( $old_file ) );
							}
						}
						update_user_meta($user_id,"pie_".$field_slug, $temp_file_url);
					}
				}
			}else{
				update_user_meta($user_id,"pie_".$field_slug, "");
			}
		}
		/*
			*	Check SSL enable or not. And return true/false
		*/
		function PR_IS_SSL(){
			$piereg_secure_cookie = false;
			if ( !force_ssl_admin() && is_ssl() ) {
				$piereg_secure_cookie = true;
				force_ssl_admin(true);
			}
			return $piereg_secure_cookie;
		}
		function require_once_file($file_name = false)
		{
			if($file_name)
			{
				if(file_exists($file_name))
					require_once($file_name);
				else
					echo '<div style="background: none repeat scroll 0 0 rgb(252, 214, 214);border: 1px solid rgb(204, 204, 204);color: rgb(145, 7, 7);display: inline-block;margin: 5px 0;padding: 10px;width: auto;"><b>Warning :</b> File ( <b>'.(basename($file_name)).'</b> ) not found! [DIR : '.$file_name.']</div>';
			}
			return false;
		}
		/*
			*	Error Logging
			*	Error_log Message Type
			0 	message is sent to PHP's system logger, using the Operating System's system logging mechanism or a file, depending on what the error_log configuration directive is set to. This is the default option.
			1 	message is sent by email to the address in the destination parameter. This is the only message type where the fourth parameter, extra_headers is used.
			2 	No longer an option.
			3 	message is appended to the file destination. A newline is not automatically added to the end of the message string.
			4 	message is sent directly to the SAPI logging handler. 
		*/
		function pr_error_log($error,$error_type = 'error',$message_type = 3){
			if(!$error)
				return false;
			
			$error_header = "[".date_i18n("D d-m-Y H:i:s")."] [Client : ".$_SERVER['REMOTE_ADDR']."] [URI : ".$_SERVER['REQUEST_URI']."] [".$error_type."] ";
			$error_message = $error_header.$error."\r\n\r\n";
			return error_log($error_message, $message_type, PIEREG_DIR_NAME."/log/piereg_log.log");
		}
		function pr_payment_log($log_message,$file_name = "payment-log",$error_type = 'error',$message_type = 3){
			if(!$log_message)
				return false;
			$log_message = $log_message."\r\n============================================\r\n";
			return error_log($log_message, $message_type, PIEREG_DIR_NAME."/log/".$file_name.".log");
		}
		/*
			*	Return error info
		*/
		function get_error_log_info($function = "",$line = "",$file = ""){
			return " [Function : ".($function)."] [Line : ".($line)."] [File : ".($file)."]";
		}
		/*
			* Set PR forms Stats
		*/
		function set_pr_stats($stats,$type){
			if(!$stats || !$type)
				return false;
			
			switch($stats){
				case "login":
				case "forgot":
				case "register":
					if($type === "view" || $type === "used"){
						$piereg_stats = get_option(PIEREG_STATS_OPTION);
						if(!isset($piereg_stats[$stats][$type]))
							$piereg_stats[$stats][$type] = 1;
						else
							$piereg_stats[$stats][$type] = (intval($piereg_stats[$stats][$type]) + 1);
						
						update_option(PIEREG_STATS_OPTION,$piereg_stats);
						return true;
					}
					return false;
				break;
				default:
					return false;
				break;
			}
		}
		static function static_set_pr_stats($stats,$type){
			if(!$stats || !$type)
				return false;
			
			switch($stats){
				case "login":
				case "forgot":
				case "register":
					if($type === "view" || $type === "used"){
						$piereg_stats = get_option(PIEREG_STATS_OPTION);
						if(!isset($piereg_stats[$stats][$type]))
							$piereg_stats[$stats][$type] = 1;
						else
							$piereg_stats[$stats][$type] = (intval($piereg_stats[$stats][$type]) + 1);
						
						update_option(PIEREG_STATS_OPTION,$piereg_stats);
						return true;
					}
					return false;
				break;
				default:
					return false;
				break;
			}
		}
		/*
			*	Save currency name with array
		*/
		function piereg_save_currency(){
                    $old_currency_array = get_option(PIEREG_CURRENCY_OPTION);
					$currency_array[0]["code"] = "AFN";$currency_array[0]["name"] = "Afghanistan Afghani";
					$currency_array[1]["code"] = "ALL";$currency_array[1]["name"] = "Albania Lek";
					$currency_array[2]["code"] = "DZD";$currency_array[2]["name"] = "Algeria Dinar";
					$currency_array[3]["code"] = "AOA";$currency_array[3]["name"] = "Angola Kwanza";
					$currency_array[4]["code"] = "ARS";$currency_array[4]["name"] = "Argentina Peso";
					$currency_array[5]["code"] = "AMD";$currency_array[5]["name"] = "Armenia Dram";
					$currency_array[6]["code"] = "AWG";$currency_array[6]["name"] = "Aruba Guilder";
					$currency_array[7]["code"] = "AUD";$currency_array[7]["name"] = "Australia Dollar";
					$currency_array[8]["code"] = "AZN";$currency_array[8]["name"] = "Azerbaijan New Manat";
					$currency_array[9]["code"] = "BSD";$currency_array[9]["name"] = "Bahamas Dollar";
					$currency_array[10]["code"] = "BHD";$currency_array[10]["name"] = "Bahrain Dinar";
					$currency_array[11]["code"] = "BDT";$currency_array[11]["name"] = "Bangladesh Taka";
					$currency_array[12]["code"] = "BBD";$currency_array[12]["name"] = "Barbados Dollar";
					$currency_array[13]["code"] = "BYR";$currency_array[13]["name"] = "Belarus Ruble";
					$currency_array[14]["code"] = "BZD";$currency_array[14]["name"] = "Belize Dollar";
					$currency_array[15]["code"] = "BMD";$currency_array[15]["name"] = "Bermuda Dollar";
					$currency_array[16]["code"] = "BTN";$currency_array[16]["name"] = "Bhutan Ngultrum";
					$currency_array[17]["code"] = "BOB";$currency_array[17]["name"] = "Bolivia Boliviano";
					$currency_array[18]["code"] = "BAM";$currency_array[18]["name"] = "Bosnia and Herzegovina Convertible Marka";
					$currency_array[19]["code"] = "BWP";$currency_array[19]["name"] = "Botswana Pula";
					$currency_array[20]["code"] = "BRL";$currency_array[20]["name"] = "Brazil Real";
					$currency_array[21]["code"] = "BND";$currency_array[21]["name"] = "Brunei Darussalam Dollar";
					$currency_array[22]["code"] = "BGN";$currency_array[22]["name"] = "Bulgaria Lev";
					$currency_array[23]["code"] = "BIF";$currency_array[23]["name"] = "Burundi Franc";
					$currency_array[24]["code"] = "KHR";$currency_array[24]["name"] = "Cambodia Riel";
					$currency_array[25]["code"] = "CAD";$currency_array[25]["name"] = "Canada Dollar";
					$currency_array[26]["code"] = "CVE";$currency_array[26]["name"] = "Cape Verde Escudo";
					$currency_array[27]["code"] = "KYD";$currency_array[27]["name"] = "Cayman Islands Dollar";
					$currency_array[28]["code"] = "CLP";$currency_array[28]["name"] = "Chile Peso";
					$currency_array[29]["code"] = "CNY";$currency_array[29]["name"] = "China Yuan Renminbi";
					$currency_array[30]["code"] = "COP";$currency_array[30]["name"] = "Colombia Peso";
					$currency_array[31]["code"] = "XOF";$currency_array[31]["name"] = "CommunautÃ© FinanciÃ¨re Africaine (BCEAO) Franc";
					$currency_array[32]["code"] = "XAF";$currency_array[32]["name"] = "CommunautÃ© FinanciÃ¨re Africaine (BEAC) CFA Franc BEAC";
					$currency_array[33]["code"] = "KMF";$currency_array[33]["name"] = "Comoros Franc";
					$currency_array[34]["code"] = "XPF";$currency_array[34]["name"] = "Comptoirs FranÃ§ais du Pacifique (CFP) Franc";
					$currency_array[35]["code"] = "CDF";$currency_array[35]["name"] = "Congo/Kinshasa Franc";
					$currency_array[36]["code"] = "CRC";$currency_array[36]["name"] = "Costa Rica Colon";
					$currency_array[37]["code"] = "HRK";$currency_array[37]["name"] = "Croatia Kuna";
					$currency_array[38]["code"] = "CUC";$currency_array[38]["name"] = "Cuba Convertible Peso";
					$currency_array[39]["code"] = "CUP";$currency_array[39]["name"] = "Cuba Peso";
					$currency_array[40]["code"] = "CZK";$currency_array[40]["name"] = "Czech Republic Koruna";
					$currency_array[41]["code"] = "DKK";$currency_array[41]["name"] = "Denmark Krone";
					$currency_array[42]["code"] = "DJF";$currency_array[42]["name"] = "Djibouti Franc";
					$currency_array[43]["code"] = "DOP";$currency_array[43]["name"] = "Dominican Republic Peso";
					$currency_array[44]["code"] = "XCD";$currency_array[44]["name"] = "East Caribbean Dollar";
					$currency_array[45]["code"] = "EGP";$currency_array[45]["name"] = "Egypt Pound";
					$currency_array[46]["code"] = "SVC";$currency_array[46]["name"] = "El Salvador Colon";
					$currency_array[47]["code"] = "ERN";$currency_array[47]["name"] = "Eritrea Nakfa";
					$currency_array[48]["code"] = "ETB";$currency_array[48]["name"] = "Ethiopia Birr";
					$currency_array[49]["code"] = "EUR";$currency_array[49]["name"] = "Euro Member Countries";
					$currency_array[50]["code"] = "FKP";$currency_array[50]["name"] = "Falkland Islands (Malvinas) Pound";
					$currency_array[51]["code"] = "FJD";$currency_array[51]["name"] = "Fiji Dollar";
					$currency_array[52]["code"] = "GMD";$currency_array[52]["name"] = "Gambia Dalasi";
					$currency_array[53]["code"] = "GEL";$currency_array[53]["name"] = "Georgia Lari";
					$currency_array[54]["code"] = "GHS";$currency_array[54]["name"] = "Ghana Cedi";
					$currency_array[55]["code"] = "GIP";$currency_array[55]["name"] = "Gibraltar Pound";
					$currency_array[56]["code"] = "GTQ";$currency_array[56]["name"] = "Guatemala Quetzal";
					$currency_array[57]["code"] = "GGP";$currency_array[57]["name"] = "Guernsey Pound";
					$currency_array[58]["code"] = "GNF";$currency_array[58]["name"] = "Guinea Franc";
					$currency_array[59]["code"] = "GYD";$currency_array[59]["name"] = "Guyana Dollar";
					$currency_array[60]["code"] = "HTG";$currency_array[60]["name"] = "Haiti Gourde";
					$currency_array[61]["code"] = "HNL";$currency_array[61]["name"] = "Honduras Lempira";
					$currency_array[62]["code"] = "HKD";$currency_array[62]["name"] = "Hong Kong Dollar";
					$currency_array[63]["code"] = "HUF";$currency_array[63]["name"] = "Hungary Forint";
					$currency_array[64]["code"] = "ISK";$currency_array[64]["name"] = "Iceland Krona";
					$currency_array[65]["code"] = "INR";$currency_array[65]["name"] = "India Rupee";
					$currency_array[66]["code"] = "IDR";$currency_array[66]["name"] = "Indonesia Rupiah";
					$currency_array[67]["code"] = "XDR";$currency_array[67]["name"] = "International Monetary Fund (IMF) Special Drawing Rights";
					$currency_array[68]["code"] = "IRR";$currency_array[68]["name"] = "Iran Rial";
					$currency_array[69]["code"] = "IQD";$currency_array[69]["name"] = "Iraq Dinar";
					$currency_array[70]["code"] = "IMP";$currency_array[70]["name"] = "Isle of Man Pound";
					$currency_array[71]["code"] = "ILS";$currency_array[71]["name"] = "Israel Shekel";
					$currency_array[72]["code"] = "JMD";$currency_array[72]["name"] = "Jamaica Dollar";
					$currency_array[73]["code"] = "JPY";$currency_array[73]["name"] = "Japan Yen";
					$currency_array[74]["code"] = "JEP";$currency_array[74]["name"] = "Jersey Pound";
					$currency_array[75]["code"] = "JOD";$currency_array[75]["name"] = "Jordan Dinar";
					$currency_array[76]["code"] = "KZT";$currency_array[76]["name"] = "Kazakhstan Tenge";
					$currency_array[77]["code"] = "KES";$currency_array[77]["name"] = "Kenya Shilling";
					$currency_array[78]["code"] = "KPW";$currency_array[78]["name"] = "Korea (North) Won";
					$currency_array[79]["code"] = "KRW";$currency_array[79]["name"] = "Korea (South) Won";
					$currency_array[80]["code"] = "KWD";$currency_array[80]["name"] = "Kuwait Dinar";
					$currency_array[81]["code"] = "KGS";$currency_array[81]["name"] = "Kyrgyzstan Som";
					$currency_array[82]["code"] = "LAK";$currency_array[82]["name"] = "Laos Kip";
					$currency_array[83]["code"] = "LBP";$currency_array[83]["name"] = "Lebanon Pound";
					$currency_array[84]["code"] = "LSL";$currency_array[84]["name"] = "Lesotho Loti";
					$currency_array[85]["code"] = "LRD";$currency_array[85]["name"] = "Liberia Dollar";
					$currency_array[86]["code"] = "LYD";$currency_array[86]["name"] = "Libya Dinar";
					$currency_array[87]["code"] = "MOP";$currency_array[87]["name"] = "Macau Pataca";
					$currency_array[88]["code"] = "MKD";$currency_array[88]["name"] = "Macedonia Denar";
					$currency_array[89]["code"] = "MGA";$currency_array[89]["name"] = "Madagascar Ariary";
					$currency_array[90]["code"] = "MWK";$currency_array[90]["name"] = "Malawi Kwacha";
					$currency_array[91]["code"] = "MYR";$currency_array[91]["name"] = "Malaysia Ringgit";
					$currency_array[92]["code"] = "MVR";$currency_array[92]["name"] = "Maldives (Maldive Islands) Rufiyaa";
					$currency_array[93]["code"] = "MRO";$currency_array[93]["name"] = "Mauritania Ouguiya";
					$currency_array[94]["code"] = "MUR";$currency_array[94]["name"] = "Mauritius Rupee";
					$currency_array[95]["code"] = "MXN";$currency_array[95]["name"] = "Mexico Peso";
					$currency_array[96]["code"] = "MDL";$currency_array[96]["name"] = "Moldova Leu";
					$currency_array[97]["code"] = "MNT";$currency_array[97]["name"] = "Mongolia Tughrik";
					$currency_array[98]["code"] = "MAD";$currency_array[98]["name"] = "Morocco Dirham";
					$currency_array[99]["code"] = "MZN";$currency_array[99]["name"] = "Mozambique Metical";
					$currency_array[100]["code"] = "MMK";$currency_array[100]["name"] = "Myanmar (Burma) Kyat";
					$currency_array[101]["code"] = "NAD";$currency_array[101]["name"] = "Namibia Dollar";
					$currency_array[102]["code"] = "NPR";$currency_array[102]["name"] = "Nepal Rupee";
					$currency_array[103]["code"] = "ANG";$currency_array[103]["name"] = "Netherlands Antilles Guilder";
					$currency_array[104]["code"] = "NZD";$currency_array[104]["name"] = "New Zealand Dollar";
					$currency_array[105]["code"] = "NIO";$currency_array[105]["name"] = "Nicaragua Cordoba";
					$currency_array[106]["code"] = "NGN";$currency_array[106]["name"] = "Nigeria Naira";
					$currency_array[107]["code"] = "NOK";$currency_array[107]["name"] = "Norway Krone";
					$currency_array[108]["code"] = "OMR";$currency_array[108]["name"] = "Oman Rial";
					$currency_array[109]["code"] = "PKR";$currency_array[109]["name"] = "Pakistan Rupee";
					$currency_array[110]["code"] = "PAB";$currency_array[110]["name"] = "Panama Balboa";
					$currency_array[111]["code"] = "PGK";$currency_array[111]["name"] = "Papua New Guinea Kina";
					$currency_array[112]["code"] = "PYG";$currency_array[112]["name"] = "Paraguay Guarani";
					$currency_array[113]["code"] = "PEN";$currency_array[113]["name"] = "Peru Nuevo Sol";
					$currency_array[114]["code"] = "PHP";$currency_array[114]["name"] = "Philippines Peso";
					$currency_array[115]["code"] = "PLN";$currency_array[115]["name"] = "Poland Zloty";
					$currency_array[116]["code"] = "QAR";$currency_array[116]["name"] = "Qatar Riyal";
					$currency_array[117]["code"] = "RON";$currency_array[117]["name"] = "Romania New Leu";
					$currency_array[118]["code"] = "RUB";$currency_array[118]["name"] = "Russia Ruble";
					$currency_array[119]["code"] = "RWF";$currency_array[119]["name"] = "Rwanda Franc";
					$currency_array[120]["code"] = "SHP";$currency_array[120]["name"] = "Saint Helena Pound";
					$currency_array[121]["code"] = "WST";$currency_array[121]["name"] = "Samoa Tala";
					$currency_array[122]["code"] = "SAR";$currency_array[122]["name"] = "Saudi Arabia Riyal";
					$currency_array[123]["code"] = "SPL*";$currency_array[123]["name"] = "Seborga Luigino";
					$currency_array[124]["code"] = "RSD";$currency_array[124]["name"] = "Serbia Dinar";
					$currency_array[125]["code"] = "SCR";$currency_array[125]["name"] = "Seychelles Rupee";
					$currency_array[126]["code"] = "SLL";$currency_array[126]["name"] = "Sierra Leone Leone";
					$currency_array[127]["code"] = "SGD";$currency_array[127]["name"] = "Singapore Dollar";
					$currency_array[128]["code"] = "SBD";$currency_array[128]["name"] = "Solomon Islands Dollar";
					$currency_array[129]["code"] = "SOS";$currency_array[129]["name"] = "Somalia Shilling";
					$currency_array[130]["code"] = "ZAR";$currency_array[130]["name"] = "South Africa Rand";
					$currency_array[131]["code"] = "LKR";$currency_array[131]["name"] = "Sri Lanka Rupee";
					$currency_array[132]["code"] = "SDG";$currency_array[132]["name"] = "Sudan Pound";
					$currency_array[133]["code"] = "SRD";$currency_array[133]["name"] = "Suriname Dollar";
					$currency_array[134]["code"] = "SZL";$currency_array[134]["name"] = "Swaziland Lilangeni";
					$currency_array[135]["code"] = "SEK";$currency_array[135]["name"] = "Sweden Krona";
					$currency_array[136]["code"] = "CHF";$currency_array[136]["name"] = "Switzerland Franc";
					$currency_array[137]["code"] = "SYP";$currency_array[137]["name"] = "Syria Pound";
					$currency_array[138]["code"] = "STD";$currency_array[138]["name"] = "SÃ£o TomÃ© and PrÃ­ncipe Dobra";
					$currency_array[139]["code"] = "TWD";$currency_array[139]["name"] = "Taiwan New Dollar";
					$currency_array[140]["code"] = "TJS";$currency_array[140]["name"] = "Tajikistan Somoni";
					$currency_array[141]["code"] = "TZS";$currency_array[141]["name"] = "Tanzania Shilling";
					$currency_array[142]["code"] = "THB";$currency_array[142]["name"] = "Thailand Baht";
					$currency_array[143]["code"] = "TOP";$currency_array[143]["name"] = "Tonga Pa'anga";
					$currency_array[144]["code"] = "TTD";$currency_array[144]["name"] = "Trinidad and Tobago Dollar";
					$currency_array[145]["code"] = "TND";$currency_array[145]["name"] = "Tunisia Dinar";
					$currency_array[146]["code"] = "TRY";$currency_array[146]["name"] = "Turkey Lira";
					$currency_array[147]["code"] = "TMT";$currency_array[147]["name"] = "Turkmenistan Manat";
					$currency_array[148]["code"] = "TVD";$currency_array[148]["name"] = "Tuvalu Dollar";
					$currency_array[149]["code"] = "UGX";$currency_array[149]["name"] = "Uganda Shilling";
					$currency_array[150]["code"] = "UAH";$currency_array[150]["name"] = "Ukraine Hryvnia";
					$currency_array[151]["code"] = "AED";$currency_array[151]["name"] = "United Arab Emirates Dirham";
					$currency_array[152]["code"] = "GBP";$currency_array[152]["name"] = "United Kingdom Pound";
					$currency_array[153]["code"] = "USD";$currency_array[153]["name"] = "United States Dollar";
					$currency_array[154]["code"] = "UYU";$currency_array[154]["name"] = "Uruguay Peso";
					$currency_array[155]["code"] = "UZS";$currency_array[155]["name"] = "Uzbekistan Som";
					$currency_array[156]["code"] = "VUV";$currency_array[156]["name"] = "Vanuatu Vatu";
					$currency_array[157]["code"] = "VEF";$currency_array[157]["name"] = "Venezuela Bolivar";
					$currency_array[158]["code"] = "VND";$currency_array[158]["name"] = "Viet Nam Dong";
					$currency_array[159]["code"] = "YER";$currency_array[159]["name"] = "Yemen Rial";
					$currency_array[160]["code"] = "ZMW";$currency_array[160]["name"] = "Zambia Kwacha";
					$currency_array[161]["code"] = "ZWD";$currency_array[161]["name"] = "Zimbabwe Dollar";
                    update_option(PIEREG_CURRENCY_OPTION,$currency_array);
		}
		/*
			* Array To json
		*/
		function piereg_array_to_json($array_value){
			$result = json_encode($array_value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
			return $result;
		}
		/*
			* Json To Array
		*/
		function piereg_json_to_array($json_value,$assoc = true){
			$result = json_decode($json_value,$assoc);
			return $result;
		}
		function read_upload_file($file_dir){
			if(!file_exists($file_dir) || empty($file_dir))
			{
				$_POST['error'] = __("File not exists","piereg");
				return false;
			}
			
			$FileData = "";
			if(function_exists("file_get_contents")){
				$FileData = file_get_contents($file_dir,false);
			}
			//Get Log File by `fopen`
			elseif(function_exists("fopen")){
				$fh = fopen($file_dir, 'r');
				$FileData = fread($fh, filesize($file_dir));
				fclose($fh);
			}
			//Get Log File by `Command`
			else{
				$FileData = `cat $file_dir`;
			}
			return $FileData;
		}
		function read_file_from_url($url){
			$FileData = "";
			//Get File By FILE_GET_CONTENTS
			if(function_exists("file_get_contents")){
				$FileData = file_get_contents($url);
			}
			//Read File By CURL
			elseif(extension_loaded("curl")){
				$curlSession = curl_init();
				curl_setopt($curlSession, CURLOPT_URL, $url);
				curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
				curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
				$FileData = curl_exec($curlSession);
				curl_close($curlSession);
			}
			return $FileData;
		}
		function piereg_array_replace_recursive($base, $replacements)
		{
			if(is_array($base) && is_array($replacements)){
				foreach (array_slice(func_get_args(), 1) as $replacements) {
					$bref_stack = array(&$base);
					$head_stack = array($replacements);
		
					do {
						end($bref_stack);
		
						$bref = &$bref_stack[key($bref_stack)];
						$head = array_pop($head_stack);
		
						unset($bref_stack[key($bref_stack)]);
		
						foreach (array_keys($head) as $key) {
							if (isset($key, $bref) && is_array($bref[$key]) && is_array($head[$key])) {
								$bref_stack[] = &$bref[$key];
								$head_stack[] = $head[$key];
							} else {
								$bref[$key] = $head[$key];
							}
						}
					} while(count($head_stack));
				}
				return $base;
			}
			else{
				return false;
			}
		}
		function get_pr_forms_info()
		{
			$pr_form = array();
			$fields_id = get_option("piereg_form_fields_id");
			if(!empty($fields_id))
			{
				$count = 0;
				for($a=1;$a<=$fields_id;$a++)
				{
					$option = get_option("piereg_form_field_option_".$a);
					if( !empty($option) && is_array($option) && isset($option['Id']) && (!isset($option['IsDeleted']) || trim($option['IsDeleted']) != 1) ) 
						$pr_form[$a] = $option;
				}
			}
			return $pr_form;
		}
		function get_page_uri($page_id, $query_string = ""){
			$this->pr_get_WP_Rewrite();
			if(!empty($query_string))
				return $this->pie_modify_custom_url(get_permalink($page_id),$query_string);
			else
				return get_permalink(intval($page_id));
		}
		static function static_get_page_uri($page_id, $query_string = ""){
			if ( empty( $GLOBALS['wp_rewrite'] ) )
				$GLOBALS['wp_rewrite'] = new WP_Rewrite();
			
			if(!empty($query_string))
				return PieReg_Base::static_pie_modify_custom_url(get_permalink($page_id),$query_string);
			else
				return get_permalink(intval($page_id));
		}
		function get_current_permalink(){
			$this->pr_get_WP_Rewrite();
			return get_permalink();
		}
		static function static_get_current_permalink(){
			if ( empty( $GLOBALS['wp_rewrite'] ) )
				$GLOBALS['wp_rewrite'] = new WP_Rewrite();
			
			return get_permalink();
		}
		function pr_get_WP_Rewrite(){
			if ( empty( $GLOBALS['wp_rewrite'] ) )
				$GLOBALS['wp_rewrite'] = new WP_Rewrite();
		}
		function include_pr_menu_pages($page_dir = false){
			$this->require_once_file($page_dir);
		}		
		function include_pr_menu_pages_previous($page_dir = false){
			if( !$this->piereg_pro_is_activate )
				$this->piereg_pro_is_activate();
			
			if( $this->piereg_pro_is_activate || $_GET['page'] == 'pie-help' || $_GET['page'] == 'pie-help' ){
				$this->require_once_file($page_dir);
			}else{
				$this->require_once_file( plugin_dir_path( dirname(__FILE__) ) .'/menus/PieLicenseKeyPage.php');
			}
		}
		
		function piereg_get_wp_plugable_file( $required = false, $file_name = "",$function_name = ""){
			if($file_name == "" || $function_name = ""){
				if(!function_exists('wp_get_current_user')) {
					if($required) {
						require_once(ABSPATH . "wp-includes/pluggable.php");
					} else {
						include(ABSPATH . "wp-includes/pluggable.php"); 
					}
				}
			}
			elseif(!function_exists($function_name)) {
				if(file_exists(ABSPATH . "wp-includes/".$file_name.".php"))
					include(ABSPATH . "wp-includes/".$file_name.".php"); 
			}
		}
		
		function get_period_by_days($days){
			switch($days){
				case "7":
					return "Weekly";
				break;
				case "30":
					return "Monthly";
				break;
				case "182":
					return "Half Yearly";
				break;
				case "273":
					return "Quarterly";
				break;
				case "365":
					return "Yearly";
				break;
				case "days":
				case "Day":
					return "Day";
				break;
				case "weeks":
				case "Week":
					return "Weekly";
				break;
				case "months":
				case "Month":
					return "Monthly";
				break;
			}
		}
		
		function get_period_by_days_for_payment($days,$frequency = 0){
			$period = array();
			switch($days){
				case "7":
					$period['PERIOD'] = "Week";
					$period['FREQUENCY'] = (!empty($frequency)) ? $frequency : 1;
				break;
				case "30":
					$period['PERIOD'] = "Month";
					$period['FREQUENCY'] = (!empty($frequency)) ? $frequency : 1;
				break;
				case "182":
					$period['PERIOD'] = "Month";
					$period['FREQUENCY'] = (!empty($frequency)) ? $frequency : 6;
				break;
				case "273":
					$period['PERIOD'] = "Month";
					$period['FREQUENCY'] = (!empty($frequency)) ? $frequency : 9;
				break;
				case "273":
					$period['PERIOD'] = "Year";
					$period['FREQUENCY'] = (!empty($frequency)) ? $frequency : 1;
				break;
				case "days":
					$period['PERIOD'] = "Day";
					$period['FREQUENCY'] = (!empty($frequency)) ? $frequency : 1;
				break;
				case "weeks":
					$period['PERIOD'] = "Week";
					$period['FREQUENCY'] = (!empty($frequency)) ? $frequency : 1;
				break;
				case "months":
					$period['PERIOD'] = "Month";
					$period['FREQUENCY'] = (!empty($frequency)) ? $frequency : 1;
				break;
				case "0":
					$period['PERIOD'] = "One Time";
					$period['FREQUENCY'] = 1;
				break;
				default:
					$period['PERIOD'] = "";
					$period['FREQUENCY'] = 0;
				break;
			}
			return $period;
		}
		/*
			*	Get currency code for payment method
		*/
		function pr_get_currency_code($option = array()){
			if( empty($option) || !is_array($option) )
				$options = $this->get_pr_global_options();
			
			return ( ( isset($options['currency']) && !empty($options['currency']) ) ? $options['currency'] : 'USD' );
		}
		/*
			*	Get Username form Email address
		*/
		function get_username_by_email($email){
			return preg_replace('/([^@]*).*/', '$1', $email);
		}
		/*
			*	Get and Update User Payment Log
		*/
		function update_user_payment_log($user_id,$payment_log){
			if( empty($user_id) || empty($payment_log) || !is_array($payment_log) )
				return false;
			
			$old_payment_log =  $this->get_user_payment_log( $user_id );
			$payment_log_array = array();
			
			if(!empty($old_payment_log))
				$payment_log_array = $old_payment_log;
			
			$payment_log_array[] = $payment_log;
			update_user_meta( $user_id, "piereg_user_payment_log", $payment_log_array);
			return $payment_log_array;
		}
		function get_user_payment_log($user_id){
			if( empty($user_id) )
				return false;
			$payment_log =  get_user_meta( $user_id, "piereg_user_payment_log", true );
			return $payment_log;
		}
		/*
			*	Sanitize  All Post Fields
		*/
		function piereg_sanitize_post_data($post = array()){
			if(!is_array($post) || empty($post))
				return false;
			
			foreach($post as $key=>$val){
				if( isset($_POST[$key]) && strpos($key,"username") !== false ){
					$_POST[$key] = esc_sql(esc_attr(sanitize_user($_POST[$key])));
				}elseif( isset($_POST[$key]) && ( strpos($key,"email") !== false ||  strpos($key,"e_mail") !== false ) ){
					$_POST[$key] = esc_sql(esc_attr(sanitize_email($_POST[$key])));
				}elseif( isset($_POST[$key]) ){
					$_POST[$key] = $this->piereg_post_array_filter($_POST[$key]);
				}
			}
		}
		function piereg_post_array_filter($post){
			$new_post = $post;
			if( isset($new_post) && is_array($new_post) ){
				foreach($new_post as $k=>$val){
					$new_post[$k] = $this->piereg_post_array_filter($val);
				}
				return $new_post;
			}else{
				return esc_sql(esc_attr(sanitize_text_field($new_post)));
			}
		}
		
		function stripslashes_deep($value){
			return is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
		}
		
		function piereg_save_payment_log_option($email,$method,$type,$responce){
			$date_time = date_i18n("d-m-Y H:i:s");
			$key = md5( time() );
			$data = get_option("piereg_payment_log_option");
			
			if( empty($data) )
				$data = array();
			
			$data[$key]['email'] = $email;
			$data[$key]['method'] = $method;
			$data[$key]['type'] = $type;
			$data[$key]['responce'] = $responce;
			$data[$key]['date'] = $date_time;
			update_option( "piereg_payment_log_option", $data );
		}
		
		function isUserIpsIsBlocked($current_ip, $array_ips)
		{
			$isblocked	= false;			
			foreach( $array_ips as $blockip ) {
				if( strpos($blockip,'/') !== false ) {
					$rangefrom 		= ip2long(substr($blockip, 0, strpos($blockip,"/")));
					$rangesexplode	= explode("/",$blockip);
					
					$arr 			= explode('.', $rangesexplode[0]);
					$ipstart 		= implode('.',	array_slice($arr, 0, 3));
					$rangeto		= ip2long($ipstart . "." . $rangesexplode[1]);
					
					if($current_ip >= $rangefrom && $current_ip <= $rangeto ){
						$isblocked = true;
						break;
					}
					
				} else if( ($current_ip == ip2long($blockip)) && ip2long($blockip)) {
						$isblocked = true;
						break;
				}				
			}
			
			if($isblocked){
				return true;				
			}else{
				return false;	
			}
		}
		
		
		function isUserNameIsBlocked( $current_username, $array_username) {
			$isblocked	= false;
			
			foreach($array_username as $username)
			{
				if(strpos($username,"*") !== false ) 
				{
					$username = str_replace("*","",$username);
					if( strpos($current_username,$username) !== false ) {
						$isblocked = true;
						break;
					}
				
				} else if($username == $current_username) {
					
						$isblocked = true;
						break;
				}
			}
			
			if($isblocked)
				return true;
			else
				return false;
			
		}
		
		function isEmailAddressIsBlocked( $current_emailaddr, $array_email ) {
			$isblocked	= false;
			
			foreach($array_email as $email)
			{
				if(strpos($email,"*") !== false ) 
				{
					$email = str_replace("*","",$email);
					if( strpos($current_emailaddr,$email) !== false ) {
						$isblocked = true;
						break;
					}
				
				} else if($email == $current_emailaddr) {
					
						$isblocked = true;
						break;
				}
			}
			
			if($isblocked)
				return true;
			else
				return false;
		}
		
		function regFormForFreeVers( $isDelete=false ) {
			$fields_id 		= get_option("piereg_form_fields_id");
			$form_on_free	= get_option("piereg_form_free_id");
			$count 			= 0;
			
			if( ( !empty($fields_id) && !$form_on_free ) || $isDelete )
			{
				for( $a=1; $a<=$fields_id; $a++ )
				{
					$option 	= get_option("piereg_form_field_option_".$a);					
					if( !empty($option) && is_array($option) && isset($option['Id']) && !isset($option['IsDeleted']) )
					{	
						$count++;
						if( $count == 1 )
						{
							update_option('piereg_form_free_id', $option['Id']);
							$form_on_free .= $option['Id'];
						}
						break;
					}
				}
			}
			return $form_on_free;			
		}
		
		function getValue( $_is_not_form=false, $_other_values=array() ){
			
			if( is_bool($_is_not_form) && $_is_not_form === true )
			{				
				$value 				= isset($_other_values['_value']) 	? $_other_values['_value'] 	: "";
				$this->type			= isset($_other_values['_type']) 	? $_other_values['_type']	: "";
			}
			else {
				$value 		= $this->stripslashes_deep(get_user_meta($this->user_id, $this->slug,true));  #get_usermeta deprecated
			}
						
			
			if($this->type=="date")
			{
				if( ($this->field['date_type'] == "datepicker") && isset($value['date']) && !isset($value['date']['mm']) ) {
					$val = isset($value['date'][0]) ? $value['date'][0] : "";
					return $val;
				}
				else if(isset($value['date']) && is_array($value['date']))
				{
					if(!isset($value['date']['mm'])) {
						$val = isset($value['date'][0]) ? $value['date'][0] : "";
						return $val;
					}
					
					$val = $this->field['date_format'];
					if( is_bool($_is_not_form) && $_is_not_form === true )
					{
						$val	= "mm/dd/yy";
					}
					
					$mm_val = (!empty($value['date']['mm'])) ? $value['date']['mm'] : "mm";
					$val = str_replace("mm",$mm_val,$val);
					
					$dd_val = (!empty($value['date']['dd'])) ? $value['date']['dd'] : "dd";
					$val = str_replace("dd",$dd_val,$val);
					
					$yy_val = (!empty($value['date']['yy'])) ? $value['date']['yy'] : "yy";
					$val = str_replace("yy",$yy_val,$val);
					
					return 	$val;				
				} 
				
				return $value;			
			}
			else if($this->type=="time")
			{
				//print_r($value); die;
				if(((isset($value['hh']) && $value['hh'] === '') && (isset ($value['mm']) && $value['mm'] === ''))){
					return false;
				}
				if(is_array($value)){
					if($value['hh'] != '')
						$value['hh'] = ($value['hh']);
					if($value['mm'] != '')
						$value['mm'] = ($value['mm']);
						
					
					if ( isset($value['time_format'])  )  $last = array_pop($value);	
					else $last = "";
					
					return implode(" : ",$value) . ' ' . $last;
				}
				return $value;
			}
			else if($this->type=="invitation")
			{
				$value = get_user_meta($this->user_id, "invite_code", true); #get_usermeta deprecated
				
				if(is_array($value))
					return implode(", ",$value);
				else 
					return $value;
			}
			else if($this->type=="list")
			{				
			
				if(!is_array($value))
				return $value;
				$list = "";
				$list = '<table class="piereg_custom_list '.$this->slug.'">';
				for($a = 0 ; $a < sizeof($value) ; $a++)
				{
					if(array_filter($value[$a])){
						$list .= '<tr>';
						$row  = "";
						for($b = 0 ; $b < sizeof($value[$a]) ; $b++)
						{
							$row 	.= $value[$a][$b];
							$list 	.= '<td>'.$value[$a][$b]."</td>";
						}
						if(!empty($row))
						$list .= '</tr>';
					}
				}
				$list .= '</table>';
				$value = $list ;	

			}
			else if($this->type=="multiselect" && $_is_not_form !== true )
			{
				if($value) {
					$list = "<ol>";
					$combined_array = array_combine($this->field['value'],$this->field['display']);	
					
					for($a = 0 ; $a < sizeof($value) ; $a++ )
					{
						if(isset($value[$a]))
						{
							if( $this->field['list_type'] == 'None' )
							{
								$list .= "<li>".$combined_array[$value[$a]]."</li>";
								
							} else {
								
								$list .= "<li>".$value[$a]."</li>";
							
							}
						}
					}	
					$list .= "</ol>";				
				}
				$value = $list;					
			}
			elseif($this->type == "dropdown" && $_is_not_form !== true ){
			
				$combined_array = array_combine($this->field['value'],$this->field['display']);
				$corrected_value = array();
				for($a = 0 ; $a < sizeof($value) ; $a++ )
				{
					if(isset($value[$a]))
					{
						
						if($this->field['list_type']=='None')
						{
							$corrected_value[$a] = $combined_array[$value[$a]];		
						} else {
							
							$corrected_value[$a] = $value[$a];
						
						}
					}					
					
				}
				$value = implode(", ",$corrected_value);
				
			}else if( ($this->type == "checkbox" || $this->type == "radio") && $_is_not_form !== true )
			{
				$combined_array = array_combine($this->field['value'],$this->field['display']);
				$corrected_value = array();
				for($a = 0 ; $a < sizeof($value) ; $a++ )
				{
					if(isset($value[$a]))
						$corrected_value[$a] = $combined_array[$value[$a]];
				}
				
				$value = implode(", ",$corrected_value);
			}
			else if($this->type=="address")
			{
				$results = "";
				if(is_array($value)) {
					$ret = (isset($value[0]) && is_array($value[0])) ? $value[0] : $value; 
					foreach($ret as $key => $val) {
						$results .= ucwords($key) . ' : ' . $val . '<br />';
					}
				}
				$value = $results;
			}
			else if( is_array($value) )
			{				
				$value 	= implode(",", $value);								
			}
			return $value;	
		}	
		
		function checkIfConditionApplied( $field, $metakey=false, $fields_reg_form=false){
			
			if( $fields_reg_form ){
				$this->field	= $field;
				$this->data		= $fields_reg_form;
			}
			
			$current_field			= $this->field;	
			$field_cond_rule 		= $this->field['field_rule_operator'];
			$field_cond_rule_value 	= $this->field['conditional_value'];		
			$field_cond_id 			= $this->field['selected_field'];
				
			// get and create field type applying condtional logics
			$field_conditional 	= $this->data[$field_cond_id];
			$this->type 		= $field_conditional['type'];
			$this->slug 		= $metakey . $this->createFieldName($field_conditional['type']."_".((isset($field_conditional['id']))?$field_conditional['id']:""));
			$field_cond_value 	= "";
			
			if( $fields_reg_form )
			{
				// get value of field ($_POST) applying conditional logics 
				if(isset($field_conditional['id'])) {
					$slug_matched = $this->createFieldName($field_conditional['type']."_".$field_conditional['id']);			
				} else {
					$slug_matched = $this->createFieldName($field_conditional['type']."_");
				}
				
				if($field_conditional['type']=="username" || $field_conditional['type']=="password"){
					  $slug_matched  = $this->createFieldName($field_conditional['type']);
				}
				elseif($field_conditional['type']=="email"){
					  $slug_matched  = $this->createFieldName("e_mail");
				}
				elseif($field_conditional['type']=="name"){
					  $slug_matched  = "first_name";
				}
				
				$field_cond_value =  $_POST[$slug_matched];
				
			} else {
				// get value of field applying conditional logics 
				switch($this->type) 
				{
					case "username":
						$field_cond_value = $this->user->data->user_login;
					break;
					case "email":
						$field_cond_value = $this->user->data->user_email;
					break;
					case "name":
						$this->slug 		= "first_name";
						$field_cond_value 	= $this->getValue();
					break;
					default:
						$field_cond_value 	= $this->getValue($this->type, $this->slug);				
				}	
			}
			
			// check whether condtional logics applying or not 
			$condition = false;
			if($field_cond_value || ($field_cond_rule == "empty" && $field_cond_value == "" ) ) {
				if( $field_cond_rule == "!=" ) {
					if ( $field_cond_value != $field_cond_rule_value ) {					
						$condition = true;
					}
				
				} 
				else if ($field_cond_rule == "==") {
						if ( $field_cond_value == $field_cond_rule_value ) {					
							$condition = true;
						}
				} 
				else if ($field_cond_rule == "empty") {		
						if ( $field_cond_value == "" ) {					
							$condition = true;
						}				
				} 
				else if ($field_cond_rule == "not_empty") {
						if ( $field_cond_value != "" ) {					
							$condition = true;
						}
					
				} 
				else if ($field_cond_rule == ">") {
						if ( $field_cond_value > $field_cond_rule_value ) {					
							$condition = true;
						}
					
				} 
				else if ($field_cond_rule == "<") {
						if ( $field_cond_value < $field_cond_rule_value ) {					
							$condition = true;
						}
				} 
				else if ($field_cond_rule == "contains") {
						if (strpos($field_cond_value, $field_cond_rule_value) !== false) {
							$condition = true;
						}
					
				} 
				else if ($field_cond_rule == "starts_with") {					
						if($field_cond_rule_value === "" || strrpos($field_cond_value, $field_cond_rule_value, -strlen($field_cond_value)) !== FALSE) {
							$condition = true;
						}
						if(substr_compare($field_cond_value, $field_cond_rule_value, 0, strlen($field_cond_rule_value)) === 0) {
							
						}
				} 
				else if ($field_cond_rule == "ends_with") {
						if( $field_cond_rule_value === "" || (($temp = strlen($field_cond_value) - strlen($field_cond_rule_value)) >= 0 && strpos($field_cond_value, $field_cond_rule_value, $temp) !== FALSE) ) {
							$condition = true;
						}						
				} 
				else if ($field_cond_rule == "range") {
						$field_cond_rule_value 		 = explode(",", $field_cond_rule_value);
						$field_cond_rule_value_start = isset($field_cond_rule_value[0])  ? $field_cond_rule_value[0] : "";
						$field_cond_rule_value_end 	 = isset($field_cond_rule_value[1])  ? $field_cond_rule_value[1] : "";
						if( $field_cond_value >= $field_cond_rule_value_start && $field_cond_value <= $field_cond_rule_value_end ) {
							$condition = true;
						}
				}		
			
			}
			
			return $condition;
		}
	}
}	