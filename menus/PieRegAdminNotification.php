<?php
$piereg = get_option( 'pie_register_2' );
if(isset($_POST['notice']) && $_POST['notice'] ){
	echo '<div id="message" class="updated fade"><p><strong>' . $_POST['notice'] . '.</strong></p></div>';
}
?>
<script type="text/javascript" src="<?php echo plugins_url("ckeditor/ckeditor.js",dirname(__FILE__));?>"></script>
<div id="container" class="pieregister-admin">
  <div class="right_section">
    <div class="notifications">
      <h2><?php _e("Notifications : Registration Form",'piereg');?></h2>
      <form method="post" action="">
        <ul>
          <li>
            <div class="fields">
              <h2><?php _e("Notifications to Administrator",'piereg');?></h2>
              <input name="enable_admin_notifications" <?php echo ($piereg['enable_admin_notifications']=="1")?'checked="checked"':''?> type="checkbox" class="checkbox" value="1" />
              <?php _e("Enable email notification to administrators",'piereg');?>
              <p><?php _e("Enter a message below to receive a notification email when users submit this form.",'piereg');?></p>
            </div>
          </li>
          <li>
            <div class="fields">
              <label><?php _e("Send To Email*",'piereg');?></label>
              <input name="admin_sendto_email" value="<?php echo $piereg['admin_sendto_email']?>" type="text" class="input_fields" />
            </div>
          </li>
          <li>
            <div class="fields">
              <label><?php _e("From Name",'piereg');?></label>
              <input name="admin_from_name" value="<?php echo $piereg['admin_from_name']?>" type="text" class="input_fields2" />
            </div>
          </li>
          <li>
            <div class="fields">
              <label><?php _e("From Email",'piereg');?></label>
              <input name="admin_from_email" value="<?php echo $piereg['admin_from_email']?>" type="text" class="input_fields2" />
            </div>
          </li>
          <li>
            <div class="fields">
              <label><?php _e("Reply To",'piereg');?></label>
              <input name="admin_to_email" value="<?php echo $piereg['admin_to_email']?>" type="text" class="input_fields2" />
            </div>
          </li>
          <li>
            <div class="fields">
              <label><?php _e("BCC",'piereg');?></label>
              <input  name="admin_bcc_email" value="<?php echo $piereg['admin_bcc_email']?>" type="text" class="input_fields" />
            </div>
          </li>
          <li>
            <div class="fields">
              <label><?php _e("Subject",'piereg');?></label>
              <input name="admin_subject_email" value="<?php echo $piereg['admin_subject_email']?>" type="text" class="input_fields" />
            </div>
          </li>
          <li>
            <div class="fields">
              <label style="width:auto;margin-right:20px;"><?php _e("Send HTML Format",'piereg');?></label>
                <div class="radio_fields">
                    <input type="radio" id="admin_message_email_formate_yes" name="admin_message_email_formate" value="1" <?php echo ($piereg['admin_message_email_formate'] == "1")? ' checked="checked" ' : '' ?>>
                    <label for="admin_message_email_formate_yes" style="float:none;"><?php _e("Yes",'piereg');?></label>
                    &nbsp;&nbsp;
                    <input type="radio" id="admin_message_email_formate_no" name="admin_message_email_formate" value="0" <?php echo ($piereg['admin_message_email_formate'] == "0")? ' checked="checked" ' : '' ?>>
                    <label for="admin_message_email_formate_no" style="float:none;"><?php _e("No",'piereg');?></label>
                </div>
            </div>
          </li>
          <li>
            <div class="fields">
              <label><?php _e("Message",'piereg');?></label>
              <p><strong><?php _e("Replacement Keys","piereg"); ?>:</strong>
              
			<?php
				$fields = maybe_unserialize(get_option("pie_fields"));
				$replacement_fields = '';
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
						$replacement_fields .= '<option value="%'.$meta_key.'%">'.ucwords($pie_fields['label']).'</option>';	
					}
				}
				?>
				<select name="replacement_keys" id="replacement_keys" style="font-size:14px;">
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
                        <option value="%user_ip%"><?php _e("User IP",'piereg') ?></option>
                        <!--<option value="%activationurl%"><?php _e("User Activation URL",'piereg') ?></option>-->
                    </optgroup>
                </select>
			   </p>
              <textarea name="admin_message_email" id="piereg_text_editor"><?php echo $piereg['admin_message_email']?></textarea>
              
              
				<script type="text/javascript">
					var piereg = jQuery.noConflict();
					CKEDITOR.replace('piereg_text_editor',{removeButtons: 'About'});
					piereg(document).ready(function(){
						piereg("#replacement_keys").change(function(){
							CKEDITOR.instances.piereg_text_editor.insertHtml(piereg(this).val().trim());
							piereg(this).val('select');
						});
					});
                </script>
              
              <div class="piereg_clear"></div>
            </div>
          </li>
        <li>        
            <div class="fields">
                <input name="action" value="pie_reg_update" type="hidden" />
                
                <input type="hidden" name="admin_email_notification_page" value="1" />
                
                <p  class="submit"><input style="background: #464646;color: #ffffff;border: 0;cursor: pointer;padding: 5px 0px 5px 0px;margin-top: 15px;min-width: 113px;" class="submit_btn" name="Submit" value="<?php _e('Save Changes','piereg');?>" type="submit" /></p>
            </div>
        </li>
	</ul>
      </form>
    </div>
  </div>
</div>
