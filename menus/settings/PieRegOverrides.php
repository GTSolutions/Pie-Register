<?php $piereg = PieReg_Base::get_pr_global_options(); ?>
<div class="forms_max_label">
<form action="" method="post" id="frm_settings_overrides">
  <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_settings_overrides','piereg_settings_overrides'); ?>
  <div class="fields">
    <div class="radio_fields">
      <input type="checkbox" name="redirect_user" id="redirect_user" value="1" <?php echo ($piereg['redirect_user']=="1")?'checked="checked"':''?> />	
    </div>
    <label for="redirect_user" class="label_mar_top">
      <?php _e("When a user is logged in, do not show login, registration and forgot password links.",'piereg') ?>
    </label>
  </div>
  <div class="fields">
    <div class="radio_fields">
      <input type="checkbox" name="show_admin_bar" id="show_admin_bar" value="1" <?php echo ($piereg['show_admin_bar']=="1")?'checked="checked"':''?> />	
    </div>
    <label for="show_admin_bar" class="label_mar_top">
      <?php _e("Do not show admin bar.",'piereg') ?>
    </label>
  </div>
  <div class="fields">
    <div class="radio_fields">
      <input type="checkbox" name="block_WP_profile" id="block_WP_profile" value="1" <?php echo ($piereg['block_WP_profile']=="1")?'checked="checked"':''?> />	
    </div>
    <label for="block_WP_profile" class="label_mar_top">
      <?php _e("Redirect users to custom profile page, if one exists.",'piereg') ?>
    </label>
  </div>
  <div class="fields">
    <div class="radio_fields">
      <input type="checkbox" name="block_wp_login" id="block_wp_login" value="1" <?php echo ($piereg['block_wp_login']=="1")?'checked="checked"':''?> />	
    </div>
    <label for="block_wp_login" class="label_mar_top">
      <?php _e("Do not allow users to login from the WordPress login page. Note: You must select an alternate login page.",'piereg') ?>
    </label>
  </div>
  <div class="fields">
    <div class="radio_fields">
      <input type="checkbox" name="allow_pr_edit_wplogin" id="allow_pr_edit_wplogin" value="1" <?php echo ($piereg['allow_pr_edit_wplogin']=="1")?'checked="checked"':''?> />	
    </div>
    <label for="allow_pr_edit_wplogin" class="label_mar_top">
      <?php _e("Allow Pie-Register to add header Footer in wp-login.php.",'piereg') ?>
    </label>
  </div>
  <div class="fields">
    <input name="action" value="pie_reg_settings" type="hidden" />
    <input type="submit" class="submit_btn submit_btn_mar_ryt" value="<?php _e("Save Settings","piereg"); ?>" />
  </div>
</form>
</div>