<?php

function edit_userdata($form_id = "default"){
	global $current_user;
	get_currentuserinfo();
	$form 		= new Edit_form_template($current_user,$form_id);
	$profile_fields_data = "";
	$profile_fields_data .= '<div class="pieregProfileWrapper pieregWrapper">
	<form enctype="multipart/form-data" id="pie_regiser_form" method="post" action="'.$_SERVER['REQUEST_URI'].'">';
	$output = $form->editProfile($current_user);
	$profile_fields_data .= $output ;
	$profile_fields_data .= '</ul></form></div>';
	return $profile_fields_data;
}