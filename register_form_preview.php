<?php
//For backwards compatibility, load wordpress if it hasn't been loaded yet
//Will be used if this file is being called directly
if(!class_exists("PieRegister")){
    for ( $i = 0; $i < $depth = 10; $i++ ) {
        $wp_root_path = str_repeat( '../', $i );

        if ( file_exists("{$wp_root_path}wp-load.php" ) ) {
            require_once("{$wp_root_path}wp-load.php");
            require_once("{$wp_root_path}wp-admin/includes/admin.php");
            break;
        }
    }

    //redirect to the login page if user is not authenticated
    auth_redirect();
}



?>
<html>
<head>
<title><?php _e("Form Preview", "piereg") ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="<?php echo plugins_url('pie-register'); ?>/css/front.css"  />
<script type='text/javascript' src='<?php echo plugins_url("js/jquery.js",__FILE__)?>'></script>
<?php wp_enqueue_script('password-strength-meter'); ?>
<script type='text/javascript' src='<?php echo includes_url("/js/zxcvbn-async.js")?>'></script>
<script type='text/javascript' src='<?php echo admin_url("js/password-strength-meter.min.js")?>'></script>


<script type='text/javascript' src='<?php echo get_bloginfo("url");?>/wp-admin/js/password-strength-meter.min.js'></script>
<script type='text/javascript' src='<?php echo plugins_url("js/jquery-ui.js",__FILE__)?>'></script>
<script type='text/javascript' src='<?php echo plugins_url("js/datepicker.js",__FILE__)?>'></script>
<script type='text/javascript' src='<?php echo plugins_url("js/piereg_validation.js",__FILE__)?>'></script>
<script type='text/javascript' src='<?php echo plugins_url("js/pie_password_checker.js",__FILE__)?>'></script>
<!--<script type='text/javascript' src='<?php echo includes_url("/js/zxcvbn.min.js")?>'></script>-->


<?php
global $piereg_global_options;
$this->print_multi_lang_script_vars();
?>
<script type="text/javascript">
var piereg = jQuery.noConflict();
var piereg_pass_str_meter_string = new Array(
											 '<?php _e("Strength Indicator","piereg");?>',
											 '<?php _e("Very weak","piereg");?>',
											 '<?php _e("Weak","piereg");?>',
											 '<?php _e("Medium","piereg");?>',
											 '<?php _e("Strong","piereg");?>',
											 '<?php _e("Mismatch","piereg");?>'
											 );
var piereg_current_date		= '<?php echo date("Y"); ?>';
var piereg_startingDate		= '<?php echo $piereg_global_options['piereg_startingDate']; ?>';
var piereg_endingDate		= '<?php echo $piereg_global_options['piereg_endingDate']; ?>';

var piereg_validation_engn = new Array(
										 '<?php _e("none","piereg");?>',
										 '<?php _e("* This field is required","piereg");?>',
										 '<?php _e("* Please select an option","piereg");?>',
										 '<?php _e("* This checkbox is required","piereg");?>',
										 '<?php _e("* Both date range fields are required","piereg");?>',
										 '<?php _e("* Field must equal test","piereg");?>',
										 '<?php _e("* Invalid ","piereg");?>',
										 '<?php _e("Date Range","piereg");?>',
										 '<?php _e("Date Time Range","piereg");?>',
										 '<?php _e("* Minimum ","piereg");?>',
										 '<?php _e(" characters required","piereg");?>',
										 '<?php _e("* Maximum ","piereg");?>',
										 '<?php _e(" characters allowed","piereg");?>',
										 '<?php _e("* You must fill one of the following fields","piereg");?>',
										 '<?php _e("* Minimum value is ","piereg");?>',
										 '<?php _e("* Date prior to ","piereg");?>',
										 '<?php _e("* Date past ","piereg");?>',
										 '<?php _e(" options allowed","piereg");?>',
										 '<?php _e("* Please select ","piereg");?>',
										 '<?php _e(" options","piereg");?>',
										 '<?php _e("* Fields do not match","piereg");?>',
										 '<?php _e("* Invalid credit card number","piereg");?>',
										 '<?php _e("* Invalid phone number","piereg");?>',
										 '<?php _e("* Allowed Format (xxx) xxx-xxxx","piereg");?>',
										 '<?php _e("* Minimum 10 Digits starting with Country Code","piereg");?>',
										 '<?php _e("* Invalid email address","piereg");?>',
										 '<?php _e("* Not a valid integer","piereg");?>',
										 '<?php _e("* Invalid number","piereg");?>',
										 '<?php _e("* Invalid month","piereg");?>',
										 '<?php _e("* Invalid day","piereg");?>',
										 '<?php _e("* Invalid year","piereg");?>',
										 '<?php _e("* Invalid file extension","piereg");?>',
										 '<?php _e("* Invalid date, must be in YYYY-MM-DD format","piereg");?>',
										 '<?php _e("* Invalid IP address","piereg");?>',
										 '<?php _e("* Invalid URL","piereg");?>',
										 '<?php _e("* Numbers only","piereg");?>',
										 '<?php _e("* Letters only","piereg");?>',
										 '<?php _e("* No special characters allowed","piereg");?>',
										 '<?php _e("* This user is already taken","piereg");?>',
										 '<?php _e("* Validating, please wait","piereg");?>',
										 '<?php _e("* This username is available","piereg");?>',
										 '<?php _e("* This user is already taken","piereg");?>',
										 '<?php _e("* Validating, please wait","piereg");?>',
										 '<?php _e("* This name is already taken","piereg");?>',
										 '<?php _e("* This name is available","piereg");?>',
										 '<?php _e("* Validating, please wait","piereg");?>',
										 '<?php _e("* This name is already taken","piereg");?>',
										 '<?php _e("* Please input HELLO","piereg");?>',
										 '<?php _e("* Invalid Date","piereg");?>',
										 '<?php _e("* Invalid Date or Date Format","piereg");?>',
										 '<?php _e("Expected Format: ","piereg");?>',
										 '<?php _e("mm/dd/yyyy hh:mm:ss AM|PM or ","piereg");?>',
										 '<?php _e("yyyy-mm-dd hh:mm:ss AM|PM","piereg");?>',
										 '<?php _e("* Invalid Username","piereg");?>',
										 '<?php _e("* Invalid File","piereg");?>'
										 );
</script>






</head>
<body class="piereg_preview_page">
<div class="piereg_main_wrapper pieregWrapper"  id="pie_register_reg_form">
<?php
global $errors;
$errors = new WP_Error();
//Printing Success Message
if(isset($_POST['success']) && $_POST['success'] != "")
	echo '<p class="piereg_message">'.apply_filters('piereg_messages',__($_POST['success'],"piereg")).'</p>';

if(sizeof($errors->errors) > 0)
{
	foreach($errors->errors as $err)
	{
		$error .= $err[0] . "<br />";	
	}
	echo '<p class="piereg_login_error">'.apply_filters('piereg_messages',__($error,"piereg")).'</p>';
}
	
echo $form->addFormData();


if($form->countPageBreaks() > 1){
		
?>
<div class="pieregformWrapper">
<div class="piereg_progressbar"></div>
<?php
echo PieRegister::piereg_ProgressBarScripts($form->countPageBreaks());
}
?>
<form enctype="multipart/form-data" id="pie_regiser_form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
  <ul id="pie_register">
    <?php
	echo $form->printFields();
?>
  </ul>
</form>
</div>
<script type="text/javascript">
wp_custom_login_remove_element('wp-admin-css');
wp_custom_login_remove_element('colors-fresh-css');

function wp_custom_login_remove_element(id) 
{
	if(!document.getElementById(id))
	return false;
	var element = document.getElementById(id);
	element.parentNode.removeChild(element);
}
<?php 
if($form->pages > 1)
{
?>
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
<?php } ?>
</script>

</div>
</body>
</html>