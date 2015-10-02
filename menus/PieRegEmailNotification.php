<?php

$piereg = get_option( 'pie_register_2' );

$piereg_custom = get_option( 'pie_register_custom' );

if( $_POST['notice'] ){

echo '<div id="message" class="updated fade"><p><strong>' . $_POST['notice'] . '.</strong></p></div>';

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

<div id="pie-register">

<h2><?php _e('Customize User Notification Email', 'piereg');?></h2>

<form method="post" action="" enctype="multipart/form-data">

<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg-update-options'); ?>

<p class="submit"><input name="Submit" value="<?php _e('Save Changes','piereg');?>" type="submit" /></p>

<h3><?php _e('Customize User Notification Email', 'piereg');?></h3>

<table class="form-table"> 

<tbody>

<tr valign="top">

<th scope="row"><label><?php _e('Custom User Email Notification', 'piereg');?></label></th>

<td><label><input type="checkbox" name="piereg_custom_msg" id="custom_msg" value="1" <?php if( $piereg['custom_msg'] ) echo 'checked="checked"';?> /> <?php _e('Enable', 'piereg');?></label></td>

</tr>

</tbody>

</table>

<div id="enabled_msg">

<table class="form-table">

<tbody>

<tr valign="top">

<th scope="row"><label for="from"><?php _e('From Email', 'piereg');?></label></th>

<td><input type="text" name="piereg_from" id="from" style="width:250px;" value="<?php echo $piereg['from'];?>" /></td>

</tr>

<tr valign="top">

<th scope="row"><label for="fromname"><?php _e('From Name', 'piereg');?></label></th>

<td><input type="text" name="piereg_fromname" id="fromname" style="width:250px;" value="<?php echo $piereg['fromname'];?>" /></td>

</tr>

<tr valign="top">

<th scope="row"><label for="subject"><?php _e('Subject', 'piereg');?></label></th>

<td><input type="text" name="piereg_subject" id="subject" style="width:350px;" value="<?php echo $piereg['subject'];?>" /></td>

</tr>

<tr valign="top">

<th scope="row"><label for="msg"><?php _e('User Message', 'piereg');?></label></th>

<td>

<?php

if( $piereg['firstname'] ) $custom_keys .= ' &nbsp; %firstname%';

if( $piereg['lastname'] ) $custom_keys .= ' &nbsp; %lastname%';

if( $piereg['website'] ) $custom_keys .= ' &nbsp; %website%';

if( $piereg['aim'] ) $custom_keys .= ' &nbsp; %aim%';

if( $piereg['yahoo'] ) $custom_keys .= ' &nbsp; %yahoo%';

if( $piereg['jabber'] ) $custom_keys .= ' &nbsp; %jabber%';

if( $piereg['about'] ) $custom_keys .= ' &nbsp; %about%';

if( $piereg['code'] ) $custom_keys .= ' &nbsp; %'.$piereg['codename'].'code%';

if( is_array($piereg_custom) ){

foreach( $piereg_custom as $k=>$v ){

$meta = $this->Label_ID($v['label']);

$value = get_usermeta( $user_id, $meta );

$custom_keys .= ' &nbsp; %'.$meta.'%';

}

}

?>

<p><strong><?php _e('Replacement Keys', 'piereg');?>:</strong> &nbsp; %user_login%  &nbsp; %user_pass% &nbsp; %user_email% &nbsp; %blogname% &nbsp; %siteurl% <?php echo $custom_keys; ?>&nbsp; %user_ip% &nbsp; %user_ref% &nbsp; %user_host% &nbsp; %user_agent% </p>

<textarea name="piereg_msg" id="msg" rows="10" cols="25" style="width:80%;height:300px;"><?php echo $piereg['msg'];?></textarea><br /><label><input type="checkbox" name="piereg_html" id="html" value="1" <?php if( $piereg['html'] ) echo 'checked="checked"';?> /> <?php _e('Send as HTML', 'piereg');?></label> &nbsp; <label><input type="checkbox" name="piereg_user_nl2br" id="html" value="1" <?php if( $piereg['user_nl2br'] ) echo 'checked="checked"';?> /> <?php _e('Convert new lines to &lt;br/> tags (HTML only)' , 'piereg');?></label></td>

</tr>

<tr valign="top">

<th scope="row"><label for="emailvmsg"><?php _e('Pending Email Verification Message', 'piereg');?></label></th>

<td><textarea name="piereg_emailvmsg" id="emailvmsg" rows="10" cols="25" style="width:80%;height:300px;"><?php echo $piereg['emailvmsg'];?></textarea><br /><label><input type="checkbox" name="piereg_emailvmsghtml" id="emailvmsghtml" value="1" <?php if( $piereg['emailvmsghtml'] ) echo 'checked="checked"';?> /> <?php _e('Send as HTML', 'piereg');?></label> &nbsp; <label><input type="checkbox" name="piereg_emailvmsguser_nl2br" id="html" value="1" <?php if( $piereg['emailvmsguser_nl2br'] ) echo 'checked="checked"';?> /> <?php _e('Convert new lines to &lt;br/> tags (HTML only)' , 'piereg');?></label></td>

</tr>

<tr valign="top">

<th scope="row"><label for="adminvmsg"><?php _e('Pending Admin Verification Message', 'piereg');?></label></th>

<td><textarea name="piereg_adminvmsg" id="adminvmsg" rows="10" cols="25" style="width:80%;height:300px;"><?php echo $piereg['adminvmsg'];?></textarea><br /><label><input type="checkbox" name="piereg_adminvmsghtml" id="adminvmsghtml" value="1" <?php if( $piereg['adminvmsghtml'] ) echo 'checked="checked"';?> /> <?php _e('Send as HTML', 'piereg');?></label> &nbsp; <label><input type="checkbox" name="piereg_adminvmsguser_nl2br" id="html" value="1" <?php if( $piereg['adminvmsguser_nl2br'] ) echo 'checked="checked"';?> /> <?php _e('Convert new lines to &lt;br/> tags (HTML only)' , 'piereg');?></label></td>

</tr>

</tbody>

</table>

</div>

<h3><?php _e('Customize Admin Notification Email', 'piereg');?></h3>

<table class="form-table"> 

<tbody>

<tr valign="top">

<th scope="row"><label for="disable_admin"><?php _e('Admin Email Notification', 'piereg');?></label></th>

<td><label><input type="checkbox" name="piereg_disable_admin" id="disable_admin" value="1" <?php if( $piereg['disable_admin'] ) echo 'checked="checked"';?> /> <?php _e('Disable', 'piereg');?></label></td>

</tr>

<tr valign="top">

<th scope="row"><label><?php _e('Custom Admin Email Notification', 'piereg');?></label></th>

<td><label><input type="checkbox" name="piereg_custom_adminmsg" id="custom_adminmsg" value="1" <?php if( $piereg['custom_adminmsg'] ) echo 'checked="checked"';?> /> <?php _e('Enable', 'piereg');?></label></td>

</tr>

</tbody>

</table>

<div id="enabled_adminmsg">

<table class="form-table">

<tbody>

<tr valign="top">

<th scope="row"><label for="adminfrom"><?php _e('From Email', 'piereg');?></label></th>

<td><input type="text" name="piereg_adminfrom" id="adminfrom" style="width:250px;" value="<?php echo $piereg['adminfrom'];?>" /></td>

</tr>

<tr valign="top">

<th scope="row"><label for="adminfromname"><?php _e('From Name', 'piereg');?></label></th>

<td><input type="text" name="piereg_adminfromname" id="adminfromname" style="width:250px;" value="<?php echo $piereg['adminfromname'];?>" /></td>

</tr>

<tr valign="top">

<th scope="row"><label for="adminsubject"><?php _e('Subject', 'piereg');?></label></th>

<td><input type="text" name="piereg_adminsubject" id="adminsubject" style="width:350px;" value="<?php echo $piereg['adminsubject'];?>" /></td>

</tr>

<tr valign="top">

<th scope="row"><label for="adminmsg"><?php _e('Admin Message', 'piereg');?></label></th>

<td>

<p><strong><?php _e('Replacement Keys', 'piereg');?>:</strong> &nbsp; %user_login%  &nbsp; %user_email% &nbsp; %blogname% &nbsp; %siteurl%  <?php echo $custom_keys; ?>&nbsp; %user_ip% &nbsp; %user_ref% &nbsp; %user_host% &nbsp; %user_agent%</p><textarea name="piereg_adminmsg" id="adminmsg" rows="10" cols="25" style="width:80%;height:300px;"><?php echo $piereg['adminmsg'];?></textarea><br /><label><input type="checkbox" name="piereg_adminhtml" id="adminhtml" value="1" <?php if( $piereg['adminhtml'] ) echo 'checked="checked"';?> /> <?php _e('Send as HTML' , 'piereg');?></label> &nbsp; <label><input type="checkbox" name="piereg_admin_nl2br" id="html" value="1" <?php if( $piereg['admin_nl2br'] ) echo 'checked="checked"';?> /> <?php _e('Convert new lines to &lt;br/> tags (HTML only)' , 'piereg');?></label></td>

</tr>

</tbody>

</table>

</div>

<input name="action" value="pie_reg_update" type="hidden" />

<input type="hidden" name="email_notification_page" value="1" />

<p class="submit"><input name="Submit" value="<?php _e('Save Changes','piereg');?>" type="submit" /></p>					

</form>

</div>