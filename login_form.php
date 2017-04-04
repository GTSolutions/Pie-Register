<?php
if(!isset($pagenow)){
	global $pagenow;
}

if(!class_exists("PieRegister")){
	require_once(PIEREG_DIR_NAME.'/pie-register.php');
}

function pieOutputLoginForm($piereg_widget = false){
if(!isset($pagenow)){
	global $pagenow;
}

$pie_register_base = new PieReg_Base();
$option			= get_option(OPTION_PIE_REGISTER);
$form_data = "";
$form_data .= '<div class="piereg_container">
<div class="piereg_login_container pieregWrapper">
<div class="piereg_login_wrapper">';
//If Registration contanis errors
global $wp_session,$errors;
$newpasspageLock = 0;

			if(isset($_GET['payment']) && $_GET['payment'] == "success")
			{
				$fields = maybe_unserialize(get_option("pie_fields"));
				$login_success = apply_filters("piereg_success_message",__( $option['payment_success_msg'], "piereg" ));
				unset($fields);
			}elseif(isset($_GET['payment']) && $_GET['payment'] == "cancel"){
				# noutusing
				/******************************************************/
				/*$user_id 		= intval(base64_decode($_GET['pay_id']));
				$user_data		= get_userdata($user_id);
				if(is_object($user_data)){
					$form 			= new Registration_form();
					$option 		= get_option( 'pie_register_2' );
					$subject 		= html_entity_decode($option['user_subject_email_payment_faild'],ENT_COMPAT,"UTF-8");
					$message_temp = "";
					if($option['user_formate_email_payment_faild'] == "0"){
						$message_temp	= nl2br(strip_tags($option['user_message_email_payment_faild']));
					}else{
						$message_temp	= $option['user_message_email_payment_faild'];
					}
					$message		= $form->filterEmail($message_temp,$user_data, "" );
					$from_name		= $option['user_from_name_payment_faild'];
					$from_email		= $option['user_from_email_payment_faild'];
					$reply_email 	= $option['user_to_email_payment_faild'];
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
	
					wp_mail($user_data->user_email, $subject, $message , $headers);
					unset($user_data);
				}*/
				/******************************************************/

				$login_error = apply_filters("piereg_cancled_message",__("You canceled your payment.","piereg"));
			}
			if(isset($errors->errors['login-error'][0]) > 0)
			{
				$login_error = apply_filters("piereg_login_error",__($errors->errors['login-error'][0],"piereg"));
			}
			elseif( (isset($_GET['pr_key']) && isset($_GET['pr_invalid_username'])) && ($_GET['pr_key'] != "" && $_GET['pr_invalid_username'] != "") && ( isset($_REQUEST['action']) && $_REQUEST['action'] != 'pie_login_sms' ) )
			{
				$pr_error_message = base64_decode(trim($_GET['pr_key']));
				if(!empty($pr_error_message))
					$login_error = apply_filters("piereg_login_after_registration_error",__($pr_error_message,"piereg"));
				else
					$login_error = apply_filters("piereg_login_after_registration_error",__("Invalid username","piereg"));
			}
			else if (! empty($_GET['action']) )
        	{
            if ( 'loggedout' == $_GET['action'] )
                $login_warning = '<strong>'.ucwords(__("warning","piereg")).'</strong>: '.apply_filters("piereg_now_logout",__("You are now logged out.","piereg"));

            elseif ( 'recovered' == $_GET['action'] )
                $login_success = '<strong>'.ucwords(__("success","piereg")).'</strong>: '.apply_filters("piereg_check_yor_emailconfrm_link",__("Check your e-mail for the confirmation link.","piereg"));

			elseif ( 'payment_cancel' == $_GET['action'] )
                $login_warning = '<strong>'.ucwords(__("warning","piereg")).'</strong>: '.apply_filters("piereg_canelled_your_registration",__("You have canelled your registration.","piereg"));

			elseif ( 'payment_success' == $_GET['action'] )
                $login_success = '<strong>'.ucwords(__("success","piereg")).'</strong>: '.apply_filters("piereg_thank_you_for_registration",__("Thank you for your registration. You will receieve your login credentials soon.","piereg"));		

			elseif ( 'activate' == $_GET['action'] )
			{
				$unverified = get_users(array('meta_key'=> 'hash','meta_value' => $_GET['activation_key']));
				if(sizeof($unverified )==1)
				{
					$user_id	= $unverified[0]->ID;
					$user_login = $unverified[0]->user_login;
					$user_email = $unverified[0]->user_email;
					if($user_login == $_GET['pie_id'])
					{
						do_action( "piereg_action_hook_before_user_activate", $user_id, $user_login, $user_email ); # newlyAddedHookFilter
						update_user_meta( $user_id, 'active', 1);
						
						/*************************************/
						/////////// THANK YOU E-MAIL //////////
						$form 			= new Registration_form();
						$subject 		= html_entity_decode($option['user_subject_email_email_thankyou'],ENT_COMPAT,"UTF-8");;
						$message_temp = "";
						if($option['user_formate_email_email_thankyou'] == "0"){
							$message_temp	= nl2br(strip_tags($option['user_message_email_email_thankyou']));
						}else{
							$message_temp	= $option['user_message_email_email_thankyou'];
						}
						$message		= $form->filterEmail($message_temp,$user_email);
						$from_name		= $option['user_from_name_email_thankyou'];
						$from_email		= $option['user_from_email_email_thankyou'];
						$reply_email 	= $option['user_to_email_email_thankyou'];
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
						if(!wp_mail($user_email, $subject, $message , $headers)){
							$form->pr_error_log("'The e-mail could not be sent. Possible reason: your host may have disabled the mail() function...'".(PieRegister::get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
						}
						/*************************************/
						$login_success = '<strong>'.ucwords(__("success","piereg")).'</strong>: '.apply_filters("piereg_your_account_is_now_active",__("Your account is now active","piereg"));	
					}
					else
					{
							 $login_error = '<strong>'.ucwords(__("error","piereg")).'</strong>: '.apply_filters("piereg_invalid_activation_key",__("Invalid activation key","piereg"));

					}	
				}else{
					$user_name = esc_sql($_GET['pie_id']);
					$user = get_userdatabylogin($user_name);
					if($user){
						$user_meta = get_user_meta( $user->ID, 'active');
						if(isset($user_meta[0]) && $user_meta[0] == 1){
							$login_warning = '<strong>'.ucwords(__("warning","piereg")).'</strong>: '.apply_filters("piereg_canelled_your_registration",__("You are already activate","piereg"));
							unset($user_meta);
							unset($user_name);
							unset($user);
						}
						else{
							$login_error = '<strong>'.ucwords(__("error","piereg")).'</strong>: '.apply_filters("piereg_invalid_activation_key",__("Invalid activation key","piereg"));
						}
					}
					else{
						$login_error = '<strong>'.ucwords(__("error","piereg")).'</strong>: '.apply_filters("piereg_invalid_activation_key",__("Invalid activation key","piereg"));
					}
				}
			}
			elseif ( 'resetpass' == $_GET['action'] || 'rp' == $_GET['action'] ){
				$user = check_password_reset_key($_GET['key'], $_GET['login']);
				if ( is_wp_error($user) ) {
					if ( $user->get_error_code() === 'expired_key' )
						$login_error = '<strong>'.ucwords(__("error","piereg")).'</strong>: '.apply_filters("piereg_you_key_has_been_expired",__("You key has been expired, please reset password again!","piereg").' <a href="'.pie_lostpassword_url().'" title="'.__("Password Lost and Found","piereg").'">'.__("Lost your password?","piereg").'</a>');
					else
						$login_error = '<strong>'.ucwords(__("error","piereg")).'</strong>: '.apply_filters("piereg_this_reset_key_invalid_or_no_longer_exists",__("This Reset key is invalid or no longer exists. Please reset password again!","piereg").' <a href="'.pie_lostpassword_url().'" title="'.__("Password Lost and Found","piereg").'">'.__("Lost your password?","piereg").'</a>');
						$newpasspageLock = 1;
				}else{
					$login_warning = '<strong>'.ucwords(__("warning","piereg")).'</strong>: '.__('Enter your new password below.',"piereg");
				}
				if(isset($_POST['pass1'])){
					$errors = new WP_Error();
					if(isset($_POST['pass1']) && trim($_POST['pass1']) == ""){
						$login_error =  '<strong>'.ucwords(__("error","piereg")).'</strong>: '.apply_filters("piereg_invalid_password",__( 'Invalid Password',"piereg" ));
						$errors->add( 'password_reset_mismatch',$login_error );
					}elseif ( isset($_POST['pass1']) and strlen($_POST['pass1']) < 7  ){
						$login_error =  '<strong>'.ucwords(__("error","piereg")).'</strong>: '.apply_filters("piereg_minimum_8_characters_required_in_password",__( 'Minimum 8 characters required in password',"piereg" ));
						$errors->add( 'password_reset_mismatch',$login_error );
					}elseif ( isset($_POST['pass1']) && $_POST['pass1'] != $_POST['pass2'] ){
						$login_error =  '<strong>'.ucwords(__("error","piereg")).'</strong>: '.apply_filters("piereg_the_passwords_do_not_match",__( 'The passwords do not match',"piereg"));
						$errors->add( 'password_reset_mismatch',$login_error );
					}
					do_action( 'validate_password_reset', $errors, $user );
					if ( ( ! $errors->get_error_code() ) && isset( $_POST['pass1'] ) && !empty( $_POST['pass1'] ) ) {
						reset_password($user, $_POST['pass1']);
						$newpasspageLock = 1;
						$login_warning = '';
						$login_error = '';
						$login_success = '<strong>'.ucwords(__("success","piereg")).'</strong>: '.apply_filters("piereg_your_password_has_been_reset",__( 'Your password has been reset.' , "piereg"));
					}
				}
			}
        }
		if(trim($wp_session['message']) != "" )
		{
			$form_data .= '<p class="piereg_login_error"> ' . apply_filters('piereg_messages',__($wp_session['message'],"piereg")) . "</p>";
			$wp_session['message'] = "";
		}
		if ( !empty($login_error) )
			$form_data .= '<p class="piereg_login_error"> ' . apply_filters('piereg_messages', $login_error) . "</p>";

		if ( !empty($login_success) )
			$form_data .= '<p class="piereg_message">' . apply_filters('piereg_messages',$login_success) . "</p>";

		if ( !empty($login_warning) )
			$form_data .= '<p class="piereg_warning">' . apply_filters('piereg_messages',$login_warning) . "</p>";

		if(isset($_POST['success']) && $_POST['success'] != "")
			$form_data .= '<p class="piereg_message">'.apply_filters('piereg_messages',__($_POST['success'],"piereg")).'</p>';

		if(isset($_POST['error']) && $_POST['error'] != "")
			$form_data .= '<p class="piereg_login_error">'.apply_filters('piereg_messages',__($_POST['error'],"piereg")).'</p>';


if ( isset($_GET['action']) && ('rp' == $_GET['action'] || 'resetpass' == $_GET['action']) && ($newpasspageLock == 0) ){
	
	if(file_exists( (get_stylesheet_directory()."/pie-register/pie_register_template/reset_password/reset_password_form_template.php"))){
		require_once(get_stylesheet_directory()."/pie-register/pie_register_template/reset_password/reset_password_form_template.php");
	}
	elseif(file_exists(dirname(__FILE__)."/pie_register_template/reset_password/reset_password_form_template.php")){
		require_once(dirname(__FILE__)."/pie_register_template/reset_password/reset_password_form_template.php");
	}
	$r_pass_form = new Reset_pass_form_template($option);
	$form_data .= '
	  <form name="resetpassform" class="piereg_resetpassform" action="'.pie_modify_custom_url(pie_login_url(),'action=resetpass&key=' . urlencode( $_GET['key'] ) . '&login=' . urlencode( $_GET['login'] )).'" method="post" autocomplete="off">
		<input type="hidden" id="user_login" value="'.esc_attr( $_GET['login'] ).'" autocomplete="off">';
			$form_data .= $r_pass_form->add_new_confirm_pass();
			$form_data .= $r_pass_form->add_submit();
			$form_data .= $r_pass_form->add_login_register($pagenow);
		$form_data .= '</form>';
}
elseif ( isset($_GET['action'],$_GET['reference_key'],$_GET['security_token']) && $_REQUEST['action'] == 'pie_login_sms' ){
	$form_data .= apply_filters("piereg_login_sms_form",$piereg_widget);
}
else{
	
		if(file_exists( (get_stylesheet_directory()."/pie-register/pie_register_template/login/login_form_template.php"))){
			require_once(get_stylesheet_directory()."/pie-register/pie_register_template/login/login_form_template.php");
		}
		elseif(file_exists(dirname(__FILE__)."/pie_register_template/login/login_form_template.php")){
			require_once(dirname(__FILE__)."/pie_register_template/login/login_form_template.php");
		}

		$login_form = new Login_form_template($option);

		$form_data .= '
		<form method="post" class="piereg_loginform" name="loginform">';
			
			$form_data .= $login_form->add_username();
			
			$form_data .= $login_form->add_password();
			
			
			global $piereg_math_captcha_login,$piereg_math_captcha_login_widget,$wpdb;
			$table_name = $wpdb->prefix . "pieregister_lockdowns";
			$user_ip = $_SERVER['REMOTE_ADDR'];
			
			$get_results = $wpdb->get_results($wpdb->prepare("SELECT * FROM `".$table_name."` WHERE `user_ip` = %s;",$user_ip));
			
			if(isset($wpdb->last_error) && !empty($wpdb->last_error))
			{
				PieRegister::pr_error_log($wpdb->last_error.(PieRegister::get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
			}
			
			$is_security_captcha = false;
			$security_captcha_login = 0;
			if(isset($get_results[0]->is_security_captcha) && $get_results[0]->is_security_captcha == 2){
				$is_security_captcha = true;
				$security_captcha_login = $option['security_captcha_login'];
			}
			$capthca_in_login = $option['capthca_in_login'];
			if($is_security_captcha){
				$capthca_in_login = $security_captcha_login;
			}
			
			if($capthca_in_login != 0 && !empty($capthca_in_login) && $option['captcha_in_login_value'] == 1){
				$attempts = false;
				if($pie_register_base->piereg_pro_is_activate){
					if($option['captcha_in_login_attempts'] > 0){
						if( count($get_results) > 0 && $option['captcha_in_login_attempts'] <= $get_results[0]->login_attempt){
							$attempts = true;
						}
					}else{
						$attempts = true;
					}
				}else{
					$attempts = true;
				}
				/*if( isset($option['captcha_in_login_attempts']) )
				{
					
					if( $option['captcha_in_login_attempts'] > 0 && $pie_register_base->piereg_pro_is_activate ){
						if( count($get_results) > 0 && $option['captcha_in_login_attempts'] <= $get_results[0]->login_attempt){
							$attempts = true;
						}
					}elseif( $option['captcha_in_login_attempts'] > 0 && !$pie_register_base->piereg_pro_is_activate ){
						$attempts = true;
					}elseif( $option['captcha_in_login_attempts'] == 0 && $pie_register_base->piereg_pro_is_activate || !$pie_register_base->piereg_pro_is_activate ){
						$attempts = true;
					}
				}*/
				
				if( $attempts ){
					if($piereg_math_captcha_login == false && $piereg_widget == false){
						if(!empty($option['capthca_in_login_label']))
							$form_data .= $login_form->add_capthca_label();
						
						$form_data  .= login_form_captcha($capthca_in_login,$piereg_widget);
						$piereg_math_captcha_login = true;
					}elseif($piereg_math_captcha_login_widget == false && $piereg_widget == true){
						if(!empty($option['capthca_in_login_label']))
							$form_data .= $login_form->add_capthca_label();
						
						$form_data  .= login_form_captcha($capthca_in_login,$piereg_widget);
						$piereg_math_captcha_login_widget = true;
					}
				}
			}
	
			$form_data .= $login_form->add_rememberme();
			$form_data .= $login_form->add_submit();
			$form_data .= $login_form->add_register_lostpassword($pagenow);
			
		$form_data .= '
		</form>';
	
}

$form_data .='</div>
</div></div>';
return $form_data;
}

if(!function_exists("login_form_captcha"))
{
	function login_form_captcha($value = 0,$piereg_widget = false){
		if(file_exists( (get_stylesheet_directory()."/pie-register/pie_register_template/login/login_form_template.php"))){
			require_once(get_stylesheet_directory()."/pie-register/pie_register_template/login/login_form_template.php");
		}
		elseif(file_exists(dirname(__FILE__)."/pie_register_template/login/login_form_template.php")){
			require_once(dirname(__FILE__)."/pie_register_template/login/login_form_template.php");
		}
		
		if(!isset($option)){
			$option = get_option(OPTION_PIE_REGISTER);
		}
		$login_form = new Login_form_template($option);
		$output = "";
		if($value == 2){ // Math Captcha
			$cap_id = "";
			if( $piereg_widget ){
				$cap_id = "is_login_widget";
				$cookie = 'Login_form_widget';
			}else{
				$cap_id = "not_login_widget";
				$cookie = 'Login_form';
			}
			
			$data = "";
			$data .='<div class="prMathCaptcha" data-cookiename="'.$cookie.'" id="'.$cap_id.'" style="display:inline-block;">';
			
			$field_id = "";
			$math_captcha_field = $login_form->add_mathcaptcha_input($piereg_widget);
			$data .=  $math_captcha_field['data'];
			$field_id = $math_captcha_field['field_id'];
			$data .= '</div>';
			$output = $data;
			 
		}elseif($value == 1 || $value == 3){//Re-Captcha
			$data = "";
			$settings  	=  get_option(OPTION_PIE_REGISTER);
			$publickey	= $settings['captcha_publc'] ;
			
			if($publickey)
			{
				$cap_id = "";
				 if( $piereg_widget ){
				 	$cap_id = "is_widget";
				 }else{
				 	$cap_id = "not_widget";
				 }
				$data .= '<div class="piereg_recaptcha_widget_div" id="'.$cap_id.'">';
				$data .= '</div>';
			}
			return $data;
		
		}
		
		return $output;
	}
}

function update_user_meta_hash() {
	$activation_key = isset($_GET['activation_key']) ? $_GET['activation_key'] : "";
    $unverified = get_users(array('meta_key'=> 'hash','meta_value' => $activation_key));
    if(sizeof($unverified )==1)
    {
        $user_id	= $unverified[0]->ID;
        $user_login = $unverified[0]->user_login;
        if($user_login == $_GET['pie_id'])
        {
            $hash = "";
            update_user_meta( $user_id, 'hash', $hash );
        }
    }
}
add_action('wp_footer','update_user_meta_hash');