<?php
function outputRegForm($fromwidget=false){
	
$users_can_register =  get_option("users_can_register");
if($users_can_register == 1){

$form 		= new Registration_form();
$success 	= '' ;
$error 		= '' ;
$option 	= get_option( 'pie_register_2' );
$registration_from_fields = '<div class="pieregformWrapper pieregWrapper"><style type="text/css">
.field_note{font-size:12px; color:#FF0000;}
.required{color:#FF0000}
</style>';
$registration_from_fields .= '<div id="show_pie_register_error_js" class="piereg_entry-content"></div>';

$registration_from_fields .= $form->addFormData();

$IsWidgetForm = "";
if($fromwidget)
	$IsWidgetForm = "widget_";

$registration_from_fields .= '<div id="pie_register_reg_form">';

$registration_from_fields .= '<form enctype="multipart/form-data" id="pie_'.(trim($IsWidgetForm)).'regiser_form" method="post" action="'.$_SERVER['REQUEST_URI'].'">';
if($form->countPageBreaks() > 1){
	$registration_from_fields .= '<div class="piereg_progressbar"></div>';
}
$registration_from_fields .= '<ul id="pie_register">';
$output = $form->printFields($fromwidget);
$registration_from_fields .= $output;
$registration_from_fields .= '</ul>	';
$registration_from_fields .= '</form>';

if($form->pages > 1)
{
	$registration_from_fields.= <<<EOL
	<script type="text/javascript">
	pieHideFields();
if(window.location.hash) 
{
	var hash = window.location.hash.substring(1); //Puts hash in variable, and removes the # character 
	var elms = document.getElementsByClassName('pageFields_'+hash);
	for(a = 0 ; a < elms.length ; a++)
	{
		elms[a].style.display = "";	
	}   
} 
else 
{
    var elms = document.getElementsByClassName('pageFields_1');
	for(a = 0 ; a < elms.length ; a++)
	{
		elms[a].style.display = "";	
	}   
}


</script>
EOL;

 }
 if($form->countPageBreaks() > 1){
	$registration_from_fields .= PieRegister::piereg_ProgressBarScripts($form->countPageBreaks());
}
 $registration_from_fields.='</div></div>';
return $registration_from_fields;
}
else{
	$registration_from_fields = '<div class="alert alert-warning"><p class="piereg_warning">'.__("User registration is currently not allowed.","piereg").'</p></div>';
    return $registration_from_fields;
}
}