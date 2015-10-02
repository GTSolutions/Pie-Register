<?php
require_once('base.php');
class Edit_form extends PieReg_Base
{
	var $id;
	var $name;
	var $field;	
	var $data;
	var $label_alignment;
	var $user;
	var $user_id;
	var $error;
	var	$pie_success;
	var	$pie_error;
	var	$pie_error_msg;
	var	$pie_success_msg;
	
	var $pages;
	
	function __construct($user)	
	{
		$this->data = $this->getCurrentFields();
		$this->user = $user;
		$this->user_id = $user->ID;
		
	}
	function addFormData()
	{
		//echo '<h1 id="piereg_pie_form_heading">'.$this->field['label'].'</h1>';	
		//echo '<p id="piereg_pie_form_desc" class="'.$this->addClass("").'" >'.$this->field['desc'].'</p>';
		//$this->user->data->display_name
		$this->label_alignment = $this->field['label_alignment'];
		return '<h1 id="piereg_pie_form_heading">'.__("Profile Page","piereg").'</h1>';
	}
	function addDefaultField()
	{
		$data = "";
		//$val = get_usermeta($this->user->data->ID , $this->field['field_name']);
		$val = get_user_meta($this->user->data->ID , $this->field['field_name'],true);
		if($this->field['field_name']=="url")
		{
			$val = $this->user->data->user_url; 		
		}
		
		if($this->field['field_name']=="description")
		{
			$data .= '<textarea name="description" id="description" rows="5" cols="80">'.$val.'</textarea>';	
		}
		else
		{
			$data .= '<input id="'.$this->id.'" name="'.$this->field['field_name'].'" class="'.$this->addClass().'"  placeholder="'.((isset($this->field['placeholder']))?$this->field['placeholder']:"").'" type="text" value="'.$val.'" />';	
		}	
		return $data;
	}
	function addTextField()
	{
		//$val = get_usermeta($this->user->data->ID , $this->slug);
		$val = get_user_meta($this->user->data->ID , $this->slug,true);
		return '<input id="'.$this->id.'" name="'.$this->name.'" class="'.$this->addClass().'"  placeholder="'.((isset($this->field['placeholder']))?$this->field['placeholder']:"").'" type="text" value="'.$val.'" />';	
	}
	function addHiddenField()
	{
		//$val = get_usermeta($this->user->data->ID , $this->slug);
		$val = get_user_meta($this->user->data->ID , $this->slug,true);
		return '<input id="'.$this->id.'" name="'.$this->name.'"  type="hidden" value="'.$val.'" />';		
	}
	function addUsername()
	{
		return '<input class="input_fields" type="text" value="'.$this->user->data->user_login.'" disabled="disabled" readonly="readonly" />';
		return '<span>'.$this->user->data->user_login.'</span>';
	}
	function addPassword()
	{
		$data = "";	
		
		$data .= '<label>'.__("Old Password","piereg").'</label><input id="old_password_'.$this->id.'" type="password" class="input_fields" value="" name="old_password" autocomplete="off"></div></li>';
		
		
		$data .= '<li class="fields pageFields_'.$this->pages.' '.$topclass.'"><div class="fieldset">'.$this->addLabel().'<input id="'.$this->id.'" name="password" class="'.$this->addClass("input_fields",array("minSize[8]")).'" placeholder="'.$this->field['placeholder'].'" type="password" value="" autocomplete="off" />';	
		
			$class = '';
			$fclass = '';
			
			$topclass = "";
			if($this->label_alignment=="top")
				$topclass = "label_top"; 
			$label2 = ((isset($this->field['label2']))?$this->field['label2']:"Confirm Password");
			$data .= '</div></li><li class="fields pageFields_'.$this->pages.' '.$topclass.'"><div class="fieldset"><label>'.__($label2,"piereg").'</label><div '.$fclass.'><input id="confirm_password_'.$this->id.'" type="password" class="input_fields piereg_validate[equals['.$this->id.']]" placeholder="'.$this->field['placeholder'].'" value="" name="confirm_password" autocomplete="off">';
		return $data;	
	}	
	function addEmail()
	{
		return '<input id="'.$this->id.'" name="e_mail" class="'.$this->addClass().'"  placeholder="'.$this->field['placeholder'].'" type="text" value="'.$this->user->data->user_email.'" autocomplete="off" />';
		
	}
	function addUpload()
	{
		$data = "";
		//$val = get_usermeta($this->user->data->ID , $this->slug);
		$val = get_user_meta($this->user->data->ID , $this->slug,true);
		$data .= '<input id="'.$this->id.'" name="'.$this->name.'" class="'.$this->addClass().'" type="file"  />';
		$data .= '<input id="'.$this->id.'" name="'.$this->name.'_hidden" value="'.$val.'" type="hidden"  />';
		$data .= '<a href="'.$val.'" target="_blank">'.basename($val).'</a>';
		return $data;
	}
	function addProfilePic()
	{
		$data = "";
		//$val = get_usermeta($this->user->data->ID , $this->slug);
		$val = get_user_meta($this->user->data->ID , $this->slug,true);
		
		$data .= '<input id="'.$this->id.'" name="'.$this->name.'" class="'.$this->addClass().' piereg_validate[funcCall[checkExtensions],ext[gif|GIF|jpeg|JPEG|jpg|JPG|png|PNG|bmp|BMP]]" type="file"  />';
		$data .= '<input id="'.$this->id.'" name="'.$this->name.'_hidden" value="'.$val.'" type="hidden"  />';
		$ext = (trim(basename($val)))? $val." Not Found" : "Profile Pictuer Not Found";
		
		if(trim($val) != "")
			$imgPath = ('<img src="'.$val.'" style="max-width:150px;" alt="'.__($imgPath,"piereg").'" />');
		elseif(function_exists("get_avatar"))
			$imgPath = get_avatar($this->user->data->ID,75);
		else
			$imgPath = ('<img src="'.plugins_url("images/userImage.png",dirname(__FILE__)).'" style="max-width:150px;" alt="'.__($imgPath,"piereg").'" />');
			
		$data .= '<div class="piereg_show_profile_pic">'.$imgPath.'</div>';
		return $data;
	}
	function addTextArea()
	{		
		//$val = get_usermeta($this->user->data->ID , $this->slug);
		$val = get_user_meta($this->user->data->ID , $this->slug,true);
		return '<textarea id="'.$this->id.'" name="'.$this->name.'" rows="'.$this->field['rows'].'" cols="'.$this->field['cols'].'"  class="'.$this->addClass("").'"  placeholder="'.$this->field['placeholder'].'">'.$val.'</textarea>';		
	}
	function addName()
	{
		$data = "";
		//$val = get_usermeta($this->user->data->ID , "first_name");
		$val = get_user_meta($this->user->data->ID , "first_name",true);
		
		$data .= '<div class="fieldset"><label>'.__($this->field['label'],"piereg").'</label>';		
		$data .= '<input id="'.$this->id.'_firstname" value="'.$val .'" name="first_name" class="'.$this->addClass().' input_fields" type="text"  />';
		
		//$val = get_usermeta($this->user->data->ID , "last_name");
		$val = get_user_meta($this->user->data->ID , "last_name",true);
			
		$topclass = "";
		if($this->label_alignment=="top")
			$topclass = "label_top"; 					
		$data .= '</div></li><li class="fields pageFields_'.$this->pages.' '.$topclass.'">';
		$data .= '<div class="fieldset"><label>'.__($this->field['label2'],"piereg").'</label>';
		$data .= '<input id="'.$this->id.'_lastname" value="'.$val .'" name="last_name" class="'.$this->addClass().' input_fields"  type="text"  /></div>';		
		return $data;
	}
	function addTime()
	{
		$data = "";
		//$val = get_usermeta($this->user->data->ID , $this->slug);
		$val = get_user_meta($this->user->data->ID , $this->slug,true);
		$data .= '<div class="piereg_time">
					<div class="time_fields">
						<input maxlength="2" id="hh_'.$this->id.'" name="'.$this->name.'[hh]" type="text"  class="'.$this->addClass().'" value="'.((isset($val['hh']))?$val['hh'] : "").'">
						<label>HH</label>
					</div>
					<span class="colon">:</span>
					<div class="time_fields">
						<input maxlength="2" id="mm_'.$this->id.'" type="text" name="'.$this->name.'[mm]"  class="'.$this->addClass().'" value="'.((isset($val['mm']))?$val['mm']:"").'">
						<label>MM</label>
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
		return $data;
	}	
	function addDropdown()
	{
		$data = "";
		$multiple = "";
		$name = $this->name."[]";
		//$val = get_usermeta($this->user->data->ID , $this->slug);
		$val = get_user_meta($this->user->data->ID , $this->slug,true);
		
		if($this->field['type']=="multiselect")
		{
			$multiple 	= 'multiple';			
		}		
		$data .= '<select '.$multiple.' id="'.$name.'" name="'.$name.'" class="'.$this->addClass("").'" >';
	
		if($this->field['list_type']=="country")
		{
			 $countries = get_option("pie_countries");			 
			$data .= $this->createDropdown($countries,$val[0]);			   	
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
		{	for($a = 0 ; $a < sizeof($this->field['value']) ; $a++)
			{
				$selected = '';
				if(is_array($val) && in_array($this->field['value'][$a],$val))
				{
					$selected = 'selected="selected"';	
				}				
				if($this->field['value'][$a] !="" && $this->field['display'][$a] != "")
				$data .= '<option '.$selected.' value="'.$this->field['value'][$a].'">'.$this->field['display'][$a].'</option>';	
			}		
		}	
		$data .= '</select>';
		return $data;
	}
	function addNumberField()
	{
		//$val = get_usermeta($this->user->data->ID , $this->slug);
		$val = get_user_meta($this->user->data->ID , $this->slug,true);
		return '<input id="'.$this->id.'" name="'.$this->name.'" class="'.$this->addClass().'"  placeholder="'.$this->field['placeholder'].'" min="'.$this->field['min'].'" max="'.$this->field['max'].'" type="number" value="'.$val.'" />';	
	}
	function addPhone()
	{		
		//$val = get_usermeta($this->user->data->ID , $this->slug);
		$val = get_user_meta($this->user->data->ID , $this->slug,true);
		return '<input id="'.$this->id.'" class="'.$this->addClass().'" name="'.$this->name.'" class="input_fields"  placeholder="'.((isset($field['placeholder']))?$field['placeholder']:"").'" type="text" value="'.$val.'" />';	
	}
	function addList()
	{
		$data = "";
		//$val = get_usermeta($this->user->data->ID , $this->slug);
		$val = get_user_meta($this->user->data->ID , $this->slug,true);
		if(!is_array($val))
		$val = array();
		//$val = get_usermeta($this->user->data->ID);
		$width  = 85 /  $this->field['cols']; 
		
		for($a = 1 ,$c=0; $a <= $this->field['rows'] ; $a++,$c++)
		{
								
				$data .= '<div class="'.$this->id.'_'.$a.' pie_list">';
				$row  = "";
				for($b = 1 ; $b <= $this->field['cols'] ;$b++)
				{
					$data .= '<input style="width:'.$width.'%;margin-right:2px;padding:0px;" type="text" name="'.$this->name.'['.$c.'][]" class="input_fields" value="'.((isset($val[$c][$b-1]))?$val[$c][$b-1]:"").'"> ';
					//$row 	.= $value[$a][$b-1];
				}	
				
				
				$data .= '</div>';		
		}
		return $data;
	}
	function addHTML()
	{
		return html_entity_decode($this->field['html']);
	}
	function addSectionBreak()
	{
		return '<div style="width:100%;float:left;border: 1px solid #aaaaaa;margin-top:25px;"></div>';	
	}
	function addCheckRadio()
	{
		$data = "";
		//$val = get_usermeta($this->user->data->ID , $this->slug);
		$val = get_user_meta($this->user->data->ID , $this->slug,true);
		if(sizeof($this->field['value']) > 0)
		{
			$data .= '<div class="radio_wrap">';
			for($a = 0 ; $a < sizeof($this->field['value']) ; $a++)
			{
				$checked = '';
				if(is_array($val) && in_array($this->field['value'][$a],$val))
				{
					$checked = 'checked="checked"';	
				}				
				
				if(!empty($this->field['display'][$a]))
				{	
					
					$data .= "<label>";
					$data .= $this->field['display'][$a];	
					$data .= "</label>";
					$data .= '<input '.$checked.' value="'.$this->field['value'][$a].'" type="'.$this->field['type'].'" '.((isset($multiple))?$multiple:"").' name="'.$this->name.'[]" class="'.$this->addClass("").' radio_fields" >';
					
					
				}
			}
			$data .= "</div>";		
		}
		return $data;
	}
	function addAddress()
	{
		$data = "";
		//$val = get_usermeta($this->user->data->ID , $this->slug);
		$val = get_user_meta($this->user->data->ID , $this->slug,true);
		$data .= '<div class="address_main">';
		$data .= '<div class="address">
		  <input type="text" name="'.$this->name.'[address]" id="'.$this->id.'" class="'.$this->addClass().'" value="'.((isset($val['address']))?$val['address']:"").'">
		  <label>'.__("Street Address","piereg").'</label>
		</div>';
		
		 if(empty($this->field['hide_address2']))
		 {
		
			$data .= '<div class="address">
			  <input type="text" name="'.$this->name.'[address2]" id="address2_'.$this->id.'"  class="'.$this->addClass().'"  value="'.((isset($val['address2']))?$val['address2']:"").'">
			  <label>'.__("Address Line 2","piereg").'</label>
			</div>';
		 }
		
		$data .= '<div class="address">
		  <div class="address2">
			<input type="text" name="'.$this->name.'[city]" id="city_'.$this->id.'" class="'.$this->addClass("input_fields",array("custom[alphabetic]")).'"  value="'.((isset($val['city']))?$val['city']:"").'">
			<label>'.__("City","piereg").'</label>
		  </div>';
		
		
		 if(empty($this->field['hide_state']))
		 {
			 	if($this->field['address_type'] == "International")
				{
					$data .= '<div class="address2"  >
					<input type="text" name="'.$this->name.'[state]" id="state_'.$this->id.'" class="'.$this->addClass("input_fields",array("custom[alphabetic]")).'"  value="'.((isset($val['state']))?$val['state']:"").'">
					<label>'.__("State / Province / Region","piereg").'</label>
				 	 </div>';		
				}
				else if($this->field['address_type'] == "United States")
				{
				  $us_states = get_option("pie_us_states");
				  $options 	= $this->createDropdown($us_states,$val['state']);	
				 
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
				  	$options 	= $this->createDropdown($can_states,$val['state']);
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
		<input id="zip_'.$this->id.'" name="'.$this->name.'[zip]" type="text" class="'.$this->addClass().'" value="'.((isset($val['zip']))?$val['zip']:"").'">
		<label>'.__("Zip / Postal Code","piereg").'</label>
		 </div>';	 
		
		
		 if($this->field['address_type'] == "International")
		 {
			 $countries = get_option("pie_countries");			 
			 $options 	= $this->createDropdown($countries,((isset($val['country']))?$val['country']:""));  
			 $data .= '<div  class="address2" >
					<select id="country_'.$this->id.'" name="'.$this->name.'[country]" class="'.$this->addClass("").'">
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
		//$val = get_usermeta($this->user->data->ID , $this->slug);
		$val = get_user_meta($this->user->data->ID , $this->slug,true);
		//var_dump($val);
		$startingDate = $this->field['startingDate'];
		$endingDate = $this->field['endingDate'];
		if($this->field['date_type'] == "datefield")
		{
			//if(!is_array($val['date']['yy']) && strlen($val['date'][0]) == 10)
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
					<input id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" maxlength="2" type="text" class="'.$this->addClass("input_fields",array("custom[month]")).'" value="'.((isset($val['date']['mm']))?$val['date']['mm']:"").'">
					<label>'.__("MM","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" maxlength="2"  type="text" class="'.$this->addClass("input_fields",array("custom[day]")).'" value="'.((isset($val['date']['dd']))?$val['date']['dd']:"").'">
					<label>'.__("DD","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" maxlength="4"  type="text" class="'.$this->addClass("input_fields",array("custom[year]")).'" value="'.((isset($val['date']['yy']))?$val['date']['yy']:"").'">
					<label>'.__("YYYY","piereg").'</label>
				  </div>
				</div>';
			} 
			else if($this->field['date_format']=="yy/mm/dd" || $this->field['date_format']=="yy.mm.dd")
			{
				$data .= '<div class="time date_format_field">
				 <div class="time_fields">
					<input value="'.$val['date']['yy'].'" id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" maxlength="4"  type="text" class="'.$this->addClass("input_fields",array("custom[year]")).'">
					<label>'.__("YYYY","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input value="'.$val['date']['mm'].'" id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" maxlength="2" type="text" class="'.$this->addClass("input_fields",array("custom[month]")).'">
					<label>'.__("MM","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input value="'.$val['date']['dd'].'" id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" maxlength="2"  type="text" class="'.$this->addClass("input_fields",array("custom[day]")).'">
					<label>'.__("DD","piereg").'</label>
				  </div>				  
				</div>';	
			}
			else
			{
				if(!isset($val['date'])){
					$val = array('date'=>array());
				}elseif(!is_array($val['date'])){
					$val['date'] = array();
				}
				$data .= '<div class="piereg_time date_format_field">
				 <div class="time_fields">
					<input value="'.$val['date']['dd'].'" id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" maxlength="2"  type="text" class="'.$this->addClass("input_fields",array("custom[day]")).'">
					<label>'.__("DD","piereg").'</label>
				  </div>	
				 <div class="time_fields">
					<input value="'.$val['date']['yy'].'" id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" maxlength="4"  type="text" class="'.$this->addClass("input_fields",array("custom[year]")).'">
					<label>'.__("YYYY","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input value="'.$val['date']['mm'].'" id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" maxlength="2" type="text" class="'.$this->addClass("input_fields",array("custom[month]")).'">
					<label>'.__("MM","piereg").'</label>
				  </div>				  			  
				</div>';	
			}
		}
		else if($this->field['date_type'] == "datepicker")
		{
			if(isset($val['date']))
			if(isset($val['date']['yy']) && is_array($val['date']['yy']))
			{
				$val = 	$val['date']['yy']."-".$val['date']['mm']."-".$val['date']['dd'];
			}
			else
			{
				$val = 	$val['date'][0];	
			}	
			
			
				
				$data .=	'<div class="piereg_time date_format_field">
				  <input id="'.$this->id.'" name="'.$this->name.'[date][]" readonly="readonly" type="text" class="'.$this->addClass().' date_start" value="'.$val.'">';
				  
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
					  	$data .= '<option value="'.$a.'"';
						$data .= (isset($val['date']['mm']) && $val['date']['mm'] == $a)? 'selected="selected"' : "";
						$data .= '>'.__($a,"piereg").'</option>';
					  }
					  $data .= '
					</select>
				  </div>';
				  /*echo
				  '<div class="time_fields">
					<select id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" class="'.$this->addClass("").'">
					  <option value="">Day</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option>
					</select>
				  </div>';*/
				  
				  $data .=
				  '<div class="time_fields">
					<select id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" class="'.$this->addClass("").'">
					  <option value="">'.__("Day","piereg").'</option>';
					  for($a=1;$a<=31;$a++){
					  	$data .= '<option value="'.$a.'"';
						$data .= (isset($val['date']['dd']) && $val['date']['dd'] == $a)? 'selected="selected"' : "";
						$data .= '>'.__($a,"piereg").'</option>';
					  }
					$data .= '
					</select>
				  </div>';
				  
				  $data .= '
				  <div class="time_fields">
					<select id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" class="'.$this->addClass("").'">
					  <option value="">'.__("Year","piereg").'</option>';
					  for($a=((int)$endingDate);$a>=(((int)$startingDate));$a--){
					  	$data .= '<option value="'.$a.'"';
						$data .= (isset($val['date']['yy']) && $val['date']['yy'] == $a)? 'selected="selected"' : "";
						$data .= '>'.__($a,"piereg").'</option>';
					  }
					  /*echo '
					  <option value="2013">2013</option><option value="2012">2012</option><option value="2011">2011</option><option value="2010">2010</option><option value="2009">2009</option><option value="2008">2008</option><option value="2007">2007</option><option value="2006">2006</option><option value="2005">2005</option><option value="2004">2004</option><option value="2003">2003</option><option value="2002">2002</option><option value="2001">2001</option><option value="2000">2000</option><option value="1999">1999</option><option value="1998">1998</option><option value="1997">1997</option><option value="1996">1996</option><option value="1995">1995</option><option value="1994">1994</option><option value="1993">1993</option><option value="1992">1992</option><option value="1991">1991</option><option value="1990">1990</option><option value="1989">1989</option><option value="1988">1988</option><option value="1987">1987</option><option value="1986">1986</option><option value="1985">1985</option><option value="1984">1984</option><option value="1983">1983</option><option value="1982">1982</option><option value="1981">1981</option><option value="1980">1980</option><option value="1979">1979</option><option value="1978">1978</option><option value="1977">1977</option><option value="1976">1976</option><option value="1975">1975</option><option value="1974">1974</option><option value="1973">1973</option><option value="1972">1972</option><option value="1971">1971</option><option value="1970">1970</option><option value="1969">1969</option><option value="1968">1968</option><option value="1967">1967</option><option value="1966">1966</option><option value="1965">1965</option><option value="1964">1964</option><option value="1963">1963</option><option value="1962">1962</option><option value="1961">1961</option><option value="1960">1960</option><option value="1959">1959</option><option value="1958">1958</option><option value="1957">1957</option><option value="1956">1956</option><option value="1955">1955</option><option value="1954">1954</option><option value="1953">1953</option><option value="1952">1952</option><option value="1951">1951</option><option value="1950">1950</option><option value="1949">1949</option><option value="1948">1948</option><option value="1947">1947</option><option value="1946">1946</option><option value="1945">1945</option><option value="1944">1944</option><option value="1943">1943</option><option value="1942">1942</option><option value="1941">1941</option><option value="1940">1940</option><option value="1939">1939</option><option value="1938">1938</option><option value="1937">1937</option><option value="1936">1936</option><option value="1935">1935</option><option value="1934">1934</option><option value="1933">1933</option><option value="1932">1932</option><option value="1931">1931</option><option value="1930">1930</option><option value="1929">1929</option><option value="1928">1928</option><option value="1927">1927</option><option value="1926">1926</option><option value="1925">1925</option><option value="1924">1924</option><option value="1923">1923</option><option value="1922">1922</option><option value="1921">1921</option><option value="1920">1920</option>';*/
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
					  	$data .= '<option value="'.$a.'"';
						$data .= (isset($val['date']['yy']) && $val['date']['yy'] == $a)? 'selected="selected"' : "";
						$data .= '>'.__($a,"piereg").'</option>';
					  }
					  
					  /*echo '
					  <option value="2014">2014</option><option value="2013">2013</option><option value="2012">2012</option><option value="2011">2011</option><option value="2010">2010</option><option value="2009">2009</option><option value="2008">2008</option><option value="2007">2007</option><option value="2006">2006</option><option value="2005">2005</option><option value="2004">2004</option><option value="2003">2003</option><option value="2002">2002</option><option value="2001">2001</option><option value="2000">2000</option><option value="1999">1999</option><option value="1998">1998</option><option value="1997">1997</option><option value="1996">1996</option><option value="1995">1995</option><option value="1994">1994</option><option value="1993">1993</option><option value="1992">1992</option><option value="1991">1991</option><option value="1990">1990</option><option value="1989">1989</option><option value="1988">1988</option><option value="1987">1987</option><option value="1986">1986</option><option value="1985">1985</option><option value="1984">1984</option><option value="1983">1983</option><option value="1982">1982</option><option value="1981">1981</option><option value="1980">1980</option><option value="1979">1979</option><option value="1978">1978</option><option value="1977">1977</option><option value="1976">1976</option><option value="1975">1975</option><option value="1974">1974</option><option value="1973">1973</option><option value="1972">1972</option><option value="1971">1971</option><option value="1970">1970</option><option value="1969">1969</option><option value="1968">1968</option><option value="1967">1967</option><option value="1966">1966</option><option value="1965">1965</option><option value="1964">1964</option><option value="1963">1963</option><option value="1962">1962</option><option value="1961">1961</option><option value="1960">1960</option><option value="1959">1959</option><option value="1958">1958</option><option value="1957">1957</option><option value="1956">1956</option><option value="1955">1955</option><option value="1954">1954</option><option value="1953">1953</option><option value="1952">1952</option><option value="1951">1951</option><option value="1950">1950</option><option value="1949">1949</option><option value="1948">1948</option><option value="1947">1947</option><option value="1946">1946</option><option value="1945">1945</option><option value="1944">1944</option><option value="1943">1943</option><option value="1942">1942</option><option value="1941">1941</option><option value="1940">1940</option><option value="1939">1939</option><option value="1938">1938</option><option value="1937">1937</option><option value="1936">1936</option><option value="1935">1935</option><option value="1934">1934</option><option value="1933">1933</option><option value="1932">1932</option><option value="1931">1931</option><option value="1930">1930</option><option value="1929">1929</option><option value="1928">1928</option><option value="1927">1927</option><option value="1926">1926</option><option value="1925">1925</option><option value="1924">1924</option><option value="1923">1923</option><option value="1922">1922</option><option value="1921">1921</option><option value="1920">1920</option>';*/
					$data .= '
					</select>
				  </div>';
				  $data .= '
				  <div class="time_fields">
					<select id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" class="'.$this->addClass("").'">
					  <option value="">'.__("Month","piereg").'</option>';
					  for($a=1;$a<=12;$a++){
					  	$data .= '<option value="'.$a.'"';
						$data .= (isset($val['date']['mm']) && $val['date']['mm'] == $a)? 'selected="selected"' : "";
						$data .= '>'.__($a,"piereg").'</option>';
					  }
						
					  $data .= '
					</select>
				  </div>';
				   $data .=
				  '<div class="time_fields">
					<select id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" class="'.$this->addClass("").'">
					  <option value="">'.__("Day","piereg").'</option>';
					  for($a=1;$a<=31;$a++){
					  	$data .= '<option value="'.$a.'"';
						$data .= (isset($val['date']['dd']) && $val['date']['dd'] == $a)? 'selected="selected"' : "";
						$data .= '>'.__($a,"piereg").'</option>';
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
					  	$data .= '<option value="'.$a.'"';
						$data .= (isset($val['date']['dd']) && $val['date']['dd'] == $a)? 'selected="selected"' : "";
						$data .= '>'.__($a,"piereg").'</option>';
					  }
					$data .= '
					</select>
				  </div>';
				  
				  $data .= '
				  <div class="time_fields">
					<select id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" class="'.$this->addClass("").'">
					  <option value="">'.__("Month","piereg").'</option>';
					  for($a=1;$a<=12;$a++){
					  	$data .= '<option value="'.$a.'"';
						$data .= (isset($val['date']['mm']) && $val['date']['mm'] == $a)? 'selected="selected"' : "";
						$data .= '>'.__($a,"piereg").'</option>';
					  }
						
					  $data .= '
					</select>
				  </div>';
				  	 $data .= '
				  <div class="time_fields">
					<select id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" class="'.$this->addClass("").'">
					  <option value="">'.__("Year","piereg").'</option>';
					  for($a=((int)$endingDate);$a>=(((int)$startingDate));$a--){
					  //for($a=((int)date("Y"));$a>=(((int)date("Y"))-100);$a--){
					  	$data .= '<option value="'.$a.'"';
						$data .= (isset($val['date']['yy']) && $val['date']['yy'] == $a)? 'selected="selected"' : "";
						$data .= '>'.__($a,"piereg").'</option>';
					  }
					  /*echo '
					  <option value="2013">2013</option><option value="2012">2012</option><option value="2011">2011</option><option value="2010">2010</option><option value="2009">2009</option><option value="2008">2008</option><option value="2007">2007</option><option value="2006">2006</option><option value="2005">2005</option><option value="2004">2004</option><option value="2003">2003</option><option value="2002">2002</option><option value="2001">2001</option><option value="2000">2000</option><option value="1999">1999</option><option value="1998">1998</option><option value="1997">1997</option><option value="1996">1996</option><option value="1995">1995</option><option value="1994">1994</option><option value="1993">1993</option><option value="1992">1992</option><option value="1991">1991</option><option value="1990">1990</option><option value="1989">1989</option><option value="1988">1988</option><option value="1987">1987</option><option value="1986">1986</option><option value="1985">1985</option><option value="1984">1984</option><option value="1983">1983</option><option value="1982">1982</option><option value="1981">1981</option><option value="1980">1980</option><option value="1979">1979</option><option value="1978">1978</option><option value="1977">1977</option><option value="1976">1976</option><option value="1975">1975</option><option value="1974">1974</option><option value="1973">1973</option><option value="1972">1972</option><option value="1971">1971</option><option value="1970">1970</option><option value="1969">1969</option><option value="1968">1968</option><option value="1967">1967</option><option value="1966">1966</option><option value="1965">1965</option><option value="1964">1964</option><option value="1963">1963</option><option value="1962">1962</option><option value="1961">1961</option><option value="1960">1960</option><option value="1959">1959</option><option value="1958">1958</option><option value="1957">1957</option><option value="1956">1956</option><option value="1955">1955</option><option value="1954">1954</option><option value="1953">1953</option><option value="1952">1952</option><option value="1951">1951</option><option value="1950">1950</option><option value="1949">1949</option><option value="1948">1948</option><option value="1947">1947</option><option value="1946">1946</option><option value="1945">1945</option><option value="1944">1944</option><option value="1943">1943</option><option value="1942">1942</option><option value="1941">1941</option><option value="1940">1940</option><option value="1939">1939</option><option value="1938">1938</option><option value="1937">1937</option><option value="1936">1936</option><option value="1935">1935</option><option value="1934">1934</option><option value="1933">1933</option><option value="1932">1932</option><option value="1931">1931</option><option value="1930">1930</option><option value="1929">1929</option><option value="1928">1928</option><option value="1927">1927</option><option value="1926">1926</option><option value="1925">1925</option><option value="1924">1924</option><option value="1923">1923</option><option value="1922">1922</option><option value="1921">1921</option><option value="1920">1920</option>';*/
					  $data .= '
					</select>
				  </div>';	 
				$data .= '</div>';	
			}			
		}
		return $data;
	}		
		
	function createFieldName($text)
	{
		return $this->getMetaKey($text);			
	}
	function createFieldID()
	{
		return "field_".((isset($this->field['id']))?$this->field['id']:"");
	}
	function addLabel()
	{
		if($this->field['type']=="name" && $this->field['name_format']=="normal")
		{
			return "";
		}
	
		return '<label for="'.$this->id.'">'.__($this->field['label'],"piereg").'</label>';		
	}
	function addClass($default = "input_fields",$val = array())
	{
		$class = $default." ".((isset($this->field['css']))?$this->field['css']:"");
		
		
		if(isset($this->field['required']) && $this->field['required'] && $this->field['type'] != "password")
		{
			if($this->field['type'] == "upload" || $this->field['type'] == "profile_pic"){
				$meta_value = get_user_meta($this->user->data->ID , $this->slug,true);
				if(empty($meta_value)){
					$val[] = "required";
				}
			}else{
				$val[] = "required";
			}
		}
		
		
		if(isset($this->field['validation_rule']) && $this->field['validation_rule']=="number" )
		{
			$val[] = "custom[number]";		
		}
		else if(isset($this->field['validation_rule']) && $this->field['validation_rule']=="alphanumeric")
		{
			$val[] = "custom[alphanumeric]";		
		}
		else if((isset($this->field['validation_rule']) && $this->field['validation_rule']=="email") || $this->field['type']=="email")
		{
			$val[] = "custom[email]";		
		}
		else if((isset($this->field['validation_rule']) && $this->field['validation_rule']=="website") || $this->field['type']=="website")
		{
			$val[] = "custom[url]";		
		}
		else if($this->field['type']=="phone")
		{
			$val[] = "custom[phone]";		
		}
		else if($this->field['type']=="time")
		{
			$val[] = "custom[number]";	
			$val[] = "minSize[2]";
			$val[] = "maxSize[2]";	
		}
		else if($this->field['type']=="upload" && !empty($this->field['file_types']) && explode(",",$this->field['file_types']) > 0)
		{
			$val[] = "funcCall[checkExtensions]";
			$val[] = "ext[".str_replace(",","|",$this->field['file_types'])."]";			
		}
		
		if( !empty($val) && count($val) > 0)
		{
			$val = " piereg_validate[".implode(",",$val)."]";
			$class .= $val;
		}
		
		return $class;	
	}

	function addSubmit()
	{
		$data = "";
		if($this->pages > 1)
		{
			$data .= '<input class="pie_prev" name="pie_prev" id="pie_prev_'.$this->pages.'" type="button" value="'.__("Previous","piereg").'" />';
			$data .= '<input id="pie_prev_'.$this->pages.'_curr" name="page_no" type="hidden" value="'.($this->pages-1).'" />';						
		}
		$check_payment = get_option("pie_register_2");
		$cancel_url = $this->get_current_permalink();
		if( isset($check_payment['alternate_profilepage']) && !empty($check_payment['alternate_profilepage']) && empty($cancel_url) ){
			$cancel_url = $this->get_page_permalink_by_id( $check_payment['alternate_profilepage'] );
		}
		$data .= '<input type="button" class="piereg_cancel_profile_edit_btn" onclick="location.replace(\''.($cancel_url).'\');" value="'.__("Cancel","piereg").'" />';
		$data .= '<input name="pie_submit_update" type="submit" value="'.__($this->field['text'],"piereg").'" />';	
		return $data;
	}
	
	
	function printFields($user)
	{
		$profile_fields_data = "";
		$update = get_option( 'pie_register_2' );	
		
		foreach($this->data as $this->field)
		{
			
			if ($this->field['type']=="")
			{
				continue;
			}/*elseif($this->field['type']=="form"){
				//$profile_fields_data .= $this->addFormData();
				continue;
			}*/
			
			$this->name 	= $this->createFieldName($this->field['type']."_".$this->field['id']);
			$this->slug 	= $this->createFieldName("pie_".$this->field['type']."_".$this->field['id']);
			$this->id 		= $this->createFieldID();
			
			//We don't need to print li for hidden field
			if ($this->field['type'] == "hidden")
			{
				$profile_fields_data .= $this->addHiddenField();
				continue;
			}
			
			
			$topclass = "";
			if($this->label_alignment=="top")
				$topclass = "label_top";
			
			$profile_fields_data .= '<li class="fields pageFields_'.$this->pages.' '.$topclass.'">';
			
			//When to add label
			switch($this->field['type']) :				
				case 'text' :								
				case 'website' :							
				case 'username' :
				case 'password':			
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
				case 'phone':			
				case 'date':				
				case 'list':							
				case 'default':
					$profile_fields_data .= '<div class="fieldset">'.$this->addLabel();
				break;							
			endswitch;
			
			
			//Printting Field
			switch($this->field['type']) :
				case 'form':
				//$profile_fields_data .= $this->addFormData();
				break;
				case 'text' :								
				case 'website' :
				$profile_fields_data .= $this->addTextField();
				break;				
				case 'username' :
				$profile_fields_data .= $this->addUsername();
				break;
				/*case 'password' :
				$profile_fields_data .= $this->addPassword();
				break;*/
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
				case 'html':
				$profile_fields_data .= $this->addHTML();
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
				case 'date':
				$profile_fields_data .= $this->addDate();			
				break;
				case 'list':
				$profile_fields_data .= $this->addList();
				break;
				case 'submit':
				$profile_fields_data .= $this->addSubmit();
				break;				
				case 'default':
				$profile_fields_data .= $this->addDefaultField();
				break;										
			endswitch;
			
			
			//When to add label
			switch($this->field['type']) :				
				case 'text' :								
				case 'website' :							
				case 'username' :
				case 'password':			
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
				case 'profie_pic':
				case 'address':							
				case 'phone':			
				case 'date':				
				case 'list':							
				case 'default':
				$profile_fields_data .= '</div>';
				
				break;							
			endswitch;
			
		
			$profile_fields_data .= '</li>';
		}
		return $profile_fields_data;
	}
	
	function editProfile($user){
		
		$profile_fields_data = "";
		$update = get_option( 'pie_register_2' );	
		
		foreach($this->data as $this->field)
		{
			if((isset($this->field['show_in_profile']) and $this->field['show_in_profile'] == 0) && !is_admin())
				continue;
				
			if ($this->field['type']=="")
			{
				continue;
			}
			if ($this->field['type']=="math_captcha")
			{
				continue;
			}elseif($this->field['type']=="form"){
				$profile_fields_data .= $this->addFormData();
				continue;
			}
			
			$this->name 	= $this->createFieldName($this->field['type']."_".((isset($this->field['id']))?$this->field['id']:""));
			$this->slug 	= $this->createFieldName("pie_".$this->field['type']."_".((isset($this->field['id']))?$this->field['id']:""));
			$this->id 		= $this->createFieldID();
			
			//We don't need to print li for hidden field
			if ($this->field['type'] == "hidden")
			{
				$profile_fields_data .= $this->addHiddenField();
				continue;
			}
			$topclass = "";
			if($this->label_alignment=="top")
				$topclass = "label_top"; 
			
			$profile_fields_data .= '<li class="fields pageFields_'.$this->pages.' '.$topclass.'">';
			
			//When to add label
			switch($this->field['type']) :				
				case 'text' :								
				case 'website' :							
				case 'username' :
				case 'email' :
				case 'textarea':
				case 'dropdown':
				case 'multiselect':
				case 'number':
				case 'radio':
				case 'checkbox':
				case 'time':				
				case 'upload':			
				case 'profile_pic':
				case 'address':							
				case 'phone':			
				case 'date':				
				case 'list':							
				case 'default':
					$profile_fields_data .= '<div class="fieldset">'.$this->addLabel();
				break;
				case 'password':
					$profile_fields_data .= '<div class="fieldset">';
				break;
			endswitch;
			
		
			
			
			
			//Printting Field
			switch($this->field['type']) :
				case 'form':
				//$profile_fields_data .= $this->addFormData();
				break;
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
				case 'date':
				$profile_fields_data .= $this->addDate();			
				break;
				case 'list':
				$profile_fields_data .= $this->addList();
				break;
				case 'submit':
				$profile_fields_data .= $this->addSubmit();
				break;				
				case 'default':
				$profile_fields_data .= $this->addDefaultField();
				break;										
			endswitch;
			
			
			//When to add label
			switch($this->field['type']) :				
				case 'text' :								
				case 'website' :							
				case 'username' :
				case 'password':			
				case 'email' :
				case 'textarea':
				case 'dropdown':
				case 'multiselect':
				case 'number':
				case 'radio':
				case 'checkbox':
				case 'time':				
				case 'upload':			
				case 'profie_pic':
				case 'address':							
				case 'phone':			
				case 'date':				
				case 'list':							
				case 'default':
				$profile_fields_data .= '</div>';
				
				break;							
			endswitch;
			
		
			$profile_fields_data .= '</li>';
		}
		return $profile_fields_data;
	
	}
	
	
	function validateRegistration($errors)
	{
		global $wpdb,$errors,$piereg_global_options;
		$global_options = $piereg_global_options;
		$errors = new WP_Error();
		
		/*
			*	Sanitizing post data
		*/
		$this->piereg_sanitize_post_data( ( (isset($_POST) && !empty($_POST))?$_POST : array() ) );
		
		if ( empty( $_POST['e_mail'] ) || !filter_var($_POST['e_mail'],FILTER_VALIDATE_EMAIL) )
		{
			$errors->add( "password" , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_invalid_Email_address",__('Invalid E-mail address','piereg' )));
		}
		if($_POST['password'] != $_POST['confirm_password'] && isset($_POST['password'], $_POST['confirm_password']))
		{
			$errors->add( "password" , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_password_Fields_do_not_macth",__('Password Fields do not macth!','piereg' )));
		}
		/*
			*	Validate old password since 2.0.15
		*/
		if(!wp_check_password( $_POST['old_password'], $this->user->data->user_pass, $this->user->ID ) && !empty($_POST['old_password']))
		{
			$errors->add( "password" , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_old_password_Fields_do_not_macth",__('Old Password Fields do not macth!','piereg' )));
		}elseif(!empty($_POST['password']) && empty($_POST['old_password']) ){
			$errors->add( "password" , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_old_password_Fields_do_not_macth",__('Invalid Old Password Field!','piereg' )));
		}elseif($_POST['old_password'] == $_POST['password'] && !empty($_POST['password']) && !empty($_POST['old_password']))
		{
			$errors->add( "password" , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_old_password_and_new_password_same",__('Old and New passwords are same!','piereg' )));
		}
		
		 foreach($this->data as $field)
		 {
			$break = FALSE;
			//Printting Field
			switch($field['type']) 
			{
				case 'form':
				case 'username':
				case 'submit':
				case 'hidden':
				case 'invitation':
				case 'password':
				{			
					$break = TRUE;			
				}
				break;
			}
			if($break)
			{
				continue;	
			}
			/*
				*	Validate Show Profile
			*/
			if(isset($field['show_in_profile']) && $field['show_in_profile'] == 0)
			{
				continue;	
			}
			
			$slug = $this->createFieldName($field['type']."_".$field['id']);	
			if($field['type']=="username" || $field['type']=="password"){
				$slug  = $this->createFieldName($field['type']);
			}
			else if($field['type']=="name"){
				$slug  = $this->createFieldName("first_name");
			}
			else if($field['type']=="email"){
				$slug  = $this->createFieldName("e_mail");
				/*
					*	Is Email & Admin Verification On then
					*	Add since 2.0.13
					*	Modefy since 2.0.15
				*/
				if($this->user->data->user_email != $_POST['e_mail'] && !empty($global_options['email_edit_verification_step']) )
				{
					//Email Exists or not
					if(!email_exists($_POST['e_mail']))
					{
						/*
							*	Save New Email Address in user meta
						*/
						update_user_meta($this->user->data->ID,"new_email_address",$_POST['e_mail']);
						$email_key = md5((uniqid("piereg_").time()));
						/*
							*	Email Key add in array for email template
						*/
						if( intval($global_options['email_edit_verification_step']) == "1"){
							$keys_array = array("reset_email_key"=>$email_key);
							$email_slug = "email_edit_verification";
							$user_email_address = esc_sql($_POST['e_mail']);
							$email_edit_success_msg = "Please follow the link sent to your new Email to verify and make the change applied!";
						}elseif(intval($global_options['email_edit_verification_step']) == "2"){
							$keys_array = array("confirm_current_email_key"=>$email_key);
							$email_slug = "current_email_verification";
							$user_email_address = esc_sql($this->user->data->user_email);
							$email_edit_success_msg = "Please confirm the link sent to your current Email to verify and make the change applied!";
						}
						/*
							*	Email send snipt
						*/
						$subject		= html_entity_decode($global_options["user_subject_email_{$email_slug}"],ENT_COMPAT,"UTF-8");
						$message_temp 	= "";
						if($global_options["user_formate_email_{$email_slug}"] == "0"){
							$message_temp	= nl2br(strip_tags($global_options["user_message_email_{$email_slug}"]));
						}else{
							$message_temp	= $global_options["user_message_email_{$email_slug}"];
						}
						
						$message		= $this->filterEmail($message_temp,$this->user->data, "",false,$keys_array );
						$from_name		= $global_options["user_from_name_{$email_slug}"];
						$from_email		= $global_options["user_from_email_{$email_slug}"];					
						$reply_email 	= $global_options["user_to_email_{$email_slug}"];
						
						//Headers
						$headers  = "MIME-Version: 1.0" . "\r\n";
						$headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
					
						if(!empty($from_email) && filter_var($from_email,FILTER_VALIDATE_EMAIL))//Validating From
						$headers .= "From: ".$from_name." <".$from_email."> \r\n";
						if($reply_email){
							$headers .= "Reply-To: {$reply_email}\r\n";
							$headers .= "Return-Path: {$from_name}\r\n";
						}else{
							$headers .= "Reply-To: {$from_email}\r\n";
							$headers .= "Return-Path: {$from_email}\r\n";
						}
						if(!wp_mail($user_email_address, $subject, $message , $headers))
						{
							$errors->add('check-error',apply_filters("piereg_problem_and_the_email_was_probably_not_sent",__("There was a problem and the email was probably not sent.",'piereg')));
						}
						
						/*
							*	Update Email Hash Key
						*/
						update_user_meta($this->user->data->ID,"new_email_address_hashed",$email_key);
						$_POST['e_mail'] = $this->user->data->user_email;
						$_POST['success'] = __($email_edit_success_msg,"piereg");
					}else{
						$errors->add( $slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.__('This Email already Exists','piereg') );
					}
				}else{
					$this->user->data->user_email = $_POST['e_mail'];
				}
			}
			$field_name			= ((isset($_POST[$slug]))?$_POST[$slug]:"");
			$required 			= (isset($field['required']))?$field['required']:"";
			$rule				= (isset($field['validation_rule']))?$field['validation_rule']:"";
			$validation_message	= (!empty($field['validation_message']) ? $field['validation_message'] : ((isset($field['label']))?$field['label']:"") ." is required.");
			
			if($field['type']=="upload")
			{
				if( isset($_FILES[$slug]['name']) && $_FILES[$slug]['name'] != '' ){
					$this->pie_upload_files($this->user_id,$field,$slug);
					$validation_message = "";
				}elseif($required && ( !isset($_FILES[$slug]['name']) || empty($_FILES[$slug]['name']) ) )
					$field_name = "";
			}
			if($field['type']=="profile_pic")
			{
				if( isset($_FILES[$slug]['name']) && $_FILES[$slug]['name'] != '' ){
					$this->pie_profile_pictures_upload($this->user_id,$field,$slug);
					$validation_message = "";
				}elseif($required && ( !isset($_FILES[$slug]['name']) || empty($_FILES[$slug]['name']) ) )
					$field_name = "";
			}
			
			
			if( (!isset($field_name) || empty($field_name)) && $required)
			{
				if($field['type']=="profile_pic" || $field['type']=="upload"){
					if(empty($_POST[$slug.'_hidden']))
						$errors->add( $slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.$validation_message );
				}elseif($field['type']=="math_captcha"){
					
				}else{
					$errors->add( $slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.$validation_message );
				}
			}
			else if($rule=="number")
			{
				if(!is_numeric($field_name))
				{
										
					$errors->add( $slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.$field['label'] .apply_filters("piereg_field_must_contain_only_numbers",__(' field must contain only numbers.','piereg' )));
				}	
			}
			else if($rule=="alphanumeric")
			{
				if(! preg_match("/^([a-z0-9])+$/i", $field_name))
				{
					$errors->add( $slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.$field['label'] .apply_filters("piereg_field_may_only_contain_alpha_numeric_characters",__(' field may only contain alpha-numeric characters.','piereg' )));
				}	
			}	
			else if($rule=="email")
			{
				if(!filter_var($field_name,FILTER_VALIDATE_EMAIL))
				{
					$errors->add( $slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.$field['label'] .apply_filters("piereg_field_must_contain_a_valid_email_address",__(' field must contain a valid email address.','piereg' )));	
				}
			}	
			else if($rule=="website")
			{
				if(!filter_var($field_name,FILTER_VALIDATE_URL))
				{
					$errors->add( $slug , '<strong>'.__(ucwords('e'),'piereg').'</strong>: '.$field['label'] .apply_filters("piereg_must_be_a_valid_URL",__(' must be a valid URL.','piereg' )));
				}	
			}
		 }
		return $errors;
	}
	function UpdateUser()
	{
		global $wpdb,$pie_success,$pie_error,$pie_error_msg,$pie_suucess_msg,$errors;
		$errors = new WP_Error();
		$password = $_POST['password'];
		do_action("pr_edit_user_profile_update_before",$this->user_id,$_POST);
		foreach($this->data as $field)
		{
			//Some form fields which we can't save like paypal, submit,formdata
			
			//if(!isset($field['meta']))/* && $field['field_name'] != 'url'*/
			//{
				if($field['type']=="default")
				{
					$slug 				= $field['field_name'];				
					$value				= ((isset($_POST[$slug]))?$_POST[$slug]:"");
					if(update_user_meta($this->user_id, $slug, $value)) $this->pie_success = 1;
					else
					$this->pie_error = 1;
				}
				else if($field['type']=="name")
				{
					$slug 				= "first_name";				
					$value				= $_POST[$slug];
					if(update_user_meta($this->user_id, $slug, $value)) $this->pie_success = 1;
					else
					$this->pie_error = 1;	
					
					$slug 				= "last_name";				
					$value				= $_POST[$slug];
					if(update_user_meta($this->user_id, $slug, $value)) $this->pie_success = 1;
					else
					$this->pie_error = 1;	
				}
				else
				{
					$slug 				= $this->createFieldName($field['type']."_".((isset($field['id']))?$field['id']:""));
					switch($field['type']) :
										
						case 'text' :
						case 'textarea':
						case 'dropdown':
						case 'multiselect':
						case 'number':
						case 'radio':
						case 'checkbox':
						case 'html':
						case 'address':
						case 'phone':
						case 'date':
						case 'list':
							$field_name = ((isset($_POST[$slug]))?$_POST[$slug]:"");
							if(update_user_meta($this->user_id, "pie_".$slug, $field_name))
								$this->pie_success = 1;
							else
								$this->pie_error = 1;
						break;
						case 'time':
							if($_POST[$slug]['time_format'])
							{
								$_POST[$slug]['hh'] = intval($_POST[$slug]['hh']);
								if($_POST[$slug]['hh'] > 12)
									$_POST[$slug]['hh'] = "12";
								
								$_POST[$slug]['mm'] = intval($_POST[$slug]['mm']);
								if($_POST[$slug]['mm'] > 59)
									$_POST[$slug]['mm'] = "59";
								$field_name			= $_POST[$slug];
								update_user_meta($this->user_id, "pie_".$slug, $field_name);
							}else{
								$_POST[$slug]['hh'] = intval($_POST[$slug]['hh']);
								if($_POST[$slug]['hh'] > 23)
									$_POST[$slug]['hh'] = "23";
								
								$_POST[$slug]['mm'] = intval($_POST[$slug]['mm']);
								if($_POST[$slug]['mm'] > 59)
									$_POST[$slug]['mm'] = "59";
								
								$field_name			= $_POST[$slug];
								update_user_meta($this->user_id, "pie_".$slug, $field_name);
							}
							break;
					endswitch;
				}
			//}
		}
		
		do_action("pr_edit_user_profile_update_after",$this->user_id,$_POST,$this->pie_error,$this->pie_success);
		
		if($this->pie_error)
			$this->pie_error_msg = __('Something Went Wrong updating Profile fields, please try again!','piereg');
		if($this->pie_success)
			$this->pie_success_msg = __('Your Profile has been updated.','piereg');
	}		
			
}