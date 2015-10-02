<?php
function pieOutputLoginForm($piereg_widget = false){
$users_can_register =  get_option("users_can_register");
$option			= get_option("pie_register_2");
$form_data = "";
$form_data .= '<div class="piereg_container">
<div class="piereg_login_container">
<div class="piereg_login_wrapper">';

  //If Registration contanis errors
global $wp_session,$errors;
$newpasspageLock = 0;

			if(isset($_GET['payment']) && $_GET['payment'] == "success")
			{
				$fields = maybe_unserialize(get_option("pie_fields"));
				$login_success = apply_filters("piereg_success_message",__($fields['submit']['message'],"piereg"));
				unset($fields);
			}elseif(isset($_GET['payment']) && $_GET['payment'] == "cancel"){
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
					if($user_login == $_GET['id'])
					{
						update_user_meta( $user_id, 'active', 1);
						$hash = "";
						update_user_meta( $user_id, 'hash', $hash );
						
						/*************************************/
						/////////// THANK YOU E-MAIL //////////
						
						
						$form 			= new Registration_form();
						$subject 		= html_entity_decode($option['user_subject_email_email_thankyou'],ENT_COMPAT,"UTF-8");
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
		
						wp_mail($user_email, $subject, $message , $headers);
						
						/////////// END THANK YOU E-MAIL //////////
						/*************************************/
						
						$login_success = '<strong>'.ucwords(__("success","piereg")).'</strong>: '.apply_filters("piereg_your_account_is_now_active",__("Your account is now active","piereg"));	
					}
					else
					{
						 $login_error = '<strong>'.ucwords(__("error","piereg")).'</strong>: '.apply_filters("piereg_invalid_activation_key",__("Invalid activation key","piereg"));
					}
				}else{
					$user_name = esc_sql($_GET['id']);
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
						$login_error = '<strong>'.ucwords(__("error","piereg")).'</strong>: '.apply_filters("piereg_invalid_activation_key",__("You are block","piereg"));
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
			$form_data .= '<p class="piereg_login_error"> ' . apply_filters('piereg_messages', $login_error) . "</p>\n";
		
		if ( !empty($login_success) )
			$form_data .= '<p class="piereg_message">' . apply_filters('piereg_messages',$login_success) . "</p>\n";
		
		if ( !empty($login_warning) )
			$form_data .= '<p class="piereg_warning">' . apply_filters('piereg_messages',$login_warning) . "</p>\n";
		
		if(isset($_POST['success']) && $_POST['success'] != "")
			$form_data .= '<p class="piereg_message">'.apply_filters('piereg_messages',__($_POST['success'],"piereg")).'</p>';
		if(isset($_POST['error']) && $_POST['error'] != "")
			$form_data .= '<p class="piereg_login_error">'.apply_filters('piereg_messages',__($_POST['error'],"piereg")).'</p>';	
		
if ( isset($_GET['action']) && ('rp' == $_GET['action'] || 'resetpass' == $_GET['action']) && ($newpasspageLock == 0) ){
	$form_data .= '
	  <form name="resetpassform" class="piereg_resetpassform" action="'.pie_modify_custom_url(pie_login_url(),'action=resetpass&key=' . urlencode( $_GET['key'] ) . '&login=' . urlencode( $_GET['login'] )).'" method="post" autocomplete="off">
	
		<input type="hidden" id="user_login" value="'.esc_attr( $_GET['login'] ).'" autocomplete="off">
		<div class="field">
		  <label for="pass1">'.__("New password","piereg").'</label>
		  <input type="password" name="pass1" id="pass1" class="input validate[required]" size="20" value="" autocomplete="off">
		</div>
		<div class="field">
		  <label for="pass2">'.__("Confirm new password","piereg").'</label>
		  <input type="password" name="pass2" id="pass2" class="input validate[required,equals[pass1]]" size="20" value="" autocomplete="off">
		</div>
		<div class="pie_submit">
		  <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="'.__("Reset Password","piereg").'">
		</div>
		<div class="field">
		 <div class="nav">
		 	<a href="'.pie_login_url().'">'.__("Log in","piereg").'</a>';
	if($users_can_register == 1){
		$form_data	.= '&nbsp;|&nbsp;<a href="'.pie_registration_url().'">'.__("Register","piereg").'</a>';
	}
	$form_data .= '</div>
		</div>
		<div class="backtoblog">
			<a title="'.__("Are you lost?","piereg").'" href="'.get_bloginfo("url").'">&larr; '.__("Back to","piereg").' '.get_bloginfo("name").'</a>
		</div>
	  </form>';
}else{
	$form_data .= '
	<form method="post" action="" class="piereg_loginform" name="loginform">
		<p>';
		
	if(isset($option['login_username_label']) && !empty($option['login_username_label'])){
			$form_data .= '<label for="user_login">'.((isset($option['login_username_label']) && !empty($option['login_username_label']))? __($option['login_username_label'],"piereg") : __("Username","piereg")) .'</label>';
	}
	$user_name_val = ((isset($_POST['log']) && !empty($_POST['log']))?$_POST['log']:"");
	$form_data .= '<input placeholder="'.((isset($option['login_username_placeholder']) && !empty($option['login_username_placeholder']))? __($option['login_username_placeholder'],"piereg") : "").'" type="text" size="20" value="'.$user_name_val.'" class="input validate[required]" id="user_login" name="log">
		</p>
		<p>';
	
	if(isset($option['login_password_label']) && !empty($option['login_password_label'])){
		$form_data .= '<label for="user_pass">'.((isset($option['login_password_label']) && !empty($option['login_password_label']))? __($option['login_password_label'],"piereg") : __("Password","piereg")).'</label>';
	}
	
	$form_data .= '
			<input placeholder="'.((isset($option['login_password_placeholder']) && !empty($option['login_password_placeholder']))? __($option['login_password_placeholder'],"piereg") : "").'" type="password" size="20" value="" class="input validate[required]" id="user_pass" name="pwd">
		</p>';
		
		global $piereg_math_captcha_login,$piereg_math_captcha_login_widget;
		if($option['capthca_in_login'] != 0 && !empty($option['capthca_in_login'])){
			if($piereg_math_captcha_login == false && $piereg_widget == false){
				$form_data  .= '<p>';
				if(!empty($option['capthca_in_login_label']))
					$form_data  .= '<label style="margin-top:0px;">'.$option['capthca_in_login_label'].'</label>';
				
				$form_data  .= login_form_captcha($option['capthca_in_login'],$piereg_widget);
				$form_data  .= '</p>';
				$piereg_math_captcha_login = true;
			}elseif($piereg_math_captcha_login_widget == false && $piereg_widget == true){
				$form_data  .= '<p>';
				if(!empty($option['capthca_in_login_label']))
					$form_data  .= '<label style="margin-top:0px;">'.$option['capthca_in_login_label'].'</label>';
				
				$form_data  .= login_form_captcha($option['capthca_in_login'],$piereg_widget);
				$form_data  .= '</p>';
				$piereg_math_captcha_login_widget = true;
			}
		}
		//if(!is_page()) {
			$form_data .= '
			<p class="forgetmenot">
				<label for="rememberme">
					<input type="checkbox" value="forever" id="rememberme" name="rememberme"> '.__("Remember Me","piereg").'
				</label>
			</p>';
		//}
		$form_data .= '
		<p class="submit">
			<input type="submit" value="'.__("Log In","piereg").'" class="button button-primary button-large" id="wp-submit" name="wp-submit">
			<input type="hidden" value="'.admin_url().'" name="redirect_to">
			<input type="hidden" value="1" name="testcookie">
		</p>';
		
		//if(!is_page() ) {
			$form_data .= '<p id="nav">';
			if($users_can_register == 1){		
				$form_data .= '<a href="'.pie_registration_url().'">'.__("Register","piereg").'</a>&nbsp;<a style="cursor:default;text-decoration:none;" href="javascript:;">&nbsp;|&nbsp;</a>&nbsp;';
			}
			$form_data .= '<a title="'.__("Password Lost and Found","piereg").'" href="'.pie_lostpassword_url().'">'.__("Lost your password?","piereg").'</a> </p>';
		//} ?>
	
		<?php if(isset($pagenow) && $pagenow == 'wp-login.php' ){
					$form_data .= '
					<p id="backtoblog"><a title="'.__("Are you lost?","piereg").'" href="'.bloginfo("url").'">&larr;'.__(" Back to","piereg").' '.get_bloginfo("name").'</a></p>';
			} 
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
		$output = "";
		if($value == 1){//Re-Captcha
			$data = "";
			$settings  	=  get_option("pie_register_2");
			$publickey		= $settings['captcha_publc'] ;
			
			if($publickey)
			{
				$captcha_skin = (isset($settings['piereg_recapthca_skin_login']) && !empty($settings['piereg_recapthca_skin_login']))?$settings['piereg_recapthca_skin_login']:"red";
				$data .= '<script type="text/javascript">
					 var RecaptchaOptions = {
						theme : "'.$captcha_skin.'"
					 };
				 </script>';
				$data .= '<div id="recaptcha_widget_div">';
					require_once(PIEREG_DIR_NAME.'/recaptchalib.php');
				$data .= recaptcha_get_html($publickey);
				$data .= '</div>';
			}
			return $data;
		
		}elseif($value == 2){ // Math Captcha
			$operator = rand(0,1);
			////1 for add(+)
			////0 for subtract(-)
			$result = 0;
			if($operator == 1){	
				$start = rand(1,9);
				$end = rand(5,20);
				$result = $start + $end;
				$operator = "+";
			}
			else{
				$start = rand(50,30);
				$end = rand(5,20);
				$result = $start - $end;
				$operator = "-";
			}
			$result1 = $result + 12;
			$result2 = $result + 786;
			$result3 = $result - 5;
			$result1 = base64_encode($result1);
			$result2 = base64_encode($result2);
			$result3 = base64_encode($result3);
			//print_r($_COOKIE['piereg_math_captcha_registration']);
			$data = "";
			$data .='<div style="display:inline-block;">
			<script type="text/javascript">';
			if($piereg_widget == true){
				/*$data .= 'document.cookie= "piereg_math_captcha_Login_form_widget="+dummy_array;';*/
				$data .= 'document.cookie= "piereg_math_captcha_Login_form_widget='.$result1."|".$result2."|".$result3.'";';
			}
			else{
				/*$data .= 'document.cookie= "piereg_math_captcha_Login_form="+dummy_array;';*/
				$data .= 'document.cookie= "piereg_math_captcha_Login_form='.$result1."|".$result2."|".$result3.'";';
			}
			$data .= '</script>';
			
			
			
			//$data .= '<div id="pieregister_math_captha_login_form" class="piereg_math_captcha"></div>';//canvase div for math captcha display
			//$data .= '<input id="" type="text" style="width:auto;margin: 6px 0 0 6px;" name="piereg_math_captcha_login"/>';
			
			$field_id = "";
			if($piereg_widget == true){
				$data .= '<div id="pieregister_math_captha_login_form_widget" class="piereg_math_captcha"></div>';
				$data .= '<input id="" type="text" class="piereg_math_captcha_input" name="piereg_math_captcha_login_widget"/>';
				$field_id = "#pieregister_math_captha_login_form_widget";
			}
			else{
				$data .= '<div id="pieregister_math_captha_login_form" class="piereg_math_captcha"></div>';
				$data .= '<input id="" type="text" style="width:auto;margin-top:2px;" name="piereg_math_captcha_login"/>';
				$field_id = "#pieregister_math_captha_login_form";
			}
			
			
			$image_name = rand(0,10);
			$color[0] = 'rgba(0, 0, 0, 0.6)';
			$color[1] = 'rgba(153, 31, 0, 0.9)';
			$color[2] = 'rgba(64, 171, 229,0.8)';
			$color[3] = 'rgba(0, 61, 21, 0.8)';
			$color[4] = 'rgba(0, 0, 204, 0.7)';
			$color[5] = 'rgba(0, 0, 0, 0.5)';
			$color[6] = 'rgba(198, 81, 209, 1.0)';
			$color[7] = 'rgba(0, 0, 999, 0.5)';
			$color[8] = 'rgba(0, 0, 0, 0.5)';
			$color[9] = 'rgba(0, 0, 0, 0.5)';
			$color[10] = 'rgba(255, 63, 143, 0.9)';
			
			$data .= '
			 <script type="text/javascript">
				jQuery("'.$field_id.'").css({
					"background" : "url('.plugins_url('pie-register').'/images/math_captcha/'.$image_name.'.png)",
					"color"		 : "'.$color[$image_name].'"
				});
				jQuery("'.$field_id.'").html("'.$start." ".$operator." ".$end . ' = ");
			 </script>
			 </div>';
			 
			 $output = $data;
			 
		}
		return $output;
	}
}