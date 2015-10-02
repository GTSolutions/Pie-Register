<?php

global $wpdb;

$piereg = get_option( 'pie_register_2' );

$piereg_custom = get_option( 'pie_register_custom' );

$prefix=$wpdb->prefix."pieregister_";

$codetable=$prefix."code";

$incodes=$wpdb->get_results("SELECT `name` FROM ".$codetable." WHERE `status`='1';");

foreach($incodes as $incode){

$invite_codes.=$incode->name."\n";

}

if( $_POST['notice'] )

echo '<div id="message" class="updated fade"><p><strong>' . $_POST['notice'] . '.</strong></p></div>';

if( !is_array($piereg['profile_req']) )

$piereg['profile_req'] = array();

if( is_array($piereg['codepass']) ){

foreach( $piereg['codepass'] as $code ){

$cod.=(trim($code)."\n");

}

$piereg['codepass']=$cod;

}

$types = '<option value="text">Text Field</option><option value="date">Date Field</option><option value="select">Select Field</option><option value="checkbox">Checkbox</option><option value="radio">Radio Box</option><option value="textarea">Text Area</option><option value="hidden">Hidden Field</option>';

$extras = '<div class="extraoptions" style="float:left"><label>Extra Options: <input type="text" class="extraops" name="extraoptions[0]" value="" /></label></div>';

if( is_array($piereg_custom) ){

foreach( $piereg_custom as $k => $v ) {

$types = '<option value="text"';

if( $v['fieldtype'] == 'text' ) $types .= ' selected="selected"';

$types .='>Text Field</option><option value="date"';

if( $v['fieldtype'] == 'date' ) $types .= ' selected="selected"';

$types .='>Date Field</option><option value="select"';

if( $v['fieldtype'] == 'select' ) $types .= ' selected="selected"';

$types .= '>Select Field</option><option value="checkbox"';

if( $v['fieldtype'] == 'checkbox' ) $types .= ' selected="selected"';

$types .= '>Checkbox</option><option value="radio"';

if( $v['fieldtype'] == 'radio' ) $types .= ' selected="selected"';

$types .= '>Radio Box</option><option value="textarea"';

if( $v['fieldtype'] == 'textarea' ) $types .= ' selected="selected"';

$types .= '>Text Area</option><option value="hidden"';

if( $v['fieldtype'] == 'hidden' ) $types .= ' selected="selected"';

$types .= '>Hidden Field</option>';

$extras = '<div class="extraoptions" style="float:left;"><label>Extra Options: <input type="text" name="extraoptions['.$k.']" class="extraops" value="' . $v['extraoptions'] . '" /></label></div>';

$rows .= '<tr valign="top" class="row_block">

<th scope="row"><label for="custom">' . __('Custom Field', 'piereg') . '</label></th>

<td><input type="text" name="label['.$k.']" class="custom" style="font-size:16px;padding:2px; width:50%; display:block;clear:both;" value="' . $v['label'] . '" /> &nbsp; ';

$rows .= '<select name="fieldtype['.$k.']" class="fieldtype">'.$types.'</select> '.$extras.' &nbsp; ';

$rows .= '<label><input type="checkbox" name="reg['.$k.']" class="reg" value="1"';

if( $v['reg'] ) $rows .= ' checked="checked"';

$rows .= ' /> ' .  __('Add Registration Field', 'piereg') . '</label> &nbsp; <label><input type="checkbox" name="profile['.$k.']" class="profile" value="1"';

if( $v['profile'] ) $rows .= ' checked="checked"';

$rows .= ' /> ' . __('Add Profile Field', 'piereg') . '</label> &nbsp; <label><input type="checkbox" name="required['.$k.']" class="required" value="1"';

if( $v['required'] ) $rows .= ' checked="checked"';

$rows .= ' /> ' . __('Required', 'piereg') . '</label> &nbsp;

<a href="#" onClick="return selrem(this);" class="remove_row"><img src="' . $this->plugin_url . 'removeBtn.gif" alt="' . __("Remove Row","piereg") . '" title="' . __("Remove Row","piereg") . '" /></a>

<a href="#" onClick="return seladd(this);" class="add_row"><img src="' . $this->plugin_url . 'addBtn.gif" alt="' . __("Add Row","piereg") . '" title="' . __("Add Row","piereg") . '" /></a></td>

</tr>';

}

}

//Check if there's Expired Codes

$expiredcodes=$this->SelectCode();

if($expiredcodes){

$echoExpiredCode="<div class='expired_code'><ul>";

foreach($expiredcodes as $expiredcode){

$echoExpiredCode.="<li>".$expiredcode->name."</li>";

}

$echoExpiredCode.="</ul></div>";

}

?>

<style>

.fade{opacity:0;-webkit-transition:opacity .15s linear;transition:opacity .15s linear}

.fade.in{opacity:1}

.alert{padding:15px;margin-bottom:20px;border:1px solid transparent;border-radius:4px}.alert h4{margin-top:0;color:inherit}.alert .alert-link{font-weight:bold}.alert>p,.alert>ul{margin-bottom:0}.alert>p+p{margin-top:5px}.alert-dismissable{padding-right:35px}.alert-dismissable .close{position:relative;top:-2px;right:-21px;color:inherit}.alert-success{color:#3c763d;background-color:#dff0d8;border-color:#d6e9c6}.alert-success hr{border-top-color:#c9e2b3}.alert-success .alert-link{color:#2b542c}.alert-info{color:#31708f;background-color:#d9edf7;border-color:#bce8f1}.alert-info hr{border-top-color:#a6e1ec}.alert-info .alert-link{color:#245269}.alert-warning{color:#8a6d3b;background-color:#fcf8e3;border-color:#faebcc}.alert-warning hr{border-top-color:#f7e1b5}.alert-warning .alert-link{color:#66512c}.alert-danger{color:#a94442;background-color:#f2dede;border-color:#ebccd1}.alert-danger hr{border-top-color:#e4b9c0}.alert-danger .alert-link{color:#843534}

</style>

<div class="alert alert-warning fade in">

<p><?php _e('You are currently using version of 1 of Pie-Register, which will be deprecated soon. We have released a Version 2.0 of this plugin. Please ' , 'piereg');?><a href="http://pieregister.genetechsolutions.com/pie-register-version-2-0-beta-has-arrived/" title="Pie-Register Version 2.0" target="_blank"><?php _e('Click here', 'piereg');?></a> <?php _e('to get more information.');?></p>

</div>

<div id="pie-register" class="wrap">

<h2>

<?php _e('Pie Register Settings', 'piereg')?>

</h2>

<form method="post" action="" enctype="multipart/form-data">

<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg-update-options'); ?>

<p class="submit">

<input name="Submit" value="<?php _e('Save Changes','piereg');?>" type="submit" />

<table class="form-table">

<tbody>

<tr valign="top">

<th scope="row"><label for="password">

<?php _e('Password', 'piereg');?>

</label></th>

<td><label>

<input type="checkbox" name="piereg_password" id="password" value="1" <?php if( $piereg['password'] ) echo 'checked="checked"';?> />

<?php _e('Allow New Registrations to set their own Password', 'piereg');?>

</label>

<br />

<label>

<input type="checkbox" name="piereg_password_meter" id="pwm" value="1" <?php if( $piereg['password_meter'] ) echo 'checked="checked"';?> />

<?php _e('Enable Password Strength Meter','piereg');?>

</label>

<div id="meter" style="margin-left:20px;">

<label>

<span style="width:60px;display:inline-block"><?php _e('Short', 'piereg');?></span>

<input type="text" name="piereg_short" value="<?php echo $piereg['short'];?>" />

</label>

<br />

<label>

<span style="width:60px;display:inline-block"><?php _e('Bad', 'piereg');?></span>

<input type="text" name="piereg_bad" value="<?php echo $piereg['bad'];?>" />

</label>

<br />

<label>

<span style="width:60px;display:inline-block"><?php _e('Good', 'piereg');?></span>

<input type="text" name="piereg_good" value="<?php echo $piereg['good'];?>" />

</label>

<br />

<label>

<span style="width:60px;display:inline-block"><?php _e('Strong', 'piereg');?></span>

<input type="text" name="piereg_strong" value="<?php echo $piereg['strong'];?>" />

</label>

<br />

<label>

<span style="width:60px;display:inline-block"><?php _e('Mismatch', 'piereg');?></span>

<input type="text" name="piereg_mismatch" value="<?php echo $piereg['mismatch'];?>" />

</label>

<br />

</div></td>

</tr>

<tr valign="top">

<th scope="row"><label for="logo">

<?php _e('Custom Logo URL', 'piereg');?>

</label></th>

<td><?php if($piereg['custom_logo_url'] == '' && $piereg['logo'] != '') $piereg['custom_logo_url'] = $piereg['logo'];?>

<input id="pie_custom_logo_url" type="text" name="custom_logo_url" value="<?php echo $piereg['custom_logo_url'];?>" placeholder="Please enter Logo URL" style="width:60%;" /> or <button id="pie_custom_logo_button" class="button" type="button" value="1" name="pie_custom_logo_button">Select Image to Upload</button>

<?php if ( $piereg['custom_logo_url'] ) {?>

<br />

<img src="<?php echo $piereg['custom_logo_url'];?>" alt="" /><br />

<label>

<input type="checkbox" name="remove_logo" value="1" />

<?php _e('Delete Logo', 'piereg');?>

</label>

<?php } /*else { ?>

<p><small><strong>

<?php _e('Having troubles uploading?','piereg');?>

</strong>

<?php _e('Uncheck "Organize my uploads into month- and year-based folders" in','piereg');?>

<a href="<?php echo get_option('siteurl');?>/wp-admin/options-misc.php">

<?php _e('Miscellaneous Settings', 'piereg');?>

</a>.

<?php _e('(You can recheck this option after your logo has uploaded.)','piereg');?>

</small></p>

<?php }*/ ?></td>

</tr>

<tr valign="top">

<th scope="row"><label for="paypal_option">

<?php _e('Enable Paypal', 'piereg');?>

</label></th>

<td><label>

<input type="checkbox" name="piereg_paypal_option" id="paypal_option" value="1" <?php if( $piereg['paypal_option'] ) echo 'checked="checked"';?> />

<?php _e('Add Paypal payment gateway for registration.', 'piereg');?>

</label></td>

</tr>

<tr valign="top">

<th scope="row"><label for="email_verify">

<?php _e('Email Verification', 'piereg');?>

</label></th>

<td><label>

<input type="checkbox" name="piereg_email_verify" id="email_verify" onchange="javascript:toggleVerificationType('#email_verify','#admin_verify');" value="1" <?php if( $piereg['email_verify'] ) echo 'checked="checked"';?> />

<?php _e('Prevent fake email address registrations.', 'piereg');?>

</label>

<br />

<?php _e('Requires new registrations to click a link in the notification email to enable their account.', 'piereg');?>

<div id="grace">

<label for="email_delete_grace"><strong>

<?php _e('Grace Period (days)', 'piereg');?>

</strong>: </label>

<input type="text" name="piereg_email_delete_grace" id="email_delete_grace" style="width:50px;" value="<?php echo $piereg['email_delete_grace'];?>" />

<br />

<?php _e('Unverified Users will be automatically deleted after grace period expires', 'piereg');?>

</div></td>

</tr>

<tr valign="top">

<th scope="row"><label for="admin_verify">

<?php _e('Admin Verification', 'piereg');?>

</label></th>

<td><label>

<input type="checkbox" onchange="javascript:toggleVerificationType('#admin_verify','#email_verify');" name="piereg_admin_verify" id="admin_verify" value="1" <?php if( $piereg['admin_verify'] ) echo 'checked="checked"';?> />

<?php _e('Moderate all user registrations to require admin approval. NOTE: Email Verification must be DISABLED to use this feature.', 'piereg');?>

</label></td>

</tr>

<tr valign="top">

<th scope="row"><label for="code">

<?php _e($piereg['codename'].' Code', 'piereg');?>

</label></th>

<td><label>

<input type="checkbox" name="piereg_code" id="code" value="1" <?php if( $piereg['code'] ) echo 'checked="checked"';?> />

<?php _e('Enable '.$piereg['codename'].' Code(s)', 'piereg');?>

</label>

<div id="codepass">

<label>

<input type="checkbox" name="piereg_dash_widget" value="1" <?php if( $piereg['dash_widget'] ) echo 'checked="checked"'; ?>  />

<?php _e('Enable '.$piereg['codename'].' Code Tracking Dashboard Widget', 'piereg');?>

</label>

<br />

<label>

<input type="checkbox" name="piereg_code_req" id="code_req" value="1" <?php if( $piereg['code_req'] ) echo 'checked="checked"';?> />

<?php _e('Require '.$piereg['codename'].' Code to Register', 'piereg');?>

</label>

<div class="code_block">

<textarea rows="25" cols="50" name="piereg_codepass"><?php echo $invite_codes;//echo $piereg['codepass'];?></textarea>

<?php /*?><!-- &nbsp;

<a href="#" onClick="return selremcode(this);" class="remove_code"><img src="<?php echo $this->plugin_url; ?>removeBtn.gif" alt="<?php _e("Remove Code","piereg")?>" title="<?php _e("Remove Code","piereg")?>" /></a>

<a href="#" onClick="return seladdcode(this);" class="add_code"><img src="<?php echo $this->plugin_url; ?>addBtn.gif" alt="<?php _e("Add Code","piereg")?>" title="<?php _e("Add Code","piereg")?>" /></a>--> <?php */?>

</div>

<small>

<?php _e('One of these codes will be required for users to register.', 'piereg');?>

</small>

<div> <?php echo ($echoExpiredCode)?$echoExpiredCode. _e('Expired '.$piereg['codename'].' Code(s)', 'piereg'):''; ?>

<label>

<input type="textbox" name="piereg_codename" id="codename" value="<?php echo $piereg['codename'];?>"  />

<?php _e('Rename '.$piereg['codename'].' Code(s) Field', 'piereg');?>

</label>

</div>

<small>

<?php _e('Please note: You will need to update the Message Body on Email Notification Page as you rename.', 'piereg');?>

</small>

<div>

<label>

<input type="checkbox" name="piereg_code_auto_del" id="code_auto_del" value="1" <?php if( $piereg['code_auto_del'] ) echo 'checked="checked"';?> />

<?php _e('Auto Delete the Expired '.$piereg['codename'].' Code', 'piereg');?>

</label>

</div>

<div>

<label>

<input size="3" type="textbox" name="piereg_codeexpiry" id="codeexpiry" value="<?php echo $piereg['codeexpiry'];?>" onkeyup="javascript:this.value=this.value.replace(/[^0-9]/g, '');"  />

<?php _e('Times each '.$piereg['codename'].' code will be used.', 'piereg');?>

</label>

</div>

<small>

<?php _e('Please note: Enter Numeric Value Only. "0" (Zero) means Unlimited', 'piereg');?>

</small> </div></td>

</tr>

<tr valign="top">

<th scope="row"><label for="captcha">

<?php _e('CAPTCHA', 'piereg');?>

</label></th>

<td><label>

<input type="radio" name="piereg_captcha" id="none" value="0" <?php if( $piereg['captcha'] == 0 ) echo 'checked="checked"';?> />

<?php _e('None', 'piereg');?>

</label>

<input type="radio" name="piereg_captcha" id="captcha" value="1" <?php if( $piereg['captcha'] == 1 ) echo 'checked="checked"';?> />

<?php _e('Simple CAPTCHA', 'piereg');?>

</label>

<label>

<input type="radio" name="piereg_captcha" id="recaptcha" value="2" <?php if( $piereg['captcha'] == 2 ) echo 'checked="checked"';?> />

<a href="http://recaptcha.net/">

<?php _e('reCAPTCHA','piereg');?>

</a></label>

<div id="reCAPops">

<label for="public_key">

<?php _e('reCAPTCHA Public Key:','piereg');?>

</label>

<input type="text" style="width:500px;" name="piereg_reCAP_public_key" id="public_key" value="<?php echo $piereg['reCAP_public_key'];?>" />

<a href="https://www.google.com/recaptcha/admin/create" target="_blank">

<?php _e('Sign up &raquo;','piereg');?>

</a><br />

<label for="private_key">

<?php _e('reCAPTCHA Private Key:','piereg');?>

</label>

<input type="text" style="width:500px;" id="private_key" name="piereg_reCAP_private_key" value="<?php echo $piereg['reCAP_private_key'];?>" />

</div></td>

</tr>

<tr valign="top">

<th scope="row"><label for="disclaimer">

<?php _e('Disclaimer', 'piereg');?>

</label></th>

<td><label>

<input type="checkbox" name="piereg_disclaimer" id="disclaimer" value="1" <?php if($piereg['disclaimer']) echo 'checked="checked"';?> />

<?php _e('Enable Disclaimer','piereg');?>

</label>

<div id="disclaim_content">

<label for="disclaimer_title">

<?php _e('Disclaimer Title','piereg');?>

</label>

<input type="text" name="piereg_disclaimer_title" id="disclaimer_title" value="<?php echo html_entity_decode($piereg['disclaimer_title']);?>" />

<br />

<label for="disclaimer_content">

<?php _e('Disclaimer Content','piereg');?>

</label>

<br />

<textarea name="piereg_disclaimer_content" id="disclaimer_content" cols="25" rows="10" style="width:80%;height:300px;display:block;"><?php echo html_entity_decode($piereg['disclaimer_content']);?></textarea>

<br />

<label for="disclaimer_agree">

<?php _e('Agreement Text','piereg');?>

</label>

<input type="text" name="piereg_disclaimer_agree" id="disclaimer_agree" value="<?php echo ($piereg['disclaimer_agree']);?>" />

</div></td>

</tr>

<tr valign="top">

<th scope="row"><label for="license">

<?php _e('License Agreement', 'piereg');?>

</label></th>

<td><label>

<input type="checkbox" name="piereg_license" id="license" value="1" <?php if($piereg['license']) echo 'checked="checked"';?> />

<?php _e('Enable License Agreement','piereg');?>

</label>

<div id="lic_content">

<label for="license_title">

<?php _e('License Title','piereg');?>

</label>

<input type="text" name="piereg_license_title" id="license_title" value="<?php echo ($piereg['license_title']);?>" />

<br />

<label for="license_content">

<?php _e('License Content','piereg');?>

</label>

<br />

<textarea name="piereg_license_content" id="license_content" cols="25" rows="10" style="width:80%;height:300px;display:block;"><?php echo html_entity_decode($piereg['license_content']);?></textarea>

<br />

<label for="license_agree">

<?php _e('Agreement Text','piereg');?>

</label>

<input type="text" name="piereg_license_agree" id="license_agree" value="<?php echo ($piereg['license_agree']);?>" />

</div></td>

</tr>

<tr valign="top">

<th scope="row"><label for="privacy">

<?php _e('Privacy Policy', 'piereg');?>

</label></th>

<td><label>

<input type="checkbox" name="piereg_privacy" id="privacy" value="1" <?php if($piereg['privacy']) echo 'checked="checked"';?> />

<?php _e('Enable Privacy Policy','piereg');?>

</label>

<div id="priv_content">

<label for="privacy_title">

<?php _e('Privacy Policy Title','piereg');?>

</label>

<input type="text" name="piereg_privacy_title" id="privacy_title" value="<?php echo $piereg['privacy_title'];?>" />

<br />

<label for="privacy_content">

<?php _e('Privacy Policy Content','piereg');?>

</label>

<br />

<textarea name="piereg_privacy_content" id="privacy_content" cols="25" rows="10" style="width:80%;height:300px;display:block;"><?php echo html_entity_decode($piereg['privacy_content']);?></textarea>

<br />

<label for="privacy_agree">

<?php _e('Agreement Text','piereg');?>

</label>

<input type="text" name="piereg_privacy_agree" id="privacy_agree" value="<?php echo $piereg['privacy_agree'];?>" />

</div></td>

</tr>

<tr valign="top">

<th scope="row"><label for="email_exists">

<?php _e('Allow Existing Email', 'piereg');?>

</label></th>

<td><label>

<input type="checkbox" name="piereg_email_exists" id="email_exists" value="1" <?php if( $piereg['email_exists'] ) echo 'checked="checked""';?> />

<?php _e('Allow new registrations to use an email address that has been previously registered', 'piereg');?>

</label></td>

</tr>

<tr valign="top">

<th scope="row"><label for="login_redirect">

<?php _e('After Signup URL', 'piereg');?>

</label></th>

<td><input type="text" name="piereg_login_redirect" id="login_redirect" style="width:250px;" value="<?php echo $piereg['login_redirect'];?>" />

<small>

<?php _e('Users will be landed here after Signup.', 'piereg');?>

</small></td>

</tr>

<tr valign="top">

<th scope="row"><label for="loginredirect">

<?php _e('After Sign-in URL', 'piereg');?>

</label></th>

<td><input type="text" name="piereg_loginredirect" id="loginredirect" style="width:250px;" value="<?php echo $piereg['loginredirect'];?>" />

<small>

<?php _e('Users will be landed here after Sign in.', 'piereg');?>

</small></td>

</tr>

</tbody>

</table>

<h3>

<?php _e('Additional Profile Fields', 'piereg');?>

</h3>

<p>

<?php _e('Check the fields you would like to appear on the Registration Page.', 'piereg');?>

</p>

<table class="form-table">

<tbody>

<tr valign="top">

<th scope="row"><label for="name">

<?php _e('Name', 'piereg');?>

</label></th>

<td><label>

<input type="checkbox" name="piereg_firstname" id="name" value="1" <?php if( $piereg['firstname'] ) echo 'checked="checked"';?> />

<?php _e('First Name', 'piereg');?>

</label>

&nbsp;

<label>

<input type="checkbox" name="piereg_lastname" value="1" <?php if( $piereg['lastname'] ) echo 'checked="checked"';?> />

<?php _e('Last Name', 'piereg');?>

</label></td>

</tr>

<tr valign="top">

<th scope="row"><label for="contact">

<?php _e('Contact Info', 'piereg');?>

</label></th>

<td><label>

<input type="checkbox" name="piereg_website" id="contact" value="1" <?php if( $piereg['website'] ) echo 'checked="checked"';?> />

<?php _e('Website', 'piereg');?>

</label>

&nbsp;

<label>

<input type="checkbox" name="piereg_aim" value="1" <?php if( $piereg['aim'] ) echo 'checked="checked"';?> />

<?php _e('AIM', 'piereg');?>

</label>

&nbsp;

<label>

<input type="checkbox" name="piereg_yahoo" value="1" <?php if( $piereg['yahoo'] ) echo 'checked="checked"';?> />

<?php _e('Yahoo IM', 'piereg');?>

</label>

&nbsp;

<label>

<input type="checkbox" name="piereg_jabber" value="1" <?php if( $piereg['jabber'] ) echo 'checked="checked"';?> />

<?php _e('Jabber / Google Talk', 'piereg');?>

</label>

&nbsp;

<label>

<input type="checkbox" name="piereg_phone" value="1" <?php if( $piereg['phone'] ) echo 'checked="checked"';?> />

<?php _e('Phone # / Mobile #.', 'piereg');?>

</label></td>

</tr>

<tr valign="top">

<th scope="row"><label for="about">

<?php _e('About Yourself', 'piereg');?>

</label></th>

<td><label>

<input type="checkbox" name="piereg_about" id="name" value="1" <?php if( $piereg['about'] ) echo 'checked="checked"';?> />

<?php _e('About Yourself', 'piereg');?>

</label></td>

</tr>

<tr valign="top">

<th scope="row"><label for="req">

<?php _e('Required Profile Fields', 'piereg');?>

</label></th>

<td><label>

<input type="checkbox" name="piereg_profile_req[]" value="firstname" <?php if( in_array('firstname', $piereg['profile_req']) ) echo 'checked="checked"';?> />

<?php _e('First Name', 'piereg');?>

</label>

&nbsp;

<label>

<input type="checkbox" name="piereg_profile_req[]" value="lastname" <?php if( in_array('lastname', $piereg['profile_req']) ) echo 'checked="checked"';?> />

<?php _e('Last Name', 'piereg');?>

</label>

&nbsp;

<label>

<input type="checkbox" name="piereg_profile_req[]" value="website" <?php if( in_array('website', $piereg['profile_req']) ) echo 'checked="checked"';?> />

<?php _e('Website', 'piereg');?>

</label>

&nbsp;

<label>

<input type="checkbox" name="piereg_profile_req[]" value="aim" <?php if( in_array('aim', $piereg['profile_req']) ) echo 'checked="checked"';?> />

<?php _e('AIM', 'piereg');?>

</label>

&nbsp;

<label>

<input type="checkbox" name="piereg_profile_req[]" value="yahoo" <?php if( in_array('yahoo', $piereg['profile_req']) ) echo 'checked="checked"';?> />

<?php _e('Yahoo IM', 'piereg');?>

</label>

&nbsp;

<label>

<input type="checkbox" name="piereg_profile_req[]" value="jabber" <?php if( in_array('jabber', $piereg['profile_req']) ) echo 'checked="checked"';?> />

<?php _e('Jabber / Google Talk', 'piereg');?>

</label>

&nbsp;

<label>

<input type="checkbox" name="piereg_profile_req[]" value="phone" <?php if( in_array('phone', $piereg['profile_req']) ) echo 'checked="checked"';?> />

<?php _e('Phone # / Mobile #', 'piereg');?>

</label>

&nbsp;

<label>

<input type="checkbox" name="piereg_profile_req[]" value="about" <?php if( in_array('about', $piereg['profile_req']) ) echo 'checked="checked"';?> />

<?php _e('About Yourself', 'piereg');?>

</label></td>

</tr>

<tr valign="top">

<th scope="row"><label for="require_style">

<?php _e('Required Field Style Rules', 'piereg');?>

</label></th>

<td><input type="text" name="piereg_require_style" id="require_style" value="<?php echo $piereg['require_style'];?>" style="width: 350px;" /></td>

</tr>

</tbody>

</table>

<h3>

<?php _e('User Defined Fields', 'piereg');?>

</h3>

<p>

<?php _e('Enter the custom fields you would like to appear on the Registration Page.', 'piereg');?>

</p>

<p><small>

<?php _e('Enter Extra Options for Select, Checkboxes and Radio Fields as comma seperated values. For example, if you chose a select box for a custom field of "Gender", your extra options would be "Male,Female".','piereg');?>

</small></p>

<table class="form-table">

<tbody>

<?php if( $rows ){ echo $rows; }else{ ?>

<tr valign="top" class="row_block">

<th scope="row"><label for="custom">

<?php _e('Custom Field', 'piereg');?>

</label></th>

<td><input type="text" name="label[0]" class="custom" style="font-size:16px;padding:2px; width:50%;clear:both;display:block;" value="" />

&nbsp;

<select class="fieldtype" name="fieldtype[0]">

<?php echo $types; ?>

</select>

<?php echo $extras;?> &nbsp;

<label>

<input type="checkbox" name="reg[0]" class="reg" value="1" />

<?php _e('Add Registration Field', 'piereg');?>

</label>

&nbsp;

<label>

<input type="checkbox" name="profile[0]"  class="profile" value="1" />

<?php _e('Add Profile Field', 'piereg');?>

</label>

&nbsp;

<label>

<input type="checkbox" name="required[0]" class="required" value="1" />

<?php _e('Required', 'piereg');?>

</label>

&nbsp; <a href="#" onClick="return selrem(this);" class="remove_row"><img src="<?php echo $this->plugin_url; ?>removeBtn.gif" alt="<?php _e("Remove Row","piereg")?>" title="<?php _e("Remove Row","piereg")?>" /></a> <a href="#" onClick="return seladd(this);" class="add_row"><img src="<?php echo $this->plugin_url; ?>addBtn.gif" alt="<?php _e("Add Row","piereg")?>" title="<?php _e("Add Row","piereg")?>" /></a></td>

</tr>

<?php } ?>

</tbody>

</table>

<table class="form-table">

<tbody>

<tr valign="top">

<th scope="row"><label for="date">

<?php _e('Date Field Settings', 'piereg');?>

</label></th>

<td><label>

<?php _e('First Day of the Week','piereg');?>

:

<select type="select" name="piereg_firstday">

<option value="7" <?php if( $piereg['firstday'] == '7' ) echo 'selected="selected"';?>>

<?php _e('Monday','piereg');?>

</option>

<option value="1" <?php if( $piereg['firstday'] == '1' ) echo 'selected="selected"';?>>

<?php _e('Tuesday','piereg');?>

</option>

<option value="2" <?php if( $piereg['firstday'] == '2' ) echo 'selected="selected"';?>>

<?php _e('Wednesday','piereg');?>

</option>

<option value="3" <?php if( $piereg['firstday'] == '3' ) echo 'selected="selected"';?>>

<?php _e('Thursday','piereg');?>

</option>

<option value="4" <?php if( $piereg['firstday'] == '4' ) echo 'selected="selected"';?>>

<?php _e('Friday','piereg');?>

</option>

<option value="5" <?php if( $piereg['firstday'] == '5' ) echo 'selected="selected"';?>>

<?php _e('Saturday','piereg');?>

</option>

<option value="6" <?php if( $piereg['firstday'] == '6' ) echo 'selected="selected"';?>>

<?php _e('Sunday','piereg');?>

</option>

</select>

</label>

&nbsp;

<label for="dateformat">

<?php _e('Date Format','piereg');?>

:</label>

<input type="text" name="piereg_dateformat" id="dateformat" value="<?php echo $piereg['dateformat'];?>" style="width:100px;" />

&nbsp;

<label for="startdate">

<?php _e('First Selectable Date','piereg');?>

:</label>

<input type="text" name="piereg_startdate" id="startdate" value="<?php echo $piereg['startdate'];?>"  style="width:100px;" />

<br />

<label for="calyear">

<?php _e('Default Year','piereg');?>

:</label>

<input type="text" name="piereg_calyear" id="calyear" value="<?php echo $piereg['calyear'];?>" style="width:40px;" />

&nbsp;

<label for="calmonth">

<?php _e('Default Month','piereg');?>

:</label>

<select name="piereg_calmonth" id="calmonth">

<option value="cur" <?php if( $piereg['calmonth'] == 'cur' ) echo 'selected="selected"';?>>

<?php _e('Current Month','piereg');?>

</option>

<option value="1" <?php if( $piereg['calmonth'] == '1' ) echo 'selected="selected"';?>>

<?php _e('Jan','piereg');?>

</option>

<option value="2" <?php if( $piereg['calmonth'] == '2' ) echo 'selected="selected"';?>>

<?php _e('Feb','piereg');?>

</option>

<option value="3" <?php if( $piereg['calmonth'] == '3' ) echo 'selected="selected"';?>>

<?php _e('Mar','piereg');?>

</option>

<option value="4" <?php if( $piereg['calmonth'] == '4' ) echo 'selected="selected"';?>>

<?php _e('Apr','piereg');?>

</option>

<option value="5" <?php if( $piereg['calmonth'] == '5' ) echo 'selected="selected"';?>>

<?php _e('May','piereg');?>

</option>

<option value="6" <?php if( $piereg['calmonth'] == '6' ) echo 'selected="selected"';?>>

<?php _e('Jun','piereg');?>

</option>

<option value="7" <?php if( $piereg['calmonth'] == '7' ) echo 'selected="selected"';?>>

<?php _e('Jul','piereg');?>

</option>

<option value="8" <?php if( $piereg['calmonth'] == '8' ) echo 'selected="selected"';?>>

<?php _e('Aug','piereg');?>

</option>

<option value="9" <?php if( $piereg['calmonth'] == '9' ) echo 'selected="selected"';?>>

<?php _e('Sep','piereg');?>

</option>

<option value="10" <?php if( $piereg['calmonth'] == '10' ) echo 'selected="selected"';?>>

<?php _e('Oct','piereg');?>

</option>

<option value="11" <?php if( $piereg['calmonth'] == '11' ) echo 'selected="selected"';?>>

<?php _e('Nov','piereg');?>

</option>

<option value="12" <?php if( $piereg['calmonth'] == '12' ) echo 'selected="selected"';?>>

<?php _e('Dec','piereg');?>

</option>

</select></td>

</tr>

</tbody>

</table>

<p class="submit">

<input name="Submit" value="<?php _e('Save Changes','piereg');?>" type="submit" class="button" />

<input name="action" value="pie_reg_update" type="hidden" />

<input type="hidden" name="pieregister_page" value="1" />

<button type="submit" name="pieregister_reset" value="1" onclick="javascript: return confirm('Warning: All Settings will be Lost, are you sure?');">Reset</button>

</p>

</form>

</div>