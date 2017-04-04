<?php
if( file_exists( dirname(__FILE__) . '/base.php') ) 
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

    function __construct($form_id = "default")
    {
		$this->data = $this->getCurrentFields($form_id);
		$this->default_fields = FALSE;
		add_action("init", array($this,"get_user_registered_form"));
		add_action( 'user_edit_form_tag', array($this,"piereg_wp_admin_form_tag") );
    }
	function get_user_registered_form(){
		if(is_user_logged_in()){
			$form_id = get_user_meta(get_current_user_id(),"user_registered_form_id",true);
			if( is_numeric($form_id) && !empty($form_id) )
				$this->data = $this->getCurrentFields($form_id);
		}
	}
    function addTextField($disabled)
    {
       	$val = get_user_meta($this->user_id, $this->slug, true); #get_usermeta deprecated
		if(is_array($val))
		{
			$val = implode( ",", $val );	
		}
		echo '<input'.$disabled.' id="' . $this->id . '" name="' . $this->slug . '" class="' . $this->field['css'] . '"  placeholder="' . $this->field['placeholder'] . '" type="text" value="' . $val . '" />';
    }

    function addTextArea($disabled)
    {
        $val = get_user_meta($this->user_id, $this->slug, true); #get_usermeta deprecated
		if(is_array($val))
		{
			$val = implode( ",", $val );	
		}
		echo '<textarea'.$disabled.' id="' . $this->id . '" name="' . $this->slug . '" rows="' . $this->field['rows'] . '" cols="' . $this->field['cols'] . '"  class="' . $this->field['css'] . '"  placeholder="' . $this->field['placeholder'] . '">' . $val . '</textarea>';
    }

    function addDropdown($disabled)
    {
        $sel_options = get_user_meta($this->user_id, $this->slug, true); #get_usermeta deprecated
        $multiple    = "";
        if ($this->type == "multiselect") {
				$multiple = 'multiple';
				$this->slug .= "[]";
			echo '<select' . $disabled . ' ' . $multiple . ' id="' . $this->id . '" name="' . $this->slug . '" class="' . $this->field['css'] . '" style="min-width:200px;" >';
			if (sizeof($this->field['value']) > 0) {
				for ($a = 0; $a < sizeof($this->field['value']); $a++) {
					$selected = '';
					if(isset($this->field['value'][$a]) && !empty($this->field['value'][$a]))
					{
						$sel_options = (isset($sel_options[0]) && is_array($sel_options[0])) ? $sel_options[0] : $sel_options;
						if (in_array($this->field['value'][$a], $sel_options)){
							$selected = 'selected="selected"';
						}
					}
					if ($this->field['value'][$a] != "" && $this->field['display'][$a] != "")
						echo '<option ' . $selected . ' value="' . $this->field['value'][$a] . '">' . $this->field['display'][$a] . '</option>';
				}
			}
			echo '</select>';
		}elseif ($this->type == "dropdown"){
			$sel_options = (isset($sel_options[0]) && is_array($sel_options[0])) ? $sel_options[0] : $sel_options;
			echo '<select' . $disabled . ' id="' . $this->id . '" name="' . $this->slug . '" class="' . $this->field['css'] . '" style="min-width:100px;" >';
			if($this->field['list_type']=="country")
			{
				$countries = get_option("pie_countries");			 
				echo $this->createDropdown($countries,$sel_options);			   	
			}
			else if($this->field['list_type']=="us_states")
			{
				$us_states	= get_option("pie_us_states");
				echo $this->createDropdown($us_states,$sel_options);
			}
			else if($this->field['list_type']=="can_states")
			{
				$can_states	= get_option("pie_can_states");
				echo $this->createDropdown($can_states,$sel_options);
			}
			else if (sizeof($this->field['value']) > 0) {
				
				for ($a = 0; $a < sizeof($this->field['value']); $a++) {
					$selected = '';
					$sel_options = (isset($sel_options[0]) && is_array($sel_options[0])) ? $sel_options[0] : $sel_options;
					if ( in_array($this->field['value'][$a], $sel_options) ) {
						$selected = 'selected="selected"';
					}
					if ($this->field['value'][$a] != "" && $this->field['display'][$a] != "")
						echo '<option ' . $selected . ' value="' . $this->field['value'][$a] . '">' . $this->field['display'][$a] . '</option>';
				}
			}
			echo '</select>';
		
		}
    }

    function addNumberField($disabled)
    {
		$val   = get_user_meta($this->user_id, $this->slug, true); #get_usermeta deprecated
		if(is_array($val))
		{
			$val = implode( ",", $val );	
		}
        echo '<input' . $disabled .' id="' . $this->id . '" name="' . $this->slug . '" class="' . $this->field['css'] . '"  placeholder="' . $this->field['placeholder'] . '" min="' . $this->field['min'] . '" max="' . $this->field['max'] . '" type="number" value="' . $val . '" />';
    }

    function addCheckRadio($disabled)
    {
        if (sizeof($this->field['value']) > 0) {
            $val = get_user_meta($this->user_id, $this->slug, true); #get_usermeta deprecated
            for ($a = 0; $a < sizeof($this->field['value']); $a++) {
                $checked = '';
				$val = (isset($val[0]) && is_array($val[0])) ? $val[0] : $val;
                if (is_array($val) && in_array($this->field['value'][$a], $val)) {
                    $checked = 'checked="checked"';
                }
                echo '<span style="margin-left:5px;">'.$this->field['display'][$a].'</span>';
                echo '<input' . $disabled . ' style="margin-left:5px;" value="' . $this->field['value'][$a] . '" ' . $checked . ' type="' . $this->type . '" ' . ((isset($multiple))?$multiple:"") . ' name="' . $this->slug . '[]" class="' . $this->field['css'] . '"  >';
            }
        }
    }

    function addHTML()
    {
        echo $this->field['html'];
    }

	function addUpload($disabled)
	{
		$val = get_user_meta($this->user_id, $this->slug, true); #get_usermeta deprecated
		
		if(is_array($val))
		{
			$val = implode( ",", $val );	
		}
		echo '<input'.$disabled.' name="' . $this->slug . '" type="file" value="'.$val .'">';
		echo (trim($val) != "")? '<br /><a href="'.$val.'" target="_blank">'.basename($val).'</a>' : "";
	}

    function addProfilePic($disabled)
	{
		$data = "";
		$val = get_user_meta($this->user_id , $this->slug, true); #get_usermeta deprecated
		
		if(is_array($val))
		{
			$val = implode( ",", $val );	
		}
		$data .= '<input'.$disabled.' id="'.$this->id.'" name="'.$this->slug.'" type="file" class=" validate[funcCall[checkExtensions],ext[gif|jpeg|jpg|png|bmp]]" />';
		$data .= '<input id="'.$this->id.'" name="'.$this->slug.'_hidden" value="'.$val.'" type="hidden"  />';
		$ext = (trim(basename($val)))? $val." Not Found" : "Profile Pictuer Not Found";
		$imgPath = (trim($val) != "")? $val : plugins_url("images/userImage.png",dirname(__FILE__));
		$data .= '<br /><img src="'.$imgPath.'" style="max-width:150px;" alt="'.__($imgPath,"piereg").'" />';
		echo $data;
	}

    function addAddress($disabled)
    {
        $address = get_user_meta($this->user_id, $this->slug, true); #get_usermeta deprecated
        echo '<div class="address">
		  <input'.$disabled.' type="text" name="' . $this->slug . '[address]" id="' . $this->id . '" value="' . ((isset($address['address']))?$address['address']:"") . '" >
		  <label>'.__("Street Address","piereg").'</label>
		</div>';
        if (empty($this->field['hide_address2'])) {
            echo '<div class="address">
			  <input'.$disabled.' type="text" name="' . $this->slug . '[address2]" id="address2_' . $this->id . '" value="' . ((isset($address['address2']))?$address['address2']:"") . '" >
			  <label>'.__("Address Line 2","piereg").'</label>
			</div>';
        }
        echo '<div class="address">
		  <div class="address2">
			<input'.$disabled.' type="text" name="' . $this->slug . '[city]" id="city_' . $this->id . '" value="' . ((isset($address['city']))?$address['city']:"") . '">
			<label>'.__("City","piereg").'</label>
		  </div>';
        if (empty($this->field['hide_state'])) {
            if ($this->field['address_type'] == "International") {
                echo '<div class="address2"  >
					<input'.$disabled.' type="text" name="' . $this->slug . '[state]" id="state_' . $this->id . '" value="' . ((isset($address['state']))?$address['state']:"") . '">
					<label>'.__("State / Province / Region","piereg").'</label>
				 	 </div>';
            } else if ($this->field['address_type'] == "United States") {
                $us_states = get_option("pie_us_states");
                $options   = $this->createDropdown($us_states, ((isset($address['state']))?$address['state']:""));
                echo '<div class="address2"  >
					<select'.$disabled.' id="state_' . $this->id . '" name="' . $this->slug . '[state]">
					 ' . $options . ' 
					</select>
					<label>'.__("State","piereg").'</label>
				  </div>';
            } else if ($this->field['address_type'] == "Canada") {
                $can_states = get_option("pie_can_states");
                $options    = $this->createDropdown($can_states, ((isset($address['state']))?$address['state']:""));
                echo '<div class="address2">
						<select'.$disabled.' id="state_' . $this->id . '" name="' . $this->slug . '[state]">
						  ' . $options . '
						</select>
						<label>'.__("Province","piereg").'</label>
					  </div>';
            }
        }
        echo '</div>';
        echo '<div class="address">';
        echo ' <div class="address2">
		<input'.$disabled.' id="zip_' . $this->id . '" name="' . $this->slug . '[zip]" type="text" value="' . ((isset($address['zip']))?$address['zip']:"") . '" >
		<label>'.__("Zip / Postal Code","piereg").'</label>
		</div>';
        if ($this->field['address_type'] == "International") {
            $countries = get_option("pie_countries");
            $options   = $this->createDropdown($countries, ((isset($address['country']))?$address['country']:""));
            echo '<div class="address2" >
					<select'.$disabled.' id="country_' . $this->id . '" name="' . $this->slug . '[country]" >
                    <option value="">'.__("Select Country","piereg").'</option>
					' . $options . '
					 </select>
					<label>'.__("Country","piereg").'</label>
		  		</div>';
        }
        echo '</div>';
    }

    function addPhone($disabled)
    {
		$val   = get_user_meta($this->user_id, $this->slug, true); #get_usermeta deprecated
		if(is_array($val))
		{
			$val = implode( ",", $val );	
		}
		echo '<input'.$disabled.' id="' . $this->id . '"  name="' . $this->slug . '" class="input_fields"  placeholder="' . (isset($field['placeholder']) ? $field['placeholder'] : "") . '" type="text" value="' . $val . '" />';
    }

    function addInvitation($disabled)
    {
		$val   = get_user_meta($this->user_id, "invite_code", true); #get_usermeta deprecated
		if(is_array($val))
		{
			$val = implode( ",", $val );	
		}
		echo '<input'.$disabled.' id="' . $this->id . '"  name="' . $this->slug . '" class="input_fields"  placeholder="' . (isset($field['placeholder']) ? $field['placeholder'] : "") . '" type="text" disabled="disabled" style="width:25em;" value="' . $val . '" />';
    }
	
	function addTime($disabled)
    {
        $time = get_user_meta($this->user_id, $this->slug, true); #get_usermeta deprecated
        echo '<input' . $disabled . ' size="2" maxlength="2" id="hh_' . $this->id . '" name="' . $this->slug . '[hh]" type="text" value="' . ((isset($time['hh']))?$time['hh']:"") . '"> : <input' . $disabled . ' size="2" maxlength="2" id="mm_' . $this->id . '" type="text" name="' . $this->slug . '[mm]"  value="' . ((isset($time['mm']))?$time['mm']:"") . '">';
		
        if ($this->field['time_type'] == "12") {
            $time_format = ((isset($time['time_format']))?$time['time_format']:"");
            echo '<select' . $disabled . ' name="' . $this->slug . '[time_format]" >			
			<option ' . (($time_format == "am") ? 'selected="selected"' : "") . ' value="am">'.__("AM","piereg").'</option>
			<option ' . (($time_format == "pm") ? 'selected="selected"' : "") . ' value="pm">'.__("PM","piereg").'</option>			
			</select>';
        }
        echo '</div>';
    }

    function addDate($disabled)
    {
        $date = get_user_meta($this->user_id, $this->slug, true); #get_usermeta deprecated
		if(!$date)
		{
			$date['date']['mm'] = "";
			$date['date']['dd'] = "";
			$date['date']['yy'] = "";	
		}
        if ($this->field['date_type'] == "datefield") {
            if ($this->field['date_format'] == "mm/dd/yy") {
                echo '<div class="piereg_time date_format_field">
				  <div class="time_fields">
					<input'.$disabled.' id="mm_' . $this->id . '" name="' . $this->slug . '[date][mm]" maxlength="2" type="text" value="' . $date['date']['mm'] . '" >
					<label>'.__("MM","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input'.$disabled.' id="dd_' . $this->id . '" name="' . $this->slug . '[date][dd]" maxlength="2"  type="text" value="' . $date['date']['dd'] . '">
					<label>'.__("DD","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input'.$disabled.' id="yy_' . $this->id . '" name="' . $this->slug . '[date][yy]" maxlength="4"  type="text" value="' . $date['date']['yy'] . '">
					<label>'.__("yy","piereg").'</label>
				  </div>
				</div>';
            } else if ($this->field['date_format'] == "yy/mm/dd" || $this->field['date_format'] == "yy.mm.dd") {
                echo '<div class="piereg_time date_format_field">
				 <div class="time_fields">
					<input'.$disabled.' id="yy_' . $this->id . '" name="' . $this->slug . '[date][yy]" maxlength="4"  type="text" value="' . $date['date']['yy'] . '">
					<label>'.__("yy","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input'.$disabled.' id="mm_' . $this->id . '" name="' . $this->slug . '[date][mm]" maxlength="2" type="text" value="' . $date['date']['mm'] . '">
					<label>'.__("MM","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input'.$disabled.' id="dd_' . $this->id . '" name="' . $this->slug . '[date][dd]" maxlength="2"  type="text" value="' . $date['date']['dd'] . '">
					<label>'.__("DD","piereg").'</label>
				  </div>				  
				</div>';
            } else {
                echo '<div class="piereg_time date_format_field">
				 <div class="time_fields">
					<input'.$disabled.' id="dd_' . $this->id . '" name="' . $this->slug . '[date][dd]" maxlength="2"  type="text" value="' . $date['date']['dd'] . '">
					<label>'.__("DD","piereg").'</label>
				  </div>	
				 <div class="time_fields">
					<input'.$disabled.' id="yy_' . $this->id . '" name="' . $this->slug . '[date][yy]" maxlength="4"  type="text" value="' . $date['date']['yy'] . '">
					<label>'.__("yy","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input'.$disabled.' id="mm_' . $this->id . '" name="' . $this->slug . '[date][mm]" maxlength="2" type="text" value="' . $date['date']['mm'] . '">
					<label>'.__("MM","piereg").'</label>
				  </div>				  			  
				</div>';
            }
        } 
		else if ($this->field['date_type'] == "datepicker") {
			$returned_date = ( isset($date['date'][0]) ) ? $date['date'][0] : "";
			echo '<div class="piereg_time date_format_field">
				  <input'.$disabled.' id="' . $this->id . '" class="date_start" name="' . $this->slug . '[date][]" readonly="readonly" value="' . $returned_date . '" type="text" ></div>';
        } else if ($this->field['date_type'] == "datedropdown") {
            echo '<div class="piereg_time date_format_field">
					<select'.$disabled.' id="mm_' . $this->id . '" name="' . $this->slug . '[date][mm]">
					  <option value="">'.__("Month","piereg").'</option>';
            for ($a = 1; $a <= 12; $a++) {
                $sel = '';
                if ((int) $a == (int) $date['date']['mm']) {
                    $sel = 'selected="selected"';
                }
                echo '<option ' . $sel . ' value="' . $a . '">' . __(sprintf("%02s", $a),"piereg") . '</option>';
            }
            echo '</select>
					<select'.$disabled.' id="dd_' . $this->id . '" name="' . $this->slug . '[date][dd]">
					  <option value="">'.__("Day","piereg").'</option>';
            for ($a = 1; $a <= 31; $a++) {
                $sel = '';
                if ((int) $a == (int) $date['date']['dd']) {
                    $sel = 'selected="selected"';
                }
                echo '<option ' . $sel . ' value="' . $a . '">' . __(sprintf("%02s", $a),"piereg") . '</option>';
            }
            echo '</select>
					<select'.$disabled.' id="yy_' . $this->id . '" name="' . $this->slug . '[date][yy]">
					  <option value="">'.__("Year","piereg").'</option>';
            for ($a = 2099; $a >= 1900; $a--) {
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

	function addList($disabled)
	{
		$list = get_user_meta($this->user_id, $this->slug, true);	#get_usermeta deprecated
		$width  = 90 /  $this->field['cols']; 
		for($a = 1 ,$c=0; $a <= $this->field['rows'] ; $a++,$c++)
		{
			echo '<div style="max-width:55%;">';
			for($b = 1,$d=0 ; $b <= $this->field['cols'] ;$b++,$d++)
			{
				if(!is_array($list))
				$list[$c][$d] = "";
				echo '<input'.$disabled.' value="'.$list[$c][$d].'" style="width:'.$width.'%;margin-right:2px;" type="text" name="'.$this->slug.'['.$c.'][]" class="input_fields"> ';
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
        $key_id = (isset($this->field['id'])) ? $this->field['id'] : "";
		return "field_" . $key_id;
    }

    function addLabel()
    {
		return '<label for="' . $this->id . '">' . $this->field['label'] . '</label>';
    }

    function printFields()
    {
        $update     = get_option(OPTION_PIE_REGISTER);
		$disabled 	= "";
		
		if( !current_user_can( 'administrator' ) ) {
			$disabled 	= ' disabled="disabled"';
		}
		
        switch ($this->type):
            case 'text':           
                $this->addTextField($disabled);
                break;
            case 'textarea':
                $this->addTextArea($disabled);
                break;
            case 'dropdown':
            case 'multiselect':
                $this->addDropdown($disabled);
                break;
            case 'number':
                $this->addNumberField($disabled);
                break;
            case 'radio':
            case 'checkbox':
                $this->addCheckRadio($disabled);
                break;
            case 'html':
                $this->addHTML(); 
                break;
            case 'time':
                $this->addTime($disabled);
                break;
            case 'upload':
                $this->addUpload($disabled);
                break;
            case 'profile_pic':
                $this->addProfilePic($disabled);
                break;
            case 'address':
                $this->addAddress($disabled);
                break;
            case 'captcha':
                $this->addCaptcha($disabled);
                break;
            case 'phone':
			/*
				*	Just Work 2 way login
			*/
            case 'two_way_login_phone':
                $this->addPhone($disabled);
             	break;
				
			case 'invitation':
				$this->addInvitation($disabled);
				break;
				
            case 'date':
                $this->addDate($disabled);
                break;
            case 'list':
                $this->addList($disabled);
                break;
        endswitch;
    }
    function edit_user_profile($user)
    {
		$form_id = "default";
		if(isset($_GET['user_id'])){
			$form_id = get_user_meta((intval($_GET['user_id'])),"user_registered_form_id",true);
		}elseif(is_admin()){
			$form_id = get_user_meta((intval($user->ID)),"user_registered_form_id",true);
		}
		$form_id = ((!empty($form_id)) ? $form_id : "default");
		$this->data = $this->getCurrentFields($form_id);
		
        if (sizeof($this->data) > 0 && is_array($this->data))
		{
           	$this->user_id = $user->ID;
			echo "<h3>". __('Pie-Register Registration Fields','piereg')."</h3>";
            echo '<table class="form-table">';
			
           	foreach ($this->data as $this->field) 
		   	{
			  	$key_id = (isset($this->field['id'])) ? $this->field['id'] : "";
             	$this->slug = $this->createFieldName($this->field['type']."_".$key_id);
                $this->type = $this->field['type'];
                $this->id   = $this->createFieldID();	   
				if((isset($this->field['show_in_profile']) and $this->field['show_in_profile'] == 0 ) && !is_admin())
					continue;
				/*
					*	Just work 2way login
				*/
				if($this->type == "two_way_login_phone")
				{
					$this->slug = "piereg_two_way_login_phone";
				}
				//When to add label
				switch($this->type) :
					case 'two_way_login_phone':
						include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
						$twilio_option = get_option("pie_register_twilio");
						$plugin_status = get_option('piereg_api_manager_addon_Twilio_activated');
						$pie_register_base = new PieReg_Base();
						if( is_plugin_active("pie-register-twilio/pie-register-twilio.php") && isset($twilio_option["enable_twilio"]) && $twilio_option["enable_twilio"] == 1 && $pie_register_base->piereg_pro_is_activate && $plugin_status == "Activated" ){
							echo '<tr><td>'.$this->addLabel().'</td><td>';
							echo $this->printFields().'</td></tr>';
						}
					break;
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
					/*
						*	Just Work 2 way login
					*/
					
					case 'invitation':
					case 'date':
					case 'list':
					case "default" && $this->default_fields:
						echo '<tr><td>'.$this->addLabel().'</td><td>';
						echo $this->printFields().'</td></tr>';
					break;
				endswitch;
			 }
           echo '</table>';
        }
    }

	function updateMyProfile($user_id) 
	{
     	if ( current_user_can( 'administrator' ) ) // only admin can update profile from WP Admin Profile Page
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
			
			/*
				*	Just Work 2way login
			*/
			if($this->field['type'] == "two_way_login_phone")
			{
				$this->slug = "piereg_two_way_login_phone";
			}
			
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
                if (!preg_match("/^([a-z 0-9])+$/i", $field_name)) {
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
		$form_id 	  	= get_user_meta($user_id,"user_registered_form_id",true);
		$this->data 	= $this->getCurrentFields($form_id);
		
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
				case 'invitation':
				case 'date':
				case 'list':
					$field_value = $_POST[$slug];
					update_user_meta($user_id,$slug, $field_value);
				break;
				/*
					*	Just work 2way login phone
				*/
				case "two_way_login_phone":
					$slug = "piereg_two_way_login_phone";
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
?>