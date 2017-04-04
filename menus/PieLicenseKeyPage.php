<?php
global $errors;
$license_key_errors = "";
if(isset($errors->errors['piereg_license_error']) && !empty($errors->errors['piereg_license_error']) && is_array($errors->errors['piereg_license_error']))
{
	foreach($errors->errors['piereg_license_error'] as $error_val){
		$license_key_errors .= "<p><strong>".$error_val."</strong></p>";
	}
}
?>
<div id="container"  class="pieregister-admin">
	<?php
	$piereg = PieReg_Base::get_pr_global_options();
    if( isset($_POST['error']) && !empty($_POST['error']) ){
        echo '<div id="error" class="error fade"><p><strong>' . $_POST['error'] . '</strong></p></div>';
    }
	elseif( isset($license_key_errors) && !empty($license_key_errors) ){
        echo '<div id="error" class="error fade">' . $license_key_errors . '</div>';
    }
    elseif( isset($_POST['success']) && !empty($_POST['success']) ){
        echo '<div id="message" class="updated fade"><p><strong>' . $_POST['success'] . '</strong></p></div>';
	}
    ?>
    <div class="right_section">
        <div class="settings">
        	<?php if( $_GET['tab'] != 'license' ){ ?>
	        	<h2><?php _e("Licence Key Settings",'piereg') ?></h2>
            <?php } ?>
            <div class="fields"> <?php _e("You need to activate the license key to use premium features",'piereg') ?></div>
            <form method="post" action="">
				<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_license_key_nonce','piereg_license_key_nonce'); ?>
            	<?php if( $_GET['tab'] != 'license' ){ ?>
               	 	<h3><?php _e("Activation",'piereg') ?></h3>
            	<?php } ?>
                <div class="fields">
                    <label for="piereg_license_key"><?php _e("License Key",'piereg') ?></label>
                    <input type="text" name="piereg_license_key" id="piereg_license_key" value="<?php echo (isset($piereg['piereg_license_key'])?$piereg['piereg_license_key']:""); ?>" class="input_fields" required="required" autocomplete="off">
                    <span class="piereg_license_key_icon_span"><img class="piereg_license_key_icon" title="" src="<?php echo plugins_url("images/warning.png", dirname(__FILE__) ); ?>"></span>
                </div>
                <div class="fields">
                    <label for="piereg_license_email"><?php _e("Email Address",'piereg') ?></label>
                    <input type="email" name="piereg_license_email" id="piereg_license_email" value="<?php echo (isset($piereg['piereg_license_email'])?$piereg['piereg_license_email']:""); ?>" class="input_fields" required="required" autocomplete="off">
                    <span class="piereg_license_key_icon_span"><img class="piereg_license_key_icon" title="" src="<?php echo plugins_url("images/warning.png", dirname(__FILE__) ); ?>"></span>
                </div>
                <div class="fields fields2">
	                <input type="submit" class="submit_btn" name="save_license_key_settings" value=" <?php _e("Activate","piereg");?> " />
                    <p><a href="https://store.genetech.co/" target="_blank" title="Purchase License Key"><?php _e("Already Purchased",'piereg') ?>?</a> or <a href="http://shopdev.genetechsolutions.com/baqar/wordpress-401" target="_blank" title="Purchase License Key"><?php _e("Click here to Purchase",'piereg') ?></a></p>
                </div>
    	        <input type="hidden" name="piereg_activate_license_key" value="1" />
    	        <input type="hidden" name="piereg_license_key_redirect_to" value="<?php echo (!empty($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:""); ?>" />
            </form>
        </div>
    </div>
</div>