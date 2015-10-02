<?php
require_once('base.php');
class Profile_front extends PieReg_Base
{
    var $field;
    var $user_id;
    var $slug;
    var $type;
    var $name;
    var $id;
    var $data;
	var $user;
	
    function __construct($user)
    {
        $this->data = $this->getCurrentFields();
		$this->user = $user;
		$this->user_id = $user->ID;			
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
        return '<label for="' . $this->id . '">' . __($this->field['label'],"piereg") . '</label>';
    }   
	function print_user_profile()
    {
		$data = "";
        if (sizeof($this->data) > 0) 
		{
          	$data .= '<h1 id="piereg_pie_form_heading">'.__("Profile Page","piereg").'</h1>';
			$data .= '<span class="piereg-profile-logout-url"><a href="'.wp_logout_url().'">'.__("Logout","piereg").'</a></span>';
            /*$data .= '<a class="piereg_edit_profile_link" href="?page_id='.((isset($_GET['page_id']))?$_GET['page_id']:"").'&edit_user=1"></a>';*/
            $data .= '<a class="piereg_edit_profile_link" href="'.( add_query_arg( array("edit_user" => "1"), $this->piereg_get_current_url() ) ).'"></a>';
		    $data .= '<table border="0" cellpadding="0" cellspacing="0" class="pie_profile" id="pie_register">';
			if(is_array($this->data)){
				   foreach ($this->data as $this->field)
				   {
             	$this->slug = $this->createFieldName($this->field['type']."_".((isset($this->field['id']))?$this->field['id']:""));
                $this->type = $this->field['type'];
                $this->id   = $this->createFieldID();	
				
				if($this->type=="default")
					 $this->slug   = $this->field['field_name'];
			   
			   if(isset($this->field['show_in_profile']) && $this->field['show_in_profile']=="0")
			   {
			   		continue;
			   }
			  
				//When to add label
				switch($this->type) :				
					case 'password':
					case 'form':
					continue;
					break;
					
					
					case 'username' :
					$data .= '<tr><td class="fields fields2">'.$this->addLabel();
					$data .= '</td><td class="fields"><span>'.$this->user->data->user_login.'</span></td></tr>';
					break;
					
					case 'email' :
					$data .= '<tr><td class="fields fields2">'.$this->addLabel();
					$data .= '</td><td class="fields"><span>'.$this->user->data->user_email.'</span></td></tr>';
					break;
					
					case 'default' &&  $this->slug=="url":											
					$data .= '<tr><td class="fields fields2">'.$this->addLabel();
					$data .= '</td><td class="fields"><span>'.$this->user->data->user_url.'</span></td></tr>';
					break;
					
					
					case 'name':
					//if($this->field['show_in_profile']){
						$this->slug = "first_name";
						$data .= '<tr><td class="fields fields2"><label>'.__($this->field['label'],"piereg").'</label>';
						$data .= '</td><td class="fields"><span>'.$this->getValue().'</span></td></tr>';
						
						$this->slug = "last_name";
						$data .= '<tr><td class="fields fields2"><label>'.__($this->field['label2'],"piereg").'</label>';
						$data .= '</td><td class="fields"><span>'.$this->getValue().'</span></td></tr>';
					//}
					break;
										
					
					case 'profile_pic':
					$data .= '<tr><td class="fields fields2">'.$this->addLabel();
					$imgPath = (trim($this->getValue($this->type, $this->slug)) != "")? $this->getValue($this->type, $this->slug) : plugins_url("images/userImage.png",dirname(__FILE__));
					global $current_user;
					$imgPath = apply_filters("piereg_profile_image_url",$imgPath,$current_user);
					$data .= '</td><td class="fields"><img src="'.$imgPath.'" style="max-width:150px;" /></td></tr>';
					break;			
					case 'upload':
					$data .= '<tr><td class="fields fields2">'.$this->addLabel();
					$upload_file_value = $this->getValue($this->type, $this->slug);
					$data .= '</td><td class="fields"><a href="'.$upload_file_value.'" target="_blank">'.basename($upload_file_value).'</a></td></tr>';
					break;
					case 'text' :
					case 'textarea':
					case 'dropdown':
					case 'multiselect':
					case 'number':
					case 'radio':
					case 'checkbox':													
					case 'time':	
					case 'address':							
					case 'phone':				
					case 'date':				
					case 'list':
					case "default":
					$data .= '<tr><td class="fields fields2">'.$this->addLabel();
					$data .= '</td><td class="fields"><span>'.$this->getValue($this->type, $this->slug).'</span></td></tr>';
					break;	
									
				endswitch; 
			 }
			 }
           $data .= '</table>';
	
        }
		return $data;
    }
	function getValue()
	{
		//$value = get_usermeta($this->user_id, $this->slug);
		$value = get_user_meta($this->user_id, $this->slug,true);
		if($this->type=="date")
		{
			if(isset($value['date']) && is_array($value['date']))
			{
				if($this->field['date_format']=="datefield" || $this->field['date_format']=="datedropdown" )
				{
					$val = $this->field['date_format'];
					$val = str_replace("mm",$value['date']['mm'],$val);
					$val = str_replace("dd",$value['date']['dd'],$val);
					$val = str_replace("yy",$value['date']['yy'],$val);
					return 	$val;
				}
				else
				{
					return implode($this->field['date_format'][2],$value['date']);			
				}
				
				
			}
			return $value;			
		}
		else if($this->type=="time")
		{
			if(is_array($value))
			return implode(" : ",$value);	
			return $value;
		}
		else if($this->type=="list")
		{
			if(!is_array($value))
			return $value;
			$list = "";
			$list = '<table class="piereg_custom_list '.$this->slug.'">';
			for($a = 0 ; $a < sizeof($value) ; $a++)
			{
				if(array_filter($value[$a])){
				$list .= '<tr>';
				$row  = "";
				for($b = 0 ; $b < sizeof($value[$a]) ; $b++)
				{
					$row 	.= $value[$a][$b];
					$list 	.= '<td>'.$value[$a][$b]."</td>";
				}
				if(!empty($row))
				//$list .= "<br />";
				$list .= '</tr>';
				}
			}
			$list .= '</table>';
			
			$value = $list ;	
		}
		else if($this->type=="multiselect")
		{
			$list = "<ol>";
			for($a = 0 ; $a < sizeof($value) ; $a++ )
			{
				if(isset($value[$a]))
					$list .= "<li>".$value[$a]."</li>";	
			}	
			$list .= "</ol>";
			$value = $list;	
		}
		else if(is_array($value))
		{
			$value = array_filter($value);
			return implode(", ",$value);	
		}
		return $value;	
	}	
}