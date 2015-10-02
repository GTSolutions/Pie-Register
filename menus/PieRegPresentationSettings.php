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

<form method="post" action="" enctype="multipart/form-data">

<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg-update-options'); ?>

<p class="submit"><input name="Submit" value="<?php _e('Save Changes','piereg');?>" type="submit" /></p>

<h3><?php _e('Custom CSS for Register & Login Pages', 'piereg');?></h3>

<p><?php _e('CSS Rule Example:', 'piereg');?>

<code>

#user_login{

font-size: 20px;	

width: 97%;

padding: 3px;

margin-right: 6px;

}</code>

<table class="form-table">

<tbody>

<tr valign="top">

<th scope="row"><label for="register_css"><?php _e('Custom Register CSS', 'piereg');?></label></th>

<td><textarea name="piereg_register_css" id="register_css" rows="20" cols="40" style="width:80%; height:200px;"><?php echo stripslashes($piereg['register_css']);?></textarea></td>

</tr>

<tr valign="top">

<th scope="row"><label for="login_css"><?php _e('Custom Login CSS', 'piereg');?></label></th>

<td><textarea name="piereg_login_css" id="login_css" rows="20" cols="40" style="width:80%; height:200px;"><?php echo html_entity_decode(stripslashes($piereg['login_css']));?></textarea></td>

</tr>

</tbody>

</table>

<input name="action" value="pie_reg_update" type="hidden" />

<input type="hidden" name="presentation_page" value="1" />

<p class="submit"><input name="Submit" value="<?php _e('Save Changes','piereg');?>" type="submit" /></p>

</div>

