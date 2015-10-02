<?php
function edit_userdata(){
	global $current_user;
	get_currentuserinfo();	
	$form = new Edit_form($current_user);
	$profile_fields_data = "";
	$profile_fields_data .= '<div class="pieregProfileWrapper pieregWrapper">
	<form enctype="multipart/form-data" id="pie_regiser_form" method="post" action="'.$_SERVER['REQUEST_URI'].'">
	<ul id="pie_register">';
	$output = $form->editProfile($current_user);
	$profile_fields_data .= $output ;
	$profile_fields_data .= '</ul>
	</form></div>';
	
	return $profile_fields_data;
}