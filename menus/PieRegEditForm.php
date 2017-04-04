<?php 
$data 	= PieRegister::getCurrentFields();
if(!is_array($data) || sizeof($data) == 0) {	
	$data 	= get_option( 'pie_fields_default' );
}

$button = get_option(OPTION_PIE_REGISTER);
$meta   = PieRegister::getDefaultMeta();

if( !isset($_GET['form_id']) && !$this->piereg_pro_is_activate )
{
	die("You don't have permission to access this page.");
}

?>
<div style="width:99%;overflow:hidden;" class="pieregister-admin">
  <div class="right_section">
    <div class="pie_wrap">
      <h2>
        <?php _e("Manage Forms : Form Editor","piereg"); ?>
      </h2>
      <?php
       	if( isset($_POST['error_message']) && !empty( $_POST['error_message'] ) )
            echo '<div id="error" class="error fade msg_belowheading"><p><strong>' . $_POST['error_message']  . "</strong></p></div>";
			
       	if(isset( $_POST['success_message'] ) && !empty( $_POST['success_message'] ))
            echo '<div id="message" class="updated fade msg_belowheading"><p><strong>' . $_POST['success_message']  . "</strong></p></div>";
		
    	?>
      <form method="post" id="formeditor">
        <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_reg_form_nonce','piereg_reg_form_nonce'); ?>
        <?php
        if(isset($_GET['form_id']) and ((int)$_GET['form_id']) != 0){
			echo '	<input type="hidden" name="form_id" value="'.base64_encode(((int)$_GET['form_id'])).'">
      				<input type="hidden" name="page" value="edit">';
		}?>
        <input type="hidden" name="field[form][type]" value="form">
        <input type="hidden" name="field[form][meta]" value="0">
        <div style="clear: both;float: none;">
          <input type="submit" style="padding:0px 20px;" class="button button-primary button-large" name="pie_form"  value="<?php _e("Save Settings",'piereg');?>">
          <?php
        if(isset($_GET['form_id']) and ((int)$_GET['form_id']) != 0){
			$form_id = isset($_GET['form_id'])?(int)$_GET['form_id']:0;
			$form_name = str_replace(" ","_",$data['form']['label']);
			$preview_url = add_query_arg(array('pr_preview'=>1,'form_id'=>$form_id,'prFormId'=>$form_id,'form_name'=>$form_name), get_permalink($button['alternate_register']));
		?>
        	<a href="<?php echo $preview_url; ?>" style="padding:0px 20px;"target="_blank" class="button button-primary button-large" name="pie_form">
          <?php _e("Preview",'piereg');?>
          </a>
          <?php } ?>
        </div>
        <!--Form Settings-->
        <ul>
          <li class="fields">
            <div class="fields_options" id="field_form_title"> <a href="#" class="edit_btn" title="<?php _e("Edit Form","piereg"); ?>"></a>
              <label> <?php echo $data['form']['label']?> </label>
              <br>
              <p id="paragraph_form"> <?php echo $this->piereg_get_small_string($data['form']['desc'],350);?> </p>
            </div>
            <div class="fields_main">
              <div class="advance_options_fields">
                <div class="advance_fields">
                  <label for="form_title">
                    <?php _e("Label","piereg"); ?>
                  </label>
                  <input id="form_title" value="<?php echo $data['form']['label']?>" type="text" name="field[form][label]" class="input_fields field_label">
                </div>
                <div class="advance_fields">
                  <label for="form_desc">
                    <?php _e("Description","piereg"); ?>
                  </label>
                  <textarea name="field[form][desc]" id="paragraph_textarea_form" rows="8" cols="16"><?php echo html_entity_decode(stripslashes($data['form']['desc'])); ?></textarea>
                </div>
                <div class="advance_fields">
                  <label>
                    <?php _e("CSS Class Name","piereg"); ?>
                  </label>
                  <input type="text" name="field[form][css]" value="<?php echo $data['form']['css'];?>" class="input_fields">
                </div>
                <div class="advance_fields">
                  <label>
                    <?php _e("Label Alignment","piereg"); ?>
                  </label>
                  <select class="swap_class" onchange="swapClass(this.value);" name="field[form][label_alignment]">
                    <option <?php if($data['form']['label_alignment']=='top') echo 'selected="selected"';?> value="top">
                    <?php _e("Top","piereg"); ?>
                    </option>
                    <option <?php if($data['form']['label_alignment']=='left') echo 'selected="selected"';?> value="left">
                    <?php _e("Left","piereg"); ?>
                    </option>
                  </select>
                </div>
                <div class="advance_fields">
                  <label id="set_user_role_"><?php echo __("Registering User Role","piereg"); ?></label>
                  <select id="set_user_role_" name="set_user_role_" >
                    <?php
						if(
						   isset($_GET['form_id']) and intval($_GET['form_id']) != 0 and 
						   isset($_GET['form_name']) and $_GET['form_name']!= ""
						   )
						{
							$user_role = $button['pie_regis_set_user_role_'.intval($_GET['form_id'])];
							$user_role = ( $user_role != "") ? $user_role : 'subscriber';
						}
						else
						{
							$user_role = $button['pie_regis_set_user_role_'];
							$user_role = ( $user_role != "") ? $user_role : 'subscriber';
						}
						
						global $wp_roles;
						
						$role = $wp_roles->roles;
						$wp_default_user_role = get_option("default_role");
						
						foreach($role as $key=>$value)
						{
							echo '<option value="'.$key.'"';
							echo ($user_role == $key) ? ' selected="selected" ' : '';
							echo '>'.$value['name'].'</option>';
						}
						?>
                  </select>
                </div>
                <?php
				$notification = array(__("Admin Verification","piereg"),
									  __("E-mail Verification","piereg"),
									  __("E-mail & Admin Verification","piereg"));
			  	?>
                <div class="advance_fields">
                  <label>
                    <?php _e("User Verification","piereg"); ?>
                  </label>
                  <select name="field[form][user_verification]" id="form_user_verification"  class="checkbox_fields enabel_user_verification">
                    <option value="0" <?php echo (((isset($data['form']['user_verification']) && $data['form']['user_verification'] == 0) || (!isset($data['form']['user_verification'])))?'selected="selected"':"");?> >
                    <?php _e("Use Default","piereg"); ?>
                    </option>
                    <option value="1" <?php echo ((isset($data['form']['user_verification']) && $data['form']['user_verification'] == 1)?'selected="selected"':"");?> ><?php echo $notification[0]; ?></option>
                    <option value="2" <?php echo ((isset($data['form']['user_verification']) && $data['form']['user_verification'] == 2)?'selected="selected"':"");?> ><?php echo $notification[1];?></option>
                    <?php if( $this->piereg_pro_is_activate ) { ?>
                    <option value="3" <?php echo ((isset($data['form']['user_verification']) && $data['form']['user_verification'] == 3)?'selected="selected"':"");?> ><?php echo $notification[2];?></option>
                    <?php } ?>
                  </select>
                </div>
                <?php if( $this->piereg_pro_is_activate ) { ?>
                <!--	Conditional's Logic		-->
                <div class="enabel_conditional_logic_area">
                  <div class="advance_fields">
                    <label>
                      <?php _e("Enable Conditional Logic","piereg"); ?>
                    </label>
                    <select name="field[form][conditional_logic]" data-conditional_area="form_conditional_area" id="form_conditional_logic"  class="enabel_conditional_logic">
                      <option value="1" <?php echo ((isset($data['form']['conditional_logic']) && $data['form']['conditional_logic'] == 1)?'selected="selected"':""); ?>>
                      <?php _e("Yes","piereg"); ?>
                      </option>
                      <option value="0" <?php echo (((isset($data['form']['conditional_logic']) && $data['form']['conditional_logic'] != 1)|| !isset($data['form']['conditional_logic']))?'selected="selected"':""); ?>>
                      <?php _e("No","piereg"); ?>
                      </option>
                    </select>
                  </div>
                  <div class="advance_fields" id="form_conditional_area" <?php echo (((isset($data['form']['conditional_logic']) && $data['form']['conditional_logic'] != 1)|| !isset($data['form']['conditional_logic']))?'style="display:none;"':""); ?>>
                    <div class="iscCnditionalOn">
                      <?php
					$is_add_new = true;
					if(isset($data['form']['notification'])){
					$xx = 0;	
					foreach($data['form']['notification'] as $form_notf_key=>$form_notf_val){ ?>
                      <div class="advance_fields">
                        <label for="form_notification">
                          <?php _e("Verifications","piereg"); ?>
                        </label>
                        <select name="field[form][notification][]" id="form_notification"  class="form_notification" style="width:100px;" >
                          <option value="1" <?php echo ((isset($data['form']['notification'][$form_notf_key]) && $data['form']['notification'][$form_notf_key] == 1)?'selected="selected"':"");?> ><?php echo $notification[0]; ?></option>
                          <option value="2" <?php echo ((isset($data['form']['notification'][$form_notf_key]) && $data['form']['notification'][$form_notf_key] == 2)?'selected="selected"':"");?> ><?php echo $notification[1];?></option>
                          <option value="3" <?php echo ((isset($data['form']['notification'][$form_notf_key]) && $data['form']['notification'][$form_notf_key] == 3)?'selected="selected"':"");?> ><?php echo $notification[2];?></option>
                        </select>
                        <span style="color:#fff;">
                        <?php _e("this field if","piereg");?>
                        </span>
                        <input type="hidden" id="form_selected_field_value" value="<?php echo ((isset($data['form']['selected_field'][$form_notf_key]))?$data['form']['selected_field'][$form_notf_key]:""); ?>">
                        <select data-selected-value="<?php echo ((isset($data['form']['selected_field'][$form_notf_key]))?$data['form']['selected_field'][$form_notf_key]:""); ?>" data-selected_field="form_selected_field" name="field[form][selected_field][]" class="form_selected_field piereg_all_field_dropdown" style="width:100px;">
                        </select>
                        <select id="form_field_rule_operator" name="field[form][field_rule_operator][]" class="field_rule_operator_select" style="width:auto;">
                          <option value="==" <?php echo ((isset($data['form']['field_rule_operator'][$form_notf_key]) && $data['form']['field_rule_operator'][$form_notf_key] == "==")?'selected="selected"':"");?> >
                          <?php _e("equal","piereg"); ?>
                          </option>
                          <option value="!=" <?php echo ((isset($data['form']['field_rule_operator'][$form_notf_key]) && $data['form']['field_rule_operator'][$form_notf_key] == "!=")?'selected="selected"':"");?>>
                          <?php _e("not equal","piereg"); ?>
                          </option>
                          <option value="empty" <?php echo ((isset($data['form']['field_rule_operator'][$form_notf_key]) && $data['form']['field_rule_operator'][$form_notf_key] == "empty")?'selected="selected"':"");?>>
                          <?php _e("empty","piereg"); ?>
                          </option>
                          <option value="not_empty" <?php echo ((isset($data['form']['field_rule_operator'][$form_notf_key]) && $data['form']['field_rule_operator'][$form_notf_key] == "not_empty")?'selected="selected"':"");?>>
                          <?php _e("not empty","piereg"); ?>
                          </option>
                          <option value=">" <?php echo ((isset($data['form']['field_rule_operator'][$form_notf_key]) && $data['form']['field_rule_operator'][$form_notf_key] == ">")?'selected="selected"':"");?>>
                          <?php _e("greater than","piereg"); ?>
                          </option>
                          <option value="<" <?php echo ((isset($data['form']['field_rule_operator'][$form_notf_key]) && $data['form']['field_rule_operator'][$form_notf_key] == "<")?'selected="selected"':"");?>>
                          <?php _e("less than","piereg"); ?>
                          </option>
                          <option value="contains" <?php echo ((isset($data['form']['field_rule_operator'][$form_notf_key]) && $data['form']['field_rule_operator'][$form_notf_key] == "contains")?'selected="selected"':"");?>>
                          <?php _e("contains","piereg"); ?>
                          </option>
                          <option value="starts_with" <?php echo ((isset($data['form']['field_rule_operator'][$form_notf_key]) && $data['form']['field_rule_operator'][$form_notf_key] == "starts_with")?'selected="selected"':"");?>>
                          <?php _e("starts with","piereg"); ?>
                          </option>
                          <option value="ends_with" <?php echo ((isset($data['form']['field_rule_operator'][$form_notf_key]) && $data['form']['field_rule_operator'][$form_notf_key] == "ends_with")?'selected="selected"':"");?>>
                          <?php _e("ends with","piereg"); ?>
                          </option>
                        </select>
                        <div class="wrap_cond_value"><input type="text" value="<?php echo ((isset($data['form']['conditional_value'][$form_notf_key]))?$data['form']['conditional_value'][$form_notf_key]:""); ?>" name="field[form][conditional_value][]" id="form_conditional_value" class="input_fields conditional_value conditional_value_input" placeholder="Enter Value"></div>
                        <?php if($is_add_new){ ?>
                        <a href="javascript:;" class="add_conditional_value_fields" style="color:white">+</a>
                        <?php } else { ?>
                        <a href="javascript:;" class="delete_conditional_value_fields" style="color:white;font-size: 13px;margin-left: 2px;">x</a>
                        <?php } 
						$is_add_new = false;
						$xx++;
						?>
                      </div>
                      <?php }
					} 
					?>
                    </div>
                    <?php 
					if($is_add_new){ ?>
                    <div class="advance_fields">
                      <label for="form_notification">
                        <?php _e("Verifications","piereg"); ?>
                      </label>
                      <select name="field[form][notification][]" id="form_notification"  class="form_notification" style="width:100px;" >
                        <option value="1" selected="selected"><?php echo $notification[0]; ?></option>
                        <option value="2"><?php echo $notification[1];?></option>
                        <option value="3"><?php echo $notification[2];?></option>
                      </select>
                      <span style="color:#fff;">
                      <?php _e("this field if","piereg");?>
                      </span>
                      <select data-selected_field="form_selected_field" name="field[form][selected_field][]" class="form_selected_field piereg_all_field_dropdown" style="width:100px;">
                      </select>
                      <select id="form_field_rule_operator" name="field[form][field_rule_operator][]" class="field_rule_operator_select" style="width:auto;">
                        <option selected="selected" value="==">
                        <?php _e("equal","piereg"); ?>
                        </option>
                        <option value="!=">
                        <?php _e("not equal","piereg"); ?>
                        </option>
                        <option value="empty">
                        <?php _e("empty","piereg"); ?>
                        </option>
                        <option value="not_empty">
                        <?php _e("not empty","piereg"); ?>
                        </option>
                        <option value=">">
                        <?php _e("greater than","piereg"); ?>
                        </option>
                        <option value="<">
                        <?php _e("less than","piereg"); ?>
                        </option>
                        <option value="contains">
                        <?php _e("contains","piereg"); ?>
                        </option>
                        <option value="starts_with">
                        <?php _e("starts with","piereg"); ?>
                        </option>
                        <option value="ends_with">
                        <?php _e("ends with","piereg"); ?>
                        </option>
                      </select>
                      <div class="wrap_cond_value"><input type="text" name="field[form][conditional_value][]" id="form_conditional_value" class="input_fields conditional_value conditional_value_input" placeholder="Enter Value"></div>
                      <a href="javascript:;" class="add_conditional_value_fields" style="color:white">+</a> </div>
                    <?php } ?>
                  </div>
                </div>
                <?php } ?>
              </div>
            </div>
          </li>
        </ul>
        <!--Form Settings End-->
        <fieldset>
          <legend align="center"><?php echo _e("Drag Fields Here","piereg"); ?></legend>
          <div id="hint_1" class="fields_hint" style="left: 95%;top: 50%; z-index:2;"> <img src="<?php echo plugins_url('pie-register'); ?>/images/left_arrow.jpg" width="45" height="26" align="left">
            <div class="hint_content">
              <h4>
                <?php _e("Did You Know ?","piereg"); ?>
              </h4>
              <span>
              <?php _e("You can sort fields vertically","piereg"); ?>
              </span> <br>
              <input type="button" class="thanks" value="<?php _e("Yes Thanks !","piereg"); ?>">
            </div>
          </div>
          <!--Form Fields-->
          <ul id="elements"  class="piereg_registration_form_fields">
            <?php   
		if(sizeof($data) >  0) 
		{
			$no = max(array_keys($data));			
			$field_values = array();
			$meta   = $this->getDefaultMeta();
			/*$is_pricing == false;*/
			foreach($data as $field)
			{	
				if( $field['type'] == "honeypot" && !$this->piereg_pro_is_activate ) {	
					continue;
				}	
					
				
				if( $field['type'] == "two_way_login_phone" ) {
					include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
					$twilio_option = get_option("pie_register_twilio");
					$plugin_status = get_option('piereg_api_manager_addon_Twilio_activated');
					if( isset($twilio_option["enable_twilio"]) && $twilio_option["enable_twilio"] == 0 || !$this->piereg_pro_is_activate || $plugin_status != "Activated" || !is_plugin_active("pie-register-twilio/pie-register-twilio.php") ) {	
						continue;
					}
				}
					
				//We don't need Form and Submit Button in sorting
				if($field['type']=="submit" || $field['type']=="" || $field['type']=="form" || ($field['type']=="invitation" && $button["enable_invitation_codes"]=="0"))
				{
					continue;
				}
					
					?>
            <li class="fields">
              <div id="holder_<?php echo $field['id']?>" class="fields_options fields_optionsbg">
              <?php
		
		if($field['type'] == "url" || $field['type'] == "aim" || $field['type'] == "yim" || $field['type'] == "jabber" || $field['type'] == "description") 
		{
			$field['type'] = "default";	
		}
				
				$data_field_id = '';
				  switch($field['type']) :
						case 'text' :
						//case 'username' :
						case 'website' :
						case 'hidden' :
						case 'phone':
						case 'invitation' :
						//case 'password' :
						//case 'email' :
						case 'textarea':
						case 'dropdown':
						//case 'multiselect':
						case 'number':
						//case 'radio':
						//case 'checkbox':
						case 'name':
						//case 'pricing':
							$data_field_id = ' data-field_id="'.$field['id'].'" '; // Applied condtional logics in these fields. 
						break; 
				  endswitch;
		
		
		 //We can't edit default wordpress fields
		 echo '<a href="javascript:;" class="edit_btn" title="'.__("Edit","piereg").'"></a>';
          ?>
              <!--Adding Label-->
              <div class="label_position"  id="field_label_<?php echo $field['id']?>"  <?php echo $data_field_id; ?>>
                <?php if( isset($field['label']) && $field['label'] == "E-mail")  { ?>
                <label><?php echo _e("Email"); ?></label>
                <?php } else { ?>
                <label><?php echo (empty($field['label']) ? $field['type']:trim($field['label']))?></label>
                <?php } ?>
              </div>
              <?php
           //We can't remove Username, password and email fields
		    if(!isset($field['remove']) || ($field['type'] == "username") )
				echo '<a href="javascript:;" rel="'.$field['id'].'" class="delete_btn" title="'.(__("Delete","piereg")).'">X</a>';                
			else
				echo '<input  name="field['.$field['id'].'][remove]" value="0" type="hidden" /> '; 
			
			$piereg_recaptcha_area = "";
			if( $field['type'] == "captcha" )
				$piereg_recaptcha_area = "piereg_recaptcha_area";
           		?>
              <input type="hidden" name="field[<?php echo $field['id']?>][id]" value="<?php echo $field['id']?>" id="id_<?php echo $field['id']?>">
              <input type="hidden" name="field[<?php echo $field['id']?>][type]" id="type_<?php echo $field['id']?>" value="<?php echo $field['type']?>" >
              <div class="fields_position <?php echo $piereg_recaptcha_area; ?>" id="field_position_<?php echo $field['id']?>">
                <?php
					switch($field['type']) :
					case 'text' :
					case 'username' :
					case 'website' :
					case 'hidden' :
					case 'phone':
					case 'two_way_login_phone':
						$this->addTextField($field,$field['id'],$field['type']);
					break;
					case 'honeypot':
						$this->addHoneyPotField($field,$field['id'],$field['type']);
					break;
					case 'invitation' :					
						$this->addInvitationField($field,$field['id']);					
					break;
					case 'password' :
						$this->addPassword($field,$field['id']);
					break;
					case 'email' :
						$this->addEmail($field,$field['id']);
					break;
					case 'textarea':
						$this->addTextArea($field,$field['id']);
					break;
					case 'dropdown':
					case 'multiselect':
						$this->addDropdown($field,$field['id']);
					break;
					case 'number':
						$this->addNumberField($field,$field['id']);			
					break;
					case 'radio':
					case 'checkbox':
						$this->addCheckRadio($field,$field['id']);
					break;
					case 'html':
						$this->addHTML($field,$field['id']);
					break;
					case 'name':
						$this->addName($field,$field['id']);
					break;
					case 'time':
						$this->addTime($field,$field['id']);
					break;
					case 'upload':
						$this->addUpload($field,$field['id']);
					break;
					case 'profile_pic':	
						$this->addProfilePicUpload($field,$field['id']);
					break;
					case 'address':
						$this->addAddress($field,$field['id']);
					break;
					case 'captcha':
						$this->addCaptcha($field,$field['id']);
					break;			
					case 'math_captcha':
						$this->addMath_Captcha($field,$field['id']);
					break;
					case 'date':
						$this->addDate($field,$field['id']);
					break;
					case 'list':
						$this->addList($field,$field['id']);
					break;
					case 'pricing':
						$this->addPricing($field,$field['id']);
						/*$is_pricing = true;*/
					break;
					case 'sectionbreak':
						$this->addSectionBreak($field,$field['id']);
					break;
					case 'pagebreak':
						$this->addPageBreak($field,$field['id']);
					break;
					case 'default':
						$this->addDefaultField($field,$field['id']);
					break;
				endswitch;				
					
				$field_values[$field['id']] = serialize($this->cleantext($field,$field['id']));
				
				  echo "</div>";
				 			  
			 ?>
              </div>
              <?php 
			if(isset($meta[$field['type']]) && $field['type'] == "pricing" ){
				$payment_gateways_html = "";
				$payment_gateways_list = $this->payment_gateways_list();
				foreach($payment_gateways_list as $pgKey=>$pgval){
					$selected_pnl = "";
					
					if( isset($field['allow_payment_gateways']) && !empty($field['allow_payment_gateways']) && is_array($field['allow_payment_gateways']) ){
						if( in_array($pgKey,$field['allow_payment_gateways']) ){
							$selected_pnl = 'checked="checked"';
						}
					}else{
						$selected_pnl = 'checked="checked"';
					}
					
					$payment_gateways_html .= '<label for="allow_payment_gateways_'.$pgKey.'" class="required piereg-payment-list"><input name="field['.$field['id'].'][allow_payment_gateways][]" id="allow_payment_gateways_'.$pgKey.'" value="'.$pgKey.'" type="checkbox" '.$selected_pnl.' class="checkbox_fields">'.$pgval.'</label>';
				}
				
				if( $payment_gateways_html == "" )
				{
					$payment_gateways_html .= "<label class='piereg-payment-list'>No payment gateway enable.</label>";	
				}
				
				echo str_replace( array("%d%","%payment_gateways_list_box%") , array($field['id'],$payment_gateways_html) , $meta[$field['type']] );
				
			}elseif(isset($meta[$field['type']])){
				echo str_replace("%d%",$field['id'],$meta[$field['type']]);
			}
		  		
		  	?>
            </li>
            <?php 	
				
			}	
		}
		
		?>
          </ul>
        </fieldset>
        <ul id="submit_ul">
          <li class="fields">
            <div class="fields_options submit_field"> <a href="#" class="edit_btn" title="<?php _e("Edit Button","piereg"); ?>"></a>
              <input id="reset_btn" disabled="disabled" name="fields[reset]" type="reset" class="submit_btn" value="<?php echo $data['submit']['reset_text']?>" />
              <input disabled="disabled" name="fields[submit]" type="submit" class="submit_btn" value="<?php echo $data['submit']['text']?>" />
              <input name="field[submit][label]" value="Submit"  type="hidden" />
              <input name="field[submit][type]" value="submit" type="hidden" />
              <input name="field[submit][remove]" value="0" type="hidden" />
              <input name="field[submit][meta]" value="0" type="hidden">
            </div>
            <div class="fields_main">
              <div class="advance_options_fields advance_options_submit">
                <div class="advance_fields">
                  <label>
                    <?php _e("Submit Button Text","piereg"); ?>
                  </label>
                  <input type="text" class="input_fields" name="field[submit][text]" value="<?php echo $data['submit']['text']?>">
                </div>
                <div class="advance_fields">
                  <label>
                    <?php _e("Show Reset Button","piereg"); ?>
                  </label>
                  <select onchange="showHideReset();" id="show_reset" class="swap_reset" name="field[submit][reset]">
                    <option <?php if($data['submit']['reset']=='0') echo 'selected="selected"';?> value="0">
                    <?php _e("No","piereg"); ?>
                    </option>
                    <option <?php if($data['submit']['reset']=='1') echo 'selected="selected"';?> value="1">
                    <?php _e("Yes","piereg"); ?>
                    </option>
                  </select>
                </div>
                <div class="advance_fields">
                  <label>
                    <?php _e("Reset Button Text","piereg"); ?>
                  </label>
                  <input type="text" class="input_fields" name="field[submit][reset_text]" value="<?php echo $data['submit']['reset_text']?>">
                </div>
                <div class="advance_fields">
                  <label>
                    <?php _e("Confirmation Message","piereg"); ?>
                  </label>
                  <div class="radio_fields">
                    <input class="reg_success" type="radio" value="text" name="field[submit][confirmation]" <?php if($data['submit']['confirmation']=='text') echo 'checked="checked"';?>>
                    <label>
                      <?php _e("Text","piereg"); ?>
                    </label>
                    <input class="reg_success" type="radio" value="page" name="field[submit][confirmation]" <?php if($data['submit']['confirmation']=='page') echo 'checked="checked"';?>>
                    <label>
                      <?php _e("Page","piereg"); ?>
                    </label>
                    <input class="reg_success" type="radio" value="redirect" name="field[submit][confirmation]" <?php if($data['submit']['confirmation']=='redirect') echo 'checked="checked"';?>>
                    <label>
                      <?php _e("Redirect","piereg"); ?>
                    </label>
                  </div>
                </div>
                <div class="advance_fields submit_meta submit_meta_redirect">
                  <label>
                    <?php _e("Redirect URL","piereg"); ?>
                  </label>
                  <input type="text" class="input_fields" name="field[submit][redirect_url]" value="<?php echo $data['submit']['redirect_url']?>">
                </div>
                <div class="advance_fields submit_meta submit_meta_page">
                  <label>
                    <?php _e("Select Page","piereg"); ?>
                  </label>
                  <?php  $args =  array("name"=>"field[submit][page]","selected"=>$data['submit']['page']);wp_dropdown_pages( $args ); ?>
                </div>
                <div class="advance_fields submit_meta submit_meta_text">
                  <label>
                    <?php _e("Registration Success Message","piereg"); ?>
                  </label>
                  <textarea name="field[submit][message]" rows="8" cols="16"><?php echo $data['submit']['message']; ?></textarea>
                </div>
              </div>
            </div>
          </li>
        </ul>
        <?php
	  	if($this->check_enable_payment_method() == "true")
		{
			?>
        <ul id="paypal_button">
          <li class="fields">
            <?php do_action("show_icon_payment_gateway"); ?>
          </li>
        </ul>
        <?php
		}
	  ?>
        <input type="submit" style="float: right;margin-right:85px;" class="button button-primary button-large" name="pie_form"  value="<?php _e("Save Settings",'piereg');?>">
      </form>
    </div>
    <div class="right_menu">
      <div id="hint_0" style="top: 135px;margin-left: -271px;position: fixed;float:right;" class="fields_hint"> <img src="<?php echo plugins_url('pie-register'); ?>/images/right_arrow.jpg" width="45" height="26" align="right">
        <div class="hint_content">
          <h4>
            <?php _e("Did You Know ?","piereg"); ?>
          </h4>
          <span>
          <?php _e("You can Drag n Drop fields.","piereg"); ?>
          </span> <br>
          <input type="button" class="thanks" value="<?php _e("Yes Thanks !","piereg"); ?>">
        </div>
      </div>
      <ul>
        <li id="default_fields"><a class="right_menu_heading" href="javascript:;">
          <?php _e("Default Fields","piereg"); ?>
          </a>
          <ul class="controls picker pie-content-ul"  id="content_1">
            <li class="standard_name"><a name="username" class="default" href="javascript:;">
              <?php _e("Username","piereg"); ?>
              </a></li>
            <li class="standard_name"><a name="name" class="default" href="javascript:;">
              <?php _e("Name","piereg"); ?>
              </a></li>
            <li class="standard_website"><a name="url" class="default" href="javascript:;">
              <?php _e("Website","piereg"); ?>
              </a></li>
            <li class="standard_aim"><a name="aim" class="default" href="javascript:;">
              <?php _e("AIM","piereg"); ?>
              </a></li>
            <li class="standard_yahoo"><a name="yim" class="default" href="javascript:;">
              <?php _e("Yahoo IM","piereg"); ?>
              </a></li>
            <li class="standard_google"><a name="jabber" class="default" href="javascript:;">
              <?php _e("Jabber / Google Talk","piereg"); ?>
              </a></li>
            <li class="standard_about"><a name="description" class="default" href="javascript:;">
              <?php _e("About Yourself","piereg"); ?>
              </a></li>
          </ul>
        </li>
        <li id="standard_fields"><a class="right_menu_heading" href="javascript:;">
          <?php _e("Standard Fields","piereg"); ?>
          </a>
          <ul class="controls picker pie-content-ul"  id="content_2">
            <li class="standard_text"><a name="text" href="javascript:;">
              <?php _e("Text Field","piereg"); ?>
              </a></li>
            <li class="standard_textarea"><a name="textarea" href="javascript:;">
              <?php _e("Text Area","piereg"); ?>
              </a></li>
            <li class="standard_dropdown"><a name="dropdown" href="javascript:;">
              <?php _e("Drop Down","piereg"); ?>
              </a></li>
            <li class="standard_multiselect"><a name="multiselect" href="javascript:;">
              <?php _e("Multi Select","piereg"); ?>
              </a></li>
            <li class="standard_numbers"><a name="number" href="javascript:;">
              <?php _e("Number","piereg"); ?>
              </a></li>
            <li class="standard_checkbox"><a name="checkbox" href="javascript:;">
              <?php _e("Checkbox","piereg"); ?>
              </a></li>
            <li class="standard_radio"><a name="radio" href="javascript:;">
              <?php _e("Radio Buttons","piereg"); ?>
              </a></li>
            <li class="standard_hidden"><a name="hidden" href="javascript:;">
              <?php _e("Hidden Field","piereg"); ?>
              </a></li>
            <li class="standard_html"><a name="html" href="javascript:;">
              <?php _e("HTML Script","piereg"); ?>
              </a></li>
            <li class="standard_selection"><a name="sectionbreak" href="javascript:;">
              <?php _e("Section Break","piereg"); ?>
              </a></li>
            <li class="standard_pagebreak"><a name="pagebreak" href="javascript:;">
              <?php _e("Page Break","piereg"); ?>
              </a></li>
          </ul>
        </li>
        <li id="advanced_fields"><a class="right_menu_heading" href="javascript:;">
          <?php _e("Advanced Fields","piereg"); ?>
          </a>
          <ul class="controls picker pie-content-ul"  id="content_3">
            <li class="standard_address"><a name="address" href="javascript:;">
              <?php _e("Address","piereg"); ?>
              </a></li>
            <li class="standard_date"><a name="date" href="javascript:;">
              <?php _e("Date","piereg"); ?>
              </a></li>
            <li class="standard_time"><a name="time" href="javascript:;">
              <?php _e("Time","piereg"); ?>
              </a></li>
            <li class="standard_phone"><a name="phone" href="javascript:;">
              <?php _e("Phone","piereg"); ?>
              </a></li>
            <?php 
          include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		  $twilio_option = get_option("pie_register_twilio");
		  $plugin_status = get_option('piereg_api_manager_addon_Twilio_activated');
		  if( is_plugin_active("pie-register-twilio/pie-register-twilio.php") && isset($twilio_option["enable_twilio"]) && $twilio_option["enable_twilio"] == 1 && $this->piereg_pro_is_activate && $plugin_status == "Activated" ){ ?>
            <li class="standard_twoway_phone"><a name="two_way_login_phone" class="default" href="javascript:;">
              <?php _e("2Way Login Phone #","piereg"); ?>
              </a></li>
            <?php 
		  } ?>
            <li class="standard_upload"><a name="upload" href="javascript:;">
              <?php _e("Upload File","piereg"); ?>
              </a></li>
            <li class="standard_profile"><a name="profile_pic" class="default" href="javascript:;">
              <?php _e("Profile Picture","piereg"); ?>
              </a></li>
            <li class="standard_list"><a name="list" href="javascript:;">
              <?php _e("List","piereg"); ?>
              </a></li>
            <?php if( $button['enable_paypal'] == 1 || PieRegister::check_payment_plugin_activation() == "true" ): ?>
            <li class="standard_pricing"><a name="pricing" class="default" href="javascript:;">
              <?php _e("Membership","piereg"); ?>
              </a></li>
            <?php endif; ?>
            <?php if($button['enable_invitation_codes']==1) { ?>
            <li class="standard_invitation"><a name="invitation" class="default" href="javascript:;">
              <?php _e("Invitation Codes","piereg"); ?>
              </a></li>
            <?php } ?>
            <li class="standard_captcha_n"><a name="captcha" class="default" href="javascript:;">
              <?php _e("Re-Captcha","piereg"); ?>
              </a></li>
            <li class="standard_captcha"><a name="math_captcha" class="default" href="javascript:;">
              <?php _e("Math Captcha","piereg"); ?>
              </a></li>
            <?php if( $this->piereg_pro_is_activate ) { ?>
            <li class="standard_honeypot"><a name="honeypot" class="default" href="javascript:;">
              <?php _e("Honeypot","piereg"); ?>
              </a></li>
            <?php } ?>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</div>