<?php $piereg = PieReg_Base::get_pr_global_options(); ?>
<div class="forms_max_label ux_wrap">
<form action="" method="post" id="frm_settings_ux">
<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_settings_ux','piereg_settings_ux'); ?>
<?php if( (isset($_GET['tab']) && $_GET['tab'] == 'ux') && (isset($_GET['subtab']) && $_GET['subtab'] == 'advanced')) { ?>
        <?php if($this->piereg_pro_is_activate): ?>
        <div class="fields">
            <div class="radio_fields">
                 <input type="checkbox" name="login_after_register" id="login_after_register_yes" value="1" <?php echo ($piereg['login_after_register']=="1")?'checked="checked"':''?> />
            </div>
            <label class="label_mar_top" for="login_after_register_yes"><?php _e("Auto login users after registration.",'piereg') ?></label><br /><br />
            <span class="quotation_ux_adv"><?php _e("Make sure email/admin verifications and payment methods are off","piereg"); ?></span>
        </div>
        
        <h3><?php _e("Pie-Register Theme",'piereg') ?></h3>
        <div class="fields">
            <label for="piereg_pr_theme"><?php _e("Select Theme",'piereg') ?></label>
            <select name="pr_theme" id="piereg_pr_theme">
                <option value="0" <?php echo ((isset($piereg['pr_theme']) && $piereg['pr_theme'] == 0 )?'selected="selected"':'') ?>>
                    <?php _e("Theme Default","piereg"); ?>
                </option>
                <?php 
                    $theme_name_array 		= array();
                    $theme_name_array[1] 	= 'Black Cherry';
                    $theme_name_array[2]	= 'Fresh Blue';
                    $theme_name_array[3] 	= 'Digital Pink';
                    $theme_name_array[4] 	= 'Dull Blue';
                    $theme_name_array[5] 	= 'Yellow Stroke';
                    $theme_name_array[6] 	= 'Glossy Spring';
                    $theme_name_array[7] 	= 'Eco Green';
                    $theme_name_array[8] 	= 'Soft Pink';
                    $theme_name_array[9] 	= 'Tangerine';
                
                for($x = 1;$x <= 9; $x++){ ?>
                    <?php $theme_name = $theme_name_array[$x]; ?>
                    <option value="<?php echo $x ?>" <?php echo ((isset($piereg['pr_theme']) && $piereg['pr_theme'] == $x )?'selected="selected"':'') ?>>
                        <?php _e($theme_name,"piereg"); ?>
                    </option>
                <?php } ?>
            </select>
            <input type="hidden" name="is_advanced" value="1" />
            <span class="quotation"><?php _e("Select a theme for Pie Register.",'piereg') ?></span>
        </div>
        <div class="fields fields_submitbtn">
            <input name="action" value="pie_reg_settings" type="hidden" />
            <input type="submit" class="submit_btn" value="<?php _e("Save Settings","piereg"); ?>" />
        </div>
        <?php else:
			$this->require_once_file( $this->plugin_dir .'/menus/PieLicenseKeyPage.php');
			?>
    	<?php endif; ?>
<?php } else { ?>	
	
    <div class="fields">
        <div class="radio_fields">
            <input type="checkbox" name="display_hints" id="display_hints" value="1" <?php echo ($piereg['display_hints']=="1") ? 'checked="checked"' :''?> />
        </div>
        <label class="label_mar_top" for="display_hints">
			<?php _e("Show tips and hints on form editor tool.",'piereg') ?>
        </label>        
    </div>
    
    <hr class="seperator">
    
    <h3>
		<?php _e("Login Form",'piereg'); ?>
      </h3>
      <div class="fields">
        <label for="login_username_label">
          <?php _e("Username Label",'piereg') ?>
        </label>
        <input type="text" name="login_username_label" id="login_username_label" value="<?php echo $piereg['login_username_label']; ?>" class="input_fields" />
      </div>
      <div class="fields">
        <label for="login_username_placeholder">
          <?php _e("Username Placeholder",'piereg') ?>
        </label>
        <input type="text" name="login_username_placeholder" id="login_username_placeholder" value="<?php echo $piereg['login_username_placeholder']; ?>" class="input_fields" />
      </div>
      <div class="fields">
        <label for="login_password_label">
          <?php _e("Password Label",'piereg') ?>
        </label>
        <input type="text" name="login_password_label" id="login_password_label" value="<?php echo $piereg['login_password_label']; ?>" class="input_fields" />
      </div>
      <div class="fields">
        <label for="login_password_placeholder">
          <?php _e("Password Placeholder",'piereg') ?>
        </label>
        <input type="text" name="login_password_placeholder" id="login_password_placeholder" value="<?php echo $piereg['login_password_placeholder']; ?>" class="input_fields" />
      </div>
  
    <hr class="seperator">
    
    <h3>
		<?php _e("Forgot Password Form",'piereg'); ?>
      </h3>
      <div class="fields">
        <label for="forgot_pass_username_label">
          <?php _e("Username Label",'piereg') ?>
        </label>
        <input type="text" name="forgot_pass_username_label" id="forgot_pass_username_label" value="<?php echo $piereg['forgot_pass_username_label']; ?>" class="input_fields" />
      </div>
      <div class="fields">
        <label for="forgot_pass_username_placeholder">
          <?php _e("Username Placeholder",'piereg') ?>
        </label>
        <input type="text" name="forgot_pass_username_placeholder" id="forgot_pass_username_placeholder" value="<?php echo $piereg['forgot_pass_username_placeholder']; ?>" class="input_fields" />
      </div>
      
       <hr class="seperator">
    
    <h3><?php _e("Custom Logo",'piereg'); ?></h3>
    <div class="divIndent">	
        <div class="fields">
            <label for="logo"><?php _e('Custom Logo URL', 'piereg');?></label>
            <?php wp_enqueue_script('thickbox'); ?>
            <?php
            if( ( isset($piereg['custom_logo_url']) && $piereg['custom_logo_url'] == '') && (isset($piereg['logo']) && $piereg['logo'] != '') )
            $piereg['custom_logo_url'] = $piereg['logo'];?>
            <input id="pie_custom_logo_url" type="text" name="custom_logo_url" value="<?php echo $piereg['custom_logo_url'];?>" placeholder="<?php _e("Please enter Logo URL","piereg"); ?>" class="input_fields" style="width:43%;" />
            &nbsp;<sub><span style="font-size:16px;"><?php _e( 'OR', 'piereg' ); ?></span></sub>&nbsp;
            <?php add_thickbox();?>
            <button id="pie_custom_logo_button" class="button" type="button" value="1" name="pie_custom_logo_button">
            <?php _e( 'Select Image to Upload', 'piereg' ); ?>
            </button>
        </div>
        <div class="fields">
            <label for="custom_logo_title"><?php _e( 'Tooltip Text', 'piereg' ); ?></label>
            <input type="text" name="custom_logo_tooltip" class="input_fields" id="custom_logo_title" value="<?php echo $piereg['custom_logo_tooltip'];?>" placeholder="<?php _e("Enter logo tooltip text","piereg"); ?>" />
            <span class="quotation"><?php _e("Show tooltip on custom logo.","piereg"); ?></span>
        </div>
        <div class="fields">
            <label for="custom_logo_link"><?php _e( 'Link URL', 'piereg' ); ?></label>
            <input type="text" name="custom_logo_link" class="input_fields" id="custom_logo_link" value="<?php echo $piereg['custom_logo_link'];?>" 
                placeholder="<?php _e("Enter logo Link","piereg"); ?>" />
        </div>
        <?php if ( $piereg['custom_logo_url'] ) {?>
            <div class="fields">
                <label><?php _e( 'Selected Logo', 'piereg' ); ?></label>
                <img src="<?php echo $piereg['custom_logo_url'];?>" alt="<?php _e( 'Custom Logo', 'piereg' ); ?>" />
            </div>
            <div class="fields">
                <label><?php _e( 'Show Custom Logo', 'piereg' ); ?></label>
                <div class="radio_fields">
                    <input type="radio" name="show_custom_logo" value="1" 
                        id="show_custom_logo_yes" <?php echo ($piereg['show_custom_logo'] == "1")? 'checked="checked"' : '' ?> />
                    <label for="show_custom_logo_yes"><?php _e('Yes', 'piereg');?></label>
                    
                    <input type="radio" name="show_custom_logo" value="0" 
                        id="show_custom_logo_no" <?php echo ($piereg['show_custom_logo'] == "0")? 'checked="checked"' : '' ?> />
                    <label for="show_custom_logo_no"><?php _e('No', 'piereg');?></label>
                </div>
            </div>
        <?php } ?>   
    </div>
    <hr class="seperator" />
    <div class="fields">
      <div class="radio_fields">
      	<input type="checkbox" name="outputcss" id="outputcss" value="1" <?php echo ($piereg['outputcss']=="1")?'checked="checked"':''?> />
      </div>
      <label class="label_mar_top" for="outputcss"><?php _e("Let Pie Register generate custom CSS. Turn this off if you have themes installed that conflict with Pie Register's CSS.",'piereg') ?></label>
    </div>
    <div class="fields">
        <div class="radio_fields">
        	<input type="checkbox" name="outputjquery_ui" id="outputjquery_ui" value="1" <?php echo ($piereg['outputjquery_ui']=="1")?'checked="checked"':''?>  />
        </div>
        <label class="label_mar_top" for="outputjquery_ui"><?php _e("Let Pie Register generate jQuery UI for enhancements. Warning: Turning this off may restrict Pie Register's functionality. Do it at your own peril!",'piereg') ?></label>
    </div>
    <h3><?php _e("Custom CSS",'piereg'); ?></h3>
    <div class="fields">
      <!--<span class="quotation mar_left_none" ><?php //_e("If need to apply custom CSS to Pie Register, enter it here. Note: Do not use style tags.",'piereg') ?></span>-->
      <span class="quotation mar_left_none"><?php _e("Note: Custom CSS is now deprecated. Please copy the code and paste in your theme options or use another plugin. ",'piereg') ?></span>
      <textarea disabled="disabled" name="custom_css"><?php echo html_entity_decode($piereg['custom_css'],ENT_COMPAT,"UTF-8")?></textarea>      
    </div>
    <h3><?php _e("Tracking Code",'piereg'); ?></h3>
    <div class="fields">
      <!--<span class="quotation mar_left_none"><?php //_e("Enter your custom tracking code (Java Script) here.",'piereg') ?></span>-->
      <span class="quotation mar_left_none"><?php _e("Note: Tracking Code is now deprecated. Please copy the code and paste in your theme option or use another plugin. ",'piereg') ?></span>
      <textarea disabled="disabled" name="tracking_code"><?php echo html_entity_decode($piereg['tracking_code'],ENT_COMPAT,"UTF-8")?></textarea>         
    </div>
    <div class="fields fields_submitbtn">
        <input name="action" value="pie_reg_settings" type="hidden" />
        <input type="submit" class="submit_btn" value="<?php _e("Save Settings","piereg"); ?>" />
    </div>
<?php } ?>
</form>
</div>