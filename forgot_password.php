<?php
if(!class_exists("PieRegister")){
	require_once(PIEREG_DIR_NAME.'/pie-register.php');
}

function pieResetFormOutput($piereg_widget = false){
	$pie_register_base = new PieReg_Base();
	/*
		*	Sanitizing post data
	*/
	
	$pie_register_base->piereg_sanitize_post_data( ( (isset($_POST['is_forgot_form']) && !empty($_POST['is_forgot_form'])) ? $_POST : array() ) );
	$option = get_option(OPTION_PIE_REGISTER);
	$forgot_pass_form = '';
	$forgot_pass_form .= '
	<div class="piereg_entry-content pieregForgotPassword pieregWrapper">
	<div id="piereg_forgotpassword">';
	$warning 	= '<strong>'.ucwords(__("warning","piereg")).'</strong>: '.__("Please enter your username or email address. You will receive a link to create a new password via email.",'piereg');
	$success	= "";
	$error		= array();
	if (isset($_POST['reset_pass']) && trim($_POST['user_login']) != "")
	{
		if(isset($option['piereg_security_attempts_forgot_value']) && $option['piereg_security_attempts_forgot_value'] == 1 && $pie_register_base->piereg_pro_is_activate){
			global $wpdb;
			$table_name = $wpdb->prefix . "pieregister_lockdowns";
			$user_ip 	= $_SERVER['REMOTE_ADDR'];
			$user_id 	= 0;
			$release_time = date_i18n('Y-m-d H:i:s', strtotime( ('+'.intval($option['security_attempts_forgot_time'])." minutes"), strtotime(date("Y-m-d H:i:s"))));
			
			$get_results = $wpdb->get_results($wpdb->prepare("SELECT * FROM `".$table_name."` WHERE `user_ip` = %s AND `attempt_from` = 'is_forgot';",$user_ip));
			
			if(isset($wpdb->last_error) && !empty($wpdb->last_error))
			{
				$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
			}
			
			if( (date_i18n("Y-m-d",strtotime($get_results[0]->release_time)) < date_i18n("Y-m-d")) ){
				piereg_delete_authentication();
				$get_results = $wpdb->get_results($wpdb->prepare("SELECT * FROM `".$table_name."` WHERE `user_ip` = %s AND `attempt_from` = 'is_forgot';",$user_ip));
				if(isset($wpdb->last_error) && !empty($wpdb->last_error))
				{
					$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
				}
			}
			if(!empty($get_results)){
				if( intval($get_results[0]->login_attempt) < intval($option['security_attempts_forgot']) ){
					$login_attempt = intval($get_results[0]->login_attempt) + 1;
					if(!$wpdb->query($wpdb->prepare("UPDATE `".$table_name."` SET `user_id`=%d, `login_attempt`=%d, `release_time`=%s WHERE `user_ip` = %s AND `attempt_from` = 'is_forgot'",$user_id,$login_attempt,$release_time,$user_ip)) ){
						$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
					}
				}else{
					$release_time = $get_results[0]->release_time;
					$current_time = date_i18n("Y-m-d H:i:s");
					if($current_time >= $release_time){
						$release_time = date_i18n('Y-m-d H:i:s', strtotime( ('+'.intval($option['security_attempts_forgot_time'])." minutes"), strtotime(date("Y-m-d H:i:s"))));
						if(!$wpdb->query($wpdb->prepare("UPDATE `".$table_name."` SET `user_id`=%d, `release_time`=%s WHERE `user_ip` = %s AND `attempt_from` = 'is_forgot'",$user_id,$release_time,$user_ip)) ){
							$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
						}
						
					}else{
						
					}
					$error[] = '<strong>'.ucwords(__("Error","piereg")).'</strong>: '.__("We are sorry, but your IP has been blocked due to too many recent failed attempts.",'piereg');
				}
				
			}else{
				if(!$wpdb->query($wpdb->prepare("INSERT INTO `".$table_name."` (`user_id`, `login_attempt`, `attempt_from`, `is_security_captcha`, `attempt_time`, `release_time`, `user_ip`) VALUES (%d,%d,%s,%d,%s,%s,%s);",$user_id,1,'is_forgot',$is_security_captcha,date_i18n("Y-m-d H:i:s"),$release_time,$user_ip)) ){
						$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
					}
				
			}
		}
		
		if(isset($_POST['user_login']) and trim($_POST['user_login']) == ""){
			$error[] = '<strong>'.ucwords(__("error","piereg")).'</strong>: '.__('Invalid Username or Email, try again!','piereg');
		}
		$error_found = 0;
		
		if(isset($option['captcha_in_forgot_value']) && !empty($option['captcha_in_forgot_value'])){
			if($option['capthca_in_forgot_pass'] == 2){
				
				$invalidcaptchaerror = '<strong>'.ucwords(__("error","piereg")).'</strong>: '. apply_filters('piereg_forgot_invalid_captcha_error',__('Invalid Captcha','piereg')); # newlyAddedHookFilter
				if(isset($_POST['piereg_math_captcha_forgot_pass']))
				{
					$piereg_cookie_array =  $_COOKIE['piereg_math_captcha_forgot_password'];
					$piereg_cookie_array = explode("|",$piereg_cookie_array);
					$cookie_result1 = (intval(base64_decode($piereg_cookie_array[0])) - 12);
					$cookie_result2 = (intval(base64_decode($piereg_cookie_array[1])) - 786);
					$cookie_result3 = (intval(base64_decode($piereg_cookie_array[2])) + 5);
					if( ($cookie_result1 == $cookie_result2) && ($cookie_result3 == $_POST['piereg_math_captcha_forgot_pass'])){
					}
					else{
						$error[] = $invalidcaptchaerror;
						$error_found++;
					}
				}
				elseif(isset($_POST['piereg_math_captcha_forgot_pass_widget']))
				{
					$piereg_cookie_array =  $_COOKIE['piereg_math_captcha_forgot_password_widget'];
					$piereg_cookie_array = explode("|",$piereg_cookie_array);
					$cookie_result1 = (intval(base64_decode($piereg_cookie_array[0])) - 12);
					$cookie_result2 = (intval(base64_decode($piereg_cookie_array[1])) - 786);
					$cookie_result3 = (intval(base64_decode($piereg_cookie_array[2])) + 5);
					if( ($cookie_result1 == $cookie_result2) && ($cookie_result3 == $_POST['piereg_math_captcha_forgot_pass_widget'])){
					}
					else{
						$error[] = $invalidcaptchaerror;
						$error_found++;
					}
				}
				else{
					$error[] = $invalidcaptchaerror;
					$error_found++;
				}
			}//Validate New Recaptcha
			elseif($option['capthca_in_forgot_pass'] == 3){
				
				$settings  	=  get_option(OPTION_PIE_REGISTER);
				$privatekey	= $settings['captcha_private'];
				
				$captcha = "";
				if(isset($_POST['g-recaptcha-response'])){
					$captcha=$_POST['g-recaptcha-response'];
				}
				$response = $pie_register_base->read_file_from_url("https://www.google.com/recaptcha/api/siteverify?secret=".trim($privatekey)."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
				$resp = json_decode($response,true);
				if($resp['success'] == false){
					$error[] = '<strong>'.ucwords(__("error","piereg")).'</strong>: '.apply_filters('piereg_forgot_invalid_captcha_error',__('Invalid Security Code','piereg')); # newlyAddedHookFilter
					$error_found++;
				}
			}
		}
		
		if( $error_found == 0 && (isset($error) && count($error) == 0) ){
			global $wpdb,$wp_hasher;
			$error 		= array();
			$username = trim($_POST['user_login']);
			$user_exists = false;
			// First check by username
			if ( username_exists( $username ) ){
				$user_exists = true;
				$user = get_user_by('login', $username);
			}
			// Then, by e-mail address
			elseif( email_exists($username) ){
					$user_exists = true;
					//$user = get_user_by_email($username);
					$user = get_user_by( 'email', $username );
			}
			else{
				$error[] = '<strong>'.ucwords(__("error","piereg")).'</strong>: '.__('Username or Email was not found, try again!','piereg');
			}
			if ($user_exists){
				$user_login = $user->user_login;
				$user_email = $user->user_email;
				$allow = apply_filters( 'allow_password_reset', true, $user->ID );
				
				piereg_delete_authentication();
				
				if($allow){
					
					// Generate something random for a key...
					$key = wp_generate_password( 20, false );
					do_action( 'retrieve_password_key', $user_login, $key );
					
					if ( empty( $wp_hasher ) ) {
						require_once ABSPATH . 'wp-includes/class-phpass.php';
						$wp_hasher = new PasswordHash( 8, true );
					}
										
					$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
					
					// Now insert the new md5 key into the db
					$wpdb->update($wpdb->users, array('user_activation_key' => $hashed), array('user_login' => $user_login));				
					
					$message_temp = "";
					if($option['user_formate_email_forgot_password_notification'] == "0"){
						$message_temp	= nl2br(strip_tags($option['user_message_email_forgot_password_notification']));
					}else{
						$message_temp	= $option['user_message_email_forgot_password_notification'];
					}
					$message		= $pie_register_base->filterEmail($message_temp,$user->user_login, '',$key );
					$from_name		= $option['user_from_name_forgot_password_notification'];
					$from_email		= $option['user_from_email_forgot_password_notification'];					
					$reply_email 	= $option['user_to_email_forgot_password_notification'];
					$subject 		= html_entity_decode($option['user_subject_email_forgot_password_notification'],ENT_COMPAT,"UTF-8");
					//Headers
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
					if(!empty($from_email) && filter_var($from_email,FILTER_VALIDATE_EMAIL))//Validating From
					$headers .= "From: ".$from_name." <".$from_email."> \r\n";
					if($reply_email){
						$headers .= "Reply-To: {$reply_email}\r\n";
						$headers .= "Return-Path: {$from_name}\r\n";
					}else{
						$headers .= "Reply-To: {$from_email}\r\n";
						$headers .= "Return-Path: {$from_email}\r\n";
					}
					//send email meassage
					if (FALSE == wp_mail($user_email, $subject, $message,$headers)){
						$error[] =  '<strong>'.ucwords(__("error","piereg")).'</strong>: '.__('The e-mail could not be sent.','piereg') . "<br />" . __('Possible reason: your host may have disabled the mail() function...','piereg') ;
						$pie_register_base->pr_error_log("'The e-mail could not be sent. Possible reason: your host may have disabled the mail() function...'".($pie_register_base->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
					}
					unset($key);
					unset($hashed);
					unset($_POST['user_login']);
				}else{
					$error[] = apply_filters('piereg_password_reset_not_allowed_text',__("Password reset is not allowed for this user","piereg"));
				}
				
				if (count($error) == 0 )
				{
					PieRegister::set_pr_stats("forgot","used");
					$success =  '<strong>'.ucwords(__("success","piereg")).'</strong>: '.apply_filters("piereg_message_will_be_sent_to_your_email",__('A message will be sent to your email address.','piereg'));
				}	
			}
		}
	}
	
	$forgot_pass_form .='<div id="piereg_login">';
	if ( (isset($error) && is_array($error) && count($error) == 0) && !empty($success) ) {
		$forgot_pass_form .= '<div class="alert alert-successs"><p class="piereg_message">';
		$forgot_pass_form .= $success;
		$forgot_pass_form .= '</p></div>';
	} else if (isset($error) && is_array($error) && count($error) > 0 ) {
		$forgot_pass_form .= '<div class="alert alert-danger"><p class="piereg_login_error">';
		$forgot_pass_form .= $error[0];
		$forgot_pass_form .= '</p></div>';
	} elseif($warning) {
		$forgot_pass_form .= '<div class="alert alert-warning"><p class="piereg_warning">'.$warning.'</p></div>';
	}
	if(file_exists( (get_stylesheet_directory()."/pie-register/pie_register_template/reset_password/reset_password_form_template.php"))){
		require_once(get_stylesheet_directory()."/pie-register/pie_register_template/reset_password/reset_password_form_template.php");
	}
	else{
		require_once(dirname(__FILE__)."/pie_register_template/reset_password/reset_password_form_template.php");
	}

	$reset_form = new Reset_pass_form_template($option);
	$forgot_pass_form .= '<form method="post" action="'.$_SERVER['REQUEST_URI'].'" id="piereg_lostpasswordform">';
		$forgot_pass_form .= $reset_form->add_email_or_username();
		$forgot_pass_form .= '<input type="hidden" value="" name="redirect_to">';
		$forgot_pass_form .= '<input type="hidden" value="1" name="is_forgot_form">';
		global $piereg_math_captcha_forgot_pass,$piereg_math_captcha_forgot_pass_widget;
		
		if($option['capthca_in_forgot_pass'] != 0 && !empty($option['capthca_in_forgot_pass']) && isset($option['captcha_in_forgot_value']) && $option['captcha_in_forgot_value'] == 1){
			if($piereg_math_captcha_forgot_pass == false && $piereg_widget == false)
			{
				if(!empty($option['capthca_in_forgot_pass_label']))
					$forgot_pass_form .= $reset_form->add_capthca_label();
				$forgot_pass_form .= forgot_pass_captcha($option['capthca_in_forgot_pass'],$piereg_widget);
				$piereg_math_captcha_forgot_pass = true;
			}elseif($piereg_math_captcha_forgot_pass_widget == false && $piereg_widget == true && isset($option['captcha_in_forgot_value']) && $option['captcha_in_forgot_value'] == 1){
				if(!empty($option['capthca_in_forgot_pass_label']))
					$forgot_pass_form .= $reset_form->add_capthca_label();
				$forgot_pass_form .= forgot_pass_captcha($option['capthca_in_forgot_pass'],$piereg_widget);
				$piereg_math_captcha_forgot_pass_widget = true;
			}
		}
		
		do_action('pieresetpass');
		$forgot_pass_form .= $reset_form->add_reset_submit();
			
			if(isset($pagenow)){$pagenow;}else{$pagenow="";}
			$forgot_pass_form .= $reset_form->add_register_or_login($pagenow);
		$forgot_pass_form .= '
		<input type="hidden" name="reset_pass" value="1" />
		<input type="hidden" name="user-cookie" value="1" />
	  </form>
	</div>
	</div>
	</div>';
	return $forgot_pass_form;
}

if(!function_exists("forgot_pass_captcha"))
{
	function forgot_pass_captcha($value = 0,$piereg_widget = false){
		$option = get_option(OPTION_PIE_REGISTER);
		if(file_exists( (get_stylesheet_directory()."/pie-register/pie_register_template/reset_password/reset_password_form_template.php"))){
			require_once(get_stylesheet_directory()."/pie-register/pie_register_template/reset_password/reset_password_form_template.php");
		}
		else{
			require_once(dirname(__FILE__)."/pie_register_template/reset_password/reset_password_form_template.php");
		}
		$reset_form = new Reset_pass_form_template($option);
		$output = "";
		//Forcefully override the new captcha
		if($value == 1) $value = 3;
		if($value == 2){ // Math Captcha
			$cap_id = "";
			 if( $piereg_widget ){
				$cap_id = "is_forgot_widget";
				$cookie = "forgot_password_widget";
			 }else{
				$cap_id = "not_forgot_widget";
				$cookie = "forgot_password";
			 }
			$data = "";
			
			$data .='<div class="prMathCaptcha" data-cookiename="'.$cookie.'" id="'.$cap_id.'" style="display:inline-block;">';
			$field_id = "";
			
			$mathcapthca_input = $reset_form->add_mathcapthca_input($piereg_widget);
			$data 	.= $mathcapthca_input['data'];
			$field_id = $mathcapthca_input['field_id'];
			
			
			$data .= '</div>';
			$output = $data;
			 
		}elseif($value == 3 || $value == 1){//New Re-Captcha
			$data = "";
			$settings  	=  get_option(OPTION_PIE_REGISTER);
			$publickey	= $settings['captcha_publc'] ;
			if($publickey)
			{
				$cap_id = "";
				 if( $piereg_widget ){
				 	$cap_id = "is_forgot_widget";
				 }else{
				 	$cap_id = "not_forgot_widget";
				 }
				 
				$data .= '<div class="piereg_recaptcha_widget_div" id="'.$cap_id.'">';
				$data .= '</div>';
			}
			return $data;
		
		}
		return $output;
	}
}
function piereg_delete_authentication(){
	global $wpdb;
	$table_name = $wpdb->prefix . "pieregister_lockdowns";
	$user_ip = $_SERVER['REMOTE_ADDR'];
	$user_ip = esc_sql($user_ip);
	$wpdb->query($wpdb->prepare("DELETE FROM `".$table_name."` WHERE `user_ip` = %s AND `attempt_from` = 'is_forgot'",$user_ip));
	if(isset($wpdb->last_error) && !empty($wpdb->last_error)){
		$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
	}
	return true;
}
?>