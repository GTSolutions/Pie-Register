<?php
if(!class_exists("Login_form_template"))
{
	class Login_form_template
	{
		var $pr_option;
		function __construct($option)
		{
			$this->pr_option = $option;
		}
		
		function add_username(){
			$form_data = '<p>';
			if(isset($this->pr_option['login_username_label']) && !empty($this->pr_option['login_username_label'])){
					$form_data .= '<label for="user_login">'.((isset($this->pr_option['login_username_label']) && !empty($this->pr_option['login_username_label']))? __($this->pr_option['login_username_label'],"piereg") : __("Username","piereg")) .'</label>';
			}
			$user_name_val = ((isset($_POST['log']) && !empty($_POST['log']))?$_POST['log']:"");
			$form_data .= '<input placeholder="'.((isset($this->pr_option['login_username_placeholder']) && !empty($this->pr_option['login_username_placeholder']))? __($this->pr_option['login_username_placeholder'],"piereg") : "").'" type="text" value="'.$user_name_val.'" class="input input_fields" id="user_login" name="log">';
			$form_data .= '</p>';
			return $form_data;
		}
		function add_password(){
			
			$form_data = '<p>';
			if(isset($this->pr_option['login_password_label']) && !empty($this->pr_option['login_password_label'])){
				$form_data .= '<label for="user_pass">'.((isset($this->pr_option['login_password_label']) && !empty($this->pr_option['login_password_label']))? __($this->pr_option['login_password_label'],"piereg") : __("Password","piereg")).'</label>';
			}
			$form_data .= '<input placeholder="'.((isset($this->pr_option['login_password_placeholder']) && !empty($this->pr_option['login_password_placeholder']))? __($this->pr_option['login_password_placeholder'],"piereg") : "").'" type="password" value="" class="input input_fields" id="user_pass" name="pwd">';
			$form_data .= '</p>';
			return $form_data;
		}
		function add_rememberme(){
			$form_data = '<p class="forgetmenot">';
				$form_data .= '<label for="rememberme">';
					$form_data .= '<input type="checkbox" value="forever" id="rememberme" name="rememberme">'.__("Remember Me","piereg");
				$form_data .= '</label>';
			$form_data .= '</p>';
			return $form_data;
		}
		function add_submit(){
			$form_data = '<p class="submit">';
				$form_data .= '<input type="submit" value="'.__("Log In","piereg").'" class="button button-primary button-large" id="wp-submit" name="wp-submit">';
				$form_data .= '<input type="hidden" value="'.admin_url().'" name="redirect_to">';
				$form_data .= '<input type="hidden" value="1" name="testcookie">';
			$form_data .= '</p>';
			return $form_data;
		}
		function add_register_lostpassword($pagenow){
			$form_data = '<p id="nav">';
				/* Anyone can register */
				$classPieRegister = new PieRegister();
				if($classPieRegister->is_anyone_can_register()){
					$form_data .= '<a href="'.pie_registration_url().'">'.__("Register","piereg").'</a>';
					$form_data .= '<a style="cursor:default;text-decoration:none;" href="javascript:;"> | </a>';
				}
				$form_data .= '<a title="'.__("Password Lost and Found","piereg").'" href="'.pie_lostpassword_url().'">'.__("Lost your password?","piereg").'</a>';
			$form_data .= '</p>';
			if(isset($pagenow) && $pagenow == 'wp-login.php' ){
				$form_data .= '<p id="backtoblog">';
					$form_data .= '<a title="'.__("Are you lost?","piereg").'" href="'.get_bloginfo("url").'">&larr;'.__(" Back to ".get_bloginfo("name"),"piereg").'</a>';
				$form_data .= '</p>';
			}
			
			$form_data = apply_filters('pie_forgotpassword_form_links',$form_data);
			
			return $form_data;
		}
		function add_mathcaptcha_input($piereg_widget = false){
			$data = "";
			$field_id = "";
			if($piereg_widget == true){
				$data .= '<div id="pieregister_math_captha_login_form_widget" class="piereg_math_captcha"></div>';
				$data .= '<input id="" type="text" class="piereg_math_captcha_input pr_math_captcha_input_field" name="piereg_math_captcha_login_widget"/>';
				$field_id = "#pieregister_math_captha_login_form_widget";
			}
			else{
				$data .= '<div id="pieregister_math_captha_login_form" class="piereg_math_captcha"></div>';
				$data .= '<input id="" type="text" class="piereg_math_captcha_input pr_math_captcha_input_field" name="piereg_math_captcha_login"/>';
				$field_id = "#pieregister_math_captha_login_form";
			}
			return array("data" => $data,"field_id" => $field_id);
		}
		function add_capthca_label(){
			$form_data  = '<p>';
			$form_data .= '<label style="margin-top:0px;">'.$this->pr_option['capthca_in_login_label'].'</label>';
			$form_data .= '</p>';
			return $form_data;
		}
	}
}