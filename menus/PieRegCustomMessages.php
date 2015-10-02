<?php
$piereg = get_option( 'pie_register' );
if( $_POST['notice'] ){
echo '<div id="message" class="updated fade"><p><strong>' . $_POST['notice'] . '.</strong></p></div>';
}
?>
<style>
button,
input,
select,
textarea {
margin: 0;
font-size: 100%;
vertical-align: middle;
}
button,
input {
*overflow: visible;
line-height: normal;
}
button::-moz-focus-inner,
input::-moz-focus-inner {
padding: 0;
border: 0;
}
button,
html input[type="button"],
input[type="reset"],
input[type="submit"] {
cursor: pointer;
-webkit-appearance: button;
}
label,
select,
button,
input[type="button"],
input[type="reset"],
input[type="submit"],
input[type="radio"],
input[type="checkbox"] {
cursor: pointer;
}
.fields{
margin:0 0 10px;
clear:both;
}
form {
margin: 0 0 20px;
}
fieldset {
padding: 0;
margin: 0;
border: 0;
}
legend {
display: block;
width: 100%;
padding: 0;
margin-bottom: 20px;
font-size: 21px;
line-height: 40px;
color: #333333;
border: 0;
border-bottom: 1px solid #e5e5e5;
}
legend small {
font-size: 15px;
color: #999999;
}
label,
input,
button,
select,
textarea {
font-size: 14px;
font-weight: normal;
line-height: 20px;
}
input,
button,
select,
textarea {
font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
}
label {
display: inline-block;
margin-bottom: 5px;
}
input[type="text"],
.uneditable-input {
display: inline-block;
padding: 4px 6px;
margin-bottom: 10px;
font-size: 14px;
line-height: 20px;
color: #555555;
vertical-align: middle;
-webkit-border-radius: 4px;
-moz-border-radius: 4px;
border-radius: 4px;
}
input,
textarea,
.uneditable-input {
width: 206px;
}
input[type="text"],
.uneditable-input {
background-color: #ffffff;
border: 1px solid #cccccc;
-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
-moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
-webkit-transition: border linear 0.2s, box-shadow linear 0.2s;
-moz-transition: border linear 0.2s, box-shadow linear 0.2s;
-o-transition: border linear 0.2s, box-shadow linear 0.2s;
transition: border linear 0.2s, box-shadow linear 0.2s;
}
input[type="text"]:focus,
.uneditable-input:focus {
border-color: rgba(82, 168, 236, 0.8);
outline: 0;
outline: thin dotted \9;
/* IE6-9 */
-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(82, 168, 236, 0.6);
-moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(82, 168, 236, 0.6);
box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(82, 168, 236, 0.6);
}
[class*="span"] {
float: left;
min-height: 1px;
margin-left: 20px;
}
.span12 {
width: 940px;
}
.span11 {
width: 860px;
}
.span10 {
width: 780px;
}
.span9 {
width: 700px;
}
.span8 {
width: 620px;
}
.span7 {
width: 540px;
}
.span6 {
width: 460px;
}
.span5 {
width: 380px;
}
.span4 {
width: 300px;
}
.span3 {
width: 220px;
}
.span2 {
width: 140px;
}
.span1 {
width: 60px;
}
.fade{opacity:0;-webkit-transition:opacity .15s linear;transition:opacity .15s linear}
.fade.in{opacity:1}
.alert{padding:15px;margin-bottom:20px;border:1px solid transparent;border-radius:4px}.alert h4{margin-top:0;color:inherit}.alert .alert-link{font-weight:bold}.alert>p,.alert>ul{margin-bottom:0}.alert>p+p{margin-top:5px}.alert-dismissable{padding-right:35px}.alert-dismissable .close{position:relative;top:-2px;right:-21px;color:inherit}.alert-success{color:#3c763d;background-color:#dff0d8;border-color:#d6e9c6}.alert-success hr{border-top-color:#c9e2b3}.alert-success .alert-link{color:#2b542c}.alert-info{color:#31708f;background-color:#d9edf7;border-color:#bce8f1}.alert-info hr{border-top-color:#a6e1ec}.alert-info .alert-link{color:#245269}.alert-warning{color:#8a6d3b;background-color:#fcf8e3;border-color:#faebcc}.alert-warning hr{border-top-color:#f7e1b5}.alert-warning .alert-link{color:#66512c}.alert-danger{color:#a94442;background-color:#f2dede;border-color:#ebccd1}.alert-danger hr{border-top-color:#e4b9c0}.alert-danger .alert-link{color:#843534}
</style>
<div class="alert alert-warning fade in">
<p><?php _e('You are currently using version of 1 of Pie-Register, which will be deprecated soon. We have released a Version 2.0 of this plugin. Please ' , 'piereg');?><a href="http://pieregister.genetechsolutions.com/pie-register-version-2-0-beta-has-arrived/" title="Pie-Register Version 2.0" target="_blank"><?php _e('Click here', 'piereg');?></a> <?php _e('to get more information.');?></p>
</div>
<h2><?php _e('Edit Site Messages', 'piereg');?></h2>
<div id="pie-register">
<form method="post" action="" enctype="multipart/form-data">
<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg-update-options'); ?>
<p class="submit"><input name="Submit" value="<?php _e('Save Changes','piereg');?>" type="submit" /></p>
<div class="fields"><div class="label span2"><?php _e('Message #1', 'piereg');?></div><input type="text" name="piereg__admin_message_1" id="_admin_message_1" class="span5" value="<?php echo $piereg['_admin_message_1'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #2', 'piereg');?></div><input type="text" name="piereg__admin_message_2" id="_admin_message_2" class="span5" value="<?php echo $piereg['_admin_message_2'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #3', 'piereg');?></div><input type="text" name="piereg__admin_message_3" id="_admin_message_3" class="span5" value="<?php echo $piereg['_admin_message_3'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #4', 'piereg');?></div><input type="text" name="piereg__admin_message_4" id="_admin_message_4" class="span5" value="<?php echo $piereg['_admin_message_4'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #5', 'piereg');?></div><input type="text" name="piereg__admin_message_5" id="_admin_message_5" class="span5" value="<?php echo $piereg['_admin_message_5'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #6', 'piereg');?></div><input type="text" name="piereg__admin_message_6" id="_admin_message_6" class="span5" value="<?php echo $piereg['_admin_message_6'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #7', 'piereg');?></div><input type="text" name="piereg__admin_message_7" id="_admin_message_7" class="span5" value="<?php echo $piereg['_admin_message_7'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #8', 'piereg');?></div><input type="text" name="piereg__admin_message_8" id="_admin_message_8" class="span5" value="<?php echo $piereg['_admin_message_8'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #9', 'piereg');?></div><input type="text" name="piereg__admin_message_9" id="_admin_message_9" class="span5" value="<?php echo $piereg['_admin_message_9'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #10', 'piereg');?></div><input type="text" name="piereg__admin_message_10" id="_admin_message_10" class="span5" value="<?php echo $piereg['_admin_message_10'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #12', 'piereg');?></div><input type="text" name="piereg__admin_message_12" id="_admin_message_12" class="span5" value="<?php echo $piereg['_admin_message_12'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #13', 'piereg');?></div><input type="text" name="piereg__admin_message_13" id="_admin_message_13" class="span5" value="<?php echo $piereg['_admin_message_13'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #14', 'piereg');?></div><input type="text" name="piereg__admin_message_14" id="_admin_message_14" class="span5" value="<?php echo $piereg['_admin_message_14'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #15', 'piereg');?></div><input type="text" name="piereg__admin_message_15" id="_admin_message_15" class="span5" value="<?php echo $piereg['_admin_message_15'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #16', 'piereg');?></div><input type="text" name="piereg__admin_message_16" id="_admin_message_16" class="span5" value="<?php echo $piereg['_admin_message_16'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #17', 'piereg');?></div><input type="text" name="piereg__admin_message_17" id="_admin_message_17" class="span5" value="<?php echo $piereg['_admin_message_17'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #18', 'piereg');?></div><input type="text" name="piereg__admin_message_18" id="_admin_message_18" class="span5" value="<?php echo $piereg['_admin_message_18'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #19', 'piereg');?></div><input type="text" name="piereg__admin_message_19" id="_admin_message_19" class="span5" value="<?php echo $piereg['_admin_message_19'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #20', 'piereg');?></div><input type="text" name="piereg__admin_message_20" id="_admin_message_20" class="span5" value="<?php echo $piereg['_admin_message_20'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #21', 'piereg');?></div><input type="text" name="piereg__admin_message_21" id="_admin_message_21" class="span5" value="<?php echo $piereg['_admin_message_21'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #22', 'piereg');?></div><input type="text" name="piereg__admin_message_22" id="_admin_message_22" class="span5" value="<?php echo $piereg['_admin_message_22'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #23', 'piereg');?></div><input type="text" name="piereg__admin_message_23" id="_admin_message_23" class="span5" value="<?php echo $piereg['_admin_message_23'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #24', 'piereg');?></div><input type="text" name="piereg__admin_message_24" id="_admin_message_24" class="span5" value="<?php echo $piereg['_admin_message_24'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #25', 'piereg');?></div><input type="text" name="piereg__admin_message_25" id="_admin_message_25" class="span5" value="<?php echo $piereg['_admin_message_25'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #26', 'piereg');?></div><input type="text" name="piereg__admin_message_26" id="_admin_message_26" class="span5" value="<?php echo $piereg['_admin_message_26'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #27', 'piereg');?></div><input type="text" name="piereg__admin_message_27" id="_admin_message_27" class="span5" value="<?php echo $piereg['_admin_message_27'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #28', 'piereg');?></div><input type="text" name="piereg__admin_message_28" id="_admin_message_28" class="span5" value="<?php echo $piereg['_admin_message_28'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #29', 'piereg');?></div><input type="text" name="piereg__admin_message_29" id="_admin_message_29" class="span5" value="<?php echo $piereg['_admin_message_29'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #30', 'piereg');?></div><input type="text" name="piereg__admin_message_30" id="_admin_message_30" class="span5" value="<?php echo $piereg['_admin_message_30'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #31', 'piereg');?></div><input type="text" name="piereg__admin_message_31" id="_admin_message_31" class="span5" value="<?php echo $piereg['_admin_message_31'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #32', 'piereg');?></div><input type="text" name="piereg__admin_message_32" id="_admin_message_32" class="span5" value="<?php echo $piereg['_admin_message_32'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #33', 'piereg');?></div><input type="text" name="piereg__admin_message_33" id="_admin_message_33" class="span5" value="<?php echo $piereg['_admin_message_33'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #34', 'piereg');?></div><input type="text" name="piereg__admin_message_34" id="_admin_message_34" class="span5" value="<?php echo $piereg['_admin_message_34'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #35', 'piereg');?></div><input type="text" name="piereg__admin_message_35" id="_admin_message_35" class="span5" value="<?php echo $piereg['_admin_message_35'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #36', 'piereg');?></div><input type="text" name="piereg__admin_message_36" id="_admin_message_36" class="span5" value="<?php echo $piereg['_admin_message_36'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #37', 'piereg');?></div><input type="text" name="piereg__admin_message_37" id="_admin_message_37" class="span5" value="<?php echo $piereg['_admin_message_37'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #38', 'piereg');?></div><input type="text" name="piereg__admin_message_38" id="_admin_message_38" class="span5" value="<?php echo $piereg['_admin_message_38'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #39', 'piereg');?></div><input type="text" name="piereg__admin_message_39" id="_admin_message_39" class="span5" value="<?php echo $piereg['_admin_message_39'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #40', 'piereg');?></div><input type="text" name="piereg__admin_message_40" id="_admin_message_40" class="span5" value="<?php echo $piereg['_admin_message_40'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #41', 'piereg');?></div><input type="text" name="piereg__admin_message_41" id="_admin_message_41" class="span5" value="<?php echo $piereg['_admin_message_41'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #42', 'piereg');?></div><input type="text" name="piereg__admin_message_42" id="_admin_message_42" class="span5" value="<?php echo $piereg['_admin_message_42'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #43', 'piereg');?></div><input type="text" name="piereg__admin_message_43" id="_admin_message_43" class="span5" value="<?php echo $piereg['_admin_message_43'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #44', 'piereg');?></div><input type="text" name="piereg__admin_message_44" id="_admin_message_44" class="span5" value="<?php echo $piereg['_admin_message_44'];?>" /></div> 
<div class="fields"><div class="label span2"><?php _e('Message #45', 'piereg');?></div><input type="text" name="piereg__admin_message_45" id="_admin_message_45" class="span5" value="<?php echo $piereg['_admin_message_45'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #46', 'piereg');?></div><input type="text" name="piereg__admin_message_46" id="_admin_message_46" class="span5" value="<?php echo $piereg['_admin_message_46'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #47', 'piereg');?></div><input type="text" name="piereg__admin_message_47" id="_admin_message_47" class="span5" value="<?php echo $piereg['_admin_message_47'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #48', 'piereg');?></div><input type="text" name="piereg__admin_message_48" id="_admin_message_48" class="span5" value="<?php echo $piereg['_admin_message_48'];?>" /></div> 
<div class="fields"><div class="label span2"><?php _e('Message #49', 'piereg');?></div><input type="text" name="piereg__admin_message_49" id="_admin_message_49" class="span5" value="<?php echo $piereg['_admin_message_49'];?>" /></div>
<div class="fields"><div class="label span2"><?php _e('Message #50', 'piereg');?></div><input type="text" name="piereg__admin_message_50" id="_admin_message_50" class="span5" value="<?php echo $piereg['_admin_message_50'];?>" /></div>
<input name="action" value="pie_reg_update" type="hidden" />
<input type="hidden" name="customised_messages_page" value="1" />
<p class="submit"><input name="Submit" value="<?php _e('Save Changes','piereg');?>" type="submit" /></p>
</form>
</div>