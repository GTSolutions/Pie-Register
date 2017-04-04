<?php
if( file_exists( dirname(__FILE__) . '/base.php') ) 
	require_once('base.php');

class Edit_form extends PieReg_Base
{
	var $id;
	var $no;
	var $name;
	var $field;	
	var $data;
	var $label_alignment;
	var $user;
	var $user_id;
	var $error;
	var	$pie_success;
	var	$pie_error;
	var	$pie_error_msg;
	var	$pie_success_msg;
	var $pages;
	function __construct($user,$form_id = "default")	
	{
		parent::__construct();
		$this->data = $this->getCurrentFields($form_id);
		$this->user = $user;
		$this->user_id = $user->ID;
		$this->label_alignment = ((isset($this->data['form']['label_alignment']))?$this->data['form']['label_alignment']:"");
	}
	function createFieldName($text)
	{
		return $this->getMetaKey($text);
	}
	function createFieldID()
	{
		return "field_".((isset($this->field['id']))?$this->field['id']:"");
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
		global $wpdb;
		
		$global_options = $this->get_pr_global_options();
		
		/*
			*	Sanitizing post data
		*/
		$this->piereg_sanitize_post_data( ( (isset($_POST) && !empty($_POST))?$_POST : array() ) );
		
		$regXemail = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/";
		if ( empty( $_POST['e_mail'] ) || !preg_match($regXemail,$_POST['e_mail']) )
		{
			$errors->add( "email" , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_invalid_Email_address",__('Invalid E-mail address','piereg' )));		
		}	
		
		if($_POST['password'] != $_POST['confirm_password'] && isset($_POST['password'], $_POST['confirm_password']))
		{
			$errors->add( "password" , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_password_Fields_do_not_macth",__('New and Confirm Password Fields do not macth!','piereg' )));
		}
		
		if(!wp_check_password( $_POST['old_password'], $this->user->data->user_pass, $this->user->ID ) && !empty($_POST['old_password']))
		{
			$errors->add( "password" , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_old_password_Fields_do_not_macth",__('Old Password Field do not macth!','piereg' )));
		}elseif(!empty($_POST['password']) && empty($_POST['old_password']) ){
			$errors->add( "password" , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_old_password_Fields_do_not_macth",__('Invalid Old Password Field!','piereg' )));
		}
		elseif($_POST['old_password'] == $_POST['password'] && !empty($_POST['password']) && !empty($_POST['old_password']))
		{
			$errors->add( "password" , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_old_password_and_new_password_same",__('Old and New passwords are same!','piereg' )));
		}
		 
		
		 foreach($this->data as $field)
		 {
			
			$break = FALSE;
			//Printting Field
			switch($field['type']) 
			{
				case 'form':
				case 'username':
				case 'submit':
				case 'hidden':
				case 'invitation':
				case 'password':
				case 'honeypot':
				{			
					$break = TRUE;			
				}
				break;
			}
			if($break)
			{
				continue;	
			}
			/*
				*	Validate Show Profile
			*/
			if(isset($field['show_in_profile']) && $field['show_in_profile'] == 0)
			{
				continue;	
			}
			
			
			$slug = $this->createFieldName($field['type']."_".$field['id']);	
			if($field['type']=="username" || $field['type']=="password"){
				  $slug  = $this->createFieldName($field['type']);
			}
			else if($field['type']=="name"){
				  $slug  = $this->createFieldName("first_name");
			}
			else if($field['type']=="email"){
				$slug  = $this->createFieldName("e_mail");
				/*
					*	If Email Verification on Then
					*	Add since 2.0.13
				*/
				
				if($this->user->data->user_email != $_POST['e_mail'] && !empty($global_options['email_edit_verification_step']) )
				{
					//Email dosen't exists
					if(!email_exists($_POST['e_mail']))
					{
						/*
							*	Save New Email Address in user meta
						*/
						update_user_meta($this->user->data->ID,"new_email_address",$_POST['e_mail']);
						/*
							*	Generate Key
						*/
						$email_key = md5((uniqid("piereg_").time()));
						/*
							*	Email Key add in array for email template
						*/
						if( intval($global_options['email_edit_verification_step']) == "1"){
							$keys_array = array("reset_email_key"=>$email_key);
							$email_slug = "email_edit_verification";
							$user_email_address = esc_sql($_POST['e_mail']);
							$email_edit_success_msg = "Please follow the link sent to your new Email to verify and make the change applied!";
						}elseif(intval($global_options['email_edit_verification_step']) == "2"){
							$keys_array = array("confirm_current_email_key"=>$email_key);
							$email_slug = "current_email_verification";
							$user_email_address = esc_sql($this->user->data->user_email);
							$email_edit_success_msg = "Please confirm the link sent to your current Email to verify and make the change applied!";
						}
						/*
							*	Email send snipt
						*/
						$subject 		= html_entity_decode($global_options["user_subject_email_{$email_slug}"],ENT_COMPAT,"UTF-8");
						$message_temp = "";
						if($global_options["user_formate_email_{$email_slug}"] == "0"){
							$message_temp	= nl2br(strip_tags($global_options["user_message_email_{$email_slug}"]));
						}else{
							$message_temp	= $global_options["user_message_email_{$email_slug}"];
						}
						
						$message		= $this->filterEmail($message_temp,$this->user->data->user_login, "",false,$keys_array );
						$from_name		= $global_options["user_from_name_{$email_slug}"];
						$from_email		= $global_options["user_from_email_{$email_slug}"];					
						$reply_email 	= $global_options["user_to_email_{$email_slug}"];
						
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
						
						$error_sending_email 	 = false;
						if(!wp_mail($user_email_address, $subject, $message , $headers))
						{
							$error_sending_email = true;
							$errors->add('check-error',apply_filters("piereg_problem_and_the_email_was_probably_not_sent",__("There was a problem and the email was probably not sent.",'piereg')));
							$this->pr_error_log("'The e-mail could not be sent. Possible reason: your host may have disabled the mail() function...'".($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
						}
						
						/*
							*	Update Email Hash Key
						*/
						update_user_meta($this->user->data->ID,"new_email_address_hashed",$email_key);
						$_POST['e_mail'] = $this->user->data->user_email;
						
						if(!$error_sending_email):
							$_POST['success'] = __($email_edit_success_msg,"piereg");
						else:
							$_POST['success'] = "";
						endif;
						
					}else{
						$errors->add( $slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.__('This Email already Exists','piereg') );
					}
				}else{
					$this->user->data->user_email = $_POST['e_mail'];
				}
			}
			/*
				*	work just 2way login phone
			*/
			elseif($field['type']=="two_way_login_phone"){
				$slug  = "piereg_two_way_login_phone";
				$phone_format = "international";
			}
			
			$required 			= (isset($field['required']))?$field['required']:"";
			if($field['type']=="upload"){
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
			elseif($field['type']=="profile_pic")
			{
				$field_name = $_FILES[$slug]['name'];
				if($_FILES[$slug]['name'] != ''){
					$result = $this->piereg_validate_files($_FILES[$slug]['name'],array("gif","GIF","jpeg","JPEG","jpg","JPG","png","PNG","bmp","BMP"));
					if(!$result){
						$errors->add( $slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_Invalid_File_Type_In_Profile_Picture",__('Invalid File Type In Profile Picture.','piereg' )));
					}
				}
			}
			
			else if($field['type']=="invitation"  && $piereg["enable_invitation_codes"]=="1") // AA - dkny
			{
				
				$field_name = $code = $_POST[$slug];
				if($required != "" || $_POST[$slug] != "")
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
			
			/*
				*	Just for two way login
			*/
			elseif($field['type']=="two_way_login_phone")
			{
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				$twilio_option = get_option("pie_register_twilio");
				$plugin_status = get_option('piereg_api_manager_addon_Twilio_activated');
				$pie_register_base = new PieReg_Base();
				if( is_plugin_active("pie-register-twilio/pie-register-twilio.php") && isset($twilio_option["enable_twilio"]) && $twilio_option["enable_twilio"] == 1 && $pie_register_base->piereg_pro_is_activate && $plugin_status == "Activated" ){
					$slug = "piereg_two_way_login_phone";
				}else{
					$required = "";
				}
			}
			$field_name			= ((isset($_POST[$slug]))?$_POST[$slug]:"");
			$rule				= (isset($field['validation_rule']))?$field['validation_rule']:"";
			$validation_message	= (!empty($field['validation_message']) ? $field['validation_message'] : ((isset($field['label']))?$field['label']:"") ." is required.");
			
			
			if( $this->piereg_pro_is_activate && ($field['conditional_logic'] == 1 && !is_array($field['selected_field'])) ) {
				
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
			
			if( (!isset($field_name) || empty($field_name)) && $required)
			{
				if($field['type']=="profile_pic" || $field['type']=="upload"){
					//echo "<pre>"; print_r($_FILES); print_r($field); echo "</pre>";
					$uploaded = get_user_meta($this->user->data->ID , "pie_".$slug, true);
					
					//if(empty($_POST[$slug.'_hidden']))
					if(empty($_FILES[$slug]['name']) && empty($uploaded) )
						$errors->add( $slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.$validation_message );
				}elseif($field['type']=="math_captcha"){
					
				}else{
					$errors->add( $slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.$validation_message );
				}
			}else if((!isset($field_name) || empty($field_name)) && !$required){
				continue;
			}
			else if($rule=="number" && !empty($field_name))
			{
				if(!is_numeric($field_name))
				{
					$errors->add( $slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.$field['label'] .apply_filters("piereg_field_must_contain_only_numbers",__(' field must contain only numbers.','piereg' )));
				}	
			}
			else if($rule=="alphanumeric" && !empty($field_name))
			{
				if(! preg_match("/^([a-z0-9 ])+$/i", $field_name))
				{
					$errors->add( $slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.$field['label'] .apply_filters("piereg_field_may_only_contain_alpha_numeric_characters",__(' field may only contain alpha-numeric characters.','piereg' )));
				}	
			}		
			else if($rule=="alphabetic" && !empty($field_name))
			{
				if(! preg_match("/^[a-zA-Z]+$/", $field_name))
				{
					$errors->add( $slug ,"<strong>". __(ucwords("Error"),"piereg").":</strong> ".$field['label'] .apply_filters("piereg_field_may_only_contain_alphabetic_characters",__(" field may only contains alphabetic letters."  ,"piereg")));		
				}	
			}
			else if($rule=="email" && !empty($field_name))
			{
				$regXemail = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/";
				if( !preg_match($regXemail,$field_name) )
				{
					$errors->add( $slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.$field['label'] .apply_filters("piereg_field_must_contain_a_valid_email_address",__(' field must contain a valid email address.','piereg' )));	
				}	
			}	
			else if( ($rule=="website" && !empty($field_name)) || (isset($field['field_name']) && $field['field_name'] == 'url') )
			{
				if(!filter_var($field_name,FILTER_VALIDATE_URL))
				{
					$errors->add( $slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.$field['label'] .apply_filters("piereg_must_be_a_valid_URL",__(' must be a valid URL.','piereg' )));
				}	
			}
			if( (isset($phone_format) && $phone_format == "international") && isset($_POST[$slug]) && !empty($_POST[$slug]) ){
				if(!is_numeric($field_name)){
					$errors->add( $slug ,"<strong>". __(ucwords("Error"),"piereg").":</strong> ".$field['label'] .apply_filters("piereg_must_be_a_valid_URL",__(" invalid field." ,"piereg")));
				}
			}
		 }			
		return $errors;
	}
	function UpdateUser()
	{
		global $wpdb,$pie_success,$pie_error,$pie_error_msg,$pie_suucess_msg,$errors;
		$errors = new WP_Error();
		//$password = $_POST['password'];
		//print_r($_POST); die;
		foreach($this->data as $field)
		{
			//Some form fields which we can't save like paypal, submit,formdata
			
				if($field['type']=="default")
				{
					$slug 				= $field['field_name'];				
					$value				= ((isset($_POST[$slug]))?$_POST[$slug]:"");
					if(update_user_meta($this->user_id, $slug, $value)) $this->pie_success = 1;
					else
					$this->pie_error = 1;
				}				
				else if($field['type']=="name")
				{
					$slug 				= "first_name";				
					$value				= $_POST[$slug];
					if(update_user_meta($this->user_id, $slug, $value)) $this->pie_success = 1;
					else
					$this->pie_error = 1;	
					
					$slug 				= "last_name";				
					$value				= $_POST[$slug];
					if(update_user_meta($this->user_id, $slug, $value)) $this->pie_success = 1;
					else
					$this->pie_error = 1;	
				}
				else
				{
					$slug 				= $this->createFieldName($field['type']."_".((isset($field['id']))?$field['id']:""));
					
					switch($field['type']) :
										
						case 'text' :
						case 'textarea':
						case 'dropdown':
						case 'multiselect':
						case 'number':
						case 'radio':
						case 'checkbox':
						case 'html':
						case 'address':
						case 'phone':
						case 'invitation':
						case 'date':
						case 'list':
							$field_name			= ((isset($_POST[$slug]))?$_POST[$slug]:"");
							if(update_user_meta($this->user_id, "pie_".$slug, $field_name))
								$this->pie_success = 1;
							else
								$this->pie_error = 1;
							break;
						case 'upload':
							if(isset($_FILES[$slug]['name']) && $_FILES[$slug]['name'] != ''){
								$this->pie_upload_files($this->user_id,$field,$slug);
							}
							break;
						case 'profile_pic':
							if(isset($_FILES[$slug]['name']) && $_FILES[$slug]['name'] != ''){
								$this->pie_profile_pictures_upload($this->user_id,$field,$slug);
							}
							break;
						case 'time':
							/*if($_POST[$slug]['time_format'])
							{
								$_POST[$slug]['hh'] = intval($_POST[$slug]['hh']);
								$num_length = strlen((string)$_POST[$slug]['hh']);
								
								if($num_length == 1)
									$_POST[$slug]['hh'] = "0" . $_POST[$slug]['hh'];
								
								if($_POST[$slug]['hh'] > 12)
									$_POST[$slug]['hh'] = "12";
								
								$_POST[$slug]['mm'] = intval($_POST[$slug]['mm']);
								$num_length = strlen((string)$_POST[$slug]['mm']);
								if($num_length == 1)
									$_POST[$slug]['mm'] = "0" . $_POST[$slug]['mm'];
									
								if($_POST[$slug]['mm'] > 59)
									$_POST[$slug]['mm'] = "59";
									
								$field_name			= $_POST[$slug];
								if(update_user_meta($this->user_id, "pie_".$slug, $field_name))
									$this->pie_success = 1;
								else
									$this->pie_error = 1;
							}else{
								$_POST[$slug]['hh'] = intval($_POST[$slug]['hh']);
								$num_length = strlen((string)$_POST[$slug]['hh']);
								if($num_length == 1)
									$_POST[$slug]['hh'] = "0" . $_POST[$slug]['hh'];
									
								if($_POST[$slug]['hh'] > 23)
									$_POST[$slug]['hh'] = "23";
								
								$_POST[$slug]['mm'] = intval($_POST[$slug]['mm']);
								$num_length = strlen((string)$_POST[$slug]['mm']);
								if($num_length == 1)
									$_POST[$slug]['mm'] = "0" . $_POST[$slug]['mm'];
									
								if($_POST[$slug]['mm'] > 59)
									$_POST[$slug]['mm'] = "59";
								
								$field_name			= $_POST[$slug];
								if(update_user_meta($this->user_id, "pie_".$slug, $field_name))
									$this->pie_success = 1;
								else
									$this->pie_error = 1;
							}*/
							
							if($_POST[$slug]['time_format'])
							{
								$_POST[$slug]['hh'] = sprintf('%02d',intval($_POST[$slug]['hh']));
								if($_POST[$slug]['hh'] > 12)
									$_POST[$slug]['hh'] = "12";
								
								$_POST[$slug]['mm'] = sprintf('%02d',intval($_POST[$slug]['mm']));
								if($_POST[$slug]['mm'] > 59)
									$_POST[$slug]['mm'] = "59";
									
								$field_name			= $_POST[$slug];
								if(update_user_meta($this->user_id, "pie_".$slug, $field_name))
									$this->pie_success = 1;
								else
									$this->pie_error = 1;
							}else{
								$_POST[$slug]['hh'] = sprintf('%02d',intval($_POST[$slug]['hh']));
								if($_POST[$slug]['hh'] > 23)
									$_POST[$slug]['hh'] = "23";
								
								$_POST[$slug]['mm'] = sprintf('%02d',intval($_POST[$slug]['mm']));
								if($_POST[$slug]['mm'] > 59)
									$_POST[$slug]['mm'] = "59";
								
								$field_name			= $_POST[$slug];
								if(update_user_meta($this->user_id, "pie_".$slug, $field_name))
									$this->pie_success = 1;
								else
									$this->pie_error = 1;
							}
							break;
							/*
								*	Just For Two Way Login
							*/
							case 'two_way_login_phone':
								$slug = "piereg_two_way_login_phone";
								$field_name			= $_POST[$slug];
								if(update_user_meta($this->user_id, $slug, $field_name))
									$this->pie_success = 1;
								else
									$this->pie_error = 1;
							break;
					endswitch;
				}
		}
		if($this->pie_error)
			$this->pie_error_msg = __('Something Went Wrong updating Profile fields, please try again!','piereg');
		if($this->pie_success)
			$this->pie_success_msg = __('Your Profile has been updated.','piereg');
	}		
			
}

if( file_exists( get_stylesheet_directory().'/pie-register/pie_register_template/profile_edit/edit_form_template.php' ) ){
	require_once( get_stylesheet_directory().'/pie-register/pie_register_template/profile_edit/edit_form_template.php' );
}
else{
	require_once(dirname(dirname(__FILE__)).'/pie_register_template/profile_edit/edit_form_template.php');
}