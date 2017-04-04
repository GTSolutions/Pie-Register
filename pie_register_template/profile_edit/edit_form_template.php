<?php
if( file_exists( (PIEREG_DIR_NAME)."/classes/edit_form.php" ) ){
	require_once( (PIEREG_DIR_NAME)."/classes/edit_form.php" );
}

class Edit_form_template extends Edit_form
{
	function __construct($user,$form_id = "default")	
	{
		parent::__construct($user,$form_id);
	}
	function addDesc()
	{
		if(!empty($this->field['desc']))
		{
			return '<p class="desc">'.html_entity_decode($this->field['desc']).'</p>';
		}
		return "";
	}
	function addFormData()
	{
		return '<h1 id="piereg_pie_form_heading">'.__("Profile Page","piereg").'</h1>';
	}
	function addDefaultField()
	{
		$data = "";
		$val = get_user_meta($this->user->data->ID , $this->field['field_name'], true);  #get_usermeta deprecated
		$data .= '<div class="fieldset">'.$this->addLabel();
		
		if($this->field['field_name']=="url") {
			if( empty($val) ) {
				$val = $this->user->data->user_url;
			}			
		}
		
		if($this->field['field_name']=="description")
		{
			$data .= '<textarea name="description" data-field_id="piereg_field_'.$this->no.'" id="description" rows="5" cols="80">'.$val.'</textarea>';	
		}
		else
		{
			$placeholder = isset($this->field['placeholder']) ? $this->field['placeholder'] : "";
			$data .= '<input id="'.$this->id.'" name="'.$this->field['field_name'].'" data-field_id="piereg_field_'.$this->no.'" class="'.$this->addClass().'"  placeholder="'.$placeholder.'" type="text" value="'.$val.'" />';	
		}
		
		$data .= '</div>';
		return $data;
	}
	
	function addTextField(){
		$val   = get_user_meta($this->user->data->ID , $this->slug, true); #get_usermeta deprecated
		if(is_array($val)){
			$val = implode( ",", $val );
		}
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
		// '.$cl_class.'"'.$cl_data.' 
		$data  = '<div class="fieldset">'.$this->addLabel();
		$data .= '<input id="'.$this->id.'" name="'.$this->name.'" data-field_id="piereg_field_'.$this->no.'" class="'.$this->addClass().' '.$cl_class.'"'.$cl_data.'  '.$this->addValidation().' placeholder="'.$this->field['placeholder'].'" type="text" value="'.$val.'" />';
		$data .= $this->addDesc();
		//$data .= $this->made_conditional_logic();
		$data .= '</div>';
		return $data;
	}
	function addHiddenField()
	{
		$val = get_user_meta($this->user->data->ID , $this->slug, true); #get_usermeta deprecated
		return '<input id="'.$this->id.'" name="'.$this->name.'" data-field_id="piereg_field_'.$this->no.'"  type="hidden" value="'.$val.'" />';		
	}
	function addUsername(){
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
		// '.$cl_class.'"'.$cl_data.'
		$data  = '<div class="fieldset">'.$this->addLabel();
		$data .= '<input type="text" data-field_id="piereg_field_'.$this->no.'" value="'.$this->user->data->user_login.'" readonly="readonly" disabled="disabled" class="'.$this->field['css'].' input_fields '.$cl_class.'"'.$cl_data.' />';
		$data .= $this->addDesc();
		//$data .= $this->made_conditional_logic();
		$data .= '</div>';
		return $data;
	}
	function addPassword(){
		$class = "";
		$fclass = "";
		$topclass = "";
		
		$data = "";	
		$data .= '<div class="fieldset"><label>'.__("Old Password","piereg").'</label><div '.$fclass.'><input id="old_password_'.$this->id.'" type="password" class="input_fields" value="" name="old_password" autocomplete="off"></div></li>';
		
		if($this->label_alignment=="top")
			$topclass = "label_top"; 
				
		$data .= '<li class="fields pageFields_'.$this->pages.' '.$topclass.'"><div class="fieldset">'.$this->addLabel();
		$data .= '<input id="'.$this->id.'" name="password" data-field_id="piereg_field_'.$this->no.'" class="'.$this->addClass("input_fields",array("minSize[8]")).'" placeholder="'.$this->field['placeholder'].'" type="password" value="" autocomplete="off" />';	
		
		$data .= '</div></li><li class="fields pageFields_'.$this->pages.' '.$topclass.'"><div class="fieldset"><label>'.__($this->field['label2'],"piereg").'</label><div '.$fclass.'><input id="confirm_password_'.$this->id.'" type="password" class="input_fields '.$this->field['css'].' piereg_validate[equals['.$this->id.']]" placeholder="'.$this->field['placeholder'].'" value="" name="confirm_password" autocomplete="off">';
			
		$data .= $this->addDesc();
		//$data .= $this->made_conditional_logic();
		$data .= '</div>';
		return $data;	
	}	
	function addEmail(){
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
		// '.$cl_class.'"'.$cl_data.'
		$data  = '<div class="fieldset">'.$this->addLabel();
		$data .= '<input id="'.$this->id.'" name="e_mail" data-field_id="piereg_field_'.$this->no.'" class="'.$this->addClass().' '.$cl_class.'"'.$cl_data.' '.$this->addValidation().' placeholder="'.$this->field['placeholder'].'" type="text" value="'.$this->user->data->user_email.'" autocomplete="off" />';
		$data .= $this->addDesc();
		//$data .= $this->made_conditional_logic();
		$data .= '</div>';
		return $data;
	}
	function addUpload()
	{
		$data = "";
		$data .= '<div class="fieldset">'.$this->addLabel();
		$val = get_user_meta($this->user->data->ID , $this->slug, true); #get_usermeta deprecated
		
		if(is_array($val))
		{
			$val = implode( ",", $val );	
		}
		
		$data .= '<input id="'.$this->id.'" name="'.$this->name.'" data-field_id="piereg_field_'.$this->no.'" class="'.$this->addClass().'" '.$this->addValidation().' type="file"  />';
		$data .= '<input id="'.$this->id.'" name="'.$this->name.'_hidden" value="'.$val.'" type="hidden"  />';
		$data .= '<a href="'.$val.'" target="_blank">'.basename($val).'</a>';
		$data .= "</div>";
		return $data;
	}
	function addProfilePic()
	{
		$data = "";
		$data .= '<div class="fieldset">'.$this->addLabel();
		$val = get_user_meta($this->user->data->ID , $this->slug, true); #get_usermeta deprecated
		if(is_array($val))
		{
			$val = implode( ",", $val );	
		}
		$data .= '<input id="'.$this->id.'" name="'.$this->name.'" data-field_id="piereg_field_'.$this->no.'" class="'.$this->addClass().' piereg_validate[funcCall[checkExtensions],ext[gif|GIF|jpeg|JPEG|jpg|JPG|png|PNG|bmp|BMP]]" '.$this->addValidation().' type="file"  />';
		$data .= '<input id="'.$this->id.'" name="'.$this->name.'_hidden" value="'.$val.'" type="hidden"  />';
		$ext = (trim(basename($val)))? $val." Not Found" : "Profile Pictuer Not Found";
		$imgPath = (trim($val) != "")? $val : plugins_url("images/userImage.png",dirname(dirname(__FILE__)));
		$data .= '<br /><img class="edit-profile-img" src="'.$imgPath.'" style="max-width:150px;" alt="'.__($imgPath,"piereg").'" />';
		$data .= "</div>";
		return $data;
	}
	function addTextArea(){
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
		// '.$cl_class.'"'.$cl_data.'
		$val = stripslashes(get_user_meta($this->user->data->ID , $this->slug, true)); #get_usermeta deprecated
		$data = '<div class="fieldset">'.$this->addLabel();
		$data .='<textarea id="'.$this->id.'" data-field_id="piereg_field_'.$this->no.'" name="'.$this->name.'" rows="'.$this->field['rows'].'" cols="'.$this->field['cols'].'"  class="'.$this->addClass().' '.$cl_class.'"'.$cl_data.'  placeholder="'.$this->field['placeholder'].'">'.$val.'</textarea>';
		$data .= $this->addDesc();
		//$data .= $this->made_conditional_logic();
		$data .= '</div>';
		return $data;
	}
	function addName(){
		$data = "";
		$val = get_user_meta($this->user->data->ID , "first_name", true); #get_usermeta deprecated
		if(is_array($val))
		{
			$val = implode(',', $val);	
		}
		$data .= '<div class="fieldset"><label>'.__($this->field['label'],"piereg").'</label>';
		$data .= '<input id="'.$this->id.'_firstname" data-field_id="piereg_field_'.$this->no.'" value="'.$val .'" name="first_name" class="'.$this->addClass().' input_fields piereg_name_input_field" '.$this->addValidation().' type="text"  />';
		$val = get_user_meta($this->user->data->ID , "last_name", true); #get_usermeta deprecated
		if(is_array($val)){
			$val = implode(',', $val);
		}
		$topclass = "";
		if($this->label_alignment=="top")
			$topclass = "label_top"; 					
		$data .= '</div>';
		$data .= '<div class="fieldset"><label>'.__($this->field['label2'],"piereg").'</label>';
		$data .= '<input id="'.$this->id.'_lastname" value="'.$val .'" name="last_name" class="'.$this->addClass().' input_fields piereg_name_input_field" '.$this->addValidation().' type="text"  />';
		$data .= $this->addDesc();
		//$data .= $this->made_conditional_logic();
		$data .= '</div>';
		return $data;
	}
	function addTime(){
		$data = "";
		$data .= '<div class="fieldset">'.$this->addLabel();
		$val = get_user_meta($this->user->data->ID , $this->slug, true); #get_usermeta deprecated
		$data .= '<div class="piereg_time">
					<div class="time_fields">
						<input maxlength="2" id="hh_'.$this->id.'" name="'.$this->name.'[hh]" type="text"  class="'.$this->addClass().'" '.$this->addValidation().' value="'.((isset($val['hh']))?$val['hh'] : "").'">
						<label>'.__("HH","piereg").'</label>
					</div>
					<span class="colon">:</span>
					<div class="time_fields">
						<input maxlength="2" id="mm_'.$this->id.'" type="text" name="'.$this->name.'[mm]"  class="'.$this->addClass().'" '.$this->addValidation().' value="'.((isset($val['mm']))?$val['mm']:"").'">
						<label>'.__("MM","piereg").'</label>
					</div>
				<div id="time_format_field_'.$this->id.'" class="time_fields"></div>';
		if($this->field['time_type']=="12")
		{
			$time_format = ((isset($val['time_format']))?$val['time_format']:"");
			$data .= '<div id="time_format_field_'.$this->id.'" class="time_fields">
				<select name="'.$this->name.'[time_format]" >
					<option ' . (($time_format == "am") ? ' selected="selected" ' : "") . ' value="am">'.__("AM","piereg").'</option>
					<option ' . (($time_format == "pm") ? ' selected="selected" ' : "") . ' value="pm">'.__("PM","piereg").'</option>
				</select>
			</div>';
		}
		$data .= '</div>';
		$data .= '</div>';
		
		return $data;
	}	
	function addDropdown(){
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
		// '.$cl_class.'"'.$cl_data.'
		$data = "";
		$data .= '<div class="fieldset">'.$this->addLabel();
		$multiple = "";
		$name = $this->name."[]";
		$val = get_user_meta($this->user->data->ID , $this->slug, true); #get_usermeta deprecated
		
		if(!is_array($val)):
			$sel = !empty($val) ? $val : "";
		else:
			$sel = !empty($val) ? $val[0] : "";
		endif;
		
		if($this->field['type']=="multiselect")
		{
			$multiple 	= 'multiple';			
			$sel = $val;
		}		
		
		$data .= '<select '.$multiple.' id="'.$this->name.'" data-field_id="piereg_field_'.$this->no.'" name="'.$name.'" class="'.$this->addClass("").' '.$cl_class.'"'.$cl_data.' '.$this->addValidation().' >';
		if($this->field['list_type']=="country")
		{
			$countries = get_option("pie_countries");			 
			$data .= $this->createDropdown($countries,$sel);			   	
		}
		else if($this->field['list_type']=="us_states")
		{
			$us_states	= get_option("pie_us_states");
			$data .= $this->createDropdown($us_states,$sel);
		}
		else if($this->field['list_type']=="can_states")
		{
			$can_states	= get_option("pie_can_states");
			$data .= $this->createDropdown($can_states,$sel);
		}
		else if($this->field['list_type']=="months")
		{
			$data .= '<option value = "1">'.__("January","piereg").'</option>
				<option value = "2">'.__("February","piereg").'</option>
				<option value = "3">'.__("March","piereg").'</option>
				<option value = "4">'.__("April","piereg").'</option>
				<option value = "5">'.__("May","piereg").'</option>
				<option value = "6">'.__("June","piereg").'</option>
				<option value = "7">'.__("July","piereg").'</option>
				<option value = "8">'.__("August","piereg").'</option>
				<option value = "9">'.__("September","piereg").'</option>
				<option value = "10">'.__("October","piereg").'</option>
				<option value = "11">'.__("November","piereg").'</option>
				<option value = "12">'.__("December","piereg").'</option>';
		}
		else if(sizeof($this->field['value']) > 0)
		{
			for($a = 0 ; $a < sizeof($this->field['value']) ; $a++)
			{
				$selected 	= "";				
				
				if( (is_array($val) && in_array($this->field['value'][$a],$val)) || (!empty($this->field['value'][$a]) && $val == $this->field['value'][$a]) ){
					$selected = 'selected="selected"';	
				}				
				
				//if($this->field['value'][$a] !="" && $this->field['display'][$a] != "")
				$data .= '<option '.$selected.' value="'.$this->field['value'][$a].'">'.$this->field['display'][$a].'</option>';	
			}		
		}	
		$data .= '</select>';
		$data .= $this->addDesc();
		//$data .= $this->made_conditional_logic();
		$data .= '</div>';
		return $data;
	}
	function addNumberField(){
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
		// '.$cl_class.'"'.$cl_data.'
		$val = get_user_meta($this->user->data->ID , $this->slug, true); #get_usermeta deprecated
		if(is_array($val))
		{
			$val = implode( ",", $val );	
		}
		$data = '<div class="fieldset">'.$this->addLabel();
		$data .= '<input id="'.$this->id.'" data-field_id="piereg_field_'.$this->no.'" name="'.$this->name.'" class="'.$this->addClass().' '.$cl_class.'"'.$cl_data.' '.$this->addValidation().' placeholder="'.$this->field['placeholder'].'" min="'.$this->field['min'].'" max="'.$this->field['max'].'" type="number" value="'.$val.'" />';
		$data .= $this->addDesc();
		//$data .= $this->made_conditional_logic();
		$data .= '</div>';
		return $data;
	}
	function addPhone(){
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
		// '.$cl_class.'"'.$cl_data.'
		$val = get_user_meta($this->user->data->ID , $this->slug, true); #get_usermeta deprecated
		if(is_array($val))
		{
			$val = implode( ",", $val );
		}
		$data = '<div class="fieldset">'.$this->addLabel();
		$data .= '<input id="'.$this->id.'" data-field_id="piereg_field_'.$this->no.'" name="'.$this->name.'" class="'.$this->addClass().' input_fields '.$cl_class.'"'.$cl_data.' '.$this->addValidation().' placeholder="'.((isset($field['placeholder']))?$field['placeholder']:"").'" type="text" value="'.$val.'" />';
		//$data .= $this->made_conditional_logic();
		$data .= '</div>';
		return $data;
	}
	function addList()
	{
		$data = "";
		$data .= '<div class="fieldset">'.$this->addLabel();
		$val = get_user_meta($this->user->data->ID , $this->slug, true); #get_usermeta deprecated
		if(!is_array($val))
		$val = array();
		$width  = 85 /  $this->field['cols'];
		$data .= '<div class="pie_list_cover">';
		for($a = 1 ,$c=0; $a <= $this->field['rows'] ; $a++,$c++)
		{
			$data .= '<div class="'.$this->id.'_'.$a.' pie_list">';
			$row  = "";
			for($b = 1 ; $b <= $this->field['cols'] ;$b++)
			{
				$data .= '<input style="width:'.$width.'%;margin-right:2px;padding:0px;" type="text" name="'.$this->name.'['.$c.'][]" class="input_fields" value="'.((isset($val[$c][$b-1]))?$val[$c][$b-1]:"").'"> ';
			}	
			$data .= '</div>';
		}
		$data .= '</div>';
		$data .= '</div>';
		return $data;
	}
	function addHTML()
	{
		return html_entity_decode($this->field['html']);
	}
	function addSectionBreak(){
		$class = "";
		
		if($this->label_alignment == "left")
			$class .= "wdth-lft ";
		
		$class .= "sectionBreak";
		
		$data  = '<div class="fieldset aligncenter">';
		//$data .= $this->addLabel();
		$data .= '<div class="'.$class.'">';
		$data .= '</div>';
		$data .= $this->addDesc();
		//$data .= $this->made_conditional_logic();
		$data .= '</div>';
		return $data;
	}
	function addCheckRadio()
	{
		$data = "";
		$data = '<div class="fieldset">'.$this->addLabel();
		$val = get_user_meta($this->user->data->ID , $this->slug, true); #get_usermeta deprecated
		if(sizeof($this->field['value']) > 0)
		{
			$data .= '<div class="radio_wrap">';
			for($a = 0 ; $a < sizeof($this->field['value']) ; $a++)
			{
				$checked = '';
				
				if( (is_array($val) && in_array($this->field['value'][$a],$val)) || (is_array($val) && in_array($this->field['value'][$a],$val)) )
				{
					$checked = 'checked="checked"';	
				}				
				if(!empty($this->field['display'][$a]))
				{
					$dymanic_class = $this->field['type']."_".$this->field['id'];
					$data .= "<label>";
					$data .= '<input '.$checked.' value="'.$this->field['value'][$a].'" data-field_id="piereg_field_'.$this->no.'" type="'.$this->field['type'].'" '.((isset($multiple))?$multiple:"").' name="'.$this->name.'[]" class="'.$this->addClass("").' radio_fields" '.$this->addValidation().' data-map-field-by-class="'.$dymanic_class.'" >';
					$data .= $this->field['display'][$a];
					$data .= "</label>";
				}
			}
			$data .= "</div>";		
		}
		$data .= "</div>";
		return $data;
	}
	function addAddress()
	{
		$data = "";
		$data .= '<div class="fieldset">'.$this->addLabel();
		$val = get_user_meta($this->user->data->ID , $this->slug, true); #get_usermeta deprecated
		$data .= '<div class="address_main">';
		$data .= '<div class="address">
		  <input type="text" name="'.$this->name.'[address]" id="'.$this->id.'" class="'.$this->addClass().'" '.$this->addValidation().' value="'.((isset($val['address']))?$val['address']:"").'">
		  <label>'.__("Street Address","piereg").'</label>
		</div>';
		 if(empty($this->field['hide_address2']))
		 {
			$data .= '<div class="address">
			  <input type="text" name="'.$this->name.'[address2]" id="address2_'.$this->id.'" class="input_fields '.$this->field['css'].'" '.$this->addValidation().' value="'.((isset($val['address2']))?$val['address2']:"").'">
			  <label>'.__("Address Line 2","piereg").'</label>
			</div>';
		 }
		$data .= '<div class="address">
		  <div class="address2">
			<input type="text" name="'.$this->name.'[city]" id="city_'.$this->id.'" class="input_fields addressLine2" '.$this->addValidation().' value="'.((isset($val['city']))?$val['city']:"").'">
			<label>'.__("City","piereg").'</label>
		  </div>';
		 if(empty($this->field['hide_state']))
		 {
			 	if($this->field['address_type'] == "International")
				{
					$data .= '<div class="address2"  >
					<input type="text" name="'.$this->name.'[state]" id="state_'.$this->id.'" class="'.$this->addClass().'" value="'.((isset($val['state']))?$val['state']:"").'">
					<label>'.__("State / Province / Region","piereg").'</label>
				 	 </div>';		
				}
				else if($this->field['address_type'] == "United States")
				{
				  $us_states = get_option("pie_us_states");
				  $options 	= $this->createDropdown($us_states,((isset($val['state']))?$val['state']:""));	
				  $data .= '<div class="address2"  >
					<select id="state_'.$this->id.'" name="'.$this->name.'[state]" class="'.$this->addClass("").'">
					 '.$options.' 
					</select>
					<label>'.__("State","piereg").'</label>
				  </div>';	
				}
				else if($this->field['address_type'] == "Canada")
				{
					$can_states = get_option("pie_can_states");
				  	$options 	= $this->createDropdown($can_states,((isset($val['state']))?$val['state']:""));
					$data .= '<div class="address2">
						<select id="state_'.$this->id.'" class="'.$this->addClass("").'" name="'.$this->name.'[state]">
						  '.$options.'
						</select>
						<label>'.__("Province","piereg").'</label>
					  </div>';		
				}
		 }
		$data .= '</div>';
		$data .= '<div class="address">';	
		$data .= ' <div class="address2">
		<input id="zip_'.$this->id.'" name="'.$this->name.'[zip]" type="text" class="'.$this->addClass().'" '.$this->addValidation().' value="'.((isset($val['zip']))?$val['zip']:"").'">
		<label>'.__("Zip / Postal Code","piereg").'</label>
		</div>';	 
		if($this->field['address_type'] == "International")
		{
			 $countries = get_option("pie_countries");			 
			 $options 	= $this->createDropdown($countries,((isset($val['country']))?$val['country']:""));  
			 $data .= '<div  class="address2" >
					<select id="country_'.$this->id.'" name="'.$this->name.'[country]" class="'.$this->addClass("").'" '.$this->addValidation().'> 
                    <option value="">'.__("Select Country","piereg").'</option>
					'. $options .'
					 </select>
					<label>'.__("Country","piereg").'</label>
		  		</div>';
		}
		$data .= '</div>';
		$data .= '</div>';
		$data .= '</div>';
		return $data;
	}	
	function addDate()
	{
		$data = "";
		$data .= '<div class="fieldset">'.$this->addLabel();
		$val = get_user_meta($this->user->data->ID , $this->slug, true);  #get_usermeta deprecated
		
		if($this->field['date_type'] == "datefield")
		{
			if(isset($val['date']) && !is_array($val['date']))
			{
				$val['date']['mm']	= "";
				$val['date']['dd']	= "";
				$val['date']['yy']	= "";
			}
			if($this->field['date_format']=="mm/dd/yy")
			{
			$data .= '<div class="piereg_time date_format_field">
				  <div class="time_fields">
					<input id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" maxlength="2" type="text" class="'.$this->addClass("input_fields",array("custom[month]")).'" '.$this->addValidation().' value="'.((isset($val['date']['mm']))?$val['date']['mm']:"").'">
					<label>'.__("MM","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" maxlength="2"  type="text" class="'.$this->addClass("input_fields",array("custom[day]")).'" '.$this->addValidation().' value="'.((isset($val['date']['dd']))?$val['date']['dd']:"").'">
					<label>'.__("DD","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" maxlength="4"  type="text" class="'.$this->addClass("input_fields",array("custom[year]")).'" '.$this->addValidation().' value="'.((isset($val['date']['yy']))?$val['date']['yy']:"").'">
					<label>'.__("YYYY","piereg").'</label>
				  </div>
				</div>';
			} 
			else if($this->field['date_format']=="yy/mm/dd" || $this->field['date_format']=="yy.mm.dd")
			{
				$data .= '<div class="piereg_time time date_format_field">
				 <div class="time_fields">
					<input value="'.(isset($val['date']['yy'])?$val['date']['yy']:"").'" id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" maxlength="4"  type="text" class="'.$this->addClass("input_fields",array("custom[year]")).'">
					<label>'.__("YYYY","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input value="'.(isset($val['date']['mm'])?$val['date']['mm']:"").'" id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" maxlength="2" type="text" '.$this->addValidation().'  class="'.$this->addClass("input_fields",array("custom[month]")).'">
					<label>'.__("MM","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input value="'.(isset($val['date']['dd'])?$val['date']['dd']:"").'" id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" maxlength="2"  type="text" class="'.$this->addClass("input_fields",array("custom[day]")).'">
					<label>'.__("DD","piereg").'</label>
				  </div>				  
				</div>';	
			}
			else
			{
				$data .= '<div class="piereg_time date_format_field">
				 <div class="time_fields">
					<input value="'.(isset($val['date']['dd']) ? $val['date']['dd'] :"").'" id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" maxlength="2" type="text" class="'.$this->addClass("input_fields",array("custom[day]")).'">
					<label>'.__("DD","piereg").'</label>
				  </div>				 
				  <div class="time_fields">
					<input value="'.(isset($val['date']['mm'])?$val['date']['mm']:"").'" id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" maxlength="2" type="text" class="'.$this->addClass("input_fields",array("custom[month]")).'">
					<label>'.__("MM","piereg").'</label>
				  </div>	
				  <div class="time_fields">
					<input value="'.(isset($val['date']['yy'])?$val['date']['yy']:"").'" id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" maxlength="4"  type="text" '.$this->addValidation().' class="'.$this->addClass("input_fields",array("custom[year]")).'">
					<label>'.__("YYYY","piereg").'</label>
				  </div>
				</div>';	
			}
		}
		else if($this->field['date_type'] == "datepicker")
		{
			if(isset($val['date']))
			if(isset($val['date']['yy']) && is_array($val['date']['yy']))
			{
				$val = 	$val['date']['yy']."-".($val['date']['mm'])."-".($val['date']['dd']);
			}
			else
			{
				$val = 	(isset($val['date'][0])) ? $val['date'][0] : "";	
			}	
				if( $this->field['calendar_icon'] == "calendar" || $this->field['calendar_icon'] == "custom" ) 
				  $data .=	'<div class="piereg_time date_format_field date_with_icon">';
				else
				  $data .=	'<div class="piereg_time date_format_field">';
				
				$data .= '<input id="'.$this->id.'" name="'.$this->name.'[date][]" readonly="readonly" type="text" class="'.$this->addClass().' date_start" value="'.$val.'">';
				if($this->field['calendar_icon'] == "calendar")
				{
					 $data .=  '<img id="'.$this->id.'_icon" class="calendar_icon" src="'.plugins_url('pie-register').'/images/calendar.png" />';
				}
				else if($this->field['calendar_icon'] == "custom")
				{
					 $data .=  '<img id="'.$this->id.'_icon" class="calendar_icon" src="'.$this->field['calendar_icon_url'].'"  />'; 
				}
				 $data .= '</div>';	
		}
		else if($this->field['date_type'] == "datedropdown")
		{
			if($this->field['date_format']=="mm/dd/yy")
			{
					$data .= '<div class="piereg_time date_format_field">
				  <div class="time_fields">
					<select id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" class="'.$this->addClass("").'" '.$this->addValidation().'>
					  <option value="">'.__("Month","piereg").'</option>';
					  for($a=1;$a<=12;$a++){
					  	$data .= '<option value="'.str_pad($a, 2, "0", STR_PAD_LEFT).'"';
						$data .= (isset($val['date']['mm']) && $val['date']['mm'] == $a)? 'selected="selected"' : "";
						$data .= '>'.str_pad(__($a,"piereg"), 2, "0", STR_PAD_LEFT).'</option>';
					  }
					  $data .= '
					</select>
				  </div>';

				  $data .=
				  '<div class="time_fields">
					<select id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" class="'.$this->addClass("").'" '.$this->addValidation().'>
					  <option value="">'.__("Day","piereg").'</option>';
					  for($a=1;$a<=31;$a++){
					  	$data .= '<option value="'.str_pad($a, 2, "0", STR_PAD_LEFT).'"';
						$data .= (isset($val['date']['dd']) && $val['date']['dd'] == $a)? 'selected="selected"' : "";
						$data .= '>'.str_pad(__($a,"piereg"), 2, "0", STR_PAD_LEFT).'</option>';
					  }
					$data .= '
					</select>
				  </div>';
				  $data .= '
				  <div class="time_fields">
					<select id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" class="'.$this->addClass("").'" '.$this->addValidation().'>
					  <option value="">'.__("Year","piereg").'</option>';
					  for($a=((int)date("Y"));$a>=(((int)date("Y"))-100);$a--){
					  	$data .= '<option value="'.$a.'"';
						$data .= (isset($val['date']['yy']) && $val['date']['yy'] == $a)? 'selected="selected"' : "";
						$data .= '>'.__($a,"piereg").'</option>';
					  }
					  $data .= '
					</select>
				  </div>
				</div>';
			}
			else if($this->field['date_format']=="yy/mm/dd" || $this->field['date_format']=="yy.mm.dd")
			{
					$data .= '<div class="piereg_time date_format_field">
					 <div class="time_fields">
					<select id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" class="'.$this->addClass("").'">
					  <option value="">'.__("Year","piereg").'</option>';
					  for($a=((int)date("Y"));$a>=(((int)date("Y"))-100);$a--){
					  	$data .= '<option value="'.$a.'"';
						$data .= (isset($val['date']) && $val['date']['yy'] == $a)? 'selected="selected"' : "";
						$data .= '>'.__($a,"piereg").'</option>';
					  }
					$data .= '
					</select>
				  </div>';
				  $data .= '
				  <div class="time_fields">
					<select id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" class="'.$this->addClass("").'" '.$this->addValidation().'>
					  <option value="">'.__("Month","piereg").'</option>';
					  for($a=1;$a<=12;$a++){
					  	$data .= '<option value="'.str_pad($a, 2, "0", STR_PAD_LEFT).'"';
						$data .= (isset($val['date']) && $val['date']['mm'] == $a)? 'selected="selected"' : "";
						$data .= '>'.str_pad(__($a,"piereg"), 2, "0", STR_PAD_LEFT).'</option>';
					  }
					  $data .= '
					</select>
				  </div>';
				   $data .=
				  '<div class="time_fields">
					<select id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" class="'.$this->addClass("").'">
					  <option value="">'.__("Day","piereg").'</option>';
					  for($a=1;$a<=31;$a++){
					  	$data .= '<option value="'.str_pad($a, 2, "0", STR_PAD_LEFT).'"';
						$data .= (isset($val['date']) && $val['date']['dd'] == $a)? 'selected="selected"' : "";
						$data .= '>'.str_pad(__($a,"piereg"), 2, "0", STR_PAD_LEFT).'</option>';
					  }
					$data .= '
					</select>
				  </div>				 
				</div>';
			}
			else
			{
				$data .= '<div class="piereg_time date_format_field">';
				
				  
				  $data .=
				  '<div class="time_fields">
					<select id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" class="'.$this->addClass("").'">
					  <option value="">'.__("Day","piereg").'</option>';
					  for($a=1;$a<=31;$a++){
					  	$data .= '<option value="'.str_pad($a, 2, "0", STR_PAD_LEFT).'"';
						$data .= (isset($val['date']) && $val['date']['dd'] == $a)? 'selected="selected"' : "";
						$data .= '>'.str_pad(__($a,"piereg"), 2, "0", STR_PAD_LEFT).'</option>';
					  }
					$data .= '
					</select>
				  </div>';
				  
				  $data .= '
				  <div class="time_fields">
					<select id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" class="'.$this->addClass("").'" '.$this->addValidation().'>
					  <option value="">'.__("Month","piereg").'</option>';
					  for($a=1;$a<=12;$a++){
					  	$data .= '<option value="'.str_pad($a, 2, "0", STR_PAD_LEFT).'"';
						$data .= (isset($val['date']) && $val['date']['mm'] == $a)? 'selected="selected"' : "";
						$data .= '>'.str_pad(__($a,"piereg"), 2, "0", STR_PAD_LEFT).'</option>';
					  }
						
					  $data .= '
					</select>
				  </div>';
				  	 $data .= '
				  <div class="time_fields">
					<select id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" class="'.$this->addClass("").'">
					  <option value="">'.__("Year","piereg").'</option>';
					  for($a=((int)date("Y"));$a>=(((int)date("Y"))-100);$a--){
					  	$data .= '<option value="'.$a.'"';
						$data .= (isset($val['date']) && $val['date']['yy'] == $a)? 'selected="selected"' : "";
						$data .= '>'.__($a,"piereg").'</option>';
					  }
					  
					  $data .= '
					</select>
				  </div>';	 
				$data .= '</div>';	
			}			
		}
		$data .= $this->addDesc();
		$data .= '</div>';
		return $data;
	}		
		
	function addLabel()
	{
		if($this->field['type']=="name" && $this->field['name_format']=="normal")
		{
			return "";
		}
		
		$field_required = "";
		if(isset($this->field['required']) && $this->field['required'] != "")
			$field_required .= '&nbsp;<span class="piereg_field_required_label">*</span>';
		
		return '<label for="'.$this->id.'">'. $condition . __($this->field['label'],"piereg").$field_required.'</label>';		
	}
	function addClass($default = "input_fields",$val = array())
	{
		$fieldcss = isset($this->field['css']) ? $this->field['css'] : "";
		$class = $default." ".$fieldcss;
		
		if(isset($this->field['required']) && $this->field['required'] && $this->field['type'] != "password") {
			
			if($this->field['type'] == 'upload' || $this->field['type'] == 'profile_pic') {
				
				$uploaded = get_user_meta($this->user->data->ID , $this->slug, true); #get_usermeta deprecated
				if( empty($uploaded) ){
					$val[] = "required";
				}	
			} else {
				$val[] = "required";	
			}
		}
		
		if(isset($this->field['length']) && intval($this->field['length']) > 0 )
		{
			$val[] = "maxSize[".intval($this->field['length'])."]";
		}

		if(isset($this->field['validation_rule']) && $this->field['validation_rule']=="number" )
		{
			$val[] = "custom[number]";
		}
		else if(isset($this->field['validation_rule']) && $this->field['validation_rule']=="alphanumeric")
		{
			$val[] = "custom[alphanumeric]";
		}
		else if((isset($this->field['validation_rule']) && $this->field['validation_rule']  =="alphabetic" ) || $this->field['type']=="name")
		{
			$val[] = "custom[alphabetic]";
		}
		else if((isset($this->field['validation_rule']) && $this->field['validation_rule']=="email") || $this->field['type']=="email")
		{
			$val[] = "custom[email]";
		}
		else if( 
				(isset($this->field['validation_rule'])) && ($this->field['validation_rule']=="website" || $this->field['type']=="website") 
				|| (isset($this->field['field_name']) && $this->field['field_name'] == 'url') 
			)
		{
			$val[] = "custom[url]";
		}
		else if((isset($this->field['validation_rule']) && $this->field['validation_rule']=="standard") || (isset($this->field['phone_format']) && $this->field['phone_format']=="standard" ))
		{
			$val[] = "custom[phone_standard]";		
		}
		else if((isset($this->field['validation_rule']) && $this->field['validation_rule']=="international") || (isset($this->field['phone_format']) && $this->field['phone_format']=="international"))
		{
			$val[] = "custom[phone_international]";		
		}
		else if($this->field['type']=="time")
		{
			$val[] = "custom[number]";	
			$val[] = "minSize[1]";
			$val[] = "maxSize[2]";	
		}
		else if($this->field['type']=="upload" && explode(",",$this->field['file_types']) > 0)
		{
			$val[] = "funcCall[checkExtensions]";	
			$val[] = "ext[".str_replace(",","|",$this->field['file_types'])."]";			
		}
		
		if(sizeof($val) > 0)
		{
			$val = " piereg_validate[".implode(",",$val)."]";
			$class .= $val;	
		}
		
		return $class;	
	}

	function addSubmit()
	{
		$data  = "";
		$data .= '<div class="pie_wrap_buttons">';
		if($this->pages > 1)
		{
			$data .= '<input class="pie_prev" name="pie_prev" id="pie_prev_'.$this->pages.'" type="button" value="'.__("Previous","piereg").'" />';
			$data .= '<input id="pie_prev_'.$this->pages.'_curr" name="page_no" type="hidden" value="'.($this->pages-1).'" />';						
		}
		$check_payment = get_option(OPTION_PIE_REGISTER);
		$cancel_url = $this->get_current_permalink();
		if( isset($check_payment['alternate_profilepage']) && !empty($check_payment['alternate_profilepage']) && empty($cancel_url) ){
			$cancel_url = $this->get_page_uri( $check_payment['alternate_profilepage'] );
		}
		$data .= '<input type="button" class="piereg_cancel_profile_edit_btn" onclick="location.replace(\''.($cancel_url).'\');" value="'.__("Cancel","piereg").'" />';
		$data .= '<input name="pie_submit_update" type="submit" value="'.__($this->field['text'],"piereg").'" />';
		$data .= '</div>';
		return $data;
	}
	
	function made_conditional_logic(){
		if( isset($this->field['conditional_logic']) && $this->field['conditional_logic'] == "1" && $this->piereg_pro_is_activate) {
			true;
		}
	}
	function editProfile($user){
		
		$profile_fields_data = "";
		$update = get_option(OPTION_PIE_REGISTER);
		$profile_fields_data .= $this->addFormData();
		$profile_fields_data .= '<ul id="pie_register">';
		
		if( is_array($this->data) && count($this->data) > 0 )
		{
			//echo "<pre>"; print_r($this->data); echo "</pre>";
			foreach($this->data as $this->field)
			{
				if((isset($this->field['show_in_profile']) and $this->field['show_in_profile'] == 0) && !is_admin()){
					continue;
				}
				elseif($this->field['type']=="" || $this->field['type'] == "form" || $this->field['type'] == "html"){
					continue;
				}
				elseif($this->field['type']=="math_captcha"){
					continue;
				}
				
				$this->field_status = "";
				
				// check status on fields having conditional logics
				if( (isset($this->field['conditional_logic']) && $this->field['conditional_logic']==1) && !isset($this->field['notification']) )
				{
					$conitional_logic_status = $this->checkIfConditionApplied( $this->field , "pie_");
					
					// if condtion apply on show fields
					if( (isset($this->field['field_status']) && $this->field['field_status']==1) && $conitional_logic_status == false) {
						$this->field_status = 'style="display:none;"';
					} else if( (isset($this->field['field_status']) && $this->field['field_status']==1) && $conitional_logic_status == true ){
						$this->field_status = 'style="display:block;"';
					}
					
					// if condtion apply on hide fields
					if( (isset($this->field['field_status']) && $this->field['field_status']==0) && $conitional_logic_status == true) {
						$this->field_status = 'style="display:none;"';
					} else if( (isset($this->field['field_status']) && $this->field['field_status']==0) && $conitional_logic_status == false){
						$this->field_status = 'style="display:block;"';
					}
					
				}
					
				$this->name 	= $this->createFieldName($this->field['type']."_".((isset($this->field['id']))?$this->field['id']:""));
				$this->slug 	= $this->createFieldName("pie_".$this->field['type']."_".((isset($this->field['id']))?$this->field['id']:""));
				$this->id 		= $this->createFieldID();
				$this->no		= (isset($this->field['id'])) ? $this->field['id'] : "";
				
				//We don't need to print li for hidden field
				if ($this->field['type'] == "hidden")
				{
					$profile_fields_data .= $this->addHiddenField();
					continue;
				}
				
				/*$this->field_status = "";
				if( (isset($this->field['conditional_logic']) && $this->field['conditional_logic'] == "1") && $this->piereg_pro_is_activate){
					if( ($this->field['field_status'] == "1" and $this->field['selected_field'] != "") and ($this->field['field_rule_operator'] != "" and $this->field['conditional_value'] != "") ){
						$this->field_status = 'style="display:none;"';
					}else{						
						$this->field_status = 'style="display:block;"';
					}
				}*/				
				
				$topclass = "";
				if($this->label_alignment=="top")
					$topclass = "label_top"; 
				
				$class_x		= (isset($this->field['id'])) ? $this->field['id'] : "";
				$profile_fields_data .= '<li class="fields pageFields_'.$this->pages.' '.$topclass.' piereg_li_'.$class_x.'" '.$this->field_status.'>';
				
				//Printting Field
				switch($this->field['type']) :
					case 'text' :								
					case 'website' :
						$profile_fields_data .= $this->addTextField();
					break;				
					case 'username' :
						$profile_fields_data .= $this->addUsername();
					break;
					case 'password' :
						$profile_fields_data .= $this->addPassword();
					break;
					case 'email' :
						$profile_fields_data .= $this->addEmail();
					break;
					case 'textarea':
						$profile_fields_data .= $this->addTextArea();
					break;
					case 'dropdown':
					case 'multiselect':
						$profile_fields_data .= $this->addDropdown();
					break;
					case 'number':
						$profile_fields_data .= $this->addNumberField();
					break;
					case 'radio':
					case 'checkbox':
						$profile_fields_data .= $this->addCheckRadio();
					break;
					case 'name':
						$profile_fields_data .= $this->addName();
					break;
					case 'time':
						$profile_fields_data .= $this->addTime();
					break;
					case 'upload':
						$profile_fields_data .= $this->addUpload();
					break;
					case 'profile_pic':
						$profile_fields_data .= $this->addProfilePic();
					break;
					case 'address':
						$profile_fields_data .= $this->addAddress();
					break;
					case 'phone':
						$profile_fields_data .= $this->addPhone();
					break;
					/*
						*	Just For Two Way Login
					*/
					case 'two_way_login_phone':
						include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
						$twilio_option = get_option("pie_register_twilio");
		 				$plugin_status = get_option('piereg_api_manager_addon_Twilio_activated');
						$pie_register_base = new PieReg_Base();
						if( is_plugin_active("pie-register-twilio/pie-register-twilio.php") && isset($twilio_option["enable_twilio"]) && $twilio_option["enable_twilio"] == 1 && $pie_register_base->piereg_pro_is_activate && $plugin_status == "Activated" ){
							$this->name = "piereg_two_way_login_phone";
							$this->slug = "piereg_two_way_login_phone";
							$profile_fields_data .= $this->addPhone();
						}
					break;
					case 'date':
						$profile_fields_data .= $this->addDate();
					break;
					case 'list':
						$profile_fields_data .= $this->addList();
					break;			
					case 'default':
						$profile_fields_data .= $this->addDefaultField();
					break;
					case "sectionbreak":
						$profile_fields_data .= $this->addSectionBreak();
					break;
					case 'submit':
						$profile_fields_data .= $this->addSubmit();
					break;	
				endswitch;
				
				$profile_fields_data .= '</li>';
			}
		}
		
		$profile_fields_data .= '</ul>';
		return $profile_fields_data;	
	}
}