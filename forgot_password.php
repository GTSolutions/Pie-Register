<?php
function pieResetFormOutput($piereg_widget = false){
	$pie_register_base = new PieReg_Base();
	/*
		*	Sanitizing post data
	*/
	$pie_register_base->piereg_sanitize_post_data( ( (isset($_POST) && !empty($_POST))?$_POST : array() ) );
	
	$option = get_option('pie_register_2');
	$forgot_pass_form = '';
	$forgot_pass_form .= '
	<div class="piereg_entry-content pieregForgotPassword">
	<div id="piereg_forgotpassword">';
	
	$warning 	= '<strong>'.ucwords(__("warning","piereg")).'</strong>: '.__("Please enter your username or email address. You will receive a link to create a new password via email.",'piereg');
	$success	= "";
	if(isset($_POST['user_login']) and trim($_POST['user_login']) == ""){
		$error[] = '<strong>'.ucwords(__("error","piereg")).'</strong>: '.__('Invalid Username or Email, try again!','piereg');
	}elseif (isset($_POST['reset_pass']))
	{
		$error_found = 0;
		if($option['capthca_in_forgot_pass'] == 1){
			$settings  		=  get_option("pie_register_2");
			$privatekey		=  $settings['captcha_private'] ;
			if($privatekey){
				require_once(PIEREG_DIR_NAME.'/recaptchalib.php');
				$resp = recaptcha_check_answer ($privatekey,$_SERVER["REMOTE_ADDR"],$_POST["recaptcha_challenge_field"],$_POST["recaptcha_response_field"]);
				if (!$resp->is_valid) {
					$error[] = '<strong>'.ucwords(__("error","piereg")).'</strong>: '.__('Invalid Security Code','piereg');
					$error_found++;
				}
			}
			
			
		}elseif($option['capthca_in_forgot_pass'] == 2){
			
			if(isset($_POST['piereg_math_captcha_forgot_pass']))
			{
				$piereg_cookie_array =  $_COOKIE['piereg_math_captcha_forgot_password'];
				/*$piereg_cookie_array = explode(",",$piereg_cookie_array);*/
				$piereg_cookie_array = explode("|",$piereg_cookie_array);
				$cookie_result1 = (intval(base64_decode($piereg_cookie_array[0])) - 12);
				$cookie_result2 = (intval(base64_decode($piereg_cookie_array[1])) - 786);
				$cookie_result3 = (intval(base64_decode($piereg_cookie_array[2])) + 5);
				if( ($cookie_result1 == $cookie_result2) && ($cookie_result3 == $_POST['piereg_math_captcha_forgot_pass'])){
				}
				else{
					$error[] = '<strong>'.ucwords(__("error","piereg")).'</strong>: '.__('Invalid Captcha','piereg');
					$error_found++;
				}
			}
			elseif(isset($_POST['piereg_math_captcha_forgot_pass_widget']))
			{
				$piereg_cookie_array =  $_COOKIE['piereg_math_captcha_forgot_password_widget'];
				/*$piereg_cookie_array = explode(",",$piereg_cookie_array);*/
				$piereg_cookie_array = explode("|",$piereg_cookie_array);
				$cookie_result1 = (intval(base64_decode($piereg_cookie_array[0])) - 12);
				$cookie_result2 = (intval(base64_decode($piereg_cookie_array[1])) - 786);
				$cookie_result3 = (intval(base64_decode($piereg_cookie_array[2])) + 5);
				if( ($cookie_result1 == $cookie_result2) && ($cookie_result3 == $_POST['piereg_math_captcha_forgot_pass_widget'])){
				}
				else{
					$error[] = '<strong>'.ucwords(__("error","piereg")).'</strong>: '.__('Invalid Captcha','piereg');
					$error_found++;
				}
			}
			else{
				$error[] = '<strong>'.ucwords(__("error","piereg")).'</strong>: '.__('Invalid Captcha','piereg');
				$error_found++;
			}
		}
		
		if( $error_found == 0 ){
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
					$user = get_user_by_email($username);
			}
			else{
				$error[] = '<strong>'.ucwords(__("error","piereg")).'</strong>: '.__('Username or Email was not found, try again!','piereg');
			}
			/*
				*	If User Exist then
			*/

			if ($user_exists){
				
				$user_login = $user->user_login;
				$user_email = $user->user_email;
		
				$allow = apply_filters( 'allow_password_reset', true, $user->ID );
				if($allow){
					//Generate something random for key...
					$key = wp_generate_password( 20, false );
					
					//let other plugins perform action on this hook
					do_action( 'retrieve_password_key', $user_login, $key );
					
					//Generate something random for a hash...
					if ( empty( $wp_hasher ) ) {
						require_once ABSPATH . 'wp-includes/class-phpass.php';
						$wp_hasher = new PasswordHash( 8, true );
					}
					
					//$hashed = $wp_hasher->HashPassword( $key );
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
						$error[] =  '<strong>'.ucwords(__("error","piereg")).'</strong>: '.__('The e-mail could not be sent.','piereg') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function...','piereg') ;
					}
					
					unset($key);
					unset($hashed);
					unset($_POST['user_login']);
				}else{
					$error[] = apply_filters('piereg_password_reset_not_allowed_text',__("Password reset is not allowed for this user","piereg"));
				}
				
				
				
				
				/*$message = __('Someone has asked to reset the password for the following site and username.','piereg') . "\r\n\r\n";
				$message .= get_option('siteurl') . "\r\n\r\n";
				$message .= sprintf(__('Username:','piereg')." %s ", $user_login) . "\r\n\r\n";
				$message .= __('To reset your password visit the following address, otherwise just ignore this email and nothing will happen.','piereg') . "\r\n\r\n";
			   $message .= network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . "&redirect_to=".urlencode(get_option('siteurl'))."\r\n";*/
			 
				if (count($error) == 0 )
				{
					$success =  '<strong>'.ucwords(__("success","piereg")).'</strong>: '.apply_filters("piereg_message_will_be_sent_to_your_email",__('A message will be sent to your email address.','piereg'));
				}	
			}
		}
		
	}
	
	
	$forgot_pass_form .='<div id="piereg_login">';
		if (isset($error) && is_array($error) && count($error) == 0 ) {	  
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
	$forgot_pass_form .= '
	  <form method="post" action="'.$_SERVER['REQUEST_URI'].'" id="piereg_lostpasswordform">
		<p>';
		if(isset($option['forgot_pass_username_label']) && !empty($option['forgot_pass_username_label']))
		{
			$forgot_pass_form .= '<label for="user_login">'.((isset($option['forgot_pass_username_label']) && !empty($option['forgot_pass_username_label']))? __($option['forgot_pass_username_label'],"piereg"): __("Username or E-mail:","piereg")).'</label>';
		}
		  
    $forgot_pass_form .= '<input type="text" size="20" value="" class="input validate[required]" id="user_login" name="user_login" style="margin : 10px 0px;" placeholder="'.((isset($option['forgot_pass_username_placeholder']) && !empty($option['forgot_pass_username_placeholder']))? $option['forgot_pass_username_placeholder']: "").'">
		</p>
		<input type="hidden" value="" name="redirect_to">';
		global $piereg_math_captcha_forgot_pass,$piereg_math_captcha_forgot_pass_widget;
		if($option['capthca_in_forgot_pass'] != 0 && !empty($option['capthca_in_forgot_pass'])){
			if($piereg_math_captcha_forgot_pass == false && $piereg_widget == false)
			{
				$forgot_pass_form .= '<p>';
				if(!empty($option['capthca_in_forgot_pass_label']))
				$forgot_pass_form .= '<label style="margin-top:0px;">'.$option['capthca_in_forgot_pass_label'].'</label>';
				$forgot_pass_form .= forgot_pass_captcha($option['capthca_in_forgot_pass'],$piereg_widget);
				$forgot_pass_form .= '</p>';
				$piereg_math_captcha_forgot_pass = true;
			}elseif($piereg_math_captcha_forgot_pass_widget == false && $piereg_widget == true){
				$forgot_pass_form .= '<p>';
				if(!empty($option['capthca_in_forgot_pass_label']))
				$forgot_pass_form .= '<label style="margin-top:0px;">'.$option['capthca_in_forgot_pass_label'].'</label>';
				$forgot_pass_form .= forgot_pass_captcha($option['capthca_in_forgot_pass'],$piereg_widget);
				$forgot_pass_form .= '</p>';
				$piereg_math_captcha_forgot_pass_widget = true;
			}
		}
		
		$forgot_pass_form .= '<p class="submit">';
		  do_action('pieresetpass');
		  $forgot_pass_form .= '
		  <input type="submit" value="'.__('Reset my password',"piereg").'" class="button button-primary button-large" id="wp-submit" name="user-submit">
		</p>';
		
		//if(!is_page()) {
		if(isset($pagenow) && $pagenow == 'wp-login.php' ){
			$forgot_pass_form .= '<p class="forgot_pass_links"> <a href="file://///192.168.14.2/projects/baqar/test_wp_plugin/wp-content/plugins/pie-register/'.wp_login_url().'">'.__('Log in',"piereg").'</a> | <a href="file://///192.168.14.2/projects/baqar/test_wp_plugin/wp-content/plugins/pie-register/'.wp_registration_url().'">'.__('Register',"piereg").'</a> </p>
			<p class="forgot_pass_links"><a title="'.__('Are you lost?',"piereg").'" href="file://///192.168.14.2/projects/baqar/test_wp_plugin/wp-content/plugins/pie-register/'.get_bloginfo("url").'">&larr; '.__('Back to',"piereg").' '.get_bloginfo("name").'</a></p>';
		}
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
		$output = "";
		if($value == 1){//Re-Captcha
			$data = "";
			$settings  	=  get_option("pie_register_2");
			$publickey		= $settings['captcha_publc'] ;
			 
			if($publickey)
			{
				$data .= '<div style="float: left;">';
				$captcha_skin = (isset($settings['piereg_recapthca_skin_forgot_pass']) && !empty($settings['piereg_recapthca_skin_forgot_pass']))?$settings['piereg_recapthca_skin_forgot_pass']:"red";
				$data .= '<script type="text/javascript">
					 var RecaptchaOptions = {
						theme : "'.$captcha_skin.'"
					 };
				 </script>';
				$data .= '<div id="recaptcha_widget_div">';
					require_once(PIEREG_DIR_NAME.'/recaptchalib.php');
				$data .= recaptcha_get_html($publickey);
				$data .= '</div>';
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
				/*$data .= 'document.cookie= "piereg_math_captcha_forgot_password_widget="+dummy_array;';*/
				$data .= 'document.cookie= "piereg_math_captcha_forgot_password_widget='.$result1."|".$result2."|".$result3.'";';
			}
			else{
				/*$data .= 'document.cookie= "piereg_math_captcha_forgot_password="+dummy_array;';*/
				$data .= 'document.cookie= "piereg_math_captcha_forgot_password='.$result1."|".$result2."|".$result3.'";';
			}
			$data .= '</script>';
			$field_id = "";
			if($piereg_widget == true){
				$data .= '<div id="pieregister_math_captha_forgot_password_widget" class="piereg_math_captcha"></div>';
				$data .= '<input id="" type="text" class="piereg_math_captcha_input" name="piereg_math_captcha_forgot_pass_widget"/>';
				$field_id = "#pieregister_math_captha_forgot_password_widget";
			}
			else{
				$data .= '<div id="pieregister_math_captha_forgot_password" class="piereg_math_captcha"></div>';
				$data .= '<input id="" type="text" class="piereg_math_captcha_input" name="piereg_math_captcha_forgot_pass"/>';
				$field_id = "#pieregister_math_captha_forgot_password";
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
					"background" : "url('.plugins_url('/images/math_captcha/'.$image_name.'.png',__FILE__).')",
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