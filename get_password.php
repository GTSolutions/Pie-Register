<?php 
$warning 	= apply_filters('piereg_reset_password_warning',__("Enter your new password below.",'piereg')); # newlyAddedHookFilter
$success	= "";
$errors 	= new WP_Error();

if ( isset($_POST['pass1']) && $_POST['pass1'] != $_POST['pass2'] ) {	
	$errors->add( 'password_reset_mismatch', 
				apply_filters("piereg_reset_password_error",__('The passwords do not match.', 'piereg')) # newlyAddedHookFilter
			);
	
}

do_action( 'validate_password_reset', $errors, $user );
if ( ( ! $errors->get_error_code() ) && isset( $_POST['pass1'] ) && !empty( $_POST['pass1'] ) ) {
	reset_password($user, $_POST['pass1']);
	
	$success = apply_filters('piereg_reset_password_success',__( 'Your password has been reset.','piereg' )); # newlyAddedHookFilter	
}
?>

<div id="piereg_login">
  <?php if ($success != "") {
	 	?>
  <p class="piereg_message"> <?php echo $success?> </p>
  <?php
	} else if (isset($errors->errors['password_reset_mismatch'][0]) && !empty($errors->errors['password_reset_mismatch'][0])  ) {  
		?>
  <p class="piereg_login_error">
    <?php  print_r($errors->errors['password_reset_mismatch'][0]); ?>
  </p>
  <?php
	} else {
?>
  <p class="piereg_warning"> <?php echo $warning?> </p>
  <?php 
	
	}
  ?>
  <form name="resetpassform" id="piereg_resetpassform" action="<?php echo esc_url( site_url( 'wp-login.php?action=resetpass&key=' . urlencode( $_GET['key'] ) . '&login=' . urlencode( $_GET['login'] ), 'login_post' ) ); ?>" method="post" autocomplete="off">
    <input type="hidden" id="user_login" value="<?php echo esc_attr( $_GET['login'] ); ?>" autocomplete="off">
    <p>
      <label for="pass1">
        <?php _e('New password', 'piereg'); ?>
      </label>
      <br />
      <input type="password" name="pass1" id="pass1" class="input input_fields validate[required]" size="20" value="" autocomplete="off">
    </p>
    <p>
      <label for="pass2">
        <?php _e('Confirm new password', 'piereg'); ?>
      </label>
      <br />
      <input type="password" name="pass2" id="pass2" class="input input_fields validate[required,equals[pass1]]" size="20" value="" autocomplete="off">
    </p>
    <p class="submit">
      <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="Reset Password">
    </p>
    <?php  
     	$form_links  = '<p id="nav"> <a href="'.wp_login_url().'">Log in</a> | <a href="'.site_url("/wp-login.php?action=register").'">Register</a> </p>';
    	$form_links .= '<p id="backtoblog"><a title="Are you lost?" href="'.bloginfo("url").'?>">← Back to Pie Register</a></p>';  		
		apply_filters( 'pie_getpassword_form_links', $form_links );		
		echo $form_links;
		?>
  </form>
</div>
