<?php
$piereg 				= get_option( 'pie_register_2' );
$pie_user_email_types 	= get_option( 'pie_user_email_types' );

if(isset($_POST['notice']) && $_POST['notice'] ){
	echo '<div id="message" class="updated fade"><p><strong>' . $_POST['notice'] . '.</strong></p></div>';
}
$replacement_fields = "";			   	
$fields = maybe_unserialize(get_option("pie_fields"));
if(sizeof($fields ) > 0)
{
	
	foreach($fields as $pie_fields)	
	{
		
		switch($pie_fields['type']) :
		case 'default' :
		case 'form' :					
		case 'submit' :
		case 'username' :
		case 'email' :
		case 'password' :
		case 'name' :
		case 'pagebreak' :
		case 'sectionbreak' :
		case 'html' :
		case 'hidden' :
		case 'captcha' :
		case 'math_captcha' :
		continue 2;
		break;
		endswitch;						
							
		if($pie_fields['type'] == "invitation")
			$meta_key = "invitation_code";
		else
			$meta_key	= "pie_".$pie_fields['type']."_".$pie_fields['id'];
		
			
		//$replacement_fields .= "&nbsp; %".$meta_key."% &nbsp;";
		$replacement_fields .= '<option value="%'.$meta_key.'%">'.$pie_fields['label'].'</option>';
	}
}
?> 

<script type="text/javascript" src="<?php echo plugins_url("ckeditor/ckeditor.js",dirname(__FILE__));?>"></script>
<script type="text/javascript">
var piereg = jQuery.noConflict();
piereg(document).ready(function(e) {
	var types =  document.getElementsByName("user_email_type");
	
	for(a = 0 ; a < types.length ; a++ )
	{
		var val = document.getElementsByName("user_email_type")[a].value;
		piereg("."+val).hide();
	}
	
	piereg('input[name="user_email_type"]').click(function(e) {
		
		for(a = 0 ; a < types.length ; a++ )
		{
			var val = document.getElementsByName("user_email_type")[a].value;
			piereg("."+val).hide();
		}
		
		var val = piereg(this).val();
		piereg("."+val).show();
	});
	piereg('input[name="user_email_type"]').eq(0).trigger("click"); 
	<?php if(isset($_POST['user_email_type']))
	{
	?>piereg('input[value="<?php echo $_POST['user_email_type']?>"]').eq(0).trigger("click"); <?php 
	} 
	?>
	
	   
});
 
</script>
<div id="container" class="pieregister-admin">
  <div class="right_section">
    <div class="notifications">
       <h2><?php _e("Notifications : Registration Form",'piereg') ?></h2>
      <form method="post" action="">
      <p class="submit">
          <input name="Submit" style="background: #464646;color: #ffffff;border: 0;cursor: pointer;padding: 5px 0px 5px 0px;margin-top: 15px;
min-width: 113px;float:right;" value="<?php _e('Save Changes','piereg');?>" type="submit" />
        </p>
        <ul>
          <li>
            <div class="fields">
               <h2><?php _e("Notifications to Users",'piereg') ?></h2>
              
              <p><?php _e("Enter a message below to receive a notification email when users submit this form.",'piereg') ?></p>        
              
            </div>
          </li>
          <li>
            <div class="fields">
              <label><?php _e("Messsage Type",'piereg') ?></label>
              <?php foreach ($pie_user_email_types as $val=>$type) { ?>
                    <div class="piereg_message_type_links">
                      <input id="user_email_type_<?php echo $val?>" name="user_email_type" value="<?php echo $val?>" type="radio" />
                      <label for="user_email_type_<?php echo $val?>"><?php _e($type,"piereg")?></label>
                    </div>
                    <!--&nbsp;&nbsp;-->
              <?php } ?>
            </div>
            <?php foreach ($pie_user_email_types as $val=>$type) { ?>
         <!-- <li class="<?php echo $val?>">
            <div class="fields">
              <label><?php _e("Send To Email*",'piereg') ?></label>
              <input name="user_sendto_email_<?php echo $val?>" value="<?php echo $piereg['user_sendto_email_'.$val]?>" type="text" class="input_fields" />
            </div>
          </li>-->
          <li class="<?php echo $val?>">
            <div class="fields">
              <label><?php _e("From Name",'piereg') ?></label>
              <input name="user_from_name_<?php echo $val?>" value="<?php echo $piereg['user_from_name_'.$val]?>" type="text" class="input_fields2" />
            </div>
          </li>
          <li class="<?php echo $val?>">
            <div class="fields">
              <label><?php _e("From Email",'piereg') ?></label>
              <input name="user_from_email_<?php echo $val?>" value="<?php echo $piereg['user_from_email_'.$val]?>" type="text" class="input_fields2" />
            </div>
          </li>
          <li class="<?php echo $val?>">
            <div class="fields">
              <label><?php _e("Reply To",'piereg') ?></label>
              <input name="user_to_email_<?php echo $val?>" value="<?php echo $piereg['user_to_email_'.$val]?>" type="text" class="input_fields2" />
            </div>
          </li>
         <!-- <li class="<?php echo $val?>">
            <div class="fields">
              <label><?php _e("BCC",'piereg') ?></label>
              <input  name="user_bcc_email_<?php echo $val?>" value="<?php echo $piereg['user_bcc_email_'.$val]?>" type="text" class="input_fields" />
            </div>
          </li>-->
          <li class="<?php echo $val?>">
            <div class="fields">
              <label><?php _e("Subject",'piereg') ?></label>
              <input name="user_subject_email_<?php echo $val?>" value="<?php echo $piereg['user_subject_email_'.$val]?>" type="text" class="input_fields" />
            </div>
          </li>
          
          <li class="<?php echo $val?>">
                <div class="fields">
                  <label style="width:auto;margin-right:20px;"><?php _e("Send HTML Format",'piereg');?></label>
                    <div class="radio_fields">
                        <input type="radio" id="<?php echo 'user_formate_email_'.$val; ?>_yes" name="<?php echo 'user_formate_email_'.$val; ?>" value="1" <?php echo ($piereg['user_formate_email_'.$val] == "1")? ' checked="checked" ' : '' ?>>
                        <label for="<?php echo 'user_formate_email_'.$val; ?>_yes" style="float:none;"><?php _e("Yes",'piereg');?></label>
                        &nbsp;&nbsp;
                        <input type="radio" id="<?php echo 'user_formate_email_'.$val; ?>_no" name="<?php echo 'user_formate_email_'.$val; ?>" value="0" <?php echo ($piereg['user_formate_email_'.$val] == "0")? ' checked="checked" ' : '' ?>>
                        <label for="<?php echo 'user_formate_email_'.$val; ?>_no" style="float:none;"><?php _e("No",'piereg');?></label>
                    </div>
                </div>
          </li>
          <li class="<?php echo $val?>">
            <div class="fields">
              <label><?php _e("Message",'piereg') ?></label>
              
              <p><strong><?php _e("Replacement Keys","piereg");?>:</strong> <!--&nbsp; %user_login%  &nbsp; %user_email% &nbsp; %blogname% &nbsp; %siteurl%  &nbsp; %activationurl%  &nbsp; %firstname% &nbsp; %lastname% &nbsp; %pending_payment_url% &nbsp; %user_url% &nbsp; %user_aim% &nbsp; %user_yim %&nbsp; %user_jabber%&nbsp; %user_biographical_nfo% &nbsp;  %all_field% &nbsp; %user_registration_date% &nbsp; %reset_password_url%-->
               <?php //echo $replacement_fields?>          
				<select name="replacement_keys<?php echo $val?>" id="replacement_keys<?php echo $val?>" style="font-size:14px;" >
                	<option value="select"><?php _e("Select",'piereg') ?></option>
                    <optgroup label="<?php _e("Default Fields",'piereg') ?>">
                        <option value="%user_login%"><?php _e("User Name",'piereg') ?></option>
                        <option value="%user_email%"><?php _e("User E-mail",'piereg') ?></option>
                        <option value="%firstname%"><?php _e("User First Name",'piereg') ?></option>
                        <option value="%lastname%"><?php _e("User Last Name",'piereg') ?></option>
                        <option value="%user_url%"><?php _e("User URL",'piereg') ?></option>
                        <option value="%user_aim%"><?php _e("User AIM",'piereg') ?></option>
                        <option value="%user_yim%"><?php _e("User YIM",'piereg') ?></option>
                        <option value="%user_jabber%"><?php _e("User Jabber",'piereg') ?></option>
                        <option value="%user_biographical_nfo%"><?php _e("User Biographical Info",'piereg') ?></option>
                        <option value="%user_registration_date%"><?php _e("User Registration Date",'piereg') ?></option>
                    </optgroup>
                    <optgroup label="<?php _e("Custom Fields",'piereg') ?>">
	                	<?php echo $replacement_fields; ?>
                    </optgroup>
                    <optgroup label="<?php _e("Other",'piereg') ?>">
                        <option value="%blogname%"><?php _e("Blog Name",'piereg') ?></option>
                        <option value="%siteurl%"><?php _e("Site URL",'piereg') ?></option>
                        <option value="%blogname_url%"><?php _e("Blog Name With Site URL",'piereg') ?></option>
                        <option value="%reset_password_url%"><?php _e("Reset Password URL",'piereg') ?></option>
                        <option value="%activationurl%"><?php _e("User Activation URL",'piereg') ?></option>
                        <option value="%reset_email_url%"><?php _e("Reset Email URL",'piereg') ?></option>
                        <option value="%confirm_current_email_url%"><?php _e("Confirm Current Email URL",'piereg') ?></option>
                        <option value="%user_ip%"><?php _e("User IP",'piereg') ?></option>
                        <option value="%user_new_email%"><?php _e("User New E-mail",'piereg') ?></option>
                        <option value="%pending_payment_url%"><?php _e("Pending Payment URL",'piereg') ?></option>
                    </optgroup>
                </select>
               </p>

				
                
              <textarea name="user_message_email_<?php echo $val?>" id="piereg_text_editor_<?php echo $val?>" class="ckeditor"><?php echo $piereg['user_message_email_'.$val]?></textarea>
<script type="text/javascript">
CKEDITOR.replace('piereg_text_editor_<?php echo $val?>',{removeButtons: 'About'});
piereg(document).ready(function(){
	piereg("#replacement_keys<?php echo $val?>").change(function(){
		CKEDITOR.instances.piereg_text_editor_<?php echo $val?>.insertHtml(piereg(this).val().trim());
		piereg(this).val('select');
	});
});
</script>

              <div class="piereg_clear"></div>
              
            </div>
          </li>
          <?php } ?>
        </ul>
        <input name="action" value="pie_reg_update" type="hidden" />
        <input type="hidden" name="user_email_notification_page" value="1" />
        <p class="submit">
          <input name="Submit" style="background: #464646;color: #ffffff;border: 0;cursor: pointer;padding: 5px 0px 5px 0px;margin-top: 15px;
min-width: 113px;float:right;" value="<?php _e('Save Changes','piereg');?>" type="submit" />
        </p>
      </form>
      
<?php 
		$old_ver_options = get_option("pie_register");
		if($old_ver_options['adminvmsg'] != "" || $old_ver_options['emailvmsg'] != "" || $old_ver_options['msg'] )
		{
?>
            <div class="fields">
                <form method="post">
                    <label><?php _e("Click here to import version 1.x email template","piereg"); ?></label>                
                    <p class="submit"><input name="import_email_template_from_version_1" style="background: #464646;color: #ffffff;border: 0;cursor: pointer;padding: 5px;margin-top: 15px;" value=" <?php _e('Import email template','piereg');?> " type="submit" /></p>
                    <input type="hidden" name="old_version_emport" value="yes" />
                </form>
            </div>
<?php
		}
		unset($old_ver_options);
?>
      
      
    </div>
  </div>
</div>
