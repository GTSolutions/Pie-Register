<style>
.captcha_in_login_attempts {
	width:25% !important;
}
</style>
<?php $piereg = PieReg_Base::get_pr_global_options(); ?>
<div class="forms_max_label">
<form method="post" action="" id="piereg_form_general_settings_page" onsubmit="return validateSettings();">
  <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_settings_security_b','piereg_settings_security_b'); ?>
  <h3>
    <?php _e("Login Form",'piereg'); ?>
  </h3>
  <div class="fields">
    <label for="capthca_in_login_form">
      <?php _e("Show Captcha on login form?",'piereg') ?>
    </label>
    <div class="radio_fields">
      <input type="radio" name="captcha_in_login_value" id="captcha_in_login_value_0" class="captcha_in_login_value" value="0" checked="checked" <?php echo (isset($piereg['captcha_in_login_value']) && $piereg['captcha_in_login_value'] == '0')?'checked="checked"':''; ?> />
      <label for="captcha_in_login_value_0">No</label>
      <input type="radio" name="captcha_in_login_value" id="captcha_in_login_value_1" class="captcha_in_login_value" value="1" <?php echo (isset($piereg['captcha_in_login_value']) && $piereg['captcha_in_login_value'] == '1')?'checked="checked"':''; ?> />
      <label for="captcha_in_login_value_1">Yes</label>
      <?php if($this->piereg_pro_is_activate) { ?>
          <label for="captcha_in_login_attempts" class="lbl_style1"> <?php _e('After', 'piereg'); ?>
            <select class="captcha_in_login_attempts" name="captcha_in_login_attempts" id="captcha_in_login_attempts">
               <option <?php echo (isset($piereg['captcha_in_login_attempts']) && $piereg['captcha_in_login_attempts'] == '0')?'selected="selected"':''; ?> value="0">0</option>
              <option <?php echo (isset($piereg['captcha_in_login_attempts']) && $piereg['captcha_in_login_attempts'] == '2')?'selected="selected"':''; ?> value="2">2</option>
              <option <?php echo (isset($piereg['captcha_in_login_attempts']) && $piereg['captcha_in_login_attempts'] == '3')?'selected="selected"':''; ?> value="3">3</option>
              <option <?php echo (isset($piereg['captcha_in_login_attempts']) && $piereg['captcha_in_login_attempts'] == '4')?'selected="selected"':''; ?> value="4">4</option>
              <option <?php echo (isset($piereg['captcha_in_login_attempts']) && $piereg['captcha_in_login_attempts'] == '5')?'selected="selected"':''; ?> value="5">5</option>
              <option <?php echo (isset($piereg['captcha_in_login_attempts']) && $piereg['captcha_in_login_attempts'] == '10')?'selected="selected"':''; ?> value="10">10</option>
              <option <?php echo (isset($piereg['captcha_in_login_attempts']) && $piereg['captcha_in_login_attempts'] == '15')?'selected="selected"':''; ?> value="15">15</option>
            </select>
            <?php _e('invalid login attempts', 'piereg'); ?></label>
      <?php } ?>
    </div>
  </div>
  <div class="fields piereg_captcha_label_show" <?php echo ((!isset($piereg['captcha_in_login_value']) || isset($piereg['captcha_in_login_value']) && $piereg['captcha_in_login_value'] == 0 )?'style="display:none;"':'') ?>>
    <label for="capthca_in_login_label">
      <?php _e("Captcha Label",'piereg') ?>
    </label>
    <input type="text" name="capthca_in_login_label" id="capthca_in_login_label" value="<?php echo $piereg['capthca_in_login_label']; ?>" class="input_fields" />
  </div>
  <div class="fields piereg_captcha_type_show" <?php echo ((!isset($piereg['captcha_in_login_value']) || isset($piereg['captcha_in_login_value']) && $piereg['captcha_in_login_value'] == 0 )?'style="display:none;"':'') ?>>
    <label for="piereg_capthca_in_login">
      <?php _e("Captcha Type",'piereg') ?>
    </label>
    <select name="capthca_in_login" id="piereg_capthca_in_login">
      <option value="3" <?php echo ((isset($piereg['capthca_in_login']) && $piereg['capthca_in_login'] == 3 )?'selected="selected"':'') ?>>
      <?php _e("No Captcha ReCaptcha",'piereg') ?>
      </option>
      
      <!--<option value="1" <?php //echo ((isset($piereg['capthca_in_login']) && $piereg['capthca_in_login'] == 1 )?'selected="selected"':'') ?>>
      <?php //_e("Classic ReCaptcha",'piereg') ?>
      </option>-->
      
      <option value="2" <?php echo ((isset($piereg['capthca_in_login']) && $piereg['capthca_in_login'] == 2 )?'selected="selected"':'') ?>>
      <?php _e("Math Captcha",'piereg') ?>
      </option>
    </select>
    <span class="quotation">
    <?php _e("Appear Captcha in login form which you want.",'piereg') ?>
    </span>
    <span class="quotation" id="note_quotation" <?php echo ((isset($piereg['captcha_in_login_value']) && $piereg['captcha_in_login_value'] == 0 || isset($piereg['capthca_in_login']) && $piereg['capthca_in_login'] != 1 )?'style="display:none;"':'') ?>>
    <?php _e("<strong>Note:</strong> Classic ReCaptcha will not show multiple times on a single page.",'piereg') ?>
    </span>
    </div>
  
    <?php if($this->piereg_pro_is_activate) { ?>
      <div class="fields">
        <div class="container_attemps">
        <input type="checkbox" name="piereg_security_attempts_login_value" value="1" <?php echo (isset($piereg['piereg_security_attempts_login_value']) && $piereg['piereg_security_attempts_login_value'] == '1')?'checked="checked"':''; ?> />
         <?php _e("Lockout user for",'piereg') ?>
        <select class="security_attempts_drop" name="security_attempts_login_time" id="piereg_security_attempts_login_time">
          <option value="1" <?php echo ((isset($piereg['security_attempts_login_time']) && $piereg['security_attempts_login_time'] == 1 )?'selected="selected"':'') ?>>
          <?php _e("1","piereg"); ?>
          </option>
          <option value="2" <?php echo ((isset($piereg['security_attempts_login_time']) && $piereg['security_attempts_login_time'] == 2 )?'selected="selected"':'') ?>>
          <?php _e("2","piereg"); ?>
          </option>
          <option value="3" <?php echo ((isset($piereg['security_attempts_login_time']) && $piereg['security_attempts_login_time'] == 3 )?'selected="selected"':'') ?>>
          <?php _e("3","piereg"); ?>
          </option>
          <option value="4" <?php echo ((isset($piereg['security_attempts_login_time']) && $piereg['security_attempts_login_time'] == 4 )?'selected="selected"':'') ?>>
          <?php _e("4","piereg"); ?>
          </option>
          <option value="5" <?php echo ((isset($piereg['security_attempts_login_time']) && $piereg['security_attempts_login_time'] == 5 )?'selected="selected"':'') ?>>
          <?php _e("5","piereg"); ?>
          </option>
          <option value="7" <?php echo ((isset($piereg['security_attempts_login_time']) && $piereg['security_attempts_login_time'] == 7 )?'selected="selected"':'') ?>>
          <?php _e("7","piereg"); ?>
          </option>
          <option value="10" <?php echo ((isset($piereg['security_attempts_login_time']) && $piereg['security_attempts_login_time'] == 10 )?'selected="selected"':'') ?>>
          <?php _e("10","piereg"); ?>
          </option>
          <option value="30" <?php echo ((isset($piereg['security_attempts_login_time']) && $piereg['security_attempts_login_time'] == 30 )?'selected="selected"':'') ?>>
          <?php _e("30","piereg"); ?>
          </option>
          <option value="60" <?php echo ((isset($piereg['security_attempts_login_time']) && $piereg['security_attempts_login_time'] == 60 )?'selected="selected"':'') ?>>
          <?php _e("60","piereg"); ?>
          </option>
          <option value="90" <?php echo ((isset($piereg['security_attempts_login_time']) && $piereg['security_attempts_login_time'] == 90 )?'selected="selected"':'') ?>>
          <?php _e("90","piereg"); ?>
          </option>
          <option value="120" <?php echo ((isset($piereg['security_attempts_login_time']) && $piereg['security_attempts_login_time'] == 120 )?'selected="selected"':'') ?>>
          <?php _e("120","piereg"); ?>
          </option>
        </select>
        <?php _e("minutes after",'piereg') ?>
        <select class="security_attempts_drop" name="security_attempts_login" id="piereg_security_attempts_login">
          <option value="2" <?php echo ((isset($piereg['security_attempts_login']) && $piereg['security_attempts_login'] == 2 )?'selected="selected"':'') ?>>
          <?php _e("2","piereg"); ?>
          </option>
          <option value="3" <?php echo ((isset($piereg['security_attempts_login']) && $piereg['security_attempts_login'] == 3 )?'selected="selected"':'') ?>>
          <?php _e("3","piereg"); ?>
          </option>
          <option value="4" <?php echo ((isset($piereg['security_attempts_login']) && $piereg['security_attempts_login'] == 4 )?'selected="selected"':'') ?>>
          <?php _e("4","piereg"); ?>
          </option>
          <option value="5" <?php echo ((isset($piereg['security_attempts_login']) && $piereg['security_attempts_login'] == 5 )?'selected="selected"':'') ?>>
          <?php _e("5","piereg"); ?>
          </option>
          <option value="7" <?php echo ((isset($piereg['security_attempts_login']) && $piereg['security_attempts_login'] == 7 )?'selected="selected"':'') ?>>
          <?php _e("7","piereg"); ?>
          </option>
          <option value="10" <?php echo ((isset($piereg['security_attempts_login']) && $piereg['security_attempts_login'] == 10 )?'selected="selected"':'') ?>>
          <?php _e("10","piereg"); ?>
          </option>
          <option value="15" <?php echo ((isset($piereg['security_attempts_login']) && $piereg['security_attempts_login'] == 15 )?'selected="selected"':'') ?>>
          <?php _e("15","piereg"); ?>
          </option>
        </select>
        <?php _e("invalid login attempts",'piereg') ?>. </div> </div>
    <?php } ?>
    <hr class="seperator" />
  <h3>
    <?php _e("Forgot Password Form",'piereg'); ?>
  </h3>
  <div class="fields">
    <label for="capthca_in_forgot_form" class="limit_width">
      <?php _e("Show Captcha on forgot password form?",'piereg') ?>
    </label>
    <div class="radio_fields">
      <input type="radio" name="captcha_in_forgot_value" id="captcha_in_forgot_value_0" class="captcha_in_forgot_value" value="0" checked="checked" <?php echo (isset($piereg['captcha_in_forgot_value']) && $piereg['captcha_in_forgot_value'] == '0')?'checked="checked"':''; ?> />
      <label for="captcha_in_forgot_value_0">No</label>
      <input type="radio" name="captcha_in_forgot_value" id="captcha_in_forgot_value_1" class="captcha_in_forgot_value" value="1" <?php echo (isset($piereg['captcha_in_forgot_value']) && $piereg['captcha_in_forgot_value'] == '1')?'checked="checked"':''; ?> />
      <label for="captcha_in_forgot_value_1">Yes</label>
    </div>
  </div>
  <div class="fields piereg_capthca_forgot_pass_label_show" <?php echo (!isset($piereg['captcha_in_forgot_value']) || (isset($piereg['captcha_in_forgot_value']) && $piereg['captcha_in_forgot_value'] == 0 )?'style="display:none;"':'') ?>>
    <label for="capthca_in_forgot_pass_label">
      <?php _e("Captcha Label",'piereg') ?>
    </label>
    <input type="text" name="capthca_in_forgot_pass_label" id="capthca_in_forgot_pass_label" value="<?php echo $piereg['capthca_in_forgot_pass_label']; ?>" class="input_fields" />
  </div>
  <div class="fields piereg_captcha_forgot_pass_type_show" <?php echo (!isset($piereg['captcha_in_forgot_value']) || (isset($piereg['captcha_in_forgot_value']) && $piereg['captcha_in_forgot_value'] == 0 )?'style="display:none;"':'') ?>>
    <label for="piereg_capthca_in_forgot_pass">
      <?php _e("Captcha Type",'piereg') ?>
    </label>
    <select name="capthca_in_forgot_pass" id="piereg_capthca_in_forgot_pass">
      <option value="3" <?php echo ((isset($piereg['capthca_in_forgot_pass']) && $piereg['capthca_in_forgot_pass'] == 3 )?'selected="selected"':'') ?>>
      <?php _e("No Catpcha ReCaptcha",'piereg') ?>
      </option>
      <!--
      <option value="1" <?php //echo ((isset($piereg['capthca_in_forgot_pass']) && $piereg['capthca_in_forgot_pass'] == 1 )?'selected="selected"':'') ?>>
      <?php //_e("Classic ReCaptcha",'piereg') ?>
      </option>-->
      <option value="2" <?php echo ((isset($piereg['capthca_in_forgot_pass']) && $piereg['capthca_in_forgot_pass'] == 2 )?'selected="selected"':'') ?>>
      <?php _e("Math Captcha",'piereg') ?>
      </option>
    </select>
    <span class="quotation">
    <?php _e("Appear Captcha in Forgot Password form which you want.",'piereg') ?>
    </span>
    <span class="quotation" id="for_note_quotation" <?php echo ((isset($piereg['captcha_in_forgot_value']) && $piereg['captcha_in_forgot_value'] == 0 || isset($piereg['capthca_in_forgot_pass']) && $piereg['capthca_in_forgot_pass'] != 1 )?'style="display:none;"':'') ?>>
    <?php _e("<strong>Note:</strong> Classic ReCaptcha will not show multiple times on a single page.",'piereg') ?>
    </span>
    </div>
  
    <?php if($this->piereg_pro_is_activate) { ?>
      <div class="fields">
        <div class="container_attemps">
        <input type="checkbox" name="piereg_security_attempts_forgot_value" value="1" <?php echo (isset($piereg['piereg_security_attempts_forgot_value']) && $piereg['piereg_security_attempts_forgot_value'] == '1')?'checked="checked"':''; ?> />
        <?php _e("Lockout user for",'piereg') ?>
        <select class="security_attempts_drop" name="security_attempts_forgot_time" id="piereg_security_attempts_forgot_time">
          <option value="1" <?php echo ((isset($piereg['security_attempts_forgot_time']) && $piereg['security_attempts_forgot_time'] == 1 )?'selected="selected"':'') ?>>
          <?php _e("1","piereg"); ?>
          </option>
          <option value="2" <?php echo ((isset($piereg['security_attempts_forgot_time']) && $piereg['security_attempts_forgot_time'] == 2 )?'selected="selected"':'') ?>>
          <?php _e("2","piereg"); ?>
          </option>
          <option value="3" <?php echo ((isset($piereg['security_attempts_forgot_time']) && $piereg['security_attempts_forgot_time'] == 3 )?'selected="selected"':'') ?>>
          <?php _e("3","piereg"); ?>
          </option>
          <option value="4" <?php echo ((isset($piereg['security_attempts_forgot_time']) && $piereg['security_attempts_forgot_time'] == 4 )?'selected="selected"':'') ?>>
          <?php _e("4","piereg"); ?>
          </option>
          <option value="5" <?php echo ((isset($piereg['security_attempts_forgot_time']) && $piereg['security_attempts_forgot_time'] == 5 )?'selected="selected"':'') ?>>
          <?php _e("5","piereg"); ?>
          </option>
          <option value="7" <?php echo ((isset($piereg['security_attempts_forgot_time']) && $piereg['security_attempts_forgot_time'] == 7 )?'selected="selected"':'') ?>>
          <?php _e("7","piereg"); ?>
          </option>
          <option value="10" <?php echo ((isset($piereg['security_attempts_forgot_time']) && $piereg['security_attempts_forgot_time'] == 10 )?'selected="selected"':'') ?>>
          <?php _e("10","piereg"); ?>
          </option>
          <option value="30" <?php echo ((isset($piereg['security_attempts_forgot_time']) && $piereg['security_attempts_forgot_time'] == 30 )?'selected="selected"':'') ?>>
          <?php _e("30","piereg"); ?>
          </option>
          <option value="60" <?php echo ((isset($piereg['security_attempts_forgot_time']) && $piereg['security_attempts_forgot_time'] == 60 )?'selected="selected"':'') ?>>
          <?php _e("60","piereg"); ?>
          </option>
          <option value="90" <?php echo ((isset($piereg['security_attempts_forgot_time']) && $piereg['security_attempts_forgot_time'] == 90 )?'selected="selected"':'') ?>>
          <?php _e("90","piereg"); ?>
          </option>
          <option value="120" <?php echo ((isset($piereg['security_attempts_forgot_time']) && $piereg['security_attempts_forgot_time'] == 120 )?'selected="selected"':'') ?>>
          <?php _e("120","piereg"); ?>
          </option>
        </select>
        <?php _e("minutes after",'piereg') ?>
        <select class="security_attempts_drop" name="security_attempts_forgot" id="piereg_security_attempts_forgot">
          <option value="2" <?php echo ((isset($piereg['security_attempts_forgot']) && $piereg['security_attempts_forgot'] == 2 )?'selected="selected"':'') ?>>
          <?php _e("2","piereg"); ?>
          </option>
          <option value="3" <?php echo ((isset($piereg['security_attempts_forgot']) && $piereg['security_attempts_forgot'] == 3 )?'selected="selected"':'') ?>>
          <?php _e("3","piereg"); ?>
          </option>
          <option value="4" <?php echo ((isset($piereg['security_attempts_forgot']) && $piereg['security_attempts_forgot'] == 4 )?'selected="selected"':'') ?>>
          <?php _e("4","piereg"); ?>
          </option>
          <option value="5" <?php echo ((isset($piereg['security_attempts_forgot']) && $piereg['security_attempts_forgot'] == 5 )?'selected="selected"':'') ?>>
          <?php _e("5","piereg"); ?>
          </option>
          <option value="7" <?php echo ((isset($piereg['security_attempts_forgot']) && $piereg['security_attempts_forgot'] == 7 )?'selected="selected"':'') ?>>
          <?php _e("7","piereg"); ?>
          </option>
          <option value="10" <?php echo ((isset($piereg['security_attempts_forgot']) && $piereg['security_attempts_forgot'] == 10 )?'selected="selected"':'') ?>>
          <?php _e("10","piereg"); ?>
          </option>
          <option value="15" <?php echo ((isset($piereg['security_attempts_forgot']) && $piereg['security_attempts_forgot'] == 15 )?'selected="selected"':'') ?>>
          <?php _e("15","piereg"); ?>
          </option>
        </select>
        <?php _e("invalid login attempts",'piereg') ?>. </div>
        </div>
   <?php } ?>
    <hr class="seperator" />
  <h3>
    <?php _e("reCAPTCHA Settings",'piereg'); ?>
  </h3>
  <div class="fields">
    <p>
      <?php _e("Pie Register integrates with reCAPTCHA, a free CAPTCHA services that helps to digitize Books while Protecting your forms from spam bots. Please click <a href='https://www.google.com/recaptcha/admin' target='_blank'>here</a> to get reCaptcha keys for your site.",'piereg') ?>
    </p>
    <p id="piereg_reCAPTCHA_Public_Key_error" style="display:none;color:#F00;"><strong>
      <?php _e("Error : Invalid Re-Captcha keys",'piereg') ?>
      </strong></p>
  </div>
  <div class="fields">
    <label for="piereg_reCAPTCHA_Public_Key">
      <?php _e("reCAPTCHA Site Key",'piereg') ?>
    </label>
    <input type="text" id="piereg_reCAPTCHA_Public_Key" name="captcha_publc" class="input_fields" value="<?php echo $piereg['captcha_publc']?>" />
    <span class="quotation">
    <?php _e("Required Only if you decide to Use the reCAPTCHA field. Sign Up for a Free account to get the key.",'piereg') ?>
    </span> </div>
  <div class="fields">
    <label for="piereg_reCAPTCHA_Private_Key">
      <?php _e("reCAPTCHA Secret Key",'piereg') ?>
    </label>
    <input type="text" id="piereg_reCAPTCHA_Private_Key" name="captcha_private" class="input_fields" value="<?php echo $piereg['captcha_private']?>" />
    <span class="quotation">
    <?php _e("Required Only if you decide to Use the reCAPTCHA field. Sign Up for a Free account to get the key.",'piereg') ?>
    </span> </div>
    <hr class="seperator" />
  <h3>
    <?php _e("User Verification",'piereg'); ?>
  </h3>
  <div class="fields">
   <p>
      <?php _e("Note: Admin and Email verifications wont work when Payment gateway is enable.",'piereg') ?>
    </p>
  </div>
  <div class="fields">
    <label>
      <?php _e("New User Verification",'piereg') ?>
    </label>
    <div>
      <select name="verification" id="verification_2" >
        <option value="0" <?php echo (($piereg['verification']=="0")?'selected="selected"':"");?> >
        <?php _e("Disable","piereg"); ?>
        </option>
        <option value="1" <?php echo (($piereg['verification']=="1")?'selected="selected"':"");?> >
        <?php _e("Admin Approval","piereg"); ?>
        </option>
        <option value="2" <?php echo (($piereg['verification']=="2")?'selected="selected"':"");?> >
        <?php _e("Verify Email Address","piereg"); ?>
        </option>
        <?php if( $this->piereg_pro_is_activate ) { ?>
            <option value="3" <?php echo (($piereg['verification']=="3")?'selected="selected"':"");?> >
            <?php _e("Admin Approval AND Verify Email Address","piereg"); ?>
            </option>
        <?php } ?>
      </select>
    </div>
    <div class="verification_data"> <span>
      <?php _e("<strong>Admin Approval</strong> - Site admin has to approve each new user.",'piereg') ?>
      </span> <br />
      <span>
      <?php _e("<strong>Verify Email Address</strong> - Require new registrations to click a link sent via email to enable their account.",'piereg') ?>
      </span>
      <p><strong>
        <?php _e("Grace Period (days)",'piereg') ?>
        :
        <input type="text" name="grace_period" class="input_fields2" value="<?php echo $piereg['grace_period']?>" />
        </strong></p>
      <p>
        <?php _e("Unverified users will be automatically deleted after the grace period expires. 0 (Zero) for Unlimited",'piereg') ?>
      </p>
    </div>
  </div>
  <div class="fields">
    <label>
      <?php _e("Verify Email Address Change",'piereg') ?>
    </label>
    <div class="radio_fields max_label_300">
      <input type="radio" value="1" name="email_edit_verification_step" id="email_edit_verification_1" <?php echo ($piereg['email_edit_verification_step']=="1")?'checked="checked"':''?> class="step_email_edit_verif" />
      <label for="email_edit_verification_1">
        <?php _e("<strong>1-Step:</strong> Verify new email address.",'piereg') ?>
      </label>
      <input type="radio" value="2" name="email_edit_verification_step" id="email_edit_verification_2" <?php echo ($piereg['email_edit_verification_step']=="2")?'checked="checked"':''?> class="step_email_edit_verif" />
      <label for="email_edit_verification_2">
        <?php _e("<strong>2-Step:</strong> Authenticate request by sending an email to old address + verify new address.",'piereg') ?>
      </label>
      <input type="radio" value="0" name="email_edit_verification_step" id="email_edit_verification_0" <?php echo ($piereg['email_edit_verification_step']=="0")?'checked="checked"':''?> class="step_email_edit_verif" />
      <label for="email_edit_verification_0">
        <?php _e("Off",'piereg') ?>
      </label>
    </div>
    
  <?php if($this->piereg_pro_is_activate) { ?>
      <hr class="seperator" />
      <h3>
        <?php _e("Restrict For Search Engine(s) / Bot",'piereg'); ?>
      </h3>
      <div class="fields">
        <div class="radio_fields">
          <input type="checkbox" value="1" name="restrict_bot_enabel" id="captcha_publc" <?php echo (isset($piereg['restrict_bot_enabel']) && $piereg['restrict_bot_enabel']=="1")?'checked="checked"':''?> />
        </div>
        <label for="captcha_publc" class="label_mar_top">
          <?php _e("Restrict search engines and bots from crawling pages",'piereg') ?>
        </label>
      </div>
      <div class="fields">
        <label for="restrict_bot_content" class="label_textarea">
          <?php _e("Other User Agents to Reject",'piereg') ?>
        </label>
        <textarea name="restrict_bot_content"><?php echo ($piereg['restrict_bot_content']!="")?$piereg['restrict_bot_content']:"bot\r\nia_archive\r\nslurp crawl\r\nspider\r\nYandex";?></textarea>
      </div>
      <div class="fields">
        <label for="restrict_bot_content_message" class="label_textarea">
          <?php _e("Text to send bots when blocking access",'piereg') ?>
        </label>
        <textarea name="restrict_bot_content_message"><?php echo ($piereg['restrict_bot_content_message']!="")?$piereg['restrict_bot_content_message']:"Restricted Post: You are not allowed to view the content of this post";?></textarea>
      </div>
  <?php } ?>
  <input name="action" value="pie_reg_settings" type="hidden" />
  <div class="fields fields_submitbtn">
    <input type="submit" class="submit_btn" value=" <?php _e("Save Changes","piereg");?> " />
  </div>
</form>
</div>