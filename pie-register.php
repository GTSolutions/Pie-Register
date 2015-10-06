<?php
/*
Plugin Name: Pie Register
Plugin URI: http://pieregister.genetechsolutions.com/
Description: <strong>WordPress 3.5 + ONLY.</strong> Enhance your Registration form, Custom logo, Password field, Invitation codes, Paypal, Captcha validation, Email verification and more.

Author: Genetech Solutions
Version: 2.0.20
Author URI: http://www.genetechsolutions.com/
GitHub Plugin URI: https://github.com/GTSolutions/Pie-Register
GitHub Branch:     master
CHANGELOG
See readme.txt
*/

$piereg_dir_path = dirname(__FILE__);
define('PIEREG_DIR_NAME',$piereg_dir_path);
if(!defined('PIEREG_DB_VERSION'))
	define('PIEREG_DB_VERSION','2.0.13');

if(!defined('PIEREG_PLUGIN_BASENAME'))
	define('PIEREG_PLUGIN_BASENAME',plugin_basename( __FILE__ ));

define('LOG_FILE', '.ipn_results.log');
define('SSL_P_URL', 'https://www.paypal.com/cgi-bin/webscr');
define('SSL_SAND_URL','https://www.sandbox.paypal.com/cgi-bin/webscr');

global $pie_success,$pie_error,$pie_error_msg,$pie_suucess_msg, $pagenow,$action,$profile,$errors;
global $piereg_math_captcha_register,$piereg_math_captcha_register_widget,$piereg_math_captcha_login,$piereg_math_captcha_login_widget,$piereg_math_captcha_forgot_pass,$piereg_math_captcha_forgot_pass_widget;

$piereg_math_captcha_register = false;
$piereg_math_captcha_register_widget = false;
$piereg_math_captcha_login = false;
$piereg_math_captcha_login_widget = false;
$piereg_math_captcha_forgot_pass = false;
$piereg_math_captcha_forgot_pass_widget = false;

require_once(PIEREG_DIR_NAME.'/dash_widget.php');
require_once(PIEREG_DIR_NAME.'/classes/base.php');
require_once(PIEREG_DIR_NAME.'/classes/profile_admin.php');
require_once(PIEREG_DIR_NAME.'/classes/profile_front.php');
require_once(PIEREG_DIR_NAME.'/classes/registration_form.php');
require_once(PIEREG_DIR_NAME.'/classes/edit_form.php');
require_once(PIEREG_DIR_NAME.'/widget.php');
if( !class_exists('PieRegister') ){
	class PieRegister extends PieReg_Base{
		//public static $instance;
		public static $pieinstance;
		var	$pie_success;
		var	$pie_error;
		var	$pie_error_msg;
		var	$pie_success_msg;	
		private $ipn_status;
		public $txn_id;
		public $ipn_log;
		private $ipn_response;
		public $ipn_data = array();
		public $postvars;
		private $ipn_debug;
		/*
			*	Add since 2.0.13
		*/
		private $piereg_jquery_enable = false;
	
		
		function __construct()
		{
			$this->ipn_status = '';
			$this->txn_id = null;
			$this->ipn_log = true;
			$this->ipn_response = '';
			$this->ipn_debug = false;
	
			
			
			self::$pieinstance = $this;
			
			/***********************/
			parent::__construct();
			global $pagenow,$wp_version,$profile;
			
			$errors = new WP_Error();
			
			add_action('wp_ajax_get_meta_by_field', array($this,'getMeta'));
			
			
			add_action('wp_ajax_check_username',  array($this,'unique_user' ));
			add_action('wp_ajax_nopriv_check_username',  array($this,'unique_user' ));	
			
			add_action( 'admin_init', array($this,'piereg_register_scripts') );
			#Adding Menus
			add_action( 'admin_menu',  array($this,'AddPanel') );
			
			#plugin page links
			add_filter( 'plugin_action_links' , array($this,'add_action_links'),10,2 );
			
			//Add paypal payment method
			add_action("check_payment_method_paypal", array($this, "check_payment_method_paypal"));
			
			
			//Adding "embed form" button      
			add_action('media_buttons_context', array($this, 'add_pie_form_button'));
			
			if(in_array(basename($_SERVER['PHP_SELF']), array('post.php', 'page.php', 'page-new.php', 'post-new.php'))){
				add_action('admin_footer',  array($this, 'add_pie_form_popup'));
			}
			
			#Adding Short Code Functionality
			add_shortcode( 'pie_register_login',  array($this,'showLoginForm') );	
			add_shortcode( 'pie_register_form',  array($this,'showForm') );		
			add_shortcode( 'pie_register_profile', array($this,'showProfile') );
			add_shortcode( 'pie_register_forgot_password',  array($this,'showForgotPasswordForm') );
			add_shortcode( 'pie_register_renew_account',  array($this,'show_renew_account') );		
			
			
			#Genrate Warnings
			add_action('admin_notices', array($this, 'warnings'),20);
			
			add_action( 'init', array($this,'pie_main') );	
				
			$profile = new Profile_admin();
			add_action('show_user_profile',array($profile,"edit_user_profile"));
			add_action('personal_options_update',array($profile,"updateMyProfile"));
			
			add_action('edit_user_profile',array($profile,"edit_user_profile"));
			add_action('edit_user_profile_update', array($profile,'updateProfile'));	
			
			add_action( 'widgets_init', array($this,'initPieWidget'));
			
			add_action('get_header', array($this,'add_ob_start'));
			//It will redirect the User to the home page if the curren tpage is a alternate login page
			add_filter('get_header', array($this,'checkLoginPage'));
			
			add_action('payment_validation_paypal',	array($this, 'payment_validation_paypal'));
				
			add_action("Add_payment_option",		array($this,"Add_payment_option"));
			add_action("add_payment_method_script", array($this,"add_payment_method_script"));
			add_action("add_select_payment_script",	 array($this,"add_select_payment_script"));
			add_action("get_payment_content_area",	 array($this,"get_payment_content_area"));
			
			add_action("show_icon_payment_gateway",	array($this,"show_icon_payment_gateway"));
			
			add_filter("piereg_messages",array($this,"modify_all_notices"));
			
			
			/*update update_invitation_code form ajax*/
			add_action( 'wp_ajax_pireg_update_invitation_code', array($this,'pireg_update_invitation_code_cb_url' ));
			add_action( 'wp_ajax_nopriv_pireg_update_invitation_code', array($this,'pireg_update_invitation_code_cb_url' ));
			//add_action( 'admin_enqueue_scripts' ,array($this,'pie_admin_menu_style_enqueu') );
			////FRONT END SCRIPTS
			add_action('wp_head',array($this,'pie_frontend_ajaxurl'));
			add_action('wp_enqueue_scripts',array($this,'pie_frontend_enqueu_scripts'));
			
			/*
				*	Add sub links in wp plugin's page
			*/
			add_filter( 'plugin_row_meta', array( $this, 'piereg_plugin_row_meta' ), 10, 2 );
			
		}
		function pie_admin_menu_style_enqueu(){
			wp_register_style( 'pie_menu_style_css', plugins_url("/css/piereg_menu_style.css",__FILE__),false,'2.0', "all" );
			wp_enqueue_style( 'pie_menu_style_css' );
		}
		
		function piereg_register_scripts(){
			wp_register_script('pie_calendarcontrol_js',plugins_url("/js/CalendarControl.js",__FILE__),'jquery','2.0',false);
			wp_register_script('pie_datepicker_js',plugins_url("/js/datepicker.js",__FILE__),'jquery','2.0',false);
			wp_register_script('pie_drag_js',plugins_url("/js/drag.js",__FILE__),'jquery','2.0',false);
			wp_register_script('pie_mCustomScrollbar_js',plugins_url("/js/jquery.mCustomScrollbar.min.js",__FILE__),'jquery','2.0',false);
			wp_register_script('pie_mousewheel_js',plugins_url("/js/jquery.mousewheel.min.js",__FILE__),'jquery','2.0',false);
			wp_register_script('pie_sidr_js',plugins_url("/js/jquery.sidr.min.js",__FILE__),'jquery','2.0',false);
			wp_register_script('pie_phpjs_js',plugins_url("/js/phpjs.js",__FILE__),'jquery','2.0',false);
			wp_register_script('pie_registermain_js',plugins_url("/js/pie-register-main.js",__FILE__),'jquery','2.0',false);
			wp_register_script('pie_regs_js',plugins_url("/js/pie_regs.js",__FILE__),'jquery','2.0',false);
			wp_register_script('pie_tooltip_js',plugins_url("/js/tooltip.js",__FILE__),'jquery','2.0',false);
			wp_register_script('pie_validation_js',plugins_url("/js/piereg_validation.js",__FILE__),'jquery','2.0',false);
			wp_register_script('pie_password_checker',plugins_url("/js/pie_password_checker.js",__FILE__),'jquery','2.0',false);
			
			/////////////////////////////////////////////////
			wp_register_style( 'pie_jqueryui_css', plugins_url("/css/jquery-ui.css",__FILE__),false,'2.0', "all" );
			wp_register_style( 'pie_front_css', plugins_url("/css/front.css",__FILE__),false,'2.0', "all" );
			wp_register_style( 'pie_mCustomScrollbar_css', plugins_url("/css/jquery.mCustomScrollbar.css",__FILE__),false,'2.0', "all" );
			wp_register_style( 'pie_style_css', plugins_url("/css/style.css",__FILE__),false,'2.0', "all" );
			wp_register_style( 'pie_tooltip_css', plugins_url("/css/tooltip.css",__FILE__),false,'2.0', "all" );
			wp_register_style( 'pie_validation_css', plugins_url("/css/validation.css",__FILE__),false,'2.0', "all" );
		}
		function print_multi_lang_script_vars(){
			global $piereg_global_options;
			$options = $piereg_global_options;
			?>
			<script type="text/javascript">
				var piereg_pass_str_meter_string = new Array(
															 '<?php echo (($options['pass_strength_indicator_label'])?__($options['pass_strength_indicator_label'],"piereg"):"");?>',
															 '<?php echo (($options['pass_very_weak_label'])?__($options['pass_very_weak_label'],"piereg"):"");?>',
															 '<?php echo (($options['pass_weak_label'])?__($options['pass_weak_label'],"piereg"):"");?>',
															 '<?php echo (($options['pass_medium_label'])?__($options['pass_medium_label'],"piereg"):"");?>',
															 '<?php echo (($options['pass_strong_label'])?__($options['pass_strong_label'],"piereg"):"");?>',
															 '<?php echo (($options['pass_mismatch_label'])?__($options['pass_mismatch_label'],"piereg"):"");?>'
															 );
				
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
													 '<?php _e("* Invalid File","piereg");?>',
													 '<?php _e("* Maximum value is ","piereg");?>',
													 '<?php _e("* Alphabetic Letters only","piereg");?>'
													 );
			</script>
			<?php
			
		}
		function pie_frontend_enqueu_scripts(){
			global $piereg_global_options;
			$this->print_multi_lang_script_vars();
			?>
			<script type="text/javascript">
				var piereg_current_date		= '<?php echo date("Y"); ?>';
				var piereg_startingDate		= '<?php echo $piereg_global_options['piereg_startingDate']; ?>';
				var piereg_endingDate		= '<?php echo $piereg_global_options['piereg_endingDate']; ?>';
			</script>
			<?php
			if(isset($piereg_global_options['outputcss']) && $piereg_global_options['outputcss'] == 1)
			{
				wp_enqueue_style('pie_front_css');
				wp_enqueue_style('pie_validation_css');
			}
			////////////////////////////////////////////
			if(isset($piereg_global_options['outputjquery_ui']) && $piereg_global_options['outputjquery_ui'] == 1)
			{
				wp_deregister_script('jquery-ui-core');
				wp_register_script('jquery-ui-core', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js',array('jquery'),'1.10.4',false);
				wp_enqueue_script('jquery-ui-core');
				//wp_enqueue_script('jquery-ui-datepicker');
			}
			wp_enqueue_script("pie_datepicker_js");
			wp_enqueue_script("pie_validation_js");
			wp_enqueue_script('zxcvbn-async');
			wp_enqueue_script('password-strength-meter');
			wp_enqueue_script('pie_password_checker');
		}
		function pie_frontend_ajaxurl(){
			?>
			<script type="text/javascript">
				var ajaxurl 				= '<?php echo admin_url('admin-ajax.php'); ?>';
			</script>
			<?php
		}
		function pie_admin_enqueu_scripts(){
			
			?>
			<script type="text/javascript">
				var piereg_wp_comtent_url 		= '<?php echo content_url(); ?>';
				var piereg_wp_home_url 			= '<?php echo home_url(); ?>';
				var piereg_wp_site_url 			= '<?php echo site_url(); ?>';
				var piereg_wp_admin_url 		= '<?php echo admin_url(); ?>';
				var piereg_wp_includes_url 		= '<?php echo includes_url(); ?>';
				var piereg_wp_plugins_url 		= '<?php echo plugins_url(); ?>';
				var piereg_wp_pie_register_url 	= '<?php echo plugins_url("pie-register"); ?>';
			</script>
			<?php
				global $piereg_global_options;
				$this->print_multi_lang_script_vars();
				?>
				<script type="text/javascript">
					var piereg_current_date		= '<?php echo date("Y"); ?>';
					var piereg_startingDate		= '<?php echo $piereg_global_options['piereg_startingDate']; ?>';
					var piereg_endingDate		= '<?php echo $piereg_global_options['piereg_endingDate']; ?>';
				</script>
			<?php
			wp_enqueue_style('pie_jqueryui_css');
			wp_enqueue_style( 'pie_mCustomScrollbar_css' );
			
			wp_enqueue_style( 'pie_style_css' );
			wp_enqueue_style('pie_tooltip_css');
			wp_enqueue_style('pie_validation_css');
			////////////////////////////////////////////
			/*
				*	Add Since 2.0.13
			*/
			if($this->piereg_jquery_enable){
				wp_deregister_script('jquery');
				wp_register_script('jquery', (plugins_url("/js/jquery.js",__FILE__)),"",'2.0.0',false);
				wp_enqueue_script( 'jquery' );
			}
			
			wp_enqueue_script( 'jquery-ui-core' );	
			wp_enqueue_script('pie_calendarcontrol_js');	
			wp_enqueue_script("pie_datepicker_js");
			wp_enqueue_script("pie_drag_js");
			wp_enqueue_script("pie_mCustomScrollbar_js");
			wp_enqueue_script("pie_mousewheel_js" );
			wp_enqueue_script( 'pie_sidr_js' );	
			wp_enqueue_script("pie_phpjs_js");
			wp_enqueue_script("pie_registermain_js");
			wp_enqueue_script("pie_regs_js" );
			wp_enqueue_script("pie_tooltip_js" );
			wp_enqueue_script("pie_validation_js" );
		}
		function modify_all_notices($notice)
		{
			$Start_notice = "";/*Write your message*/
			$End_notice = "";/*Write your message*/
			return $Start_notice.$notice.$End_notice;
		}
		
		function initPieWidget()
		{
			register_widget( 'Pie_Register_Widget' );
			register_widget( 'Pie_Login_Widget' );
			register_widget( 'Pie_Forgot_Widget' );	
		}
		
		
		//Plugin Menu Link
		function add_action_links( $links, $file ) 
		{
			 if ( $file != plugin_basename( __FILE__ ))
				return $links;
			
			$links[] = '<a href="'. get_admin_url(null, 'admin.php?page=pie-general-settings') .'">'.(__("General Settings","piereg")).'</a>';   		
			return $links;
		}
		function pie_main()
		{
			//LOCALIZATION
			load_plugin_textdomain( 'piereg', false, dirname(plugin_basename(__FILE__)) . '/lang/');
			
			$pie_plugin_db_version = get_option('piereg_plugin_db_version');
			if($pie_plugin_db_version != PIEREG_DB_VERSION){
				$this->install_settings();
			}
			//$option = get_option( 'pie_register_2' );
			global $piereg_global_options;
			$option = $piereg_global_options;
			
			/*********************************************/
			/////////////// PIEREG LOGOUT ////////////////
			if( (isset($_GET['piereg_logout_url']) and isset($_GET['redirect_to'])) and ($_GET['piereg_logout_url'] == "true") ){
				$redirect_to = (!empty($_GET['redirect_to']))? urldecode($_GET['redirect_to']) : home_url();
				wp_logout();
				wp_redirect($redirect_to);
				exit;
			}
			/*********************************************/
			/////////// Register Scripts ////////////
			$this->piereg_register_scripts();
			////////////////////////////////////////
			if(isset($_REQUEST['action']) && $_REQUEST['action'] == "check_username")
				return;
			
			global $pagenow;
			
			if($option['custom_css'] != "")
				add_action('wp_head', array($this,'addCustomCSS'));
			if( $option['tracking_code'] != "")
				add_action('wp_footer', array($this,'addCustomScripts'));
			
			// check to prevent php "notice: undefined index" msg
			$theaction ='';	
			
			if(isset($_GET['pr_preview']) && $_GET['pr_preview']==1) 
			{
				global $errors;		
				$form 		= new Registration_form();
				$success 	= '' ;
				
				include_once($this->plugin_dir."/register_form_preview.php");			
				exit;			
			}elseif((isset($_GET['show_dash_widget']) && $_GET['show_dash_widget']==1) and (isset($_GET['invitaion_code']) && $_GET['invitaion_code']!=""))
			{
				$this->show_invitaion_code_user();
			}
			
			if(isset($_GET['action'])) 
				$theaction = $_GET['action']; 
				 
				
			//PAYPAL VALIDATION
			$this->ValidPUser();
			
			#Save Settings
			if( isset($_POST['action']) && $_POST['action'] == 'pie_reg_update' )
			{$this->SaveSettings();}
			if(isset($_POST['Remove_license_x']) and ((int)$_POST['Remove_license_x']) != "0" and isset($_POST['Remove_license_y']))
			{$this->Remove_license_Key();}
			
			add_filter('allow_password_reset',array($this,'checkUserAllowedPassReset'),20,2);
			
			
			#Reset Settings to default
			if( isset($_POST['piereg_default_settings']) )
			{
				$this->piereg_default_settings();
			}
			
			#Admin Verify Users
			if( isset($_POST['verifyit']) ){
				$this->verifyUsers();
			}
			#Admin Send Payment Link
			if( isset($_POST['paymentl']) && !(empty($option['paypal_butt_id'])) && $option['enable_paypal']==1)
				$this->PaymentLink();
			
			#Admin Resend VerificatioN Email
			if( isset($_POST['emailverifyit']) )
				$this->AdminEmailValidate() ;		
				
			#Admin Delete Unverified User
			if( isset($_POST['vdeleteit']))			
				$this->AdminDeleteUnvalidated();	
			
			
			/*
				*	Add since 2.0.13
				*	Modefy Since 2.0.15
				*	Change email after verify
			*/
			$this->edit_email_verification();
			
			//Blocking wp admin for registered users
			/*
				*	Modefy this code since 2.0.15
				*	If User Verification On then Block WP Pages
			*/
			if(
			   ($pagenow == 'wp-login.php' && ( $option['block_wp_login'] == 1 || !empty($option['verification']) ) ) && 
			   ($theaction != 'logout' && !isset($_REQUEST['interim-login']) )
			   )
			{
				switch($theaction){
					case "register":
						wp_redirect($this->get_redirect_url_pie(get_permalink($option['alternate_register'])));
						exit;
					break;
					case "lostpassword":
						wp_redirect($this->get_redirect_url_pie(get_permalink($option['alternate_forgotpass'])));
						exit;
					break;
					default:
						if(!empty($option['alternate_login'])){
							wp_redirect($this->get_redirect_url_pie(get_permalink($option['alternate_login'])));
							exit;
						}
					break;
				}
			}
			//Blocking access of users to default pages if redirect is on 
			//Patched since 2.0.11
			//Password protected pages bug
			//Resolved by adding postpass
			if($theaction != 'logout' && $theaction != 'postpass' )
			{
				if((is_user_logged_in() && $pagenow == 'wp-login.php') && ($option['redirect_user']==1   && $theaction != 'logout'))
				{
					if(!isset($_REQUEST['interim-login'])){
						$this->afterLoginPage();
					}
				}
			}
			//
			if(trim($pagenow) == "profile.php" && $option['block_WP_profile']==1 )
			{
				$current_user = wp_get_current_user();
				if(trim($current_user->roles[0]) == "subscriber")
				{
					//$profile_page = get_option("Profile_page_id");
					$profile_page = $option['alternate_profilepage'];
					wp_redirect($this->get_redirect_url_pie(get_permalink($profile_page)));
				}
			}
			
				
			if(isset($_POST['log']) && isset($_POST['pwd'])){
				$this->checkLogin();
			}
			
			else if(isset($_POST['pie_submit']))	
				$this->check_register_form();
				
			else if(isset($_POST['pie_renew']))
			{
				$this->renew_account();
			}
			
			// if the user is on the login page, then let the game begin
			if($theaction != 'logout' && $theaction != 'postpass' )
			{
				if ($pagenow == 'wp-login.php' && $theaction != 'logout'){
					add_action('login_init',array($this,'pieregister_login'),1);
				}
			}
			//OImport Export Section
			if(isset($_POST['pie_fields_csv']) || isset($_POST['pie_meta_csv'])){
				$this->generateCSV();
			}
			else if(isset($_FILES['csvfile']['name'])){
				$this->importUsers();
			}
			if(isset($_POST['pie_form']))
			{
				/*
					*	Modefy since 2.0.15
				*/
				//This will make sure no one tempers the field from the client side
				/*$required = array("form","username","email","password","submit");*/
				$required = array("form","email","password","submit");
				$length   = 0;
				foreach($_POST['field'] as $field)
				{
					if(in_array($field['type'],$required))
					$length++;
				}
				if($length==sizeof($required))
				{
					$this->saveFields();
				}
			}
			
			if(
				isset($_POST['invitaion_code_bulk_option']) and isset($_POST['btn_submit_invitaion_code_bulk_option']) and isset($_POST['select_invitaion_code_bulk_option']) and $_POST['invitaion_code_bulk_option'] != "" and $_POST['btn_submit_invitaion_code_bulk_option'] != ""
			  )
			{
				if(trim($_POST['invitaion_code_bulk_option']) == "delete")
				{
					$this->delete_invitation_codes($_POST['select_invitaion_code_bulk_option']);
				}
				else if(trim($_POST['invitaion_code_bulk_option']) == "active")
				{
					$this->active_or_unactive_invitation_codes($_POST['select_invitaion_code_bulk_option'],"1");
				}
				else if(trim($_POST['invitaion_code_bulk_option']) == "unactive")
				{
					$this->active_or_unactive_invitation_codes($_POST['select_invitaion_code_bulk_option'],"0");
				}
			}
			if(isset($_POST['invitation_code_per_page_items']) and $_POST['invitation_code_per_page_items'] != "")
			{
				$opt = get_option("pie_register_2");
				$val = ((int)($_POST['invitation_code_per_page_items']) != 0)? ((int)$_POST['invitation_code_per_page_items']) : "10";
				$opt['invitaion_codes_pagination_number'] = $val;
				update_option("pie_register_2",$opt);
				global $piereg_global_options;
				$piereg_global_options = $opt;
				unset($opt);
			}
			
			/*if($option['modify_avatars'])
			{
				add_filter('get_avatar',array($this,'add_custom_avatars'),88888);
			}*/
			if($option['show_admin_bar'] == "0")
			{
				$this->subscriber_show_admin_bar();// show/hide admin bar
			}
			if( $option['block_wp_login'] ){
				add_filter( 'login_url', array($this,'pie_login_url'),88888,1);
				add_filter( 'lostpassword_url', array($this,'pie_lostpassword_url'),88888,1);
				add_filter( 'register_url', array($this,'pie_registration_url'),88888,1);
				add_filter( 'logout_url', array($this,'piereg_logout_url'),88888,2);
			}
			add_filter( 'piereg_password_reset_not_allowed_text', array($this,'piereg_password_reset_not_allowed_text_function'),20,1);
			
			if(isset($_POST['import_email_template_from_version_1']) and $_POST['old_version_emport'] == "yes")
			{
				$old_options = get_option("pie_register");
				$new_options = get_option("pie_register_2");
				$new_options['user_message_email_admin_verification'] = nl2br($old_options['adminvmsg']);
				$new_options['user_message_email_email_verification'] = nl2br($old_options['emailvmsg']);
				$new_options['user_message_email_default_template'] = nl2br($old_options['msg']);
				update_option("pie_register_2",$new_options);
				global $piereg_global_options;
				$piereg_global_options = $new_options;
			}
			
			if($option['show_custom_logo'] == 1){
				
				if(trim($option['custom_logo_url']) != ""){
					add_action('login_enqueue_scripts', array($this,'piereg_login_logo'));
				}
				add_filter( 'login_headertitle',  array($this,'piereg_login_logo_url_title' ));
				add_filter( 'login_headerurl',  array($this,'piereg_login_logo_url' ));
			}
			
			add_action( 'wp_footer',  array($this,'print_in_footer' ));
		}
		
		function print_in_footer(){
			echo '<div class="pieregWrapper" style="display:none;">';
			echo "<iframe id='CalendarControlIFrame' src='javascript:false;' frameBorder='0' scrolling='no'></iframe>";
			echo "<div id='CalendarControl'></div>";
			echo "</div>";
		}
		private function show_invitaion_code_user(){
			global $errors,$wpdb;
				$prefix=$wpdb->prefix."pieregister_";
				$inv_code = esc_html(base64_decode($_GET['invitaion_code']));
				
				$invitaion_code_users = $wpdb->get_results(  $wpdb->prepare( "SELECT `user_login`,`user_email` FROM `wp_users` WHERE `ID` IN (SELECT user_id FROM `wp_usermeta` Where meta_key = 'invite_code' and meta_value = %s )", $inv_code )  );
				?>
				<style type="text/css">
					table.invitaion-code-table thead td,table.invitaion-code-table tfoot td{
						background:#333;
						font-size:16px;
						font-weight:bold;
						color:#FFF;
					}
					table.invitaion-code-table tr:nth-child(even){background:#E8E8E8;}
					table.invitaion-code-table tr:hover{background:#666;color:#FFF;}
				</style>
				<div style="width:100%">
					<h2><?php _e("Activation Code","piereg");echo " : ".wp_kses($inv_code); ?></h2>
					<table class="invitaion-code-table" width="100%" cellpadding="10" cellspacing="0">
						<thead>
							<tr>
								<td><?php _e("User Name","piereg"); ?></td>
								<td><?php _e("User E-mail","piereg"); ?></td>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td><?php _e("User Name","piereg"); ?></td>
								<td><?php _e("User E-mail","piereg"); ?></td>
							</tr>
						</tfoot>
						<?php
							foreach($invitaion_code_users as $row){
								echo '<tr>';
								echo '<td>'.$row->user_login.'</td>';
								echo '<td>'.$row->user_email.'</td>';
								echo '</tr>';
							}
						?>
					</table>
				</div>
				<?php
				exit;
		}
		function piereg_login_logo() {
			$option = get_option( 'pie_register_2' );		
			$logo_data = @getimagesize($option['custom_logo_url']);
			?>
			<style type="text/css">
				body.login div#login h1 a {
					background-image: url('<?php echo $option['custom_logo_url']; ?>');
					background-size:<?php echo $logo_data[0]."px ".$logo_data[1]."px"; ?>;
					width:<?php echo $logo_data[0]."px "; ?>;
					height:<?php echo $logo_data[1]."px "; ?>;
				}
			</style>
			<?php
			unset($option);
		}
		function piereg_login_logo_url_title() {
			$option = get_option( 'pie_register_2' );
			return $option['custom_logo_tooltip'];
			unset($option);
		}
		function piereg_login_logo_url() {
			$option = get_option( 'pie_register_2' );
			return $option['custom_logo_link'];
			unset($option);
		}
		function payment_success_cancel_after_register($query_string){
			global $wpdb;
			$option = get_option( 'pie_register_2' );
			$fields 			= maybe_unserialize(get_option("pie_fields"));
			$confirmation_type 	= $fields['submit']['confirmation'];
			
			if($confirmation_type == "page"){
				wp_safe_redirect(get_permalink($fields['submit']['page']));
				exit;
			}elseif($confirmation_type == "redirect"){
				wp_redirect($fields['submit']['redirect_url']);
				exit;
			}elseif($confirmation_type == "text" ){
				wp_safe_redirect($this->pie_modify_custom_url(get_permalink($option['alternate_login']),$query_string));
				exit;
			}
		}
		function get_redirect_url_pie($get_url){
			$get_url = trim($get_url);
			if(!$get_url) return false;
			if($_SERVER['QUERY_STRING']){
				if(strpos($get_url,"?"))
					$url = $get_url."&".$_SERVER['QUERY_STRING'];
				else
					$url = $get_url."?".$_SERVER['QUERY_STRING'];
			}
			else{
				$url = $get_url;
			}
			return $url;
		}
		function subscriber_show_admin_bar()
		{
			global $current_user;
			$current_user->caps = array_keys($current_user->caps);
			$ncaps = count($current_user->caps);
			$role = $current_user->caps[$ncaps - 1];
			if( trim(strtolower($role)) == "subscriber" )
			{
				show_admin_bar( false );
			}
			unset($current_user);
		}
		
		//"Insert Form" button to the post/page edit screen
		function add_pie_form_button($context)
		{
			$is_post_edit_page = in_array(basename($_SERVER['PHP_SELF']), array('post.php', 'page.php', 'page-new.php', 'post-new.php'));
			if(!$is_post_edit_page)
				return $context;
			//$out = '<a href="#TB_inline?width=480&inlineId=select_pie_form" class="thickbox" id="add_pie_form" title="' . __("Add Pie Register Form", 'piereg') . '"><img src="'.plugins_url('pie-register').'/images/form-icon.png" alt="' . __("Add Pie Register Form", 'piereg') . '" /></a>';
			
			$out = '<a href="#TB_inline?width=480&inlineId=select_pie_form" class="thickbox button" id="add_pie_form" title="' . __("Add Pie Register Form", 'piereg') . '" ><span style="background: url('.plugins_url('pie-register').'/images/form-icon.png); background-repeat: no-repeat; background-position: left bottom;" class="wp-media-buttons-icon"></span> '.__("Add Form","piereg").'</a>';
			return $context . $out;
		}
		function checkLoginPage()
		{
			$option 		= get_option('pie_register_2');	
			$current_page	= get_the_ID();
			if($option['block_wp_login']==1 && $option['alternate_login'] > 0 && is_user_logged_in() && $current_page == $option['alternate_login'] )
			{	
				
				$this->afterLoginPage();			
			}
		}
		function add_pie_form_popup()
		{
			 ?>
			  <script type="text/javascript">
				function addForm(){
					var form_id = jQuery("#pie_forms").val();
					if(form_id == ""){
						alert("<?php _e("Please select a form", "piereg") ?>");
						return;
					}
				   
					window.send_to_editor(form_id);
				}
			</script>
			 <div id="select_pie_form" style="display:none;">
				<div >
					<div>
						<div style="padding:15px 15px 0 15px;">
							<h3 style="color:#5A5A5A!important; font-family:Georgia,Times New Roman,Times,serif!important; font-size:1.8em!important; font-weight:normal!important;"><?php _e("Select A Form", "piereg"); ?></h3>
							<span>
								<?php _e("Select a form below to add it to your post or page.", "piereg"); ?>
							</span>
						</div>
						<div style="padding:15px 15px 0 15px;">
							<select id="pie_forms">
								
								<option value="[pie_register_form]"><?php _e("Registration Form","piereg") ?></option>
								<option value="[pie_register_login]"><?php _e("Login Form","piereg") ?></option>
								<option value="[pie_register_forgot_password]"><?php _e("Forgot Password Form","piereg") ?></option>
								<option value="[pie_register_profile]"><?php _e("Profile Page","piereg") ?></option>
								
							</select> <br/>
							
						</div>
						
						<div style="padding:15px;">
							<input type="button" class="button-primary" value="Insert Form" onclick="addForm();"/>&nbsp;&nbsp;&nbsp;
						<a class="button" style="color:#bbb;" href="#" onclick="tb_remove(); return false;"><?php _e("Cancel", "piereg"); ?></a>
						</div>
					</div>
				</div>  
			</div>	
		<?php
		}
		function getMeta()
		{
			$meta =  get_option( 'pie_fields_meta');
			$meta = $meta[$_POST['field_type']];
			$meta = str_replace("%d%",$_POST['id'],$meta);	
			$meta .= '<input value = "'.$_POST['field_type'].'" type="hidden" class="input_fields" name="field['.$_POST['id'].'][type]" id="type_'.$_POST['id'].'">';		
			
			echo $meta;
			die();	
		}
		
		function process_login_form(){
			get_header();
			//ob_start();
			include_once("login_form.php");
			$output = pieOutputLoginForm();
			echo $output;
			//ob_end_flush();
			//include_once("login_form.php");
			get_footer();
			exit;
		}
		function checkLogin()
		{
			global $errors, $wp_session;
			$errors = new WP_Error();
			$option = get_option('pie_register_2');
			/*
				*	Sanitizing post data
			*/
			$this->piereg_sanitize_post_data( ( (isset($_POST) && !empty($_POST))?$_POST : array() ) );
			if(empty($_POST['log']) || empty($_POST['pwd']))
			{
				$errors->add('login-error',apply_filters("piereg_Invalid_username_or_password",__('Invalid username or password.','piereg')));
			}
			else
			{
				$error_found = 0;
				if($option['capthca_in_login'] == 1){
					$settings  		=  get_option("pie_register_2");
					$privatekey		= $settings['captcha_private'] ;
					if($privatekey)
					{
						require_once(PIEREG_DIR_NAME.'/recaptchalib.php');
						$resp = recaptcha_check_answer ($privatekey,$_SERVER["REMOTE_ADDR"],$_POST["recaptcha_challenge_field"],$_POST["recaptcha_response_field"]);
						
						if (!$resp->is_valid) {
							$errors->add('login-error',apply_filters("Invalid_Security_Code",__('Invalid Security Code','piereg')));
							$error_found++;
						}
					}
				}
				elseif($option['capthca_in_login'] == 2){
					
					if(isset($_POST['piereg_math_captcha_login']))//Login form in Page
					{
						$piereg_cookie_array =  $_COOKIE['piereg_math_captcha_Login_form'];
						/*$piereg_cookie_array = explode(",",$piereg_cookie_array);*/
						$piereg_cookie_array = explode("|",$piereg_cookie_array);
						$cookie_result1 = (intval(base64_decode($piereg_cookie_array[0])) - 12);
						$cookie_result2 = (intval(base64_decode($piereg_cookie_array[1])) - 786);
						$cookie_result3 = (intval(base64_decode($piereg_cookie_array[2])) + 5);
						if( ($cookie_result1 == $cookie_result2) && ($cookie_result3 == $_POST['piereg_math_captcha_login'])){
						}
						else{
							$errors->add('login-error',apply_filters("Invalid_Security_Code",__('Invalid Security Code','piereg')));
							$error_found++;
						}
					}
					elseif(isset($_POST['piereg_math_captcha_login_widget']))//Login form in widget
					{
						$piereg_cookie_array =  $_COOKIE['piereg_math_captcha_Login_form_widget'];
						/*$piereg_cookie_array = explode(",",$piereg_cookie_array);*/
						$piereg_cookie_array = explode("|",$piereg_cookie_array);
						$cookie_result1 = (intval(base64_decode($piereg_cookie_array[0])) - 12);
						$cookie_result2 = (intval(base64_decode($piereg_cookie_array[1])) - 786);
						$cookie_result3 = (intval(base64_decode($piereg_cookie_array[2])) + 5);
						if( ($cookie_result1 == $cookie_result2) && ($cookie_result3 == $_POST['piereg_math_captcha_login_widget'])){
						}
						else{
							$errors->add('login-error',apply_filters("Invalid_Security_Code",__('Invalid Security Code','piereg')));
							$error_found++;
						}
					}else{
						$errors->add('login-error',apply_filters("Invalid_Security_Code",__('Invalid Security Code','piereg')));
						$error_found++;
					}
				}
				if($error_found == 0){
					$creds = array();
					$creds['user_login'] 	= $_POST['log'];
					$creds['user_password'] = $_POST['pwd'];
					$creds['remember'] 		= isset($_POST['rememberme']);
					
					$piereg_secure_cookie = false;
					if ( (!empty($_POST['log']) && !empty($_POST['pwd'])) && (!force_ssl_admin() && is_ssl()) ) {
						$piereg_secure_cookie = true;
						force_ssl_admin(true);
					}
					$user = wp_signon( $creds, $piereg_secure_cookie);
				
					if ( is_wp_error($user))
					{
						$user_login_error = $user->get_error_message();
						if(strpos(strip_tags($user_login_error),'Invalid username',5) > 6)
						{
							$user_login_error = apply_filters('pie_invalid_username_password_msg_txt','<strong>'.ucwords(__("error","piereg")).'</strong>: '.__("Invalid username","piereg").'. <a href="'.$this->pie_lostpassword_url().'" title="'.__("Password Lost and Found","piereg").'">'.__("Lost your password?","piereg").'</a>');
						}else if(strpos(strip_tags($user_login_error),'password you entered',9) > 10)
						{
							$user_login_error = apply_filters('pie_invalid_user_password_msg_txt','<strong>'.ucwords(__("error","piereg")).'</strong>: '.__("The password you entered for the username","piereg").' <strong>'.$_POST['log'].'</strong> '.__("is incorrect","piereg").'. <a href="'.$this->pie_lostpassword_url().'" title="'.__("Password Lost and Found","piereg").'">'.__("Lost your password?","piereg").'</a>');
						}
						$errors->add('login-error',apply_filters("piereg_login_error",$user_login_error));
					}
					else
					{
						if(in_array("administrator",(array)$user->roles)){
							/*
								*	Add Since 2.0.13
							*/
							if( $user ) {
								wp_set_current_user( $user->ID, $user->user_login );
								wp_set_auth_cookie( $user->ID, $creds['remember'], $piereg_secure_cookie );
								do_action( 'wp_login', $user->user_login, $user );
							}
							do_action("piereg_admin_login_before_redirect_hook",$user);
								
							if(isset($_GET['redirect_to']) and $_GET['redirect_to'] != ""){
								wp_redirect($_GET['redirect_to']);
								exit;
							}
							
							wp_safe_redirect(admin_url());
							exit;
						}
						else
						{
							$active = get_user_meta($user->ID,"active",true);
							//Delete User after grace Period
							if($active == "0")//If not active
							{
								$delete_user = true;
								if($this->deleteUsers($user->ID,$user->user_email,$user->user_registered)){
									$errors->add("login-error",apply_filters("piereg_your_account_has_no_longer_exist",__("Your account has no longer exist.")));
									$delete_user = false;
								}
								if($delete_user){
									wp_logout();
									$errors->add('login-error',apply_filters("piereg_your_account_is_not_activated",__('Your account is not activated!.','piereg')));
								}
							}elseif(empty($active))
							{
								/*
									*	Add Since 2.0.13
								*/
								if( $user ) {
									wp_set_current_user( $user->ID, $user->user_login );
									wp_set_auth_cookie( $user->ID, $creds['remember'], $piereg_secure_cookie );
									do_action( 'wp_login', $user->user_login, $user );
								}
								do_action("piereg_user_login_before_redirect_hook",$user);
								$this->afterLoginPage();
								exit;
							}
							else{
								/*
									*	Add Since 2.0.13
								*/
								if( $user ) {
									wp_set_current_user( $user->ID, $user->user_login );
									wp_set_auth_cookie( $user->ID, $creds['remember'], $piereg_secure_cookie );
									do_action( 'wp_login', $user->user_login, $user );
								}
								do_action("piereg_user_login_before_redirect_hook",$user);
								$this->afterLoginPage();
								exit;
							}
						}
					}
				}
			}
		}
		//Add the Settings and User Panels
		function AddPanel()
		{ 
			$update = get_option( 'pie_register_2' );
					
			$pie_page_suffix_1 = add_object_page( "Pie Register", __('Pie Register',"piereg"), 'manage_options', 'pie-register',  array($this,'RegPlusEditForm'), plugins_url("/images/pr_icon.png",__FILE__) );	
			
			add_action('admin_print_scripts-' . $pie_page_suffix_1, array($this,'pieregister_admin_scripts_styles'));
			
			$pie_page_suffix_2 = add_submenu_page( 'pie-register', 'Form Editor', __('Form Editor',"piereg"), 'manage_options', 'pie-register', array($this, 'RegPlusEditForm') );		
			
			add_action('admin_print_scripts-' . $pie_page_suffix_2, array($this,'pieregister_admin_scripts_styles'));
			
			
			$pie_page_suffix_3 = add_submenu_page( 'pie-register', 'General Settings', __('General Settings',"piereg"), 'manage_options', 'pie-general-settings', array($this, 'PieGeneralSettings') );	
			
			add_action('admin_print_scripts-' . $pie_page_suffix_3, array($this,'pieregister_admin_scripts_styles'));	
			
			$pie_page_suffix_4 = add_submenu_page( 'pie-register', 'Payment Gateway Settings', __('Payment Gateway',"piereg"), 'manage_options', 'pie-gateway-settings', array($this, 'PieRegPaymentGateway') );
			
			add_action('admin_print_scripts-' . $pie_page_suffix_4, array($this,'pieregister_admin_scripts_styles'));
			
			$pie_page_suffix_5 = add_submenu_page( 'pie-register', 'Email Notification Settings', __('Admin Notifications',"piereg"), 'manage_options', 'pie-admin-notification', array($this, 'PieRegAdminNotification') );	
			
			add_action('admin_print_scripts-' . $pie_page_suffix_5, array($this,'pieregister_admin_scripts_styles'));	
			
			$pie_page_suffix_6 = add_submenu_page( 'pie-register', 'Email Notification Settings', __('User Notifications',"piereg"), 'manage_options', 'pie-user-notification', array($this, 'PieRegUserNotification') );	
			
			add_action('admin_print_scripts-' . $pie_page_suffix_6, array($this,'pieregister_admin_scripts_styles'));	
			
			$pie_page_suffix_7 = add_submenu_page( 'pie-register', 'Import/Export', __('Import/Export',"piereg"), 'manage_options', 'pie-import-export', array($this, 'PieRegImportExport'));		
			
			add_action('admin_print_scripts-' . $pie_page_suffix_7, array($this,'pieregister_admin_scripts_styles'));
			
			$pie_page_suffix_8 = add_submenu_page( 'pie-register', 'Invitation Codes', __('Invitation Codes',"piereg"), 'manage_options', 'pie-invitation-codes', array($this, 'PieRegInvitationCodes'));		
			
			add_action('admin_print_scripts-' . $pie_page_suffix_8, array($this,'pieregister_admin_scripts_styles'));
			
			
			// help page
			$pie_page_suffix_9 = add_submenu_page( 'pie-register', 'Help', __('Help',"piereg"), 'manage_options', 'pie-help', array($this, 'PieRegHelp'));		
			
			add_action('admin_print_scripts-' . $pie_page_suffix_9, array($this,'pieregister_admin_scripts_styles'));
			
			add_action('admin_print_scripts-' . $pie_page_suffix_1, array($this,'pieregister_admin_scripts_styles'));
			
			if( $update['verification'] == 1 || $update['verification'] == 2 || $update['enable_paypal'] == 1){
				$pie_page_suffix_10 = add_users_page( 'Unverified Users', 'Unverified Users', 'manage_options', 'unverified-users', array($this, 'Unverified') );
				add_action('admin_print_scripts-' . $pie_page_suffix_10, array($this,'pieregister_admin_scripts_styles'));
			}
			do_action('pie_register_add_menu');
			
		}
		
		function pieregister_admin_scripts_styles(){
			$this->pie_admin_enqueu_scripts();
		}
		
		function block_wp_admin() 
		{
			if (strpos(strtolower($_SERVER['REQUEST_URI']),'/wp-admin/') !== false) 
			{
				if ( !current_user_can( 'manage_options' ) ) 
				{
					wp_redirect($this->get_redirect_url_pie(get_option('siteurl')),302);
				}
			}	
		}
		//deprecated
		
		function saveFields()
		{
			$math_cpatcha_enable = "false";
			$piereg_startingDate = "1901";
			$piereg_endingDate = date("Y");
			foreach($_POST['field'] as $k=>$fv){
				if($fv['type'] == 'html'){
					$fv['html'] = htmlentities(stripslashes($fv['html']), ENT_QUOTES | ENT_IGNORE, "UTF-8");
				}
				if($fv['type'] == 'math_captcha'){
					$math_cpatcha_enable = "true";
				}
				//since 2.0.10
				if(isset($fv['desc']))
				{
					$fv['desc'] = htmlentities(stripslashes($fv['desc']), ENT_QUOTES | ENT_IGNORE, "UTF-8");
				}
				//since 2.0.12
				if($fv['type'] == 'date'){
					$pattern = '/[0-9]{4}/';
					$subject = $fv['startingDate'];
					if(
						(strlen($fv['startingDate']) == 4 && preg_match($pattern, $subject))&&
						(intval($fv['startingDate']) <= intval($fv['endingDate']))
					  ){
						$fv['startingDate'] = $fv['startingDate'];
						$piereg_startingDate = $fv['startingDate'];
					}
					else{
						$fv['startingDate'] = "1901";
						$piereg_startingDate = "1900";
					}
						
					$subject = $fv['endingDate'];
					if(
					   (strlen($fv['endingDate']) == 4 && preg_match($pattern, $subject)) && 
					   (intval($fv['endingDate']) >= intval($fv['startingDate']))
					   ){
						$fv['endingDate'] = $fv['endingDate'];
						$piereg_endingDate = $fv['endingDate'];
					}
					else{
						$fv['endingDate'] = date("Y");
						$piereg_endingDate = date("Y");
					}
						
					
						
				}
				
				$updated_post[$k] = $fv;
			}
			if(!$_POST['field'])
					$_POST['field'] =  get_option( 'pie_fields_default' );
		
			do_action("pie_fields_save");
			update_option("pie_fields",serialize($updated_post));
			
			$options = get_option("pie_register_2");
			$options['pie_regis_set_user_role_'] = $_POST['set_user_role_'];
			$options['piereg_startingDate'] = $piereg_startingDate;
			$options['piereg_endingDate'] = $piereg_endingDate;
			update_option("pie_register_2",$options);
			global $piereg_global_options;
			$piereg_global_options = $options;
			update_option("piereg_math_cpatcha_enable",$math_cpatcha_enable);
			// Update Wordpress Drefault User Role
			update_option("default_role",$_POST['set_user_role_']);
		}
		//Opening Form Editor
		function RegPlusEditForm()
		{ 		
			$data 	= $this->getCurrentFields();
			if(!is_array($data) || sizeof($data) == 0)
			{
				$data 	= get_option( 'pie_fields_default' );	
			}
			
			require_once($this->plugin_dir.'/menus/PieRegEditForm.php');		
		}
		
		function addCustomCSS()
		{
			$option = get_option( 'pie_register_2' );
				
			if($option['custom_css'] != "" && $option['outputcss'] == 1)
			{
				/*echo '<style>'.$option['custom_css'].'</style>';*/
				echo '<style>'.html_entity_decode($option['custom_css'],ENT_COMPAT,"UTF-8").'</style>';
			}
			
		}
		function addCustomScripts()
		{
			$option = get_option( 'pie_register_2' );
				
			if($option['tracking_code'] != "")
			{
				/*echo stripslashes($option['tracking_code']);*/
				echo '<script type="text/javascript">'.html_entity_decode($option['tracking_code'],ENT_COMPAT,"UTF-8").'</script>';
			}
		}
		function pieregister_login()
		{
			$option = get_option( 'pie_register_2' );
			if($option['allow_pr_edit_wplogin'] == 1){
				
				global $errors;
				if (isset($_REQUEST['action'])) :
					$action = $_REQUEST['action'];
				else :
					$action = 'login';
				endif;
				
				switch($action) :
					case 'lostpassword' :
					case 'retrievepassword' :
						$this->process_lostpassword();
					break;
					case 'resetpass' :
					case 'rp' :
						$this->process_getpassword();
					break;	
					case 'register':
					$this->process_register_form();		
					case 'login':
					default:
						$this->process_login_form();
					break;
				endswitch;	
				exit;
			}
			return false;
		}
		//deprecated
		/*function addUrl()	
		{
			
			?><script type="text/javascript">
	var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
	</script><?php
		}*/
		function process_register_form()
		{
			global $errors;
			
			$form 		= new Registration_form();
			$success 	= '' ;	
				
			get_header();
			include_once("register_form.php");
			//Printing Success Message
			echo outputRegForm();
			get_footer();	
			
			exit;
		}
		function check_register_form()
		{
			global $errors, $wp_session;
			if(($this->check_enable_payment_method()) == "false")
			{
				$this->pie_save_registration();
			}
			else if(($this->check_enable_payment_method()) == "true")
			{
				if(isset($_POST['select_payment_method']) and trim($_POST['select_payment_method']) != "" and $_POST['select_payment_method'] != "select")
				{
					$this->pie_save_registration();
				}
				else{
					$_POST['error'] = __("Please select any payment method","piereg");
				}
			}
			else if(trim($wp_session['payment_error']) != "")
			{
				$_POST['error'] = __($wp_session['payment_error'],"piereg");
				$wp_session['payment_error'] = "";
				$wp_session['payment_sussess'] = "";
			}
		}
		function piereg_generate_username($email = "",$is_generate_username = false,$username_prifex = ""){
			if($is_generate_username && !isset($_POST['username']) ){
				$username = "";
				while(1){
					$username = strtolower( ( !empty($username_prifex) ? $username_prifex . "_" : "" ) .wp_generate_password( 7, false, false));
					if(!username_exists($username)){
						break;
					}
				}
				$_POST['username'] = trim($username);
			}
			else if(isset($_POST['e_mail'],$email) && !empty($_POST['e_mail']) && !isset($_POST['username']))
				$_POST['username'] = $_POST['e_mail'];
			else if(!isset($_POST['username']))
				$this->piereg_generate_username("",true);
		}
		function pie_save_registration()
		{
			$this->piereg_generate_username(esc_sql($_POST['e_mail']), apply_filters("piereg_generate_unique_username",false), apply_filters("piereg_generate_username_with_prifex",false));
			
			add_filter('wp_mail_content_type', array($this,'set_html_content_type'));
			global $errors;
			$form 		= new Registration_form();
			$errors 	= $form->validateRegistration($errors);
			
			$option 	= get_option( 'pie_register_2' );
			//If Registration doesn't have errors
			
			if(sizeof($errors->errors) == 0)
			{
					
				do_action('pie_register_after_register_validate');				 
				//Inserting User
				$pass = $_POST['password'];
				$user_data = array(
								   'user_pass' 		=> $pass,
								   'user_login' 	=> $_POST['username'],
								   'user_email' 	=> $_POST['e_mail'],
								   'role' 			=> get_option('default_role')
								   );
				if(isset($_POST['first_name']) && !empty($_POST['first_name']) )
				{
					$display_name = $_POST['first_name'].((isset($_POST['last_name']) && !empty($_POST['last_name']))?" ".$_POST['last_name']:"");
					$user_data['display_name'] = $display_name;
				}
				if(isset($_POST['url']))
				{
					$user_data["user_url"] =  $_POST['url'];	 
				}
				
				$user_id = wp_insert_user( $user_data );
				$form->addUser($user_id);
				/*
					*	Update Nickname User Meta
					*	Add since 2.0.13
				*/
				if(isset($_POST['first_name']) && !empty($_POST['first_name']) )
				{
					$display_name = $_POST['first_name'].((isset($_POST['last_name']) && !empty($_POST['last_name']))?" ".$_POST['last_name']:"");
					update_user_meta( $user_id, 'nickname', $display_name);
				}
				
				$new_role = 'subscriber';
				$new_role = get_option("default_role");
				// Remove Science 2.0.10
				/*if(isset($option['pie_regis_set_user_role_']) and trim($option['pie_regis_set_user_role_']) != "")
				{
					$wp_role = get_option("default_role");
					$new_role = (isset($option['pie_regis_set_user_role_']) && !empty($option['pie_regis_set_user_role_']))?strtolower($option['pie_regis_set_user_role_']):$wp_role;
					
				}*/
				//// update user role using wordpress function
				wp_update_user( array ('ID' => $user_id, 'role' => $new_role ) ) ;
				$user 		= new WP_User($user_id);
				do_action('pie_register_after_register_validate',$user);
				////////////////////////////////////////////////////
				/******** Admin Notification *******/
				if($option['enable_admin_notifications'] == "1")
				{
					$message_temp = "";
					if($option['admin_message_email_formate'] == "0"){
						$message_temp	= nl2br(strip_tags($option['admin_message_email']));
					}else{
						$message_temp	= $option['admin_message_email'];
					}
					$message  		= $form->filterEmail($message_temp,$user,$pass);
					$subject		= html_entity_decode($option['admin_subject_email'],ENT_COMPAT,"UTF-8");
					$to				= trim($option['admin_sendto_email']);
					$from_name		= trim($option['admin_from_name']);
					$from_email		= trim($option['admin_from_email']);
					$bcc			= trim($option['admin_bcc_email']);
					$reply_to_email	= trim($option['admin_to_email']);
		
					if(empty($to))//if not valid email address then use wordpress default admin
					{
						$to = get_option('admin_email');
					}
					//Headers
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
					if(!empty($from_email))//Validating From
						$headers .= "From: ".$from_name." <".$from_email."> \r\n";
		
					if(!empty($bcc))//Validating BCC
						$headers .= "Bcc: " . $bcc . " \r\n";
		
					if(!empty($reply_to_email))//Validating Reply To
						$headers .= "Reply-To: <".$reply_to_email."> \r\n";
					
					if($reply_to_email)
						$headers .= "Return-Path: {$reply_to_email}\r\n";
					else
						$headers .= "Return-Path: {$from_email}\r\n";
					
					wp_mail($to,$subject, $message,$headers);
					
				}
				if(!(empty($option['paypal_butt_id'])) && $option['enable_paypal']==1)
				{
					$_POST['user_id'] = $user_id;
					update_user_meta( $user_id, 'active', 0);
					do_action("check_payment_method_paypal");// function prefix check_payment_method_
				}
				else if($option['verification'] == 1 )//Admin Verification
				{
					update_user_meta( $user_id, 'active', 0);
					update_user_meta( $user_id, 'register_type', "admin_verify");
					$subject 		= html_entity_decode($option['user_subject_email_admin_verification'],ENT_COMPAT,"UTF-8");
					$message_temp = "";
					if($option['user_formate_email_admin_verification'] == "0"){
						$message_temp	= nl2br(strip_tags($option['user_message_email_admin_verification']));
					}else{
						$message_temp	= $option['user_message_email_admin_verification'];
					}
					$message		= $form->filterEmail($message_temp,$user, $pass );
					$from_name		= $option['user_from_name_admin_verification'];
					$from_email		= $option['user_from_email_admin_verification'];
					$reply_email 	= $option['user_to_email_admin_verification'];
					//Headers
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
					if(!empty($from_email) && filter_var($from_email,FILTER_VALIDATE_EMAIL))//Validating From
					$headers .= "From: ".$from_name." <".$from_email."> \r\n";
					if($reply_email){
						$headers .= "Reply-To: {$reply_email}\r\n";
						$headers .= "Return-Path: {$from_name}\r\n";
					}else{
						$headers .= "Reply-To: {$from_email}\r\n";
						$headers .= "Return-Path: {$from_email}\r\n";
					}
					wp_mail($_POST['e_mail'], $subject, $message , $headers);
					$_POST['registration_success'] = apply_filters("piereg_thank_you_for_your_registration",__("Thank you for your registration. You will be notified once the admin approves your account.",'piereg'));	
				
				}
				else if($option['verification'] == 2 )//E-Mail Link Verification
				{
					update_user_meta( $user_id, 'active', 0);
					$hash = md5( time() );
					update_user_meta( $user_id, 'hash', $hash );
					update_user_meta( $user_id, 'register_type', "email_verify");
					$subject 		= html_entity_decode($option['user_subject_email_email_verification'],ENT_COMPAT,"UTF-8");
					$message_temp = "";
					if($option['user_formate_email_email_verification'] == "0"){
						$message_temp	= nl2br(strip_tags($option['user_message_email_email_verification']));
					}else{
						$message_temp	= $option['user_message_email_email_verification'];
					}
					$message		= $form->filterEmail($message_temp,$user, $pass );
					$from_name		= $option['user_from_name_email_verification'];
					$from_email		= $option['user_from_email_email_verification'];
					$reply_email 	= $option['user_to_email_email_verification'];
					//Headers
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
					if(!empty($from_email) && filter_var($from_email,FILTER_VALIDATE_EMAIL))//Validating From
						$headers .= "From: ".$from_name." <".$from_email."> \r\n";
					if($reply_email){
						$headers .= "Reply-To: {$reply_email}\r\n";
						$headers .= "Return-Path: {$from_name}\r\n";
					}else{
						$headers .= "Reply-To: {$from_email}\r\n";
						$headers .= "Return-Path: {$from_email}\r\n";
					}
					wp_mail($_POST['e_mail'], $subject, $message , $headers);
					$_POST['registration_success'] = apply_filters("piereg_thank_you_for_your_registration",__("Thank you for your registration. An activation link with your password has been sent to you.",'piereg'));
						
				}
				else if($option['verification'] == 0 ){
					update_user_meta( $user_id, 'active', 1);
					/************ User Notification **************/
					
					$subject 		= html_entity_decode($option['user_subject_email_default_template'],ENT_COMPAT,"UTF-8");
					$message_temp = "";
					if($option['user_formate_email_default_template'] == "0"){
						$message_temp	= nl2br(strip_tags($option['user_message_email_default_template']));
					}else{
						$message_temp	= $option['user_message_email_default_template'];
					}
					$message		= $form->filterEmail($message_temp,$user, $pass );
					$from_name		= $option['user_from_name_default_template'];
					$from_email		= $option['user_from_email_default_template'];					
					$reply_email 	= $option['user_to_email_default_template'];
							
					//Headers
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
				
					if(!empty($from_email) && filter_var($from_email,FILTER_VALIDATE_EMAIL))//Validating From
					$headers .= "From: ".$from_name." <".$from_email."> \r\n";
					if($reply_email){
						$headers .= "Reply-To: {$reply_email}\r\n";
						$headers .= "Return-Path: {$from_name}\r\n";
					}else{
						$headers .= "Reply-To: {$from_email}\r\n";
						$headers .= "Return-Path: {$from_email}\r\n";
					}
					wp_mail($_POST['e_mail'], $subject, $message , $headers);	
				}
				do_action('pie_register_after_register',$user);
				
				$fields 			= maybe_unserialize(get_option("pie_fields"));
				$confirmation_type 	= $fields['submit']['confirmation'];
				
				if($confirmation_type== "page")
				{
					/*?>
					<script type="text/javascript" language="javascript">
						location.replace("<?php echo get_permalink($fields['submit']['page']); ?>");
					</script>
					<?php*/
					wp_safe_redirect(get_permalink($fields['submit']['page']));
					exit;
				}
				else if($confirmation_type == "redirect")
				{
					/*?>
					<script type="text/javascript" language="javascript">
						location.replace("<?php echo $fields['submit']['redirect_url'] ?>");
					</script>
					<?php*/
					wp_redirect($fields['submit']['redirect_url']);
					exit;
				}
				else
				{
					$_POST['registration_success']	= __($fields['submit']['message'],"piereg");
				}
				
			}
		}
		
		function check_payment_method_paypal()
		{
			$user_id 	= $_POST['user_id'];
			$user_email = $_POST['e_mail'];
			
			add_filter( 'wp_mail_content_type', array($this,'set_html_content_type' ));
			global $errors;
			$form 		= new Registration_form();
			$errors 	= $form->validateRegistration($errors);
			$option 	= get_option( 'pie_register_2' );	
			
			update_user_meta( $user_id, 'active', 0);
			$hash = md5( time() );
			update_user_meta( $user_id, 'hash', $hash );
			
			
			$subject 		= html_entity_decode($option['user_subject_email_pending_payment'],ENT_COMPAT,"UTF-8");
			$message_temp = "";
			if($option['user_formate_email_pending_payment'] == "0"){
				$message_temp	= nl2br(strip_tags($option['user_message_email_pending_payment']));
			}else{
				$message_temp	= $option['user_message_email_pending_payment'];
			}
			$message		= $form->filterEmail($message_temp,$user_id, $pass );
			$from_name		= $option['user_from_name_pending_payment'];
			$from_email		= $option['user_from_email_pending_payment'];
			$reply_email	= $option['user_to_email_pending_payment'];
					
			//Headers
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
			if(!empty($from_email) && filter_var($from_email,FILTER_VALIDATE_EMAIL))//Validating From
				$headers .= "From: ".$from_name." <".$from_email."> \r\n";
			if($reply_email){
				$headers .= "Reply-To: {$reply_email}\r\n";
				$headers .= "Return-Path: {$from_name}\r\n";
			}else{
				$headers .= "Reply-To: {$from_email}\r\n";
				$headers .= "Return-Path: {$from_email}\r\n";
			}
			wp_mail($_POST['e_mail'], $subject, $message , $headers);
			
			update_user_meta( $user_id, 'register_type', "payment_verify");
			if($option['paypal_sandbox']=="no")
			{
				$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
				/*echo '<form id="paypal_form" action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="'.$option['paypal_butt_id'].'">
				<input name="custom" type="hidden" value="'.$hash.'|'.$user_id.'">
				<input name="cancel_return" type="hidden" value="'.trailingslashit(get_bloginfo("url")).'?action=payment_cancel&paypal='.base64_encode($user_id).'">
				</form>';*/
			}
			else
			{
				$paypal_url = "https://sandbox.paypal.com/cgi-bin/webscr";
				/*echo '<form  id="paypal_form" action="https://sandbox.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="'.$option['paypal_butt_id'].'">
				<input name="custom" type="hidden" value="'.$hash.'|'.$user_id.'">
				<input name="cancel_return" type="hidden" value="'.trailingslashit(get_bloginfo("url")).'?action=payment_cancel&paypal='.base64_encode($user_id).'">
				</form>';*/
			}
			$form = '<form id="paypal_form" action="'.$paypal_url.'" method="post">'.
						'<input type="hidden" name="cmd" value="_s-xclick">'.
						'<input type="hidden" name="hosted_button_id" value="'.$option['paypal_butt_id'].'">'.
						'<input type="hidden" name="custom" value="'.$hash.'|'.$user_id.'">'.
						'<input type="hidden" name="bn" value="Genetech_SI_Custom">'.
						'<input type="hidden" name="cancel_return" value="'.trailingslashit(get_bloginfo("url")).'?action=payment_cancel&paypal='.base64_encode($user_id).'">'./*
						'<input type="hidden" name="notify_url" value="'.trailingslashit(get_bloginfo("url")).'?action=ipn_success&paypal='.base64_encode( $hash.'|'.$user_id ).'">'.*/
						'<input type="submit" style="display:none">'.
					'</form>';
					
			/*$nvpStr = 	"?cmd=".urlencode("_s-xclick").
						"&hosted_button_id=".urlencode($option['paypal_butt_id']).
						"&hidden=".urlencode($hash.'|'.$user_id).
						"&bn=Genetech_SI_Custom".
						"&cancel_return=".urlencode( trailingslashit(get_bloginfo("url")).'?action=payment_cancel&paypal='.base64_encode($user_id) ).
						"&notify_url=".urlencode( trailingslashit(get_bloginfo("url")).'?action=ipn_success&paypal='.base64_encode( $hash.'|'.$user_id ) );*/
			
			//wp_redirect($paypal_url.$nvpStr);
			echo $form.'<script type="text/javascript">document.getElementById("paypal_form").submit();</script>';
			die();
		}
		function process_lostpassword()
		{
			global $errors ;
			include_once("forgot_password.php");
			get_header();	
			
			//$this->pie_frontend_enqueu_scripts();
		
			
			$output =  pieResetFormOutput();
			echo $output;
			get_footer();
			exit;
		}
		function process_getpassword()
		{
			global $errors ;
			$user 		= check_password_reset_key($_GET['key'], $_GET['login']);
			if ( is_wp_error($user) ) 
			{	
				wp_redirect( site_url('wp-login.php?action=lostpassword&error=invalidkey') );
				exit;
			}
			
			get_header();
			
			//$this->pie_frontend_enqueu_scripts();
			include_once("get_password.php");
			$get_form = piereg_get_passwird();
			echo $get_form;
			get_footer();
			exit;	
		}
		
		function Unverified(){
				global $wpdb;
				if(isset( $_POST['notice'] ) && !empty( $_POST['notice'] ))
					echo '<div id="message" class="updated fade"><p><strong>' . $_POST['notice'] . '.</strong></p></div>';
				else if(isset( $_POST['error'] ) && !empty( $_POST['error'] ))
					echo '<div id="error" class="error fade"><p><strong>' . $_POST['error'] . '.</strong></p></div>';
				
				$unverified = get_users(array('meta_key'=> 'active','meta_value'   => 0));			
				$piereg = get_option('pie_register_2');
				?>
	<div class="wrap">
	  <h2>
		<?php _e('Unverified Users', 'piereg')?>
	  </h2>
	  <form id="verify-filter" method="post" action="">
		<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_unverified_nonce','piereg_unverified_users_nonce'); ?>
		<div class="tablenav">
		  <div class="alignleft">
			<input onclick="return window.confirm('<?php _e('This will verify users of all types','piereg');?>'); " value="<?php _e('Verify Checked Users','piereg');?>" name="verifyit" class="button-secondary" type="submit">
			<?php //wp_nonce_field('name_of_my_action','piereg_wp_nonce'); ?>
			&nbsp;
			<?php //if( !(empty($piereg['paypal_butt_id'])) && $piereg['enable_paypal']==1){ ?>
			<input value="<?php _e('Resend Pending Payment E-mail','piereg');?>" name="paymentl" class="button-secondary" type="submit">
			<?php //}  else if( $piereg['verification'] == 2 ){ ?>
			 &nbsp;
			<input value="<?php _e('Resend Verification E-mail','piereg');?>" name="emailverifyit" class="button-secondary" type="submit">
			<?php //} ?>
			&nbsp;
			<input value="<?php _e('Delete','piereg');?>" name="vdeleteit" class="button-secondary delete" type="submit">
		  </div>
		  <br class="piereg_clear">
		</div>
		<br class="piereg_clear">
		<table class="widefat">
		  <thead>
			<tr class="thead">
			  <th scope="col" class="check-column"><input onclick="checkAll(document.getElementById('verify-filter'));" type="checkbox">
			  </th>
			  <th><?php _e('User Name','piereg');?></th>
			  <th><?php _e('E-mail','piereg');?></th>
			  <th><?php _e('Registration Type','piereg');?></th>
			  <th><?php _e('Role','piereg');?></th>
			</tr>
		  </thead>
		  <tbody id="users" class="list:user user-list">
			<?php 
									foreach( $unverified as $un) {
									if( $alt ) $alt = ''; else $alt = "alternate";
									$user_object = new WP_User($un->ID);
									$roles = $user_object->roles;
									$role = array_shift($roles);
									/*
									if( $piereg['email_verify'] )
										$reg_type = get_user_meta($un->ID, 'email_verify_user',true);
									else if( $piereg['admin_verify'] )*/
										$reg_type = get_user_meta($un->ID, 'register_type');
								?>
			<tr id="user-1" class="<?php echo $alt;?>">
			  <th scope="row" class="check-column"><input name="vusers[]" id="user_<?php echo $un->ID;?>" class="administrator" value="<?php echo $un->ID;?>" type="checkbox"></th>
			  <td><strong><?php echo $un->user_login;?></strong></td>
			  <td><a href="mailto:<?php echo $un->user_email;?>" title="<?php _e('E-mail', 'piereg'); echo ": ".$un->user_email;?>"><?php echo $un->user_email;?></a></td>
			  <td><?php echo ucwords($reg_type[0]);?></td>
			  <td><?php echo ucwords($role);?></td>
			  
			</tr>
			<?php } ?>
		  </tbody>
		</table>
	  </form>
	</div>
	<?php
			}
		function verifyUsers()
		{
			
			if(isset($_POST['piereg_unverified_users_nonce']) && wp_verify_nonce( $_POST['piereg_unverified_users_nonce'], 'piereg_unverified_nonce' ))//Verify true nonce then
			{
				
				$valid = $_POST['vusers'];
				if($valid)
				{
					$option = get_option('pie_register_2');
					foreach( $valid as $user_id )
					{
						if ( $user_id )
						{
							update_user_meta( $user_id, 'active',1);
							//Sending E-Mail to newly active user
							$user 			= new WP_User($user_id);
							$subject 		= html_entity_decode($option['user_subject_email_email_thankyou'],ENT_COMPAT,"UTF-8");
							$user_email 	= $user->user_email;
							$message_temp = "";
							if($option['user_formate_email_email_thankyou'] == "0"){
								$message_temp	= nl2br(strip_tags($option['user_message_email_email_thankyou']));
							}else{
								$message_temp	= $option['user_message_email_email_thankyou'];
							}
							$message		= $this->filterEmail($message_temp,$user,$pass);
							$from_name		= $option['user_from_name_email_thankyou'];
							$from_email		= $option['user_from_email_email_thankyou'];
							$reply_email	= $option['user_to_email_email_thankyou'];
							//Headers
							$headers  = 'MIME-Version: 1.0' . "\r\n";
							$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
							if(!empty($from_email) && filter_var($from_email,FILTER_VALIDATE_EMAIL))//Validating From
								$headers .= "From: ".$from_name." <".$from_email."> \r\n";
							if($reply_email){
								$headers .= "Reply-To: {$reply_email}\r\n";
								$headers .= "Return-Path: {$from_name}\r\n";
							}else{
								$headers .= "Reply-To: {$from_email}\r\n";
								$headers .= "Return-Path: {$from_email}\r\n";
							}
							wp_mail($user_email, $subject, $message , $headers);
						}
					}
					$_POST['notice'] = __("User(s) has been activated");
				}
				else
					$_POST['notice'] = "<strong>".__('error','piereg').":</strong>".__("Please select a user to send emails to", "piereg");
			}else{
				$_POST['error'] = __("Sorry, your nonce did not verify","piereg");
			}
		}
		function PaymentLink()
		{
			global $wpdb;
			$valid = $_POST['vusers'];
			add_filter( 'wp_mail_content_type', array($this,'set_html_content_type' ));
			if( is_array($valid))
			{
				$option = get_option('pie_register_2');
				$sent = 0;
				foreach( $valid as $user_id )
				{
					$reg_type = get_user_meta($user_id, 'register_type');
					
					if($reg_type[0] != "payment_verify")
					{
						continue;
					}
					$sent++;
					update_user_meta( $user_id, 'active', 0);
					$hash = md5( time() );
					update_user_meta( $user_id, 'hash', $hash );
					
					$user 			= new WP_User($user_id);
					$subject 		= html_entity_decode($option['user_subject_email_pending_payment_reminder'],ENT_COMPAT,"UTF-8");
					$message_temp = "";
					if($option['user_formate_email_pending_payment_reminder'] == "0"){
						$message_temp	= nl2br(strip_tags($option['user_message_email_pending_payment_reminder']));
					}else{
						$message_temp	= $option['user_message_email_pending_payment_reminder'];
					}
					$message		= $this->filterEmail($message_temp,$user, $pass );
					$from_name		= $option['user_from_name_pending_payment_reminder'];
					$from_email		= $option['user_from_email_pending_payment_reminder'];
					$reply_email	= $option['user_to_email_pending_payment_reminder'];
					//Headers
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
					if(!empty($from_email) && filter_var($from_email,FILTER_VALIDATE_EMAIL))//Validating From
						$headers .= "From: ".$from_name." <".$from_email."> \r\n";
					if($reply_email){
						$headers .= "Reply-To: {$reply_email}\r\n";
						$headers .= "Return-Path: {$from_name}\r\n";
					}else{
						$headers .= "Reply-To: {$from_email}\r\n";
						$headers .= "Return-Path: {$from_email}\r\n";
					}
					wp_mail($user->user_email, $subject, $message , $headers);	
				}
				if($sent > 0)
					$_POST['notice'] = __("Payment Link Emails have been re-sent", "piereg");
				else
					$_POST['notice'] = __("Invalid User Types", "piereg");
			}
			else
			{
				$_POST['notice'] = "<strong>".__('error','piereg').":</strong>".__("Please select a user to send emails to", "piereg");
			}
		}
		function AdminEmailValidate()
		{
			
				global $wpdb;			
				$valid = $_POST['vusers'];
				add_filter( 'wp_mail_content_type', array($this,'set_html_content_type' ));
				if( is_array($valid) ) {
				$option = get_option('pie_register_2');
				$sent = 0;
				foreach( $valid as $user_id )
				{
						
						$reg_type = get_user_meta($user_id, 'register_type');
						if($reg_type[0] != "email_verify")
						{
							continue;	
						}
						$sent ++;
						update_user_meta( $user_id, 'active', 0);
						$hash = md5( time() );
						update_user_meta( $user_id, 'hash', $hash );
						
			
						$user 			= new WP_User($user_id);
						
						$subject 		= html_entity_decode($option['user_subject_email_email_verification_reminder'],ENT_COMPAT,"UTF-8");	
						
						$message_temp = "";
						if($option['user_formate_email_email_verification_reminder'] == "0"){
							$message_temp	= nl2br(strip_tags($option['user_message_email_email_verification_reminder']));
						}else{
							$message_temp	= $option['user_message_email_email_verification_reminder'];
						}
						$message		= $this->filterEmail($message_temp,$user, $pass );
						$from_name		= $option['user_from_name_email_verification_reminder'];
						$from_email		= $option['user_from_email_email_verification_reminder'];
						$replay_email	= $option['user_to_email_email_verification_reminder'];
								
						//Headers
						$headers  = 'MIME-Version: 1.0' . "\r\n";
						$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
					
						if(!empty($from_email) && filter_var($from_email,FILTER_VALIDATE_EMAIL))//Validating From
						$headers .= "From: ".$from_name." <".$from_email."> \r\n";	
						if($reply_email){
							$headers .= "Reply-To: {$reply_email}\r\n";
				
							$headers .= "Return-Path: {$from_name}\r\n";
				
						}else{
				
							$headers .= "Reply-To: {$from_email}\r\n";
				
							$headers .= "Return-Path: {$from_email}\r\n";
				
						}
			
									
						wp_mail($user->user_email, $subject, $message , $headers);	
				}
				
				if($sent > 0)
						$_POST['notice'] = __("Verification Emails have been re-sent", "piereg");
					else
						$_POST['notice'] = __("Invalid User Types", "piereg");
				}
				else
				$_POST['notice'] = "<strong>".__('error','piereg').":</strong>".__("Please select a user to send emails to", "piereg");
				
			}
		function AdminDeleteUnvalidated()
		{
			global $wpdb;
			$piereg = get_option('pie_register_2');
			$valid = $_POST['vusers'];
			if($valid)
			{	
				include_once( ABSPATH . 'wp-admin/includes/user.php' );
				foreach( $valid as $user_id )
				{
					if ( $user_id ) 
					{
						wp_delete_user($user_id);
					}
				}
				$_POST['notice'] = __("User(s) has been deleted");
			}
		}
		function cleantext($text)
		{
			$text = str_replace(chr(13), " ", $text); //remove carriage returns
			$text = str_replace(chr(10), " ", $text);
			return $text;
		}
		function disable_magic_quotes_gpc(&$value)
		{	
			$value = stripslashes($value);
			return $value;
		}
		function PieGeneralSettings()
		{
			$option 		= get_option( 'pie_register_2' );
			require_once($this->plugin_dir.'/menus/PieGeneralSettings.php');			
		}
		function PieRegPaymentGateway()
		{
			require_once($this->plugin_dir.'/menus/PieRegPaymentGateway.php');				
		}
		function PieRegAdminNotification()
		{
			require_once($this->plugin_dir.'/menus/PieRegAdminNotification.php');	
		}
		function PieRegUserNotification()
		{
			require_once($this->plugin_dir.'/menus/PieRegUserNotification.php');	
		}
		function PieRegCustomMessages()
		{
			require_once($this->plugin_dir.'/menus/PieRegCustomMessages.php');		
		}
		function PieRegHelp()
		{
			require_once($this->plugin_dir.'/menus/PieRegHelp.php');
		}
		
		function PieRegInvitationCodes()
		{
			global $wpdb;
			$piereg 	= get_option( 'pie_register_2' );		
			$codetable	= $this->codeTable();
			
			if( isset($_POST['invi_del_id']) ) 
			{
				if(isset($_POST['piereg_invitation_nonce']) && wp_verify_nonce( $_POST['piereg_invitation_nonce'], 'piereg_wp_invitation_nonce' ))
				{
					/*if($wpdb->query("DELETE FROM ".$codetable." WHERE id = ".$_POST['invi_del_id']))*/	
					if($wpdb->query( $wpdb->prepare("DELETE FROM ".$codetable." WHERE id = %s", $_POST['invi_del_id']) ))
					$_POST['notice'] = "The Invitation Code has been deleted";
				}else{
					$_POST['error'] = __("Sorry, your nonce did not verify","piereg");
				}
			}
			else if( isset($_POST['status_id']) ) 
			{
				if(isset($_POST['piereg_invitation_nonce']) && wp_verify_nonce( $_POST['piereg_invitation_nonce'], 'piereg_wp_invitation_nonce' ))
				{
					if($wpdb->query("update ".$codetable." SET status = CASE WHEN status = 1 THEN  0 WHEN status = 0 THEN 1 ELSE  0 END  WHERE id = ".$_POST['status_id']))	
					$_POST['notice'] = "Status has been changed.";
				}
				else{
					$_POST['error'] = __("Sorry, your nonce did not verify","piereg");
				}
			}
			else if( isset($_POST['piereg_codepass']) ) 
			{
				if(isset($_POST['piereg_invitation_nonce']) && wp_verify_nonce( $_POST['piereg_invitation_nonce'], 'piereg_wp_invitation_nonce' ))
				{
					$update["codepass"] = $_POST['piereg_codepass'];
					$codespasses=explode("\n",$update["codepass"]);
					
					foreach( $codespasses as $k=>$v )
					{
						$this->InsertCode(trim($v));
					}
					$piereg['enable_invitation_codes'] = 	$_POST['enable_invitation_codes'];	
					update_option( 'pie_register_2',$piereg);
					global $piereg_global_options;
					$piereg_global_options = $piereg;	
					
					if(isset($_POST['invitation_code_usage']) && is_numeric($_POST['invitation_code_usage'])  && $_POST['invitation_code_usage'] > 0)
					{
						$piereg["invitation_code_usage"] = $_POST['invitation_code_usage'];
						update_option( 'pie_register_2',$piereg);
						global $piereg_global_options;
						$piereg_global_options = $piereg;
					}
				}
				else{
					$_POST['error'] = __("Sorry, your nonce did not verify","piereg");
				}
			}
			require_once($this->plugin_dir.'/menus/PieRegInvitationCodes.php');		
		}
		function InsertCode($name)
		{
				if(empty($name)) return false;
				
				global $wpdb;
				$piereg=get_option( 'pie_register_2' );
				
				$codetable=$this->codeTable();
				$expiry=((isset($piereg['codeexpiry']))?$piereg['codeexpiry']:0);
				$users = $wpdb->get_results( "SELECT * FROM $codetable WHERE `name`='{$name}'" );
				$counts = count($users);
				$wpdb->flush();
				
				if( $counts > 0 )
				{
					return true;
				}
				
				$name = esc_sql(trim(preg_replace("/[^A-Za-z0-9_-]/", '', $name)));			
				$date=date("Y-m-d");
				$usage = $_POST['invitation_code_usage'];			
				$wpdb->query("INSERT INTO ".$codetable." (`created`,`modified`,`name`,`count`,`status`,`code_usage`)VALUES('".$date."','".$date."','".$name."','".$counts."','1','".$usage."')");
				$wpdb->flush();
				return true;
			}
		function generateCSV()
		{
			if(isset($_POST['piereg_export_users_nonce']) && wp_verify_nonce( $_POST['piereg_export_users_nonce'], 'piereg_wp_exportusers_nonce' ))
			{
				global $wpdb;
				$user_table 		= $wpdb->prefix . "users";
				$user_meta_table 	= $wpdb->prefix . "usermeta";
				
				
				$fields = "";
				if(sizeof($_POST['pie_fields_csv']) > 0)
				{
					$fields	=	implode(',',array_keys($_POST['pie_fields_csv']));					
				}			
				
				
				if(!isset($_POST['pie_fields_csv']) || sizeof($_POST['pie_fields_csv']) == 0)
				{
					$_POST['pie_fields_csv'] = array();
				}
				if(!isset($_POST['pie_meta_csv']) || sizeof($_POST['pie_meta_csv']) == 0)
				{
					$_POST['pie_meta_csv'] = array();
				}
					
				
				$heads	= array_merge(array("id"=>"User ID"),$_POST['pie_fields_csv'],$_POST['pie_meta_csv']);
				
				$query 	= "SELECT ID ";
				$query 	.= ($fields)?",$fields " : "";
				$query 	.= " FROM $user_table ";
				
				if($_POST['date_start'] != "" || $_POST['date_end'] != "")
				{
					$_date_start = date("Y-m-d",strtotime($_POST['date_start']));
					$_date_end = date("Y-m-d",strtotime($_POST['date_end']));
					$date_start = FALSE;
					$query .= " where ";
					if($_POST['date_start'] != "")
					{
						$query .= " user_registered >= '{$_date_start} 00:00:00' ";
						$date_start = TRUE;			
					}
					
					if($_POST['date_end'] != "")
					{
						if($date_start)
						{
							$query .= " AND ";	
						}
						$query .= " user_registered <= '{$_date_end} 23:59:59' ";			
					}		
				}		
				$query .= " order by user_login asc";
		
				$users = $wpdb->get_results($query,ARRAY_A);
				global  $wp_roles,$wpdb;
				if(sizeof($users ) > 0){
					$dfile = "pieregister_exported_users_".date("Y-m-d").".csv";
					header('Content-Type: application/csv');
					header('Content-Disposition: attachment; filename='.$dfile);
					header('Pragma: no-cache');
					echo '"'.implode('","',$heads).'"'."\r\n";
					
					foreach ($users as $user_key=>$user_value){
						$content_data = '';
						foreach($user_value as $single_user_data){
							$content_data.='"'.$single_user_data.'",'; 
						}
						if(sizeof($_POST['pie_meta_csv']) > 0){
							foreach($_POST['pie_meta_csv'] as $key=>$value){
								
								if($key == "wp_capabilities"){
									$user = get_userdata( $user_value['ID'] );
									
									 $capabilities = $user->{$wpdb->prefix . 'capabilities'};
			
									if ( !isset( $wp_roles ) )
										$wp_roles = new WP_Roles();
									$meta_value = '';
									foreach ( $wp_roles->role_names as $role => $name ):
										if ( array_key_exists( $role, $capabilities ) )
											$meta_value = $role;
									endforeach;
								}
								else{
									$meta_value = get_user_meta($user_value['ID'],$key,true);
								}
								
								$content_data.='"'.htmlentities($meta_value, ENT_QUOTES | ENT_IGNORE, "UTF-8").'"'.",";
							}
						}
						echo rtrim($content_data,',');
						echo "\r\n";
					}
					die();
				}
				else
				{
					$_POST['error_message'] = __("No Record Found","piereg");
				}
			}
			else{
				$_POST['error_message'] = __("Sorry, your nonce did not verify","piereg");
			}
		}
		function csv_to_array($filename='', $delimiter=','){
			if(!file_exists($filename) || !is_readable($filename))
				return FALSE;
			$header = NULL;
			$data = array();
			if (($handle = fopen($filename, 'r')) !== FALSE)
			{
				while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
				{
					if(!$header)
						$header = $row;
					else
						$data[] = array_combine($header, $row);
				}
				fclose($handle);
			}
			return $data;
		}
		function importUsers()
		{
			if(isset($_POST['piereg_import_users_nonce']) && wp_verify_nonce( $_POST['piereg_import_users_nonce'], 'piereg_wp_importusers_nonce' ))
			{
				$success_import = 0;
				$unsuccess_import = 0;
				$already_exist = 0;
				if(empty($_FILES['csvfile']['name']))
				{
					$_POST['error_message'] = apply_filters("piereg_didnt_select_file_to_import",__("You did not select a file to import users",'piereg'));
					return;	
				}
				$ext = pathinfo($_FILES['csvfile']['name'], PATHINFO_EXTENSION);
				if($ext != "csv")
				{
					$_POST['error_message'] = __("Invalid CSV file.",'piereg');	
					return;	
				}
				$current_csv_file_data = "";
				if ($_FILES['csvfile']['tmp_name']){
					$csv_data = $this->csv_to_array($_FILES['csvfile']['tmp_name']);
				}
				if(!isset($csv_data[0]) or sizeof($csv_data[0]) < 3)
				{
					 $_POST['error_message'] = __("Invalid CSV File. It must contain all the default user fields.",'piereg');
					 return;	
				}
				$table_fields = array(
									  //////////// DEFAULT FEILDS //////////
									  "User ID"=>"ID",
									  "Username"=>"user_login",
									  "Password"=>'user_pass',
									  "Nickname"=>"user_nicename",
									  "E-mail"=>"user_email",
									  "Website"=>"user_url",
									  "User Registered"=>"user_registered",
									  "Display name"=>"display_name",
									  ///////////// USER META /////////////
									  "First Name"=>"first_name",
									  "Last Name"=>"last_name",
									  "Biographical Info"=>"description",
									  "Role"=>"wp_capabilities");
				$user_csv_data = array();
				$temp_data = array();
				$user_default_data = array();
				$user_meta_key = array();
				foreach($csv_data as $arr_key=>$arr_val){
					foreach($arr_val as $head_key=>$user_data){
						switch($head_key):
							case 'User ID' :
							case 'Username' :
							case 'user_pass' :
							case 'Nickname' :
							case 'E-mail' :
							case 'Website' :
							case 'User Registered' :
							case 'Display name' :
								$user_default_data[$table_fields[$head_key]] = utf8_encode(html_entity_decode($user_data));
							break;
							case 'First Name' :
							case 'Last Name' :
							case 'Biographical Info' :
							case 'Role' :
								$user_meta_key[$table_fields[$head_key]] = utf8_encode(html_entity_decode($user_data));
							break;
						endswitch;
						$temp_data[$table_fields[$head_key]] = $user_data;
					}
					$user_csv_data[$arr_key] = $temp_data;
					$user_default_data['user_pass'] = wp_generate_password();
					
					if ( username_exists( $user_default_data['user_login'] ) ){
						if(isset($_POST['update_existing_users']) && $_POST['update_existing_users'] == "yes"){
							$user_id = wp_update_user($user_default_data);
							if(isset($user_id)){
								$this->update_user_meta_by_array($user_id,$user_meta_key);
							}
						}else{
							$already_exist++;
						}
					}else{
						if(get_user_by('ID',$user_default_data['ID'])){
							if(isset($_POST['update_existing_users']) && $_POST['update_existing_users'] == "yes"){
								$user_id = wp_update_user($user_default_data);
								if(isset($user_id)){
									$this->update_user_meta_by_array($user_id,$user_meta_key);
								}
							}else{
								$already_exist++;
							}
						}else{
							unset($user_default_data['ID']);
							$user_id = wp_insert_user($user_default_data);
							if(isset($user_id)){
								$this->update_user_meta_by_array($user_id,$user_meta_key);
							}
							$success_import++;
						}
					}
					
					unset($temp_data);
					unset($user_meta_key);
					unset($user_default_data);
				}
				
				$_POST['success_message'] = __("$success_import user(s) imported.",'piereg');
				if($unsuccess_import)
					$_POST['error_message'] = __("$unsuccess_import user(s) do not imported.",'piereg');
					
				if($already_exist){
					if(isset($_POST['update_existing_users']) && $_POST['update_existing_users'] == "yes"){
						$_POST['success_message'] .= "<br />".__("$already_exist user(s) Update.",'piereg');
					}else{
						$_POST['error_message'] = __("$already_exist user(s) already exist.",'piereg');
					}
				}
			}else{
				$_POST['error_message'] = __("Sorry, your nonce did not verify","piereg");	
			}
		}
		function update_user_meta_by_array($user_id,$user_meta_keys)
		{
			if(isset($user_id) and isset($user_meta_keys))
			{
				if(is_array($user_meta_keys)){
					foreach($user_meta_keys as $key=>$val){
						if($key == "wp_capabilities"){
							$wp_user_object = new WP_User($user_id);
							$wp_user_object->set_role($val);
							unset($wp_user_object);
						}else{
							update_user_meta($user_id,$key,$val);
						}
					}
				}
			}
		}
		function PieRegImportExport()
		{
			require_once($this->plugin_dir.'/menus/PieRegImportExport.php');		
		}
		
		function SaveSettings()
		{
			$update = get_option( 'pie_register_2' );
			if( isset($_POST['action']) && $_POST['action'] == 'pie_reg_update' )
			{
				if(isset($_POST['payment_gateway_page']))
				{
					if(isset($_POST['piereg_paypal_settings_nonce']) && wp_verify_nonce( $_POST['piereg_paypal_settings_nonce'], 'piereg_wp_paypal_settings_nonce' ))
					{
						$update["enable_paypal"]	= intval($_POST['enable_paypal']);
						$update["paypal_butt_id"]	= $this->disable_magic_quotes_gpc($_POST['piereg_paypal_butt_id']);
						$update["paypal_sandbox"]	= $_POST['piereg_paypal_sandbox'];
					}else{
						$_POST['error'] = __("Sorry, your nonce did not verify","piereg");
					}
				}
				else if(isset($_POST['admin_email_notification_page'])){
					
					$update['enable_admin_notifications']	= intval($_POST['enable_admin_notifications']);
					$update['admin_sendto_email']			= trim($_POST['admin_sendto_email']);
					$update['admin_from_name']				= $this->disable_magic_quotes_gpc(mb_convert_encoding($_POST['admin_from_name'],'HTML-ENTITIES','utf-8'));
					$update['admin_from_email']				= trim($_POST['admin_from_email']);
					$update['admin_to_email']				= trim($_POST['admin_to_email']);
					$update['admin_bcc_email']				= trim($_POST['admin_bcc_email']);
					$update['admin_subject_email']			= $this->disable_magic_quotes_gpc(mb_convert_encoding($_POST['admin_subject_email'],'HTML-ENTITIES','utf-8'));
					
					$update['admin_message_email_formate']	= intval($_POST['admin_message_email_formate']);
					$update['admin_message_email']			= $this->disable_magic_quotes_gpc(mb_convert_encoding($_POST['admin_message_email'],'HTML-ENTITIES','utf-8'));
				
					
				}
				else if(isset($_POST['user_email_notification_page']))
				{
					
					$pie_user_email_types = get_option( 'pie_user_email_types'); 
					
					foreach ($pie_user_email_types as $val=>$type){
						
						$update['user_from_name_'.$val]		= $this->disable_magic_quotes_gpc(mb_convert_encoding(trim($_POST['user_from_name_'.$val]),'HTML-ENTITIES','utf-8'));
						$update['user_from_email_'.$val]	= $this->disable_magic_quotes_gpc(mb_convert_encoding(trim($_POST['user_from_email_'.$val]),'HTML-ENTITIES','utf-8'));
						$update['user_to_email_'.$val]		= $this->disable_magic_quotes_gpc(mb_convert_encoding(trim($_POST['user_to_email_'.$val]),'HTML-ENTITIES','utf-8'));
						/*$update['user_bcc_email_'.$val]		= $this->disable_magic_quotes_gpc(mb_convert_encoding(trim($_POST['user_bcc_email_'.$val]),'HTML-ENTITIES','utf-8'));*/
						$update['user_subject_email_'.$val]	= $this->disable_magic_quotes_gpc(mb_convert_encoding(trim($_POST['user_subject_email_'.$val]),'HTML-ENTITIES','utf-8'));
						$update['user_formate_email_'.$val]	= intval($_POST['user_formate_email_'.$val]);
						$update['user_message_email_'.$val]	= $this->disable_magic_quotes_gpc(mb_convert_encoding($_POST['user_message_email_'.$val],'HTML-ENTITIES','utf-8'));	
					}
					
				}
				else if(isset($_POST['general_settings_page'])){
					if(isset($_POST['piereg_general_settings_nonce']) && wp_verify_nonce( $_POST['piereg_general_settings_nonce'], 'piereg_wp_general_settings_nonce' ))
					{
						$update['display_hints']			= intval($_POST['display_hints']);
						/*$update['subscriber_login']			= intval($_POST['subscriber_login']);
						$update['modify_avatars']			= intval($_POST['modify_avatars']);*/
						$update['show_admin_bar']			= intval($_POST['show_admin_bar']);
						$update['allow_pr_edit_wplogin']	= intval($_POST['allow_pr_edit_wplogin']);
						
						$update['block_WP_profile']			= intval($_POST['block_WP_profile']);
						
						$update['redirect_user']			= intval($_POST['redirect_user']);
						$update['block_wp_login']			= intval($_POST['block_wp_login']);
						$update['alternate_register']		= intval($_POST['alternate_register']);
						
						$update['alternate_login']			= intval($_POST['alternate_login']);
						$update['alternate_forgotpass']		= intval($_POST['alternate_forgotpass']);
						$update['alternate_profilepage']	= intval($_POST['alternate_profilepage']);
						$update['after_login']				= intval($_POST['after_login']);
						
						$update['alternate_logout']			= intval($_POST['alternate_logout']);
						
						$update['alternate_logout_url']		= $_POST['alternate_logout_url'];
						
						//since 2.0.10
						
						$update['login_username_label']	= $_POST['login_username_label'];
						$update['login_username_placeholder']	= $_POST['login_username_placeholder'];
						$update['login_password_label'] = $_POST['login_password_label'];
						$update['login_password_placeholder'] = $_POST['login_password_placeholder'];
						$update['capthca_in_login_label']	= $_POST['capthca_in_login_label'];
						$update['piereg_recapthca_skin_login'] = $_POST['piereg_recapthca_skin_login'];
						$update['capthca_in_login']			= intval($_POST['capthca_in_login']);
						
						$update['forgot_pass_username_label']	= $_POST['forgot_pass_username_label'];
						$update['forgot_pass_username_placeholder'] = $_POST['forgot_pass_username_placeholder'];
						
						
						$update['pass_strength_indicator_label']	= $_POST['pass_strength_indicator_label'];
						$update['pass_very_weak_label']				= $_POST['pass_very_weak_label'];
						$update['pass_weak_label']					= $_POST['pass_weak_label'];
						$update['pass_medium_label']				= $_POST['pass_medium_label'];
						$update['pass_strong_label']				= $_POST['pass_strong_label'];
						$update['pass_mismatch_label']				= $_POST['pass_mismatch_label'];
						
						$update['capthca_in_forgot_pass_label']	= $_POST['capthca_in_forgot_pass_label'];
						$update['piereg_recapthca_skin_forgot_pass']	= $_POST['piereg_recapthca_skin_forgot_pass'];
						$update['capthca_in_forgot_pass']	= intval($_POST['capthca_in_forgot_pass']);
						
						//since 2.0.10
						$update['remove_PR_settings']		= intval($_POST['remove_PR_settings']);
						
						
						$update['outputcss']				= intval($_POST['outputcss']);
						$update['outputjquery_ui']			= intval($_POST['outputjquery_ui']);
						
						/*$update['outputhtml']				= $this->disable_magic_quotes_gpc(mb_convert_encoding($_POST['outputhtml'],'HTML-ENTITIES','utf-8'));
						$update['no_conflict']				= $this->disable_magic_quotes_gpc(mb_convert_encoding($_POST['no_conflict'],'HTML-ENTITIES','utf-8'));*/
						$update['verification']				= intval($_POST['verification']);
						$update['email_edit_verification_step'] = ((intval($_POST['email_edit_verification_step']) > 0 && intval($_POST['email_edit_verification_step']) < 3)? intval($_POST['email_edit_verification_step']) : 1 );
						
						$update['grace_period']				= intval($_POST['grace_period']);
						$update['captcha_publc']			= $_POST['captcha_publc'];
						$update['captcha_private']			= $_POST['captcha_private'];
						/*$update['custom_css']				= strip_tags($_POST['custom_css']);*/
						$update['custom_css']				= $this->disable_magic_quotes_gpc(mb_convert_encoding(strip_tags($_POST['custom_css']),'HTML-ENTITIES','utf-8'));
						/*$update['tracking_code']			= $_POST['tracking_code'];*/
						$update['tracking_code']			= $this->disable_magic_quotes_gpc(mb_convert_encoding(strip_tags($_POST['tracking_code']),'HTML-ENTITIES','utf-8'));
						$update['custom_logo_url']			= $_POST['custom_logo_url'];
						
						$update['custom_logo_tooltip']		= $_POST['custom_logo_tooltip'];
						
						$update['custom_logo_link']			= $_POST['custom_logo_link'];
						$update['show_custom_logo']			= (isset($_POST['show_custom_logo']))?$_POST['show_custom_logo']:0;
					}else{
						$_POST['error'] = __("Sorry, your nonce did not verify","piereg");
					}
				}
				update_option( 'pie_register_2', $update );
				global $piereg_global_options;
				$piereg_global_options = $update;
				/**
					* update global options since 2.0.12
				**/
				global $piereg_global_options;
				$piereg_global_options = $update;
				
				if(isset($error) && trim($error) != "" )
				{
					$_POST['PR_license_notice'] = $error;
				}
				if(!isset($_POST['error']) && empty($_POST['error']))
					$_POST['notice'] = apply_filters("piereg_settings_saved",__('Settings Saved', 'piereg'));
			}
		}
		function addTextField($field,$no)
		{		
			$name 	= $this->createFieldName($field,$no);
			$id 	= $this->createFieldID($field,$no);
			
			echo '<input disabled="disabled" id="'.$id.'" name="'.$name.'" class="input_fields"  placeholder="'.((isset($field['placeholder']))?$field['placeholder']:"").'" type="text" value="'.((isset($field['default_value']))?$field['default_value']: "" ).'" />';	
		}
		function addInvitationField($field,$no)
		{		
			$name 	= $this->createFieldName($field,$no);
			
			echo '<input type="hidden" id="default_'.$field['type'].'">';
			echo '<input disabled="disabled" id="invitation_field" name="'.$name.'" class="input_fields"  placeholder="'.$field['placeholder'].'" type="text" value="'.((isset($field['default_value']))?$field['default_value']: "" ).'" />';	
		}
		function addDefaultField($field,$no)
		{		
			$name 	= $this->createFieldName($field,$no);
			$id 	= $this->createFieldID($field,$no);
			
			if($field['field_name']=="description")
			{
				echo '<textarea  rows="5" cols="73" disabled="disabled" id="default_'.$field['field_name'].'" name="'.$name.'" style="width:100%;"></textarea>';	
			}
			else
			{
				echo '<input disabled="disabled" id="default_'.$field['field_name'].'" name="'.$name.'" class="input_fields"  placeholder="'.(isset($field['placeholder'])? $field['placeholder']:"").'" type="text"  />';	
			}
			
			echo '<input type="hidden" name="field['.$field['id'].'][id]" value="'.$field['id'].'" id="id_'.$field['id'].'">';
			echo '<input type="hidden" name="field['.$field['id'].'][type]" value="default" id="type_'.$field['id'].'">';
			echo '<input type="hidden" name="field['.$field['id'].'][label]" value="'.$field['label'].'" id="label_'.$field['id'].'">';
			echo '<input type="hidden" name="field['.$field['id'].'][field_name]" value="'.$field['type'].'" id="label_'.$field['id'].'">';
			echo '<input type="hidden" id="default_'.$field['type'].'">';
		}
		function addEmail($field,$no)
		{
			$name 			= $this->createFieldName($field,$no);
			$id 			= $this->createFieldID($field,$no);
			$confirm_email = 'style="display:none;"';
			
			echo '<input disabled="disabled" id="'.$id.'" name="'.$name.'" class="input_fields"  placeholder="'.$field['placeholder'].'" type="text" value="'.((isset($field['default_value']))?$field['default_value']: "" ).'" />';
			
			if(isset($field['confirm_email']))
			{
				$confirm_email	= "";
			}
			
			$label2 = (isset($field['label2']) and !empty($field['label2']))?$field['label2'] : __("Confirm E-Mail","piereg");
			echo '</div><div '.$confirm_email.' id="field_label2_'.$no.'" class="label_position confrim_email_label2"><label>'.$label2.'</label></div><div class="fields_position"><div id="confirm_email_field_'.$no.'" '.$confirm_email.' class="inner_fields"><input disabled="disabled" type="text" class="input_fields" placeholder="'.$field['placeholder'].'" ></div>';
		}
		function addPassword($field,$no)
		{		
			$name 			= $this->createFieldName($field,$no);
			$id 			= $this->createFieldID($field,$no);
			
			
			echo '<input disabled="disabled" id="'.$id.'" name="'.$name.'" class="input_fields"  placeholder="'.$field['placeholder'].'" type="text" value="" />';
			
			$label2 = (isset($field['label2']) and !empty($field['label2']))?$field['label2'] : __("Confirm Password","piereg");
			echo '</div><div id="field_label2_'.$no.'" class="label_position"><label>'.$label2.'</label></div><div class="fields_position"><div id="confirm_email_field_'.$no.'" '.((isset($confirm_email))?$confirm_email:"").' class="inner_fields"><input disabled="disabled" type="text" class="input_fields" placeholder="'.$field['placeholder'].'" > </div>';
		}
		
		function addUpload($field,$no)
		{
			$name 	= $this->createFieldName($field,$no);
			$id 	= $this->createFieldID($field,$no);
			
			echo '<input disabled="disabled" id="'.$id.'" name="'.$name.'" class="input_fields"  type="file"  />';	
		}
		function addProfilePicUpload($field,$no)
		{
			$name 	= $this->createFieldName($field,$no);
			$id 	= $this->createFieldID($field,$no);
			echo '<input disabled="disabled" id="'.$id.'" name="'.$name.'" class="input_fields"  type="file"  />';
		}
		
		function addAddress($field,$no)
		{
			$name 	= $this->createFieldName($field,$no);
			$id 	= $this->createFieldID($field,$no);
			
			echo '<input type="hidden" id="default_'.$field['type'].'">';
			echo '<div class="address" id="address_fields">
			  <input disabled="disabled" type="text" class="input_fields">
			  <label>'.__("Street Address","piereg").'</label>
			</div>
			<div class="address" id="address_address2_'.$no.'">
			  <input disabled="disabled" type="text" class="input_fields">
			  <label>'.__("Address Line 2","piereg").'</label>
			</div>
			<div class="address">
			  <div class="address2">
				<input disabled="disabled" type="text" class="input_fields">
				<label>'.__("City","piereg").'</label>
			  </div>';
			
			 $hide_state = "";
			 if(isset($field['hide_state']) && $field['hide_state'])
			 {
				$hide_state 		= 'style="display:none;"';	
				$hide_usstate 		= 'style="display:none;"';	
				$hide_canstate 		= 'style="display:none;"';	 
			 } 
			 else 
			 {
					if($field['address_type'] == "International")
					{
						$hide_state 		= '';		
					}
					else if($field['address_type'] == "United States")
					{
						$hide_usstate 		= '';	
					}
					else if($field['address_type'] == "Canada")
					{
						$hide_canstate 		= '';	
					}
			 }
			
			
			 echo '<div class="address2 state_div_'.$no.'" id="state_'.$no.'" '.$hide_state .'>
				<input disabled="disabled" type="text" class="input_fields">
				<label>'.__("State / Province / Region","piereg").'</label>
			  </div>
			  <div class="address2 state_div_'.$no.'" id="state_us_'.$no.'" '.((isset($hide_usstate))?$hide_usstate:"") .'>
				<select disabled="disabled" id="state_us_field_'.$no.'">
				  <option value="" selected="selected">'.$field['us_default_state'].'</option>
				  
				</select>
				<label>'.__("State","piereg").'</label>
			  </div>
			  <div class="address2 state_div_'.$no.'" id="state_canada_'.$no.'" '.((isset($hide_usstate))?$hide_canstate:"").'>
				<select disabled="disabled" id="state_canada_field_'.$no.'">
				  <option value="" selected="selected">'.$field['canada_default_state'].'</option>
				  
				</select>
				<label>'.__("Province","piereg").'</label>
			  </div>
			</div>
			<div class="address">';
			 
			
			 $hideAddress2= "";
			 if(isset($field['hide_address2']) && $field['hide_address2'])
			 {
				$hideAddress2 = 'style="display:none;"';	 
			 }		
			echo ' <div class="address2" '.$hideAddress2.'>
				<input disabled="disabled" type="text" class="input_fields">
				<label>'.__("Zip / Postal Code","piereg").'</label>
			  </div>';
			 
			 $hideCountry = "";
			 if($field['address_type'] != "International")
			 {
				$hideCountry = 'style="display:none;"';	 
			 }
			 
			  echo '<div id="address_country_'.$no.'" class="address2" '.$hideCountry.'>
						<select disabled="disabled">
							<option>'.$field['default_country'].'</option>
						</select>
						<label>'.__("Country","piereg").'</label>
					</div>
			</div>';	
		}
		function addTextArea($field,$no)
		{
			$name 	= $this->createFieldName($field,$no);
			$id 	= $this->createFieldID($field,$no);
				
			echo '<textarea disabled="disabled" id="'.$id.'" name="'.$name.'" rows="'.$field['rows'].'" cols="'.$field['cols'].'"   placeholder="'.$field['placeholder'].'" style="width:100%;">'.$field['default_value'].'</textarea>';		
		}
		
		function addDropdown($field,$no)
		{
			
			$name 	= $this->createFieldName($field,$no);
			$id 	= $this->createFieldID($field,$no);
			
			$multiple = "";
			if($field['type']=="multiselect")
			{
				$multiple 	= 'multiple';	
				$name		.= "[]";	
			}
			
					
			echo '<select '.$multiple.' id="'.$name.'" name="'.$name.'" disabled="disabled">';
		
			if(sizeof($field['value']) > 0)
			{
			
				for($a = 0 ; $a < sizeof($field['value']) ; $a++)
				{
					$selected = '';
					if(in_array($a,$field['selected']))
					{
						$selected = 'selected="selected"';	
					}				
					echo '<option '.$selected.' value="'.$field['value'][$a].'">'.$field['display'][$a].'</option>';	
				}		
			}	
			echo '</select>';			
		}
		function addNumberField($field,$no)
		{
			$name 	= $this->createFieldName($field,$no);
			$id 	= $this->createFieldID($field,$no);
			
			echo '<input disabled="disabled" id="'.$id.'" name="'.$name.'" class="input_fields"  placeholder="'.$field['placeholder'].'" min="'.$field['min'].'" max="'.$field['max'].'" type="number" value="'.$field['default_value'].'" />';		
		}
		function addCheckRadio($field,$no)
		{
			if(sizeof($field['value']) > 0)
			{
				echo '<div class="radio_wrap">';
				$name 	= $this->createFieldName($field,$no);
				$id 	= $this->createFieldID($field,$no);
				
					
				for($a = 0 ; $a < sizeof($field['value']) ; $a++)
				{
					$checked = '';
					if(isset($field['selected']) && is_array($field['selected']) && in_array($a,$field['selected']))
					{
						$checked = 'checked="checked"';	
					}				
					echo '<label>'.$field['display'][$a].'</label>';	
					echo '<input '.$checked.' type="'.$field['type'].'" '.((isset($multiple))?$multiple:"").' name="'.$field['type'].'_'.$field['id'].'[]" class="radio_fields" disabled="disabled" >';
				}		
				echo '</div>';
			}			
		}	
		function addDate($field,$no)
		{		
			$name 	= $this->createFieldName($field,$no);
			$id 	= $this->createFieldID($field,$no);
			
			$datefield 		= 'style="display:none;"';
			$datepicker 	= 'style="display:none;"';
			$datedropdown 	= 'style="display:none;"';
			$calendar_icon 	= 'style="display:none;"';
			$calendar_url 	= 'style="display:none;"';
			
			if($field['date_type'] == "datefield")
			{
				$datefield = "";		
			}
			else if($field['date_type'] == "datepicker")
			{
				$datepicker = "";
				if($field['calendar_icon'] == "calendar")		
				{
					$calendar_icon = "";
				}
			}
			else if($field['date_type'] == "datedropdown")
			{
				$datedropdown = "";		
			}
			
			echo '<div class="time date_format_field" id="datefield_'.$no.'" '.$datefield.'>
					  <div class="time_fields" id="mm_'.$no.'">
						<input disabled="disabled" type="text" class="input_fields">
						<label>'.__("MM","piereg").'</label>
					  </div>
					  <div class="time_fields" id="dd_'.$no.'">
						<input disabled="disabled" type="text" class="input_fields">
						<label>'.__("DD","piereg").'</label>
					  </div>
					  <div class="time_fields" id="yy_'.$no.'">
						<input disabled="disabled" type="text" class="input_fields">
						<label>'.__("YYYY","piereg").'</label>
					  </div>
					</div>';
					
			echo	'<div class="time date_format_field" id="datepicker_'.$no.'" '.$datepicker.'>
					  <input disabled="disabled" type="text" class="input_fields">
					  <img src="'.plugins_url('pie-register').'/images/calendar.png" id="calendar_image_'.$no.'" '.$calendar_icon.' /> </div>';
					  
				  
			echo '<div class="time date_format_field" id="datedropdown_'.$no.'"  '.$datedropdown.'>
					  <div class="time_fields" id="month_'.$no.'">
						<select disabled="disabled">
						  <option>'.__("Month","piereg").'</option>
						</select>
					  </div>
					  <div class="time_fields" id="day_'.$no.'">
						<select disabled="disabled">
						  <option>'.__("Day","piereg").'</option>
						</select>
					  </div>
					  <div class="time_fields" id="year_'.$no.'">
						<select disabled="disabled">
						  <option>'.__("Year","piereg").'</option>
						</select>
					  </div>
					</div>';	
			
			
		}
		function piereg_get_small_string($string,$lenght=100){
			$string = strip_tags(html_entity_decode( $string , ENT_COMPAT, 'UTF-8'));
			if(strlen($string) > $lenght){
				$string = wordwrap($string, $lenght, "<br />", true);
				$string = explode("<br />",$string);
				return $string[0]."....";
			}
			return $string;
		}
		function addHTML($field,$no)
		{
			echo '<div id="field_'.$no.'" class="htmldiv" id="htmlbox_'.$no.'_div">'.$this->piereg_get_small_string($field['html'],200).'</div>';
		}
		function addSectionBreak($field,$no)
		{
			echo '<div style="width:100%;float:left;border: 1px solid #aaaaaa;margin-top:25px;"></div>';	
		}
		function addPageBreak($field,$no)
		{
			echo '<img src="'.plugins_url('pie-register').'/images/pagebreak.png" style="max-width:100%;" />';
			
		}
		function addName($field,$no)
		{
			echo '<input type="hidden" id="default_'.$field['type'].'">';
			echo '<input disabled="disabled" type="text" class="input_fields">';
			$label2 = (isset($field['label2']) and !empty($field['label2']))?$field['label2'] : __("Last Name","piereg");
			echo '</div><div id="field_label2_'.$no.'" class="label_position"><label>'.$label2.'</label></div><div class="fields_position">  <input disabled="disabled" type="text" class="input_fields">';	
		
		}
		function addTime($field,$no)
		{
			
			$name 	= $this->createFieldName($field,$no);
			$id 	= $this->createFieldID($field,$no);
			
			$format = "display:none;";		
			
			if($field['time_type']=="12")
			{
				$format = "";
			}		
			echo '<div class="time"><div class="time_fields"><input disabled="disabled" type="text" class="input_fields"><label>'.__("HH","piereg").'</label></div><span class="colon">:</span><div class="time_fields"><input disabled="disabled" type="text" class="input_fields"><label>'.__("MM","piereg").'</label></div><div id="time_format_field_'.$no.'" class="time_fields" style="'.$format.'"><select disabled><option>'.__("AM","piereg").'</option><option>PM</option></select></div></div>';
		
		}
		function addCaptcha($field,$no)
		{
			$name 	= $this->createFieldName($field,$no);
			$id 	= $this->createFieldID($field,$no);
				
			echo '<img id="captcha_img" src="'.plugins_url('pie-register').'/images/recatpcha.jpg" />';	
			echo '<input type="hidden" id="default_'.$field['type'].'">';	
		}
		function addMath_Captcha($field,$no)
		{
			$name 	= $this->createFieldName($field,$no);
			$id 	= $this->createFieldID($field,$no);
				
			echo '<img id="captcha_img" src="'.plugins_url('pie-register').'/images/math_catpcha.png" />';	
			echo '<input type="hidden" id="default_'.$field['type'].'">';	
		}
		function addList($field,$no)
		{
			if($field['cols']=="0")
			$field['cols'] = 1;
			
			$name 	= $this->createFieldName($field,$no);
			$id 	= $this->createFieldID($field,$no);
			$width  = 90 / $field['cols']; 
			
			for($a = 1 ; $a <= $field['cols'] ;$a ++)
			{
				echo '<input type="text" id="field_'.$no.'" class="input_fields" style="width:'.$width.'%;margin-right:2px;" >';
			}
			echo '<img src="'.plugins_url('pie-register').'/images/plus.png" />';
		}		
		function createFieldName($field,$no)
		{
			return "field_[".$field['id']."]";		
		}
		function createFieldID($field,$no)
		{
			return "field_".$field['id'];	
		}
		function pie_retrieve_password_title()
		{
			$option = get_option( 'pie_register_2' );
			return $option['user_subject_email_email_forgotpassword'];		
		}
		function pie_retrieve_password_message($content,$key)
		{
			$activation_url =  wp_login_url("url")."?action=rp&key=".$key."&login=".$_POST['log'];
			$option 		= get_option( 'pie_register_2' );		 
			echo str_replace("%forgot_pass_link%","$activation_url",$option['user_message_email_email_forgotpassword']);		
			
		}
		private function log_ipn_results($success) {
			$hostname = gethostbyaddr ( $_SERVER ['REMOTE_ADDR'] );
			// Timestamps
			$text = '[' . date ( 'm/d/Y g:i A' ) . '] - ';
			// Success or failure being logged?
			if ($success)
				$this->ipn_status = $text . 'SUCCESS:' . $this->ipn_status . "!\n";
			else
				$this->ipn_status = $text . 'FAIL: ' . $this->ipn_status . "!\n";
				// Log the POST variables
			$this->ipn_status .= "[From:" . $hostname . "|" . $_SERVER ['REMOTE_ADDR'] . "]IPN POST Vars Received By Paypal_IPN Response API:\n";
			foreach ( $this->ipn_data as $key => $value ) {
				$this->ipn_status .= "$key=$value \n";
			}
			// Log the response from the paypal server
			$this->ipn_status .= "IPN Response from Paypal Server:\n" . $this->ipn_response;
			$this->write_to_log ();
		}
		private function write_to_log() {
			if (! $this->ipn_log)
				return; // is logging turned off?
			// Write to log
			$fp = fopen ( LOG_FILE , 'a' );
			fwrite ( $fp, $this->ipn_status . "\n\n" );
			fclose ( $fp ); // close file
			chmod ( LOG_FILE , 0600 );
		}
		public function validate_ipn() {
			global $wpdb;
			$piereg = get_option( 'pie_register_2' );
			$hostname = gethostbyaddr ( $_SERVER ['REMOTE_ADDR'] );
			if (! preg_match ( '/paypal\.com$/', $hostname )) {
				$this->ipn_status = 'Validation post isn\'t from PayPal';
				$this->log_ipn_results ( false );
				return false;
			}
			
			if (isset($this->txn_id)&& in_array($_POST['txn_id'],$this->txn_id)) {
				$this->ipn_status = "txn_id have a duplicate";
				$this->log_ipn_results ( false );
				return false;
			}
			// parse the paypal URL
			$paypal_url = ($_POST['test_ipn'] == 1) ? SSL_SAND_URL : SSL_P_URL;
			$url_parsed = parse_url($paypal_url);        
			
			// generate the post string from the _POST vars aswell as load the
			// _POST vars into an arry so we can play with them from the calling
			// script.
			$post_string = '';
			  
			$this->postvars = $_POST;
			foreach ($_POST as $field=>$value) { 
				$this->ipn_data["$field"] = $value;
				$post_string .= $field.'='.urlencode(stripslashes($value)).'&'; 
			}
			$post_string.="cmd=_notify-validate"; // append ipn command
			
			// open the connection to paypal
			if ($piereg['paypal_sandbox'] == "yes")
				$fp = fsockopen ( 'ssl://www.sandbox.paypal.com', "443", $err_num, $err_str, 60 );
			else
				$fp = fsockopen ( 'ssl://www.paypal.com', "443", $err_num, $err_str, 60 );
	 
			if(!$fp) {
				// could not open the connection.  If loggin is on, the error message
				// will be in the log.
				$this->ipn_status = "fsockopen error no. $err_num: $err_str";
				$this->log_ipn_results(false);       
				return false;
			} else { 
				// Post the data back to paypal
				fputs($fp, "POST $url_parsed[path] HTTP/1.1\r\n"); 
				fputs($fp, "Host: $url_parsed[host]\r\n"); 
				fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n"); 
				fputs($fp, "Content-length: ".strlen($post_string)."\r\n"); 
				fputs($fp, "Connection: close\r\n\r\n"); 
				fputs($fp, $post_string . "\r\n\r\n"); 
			
				// loop through the response from the server and append to variable
				while(!feof($fp)) { 
				$this->ipn_response .= fgets($fp, 1024); 
			   } 
			  fclose($fp); // close connection
			}
			
			// Invalid IPN transaction.  Check the $ipn_status and log for details.
			if (!eregi("VERIFIED",$this->ipn_response)) {
				return false;
			} else {
				$this->ipn_status = "IPN VERIFIED";
				//////////// Verify User /////////////
				//if( isset($_REQUEST['paypal']) && !empty($_REQUEST['paypal']) )
				$this->processPostPayment();
				//////////////////////////////////////
				$this->log_ipn_results(true); 
				return true;
			}
		} 
		function ValidPUser(){
			global $wpdb;
			$piereg = get_option( 'pie_register_2' );
			
			if(isset($_POST['txn_id']) && $_POST['txn_id']){
				//We have a IPN to Validate
				$this->validate_ipn();
			}
			if(isset($_GET['action']) && $_GET['action'] == 'payment_success'){
				$this->payment_success_cancel_after_register("payment=success");
			}elseif(isset($_GET['action']) && $_GET['action'] == 'payment_cancel'){		   
				/******************************************************/
				$user_id 		= intval(base64_decode($_GET['paypal']));
				$user_data		= get_userdata($user_id);
				if(is_object($user_data)){
					$form 			= new Registration_form();
					$option 		= get_option( 'pie_register_2' );
					$subject 		= html_entity_decode($option['user_subject_email_payment_faild'],ENT_COMPAT,"UTF-8");
					$message_temp = "";
					if($option['user_formate_email_payment_faild'] == "0"){
						$message_temp	= nl2br(strip_tags($option['user_message_email_payment_faild']));
					}else{
						$message_temp	= $option['user_message_email_payment_faild'];
					}
					$message		= $form->filterEmail($message_temp,$user_data, "" );
					$from_name		= $option['user_from_name_payment_faild'];
					$from_email		= $option['user_from_email_payment_faild'];
					$reply_email 	= $option['user_to_email_payment_faild'];
					//Headers
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
					if(!empty($from_email) && filter_var($from_email,FILTER_VALIDATE_EMAIL))//Validating From
						$headers .= "From: ".$from_name." <".$from_email."> \r\n";
					if($reply_email){
						$headers .= "Reply-To: {$reply_email}\r\n";
						$headers .= "Return-Path: {$from_name}\r\n";
					}else{
						$headers .= "Reply-To: {$from_email}\r\n";
						$headers .= "Return-Path: {$from_email}\r\n";
					}
					wp_mail($user_data->user_email, $subject, $message , $headers);
					unset($user_data);
				}
				/******************************************************/
				$this->payment_success_cancel_after_register("payment=cancel");
			}else{
				return false;
			}
		}
		
		function processPostPayment()
		{
			add_filter( 'wp_mail_content_type', array($this,'set_html_content_type' ));
			$return_data = explode("|",$_POST['custom']);
			$hash 		= $return_data[0];
			$user_id 	= $return_data[1];
			if(!is_numeric($user_id ))
				return false;
			
			$check_hash = get_usermeta( $user_id, "hash");
			if($check_hash != $hash)
				return false;
			
			$user 		= new WP_User($user_id);
			$option 	= get_option('pie_register_2');
			update_user_meta( $user_id, 'active',1);
			//Sending E-Mail to newly active user
			$subject 		= html_entity_decode($option['user_subject_email_payment_success'],ENT_COMPAT,"UTF-8");
			$user_email 	= $user->user_email;
			$message_temp = "";
			if($option['user_formate_email_payment_success'] == "0"){
				$message_temp	= nl2br(strip_tags($option['user_message_email_payment_success']));
			}else{
				$message_temp	= $option['user_message_email_payment_success'];
			}
			$message		= $this->filterEmail($message_temp,$user,$pass);
			$from_name		= $option['user_from_name_payment_success'];
			$from_email		= $option['user_from_email_payment_success'];			
			$reply_email	= $option['user_to_email_payment_success'];
			//Headers
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
			if(!empty($from_email) && filter_var($from_email,FILTER_VALIDATE_EMAIL))//Validating From
				$headers .= "From: ".$from_name." <".$from_email."> \r\n";	
			if($reply_email){
				$headers .= "Reply-To: {$reply_email}\r\n";
				$headers .= "Return-Path: {$from_name}\r\n";
			}else{
				$headers .= "Reply-To: {$from_email}\r\n";
				$headers .= "Return-Path: {$from_email}\r\n";
			}
			wp_mail($user_email, $subject, $message , $headers);
		}
		function set_html_content_type() 
		{
			return 'text/html';
		}
		function deleteUsers($user_id = 0,$user_email = "",$user_registered = "")
		{
			
			
			$register_type = get_user_meta($user_id,"register_type");
			
			$option 		= get_option( 'pie_register_2' );
			$grace			= ((int)$option['grace_period']);
			
			if(($grace != 0 and $user_id != 0) and ($user_email != "" and $user_registered != "") and $register_type[0] != "admin_verify")
			{
				$date			= date("Y-m-d 00:00:00",strtotime("-{$grace} days"));
	
				if($user_registered < $date)
				{
					global $errors,$wpdb;
					$errors = new WP_Error();
					$errors->add("Login-error",apply_filters("piereg_your_account_has_no_longer_exist",__("Your account has no longer exist.","piereg")));
					global $wpdb;
					$user_table = $wpdb->prefix."users";
					$user_meta_table = $wpdb->prefix."usermeta";
					$wpdb->query("DELETE FROM `".$user_meta_table."` WHERE `user_id` = '".$user_id."'");
					$wpdb->query("DELETE FROM `".$user_table."` WHERE `ID` = '".$user_id."'");
					wp_logout();
					return true;
					
				}
			}
			return false;
		}
		function unique_user()
		{
			$username 	= $_REQUEST['fieldValue'];		
			$validateId	= $_REQUEST['fieldId'];
			
			$arrayToJs = array();
			$arrayToJs[0] = $validateId;
			if(!username_exists($username ))
			{		// validate??
					$arrayToJs[1] = true;			// RETURN TRUE
					echo json_encode($arrayToJs);			// RETURN ARRAY WITH success
			}
			else
			{
				for($x=0;$x<1000000;$x++)
				{
					if($x == 990000)
					{
						$arrayToJs[1] = false;
						echo json_encode($arrayToJs);		// RETURN ARRAY WITH ERROR
					}
				}				
			}
			die();
		}
		function showForm()
		{
			$this->piereg_ssl_template_redirect();
			global $errors;
			$error = "";
			$option 		= get_option( 'pie_register_2' );	
			if(is_user_logged_in() && $option['redirect_user']==1 )
			{
				$this->afterLoginPage();
				return "";	
			}		
			else
			{
					
				add_filter( 'wp_mail_content_type', array($this,'set_html_content_type' ));
				//ob_start();
				$output = '';
				if(isset($_POST['success']) && $_POST['success'] != "")
					$output .= '<p class="piereg_message">'.apply_filters('piereg_messages',__($_POST['success'],"piereg")).'</p>';
				if(isset($_POST['error']) && $_POST['error'] != "")
					$output .= '<p class="piereg_login_error">'.apply_filters('piereg_messages',__($_POST['error'],"piereg")).'</p>';
				
				if(isset($_POST['registration_success']) && $_POST['registration_success'] != "")
					$output .= '<p class="piereg_message">'.apply_filters('piereg_messages',__($_POST['registration_success'],"piereg")).'</p>';
				if(isset($_POST['registration_error']) && $_POST['registration_error'] != "")
					$output .= '<p class="piereg_login_error">'.apply_filters('piereg_messages',__($_POST['registration_error'],"piereg")).'</p>';
				
				if(isset($errors->errors) && sizeof($errors->errors) > 0)
				{
					foreach($errors->errors as $key=>$err)
					{
						if($key != "login-error")
							$error .= $err[0] . "<br />";	
					}
					if(!empty($error))
						$output .= '<p class="piereg_login_error">'.apply_filters('piereg_messages',__($error,"piereg")).'</p>';
				}
				
				include_once("register_form.php");
				$output .= outputRegForm();
				return $output;
			}
			
		}
		function showLoginForm()
		{
			$this->piereg_ssl_template_redirect();
			global $errors,$pagenow;
			$option = get_option( 'pie_register_2' );
			if(is_user_logged_in() && $option['redirect_user']==1 )
			{
				$this->afterLoginPage();
				return "";	
				
			}
			else
			{
				include_once("login_form.php");
				$output = pieOutputLoginForm();
				return  $output;
			}
		}
		function showForgotPasswordForm()
		{
			$this->piereg_ssl_template_redirect();
			global $errors;
			$option 		= get_option( 'pie_register_2' );	
			if(is_user_logged_in() && $option['redirect_user']==1 )
			{
				$this->afterLoginPage();
				return "";	
			}	
			
			else
			{
				include_once("forgot_password.php");
				$output =  pieResetFormOutput();
				return $output;
			}
				
		}	
		function showProfile()
		{
			$this->piereg_ssl_template_redirect();
			global $current_user,$pie_success,$pie_error,$pie_error_msg,$pie_suucess_msg;
			if ( is_user_logged_in() ){
				
				get_currentuserinfo();
				
				if(isset($_GET['edit_user']) && $_GET['edit_user'] == "1"){
					
					$form 		= new Edit_form($current_user);
					if(isset($_POST['pie_submit_update'])  )			
					{
						$form->error = "";
						$errors = new WP_Error();
						$errors = $form->validateRegistration($errors);	
						
						if(sizeof($errors->errors) > 0)
						{
							foreach($errors->errors as $err)
							{
								$form->error .= $err[0] . "<br />";	
							}		  	
						}	
						else
						{
							 $user_data = array('ID' => $current_user->ID);
							 if(isset($_POST['url']))
							 {
								$user_data["user_url"] =  $_POST['url'];
								$form->pie_success = 1;
							 }
							 if($current_user->data->email != $_POST['e_mail'])
							 {
								 $user_data["user_email"] =  $_POST['e_mail'];
								 $form->pie_success = 1;
							 }
							 /*
							 	*	Modefy since 2.0.15
							 */
							 if(wp_check_password( $_POST['old_password'], $current_user->data->user_pass, $current_user->ID ) && !empty($_POST['password']) && $_POST['password'] == $_POST['confirm_password'])
							 {
								$user_data["user_pass"] =  $_POST['password'];
								$form->pie_success = 1;
							 }
							 $id = wp_update_user( $user_data );						
							 $form->UpdateUser();
						}
								
					}
					$output = '';
					if($form->pie_success)
						$output .= '<div class="alert alert-success"><p class="piereg_message">'.$form->pie_success_msg.'</p></div>';
					elseif($form->error != "")
						$output .= '<div class="alert alert-danger"><p class="piereg_login_error">'.$form->error.'</p></div>';	
					if(isset($_POST['success']) && $_POST['success'] != "")
						$output .= '<p class="piereg_message">'.apply_filters('piereg_messages',__($_POST['success'],"piereg")).'</p>';
					if(isset($_POST['error']) && $_POST['error'] != "")
						$output .= '<p class="piereg_login_error">'.apply_filters('piereg_messages',__($_POST['error'],"piereg")).'</p>';
						
					if( isset($_GET['pr_msg'], $_GET['type']) && !empty($_GET['pr_msg']) && $_GET['type'] == "success" )
						$output .= '<p class="piereg_message">'.apply_filters('piereg_messages',base64_decode($_GET['pr_msg'])).'</p>';
					elseif( isset($_GET['pr_msg'], $_GET['type']) && !empty($_GET['pr_msg']) && $_GET['type'] == "error" )
						$output .= '<p class="piereg_login_error">'.apply_filters('piereg_messages',base64_decode($_GET['pr_msg'])).'</p>';
					
						
					require_once($this->plugin_dir."/edit_form.php");
					$output.= edit_userdata();
					return $output;
				}
				else
				{
					$profile_front = new Profile_front($current_user);		
					$profile_form_data = $profile_front->print_user_profile();
					return $profile_form_data;
				}
			}
			else
			{
				return __('Please','piereg').' <a href="'.wp_login_url($this->get_current_permalink()).'">'. __('login','piereg').'</a> '.__('to see your profile','piereg');
			}	
		}
		function show_renew_account()
		{
			$this->piereg_ssl_template_redirect();
		}
		function afterLoginPage()
		{
			$option = get_option("pie_register_2");
			if(isset($_GET['redirect_to']) and $_GET['redirect_to'] != ""){
				wp_redirect($_GET['redirect_to']);
			}elseif($option['after_login'] > 0)
			{
				wp_safe_redirect(get_permalink($option['after_login']));
			}else{
				wp_redirect(site_url());
			}
			exit;
		}
		function afterLoginPage_admin_init(){ 
			$option = get_option("pie_register_2");
			if(isset($_GET['redirect_to']) and $_GET['redirect_to'] != ""){
				wp_redirect($_GET['redirect_to']);
			}elseif($option['after_login'] > 0)
			{
				wp_safe_redirect(get_permalink($option['after_login']));
			}/*else{
				wp_redirect(site_url());
			}*/
			exit;
		}
		function add_ob_start()
		{
			ob_start();
		}
		
		function flush_ob_end()
		{
			 ob_clean();
		}
		
		function payment_validation_paypal()
		{
			global $errors, $wp_session;
			add_filter('wp_mail_content_type', array($this,'set_html_content_type'));
			
			$form 		= new Registration_form();
			$errors 	= $form->validateRegistration($errors);
			$option 	= get_option( 'pie_register_2' );	
			
			update_user_meta( $user_id, 'active', 0);
			$hash = md5( time() );
			update_user_meta( $user_id, 'hash', $hash );
			
			
			$subject 		= html_entity_decode($option['user_subject_email_pending_payment'],ENT_COMPAT,"UTF-8");
			
			$message_temp = "";
			if($option['user_formate_email_pending_payment'] == "0"){
				$message_temp	= nl2br(strip_tags($option['user_message_email_pending_payment']));
			}else{
				$message_temp	= $option['user_message_email_pending_payment'];
			}
			$message		= $form->filterEmail($message_temp,$user, $pass );
			$from_name		= $option['user_from_name_pending_payment'];
			$from_email		= $option['user_from_email_pending_payment'];					
			$reply_email	= $option['user_to_email_pending_payment'];	
					
			//Headers
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		
			if(!empty($from_email) && filter_var($from_email,FILTER_VALIDATE_EMAIL))//Validating From
			$headers .= "From: ".$from_name." <".$from_email."> \r\n";
			if($reply_email){
				$headers .= "Reply-To: {$reply_email}\r\n";
				$headers .= "Return-Path: {$from_name}\r\n";
			}else{
				$headers .= "Reply-To: {$from_email}\r\n";
				$headers .= "Return-Path: {$from_email}\r\n";
			}
						
			wp_mail($_POST['e_mail'], $subject, $message , $headers);
			
			update_user_meta( $user_id, 'register_type', "payment_verify");
			
			if($option['paypal_sandbox']=="no")
			{
				echo '<form id="paypal_form" action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="'.$option['paypal_butt_id'].'">
				<input name="custom" type="hidden" value="'.$hash.'|'.$user_id.'">
				</form>';	
			}
			else
			{
				echo '<form  id="paypal_form" action="https://sandbox.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="'.$option['paypal_butt_id'].'">
				<input name="custom" type="hidden" value="'.$hash.'|'.$user_id.'">
				</form>';
			}	
			echo '<script type="text/javascript">document.getElementById("paypal_form").submit();</script>';
			echo 'document.getElementById("paypal_form").submit();';
			die();
		}
		
		
		function Add_payment_option() // Only For Paypal
		{
			$check_payment = get_option("pie_register_2");
			if($check_payment["enable_2checkout"] == 1 && !(empty($check_payment['piereg_2checkout_api_id'])) )
			{
				echo '<option value="paypal" data-img="https://www.paypalobjects.com/en_US/i/logo/paypal_logo.gif">'.__("Paypal (one time subscription)","piereg").'</option>';
			}
		}
		function add_payment_method_script() // Only For Paypal
		{
			$check_payment = get_option("pie_register_2");
			if($check_payment["enable_paypal"] == 1 && !(empty($check_payment['paypal_butt_id'])) )
			{
				//Add jQuery for payment Method
				?>
					if(jQuery(this).val() == "paypal")
					{
						payment = 'You are Select paypal payment method.';
						image = '<img src="'+jQuery('option:selected',jQuery(this)).attr('data-img')+'" style="max-width: 150px;padding-top: 20px;" />';
					}
				<?php 
			}
		}
		function add_select_payment_script()
		{
			?> 
			<script type="text/javascript">
				var piereg = jQuery.noConflict();
				piereg(document).ready(function(){
					piereg("#select_payment_method").change(function(){
						if(piereg(this).val() != "")
						{
							var payment = "", image = "";
							<?php do_action('add_payment_method_script'); ?>
							piereg("#show_payment_method").html(payment);
							piereg("#show_payment_method_image").html(image);
						}
						else
						{
							piereg("#show_payment_method").html("");
							piereg("#show_payment_method_image").html("");
						}
					});
				});
			</script>
			<?php
		}
		function get_payment_content_area()
		{
			echo '<div id="show_payment_method_image"></div>';
			echo '<div id="show_payment_method"></div><br>';
		}
		function show_icon_payment_gateway() // for paypal
		{
			$button = get_option("pie_register_2");
			if(!(empty($button['paypal_butt_id'])) && $button['enable_paypal']==1)
			{
				?>
				  <div class="fields_options submit_field">
					<img style="width:100%;" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" />
				  </div>
				<?php
			}
		}
		function renew_account()
		{
			if(isset($_POST['select_payment_method']) and trim($_POST['select_payment_method']) != "")
			{
				$creds = array();
				$creds['user_login'] 	= $_POST['user_name'];
				$creds['user_password'] = $_POST['u_pass'];
				$creds['remember'] 		= isset($_POST['rememberme']);
				$user = wp_signon( $creds, false );
				if($user->ID != 0 or $user->ID != "")
				{
					do_action( 'wp_login', $user->user_login, $user );
					$user_meta = get_user_meta($user->ID);
					if($user_meta['active'][0] == 0)
					{
						if(isset($_POST['select_payment_method']) and $_POST['select_payment_method'] != "" )//Goto payment method Like check_payment_method_paypal
						{
							$_POST['user_id'] = $user->ID;
							$_POST['renew_account_msg'] = apply_filters("piereg_Renew_Account",__("Renew Account","piereg"));
							do_action("check_payment_method_".$_POST['select_payment_method']);
						}
					}
				}
				else
				{
					$_POST['error'] = apply_filters("piereg_Invalid_Username_or_Password",__("Invalid Username or Password","piereg"));
				}
			}
			else
			{
				$_POST['error'] = apply_filters("piereg_Please_Select_any_payment_method",__("Please Select any payment method","piereg"));
				wp_logout();
			}
		}
		
		function wp_mail_send($to_email = "",$key = "",$additional_msg = "",$msg = "")
		{
			global $errors;
			$errors = new WP_Error();
			if(trim($key) != "" and trim($to_email) != "" )
			{
				$email_types = get_option("pie_register_2");
				$message_temp = "";
				if($email_types['user_formate_email_'.$key] == "0"){
					$message_temp	= nl2br(strip_tags($email_types['user_message_email_'.$key]));
				}else{
					$message_temp	= $option['user_message_email_'.$key];
				}
				$message  		= $this->filterEmail( ($message_temp."<br />".$additional_msg) ,$to_email);
				$to				= $to_email;
				$from_name		= $email_types['user_from_name_'.$key];
				$from_email		= $email_types['user_from_email_'.$key];
				$reply_to_email	= $email_types['user_to_email_'.$key];
				$subject		= html_entity_decode($email_types['user_subject_email_'.$key],ENT_COMPAT,"UTF-8");
				if(!filter_var($to,FILTER_VALIDATE_EMAIL))//if not valid email address then use wordpress default admin
				{
					$to = get_option('admin_email');
				}
				//Headers
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
				if(!empty($from_email) && filter_var($from_email,FILTER_VALIDATE_EMAIL))//Validating From
					$headers .= "From: ".$from_name." <".$from_email."> \r\n";
				
				if(!empty($reply_to_email) && filter_var($reply_to_email,FILTER_VALIDATE_EMAIL))//Validating Reply To
					$headers .= 'Reply-To: <'.$reply_to_email.'> \r\n';
				if($reply_to_email){
					$headers .= "Return-Path: {$from_name}\r\n";
				}else{
					$headers .= "Return-Path: {$from_email}\r\n";
				}
				
				if(!wp_mail($to,$subject,$message,$headers))
				{
					$errors->add('check-error',apply_filters("piereg_problem_and_the_email_was_probably_not_sent",__("There was a problem and the email was probably not sent.",'piereg')));
				}
				else{
					if(trim($msg) != "")
					{
						$_POST['success'] = __($msg,"piereg");
					}
				}
			}
		}
		
		
		function add_custom_avatars($avatar="", $id_or_email="", $size="")
		{
			/*if(is_user_logged_in())
			  {
				$current_user = wp_get_current_user();
				
				$profile_pic_array = get_user_meta($current_user->ID);
				foreach($profile_pic_array as $key=>$val)
				{
					if(strpos($key,'profile_pic') !== false)
					{
						$profile_pic = trim($val[0]);
					}
				}
				
				if(!preg_match('/(http|https):\/\/(www\.)?[\w-_\.]+\.[a-zA-Z]+\/((([\w-_\/]+)\/)?[\w-_\.]+\.(png|gif|jpg|jpeg|xpng|bmp))/',$profile_pic)){
					$profile_pic = plugin_dir_url(__FILE__).'images/userImage.png';
				}
				
				if(trim($profile_pic) != "")
				{
				  return '<img src="'.$profile_pic.'" class="avatar photo" style="max-height:64px;max-width:64px;" width="'.$size.'" height="'.$size.'" alt="'.$current_user->display_name .'" />';
				}
			  }*/
			  
		}
		/*function delete_invitation_codes($ids="0")
		{
			global $wpdb;
			$prefix=$wpdb->prefix."pieregister_";
			$codetable=$prefix."code";
			$sql = "DELETE FROM `$codetable` WHERE `id` IN ( ".$ids." )";
			$wpdb->query($sql);
		}
		function active_or_unactive_invitation_codes($ids="0",$status="1")
		{
			global $wpdb;
			$prefix=$wpdb->prefix."pieregister_";
			$codetable=$prefix."code";
			$sql = "UPDATE `".$codetable."` SET `status`= ".$status." WHERE `id` IN (".$ids.")";
			$wpdb->query($sql);
		}*/
		function delete_invitation_codes($ids="0")
		{
			global $wpdb;
			$prefix=$wpdb->prefix."pieregister_";
			$codetable=$prefix."code";
			
			$array_ids 		= explode(',', $ids);
			$count_ids 		= count($array_ids);
			$placeholders 	= array_fill(0, $count_ids, '%d');
			$format 		= implode(', ', $placeholders);
			
			$sql = "DELETE FROM `$codetable` WHERE `id` IN($format)";
			$wpdb->query( $wpdb->prepare($sql, $array_ids) );
		}
		function active_or_unactive_invitation_codes($ids="0",$status="1")
		{
			global $wpdb;
			$prefix=$wpdb->prefix."pieregister_";
			$codetable=$prefix."code";
			
			$array_ids 		= explode(',', $ids);
			$count_ids 		= count($array_ids);
			$placeholders 	= array_fill(0, $count_ids, '%d');
			$format 		= implode(', ', $placeholders);
			
			$sql = "UPDATE `".$codetable."` SET `status`= %s WHERE `id` IN($format)";
			
			$args[] = $status;
			$args	= array_merge($args, $array_ids);
			
			$wpdb->query( $wpdb->prepare($sql, $args) );
		} 
		function pireg_update_invitation_code_cb_url()
		{
			global $wpdb;
			$prefix=$wpdb->prefix."pieregister_";
			$codetable=$prefix."code";
			$inv_code_id = intval($_POST['data']['id']);
			if( isset($_POST['data']))
			{
				if(trim($_POST['data']['id']) != "" and trim($_POST['data']['value']) != "" and trim($_POST['data']['type']) != "")
				{
					global $wpdb;
					$sql ="";
					if(trim($_POST['data']['type']) == "name")
					{
						$sql_res_sel = $wpdb->get_var( $wpdb->prepare( "SELECT `name` FROM `{$codetable}` WHERE `name` = %s", $_POST['data']['value']) );
						if(!$sql_res_sel)
							$sql = "UPDATE `{$codetable}` SET `name`='".esc_sql($_POST['data']['value'])."' WHERE `id` = '{$inv_code_id}'";
						else{
							echo "duplicate";
							die();
						}
					}
					else if(trim($_POST['data']['type']) == "code_usage")
					{
						$sql = "UPDATE `{$codetable}` SET `code_usage`='".esc_sql($_POST['data']['value'])."' WHERE `id` = ".((int)$_POST['data']['id'])."";
					}
					$result = $wpdb->query($sql);
					if($result)
					{
						echo "done";
					}
					else{
						echo "error";
					}
				}
			}
			die();
		}
		
		function piereg_ProgressBarScripts($countPageBreaks)
		{
			$output = '
				<script type="text/javascript">
				  piereg(document).ready(function() {
					 piereg( ".piereg_progressbar" ).progressbar({
					  value:  1 /'.$countPageBreaks.' * 100
					});  
				});
			</script>
			';
			return $output;
		}
		function checkUserAllowedPassReset($val,$userid){
			if(!$userid) return false;
			//Check if the user is active or not
			//if active true, or false
			global $piereg_global_options;
			
			if(
			   (isset($piereg_global_options['verification']) && ($piereg_global_options['verification'] == "2" || $piereg_global_options['verification'] == "1")) ||
			   ((!empty($piereg_global_options['paypal_butt_id'])) && $piereg_global_options['enable_paypal'] == "1" )
			   ){
				$user_active_status = get_user_meta($userid,"active",true);
				//If employee register from wp-register the active meta is not saved
				if($user_active_status == "")
					return true;
				//If employee register from wp-register the active meta is not saved
				return (($user_active_status == 1)?true:false);
			}
			return true;
		}
		function piereg_password_reset_not_allowed_text_function($text)
		{
			return $text;
		}
		function piereg_plugin_row_meta( $links, $file ) {
			if ( $file == PIEREG_PLUGIN_BASENAME ) {
				$row_meta = array(
					'docs'		=>	'<a href="' . esc_url( apply_filters( 'pieregister_docs_url', 'http://pieregister.genetechsolutions.com/documentation/' ) ) . '" title="' . esc_attr( __( 'View Pie-Register Documentation', 'piereg' ) ) . '" target="_blank">' . __( 'Docs', 'piereg' ) . '</a>',
					'support'	=>	'<a href="' . esc_url( apply_filters( 'pieregister_support_url', 'http://pieregister.genetechsolutions.com/forum/' ) ) . '" title="' . esc_attr( __( 'Visit Customer Support Forum', 'piereg' ) ) . '" target="_blank">' . __( 'Support', 'piereg' ) . '</a>',
				);
	
				return array_merge( $links, $row_meta );
			}
	
			return (array) $links;
		}
		function piereg_ssl_template_redirect(){
			if ( (defined("FORCE_SSL_ADMIN") && FORCE_SSL_ADMIN == true) || is_ssl() ) {
				if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') {
					wp_redirect( preg_replace('|^http://|', 'https://', $this->get_current_permalink()) );
					exit;
				}
			}
		}
		function edit_email_verification(){
			if(isset($_GET['action']) && ($_GET['action'] == "current_email_verify" || $_GET['action'] == "email_edit")){
				$username 			= esc_sql($_GET['login']);
				$email_verify_key 	= $_GET['key'];
				$user_data_temp 	= get_user_by('login', $username);
				global $errors, $piereg_global_options;
				$errors = new WP_Error();
				$global_options = $piereg_global_options;
				$success_message = "";
				$type = "success";
				if( ($_GET['action'] == "current_email_verify" ) && (isset($_GET['key']) && !empty($_GET['key']) ) && (isset($_GET['login']) && !empty($_GET['login']) ))
				{
					$email_verify_orignal_key = get_user_meta($user_data_temp->data->ID,"new_email_address_hashed",true);
					
					if($email_verify_orignal_key == $email_verify_key)
					{
						$new_email_address = get_user_meta($user_data_temp->data->ID,"new_email_address",true);
						$email_key = md5(uniqid("piereg_").time());
						$keys_array = array("reset_email_key"=>$email_key);
						
						/*
							*	Email send snipt
						*/
						$subject		= html_entity_decode($global_options["user_subject_email_email_edit_verification"],ENT_COMPAT,"UTF-8");
						$message_temp 	= "";
						if($global_options["user_formate_email_{$email_slug}"] == "0"){
							$message_temp	= nl2br(strip_tags($global_options["user_message_email_email_edit_verification"]));
						}else{
							$message_temp	= $global_options["user_message_email_email_edit_verification"];
						}
						
						$message		= $this->filterEmail($message_temp,$user_data_temp->data, "",false,$keys_array );
						$from_name		= $global_options["user_from_name_email_edit_verification"];
						$from_email		= $global_options["user_from_email_email_edit_verification"];					
						$reply_email 	= $global_options["user_to_email_email_edit_verification"];
						
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
						if(!wp_mail($new_email_address, $subject, $message , $headers))
						{
							$print_message = __("There was a problem and the email was probably not sent.",'piereg');
							$type = "0";
						}
						
						/*
							*	Update Email Hash Key
						*/
						update_user_meta($user_data_temp->data->ID,"new_email_address_hashed",$email_key);
						if(empty($print_message)){
							$print_message = __("Please follow the link sent to your new Email to verify and make the change applied!","piereg");
							$type="success";
						}
					}else{
						$print_message = __('Invalid request. This key is no longer exists','piereg' );
						$type = "error";
					}
				}
				elseif( ($_GET['action'] == "email_edit" ) && (isset($_GET['key']) && !empty($_GET['key']) ) && (isset($_GET['login']) && !empty($_GET['login']) ))
				{
					$email_verify_orignal_key = get_user_meta($user_data_temp->data->ID,"new_email_address_hashed",true);
					$old_email = get_user_meta($user_data_temp->data->ID,"new_email_address",true);
					if($email_verify_orignal_key == $email_verify_key)
					{
						$user_id_temp = wp_update_user( array( 'ID' => $user_data_temp->data->ID, 'user_email' => $old_email ) );
						if ( is_wp_error( $user_id_temp ) ) {
							$print_message = __('There is a problem updating your new Email Address, Pleasde try again!','piereg' );
							$type = "error";
						}
						else{
							delete_user_meta($user_data_temp->data->ID,"new_email_address_hashed");
							delete_user_meta($user_data_temp->data->ID,"new_email_address");
							$print_message = __("You have successfully verified your new Email address. Your Account Email has been changed","piereg");
							$type = "success";
						}
					}
					else{
						$print_message = __('Invalid request. This key is no longer exists','piereg' );
						$type = "error";
					}
				}
				
				if( is_user_logged_in() ){
					$print_message = base64_encode($print_message);
					wp_safe_redirect($this->get_page_permalink_by_id($global_options["alternate_profilepage"],"edit_user=1&pr_msg={$print_message}&type={$type}"));
					exit;
				}else{
					$_POST[$type] = $print_message;
				}
			}
		}
	}
}
if( class_exists('PieRegister') ){
	$pie_register = new PieRegister();
	if(isset($pie_register)){
		register_activation_hook( __FILE__, array(  &$pie_register, 'install_settings' ) );
		register_deactivation_hook( __FILE__, array(  &$pie_register, 'uninstall_settings' ) );
		
		function pie_registration_url($url=false)
		{
			return PieRegister::static_pie_registration_url($url);
		}
		function pie_login_url($url=false)
		{
			return PieRegister::static_pie_login_url($url);
		}
		function pie_lostpassword_url($url=false)
		{
			return PieRegister::static_pie_lostpassword_url($url);
		}
		function piereg_logout_url($url=false)
		{
			return PieRegister::static_piereg_logout_url($url);
		}
		function pie_modify_custom_url($url,$query_string=false){
			return PieRegister::static_pie_modify_custom_url($url,$query_string);
		}
	}
register_uninstall_hook( __FILE__, array(  "PieRegister" , 'piereg_remove_all_settings' ) );
}