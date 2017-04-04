<?php
if( file_exists( dirname(__FILE__) . '/base.php') ) 
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

    function __construct($user,$form_id = "default")
    {
        $this->data = $this->getCurrentFields($form_id);
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

	function print_user_profile($form_id = "default")
    {
		$data = "";
        if (sizeof($this->data) > 0) 
		{
			$data .= '<div class="piereg_profile_cont">';
          	$data .= '<h1 id="piereg_pie_form_heading">'.__("Profile Page","piereg").'</h1>';
			$data .= '<span class="piereg-profile-logout-url"><a href="'.wp_logout_url().'">'.__("Logout","piereg").'</a></span>';
            $data .= '<a class="piereg_edit_profile_link" href="' . (add_query_arg( array("edit_user" => "1"), $this->piereg_get_current_url() )) . '"></a>';
		    $data .= '<table border="0" cellpadding="0" cellspacing="0" class="pie_profile" id="pie_register">';
			
			if(is_array($this->data)){
				foreach ($this->data as $this->field)
				{
					// check status on fields having conditional logics
					if( (isset($this->field['conditional_logic']) && $this->field['conditional_logic']==1) && !isset($this->field['notification']) )
					{
						$conitional_logic_status = $this->checkIfConditionApplied( $this->field );
						
						// if condtion apply on show fields
						if( (isset($this->field['field_status']) && $this->field['field_status']==1) && $conitional_logic_status == false) {
							continue;
						}
						
						// if condtion apply on hide fields
						if( (isset($this->field['field_status']) && $this->field['field_status']==0) && $conitional_logic_status == true) {
							continue;
						}
						
					}
					
					$this->slug = $this->createFieldName($this->field['type']."_".((isset($this->field['id']))?$this->field['id']:""));
					$this->type = $this->field['type'];
					$this->id   = $this->createFieldID();	
					if($this->type=="default")
						 $this->slug   = $this->field['field_name'];
					/*
						*	Just Work 2Way Login Phone
					*/
					elseif($this->type == "two_way_login_phone")
							$this->slug = "piereg_two_way_login_phone";
							
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
							$this->slug = "first_name";
							$data .= '<tr><td class="fields fields2"><label>'.__($this->field['label'],"piereg").'</label>';
							$data .= '</td><td class="fields"><span>'.$this->getValue().'</span></td></tr>';
							$this->slug = "last_name";
							$data .= '<tr><td class="fields fields2"><label>'.__($this->field['label2'],"piereg").'</label>';
							$data .= '</td><td class="fields"><span>'.$this->getValue().'</span></td></tr>';
						break;
						case 'profile_pic':
							$data .= '<tr><td class="fields fields2">'.$this->addLabel();
							$imgPath = (trim($this->getValue($this->type, $this->slug)) != "")? $this->getValue($this->type, $this->slug) : plugins_url("images/userImage.png",dirname(__FILE__));
							global $current_user;
							$imgPath = apply_filters("piereg_profile_image_url",$imgPath,$current_user);
							$data .= '</td><td class="fields"><span><img src="'.$imgPath.'" style="max-width:150px;" /></span></td></tr>';
						break;			
						case 'upload':
							$data .= '<tr><td class="fields fields2">'.$this->addLabel();
							$upload_file_value = $this->getValue($this->type, $this->slug);
							$data .= '</td><td class="fields"><a class="uploaded_file" href="'.$upload_file_value.'" target="_blank">'.basename($upload_file_value).'</a></td></tr>';
						break;						
						case 'address':
							$data .= '<tr><td class="fields fields2" style="vertical-align:top;">'.$this->addLabel();
							$data .= '</td><td class="fields"><span>'.$this->getValue($this->type, $this->slug).'</span></td></tr>';
						break;
						case 'two_way_login_phone':
							include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
							$twilio_option = get_option("pie_register_twilio");
							$plugin_status = get_option('piereg_api_manager_addon_Twilio_activated');
							$pie_register_base = new PieReg_Base();
							if( is_plugin_active("pie-register-twilio/pie-register-twilio.php") && isset($twilio_option["enable_twilio"]) && $twilio_option["enable_twilio"] == 1 && $pie_register_base->piereg_pro_is_activate && $plugin_status == "Activated" ){
								$data .= '<tr><td class="fields fields2">'.$this->addLabel();
								$data .= '</td><td class="fields"><span>'.$this->getValue($this->type, $this->slug).'</span></td></tr>';
							}
						break;
						case 'text' :
						case 'textarea':
						case 'dropdown':
						case 'multiselect':
						case 'number':
						case 'radio':
						case 'checkbox':
						case 'time':
						case 'phone':
						case 'date':
						case 'list':						
						case 'invitation':
						case "default":
							$data .= '<tr><td class="fields fields2">'.$this->addLabel();
							$data .= '</td><td class="fields"><span>'.$this->getValue().'</span></td></tr>';
						break;
					endswitch;
			 	}
			 }
           $data .= '</table>';
           $data .= '</div>';
        }
		return $data;
    }
	
}