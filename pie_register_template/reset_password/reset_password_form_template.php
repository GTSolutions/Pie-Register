<?php
if(!class_exists("Reset_pass_form_template"))
{
	class Reset_pass_form_template
	{
		var $pr_option;
		function __construct($option)
		{
			$this->pr_option = $option;
		}
		function add_new_confirm_pass(){
			$data  = '<p class="field">';
				$data .= '<label for="pass1">'.__("New password","piereg").'</label>';
				$data .= '<input type="password" name="pass1" id="pass1" class="input input_fields validate[required]" size="20" value="" autocomplete="off">';
			$data .= '</p>';
			$data .= '<p class="field">';
		  	$data .= '<label for="pass2">'.__("Confirm new password","piereg").'</label>';
		  	$data .= '<input type="password" name="pass2" id="pass2" class="input input_fields validate[required,equals[pass1]]" size="20" value="" autocomplete="off">';
			$data .= '</p>';
			return $data;
		}
		function add_submit(){
			$data  = '<div class="pie_submit">';
			$data .= '<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="'.__("Reset Password","piereg").'">';
			$data .= '</div>';
			return $data;
		}
		function add_login_register($pagenow){
			$data  = '<div class="field">';
				$data .= '<p class="nav">';
					$data .= '<a href="'.pie_login_url().'">'.__("Log in","piereg").'</a>';
					$data .= ' | ';
					$data .= '<a href="'.pie_registration_url().'">'.__("Register","piereg").'</a>';
				$data .= '</p>';
			$data .= '</div>';
			if(isset($pagenow) && $pagenow == 'wp-login.php' ){
				$data .= '<div class="backtoblog">';
					$data .= '<a title="'.__("Are you lost?","piereg").'" href="'.get_bloginfo("url").'">&larr; '.__("Back to","piereg").' '.get_bloginfo("name").'</a>';
				$data .= '</div>';
			}
			return $data;
		}
		function add_email_or_username(){
			$data  = '<p>';
			if(isset($this->pr_option['forgot_pass_username_label']) && !empty($this->pr_option['forgot_pass_username_label']))
			{
				$data .= '<label for="user_login">'.((isset($this->pr_option['forgot_pass_username_label']) && !empty($this->pr_option['forgot_pass_username_label']))? $this->pr_option['forgot_pass_username_label']: __("Username or E-mail:","piereg")).'</label>';
			}
		    $data .= '<input type="text" size="20" value="" class="input input_fields validate[required]" id="user_login" name="user_login" placeholder="'.((isset($this->pr_option['forgot_pass_username_placeholder']) && !empty($this->pr_option['forgot_pass_username_placeholder']))? $this->pr_option['forgot_pass_username_placeholder']: "").'">';
			$data .= '</p>';
			return $data;
		}
		function add_capthca_label(){
			$forgot_pass_form  = '<p>';
			$forgot_pass_form .= '<label style="margin-top:0px;">'.$this->pr_option['capthca_in_forgot_pass_label'].'</label>';
			$forgot_pass_form .= '</p>';
			return $forgot_pass_form;
		}
		function add_mathcapthca_input($piereg_widget = false){
			$data = "";
			$field_id = "";
			if($piereg_widget == true){
				$data .= '<div id="pieregister_math_captha_forgot_password_widget" class="piereg_math_captcha"></div>';
				$data .= '<input id="" type="text" class="piereg_math_captcha_input pr_math_captcha_input_field" name="piereg_math_captcha_forgot_pass_widget"/>';
				$field_id = "#pieregister_math_captha_forgot_password_widget";
			}
			else{
				$data .= '<div id="pieregister_math_captha_forgot_password" class="piereg_math_captcha"></div>';
				$data .= '<input id="" type="text" class="piereg_math_captcha_input pr_math_captcha_input_field" name="piereg_math_captcha_forgot_pass"/>';
				$field_id = "#pieregister_math_captha_forgot_password";
			}
			return array("data"=>$data,"field_id"=>$field_id);
		}
		function add_reset_submit(){
			$forgot_pass_form  = '<p class="submit">';
			$forgot_pass_form .= '<input type="submit" value="'.__('Reset my password',"piereg").'" class="button button-primary button-large" id="wp-submit" name="user-submit">';
			$forgot_pass_form .= '</p>';
			return $forgot_pass_form;
		}
		function add_register_or_login($pagenow){
			$forgot_pass_form = "";
			if(isset($pagenow) && $pagenow == 'wp-login.php'){
				$forgot_pass_form  = '<p class="forgot_pass_links">';
				$forgot_pass_form .= '<a href="'.wp_login_url().'">'.__('Log in',"piereg").'</a>';
				$forgot_pass_form .= ' | ';
				$forgot_pass_form .= '<a href="'.wp_registration_url().'">'.__('Register',"piereg").'</a>';
				$forgot_pass_form .= '</p>';
				$forgot_pass_form .= '<p class="forgot_pass_links">';
				$forgot_pass_form .= '<a title="'.__('Are you lost?',"piereg").'" href="'.get_bloginfo("url").'">&larr; '.__('Back to',"piereg").' '.get_bloginfo("name").'</a>';
				$forgot_pass_form .= '</p>';
			}
			
			$forgot_pass_form = apply_filters('pie_login_form_links',$forgot_pass_form); # newlyAddedHookFilter
			return $forgot_pass_form;
		}
	}
}