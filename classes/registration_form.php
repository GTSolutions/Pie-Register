<?php
require_once('base.php');
class Registration_form extends PieReg_Base
{
	var $id;
	var $name;
	var $field;	
	var $data;
	var $label_alignment;
	var $pages;
	
	function __construct()	
	{
		$this->data = $this->getCurrentFields();
		$this->label_alignment = ((isset($this->data['form']['label_alignment']))?$this->data['form']['label_alignment']:"left");
		$this->pages = 1;
		//add_action("Add_payment_option",		array($this,"Add_payment_option"));
		//add_action("add_payment_method_script", array($this,"add_payment_method_script"));
		
	}
	function addFormData()
	{
		
		$data = "";
		
		$data .= '<div class="fieldset '.$this->data['form']['css'].'">';
		$data .= '<h2 id="piereg_pie_form_heading">'.$this->data['form']['label'].'</h2>';	
		$data .= '<p id="piereg_pie_form_desc" >'.$this->data['form']['desc'].'</p>';		
		
		$data .= '</div>';
		return $data;
	
	}
	function addDefaultField()
	{
		$data = "";
		$this->name = $this->field['field_name'];
		if($this->field['field_name']=="description")
		{
			$data .= '<textarea name="description" id="description" rows="5" cols="80">'.$this->getDefaultValue().'</textarea>';	
		}
		else
		{
			$data .= '<input id="'.$this->id.'" name="'.$this->field['field_name'].'" class="'.$this->addClass().'"  placeholder="'.(isset($this->field['placeholder'])?$this->field['placeholder']:"").'" type="text" value="'.$this->getDefaultValue().'" />';	
		}	
		return $data;
	}
	function addTextField()
	{
		return '<input id="'.$this->id.'" name="'.$this->name.'" class="'.$this->addClass().'"  '.$this->addValidation().'  placeholder="'.$this->field['placeholder'].'" type="text" value="'.$this->getDefaultValue().'" />';
	}
	function addHiddenField()
	{
		return '<input id="'.$this->id.'" name="'.$this->name.'"  type="hidden" value="'.$this->getDefaultValue().'" />';		
	}
	function addUsername($form_widget = false)
	{
		$formwidget = (isset($form_widget) && $form_widget == true)? '_widget' : '';
		return '<input id="username'.$formwidget.'" name="username" class="input_fields piereg_validate[required,username]" placeholder="'.$this->field['placeholder'].'" type="text" value="'.$this->getDefaultValue('username').'" data-errormessage-value-missing="'.((isset($this->field['validation_message']))?$this->field['validation_message']:"").'"  />';	
		
	}
	function addPassword($fromwidget)
	{
		$style = "";
		$data = "";
		if($fromwidget == true)
		{
			$this->id = $this->id."_widget";
		}
		
		if($this->label_alignment=="left")
			$style = 'class = "wdth-lft mrgn-lft"';
		
		
		$data .= '<input '; 
		
		if($this->field['show_meter']==1)
		{
			//$data .= 'onkeyup="passwordStrength(this.value)" ';
		}
		
		$data .= 'id="'.$this->id.'" name="password" class="'.$this->addClass("input_fields",array("minSize[8]")).'" placeholder="'.$this->field['placeholder'].'" type="password" data-errormessage-value-missing="'.((isset($this->field['validation_message']))?$this->field['validation_message']:"").'" data-errormessage-range-underflow="'.((isset($this->field['validation_message']))?$this->field['validation_message']:"").'" data-errormessage-range-overflow="'.((isset($this->field['validation_message']))?$this->field['validation_message']:"").'" autocomplete="off" />';
				
	
		$class = '';
		$fclass = '';
		
		$topclass = "";
		if($this->label_alignment=="top")
			$topclass = "label_top"; 
		//$widget = ( ($fromwidget)? ' pie_widget-2 #' : '' );
		$label2 = (isset($this->field['label2']) and !empty($this->field['label2']))? __($this->field['label2'],"piereg") : __("Confirm Password","piereg");
		
		$data .= '</div></li><li class="fields pageFields_'.$this->pages.' '.$topclass.'"><div class="fieldset"><label>'.$label2.'</label><input id="confirm_password_'.$this->id.'" name="confirm_password" type="password" data-errormessage-value-missing="'.((isset($this->field['validation_message']))?$this->field['validation_message']:"").'" data-errormessage-range-underflow="'.((isset($this->field['validation_message']))?$this->field['validation_message']:"").'" data-errormessage-range-overflow="'.((isset($this->field['validation_message']))?$this->field['validation_message']:"").'" class="input_fields '.$this->field['css'].' piereg_validate[required,equals['.$this->id.']]" placeholder="'.$this->field['placeholder'].'" autocomplete="off" />';	
		
			
		return $data;
			
	}	
	function addEmail($fromwidget)
	{
		$data = "";
		if($fromwidget == true)
		{
			$this->id = $this->id."_widget";
		}
		
		$data .='<input id="'.$this->id.'" name="e_mail" class="'.$this->addClass().'"  '.$this->addValidation().'  placeholder="'.$this->field['placeholder'].'" type="text" value="'.$this->getDefaultValue("e_mail").'" />';
		
		if(isset($this->field['confirm_email']))
		{
			$class = '';
			$fclass = '';
			
			$topclass = "";
			if($this->label_alignment=="top")
				$topclass = "label_top"; 	
		
			
			//$widget = ( ($fromwidget)? ' pie_widget-2 #' : '' );
			$label2 = (isset($this->field['label2']) and !empty($this->field['label2']))? __($this->field['label2'],"piereg") : __("Confirm E-mail","piereg");
			$data .= '</div></li><li class="fields pageFields_'.$this->pages.' '.$topclass .'"><div class="fieldset"><label>'.$label2.'</label><input  placeholder="'.$this->field['placeholder'].'" id="confirm_email_'.$this->id.'" '.$this->addValidation().' type="text" class="input_fields '.$this->field['css'].' piereg_validate[required,equals['.$this->id.']]" autocomplete="off">';
		}	
		return $data;
	}
	function addUpload()
	{
		return '<input id="'.$this->id.'" name="'.$this->name.'" class="'.$this->addClass().'"  '.$this->addValidation().' type="file" />';
	}
	function addProfilePicUpload()
	{
		return '<input id="'.$this->id.'" name="'.$this->name.'" class="'.$this->addClass("input_fields",array("funcCall[checkExtensions],ext[gif|GIF|jpeg|JPEG|jpg|JPG|png|PNG|bmp|BMP]")).'"  '.$this->addValidation().' type="file"  />';	
	}
	function addTextArea()
	{
		return '<textarea id="'.$this->id.'" name="'.$this->name.'" rows="'.$this->field['rows'].'" cols="'.$this->field['cols'].'"  class="'.$this->addClass("input_fields").'"  placeholder="'.$this->field['placeholder'].'">'.$this->getDefaultValue().'</textarea>';		
	}
	function addName()
	{
		$data = "";
		$data .= '<div class="fieldset"><label>'.__($this->field['label'],"piereg").'</label>';
		$data .= '<input value="'.$this->getDefaultValue('first_name').'" id="'.$this->id.'_firstname" name="first_name" class="'.$this->addClass().' input_fields" '.$this->addValidation().'  type="text"  />';
		
		$topclass = "";
		if($this->label_alignment=="top")
			$topclass = "label_top"; 					
	
		//$data .= '</div></li><li class="fields pageFields_'.$this->pages.' '.$topclass.'">';
			
		$label2 = (isset($this->field['label2']) and !empty($this->field['label2']))? __($this->field['label2'],"piereg") : __("Last Name","piereg");
		/*$data .= '<div class="fieldset"><label>'.$label2.'</label>';*/
		$data .= '</div><div class="fieldset fieldset_child"><label>'.$label2.'</label>';
		$data .= '<input value="'.$this->getDefaultValue('last_name').'" id="'.$this->id.'_lastname" name="last_name" class="'.$this->addClass().' input_fields" '.$this->addValidation().'  type="text"  /></div>';	
		return $data;
		
	}
	function addTime()
	{
		$data = "";
		$this->field['hours'] = TRUE;
		$name = $this->name;
		
		$time_this_values = $this->getDefaultValue($name);
		
		$data .= '<div class="piereg_time"><div class="time_fields"><input value="'.((isset($time_this_values["hh"]))?$time_this_values["hh"]:"").'" maxlength="2" id="hh_'.$this->id.'" name="'.$this->name.'[hh]" type="text"  class="'.$this->addClass().'"  '.$this->addValidation().'><label>'.__("HH","piereg").'</label></div>';
		$this->field['hours'] = FALSE;
		
		$this->field['mins'] = TRUE;
		$data .= '<span class="colon">:</span><div class="time_fields"><input value="'.((isset($time_this_values["mm"]))?$time_this_values["mm"]:"").'" maxlength="2" id="mm_'.$this->id.'" type="text" name="'.$this->name.'[mm]"  class="'.$this->addClass().'"  '.$this->addValidation().'><label>'.__("MM","piereg").'</label></div><div id="time_format_field_'.$this->id.'" class="time_fields"></div>';
		$this->field['mins'] = FALSE;
		
		if($this->field['time_type']=="12")
		{
			$time_format_val = (isset($time_this_values["time_format"]))?$time_this_values["time_format"]:"";
			$data .= '<div class="time_fields"><select name="'.$this->name.'[time_format]" >
				<option value="am" '; 
				$data .=($time_format_val == "am")?'selected=""':'';
				$data .='>'.__("AM","piereg").'</option>';
				$data .='<option value="pm"  ';
				$data .=($time_format_val == "pm")?'selected=""':'';
				$data .='>'.__("PM","piereg").'</option>
			</select></div>';
		}
		
		$data .= '</div>';
		return $data;
	}	
	function addDropdown()
	{
		$data = "";
		$multiple = "";
		$name = $this->name;
		$thispostedvalue = $this->getDefaultValue();
		
		if($this->field['type']=="multiselect")
		{
			$multiple 	= 'multiple';
			$name = $this->name."[]";
		}
		$data .= '<select '.$multiple.' id="'.$name.'" name="'.$name.'" class="'.$this->addClass("").'" '.$this->addValidation().'  >';
	
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
			{	for($a = 0 ; $a < sizeof($this->field['value']) ; $a++)
				{
					$selected = '';
					if(isset($this->field['selected']) && is_array($this->field['selected']) && in_array($a,$this->field['selected']))
					{
						$selected = 'selected="selected"';	
					}
					if(is_array($thispostedvalue)){
						foreach($thispostedvalue as $thissinglepostedval){
							
							if($thissinglepostedval == $this->field['value'][$a])
								$selected = 'selected="selected"';
							//$checked = 'checked="checked"';){
						}
					}
					elseif($thispostedvalue == $this->field['value'][$a]){
						$selected = 'selected="selected"';
					}
					//var_dump($this->field['value'][$a]);
					//echo($thispostedval." | ".$this->field['value'][$a]." | ".$selected)."<br />";
					if($this->field['value'][$a] !="" && $this->field['display'][$a] != "")
					$data .= '<option '.$selected.' value="'.$this->field['value'][$a].'">'.$this->field['display'][$a].'</option>';	
					}
			}
		$data .= '</select>';	
		return $data;
	}
	function addNumberField()
	{
		$data = "";
		$data .= '<input id="'.$this->id.'" name="'.$this->name.'" class="'.$this->addClass().'"  '.$this->addValidation().'  placeholder="'.$this->field['placeholder'].'" type="number" value="'.$this->getDefaultValue().'"' ;
		
		if(!empty($this->field['min']))
		$data .= 'min="'.$this->field['min'].'"';
		
		if(!empty($this->field['max']))
		$data .= 'max="'.$this->field['max'].'"';
		
		$data .= '/>';	
		return $data;
	}
	function addPhone()
	{		
		return '<input id="'.$this->id.'" class="'.$this->addClass().'"  '.$this->addValidation().' name="'.$this->name.'"  placeholder="'.((isset($field['placeholder']))?$field['placeholder']:"").'" type="text" value="'.$this->getDefaultValue().'" />';	
	}
	function addList()
	{
		$data = "";
		$width  = 85 /  $this->field['cols']; 
		$name = $this->name;
		
		$list_this_values = $this->getDefaultValue($name);
		
		for($a = 1 ,$c=0; $a <= $this->field['rows'] ; $a++,$c++)
		{
			if($a==1)
			{
				$data .= '<div class="'.$this->id.'_'.$a.' pie_list">';
				
				
				for($b = 1 ; $b <= $this->field['cols'] ;$b++)
				{
					$data .= '<input value="'.((isset($list_this_values[$c][$b-1]))?$list_this_values[$c][$b-1]:"").'" style="width:'.$width.'%;margin-right:2px;" type="text" name="'.$this->name.'['.$c.'][]" class="input_fields"> ';
				}
				if( ((int)$this->field['rows']) > 1)
				{
					$data .= ' <img src="'.plugins_url('pie-register').'/images/plus.png" onclick="addList('.$this->field['rows'].','.$this->field['id'].');" alt="add" /></div>';		
				}
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
					$data .= '<input value="'.((isset($list_this_values[$c][$b-1]))?$list_this_values[$c][$b-1]:"").'" style="width:'.$width.'%;margin-right:2px;" type="text" name="'.$this->name.'['.$c.'][]" class="input_fields">';
				}
				
					$data .= ' <img src="'.plugins_url('pie-register').'/images/minus.gif" onclick="removeList('.$this->field['rows'].','.$this->field['id'].','.$a.');" alt="add" /></div>';
				
				
			}
		}
		return $data;
	}
	function addHTML()
	{
		
		$data = '<div class="piereg-html-field-content" >';
		$data .= html_entity_decode($this->field['html']);
		$data .= '</div>';
		$data .= $this->addDesc();
		return $data;
		
		return html_entity_decode($this->field['html']);
	}
	function addSectionBreak()
	{
		$class = "";
		
		if($this->label_alignment == "left")
		$class .= "wdth-lft ";
		
		$class .= "sectionBreak";
		
		return '<div class="'.$class.'"></div>';	
	}
	function addCheckRadio()
	{
		$data = "";
		if(sizeof($this->field['value']) > 0)
		{
			$data .= '<div class="radio_wrap">';
			$thispostedvalue = $this->getDefaultValue();
			for($a = 0 ; $a < sizeof($this->field['value']) ; $a++)
			{
				$checked = '';
				
					
				if(isset($this->field['selected']) && is_array($this->field['selected']) && in_array($a,$this->field['selected']))
					$checked = 'checked="checked"';	
				else
					$checked = '';
				
				if(is_array($thispostedvalue)){
					foreach($thispostedvalue as $thissinglepostedval){
						if($thissinglepostedval == $this->field['value'][$a])
							$checked = 'checked="checked"';
						//$checked = 'checked="checked"';){
					}
				}
				
				
				//if(!empty($this->field['display'][$a]))
				//{	
				
					$dymanic_class = $this->field['type']."_".$this->field['id'];
					$data .= "<label>";
					$data .= $this->field['display'][$a];	
					$data .= "</label>";
					$data .= '<input '.$checked.' value="'.$this->field['value'][$a].'" type="'.$this->field['type'].'" '.((isset($multiple))?$multiple:"").' name="'.$this->name.'[]" class="'.$this->addClass("input_fields").' radio_fields" '.$this->addValidation().' data-map-field-by-class="'.$dymanic_class.'" >';
					
					
				//}
			}
			$data .= "</div>";		
		}
		return $data;
	}
	function addAddress()
	{
		$address_values = $this->getDefaultValue($this->name);
		$data = "";
		$data .= '<div class="address_main">';
		$data .= '<div class="address">
		  <input type="text" name="'.$this->name.'[address]" id="'.$this->id.'" class="'.$this->addClass().'"  '.$this->addValidation().' value="'.((isset($address_values['address']))?$address_values['address']:"").'">
		  <label>'.__("Street Address","piereg").'</label>
		</div>';
		
		 //if(!$this->field['hide_address2'])
		 if(empty($this->field['hide_address2']))
		 {
		
			$data .= '<div class="address">
			  <input type="text" name="'.$this->name.'[address2]" id="address2_'.$this->id.'"  class="input_fields" value="'.((isset($address_values['address2']))?$address_values['address2']:"").'">
			  <label>'.__("Address Line 2","piereg").'</label>
			</div>';
		 }
		
		$data .= '<div class="address">
		  <div class="address2">
			<input type="text" name="'.$this->name.'[city]" id="city_'.$this->id.'" class="'.$this->addClass("input_fields",array("custom[alphabetic]")).'"  '.$this->addValidation().' value="'.((isset($address_values['city']))?$address_values['city']:"").'">
			<label>'.__("City","piereg").'</label>
		  </div>';
		
		
		 //if(!$this->field['hide_state'])
		 if(empty($this->field['hide_state']))
		 {
			 	if($this->field['address_type'] == "International")
				{
					$data .= '<div class="address2"  >
					<input type="text" name="'.$this->name.'[state]" id="state_'.$this->id.'" class="'.$this->addClass("input_fields",array("custom[alphabetic]")).'"  '.$this->addValidation().' value="'.((isset($address_values['state']))?$address_values['state']:"").'">
					<label>'.__("State / Province / Region","piereg").'</label>
				 	 </div>';		
				}
				else if($this->field['address_type'] == "United States")
				{
				  $us_states = get_option("pie_us_states");
				  $selectedoption = ($address_values['state'])?$address_values['state']:$this->field['us_default_state'];
				  $options 	= $this->createDropdown($us_states,$selectedoption);	
				 
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
					$selectedoption = ($address_values['state'])?$address_values['state']:$this->field['canada_default_state'];
				  	$options 	= $this->createDropdown($can_states,$selectedoption);
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
		<input id="zip_'.$this->id.'" name="'.$this->name.'[zip]" type="text" class="'.$this->addClass().'"  '.$this->addValidation().' value="'.((isset($address_values['zip']))?$address_values['zip']:"").'">
		<label>'.__("Zip / Postal Code","piereg").'</label>
		 </div>';	 
		
		
		 if($this->field['address_type'] == "International")
		 {
			 $countries = get_option("pie_countries");
			 $selectedoption = (isset($address_values['country']) && $address_values['country'])?$address_values['country']:$this->field['default_country'];		 
			 $options 	= $this->createDropdown($countries,$selectedoption);  
			 $data .= '<div  class="address2" >
					<select id="country_'.$this->id.'" name="'.$this->name.'[country]" class="'.$this->addClass("").'"   '.$this->addValidation().'>
                    <option value="">'.__("Select Country","piereg").'</option>
					'. $options .'
					 </select>
					<label>'.__("Country","piereg").'</label>
		  		</div>';
		 }
		 
		 
		$data .= '</div>';
		$data .= '</div>';
		return $data;
	}	
	function addDate()
	{			
		$data = "";
		$date_this_values = array();
		$date_this_values = $this->getDefaultValue($this->name);
		if(is_array($date_this_values)){
			$date_this_values['date']['mm']="";
			$date_this_values['date']['dd']="";
			$date_this_values['date']['yy']="";
		}
		$startingDate = $this->field['startingDate'];
		$endingDate = $this->field['endingDate'];
		
		
		if($this->field['date_type'] == "datefield")
		{
			if($this->field['date_format']=="mm/dd/yy")
			{
			
			$data .= '<div class="piereg_time date_format_field">
				  <div class="time_fields">
					<input id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" maxlength="2" type="text" class="'.$this->addClass("input_fields",array("custom[month]")).'" '.$this->addValidation().' value="'.((isset($date_this_values['date']['mm']))?$date_this_values['date']['mm']:"").'">
					<label>'.__("MM","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" maxlength="2"  type="text" class="'.$this->addClass("input_fields",array("custom[day]")).'" '.$this->addValidation().' value="'.((isset($date_this_values['date']['dd']))?$date_this_values['date']['dd']: "").'">
					<label>'.__("DD","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" maxlength="4"  type="text" class="'.$this->addClass("input_fields",array("custom[year],max[".$endingDate."],min[".$startingDate."]")).'" '.$this->addValidation().' value="'.((isset($date_this_values['date']['yy']))?$date_this_values['date']['yy']:"").'">
					<label>'.__("YYYY","piereg").'</label>
				  </div>
				</div>';
			} 
			else if($this->field['date_format']=="yy/mm/dd" || $this->field['date_format']=="yy.mm.dd")
			{
				$data .= '<div class="piereg_time date_format_field">
				 <div class="time_fields">
					<input id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" maxlength="4"  type="text" class="'.$this->addClass("input_fields",array("custom[year],max[".$endingDate."],min[".$startingDate."]")).'" value="'.((isset($date_this_values['date']['yy']))?$date_this_values['date']['yy']:"").'">
					<label>'.__("YYYY","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" maxlength="2" type="text" class="'.$this->addClass("input_fields",array("custom[month]")).'" '.$this->addValidation().' value="'.((isset($date_this_values['date']['mm']))?$date_this_values['date']['mm']:"").'">
					<label>'.__("MM","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" maxlength="2"  type="text" class="'.$this->addClass("input_fields",array("custom[day]")).'" value="'.((isset($date_this_values['date']['dd']))?$date_this_values['date']['dd']:"").'">
					<label>'.__("DD","piereg").'</label>
				  </div>				  
				</div>';	
			}
			else if($this->field['date_format']=="dd/mm/yy" || $this->field['date_format']=="dd-mm-yy" || $this->field['date_format']=="dd.mm.yy")
			{
				$data .= '<div class="piereg_time date_format_field">
				  <div class="time_fields">
					<input id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" maxlength="2"  type="text" class="'.$this->addClass("input_fields",array("custom[day]")).'" value="'.((isset($date_this_values['date']['dd']))?$date_this_values['date']['dd']:"").'">
					<label>'.__("DD","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" maxlength="2" type="text" class="'.$this->addClass("input_fields",array("custom[month]")).'" '.$this->addValidation().' value="'.((isset($date_this_values['date']['mm']))?$date_this_values['date']['mm']:"").'">
					<label>'.__("MM","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" maxlength="4"  type="text" class="'.$this->addClass("input_fields",array("custom[year],max[".$endingDate."],min[".$startingDate."]")).'" value="'.((isset($date_this_values['date']['yy']))?$date_this_values['date']['yy']:"").'">
					<label>'.__("YYYY","piereg").'</label>
				  </div>
				</div>';	
			}
			else
			{
				$data .= '<div class="piereg_time date_format_field">
				 <div class="time_fields">
					<input id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" maxlength="2"  type="text" class="'.$this->addClass("input_fields",array("custom[day]")).'" value="'.((isset($date_this_values['date']['dd']))?$date_this_values['date']['dd']:"").'">
					<label>'.__("DD","piereg").'</label>
				  </div>	
				 <div class="time_fields">
					<input id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" maxlength="4"  type="text" class="'.$this->addClass("input_fields",array("custom[year],max[".$endingDate."],min[".$startingDate."]")).'" value="'.((isset($date_this_values['date']['yy']))?$date_this_values['date']['yy']:"").'">
					<label>'.__("YYYY","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" maxlength="2" type="text" class="'.$this->addClass("input_fields",array("custom[month]")).'" '.$this->addValidation().'" value="'.((isset($date_this_values['date']['mm']))?$date_this_values['date']['mm']:"").'">
					<label>'.__("MM","piereg").'</label>
				  </div>				  			  
				</div>';	
			}
		}
		else if($this->field['date_type'] == "datepicker")
		{
		
						
			$data .=	'<div class="piereg_time date_format_field">
			  <input id="'.$this->id.'" name="'.$this->name.'[date][]" readonly="readonly" type="text" class="'.$this->addClass().' date_start" title="'.$this->field['date_format'].'" value="';
			$data .=	(isset($date_this_values['date'][0]))?$date_this_values['date'][0] : "";
			$data .=	'">';
			  
			 $data .= '<input id="'.$this->id.'_format" type="hidden"  value="'.((isset($this->field['date_format']))?$this->field['date_format']:"").'">';
			 $data .= '<input id="'.$this->id.'_firstday" type="hidden"  value="'.((isset($this->field['firstday']))?$this->field['firstday']:"").'">';
			
			 $data .= '<input id="'.$this->id.'_startdate" type="hidden"  value="'.((isset($this->field['startdate']))?$this->field['startdate']:"").'">';
			  
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
					<select id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" class="'.$this->addClass("").'">
					  <option value="">'.__("Month","piereg").'</option>';
					  for($a=1;$a<=12;$a++){
						  if(isset($date_this_values['date']['mm']) && $date_this_values['date']['mm'] == $a)
						  	$sel = ' selected=""';
						  else
							$sel = '';
						  $data .= '<option value="'.$a.'" '.$sel.'>'.__($a,"piereg").'</option>';
					  }
					  $data .= '
					</select>
				  </div>
				  <div class="time_fields">
					<select id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" class="'.$this->addClass("").'">
					  <option value="">'.__("Day","piereg").'</option>';
					  for($a=1;$a<=31;$a++){
						  if(isset($date_this_values['date']['dd']) && $date_this_values['date']['dd'] == $a)
						  	$sel = ' selected=""';
						  else
						  $sel = '';	
						  $data .= '<option value="'.$a.'" '.$sel.'>'.__($a,"piereg").'</option>';
					  }
					  $data .= '
					</select>
				  </div>
				  <div class="time_fields">
					<select id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" class="'.$this->addClass("").' piereg_select_year">
					  <option value="" data-empty-vlue="true">'.__("Year","piereg").'</option>'; 
  					  for($a=((int)$endingDate);$a>=(((int)$startingDate));$a--){
					  //for($a=((int)date("Y") + 100);$a>=(((int)date("Y"))-100);$a--){
						  if(isset($date_this_values['date']['yy']) && $date_this_values['date']['yy'] == $a)
						  	$sel = ' selected=""';
						  else
						  $sel = '';	
						  $data .= '<option value="'.$a.'" '.$sel.'>'.__($a,"piereg").'</option>';
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
  					  for($a=((int)$endingDate);$a>=(((int)$startingDate));$a--){
					  //for($a=((int)date("Y"));$a>=(((int)date("Y"))-100);$a--){
						  if(isset($date_this_values['date']['yy']) && $date_this_values['date']['yy'] == $a)
						  	$sel = ' selected=""';
						  else
						  $sel = '';	
						  $data .=  '<option value="'.$a.'" '.$sel.'>'.__($a,"piereg").'</option>';
					  }
					  $data .=  '
					</select>
				  </div>
				  <div class="time_fields">
					<select id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" class="'.$this->addClass("").'">
					  <option value="">'.__("Month","piereg").'</option>';
					  for($a=1;$a<=12;$a++){
						  if(isset($date_this_values['date']['mm']) && $date_this_values['date']['mm'] == $a)
						  	$sel = ' selected=""';
						  else
						  $sel = '';	
						  $data .=  '<option value="'.$a.'" '.$sel.'>'.__($a,"piereg").'</option>';
					  }
					  $data .=  '
					</select>
				  </div>
				  <div class="time_fields">
					<select id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" class="'.$this->addClass("").'">
					  <option value="">'.__("Day","piereg").'</option>';
					  for($a=1;$a<=31;$a++){
						  if(isset($date_this_values['date']['dd']) && $date_this_values['date']['dd'] == $a)
						  	$sel = ' selected=""';
						  else
						  $sel = '';	
						  $data .=  '<option value="'.$a.'" '.$sel.'>'.__($a,"piereg").'</option>';
					  }
					  $data .=  '
					</select>
				  </div>				 
				</div>';
			
			}
			else
			{
				$data .= '<div class="piereg_time date_format_field">
				
				  
				  <div class="time_fields">
					<select id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" class="'.$this->addClass("").'">
					  <option value="">'.__("Day","piereg").'</option>';
					  for($a=1;$a<=31;$a++){
						  if(isset($date_this_values['date']['dd']) && $date_this_values['date']['dd'] == $a)
						  	$sel = ' selected=""';
						  else
						  $sel = '';	
						  $data .=  '<option value="'.$a.'" '.$sel.'>'.__($a,"piereg").'</option>';
					  }
					  $data .=  '
					</select>
				  </div>	
				  <div class="time_fields">
					<select id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" class="'.$this->addClass("").'">
					  <option value="">'.__("Month","piereg").'</option>';
					  for($a=1;$a<=12;$a++){
						  if(isset($date_this_values['date']['mm']) && $date_this_values['date']['mm'] == $a)
						  	$sel = ' selected=""';
						  else
						  $sel = '';	
						  $data .=  '<option value="'.$a.'" '.$sel.'>'.__($a,"piereg").'</option>'; 
					  }
					  $data .=  '
					</select>
				  </div>
				  	 <div class="time_fields">
					<select id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" class="'.$this->addClass("").'">
					  <option value="">'.__("Year","piereg").'</option>'; 
  					  for($a=((int)$endingDate);$a>=(((int)$startingDate));$a--){
					  //for($a=((int)date("Y"));$a>=(((int)date("Y"))-100);$a--){
						  if(isset($date_this_values['date']['yy']) && $date_this_values['date']['yy'] == $a)
						  	$sel = ' selected=""';
						  else
						  $sel = '';	
						  $data .=  '<option value="'.$a.'" '.$sel.'>'.__($a,"piereg").'</option>';
					  }
					  $data .=  '
					</select>
				  </div>			 
				</div>';	
			}			
		}
		return $data;
	}
	function addInvitationField()
	{
			return '<input id="'.$this->id.'" name="invitation" class="'.$this->addClass().'"  placeholder="'.$this->field['placeholder'].'" type="text" value="'.$this->getDefaultValue().'" />';		
	}	
		
	function createFieldName($text)
	{
		return $this->getMetaKey($text);			
	}
	function createFieldID()
	{
		return "field_".$this->field['id'];	
	}
	function getDefaultValue($name="")
	{
		if($name != "")
		{
			$this->name = $name;	
		}
		if(isset($_POST[$this->name]))
		{
			return $_POST[$this->name];	
		}
		return (isset($this->field['default_value']))?$this->field['default_value']:"";
	}
	function addDesc()
	{
		if(!empty($this->field['desc']))
		{
			//return '<span class="desc">'.$this->field['desc'].'</span>';
			//return '<p class="desc">'.$this->field['desc'].'</p>';
			//since 2.0.10
			return '<p class="desc">'.html_entity_decode($this->field['desc']).'</p>';
		}
	}
	function addLabel()
	{
		if($this->field['type'] == "html" && $this->field['label'] == ""){
			return "";
		}
		elseif($this->field['type']=="name" && $this->field['name_format']=="normal")
		{
			return "";
		}		
					
		$topclass = "";
		if($this->label_alignment=="top")
			$topclass = "label_top";
		
		return '<label class="'.$topclass .'" for="'.$this->name.'">'.__($this->field['label'],"piereg").'</label>';
	}
	function addClass($default = "input_fields",$val = array())
	{
		$class = $default." ".(isset($this->field['css'])?$this->field['css']:"");
		
		if(isset($this->field['required']) && $this->field['required'])
		{
			$val[] = "required";		
		}
		
		if(isset($this->field['length']) && intval($this->field['length']) > 0 )
		{
			$val[] = "maxSize[".intval($this->field['length'])."]";
		}
		
		
		if	((isset($this->field['validation_rule']) && $this->field['validation_rule']=="number" ) || $this->field['type']=="number")
		{
			$val[] = "custom[number]";		
		}
		else if(isset($this->field['validation_rule']) && $this->field['validation_rule']=="alphanumeric")
		{
			$val[] = "custom[alphanumeric]";		
		}
		else if((isset($this->field['validation_rule']) && $this->field['validation_rule']  =="email" ) || $this->field['type']=="email")
		{
			$val[] = "custom[email]";		
		}
		else if((isset($this->field['validation_rule']) && $this->field['validation_rule']=="website") || $this->field['type']=="website")
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
			$val[] = "minSize[2]";
			$val[] = "maxSize[2]";
			$val[] = "min[0]";
			
			if($this->field['hours']==TRUE)
			{
				if($this->field['time_type']=="12")
				{
					$val[] = "max[12]";
				}
				else
				{
					$val[] = "max[23]";	
				}
			}
			else if($this->field['mins']==TRUE)
			{
				$val[] = "max[59]";	
			}
				
		}
		else if($this->field['type']=="upload" && explode(",",$this->field['file_types']) > 0)
		{
			if(!empty($this->field['file_types']))
			{
				$val[] = "funcCall[checkExtensions]";	
				$val[] = "ext[".str_replace(array(","," "),array("|",""),$this->field['file_types'])."]";
			}
		}
		
		if(sizeof($val) > 0)
		{
			$val = " piereg_validate[".implode(",",$val)."]";
			$class .= $val;	
		}
		
		return $class;	
	}
	function addValidation()
	{
				
		
		if((isset($this->field['required']) && $this->field['required']) && !empty($this->field['validation_message']) && isset($this->field['validation_message']))
		{
			$val[] = 'data-errormessage-value-missing="'.$this->field['validation_message'].'"';
		}
		
		
		if(isset($this->field['validation_rule']))
		{
			if(
				$this->field['validation_rule']=="number" || 
				$this->field['type']=="number" || $this->field['validation_rule']=="alphanumeric" || 
				$this->field['validation_rule']=="email" || $this->field['type']=="email" || 
				$this->field['validation_rule']=="website" || $this->field['type']=="website" || 
				$this->field['type']=="phone" || $this->field['type']=="date")
			{
				$val[] = 'data-errormessage-custom-error="'.((isset($this->field['validation_message']))?$this->field['validation_message']:"").'"';
			}		
		}
		else if($this->field['type']=="time")
		{
			$val[] = 'data-errormessage-custom-error="'.((isset($this->field['validation_message']))?$this->field['validation_message']:"").'"';		
			$val[] = 'data-errormessage-range-underflow="'.((isset($this->field['validation_message']))?$this->field['validation_message']:"").'"';	
			$val[] = 'data-errormessage-range-overflow="'.((isset($this->field['validation_message']))?$this->field['validation_message']:"").'"';
		}
		
		if(isset($val) && sizeof($val) > 0)
		{
			return implode(" ",$val);			
		}
		
		
	}
	
	function addCaptcha()
	{
		$data = "";
		$settings  	=  get_option("pie_register_2");
		$publickey		= $settings['captcha_publc'] ;
		 
		if($publickey)
		{
			$captcha_skin = (isset($this->field['recaptcha_skin']) && !empty($this->field['recaptcha_skin']))?$this->field['recaptcha_skin']:"red";
			$data .= '<script type="text/javascript">
				 var RecaptchaOptions = {
					theme : "'.$captcha_skin.'"
				 };
				 </script>';
			$data .= '<div id="recaptcha_widget_div">';
				require_once(PIEREG_DIR_NAME.'/recaptchalib.php');
			$data .= recaptcha_get_html($publickey);
			$data .= '</div>';
		}
		return $data;
	}
	
	function addMath_Captcha($piereg_widget = false)
	{	
		$operator = rand(0,1);
		////1 for add(+)
		////0 for subtract(-)
		$result = 0;
		if($operator == 1){	
			$start = rand(1,9);
			$end = rand(5,20);
			$result = $start + $end;
			$operator = "+";
		}
		else{
			$start = rand(50,30);
			$end = rand(5,20);
			$result = $start - $end;
			$operator = "-";
		}
			
		$result1 = $result + 12;
		$result2 = $result + 786;
		$result3 = $result - 5;
		$result1 = base64_encode($result1);
		$result2 = base64_encode($result2);
		$result3 = base64_encode($result3);
		$data = "";
		
		$data .='
		<script type="text/javascript">';
		if($piereg_widget == true){
			/*$data .= 'document.cookie="piereg_math_captcha_registration_widget="+dummy_array;';*/
			$data .= 'document.cookie="piereg_math_captcha_registration_widget='.$result1."|".$result2."|".$result3.'";';
		}
		else{
			/*$data .= 'document.cookie="piereg_math_captcha_registration="+dummy_array;';*/
			$data .= 'document.cookie="piereg_math_captcha_registration='.$result1."|".$result2."|".$result3.'";';
		}
		$data .= '</script>';
		
		
		$field_id = "";
		if($piereg_widget == true){
			$data .= '<div id="pieregister_math_captha_widget" class="piereg_math_captcha"></div>';
			$data .= '<input id="'.$this->id.'" type="text" data-errormessage-value-missing="'.((isset($this->field['validation_message']))?$this->field['validation_message']:"").'" data-errormessage-range-underflow="'.((isset($this->field['validation_message']))?$this->field['validation_message']:"").'" data-errormessage-range-overflow="'.((isset($this->field['validation_message']))?$this->field['validation_message']:"").'" class="'.$this->addClass().'" placeholder="'.$this->field['placeholder'].'" style="width:auto;margin-top:9px;" name="piereg_math_captcha_widget"/>';
			$field_id = "#pieregister_math_captha_widget";
		}
		else{
			$data .= '<div id="pieregister_math_captha" class="piereg_math_captcha"></div>';
			$data .= '<input id="'.$this->id.'" type="text" data-errormessage-value-missing="'.((isset($this->field['validation_message']))?$this->field['validation_message']:"").'" data-errormessage-range-underflow="'.((isset($this->field['validation_message']))?$this->field['validation_message']:"").'" data-errormessage-range-overflow="'.((isset($this->field['validation_message']))?$this->field['validation_message']:"").'" class="'.$this->addClass().'" placeholder="'.$this->field['placeholder'].'" style="width:auto;margin-top:9px;" name="piereg_math_captcha"/>';
			$field_id = "#pieregister_math_captha";
		}
		
		
		
		$image_name = rand(0,10);
		
		$color[0] = 'rgba(0, 0, 0, 0.6)';
		$color[1] = 'rgba(153, 31, 0, 0.9)';
		$color[2] = 'rgba(64, 171, 229,0.8)';
		$color[3] = 'rgba(0, 61, 21, 0.8)';
		$color[4] = 'rgba(0, 0, 204, 0.7)';
		$color[5] = 'rgba(0, 0, 0, 0.5)';
		$color[6] = 'rgba(198, 81, 209, 1.0)';
		$color[7] = 'rgba(0, 0, 999, 0.5)';
		$color[8] = 'rgba(0, 0, 0, 0.5)';
		$color[9] = 'rgba(0, 0, 0, 0.5)';
		$color[10] = 'rgba(255, 63, 143, 0.9)';
		
		$data .= '
         <script type="text/javascript">
			jQuery("'.$field_id.'").css({
				"background" : "url('.plugins_url('pie-register').'/images/math_captcha/'.$image_name.'.png)",
				"color"		 : "'.$color[$image_name].'"
			});
			jQuery("'.$field_id.'").html("'.$start." ".$operator." ".$end . ' = ");
		 </script>';
		 
		 return $data;
	}
	function addSubmit()
	{
		$data = "";
		
		$data .= '<div class="fieldset piereg_submit_button">';
		if($this->pages > 1)
		{
			$data .= '<input class="pie_prev" name="pie_prev" id="pie_prev_'.$this->pages.'" type="button" value="'.__("Previous","piereg").'" />';
			$data .= '<input id="pie_prev_'.$this->pages.'_curr" name="page_no" type="hidden" value="'.($this->pages-1).'" />';						
		}
		$check_payment = get_option("pie_register_2");
		
		//if($check_payment["enable_paypal"] == 1 ||  $check_payment["enable_authorize_net"] == 1)
		if($this->check_enable_payment_method() == "true")
		{
			do_action("add_select_payment_script"); // Add script
			$data .= "<label>".__("Select Payment","piereg")."</label>";
			$data .= '<select name="select_payment_method" id="select_payment_method">';
			$data .= '<option value="">'.__("Select","piereg").'</option>';
			do_action('Add_payment_option');
			$data .= "</select>";
			do_action("get_payment_content_area");
			$data .= '<input name="pie_submit" type="submit" value="'.$this->field['text'].'" />';	
		}
		else
		{
			$data .= '<input name="pie_submit" type="submit" value="'.__($this->field['text'],"piereg").'" />';
		}
		if($this->field['reset']==1)
		{
			$data .= '<input name="pie_reset" type="reset" value="'.__($this->field['reset_text'],"piereg").'" />';
		}
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
		else
			$cl = '';
		
		$data .= '<input id="'.$cl.'total_pages" class="piereg_regform_total_pages" name="pie_total_pages" type="hidden" value="'.$this->countPageBreaks().'" />';
		if($this->pages > 1){
			
			$data .= '<input id="'.$cl.'pie_prev_'.$this->pages.'_curr" name="page_no" type="hidden" value="'.($this->pages-1).'" />';		
			
			if($this->field['prev_button']=="text")
			{
				$data .= '<input class="pie_prev" name="pie_prev" id="'.$cl.'pie_prev_'.$this->pages.'" type="button" value="'.__($this->field['prev_button_text'],"piereg").'" />';	
			}
			else if($this->field['prev_button']=="url")
			{
				$data .= '<img class="pie_prev" name="pie_prev" id="'.$cl.'pie_prev_'.$this->pages.'" src="'.$this->field['prev_button_url'].'"  />';		
			}
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
		return $data;	
	}
	function printFields($fromwidget = false)
	{
		$pie_reg_fields = "";
		$update = get_option( 'pie_register_2' );	
		//wp_enqueue_script( 'jquery' );
		/*if($update['outputcss']==1)//Output Form CSS
		{
			wp_register_style( 'prefix-style', $this->pluginURL("css/front.css") );
			wp_enqueue_style( 'prefix-style' );	
		}*/
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
				if($this->field['type']=="form"){
					continue;
				}
				$this->name 	= $this->createFieldName($this->field['type']."_".((isset($this->field['id']))?$this->field['id']:""));
				$this->id 		= $this->name;
				//We don't need to print li for hidden field
				if ($this->field['type'] == "hidden")
				{
					$pie_reg_fields .= $this->addHiddenField();
					continue;
				}
				$topclass = "";
				if($this->label_alignment=="top")
					$topclass = "label_top";
				
				$pie_reg_fields .= '<li class="fields pageFields_'.$this->pages.' '.$topclass.'">';
	
				//When to add label
				/*switch($this->field['type']) :				
					case 'text' :								
					case 'website' :*/							
					/*case 'username' :*/
					/*case 'password':			
					case 'email' :
					case 'textarea':
					case 'dropdown':
					case 'multiselect':
					case 'number':
					case 'radio':
					case 'checkbox':
					case 'html':								
					case 'time':				
					case 'upload':			
					case 'profile_pic':			
					case 'address':				
					case 'captcha':				
					case 'phone':				
					case 'date':				
					case 'list':								
					case 'sectionbreak':				
					case 'default':
					case 'invitation':
						$pie_reg_fields .= '<div class="fieldset">'.$this->addLabel();
					break;							
				endswitch;*/
	
				if($this->field['type'] == "pagebreak")
				{
					$pie_reg_fields .= $this->addPagebreak($fromwidget);	
					$this->pages++;			
				}
				//Printting Field
				switch($this->field['type']) :				
					case 'text' :								
					case 'website' :
						$pie_reg_fields .= '<div class="fieldset">'.$this->addLabel();
						$pie_reg_fields .= $this->addTextField();
						$pie_reg_fields .= $this->addDesc().'</div>';
					break;				
					case 'username' :
						$pie_reg_fields .= '<div class="fieldset">'.$this->addLabel().$this->addUsername($fromwidget).$this->addDesc().'</div>';
					break;
					case 'password' :
						$pie_reg_fields .= '<div class="fieldset">'.$this->addLabel();
						$pie_reg_fields .= $this->addPassword($fromwidget);
						$pie_reg_fields .= $this->addDesc().'</div>';
					break;
					case 'email' :
						$pie_reg_fields .= '<div class="fieldset">'.$this->addLabel();
						$pie_reg_fields .= $this->addEmail($fromwidget);
						$pie_reg_fields .= $this->addDesc().'</div>';
					break;
					case 'textarea':
						$pie_reg_fields .= '<div class="fieldset">'.$this->addLabel();
						$pie_reg_fields .= $this->addTextArea();
						$pie_reg_fields .= $this->addDesc().'</div>';
					break;
					case 'dropdown':
					case 'multiselect':
						$pie_reg_fields .= '<div class="fieldset">'.$this->addLabel();
						$pie_reg_fields .= $this->addDropdown();
						$pie_reg_fields .= $this->addDesc().'</div>';
					break;
					case 'number':
						$pie_reg_fields .= '<div class="fieldset">'.$this->addLabel();
						$pie_reg_fields .= $this->addNumberField();		
						$pie_reg_fields .= $this->addDesc().'</div>';	
					break;
					case 'radio':
					case 'checkbox':
						$pie_reg_fields .= '<div class="fieldset">'.$this->addLabel();
						$pie_reg_fields .= $this->addCheckRadio();
						$pie_reg_fields .= $this->addDesc().'</div>';
					break;
					case 'html':
						$pie_reg_fields .= '<div class="fieldset">'.$this->addLabel();
						$pie_reg_fields .= $this->addHTML();
						$pie_reg_fields .= $this->addDesc().'</div>';
					break;
					case 'name':
						$pie_reg_fields .= $this->addName();
						$pie_reg_fields .= '<div>'.$this->addDesc().'</div>';
					break;
					case 'time':
						$pie_reg_fields .= '<div class="fieldset">'.$this->addLabel();
						$pie_reg_fields .= $this->addTime();
						$pie_reg_fields .= $this->addDesc().'</div>';
					break;
					case 'upload':
						$pie_reg_fields .= '<div class="fieldset">'.$this->addLabel();
						$pie_reg_fields .= $this->addUpload();
						$pie_reg_fields .= $this->addDesc().'</div>';
					break;
					case 'profile_pic':
						$pie_reg_fields .= '<div class="fieldset">'.$this->addLabel();
						$pie_reg_fields .= $this->addProfilePicUpload();
						$pie_reg_fields .= $this->addDesc().'</div>';
					break;
					case 'address':
						$pie_reg_fields .= '<div class="fieldset">'.$this->addLabel();
						$pie_reg_fields .= $this->addAddress();
						$pie_reg_fields .= $this->addDesc().'</div>';
					break;
					case 'captcha':
						if( isset($update['captcha_publc']) && !empty($update['captcha_publc']) ){
							$pie_reg_fields .= '<div class="fieldset">'.$this->addLabel();
							$pie_reg_fields .= $this->addCaptcha();
							$pie_reg_fields .= $this->addDesc().'</div>';
						}
					break;
					case 'math_captcha':
						global $piereg_math_captcha_register,$piereg_math_captcha_register_widget;
						if($piereg_math_captcha_register != true && $fromwidget == false){
							$pie_reg_fields .= '<div class="fieldset">'.$this->addLabel();
							$pie_reg_fields .= $this->addMath_Captcha($fromwidget);
							$pie_reg_fields .= $this->addDesc().'</div>';
							$piereg_math_captcha_register = true;
						}elseif($piereg_math_captcha_register_widget != true && $fromwidget == true){
							$pie_reg_fields .= '<div class="fieldset">'.$this->addLabel();
							$pie_reg_fields .= $this->addMath_Captcha($fromwidget);
							$pie_reg_fields .= $this->addDesc().'</div>';
							$piereg_math_captcha_register_widget = true;
						}
					break;
					case 'phone':
						$pie_reg_fields .= '<div class="fieldset">'.$this->addLabel();
						$pie_reg_fields .= $this->addPhone();
						$pie_reg_fields .= $this->addDesc().'</div>';
					break;
					case 'date':
						$pie_reg_fields .= '<div class="fieldset">'.$this->addLabel();
						$pie_reg_fields .= $this->addDate();	
						$pie_reg_fields .= $this->addDesc().'</div>';		
					break;
					case 'list':
						$pie_reg_fields .= '<div class="fieldset">'.$this->addLabel();
						$pie_reg_fields .= $this->addList();
						$pie_reg_fields .= $this->addDesc().'</div>';
					break;
					case 'submit':
						$pie_reg_fields .= $this->addSubmit();
					break;				
					case 'sectionbreak':
						$pie_reg_fields .= '<div class="fieldset">'.$this->addLabel();
						$pie_reg_fields .= $this->addSectionBreak();
						$pie_reg_fields .= $this->addDesc().'</div>';
					break;	
					case 'default':
						$pie_reg_fields .= '<div class="fieldset">'.$this->addLabel();
						$pie_reg_fields .= $this->addDefaultField();
						$pie_reg_fields .= $this->addDesc().'</div>';
					break;
					case 'invitation':
						$pie_reg_fields .= '<div class="fieldset">'.$this->addLabel();
						$pie_reg_fields .= $this->addInvitationField();
						$pie_reg_fields .= $this->addDesc().'</div>';
					break;							
				endswitch;
	
				/*switch($this->field['type']) :				
					case 'text' :						
					case 'website' :*/							
					/*case 'username' :*/
					/*case 'password':*/			
					/*case 'email' :
					case 'textarea':
					case 'dropdown':
					case 'multiselect':
					case 'number':
					case 'radio':
					case 'checkbox':
					case 'html':								
					case 'time':				
					case 'upload':			
					case 'profile_pic':
					case 'address':				
					case 'captcha':				
					case 'phone':				
					case 'date':				
					case 'list':		
					case 'default':
					case 'invitation':				
					$pie_reg_fields .= $this->addDesc();
					$pie_reg_fields .= '</div>';					
					break;							
				endswitch;*/
				
				
				/*
					*	Add Restrict Password strength meater since 2.0.13
				*/
				if($this->field['type'] == "password" )
				{
					$widget = (isset($fromwidget) && $fromwidget == true)? '_widget' : '';
					/*?>
                    <script type="text/javascript">
						var password_strength_meter<?php echo $widget; ?> = <?php echo ((isset($this->field['restrict_strength']))?intval($this->field['restrict_strength']):0); ?>;
					</script>
                    <?php*/
					$pie_reg_fields .= '<input type="hidden" id="password_strength_meter_1" data-id="1" value="'.((isset($this->field['restrict_strength']))?intval($this->field['restrict_strength']):0).'" />';
					//Weak Password	
					$strength_message = ((isset($this->field['strength_message']) && !empty($this->field['strength_message']))?__($this->field['strength_message'],"piereg"):__("Weak Password","piereg"));
                    $pie_reg_fields .= '<span id="password_strength_message_1" style="display:none;">'.$strength_message.'</span>';
				}
				
				
				$pie_reg_fields .=  '</li>';
				if($this->field['type'] == "password" && $this->field['show_meter']==1)
				{		
					$pie_reg_fields .=  '<li class="fields pageFields_'.$this->pages.' '.$topclass.'">';
					//OLD PASSWORD STRENGHT METER
					/*$pie_reg_fields .=  "<div id='password_meter' class='fieldset' ".$style.">";
					$pie_reg_fields .=  '<label id="piereg_passwordDescription">'.__("Password not entered","piereg").'</label>
					<div id="piereg_passwordStrength" class="piereg_strength0">&nbsp;</div>';
					$pie_reg_fields .=  "</div>";*/
					
					//NEW PASSWORD STRENGHT METER
					$widget = (isset($fromwidget) && $fromwidget == true)? '_widget' : '';
					$widget_style = (isset($fromwidget) && $fromwidget == true)? 'display: none;' : 'visibility:hidden;';
					$pie_reg_fields .=  '<div id="password_meter" class="fieldset" '.((isset($style))?$style:"").'>';
					$pie_reg_fields .=  '<label style="'.$widget_style.'">'.__("Password not entered","piereg").'</label>';
					//$pie_reg_fields .=  '<div id="piereg_passwordStrength'.$widget.'" class="piereg_pass" >'.__("Strength Indicator","piereg").'</div>';
					$pie_reg_fields .=  '<div id="piereg_passwordStrength'.$widget.'" class="piereg_pass" >'.__($update['pass_strength_indicator_label'],"piereg").'</div>';
					$pie_reg_fields .=  '</div>';
					$pie_reg_fields .=  '</li>';
				}
			}
		}
		return $pie_reg_fields;
	}
	function validateRegistration($errors)
	{
		if(!is_wp_error($errors))
		$errors = new WP_Error();
		$piereg 	= get_option( 'pie_register_2' );
		/*
			*	Sanitizing post data
		*/
		$this->piereg_sanitize_post_data( ( (isset($_POST) && !empty($_POST))?$_POST : array() ) );
		
		global $wpdb;
		
		do_action("pieregister_registration_validation_before");
		
		$_POST['username'] = preg_replace('/\s+/', '', strtolower($_POST['username']));
		if ( !isset($_POST['username']) && empty( $_POST['username'] ) && !validate_username($_POST['username']) )
		{
			$errors->add( "username" , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_Invalid_Username",__('Invalid Username','piereg' )));
		}
		else if ( username_exists( $_POST['username'] ) )
		{
			$errors->add( "username" , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_Username_already_exists",__('Username already exists','piereg' )));
		}		
		
		if ( !isset($_POST['e_mail']) || empty( $_POST['e_mail'] ) || !filter_var($_POST['e_mail'],FILTER_VALIDATE_EMAIL) )
		{
			$errors->add( "email" , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_Invalid_Email_address",__('Invalid E-mail address','piereg' )));
		}
		else if ( email_exists( $_POST['e_mail'] ) )
		{
			$errors->add( "email" , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_Email_address_already_exists",__('E-mail address already exists','piereg' )));
					
		}
		if(isset($_POST['password'], $_POST['confirm_password'] ) && !empty($_POST['password']) && $_POST['password'] != $_POST['confirm_password'] )
		{
			$errors->add( "password" , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_invalid_password",__('Invalid Password','piereg' )));
		}
		if(is_array($this->data)){
			 foreach($this->data as $field)
			 {
			$slug 				= $this->createFieldName($field['type']."_".(isset($field['id'])?$field['id']:""));
			if($field['type']=="username" || $field['type']=="password"){
				  $slug  = $this->createFieldName($field['type']);
			}
			if($field['type']=="email"){
				  $slug  = $this->createFieldName("e_mail");
			}
			$field_name			= ((isset($_POST[$slug]))?$_POST[$slug]:"");
			$required 			= ((isset($field['required']))?$field['required']:"");
			$rule				= ((isset($field['validation_rule']))?$field['validation_rule']:"");
			
			$validation_message	= (!empty($field['validation_message']) ? $field['validation_message'] : $field['label'] .__(" is required","piereg"));
			
			//Handling File Field
			if($field['type']=="profile_pic")
			{
				$field_name = $_FILES[$slug]['name'];
				if($_FILES[$slug]['name'] != ''){
					$result = $this->piereg_validate_files($_FILES[$slug]['name'],array("gif","GIF","jpeg","JPEG","jpg","JPG","png","PNG","bmp","BMP"));
					if(!$result){
						$errors->add( $slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_Invalid_File_Type_In_Profile_Picture",__('Invalid File Type In Profile Picture.','piereg' )));
					}
				}
			}
			elseif($field['type']=="upload"){
				$field_name = $_FILES[$slug]['name'];
				if($_FILES[$slug]['name'] != '' and $field['file_types'] != ""){
					$filter_array = stripcslashes($field['file_types']);
					$filter_array = explode(",",$filter_array);
					$result = $this->piereg_validate_files($_FILES[$slug]['name'],$filter_array);
					if(!$result){
						$errors->add( $slug , apply_filters("piereg_invalid_file",'<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_Invalid_File_Type",__('Invalid File Type','piereg' ))));
					}
				}
			}
			else if($field['type']=="invitation"  && $piereg["enable_invitation_codes"]=="1")
			{
				$field_name = $code = $_POST['invitation'];
				if($required != "" || $_POST['invitation'] != "")
				{
					$codetable	= $this->codeTable();				
					$codes = $wpdb->get_results( "SELECT * FROM $codetable where name = '$code' and status = 1");
					if(is_array($codes)){
						foreach($codes as $c)	
						{
							$times_used = $c->count;
							$usage 		= $c->code_usage;
						}
					}
					if(count($codes) != 1)
					{
						$errors->add( $slug , apply_filters("piereg_invalid_invitaion_code",'<strong>'.ucwords(__('error','piereg')).'</strong>: '.__('Invalid Invitation Code','piereg' )));		
							
					}
					elseif($times_used >= $usage and $usage != 0)
					{
						$errors->add( $slug , apply_filters("piereg_invitaion_code_expired",'<strong>'.ucwords(__('error','piereg')).'</strong>: '.__('Invitation Code has expired','piereg' )));
					}
				}
			}
			else if($field['type']=="captcha")
			{
				$settings  		=  get_option("pie_register_2");
		 		$privatekey		= $settings['captcha_private'] ;
				if( !empty( $privatekey ) ){
					require_once(PIEREG_DIR_NAME.'/recaptchalib.php');
					
					$resp = recaptcha_check_answer ($privatekey,
													$_SERVER["REMOTE_ADDR"],
													$_POST["recaptcha_challenge_field"],
													$_POST["recaptcha_response_field"]);
					
					if (!$resp->is_valid) {				 
					  $errors->add('recaptcha_mismatch',"<strong>".ucwords(__('error','piereg'))."</strong>: ". apply_filters("piereg_Invalid_Security_Code",__("Invalid Security Code", 'piereg')));
					}
				}
			
			}
			else if($field['type']=="math_captcha")
			{
				if(isset($_POST['piereg_math_captcha']))
				{
					$piereg_cookie_array =  $_COOKIE['piereg_math_captcha_registration'];
					/*$piereg_cookie_array = explode(",",$piereg_cookie_array);*/
					$piereg_cookie_array = explode("|",$piereg_cookie_array);
					$cookie_result1 = (intval(base64_decode($piereg_cookie_array[0])) - 12);
					$cookie_result2 = (intval(base64_decode($piereg_cookie_array[1])) - 786);
					$cookie_result3 = (intval(base64_decode($piereg_cookie_array[2])) + 5);
					$field_name = $_POST['piereg_math_captcha'];
					if( ($cookie_result1 == $cookie_result2) && ($cookie_result3 == $_POST['piereg_math_captcha'])){
					}
					else{
						$errors->add('math_captcha_mismatch',"<strong>".ucwords(__('error','piereg'))."</strong>: ". apply_filters("piereg_Invalid_Security_Code",__("Invalid Math Captcha", 'piereg')));
					}
				}
				elseif(isset($_POST['piereg_math_captcha_widget']))
				{
					$piereg_cookie_array =  $_COOKIE['piereg_math_captcha_registration_widget'];
					/*$piereg_cookie_array = explode(",",$piereg_cookie_array);*/
					$piereg_cookie_array = explode("|",$piereg_cookie_array);
					$cookie_result1 = (intval(base64_decode($piereg_cookie_array[0])) - 12);
					$cookie_result2 = (intval(base64_decode($piereg_cookie_array[1])) - 786);
					$cookie_result3 = (intval(base64_decode($piereg_cookie_array[2])) + 5);
					$field_name = $_POST['piereg_math_captcha_widget'];
					if( ($cookie_result1 == $cookie_result2) && ($cookie_result3 == $_POST['piereg_math_captcha_widget'])){
					}
					else{
						$errors->add('math_captcha_mismatch',"<strong>".ucwords(__('error','piereg'))."</strong>: ". apply_filters("piereg_Invalid_Security_Code",__("Invalid Math Captcha", 'piereg')));
					}
				}
				else{
					$errors->add('math_captcha_mismatch',"<strong>".ucwords(__('error','piereg'))."</strong>: ". apply_filters("piereg_Invalid_Security_Code",__("Invalid Math Captcha", 'piereg')));
				}
			}
			else if($field['type']=="name")
			{
				$field_name	= $_POST["first_name"];	
			}
			
			if( (!isset($field_name) || empty($field_name)) && $required)
			{
				
				$errors->add( $slug , "<strong>". __(ucwords("error"),"piereg").":</strong> " .$validation_message );				
			}
			else if($rule=="number")
			{
				if(!is_numeric($field_name))
				{
					$errors->add( $slug , "<strong>". __(ucwords("error"),"piereg").":</strong> ".$field['label'] .apply_filters("piereg_field_must_contain_only_numbers",__("Field must contain only numbers" ,"piereg")));		
				}	
			}
			else if($rule=="alphanumeric")
			{
				if(! preg_match("/^([a-z0-9])+$/i", $field_name))
				{
					$errors->add( $slug ,"<strong>". __(ucwords("error"),"piereg").":</strong> ".$field['label'] .apply_filters("piereg_field_may__alpha_numeric_characters",__("Field may only contain alpha-numeric characters"  ,"piereg")));		
				}	
			}	
			else if($rule=="email")
			{
				if(!filter_var($field_name,FILTER_VALIDATE_EMAIL))
				{
					$errors->add( $slug ,"<strong>". __(ucwords("error"),"piereg").":</strong> ".$field['label'] .apply_filters("piereg_field_must_contain_valid_email",__("Field must contain a valid email address" ,"piereg")));		
				}	
			}	
			else if($rule=="website")
			{
				if(!filter_var($field_name,FILTER_VALIDATE_URL))
				{
					$errors->add( $slug ,"<strong>". __(ucwords("error"),"piereg").":</strong> ".$field['label'] .apply_filters("piereg_must_be_a_valid_URL",__("Must be a valid URL" ,"piereg")));
				}	
			}				 
			
		 }
		}
		do_action("pieregister_registration_validation_after");
		return $errors;
	}
	function addUser($user_id)
	{
		global $wpdb;
		if(is_array($this->data)){
			foreach($this->data as $field)	
			{
			//Some form fields which we can't save like paypal, submit,formdata
			if(!isset($field['meta']))
			{
				if($field['type']=="default")
				{
					/* && $field['field_name'] != 'url'*/
					$slug 				= $field['field_name'];				
					$value				= $_POST[$slug];
					update_user_meta($user_id, $slug, $value);	
				}
				else if($field['type']=="invitation")
				{
					$prefix		= $wpdb->prefix."pieregister_";
					$codetable	= $prefix."code";				
					$codes 		= $wpdb->query( "update $codetable set count = count + 1 where name = '".$_POST['invitation']."' and status = 1");
					
					update_user_meta($user_id, "invite_code", $_POST['invitation']);			
				}
				else if($field['type']=="name")
				{
					$slug 				= "first_name";				
					$value				= $_POST[$slug];
					update_user_meta($user_id, $slug, $value);	
					
					$slug 				= "last_name";				
					$value				= $_POST[$slug];
					update_user_meta($user_id, $slug, $value);	
				}
				else if($field['type']=="profile_pic")
				{
					$slug 			= $this->createFieldName($field['type']."_".$field['id']);
					$field_name		= $_POST[$slug];
					$this->pie_profile_pictures_upload($user_id,$field,$slug);
				}
				else if($field['type']=="upload")
				{
					$slug 			= $this->createFieldName($field['type']."_".$field['id']);
					$field_name		= $_POST[$slug];
					$this->pie_upload_files($user_id,$field,$slug);
				}
				else
				{
					$slug 				= $this->createFieldName($field['type']."_".$field['id']);
					$field_name			= $_POST[$slug];
					update_user_meta($user_id, "pie_".$slug, $field_name);
				}
			}
		}
		}
	} 
	function countPageBreaks()
	{
		$pages = 1;
		if(count($this->data) > 0):
			foreach($this->data as $field)
			{
				if($field['type']=="pagebreak")
					$pages++;	
			}
		endif;
		return $pages ;
	}					
}