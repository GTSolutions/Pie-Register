<?php $piereg = PieReg_Base::get_pr_global_options(); ?>
<div class="pieregister-admin">
<div id="notifications_tabs" class="hideBorder" style="display:none;">
    <div class="settings">
        <h2 class="headingwidth"><?php _e("Notifications",'piereg') ?></h2>
        <?php
			if(isset($_POST['notice']) && $_POST['notice'] ){
				echo '<div id="message" class="updated fade msg_belowheading"><p><strong>' . $_POST['notice'] . '.</strong></p></div>';
			}
			if( isset($_POST['error_message']) && !empty( $_POST['error_message'] ) )
				echo '<div style="clear: both;float: none;"><p class="error">' . $_POST['error_message']  . "</p></div>";
			if( isset($_POST['error']) && !empty( $_POST['error'] ) )
				echo '<div style="clear: both;float: none;"><p class="error">' . $_POST['error']  . "</p></div>";
			if(isset( $_POST['success_message'] ) && !empty( $_POST['success_message'] ))
				echo '<div style="clear: both;float: none;"><p class="success">' . $_POST['success_message']  . "</p></div>";
			?>
        <div class="tabOverwrite">
            <div id="tabsSetting" class="tabsSetting">
                <ul class="tabLayer1">
                    <li><a href="#piereg_admin_notification"><?php _e("Admin Notification","piereg") ?></a></li>
                    <li><a href="#piereg_user_notification"><?php _e("User Notification","piereg") ?></a></li>
                </ul>
            </div>
        </div>
	</div>
    <div id="container" class="pieregister-admin">
        <div id="piereg_admin_notification">
            <div class="right_section">
                <div class="notifications">
                  <form method="post" action="#piereg_admin_notification">
                    <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_admin_email_notification','piereg_admin_email_notification'); ?>
                    <ul>
                      <li>
                        <div class="fields">
                          <input name="enable_admin_notifications" <?php echo ($piereg['enable_admin_notifications']=="1")?'checked="checked"':''?> type="checkbox" class="checkbox" value="1" />
                          <?php _e("Enable email notifications to administrator",'piereg');?>              
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
                          <div class="radio_fields flt_lft">
                            	<input type="checkbox" name="admin_message_email_formate" id="admin_message_email_formate" value="1" <?php echo ($piereg['admin_message_email_formate']=="1")?'checked="checked"':''?> />	
                          </div>
                          <label class="labelaligned"><?php _e("Email HTML Format",'piereg');?></label>
                        </div>
                      </li>
                      <li>
                        <div class="fields">
                        <label><?php _e("Message: Enter a message below to receive a notification email when new users register.",'piereg');?></label>
                        <p><strong><?php _e("Replacement Keys","piereg"); ?>:</strong>
                        <?php
                            $fields = maybe_unserialize(get_option("pie_fields"));
                            $replacement_fields = '';
                            if( sizeof($fields) > 0 && (is_array($fields) || is_object($fields)) )
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
                                    case 'html' :
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
                            <select class="piereg_replacement_keys" name="replacement_keys" id="replacement_keys">
                                <option value="select"><?php _e('Select','piereg');?></option>
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
                                </optgroup>
                            </select>
                           </p>
                          <textarea name="admin_message_email" class="ckeditor" id="piereg_text_editor"><?php echo $piereg['admin_message_email']?></textarea>
                            
                          <div class="piereg_clear"></div>
                        </div>
                      </li>
                      <li>
                        <div class="fields">
                            <input name="action" value="pie_reg_update" type="hidden" />
                            <input type="hidden" name="admin_email_notification_page" value="1" />
                            <p class="submit"><input style="background: #464646;color: #ffffff;border: 0;cursor: pointer;padding: 5px 0px 5px 0px;margin-top: -15px;margin-right:0px;min-width: 113px;float:right;" class="submit_btn" name="Submit" value="<?php _e('Save Changes','piereg');?>" type="submit" /></p>
                        </div>
                      </li>
                    </ul>
                  </form>
                </div>
              </div>
        </div>
        <div id="piereg_user_notification">
            <?php
			$pie_user_email_types 	= get_option( 'pie_user_email_types' );
			$replacement_fields = "";			   	
			$fields = maybe_unserialize(get_option("pie_fields"));
			if(sizeof($fields ) > 0 && (is_array($fields) || is_object($fields)) )
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
					
					$replacement_fields .= '<option value="%'.$meta_key.'%">'.$pie_fields['label'].'</option>';
				}
			}
			?>
            
			<div class="right_section">
                <div class="notifications">
                  <form method="post" action="#piereg_user_notification">
                  <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_user_email_notification','piereg_user_email_notification'); ?>
                    <ul>
                      <li>
                        <div class="fields">
                          <label><?php _e("Select a template to edit",'piereg') ?></label>             
                          
                          <select style="margin: 8px 0px 12px;font-size:15px;width:385px;" id="user_email_type" name="user_email_type" onchange="changediv()">
                            <?php foreach ($pie_user_email_types as $val=>$type) { ?>
                                <?php $selected = (isset($_POST['user_email_type']) && $val == $_POST['user_email_type'] ) ? true : false; ?>
                                <option <?php echo ($selected) ? 'selected="selected"' : '' ; ?> value="<?php echo $val?>"><?php _e($type,"piereg")?></option>                    
                            <?php } ?>
                          </select>
                        </div>
                        </li>
                        <?php foreach ($pie_user_email_types as $val=>$type) { ?>
                      	<li class="<?php echo $val?> hide-div">
                        <div class="fields">
                          <label><?php _e("From Name",'piereg') ?></label>
                          <input name="user_from_name_<?php echo $val?>" value="<?php echo $piereg['user_from_name_'.$val]?>" type="text" class="input_fields2" />
                        </div>
                      </li>
                      <li class="<?php echo $val?> hide-div">
                        <div class="fields">
                          <label><?php _e("From Email",'piereg') ?></label>
                          <input name="user_from_email_<?php echo $val?>" value="<?php echo $piereg['user_from_email_'.$val]?>" type="text" class="input_fields2" />
                        </div>
                      </li>
                      <li class="<?php echo $val?> hide-div">
                        <div class="fields">
                          <label><?php _e("Reply To",'piereg') ?></label>
                          <input name="user_to_email_<?php echo $val?>" value="<?php echo $piereg['user_to_email_'.$val]?>" type="text" class="input_fields2" />
                        </div>
                      </li>
                      <li class="<?php echo $val?> hide-div">
                        <div class="fields">
                          <label><?php _e("Subject",'piereg') ?></label>
                          <input name="user_subject_email_<?php echo $val?>" value="<?php echo $piereg['user_subject_email_'.$val]?>" type="text" class="input_fields" />
                        </div>
                      </li>
                      <li class="<?php echo $val?> hide-div">
                            <div class="fields">
                              <label style="width:auto;margin-right:20px;"><?php _e("Email Format: HTML Text",'piereg');?></label>
                                <div class="radio_fields">
                                    <input type="radio" id="<?php echo 'user_formate_email_'.$val; ?>_yes" name="<?php echo 'user_formate_email_'.$val; ?>" value="1" <?php echo ($piereg['user_formate_email_'.$val] == "1")? ' checked="checked" ' : '' ?>>
                                    <label for="<?php echo 'user_formate_email_'.$val; ?>_yes" style="float:none;"><?php _e("Yes",'piereg');?></label>
                                    &nbsp;&nbsp;
                                    <input type="radio" id="<?php echo 'user_formate_email_'.$val; ?>_no" name="<?php echo 'user_formate_email_'.$val; ?>" value="0" <?php echo ($piereg['user_formate_email_'.$val] == "0")? ' checked="checked" ' : '' ?>>
                                    <label for="<?php echo 'user_formate_email_'.$val; ?>_no" style="float:none;"><?php _e("No",'piereg');?></label>
                                </div>
                            </div>
                      </li>
                      <li class="<?php echo $val?> hide-div">
                        <div class="fields">
                          	<label><?php _e("Message: Enter a message below to send notification emails to users when a condition is met.",'piereg') ?></label>    
                          	<p><strong><?php _e("Replacement Keys","piereg");?>:</strong>
                            <select class="piereg_replacement_keys" name="replacement_keys<?php echo $val?>" id="replacement_keys<?php echo $val?>">
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
                                    <option value="%user_ip%"><?php _e("User IP",'piereg') ?></option>
                                    <option value="%user_new_email%"><?php _e("User New E-mail",'piereg') ?></option>
                                    <option value="%user_last_date%"><?php _e("User Last Date",'piereg') ?></option>
                                    <option value="%blogname%"><?php _e("Blog Name",'piereg') ?></option>
                                    <option value="%siteurl%"><?php _e("Site URL",'piereg') ?></option>
                                    <option value="%blogname_url%"><?php _e("Blog Name With Site URL",'piereg') ?></option>
                                    <option value="%reset_password_url%"><?php _e("Reset Password URL",'piereg') ?></option>
                                    <option value="%activationurl%"><?php _e("User Activation URL",'piereg') ?></option>
                                    <option value="%reset_email_url%"><?php _e("Reset Email URL",'piereg') ?></option>
                                    <option value="%confirm_current_email_url%"><?php _e("Confirm Current Email URL",'piereg') ?></option>
                                    <option value="%pending_payment_url%"><?php _e("Pending Payment URL",'piereg') ?></option>
                                </optgroup>
                            </select>
                           </p>
                          	<textarea name="user_message_email_<?php echo $val?>" id="piereg_text_editor_<?php echo $val?>" class="ckeditor"><?php echo $piereg['user_message_email_'.$val]?></textarea>
							
                          <div class="piereg_clear"></div>
                        </div>
                      </li>
                      <?php } ?>
                    </ul>
                    <input name="action" value="pie_reg_update" type="hidden" />
                    <input type="hidden" name="user_email_notification_page" value="1" />
                    <p class="submit btnvisibile">
                      <input name="Submit" style="background: #464646;color: #ffffff;border: 0;cursor: pointer;padding: 5px 0px 5px 0px;margin-top: 15px;margin-right:47px;min-width: 113px;float:right;" value="<?php _e('Save Changes','piereg');?>" type="submit" />
                    </p>
                  </form>
            		<?php 
                    $old_ver_options = get_option("pie_register");
                    if( (isset($old_ver_options['adminvmsg']) && $old_ver_options['adminvmsg'] != "")  || (isset($old_ver_options['emailvmsg']) && $old_ver_options['emailvmsg'] != "") || isset($old_ver_options['msg']) )
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
    </div> 
</div>
</div>