<?php
if( file_exists( (PIEREG_DIR_NAME)."/classes/registration_form.php" ) ){
	require_once( (PIEREG_DIR_NAME)."/classes/registration_form.php" );
}

class Registration_form_template extends Registration_form
{
	var $is_pr_widget = false;	
	var $pageBreak_prev_label 	= '';
	var $pageBreak_prev_type 	= '';
	
	function addDesc()
	{
		if(!empty($this->field['desc']))
		{
			return '<p class="desc">'.html_entity_decode($this->field['desc']).'</p>';
		}
	}
	function addLabel($isblank="")
	{
		if($this->field['type'] == "html" && $this->field['label'] == ""){
			return "";
		}
		if($this->field['type']=="name" && $this->field['name_format']=="normal")
		{
			return "";
		}
		$field_required = "";
		if( isset($this->field['required']) && $this->field['required'] != "" )
			$field_required .= '&nbsp;<span class="piereg_field_required_label">*</span>';
		
		$topclass = "";
		if($this->label_alignment=="top")
			$topclass = "label_top";
	
		$labelled = "";
		if(isset($this->field['label'])) {
			$labelled = __(html_entity_decode(stripslashes($this->field['label'])),"piereg").$field_required;
		}
		
		if($isblank == 'empty'){
			$labelled = "&nbsp;";
		}
		
		return '<label class="'.$topclass .'" for="'.$this->name.'">'.$labelled.'</label>';
	}
	function addFormData($title="true",$description="true")
	{
		$data = "";
		$data .= '<div class="fieldset '.$this->data['form']['css'].'">';
		if($title == "true"){
			$data .= '<h2 id="piereg_pie_form_heading">'.$this->data['form']['label'].'</h2>';	
		}
		if($description == "true"){
			$data .= '<p id="piereg_pie_form_desc" >'.nl2br(html_entity_decode(stripslashes($this->data['form']['desc']))).'</p>';
		}
		$data .= '</div>';		
		$data  = apply_filters('piereg_edit_above_form_data',$data); # newlyAddedHookFilter
		
		return $data;
	
	}
	function addDefaultField()
	{
		
		$data = "";
		$this->name = $this->field['field_name'];
		
		if(isset($this->field['placeholder'])):
			$this->field['placeholder'];
		else:
			$this->field['placeholder'] = "";
		endif;	
		
		$data .= '<div class="fieldset">'.$this->addLabel();
		if($this->made_conditional_logic()){
			$cl_class="hasConditionalLogic";
			$cl_data=' data-triggerid="'.$this->get_pr_widget_prefix().'piereg_field_'.$this->field['selected_field'].'"';
			$cl_data.=' data-content="'.$this->field['conditional_value'].'"';
			$cl_data.=' data-operator="'.$this->field['field_rule_operator'].'"';
			$cl_data.=' data-display="'.$this->field['field_status'].'"';
		}else{
			$cl_class='';
			$cl_data='';
		}
		if($this->field['field_name']=="description")
		{
			$data .= '<textarea name="description" id="description" rows="5" data-field_id="'.$this->get_pr_widget_prefix().'piereg_field_'.$this->no.'" cols="80" class="'.$cl_class.'"'.$cl_data.'>'.$this->getDefaultValue().'</textarea>';	
		}
		else
		{
			$data .= '<input id="'.$this->id.'" name="'.$this->field['field_name'].'" data-field_id="'.$this->get_pr_widget_prefix().'piereg_field_'.$this->no.'" class="'.$this->addClass().'"  placeholder="'.$this->field['placeholder'].'" type="text" value="'.$this->getDefaultValue().'" />';	
		}
		$data .= $this->addDesc();
		$data .= '</div>';
		return $data;
	}
	function addTextField(){
		if($this->made_conditional_logic()){
			$cl_class="hasConditionalLogic";
			$cl_data=' data-triggerid="'.$this->get_pr_widget_prefix().'piereg_field_'.$this->field['selected_field'].'"';
			$cl_data.=' data-content="'.$this->field['conditional_value'].'"';
			$cl_data.=' data-operator="'.$this->field['field_rule_operator'].'"';
			$cl_data.=' data-display="'.$this->field['field_status'].'"';
		}else{
			$cl_class='';
			$cl_data='';
		}
		$data  = '<div class="fieldset">';
		$data .= $this->addLabel();
		$data .= '<input id="'.$this->get_pr_widget_prefix().$this->id.'" name="'.$this->name.'" data-field_id="'.$this->get_pr_widget_prefix().'piereg_field_'.$this->no.'" class="'.$this->addClass().' '.$cl_class.'"'.$cl_data.'  '.$this->addValidation().'  placeholder="'.$this->field['placeholder'].'" type="text" value="'.$this->getDefaultValue().'" />';
		$data .= $this->addDesc();
		$data .= '</div>';
		return $data;
	}
	function addHoneypot(){
		$data  = '<div class="fieldset">';
		$data .= $this->addLabel();
		$data .= '<input id="input_field_'.$this->no.'" name="input_field_'.$this->no.'" class="'.$this->addClass("input_fields piereg_input_field_required").'"  type="text" required="required" >';
		$data .= '</div>';
		return $data;
	}
	function addHiddenField()
	{
		$data  = '<div class="fieldset">';
		$data .= '<input id="'.$this->id.'" name="'.$this->name.'" data-field_id="'.$this->get_pr_widget_prefix().'piereg_field_'.$this->no.'" type="hidden" value="'.$this->getDefaultValue().'" />';
		$data .= $this->addDesc();
		$data .= '</div>';
		return $data;
	}
	function addUsername($form_widget = false){
		if($this->made_conditional_logic()){
			$cl_class="hasConditionalLogic";
			$cl_data=' data-triggerid="'.$this->get_pr_widget_prefix().'piereg_field_'.$this->field['selected_field'].'"';
			$cl_data.=' data-content="'.$this->field['conditional_value'].'"';
			$cl_data.=' data-operator="'.$this->field['field_rule_operator'].'"';
			$cl_data.=' data-display="'.$this->field['field_status'].'"';
		}else{
			$cl_class='';
			$cl_data='';
		}
		$formwidget = (isset($form_widget) && $form_widget == true)? '_widget' : '';
		$data  = '<div class="fieldset">';
		$data .= $this->addLabel();
		$data .= '<input id="username'.$formwidget.'" name="username" data-field_id="'.$this->get_pr_widget_prefix().'piereg_field_'.$this->no.'" class="input_fields '.$this->field['css'].' piereg_validate[required,username] piereg_username_input_field '.$cl_class.'"'.$cl_data.' placeholder="'.$this->field['placeholder'].'" type="text" value="'.$this->getDefaultValue('username').'" data-errormessage-value-missing="'.$this->field['validation_message'].'"  />';
		$data .= $this->addDesc();
		$data .= '</div>';	
		return $data;
	}
	function addPassword($fromwidget,$field_status = "")
	{
		$style = "";
		$data = "";
		if($fromwidget == true)
		{
			$this->id = $this->id."_widget";
		}
		$data .= '<div class="fieldset">'.$this->addLabel();
		if($this->label_alignment=="left")
			$style = 'class = "wdth-lft mrgn-lft"';		
		
		$data .= '<input ';		
		$data .= 'id="'.$this->id.'" name="password" data-field_id="'.$this->get_pr_widget_prefix().'piereg_field_'.$this->no.'" class="'.$this->addClass("input_fields",array("minSize[8]")).' prPass1" placeholder="'.$this->field['placeholder'].'" type="password" data-errormessage-value-missing="'.$this->field['validation_message'].'" data-errormessage-range-underflow="'.$this->field['validation_message'].'" data-errormessage-range-overflow="'.$this->field['validation_message'].'" autocomplete="off" />';
				
		$field_required = "";
		if( isset($this->field['required']) && $this->field['required'] != "" )
			$field_required .= '&nbsp;<span class="piereg_field_required_label">*</span>';
		
			$class = '';
			$fclass = '';
			
			$topclass = "";
			if($this->label_alignment=="top")
				$topclass = "label_top"; 
			$label2 = (isset($this->field['label2']) and !empty($this->field['label2']))? $this->field['label2'] : __("Confirm Password","piereg");
			$data .= '</div>';
			$data .= '</li>';
			$data .= '<li class="fields pageFields_'.$this->pages.' '.$topclass.' '.$this->get_pr_widget_prefix().'piereg_li_'.$this->field['id'].'" '.$field_status.'>';
			$data .= '<div class="fieldset">';
			$data .= '<label>'.$label2.$field_required.'</label>';
			$data .= '<input id="confirm_password_'.$this->id.'" type="password" data-errormessage-value-missing="'.$this->field['validation_message'].'" data-errormessage-range-underflow="'.$this->field['validation_message'].'" data-errormessage-range-overflow="'.$this->field['validation_message'].'" class="'.$this->field['css'].' input_fields piereg_validate[required,equals['.$this->id.']]  prPass2" placeholder="'.$this->field['placeholder'].'" autocomplete="off" />';	
			$data .= $this->addDesc();
			$data .= '</div>';
			
			return $data;
	}	
	function addEmail($fromwidget)
	{
		if($this->made_conditional_logic()){
			$cl_class="hasConditionalLogic";
			$cl_data=' data-triggerid="'.$this->get_pr_widget_prefix().'piereg_field_'.$this->field['selected_field'].'"';
			$cl_data.=' data-content="'.$this->field['conditional_value'].'"';
			$cl_data.=' data-operator="'.$this->field['field_rule_operator'].'"';
			$cl_data.=' data-display="'.$this->field['field_status'].'"';
		}else{
			$cl_class='';
			$cl_data='';
		}
		$data = "";
		if($fromwidget == true)
		{
			$this->id = $this->id."_widget";
		}
		$data .= '<div class="fieldset">'.$this->addLabel();
		$data .='<input id="'.$this->id.'" name="e_mail" data-field_id="'.$this->get_pr_widget_prefix().'piereg_field_'.$this->no.'" class="'.$this->addClass().' '.$cl_class.'"'.$cl_data.'  '.$this->addValidation().' placeholder="'.$this->field['placeholder'].'" type="text" value="'.$this->getDefaultValue("e_mail").'" />';
		
		if(isset($this->field['confirm_email']))
		{
			$class = '';
			$fclass = '';
			
			$topclass = "";
			if($this->label_alignment=="top")
				$topclass = "label_top"; 	
			
			$field_required = "";
			if( isset($this->field['required']) && $this->field['required'] != "" )
				$field_required .= '&nbsp;<span class="piereg_field_required_label">*</span>';
			
			$data .= '</div>';
			$label2 = (isset($this->field['label2']) and !empty($this->field['label2']))? $this->field['label2'] : __("Confirm E-mail","piereg");
			$data .= '<div class="fieldset">';
			$data .= '<label>'.$label2.$field_required.'</label>';
			$data .= '<input  placeholder="'.$this->field['placeholder'].'" id="confirm_email_'.$this->id.'" '.$this->addValidation().' type="text" class="input_fields piereg_validate[required,equals['.$this->id.']]" autocomplete="off">';
		}
		$data .= $this->addDesc();
		$data .= '</div>';
		return $data;
	}
	function addUpload()
	{
		$data  = '<div class="fieldset">';
		$data .= $this->addLabel();
		$data .= '<input id="'.$this->id.'" name="'.$this->name.'" data-field_id="'.$this->get_pr_widget_prefix().'piereg_field_'.$this->no.'" class="'.$this->addClass().'"  '.$this->addValidation().' type="file"  />';
		if( isset( $this->field['file_types'] ) && !empty($this->field['file_types'])  )
		{
			$data .= '<p class="desc style_filetypes">Allowed File Types: '.$this->field['file_types'].'</p>';	
		}
		$data .= $this->addDesc();
		$data .= '</div>';
		return $data;
	}
	function addProfilePicUpload()
	{
		$data  = '<div class="fieldset">';
		$data .= $this->addLabel();
		$data .= '<input id="'.$this->id.'" data-field_id="'.$this->get_pr_widget_prefix().'piereg_field_'.$this->no.'" name="'.$this->name.'" class="'.$this->addClass().' piereg_validate[funcCall[checkExtensions],ext[gif|GIF|jpeg|JPEG|jpg|JPG|png|PNG|bmp|BMP]]"  '.$this->addValidation().' type="file"  />';
		$data .= $this->addDesc();
		$data .= '</div>';
		return $data;
	}
	function addTextArea()
	{
		$data  = '<div class="fieldset">';
		$data .= $this->addLabel();
		if($this->made_conditional_logic()){
			$cl_class="hasConditionalLogic";
			$cl_data=' data-triggerid="'.$this->get_pr_widget_prefix().'piereg_field_'.$this->field['selected_field'].'"';
			$cl_data.=' data-content="'.$this->field['conditional_value'].'"';
			$cl_data.=' data-operator="'.$this->field['field_rule_operator'].'"';
			$cl_data.=' data-display="'.$this->field['field_status'].'"';
		}else{
			$cl_class='';
			$cl_data='';
		}
		$data .= '<textarea id="'.$this->id.'" data-field_id="'.$this->get_pr_widget_prefix().'piereg_field_'.$this->no.'" name="'.$this->name.'" rows="'.$this->field['rows'].'" cols="'.$this->field['cols'].'" class="'.$this->addClass().' '.$cl_class.'" '.$this->addValidation().' placeholder="'.$this->field['placeholder'].'"'.$cl_data.'>';
		$data .= $this->getDefaultValue();
		$data .= '</textarea>';
		$data .= $this->addDesc();
		$data .= '</div>';
		return $data;
	}
	function addName()
	{
		if($this->made_conditional_logic()){
			$cl_class="hasConditionalLogic";
			$cl_data=' data-triggerid="'.$this->get_pr_widget_prefix().'piereg_field_'.$this->field['selected_field'].'"';
			$cl_data.=' data-content="'.$this->field['conditional_value'].'"';
			$cl_data.=' data-operator="'.$this->field['field_rule_operator'].'"';
			$cl_data.=' data-display="'.$this->field['field_status'].'"';
		}else{
			$cl_class='';
			$cl_data='';
		}
		$field_required="";
		if( isset($this->field['required']) && $this->field['required'] != "" ){
			$field_required .= '&nbsp;<span class="piereg_field_required_label">*</span>';
		}
		
		$data  = '<div class="fieldset">';
		$data .= '<label>'.__($this->field['label'],"piereg") . $field_required . '</label>';
		$data .= '<input value="'.$this->getDefaultValue('first_name').'" data-field_id="'.$this->get_pr_widget_prefix().'piereg_field_'.$this->no.'" id="'.$this->id.'_firstname" name="first_name" class="'.$this->addClass().' input_fields piereg_name_input_field '.$cl_class.'"'.$cl_data.' '.$this->addValidation().'  type="text"  />';				
		
		$topclass = "";
		if($this->label_alignment=="top")
			$topclass = "label_top";
		$data .= '</div>';
		$label2 = (isset($this->field['label2']) and !empty($this->field['label2']))? $this->field['label2'] : __("Last Name","piereg");
		$data .= '<div class="fieldset">';
		$data .= '<label>'.$label2 . $field_required .'</label>';
		$data .= '<input value="'.$this->getDefaultValue('last_name').'" id="'.$this->id.'_lastname" name="last_name" class="'.$this->addClass().' input_fields piereg_name_input_field" '.$this->addValidation().'  type="text"  />';	
		$data .= $this->addDesc();
		$data .= '</div>';
		return $data;
		
	}
	function addTime()
	{
		$data = "";
		$this->field['hours'] = TRUE;
		$name = $this->name;
		
		$time_this_values = $this->getDefaultValue($name);
		$data .= '<div class="fieldset">'.$this->addLabel();
		$data .= '<div class="piereg_time">';
		$data .= '<div class="time_fields">';
		$data .= '<input value="'.( (isset($time_this_values["hh"])) ? $time_this_values["hh"] : "" ).'" maxlength="2" id="hh_'.$this->id.'" name="'.$this->name.'[hh]" type="text"  class="'.$this->addClass().'"  '.$this->addValidation().'>';
		$data .= '<label>'.__("HH","piereg").'</label>';
		$data .= '</div>';
		$this->field['hours'] = FALSE;
		
		$this->field['mins'] = TRUE;
		$data .= '<span class="colon">:</span>';
		$data .= '<div class="time_fields">';
		$data .= '<input value="'.( (isset($time_this_values["mm"])) ? $time_this_values["mm"] : "" ).'" maxlength="2" id="mm_'.$this->id.'" type="text" name="'.$this->name.'[mm]"  class="'.$this->addClass().'"  '.$this->addValidation().'>';
		$data .= '<label>'.__("MM","piereg").'</label>';
		$data .= '</div>';
		$data .= '<div id="time_format_field_'.$this->id.'" class="time_fields"></div>';
		$this->field['mins'] = FALSE;
		
		if($this->field['time_type']=="12")
		{
			$time_format_val = ( (isset($time_this_values["time_format"])) ? $time_this_values["time_format"] : "" );
			$data .= '<div class="time_fields">';
			$data .= '<select name="'.$this->name.'[time_format]" >';
				$data .= '<option value="am" '; 
						$data .=($time_format_val == "am")?'selected=""':'';
						$data .='>AM</option>';
				$data .='<option value="pm"  ';
						$data .=($time_format_val == "pm")?'selected=""':'';
						$data .='>PM</option>';
			$data .= '</select>';
			$data .= '</div>';
		}
		
		$data .= '</div>';
		$data .= $this->addDesc();
		$data .= '</div>';
		return $data;
	}	
	function addDropdown()
	{ 
		if($this->made_conditional_logic()){
			$cl_class="hasConditionalLogic";
			$cl_data=' data-triggerid="'.$this->get_pr_widget_prefix().'piereg_field_'.$this->field['selected_field'].'"';
			$cl_data.=' data-content="'.$this->field['conditional_value'].'"';
			$cl_data.=' data-operator="'.$this->field['field_rule_operator'].'"';
			$cl_data.=' data-display="'.$this->field['field_status'].'"';
		}else{
			$cl_class='';
			$cl_data='';
		}
		$data = "";
		$multiple = "";
		$name = $this->name;
		$field_id = $this->name;
		$thispostedvalue = $this->getDefaultValue();
		
		$data .= '<div class="fieldset" >'.$this->addLabel();
		if($this->field['type']=="multiselect")
		{
			$multiple 	= 'multiple';			
			$name = $this->name."[]";
		}
		$data .= '<select '.$multiple.' id="'.$field_id.'" name="'.$name.'" data-field_id="'.$this->get_pr_widget_prefix().'piereg_field_'.$this->no.'" class="'.$this->addClass("").' '.$cl_class.'"'.$cl_data.' '.$this->addValidation().'  >';
	
		if($this->field['list_type']=="country")
		{
			 $countries = get_option("pie_countries");			 
			$data .= $this->createDropdown($countries);			   	
		}
		else if($this->field['list_type']=="us_states")
		{
			 $us_states = get_option("pie_us_states");
			 $options 	= $this->createDropdown($us_states);				 
			 $data .= $options;						   	
		}
		else if($this->field['list_type'] == "can_states")
		{
			$can_states = get_option("pie_can_states");			
			$data .= $options 	= $this->createDropdown($can_states);					
		}
		else if(sizeof($this->field['value']) > 0)
			{	
				for($a = 0 ; $a < sizeof($this->field['value']) ; $a++)
				{
					$selected = '';
					if(isset($this->field['selected']) && is_array($this->field['selected']) && in_array($a,$this->field['selected']))
					{
						$selected = 'selected="selected"';	
					}
					if(is_array($thispostedvalue)){
						foreach($thispostedvalue as $thissinglepostedval){
							if(!empty($this->field['value'][$a]) && $thissinglepostedval == $this->field['value'][$a]){
								$selected = 'selected="selected"';
							}
						}
					}
					elseif(!empty($this->field['value'][$a]) && $thispostedvalue == $this->field['value'][$a]){
						$selected = 'selected="selected"';
					}

					//if($this->field['value'][$a] !="" || $this->field['display'][$a] != "")
						$data .= '<option '.$selected.' value="'.$this->field['value'][$a].'">'.$this->field['display'][$a].'</option>';	
				}		
			}
		$data .= '</select>';
		
		$data .= $this->addDesc();
		$data .= '</div>';
		
		return $data;
	}
	function addPricing()
	{
		$data = "";
		
		if($this->check_enable_payment_method() == "true"  && isset($this->field['allow_payment_gateways']) && !empty($this->field['allow_payment_gateways']) )
		{
			$data .= '<div class="fieldset">';
			$data .= $this->addLabel('empty');
			$data .= '<img src="'.plugins_url('pie-register/images/paypal_std_btn.png').'">';
			$data .= '<p class="desc">'.__('Paypal (Standard) payment method applied.','piereg').'</p>';
			$data .= '<input type="hidden" name="select_payment_method" value="'.$this->field['allow_payment_gateways'][0].'" />';
			$data .= $this->addDesc();
			$data .= '</div>';
			return $data;
		}else{
			$data = '<div class="fieldset">';
			$data .= $this->addLabel();
			$data .= '<p>'.__("No payment methods selected by administrator.","piereg").'</p>';
			$data .= $this->addDesc();
			$data .= '</div>';
			return $data;
		}
	}
	function addNumberField()
	{
		if($this->made_conditional_logic()){
			$cl_class="hasConditionalLogic";
			$cl_data=' data-triggerid="'.$this->get_pr_widget_prefix().'piereg_field_'.$this->field['selected_field'].'"';
			$cl_data.=' data-content="'.$this->field['conditional_value'].'"';
			$cl_data.=' data-operator="'.$this->field['field_rule_operator'].'"';
			$cl_data.=' data-display="'.$this->field['field_status'].'"';
		}else{
			$cl_class='';
			$cl_data='';
		}
		$data = "";
		$data .= '<div class="fieldset">';
		$data .= $this->addLabel();
		$data .= '<input id="'.$this->id.'" name="'.$this->name.'" data-field_id="'.$this->get_pr_widget_prefix().'piereg_field_'.$this->no.'" class="'.$this->addClass().' '.$cl_class.'"'.$cl_data.'  '.$this->addValidation().'  placeholder="'.$this->field['placeholder'].'" type="number" value="'.$this->getDefaultValue().'"' ;
		
		if( $this->field['min'] !== "" )
			$data .= 'min="'.$this->field['min'].'"';
		
		if(!empty($this->field['max']))
			$data .= 'max="'.$this->field['max'].'"';
		
		$data .= '/>';
		$data .= $this->addDesc();
		$data .= '</div>';	
		return $data;
	}
	function addPhone()
	{
		if($this->made_conditional_logic()){
			$cl_class="hasConditionalLogic";
			$cl_data=' data-triggerid="'.$this->get_pr_widget_prefix().'piereg_field_'.$this->field['selected_field'].'"';
			$cl_data.=' data-content="'.$this->field['conditional_value'].'"';
			$cl_data.=' data-operator="'.$this->field['field_rule_operator'].'"';
			$cl_data.=' data-display="'.$this->field['field_status'].'"';
		}else{
			$cl_class='';
			$cl_data='';
		}
		$data  = '<div class="fieldset">';
		$data .= $this->addLabel();
		$data .= '<input id="'.$this->id.'" class="'.$this->addClass().' '.$cl_class.'"'.$cl_data.' data-field_id="'.$this->get_pr_widget_prefix().'piereg_field_'.$this->no.'"  '.$this->addValidation().' name="'.$this->name.'" type="text" value="'.$this->getDefaultValue().'" />';
		$data .= $this->addDesc();
		$data .= '</div>';
		return $data;
	}
	function addList()
	{
		$data = "";
		$width  = 90 /  intval($this->field['cols']);
		$name = $this->name;
		
		$list_this_values = $this->getDefaultValue($name);
		
		$data .= '<div class="fieldset">'.$this->addLabel();
		$data .= '<div class="'.$this->field['css'].' pie_list_cover">';
		
		for($a = 1 ,$c=0; $a <= $this->field['rows'] ; $a++,$c++)
		{
			if($a==1)
			{
				$data .= '<div class="'.$this->id.'_'.$a.' pie_list">';
				
				
				for($b = 1 ; $b <= $this->field['cols'] ;$b++)
				{
					$data .= '<input data-type="list" value="'.((isset($list_this_values[$c][$b-1]))?$list_this_values[$c][$b-1]:"").'" style="width:'.$width.'%;" type="text" '.$this->addValidation().' name="'.$this->name.'['.$c.'][]" class="'.$this->addClass().' input_fields"> ';
				}
				if( ((int)$this->field['rows']) > 1)
				{
					$data .= ' <img src="'.plugins_url('pie-register').'/images/plus.png" onclick="addList('.$this->field['rows'].','.$this->field['id'].');" alt="add" /></div>';		
				}
				
				if( $this->field['rows'] == 1 )	$data .= '</div>';		
			}
			else
			{
				if(isset($list_this_values[$c]) != false)
					$display_list_style = (!array_filter($list_this_values[$c]))? "display:none;" : "display:block;";
				else
					$display_list_style = "display:none;";
					
				$data .= '<div style="'.$display_list_style.'" class="'.$this->id.'_'.$a.' pie_list">';
				for($b = 1 ; $b <= $this->field['cols'] ;$b++)
				{
					$data .= '<input data-type="list" value="'.((isset($list_this_values[$c][$b-1]))?$list_this_values[$c][$b-1]:"").'" style="width:'.$width.'%;" type="text" '.$this->addValidation().' name="'.$this->name.'['.$c.'][]" class="'.$this->addClass().' input_fields">';
				}
					$data .= ' <img src="'.plugins_url('pie-register').'/images/minus.gif" onclick="removeList('.$this->field['rows'].','.$this->field['id'].','.$a.');" alt="add" />';
					$data .= '</div>';
			}
			
			
		}
		
		$data .= '</div>';		
		$data .= $this->addDesc();
		$data .= '</div>';
		return $data;
	}
	function addHTML()
	{
		$data  = '<div class="fieldset">';
		$data .= $this->addLabel();
		$data .= '<div class="piereg-html-field-content" >';
		$data .= html_entity_decode($this->field['html']);
		$data .= '</div>';
		$data .= $this->addDesc();
		$data .= '</div>';
		return $data;
	}
	function addSectionBreak()
	{
		$class = "";
		
		if($this->label_alignment == "left")
			$class .= "wdth-lft ";
		
		$class .= "sectionBreak";
		
		$data  = '<div class="fieldset aligncenter">';
		//$data .= $this->addLabel();
		$data .= '<div class="'.$class.'">';
		$data .= '</div>';
		$data .= $this->addDesc();
		$data .= '</div>';
		return $data;
	}
	function addCheckRadio()
	{
		$data = "";
		$data .= '<div class="fieldset">';
		$data .= $this->addLabel();
		if(sizeof($this->field['value']) > 0)
		{
			$data .= '<div class="radio_wrap">';
			$thispostedvalue = $this->getDefaultValue();
			for($a = 0 ; $a < sizeof($this->field['value']) ; $a++)
			{
				$checked = '';
				if( (isset($this->field['selected'])) && (is_array($this->field['selected']) && in_array($a,$this->field['selected'])) )
					$checked = 'checked="checked"';	
				else
					$checked = '';
				
				if(is_array($thispostedvalue)){
					foreach($thispostedvalue as $thissinglepostedval){
						if($thissinglepostedval == $this->field['value'][$a])
							$checked = 'checked="checked"';
					}
				}
				
				$dymanic_class = $this->field['type']."_".$this->field['id'];
				
				$data .= "<label>";
				$data .= '<input '.$checked.' value="'.$this->field['value'][$a].'" data-field_id="'.$this->get_pr_widget_prefix().'piereg_field_'.$this->no.'" type="'.$this->field['type'].'" '.((isset($multiple))?$multiple:"").' name="'.$this->name.'[]" class="'.$this->addClass("input_fields").' radio_fields" '.$this->addValidation().' data-map-field-by-class="'.$dymanic_class.'" >';
				$data .= $this->field['display'][$a];	
				$data .= "</label>";
			}
			$data .= "</div>";		
		}
		$data .= $this->addDesc();
		$data .= '</div>';
		return $data;
	}
	function addAddress()
	{
		$address_values = $this->getDefaultValue($this->name);
		$data = "";
		$data .= '<div class="fieldset">'.$this->addLabel();
		$data .= '<div class="address_main">';
		$data .= '<div class="address">';
		$data .= '<input type="text" name="'.$this->name.'[address]" id="'.$this->id.'" class="'.$this->addClass().'"  '.$this->addValidation().' value="'.((isset($address_values['address']))?$address_values['address']:"").'">';
		$data .= '<label>'.__("Street Address","piereg").'</label>';
		$data .= '</div>';
		
		 if(empty($this->field['hide_address2']))
		 {
			$data .= '<div class="address">';
			$data .= '<input type="text" name="'.$this->name.'[address2]" id="address2_'.$this->id.'"  class="'.$this->addClass().'"  '.$this->addValidation().' value="'.((isset($address_values['address2']))?$address_values['address2']:"").'">';
			$data .= '<label>'.__("Address Line 2","piereg").'</label>';
			$data .= '</div>';
		 }
		
		$data .= '<div class="address">';
		$data .= '<div class="address2">';
		$data .= '<input type="text" name="'.$this->name.'[city]" id="city_'.$this->id.'" class="'.$this->addClass().'"  '.$this->addValidation().' value="'.((isset($address_values['city']))?$address_values['city']:"").'">';
		$data .= '<label>'.__("City","piereg").'</label>';
		$data .= '</div>';
		
		 if(empty($this->field['hide_state']))
		 {
			 	if($this->field['address_type'] == "International")
				{
					$data .= '<div class="address2" >';
					$data .= '<input type="text" name="'.$this->name.'[state]" id="state_'.$this->id.'" class="'.$this->addClass().'" value="'.((isset($address_values['state']))?$address_values['state']:"").'">';
					$data .= '<label>'.__("State / Province / Region","piereg").'</label>';
					$data .= '</div>';
				}
				else if($this->field['address_type'] == "United States")
				{
				  $us_states = get_option("pie_us_states");
				  $selectedoption = (isset($address_values['state']))?$address_values['state']:$this->field['us_default_state'];
				  $options 	= $this->createDropdown($us_states,$selectedoption);	
				 
				  $data .= '<div class="address2" >';
					$data .= '<select id="state_'.$this->id.'" name="'.$this->name.'[state]" class="'.$this->addClass("").'">';
					$data .= $options;
					$data .= '</select>';
					$data .= '<label>'.__("State","piereg").'</label>';
				  $data .= '</div>';
				}
				else if($this->field['address_type'] == "Canada")
				{
					
					$can_states = get_option("pie_can_states");
					$selectedoption = (isset($address_values['state']))?$address_values['state']:$this->field['canada_default_state'];
				  	$options 	= $this->createDropdown($can_states,$selectedoption);
					$data .= '<div class="address2" >';
						$data .= '<select id="state_'.$this->id.'" class="'.$this->addClass("").'" name="'.$this->name.'[state]">';
						$data .= $options;
						$data .= '</select>';
						$data .= '<label>'.__("Province","piereg").'</label>';
					$data .= '</div>';
				}
		}
		$data .= '</div>';
		$data .= '<div class="address">';	
		$data .= ' <div class="address2">';
		$data .= '<input id="zip_'.$this->id.'" name="'.$this->name.'[zip]" type="text" class="'.$this->addClass().'"  '.$this->addValidation().' value="'.((isset($address_values['zip']))?$address_values['zip']:"").'">';
		$data .= '<label>'.__("Zip / Postal Code","piereg").'</label>';
		$data .= '</div>';	 
		
		
		 if($this->field['address_type'] == "International")
		 {
			 $countries = get_option("pie_countries");
			 $selectedoption = (isset($address_values['country']) && $address_values['country'])?$address_values['country']:$this->field['default_country'];		 
			 $options 	= $this->createDropdown($countries,$selectedoption);  
			 $data .= '<div  class="address2" >';
				$data .= '<select id="country_'.$this->id.'" name="'.$this->name.'[country]" class="'.$this->addClass("").'"   '.$this->addValidation().'>';
					$data .= '<option value="">'.__("Select Country","piereg").'</option>';
						$data .= $options;
					$data .= '</select>';
				$data .= '<label>'.__("Country","piereg").'</label>';
		  	$data .= '</div>';
		 }
		 
		 
		$data .= '</div>';
		$data .= '</div>';
		$data .= $this->addDesc();
		$data .= '</div>';
		return $data;
	}	
	function addDate()
	{
		$data = "";
		$data .= '<div class="fieldset">';
		$data .= $this->addLabel();
		$date_this_values = $this->getDefaultValue($this->name);
		
		if($this->field['date_type'] == "datefield")
		{
			
			if($this->field['date_format']=="mm/dd/yy")
			{
				$data .= '<div class="piereg_time date_format_field">';
                    $data .= '<div class="time_fields">';
                        $data .= '<input id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" maxlength="2" type="text" class="'.$this->addClass("input_fields",array("custom[month]")).'" '.$this->addValidation().' value="'.((isset($date_this_values['date']['mm']))?$date_this_values['date']['mm']:"").'">';
                        $data .= '<label>'.__("MM","piereg").'</label>';
                    $data .= '</div>';
                    $data .= '<div class="time_fields">';
                        $data .= '<input id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" maxlength="2"  type="text" class="'.$this->addClass("input_fields",array("custom[day]")).'" '.$this->addValidation().' value="'.((isset($date_this_values['date']['dd']))?$date_this_values['date']['dd']: "").'">';
                        $data .= '<label>'.__("DD","piereg").'</label>';
                    $data .= '</div>';
                    $data .= '<div class="time_fields">';
                        $data .= '<input id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" maxlength="4"  type="text" class="'.$this->addClass("input_fields",array("custom[year]")).'" '.$this->addValidation().' value="'.((isset($date_this_values['date']['yy']))?$date_this_values['date']['yy']:"").'">';
                        $data .= '<label>'.__("YYYY","piereg").'</label>';
                    $data .= '</div>';
				$data .= '</div>';
			} 
			else if($this->field['date_format']=="yy/mm/dd" || $this->field['date_format']=="yy.mm.dd")
			{
				$data .= '<div class="piereg_time date_format_field">';
                    $data .= '<div class="time_fields">';
                        $data .= '<input id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" maxlength="4"  type="text" class="'.$this->addClass("input_fields",array("custom[year]")).'" value="'.((isset($date_this_values['date']['yy']))?$date_this_values['date']['yy']:"").'">';
                        $data .= '<label>'.__("YYYY","piereg").'</label>';
                    $data .= '</div>';
                    $data .= '<div class="time_fields">';
                        $data .= '<input id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" maxlength="2" type="text" class="'.$this->addClass("input_fields",array("custom[month]")).'" '.$this->addValidation().' value="'.((isset($date_this_values['date']['mm']))?$date_this_values['date']['mm']:"").'">';
                        $data .= '<label>'.__("MM","piereg").'</label>';
                    $data .= '</div>';
                    $data .= '<div class="time_fields">';
                        $data .= '<input id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" maxlength="2"  type="text" class="'.$this->addClass("input_fields",array("custom[day]")).'" value="'.((isset($date_this_values['date']['dd']))?$date_this_values['date']['dd']:"").'">';
                        $data .= '<label>'.__("DD","piereg").'</label>';
                    $data .= '</div>';
				$data .= '</div>';
			}
			else if($this->field['date_format']=="dd/mm/yy" || $this->field['date_format']=="dd-mm-yy" || $this->field['date_format']=="dd.mm.yy")
			{
                $data .= '<div class="piereg_time date_format_field">';
                    $data .= '<div class="time_fields">';
                        $data .= '<input id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" maxlength="2"  type="text" class="'.$this->addClass("input_fields",array("custom[day]")).'" value="'.((isset($date_this_values['date']['dd']))?$date_this_values['date']['dd']:"").'">';
                        $data .= '<label>'.__("DD","piereg").'</label>';
                    $data .= '</div>';
                    $data .= '<div class="time_fields">';
                        $data .= '<input id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" maxlength="2" type="text" class="'.$this->addClass("input_fields",array("custom[month]")).'" '.$this->addValidation().' value="'.((isset($date_this_values['date']['mm']))?$date_this_values['date']['mm']:"").'">';
                        $data .= '<label>'.__("MM","piereg").'</label>';
                    $data .= '</div>';
                    $data .= '<div class="time_fields">';
                        $data .= '<input id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" maxlength="4"  type="text" class="'.$this->addClass("input_fields",array("custom[year]")).'" value="'.((isset($date_this_values['date']['yy']))?$date_this_values['date']['yy']:"").'">';
                        $data .= '<label>'.__("YYYY","piereg").'</label>';
                    $data .= '</div>';
                $data .= '</div>';
			}
			else
			{
                $data .= '<div class="piereg_time date_format_field">';
                    $data .= '<div class="time_fields">';
                        $data .= '<input id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" maxlength="2"  type="text" class="'.$this->addClass("input_fields",array("custom[day]")).'" value="'.((isset($date_this_values['date']['dd']))?$date_this_values['date']['dd']:"").'">';
                        $data .= '<label>'.__("DD","piereg").'</label>';
                    $data .= '</div>';
                    $data .= '<div class="time_fields">';
                        $data .= '<input id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" maxlength="4"  type="text" class="'.$this->addClass("input_fields",array("custom[year]")).'" value="'.((isset($date_this_values['date']['yy']))?$date_this_values['date']['yy']:"").'">';
                        $data .= '<label>'.__("YYYY","piereg").'</label>';
                    $data .= '</div>';
                    $data .= '<div class="time_fields">';
                        $data .= '<input id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" maxlength="2" type="text" class="'.$this->addClass("input_fields",array("custom[month]")).'" '.$this->addValidation().'" value="'.((isset($date_this_values['date']['mm']))?$date_this_values['date']['mm']:"").'">';
                        $data .= '<label>'.__("MM","piereg").'</label>';
                    $data .= '</div>';
                $data .= '</div>';
			}
		}
		else if($this->field['date_type'] == "datepicker")
		{
				if( $this->field['calendar_icon'] == "calendar" || $this->field['calendar_icon'] == "custom" ) 
				  $data .= '<div class="piereg_time date_format_field date_with_icon">';
				else 
				  $data .= '<div class="piereg_time date_format_field">';
				
				  $data .= '<input id="'.$this->id.'" name="'.$this->name.'[date][]" readonly="readonly" type="text" class="'.$this->addClass().' date_start" title="'.$this->field['date_format'].'" value="';
				  
				$data .= ( (isset($date_this_values['date'][0]) && !empty($date_this_values['date'][0])) ? $date_this_values['date'][0] : "" );
				$data .= '" />';
				$data .= '<input id="'.$this->id.'_format" type="hidden"  value="'.((isset($this->field['date_format'])) ? $this->field['date_format'] : "").'">';
				$data .= '<input id="'.$this->id.'_firstday" type="hidden"  value="'.((isset($this->field['firstday'])) ? $this->field['firstday'] : "").'">';
				$data .= '<input id="'.$this->id.'_startdate" type="hidden"  value="'.((isset($this->field['startdate'])) ? $this->field['startdate'] : "").'">';
				  
				if($this->field['calendar_icon'] == "calendar")
				{
					 $data .=  '<img id="'.$this->id.'_icon" class="calendar_icon" src="'.plugins_url('pie-register').'/images/calendar.png" />';
				}
				else if($this->field['calendar_icon'] == "custom")
				{
					 $data .=  '<img id="'.$this->id.'_icon" class="calendar_icon" src="'.$this->field['calendar_icon_url'].'" />'; 
				}
				  
				 $data .= '</div>';	
		}
		else if($this->field['date_type'] == "datedropdown")
		{
				
			if($this->field['date_format']=="mm/dd/yy")
			{
			
					$data .= '<div class="piereg_time date_format_field">';
					  $data .= '<div class="time_fields">';
						$data .= '<select id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" class="'.$this->addClass("input_fields",array("custom[month]")).'" '.$this->addValidation().'>';
						  $data .= '<option value="">'.__("Month","piereg").'</option>';
						  for($a=1;$a<=12;$a++){
							  if(isset($date_this_values['date']['mm']) && $date_this_values['date']['mm'] == $a)
								$sel = ' selected=""';
							  else
							  $sel = '';	
							  $data .= '<option value="'.str_pad($a, 2, "0", STR_PAD_LEFT).'" '.$sel.'>'.str_pad(__($a,"piereg"), 2, "0", STR_PAD_LEFT).'</option>';
						  }
						  $data .= '</select>';
					  $data .= '</div>';
				  $data .= '<div class="time_fields">';
					$data .= '<select id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" class="'.$this->addClass("input_fields",array("custom[day]")).'" '.$this->addValidation().'>';
					  $data .= '<option value="">'.__("Day","piereg").'</option>';
					  for($a=1;$a<=31;$a++){
						  if(isset($date_this_values['date']['dd']) && $date_this_values['date']['dd'] == $a)
						  	$sel = ' selected=""';
						  else
						  $sel = '';	
						  $data .= '<option value="'.str_pad($a, 2, "0", STR_PAD_LEFT).'" '.$sel.'>'.str_pad(__($a,"piereg"), 2, "0", STR_PAD_LEFT).'</option>';
					  }
					  $data .= '</select>';
				  $data .= '</div>';
				  $data .= '<div class="time_fields">';
					$data .= '<select id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" class="'.$this->addClass("input_fields",array("custom[year]")).'" '.$this->addValidation().'>';
					  $data .= '<option value="">'.__("Year","piereg").'</option>';
					  for($a=((int)date("Y"));$a>=(((int)date("Y"))-100);$a--){
						  if(isset($date_this_values['date']['yy']) && $date_this_values['date']['yy'] == $a)
						  	$sel = ' selected=""';
						  else
						  	$sel = '';	
						  $data .= '<option value="'.$a.'" '.$sel.'>'.__($a,"piereg").'</option>';
					  }
					  $data .= '</select>';
				  $data .= '</div>';
				$data .= '</div>';
			}
			else if($this->field['date_format']=="yy/mm/dd" || $this->field['date_format']=="yy.mm.dd")
			{
					$data .= '<div class="piereg_time date_format_field">';
					 $data .= '<div class="time_fields">';
					$data .= '<select id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" class="'.$this->addClass("input_fields",array("custom[year]")).'">';
					  $data .= '<option value="">'.__("Year","piereg").'</option>';
					  for($a=((int)date("Y"));$a>=(((int)date("Y"))-100);$a--){
						  if(isset($date_this_values['date']['yy']) && $date_this_values['date']['yy'] == $a)
						  	$sel = ' selected=""';
						  else
						  $sel = '';	
						  $data .=  '<option value="'.$a.'" '.$sel.'>'.__($a,"piereg").'</option>';
					  }
					  $data .=  '</select>';
				  $data .= '</div>';
				  $data .= '<div class="time_fields">';
					$data .= '<select id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" class="'.$this->addClass("input_fields",array("custom[month]")).'" '.$this->addValidation().'>';
					  $data .= '<option value="">'.__("Month","piereg").'</option>';
					  for($a=1;$a<=12;$a++){
						  if(isset($date_this_values['date']['mm']) && $date_this_values['date']['mm'] == $a)
						  	$sel = ' selected=""';
						  else
						  $sel = '';	
						  $data .=  '<option value="'.str_pad($a, 2, "0", STR_PAD_LEFT).'" '.$sel.'>'.str_pad(__($a,"piereg"), 2, "0", STR_PAD_LEFT).'</option>';
					  }
					  $data .=  '</select>';
				  $data .= '</div>';
				  $data .= '<div class="time_fields">';
					$data .= '<select id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" class="'.$this->addClass("input_fields",array("custom[day]")).'">';
					  $data .= '<option value="">'.__("Day","piereg").'</option>';
					  for($a=1;$a<=31;$a++){
						  if(isset($date_this_values['date']['dd']) && $date_this_values['date']['dd'] == $a)
						  	$sel = ' selected=""';
						  else
						  $sel = '';	
						  $data .=  '<option value="'.str_pad($a, 2, "0", STR_PAD_LEFT).'" '.$sel.'>'.str_pad(__($a,"piereg"), 2, "0", STR_PAD_LEFT).'</option>';
					  }
					  $data .= '</select>';
				  $data .= '</div>';
				$data .= '</div>';
			}
			else
			{
				$data .= '<div class="piereg_time date_format_field">';
				  $data .= '<div class="time_fields">';
					$data .= '<select id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" class="'.$this->addClass("input_fields",array("custom[day]")).'">';
					  $data .= '<option value="">'.__("Day","piereg").'</option>';
					  for($a=1;$a<=31;$a++){
						  if(isset($date_this_values['date']['dd']) && $date_this_values['date']['dd'] == $a)
						  	$sel = ' selected=""';
						  else
							  $sel = '';	
						  $data .=  '<option value="'.str_pad($a, 2, "0", STR_PAD_LEFT).'" '.$sel.'>'.str_pad(__($a,"piereg"), 2, "0", STR_PAD_LEFT).'</option>';
					  }
					  $data .= '</select>';
				  $data .= '</div>';
				  $data .= '<div class="time_fields">';
					$data .= '<select id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" class="'.$this->addClass("input_fields",array("custom[month]")).'" '.$this->addValidation().'>';
					  $data .= '<option value="">'.__("Month","piereg").'</option>';
					  for($a=1;$a<=12;$a++){
						  if(isset($date_this_values['date']['mm']) && $date_this_values['date']['mm'] == $a)
						  	$sel = ' selected=""';
						  else
							  $sel = '';
						  $data .=  '<option value="'.str_pad($a, 2, "0", STR_PAD_LEFT).'" '.$sel.'>'.str_pad(__($a,"piereg"), 2, "0", STR_PAD_LEFT).'</option>'; 
					  }
					  $data .=  '</select>';
				  $data .= '</div>';
				  	 $data .= '<div class="time_fields">';
					$data .= '<select id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" class="'.$this->addClass("input_fields",array("custom[year]")).'">';
					  $data .= '<option value="">'.__("Year","piereg").'</option>';
					  for($a=((int)date("Y"));$a>=(((int)date("Y"))-100);$a--){
						  if(isset($date_this_values['date']['yy']) && $date_this_values['date']['yy'] == $a)
						  	$sel = ' selected=""';
						  else
							  $sel = '';	
						  $data .=  '<option value="'.$a.'" '.$sel.'>'.__($a,"piereg").'</option>';
					  }
					  $data .=  '</select>';
				  $data .= '</div>';
				$data .= '</div>';
			}			
		}
		$data .= $this->addDesc();
		$data .= '</div>';
		return $data;
	}
	function addInvitationField(){
		if($this->made_conditional_logic()){
			$cl_class="hasConditionalLogic";
			$cl_data=' data-triggerid="'.$this->get_pr_widget_prefix().'piereg_field_'.$this->field['selected_field'].'"';
			$cl_data.=' data-content="'.$this->field['conditional_value'].'"';
			$cl_data.=' data-operator="'.$this->field['field_rule_operator'].'"';
			$cl_data.=' data-display="'.$this->field['field_status'].'"';
		}else{
			$cl_class='';
			$cl_data='';
		}
		$data  = '<div class="fieldset">';
		$data .= $this->addLabel();
		$data .= '<input id="'.$this->id.'" name="invitation" class="'.$this->addClass().' '.$cl_class.'"'.$cl_data.' data-field_id="'.$this->get_pr_widget_prefix().'piereg_field_'.$this->no.'" placeholder="'.$this->field['placeholder'].'" type="text" value="'.$this->getDefaultValue().'" />';
		$data .= $this->addDesc();
		$data .= '</div>';
		return $data;
	}
	function addCaptcha($id)
	{
		$data = "";
		$data .= '<div class="fieldset">';
		$data .= $this->addLabel();
		$settings  	=  get_option(OPTION_PIE_REGISTER);
		$publickey	= $settings['captcha_publc'] ;
		
		if($publickey)
		{
			$data .= '<div class="input_fields piereg_recaptcha_reg_div" id="reg_form_'.$id.'">';
			$data .= '</div>';
		}
		$data .= $this->addDesc();
		$data .= '</div>';
		return $data;
	}
	function addMath_Captcha($piereg_widget = false){
		if( $piereg_widget ){
			$cap_id = "is_login_widget";
			$cookie = 'registration_widget';
		}else{
			$cap_id = "not_login_widget";
			$cookie = 'registration';
		}
		$data = '<div class="fieldset">'.$this->addLabel();
		$operator = rand(0,1);
		////1 for add(+)
		////0 for subtract(-)
		$data = "";
		$field_id = "";
		if($piereg_widget == true){
		
			$data .= '<div id="pieregister_math_captha_widget" data-cookiename="'.$cookie.'" class="piereg_math_captcha prMathCaptcha"></div>';
			$data .= '<input id="'.$this->id.'" type="text" data-errormessage-value-missing="'.$this->field['validation_message'].'" data-errormessage-range-underflow="'.$this->field['validation_message'].'" data-errormessage-range-overflow="'.$this->field['validation_message'].'" class="'.$this->addClass().'" placeholder="'.$this->field['placeholder'].'" style="width:auto;margin-top:9px;" name="piereg_math_captcha_widget"/>';
			$field_id = "#pieregister_math_captha_widget";
		}
		else{
			$data .= '<div  data-cookiename="'.$cookie.'" class="wrapmathcaptcha prMathCaptcha">';
			$data .= '<div id="pieregister_math_captha" class="piereg_math_captcha"></div>';
			$data .= '<input id="'.$this->id.'" type="text" data-errormessage-value-missing="'.$this->field['validation_message'].'" data-errormessage-range-underflow="'.$this->field['validation_message'].'" data-errormessage-range-overflow="'.$this->field['validation_message'].'" class="'.$this->addClass().'" placeholder="'.$this->field['placeholder'].'" style="width:auto;margin-top:9px;" name="piereg_math_captcha"/>';
			$data .= '</div>';
			
			$field_id = "#pieregister_math_captha";
		}
		$data .= $this->addDesc();
		return $data;
	}
	function addSubmit($options = array())
	{
		$data = "";
		$data .= '<div class="fieldset">';
		$data .= '<div class="pie_wrap_buttons">';
		if($this->pages > 1)
		{
			if( $this->pageBreak_prev_type == 'url' ) {
				$data .= '<img class="pie_prev" name="pie_prev" id="pie_prev_'.$this->pages.'" src="'.$this->pageBreak_prev_label.'"  />';				
			} else{
				if($this->pageBreak_prev_label == '')
					$this->pageBreak_prev_label = "Previous";
					
				$data .= '<input class="pie_prev" name="pie_prev" id="pie_prev_'.$this->pages.'" type="button" value="'.__($this->pageBreak_prev_label,"piereg").'" />';
			}			
			$data .= '<input id="pie_prev_'.$this->pages.'_curr" name="page_no" type="hidden" value="'.($this->pages-1).'" />';						
		}
		
		$data .= '<input name="pie_submit" type="submit" value="'.__($this->field['text'],"piereg").'" />';		
		if($this->field['reset']==1)
		{
			$data .= '<input name="pie_reset" type="reset" value="'.__($this->field['reset_text'],"piereg").'" />';
		}
		
		if( isset($options['reg_form_submission_time_enable']) && intval($options['reg_form_submission_time_enable']) == 1 && $this->piereg_pro_is_activate)
		{
			if( isset($options['reg_form_submission_time']) && intval($options['reg_form_submission_time']) > 0 ){
				$time_field_id = time();
				$data .= '<input class="prTimedField" type="hidden" name="prereg_form_submission" id="prereg_form_submission_'.$time_field_id.'" value="" />';
			}
		}
		
		$data .= $this->addDesc();
		$data .= '</div>';
		$data .= '</div>';
		return $data;
	}
	
	function addPaypal()
	{
		return '<input name="pie_submit" value="paypal" type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" />';	
	}
	
	function addPagebreak($fromwidget = false)
	{
		$data = "";
		$cl = "";
		if($fromwidget)
			$cl = 'piewid_';
		
		$data .= '<div class="fieldset">'.$this->addLabel();
		
		$data .= '<input id="'.$cl.'total_pages" class="piereg_regform_total_pages" name="pie_total_pages" type="hidden" value="'.$this->countPageBreaks().'" />';
		
		if($this->pageBreak_prev_label == ''){
			if($this->field['prev_button']=="text"){
				$this->pageBreak_prev_label = $this->field['prev_button_text'];
			} else if($this->field['prev_button']=="url") {
				$this->pageBreak_prev_label = $this->field['prev_button_url'];
			}			
		}
		
		if( $this->pageBreak_prev_type == '')
			$this->pageBreak_prev_type = $this->field['prev_button'];
		
		if($this->pages > 1){
			
			$data .= '<input id="'.$cl.'pie_prev_'.$this->pages.'_curr" name="page_no" type="hidden" value="'.($this->pages-1).'" />';		
			
			if($this->field['prev_button']=="text")
			{
				//$data .= '<input class="pie_prev" name="pie_prev" id="'.$cl.'pie_prev_'.$this->pages.'" type="button" value="'.__($this->field['prev_button_text'],"piereg").'" />';
				$data .= '<input class="pie_prev" name="pie_prev" id="'.$cl.'pie_prev_'.$this->pages.'" type="button" value="'.__($this->pageBreak_prev_label,"piereg").'" />';	
			}
			else if($this->field['prev_button']=="url")
			{
				//$data .= '<img class="pie_prev" name="pie_prev" id="'.$cl.'pie_prev_'.$this->pages.'" src="'.$this->field['prev_button_url'].'"  />';
				$data .= '<img class="pie_prev" name="pie_prev" id="'.$cl.'pie_prev_'.$this->pages.'" src="'.$this->pageBreak_prev_label.'"  />';		
			}
			
			if($this->field['prev_button']=="text"){
				$this->pageBreak_prev_label = $this->field['prev_button_text'];
			} else if($this->field['prev_button']=="url") {
				$this->pageBreak_prev_label = $this->field['prev_button_url'];
			}
			
			$this->pageBreak_prev_type = $this->field['prev_button'];
			
		}
		
		
		$data .= '<input id="'.$cl.'pie_next_'.$this->pages.'_curr" name="page_no" type="hidden" value="'.($this->pages+1).'" />';	
		if($this->field['next_button']=="text")
		{
			$data .= '<input class="'.$cl.'pie_next" name="pie_next" id="'.$cl.'pie_next_'.$this->pages.'" type="button" value="'.__($this->field['next_button_text'],"piereg").'" />';
		}
		else if($this->field['next_button']=="url")
		{
			$data .= '<img style="cursor:pointer;" src="'.$this->field['next_button_url'].'" class="'.$cl.'pie_next" name="pie_next" id="'.$cl.'pie_next_'.$this->pages.'" />';	
		}
		$data .= $this->addDesc();
		$data .= '</div>';
		return $data;	
	}
	
	function countPageBreaks()
	{
		$pages = 1;
		if( isset($this->data) && !empty($this->data) ){
			foreach($this->data as $field)
			{
				if($field['type']=="pagebreak")
					$pages++;	
			}
		}
		return $pages ;
	}
	
	function made_conditional_logic(){
		if( isset($this->field['conditional_logic']) && $this->field['conditional_logic'] == "1" && $this->piereg_pro_is_activate){
			return true;
		}
		return false;
	}
	function printFields($fromwidget = false,$form_id="default",$title="false",$description="false")
	{
		if($fromwidget == true)
			$this->is_pr_widget = true;
		else
			$this->is_pr_widget = false;
		
		if($form_id == "default" || $form_id == "0")
		{
			$id = "default";
			$this->data = $this->getCurrentFields();
			$this->label_alignment = $this->data['form']['label_alignment'];		
			$this->pages = 1;
		}
		else
		{
			$id = intval($form_id);
			$this->data = $this->getCurrentFields($id);
			$this->label_alignment = $this->data['form']['label_alignment'];		
			$this->pages = 1;
		}
		
		$pie_reg_fields = "";
		$update = get_option(OPTION_PIE_REGISTER);
		$pie_reg_fields .= $this->addFormData($title,$description);
		$pie_reg_fields .= '<ul id="pie_register">';

		if(is_array($this->data)){
			foreach($this->data as $this->field)
			{
				if ($this->field['type']=="")
				{
					continue;
				}
				if($this->field['type']=="invitation" && $update["enable_invitation_codes"]=="0")
				{
					continue;	
				}
				
				if( $this->field['type'] == "honeypot" && !$this->piereg_pro_is_activate ) {	
					continue;
				}	
				
				if($this->field['type'] == "form"){
					$pie_reg_fields .= '<input type="hidden" value="'.$id.'" name="form_id" />';
					continue;
				}
	
				$this->name 	= $this->createFieldName($this->field['type']."_".((isset($this->field['id']))?$this->field['id']:""));
				$this->id 		= $this->name;
				$this->no		= ( isset($this->field['id']) ? $this->field['id'] : "" );
	
				//We don't need to print li for hidden field
				if($this->field['type'] == "hidden")
				{
					$pie_reg_fields .= $this->addHiddenField();
					continue;
				}
				
				
				$topclass = "";
				if($this->label_alignment=="top")
					$topclass = "label_top";
				
				
				$this->field_status = "";
				if(isset($this->field['conditional_logic']) && $this->field['conditional_logic'] == "1"){
					if( ($this->field['field_status'] == "1" and $this->field['selected_field'] != "") and ($this->field['field_rule_operator'] != "" and $this->field['conditional_value'] != "") ){
						$this->field_status = 'style="display:none;"';
					}else{
						
						$this->field_status = 'style="display:block;"';
					}
				}
				
				if( $this->field['type'] == "honeypot" && $this->piereg_pro_is_activate )
					$pie_reg_fields .= '<li class="fields '.$topclass.'  '.$this->get_pr_widget_prefix().'piereg_li_'.(isset($this->field['id'])?$this->field['id']:"").'"  '.$this->field_status.' >';
				else
					$pie_reg_fields .= '<li class="fields pageFields_'.$this->pages.' '.$topclass.'  '.$this->get_pr_widget_prefix().'piereg_li_'.(isset($this->field['id'])?$this->field['id']:"").'"  '.$this->field_status.' >';
				
				if($this->field['type'] == "pagebreak")
				{
					$pie_reg_fields .= $this->addPagebreak($fromwidget);	
					$this->pages++;			
				}
				//Printting Field
				switch($this->field['type']){
					case 'text' :								
					case 'website' :
						$pie_reg_fields .= $this->addTextField();
					break;				
					case 'username' :
						$pie_reg_fields .= $this->addUsername($fromwidget);
					break;
					case 'password' :
						$pie_reg_fields .= $this->addPassword($fromwidget,$this->field_status);
					break;
					case 'email' :
						$pie_reg_fields .= $this->addEmail($fromwidget);
					break;
					case 'textarea':
						$pie_reg_fields .= $this->addTextArea();
					break;
					case 'dropdown':
					case 'multiselect':
						$pie_reg_fields .= $this->addDropdown();
					break;
					case 'number':
						$pie_reg_fields .= $this->addNumberField();			
					break;
					case 'radio':
					case 'checkbox':
						$pie_reg_fields .= $this->addCheckRadio();
					break;
					case 'html':
						$pie_reg_fields .= $this->addHTML();
					break;
					case 'name':
						$pie_reg_fields .= $this->addName();
					break;
					case 'time':
						$pie_reg_fields .= $this->addTime();
					break;
					case 'upload':
						$pie_reg_fields .= $this->addUpload();
					break;
					case 'profile_pic':
						$pie_reg_fields .= $this->addProfilePicUpload();
					break;
					case 'address':
						$pie_reg_fields .= $this->addAddress();
					break;
					case 'captcha':
						$pie_reg_fields .= $this->addCaptcha($id);
					break;
					case 'math_captcha':
						global $piereg_math_captcha_register,$piereg_math_captcha_register_widget;
						if($piereg_math_captcha_register != true && $fromwidget == false){
							$pie_reg_fields .= '<div class="fieldset">'.($this->addLabel());
							$pie_reg_fields .= $this->addMath_Captcha($fromwidget);
							$pie_reg_fields .= '</div>';
							$piereg_math_captcha_register = true;
						}elseif($piereg_math_captcha_register_widget != true && $fromwidget == true){
							$pie_reg_fields .= '<div class="fieldset">'.($this->addLabel());
							$pie_reg_fields .= $this->addMath_Captcha($fromwidget);
							$pie_reg_fields .= '</div>';
							$piereg_math_captcha_register_widget = true;
						}
					break;
					case 'phone':
						$pie_reg_fields .= $this->addPhone();
					break;
					case 'two_way_login_phone':
						$this->name = "piereg_two_way_login_phone";
						$pie_reg_fields .= $this->addPhone();
					break;
					case 'date':
						$pie_reg_fields .= $this->addDate();			
					break;
					case 'list':
						$pie_reg_fields .= $this->addList();
					break;
					case 'pricing':
						$pie_reg_fields .= $this->addPricing();
					break;
					case 'sectionbreak':
						$pie_reg_fields .= $this->addSectionBreak();
					break;	
					case 'default':
						$pie_reg_fields .= $this->addDefaultField();
					break;
					case 'honeypot':
						$pie_reg_fields .= $this->addHoneypot();
					break;
					case 'invitation':
						$pie_reg_fields .= $this->addInvitationField();
					break;
					case 'submit':
						$pie_reg_fields .= $this->addSubmit($update);
					break;					
				}
						
				if($this->field['type'] == "password" )
				{
					$widget = (isset($fromwidget) && $fromwidget == true)? '_widget' : '';
					$pie_reg_fields .= '<input class="prMinimumPasswordStrengthlength" type="hidden" id="password_strength_meter_'.$id.'" data-id="'.$id.'" value="'.((isset($this->field['restrict_strength']))?intval($this->field['restrict_strength']):0).'" />';
					//Weak Password	
					$strength_message = ((isset($this->field['strength_message']) && !empty($this->field['strength_message']))?__($this->field['strength_message'],"piereg"):__("Weak Password","piereg"));
                    $pie_reg_fields .= '<span class="prMinimumPasswordStrengthMessage" id="password_strength_message_'.$id.'" style="display:none;">'.$strength_message.'</span>';
				}
	
				$pie_reg_fields .=  '</li>';
				if($this->field['type'] == "password" && $this->field['show_meter']==1)
				{
					$topclass = "";
					if($this->label_alignment=="top")
						$topclass = "label_top";
						
					$pie_reg_fields .=  '<li class="fields pageFields_'.$this->pages.' '.$topclass.' '.$this->get_pr_widget_prefix().'piereg_li_'.$this->field['id'].'" '.$this->field_status.'>';
					//NEW PASSWORD STRENGHT METER
					$widget = (isset($fromwidget) && $fromwidget == true)? '_widget' : '';
					$widget_style = (isset($fromwidget) && $fromwidget == true)? 'display: none;' : 'visibility: hidden;';
					$pie_reg_fields .=  '<div id="password_meter" class="fieldset" '.((isset($style))?$style:"").'>';
					$pie_reg_fields .=  '<label style="'.$widget_style.'">'.__("Password not entered","piereg").'</label>';
					$pie_reg_fields .=  '<div id="piereg_passwordStrength'.$widget.'" class="piereg_pass prPasswordStrengthMeter" >'.__($update['pass_strength_indicator_label'],"piereg").'</div>';
					$pie_reg_fields .=  '</div>';
					$pie_reg_fields .=  '</li>';

				}
			}
		}
		$pie_reg_fields .= '</ul>';
		return $pie_reg_fields;
	}
	function get_pr_widget_prefix(){
		if($this->is_pr_widget == true)
			return "widget_";
			
		return "";
	}
}