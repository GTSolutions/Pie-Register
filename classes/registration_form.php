<?php
if( file_exists( dirname(__FILE__) . '/base.php') ) 
	require_once('base.php');

class Registration_form extends PieReg_Base
{
	var $id;
	var $no;
	var $name;
	var $field;
	var $data;
	var $label_alignment;
	var $pages;
	
	var $field_status;
	
	function __construct($form_id = "0")	
	{
		parent::__construct();
		$this->data = $this->getCurrentFields($form_id);
	}
	
	function createFieldName($text)
	{
		return $this->getMetaKey($text);			
	}
	function createFieldID()
	{
		return "field_".$this->field['id'];	
	}
	function getDefaultValue($name="")
	{
		if($name != "")
		{
			$this->name = $name;	
		}
		if(isset($_POST[$this->name]))
		{
			return $_POST[$this->name];	
		}
		return ((isset($this->field['default_value']))?$this->field['default_value']:"");
	}
	
	function addClass($default = "input_fields",$val = array())
	{
		$class = $default." ".(isset($this->field['css'])?$this->field['css']:"");
		if(isset($this->field['required']) && $this->field['required'])
		{
			$val[] = "required";		
		}
		
		if(isset($this->field['length']) && intval($this->field['length']) > 0 )
		{
			$val[] = "maxSize[".intval($this->field['length'])."]";
		}
		if((isset($this->field['validation_rule']) && $this->field['validation_rule']=="number" ) || $this->field['type']=="number")
		{
			$val[] = "custom[number]";		
		}
		else if(isset($this->field['validation_rule']) && $this->field['validation_rule']=="alphanumeric")
		{
			$val[] = "custom[alphanumeric]";		
		}
		else if((isset($this->field['validation_rule']) && $this->field['validation_rule']  =="email" ) || $this->field['type']=="email")
		{
			$val[] = "custom[email]";		
		}
		else if((isset($this->field['validation_rule']) && $this->field['validation_rule']  =="email" ) || $this->field['type']=="name")
		{
			$val[] = "custom[alphabetic]";		
		}
		else if(
				((isset($this->field['validation_rule']) && $this->field['validation_rule']=="website") || $this->field['type']=="website")
				|| (isset($this->field['field_name']) && $this->field['field_name'] == 'url')
			)
		{
			$val[] = "custom[url]";		
		}		
		else if((isset($this->field['validation_rule']) && $this->field['validation_rule']=="standard") || (isset($this->field['phone_format']) && $this->field['phone_format']=="standard" ))
		{
			$val[] = "custom[phone_standard]";		
		}
		else if((isset($this->field['validation_rule']) && $this->field['validation_rule']=="international") || (isset($this->field['phone_format']) && $this->field['phone_format']=="international"))
		{
			$val[] = "custom[phone_international]";		
		}
		else if($this->field['type']=="time")
		{
			$val[] = "custom[number]";	
			$val[] = "minSize[1]";
			$val[] = "maxSize[2]";
			$val[] = "min[0]";
			
			if($this->field['hours']==TRUE)
			{
				if($this->field['time_type']=="12")
				{
					$val[] = "max[12]";
				}
				else
				{
					$val[] = "max[23]";	
				}
			}
			else if($this->field['mins']==TRUE)
			{
				$val[] = "max[59]";	
			}
				
		}
		else if($this->field['type']=="upload" && explode(",",$this->field['file_types']) > 0)
		{
			if(!empty($this->field['file_types']))
			{
				$val[] = "funcCall[checkExtensions]";	
				$val[] = "ext[".str_replace(array(","," "),array("|",""),$this->field['file_types'])."]";
			}
		}
		
		if(sizeof($val) > 0)
		{
			$val = " piereg_validate[".implode(",",$val)."]";
			$class .= $val;	
		}
		
		return $class;	
	}
	
	function addValidation()
	{
		if((isset($this->field['required']) && $this->field['required']) && !empty($this->field['validation_message']))
		{
			$val[] = 'data-errormessage-value-missing="'.$this->field['validation_message'].'"';
		}
		
		if(isset($this->field['validation_rule']))
		{
			if(
				$this->field['validation_rule']=="number" || 
				$this->field['type']=="number" || $this->field['validation_rule']=="alphanumeric" || 
				$this->field['validation_rule']=="email" || $this->field['type']=="email" || 
				$this->field['validation_rule']=="website" || $this->field['type']=="website" || 
				$this->field['type']=="phone" || $this->field['type']=="date")
			{
				$val[] = 'data-errormessage-custom-error="'.$this->field['validation_message'].'"';		
			}		
		}
		else if($this->field['type']=="time")
		{
			$val[] = 'data-errormessage-custom-error="'.$this->field['validation_message'].'"';		
			$val[] = 'data-errormessage-range-underflow="'.$this->field['validation_message'].'"';	
			$val[] = 'data-errormessage-range-overflow="'.$this->field['validation_message'].'"';
		}
		
		if(isset($val) && sizeof($val) > 0)
		{
			return implode(" ",$val);			
		}
	}
	
	
	function validateRegistration($errors)
	{
		if(!is_wp_error($errors))
		$errors = new WP_Error();
		$piereg 	= get_option(OPTION_PIE_REGISTER);
		/*
			*	Sanitizing post data
		*/
		$this->piereg_sanitize_post_data( ( (isset($_POST) && !empty($_POST))?$_POST : array() ) );
		global $wpdb;
		
		do_action("pieregister_registration_validation_before");
		
		$_POST['username'] = preg_replace('/\s+/', '', strtolower($_POST['username']));
		if ( !isset($_POST['username']) && empty( $_POST['username'] ) && !validate_username($_POST['username']) )
		{
			$errors->add( "username" , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_Invalid_Username",__('Invalid Username','piereg' )));
		}
		else if ( username_exists( $_POST['username'] ) )
		{
			$errors->add( "username" , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_Username_already_exists",__('Username already exists','piereg' )));
		}		
		
		$regXemail = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/";
		if ( !isset($_POST['e_mail']) || empty( $_POST['e_mail'] ) || !preg_match($regXemail,$_POST['e_mail']) )
		{
			$errors->add( "email" , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_Invalid_Email_address",__('Invalid E-mail address','piereg' )));
		}
		else if ( email_exists( $_POST['e_mail'] ) )
		{
			$errors->add( "email" , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_Email_address_already_exists",__('E-mail address already exists','piereg' )));
		}
		
		/*
			* To validate the block users by username and client ip address if enable.
		*/
		if( $this->piereg_pro_is_activate )	
		{
			if( isset($piereg['piereg_blk_ip']) && (isset($piereg['enable_blockedips']) && $piereg['enable_blockedips'] == 1) )
			{
				$array_ips 			= array_map( 'trim', explode(PHP_EOL,$piereg['piereg_blk_ip']) );
				$user_ip_address 	= (getenv('HTTP_X_FORWARDED_FOR')) ? getenv('HTTP_X_FORWARDED_FOR') : getenv('REMOTE_ADDR');
				
				if( $this->isUserIpsIsBlocked( ip2long($user_ip_address), $array_ips ) ) 
				{
					$errors->add( "blk_ipaddress" , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_ipaddress_blocked",__('Your IP address has been blocked by administrator.','piereg' )));
				}
			}
			
			if( isset($piereg['piereg_blk_username']) && (isset($piereg['enable_blockedusername']) && $piereg['enable_blockedusername'] == 1) )
			{
				$array_username 	= array_map( 'trim', explode(PHP_EOL,$piereg['piereg_blk_username']) );
				$array_username 	= array_map( 'strtolower', $array_username );
				
				if(	$this->isUserNameIsBlocked(strtolower($_POST['username']),$array_username) )
				{
					$errors->add( "blk_username" , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_username_blocked",__('This user has been blocked by administrator.','piereg' )));
				}
			}
			
			if( isset($piereg['piereg_blk_email']) && (isset($piereg['enable_blockedemail']) && $piereg['enable_blockedemail'] == 1) )
			{
				$array_emailaddr 	= array_map( 'trim', explode(PHP_EOL,$piereg['piereg_blk_email']) );
				$array_emailaddr 	= array_map( 'strtolower', $array_emailaddr );
				
				if(	$this->isEmailAddressIsBlocked($_POST['e_mail'],$array_emailaddr) ) {
					$errors->add( "blk_email" , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_email_blocked",__('This email address has been blocked by administrator.','piereg' )));					
					
				}
				
			}
		}
		/*
			*	Security Enhansment Form Time submission
		*/
		if( isset($piereg['reg_form_submission_time_enable']) && $piereg['reg_form_submission_time_enable'] == 1 )
		{
			if( isset($_POST['prereg_form_submission']) ){
				// If Empty
				if( empty($_POST['prereg_form_submission']) ){
					$errors->add( "piereg_form_time_submission" , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_form_time_submission",__('Spamming not allowed','piereg' )));
				}else{
					$form_submission_time 	= $_POST['prereg_form_submission'];
					$form_current_time		= date_i18n("y-m-d H:i:s");
					$form_target_time 		= date_i18n("y-m-d H:i:s", strtotime("+".intval( $piereg['reg_form_submission_time'] )." seconds",strtotime($form_submission_time)));
					if( $form_current_time < $form_target_time )
						$errors->add( "piereg_form_time_submission" , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_form_time_submission",__('Spamming not allowed','piereg' )));
				}
			}elseif( !isset($_POST['prereg_form_submission']) && $piereg['reg_form_submission_time'] > 0 ){
				$errors->add( "form_time_sibmission" , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_form_time_sibmission",__('Spamming not allowed','piereg' )));
			}
		}
		if(is_array($this->data)){
			 foreach($this->data as $field)
			 {
				
			if(isset($field['id'])) {
				$slug = $this->createFieldName($field['type']."_".$field['id']);			
			} else {
				$slug = $this->createFieldName($field['type']."_");
			}
			$phone_format = "";
			if($field['type']=="username" || $field['type']=="password"){
				  $slug  = $this->createFieldName($field['type']);
			}
			elseif($field['type']=="email"){
				  $slug  = $this->createFieldName("e_mail");
			}
			/*
				*	work just 2way login phone
			*/
			elseif($field['type']=="two_way_login_phone"){
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				$twilio_option = get_option("pie_register_twilio");
				$plugin_status = get_option('piereg_api_manager_addon_Twilio_activated');
				if( is_plugin_active("pie-register-twilio/pie-register-twilio.php") && isset($twilio_option["enable_twilio"]) && $twilio_option["enable_twilio"] == 1 && $this->piereg_pro_is_activate && $plugin_status == "Activated" ){
					$slug  = "piereg_two_way_login_phone";
					$phone_format = "international";
				}
			}
			elseif($field['type']=="phone"){
				$phone_format = ( isset($field['phone_format']) ? $field['phone_format'] : "" );
			}

			$field_name			= ( isset($_POST[$slug]) ? $_POST[$slug] : "");
			$required 			= ( isset($field['required']) ? $field['required'] : "" );
			
			$rule				= ( isset($field['validation_rule']) ? $field['validation_rule'] : "" );
			
			$validation_message	= "";
			
			if(isset($_POST[$slug]) && $_POST[$slug] != "")
			{
					$key = $slug;
					$row = $_POST[$slug];
					$key_id = explode("_",$key);
					
					if($row == "" and $this->data[$field['required']] != "" ){
						$crnt_fld = $this->data[$field['id']];
						$main_fld = $this->data[$field['selected_field']];
						
						$slug = $this->createFieldName($main_fld['type']."_".$main_fld['id']);
						
						$main_field_value = "";
						if($main_fld['type'] == "dropdown")
						{
							$main_field_value = $_POST[$slug][0];
						}
						else
						{
							$main_field_value = $_POST[$slug];
						}
						
						if( ($field['conditional_value'] != "" && $field['field_rule_operator'] != "" ) && $field['conditional_logic'] == "1" && $this->piereg_pro_is_activate )
						{
							if($field['conditional_value'] == $main_field_value)
							{
								 $validation_message	= (!empty($field['validation_message']) ? $field['validation_message'] : $field['label']." ".__("is required.","piereg"));
								
							}
						}
						
				}
			}
			
			/*
				* Validate List
			*/
				if($field['type'] == "list" && $field['required'] != "" )
				{					
						
					$list = $_POST[$field['type'] .'_'.$field['id']];					
					$validation = false; 										
					foreach ($list[0] as $value) {

						if ($value != "") { 
							$validation = true;
						}
					}

					

					if( $validation == false )
					{ 						
						$validation_message	= (!empty($field['validation_message']) ? $field['validation_message'] : $field['label']." ".__("is required.","piereg"));
						$errors->add( $slug , "<strong>". ucwords(__("error","piereg")).":</strong> " .$validation_message );	
						
					}else
					{
						$required 			= "";
					}
				}
			
			
			/*
				*	validate honeypot
			*/
			if($field['type']=="honeypot" && $this->piereg_pro_is_activate ){
				if( isset($_POST["input_field_".$field['id']]) && !empty($_POST["input_field_".$field['id']]) ){
					$validation_message	= (!empty($field['validation_message']) ? $field['validation_message'] : $field['label']." ".__("is required.","piereg"));
				}elseif( !isset($_POST["input_field_".$field['id']]) ){
					$validation_message	= (!empty($field['validation_message']) ? $field['validation_message'] : $field['label']." ".__("is required.","piereg"));
				}else{
					$required = "";
				}
			}
			
			if($field['type']=="two_way_login_phone")
			{
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				$twilio_option = get_option("pie_register_twilio");
				$plugin_status = get_option('piereg_api_manager_addon_Twilio_activated');
				if( is_plugin_active("pie-register-twilio/pie-register-twilio.php") && isset($twilio_option["enable_twilio"]) && $twilio_option["enable_twilio"] == 1 && $this->piereg_pro_is_activate && $plugin_status == "Activated" ){
					$validation_message	= (!empty($field['validation_message']) ? $field['validation_message'] : $field['label']." ".__("is required.","piereg"));
				}else{
					$required = "";
				}
			}
				
			if( $this->piereg_pro_is_activate && ($field['conditional_logic'] == 1 && !is_array($field['selected_field'])) ) {
				
				// If condition applies and field_status == 'show' then field is required, and 
				// if condition applies and field_status == 'hide' then field is not required else required.
				$required = 0; 
				if( $this->checkIfConditionApplied($field, false, $this->data) ){
					if( $field['field_status'] == 1 ) {
						$required = 1;
					}
				} else {
					if( $field['field_status'] == 0 ) {
						$required = 1;
					}
				}
			}
			
			//if( ($validation_message == "" && $required != "" ) && ( ( isset($field['conditional_logic']) && $field['conditional_logic'] == 0) && $this->piereg_pro_is_activate ) )
			if( $validation_message == "" && $required != "" )
			{
				$validation_message	= (!empty($field['validation_message']) ? $field['validation_message'] : $field['label']." ".__("is required.","piereg"));
			}
			
			//Handling File Field
			if($field['type']=="profile_pic")
			{
				$field_name = $_FILES[$slug]['name'];
				if($_FILES[$slug]['name'] != ''){
					$result = $this->piereg_validate_files($_FILES[$slug]['name'],array("gif","GIF","jpeg","JPEG","jpg","JPG","png","PNG","bmp","BMP"));
					if(!$result){
						$errors->add( $slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_Invalid_File_Type_In_Profile_Picture",__('Invalid File Type In Profile Picture.','piereg' )));
					}
				}
			}
			elseif($field['type']=="upload"){
				$field_name = $_FILES[$slug]['name'];
				if($_FILES[$slug]['name'] != '' and $field['file_types'] != ""){
					$filter_array = stripcslashes($field['file_types']);
					$filter_array = explode(",",$filter_array);
					$result = $this->piereg_validate_files($_FILES[$slug]['name'],$filter_array);
					if(!$result){
						$errors->add( $slug , apply_filters("piereg_invalid_file",'<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_Invalid_File_Type",__('Invalid File Type','piereg' ))));
					}
				}
			}
			else if($field['type']=="invitation"  && $piereg["enable_invitation_codes"]=="1")
			{
				$field_name = $code = $_POST['invitation'];
				if($required != "" || $_POST['invitation'] != "")
				{
					$codetable	= $this->codeTable();				
					$codes = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $codetable where BINARY name = %s and status = %d", $code, 1) );
					if(isset($wpdb->last_error) && !empty($wpdb->last_error)){
						$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
					}
					if(is_array($codes)){
						foreach($codes as $c)	
						{
							$times_used = $c->count;
							$usage 		= $c->code_usage;
						}
					}
					if(count($codes) != 1)
					{
						$errors->add( $slug , apply_filters("piereg_invalid_invitaion_code",'<strong>'.ucwords(__('error','piereg')).'</strong>: '.__('Invalid Invitation Code','piereg' )));
					}
					elseif($times_used >= $usage and $usage != 0)
					{
						$errors->add( $slug , apply_filters("piereg_invitaion_code_expired",'<strong>'.ucwords(__('error','piereg')).'</strong>: '.__('Invitation Code has expired','piereg' )));
					}
				}
			}
			else if($field['type']=="captcha"){
				$settings  		=  get_option(OPTION_PIE_REGISTER);
				$privatekey		= $settings['captcha_private'];
				//No Captcha ReCaptcha
				if( !empty($privatekey) ){
					$captcha = "";
					if(isset($_POST['g-recaptcha-response'])){
						$captcha=$_POST['g-recaptcha-response'];
					}
					$response = $this->read_file_from_url("https://www.google.com/recaptcha/api/siteverify?secret=".trim($privatekey)."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
					$resp = json_decode($response,true);
					if($resp['success'] == false){
						$errors->add('recaptcha_mismatch',"<strong>".ucwords(__('error','piereg'))."</strong>: ". apply_filters("piereg_Invalid_Security_Code",__("Invalid Security Code", 'piereg')));
						$error_found++;
					}
				}
				
			
			}
			else if($field['type']=="math_captcha")
			{ 
				if(isset($_POST['piereg_math_captcha']))
				{
					$piereg_cookie_array =  $_COOKIE['piereg_math_captcha_registration'];
					$piereg_cookie_array = explode("|",$piereg_cookie_array);
					$cookie_result1 = (intval(base64_decode($piereg_cookie_array[0])) - 12);
					$cookie_result2 = (intval(base64_decode($piereg_cookie_array[1])) - 786);
					$cookie_result3 = (intval(base64_decode($piereg_cookie_array[2])) + 5);
					$field_name = $_POST['piereg_math_captcha'];
					if( ($cookie_result1 == $cookie_result2) && ($cookie_result3 == $_POST['piereg_math_captcha'])){
					}
					else{
						$errors->add('math_captcha_mismatch',"<strong>".ucwords(__('error','piereg'))."</strong>: ". apply_filters("piereg_Invalid_Security_Code",__("Invalid Math Captcha", 'piereg')));
					}
				}
				elseif(isset($_POST['piereg_math_captcha_widget']))
				{
					$piereg_cookie_array =  $_COOKIE['piereg_math_captcha_registration_widget'];
					$piereg_cookie_array = explode("|",$piereg_cookie_array);
					$cookie_result1 = (intval(base64_decode($piereg_cookie_array[0])) - 12);
					$cookie_result2 = (intval(base64_decode($piereg_cookie_array[1])) - 786);
					$cookie_result3 = (intval(base64_decode($piereg_cookie_array[2])) + 5);
					$field_name = $_POST['piereg_math_captcha_widget'];
					if( ($cookie_result1 == $cookie_result2) && ($cookie_result3 == $_POST['piereg_math_captcha_widget'])){
					}
					else{
						$errors->add('math_captcha_mismatch',"<strong>".ucwords(__('error','piereg'))."</strong>: ". apply_filters("piereg_Invalid_Security_Code",__("Invalid Math Captcha", 'piereg')));
					}
				}
				else{
					$errors->add('math_captcha_mismatch',"<strong>".ucwords(__('error','piereg'))."</strong>: ". apply_filters("piereg_Invalid_Security_Code",__("Invalid Math Captcha", 'piereg')));
				}
			}
			else if($field['type']=="name")
			{
				$field_name	= $_POST["first_name"];	
			}			
			
			
			//if( (!isset($field_name) || empty($field_name)) && ( $required != "" && $field['conditional_logic'] == 0 && $this->piereg_pro_is_activate ))
			if( (!isset($field_name) || empty($field_name)) && $required)
			{
				$errors->add( $slug , "<strong>". ucwords(__("error","piereg")).":</strong> " .$validation_message );				
			} else if((!isset($field_name) || empty($field_name)) && !$required){
				continue;
			}
			else if($rule=="number" && !empty($field_name))
			{
				if(!is_numeric($field_name))
				{
					$errors->add( $slug , "<strong>". __(ucwords("Error"),"piereg").":</strong> ".$field['label'] .apply_filters("piereg_field_must_contain_only_numbers",__(" field must contain only numbers." ,"piereg")));		
				}	
			}
			else if($rule=="alphanumeric" && !empty($field_name))
			{
				if(! preg_match("/^([a-z 0-9])+$/i", $field_name))
				{
					$errors->add( $slug ,"<strong>". __(ucwords("Error"),"piereg").":</strong> ".$field['label'] .apply_filters("piereg_field_may__alpha_numeric_characters",__(" field may only contain alpha-numeric characters."  ,"piereg")));		
				}	
			}	
			else if($rule=="alphabetic" && !empty($field_name))
			{
				if(! preg_match("/^[a-zA-Z ]+$/", $field_name))
				{
					$errors->add( $slug ,"<strong>". __(ucwords("Error"),"piereg").":</strong> ".$field['label'] .apply_filters("piereg_field_may__alphabetic_characters",__(" field may only contains alphabetic letters."  ,"piereg")));		
				}	
			}
			else if($rule=="email" && !empty($field_name))
			{
				$regXemail = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/";
				if( !preg_match($regXemail,$field_name) )
				{
					$errors->add( $slug ,"<strong>". __(ucwords("Error"),"piereg").":</strong> ".$field['label'] .apply_filters("piereg_field_must_contain_valid_email",__(" field must contain a valid email address." ,"piereg")));		
				}	
			}	
			else if($rule=="website" && !empty($field_name) || (isset($field['field_name']) && $field['field_name'] == 'url'))
			{
				if(!filter_var($field_name,FILTER_VALIDATE_URL))
				{
					$errors->add( $slug ,"<strong>". __(ucwords("Error"),"piereg").":</strong> ".$field['label'] .apply_filters("piereg_must_be_a_valid_URL",__(" must be a valid URL." ,"piereg")));
				}	
			}
			if( $phone_format == "international" && isset($_POST[$slug]) && !empty($_POST[$slug]) ){
				if(!is_numeric($field_name)){
					$errors->add( $slug ,"<strong>". __(ucwords("Error"),"piereg").":</strong> ".$field['label'] .apply_filters("piereg_must_be_a_valid_URL",__(" invalid field." ,"piereg")));
				}
			}
			
		 }
		}
		do_action("pieregister_registration_validation_after");
		return $errors;
	}
	function addUser($user_id)
	{
		global $wpdb;
		if(is_array($this->data)){
			foreach($this->data as $field)
			{
				//Some form fields which we can't save like paypal, submit,formdata
				if(!isset($field['meta']))
				{
					if($field['type']=="default")
					{
						$slug 				= $field['field_name'];				
						$value				= $_POST[$slug];
						update_user_meta($user_id, $slug, $value);	
					}
					else if($field['type']=="invitation")
					{
						$prefix		= $wpdb->prefix."pieregister_";
						$codetable	= $prefix."code";				
						$codes 		= $wpdb->query( $wpdb->prepare("update $codetable set count = count + 1 where BINARY name = %s and status = %d", $_POST['invitation'], 1) );
						if(isset($wpdb->last_error) && !empty($wpdb->last_error)){
							$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
						}
						
						update_user_meta($user_id, "invite_code", $_POST['invitation']);			
					}
					else if($field['type']=="name")
					{
						$slug 				= "first_name";				
						$value				= $_POST[$slug];
						update_user_meta($user_id, $slug, $value);	
						
						$slug 				= "last_name";				
						$value				= $_POST[$slug];
						update_user_meta($user_id, $slug, $value);	
					}
					else if($field['type']=="profile_pic")
					{
						$slug 			= $this->createFieldName($field['type']."_".$field['id']);
						$field_name		= isset($_POST[$slug]) ? $_POST[$slug] : "";
						$this->pie_profile_pictures_upload($user_id,$field,$slug);
					}
					else if($field['type']=="upload")
					{
						$slug 			= $this->createFieldName($field['type']."_".$field['id']);
						$field_name		= isset($_POST[$slug]) ? $_POST[$slug] : "";
						$this->pie_upload_files($user_id,$field,$slug);
					}
					else if($field['type']=="two_way_login_phone")
					{
						include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
						$twilio_option = get_option("pie_register_twilio");
						$plugin_status = get_option('piereg_api_manager_addon_Twilio_activated');
						if( is_plugin_active("pie-register-twilio/pie-register-twilio.php") && isset($twilio_option["enable_twilio"]) && $twilio_option["enable_twilio"] == 1 && $this->piereg_pro_is_activate && $plugin_status == "Activated" ){
							$field_name			= (isset($_POST["piereg_two_way_login_phone"]) ? trim($_POST["piereg_two_way_login_phone"]) : "");
							update_user_meta($user_id, "piereg_two_way_login_phone", $field_name);
						}
					}
					else if($field['type']=="pricing")
					{
						$field_name			= (isset($_POST["pricing"]) ? trim($_POST["pricing"]) : "");
						update_user_meta($user_id, "pie_pricing", $field_name);
					}
					else
					{
						if($field['type'] != "honeypot"){
							$slug 				= $this->createFieldName($field['type']."_".$field['id']);
							$field_name			= isset($_POST[$slug]) ? $_POST[$slug] : "";
							update_user_meta($user_id, "pie_".$slug, $field_name);
						}
					}
				}
			}
		}
	}
}

if( file_exists( get_stylesheet_directory().'/pie-register/pie_register_template/registration/registration_form_template.php' ) ){
	require_once( get_stylesheet_directory().'/pie-register/pie_register_template/registration/registration_form_template.php' );
}
else{
	require_once(dirname(dirname(__FILE__)).'/pie_register_template/registration/registration_form_template.php');
}