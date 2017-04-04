<?php $piereg = PieReg_Base::get_pr_global_options(); ?>
<div class="roles_container">
<form action="" method="post" id="frm_settings_allusers" onsubmit="return validateSettings();">
  <?php 
  if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_settings_allusers','piereg_settings_allusers'); ?>
    <div class="fields">
      <label for="alternate_login">
        <?php _e("Login Page",'piereg') ?>
      </label>
      <?php  $args =  array("show_option_no_change"=>"-- Please select one --","id"=>"alternate_login","name"=>"alternate_login","selected"=>$piereg['alternate_login']);         
                wp_dropdown_pages( $args ); ?>
      <span class="quotation">
      <?php _e("This page must contain the Pie Register Login form short code.",'piereg') ?>
      </span> </div>
    <div class="fields">
      <label for="alternate_register">
        <?php _e("Registration Page",'piereg') ?>
      </label>
      <?php  $args =  array("show_option_no_change"=>"-- Please select one --","id"=>"alternate_register","name"=>"alternate_register","selected"=>$piereg['alternate_register']);         
                wp_dropdown_pages( $args ); ?>
      <span class="quotation">
      <?php _e("This page must contain the Pie Register Registration form short code.",'piereg') ?>
      </span> </div>
    <div class="fields">
      <label for="alternate_forgotpass">
        <?php _e("Forgot Password Page",'piereg') ?>
      </label>
      <?php  $args =  array("show_option_no_change"=>"-- Please select one --","id"=>"alternate_forgotpass","name"=>"alternate_forgotpass","selected"=>$piereg['alternate_forgotpass']);         
                wp_dropdown_pages( $args ); ?>
      <span class="quotation">
      <?php _e("This page must contain the Pie Register Forgot Password form short code.",'piereg') ?>
      </span> </div>
    <div class="fields">
      <label for="alternate_profilepage">
        <?php _e("Profile Page",'piereg') ?>
      </label>
      <?php  $args =  array("show_option_no_change"=>"-- Please select one --","id"=>"alternate_profilepage","name"=>"alternate_profilepage","selected"=>$piereg['alternate_profilepage']);         
                wp_dropdown_pages( $args ); ?>
      <span class="quotation">
      <?php _e("This page must contain the Pie Register Profile section short code.",'piereg') ?>
      </span> </div>
    <div class="fields">
      <label for="after_login"> 
        <?php _e("After Login Page",'piereg') ?>
      </label>
      <?php $args 	= array("show_option_no_change"=>"Default","id"=>"after_login","name"=>"after_login","selected"=>$piereg['after_login'],"echo"=>false);
            $pages	= wp_dropdown_pages( $args );
			$url	= '<option value="url"'; 
			if($piereg['after_login'] == "url") $url.=' selected="selected"'; 
			$url.='>&lt;URL&gt;</option></select>';
			$pages	= str_replace('</select>', $url, $pages);
			echo $pages;
			?>
    </div>
    <div class="fields <?php echo ($piereg['after_login'] == "url") ? "": "hide"; ?>">
      <label for="alternate_login_url"></label>	
      <input type="url" name="alternate_login_url" id="alternate_login_url" value="<?php echo $piereg['alternate_login_url']; ?>" class="input_fields" />
    </div>
    <div class="fields">
      <label for="alternate_logout">
        <?php _e("After Logout Page",'piereg') ?>
      </label>
      <?php $args 	= array("show_option_no_change"=>"None","id"=>"alternate_logout","name"=>"alternate_logout","selected"=>$piereg['alternate_logout'],"echo"=>false);
	  		$pages2	= wp_dropdown_pages( $args );
			$url2	= '<option value="url"'; 
			if($piereg['alternate_logout'] == "url") $url2.=' selected="selected"'; 
			$url2.='>&lt;URL&gt;</option></select>';
			$pages2	= str_replace('</select>', $url2, $pages2);
			echo $pages2;
			?>
    </div>   
    <div class="fields <?php echo ($piereg['alternate_logout'] == "url") ? "": "hide"; ?>">
      <label for="alternate_logout_url"></label>	
      <input type="url" name="alternate_logout_url" id="alternate_logout_url" value="<?php echo $piereg['alternate_logout_url']; ?>" class="input_fields" />
    </div>    
  <input name="action" value="pie_reg_settings" type="hidden" />
  <div class="fields fields_submitbtn">
    <input type="submit" class="submit_btn" value="<?php _e("Save Settings","piereg"); ?>" />
  </div>
</form>
</div>