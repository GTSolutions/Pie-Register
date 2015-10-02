<?php

$button = get_option('pie_register_2');
//$buttonADN = get_option('pie_register_authorize_net'); 
$meta   = $this->getDefaultMeta();  
?>
<link href="<?php echo plugins_url('css/jquery.mCustomScrollbar.css',dirname(__FILE__)); ?>" rel="stylesheet" />
<style type="text/css">
	.pie-content-ul{width:100%; max-height:200px; overflow:auto;}
</style>
<?php /*?><script type="text/javascript" src="<?php echo plugins_url("js/phpjs.js",dirname(__FILE__));?>"></script>
<script type="text/javascript" src="<?php echo plugins_url("js/drag.js",dirname(__FILE__));?>"></script><?php */?>
<script type="text/javascript" src="<?php echo plugins_url("ckeditor/ckeditor.js",dirname(__FILE__));?>"></script>
<?php /*?><script src="<?php echo plugins_url('js/jquery.mCustomScrollbar.min.js',dirname(__FILE__)); ?>"></script><?php */?>

<script type="text/javascript">
var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
var hintNum = 0;
var piereg = jQuery.noConflict();
piereg(document).ready(function(e) {
    var displayhints = "<?php echo $button['display_hints']?>";
	if(displayhints=="1")
	{
		if(sessionStorage.getItem("hint") != "abc")
		{
			piereg("#hint_"+hintNum).delay(500).fadeIn();	
		}
		piereg(".thanks").click(function() 
		{
			piereg(this).parents(".fields_hint").delay(100).fadeOut();			
			hintNum++;
			jQuery("#hint_"+hintNum).delay(500).fadeIn();
			sessionStorage.setItem("hint","abc");
		});	
	}
	else
	{
		piereg(".fields_hint").remove();	
	}
});
piereg(document).ready(function(){
	piereg( document ).tooltip({
		track: true
	});
});


var defaultMeta = Array();
<?php
foreach($meta as $key=>$value)
{
?>
	defaultMeta["<?php echo $key?>"] = '<?php echo esc_sql($value)?>';	
<?php 
}
?>
</script>

<div style="width:99%;overflow:hidden;" class="pieregister-admin">
<div class="right_section">
  <div class="pie_wrap">
    <h2>
      <?php _e("Form Editor : Registration Form",'piereg');?>
    </h2>
    
    <!-- Add Start -->
    <?php
    //$PieReg_Adds = new PieReg_Adds();
	//$PieReg_Adds->get_add("form_editor");
	?>
    <!-- Add End -->
    
    <form method="post" id="formeditor">
      <input type="hidden" name="field[form][type]" value="form">
      <input type="hidden" name="field[form][meta]" value="0">
      <br /><br /><br /><br /><br />
      <input type="submit" style="padding:0px 20px;" class="button button-primary button-large" name="pie_form"  value="<?php _e("Save Changes",'piereg');?>">
      <a href="<?php echo get_bloginfo("url")?>?pr_preview=1" style="padding:0px 20px;" target="_blank" class="button button-primary button-large" name="pie_form" ><?php _e("Preview",'piereg');?></a>
      <!--Form Settings-->
      
      <ul>
        <li class="fields">
          <div class="fields_options" id="field_form_title"> <a href="javascript:;" class="edit_btn" title="<?php _e("Edit Form","piereg") ?>"></a>
            <label>
              <?php echo $data['form']['label']?>
            </label>
            <br>
            <p id="paragraph_form">
              <?php echo stripslashes($data['form']['desc'])?>
            </p>
          </div>
          <div class="fields_main">
            <div class="advance_options_fields">
              <div class="advance_fields">
                <label><?php _e("Label","piereg"); ?></label>
                <input id="form_title" value="<?php echo $data['form']['label']?>" type="text" name="field[form][label]" class="input_fields field_label">
              </div>
              <div class="advance_fields">
                <label for="form_desc"><?php _e("Description","piereg"); ?></label>
                <textarea onkeyup="changeParaText('form');" name="field[form][desc]" id="paragraph_textarea_form" rows="8" cols="16"><?php echo $data['form']['desc'];?>
</textarea>
              </div>
              <div class="advance_fields">
                <label><?php _e("CSS Class Name","piereg"); ?></label>
                <input type="text" name="field[form][css]" value="<?php echo $data['form']['css'];?>" class="input_fields">
              </div>
              <div class="advance_fields">
                <label><?php _e("Label Alignment","piereg"); ?></label>
                <select class="swap_class" onchange="swapClass(this.value);" name="field[form][label_alignment]">
                <?php 
				$form_label_alignment = (isset($data['form']['label_alignment']) && !empty($data['form']['label_alignment']))?$data['form']['label_alignment']:"left"; 
				?>
                  <option <?php if($form_label_alignment=='top') echo 'selected="selected"';?> value="top"><?php _e("Top","piereg"); ?></option>
                  <option <?php if($form_label_alignment=='left') echo 'selected="selected"';?> value="left"><?php _e("Left","piereg"); ?></option>
                <?php unset($form_label_alignment); ?>
                </select>
              </div>
              
                <div class="advance_fields">
					<label id="set_user_role_"><?php echo __("Rgistering User Role","piereg"); ?></label>
					<select id="set_user_role_" name="set_user_role_" >
				  		<?php
						global $wp_roles;
						//$role = get_option("wp_user_roles");
						$role = $wp_roles->roles;
						$wp_default_user_role = get_option("default_role");
						//Deprecated Science 2.0.10
						$piereg_user_role = (isset($button['pie_regis_set_user_role_']) && !empty($button['pie_regis_set_user_role_']))?$button['pie_regis_set_user_role_']:$wp_default_user_role;
						foreach($role as $key=>$value)
						{
							echo '<option value="'.$key.'"';
							echo ($wp_default_user_role == $key) ? ' selected="selected" ' : '';
							echo '>'.$value['name'].'</option>';
						}
						?>
				  	</select>
				</div>
              
            </div>
          </div>
        </li>
      </ul>
      <!--Form Settings End-->
      <fieldset>
        <legend align="center"><?php echo _e("Drag Fields Here","piereg"); ?></legend>
        
        <div id="hint_1" class="fields_hint" style="left: 95%;top: 50%; z-index:2;"> <img src="<?php echo plugins_url('pie-register'); ?>/images/left_arrow.jpg" width="45" height="26" align="left">
          <div class="hint_content">
            <h4><?php _e("Did You Know ?","piereg"); ?></h4>
            <span><?php _e("You can sort fields vertically","piereg"); ?></span> <br>
            <input type="button" class="thanks" value="<?php _e("Yes Thanks !","piereg"); ?>">
          </div>
        </div>
        
      
        
        <!--Form Fields-->
        <ul id="elements">
          <?php   
		 
		if(sizeof($data) >  0) 
		{
			$no = max(array_keys($data));			
			$field_values = array();
			$meta   = $this->getDefaultMeta();
			
			foreach($data as $field)
			{				
			
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
		
		
		 //We can't edit default wordpress fields
		  //if($field['type']!="default")
          //{
          		echo '<a href="javascript:;" class="edit_btn" title="'.__("Edit Field","piereg").'"></a>';
          //} 
		  ?>
            <!--Adding Label-->
            <div class="label_position"  id="field_label_<?php echo $field['id']?>">
              <label><?php echo (empty($field['label']) ? $field['type']:trim($field['label']))?></label>
            </div>
            <?php
			/*
				*	Modify Username field Since 2.0.15
			*/
           //We can't remove password and email fields
		    //if(!isset($field['remove']))
		    if(!isset($field['remove']) || ($field['type'] == "username") )
				  	echo '<a href="javascript:;" rel="'.$field['id'].'" class="delete_btn">X</a>';                
			  	else
			  		echo '<input  name="field['.$field['id'].'][remove]" value="0" type="hidden" /> '; 
            ?>
            <input type="hidden" name="field[<?php echo $field['id']?>][id]" value="<?php echo $field['id']?>" id="id_<?php echo $field['id']?>">
            <input type="hidden" name="field[<?php echo $field['id']?>][type]" id="type_<?php echo $field['id']?>" value="<?php echo $field['type']?>" >
            <div class="fields_position" id="field_position_<?php echo $field['id']?>">
              <?php
					
										
					
					switch($field['type']) :
					
					case 'text' :
					case 'username' :
					case 'website' :
					case 'hidden' :
					case 'phone':
					$this->addTextField($field,$field['id']);
					
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
			if(isset($meta[$field['type']]))
			echo str_replace("%d%",$field['id'],$meta[$field['type']]);
		 
		  		
		  ?> </li>
          <?php 	
				
			}	
		}
		?>
		</ul>
        <script type="text/javascript"><?php
		foreach($field_values as $key=>$value)
		{
			$temp = unserialize($value);
			if($temp['type'] == "html")
			{
				if(trim($temp['html']) != "")
				{
					$temp['html'] = html_entity_decode($temp['html']);
					$value = serialize($temp);
				}
			}
			//Since 2.0.10
			$temp = unserialize($value);
		    if(isset($temp['desc']) && !empty($temp['desc']))
			{
			  	$temp['desc'] = html_entity_decode($temp['desc']);
			  	$value = serialize($temp);
			}
			
		?>
       		fillValues('<?php echo $value?>',<?php echo $key?>);
        <?php 
		}
		?>no = "<?php echo ($no + 1)?>";</script>
          <?php
	?>
        
      </fieldset>
      <ul id="submit_ul">
        <li class="fields">
          <div class="fields_options submit_field"> <a href="javascript:;" class="edit_btn" title="<?php _e("Edit Button","piereg"); ?>"></a>
            <input id="reset_btn" disabled="disabled" name="fields[reset]" type="reset" class="submit_btn" value="<?php echo $data['submit']['reset_text']?>" />
            
            <input disabled="disabled" name="fields[submit]" type="submit" class="submit_btn" value="<?php echo $data['submit']['text']?>" />
            <input  name="field[submit][label]" value="Submit"  type="hidden" />
            <input  name="field[submit][type]" value="submit" type="hidden" />
            <input  name="field[submit][remove]" value="0" type="hidden" />
            <input  name="field[submit][meta]" value="0" type="hidden">
          </div>
          <div class="fields_main">
            <div class="advance_options_fields advance_options_submit">
              <div class="advance_fields">
                <label><?php _e("Submit Button Text","piereg"); ?></label>
                <input type="text" class="input_fields" name="field[submit][text]" value="<?php echo $data['submit']['text']?>">
              </div>
              
              <div class="advance_fields">
                <label><?php _e("Show Reset Button","piereg"); ?></label>
                <select onchange="showHideReset();" id="show_reset" class="swap_reset" name="field[submit][reset]">
                  <option <?php if($data['submit']['reset']=='0') echo 'selected="selected"';?> value="0"><?php _e("No","piereg"); ?></option>
                  <option <?php if($data['submit']['reset']=='1') echo 'selected="selected"';?> value="1"><?php _e("Yes","piereg"); ?></option>
                </select>
              </div>
              
              
               <div class="advance_fields">
                <label><?php _e("Reset Button Text","piereg"); ?></label>
                <input type="text" class="input_fields" name="field[submit][reset_text]" value="<?php echo $data['submit']['reset_text']?>">
              </div>
              <div class="advance_fields">
                <label><?php _e("Confirmation Message","piereg"); ?></label>
                <div class="radio_fields">
                  <input class="reg_success" type="radio" value="text" name="field[submit][confirmation]" <?php if($data['submit']['confirmation']=='text') echo 'checked="checked"';?>>
                  <label><?php _e("Text","piereg"); ?></label>
                  <input class="reg_success" type="radio" value="page" name="field[submit][confirmation]" <?php if($data['submit']['confirmation']=='page') echo 'checked="checked"';?>>
                  <label><?php _e("Page","piereg"); ?></label>
                  <input class="reg_success" type="radio" value="redirect" name="field[submit][confirmation]" <?php if($data['submit']['confirmation']=='redirect') echo 'checked="checked"';?>>
                  <label><?php _e("Redirect","piereg"); ?></label>
                </div>
              </div>
              <div class="advance_fields submit_meta submit_meta_redirect">
                <label><?php _e("Redirect URL","piereg"); ?></label>
                <input type="text" class="input_fields" name="field[submit][redirect_url]" value="<?php echo $data['submit']['redirect_url']?>">
              </div>
              <div class="advance_fields submit_meta submit_meta_page">
                <label><?php _e("Select Page","piereg"); ?></label>
                <?php  $args =  array("name"=>"field[submit][page]","selected"=>$data['submit']['page']);         wp_dropdown_pages( $args ); ?>
              </div>
              <div class="advance_fields submit_meta submit_meta_text">
                <label><?php _e("Registration Success Message","piereg"); ?></label>
                <textarea name="field[submit][message]" rows="8" cols="16"><?php echo $data['submit']['message'];?>
</textarea>
              </div>
            </div>
          </div>
        </li>
      </ul>
      <?php
	  	if($this->check_enable_payment_method() == "true")
		{
			?>
			  <script type="text/javascript">jQuery("#submit_ul").hide();</script>
			  <ul id="paypal_button">
				<li class="fields">
                	<?php do_action("show_icon_payment_gateway"); ?>
				</li>
			  </ul>
			<?php
		}
		/*if(!(empty($button['paypal_butt_id'])) && $button['enable_paypal']==1)
	 	{
		?>
		  <script type="text/javascript">jQuery("#submit_ul").hide();</script>
          <ul id="paypal_button">
            <li class="fields">
              <div class="fields_options submit_field">
                <img style="width:100%;" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" />
              </div>
              <!--<input  name="field[submit][type]" value="paypal" type="hidden" />--> 
            </li>
          </ul>
      	<?php 
		}*/
		
	  ?> 
      <input type="submit" style="float: right;margin-right:85px;" class="button button-primary button-large" name="pie_form"  value="<?php _e("Save Changes",'piereg');?>">
    </form>
  </div>
  <div class="right_menu">
   
         <div id="hint_0" style="top: 135px;margin-left: -271px;position: fixed;float:right;" class="fields_hint"> <img src="<?php echo plugins_url('pie-register'); ?>/images/right_arrow.jpg" width="45" height="26" align="right">
          <div class="hint_content">
            <h4><?php _e("Did You Know ?","piereg"); ?></h4>
            <span><?php _e("You can Drag n Drop fields.","piereg"); ?></span> <br>
            <input type="button" class="thanks" value="<?php _e("Yes Thanks !","piereg"); ?>">
          </div>
        </div>
        
    <ul>
      <li id="default_fields"><a class="right_menu_heading" href="javascript:;"><?php _e("Default Fields","piereg"); ?></a>
        <ul class="controls picker pie-content-ul"  id="content_1">
	      <li class="standard_name"><a name="username" class="default" href="javascript:;"><?php _e("Username","piereg"); ?></a></li>
          <li class="standard_name"><a name="name" class="default" href="javascript:;"><?php _e("Name","piereg"); ?></a></li>
          <li class="standard_website"><a name="url" class="default" href="javascript:;"><?php _e("Website","piereg"); ?></a></li>
          <li class="standard_aim"><a name="aim" class="default" href="javascript:;"><?php _e("AIM","piereg"); ?></a></li>
          <li class="standard_yahoo"><a name="yim" class="default" href="javascript:;"><?php _e("Yahoo IM","piereg"); ?></a></li>
          <li class="standard_google"><a name="jabber" class="default" href="javascript:;"><?php _e("Jabber / Google Talk","piereg"); ?></a></li>
          <li class="standard_about"><a name="description" class="default" href="javascript:;"><?php _e("About Yourself","piereg"); ?></a></li>
        </ul>
      </li>
      <li id="standard_fields"><a class="right_menu_heading" href="javascript:;"><?php _e("Standard Fields","piereg"); ?></a>
            <ul class="controls picker pie-content-ul"  id="content_2">
              <li class="standard_text"><a name="text" href="javascript:;"><?php _e("Text Field","piereg"); ?></a></li>
              <li class="standard_textarea"><a name="textarea" href="javascript:;"><?php _e("Text Area","piereg"); ?></a></li>
              <li class="standard_dropdown"><a name="dropdown" href="javascript:;"><?php _e("Drop Down","piereg"); ?></a></li>
              <li class="standard_multiselect"><a name="multiselect" href="javascript:;"><?php _e("Multi Select","piereg"); ?></a></li>
              <li class="standard_numbers"><a name="number" href="javascript:;"><?php _e("Numbers","piereg"); ?></a></li>
              <li class="standard_checkbox"><a name="checkbox" href="javascript:;"><?php _e("Checkbox","piereg"); ?></a></li>
              <li class="standard_radio"><a name="radio" href="javascript:;"><?php _e("Radio Buttons","piereg"); ?></a></li>
              <li class="standard_hidden"><a name="hidden" href="javascript:;"><?php _e("Hidden","piereg"); ?></a></li>
              <li class="standard_html"><a name="html" href="javascript:;"><?php _e("HTML","piereg"); ?></a></li>
              <li class="standard_selection"><a name="sectionbreak" href="javascript:;"><?php _e("Section Break","piereg"); ?></a></li>
              <li class="standard_pagebreak"><a name="pagebreak" href="javascript:;"><?php _e("Page Break","piereg"); ?></a></li>
            </ul>
      </li>
      <li id="advanced_fields"><a class="right_menu_heading" href="javascript:;"><?php _e("Advanced Fields","piereg"); ?></a>
        <ul class="controls picker pie-content-ul"  id="content_3">
          <?php /*?><li class="standard_time"><a name="time" href="javascript:;"><?php _e("Time","piereg"); ?></a></li><?php */?>
          <li class="standard_address"><a name="address" href="javascript:;"><?php _e("Address","piereg"); ?></a></li>
          <li class="standard_captcha_n"><a name="captcha" class="default" href="javascript:;"><?php _e("Re-Captcha","piereg"); ?></a></li>
          <li class="standard_captcha"><a name="math_captcha" class="default" href="javascript:;"><?php _e("Math Captcha","piereg"); ?></a></li>
          <li class="standard_date"><a name="date" href="javascript:;"><?php _e("Date","piereg"); ?></a></li>
          <li class="standard_time"><a name="time" href="javascript:;"><?php _e("Time","piereg"); ?></a></li>
          <li class="standard_phone"><a name="phone" href="javascript:;"><?php _e("Phone","piereg"); ?></a></li>
          <li class="standard_upload"><a name="upload" href="javascript:;"><?php _e("Upload","piereg"); ?></a></li>
          <li class="standard_profile"><a name="profile_pic"  href="javascript:;"><?php _e("Profile Picture","piereg"); ?></a></li>
          <li class="standard_list"><a name="list" href="javascript:;"><?php _e("List","piereg"); ?></a></li>
          <?php if($button['enable_invitation_codes']==1) { ?> 
          <li class="standard_invitation"><a name="invitation" class="default" href="javascript:;"><?php _e("Invitation Code","piereg"); ?></a></li>
          <?php } ?>
        </ul>
      </li>
    </ul>
  </div>
</div>
</div>