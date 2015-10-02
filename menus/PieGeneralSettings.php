<script type="text/javascript">
function validateSettings()
{
	if(document.getElementById("block_wp_login_yes").checked && document.getElementById("alternate_login").value == "-1" )
	{
		alert("Please select an alternate login page.");
		return false;	
	}
	if(document.getElementById("block_wp_login_yes").checked && document.getElementById("alternate_register").value == "-1" )
	{
		alert("Please select an alternate register page.");
		return false;	
	}
	if(document.getElementById("block_wp_login_yes").checked && document.getElementById("alternate_forgotpass").value == "-1" )
	{
		alert("Please select an alternate forgot password page.");
		return false;	
	}
	if(piereg("#piereg_reCAPTCHA_Private_Key").val() != "" || piereg("#piereg_reCAPTCHA_Public_Key").val()){
		/*var patt1 = /(?=^.{40,40}$)[0-9a-zA-Z_-]/;*/
		var patt1 = /[0-9a-zA-Z_-]{40}/;
		var re_captcha_scroll_top = piereg("#piereg_reCAPTCHA_Public_Key").offset();
		if(re_captcha_scroll_top.top > 0)
			re_captcha_scroll_top.top = re_captcha_scroll_top.top - 100;
		
		var is_error = false;
		if(!piereg("#piereg_reCAPTCHA_Public_Key").val().match(patt1)){
			piereg("#piereg_reCAPTCHA_Public_Key").css({"color":"red"});
			piereg("html, body").animate({scrollTop: re_captcha_scroll_top.top }, '500');
			piereg("#piereg_reCAPTCHA_Public_Key_error").show();
			is_error = true;
		}
		if(!piereg("#piereg_reCAPTCHA_Private_Key").val().match(patt1)){
			piereg("#piereg_reCAPTCHA_Private_Key").css({"color":"red"});
			piereg("html, body").animate({scrollTop: re_captcha_scroll_top.top }, '500');
			piereg("#piereg_reCAPTCHA_Public_Key_error").show();
			is_error = true;
		}
		
		if(is_error){
			return false;
		}
		
		
	}
	return true;	
}
var piereg = jQuery.noConflict();
piereg(document).ready(function(){
	piereg( document ).tooltip({
		track: true
		/*hide: { effect: "explode", duration: 1000000 }*/
	});
});
 
</script>
<?php
$piereg = get_option( 'pie_register_2' );

if(isset( $_POST['notice'] ) && !empty($_POST['notice']) ){
	echo '<div id="message" class="updated fade"><p><strong>' . $_POST['notice'] . '</strong></p></div>';
}
elseif(isset( $_POST['license_success'] ) && !empty($_POST['license_success']) ){
	echo '<div id="message" class="updated fade"><p><strong>' . $_POST['license_success'] . '.</strong></p></div>';
}
elseif(isset( $_POST['error'] ) && !empty($_POST['error']) ){
	echo '<div id="error" class="error fade"><p><strong>' . $_POST['error'] . '.</strong></p></div>';
}
?>
<div id="container"  class="pieregister-admin">
  <div class="right_section">
    <div class="settings">
      <h2><?php _e("Settings",'piereg') ?></h2>
		<form method="post" action="" onsubmit="return validateSettings();">
        <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_general_settings_nonce','piereg_general_settings_nonce'); ?>
        
        
        
        
        
        <h3><?php _e("General Settings",'piereg') ?></h3>
        <div class="fields">
          <label><?php _e("Display Hints",'piereg') ?></label>
          <div class="radio_fields">
            <input type="radio" value="1" name="display_hints" id="display_hints_yes" <?php echo ($piereg['display_hints']=="1")?'checked="checked"':''?> />
            <label for="display_hints_yes"><?php _e("Yes",'piereg') ?></label>
            <input type="radio" value="0" name="display_hints" id="display_hints_no" <?php echo ($piereg['display_hints']=="0")?'checked="checked"':''?> />
            <label for="display_hints_no"><?php _e("No",'piereg') ?></label>
            </div>
            <span class="quotation"><?php _e("Set this to Yes if you would like to see the Tips on Form Editor Page .",'piereg') ?></span>
          </div>
         
       <fieldset class="piereg_fieldset_area">
    		<legend><?php _e("URL Settings",'piereg') ?></legend>
          
             <div class="fields">
              <label for="alternate_login"><?php _e("Login Page",'piereg') ?></label>
             
                <?php  $args =  array("show_option_no_change"=>"None","id"=>"alternate_login","name"=>"alternate_login","selected"=>$piereg['alternate_login']);      
                wp_dropdown_pages( $args ); ?>
            
               
              <span class="quotation"><?php _e("This page must contain the Pie Register Login form short code.",'piereg') ?></span> 
            </div> 
            
             <div class="fields">
              <label for="alternate_login"><?php _e("Registration Page",'piereg') ?></label>
             
                <?php  $args =  array("show_option_no_change"=>"None","id"=>"alternate_register","name"=>"alternate_register","selected"=>$piereg['alternate_register']);         
                wp_dropdown_pages( $args ); ?>
            
               
              <span class="quotation"><?php _e("This page must contain the Pie Register Registration form short code.",'piereg') ?></span> 
            </div> 
            
             <div class="fields">
              <label for="alternate_forgotpass"><?php _e("Forgot Password Page",'piereg') ?></label>
             
                <?php  $args =  array("show_option_no_change"=>"None","id"=>"alternate_forgotpass","name"=>"alternate_forgotpass","selected"=>$piereg['alternate_forgotpass']);         
                wp_dropdown_pages( $args ); ?>
            
               
              <span class="quotation"><?php _e("This page must contain the Pie Register Forgot Password form short code.",'piereg') ?></span> 
            </div>
            <div class="fields">
              <label for="alternate_profilepage"><?php _e("Profile Page",'piereg') ?></label>
             
                <?php  $args =  array("show_option_no_change"=>"None","id"=>"alternate_profilepage","name"=>"alternate_profilepage","selected"=>$piereg['alternate_profilepage']);         
                wp_dropdown_pages( $args ); ?>
            
               
              <span class="quotation"><?php _e("This page must contain the Pie Register Forgot Password form short code.",'piereg') ?></span> 
            </div>
            
            
            <div class="fields">
              <label for="after_login"><?php _e("After Sign-in Page",'piereg') ?></label>
             
                <?php  $args =  array("show_option_no_change"=>"Default","id"=>"after_login","name"=>"after_login","selected"=>$piereg['after_login']);         
                wp_dropdown_pages( $args ); ?>
              <span class="quotation"><?php _e("Subscriber level users will redirect to this page after signing in.",'piereg') ?></span> 
            </div>
        </fieldset>
        
        
        <fieldset class="piereg_fieldset_area">
    		<legend><?php _e("After Logout",'piereg') ?></legend>
            
            <div class="fields">
                <label for="alternate_logout_url"><?php _e("After Logout URL",'piereg') ?></label>
                <input type="url" name="alternate_logout_url" id="alternate_logout_url" value="<?php echo $piereg['alternate_logout_url']; ?>" class="input_fields" />
                
                <span class="quotation"><?php _e("After logout will redirect to this url after Logout.",'piereg') ?></span> 
            </div>
            
            <div class="fields">
                <center><strong><?php _e("OR","piereg"); ?></strong></center>
            </div>
            
            <div class="fields">
                <label for="alternate_logout"><?php _e("After Logout Page",'piereg') ?></label>
                <?php  $args =  array("show_option_no_change"=>"None","id"=>"alternate_logout","name"=>"alternate_logout","selected"=>$piereg['alternate_logout']);
                wp_dropdown_pages( $args ); ?>
                <span class="quotation"><?php _e("After logout will redirect to this page after Logout page.",'piereg') ?></span> 
            </div>
        </fieldset>
        
        
        <fieldset class="piereg_fieldset_area">
    		<legend><?php _e("Login Form",'piereg') ?></legend>
            
            <div class="fields">
                <label for="login_username_label"><?php _e("Username Label",'piereg') ?></label>
                <input type="text" name="login_username_label" id="login_username_label" value="<?php echo $piereg['login_username_label']; ?>" class="input_fields" />
            </div>
            <div class="fields">
                <label for="login_username_placeholder"><?php _e("Username Placeholder",'piereg') ?></label>
                <input type="text" name="login_username_placeholder" id="login_username_placeholder" value="<?php echo $piereg['login_username_placeholder']; ?>" class="input_fields" />
            </div>
            <div class="fields">
                <label for="login_password_label"><?php _e("Password Label",'piereg') ?></label>
                <input type="text" name="login_password_label" id="login_password_label" value="<?php echo $piereg['login_password_label']; ?>" class="input_fields" />
            </div>
            <div class="fields">
                <label for="login_password_placeholder"><?php _e("Password Placeholder",'piereg') ?></label>
                <input type="text" name="login_password_placeholder" id="login_password_placeholder" value="<?php echo $piereg['login_password_placeholder']; ?>" class="input_fields" />
            </div>
            
            
            <div class="fields">
                <label for="capthca_in_login_label"><?php _e("Captcha Label",'piereg') ?></label>
                <input type="text" name="capthca_in_login_label" id="capthca_in_login_label" value="<?php echo $piereg['capthca_in_login_label']; ?>" class="input_fields" />
            </div>
            <div class="fields">
                <label for="piereg_capthca_in_login"><?php _e("Captcha",'piereg') ?></label>
                <select name="capthca_in_login" id="piereg_capthca_in_login">
                    <option value="0" <?php echo ((isset($piereg['capthca_in_login']) && $piereg['capthca_in_login'] == 0 )?'selected="selected"':'') ?>><?php _e("None",'piereg') ?></option>
                    <option value="1" <?php echo ((isset($piereg['capthca_in_login']) && $piereg['capthca_in_login'] == 1 )?'selected="selected"':'') ?>><?php _e("Re-Captcha",'piereg') ?></option>
                    <option value="2" <?php echo ((isset($piereg['capthca_in_login']) && $piereg['capthca_in_login'] == 2 )?'selected="selected"':'') ?>><?php _e("Math Captcha",'piereg') ?></option>
                </select>
                <span class="quotation"><?php _e("Appear Captcha in login form which you want",'piereg') ?></span> 
            </div>
            <div class="fields piereg_recapthca_skin_login" style="display:none;">
                <label for="piereg_recapthca_skin_login"><?php _e("Re-Captcha Skin",'piereg') ?></label>
                
                <select name="piereg_recapthca_skin_login" id="piereg_recapthca_skin_login">
                    <option value="red" <?php echo ((isset($piereg['piereg_recapthca_skin_login']) && $piereg['piereg_recapthca_skin_login'] == "red" )?'selected="selected"':'') ?>><?php _e("red",'piereg') ?></option>
                    
                    <option value="white" <?php echo ((isset($piereg['piereg_recapthca_skin_login']) && $piereg['piereg_recapthca_skin_login'] == "white" )?'selected="selected"':'') ?>><?php _e("white",'piereg') ?></option>
                    
                    <option value="clean" <?php echo ((isset($piereg['piereg_recapthca_skin_login']) && $piereg['piereg_recapthca_skin_login'] == "clean" )?'selected="selected"':'') ?>><?php _e("clean",'piereg') ?></option>
                    
                    <option value="blackglass" <?php echo ((isset($piereg['piereg_recapthca_skin_login']) && $piereg['piereg_recapthca_skin_login'] == "blackglass" )?'selected="selected"':'') ?>><?php _e("blackglass",'piereg') ?></option>
                    
                </select>
            </div>
            <script type="text/javascript">
				piereg(document).ready(function(){
					piereg("#piereg_capthca_in_login").on("change",function(){
						if(piereg(this).val() == 1)
							piereg(".piereg_recapthca_skin_login").fadeIn(1000);
						else
							piereg(".piereg_recapthca_skin_login").fadeOut(1000);
					});
					if(piereg("#piereg_capthca_in_login").val() == 1)
						piereg(".piereg_recapthca_skin_login").fadeIn(1000);
				});
			</script>
            
        </fieldset>
        
        <fieldset class="piereg_fieldset_area">
    		<legend><?php _e("Forgot Password",'piereg') ?></legend>
            
            
            <div class="fields">
                <label for="forgot_pass_username_label"><?php _e("Username Label",'piereg') ?></label>
                <input type="text" name="forgot_pass_username_label" id="forgot_pass_username_label" value="<?php echo $piereg['forgot_pass_username_label']; ?>" class="input_fields" />
            </div>
            <div class="fields">
                <label for="forgot_pass_username_placeholder"><?php _e("Username Placeholder",'piereg') ?></label>
                <input type="text" name="forgot_pass_username_placeholder" id="forgot_pass_username_placeholder" value="<?php echo $piereg['forgot_pass_username_placeholder']; ?>" class="input_fields" />
            </div>
            
            
            
            
            <div class="fields">
                <label for="capthca_in_forgot_pass_label"><?php _e("Captcha Label",'piereg') ?></label>
                <input type="text" name="capthca_in_forgot_pass_label" id="capthca_in_forgot_pass_label" value="<?php echo $piereg['capthca_in_forgot_pass_label']; ?>" class="input_fields" />
            </div>
            
            <div class="fields">
                <label for="piereg_capthca_in_forgot_pass"><?php _e("Captcha",'piereg') ?></label>
                <select name="capthca_in_forgot_pass" id="piereg_capthca_in_forgot_pass">
                    <option value="0" <?php echo ((isset($piereg['capthca_in_forgot_pass']) && $piereg['capthca_in_forgot_pass'] == 0 )?'selected="selected"':'') ?>><?php _e("None",'piereg') ?></option>
                    <option value="1" <?php echo ((isset($piereg['capthca_in_forgot_pass']) && $piereg['capthca_in_forgot_pass'] == 1 )?'selected="selected"':'') ?>><?php _e("Re-Captcha",'piereg') ?></option>
                    <option value="2" <?php echo ((isset($piereg['capthca_in_forgot_pass']) && $piereg['capthca_in_forgot_pass'] == 2 )?'selected="selected"':'') ?>><?php _e("Math Captcha",'piereg') ?></option>
                </select>
                <span class="quotation"><?php _e("Appear Captcha in Forgot Password form which you want",'piereg') ?></span> 
            </div>
            
            <div class="fields piereg_recapthca_skin_forgot_pas" style="display:none;">
                <label for="piereg_recapthca_skin_forgot_pass"><?php _e("Re-Captcha Skin",'piereg') ?></label>
                
                <select name="piereg_recapthca_skin_forgot_pass" id="piereg_recapthca_skin_forgot_pass">
                    <option value="red" <?php echo ((isset($piereg['piereg_recapthca_skin_forgot_pass']) && $piereg['piereg_recapthca_skin_forgot_pass'] == "red" )?'selected="selected"':'') ?>><?php _e("red",'piereg') ?></option>
                    
                    <option value="white" <?php echo ((isset($piereg['piereg_recapthca_skin_forgot_pass']) && $piereg['piereg_recapthca_skin_forgot_pass'] == "white" )?'selected="selected"':'') ?>><?php _e("white",'piereg') ?></option>
                    
                    <option value="clean" <?php echo ((isset($piereg['piereg_recapthca_skin_forgot_pass']) && $piereg['piereg_recapthca_skin_forgot_pass'] == "clean" )?'selected="selected"':'') ?>><?php _e("clean",'piereg') ?></option>
                    
                    <option value="blackglass" <?php echo ((isset($piereg['piereg_recapthca_skin_forgot_pass']) && $piereg['piereg_recapthca_skin_forgot_pass'] == "blackglass" )?'selected="selected"':'') ?>><?php _e("blackglass",'piereg') ?></option>
                    
                </select>
            </div>
            
            <script type="text/javascript">
				piereg(document).ready(function(){
					piereg("#piereg_capthca_in_forgot_pass").on("change",function(){
						if(piereg(this).val() == 1)
							piereg(".piereg_recapthca_skin_forgot_pas").fadeIn(1000);
						else
							piereg(".piereg_recapthca_skin_forgot_pas").fadeOut(1000);
					});
					if(piereg("#piereg_capthca_in_forgot_pass").val() == 1)
						piereg(".piereg_recapthca_skin_forgot_pas").fadeIn(1000);
				});
			</script>
        </fieldset>
     
     	<fieldset class="piereg_fieldset_area">
    		<legend><?php _e("Frontend Settings",'piereg') ?></legend>
            <div class="fields">
              <label><?php _e("Redirect Logged-in Users",'piereg') ?></label>
              <div class="radio_fields">
                <input type="radio" value="1" name="redirect_user" id="redirect_user_yes" <?php echo ($piereg['redirect_user']=="1")?'checked="checked"':''?> />
                <label for="redirect_user_yes"><?php _e("Yes",'piereg') ?></label>
                <input type="radio" value="0" name="redirect_user" id="redirect_user_no" <?php echo ($piereg['redirect_user']=="0")?'checked="checked"':''?> />
                <label for="redirect_user_no"><?php _e("No",'piereg') ?></label>
              </div>
              <span class="quotation"><?php _e("Set this to Yes if you would like to block Login, Registration & Forgot Password pages for logged in users.",'piereg') ?></span>
           </div>
            
           <?php /*?> <div class="fields">
              <label><?php _e("Modify Avatars",'piereg') ?></label>
              <div class="radio_fields">
                <input type="radio" value="1" name="modify_avatars" id="modify_avatars_yes" <?php echo ($piereg['modify_avatars']=="1")?'checked="checked"':''?> />
                <label for="modify_avatars_yes"><?php _e("Yes",'piereg') ?></label>
                <input type="radio" value="0" name="modify_avatars" id="modify_avatars_no" <?php echo ($piereg['modify_avatars']=="0")?'checked="checked"':''?> />
                <label for="modify_avatars_no"><?php _e("No",'piereg') ?></label>
              </div>
              <span class="quotation"><?php _e("Use Profile Picture as Avatars (if available)",'piereg') ?></span>
            </div><?php */?>
           
           <div class="fields">
              <label><?php _e("Show Admin Bar",'piereg') ?></label>
              <div class="radio_fields">
                <input type="radio" value="1" name="show_admin_bar" id="show_admin_bar_yes" <?php echo ($piereg['show_admin_bar']=="1")?'checked="checked"':''?> />
                <label for="show_admin_bar_yes"><?php _e("Yes",'piereg') ?></label>
                <input type="radio" value="0" name="show_admin_bar" id="show_admin_bar_no" <?php echo ($piereg['show_admin_bar']=="0")?'checked="checked"':''?> />
                <label for="show_admin_bar_no"><?php _e("No",'piereg') ?></label>
              </div>
              <span class="quotation"><?php _e("Show Admin Bar for Subscriber.",'piereg') ?></span>
           </div>
           
           <div class="fields">
              <label><?php _e("Override WP-Profile",'piereg') ?></label>
              <div class="radio_fields">
                <input type="radio" value="1" name="block_WP_profile" id="block_WP_profile_yes" <?php echo ($piereg['block_WP_profile']=="1")?'checked="checked"':''?> />
                <label for="block_WP_profile_yes"><?php _e("Yes",'piereg') ?></label>
                <input type="radio" value="0" name="block_WP_profile" id="block_WP_profile_no" <?php echo ($piereg['block_WP_profile']=="0")?'checked="checked"':''?> />
                <label for="block_WP_profile_no"><?php _e("No",'piereg') ?></label>
              </div>
              <span class="quotation"><?php _e("Redirect Your Subscriber to Custom Profile Page (if Exists)",'piereg') ?></span>
           </div>
           
           <div class="fields">
              <label><?php _e("Block WP-Login Pages",'piereg') ?></label>
              <div class="radio_fields">
                <input type="radio" value="1" name="block_wp_login" id="block_wp_login_yes" <?php echo ($piereg['block_wp_login']=="1")?'checked="checked"':''?> />
                <label for="block_wp_login_yes"><?php _e("Yes",'piereg') ?></label>
                <input type="radio" value="0" name="block_wp_login" id="block_wp_login_no" <?php echo ($piereg['block_wp_login']=="0")?'checked="checked"':''?> />
                <label for="block_wp_login_no"><?php _e("No",'piereg') ?></label>
              </div>
              <span class="quotation"><?php _e("Set this to Yes if you would like to block WP Login. You must select alternate pages.",'piereg') ?></span> </div>
           <div class="fields">
              <label><?php _e("Modify WP-LOGIN",'piereg') ?></label>
              <div class="radio_fields">
                <input type="radio" value="1" name="allow_pr_edit_wplogin" id="allow_pr_edit_wplogin_yes" <?php echo ($piereg['allow_pr_edit_wplogin']=="1")?'checked="checked"':''?> />
                <label for="allow_pr_edit_wplogin_yes"><?php _e("Yes",'piereg') ?></label>
                <input type="radio" value="0" name="allow_pr_edit_wplogin" id="allow_pr_edit_wplogin_no" <?php echo ($piereg['allow_pr_edit_wplogin']=="0")?'checked="checked"':''?> />
                <label for="allow_pr_edit_wplogin_no"><?php _e("No",'piereg') ?></label>
              </div>
              <span class="quotation"><?php _e("Allow Pie-Register to Add header Footer on wp-login.php.",'piereg') ?></span>
           </div>
            <div class="fields">
              <label><?php _e("Output CSS",'piereg') ?></label>
              <div class="radio_fields">
                <input type="radio" value="1" name="outputcss" id="outputcss_yes" <?php echo ($piereg['outputcss']=="1")?'checked="checked"':''?> />
                <label for="outputcss_yes"><?php _e("Yes",'piereg') ?></label>
                <input type="radio" value="0" name="outputcss" id="outputcss_no" <?php echo ($piereg['outputcss']=="0")?'checked="checked"':''?> />
                <label for="outputcss_no"><?php _e("No",'piereg') ?></label>
              </div>
              <span class="quotation"><?php _e("Set this to No if you would like to disable Pie-Register from outputting the form CSS.",'piereg') ?></span>
          	</div>
            
            <div class="fields">
                <label><?php _e("Output PR jQuery-ui",'piereg') ?></label>
                <div class="radio_fields">
                    <input type="radio" value="1" name="outputjquery_ui" id="outputjquery_ui_yes" <?php echo ($piereg['outputjquery_ui']=="1")?'checked="checked"':''?> />
                    <label for="outputjquery_ui_yes"><?php _e("Yes",'piereg') ?></label>
                    <input type="radio" value="0" name="outputjquery_ui" id="outputjquery_ui_no" <?php echo ($piereg['outputjquery_ui']=="0")?'checked="checked"':''?> />
                    <label for="outputjquery_ui_no"><?php _e("No",'piereg') ?></label>
                </div>
                <span class="quotation"><?php _e("Warning: Turning off any script here may stop Pie-Register to work properly, please do it carefully!",'piereg') ?></span>
            </div>
            <script type="text/javascript">
				piereg(document).ready(function(){
					piereg("#outputjquery_ui_no").on("click",function(){
						var pr_confrimation = confirm("Warning: Turning off this script here can stop Pie-Register to work properly! Are you sure?");
						if(!pr_confrimation)
						{
							piereg("#outputjquery_ui_yes").click();
						}
					});
				});
			</script>
           
       </fieldset>
       
       
       
       
        
        
        <!--<div class="fields">
          <label for="currency"><?php //_e("Currency",'piereg') ?></label>
          <select name="currency" id="currency">
            <option value="USD" <?php //echo ($piereg['currency']=="USD")?'selected="selected"':''?>>US Dollar</option>           
            <option value="CAD" <?php //echo ($piereg['currency']=="CAD")?'selected="selected"':''?>>Canadian Dollar</option>
          </select>
        </div>-->
        <fieldset class="piereg_fieldset_area">
    		<legend><?php _e("User Verification",'piereg') ?></legend>
            <div class="fields">
                <label><?php _e("Verifications",'piereg') ?></label>
                <div class="radio_fields">
                    <input type="radio" value="2" name="verification" id="verification_2" <?php echo ($piereg['verification']=="2")?'checked="checked"':''?> />
                    <label for="verification_2"><?php _e("Email Verification",'piereg') ?></label>
                    <input type="radio" value="1" name="verification" id="verification_1" <?php echo ($piereg['verification']=="1")?'checked="checked"':''?> />
                    <label for="verification_1"><?php _e("Admin Verification",'piereg') ?></label>
                    <input type="radio" value="0" name="verification" id="verification_0"  <?php echo ($piereg['verification']=="0")?'checked="checked"':''?> />
                    <label for="verification_0"><?php _e("Off",'piereg') ?></label>
                </div>
                <div class="verification_data">
                    <span><?php _e("Requires new registrations to click a link in the notification email to enable their account.",'piereg') ?></span>
                    <p><strong><?php _e("Grace Period (days)",'piereg') ?>:
                    <input type="text" name="grace_period" class="input_fields2" value="<?php echo $piereg['grace_period']?>" />
                    </strong></p>
                    <p><?php _e("Unverified Users will be automatically deleted after grace period expires. 0 (Zero) For Unlimited",'piereg') ?></p>
                </div>
            </div>
            
            
            <div class="fields">
                <label><?php _e("Email Edit Verifications",'piereg') ?></label>
                <div class="radio_fields">
                    <input type="radio" value="1" name="email_edit_verification_step" id="email_edit_verification_1" <?php echo ($piereg['email_edit_verification_step']=="1")?'checked="checked"':''?> class="step_email_edit_verif" />
                    <label for="email_edit_verification_1"><?php _e("1 Step Email Edit Verification",'piereg') ?></label>
                    <input type="radio" value="2" name="email_edit_verification_step" id="email_edit_verification_2" <?php echo ($piereg['email_edit_verification_step']=="2")?'checked="checked"':''?> class="step_email_edit_verif" />
                    <label for="email_edit_verification_2"><?php _e("2 Step Email Edit Verification",'piereg') ?></label>
                    <input type="radio" value="0" name="email_edit_verification_step" id="email_edit_verification_0" <?php echo ($piereg['email_edit_verification_step']=="0")?'checked="checked"':''?> class="step_email_edit_verif" />
                    <label for="email_edit_verification_0"><?php _e("Off",'piereg') ?></label>
                    
                </div>
                <span class="1step_email_edit_verification pr_email_edit_verif quotation"><strong><?php _e("Note","piereg");?></strong>&nbsp;:&nbsp;<?php _e("1 Step (Verify new address only)","piereg"); ?></span>
                <span class="2step_email_edit_verification pr_email_edit_verif quotation"><strong><?php _e("Note","piereg");?></strong>&nbsp;:&nbsp;<?php _e("2 Step (Authenticate request by sending an email to old address + verify new address)","piereg"); ?></span>
                <script type="text/javascript">
					piereg(document).ready(function(){
						piereg(".step_email_edit_verif").on("change",function(){
							email_edit_verification_description();
						});
						email_edit_verification_description();
						function email_edit_verification_description(){
							piereg("span.pr_email_edit_verif ").hide();
							if(piereg("#email_edit_verification_1").is(":checked")){
								piereg("span.2step_email_edit_verification").hide();
								piereg("span.1step_email_edit_verification").fadeIn(1000);
							}else if(piereg("#email_edit_verification_2").is(":checked")){
								piereg("span.1step_email_edit_verification").hide();
								piereg("span.2step_email_edit_verification").fadeIn(1000);
							}
						}
					});
				</script>
            </div>
            
        </fieldset>
        
        
        <!-- Password Strangth Meater -->
        <fieldset class="piereg_fieldset_area">
    		<legend><?php _e("Password Strength Meter Settings",'piereg') ?></legend>
            <div class="fields">
                <label for="pass_strength_indicator_label"><?php _e("Strength Indicator",'piereg') ?></label>
                <input type="text" name="pass_strength_indicator_label" id="pass_strength_indicator_label" value="<?php echo $piereg['pass_strength_indicator_label']; ?>" class="input_fields" />
            </div>
            <div class="fields">
                <label for="pass_very_weak_label"><?php _e("Very Weak",'piereg') ?></label>
                <input type="text" name="pass_very_weak_label" id="pass_very_weak_label" value="<?php echo $piereg['pass_very_weak_label']; ?>" class="input_fields" />
            </div>
            <div class="fields">
                <label for="pass_weak_label"><?php _e("Weak",'piereg') ?></label>
                <input type="text" name="pass_weak_label" id="pass_weak_label" value="<?php echo $piereg['pass_weak_label']; ?>" class="input_fields" />
            </div>
            <div class="fields">
                <label for="pass_medium_label"><?php _e("Medium",'piereg') ?></label>
                <input type="text" name="pass_medium_label" id="pass_medium_label" value="<?php echo $piereg['pass_medium_label']; ?>" class="input_fields" />
            </div>
            <div class="fields">
                <label for="pass_strong_label"><?php _e("Strong",'piereg') ?></label>
                <input type="text" name="pass_strong_label" id="pass_strong_label" value="<?php echo $piereg['pass_strong_label']; ?>" class="input_fields" />
            </div>
            <div class="fields">
                <label for="pass_mismatch_label"><?php _e("Mismatch",'piereg') ?></label>
                <input type="text" name="pass_mismatch_label" id="pass_mismatch_label" value="<?php echo $piereg['pass_mismatch_label']; ?>" class="input_fields" />
            </div>
        </fieldset>
        
        
        

        <div class="fields">
            <input type="submit" class="submit_btn" value="<?php _e("Save Settings","piereg"); ?>" />
        </div>
           
        <?php /* ?>
        <div style="display:none;">
        <h3><?php _e("Installation Status",'piereg') ?></h3>
        <div class="fields">
          <label><?php _e("PHP Version",'piereg') ?></label>
          <?php if(version_compare(phpversion(),  "5.0") == 1)
		  {
			  echo '<span class="installation_status">'.phpversion().'</span>';
		  }
		  else
		  {
			  echo '<span class="installation_status_faild">'.phpversion().'</span>';
			  echo '<span class="quotation">'.__("Sorry, Pie-Register requires PHP 5.0 or higher. Please deactivate Pie-Register","piereg").'</span>';
		  }
		  ?>
        </div>
        <div class="fields">
          <label><?php _e("MySQL Version",'piereg') ?></label>
          <?php
		  
		  
		  
			// Use ext/mysqli if it exists and:
			//  - WP_USE_EXT_MYSQL is defined as false, or
			//  - We are a development version of WordPress, or
			//  - We are running PHP 5.5 or greater, or
			//  - ext/mysql is not loaded.
			  
			$piereg_mytsql_version_info = "";
			global $wpdb;
			if ( function_exists( 'mysqli_connect' ) ){
				if ( defined( 'WP_USE_EXT_MYSQL' ) ){
					//mysql
					$piereg_mytsql_version_info = mysql_get_server_info($wpdb->dbh);
				} elseif ( version_compare( phpversion(), '5.5', '>=' ) || ! function_exists( 'mysql_connect' ) ) {
					//mysqli
					$piereg_mytsql_version_info = mysqli_get_server_info($wpdb->dbh);
				} elseif ( false !== strpos( $GLOBALS['wp_version'], '-' ) ) {
					//mysqli
					$piereg_mytsql_version_info = mysqli_get_server_info($wpdb->dbh);
				}else{
					//mysql
					$piereg_mytsql_version_info = mysql_get_server_info($wpdb->dbh);
				}
			}else{
				//mysql
				$piereg_mytsql_version_info = mysql_get_server_info($wpdb->dbh);
			}
			if(version_compare($piereg_mytsql_version_info,  "5.0") == 1)
			{
				echo '<span class="installation_status">'.$piereg_mytsql_version_info.'</span>';
			}
			else
			{
				echo '<span class="installation_status_faild">'.$piereg_mytsql_version_info.'</span>';
				echo '<span class="quotation">'.__("Sorry, Pie-Register requires MySQL 5.0 or higher. Please deactivate Pie-Register","piereg").'</span>';
			}
			?>
          
        </div>
        <div class="fields">
          <label><?php _e("Wordpress Version",'piereg') ?></label>
          <?php if(version_compare(get_bloginfo('version'),  "3.5") == 1)
		  {
			  echo '<span class="installation_status">'.get_bloginfo('version').'</span>';
		  }
		  else
		  {
			  echo '<span class="installation_status_faild">'.get_bloginfo('version').'</span>';
			  echo '<span class="quotation">'.__("Sorry, Pie-Register requires Wordpress 3.5 or higher. Please deactivate Pie-Register","piereg").'</span>';
		  }
		  ?>
        </div>
        <div class="fields">
          <label><?php _e("Enable Curl",'piereg') ?></label>
          <?php if(function_exists('curl_version'))
		  {
			  echo '<span class="installation_status">'.__("CURL Enable","piereg").'</span>';
		  }
		  else
		  {
			  echo '<span class="installation_status_faild">'.__("CURL Enable","piereg").'</span>';
			  echo '<span class="quotation">'.__("Please install CURL on server","piereg").'</span>';
		  }
		  ?>
        </div>
        </div>
        <?php */ ?>
        
        <h3><?php _e("reCAPTCHA Settings",'piereg') ?></h3>
        <div class="fields">
          <p><?php _e("Pie Register integrates with reCAPTCHA, a free CAPTCHA services that helps to digitize Books while Protecting your forms from spam bots. Read more about reCAPTCHA.",'piereg') ?></p>
          <p id="piereg_reCAPTCHA_Public_Key_error" style="display:none;color:#F00;"><strong><?php _e("Error : Invalid Re-Captcha keys",'piereg') ?></strong></p>
        </div>
        <div class="fields">
          <label for="piereg_reCAPTCHA_Public_Key"><?php _e("reCAPTCHA Site Key",'piereg') ?></label>
          <input type="text" id="piereg_reCAPTCHA_Public_Key" name="captcha_publc" class="input_fields" value="<?php echo $piereg['captcha_publc']?>" />
          <span class="quotation"><?php _e("Required Only if you decide to Use the reCAPTCHA field. Sign Up for a Free account to get the key.",'piereg') ?></span> </div>
        <div class="fields">
          <label for="piereg_reCAPTCHA_Private_Key"><?php _e("reCAPTCHA Secret Key",'piereg') ?></label>
          <input type="text" id="piereg_reCAPTCHA_Private_Key" name="captcha_private" class="input_fields" value="<?php echo $piereg['captcha_private']?>" />
          <span class="quotation"><?php _e("Required Only if you decide to Use the reCAPTCHA field. Sign Up for a Free account to get the key.",'piereg') ?></span> </div>
        <div class="fields">
          <input type="submit" class="submit_btn" value="<?php _e("Save Settings","piereg"); ?>" />
        </div>
     
      
        <h3><?php _e("Custom CSS",'piereg'); ?></h3>
        <div class="fields">
          <span class="quotation" style="margin-left:0px;"><?php _e("Please don't use style tags.",'piereg') ?></span>
          <textarea name="custom_css"><?php echo html_entity_decode($piereg['custom_css'],ENT_COMPAT,"UTF-8")?></textarea>        
          <input type="submit" class="submit_btn" value=" <?php _e("Save Changes","piereg");?> " />
        </div>
        <h3><?php _e("Tracking Code",'piereg'); ?></h3>
        <div class="fields">
          <span class="quotation" style="margin-left:0px;"><?php _e("Please don't use script tags.",'piereg') ?></span>
          <textarea name="tracking_code"><?php echo html_entity_decode($piereg['tracking_code'],ENT_COMPAT,"UTF-8")?></textarea>
          <input type="submit" class="submit_btn" value=" <?php _e("Save Changes","piereg");?> " />
        </div>
        
        <?php /*?><h3><?php _e("Payment Setting",'piereg'); ?></h3>
        <!-- Payment Setting-->
                <div class="fields">
                    <label for="payment_setting_amount" style="min-width:291px;"><?php echo __("Activation Amount",'piereg'); ?></label>
                    <input id="payment_setting_amount" class="input_fields" type="text" name="payment_setting_amount" <?php echo (trim($piereg['payment_setting_amount']) != "")? 'value="'.$piereg['payment_setting_amount'].'"':'0'?> />
                </div>
                <?php
				$update = get_option("pie_register_2");
				if($this->check_plugin_activation() == "true")
				{
				?>
                <div class="fields">
                    <label for="payment_setting_activation_cycle" style="min-width:291px;"><?php echo __("Activation Cycle","piereg"); ?></label>
                    <select id="payment_setting_activation_cycle" name="payment_setting_activation_cycle">
                        <option value="0" <?php echo ($piereg['payment_setting_activation_cycle']=="0")?'selected="selected"':''?>>One Time</option>
                        <option value="7" <?php echo ($piereg['payment_setting_activation_cycle']=="7")?'selected="selected"':''?>>Weekly</option>
                        <option value="30" <?php echo ($piereg['payment_setting_activation_cycle']=="30")?'selected="selected"':''?>>Monthly</option>
                        <option value="182" <?php echo ($piereg['payment_setting_activation_cycle']=="182")?'selected="selected"':''?>>Half Yearly</option>
                        <option value="273" <?php echo ($piereg['payment_setting_activation_cycle']=="273")?'selected="selected"':''?>>Quarterly</option>
                        <option value="365" <?php echo ($piereg['payment_setting_activation_cycle']=="365")?'selected="selected"':''?>>Yearly</option>
                    </select>
                </div>
                
                <div class="fields">
                    <label for="payment_setting_expiry_notice_days" style="min-width:291px;"><?php echo __("Expiry Notice (Days)","piereg"); ?></label>
                    <select id="payment_setting_expiry_notice_days" name="payment_setting_expiry_notice_days">
                            <option value="0" <?php echo ($piereg['payment_setting_expiry_notice_days']=='0')?'selected="selected"':''?>>NO</option>
                        <?php for($a = 1; $a <= 15; $a++){ ?>
                            <option value="<?php echo $a; ?>" <?php echo ($piereg['payment_setting_expiry_notice_days']==$a)?'selected="selected"':''?>><?php echo $a; ?></option>
                        <?php } ?>
                    </select>
                </div>
                
                <div class="fields">
                    <label for="payment_setting_remove_user_days" style="min-width:291px;"><?php echo __("User permanently Remove(Days)","piereg"); ?></label>
                    <select id="payment_setting_remove_user_days" name="payment_setting_remove_user_days">
                            <option value="0" <?php echo ($piereg['payment_setting_remove_user_days']=='0')?'selected="selected"':''?>>NO</option>
                        <?php for($a = 1; $a <= 15; $a++){ ?>
                            <option value="<?php echo $a; ?>" <?php echo ($piereg['payment_setting_remove_user_days']==$a)?'selected="selected"':''?>><?php echo $a; ?></option>
                        <?php } ?>
                    </select>
                </div>
                
                <div class="fields">
                    <label for="payment_setting_user_block_notice" style="min-width:291px;"><?php echo __("User Temporary Block Notice","piereg"); ?></label>
                    <input id="payment_setting_user_block_notice" class="input_fields" type="text" name="payment_setting_user_block_notice" <?php echo (trim($piereg['payment_setting_user_block_notice']) != "")? 'value="'.$piereg['payment_setting_user_block_notice'].'"':''?> />
                </div>
                 <?php 
				}
				?>
                
                <div class="fields">
                    <input name="submit_btn" style="margin:0;" class="submit_btn" value="Save Changes" type="submit" />
                </div><?php */?>
        
		<h3><?php _e("Custom Logo",'piereg'); ?></h3>

        <div class="fields">
            <label for="logo"><?php _e('Custom Logo URL', 'piereg');?></label>
            
<?php
wp_enqueue_script('thickbox');
?>
<style>
/* thickbox fix / hack because wordpress has changed the includes thickbox.css core file */
#TB_overlay {
	z-index: 99998 !important; /*they have it set at some crazy number */
}
#TB_window {
	z-index: 99999 !important; /*they have it set at some crazy number */
}
#TB_window {
	font: 12px "Open Sans", sans-serif;
	color: #333333;
}
#TB_secondLine {
	font: 10px "Open Sans", sans-serif;
	color:#666666;
}
.rtl #TB_window,
.rtl #TB_secondLine {
	font-family: Tahoma, sans-serif;
}
:lang(he-il) .rtl #TB_window,
:lang(he-il) .rtl #TB_secondLine {
	font-family: Arial, sans-serif;
}
#TB_window a:link {color: #666666;}
#TB_window a:visited {color: #666666;}
#TB_window a:hover {color: #000;}
#TB_window a:active {color: #666666;}
#TB_window a:focus{color: #666666;}
/* end thickbox fixes */
</style>
<script type="text/javascript">
/*************************************************/
///////////////// CUSTOM LOGO /////////////////////
piereg(document).on("click", "#pie_custom_logo_button", function() {
	var $Width = window.innerWidth - 100;
	var $Height = window.innerHeight - 100;
	formfield = piereg("#pie_custom_logo_url").prop("name");
	tb_show("<?php _e( 'Upload/Select Logo', 'piereg' ); ?>", "<?php echo admin_url('media-upload.php') ?>?post_id=0&amp;type=image&amp;context=custom-logo&amp;TB_iframe=1&amp;height="+$Height+"&amp;width="+$Width);
});
window.send_to_editor = function(html) {
	piereg("#pie_custom_logo_url").val(piereg("img", html).attr("src"));
	tb_remove();
}
/*************************************************/
</script>
            
            
            
<?php
 if( ( isset($piereg['custom_logo_url']) && $piereg['custom_logo_url'] == '') && (isset($piereg['logo']) && $piereg['logo'] != '') )
			$piereg['custom_logo_url'] = $piereg['logo'];?>
<input id="pie_custom_logo_url" type="text" name="custom_logo_url" value="<?php echo $piereg['custom_logo_url'];?>" placeholder="<?php _e("Please enter Logo URL","piereg"); ?>" class="input_fields" style="width:50%;" />
&nbsp;<sub><span style="font-size:16px;"><?php _e( 'OR', 'piereg' ); ?></span></sub>&nbsp;
<?php add_thickbox();?>
<button id="pie_custom_logo_button" class="button" type="button" value="1" name="pie_custom_logo_button">
<?php _e( 'Select Image to Upload', 'piereg' ); ?>
</button>
</div>
<div class="fields">
    <label for="custom_logo_title"><?php _e( 'Tooltip Text', 'piereg' ); ?></label>
	<input type="text" name="custom_logo_tooltip" class="input_fields" id="custom_logo_title" value="<?php echo $piereg['custom_logo_tooltip'];?>" placeholder="<?php _e("Enter logo tooltip","piereg"); ?>" />
    <span class="quotation"><?php _e("Show tooltip on custom logo.","piereg"); ?></span>
</div>

<div class="fields">
    <label for="custom_logo_link"><?php _e( 'Link URL', 'piereg' ); ?></label>
	<input type="text" name="custom_logo_link" class="input_fields" id="custom_logo_link" value="<?php echo $piereg['custom_logo_link'];?>" placeholder="<?php _e("Enter logo Link","piereg"); ?>" />
</div>

<?php if ( $piereg['custom_logo_url'] ) {?>
<div class="fields">	
    <label><?php _e( 'Selected Logo', 'piereg' ); ?></label>
    <img src="<?php echo $piereg['custom_logo_url'];?>" alt="<?php _e( 'Custom Logo', 'piereg' ); ?>" />
</div>
<div class="fields">
    <label><?php _e( 'Show Custom Logo', 'piereg' ); ?></label>
    <div class="radio_fields">
        <input type="radio" name="show_custom_logo" value="1" id="show_custom_logo_yes" <?php echo ($piereg['show_custom_logo'] == "1")? 'checked="checked"' : '' ?> />
        <label for="show_custom_logo_yes"><?php _e('Yes', 'piereg');?></label>
        <input type="radio" name="show_custom_logo" value="0" id="show_custom_logo_no" <?php echo ($piereg['show_custom_logo'] == "0")? 'checked="checked"' : '' ?> />
        <label for="show_custom_logo_no"><?php _e('No', 'piereg');?></label>
        
    </div>
</div>
<?php } ?>
<div class="fields">
<input type="submit" class="submit_btn" value=" <?php _e("Save Changes","piereg");?> " />
</div>

		<h3><?php _e("Remove Settings",'piereg'); ?></h3>
        <div class="fields">
            <label><?php _e('Remove Settings On Deactivation', 'piereg' ); ?>&nbsp;&nbsp;&nbsp;</label>
            <div class="radio_fields">
                <input type="radio" name="remove_PR_settings" value="1" id="remove_PR_settings_yes" <?php echo ($piereg['remove_PR_settings'] == "1")? 'checked="checked"' : '' ?> />
                <label for="remove_PR_settings_yes"><?php _e('Yes', 'piereg');?></label>
                <input type="radio" name="remove_PR_settings" value="0" id="remove_PR_settings_no" <?php echo ($piereg['remove_PR_settings'] == "0")? 'checked="checked"' : '' ?> />
                <label for="remove_PR_settings_no"><?php _e('No', 'piereg');?></label>
            </div>
            <input type="submit" class="submit_btn" value=" <?php _e("Save Changes","piereg");?> " />
        </div>

        
        <div class="fields fields2">
          <input type="submit" class="submit_btn" value=" <?php _e("Save Changes","piereg");?> " />
          <a href="javascript:;" onclick="jQuery('#frm_default').submit();" class="restore"><?php _e("Reset to Default",'piereg'); ?></a> </div>
        <input name="action" value="pie_reg_update" type="hidden" />
        <input type="hidden" name="general_settings_page" value="1" />
      </form>
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
       <h3><?php _e("Installation Status",'piereg') ?></h3>
        <div class="fields">
          <label><?php _e("PHP Version",'piereg') ?></label>
          <?php if(version_compare(phpversion(),  "5.0") == 1)
		  {
			  echo '<span class="installation_status">'.phpversion().'</span>';
		  }
		  else
		  {
			  echo '<span class="installation_status_faild">'.phpversion().'</span>';
			  echo '<span class="quotation">'.__("Sorry, Pie-Register requires PHP 5.0 or higher. Please deactivate Pie-Register","piereg").'</span>';
		  }
		  ?>
        </div>
        <div class="fields">
          <label><?php _e("MySQL Version",'piereg') ?></label>
          <?php
			/* Use ext/mysqli if it exists and:
			  *  - WP_USE_EXT_MYSQL is defined as false, or
			  *  - We are a development version of WordPress, or
			  *  - We are running PHP 5.5 or greater, or
			  *  - ext/mysql is not loaded.
			  */
			/*$piereg_mytsql_version_info = "";
			global $wpdb;
			if ( function_exists( 'mysqli_connect' ) ){
				if ( defined( 'WP_USE_EXT_MYSQL' ) ){
					//mysql
					$piereg_mytsql_version_info = mysql_get_server_info($wpdb->dbh);
				} elseif ( version_compare( phpversion(), '5.5', '>=' ) || ! function_exists( 'mysql_connect' ) ) {
					//mysqli
					$piereg_mytsql_version_info = mysqli_get_server_info($wpdb->dbh);
				} elseif ( false !== strpos( $GLOBALS['wp_version'], '-' ) ) {
					//mysqli
					$piereg_mytsql_version_info = mysqli_get_server_info($wpdb->dbh);
				}else{
					//mysql
					$piereg_mytsql_version_info = mysql_get_server_info($wpdb->dbh);
				}
			}else{
				//mysql
				$piereg_mytsql_version_info = mysql_get_server_info($wpdb->dbh);
			}*/
			global $wpdb;
			$piereg_mytsql_version_info = $wpdb->db_version();
			if(version_compare($piereg_mytsql_version_info,  "5.0") == 1)
			{
				echo '<span class="installation_status">'.$piereg_mytsql_version_info.'</span>';
			}
			else
			{
				echo '<span class="installation_status_faild">'.$piereg_mytsql_version_info.'</span>';
				echo '<span class="quotation">'.__("Sorry, Pie-Register requires MySQL 5.0 or higher. Please deactivate Pie-Register","piereg").'</span>';
			}
			?>
          
        </div>
        <div class="fields">
          <label><?php _e("Wordpress Version",'piereg') ?></label>
          <?php if(version_compare(get_bloginfo('version'),  "3.5") == 1)
		  {
			  echo '<span class="installation_status">'.get_bloginfo('version').'</span>';
		  }
		  else
		  {
			  echo '<span class="installation_status_faild">'.get_bloginfo('version').'</span>';
			  echo '<span class="quotation">'.__("Sorry, Pie-Register requires Wordpress 3.5 or higher. Please deactivate Pie-Register","piereg").'</span>';
		  }
		  ?>
        </div>
        <div class="fields">
          <label><?php _e("Curl",'piereg') ?></label>
          <?php if(function_exists('curl_version'))
		  {
			  echo '<span class="installation_status">'.__("Enable","piereg").'</span>';
		  }
		  else
		  {
			  echo '<span class="installation_status_faild">'.__("Disable","piereg").'</span>';
			  echo '<span class="quotation">'.__("Please install CURL on server","piereg").'</span>';
		  }
		  ?>
        </div>
        <div class="fields">
          <label><?php _e("File Get Contents",'piereg') ?></label>
          <?php if(function_exists('file_get_contents'))
		  {
			  echo '<span class="installation_status">'.__("Enable","piereg").'</span>';
		  }
		  else
		  {
			  echo '<span class="installation_status_faild">'.__("Disable","piereg").'</span>';
			  echo '<span class="quotation">'.__("Please install File Get Contents on server","piereg").'</span>';
		  }
		  ?>
        </div>
        <div class="fields">
          <label><?php _e("MB String",'piereg') ?></label>
          <?php if (extension_loaded('mbstring'))
		  {
			  echo '<span class="installation_status">'.__("Enable","piereg").'</span>';
		  }
		  else
		  {
			  echo '<span class="installation_status_faild">'.__("Disable","piereg").'</span>';
			  echo '<span class="quotation">'.__("Please install MB String on server","piereg").'</span>';
		  }
		  ?>
        </div>
        <?php if ( function_exists( 'ini_get' ) ){ ?>
          
	            <div class="fields">
                      <label><?php _e("PHP Post Max Size",'piereg') ?></label>
                      <?php
                      echo '<span class="installation_status installation_status_no_bg">'.(ini_get('post_max_size')).'</span>';
                  ?>
                </div>
	            <div class="fields">
                      <label><?php _e("PHP Time Limit",'piereg') ?></label>
                      <?php
                      echo '<span class="installation_status installation_status_no_bg">'.(ini_get('max_execution_time')).'</span>';
                  ?>
                </div>
                
        <?php } else {?>
		        <div class="fields">
                     <label><?php _e("ini_get",'piereg') ?></label><?php
                     echo '<span class="installation_status_faild">'.__("Disable","piereg").'</span>';
                     echo '<span class="quotation">'.__("Please install ini_get on server","piereg").'</span>';
				  ?>
				</div>
        <?php } ?>
        <div class="fields">
          <label><?php _e("WP Memory Limit",'piereg') ?></label>
          <?php
		  echo '<span class="installation_status installation_status_no_bg">'.WP_MEMORY_LIMIT.'</span>';
		  ?>
        </div>
        <div class="fields">
          <label><?php _e("WP Debug Mode",'piereg') ?></label>
          <?php
		  if ( defined('WP_DEBUG') && WP_DEBUG ) echo '<span class="installation_status installation_status_no_bg">' . __( 'Yes', 'piereg' ) . '</span>'; else echo '<span class="installation_status installation_status_no_bg">' . __( 'No', 'piereg' ) . '</span>';
		  ?>
        </div>
        <div class="fields">
          <label><?php _e("WP Language",'piereg') ?></label>
          <?php
		  echo '<span class="installation_status installation_status_no_bg">' . get_locale() . '</span>';
		  ?>
        </div>
        <div class="fields">
          <label><?php _e("WP Max Upload Size",'piereg') ?></label>
          <?php
		  echo '<span class="installation_status installation_status_no_bg">' . size_format( wp_max_upload_size() ) . '</span>';
		  ?>
        </div>
        
        
        
        
      
      
      
      
      
      
      
      
      
      
    </div>
  </div>
</div>
<form id="frm_default" method="post" onsubmit="return window.confirm('Are you sure? It will restore all the plugin settings to default.');">
<input type="hidden" value="1" name="piereg_default_settings" />
</form>