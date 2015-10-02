<?php
require_once('base.php');
class Profile_admin extends PieReg_Base
{
    var $field;
    var $user_id;
    var $slug;
    var $type;
    var $name;
    var $id;
    var $data;
	
    function __construct()
    {
        $this->data = $this->getCurrentFields();
		$this->default_fields = FALSE;
		
		add_action( 'user_edit_form_tag', array($this,"piereg_wp_admin_form_tag") );
		
		/*print_r(get_user_meta($this->user_id, $this->slug));
		print_r(get_usermeta($this->user_id, $this->slug));*/
		
    }
    function addTextField()
    {
		//get_usermeta($this->user_id, $this->slug)
        echo '<input id="' . $this->id . '" name="' . $this->slug . '" class="' . $this->field['css'] . '"  placeholder="' . $this->field['placeholder'] . '" type="text" value="' . get_user_meta($this->user_id, $this->slug,true) . '" />';
    }
    function addTextArea()
    {
		//get_usermeta($this->user_id, $this->slug)
        echo '<textarea id="' . $this->id . '" name="' . $this->slug . '" rows="' . $this->field['rows'] . '" cols="' . $this->field['cols'] . '"  class="' . $this->field['css'] . '"  placeholder="' . $this->field['placeholder'] . '">' . get_user_meta($this->user_id, $this->slug,true) . '</textarea>';
    }
    function addDropdown()
    {
        //$sel_options = get_usermeta($this->user_id, $this->slug);
        $sel_options = get_user_meta($this->user_id, $this->slug,true);
        $multiple    = "";
        if ($this->type == "multiselect") {
				$multiple = 'multiple';
				$this->slug .= "[]";
			
			echo '<select ' . $multiple . ' id="' . $this->id . '" name="' . $this->slug . '" class="' . $this->field['css'] . '" style="min-width:200px;" >';
			if (sizeof($this->field['value']) > 0) {
				for ($a = 0; $a < sizeof($this->field['value']); $a++) {
					$selected = '';
					if(isset($this->field['value'][$a]) && !empty($this->field['value'][$a]))
					{
						if (in_array($this->field['value'][$a], $sel_options)) {
							$selected = 'selected="selected"';
						}
					}
					if ($this->field['value'][$a] != "" && $this->field['display'][$a] != "")
						echo '<option ' . $selected . ' value="' . $this->field['value'][$a] . '">' . $this->field['display'][$a] . '</option>';
				}
			}
			echo '</select>';
		}elseif ($this->type == "dropdown"){
			echo '<select id="' . $this->id . '" name="' . $this->slug . '" class="' . $this->field['css'] . '" style="min-width:100px;" >';
			if (sizeof($this->field['value']) > 0) {
				for ($a = 0; $a < sizeof($this->field['value']); $a++) {
					$selected = '';
					if ( $this->field['value'][$a] == $sel_options ) {
						$selected = 'selected="selected"';
					}
					if ($this->field['value'][$a] != "" && $this->field['display'][$a] != "")
						echo '<option ' . $selected . ' value="' . $this->field['value'][$a] . '">' . $this->field['display'][$a] . '</option>';
				}
			}
			echo '</select>';
		}
		
    }
    function addNumberField()
    {
		//get_usermeta($this->user_id, $this->slug)
        echo '<input id="' . $this->id . '" name="' . $this->slug . '" class="' . $this->field['css'] . '"  placeholder="' . $this->field['placeholder'] . '" min="' . $this->field['min'] . '" max="' . $this->field['max'] . '" type="number" value="' . get_user_meta($this->user_id, $this->slug,true) . '" />';
    }
    function addCheckRadio()
    {
        if (sizeof($this->field['value']) > 0) {
            //$val = get_usermeta($this->user_id, $this->slug);
            $val = get_user_meta($this->user_id, $this->slug,true);
            for ($a = 0; $a < sizeof($this->field['value']); $a++) {
                $checked = '';
                if (is_array($val) && in_array($this->field['value'][$a], $val)) {
                    $checked = 'checked="checked"';
                }
                echo '<span style="margin-left:5px;">'.$this->field['display'][$a].'</span>';
                echo '<input style="margin-left:5px;" value="' . $this->field['value'][$a] . '" ' . $checked . ' type="' . $this->type . '" ' . ((isset($multiple))?$multiple:"") . ' name="' . $this->slug . '[]" class="' . $this->field['css'] . '"  >';
            }
        }
    }
    function addHTML()
    {
        echo $this->field['html'];
    }
   
	function addUpload()
	{
		//$val = get_usermeta($this->user_id, $this->slug);
		$val = get_user_meta($this->user_id, $this->slug,true);
				
		echo '<input name="' . $this->slug . '" type="file" value="'.$val .'">';
		echo (trim($val) != "")? '<br /><a href="'.$val.'" target="_blank">'.basename($val).'</a>' : "";
	}
    function addProfilePic()
	{
		$data = "";
		//$val = get_usermeta($this->user_id , $this->slug);
		$val = get_user_meta($this->user_id , $this->slug,true);
		$data .= '<input id="'.$this->id.'" name="'.$this->slug.'" type="file" class=" validate[funcCall[checkExtensions],ext[gif|jpeg|jpg|png|bmp]]" />';
		$data .= '<input id="'.$this->id.'" name="'.$this->slug.'_hidden" value="'.$val.'" type="hidden"  />';
		$ext = (trim(basename($val)))? $val." Not Found" : "Profile Pictuer Not Found";
		$imgPath = (trim($val) != "")? $val : plugins_url("images/userImage.png",dirname(__FILE__));
		$data .= '<br /><img src="'.$imgPath.'" style="max-width:150px;" alt="'.__($imgPath,"piereg").'" />';
		echo $data;
	}
    function addAddress()
    {
        //$address = get_usermeta($this->user_id, $this->slug);
        $address = get_user_meta($this->user_id, $this->slug,true);
        echo '<div class="address">
		  <input type="text" name="' . $this->slug . '[address]" id="' . $this->id . '" value="' . ((isset($address['address']))?$address['address']:"") . '" >
		  <label>'.__("Street Address","piereg").'</label>
		</div>';
        if (empty($this->field['hide_address2'])) {
            echo '<div class="address">
			  <input type="text" name="' . $this->slug . '[address2]" id="address2_' . $this->id . '" value="' . ((isset($address['address2']))?$address['address2']:"") . '" >
			  <label>'.__("Address Line 2","piereg").'</label>
			</div>';
        }
        echo '<div class="address">
		  <div class="address2">
			<input type="text" name="' . $this->slug . '[city]" id="city_' . $this->id . '" value="' . ((isset($address['city']))?$address['city']:"") . '">
			<label>'.__("City","piereg").'</label>
		  </div>';
        if (empty($this->field['hide_state'])) {
            if ($this->field['address_type'] == "International") {
                echo '<div class="address2"  >
					<input type="text" name="' . $this->slug . '[state]" id="state_' . $this->id . '" value="' . ((isset($address['state']))?$address['state']:"") . '">
					<label>'.__("State / Province / Region","piereg").'</label>
				 	 </div>';
            } else if ($this->field['address_type'] == "United States") {
                $us_states = get_option("pie_us_states");
                $options   = $this->createDropdown($us_states, $address['state']);
                echo '<div class="address2"  >
					<select id="state_' . $this->id . '" name="' . $this->slug . '[state]">
					 ' . $options . ' 
					</select>
					<label>'.__("State","piereg").'</label>
				  </div>';
            } else if ($this->field['address_type'] == "Canada") {
                $can_states = get_option("pie_can_states");
                $options    = $this->createDropdown($can_states, $address['state']);
                echo '<div class="address2">
						<select id="state_' . $this->id . '" name="' . $this->slug . '[state]">
						  ' . $options . '
						</select>
						<label>'.__("Province","piereg").'</label>
					  </div>';
            }
        }
        echo '</div>';
        echo '<div class="address">';
        echo ' <div class="address2">
		<input id="zip_' . $this->id . '" name="' . $this->slug . '[zip]" type="text" value="' . ((isset($address['zip']))?$address['zip']:"") . '" >
		<label>'.__("Zip / Postal Code","piereg").'</label>
		 </div>';
        if ($this->field['address_type'] == "International") {
            $countries = get_option("pie_countries");
            $options   = $this->createDropdown($countries, ((isset($address['country']))?$address['country']:""));
            echo '<div  class="address2" >
					<select id="country_' . $this->id . '" name="' . $this->slug . '[country]" >
                    <option value="">'.__("Select Country","piereg").'</option>
					' . $options . '
					 </select>
					<label>'.__("Country","piereg").'</label>
		  		</div>';
        }
        echo '</div>';
    }
    function addPhone()
    {
		//get_usermeta($this->user_id, $this->slug)
		echo '<input id="' . $this->id . '"  name="' . $this->slug . '" class="input_fields"  placeholder="' . ((isset($field['placeholder']))?$field['placeholder']:"") . '" type="text" value="' . get_user_meta($this->user_id, $this->slug,true) . '" />';
    }
    function addTime()
    {
        //$time = get_usermeta($this->user_id, $this->slug);
        $time = get_user_meta($this->user_id, $this->slug,true);
        echo '<input size="2" maxlength="2" id="hh_' . $this->id . '" name="' . $this->slug . '[hh]" type="text" value="' . ((isset($time['hh']))?$time['hh']:"") . '"> : <input size="2" maxlength="2" id="mm_' . $this->id . '" type="text" name="' . $this->slug . '[mm]"  value="' . ((isset($time['mm']))?$time['mm']:"") . '">';
        if ($this->field['time_type'] == "12") {
            $time_format = ((isset($time['time_format']))?$time['time_format']:"");
            echo '<select name="' . $this->slug . '[time_format]" >			
			<option ' . (($time_format == "am") ? 'selected="selected"' : "") . ' value="am">'.__("AM","piereg").'</option>
			<option ' . (($time_format == "pm") ? 'selected="selected"' : "") . ' value="pm">'.__("PM","piereg").'</option>			
			</select>';
        }
        echo '</div>';
    }
    function addDate()
    {
        //$date = get_usermeta($this->user_id, $this->slug);
        $date = get_user_meta($this->user_id, $this->slug,true);
		if(!$date)
		{
			$date['date']['mm'] = "";
			$date['date']['dd'] = "";
			$date['date']['yy'] = "";	
		}
		$startingDate = $this->field['startingDate'];
		$endingDate = $this->field['endingDate'];
        if ($this->field['date_type'] == "datefield") {
            if ($this->field['date_format'] == "mm/dd/yy") {
                echo '<div class="piereg_time date_format_field">
				  <div class="time_fields">
					<input id="mm_' . $this->id . '" name="' . $this->slug . '[date][mm]" maxlength="2" type="text" value="' . ((isset($date['date']['mm']))?$date['date']['mm']:"") . '" >
					<label>'.__("MM","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input id="dd_' . $this->id . '" name="' . $this->slug . '[date][dd]" maxlength="2"  type="text" value="' . ((isset($date['date']['dd']))?$date['date']['dd']:"") . '">
					<label>'.__("DD","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input id="yy_' . $this->id . '" name="' . $this->slug . '[date][yy]" maxlength="4"  type="text" value="' . ((isset($date['date']['yy']))?$date['date']['yy']:"") . '">
					<label>'.__("yy","piereg").'</label>
				  </div>
				</div>';
            } else if ($this->field['date_format'] == "yy/mm/dd" || $this->field['date_format'] == "yy.mm.dd") {
                echo '<div class="piereg_time date_format_field">
				 <div class="time_fields">
					<input id="yy_' . $this->id . '" name="' . $this->slug . '[date][yy]" maxlength="4"  type="text" value="' . ((isset($date['date']['yy']))?$date['date']['yy']:"") . '">
					<label>'.__("yy","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input id="mm_' . $this->id . '" name="' . $this->slug . '[date][mm]" maxlength="2" type="text" value="' . ((isset($date['date']['mm']))?$date['date']['mm']:"") . '">
					<label>'.__("MM","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input id="dd_' . $this->id . '" name="' . $this->slug . '[date][dd]" maxlength="2"  type="text" value="' . ((isset($date['date']['dd']))?$date['date']['dd']:"") . '">
					<label>'.__("DD","piereg").'</label>
				  </div>				  
				</div>';
            } else {
                echo '<div class="piereg_time date_format_field">
				 <div class="time_fields">
					<input id="dd_' . $this->id . '" name="' . $this->slug . '[date][dd]" maxlength="2"  type="text" value="' . ((isset($date['date']['dd']))?$date['date']['dd']:"") . '">
					<label>'.__("DD","piereg").'</label>
				  </div>	
				 <div class="time_fields">
					<input id="yy_' . $this->id . '" name="' . $this->slug . '[date][yy]" maxlength="4"  type="text" value="' . ((isset($date['date']['yy']))?$date['date']['yy']:"") . '">
					<label>'.__("yy","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input id="mm_' . $this->id . '" name="' . $this->slug . '[date][mm]" maxlength="2" type="text" value="' . ((isset($date['date']['mm']))?$date['date']['mm']:"") . '">
					<label>'.__("MM","piereg").'</label>
				  </div>				  			  
				</div>';
            }
        } 
		else if ($this->field['date_type'] == "datepicker") {
            echo '<div class="piereg_time date_format_field">
				  <input id="' . $this->id . '" name="' . $this->slug . '[date][]" value="' . $date['date'][0] . '" type="text" placeholder="'.$this->field['date_format'].'"></div>';
        }
		else if ($this->field['date_type'] == "datedropdown") {
            echo '<div class="piereg_time date_format_field">
				 
					<select id="mm_' . $this->id . '" name="' . $this->slug . '[date][mm]">
					  <option value="">'.__("Month","piereg").'</option>';
            for ($a = 1; $a <= 12; $a++) {
                $sel = '';
                if ((int) $a == (int) $date['date']['mm']) {
                    $sel = 'selected="selected"';
                }
                echo '<option ' . $sel . ' value="' . $a . '">' . __(sprintf("%02s", $a),"piereg") . '</option>';
            }
            echo '</select>
				 
				
					<select id="dd_' . $this->id . '" name="' . $this->slug . '[date][dd]">
					  <option value="">'.__("Day","piereg").'</option>';
            for ($a = 1; $a <= 31; $a++) {
                $sel = '';
                if ((int) $a == (int) $date['date']['dd']) {
                    $sel = 'selected="selected"';
                }
                echo '<option ' . $sel . ' value="' . $a . '">' . __(sprintf("%02s", $a),"piereg") . '</option>';
            }
            echo '</select>
				
				  
					<select id="yy_' . $this->id . '" name="' . $this->slug . '[date][yy]">
					  <option value="">'.__("Year","piereg").'</option>'; 
		    for($a=((int)$endingDate);$a>=(((int)$startingDate));$a--){
            //for ($a = 2099; $a >= 1900; $a--) {
                $sel = '';
                if ((int) $a == (int) $date['date']['yy']) {
                    $sel = 'selected="selected"';
                }
                echo '<option ' . $sel . ' value="' . $a . '">' . $a . '</option>';
            }
            echo '</select>
				 
				</div>';
        }
    }
	function addList()
	{
		//$list = get_usermeta($this->user_id, $this->slug);
		$list = get_user_meta($this->user_id, $this->slug,true);
		$width  = 90 /  $this->field['cols']; 
		
		for($a = 1 ,$c=0; $a <= $this->field['rows'] ; $a++,$c++)
		{
			echo '<div>';
			for($b = 1,$d=0 ; $b <= $this->field['cols'] ;$b++,$d++)
			{
				if(!is_array($list))
				$list[$c][$d] = "";
				
				echo '<input value="'.$list[$c][$d].'" style="width:'.$width.'%;margin-right:2px;" type="text" name="'.$this->slug.'['.$c.'][]" class="input_fields"> ';
			}
			echo '</div>';
		}
		
		
	}
	function createFieldName($text)
    {
        return "pie_".$this->getMetaKey($text);
    }
    function createFieldID()
    {
        return "field_" . ((isset($this->field['id']))?$this->field['id']:"");
    }
    function addLabel()
    {
        return '<label for="' . $this->id . '">' . $this->field['label'] . '</label>';
    }
    function printFields()
    {
        $update     = get_option('pie_register_2');
        switch ($this->type):
            case 'text':           
                $this->addTextField();
                break;
            case 'textarea':
                $this->addTextArea();
                break;
            case 'dropdown':
            case 'multiselect':
                $this->addDropdown();
                break;
            case 'number':
                $this->addNumberField();
                break;
            case 'radio':
            case 'checkbox':
                $this->addCheckRadio();
                break;
            case 'html':
                $this->addHTML();
                break;
            
            case 'time':
                $this->addTime();
                break;
            case 'upload':
                $this->addUpload();
                break;
            case 'profile_pic':
                $this->addProfilePic();
                break;
            case 'address':
                $this->addAddress();
                break;
            case 'captcha':
                $this->addCaptcha();
                break;
            case 'phone':
                $this->addPhone();
                break;
            case 'date':
                $this->addDate();
                break;
            case 'list':
                $this->addList();
                break;
        endswitch;
    }
    function edit_user_profile($user)
    {
        if (sizeof($this->data) > 0)
		{
            $this->user_id = $user->ID;	
			echo "<h3>Additionals Registration Fields (Pie-Register)</h3>";
            echo '<table class="form-table">';
           foreach ($this->data as $this->field) 
		   {
             	$this->slug = $this->createFieldName($this->field['type']."_". ((isset($this->field['id']))?$this->field['id']:"") );
                $this->type = $this->field['type'];
                $this->id   = $this->createFieldID();	   
			   	
				if((isset($this->field['show_in_profile']) and $this->field['show_in_profile'] == 0 ) && !is_admin())
					continue;
			   	
			   
				//When to add label
				switch($this->type) :												
					case 'time':
					case 'text' :
					case 'textarea':
					case 'dropdown':
					case 'multiselect':
					case 'number':
					case 'radio':
					case 'checkbox':					
					case 'upload':				
					case 'profile_pic':				
					case 'address':							
					case 'phone':				
					case 'date':				
					case 'list':	
					case "default" && $this->default_fields:						
					echo '<tr><th>'.$this->addLabel().'</th><td>';
					echo $this->printFields().'</td></tr>';
					break;	
											
				endswitch; 
			 }
           echo '</table>';
	
        }
    }
	
	function updateMyProfile($user_id) 
	{
     	if ( current_user_can('edit_user',$user_id) )
     	{
			$this->updateProfile($user_id); 
	 	}
 	}
    function validate_user_profile($errors, $update, $user)
    {
		/*
			*	Sanitizing post data
		*/
		$this->piereg_sanitize_post_data( ( (isset($_POST) && !empty($_POST))?$_POST : array() ) );
		
        foreach ($this->data as $this->field) {
            $this->slug         = $this->createFieldName($this->field['label']);
            $this->type         = $this->type;
            $this->id           = $this->createFieldID();
            $field_name         = $_POST[$this->slug];
            $required           = $this->field['required'];
            $rule               = $this->field['validation_rule'];
            $validation_message = (!empty($this->field['validation_message']) ? $this->field['validation_message'] : $this->field['label'] . " is required.");
            if ((!isset($field_name) || empty($field_name)) && $required) {
                $errors->add($this->slug, '<strong>'.ucwords(__('error','piereg')).'</strong>: ' . $validation_message);
            } else if ($rule == "number") {
                if (!is_numeric($field_name)) {
                    $errors->add( $slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.$this->field['label'] .apply_filters("piereg_field_must_contain_only_numbers",__(' field must contain only numbers.','piereg' )));
			    }
            } else if ($rule == "alphanumeric") {
                if (!preg_match("/^([a-z0-9])+$/i", $field_name)) {
                   	$errors->add( $slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.$this->field['label'] .apply_filters("piereg_field_may_only_contain_alpha_numeric_characters",__(' field may only contain alpha-numeric characters.','piereg' )));
                }
            } else if ($rule == "email") {
                if (!filter_var($field_name, FILTER_VALIDATE_EMAIL)) {
                    $errors->add( $slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.$this->field['label'].apply_filters("piereg_field_must_contain_a_valid_email_address",__(' field must contain a valid email address.','piereg' )));
                }
            } else if ($rule == "website") {
                if (!filter_var($field_name, FILTER_VALIDATE_URL)) {                   
					$errors->add( $slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.$this->field['label'] .apply_filters("piereg_must_be_a_valid_URL",__(' must be a valid URL.','piereg' )));
                }
            }
        }
        if (sizeof($errors->errors) == 0) {
            $this->updateProfile($user->ID);
        }
        return $errors;
    }
    function updateProfile($user_id)
    {
		global $errors;
		$errors = new WP_Error();
        foreach ($this->data as $this->field){
		//When to add label
			$slug       = $this->createFieldName($this->field['type']."_".$this->field['id']);
			
			switch($this->field['type']) :
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
						update_user_meta($user_id, $slug, $_POST[$slug]);
					}else{
						$_POST[$slug]['hh'] = intval($_POST[$slug]['hh']);
						if($_POST[$slug]['hh'] > 23)
							$_POST[$slug]['hh'] = "23";
						
						$_POST[$slug]['mm'] = intval($_POST[$slug]['mm']);
						if($_POST[$slug]['mm'] > 59)
							$_POST[$slug]['mm'] = "59";
						
						$field_name			= $_POST[$slug];
						update_user_meta($user_id, $slug, $_POST[$slug]);
					}
				break;
				case 'upload':
					$this->pie_upload_files($user_id,$this->field,$slug);
					break;
				case 'profile_pic':
					$this->pie_profile_pictures_upload($user_id,$this->field,$slug);
					break;				
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
					$field_value = $_POST[$slug];
					update_user_meta($user_id,$slug, $field_value);
				break;
			endswitch;
        }
		
    }
	function pie_profile_pictures_upload($user_id,$field,$field_slug){
		
		
		global $errors;
		$errors = new WP_Error();
		if($_FILES[$field_slug]['name'] != ''){
			////////////////////////////UPLOAD PROFILE PICTURE//////////////////////////////
			$result = $this->piereg_validate_files($_FILES[$field_slug]['name'],array("gif","jpeg","jpg","png","bmp"));
			if($result)
			{
				$temp = explode(".", $_FILES[$field_slug]["name"]);
				$extension = end($temp);
				$upload_dir = wp_upload_dir();
				$temp_dir = $upload_dir['basedir']."/piereg_users_files/".$_POST['user_id'];
				wp_mkdir_p($temp_dir);
				$temp_file_name = "profile_pic_".crc32($_POST['user_id']."_".$extension."_".time()).".".$extension;
				$temp_file_url = $upload_dir['baseurl']."/piereg_users_files/".$_POST['user_id']."/".$temp_file_name;
				if(!move_uploaded_file($_FILES[$field_slug]['tmp_name'],$temp_dir."/".$temp_file_name) && $required){
					$errors->add( $field_slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_Fail_to_upload_profile_picture",__('Fail to upload profile picture.','piereg' )));
				}else{
					update_user_meta($user_id,$field_slug, $temp_file_url);
				}
			}else{
				$errors->add( $field_slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_invalid_profile_picture",__('Invalid profile picture','piereg' )));
			}
		}
	
	
	}
	function pie_upload_files($user_id,$field,$field_slug)
	{
		
		global $errors;
		$errors = new WP_Error();
		if($_FILES[$field_slug]['name'] != ''){
			if($field['file_types'] != ""){
				$filter_string = stripcslashes($field['file_types']);
				$filter_array = explode(",",$filter_string);
				$result = $this->piereg_validate_files($_FILES[$field_slug]['name'],$filter_array);
				if($result){
					$temp = explode(".", $_FILES[$field_slug]["name"]);
					$extension = end($temp);
					$upload_dir = wp_upload_dir();
					$temp_dir = $upload_dir['basedir']."/piereg_users_files/".$user_id;
					wp_mkdir_p($temp_dir);
					$temp_file_name = "file_".crc32($user_id."_".$extension."_".time()).".".$extension;
					$temp_file_url = $upload_dir['baseurl']."/piereg_users_files/".$user_id."/".$temp_file_name;
					if(!move_uploaded_file($_FILES[$field_slug]['tmp_name'],$temp_dir."/".$temp_file_name) && $required){
						$errors->add( $field_slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_Fail_to_upload_profile_picture",__('Fail to upload profile picture.','piereg' )));
					}else{
						update_user_meta($user_id,$field_slug, $temp_file_url);
					}
				}
				else{
					$errors->add( $field_slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_Invlid_upload_file",__('Invalid upload file.','piereg' )));
				}
			}else{
				$temp = explode(".", $_FILES[$field_slug]["name"]);
				$extension = end($temp);
				$upload_dir = wp_upload_dir();
				$temp_dir = $upload_dir['basedir']."/piereg_users_files/".$_POST['user_id'];
				wp_mkdir_p($temp_dir);
				$temp_file_name = "file_".crc32($_POST['user_id']."_".$extension."_".time()).".".$extension;
				$temp_file_url = $upload_dir['baseurl']."/piereg_users_files/".$_POST['user_id']."/".$temp_file_name;
				if(!move_uploaded_file($_FILES[$field_slug]['tmp_name'],$temp_dir."/".$temp_file_name) && $required){
					$errors->add( $field_slug , '<strong>'.ucwords(__('error','piereg')).'</strong>: '.apply_filters("piereg_Fail_to_upload_profile_picture",__('Fail to upload profile picture.','piereg' )));
				}else{
					update_user_meta($user_id,$field_slug, $temp_file_url);
				}
			}
		}
	}
	
	function piereg_wp_admin_form_tag(){
		echo ' enctype="multipart/form-data" ';
	}
	
}