<?php
function PR_show_renew_account($is_renew = false,$user_array = array())
{
$piereg = PieReg_Base::get_pr_global_options();
global $errors;

$pr_data = "";
$pr_data = '
<div class="pieregformWrapper pieregWrapper">
<div class="piereg_container">
	<div class="piereg_login_container">
    	<div class="piereg_login_wrapper">
			<style type="text/css">
			.piereg_container .pie_reg_comment{text-align:center;}
			.piereg_container .field_note{font-size:12px; color:#FF0000;}
			.piereg_container .required{color:#FF0000}
			.piereg_container .piereg_entry-content{height:auto;margin: 10px auto;max-width: 793px;width: 100%;}
			.piereg_container ul#pie_register {/*padding: 0;*/list-style: none;/*float: left;*/width: 100%;margin: 0;}
			.piereg_container .fields {width: 100%;padding: 0% 0% 0px 0%;float: left;font-family: arial;color: #262626;font-size: 14px;position: relative;margin-top: 9px;margin-bottom: 8px;}
			.piereg_container .fields .fieldset {padding: 1%;display: inline-block;/*float: left;*/width: 96%;}
			.piereg_container .fields label {cursor:pointer;font-size: 14px;color: #848484;width: 100%;text-transform: capitalize;line-height: normal;width: 29%;float: left;word-break: break-word;}
			.piereg_container .fields .input_fields {border-radius: 3px;border: 1px solid #d5d5d5;padding: 0px 2% 0px 2%;margin-top: 0px;margin-bottom: 0;width: 60%;display: inline-block;color: #848484;}
			.piereg_container .msg_div{margin: 5px 0 5px 0 !important;padding: 12px 12px 12px 44px; !important;border-width: 1px !important;border-style: solid !important;color:#ffffff !important;}
			.piereg_container li{list-style:none;}
			.piereg_container .piereg_inline_block{display:inline-block;width:100%;}
			.piereg_container .pr_renew_headding{float:left;}
			.piereg_container #piereg_loginform,
			.piereg_container #piereg_login{width:100% !important;}
			.piereg_container #pie_register .fields .fieldset select{width:60%;}
			.piereg_login_container .piereg_login_wrapper label{margin:0 10px 0 0;}
			.piereg_container #piereg_login form#piereg_loginform p{margin:0px;}
			</style>
<div class="pieregister_renew_account">';
?>
<?php 	
  //If Registration contanis errors
global $wp_session;

			if(isset($errors->errors['login-error'][0]) > 0)
			{
				$message = $errors->errors['login-error'][0];						  	
			}
			else if (! empty($_GET['action']) )
        	{
          
            if ( 'loggedout' == $_GET['action'] )
                $message = __("You are now logged out.","piereg");
            elseif ( 'recovered' == $_GET['action'] )
                $message = __("Check your e-mail for the confirmation link.","piereg");
			elseif ( 'payment_cancel' == $_GET['action'] )
                $message = __("You have canelled your registration.","piereg");
			elseif ( 'payment_success' == $_GET['action'] )
                $success = __("Thank you for your registration. You will receieve your login credentials soon.","piereg");		
			elseif ( 'activate' == $_GET['action'] )
			{
				$unverified = get_users( array('meta_key'=> 'hash','meta_value' => esc_sql($_GET['activation_key'])) );
				
				if(sizeof($unverified )==1)
				{
					$user_id	= $unverified[0]->ID;
					$user_login = $unverified[0]->user_login; 	
					if($user_login == $_GET['pie_id'])
					{
						update_user_meta( $user_id, 'active', 1);
						$hash = "";
						update_user_meta( $user_id, 'hash', $hash );
						$success = __("Your account is now active","piereg");	
					}
					else
					{
						 $message = __("Invalid activation key.","piereg");	
					}
				}		
				
				 
			}
        }

		if ( !empty($message) )
			$pr_data .= '<p class="msg_div piereg_login_error"> ' . apply_filters('piereg_messages', $message) . "</p>";
		if ( !empty($success) )
			$pr_data .= '<p class="msg_div piereg_message">' . apply_filters('piereg_messages', __($success,"piereg")) . "</p>";
		if($_POST['success'] != "")
			$pr_data .= '<p class="msg_div piereg_message">'.apply_filters('piereg_messages', __($_POST['success'],"piereg")).'</p>';
		elseif($_POST['error'] != "")
			$pr_data .= '<p class="msg_div piereg_login_error">'.apply_filters('piereg_messages', __($_POST['error'],"piereg")).'</p>';
		elseif(isset($errors->errors['renew-account-error']) && $errors->errors['renew-account-error'] != ""){
			$error_msg = "";
			foreach($errors->errors['renew-account-error'] as $renew_error_val){
				$error_msg = $renew_error_val."<br />";
			}
			$pr_data .= '<p class="msg_div piereg_login_error">'.apply_filters('piereg_messages_error', $error_msg).'</p>';
			unset($error_msg);
		}
		else{
			if(isset($piereg['payment_renew_msg']) && !empty($piereg['payment_renew_msg']))
				$pr_data .= '<p class="msg_div piereg_warning">'.apply_filters('piereg_messages_warning', $piereg['payment_renew_msg']).'</p>';
		}
	
	$pr_data .= '<div id="show_pie_register_error_js"></div>';
	$pr_data .= '<div class="piereg_inline_block">';
    $pr_data .= '<h2 class="pr_renew_headding">'.(__("Renew Account","piereg")).'</h2>';
    $pr_data .= '<div style="text-align:right;"><span class="field_note">* '.(__("Required Field(s)","piereg")).'</span></div>';
	$pr_data .= '</div>';
    $pr_data .= '<div id="piereg_login">';
      $pr_data .= '<form method="post" action="" id="piereg_loginform" class="piereg_renew_account_form" name="loginform">
	  <input type="hidden" name="e_mail" value="'.($user_array['email']).'" >
	  <input type="hidden" name="username" value="'.($user_array['username']).'" >
      	<ul id="pie_register">';
			if(!empty($user_array) && $is_renew)
			{
				$pr_data .= '<li class="fields">
					<div class="fieldset">
						<label>'.(__("User Name","piereg")).'</label>
						<span>'.($user_array['username']).'</span>
					</div>
				</li>';
				$pr_data .= '<li class="fields">
					<div class="fieldset">
						<label>'.(__("E-mail","piereg")).'</label>
						<span>'.($user_array['email']).'</span>
					</div>
				</li>';
			}
			else{
				$pr_data .= '<li class="fields">
					<div class="fieldset">
						<label for="x_card_num">'.(__("User Name","piereg")).'</label>
						<input type="text" class="input_fields piereg_validate[required]" id="user_name" name="user_name" autocomplete="off" value="'.((isset($_POST['user_name']))?$_POST['user_name']:"").'" aria-required="true" aria-invalid="false">
						<span class="required">*</span>
					</div>
				</li>';
				$pr_data .= '<li class="fields">
					<div class="fieldset">
						<label for="x_card_num">'.(__("Password","piereg")).'</label>
						<input type="password" class="input_fields piereg_validate[required]" id="u_pass" name="u_pass" autocomplete="off" value="" aria-required="true" aria-invalid="false">
						<span class="required">*</span>
					</div>
				</li>';
			}
			
			$user_data_ = get_user_by( 'login', $user_array['username'] );
			$user_registered_form_id = get_user_meta( $user_data_->data->ID , "user_registered_form_id", true);
			$piereg_form_pricing_fields = get_option( "piereg_form_pricing_fields" );
			
			$allowed_payment_gateways = "";
			foreach($piereg_form_pricing_fields['form_id_'.$user_registered_form_id]['allow_payment_gateways'] as $allowed_payment_gateway_name){
				if( has_filter( 'Add_payment_option_'.$allowed_payment_gateway_name ) )
					$allowed_payment_gateways .= apply_filters( 'Add_payment_option_'.$allowed_payment_gateway_name , intval($piereg_form_pricing_fields['form_id_'.$user_registered_form_id]['field_as']) );
			}
			
			$pr_data .= '<li class="fields">
				<div class="fieldset">
					<label for="x_card_num">'.(__("Select Payment","piereg")).'</label>';
					
					if(intval($piereg_form_pricing_fields['form_id_'.$user_registered_form_id]['field_as']) == 0){
						$pr_data .= '<div class="radio_wrap baqar">';
						$pr_data .= $allowed_payment_gateways;
						$pr_data .= '</div>';
					}else{
						$pr_data .= '<select name="select_payment_method" id="select_payment_method" class="input_fields">';
						$pr_data .= '<option value="">'.__("Select","piereg").'</option>';
						$pr_data .= $allowed_payment_gateways;
						$pr_data .= $send_data."</select>";
					}
			
					
			$pr_data .= '</div>';
			$pr_payment_area = "";
			$pr_payment_area .= apply_filters("get_payment_content_area",$pr_payment_area);
			$pr_data .= $pr_payment_area;
            $pr_data .= '
			</li>
			<li class="fields">
				<p class="submit">
    	          <input type="submit" value="'.(__("Renew Account","piereg")).'" class="button button-primary button-large" id="pie_renew" name="pie_renew">
	            </p>
			</li>
      	</ul>   
      </form>
    </div>
</div>
</div>
</div>
</div>
</div>';

	return $pr_data; //Return Renew Account Form
}
?>