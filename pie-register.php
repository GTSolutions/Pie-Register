<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
/* 
Plugin Name: Pie Register (Base)
Plugin URI: http://pieregister.com/
Description: <strong>WordPress 3.5 + ONLY.</strong> Enhance your Registration form, Custom logo, Password field, Invitation codes, Paypal, Captcha validation, Email verification and more.
Author: Genetech Solutions
Version: 3.0
Author URI: http://www.genetechsolutions.com/
*/

if ( ! defined( 'ABSPATH' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;	
}

/*
	* Get Pie Register Dir Name
*/

$piereg_dir_path = dirname(__FILE__);
define('PIEREG_DIR_NAME',$piereg_dir_path);

/*
	* Include PR Files
*/

//if( file_exists(PIEREG_DIR_NAME.'/dash_widget.php') ) 			require_once(PIEREG_DIR_NAME.'/dash_widget.php');
//if( file_exists(PIEREG_DIR_NAME.'/dash_widget_stats.php') ) 		require_once(PIEREG_DIR_NAME.'/dash_widget_stats.php');
if( file_exists(PIEREG_DIR_NAME.'/classes/base.php') ) 				require_once(PIEREG_DIR_NAME.'/classes/base.php');
if( file_exists(PIEREG_DIR_NAME.'/classes/profile_admin.php') ) 	require_once(PIEREG_DIR_NAME.'/classes/profile_admin.php');
if( file_exists(PIEREG_DIR_NAME.'/classes/profile_front.php') ) 	require_once(PIEREG_DIR_NAME.'/classes/profile_front.php');
if( file_exists(PIEREG_DIR_NAME.'/classes/registration_form.php') ) require_once(PIEREG_DIR_NAME.'/classes/registration_form.php');
if( file_exists(PIEREG_DIR_NAME.'/classes/edit_form.php') )			require_once(PIEREG_DIR_NAME.'/classes/edit_form.php');
if( file_exists(PIEREG_DIR_NAME.'/widget.php') )					require_once(PIEREG_DIR_NAME.'/widget.php');
if( file_exists(PIEREG_DIR_NAME.'/piereg_walker-nav-menu.php') ) 	require_once(PIEREG_DIR_NAME.'/piereg_walker-nav-menu.php');


/*
	Move PR DB Version Name to PieRegisterBaseVariables
*/

if(!defined("LOG_FILE"))
	define('LOG_FILE', PIEREG_DIR_NAME.'/ipn_results.log');
	
if(!defined("SSL_P_URL"))
	define('SSL_P_URL', 'https://www.paypal.com/cgi-bin/webscr');
	
if(!defined("SSL_SAND_URL"))
	define('SSL_SAND_URL','https://www.sandbox.paypal.com/cgi-bin/webscr');

if (!function_exists("pr_licenseKey_errors")) {
	function pr_licenseKey_errors() {
		do_action("pr_licenseKey_errors");
	}
}

global $pie_success,$pie_error,$pie_error_msg,$pie_suucess_msg, $pagenow,$action,$profile,$errors,$piereg_math_captcha_register,$piereg_math_captcha_register_widget,$piereg_math_captcha_login,$piereg_math_captcha_login_widget,$piereg_math_captcha_forgot_pass,$piereg_math_captcha_forgot_pass_widget;

$piereg_math_captcha_register = false;
$piereg_math_captcha_register_widget = false;
$piereg_math_captcha_login = false;
$piereg_math_captcha_login_widget = false;
$piereg_math_captcha_forgot_pass = false;
$piereg_math_captcha_forgot_pass_widget = false;

if( !class_exists('PieRegister') ){
	class PieRegister extends PieReg_Base{
		var $pie_success,$pie_error,$pie_error_msg,$pie_success_msg,$txn_id,$ipn_log,$ipn_data = array(),$postvars,$pie_pr_dec_vars_array,$pie_pr_backend_dec_vars_array,$pie_ua_renew_account_url,$pie_is_social_renew_account_call,$pie_payment_methods_dat,$pie_after_login_page_redirect_url,$is_pr_preview;
		private $ipn_status,$ipn_debug,$post_block_content,$ipn_response,$set_all_users_data = array(),$piereg_jquery_enable = false;
		
		function __construct(){
			global $pagenow,$wp_version,$profile;
			//Export Database
			$this->piereg_export_db();
			/////
			$this->is_pr_preview = false;
			$this->pie_pr_dec_vars_array = false;
			$this->pie_pr_backend_dec_vars_array = false;
			$this->pie_ua_renew_account_url = false;
			$this->pie_is_social_renew_account_call = false;
			$this->pie_after_login_page_redirect_url = false;
			//Payment Log File Download & Delete
			$this->piereg_payment_log_file_action();
			///////////////////
			$this->pie_payment_methods_dat = apply_filters('add_select_payment_script',$this->pie_payment_methods_dat);
			///////////////////
			$this->ipn_status = '';
			$this->txn_id = null;
			$this->ipn_log = true;
			$this->ipn_response = '';
			$this->ipn_debug = false;
			//self::$pieinstance = $this;
			/***********************/
			parent::__construct();
			$this->pieActions();
			$this->pieFilters();
			$errors = new WP_Error();
						
			/*
				*	API Classes
			*/
			if(is_admin()){
				// Performs activations and deactivations of API License Keys
				if( file_exists(PIEREG_DIR_NAME.'/classes/api/class-wc-key-api.php') )
					require_once(PIEREG_DIR_NAME.'/classes/api/class-wc-key-api.php');
				// Checks for software updatess
				if( file_exists(PIEREG_DIR_NAME.'/classes/api/class-wc-plugin-update.php') )
					require_once(PIEREG_DIR_NAME.'/classes/api/class-wc-plugin-update.php');
				// Admin menu with the license key and license email form
				if( file_exists(plugin_dir_path( __FILE__ ) . 'classes/api/class-wc-api-manager-menu.php') )
					require_once( plugin_dir_path( __FILE__ ) . 'classes/api/class-wc-api-manager-menu.php' );
				// Load update class to update $this plugin
				$this->load_plugin_self_updater();
			}
						
			//Download or View Log file
			$this->pr_logfile_download_or_view();
		}
		function pie_main(){
			global $piereg_global_options, $pagenow;
			$option = $piereg_global_options;
			//$this->check_upgrade_remote(); # when we go live
			//LOCALIZATION
			#Place your language file in the plugin folder and name it "piereg-{language}.mo"
			#replace {language} with your language value from wp-config.php
			#load_textdomain( 'piereg', ABS_PATH_TO_MO_FILE ); // OK
			load_plugin_textdomain( 'piereg', false, dirname(plugin_basename(__FILE__)) . '/lang/');
			
			$pie_plugin_db_version = get_option('piereg_plugin_db_version');
			if($pie_plugin_db_version != PIEREG_DB_VERSION){
				$this->install_settings();
			}
			//////////////
			$this->is_pr_preview = (isset($_GET['pr_preview']))?true:false;
			//////////////
			
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
			
			// check to prevent php "notice: undefined index" msg
			$theaction = '';	
			if(isset($_GET['action'])) 
				$theaction = $_GET['action']; 
			
			if((isset($_GET['show_dash_widget']) && $_GET['show_dash_widget']==1) and (isset($_GET['invitaion_code']) && $_GET['invitaion_code']!="")){
				$this->show_invitaion_code_user();
			}
			
			//PAYPAL VALIDATION
			$this->ValidPUser();
			
			#Save Settings
			if( isset($_POST['action']) && $_POST['action'] == 'pie_reg_update' )
			{$this->SaveSettings();}
			
			if( isset($_POST['action']) && $_POST['action'] == 'pie_reg_settings' ) {
				$this->PieRegSettingsProcess();
			}
			
			
			#Reset Settings to default
			if( isset($_POST['piereg_default_settings']) )
			{
				$this->piereg_default_settings();
			}
			
			#Admin Verify Users
			if( isset($_POST['verifyit']) )		
				$this->verifyUsers();
				
			#Admin Send Payment Link
			if( isset($_POST['paymentl']) )
				$this->PaymentLink();
			
			#Admin Resend VerificatioN Email
			if( isset($_POST['emailverifyit']) )
				$this->AdminEmailValidate() ;		
				
			#Admin Delete Unverified User
			if( isset($_POST['vdeleteit']))			
				$this->AdminDeleteUnvalidated();	
		
			/*
				*	Add since 2.0.13
				*	Change email after verify
			*/
			$this->edit_email_verification();
			
			/* End */
		
		
			//Blocking wp admin for registered users
			
			if(
			   ($pagenow == 'wp-login.php' && $option['block_wp_login']==1) && 
			   ($option['alternate_login']  && $theaction != 'logout') && 
			   (!isset($_REQUEST['interim-login']))
			   ){	
				
				if ( force_ssl_admin()  && ! is_ssl() ) {
					$is_ssl = true;
				}else{
					$is_ssl = false;
				}
				
				if($theaction=="register"){
					if($is_ssl)
					 wp_redirect(preg_replace('|^http://|', 'https://', $this->get_redirect_url_pie(get_permalink($option['alternate_register']))));
					else
					 wp_redirect($this->get_redirect_url_pie(get_permalink($option['alternate_register'])));
					
				}
				else if($theaction=="lostpassword")
				{
					if($is_ssl)
					 wp_redirect(preg_replace('|^http://|', 'https://', $this->get_redirect_url_pie(get_permalink($option['alternate_forgotpass']))));
					else
					 wp_redirect($this->get_redirect_url_pie(get_permalink($option['alternate_forgotpass'])));						
				}
				else if($theaction=="")
				{
					if($is_ssl)
						wp_redirect(preg_replace('|^http://|', 'https://', $this->get_redirect_url_pie(get_permalink($option['alternate_login']))));
					else
						wp_redirect($this->get_redirect_url_pie(get_permalink($option['alternate_login'])));
				}
				
			}
			
			//Blocking access of users to default pages if redirect is on 
			
				if($theaction != 'logout' && $theaction != 'postpass' )
				{
					if((is_user_logged_in() && $pagenow == 'wp-login.php') && ($option['redirect_user']==1   && $theaction != 'logout'))
					{
						if(!isset($_REQUEST['interim-login'])){
							$this->afterLoginPage();
						}
					}
				}
				if(trim($pagenow) == "profile.php" && $option['block_WP_profile']==1 )
				{
					$current_user = wp_get_current_user();
					if(trim($current_user->roles[0]) == "subscriber")
					{
						//$profile_page = get_option("Profile_page_id");
						$profile_page = $option['alternate_profilepage'];
						if($profile_page > 0){
							wp_safe_redirect($this->get_redirect_url_pie(get_permalink($profile_page)));
							exit;
						}
					}
				}
			
			//Blocking wp admin for registered users
			
				if(isset($_POST['pie_submit'])){	
					$this->check_register_form();
				}
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
			
			if(isset($_GET['prfrmid']) and ((int)$_GET['prfrmid']) != 0 and isset($_GET['status']) and $_GET['status'] != "")
			{
				$fields_id = ((int)$_GET['prfrmid']);
				$pr_form_option = get_option("piereg_form_field_option_".$fields_id);
				$pr_form_option['Status'] = esc_sql( strtolower( trim( $_GET['status'] ) ) );
				update_option("piereg_form_field_option_".$fields_id,$pr_form_option);
				
				$_POST['notice'] = __("Successfully Change Status of Pie Register Registration Form","piereg");
			}	
			if( ( isset($_GET['prfrmid']) and ((int)$_GET['prfrmid']) != 0 and isset($_GET['action']) and $_GET['action'] == "delete" ) && $this->check_if_role_admin() )
			{

				// if users created from Form then disable it, delete if no submission. 
				$fields_id 			= ((int)$_GET['prfrmid']);
				$pr_all_form_info 	= $this->get_pr_forms_info();
				if(count($pr_all_form_info) > 1) {
					
					$fields_id 		= ((int)$_GET['prfrmid']);
					$optionsform	= get_option("piereg_form_field_option_".$fields_id);
					
					if( $optionsform['Entries'] > 0 ) {
						$optionsform['IsDeleted'] 	= 1;
						update_option("piereg_form_field_option_".$fields_id,$optionsform);
						
						// Assign new free form.
						$this->regFormForFreeVers(true);					
					} else {
						$this->delete_piereg_form();						
					}
				
				} else {
					$_POST['error_message'] = __("Can not delete last form","piereg");
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
				$opt = get_option(OPTION_PIE_REGISTER);
				$val = ((int)($_POST['invitation_code_per_page_items']) != 0)? ((int)$_POST['invitation_code_per_page_items']) : "10";
				$opt['invitaion_codes_pagination_number'] = esc_sql( $val );
				update_option(OPTION_PIE_REGISTER,$opt);
				$piereg_global_options = $opt;
				unset($opt);
			}
			
			if( $option['show_admin_bar'] == "1" )
			{
				$this->subscriber_show_admin_bar();// show/hide admin bar
			}
			
			if(isset($_POST['import_email_template_from_version_1']) and $_POST['old_version_emport'] == "yes")
			{
				$old_options = get_option("pie_register_2");
				$new_options = get_option(OPTION_PIE_REGISTER);
				$new_options['user_message_email_admin_verification'] = esc_sql( nl2br($old_options['adminvmsg']) );
				$new_options['user_message_email_email_verification'] = esc_sql( nl2br($old_options['emailvmsg']) );
				$new_options['user_message_email_default_template'] = esc_sql( nl2br($old_options['msg']) );
				update_option(OPTION_PIE_REGISTER,$new_options);
				global $piereg_global_options;
				$piereg_global_options = $new_options;
			}
			
				if($option['show_custom_logo'] == 1){
					if(trim($option['custom_logo_url']) != ""){
						add_action( 'login_enqueue_scripts', array($this,'piereg_login_logo'));
					}
					add_filter( 'login_headertitle',  array($this,'piereg_login_logo_url_title' ));
					add_filter( 'login_headerurl',  array($this,'piereg_login_logo_url' ));
				}
				add_action( 'wp_footer',  array($this,'print_in_footer' ));
			
			/*
				*	Activate license key
			*/
			$this->activate_license_key();
			$this->activate_addon_license_key();
		}
		function pieActions(){
			global $piereg_global_options, $pagenow;
			//Restrict Widgets
			if( $this->piereg_pro_is_activate )
				add_action('wp_head',	array(&$this, 'piereg_restrict_widgets'));
			add_action('wp_ajax_get_meta_by_field', array($this,'getMeta'));
			add_action('template_redirect', array($this,'pr_template_redirect') );
			add_action('wp_ajax_check_username',  array($this,'unique_user' ));
			add_action('wp_ajax_nopriv_check_username',  array($this,'unique_user' ));	
			
			add_action( 'admin_init', array($this,'piereg_register_scripts') );
			add_action( 'admin_init', array($this,'piereg_backendregister_scripts') );
			#Adding Menus
			add_action( 'admin_menu',  array($this,'AddPanel') );
			
			//Add paypal payment method
			add_action("check_payment_method_paypal", array($this, "check_payment_method_paypal"),10,1);
			
			
			//Adding "embed form" button      
			add_action('media_buttons_context', array($this, 'add_pie_form_button'));
			
			if(in_array(basename($_SERVER['PHP_SELF']), array('post.php', 'page.php', 'page-new.php', 'post-new.php'))){
				add_action('admin_footer',  array($this, 'add_pie_form_popup'));
			}
			/*
				*	User Login After registration
				*	add since 2.0.13
			*/
			add_action("pie_register_login_after_registration",array($this,"pie_register_login_after_registration"));
			//after_setup_theme
			add_action("after_setup_theme",array($this,"piereg_after_setup_theme"));
			
			#Genrate Warnings
			add_action('admin_notices', array($this, 'warnings'),20);
			
			add_action( 'init', array($this,'pie_main') );
						
			
			$profile = new Profile_admin();
			add_action('show_user_profile',array($profile,"edit_user_profile"));
			add_action('personal_options_update',array($profile,"updateMyProfile"));
			
			add_action('edit_user_profile',array($profile,"edit_user_profile"));
			add_action('edit_user_profile_update', array($profile,'updateProfile'));	
			
			add_action( 'widgets_init', array($this,'initPieWidget'));
			
			//add_action('get_header', array($this,'add_ob_start'));
			//It will redirect the User to the home page if the curren tpage is a alternate login page
			add_filter('get_header', array($this,'checkLoginPage'));
			
			add_action('payment_validation_paypal',	array($this, 'payment_validation_paypal'));
			
	
			add_action("add_select_payment_script",	 array($this,"add_select_payment_script"));
			add_filter("get_payment_content_area",	 array($this,"get_payment_content_area"));
			
			add_action("show_icon_payment_gateway",	array($this,"show_icon_payment_gateway"));
			
			add_action("pr_licenseKey_errors",array($this,"print_Rpr_licenseKey_errors"),30);
			
			add_filter("piereg_messages",array($this,"modify_all_notices"));
						
			/*update update_invitation_code form ajax*/
			add_action( 'wp_ajax_pireg_update_invitation_code', array($this,'pireg_update_invitation_code_cb_url' ));
			add_action( 'wp_ajax_nopriv_pireg_update_invitation_code', array($this,'pireg_update_invitation_code_cb_url' ));
			
			// FRONT END SCRIPTS
			add_action('wp_enqueue_scripts',array($this,'pie_frontend_enqueu_scripts'));
			add_action('admin_enqueue_scripts', array($this,'pie_backend_enqueue_scripts'));
			//User Deletion
			add_action( 'delete_user', array( $this, 'piereg_user_deletion' ) );
			#Adding Short Code Functionality
			add_shortcode( 'pie_register_login',  array($this,'showLoginForm') );
			add_shortcode( 'pie_register_profile', array($this,'showProfile') );
			add_shortcode( 'pie_register_forgot_password',  array($this,'showForgotPasswordForm') );
			add_shortcode( 'pie_register_renew_account',  array($this,'show_renew_account') );
			add_shortcode( 'pie_register_form',  array($this,'piereg_registration_form') );
			add_action("the_post",array($this,"piereg_template_restrict"));
			//Add Post Meta Box
			add_action('add_meta_boxes', array($this,'piereg_add_meta_box'));
			//Save Post Meta Box
			add_action('save_post', array($this,'piereg_save_meta_box_data'));
			//Validate User expiry period
			add_action("piereg_validate_user_expiry_period", array($this,"piereg_validate_user_expiry_period_func"),10,1);
			add_action( 'wp_footer', array($this,'print_multi_captcha_skin' ));
			
		}
		function pieFilters(){
			global $piereg_global_options;
			add_filter('add_select_payment_script',array($this,'add_payment_method_script'));
			//Add sub links in wp plugin's page
			add_filter( 'plugin_row_meta', array( $this, 'piereg_plugin_row_meta' ), 10, 2 );
			//plugin page links
			add_filter( 'plugin_action_links' , array($this,'add_action_links'),10,2 );
			add_filter("Add_payment_option_PaypalStandard", array($this,'Add_payment_option_PaypalStandard'),10,1);
			add_filter('allow_password_reset',array($this,'checkUserAllowedPassReset'),20,2);
			if($piereg_global_options['block_wp_login']){
				add_filter( 'login_url', array($this,'pie_login_url'),88888,1);
				add_filter( 'lostpassword_url', array($this,'pie_lostpassword_url'),88888,1);
				add_filter( 'register_url', array($this,'pie_registration_url'),88888,1);
				add_filter( 'logout_url', array($this,'piereg_logout_url'),88888,2);
			}
			add_filter( 'piereg_password_reset_not_allowed_text', array($this,'piereg_password_reset_not_allowed_text_function'),20,1);
		}
		//Function pr_template_redirect
		function pr_template_redirect(){
			global $piereg_global_options,$wp_query;
			
			if(is_user_logged_in()){
				if($piereg_global_options['redirect_user'] === 1
				&& ($wp_query->post->ID == $piereg_global_options['alternate_login']
				|| $wp_query->post->ID == $piereg_global_options['alternate_register']
				|| $wp_query->post->ID == $piereg_global_options['alternate_forgotpass']
				|| strpos($wp_query->post->post_content,'[pie_register_login') !== false
				|| strpos($wp_query->post->post_content,'[pie_register_forgot_password') !== false
				|| strpos($wp_query->post->post_content,'[pie_register_renew_account') !== false
				|| strpos($wp_query->post->post_content,'[pie_register_form') !== false) && false === $this->is_pr_preview):
				//Redirect Now
					$this->afterLoginPage();
				endif;
			}
		}
		/*
			*	When user deletion
		*/
		function piereg_user_deletion( $user_id ) {
			
			$subscribtion_method 	= get_user_meta( $user_id, "piereg_user_subscribtion_method", true );
			$subscribtion_id 		= get_user_meta( $user_id, "piereg_user_subscribtion_id", true );
			
			if( !empty($subscribtion_method) && !empty($subscribtion_id) )
			{
				do_action("piereg_delete_subscribtion_on_user_deletion_".$subscribtion_method ,$user_id);
			}
		}
		
		function pie_admin_menu_style_enqueu(){
			wp_register_style( 'pie_menu_style_css', plugins_url("/css/piereg_menu_style.css",__FILE__),false,'2.0', "all" );
			wp_enqueue_style( 'pie_menu_style_css' );
		} # noutusing
		function pieAllScripts(){
			array(
				'handle'	=> '',
				'src'		=> '',
				'dep'		=> '',
				'ver'		=> '',
				'footer'	=> false
			);
		}
		function piereg_register_scripts(){
			wp_register_script('pie_prVariablesDeclaration_script',plugins_url("/js/prVariablesDeclaration.js",__FILE__),false,false);
			wp_register_script('pie_prBackendVariablesDeclaration_script',plugins_url("/js/prBackendVariablesDeclaration.js",__FILE__),false,false);
			wp_register_script('pie_prVariablesDeclaration_script_Footer',plugins_url("/js/prVariablesDeclarationFooter.js",__FILE__),'','',true);
			wp_register_script('pie_prBackendVariablesDeclaration_script_Footer',plugins_url("/js/prBackendVariablesDeclarationFooter.js",__FILE__),'','',true);
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
			wp_register_script('pie_alphanum_js',plugins_url("/js/jquery.alphanum.js",__FILE__),'jquery','2.0',false);
			wp_register_script('pie_validation_js',plugins_url("/js/piereg_validation.js",__FILE__),'jquery','2.0',false);
			wp_register_script('pie_password_checker',plugins_url("/js/pie_password_checker.js",__FILE__),'jquery','2.0',false);
			wp_register_script('pie_recpatcha_script','//www.google.com/recaptcha/api.js?onload=prRecaptchaCallBack&render=explicit','','',true);
			
			/////////////////////////////////////////////////
			wp_register_style( 'pie_jqueryui_css', plugins_url("/css/jquery-ui.css",__FILE__),false,'2.0', "all" );
			wp_register_style( 'pie_front_css', plugins_url("/css/front.css",__FILE__),false,'2.0', "all" );
			wp_register_style( 'pie_mCustomScrollbar_css', plugins_url("/css/jquery.mCustomScrollbar.css",__FILE__),false,'2.0', "all" );
			wp_register_style( 'pie_style_css', plugins_url("/css/style.css",__FILE__),false,'2.0', "all" );
			wp_register_style( 'pie_tooltip_css', plugins_url("/css/tooltip.css",__FILE__),false,'2.0', "all" );
			wp_register_style( 'pie_validation_css', plugins_url("/css/validation.css",__FILE__),false,'2.0', "all" );
		}
		function piereg_backendregister_scripts(){
			//Styles
			//wp_register_style( 'pie_jqueryui_css', plugins_url("/css/jquery-ui.css",__FILE__),false,'2.0', "all" );
			wp_register_style( 'pie_admin_css', plugins_url("/css/admin.css",__FILE__),false,'1.0', "all" );
			wp_register_style( 'pie_restrict_widget_css', plugins_url("/restrict_widget/restrict_widget_css.css",__FILE__),false,'1.0', "all" );
			//Scripts
			//wp_register_script('jquery-ui', "https://code.jquery.com/ui/1.10.4/jquery-ui.js",'','2.0.0',false);
			wp_register_script('pie_ckeditor',plugins_url("/ckeditor/ckeditor.js",__FILE__),'jquery','1.0',false);
			wp_register_script('pie_restrict_widget_script',plugins_url("/restrict_widget/pie_register_widget_script.js",__FILE__),'jquery','1.0',false);
			
		}
		function getVauleOrEmpty($string=false){
			return (isset($string) && !empty($string))?$string:'';
		}
		function print_multi_lang_script_vars(){
			global $piereg_global_options;
			$opt = $piereg_global_options;
			$fields_id = get_option("piereg_form_fields_id");
			$count = 0;
			$form_ids = array();
			for($a=1;$a<=$fields_id;$a++){
				$option = get_option("piereg_form_field_option_".$a);
				if( !empty($option) && is_array($option) && isset($option['Id']) && trim($option['Status']) != "" && $option['Status'] == "enable" && (!isset($option['IsDeleted']) || trim($option['IsDeleted']) != 1) )
				{
					array_push($form_ids, 'reg_form_'.$option['Id']);
					$fields_data 	= maybe_unserialize(get_option("piereg_form_fields_".$option['Id']));
					$the_cap_field = "captcha";
					$fields_data_filtered = array_filter($fields_data, function($el) use ($the_cap_field) {
						return ( $el['type'] == $the_cap_field );
					});
					$reg_forms_theme['reg_form_'.$option['Id']] = $fields_data_filtered;
					$count++;
				}
			}
			$is_widgetTheme = $not_widgetTheme = (isset($opt['piereg_recapthca_skin_login']) && !empty($opt['piereg_recapthca_skin_login']))?$opt['piereg_recapthca_skin_login']:"red";
			$is_forgot_widgetTheme = $not_forgot_widgetTheme = (isset($opt['piereg_recapthca_skin_forgot_pass']) && !empty($opt['piereg_recapthca_skin_forgot_pass']))?$opt['piereg_recapthca_skin_forgot_pass']:"red";
			$mathCaptchaOperator = rand(0,1);
			////1 for add(+)
			////0 for subtract(-)
			$mathCaptchaResult = 0;
			if($mathCaptchaOperator == 1){	
				$mathCaptchaStart = rand(1,9);
				$mathCaptchaEnd = rand(5,20);
				$mathCaptchaResult = $mathCaptchaStart + $mathCaptchaEnd;
				$mathCaptchaOperator = "+";
			}
			else{
				$mathCaptchaStart = rand(50,30);
				$mathCaptchaEnd = rand(5,20);
				$mathCaptchaResult = $mathCaptchaStart - $mathCaptchaEnd;
				$mathCaptchaOperator = "-";
			}
				
			$mathCaptchaResult1 = $mathCaptchaResult + 12;
			$mathCaptchaResult2 = $mathCaptchaResult + 786;
			$mathCaptchaResult3 = $mathCaptchaResult - 5;
			$mathCaptchaResult1 = base64_encode($mathCaptchaResult1);
			$mathCaptchaResult2 = base64_encode($mathCaptchaResult2);
			$mathCaptchaResult3 = base64_encode($mathCaptchaResult3);
			$matchCapImage_name = rand(0,10);
			$matchCapColor = array('rgba(0, 0, 0, 0.6)','rgba(153, 31, 0, 0.9)','rgba(64, 171, 229,0.8)','rgba(0, 61, 21, 0.8)','rgba(0, 0, 204, 0.7)','rgba(0, 0, 0, 0.5)','rgba(198, 81, 209, 1.0)','rgba(0, 0, 999, 0.5)','rgba(0, 0, 0, 0.5)','rgba(0, 0, 0, 0.5)','rgba(255, 63, 143, 0.9)');
			
			$matchCapHTML = $mathCaptchaStart." ".$mathCaptchaOperator." ".$mathCaptchaEnd . " = ";
			
			if(!empty($this->pie_payment_methods_dat) && count($this->pie_payment_methods_dat) > 0){
				$pie_payment_methods_data = array();
				foreach($this->pie_payment_methods_dat as $data){
					$pie_payment_methods_data[$data['method']]['payment']	= $data['payment'];
					$pie_payment_methods_data[$data['method']]['image']		= $data['image'];
				}
			}
			$isSocialLoginRedirectOnLogin = ($opt['social_site_popup_setting'] == 1 && (isset($_POST['social_site']) && $_POST['social_site'] == "true"))?true:false;
			$this->pie_pr_dec_vars_array = array(
				'ajaxURL'						=> admin_url('admin-ajax.php'),
				'dateY'							=> date_i18n("Y"),//__( 'NiceString', 'piereg' )
				'piereg_startingDate'			=> $opt['piereg_startingDate'],
				'piereg_endingDate'				=> $opt['piereg_endingDate'],
				'pass_strength_indicator_label'	=> $this->getVauleOrEmpty($opt['pass_strength_indicator_label']),
				'pass_very_weak_label'			=> $this->getVauleOrEmpty($opt['pass_very_weak_label']),
				'pass_weak_label'				=> $this->getVauleOrEmpty($opt['pass_weak_label']),
				'pass_medium_label'				=> $this->getVauleOrEmpty($opt['pass_medium_label']),
				'pass_strong_label'				=> $this->getVauleOrEmpty($opt['pass_strong_label']),
				'pass_mismatch_label'			=> $this->getVauleOrEmpty($opt['pass_mismatch_label']),
				'ValidationMsgText1'					=> __("none","piereg"),
				'ValidationMsgText2'					=> __("* This field is required","piereg"),
				'ValidationMsgText3'					=> __("* Please select an option","piereg"),
				'ValidationMsgText4'					=> __("* This checkbox is required","piereg"),
				'ValidationMsgText5'					=> __("* Both date range fields are required","piereg"),
				'ValidationMsgText6'					=> __("* Field must equal test","piereg"),
				'ValidationMsgText7'					=> __("* Invalid ","piereg"),
				'ValidationMsgText8'					=> __("Date Range","piereg"),
				'ValidationMsgText9'					=> __("Date Time Range","piereg"),
				'ValidationMsgText10'					=> __("* Minimum ","piereg"),
				'ValidationMsgText11'					=> __(" characters required","piereg"),
				'ValidationMsgText12'					=> __("* Maximum ","piereg"),
				'ValidationMsgText13'					=> __(" characters allowed","piereg"),
				'ValidationMsgText14'					=> __("* You must fill one of the following fields","piereg"),
				'ValidationMsgText15'					=> __("* Minimum value is ","piereg"),
				'ValidationMsgText16'					=> __("* Date prior to ","piereg"),
				'ValidationMsgText17'					=> __("* Date past ","piereg"),
				'ValidationMsgText18'					=> __(" options allowed","piereg"),
				'ValidationMsgText19'					=> __("* Please select ","piereg"),
				'ValidationMsgText20'					=> __(" options","piereg"),
				'ValidationMsgText21'					=> __("* Fields do not match","piereg"),
				'ValidationMsgText22'					=> __("* Invalid credit card number","piereg"),
				'ValidationMsgText23'					=> __("* Invalid phone number","piereg"),
				'ValidationMsgText24'					=> __("* Allowed Format (xxx) xxx-xxxx","piereg"),
				'ValidationMsgText25'					=> __("* Minimum 10 Digits starting with Country Code without + sign.","piereg"),
				'ValidationMsgText26'					=> __("* Invalid email address","piereg"),
				'ValidationMsgText27'					=> __("* Not a valid integer","piereg"),
				'ValidationMsgText28'					=> __("* Invalid number","piereg"),
				'ValidationMsgText29'					=> __("* Invalid month","piereg"),
				'ValidationMsgText30'					=> __("* Invalid day","piereg"),
				'ValidationMsgText31'					=> __("* Invalid year","piereg"),
				'ValidationMsgText32'					=> __("* Invalid file extension","piereg"),
				'ValidationMsgText33'					=> __("* Invalid date, must be in YYYY-MM-DD format","piereg"),
				'ValidationMsgText34'					=> __("* Invalid IP address","piereg"),
				'ValidationMsgText35'					=> __("* Invalid URL","piereg"),
				'ValidationMsgText36'					=> __("* Numbers only","piereg"),
				'ValidationMsgText37'					=> __("* Letters only","piereg"),
				'ValidationMsgText38'					=> __("* No special characters allowed","piereg"),
				'ValidationMsgText39'					=> __("* This user is already taken","piereg"),
				'ValidationMsgText40'					=> __("* Validating, please wait","piereg"),
				'ValidationMsgText41'					=> __("* This username is available","piereg"),
				'ValidationMsgText42'					=> __("* This user is already taken","piereg"),
				'ValidationMsgText43'					=> __("* Validating, please wait","piereg"),
				'ValidationMsgText44'					=> __("* This name is already taken","piereg"),
				'ValidationMsgText45'					=> __("* This name is available","piereg"),
				'ValidationMsgText46'					=> __("* Validating, please wait","piereg"),
				'ValidationMsgText47'					=> __("* This name is already taken","piereg"),
				'ValidationMsgText48'					=> __("* Please input HELLO","piereg"),
				'ValidationMsgText49'					=> __("* Invalid Date","piereg"),
				'ValidationMsgText50'					=> __("* Invalid Date or Date Format","piereg"),
				'ValidationMsgText51'					=> __("Expected Format: ","piereg"),
				'ValidationMsgText52'					=> __("mm/dd/yyyy hh:mm:ss AM|PM or ","piereg"),
				'ValidationMsgText53'					=> __("yyyy-mm-dd hh:mm:ss AM|PM","piereg"),
				'ValidationMsgText54'					=> __("* Invalid Username","piereg"),
				'ValidationMsgText55'					=> __("* Invalid File","piereg"),
				'ValidationMsgText56'					=> __("* Maximum value is ","piereg"),
				'ValidationMsgText57'					=> __("* Alphabetic Letters only","piereg"),
				'ValidationMsgText58'					=> __("* Only Alphanumeric characters are allowed","piereg"),
				'ValidationMsgText59'					=> __("Delete","piereg"),
				'ValidationMsgText60'					=> __("Edit","piereg"),
				'reCaptcha_public_key'					=> $opt['captcha_publc'],
				'prRegFormsIds'							=> $form_ids,
				'not_widgetTheme'						=> $not_widgetTheme,
				'is_widgetTheme'						=> $is_widgetTheme,
				'not_forgot_widgetTheme'				=> $not_forgot_widgetTheme,
				'is_forgot_widgetTheme'					=> $fields_data_filtered,
				'reg_forms_theme'						=> $fields_data,
				'matchCapResult1'						=> $mathCaptchaResult1,
				'matchCapResult2'						=> $mathCaptchaResult2,
				'matchCapResult3'						=> $mathCaptchaResult3,
				'matchCapColors'						=> $matchCapColor,
				//'prMathCaptchaID'						=> $fff,
				'matchCapImgColor'						=> $matchCapColor[$matchCapImage_name],
				'matchCapImgURL'						=> plugins_url('/images/math_captcha/'.$matchCapImage_name.'.png',__FILE__),
				'matchCapHTML'							=> $matchCapHTML,
				'is_socialLoginRedirect'				=> $this->pie_is_social_renew_account_call,
				'socialLoginRedirectRenewAccount'		=> $this->pie_ua_renew_account_url,
				'isSocialLoginRedirectOnLogin'			=> $isSocialLoginRedirectOnLogin,
				'socialLoginRedirectOnLoginURL'			=> $this->pie_after_login_page_redirect_url,
				'pie_payment_methods_data'				=> $pie_payment_methods_data,
				'prTimedFieldVal'						=> date("y-m-d H:i:s")
											);
			wp_localize_script( 'pie_prVariablesDeclaration_script', 'pie_pr_dec_vars', $this->pie_pr_dec_vars_array );
			wp_enqueue_script( 'pie_prVariablesDeclaration_script' );
			wp_enqueue_script( 'pie_prVariablesDeclaration_script_Footer' );
			
		}
		//Function print_multi_lang_backend_script_vars()
		//Declares Variables that we need on WP Backend
		//Uses wp_localize to translate the variables
		function print_multi_lang_backend_script_vars(){
			global $piereg_global_options,$wp_roles,$hook_suffix;
			$payment_gateways_html = "";
			$payment_gateways_list = $this->payment_gateways_list();
			if(isset($payment_gateways_list) && is_array($payment_gateways_list) && !empty($payment_gateways_list)){
				foreach($payment_gateways_list as $pgKey=>$pgval){
					$payment_gateways_html .= '<label for="allow_payment_gateways_'.$pgKey.'" class="required piereg-payment-list"><input name="field[%d%][allow_payment_gateways][]" id="allow_payment_gateways_'.$pgKey.'" value="'.$pgKey.'" type="checkbox" checked="checked" class="checkbox_fields">'.$pgval.'</label>';
				}
			}
			$roles = $wp_roles->roles;
			$user_role_option	= "";
			$user_role_object	= "";
			$user_role_array	= "";
			if(isset($roles) && is_array($roles) && !empty($roles)){
				foreach($roles as $key=>$value){
					$user_role_array[$key] = $value['name'];
				}
			}
			$user_role_object = json_encode($user_role_array);
			$defaultMeta = $this->getDefaultMeta();
			$fields_data = $this->getCurrentFields();
			if(!is_array($fields_data) || sizeof($fields_data) == 0) {	
				$fields_data 	= get_option( 'pie_fields_default' );
			}
			$fillvalKey		= array();
			$fillvalValue	= array();
			$fillvalNum		= 0;
			foreach($fields_data as $k=>$field){
				if( ($field['type'] == "honeypot" && !$this->piereg_pro_is_activate) || $field['type']=="submit" || $field['type']=="" || $field['type']=="form" || ($field['type']=="invitation" && $button["enable_invitation_codes"]=="0") ){
					continue;
				}
				if($field['type'] == "url" || $field['type'] == "aim" || $field['type'] == "yim" || $field['type'] == "jabber" || $field['type'] == "description"){
					$field['type'] = "default";
				}
				
				if( $field['type'] == "html" )
				{
					$field['html']	= html_entity_decode($field['html']);
				}
				
				if(isset($field['desc']) && !empty($field['desc']))
				{
					$field['desc'] = html_entity_decode($field['desc']);
				}
				
				$fillvalNum++;
				array_push($fillvalKey,$field['id']);
				array_push($fillvalValue,serialize($field));
			}
			$this->pie_pr_backend_dec_vars_array = array(
				'hook_suffix'			=> $hook_suffix,
				'wp_content_url'		=> content_url(),
				'wp_home_url'			=> home_url(),
				'wp_site_url'			=> site_url(),
				'wp_admin_url'			=> admin_url(),
				'wp_includes_url'		=> includes_url(),
				'wp_plugins_url'		=> plugins_url(),
				'wp_pie_register_url'	=> plugins_url("pie-register"),
				'payment_gateways_list' => $payment_gateways_html,
				'user_default_role'		=> get_option("default_role"),
				'current_date'			=> date_i18n("Y"),
				'startingDate'			=> $piereg_global_options['piereg_startingDate'],
				'endingDate'			=> $piereg_global_options['piereg_endingDate'],
				'user_roles_array'		=> $user_role_array,
				'user_roles_object'		=> $user_role_object,
				'plsSelectForm'			=> __("Please select a form","piereg"),
				'isPRFormEditor'		=> (isset($_GET['page']) && in_array($_GET['page'],array('pie-register','pr_new_registration_form')))?true:false,
				'inValidFields'			=> __('Invalid Fields','piereg'),
				'display_hints'			=> $piereg_global_options['display_hints'],
				'defaultMeta'			=> $defaultMeta,
				'appFormCondFldsStart'	=> '<div class="advance_fields"><label for="form_notification">'.__("Verifications","piereg").'</label><select name="field[form][notification][]" id="form_notification"  class="form_notification" style="width:100px;" ><option value="1" selected="selected">'.__("Admin Verification","piereg").'</option><option value="2">'.__("E-mail Verification","piereg").'</option><option value="3">'.__("E-mail & Admin Verification","piereg").'</option></select><span style="color:#fff;"> '.__("this field if","piereg").' </span><select data-selected_field="form_selected_field" name="field[form][selected_field][]" class="form_selected_field piereg_all_field_dropdown" style="width:100px;">',
				'appFormCondFldsEnd'	=> '</select>&nbsp;<select id="form_field_rule_operator" name="field[form][field_rule_operator][]" class="field_rule_operator_select" style="width:auto;"><option selected="selected" value="==">'.(__("equal","piereg")).'</option><option value="!=">'.(__("not equal","piereg")).'</option><option value="empty">'.(__("empty","piereg")).'</option><option value="not_empty">'.(__("not empty","piereg")).'</option><option value=">">'.(__("greater than","piereg")).'</option><option value="<">'.(__("less than","piereg")).'</option><option value="contains">'.(__("contains","piereg")).'</option><option value="starts_with">'.(__("starts with","piereg")).'</option><option value="ends_with">'.(__("ends with","piereg")).'</option></select>&nbsp;<div class="wrap_cond_value"><input type="text" name="field[form][conditional_value][]" id="form_conditional_value" class="input_fields conditional_value" placeholder="Enter Value"></div>&nbsp;<a href="javascript:;" class="delete_conditional_value_fields" style="color:white;font-size: 13px;margin-left: 2px;">x</a></div>',
				'fillvalKey'			=> $fillvalKey,
				'fillvalValue'			=> $fillvalValue,
				'fillvalNo'				=> (max(array_keys($fields_data))+1),
				'fillvalNum'			=> $fillvalNum,
				'block_wp_login'		=> $piereg_global_options['block_wp_login'],
				'selectLogoText'		=> 'Upload/Select Logo',
				'mediaUploadURL'		=> admin_url('media-upload.php')
				
			);
			wp_localize_script( 'pie_prBackendVariablesDeclaration_script', 'pie_pr_backend_dec_vars', $this->pie_pr_backend_dec_vars_array );
			wp_enqueue_script( 'pie_prBackendVariablesDeclaration_script' );
			wp_enqueue_script( 'pie_prBackendVariablesDeclaration_script_Footer' );
		}
		// function pie_admin_enqueu_scripts
		//Will be replace with print_multi_lang_backend_script_vars();
		function pie_admin_enqueu_scripts(){
			$this->print_multi_lang_backend_script_vars();
		}
		function print_multi_captcha_skin() {
			
			$settings	=	get_option(OPTION_PIE_REGISTER);
			$publickey	=	$settings['captcha_publc'];
			
			$fields_id = get_option("piereg_form_fields_id");
			$count = 0;
			$form_ids = array();
			for($a=1;$a<=$fields_id;$a++)
			{
				$option = get_option("piereg_form_field_option_".$a);
				if( !empty($option) && is_array($option) && isset($option['Id']) && trim($option['Status']) != "" && $option['Status'] == "enable" && (!isset($option['IsDeleted']) || trim($option['IsDeleted']) != 1) )
				{
					array_push($form_ids, 'reg_form_'.$option['Id']);
					$count++;
				}
			}
			
			$captcha_login_script = false;
			$captcha_forgot_script = true;
			$captcha_reg_script = false;
			if($settings['captcha_in_login_value'] == 1 && $settings['capthca_in_login'] == 3 ){
				$captcha_login_script = true;
			}
			if($settings['captcha_in_forgot_value'] == 1 && $settings['capthca_in_forgot_pass'] == 3 ){
				$captcha_forgot_script = true;
			}
			if($count > 0){
				$captcha_reg_script = true;
			}
			
				if($captcha_login_script || $captcha_reg_script || $captcha_forgot_script){}
		}
		
		function pie_backend_enqueue_scripts($hook_s){
			global $piereg_global_options;
			$pr_backend_hook_suffixes = array('pie-register','pr_new_registration_form','pie-notifications','pie-invitation-codes','pie-bulk-emails','pie-gateway-settings','pie-black-listed-users','pie-settings','pie-import-export','unverified-users','pie-help','post.php','edit.php','post-new.php');
			$pr_backend_hook_suffixes = apply_filters('pr_backend_hook_suffixes',$pr_backend_hook_suffixes);
			$is_pr_page = array_filter($pr_backend_hook_suffixes, function($el) use ($hook_s) {
				return ( stripos($hook_s,$el) !== false );
			});
			if(empty($is_pr_page)){
				return false;
			}
			///////////// So We are on PR pages /////////////////
			//Enqueue Styles
			wp_enqueue_style('pie_admin_css');
			//wp_enqueue_style('pie_jqueryui_css');
			wp_enqueue_style( 'pie_mCustomScrollbar_css' );
			wp_enqueue_style( 'pie_style_css' );
			wp_enqueue_style('pie_tooltip_css');
			wp_enqueue_style('pie_validation_css');
			
			//Now We Enqueue Variables			
			$this->print_multi_lang_script_vars();
			$this->pie_admin_enqueu_scripts();
			
			/*
				*	Add Since 2.0.13
			*/
			if($this->piereg_jquery_enable){
				wp_deregister_script('jquery');
				wp_register_script('jquery', plugins_url("/js/jquery.js",__FILE__),'','2.0.0',false);
				wp_enqueue_script( 'jquery' );
			}
			
			//wp_enqueue_script( 'jquery-ui' );
			wp_enqueue_script('pie_datepicker_js');
			wp_enqueue_script('jquery-ui-core');
			//wp_enqueue_script( 'jquery-ui' );
			wp_enqueue_script('pie_calendarcontrol_js');
			wp_enqueue_script('pie_datepicker_js');
			wp_enqueue_script('pie_drag_js');
			wp_enqueue_script('pie_mCustomScrollbar_js');
			wp_enqueue_script('pie_mousewheel_js');
			wp_enqueue_script('pie_sidr_js');
			wp_enqueue_script('pie_phpjs_js');
			wp_enqueue_script('pie_registermain_js');
			wp_enqueue_script('pie_regs_js');
			wp_enqueue_script('pie_tooltip_js');
			wp_enqueue_script('pie_alphanum_js');
			wp_enqueue_script('pie_validation_js');
		}
		
		function pie_frontend_enqueu_scripts(){
			global $piereg_global_options;
			$this->print_multi_lang_script_vars();
			//
			if(isset($piereg_global_options['outputcss']) && $piereg_global_options['outputcss'] == 1){
				wp_enqueue_style( 'pie_front_css' );
				wp_enqueue_style( 'pie_validation_css' );
				if(isset($piereg_global_options['pr_theme']) && intval($piereg_global_options['pr_theme']) > 0 && $this->piereg_pro_is_activate){
					wp_register_style( 'pie_themes_css', plugins_url("/css/theme/theme-".(intval($piereg_global_options['pr_theme'])).".css",__FILE__),false,'2.0', "all" );
					wp_enqueue_style( 'pie_themes_css' );
				}
			}
			////////////////////////////////////////////
			if(isset($piereg_global_options['outputjquery_ui']) && $piereg_global_options['outputjquery_ui'] == 1){
				wp_deregister_script('jquery-ui-core');
				wp_register_script('jquery-ui-core', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.0/jquery-ui.min.js',array('jquery'),'1.8.0',false);
				wp_enqueue_script( 'jquery-ui-core' );
			}
			////
			$captcha_login_script = false;
			$captcha_forgot_script = true;
			$captcha_reg_script = false;
			if($piereg_global_options['captcha_in_login_value'] == 1 && $piereg_global_options['capthca_in_login'] == 3 ){
				$captcha_login_script = true;
			}
			if($piereg_global_options['captcha_in_forgot_value'] == 1 && $piereg_global_options['capthca_in_forgot_pass'] == 3 ){
				$captcha_forgot_script = true;
			}
			if($count > 0){
				$captcha_reg_script = true;
			}
			if($captcha_login_script || $captcha_reg_script || $captcha_forgot_script){
				wp_enqueue_script("pie_recpatcha_script" );
			}
			
			wp_enqueue_script("pie_datepicker_js" );
			wp_enqueue_script("pie_alphanum_js");
			wp_enqueue_script("pie_validation_js");
			wp_enqueue_script('password-strength-meter',false,array('jquery','zxcvbn-async'),'',true);
			wp_enqueue_script('pie_password_checker',false,array('password-strength-meter'),'',true);
		}
		function pie_frontend_ajaxurl(){
		}
		
		function check_if_role_admin()
		{
			$is_admin 			= false;
			
			$current_user 		= wp_get_current_user();
			if (user_can( $current_user, 'administrator' )) {
			  	$is_admin	= true;
			}
			
			return $is_admin;
		}
		
		function piereg_registration_form($attributes="") {
			$this->piereg_ssl_template_redirect();
			
			$id 			= 0;
			$title 			= "true";
			$description 	= "true";
			$is_preview 	= false;
			
			//// **** ONLY ADMINISTRATOR CAN VIEW FORM PREVIEW ELSE IT WILL SHOW PAGE CONTENT **** ////
			$show_preview_form 	= $this->check_if_role_admin();
			
			if( isset($_GET['pr_preview']) && $show_preview_form ){
				$is_preview 		= true;
				$prFormId 			= intval(trim($_GET['prFormId']));
				$preview_form_id 	= 0;
				
				if(isset($_GET['prFormId']) && $prFormId > 0){
					$preview_form_id 	= $prFormId;
				}
				
			}
			
			$use_free_form 		= false;
			if( !is_array($attributes) ){
				$use_free_form = true;
			}
			
			if(is_array($attributes) || $is_preview || $use_free_form ){
				
				if( $use_free_form && !$is_preview )
				{
					$id = $this->regFormForFreeVers();
				}
				else {
					
					if(is_array($attributes)) extract($attributes);					
					
					if( !$this->piereg_pro_is_activate && ($id != $this->regFormForFreeVers()) ) {
						$id = false;
					}
					
				}
				
				//////////////////////////////////////////////////
				if($is_preview && $preview_form_id > 0){
					$id = $preview_form_id;
				}
				//////////////////////////////////////////////////
				
				
				if( intval($id) != 0 )
				{
					$check_form_in_db = get_option("piereg_form_fields_".((int)$id));
					if($check_form_in_db == false || trim($check_form_in_db) == "")
					{
						$id = false;
					}else{
						$check_form_in_db = get_option("piereg_form_field_option_".((int)$id));
						if( ($check_form_in_db['Status'] == "enable" && (!isset($check_form_in_db['IsDeleted']) || trim($check_form_in_db['IsDeleted']) != 1) ) || $is_preview){
							if(!$is_preview){
								$check_form_in_db['Views'] = esc_sql( intval($check_form_in_db['Views'])+1 );
								update_option("piereg_form_field_option_".((int)$id),$check_form_in_db);
								$this->set_pr_stats("register","view");
							}
							return $this->showForm($id,$title,$description);
						}else{
							return __("Currently this form is disabled by administrator.","piereg");
						}
					}
				}else{
					$id = false;
				}
				
				if($id == false){
					return __("You are using wrong shortcode of Pie-Register.","piereg");
					$this->pr_error_log("You are using wrong shortcode of Pie-Register.".($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
				}
			}
			else{
				return __("You are using wrong shortcode of Pie-Register.","piereg");
				$this->pr_error_log("You are using wrong shortcode of Pie-Register.".($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
			}
			
		}
		
		function modify_all_notices($notice)
		{
			$Start_notice = "";/*Write your message*/
			$End_notice = "";/*Write your message*/
			return $Start_notice.$notice.$End_notice;
		}
		
		function print_Rpr_licenseKey_errors()
		{
			if(isset($_POST['PR_license_notice']))
				return $_POST['PR_license_notice'];
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
			
			$links[] = '<a href="'. get_admin_url(null, 'admin.php?page=pie-settings') .'">'.(__("Settings","piereg")).'</a>';   		
			return $links;
		}
		
		function check_upgrade_remote()
		{
			require_once ( 'pie-updates.php' );
			$plugin_current_version = '2.0.21';
			//$plugin_remote_path = plugin_dir_url( __FILE__ ) . 'update.php';	
			$plugin_remote_path = "http://pieregister.com/downloads/update.php";
			$plugin_slug = plugin_basename( __FILE__ );
			$license_user = '';
			$license_key = '';
			new WP_PieRegAutoUpdate ( $plugin_current_version, $plugin_remote_path, $plugin_slug, $license_user, $license_key );	
		}
		
		function piereg_after_setup_theme(){
			if(isset($_POST['log']) && isset($_POST['pwd'])){
				$this->checkLogin();
			}
		}

		function print_in_footer(){
			echo '<div class="pieregWrapper" style="display:none;">';
			echo "<iframe id='CalendarControlIFrame' src='javascript:false;' frameBorder='0' scrolling='no'></iframe>";
			echo "<div id='CalendarControl'></div>";
			echo "</div>";
		}
		/*
			*	Restrict Post / Page Content
		*/
		function piereg_template_restrict($post_object){
			/*
				*	Get Options
			*/
			
			$option = get_post_meta($post_object->ID,"_piereg_post_restriction");
			/*
				*	Get Global Options
			*/
			$global_options = $this->get_pr_global_options();
			
			$this->post_block_content = "";
			$piereg_post_visibility_var = ((isset($option[0]["piereg_post_visibility"]) && $option[0]["piereg_post_visibility"] != "")?$option[0]["piereg_post_visibility"]:"");
			$visible =  $this->get_post_visibility($piereg_post_visibility_var);
			
			/*
				*	Ristrict For Bot
			*/
			if( $this->piereg_pro_is_activate ) {
				$global_option_enabl = ((isset($global_options['restrict_bot_enabel']) && $global_options['restrict_bot_enabel'] == 1)? true : false);
				$option_enabl 		 = ((isset($option[0]['piereg_bot_restriction_enabel']) && $option[0]['piereg_bot_restriction_enabel'] == 1)? true : false);				
				$page_restrtiction	 = (isset($option[0]['piereg_bot_restriction_enabel'])) ? $option[0]['piereg_bot_restriction_enabel'] : false;
				
			} 
			else 
			{
				$global_option_enabl = $option_enabl = false;	
			}
			
			if( ( $global_option_enabl && $page_restrtiction !== "0" ) || ($option_enabl) )
			{
				if(trim($global_options['restrict_bot_content']) != "")
				{
					$piereg_bot_content = $global_options['restrict_bot_content'];
					if(trim($piereg_bot_content) == "")// If user agen is empty then return
						return $post_object;
					
					$bot_array = explode(PHP_EOL,$piereg_bot_content);//Explode Agent bot in array by \r\n
					$validate_bot = false;//validate bot variable
					
					foreach($bot_array as $val)
					{
						if(strpos($_SERVER['HTTP_USER_AGENT'],$val) !== false)
						{
							$validate_bot = strpos($_SERVER['HTTP_USER_AGENT'],$val);//check http user agent with defined
						}
						if($validate_bot)//If true then break loop
							break;
					}

					if($validate_bot)//if true vaqlidate then
					{
						//Get global option's message
						$global_option_enabl = "";
						if((isset($global_options['restrict_bot_content_message']) && $global_options['restrict_bot_content_message'] != ""))
							$global_option_enabl = trim($global_options['restrict_bot_content_message']);
						
						//get current post option's message
						$option_enabl = "";
						if((isset($option[0]['piereg_bot_block_content']) && $option[0]['piereg_bot_block_content'] != ""))
							$option_enabl =  trim($option[0]['piereg_bot_block_content']);
						
						$page_object = get_queried_object();
						$page_id     = get_queried_object_id();
						$this->post_block_content = (($option_enabl != "")?$option_enabl:$global_option_enabl);
						add_filter("the_content",array($this,"restrict_content_post"));
						return $page_object;//return post.
					}
				}
			}
			
			/* Restrict for Users */
			if(!isset($option[0]["piereg_post_visibility"])){
				return $post_object;
			}
			//if Visibility status is default then return
			if($option[0]['piereg_post_visibility'] == "default")
			{
				return $post_object;
			}
			if( (isset($option[0])) && ( $option[0]['piereg_block_content'] != "" && $visible) ){
				
				////0 = Redirect
				////1 = Block Content
				
				$page_object = get_queried_object();
				$page_id     = get_queried_object_id();
				
				if(($page_id == $post_object->ID) && $option[0]['piereg_restriction_type'] == 0)
				{
					//1st Redirect URL and 2nd Redirect Page
					// Current Page URL
					$pv_URIprotocol = isset($_SERVER["HTTPS"]) ? (($_SERVER["HTTPS"]==="on" || $_SERVER["HTTPS"]===1 || $_SERVER["SERVER_PORT"]===$pv_sslport) ? "https://" : "http://") :  (($_SERVER["SERVER_PORT"]===$pv_sslport) ? "https://" : "http://");
					
					$redirect_to = "redirect_to=".$pv_URIprotocol.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ;
					
					if($option[0]['piereg_redirect_url'] != ""){
						// Redirtect URL 
						$redirect_url = $option[0]['piereg_redirect_url'];
						//Redirect
						$redirect_url = $this->pie_modify_custom_url($redirect_url , $redirect_to); 
						wp_redirect($redirect_url);
						exit;
					}else{
						//Redirect Url
						$redirect_url = get_permalink($option[0]['piereg_redirect_page']);
						//Redirect
						$redirect_url = $this->pie_modify_custom_url($redirect_url , $redirect_to); 
						wp_safe_redirect($redirect_url);
						exit;
					}
				}
				
				$this->post_block_content = $option[0]['piereg_block_content'];
				add_filter("the_content",array($this,"restrict_content_post"));
				return $post_object;
				
			}
			return $post_object;
		}
		/*
			*	Checkl Visibility
		*/
		function get_post_visibility($logic){
			$visible = false;
			if($logic == "default"){
				$visible = true;
			}
			elseif($logic == "after_login"){
				$visible = !is_user_logged_in();
			}
			elseif($logic == "before_login"){
				$visible = is_user_logged_in();
			}
			elseif($logic != "" ){
				global $current_user;
				$current_user = wp_get_current_user();
				$current_user_role = (array)$current_user->roles;
				$visible = !in_array( $logic, $current_user_role );
			} 
			return $visible;
		}
		/*
			*	Restrict Post/Page Content
		*/
		function restrict_content_post($content){
			if($this->post_block_content != "")
			$content = nl2br($this->post_block_content);
			
			return $content;
		}
		/*
			*	Add Meta Box in post / page
		*/
		function piereg_add_meta_box($postType) {
			$args = array(
			   'public'   => true,
			   '_builtin' => true
			);
			
			$output = 'names'; // names or objects, note names is the default
			$operator = 'and'; // 'and' or 'or'
			
			$screens = get_post_types( $args, $output, $operator );
			
			//$screens = array( 'post', 'page' );
			foreach ( $screens as $screen ) {
				add_meta_box(
					'myplugin_sectionid',
					__( 'Pie Register - '.ucwords($screen).' Restriction', 'piereg' ),
					array($this,'myplugin_meta_box_callback'),
					$screen
				);
			}
		}
		
		/*
			*	Show Post Meta Box
		*/
		function myplugin_meta_box_callback( $post ) { 
			wp_nonce_field( 'myplugin_meta_box', 'myplugin_meta_box_nonce' );
			?>
            <div class="pie_register-admin-meta">
			<?php $result = get_post_meta( $post->ID, '_piereg_post_restriction', true );?>
				
	            <div class="piereg_restriction_field_area">
                	<h2><?php _e( 'Visibility Restrictions', 'piereg' ); ?></h2>
	                <input type="hidden" name="post_restriction[piereg_post_type]" value="<?php echo $post->post_type; ?>" />
                	<div class="piereg_label">
						<label for="piereg_post_visibility"><?php _e( 'Visibility', 'piereg' ); ?></label>
					</div>
					<div class="piereg_input">
						<?php
							$option = ((isset($result['piereg_post_visibility']) && !empty($result['piereg_post_visibility']))?$result['piereg_post_visibility']:"default");
						?>
						<select  id="piereg_post_visibility" name="post_restriction[piereg_post_visibility]">
			            	<option value="default" <?php echo ($option == "default")? 'selected="selected"' : '' ?>><?php _e('Default',"piereg") ?></option>
			            	<option value="after_login" <?php echo ($option == "after_login")? 'selected="selected"' : '' ?>><?php _e('Show to Logged in Users',"piereg") ?></option>
			            	<option value="before_login" <?php echo ($option == "before_login")? 'selected="selected"' : '' ?>><?php _e('Show to Non-Logged in Users',"piereg") ?></option>
			                <?php
							global $wp_roles;
							$role = $wp_roles->roles;
							
							foreach($role as $value)
							{ 
								?>
								<option value="<?php echo strtolower(str_replace(" ","",$value['name']));?>"<?php echo ($option == strtolower($value['name'])) ? 'selected="selected"' : ''; ?>><?php _e("Show to","piereg");echo " ".$value['name']; ?></option>
			                    <?php
							}
							?>
			            </select>
                	</div>
                </div>
                
                
                <div class="piereg_restriction_field_area pieregister_restriction_type_area">
                	<div class="piereg_label"><label><?php _e( 'Restriction Type', 'piereg' ); ?></label></div>
                    
					<div class="piereg_input">
						<?php $restriction_option = (isset($result['piereg_restriction_type']) && $result['piereg_restriction_type'] != "") ? $result['piereg_restriction_type'] : 0; ?>
                        <div class="piereg_input_radio">
	                        <label for="redirect">
                            <input type="radio" id="redirect" class="piereg_restriction_type" name="post_restriction[piereg_restriction_type]" value="0" <?php echo ($restriction_option == 0)? 'checked="checked"' : '' ?> /><?php _e( 'Redirect', 'piereg' ); ?></label>
                        </div>
                        <div class="piereg_input_radio">
	                        <label for="block_content">
                            <input type="radio" id="block_content" class="piereg_restriction_type" name="post_restriction[piereg_restriction_type]" value="1" <?php echo ($restriction_option == 1)? 'checked="checked"' : '' ?> /><?php _e( 'Block Content', 'piereg' ); ?></label>
                        </div>
                	</div>
                </div>
                
				<div id="pieregister_restriction_url_area" <?php echo ($restriction_option != 0)? 'style="display:none"' : '' ?> >
                    <div class="piereg_restriction_field_area pieregister_restriction_url_area">
                        <div class="piereg_label">
                            <?php $option = ((isset($result['piereg_redirect_url']) && $result['piereg_redirect_url'] != "") ? $result['piereg_redirect_url'] : ""); ?>
                            <label for="piereg_redirect_url"><?php _e( 'Redirect Url', 'piereg' ); ?></label>
                        </div>
                        <div class="piereg_input">
                            <input type="url" id="piereg_redirect_url" name="post_restriction[piereg_redirect_url]" value="<?php echo $option;  ?>" style="width:70%;" class="pieregister_redirect_url" />
                        </div>
                    </div>
                    
                    <div class="piereg_restriction_field_area pieregister_restriction_url_area">
                        <center><strong><?php _e('OR','piereg'); ?></strong></center>
                    </div>
                    
                    <div class="piereg_restriction_field_area pieregister_restriction_url_area">
                        <div class="piereg_label">
                            <?php $option = ((isset($result['piereg_redirect_page']) && $result['piereg_redirect_page'] != "") ? $result['piereg_redirect_page'] : -1); ?>
                            <label for="piereg_redirect_page"><?php _e( 'Redirect Page', 'piereg' ); ?></label>
                        </div>
                        <div class="piereg_input">
                            <?php
							$args =  array("show_option_no_change"=>"None","id"=>"piereg_redirect_page","class"=>"pieregister_redirect_url","name"=>"post_restriction[piereg_redirect_page]","selected"=>$option);
                            wp_dropdown_pages( $args ); ?>
                        </div>
                    </div>
                </div>
                
                <div class="piereg_restriction_field_area pieregister_block_content_area" <?php echo ($restriction_option != 1)? 'style="display:none"' : '' ?> >
                	<div class="piereg_label">
						<?php $option = ((isset($result['piereg_block_content']) && $result['piereg_block_content'] != "") ? $result['piereg_block_content'] : ""); ?>
						<label for="piereg_block_content"><?php _e( 'Block Content', 'piereg' ); ?></label>
					</div>
					<div class="piereg_input">
						<textarea id="piereg_block_content" name="post_restriction[piereg_block_content]"><?php echo $option; ?></textarea>
                	</div>
                </div>
                
                <hr class="pie_register_saprator_admin_meta"/>
                <h2><?php _e( 'Restrict For Search Engine(s)', 'piereg' ); ?></h2>
                <div class="piereg_restriction_field_area">
                	<div class="piereg_label">
						<label for="piereg_block_content"><?php _e( 'Enable', 'piereg' ); ?></label>
					</div>
					<div class="piereg_input">
						<?php $option = (isset($result['piereg_bot_restriction_enabel']) && $result['piereg_bot_restriction_enabel'] != "") ? $result['piereg_bot_restriction_enabel'] : 2; ?>
                        <div class="piereg_input_radio">
	                        <label for="restriction_enabel_yes">
                            <input type="radio" id="restriction_enabel_yes" class="piereg_bot_restriction_enabel" name="post_restriction[piereg_bot_restriction_enabel]" value="1" <?php echo ($option == 1)? 'checked="checked"' : '' ?> /><?php _e( 'Yes', 'piereg' ); ?></label>
                        </div>
                        <div class="piereg_input_radio">
	                        <label for="restriction_enabel_no">
                            <input type="radio" id="restriction_enabel_no" class="piereg_bot_restriction_enabel" name="post_restriction[piereg_bot_restriction_enabel]" value="0" <?php echo ($option == 0)? 'checked="checked"' : '' ?> /><?php _e( 'No', 'piereg' ); ?></label>
                        </div>
                        <div class="piereg_input_radio">
	                        <label for="restriction_enabel_global">
                            <input type="radio" id="restriction_enabel_global" class="piereg_bot_restriction_enabel" name="post_restriction[piereg_bot_restriction_enabel]" value="2" <?php echo ($option == 2)? 'checked="checked"' : '' ?> /><?php _e( 'Use Global', 'piereg' ); ?></label>
                        </div>
                	</div>
                </div>
                
                <div class="piereg_restriction_field_area pieregister_bot_block_content_area">
                	<div class="piereg_label">
						<?php $option = ((isset($result['piereg_bot_block_content']))?$result['piereg_bot_block_content']:""); ?>
						<label for="piereg_bot_block_content"><?php _e( 'Bot Block Content', 'piereg' ); ?></label>
					</div>
					<div class="piereg_input">
						<textarea id="piereg_bot_block_content" name="post_restriction[piereg_bot_block_content]"><?php echo $option; ?></textarea>
                	</div>
                </div>
                
            </div>
            <noscript><?php _e('Sorry, your browser does not support JavaScript!','piereg'); ?></noscript>
            
			<?php
		}
		/*
			Save Post Meta Box
		*/
		function piereg_save_meta_box_data( $post_id ) {
			update_post_meta( $post_id, '_piereg_post_restriction', $_POST['post_restriction'] );
		}
		
		
		private function show_invitaion_code_user(){
			global $errors,$wpdb;
				$prefix=$wpdb->prefix."pieregister_";
				$inv_code = base64_decode($_GET['invitaion_code']);
				
				$invitaion_code_users = $wpdb->get_results(  $wpdb->prepare( "SELECT `user_login`,`user_email` FROM `wp_users` WHERE `ID` IN (SELECT user_id FROM `wp_usermeta` Where meta_key = 'invite_code' and meta_value = %s )", $inv_code )  );
				
				if(isset($wpdb->last_error) && !empty($wpdb->last_error)){
					$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
				}
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
					<h2><?php _e("Activation Code","piereg");echo " : ".$inv_code; ?></h2>
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
			$option = get_option(OPTION_PIE_REGISTER);	
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
			$option = get_option(OPTION_PIE_REGISTER);
			return $option['custom_logo_tooltip'];
			unset($option);
		}
		function piereg_login_logo_url() {
			$option = get_option(OPTION_PIE_REGISTER);
			return $option['custom_logo_link'];
			unset($option);
		}
		function payment_success_cancel_after_register($query_string){
			global $wpdb;
			$option = get_option(OPTION_PIE_REGISTER);
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
			$current_user_caps_keys = array_keys($current_user->caps);
			$ncaps = count($current_user_caps_keys);
			if($ncaps) {
				//if( !in_array('administrator', $current_user->caps) )
				if( !array_key_exists('administrator', $current_user->caps) )
				{
					show_admin_bar( false );
				}
			}
			unset($current_user);
		}
		
		function delete_piereg_form()
		{
			if(isset($_GET['prfrmid']) and ((int)$_GET['prfrmid']) != 0 and isset($_GET['action']) and $_GET['action'] == "delete")
			{
				$fields_id 				= ((int)$_GET['prfrmid']);
				$assign_new_free_form 	= false;
				
				if( $this->regFormForFreeVers() == $fields_id ) {
					$assign_new_free_form = true;
				}
				
				delete_option("piereg_form_field_option_".$fields_id);
				delete_option("piereg_form_fields_".$fields_id);
				$user_role = get_option(OPTION_PIE_REGISTER);
				unset($user_role['pie_regis_set_user_role_'.((int)$_GET['prfrmid'])]);
				update_option(OPTION_PIE_REGISTER,$user_role);
				unset($user_role);
				unset($fields_id);
				unset($_GET['prfrmid']);
				
				if( $assign_new_free_form ) {
					$this->regFormForFreeVers(true);
				}
			}
		}
		
		//"Insert Form" button to the post/page edit screen
		function add_pie_form_button($context)
		{
			$is_post_edit_page = in_array(basename($_SERVER['PHP_SELF']), array('post.php', 'page.php', 'page-new.php', 'post-new.php'));
			if(!$is_post_edit_page)
				return $context;
			
			$out = '<a href="#TB_inline?width=480&inlineId=select_pie_form" class="thickbox button" id="add_pie_form" title="' . __("Add Pie Register Form", 'piereg') . '" ><span style="background: url('.plugins_url('pie-register').'/images/form-icon.png); background-repeat: no-repeat; background-position: left bottom;" class="wp-media-buttons-icon"></span> '.__("Add Form","piereg").'</a>';
			return $context . $out;
		}
		function checkLoginPage()
		{
			$option 		= get_option(OPTION_PIE_REGISTER);	
			$current_page	= get_the_ID();
			if($option['block_wp_login']==1 && $option['alternate_login'] > 0 && is_user_logged_in() && $current_page == $option['alternate_login'] )
			{	
				
				$this->afterLoginPage();			
			}
		}
		function add_pie_form_popup()
		{
			 ?>
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
                            	<option value=""><?php _e("Select","piereg") ?></option>
                            	<optgroup label="<?php _e("Registration Form","piereg") ?>">
                                    <?php
                                    $fields_id = get_option("piereg_form_fields_id");
									for($a=1;$a<=$fields_id;$a++)
									{
										$option = get_option("piereg_form_field_option_".$a);
										if($option != "" && (!isset($option['IsDeleted']) || trim($option['IsDeleted']) != 1) )
										{
											echo '<option value=\'[pie_register_form id="'.$option['Id'].'" title="true" description="true" ]\'>'.$option['Title'].'</option>';
										}
									}
?>
                                </optgroup>
                                <optgroup label="<?php _e("Other Form","piereg") ?>">
                                    <option value="[pie_register_login]"><?php _e("Login Form","piereg") ?></option>
                                    <option value="[pie_register_forgot_password]"><?php _e("Forgot Password Form","piereg") ?></option>
                                    <option value="[pie_register_profile]"><?php _e("Profile Page","piereg") ?></option>
                                </optgroup>
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
			$this->set_pr_stats("login","view");
			if( file_exists(PIEREG_DIR_NAME . "/login_form.php") )
				include_once("login_form.php");
			$output = pieOutputLoginForm();
			echo $output;
			get_footer();
			exit;
		}
		function checkLogin(){
			global $wpdb, $errors, $wp_session;
			$errors = new WP_Error();
			
			$option = get_option(OPTION_PIE_REGISTER);
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
				
				if( $this->piereg_pro_is_activate ){
					if( isset($option['piereg_blk_ip']) && (isset($option['enable_blockedips']) && $option['enable_blockedips'] == 1 ) ) 
					{
						$array_ips 			= array_map( 'trim', explode(PHP_EOL,$option['piereg_blk_ip']) );
						$user_ip_address 	= (getenv('HTTP_X_FORWARDED_FOR')) ? getenv('HTTP_X_FORWARDED_FOR') : getenv('REMOTE_ADDR');
						
						if( $this->isUserIpsIsBlocked( ip2long($user_ip_address), $array_ips ) ) {
							$errors->add('login-error',apply_filters("piereg_ipaddress_blocked",__('Your IP address has been blocked by administrator.','piereg')));
							$error_found++;
						}	
					}
					
					if( isset($option['piereg_blk_username']) && (isset($option['enable_blockedusername']) && $option['enable_blockedusername'] == 1 ) )
					{
						$cred_userlogin	= $_POST['log'];
						if( is_email($cred_userlogin) ) {
							$userdata 			= get_user_by('email', $cred_userlogin);
							$cred_userlogin		= strtolower($userdata->user_login);
						}
						
						$array_username 	= array_map( 'trim', explode(PHP_EOL,$option['piereg_blk_username']) );
						$array_username 	= array_map( 'strtolower', $array_username );
						
						if(	$this->isUserNameIsBlocked($cred_userlogin,$array_username) ) {
							$errors->add('login-error',apply_filters("piereg_username_blocked",__('This user has been blocked by administrator.','piereg')));
							$error_found++;
						}	
					}
					
					if( isset($option['piereg_blk_email']) && (isset($option['enable_blockedemail']) && $option['enable_blockedemail'] == 1 ) )
					{
						$cred_userlogin2	= $_POST['log'];
						if( !is_email($cred_userlogin2) ) {
							$userdata 			= get_user_by('login', $cred_userlogin2);
							$cred_userlogin2	= strtolower($userdata->user_email);
						}
						
						$array_emailaddr 	= array_map( 'trim', explode(PHP_EOL,$option['piereg_blk_email']) );
						$array_emailaddr 	= array_map( 'strtolower', $array_emailaddr );
						
						if(	$this->isEmailAddressIsBlocked($cred_userlogin2,$array_emailaddr) ) {
							$errors->add('login-error',apply_filters("piereg_username_blocked",__('This email address has been blocked by administrator.','piereg')));
							$error_found++;
						}	
					}
				}
				
				$table_name = $wpdb->prefix . "pieregister_lockdowns";
				$user_ip = $_SERVER['REMOTE_ADDR'];
				$get_results = $wpdb->get_results($wpdb->prepare("SELECT * FROM `".$table_name."` WHERE `user_ip` = %s AND `attempt_from` = 'is_login';",$user_ip));
				$piereg_security_attempts_login_value = false;
				if(isset($option['piereg_security_attempts_login_value']) && $option['piereg_security_attempts_login_value'] == 1 && $this->piereg_pro_is_activate){
					$piereg_security_attempts_login_value = true;
				}
				
				if($option['captcha_in_login_value'] == 1){
					
					$attempts = false;
					if( $option['captcha_in_login_attempts'] > 0 && $this->piereg_pro_is_activate ){
						if( isset($get_results[0]) && $option['captcha_in_login_attempts'] <= $get_results[0]->login_attempt){
							$attempts = true;
						}
					}elseif( $option['captcha_in_login_attempts'] > 0 && !$this->piereg_pro_is_activate ){
						$attempts = true;
					}elseif( $option['captcha_in_login_attempts'] == 0 && $this->piereg_pro_is_activate || !$this->piereg_pro_is_activate ){
						$attempts = true;
					}
					
					if( $attempts ){
						if($option['capthca_in_login'] == 2 || ( isset($_POST['piereg_math_captcha_login']) || isset($_POST['piereg_math_captcha_login_widget']) ) ){
							if(isset($_POST['piereg_math_captcha_login']))//Login form in Page
							{
								$piereg_cookie_array =  $_COOKIE['piereg_math_captcha_Login_form'];
								$piereg_cookie_array = explode("|",$piereg_cookie_array);
								$cookie_result1 = (intval(base64_decode($piereg_cookie_array[0])) - 12);
								$cookie_result2 = (intval(base64_decode($piereg_cookie_array[1])) - 786);
								$cookie_result3 = (intval(base64_decode($piereg_cookie_array[2])) + 5);
								if( ($cookie_result1 == $cookie_result2) && ($cookie_result3 == $_POST['piereg_math_captcha_login'])){
								}
								else{
									if($this->piereg_authentication($_POST['log'],$_POST['pwd'])){
										$errors->add('login-error',apply_filters("Invalid_Security_Code",__('Invalid Security Code','piereg')));
									}else{
										if( $piereg_security_attempts_login_value ){
											$errors->add('login-error',apply_filters("blocked_ip_due_to_many_attempts_failed",__("We are sorry, but this IP range has been blocked due to too many recent failed login attempts.","piereg")));
										}
									}
									$error_found++;
								}
							}
							elseif(isset($_POST['piereg_math_captcha_login_widget']))//Login form in widget
							{
								$piereg_cookie_array =  $_COOKIE['piereg_math_captcha_Login_form_widget'];
								$piereg_cookie_array = explode("|",$piereg_cookie_array);
								$cookie_result1 = (intval(base64_decode($piereg_cookie_array[0])) - 12);
								$cookie_result2 = (intval(base64_decode($piereg_cookie_array[1])) - 786);
								$cookie_result3 = (intval(base64_decode($piereg_cookie_array[2])) + 5);
								if( ($cookie_result1 == $cookie_result2) && ($cookie_result3 == $_POST['piereg_math_captcha_login_widget'])){
								}
								else{
									if($this->piereg_authentication($_POST['log'],$_POST['pwd'])){
										$errors->add('login-error',apply_filters("Invalid_Security_Code",__('Invalid Security Code','piereg')));
									}else{
										if( $piereg_security_attempts_login_value ){
											$errors->add('login-error',apply_filters("blocked_ip_due_to_many_attempts_failed",__("We are sorry, but this IP range has been blocked due to too many recent failed login attempts.","piereg")));
										}
									}
									$error_found++;
								}
							}else{
								if($this->piereg_authentication($_POST['log'],$_POST['pwd'])){
									$errors->add('login-error',apply_filters("Invalid_Security_Code",__('Invalid Security Code','piereg')));
								}else{
									if( $piereg_security_attempts_login_value ){
										$errors->add('login-error',apply_filters("blocked_ip_due_to_many_attempts_failed",__("We are sorry, but this IP range has been blocked due to too many recent failed login attempts.","piereg")));
									}
								}
								$error_found++;
							}
						}//New Recaptcha
						elseif($option['capthca_in_login'] == 3 || (isset($_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]) && !empty($_POST["recaptcha_challenge_field"]) && !empty($_POST["recaptcha_response_field"]) ) ){
							$settings  	=  get_option(OPTION_PIE_REGISTER);
							$privatekey	= $settings['captcha_private'];
							
							$captcha = "";
							if(isset($_POST['g-recaptcha-response'])){
								$captcha=$_POST['g-recaptcha-response'];
							}
							
							$response = $this->read_file_from_url("https://www.google.com/recaptcha/api/siteverify?secret=".trim($privatekey)."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
							$resp = json_decode($response,true);
							if($resp['success'] == false){
								if($this->piereg_authentication($_POST['log'],$_POST['pwd'])){
									$errors->add('login-error',apply_filters("Invalid_Security_Code",__('Invalid Security Code','piereg')));
								}else{
									if( $piereg_security_attempts_login_value ){
										$errors->add('login-error',apply_filters("blocked_ip_due_to_many_attempts_failed",__("We are sorry, but this IP range has been blocked due to too many recent failed login attempts.","piereg")));
									}
								}
								$error_found++;
							}
						}
					}
				}
				
				if($error_found == 0){
					
					$creds['user_login'] 	= $_POST['log'];
					if( is_email($creds['user_login']) )
					{
						$userdata 				= get_user_by('email', $creds['user_login']);
						$creds['user_login']	= strtolower($userdata->user_login);
					}
					
					$_POST['pwd']			= html_entity_decode($_POST['pwd']);
					$creds['user_password'] = $_POST['pwd'];
					$creds['remember'] 		= isset($_POST['rememberme']);
					
					$remember_user	= (isset($_POST['rememberme'])) ? true : false ;
					
					if(isset($_POST['social_site']) and $_POST['social_site'] == "true" )
					{
						require_once( ABSPATH . WPINC . '/user.php' );
						$this->piereg_get_wp_plugable_file(true); // require_once pluggable.php
						wp_set_auth_cookie($_POST['user_id_social_site'], $remember_user);
						$user = get_userdata($_POST['user_id_social_site']);
					}
					else
					{
						$piereg_secure_cookie = false;
						$piereg_secure_cookie = $this->PR_IS_SSL();
						if($this->piereg_authentication($_POST['log'],$_POST['pwd'])){
							
							$user = wp_signon( $creds, $piereg_secure_cookie);
							
							if ( !is_wp_error($user) ){
								$this->piereg_delete_authentication();
							}
						}else{
							if( $piereg_security_attempts_login_value ){
								$user = new WP_Error('piereg_authentication_failed', __("We are sorry, but this IP range has been blocked due to too many recent failed login attempts.","piereg"));
							}
						}
					}
					
					if ( is_wp_error($user))
					{
						$user_login_error = $user->get_error_message();
						if(strpos(strip_tags($user_login_error),'Invalid username',5) > 6 || strpos($user_login_error,'field is empty') !== false)
						{
							$user_login_error = apply_filters('pie_invalid_username_password_msg_txt','<strong>'.ucwords(__("error","piereg")).'</strong>: '.__("Invalid username","piereg").'. <a href="'.$this->pie_lostpassword_url().'" title="'.__("Password Lost and Found","piereg").'">'.__("Lost your password?","piereg").'</a>');
						}else if(strpos(strip_tags($user_login_error),'password you entered',9) > 10)
						{
							$user_login_error = apply_filters('pie_invalid_user_password_msg_txt','<strong>'.ucwords(__("error","piereg")).'</strong>: '.__("The password you entered for the username","piereg").' <strong>'.$_POST['log'].'</strong> '.__("is incorrect","piereg").'. <a href="'.$this->pie_lostpassword_url().'" title="'.__("Password Lost and Found","piereg").'">'.__("Lost your password?","piereg").'</a>');
						}
						$errors->add('login-error',apply_filters("piereg_login_error",$user_login_error));
						if(isset($_POST['piereg_login_after_registration']) && $option['login_after_register'] == 1){
							$error_message = base64_encode(__("Invalid username","piereg"));
							wp_safe_redirect($this->pie_modify_custom_url($this->pie_login_url(),"pr_invalid_username=true&pr_key={$error_message}"));
							exit;
						}
					}
					else
					{
						$this->set_pr_stats("login","used");
						if(in_array("administrator",(array)$user->roles)){
							
							/*
								*	Add Since 2.0.13
							*/
							if( $user ) {
								wp_set_current_user( $user->ID, $user->user_login );
								wp_set_auth_cookie( $user->ID, $remember_user );
								do_action( 'wp_login', $user->user_login, $user );
							}
							do_action("piereg_admin_login_before_redirect_hook",$user);
							
							$this->afterLoginPage(); 
							exit;
						}
						else{
							/*
								*	Check User Expiry
								*	Deprecate
							*/
							
							$active = get_user_meta($user->ID,"active",true);
							
							//Delete User after grace Period
							if(!$this->deleteUsers($user->ID,$user->user_email,$user->user_registered)){
								if($active == "0")//If not active
								{
									wp_logout();
									$check_payment = get_option(OPTION_PIE_REGISTER);
									
									// Payment Cycle Is Disabled In 3.0 Release.
									if((($this->check_enable_payment_method()) == "true") && ("not" === "using") )
									{
										global $wpdb,$pr_wp_db_prefix;
										$user_name_or_email = esc_sql($_POST['log']);
										$myrows = $wpdb->get_results( $wpdb->prepare("SELECT ID,user_login,user_email FROM `".($pr_wp_db_prefix)."users` where user_login = %s OR `user_email` = %s", $user_name_or_email, $user_name_or_email) );
										if(isset($wpdb->last_error) && !empty($wpdb->last_error)){
											$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
										}
										
										$errors->add('login-error',__('Please Renew your account. ','piereg'));
										$_POST['error'] = __('Please Renew your account',"piereg");
										$auth_key = md5( $myrows[0]->user_login );
										$user_name = trim( $myrows[0]->user_login );
										update_user_meta( $myrows[0]->ID , "pr_renew_account_hash" , $auth_key );
										
										$query_str = "pr_renew_account=true&auth=".base64_encode((urlencode($user_name)))."&auth_key=".(urlencode($auth_key));
										$renew_account_url = $this->pie_modify_custom_url($this->pie_login_url($check_payment['alternate_login']),$query_str);
										$this->pie_ua_renew_account_url = $renew_account_url;
										if(
											   isset($option['social_site_popup_setting'],$_POST['social_site']) and 
											   $option['social_site_popup_setting'] == 1 and 
											   $_POST['social_site']  == "true"
										   )
										{
											//Redirect will be triggered from js file now
										}
										else{
											wp_safe_redirect($renew_account_url);
										}
										exit;
									}
									else
									{
										$errors->add("login-error",apply_filters("piereg_your_account_is_not_active",__("Your account is not active","piereg")));
									}
								}elseif(empty($active)){
									if( $user ) {
										wp_set_current_user( $user->ID, $user->user_login );
										wp_set_auth_cookie( $user->ID, $remember_user );
										do_action( 'wp_login', $user->user_login, $user );
									}
									
									do_action("piereg_user_login_before_redirect_hook",$user);
									do_action('pie_register_after_login',$user);
									$this->afterLoginPage();
									exit;
								}
								else
								{
									do_action('pie_register_after_login',$user);
									
									// After Validation Show after login page.
									$option = get_option(OPTION_PIE_REGISTER);
									if(
										   isset($option['social_site_popup_setting']) and 
										   $option['social_site_popup_setting'] == 1 and 
										   $_POST['social_site']  == "true"
									   )
									{
										if( $user ) {
											wp_set_current_user( $user->ID, $user->user_login );
											wp_set_auth_cookie( $user->ID, $remember_user );
											do_action( 'wp_login', $user->user_login, $user );
										}
										$this->afterLoginPage();
										exit;
									}
									else
									{
										if( $user ) {
											wp_set_current_user( $user->ID, $user->user_login );
											wp_set_auth_cookie( $user->ID, $remember_user );
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
			}
		}
		function piereg_authentication($log,$pwd){
			//authenticate username and password
			$authenticate_user = wp_authenticate($log,$pwd);
			$option = get_option(OPTION_PIE_REGISTER);
			
			if($this->piereg_pro_is_activate && ((isset($option['piereg_security_attempts_login_value']) && $option['piereg_security_attempts_login_value'] === 1)  || (isset($option['captcha_in_login_value']) && $option['captcha_in_login_value'] == 1)) ){
				global $wpdb;
				$table_name = $wpdb->prefix . "pieregister_lockdowns";
				$user_ip = $_SERVER['REMOTE_ADDR'];
				$username = sanitize_user($log);
				
				$user_data = wp_authenticate($log,$pwd);
				$is_error = ( (is_wp_error($user_data))? true : $user_data );
				
				if ( is_wp_error($user_data) ) { 
					$user_id = 0;
				} else {
					$user_id = $user_data->ID;
				}
				
				$release_time = date_i18n('Y-m-d H:i:s', strtotime( ('+'.intval($option['security_attempts_login_time'])." minutes"), strtotime(date("Y-m-d H:i:s"))));
				
				$get_results = $wpdb->get_results($wpdb->prepare("SELECT * FROM `".$table_name."` WHERE `user_ip` = %s AND `attempt_from` = 'is_login';",$user_ip));
				if(isset($wpdb->last_error) && !empty($wpdb->last_error))
				{
					$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
				}
				
				if( isset($get_results[0]) && (date_i18n("Y-m-d",strtotime($get_results[0]->release_time)) < date_i18n("Y-m-d")) ){
					$this->piereg_delete_authentication();
					$get_results = $wpdb->get_results($wpdb->prepare("SELECT * FROM `".$table_name."` WHERE `user_ip` = %s AND `attempt_from` = 'is_login';",$user_ip));
					if(isset($wpdb->last_error) && !empty($wpdb->last_error))
					{
						$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
					}
				}
				$is_security_captcha = 0;
				/* is security captcha enable*/
				if(isset($option['security_captcha_attempts_login']) && intval($option['security_captcha_attempts_login']) > 0 && empty($get_results)){
					$is_security_captcha = 1;
				}
				if(isset($get_results[0]->is_security_captcha) && $get_results[0]->is_security_captcha == 1){
					
					if( intval($get_results[0]->login_attempt) < (intval($option['security_captcha_attempts_login']) - 1) ){
						$login_attempt = intval($get_results[0]->login_attempt) + 1;
						if(!$wpdb->query($wpdb->prepare("UPDATE `".$table_name."` SET `user_id`=%d, `login_attempt`=%d, `release_time`=%s WHERE `user_ip` = %s AND `attempt_from` = 'is_login'",$user_id,$login_attempt,$release_time,$user_ip)) ){
							$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
						}
						return $is_error;
					}else{
						$login_attempt = 1;
						$is_security_captcha_enable = 2;
						if(!$wpdb->query($wpdb->prepare("UPDATE `".$table_name."` SET `user_id`=%d, `login_attempt`=%d, `is_security_captcha`=%d, `release_time`=%s WHERE `user_ip` = %s AND `attempt_from` = 'is_login'",$user_id,$login_attempt,$is_security_captcha_enable,$release_time,$user_ip)) ){
							$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
						}
						return $is_error;
					}
				}
				
				if($option['captcha_in_login_value'] == 1 && $option['captcha_in_login_attempts'] > 0 && $option['piereg_security_attempts_login_value'] == 1 && $this->piereg_pro_is_activate){
					$option['security_attempts_login'] = $option['security_attempts_login'] + $option['captcha_in_login_attempts'];
				}
				if(!empty($get_results)){
					if( intval($get_results[0]->login_attempt) < intval($option['security_attempts_login']) ){
						$login_attempt = intval($get_results[0]->login_attempt) + 1;
						if(!$wpdb->query($wpdb->prepare("UPDATE `".$table_name."` SET `user_id`=%d, `login_attempt`=%d, `release_time`=%s WHERE `user_ip` = %s AND `attempt_from` = 'is_login'",$user_id,$login_attempt,$release_time,$user_ip)) ){
							$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
						}
					}elseif( isset($option['piereg_security_attempts_login_value']) && $option['piereg_security_attempts_login_value'] == 0 ){
						
						$login_attempt = intval($get_results[0]->login_attempt) + 1;
						if(!$wpdb->query($wpdb->prepare("UPDATE `".$table_name."` SET `user_id`=%d, `login_attempt`=%d, `release_time`=%s WHERE `user_ip` = %s AND `attempt_from` = 'is_login'",$user_id,$login_attempt,$release_time,$user_ip)) ){
							$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
						}
					}else{
						$release_time = $get_results[0]->release_time;
						$current_time = date_i18n("Y-m-d H:i:s");
						if($current_time >= $release_time){
							$release_time = date_i18n('Y-m-d H:i:s', strtotime( ('+'.intval($option['security_attempts_login_time'])." minutes"), strtotime(date("Y-m-d H:i:s"))));
							if(!$wpdb->query($wpdb->prepare("UPDATE `".$table_name."` SET `user_id`=%d, `release_time`=%s WHERE `user_ip` = %s AND `attempt_from` = 'is_login'",$user_id,$release_time,$user_ip)) ){
								$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
							}
							return $is_error;
						}else{
							return false;
						}
					}
					
				}else{
					
					if(!$wpdb->query($wpdb->prepare("INSERT INTO `".$table_name."` (`user_id`, `login_attempt`, `attempt_from`, `is_security_captcha`, `attempt_time`, `release_time`, `user_ip`) VALUES (%d,%d,%s,%d,%s,%s,%s);",$user_id,1,'is_login',$is_security_captcha,date_i18n("Y-m-d H:i:s"),$release_time,$user_ip)) ){
							$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
						}
					return $is_error;
				}
			}
			return true;
		}
		function piereg_delete_authentication(){
			global $wpdb;
			$table_name = $wpdb->prefix . "pieregister_lockdowns";
			$user_ip = $_SERVER['REMOTE_ADDR'];
			$user_ip = esc_sql($user_ip);
			$wpdb->query($wpdb->prepare("DELETE FROM `".$table_name."` WHERE `user_ip` = %s AND `attempt_from` = 'is_login'",$user_ip));
			if(isset($wpdb->last_error) && !empty($wpdb->last_error)){
				$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
			}
			return true;
		}
		function piereg_validate_user_expiry_period_func($user)
		{
			/*
				*	Check Payment Addons
			*/
			
			$is_active = get_user_meta($user->ID,"active",true);
			if( ($this->check_enable_payment_method()) == "true" && $is_active == 1)
			{
				global $wpdb, $wp_session,$errors,$pr_wp_db_prefix;
				$datediff = (strtotime(date_i18n("Y-m-d H:m:s"))) - (strtotime($user->data->user_registered)); // current time - registeration time
				$datediff = floor($datediff/(86400));//60*60*24 = 86400
				$pie_reg = get_option(OPTION_PIE_REGISTER);
				$user_id = $user->ID;
				$pricing_activation_cycle = 0;
				$user_payment_amount = "";
				$payment_activation_cycle = 0;
					
				$piereg_pricing_key_number = get_user_meta( $user_id , "piereg_pricing_key_number" , true );
				$piereg_pricing_key_number = intval($piereg_pricing_key_number);
				
				$user_registered_form_id = get_user_meta($user_id, "user_registered_form_id" , true);
				$piereg_form_pricing_fields = get_option("piereg_form_pricing_fields");
				$piereg_use_starting_period = get_user_meta( $user_id , "use_starting_period" );
				
				if(isset($piereg_form_pricing_fields["form_id_".(intval($user_registered_form_id))]) && !empty($piereg_form_pricing_fields["form_id_".(intval($user_registered_form_id))]) && empty($piereg_use_starting_period)){
					
					$piereg_pricing_fields = $piereg_form_pricing_fields["form_id_".(intval($user_registered_form_id))];
					
					$pricing_for = $piereg_pricing_fields['for'][$piereg_pricing_key_number];
					$pricing_for = intval($pricing_for);
					$pricing_for_period = $piereg_pricing_fields['for_period'][$piereg_pricing_key_number];
					
					if(date_i18n('Y-m-d H:i:s', strtotime('+'.$pricing_for.' '.$pricing_for_period, strtotime($user->data->user_registered))) < date_i18n('Y-m-d H:i:s')){
						update_user_meta( $user->ID, 'use_starting_period', date_i18n('Y-m-d H:i:s'));
						$errors->add('login-error',__("your account has been suspended, Please renew your account","piereg"));
						update_user_meta( $user->ID, 'active', 0);
						$this->wp_mail_send($user->data->user_email,"user_temp_blocked_notice");
						$this->wp_mail_send($user->data->user_email,'user_renew_temp_blocked_account_notice');
						wp_logout();
					}
				}
				else{
					if(isset($piereg_form_pricing_fields["form_id_".(intval($user_registered_form_id))]['activation_cycle']) && ((int)($piereg_form_pricing_fields["form_id_".(intval($user_registered_form_id))]['activation_cycle'])) > 0)
					{
						$payment_activation_cycle = intval($piereg_form_pricing_fields["form_id_".(intval($user_registered_form_id))]['activation_cycle']);
					}else{
						$payment_activation_cycle = intval($pie_reg['payment_setting_activation_cycle']);
					}
				}
				
				if( ($payment_activation_cycle) != 0)
				{
					$notice_period = ((int)$payment_activation_cycle) - ((int)$pie_reg['payment_setting_expiry_notice_days']);
					if( $datediff <= ((int)$payment_activation_cycle) )
					{
						$daysdiff = (intval($payment_activation_cycle) - $datediff);
						if($daysdiff <= intval($pie_reg['payment_setting_expiry_notice_days']))
						{
							$last_date = ( strtotime(date_i18n("Y-m-d H:m:s")) + (86400 * ( ((int)$payment_activation_cycle) - $datediff ) ) );
							$last_date = date_i18n("Y-m-d",$last_date);
							$email_variable = array();
							$email_variable['user_last_date'] = $last_date;
							$this->wp_mail_send($user->data->user_email,"user_expiry_notice","","",$email_variable);
						}
					}
					else
					{
						$errors->add('login-error',__("Your account has been blocked","piereg"));
						update_user_meta( $user->ID, 'active', 0);
						$this->wp_mail_send($user->data->user_email,"user_temp_blocked_notice");
						$this->wp_mail_send($user->data->user_email,'user_renew_temp_blocked_account_notice');
						wp_logout();
						
					}
				}
			}
		}
		
		//Add the Settings and User Panels
		function AddPanel()
		{
			$update = get_option(OPTION_PIE_REGISTER);
			
			$pie_page_suffix_1 = add_object_page( "Pie Register", __('Pie Register',"piereg") , 'manage_options', 'pie-register',  array($this,'PieRegNewForm'), plugins_url("/images/pr_icon.png",__FILE__) );
			add_action('admin_print_scripts-' . $pie_page_suffix_1 , array($this,'pieregister_admin_scripts_styles'));
			
			$pie_page_suffix_2 = add_submenu_page( 'pie-register', 'Manage Forms', __('Manage Forms',"piereg") , 'manage_options', 'pie-register', array($this, 'PieRegNewForm'));
			add_action('admin_print_scripts-' . $pie_page_suffix_2 , array($this,'pieregister_admin_scripts_styles'));
			
			do_action("piereg_add_addon_menu_profile_search");
			
			$pie_page_suffix_3 = add_submenu_page( 'admin.php?page=pie-register', 'Manage Forms', __('New Form',"piereg") , 'manage_options', 'pr_new_registration_form', array($this, 'RegPlusEditForm') );
			add_action('admin_print_scripts-' . $pie_page_suffix_3 , array($this,'pieregister_ck_admin_scripts_styles'));
			
			$pie_page_suffix_14 = add_submenu_page( 'pie-register', 'Notifications Setting', __('Notifications',"piereg") , 'manage_options', 'pie-notifications', array($this, 'PieRegNotifications') );
			add_action('admin_print_scripts-' . $pie_page_suffix_14 , array($this,'pieregister_ck_admin_scripts_styles'));
						
			$pie_page_suffix_9 = add_submenu_page( 'pie-register', 'Invitation Codes', __('Invitation Codes',"piereg") , 'manage_options', 'pie-invitation-codes', array($this, 'PieRegInvitationCodes'));
			add_action('admin_print_scripts-' . $pie_page_suffix_9 , array($this,'pieregister_admin_scripts_styles'));
			
			if(	is_plugin_active('pie-register-aweber/pie-register-aweber.php') || is_plugin_active('pie-register-mailchimp/pie-register-mailchimp.php') )
			{
				// Restrict Users by username and ip address
				$pie_page_suffix_16 = add_submenu_page( 'pie-register', 'Bulk Email', __('Bulk Email',"piereg") , 'manage_options', 'pie-bulk-emails', array($this, 'PieRegBulkEmails'));
				add_action('admin_print_scripts-' . $pie_page_suffix_16, array($this,'pieregister_rw_admin_scripts_styles'));
			}
			
			/*
				*	Add Add-ons menu in dashboard
			*/
			do_action("piereg_add_addons_menu");
			
			$pie_page_suffix_5 = add_submenu_page( 'pie-register', 'Payment Gateways Setting', __('Payment Gateways',"piereg") , 'manage_options', 'pie-gateway-settings', array($this, 'PieRegPaymentGateway') );
			add_action('admin_print_scripts-' . $pie_page_suffix_5 , array($this,'pieregister_admin_scripts_styles'));
			
			// Restrict Users by username and ip address
			if( $this->piereg_pro_is_activate )
			{
				$pie_page_suffix_15 = add_submenu_page( 'pie-register', 'Block Users', __('Block Users',"piereg") , 'manage_options', 'pie-black-listed-users', array($this, 'PieRegRestrictUsers'));
				add_action('admin_print_scripts-' . $pie_page_suffix_15, array($this,'pieregister_rw_admin_scripts_styles'));
			}
				
			$pie_page_suffix_17 = add_submenu_page( 'pie-register', 'Settings', __('Settings',"piereg") , 'manage_options', 'pie-settings', array($this, 'PieRegSettings') );
			add_action('admin_print_scripts-' . $pie_page_suffix_17 , array($this,'pieregister_rw_admin_scripts_styles'));
			
			$pie_page_suffix_8 = add_submenu_page( 'pie-register', 'Import/Export', __('Import/Export',"piereg") , 'manage_options', 'pie-import-export', array($this, 'PieRegImportExport'));
			add_action('admin_print_scripts-' . $pie_page_suffix_8 , array($this,'pieregister_admin_scripts_styles'));
			
			$pie_page_suffix_11 = add_users_page( 'Unverified Users', __('Unverified Users',"piereg") , 'manage_options', 'unverified-users', array($this, 'Unverified') );
			add_action('admin_print_scripts-' . $pie_page_suffix_11 , array($this,'pieregister_admin_scripts_styles'));
			
			$pie_page_suffix_12 = add_submenu_page( 'pie-register', 'Help', __('Help',"piereg") , 'manage_options', 'pie-help', array($this, 'PieRegHelp'));
			add_action('admin_print_scripts-' . $pie_page_suffix_12, array($this,'pieregister_admin_scripts_styles'));
			
			do_action('pie_register_add_menu');
		}
		function pieregister_admin_scripts_styles(){
			//$this->pie_admin_enqueu_scripts();
		}
		function pieregister_ck_admin_scripts_styles(){
			//$this->pie_admin_enqueu_scripts();
			
			wp_enqueue_script("pie_ckeditor");
		}
		function pieregister_rw_admin_scripts_styles(){
			//$this->pie_admin_enqueu_scripts();
			wp_enqueue_style("pie_restrict_widget_css" );
			wp_enqueue_script("pie_restrict_widget_script");
		}
		
		function saveFields()
		{
			$this->piereg_get_wp_plugable_file(true); // require_once pluggable.php
			if(isset($_POST['piereg_reg_form_nonce']) && wp_verify_nonce( $_POST['piereg_reg_form_nonce'], 'piereg_wp_reg_form_nonce' ))
			{
				$math_cpatcha_enable 		= "false";
				$piereg_startingDate 		= "1901";
				$piereg_endingDate 			= date_i18n("Y");
				$piereg_form_pricing_fields = get_option("piereg_form_pricing_fields");
				
				$piereg_form_pricing_fields_temp = array();				
				//echo "<pre>"; print_r($_POST['field']); die;
				
				foreach($_POST['field'] as $k=>$fv){
					
					if($fv['type'] == 'html'){
						$fv['html'] = htmlentities(stripslashes($fv['html']), ENT_QUOTES | ENT_IGNORE, "UTF-8");
					}
					if($fv['type'] == 'math_captcha'){
						$math_cpatcha_enable = "true";
					}
					
					//since 2.0.12					
					if($fv['type'] == 'password'){
						$meter_label_options = get_option(OPTION_PIE_REGISTER);			
						
						$meter_label_options['pass_strength_indicator_label'] 	= $fv['pass_strength_indicator_label'];
						$meter_label_options['pass_very_weak_label'] 			= $fv['pass_very_weak_label'];
						$meter_label_options['pass_weak_label'] 				= $fv['pass_weak_label'];
						$meter_label_options['pass_medium_label'] 				= $fv['pass_medium_label'];
						$meter_label_options['pass_strong_label'] 				= $fv['pass_strong_label'];
						$meter_label_options['pass_mismatch_label'] 			= $fv['pass_mismatch_label'];
						
						update_option(OPTION_PIE_REGISTER, $meter_label_options );
						PieReg_Base::set_pr_global_options(OPTION_PIE_REGISTER, $meter_label_options );
					}
					
						if($fv['type'] != 'form')
						{
							if(isset($fv['conditional_value'])) {								
								//$fv['conditional_value'] 	= (htmlentities(stripslashes($fv['conditional_value']), ENT_QUOTES | ENT_IGNORE, "UTF-8"));
								$fv['conditional_value'] 	= preg_replace('/[^a-zA-Z-0-9,. ]/', '', $fv['conditional_value']);
							}
						}
						
						
						if(isset($fv['desc'])) {
							$fv['desc'] 	= htmlspecialchars(str_replace(array('\/','\\'),'',stripslashes($fv['desc'])));
						}
						
						if(isset($fv['label'])) {
							$fv['label'] 	= htmlspecialchars(str_replace(array('\/','\\'),'',stripslashes($fv['label'])));
						}
						
						if(isset($fv['validation_message'])) {
							$fv['validation_message'] = htmlspecialchars(str_replace(array('\/','\\'),'',stripslashes($fv['validation_message'])));
						}
						
						
						if(isset($fv['css'])) {
							$fv['css'] = htmlspecialchars(str_replace(array('\/','\\'),'',stripslashes($fv['css'])));
						}
						 
						if(isset($fv['default_value'])) {
							$fv['default_value'] = htmlspecialchars(str_replace(array('\/','\\'),'',stripslashes($fv['default_value'])));
						}
						
						if(isset($fv['placeholder'])) {
							$fv['placeholder'] = htmlspecialchars(str_replace(array('\/','\\'),'',stripslashes($fv['placeholder'])));
						}
					
					if($fv['type'] == 'date'){
						$pattern = '/[0-9]{4}/';
						$subject = isset($fv['startingDate']) ? $fv['startingDate'] : "";
						if(
							( (isset($fv['startingDate']) && strlen($fv['startingDate']) == 4) && preg_match($pattern, $subject))&&
							(intval($fv['startingDate']) <= intval($fv['endingDate']))
						  ){
							$fv['startingDate'] = $fv['startingDate'];
							$piereg_startingDate = $fv['startingDate'];
						}
						else{
							$fv['startingDate'] = "1901";
							$piereg_startingDate = "1900";
						}
							
						$subject = isset($fv['endingDate']) ? $fv['endingDate'] : "";
						if(
						   ( (isset($fv['endingDate']) && strlen($fv['endingDate']) == 4) && preg_match($pattern, $subject)) && 
						   (intval($fv['endingDate']) >= intval($fv['startingDate']))
						   ){
							$fv['endingDate'] = $fv['endingDate'];
							$piereg_endingDate = $fv['endingDate'];
						}
						else{
							$fv['endingDate'] = date_i18n("Y");
							$piereg_endingDate = date_i18n("Y");
						}
					}
					
					if($fv['type'] == "pricing" && "not" == "required" )
					{
						$piereg_form_pricing_fields_temp = $fv;
						
						foreach($fv['starting_price'] as $starting_price_key=>$starting_price_val)
						{
							$fv['starting_price'][$starting_price_key] = sprintf('%0.2f', $starting_price_val);
						}
						
						foreach($fv['then_price'] as $then_price_key=>$then_price_val)
						{
							$fv['then_price'][$then_price_key] = sprintf('%0.2f', $then_price_val);
						}
						
					}
					$updated_post[$k] = $fv;
				}
				
				if(!$_POST['field'])
						$_POST['field'] =  get_option( 'pie_fields_default' );
				
				do_action("pie_fields_save");
				update_option("pie_fields",serialize($updated_post));
				
				/*modify code for multiple registration form*/
				if(isset($_POST['form_id']) and intval(base64_decode($_POST['form_id'])) != 0 and isset($_POST['page']) and $_POST['page'] == "edit")
				{
					$fields_id = intval(base64_decode($_POST['form_id']));
					
					// update fields
					update_option("piereg_form_fields_".$fields_id,serialize($updated_post));
					$piereg_form_pricing_fields["form_id_".$fields_id] = $piereg_form_pricing_fields_temp;
					update_option("piereg_form_pricing_fields",$piereg_form_pricing_fields);
					
					// update user role
					$options = get_option(OPTION_PIE_REGISTER);
					$options['pie_regis_set_user_role_'.$fields_id] = $_POST['set_user_role_'];
					$options['piereg_startingDate_'.$fields_id] = $piereg_startingDate;
					$options['piereg_endingDate_'.$fields_id] = $piereg_endingDate;
					
					// sync form fields with profile search forms
					$pie_ps_form_id = isset($options['piereg_profile_search_form_id']) ? $options['piereg_profile_search_form_id'] : "";
					if(!empty($pie_ps_form_id) && $pie_ps_form_id == $fields_id){
						
						$match_form_fields = array();
						foreach($updated_post as $updated_posts){							
							if( !isset($updated_posts['label']) )
								$updated_posts['label'] = "";
								
							if($updated_posts['type'] == 'name'){
								$first_name = strtolower(str_replace(' ' , '_', $updated_posts['label']));
								$match_form_fields[$first_name.'+:+'.$updated_posts['label']] = $updated_posts['label'];
								$last_name = strtolower(str_replace(' ' , '_', $updated_posts['label2']));
								$match_form_fields[$last_name.'+:+'.$updated_posts['label']] = $updated_posts['label'];
							}elseif($updated_posts['type'] == 'username'){
								$username = str_replace(' ' , '_', $updated_posts['type']);
								$match_form_fields[$username.'+:+'.$updated_posts['label']] = $updated_posts['label'];
							}elseif($updated_posts['type'] == 'email'){
								$email = str_replace(' ' , '_', $updated_posts['type']);
								$match_form_fields[$email.'+:+'.$updated_posts['label']] = $updated_posts['label'];
							}elseif($updated_posts['type'] != 'math_captcha' && $updated_posts['type'] != 'captcha' && $updated_posts['type'] != 'password' && $updated_posts['type'] != 'form' && $updated_posts['type'] != 'submit' && $updated_posts['type'] != 'upload'){
								$other_field = 'pie_'.str_replace(' ' , '_', $updated_posts['type']).'_'.$updated_posts['id'];
								$match_form_fields[$other_field.'+:+'.$updated_posts['label']] = $updated_posts['label'];
							}							
						}
						
						foreach($options['piereg_ps_psv_fields_label'] as $ps_fields_array){
							if(!empty($ps_fields_array)){
								foreach($ps_fields_array as $key_array=>$ps_fields_arr){
									foreach($ps_fields_arr as $key=>$ps_fields){
										if($key != 'full_name' && $key != 'registered'){
											$key_check = $key.'+:+'.$ps_fields;
											if(!array_key_exists($key_check, $match_form_fields)){
												unset($ps_fields_arr[$key]);
											}
										}
									}
									$ps_fields_ar[$key_array] = $ps_fields_arr;
								}
							}
						}
						
						if(!empty($ps_fields_ar)){
							if( empty($ps_fields_ar[1]) && array_key_exists(1, $ps_fields_ar) ){
								unset($ps_fields_ar[1]);
							}elseif( empty($ps_fields_ar[2]) && array_key_exists(2, $ps_fields_ar) ){
								unset($ps_fields_ar[2]);
							}elseif( empty($ps_fields_ar[3]) && array_key_exists(3, $ps_fields_ar) ){
								unset($ps_fields_ar[3]);
							}
							$ps_fields_a[$pie_ps_form_id] = $ps_fields_ar;
							$options['piereg_ps_psv_fields_label'] = $ps_fields_a;
						}
					}
					
					// Update Wordpress Drefault User Role
					update_option(OPTION_PIE_REGISTER,$options);
					//update form title
					$_field['Id'] = esc_sql( $fields_id );
					$_field = get_option("piereg_form_field_option_".$fields_id);
					$_field['Title'] = esc_sql( $_POST['field']['form']['label'] );
					update_option("piereg_form_field_option_".$fields_id,$_field);
				}
				else{
					$fields_id = get_option("piereg_form_fields_id");
					$fields_id = ((int)$fields_id)+1;
					update_option("piereg_form_fields_id",$fields_id);
					
					update_option("piereg_form_fields_".$fields_id,serialize($_POST['field']));
					
					$piereg_form_pricing_fields["form_id_".$fields_id] = $piereg_form_pricing_fields_temp;
					update_option("piereg_form_pricing_fields",$piereg_form_pricing_fields);
					
					$options = get_option(OPTION_PIE_REGISTER);
					$options['pie_regis_set_user_role_'.$fields_id] = esc_sql( $_POST['set_user_role_'] );
					$options['piereg_startingDate_'.$fields_id] = $piereg_startingDate;
					$options['piereg_endingDate_'.$fields_id] = $piereg_endingDate;
	
					// Update Wordpress Drefault User Role
					update_option(OPTION_PIE_REGISTER,$options);
					$_field['Id'] = $fields_id;
					$_field['Title'] = $_POST['field']['form']['label'];
					$_field['Views'] = "0";
					$_field['Entries'] = "0";
					$_field['Status'] = "enable";
					add_option("piereg_form_field_option_".$fields_id,$_field);
					wp_redirect("admin.php?page=pie-register");	
				}
				$_POST['success_message'] = __("Settings Saved","piereg");
			}
			else{
				$_POST['error_message'] = __("Sorry, your nonce did not verify","piereg");
			}
		}
		
		function pieregister_login()
		{
			$option = get_option(OPTION_PIE_REGISTER);					
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
		function process_register_form()
		{
			
			global $errors;
		
			$form 		= new Registration_form();
			$success 	= '' ;	
			
			get_header();
			//Printing Success Message
			echo $this->outputRegForm("true","true");
			$this->set_pr_stats("register","view");
			get_footer();	
			
			exit;
		}
		
		function check_register_form()
		{
			global $errors, $wp_session;
			$option = get_option(OPTION_PIE_REGISTER);	
			if(
			   		($this->check_enable_payment_method()) == "false" || 
					($this->check_enable_payment_method() == "true" && isset($option ['enable_paypal']) && $option ['enable_paypal'] == 1 ) // until multiple payment gateways release
				)
			{
				$this->pie_save_registration();
			}
			else if(($this->check_enable_payment_method()) == "true")
			{
				$pricing_key_number = 0;
				$pricing_payment_amount = 0;
				if(isset($_POST['pricing'])){
					$piereg_form_pricing_fields = get_option("piereg_form_pricing_fields");
					foreach($piereg_form_pricing_fields['form_id_'.intval($_POST['form_id'])]['display'] as $pricing_key=>$pricing_value){
						if($pricing_value == $_POST['pricing']){
							$pricing_key_number = $pricing_key;
							break;
						}
					}
					$pricing_payment_amount = $piereg_form_pricing_fields['form_id_'.intval($_POST['form_id'])]["starting_price"][$pricing_key_number];
				}
				
				if(
				   	(isset($_POST['select_payment_method']) and trim($_POST['select_payment_method']) != "" and $_POST['select_payment_method'] != "select") ||
					($pricing_payment_amount <= 0)
				  )
				{
					$this->pie_save_registration();
				}
				else
				{
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
			$form_id = 0;
			if(isset($_POST['form_id']) && intval($_POST['form_id']) > 0)
			{
				$form_id = intval($_POST['form_id']);
			}
			else{
				global $errors;
				$errors = new WP_Error();
				$errors->add("registration-error","Invalid Form");
				return false;
			}
			
			$this->piereg_generate_username(esc_sql($_POST['e_mail']), apply_filters("piereg_generate_unique_username",false), apply_filters("piereg_generate_username_with_prifex",false));
			
			add_filter('wp_mail_content_type', array($this,'set_html_content_type'));
			global $errors;
			$form 		= new Registration_form($form_id);
			$errors 	= $form->validateRegistration($errors);
			$option 	= get_option(OPTION_PIE_REGISTER);
			
			do_action('pie_register_before_register_validate');	
			
			$option_user_verification = $option['verification'];
			
			$form_user_verification = $form->data['form']['user_verification'];
			if(intval($form_user_verification) > 0)
			{
				$option_user_verification = intval($form_user_verification);
			}
			
			if(isset($form->data['form']['conditional_logic']) && $form->data['form']['conditional_logic'] == 1 && $this->piereg_pro_is_activate){
				$verify_notification = false;
				foreach($form->data['form']['notification'] as $cl_key=>$cl_val){
						
					$form_selected_field = ((isset($_POST[$form->data['form']['selected_field'][$cl_key]]) && !empty($_POST[$form->data['form']['selected_field'][$cl_key]]))?$_POST[$form->data['form']['selected_field'][$cl_key]]: "");
					
					$form_notification = ((isset($form->data['form']['notification'][$cl_key]) && !empty($form->data['form']['notification'][$cl_key]))?$form->data['form']['notification'][$cl_key]: "");
					
					$form_field_rule_operator = ((isset($form->data['form']['field_rule_operator'][$cl_key]) && !empty($form->data['form']['field_rule_operator'][$cl_key]))?$form->data['form']['field_rule_operator'][$cl_key]: "");
					
					$form_conditional_value = ((isset($form->data['form']['conditional_value'][$cl_key]) && !empty($form->data['form']['conditional_value'][$cl_key]))?$form->data['form']['conditional_value'][$cl_key]: "");
					
					switch($form_field_rule_operator){
						case "==":
							if($form_selected_field == $form_conditional_value)
							{
								$verify_notification = true;
							}
						break;
						case "!=":
							if($form_selected_field != $form_conditional_value)
							{
								$verify_notification = true;
							}
						break;
						case "empty":
							if($form_selected_field == "")
							{
								$verify_notification = true;
							}
						break;
						case "not_empty":
							if($form_selected_field != "")
							{
								$verify_notification = true;
							}
						break;
						case ">":
							if($form_selected_field > $form_conditional_value)
							{
								$verify_notification = true;
							}
						break;
						case "<":
							if($form_selected_field < $form_conditional_value)
							{
								$verify_notification = true;
							}
						break;
						case "contains":
							if( (!empty($form_selected_field) && !empty($form_conditional_value)) && strpos($form_selected_field , $form_conditional_value) >= 0)
							{
								$verify_notification = true;
							}
						break;
						case "starts_with":
							if( (!empty($form_selected_field) && !empty($form_conditional_value)) && strpos($form_selected_field , $form_conditional_value) === 0)
							{
								$verify_notification = true;
							}
						break;
						case "ends_with":
							if(substr($form_selected_field , -strlen($form_conditional_value)) === $form_conditional_value)
							{
								$verify_notification = true;
							}
						break;
					}
					if($verify_notification == true)
					{
						break;
					}
				}
				
				if($verify_notification == true && intval($form_notification) > 0 )
				{
					$option_user_verification = $form_notification;
				}
			}
			
			
			if(sizeof($errors->errors) == 0)
			{
				do_action('pie_register_after_register_validate');	
								 
				//Inserting User
				$pass = html_entity_decode($_POST['password']);
				$user_data = array(
								   'user_pass' 	=> $pass,
								   'user_login' => $_POST['username'],
								   'user_email' => $_POST['e_mail'],
								   'role' 		=> get_option('default_role')
								  );
				
				if(isset($_POST['first_name']) && !empty($_POST['first_name']) )
				{
					$display_name = $_POST['first_name'].((isset($_POST['last_name']) && !empty($_POST['last_name']))?" ".$_POST['last_name']:"");
					$user_data['display_name'] 	= $display_name;
					$user_data['user_nicename'] = $display_name;
				}
				
				if(isset($_POST['url']))
				{
					$user_data["user_url"] =  $_POST['url'];
				}
				
				$this->set_pr_stats("register","used");
				$user_id = wp_insert_user( $user_data );
				
				/*
					*	Check Pricing
				*/
				
				$pricing_user_role = "";
				$pricing_key_number = 0;
				$pricing_payment_amount = 0;
				$pricing_activation_cycle = 0;
				$piereg_form_pricing_fields = "";
				
				if(isset($_POST['pricing'])){
					$piereg_form_pricing_fields = get_option("piereg_form_pricing_fields");
					foreach($piereg_form_pricing_fields['form_id_'.intval($form_id)]['display'] as $pricing_key=>$pricing_value){
						if($pricing_value == $_POST['pricing']){
							$pricing_key_number = $pricing_key;
							break;
						}
					}
					$pricing_user_role = $piereg_form_pricing_fields['form_id_'.intval($form_id)]["role"][$pricing_key_number];
					$pricing_payment_amount = $piereg_form_pricing_fields['form_id_'.intval($form_id)]["starting_price"][$pricing_key_number];
					$pricing_activation_cycle = ( ( intval($piereg_form_pricing_fields['form_id_'.intval($form_id)]["activation_cycle"][$pricing_key_number]) >= 0 )? intval($piereg_form_pricing_fields['form_id_'.intval($form_id)]["activation_cycle"][$pricing_key_number]) : intval($option['payment_setting_activation_cycle']) );
				}else{
					$pricing_payment_amount = $option['payment_setting_amount'];
					$pricing_activation_cycle = (isset($option['payment_setting_activation_cycle'])) ? $option['payment_setting_activation_cycle'] : "";
				}
				
				$this->save_pricing_cycle_options($user_id,$pricing_key_number,$form_id,$piereg_form_pricing_fields);
				$piereg_pricing_cycle_data = get_user_meta($user_id, "piereg_pricing_cycle_data", true);
				/***** End Pricing *****/
				
				$form->addUser($user_id);
				if(isset($_POST['first_name']) && !empty($_POST['first_name']) )
				{
					$display_name = $_POST['first_name'].((isset($_POST['last_name']) && !empty($_POST['last_name']))?" ".$_POST['last_name']:"");
					$user_data['display_name'] 	= $display_name;
					$user_data['user_nicename'] = $display_name;
				}
				/*
					*	Update Nickname User Meta
				*/
				if(isset($_POST['first_name']) && !empty($_POST['first_name']) )
				{
					update_user_meta( $user_id, 'nickname', ($_POST['first_name'] . ((isset($_POST['last_name']) && !empty($_POST['last_name'])) ? " " . $_POST['last_name'] : "" )) );
				}
				$new_role = 'subscriber';
				if( intval($_POST['form_id']) != 0)
				{
					$new_role = strtolower($option['pie_regis_set_user_role_'.intval($form_id)]);
					$new_role = ( $new_role != "") ? $new_role : 'subscriber'; 
					$form_options = get_option("piereg_form_field_option_".intval($form_id));
					$form_options['Entries'] = esc_sql( intval($form_options['Entries']) + 1 );
					update_option("piereg_form_field_option_".intval($form_id),$form_options);
					unset($form_options);
				}
				else
				{
					if(isset($option['pie_regis_set_user_role_']) and trim($option['pie_regis_set_user_role_']) != "")
					{
						$new_role = strtolower($option['pie_regis_set_user_role_']);
						$new_role = ( $new_role != "") ? $new_role : 'subscriber';
					}
				}
				/*
					*	Add Pricing Role
				*/
				$new_role = ( ( isset($pricing_user_role) && !empty($pricing_user_role) ) ? trim($pricing_user_role) : $new_role );
				//// update user role using wordpress function
				wp_update_user( array ('ID' => $user_id, 'role' => $new_role ) ) ;
				update_user_meta( $user_id, "is_social", "false", $unique = false );
				update_user_meta( $user_id, "social_site_name", "", $unique = false );
				update_user_meta( $user_id, "user_registered_form_id", ((int)$form_id) );
				/*
					* User Meta for Pricing
				*/
				update_user_meta( $user_id, "piereg_pricing_key_number", $pricing_key_number );
				update_user_meta( $user_id, "piereg_pricing_payment_amount", $pricing_payment_amount );
				update_user_meta( $user_id, "piereg_pricing_user_role", $pricing_user_role );
				update_user_meta( $user_id, "piereg_pricing_activation_cycle", $pricing_activation_cycle );
				
				$user 		= new WP_User($user_id);
				
				/*
					*	Add pricing variables in user object
				*/
				$user_array = (array) $user;
				$user_array['piereg_pricing']['pricing_key_number'] = $pricing_key_number;
				$user_array['piereg_pricing']['pricing_payment_amount'] = $pricing_payment_amount;
				$user = (object) $user_array;
				
				
				
				do_action('pie_register_after_register_validate',$user);
				///////////////////////////////////////////////////
				/******** Admin Notification *******/
				$this->send_admin_notifications($option,$user,$pass);
				////////////////////////////////////////////////////
				
				// until multiple payment gateways release we use this variable to get away with multiple gateway process
				$isPayPalStandard = false;
				if( isset($_POST['select_payment_method']) && $_POST['select_payment_method'] == 'PaypalStandard'  ) {
					$isPayPalStandard = true;
				}
								
				if( 'recurring' == 'enable' )
					$checkStartPayment = true;
				else 
					$checkStartPayment = false;
				
				if( $isPayPalStandard == false && isset($user_array['piereg_pricing']['pricing_payment_amount']) && floatval($user_array['piereg_pricing']['pricing_payment_amount']) <= 0){
					$checkStartPayment = false;
				}
				
				/*Goto payment method Like check_payment_method_paypal*/
				
				if( $isPayPalStandard == false && isset($_POST['select_payment_method']) && ($_POST['select_payment_method'] != "" || $_POST['select_payment_method'] != "select") )
				{
					if($checkStartPayment){
						update_user_meta( $user_id, 'register_type', "payment_verify");
						$_POST['user_id'] = $user_id;
						update_user_meta( $user_id, 'active', 0);
						/*
							*	trigger Wordpress "user_register" hook
						*/
						do_action("user_register",$user_id);
						do_action("check_payment_method_".$_POST['select_payment_method'],$user);// function prefix check_payment_method_
					}
				}
				
				
				if( $isPayPalStandard == false && $this->check_enable_payment_method() == "true" && ($checkStartPayment) )
				{
					if( (($this->check_enable_payment_method()) == "true" and !isset($_POST['select_payment_method']) and isset($_POST['pricing']) ) or
						( isset($_POST['select_payment_method']) and $_POST['select_payment_method'] == "" or $_POST['select_payment_method'] == "select")
					  )
					{
						$_POST['error'] = __("please select any payment method","piereg");
					}
				}
				else if( $isPayPalStandard == true && (!(empty($option['paypal_butt_id'])) && $option['enable_paypal']==1) &&
                                        ($option['enable_authorize_net'] != 1 &&
                                        $option['enable_2checkout'] != 1 &&
                                        $option['enable_PaypalPro'] != 1 &&
                                        $option['enable_PaypalExp'] != 1 &&
                                        $option['enable_Skrill'] != 1)
                                        )
				{
					$_POST['user_id'] = $user_id;
					update_user_meta( $user_id, 'active', 0);
					update_user_meta( $user_id, 'register_type', "payment_verify");
					do_action("check_payment_method_paypal",$user);// function prefix check_payment_method_
				}
				else if($option_user_verification == 1 )//Admin Verification
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
					
					if(!wp_mail($_POST['e_mail'], $subject, $message , $headers)){
						$this->pr_error_log("'The e-mail could not be sent. Possible reason: your host may have disabled the mail() function...'".($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
					}
					$_POST['registration_success'] = apply_filters("piereg_thank_you_for_your_registration",__("Thank you for your registration. You will be notified once the admin approves your account.",'piereg'));
				}
				
				else if($option_user_verification == 2 )//E-Mail Link Verification
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
								
					if(!wp_mail($_POST['e_mail'], $subject, $message , $headers)){
						$this->pr_error_log("'The e-mail could not be sent. Possible reason: your host may have disabled the mail() function...'".($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
					}
					$_POST['registration_success'] = apply_filters("piereg_thank_you_for_your_registration",__("Thank you for your registration. An activation link with your password has been sent to you.",'piereg'));
						
				}
				
				else if($option_user_verification == 3 )//Admin & E-Mail Verification
				{
					/*	Admin Verification	*/
					update_user_meta( $user_id, 'active', 0);
					update_user_meta( $user_id, 'register_type', "admin_email_verify");
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
				
					if(!empty($from_email) && filter_var($from_email,FILTER_VALIDATE_EMAIL)){//Validating From
						$headers .= "From: ".$from_name." <".$from_email."> \r\n";
					}
					if($reply_email){
						$headers .= "Reply-To: {$reply_email}\r\n";
						$headers .= "Return-Path: {$from_name}\r\n";
					}else{
						$headers .= "Reply-To: {$from_email}\r\n";
						$headers .= "Return-Path: {$from_email}\r\n";
					}
					
					if(!wp_mail($_POST['e_mail'], $subject, $message , $headers)){
						$this->pr_error_log("'The e-mail could not be sent. Possible reason: your host may have disabled the mail() function...'".($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
					}
					$_POST['registration_success'] = apply_filters("piereg_thank_you_for_your_registration",__("Thank you for your registration. You will be notified once the admin approves your account",'piereg'));
				}
				
				else if($option_user_verification == 0 )//No verification required
				{
					update_user_meta( $user_id, 'active', 1);
					
					$subject 		= html_entity_decode($option['user_subject_email_default_template'],ENT_COMPAT,"UTF-8");
					$message_temp = "";
					if($option['user_formate_email_default_template'] == "0"){
						$message_temp	= (strip_tags($option['user_message_email_default_template']));
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
				
					if(!empty($from_email) && filter_var($from_email,FILTER_VALIDATE_EMAIL)){//Validating From
						$headers .= "From: ".$from_name." <".$from_email."> \r\n";
					}
					if($reply_email){
						$headers .= "Reply-To: {$reply_email}\r\n";
						$headers .= "Return-Path: {$from_name}\r\n";
					}else{
						$headers .= "Reply-To: {$from_email}\r\n";
						$headers .= "Return-Path: {$from_email}\r\n";
					}
							
					if(!wp_mail($_POST['e_mail'], $subject, $message , $headers)){
						$this->pr_error_log("'The e-mail could not be sent. Possible reason: your host may have disabled the mail() function...'".($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
					}
					
				}
				/*
					*	trigger Wordpress "user_register" hook
				*/
				do_action("user_register",$user->ID);
				do_action('pie_register_after_register',$user);
				/*
					*	User Verification and payment methods are off then Trigger Login After Registration Hook
				*/
				
				if($option_user_verification == 0  && $this->check_enable_payment_method() == "false" ){
					do_action('pie_register_login_after_registration',$user);
				}
				
				$fields 			= maybe_unserialize(get_option("pie_fields"));
				$confirmation_type 	= $form->data['submit']['confirmation'];
				
				if( isset($wp_session['payment_error']) && trim($wp_session['payment_error']) != "")
				{
					$_POST['error'] = __($wp_session['payment_error'],"piereg");
					$wp_session['payment_error'] = "";
					$wp_session['payment_sussess'] = "";
				}
				else if( isset($wp_session['payment_sussess']) && trim($wp_session['payment_sussess']) != "")
				{					
					$_POST['registration_success'] = apply_filters("piereg_payment_sussess",__($wp_session['payment_sussess'],"piereg"));
					$wp_session['payment_error'] = "";
					$wp_session['payment_sussess'] = "";
				}
				else if($confirmation_type == "" || $confirmation_type == "text" )
				{
					if(empty($_POST['registration_success']))
						$_POST['registration_success']	= __($form->data['submit']['message'],"piereg");
				}
				else if($confirmation_type == "page")
				{
					wp_safe_redirect(get_permalink($form->data['submit']['page']));
					exit;
				}
				else if($confirmation_type == "redirect")
				{
					wp_redirect($form->data['submit']['redirect_url']);
					exit;
				}
				else
				{
					if(empty($_POST['registration_success']))
						$_POST['registration_success']	= __($form->data['submit']['message'],"piereg");
				}
			}
		}
		
		function save_pricing_cycle_options($user_id,$pricing_key_number,$form_id,$pricing_fields){
			if(empty($pricing_fields))
				$pricing_fields = get_option("piereg_form_pricing_fields");
			
			$pricing_data_array = array();
			$pricing_data_array['starting_price'] 	= isset($pricing_fields["form_id_{$form_id}"]['starting_price'][$pricing_key_number]) ? $pricing_fields["form_id_{$form_id}"]['starting_price'][$pricing_key_number] : "";
			$pricing_data_array['then_price'] 		= isset($pricing_fields["form_id_{$form_id}"]['then_price'][$pricing_key_number]) ? $pricing_fields["form_id_{$form_id}"]['then_price'][$pricing_key_number] : "";
			
			$pay_for 			=  isset($pricing_fields["form_id_{$form_id}"]['for'][$pricing_key_number]) ? $pricing_fields["form_id_{$form_id}"]['for'][$pricing_key_number] : "";
			$pay_for_period		=  isset($pricing_fields["form_id_{$form_id}"]['for_period'][$pricing_key_number]) 	? $pricing_fields["form_id_{$form_id}"]['for_period'][$pricing_key_number] : "";
			
			$period = $this->get_period_by_days_for_payment($pay_for, $pay_for_period);
			
			$pricing_data_array['for'] 				= $period['PERIOD'];
			$pricing_data_array['for_period'] 		= $period['FREQUENCY'];
			
			$pay_activation_cycle	= isset($pricing_fields["form_id_{$form_id}"]['activation_cycle'][$pricing_key_number]) ? $pricing_fields["form_id_{$form_id}"]['activation_cycle'][$pricing_key_number] : "";			
			$period = $this->get_period_by_days_for_payment($pay_activation_cycle);
			
			$pricing_data_array['activation_cycle'] = $period['PERIOD'];
			$pricing_data_array['activation_cycle_frequancy'] = $period['FREQUENCY'];
			
			//Update Pricing Cycle in User Meta
			update_user_meta( $user_id, "piereg_pricing_cycle_data",$pricing_data_array);
			unset($pricing_data_array);
		}
		
		function send_admin_notifications($option,$user,$pass){
			/******** Admin Notification *******/
			if($option['enable_admin_notifications']=="1")
			{
				$message_temp = "";
				if($option['admin_message_email_formate'] == "0"){
					$message_temp	= (strip_tags($option['admin_message_email']));
				}else{
					$message_temp	= $option['admin_message_email'];
				}
				$message  		= $this->filterEmail($message_temp,$user,$pass);
				$subject		= html_entity_decode($option['admin_subject_email'],ENT_COMPAT,"UTF-8");;
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
				$headers  = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
				
				if(!empty($from_email))//Validating From
					$headers .= "From: ".$from_name." <".$from_email."> \r\n";
				
				if(!empty($bcc))//Validating BCC
					$headers .= "Bcc: " . $bcc . " \r\n";
				
				if(!empty($reply_to_email))//Validating Reply To
					$headers .= "Reply-To: <".$reply_to_email."> \r\n";
				
				if($reply_to_email)
					$headers .= "Return-Path: ".$reply_to_email." \r\n";
				else
					$headers .= "Return-Path: ".$from_email." \r\n";
	
				do_action("piereg_action_before_admin_notify_email", $option, $user); # newlyAddedHookFilter
				if(!wp_mail($to,$subject, $message,$headers)){
					$this->pr_error_log("'The e-mail could not be sent. Possible reason: your host may have disabled the mail() function...'".($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
				}
			}
		}
		
		function pie_register_login_after_registration($user = false){
			global $errors, $wp_session;
			$errors = new WP_Error();
			if(sizeof($errors->errors) == 0)
			{
				$option = $this->get_pr_global_options();
				if(isset($option['login_after_register']) && $option['login_after_register'] == 1 && $this->piereg_pro_is_activate){
					$_POST['log'] = $_POST['username'];
					$_POST['pwd'] = $_POST['password'];
					$_POST['piereg_login_after_registration'] = true;
					$_POST['rememberme'] = "";
					$this->checkLogin();
					exit;
				}
			}
		}
		
		function check_payment_method_paypal($user)
		{
			$user_id = $_POST['user_id'];
			$user_email = $_POST['e_mail'];
			add_filter( 'wp_mail_content_type', array($this,'set_html_content_type' ));
			global $errors;
			$form 		= new Registration_form();
			$errors 	= $form->validateRegistration($errors);
			$option 	= get_option(OPTION_PIE_REGISTER);
			
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
			
			$message		= $form->filterEmail($message_temp,$user_email, $pass );
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
			
			if(!wp_mail($_POST['e_mail'], $subject, $message , $headers)){
				$this->pr_error_log("'The e-mail could not be sent. Possible reason: your host may have disabled the mail() function...'".($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
			}
			
			update_user_meta( $user_id, 'register_type', "payment_verify");
			$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
			if($option['paypal_sandbox']=="no")
			{
				$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
				
			}
			else
			{
				$paypal_url = "https://sandbox.paypal.com/cgi-bin/webscr";
			}
			
			$nvpStr = 	"?cmd=_s-xclick&hosted_button_id=".trim($option['paypal_butt_id']).
						"&custom=".$hash.'__'.$user_id.
						"&bn=Genetech_SI_Custom".
						"&cancel_return=".urlencode( trailingslashit(get_bloginfo("url")).'?action=payment_cancel&paypal='.base64_encode($user_id) ).
						"&notify_url=".urlencode( trailingslashit(get_bloginfo("url")).'?action=ipn_success&paypal='.base64_encode( $hash.'|'.$user_id ) );
						
			wp_redirect($paypal_url.$nvpStr);
			exit;
		}
	
		function process_lostpassword()
		{
			global $errors ;
			if( file_exists(PIEREG_DIR_NAME . "/forgot_password.php") )
				include_once("forgot_password.php");
			get_header();	
			$this->set_pr_stats("forgot","view");
			
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
			if( file_exists(PIEREG_DIR_NAME . "/get_password.php") )
				include_once("get_password.php");
			$get_form = piereg_get_passwird();
			echo $get_form;
			get_footer();
			exit;	
		}
		
	function Unverified(){
				global $wpdb;
				if( isset($_POST['notice']) && !empty($_POST['notice']) )
					echo '<div id="message" class="updated fade"><p><strong>' . $_POST['notice'] . '.</strong></p></div>';
				if( isset($_POST['error_message']) && !empty($_POST['error_message']) )
					echo '<div id="error" class="error fade"><p><strong>' . $_POST['error_message'] . '.</strong></p></div>';
					
			
				$unverified = get_users(array('meta_key'=> 'active','meta_value'   => 0));
				$piereg = get_option(OPTION_PIE_REGISTER);
				?>
	<div class="wrap">
	  <h2><?php _e('Unverified Users', 'piereg')?></h2>
	  <form id="verify-filter" method="post" action="">
		<div class="tablenav">
		  <div class="alignleft">
			<input onclick="return window.confirm('<?php _e('This will verify users of all types','piereg');?>'); " value="<?php _e('Verify Checked Users','piereg');?>" name="verifyit" class="button-secondary" type="submit">
			&nbsp;
			<input value="<?php _e('Resend Pending Payment E-mail','piereg');?>" name="paymentl" class="button-secondary" type="submit">
			&nbsp;
			<input value="<?php _e('Resend Verification E-mail','piereg');?>" name="emailverifyit" class="button-secondary" type="submit">
			&nbsp;
			<input value="<?php _e('Delete','piereg');?>" name="vdeleteit" class="button-secondary delete" type="submit">
            <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_verifyit_nonce','piereg_verifyit_nonce'); ?>
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
				if( isset($alt) ) $alt = ''; else $alt = "alternate";
				$user_object 	= new WP_User($un->ID);
				$roles		 	= $user_object->roles;
				$role			= array_shift($roles);
				$reg_type 		= get_user_meta($un->ID, 'register_type');
				?>
				<tr id="user-1" class="<?php echo $alt;?>">
				  <th scope="row" class="check-column"><input name="vusers[]" id="user_<?php echo $un->ID;?>" class="administrator" value="<?php echo $un->ID;?>" type="checkbox"></th>
				  <td><strong><?php echo $un->user_login;?></strong></td>
				  <td><a href="mailto:<?php echo $un->user_email;?>" title="<?php _e('E-mail', 'piereg'); echo ": ".$un->user_email;?>"><?php echo $un->user_email;?></a></td>
				  <td><?php
						switch($reg_type[0]){
							case "email_verify":
								_e("E-mail Verification","piereg");
							break;
							case "admin_verify":
								_e("Admin Verification","piereg");
							break;
							case "admin_email_verify":
								_e("E-mail & Admin Verification","piereg");
							break;
							case "payment_verify":
								_e("Payment Verification","piereg");
							break;
							default:
								echo ucwords($reg_type[0]);
							break;
						}
						?>
				  </td>
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
			$this->piereg_get_wp_plugable_file(true); // require_once pluggable.php
			if(isset($_POST['piereg_verifyit_nonce']) && wp_verify_nonce( $_POST['piereg_verifyit_nonce'], 'piereg_wp_verifyit_nonce' ))
			{
				$valid = isset($_POST['vusers']) ? $_POST['vusers'] : "";
				if($valid)
				{
					$option = get_option(OPTION_PIE_REGISTER);
					foreach( $valid as $user_id )
					{
						if ( $user_id ) 
						{
							$register_type = get_user_meta( $user_id , "register_type" , true);
							if($register_type == "admin_email_verify")
							{
								$user 			= new WP_User($user_id);
								$user_email 	= $user->user_email;
								
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
								$message		= $this->filterEmail($message_temp,$user_email );
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
								if(!wp_mail($user_email, $subject, $message , $headers)){
									$this->pr_error_log("'The e-mail could not be sent. Possible reason: your host may have disabled the mail() function...'".($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
								}
							}else{
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
								
								$message		= $this->filterEmail($message_temp,$user_email);
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
								if(!wp_mail($user_email, $subject, $message , $headers)){
									$this->pr_error_log("'The e-mail could not be sent. Possible reason: your host may have disabled the mail() function...'".($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
								}
							}
						}
					}
					$_POST['notice'] = __("User(s) has been activated");
				}
				else
					$_POST['notice'] = "<strong>".__('error','piereg').":</strong>".__("Please select a user to send emails to", "piereg");
			}
			else{
				$_POST['error_message'] = __("Sorry, your nonce did not verify","piereg");
			}
		}
		function PaymentLink()
		{
			$this->piereg_get_wp_plugable_file(true); // require_once pluggable.php
			if(isset($_POST['piereg_verifyit_nonce']) && wp_verify_nonce( $_POST['piereg_verifyit_nonce'], 'piereg_wp_verifyit_nonce' ))
			{
				global $wpdb;			
				$valid = isset($_POST['vusers']) ? $_POST['vusers'] : "";
				add_filter( 'wp_mail_content_type', array($this,'set_html_content_type' ));
				if( is_array($valid)) 
				{
					
					$option = get_option(OPTION_PIE_REGISTER);
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
						
						$message		= $this->filterEmail($message_temp,$user->user_email );
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
						if(!wp_mail($user->user_email, $subject, $message , $headers)){
							$this->pr_error_log("'The e-mail could not be sent. Possible reason: your host may have disabled the mail() function...'".($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
						}
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
			else{
				$_POST['error_message'] = __("Sorry, your nonce did not verify","piereg");
			}	
		}
		function AdminEmailValidate()
		{
			$this->piereg_get_wp_plugable_file(true); // require_once pluggable.php
			if(isset($_POST['piereg_verifyit_nonce']) && wp_verify_nonce( $_POST['piereg_verifyit_nonce'], 'piereg_wp_verifyit_nonce' ))
			{
				global $wpdb;			
				$valid = isset($_POST['vusers']) ? $_POST['vusers'] : "";
				add_filter( 'wp_mail_content_type', array($this,'set_html_content_type' ));
				if( is_array($valid) ) {
				
				$option = get_option(OPTION_PIE_REGISTER);
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
						
						$message		= $this->filterEmail($message_temp,$user->user_email, $pass );
						
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
									
						if(!wp_mail($user->user_email, $subject, $message , $headers)){
							$this->pr_error_log("'The e-mail could not be sent. Possible reason: your host may have disabled the mail() function...'".($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
						}
				}
				
				if($sent > 0)
						$_POST['notice'] = __("Verification Emails have been re-sent", "piereg");
					else
						$_POST['notice'] = __("Invalid User Types", "piereg");
				}
				else
					$_POST['notice'] = "<strong>".__('error','piereg').":</strong>".__("Please select a user to send emails to", "piereg");
			}else{
				$_POST['error_message'] = __("Sorry, your nonce did not verify","piereg");
			}
		}
		function AdminDeleteUnvalidated()
		{
			$this->piereg_get_wp_plugable_file(true); // require_once pluggable.php
			if(isset($_POST['piereg_verifyit_nonce']) && wp_verify_nonce( $_POST['piereg_verifyit_nonce'], 'piereg_wp_verifyit_nonce' ))
			{
				global $wpdb;
				
				$piereg = get_option(OPTION_PIE_REGISTER);
				$valid = isset($_POST['vusers']) ? $_POST['vusers'] : "";
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
			}else{
				$_POST['error_message'] = __("Sorry, your nonce did not verify","piereg");
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
		
		function PieRegSettings()
		{
			$this->PieRegRedirectSettingsAction();
			$option = get_option(OPTION_PIE_REGISTER);
			$this->include_pr_menu_pages($this->plugin_dir.'/menus/PieRegSettings.php');
		}
		function PieRegPaymentGateway()
		{
			$this->include_pr_menu_pages($this->plugin_dir.'/menus/PieRegPaymentGateway.php');			
		}		
		function PieRegNotifications()
		{
			$this->include_pr_menu_pages($this->plugin_dir.'/menus/PieRegNotifications.php');
		}
		function PieRegImportExport()
		{
			$this->include_pr_menu_pages($this->plugin_dir.'/menus/PieRegImportExport.php');
		}		
		function PieRegHelp()
		{
			$this->include_pr_menu_pages($this->plugin_dir.'/menus/PieRegHelp.php');
		}
		function PieRegRestrictUsers()
		{
			$this->include_pr_menu_pages_previous($this->plugin_dir.'/menus/PieRegRestrictUsers.php');			
		}
		function PieRegBulkEmails()
		{
			$this->include_pr_menu_pages($this->plugin_dir.'/menus/PieRegBulkEmails.php');
		}		
		function PieRegNewForm()
		{
			$this->include_pr_menu_pages($this->plugin_dir.'/menus/PieRegRegistrationForm.php');
		}
		//Opening Form Editor
		function RegPlusEditForm(){
			$this->include_pr_menu_pages($this->plugin_dir.'/menus/PieRegEditForm.php');
		}
		
		function PieRegSettingsProcess() {
			$update = get_option(OPTION_PIE_REGISTER);
			$this->piereg_get_wp_plugable_file(true); // require_once pluggable.php
			
			if(isset($_POST['piereg_settings_allusers']) && wp_verify_nonce( $_POST['piereg_settings_allusers'], 'piereg_wp_settings_allusers' )) {
				
				$update['alternate_login'] 			= intval($_POST['alternate_login']);
				$update['alternate_register'] 		= intval($_POST['alternate_register']);
				$update['alternate_forgotpass'] 	= intval($_POST['alternate_forgotpass']);
				$update['alternate_profilepage']	= intval($_POST['alternate_profilepage']);
				$update['after_login']				= intval($_POST['after_login']);
				$update['alternate_login_url']		= $_POST['alternate_login_url'];
				$update['alternate_logout']			= intval($_POST['alternate_logout']);
				$update['alternate_logout_url']		= $_POST['alternate_logout_url'];
				
			} elseif(isset($_POST['piereg_settings_ux']) && wp_verify_nonce( $_POST['piereg_settings_ux'], 'piereg_wp_settings_ux' )) {
				if( isset($_POST['is_advanced']) && $_POST['is_advanced'] == 1 )
				{
					$update['login_after_register'] 	= isset($_POST['login_after_register']) ? intval($_POST['login_after_register']) :0;
					
					$update['pr_theme']				= intval($_POST['pr_theme']);	
				} 
				else 
				{
					$update['display_hints'] 			= isset($_POST['display_hints']) ? intval($_POST['display_hints']) :0;
					
					$update['login_username_label']	= $_POST['login_username_label'];
					$update['login_username_placeholder']	= $_POST['login_username_placeholder'];
					$update['login_password_label'] = $_POST['login_password_label'];
					$update['login_password_placeholder'] = $_POST['login_password_placeholder'];
					
					$update['forgot_pass_username_label']	= $_POST['forgot_pass_username_label'];
					$update['forgot_pass_username_placeholder'] = $_POST['forgot_pass_username_placeholder'];
					
					$update['custom_logo_url']			= $_POST['custom_logo_url'];
					$update['custom_logo_tooltip']		= $_POST['custom_logo_tooltip'];
					$update['custom_logo_link']			= $_POST['custom_logo_link'];
					$update['show_custom_logo']			= (isset($_POST['show_custom_logo']))?$_POST['show_custom_logo']:0;
					$update['outputcss'] 				= isset($_POST['outputcss']) ? intval($_POST['outputcss']) :0;
					$update['outputjquery_ui']			= isset($_POST['outputjquery_ui']) ? intval($_POST['outputjquery_ui']) :0;
					
					$custom_css					= mb_convert_encoding(strip_tags($_POST['custom_css']),'HTML-ENTITIES','utf-8');
					$update['custom_css']		= $this->disable_magic_quotes_gpc($custom_css);
					
					$tracking_code				= mb_convert_encoding(strip_tags($_POST['tracking_code']),'HTML-ENTITIES','utf-8');
					$update['tracking_code']	= $this->disable_magic_quotes_gpc($tracking_code);
				}
				
			} elseif(isset($_POST['piereg_settings_overrides']) && wp_verify_nonce( $_POST['piereg_settings_overrides'], 'piereg_wp_settings_overrides' )) {
				
				$update['redirect_user'] 			= isset($_POST['redirect_user']) ? intval($_POST['redirect_user']) :0;
				$update['show_admin_bar']			= isset($_POST['show_admin_bar']) ? intval($_POST['show_admin_bar']) :0;
				$update['block_WP_profile']			= isset($_POST['block_WP_profile']) ? intval($_POST['block_WP_profile']) :0;
				$update['block_wp_login'] 			= isset($_POST['block_wp_login']) ? intval($_POST['block_wp_login']) :0;
				$update['allow_pr_edit_wplogin']	= isset($_POST['allow_pr_edit_wplogin']) ? intval($_POST['allow_pr_edit_wplogin']) :0;
				
			} elseif(isset($_POST['piereg_settings_security_b']) && wp_verify_nonce( $_POST['piereg_settings_security_b'], 'piereg_wp_settings_security_b' )) {
				
				$update['captcha_in_login_value'] = $_POST['captcha_in_login_value'];
				$update['captcha_in_login_attempts'] = $_POST['captcha_in_login_attempts'];
				$update['capthca_in_login_label']	= $_POST['capthca_in_login_label'];
				$update['piereg_recapthca_skin_login'] = $_POST['piereg_recapthca_skin_login'];
				$update['capthca_in_login']			= intval($_POST['capthca_in_login']);
				
				if($this->piereg_pro_is_activate){
					$update['piereg_security_attempts_login_value']			= isset($_POST['piereg_security_attempts_login_value']) ? intval($_POST['piereg_security_attempts_login_value']) : "";
				}
				$update['security_attempts_login']			= intval($_POST['security_attempts_login']);
				$update['security_attempts_login_time']		= intval($_POST['security_attempts_login_time']);
								
				$update['captcha_in_forgot_value'] = $_POST['captcha_in_forgot_value'];
				$update['capthca_in_forgot_pass_label']	= $_POST['capthca_in_forgot_pass_label'];
				$update['piereg_recapthca_skin_forgot_pass']	= $_POST['piereg_recapthca_skin_forgot_pass'];
				$update['capthca_in_forgot_pass']	= intval($_POST['capthca_in_forgot_pass']);
				
				if($this->piereg_pro_is_activate){
					$update['piereg_security_attempts_forgot_value']			= isset($_POST['piereg_security_attempts_forgot_value']) ? intval($_POST['piereg_security_attempts_forgot_value']) : "";
				}
				$update['security_attempts_forgot_time']			= intval($_POST['security_attempts_forgot_time']);
				$update['security_attempts_forgot']		= intval($_POST['security_attempts_forgot']);
				
				$update['captcha_publc'] = $_POST['captcha_publc'];
				$update['captcha_private'] = $_POST['captcha_private'];
				
				$update['verification'] = intval($_POST['verification']);
				
				$update['email_edit_verification_step'] = intval($_POST['email_edit_verification_step']);
				$update['grace_period'] = intval($_POST['grace_period']);
					
				$update['restrict_bot_enabel'] = isset($_POST['restrict_bot_enabel']) ? intval($_POST['restrict_bot_enabel']) : ""; 
				
				$restrict_bot_content			= mb_convert_encoding($_POST['restrict_bot_content'],'HTML-ENTITIES','utf-8');
				$update['restrict_bot_content'] = $this->disable_magic_quotes_gpc($restrict_bot_content);
				
				$restrict_bot_content_message			= mb_convert_encoding($_POST['restrict_bot_content_message'],'HTML-ENTITIES','utf-8');
				$update['restrict_bot_content_message'] = $this->disable_magic_quotes_gpc($restrict_bot_content_message);
				
			} elseif(isset($_POST['piereg_settings_security_advanced']) && wp_verify_nonce( $_POST['piereg_settings_security_advanced'], 'piereg_wp_settings_security_advanced' )){ 
				$update['reg_form_submission_time_enable'] 	= isset($_POST['reg_form_submission_time_enable']) ? intval($_POST['reg_form_submission_time_enable']):0;
				$update['reg_form_submission_time'] 		= isset($_POST['reg_form_submission_time']) ? intval($_POST['reg_form_submission_time']):0;
			
			} else {
				$_POST['error'] = __("Sorry, your nonce did not verify","piereg");				
			}
			
			update_option(OPTION_PIE_REGISTER, $update );
			PieReg_Base::set_pr_global_options(OPTION_PIE_REGISTER, $update );
			
			if(!isset($_POST['error']) && empty($_POST['error']))
				$_POST['notice'] = apply_filters("piereg_settings_saved",__('Settings Saved', 'piereg'));

		}
		
		function PieRegRedirectSettingsAction(){
			global $wpdb;
			$prefix=$wpdb->prefix."pieregister_";
			$piereg_table_name =$prefix."redirect_settings";
			/*	Change Status	*/
			if( isset($_POST['redirect_settings_status_id']) && !empty($_POST['redirect_settings_status_id']) ){
				if($wpdb->query($wpdb->prepare("update `".$piereg_table_name."` SET `status` = CASE WHEN status = 1 THEN  0 WHEN status = 0 THEN 1 ELSE  0 END  WHERE `id` = %d",intval($_POST['redirect_settings_status_id']))))
					$_POST['notice'] = __("Status has been changed.","piereg");
				else
					$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
			}
			/*	Delete Record	*/
			elseif(isset($_POST['redirect_settings_del_id'])){
				if($wpdb->query($wpdb->prepare("DELETE FROM `".$piereg_table_name."` WHERE `id` = %d", intval($_POST['redirect_settings_del_id']))))
					$_POST['notice'] = __("Record has been deleted","piereg");
				else
					$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
			}
			/*	Add Record	*/
			elseif(isset($_POST['redirect_settings_add_new']))
			{
				$this->piereg_get_wp_plugable_file(true); // require_once pluggable.php
				if(isset($_POST['piereg_redirect_settings_nonce']) && wp_verify_nonce( $_POST['piereg_redirect_settings_nonce'], 'piereg_wp_redirect_settings_nonce' ))
				{
					if( empty($_POST['logged_in_url']) && empty($_POST['log_out_url']) && $_POST['log_in_page'] == "-1" && $_POST['log_out_page'] == "-1" ) 
					{
						$_POST['error'] = __("Please select any page or url for user role.","piereg");	
					}
					elseif(
						(isset($_POST['piereg_user_role']) && !empty($_POST['piereg_user_role'])) &&
						(isset($_POST['logged_in_url']) && isset($_POST['log_out_url'])) &&
						(!empty($_POST['logged_in_url']) || !empty($_POST['log_out_url']) || !empty($_POST['log_in_page']) || !empty($_POST['log_out_page']))
					  ){
						
						$insr_piereg_user_role 	= trim($_POST['piereg_user_role']);
						$insr_logged_in_url 	= urlencode(trim($_POST['logged_in_url']));
						$insr_log_in_page 		= intval($_POST['log_in_page']);
						$insr_log_out_url 		= urlencode(trim($_POST['log_out_url']));
						$insr_log_out_page 		= intval($_POST['log_out_page']);
						$insr_status 			= "1";
						
						$sql = "INSERT INTO ".$piereg_table_name." (`user_role`,`logged_in_url`,`logged_in_page_id`,`log_out_url`,`log_out_page_id`,`status`) VALUES (%s,%s,%d,%s,%d,%s)";
						
						if($wpdb->query( $wpdb->prepare($sql, $insr_piereg_user_role, $insr_logged_in_url, $insr_log_in_page, $insr_log_out_url, $insr_log_out_page, $insr_status) ))
							$_POST['notice'] = __("Successfully add new record","piereg");
						else
							$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
					}else{
						$_POST['error'] = __("Invalid field(s)","piereg");
					}
				}else{
					$_POST['error'] = __("Sorry, your nonce did not verify","piereg");
				}
			}
			/*	Update Record	*/
			elseif(isset($_POST['redirect_settings_update'])){
				
				if(isset($_POST['id']) && $_POST['id'] && isset($_POST['logged_in_url']) && isset($_POST['log_out_url'])){
					
					$upd_logged_in_url 	= urlencode(trim($_POST['logged_in_url']));
					$upd_log_in_page 	= intval($_POST['log_in_page']);
					$upd_log_out_url 	= urlencode(trim($_POST['log_out_url']));
					$upd_log_out_page 	= intval($_POST['log_out_page']);
					$upd_id 			= intval($_POST['id']);
					
					$sql = "UPDATE ".$piereg_table_name." SET `logged_in_url`= %s, `logged_in_page_id`=%d,`log_out_url`=%s,`log_out_page_id`=%d WHERE `id`= %d";
						
					if( $wpdb->query( $wpdb->prepare($sql, $upd_logged_in_url, $upd_log_in_page, $upd_log_out_url, $upd_log_out_page, $upd_id) ) )
						$_POST['notice'] = __("Successfully update record","piereg");
					else
						$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
				}else{
					$_POST['error'] = __("Invalid field(s)","piereg");
				}
			}
		}
		function PieRegInvitationCodes()
		{
			global $wpdb;
			$piereg 	= get_option(OPTION_PIE_REGISTER);
			$codetable	= $this->codeTable();
			if( isset($_POST['invi_del_id']) ) 
			{
				
				$this->piereg_get_wp_plugable_file(true); // require_once pluggable.php
				if(isset($_POST['piereg_invitation_code_nonce']) && wp_verify_nonce( $_POST['piereg_invitation_code_nonce'], 'piereg_wp_invitation_code_nonce' ))
				{
					if($wpdb->query( $wpdb->prepare("DELETE FROM ".$codetable." WHERE id = %s", $_POST['invi_del_id']) ))	
						$_POST['notice'] = __("The Invitation Code has been deleted","piereg");
					else
						$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
				}else{
					$_POST['error'] = __("Sorry, your nonce did not verify","piereg");
				}
			}
			
			else if( isset($_POST['status_id']) ) 
			{
				
				$this->piereg_get_wp_plugable_file(true); // require_once pluggable.php
				if(isset($_POST['piereg_invitation_code_nonce']) && wp_verify_nonce( $_POST['piereg_invitation_code_nonce'], 'piereg_wp_invitation_code_nonce' ))
				{
					if($wpdb->query( $wpdb->prepare("update ".$codetable." SET status = CASE WHEN status = 1 THEN  0 WHEN status = 0 THEN 1 ELSE  0 END  WHERE id = %s", $_POST['status_id']) )){
						$_POST['notice'] = __("Status has been changed.","piereg");
					}else{
						$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
					}
				}else{
					$_POST['error'] = __("Sorry, your nonce did not verify","piereg");
				}
			}
			
			else if( isset($_POST['piereg_codepass']) ) 
			{
				
				$this->piereg_get_wp_plugable_file(true); // require_once pluggable.php
				if(isset($_POST['piereg_invitation_code_nonce']) && wp_verify_nonce( $_POST['piereg_invitation_code_nonce'], 'piereg_wp_invitation_code_nonce' ))
				{
					
					if(isset($_POST['save_submit'])){
						$piereg['enable_invitation_codes'] = isset($_POST['enable_invitation_codes']) ? intval($_POST['enable_invitation_codes']) : 0;
						if(update_option(OPTION_PIE_REGISTER,$piereg)){
							$_POST['notice'] = __("Settings Saved","piereg");
						}
					}
					
					if(isset($_POST['add_code'])){
						if(empty($_POST['piereg_codepass']) || trim($_POST['piereg_codepass']) == ''){
							$_POST['error'] = __("Code field should not be empty.","piereg");
						}
						
						if(isset($_POST['invitation_code_usage']) && !is_numeric($_POST['invitation_code_usage']) && trim($_POST['invitation_code_usage']) != ''){
							if(empty($_POST['piereg_codepass']) || trim($_POST['piereg_codepass']) == ''){
								$_POST['error'] .= '<br/>';
							}
							$_POST['error'] .= __("Usage only allows numeric characters.","piereg");
						}elseif(isset($_POST['invitation_code_usage']) && $_POST['invitation_code_usage'] < 0){
							if(empty($_POST['piereg_codepass']) || trim($_POST['piereg_codepass']) == ''){
								$_POST['error'] .= '<br/>';
							}
							$_POST['error'] .= __("Usage should not be less than 0 (zero).","piereg");
						}
										
						$update["codepass"] = $_POST['piereg_codepass'];
						
						$codespasses=explode("\n",$update["codepass"]);				
						
						$codeadded = false;
						
						$count_code = 0;
						$count_added_code = 0;
						$count_special_char = 0;
						
						foreach( $codespasses as $k=>$v )
						{
							$v = trim($v);
							if($v != '')
							{
								$count_code++;
								
								if( $this->InsertCode($v) )
								{
									$count_added_code++;
									$codeadded = true;
								}
							
								if(!preg_match('/^[A-Za-z0-9_-]+$/', $v))
								{
									$count_special_char++;
									$special_char = true;
								}
							}						
						}
						
						if(isset($special_char) && $special_char){
							$_POST['error'] = __("Special Characters are not allowed in code field.","piereg");
						}
						
						if(!$codeadded && $count_code != $count_added_code && !isset($_POST['error'])){
							$count_not_added_code = $count_code - $count_added_code - $count_special_char;
							$_POST['notice'] = $count_not_added_code.__(" invitation Code(s) already exists","piereg");
						}elseif($codeadded) {
							if(isset($_POST['invitation_code_usage']) && is_numeric($_POST['invitation_code_usage']) && $_POST['invitation_code_usage'] >= 0)
							{
								$piereg["invitation_code_usage"] = $_POST['invitation_code_usage'];
								
								if(update_option(OPTION_PIE_REGISTER,$piereg))
								{
									$_POST['notice'] = __("Invitation Code(s) added successfully","piereg");
								}
							}
							
							if($count_code != $count_added_code){
								$count_not_added_code = $count_code - $count_added_code - $count_special_char;
								$_POST['notice'] = $count_added_code.__(" invitation Code(s) added successfully.","piereg");
								if($count_not_added_code != 0){
									$_POST['notice'] .= '<br/>'.$count_not_added_code.__(" invitation Code(s) already exists","piereg");
								}
							}else{						
								$_POST['notice'] = $count_added_code.__(" invitation Code(s) added successfully","piereg");
							}
						}
					}
				}else{
					
					$_POST['error'] = __("Sorry, your nonce did not verify","piereg");
				}
			}
			$this->include_pr_menu_pages($this->plugin_dir.'/menus/PieRegInvitationCodes.php');	
		}
		function InsertCode($name)
		{
				if(empty($name) || trim($name) == '') return false;
				
				global $wpdb;
				
				$piereg=get_option(OPTION_PIE_REGISTER);
				
				$codetable=$this->codeTable();
				$expiry = (isset($piereg['codeexpiry'])) ? $piereg['codeexpiry']: "";
				$codes = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $codetable WHERE BINARY `name`=%s", $name) );
				if(isset($wpdb->last_error) && !empty($wpdb->last_error)){
					$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
				}
				$counts = count($codes);
				$wpdb->flush();
				
				if( $counts > 0 )
				{
					return false;
				}
				
				if(!preg_match('/^[A-Za-z0-9_-]+$/', $name)) return false;
				
				$name = esc_sql(trim(preg_replace("/[^A-Za-z0-9_-]/", '', $name)));			
				if(empty($name)) return false;
				
				$date = date_i18n("Y-m-d");
				
				$usage = $_POST['invitation_code_usage'];
				if(!empty($usage) && !is_numeric($usage) || $usage < 0){
					return false;
				}
								
				if(!$wpdb->query( $wpdb->prepare("INSERT INTO ".$codetable." (`created`,`modified`,`name`,`count`,`status`,`code_usage`)VALUES(%s,%s,%s,%s,%s,%s)", $date, $date, $name, $counts, "1", $usage) )){
					$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
				}
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
					$_date_start = date_i18n("Y-m-d",strtotime($_POST['date_start']));
					$_date_end = date_i18n("Y-m-d",strtotime($_POST['date_end']));
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
				
				/*$sql_pre 	= $wpdb->prepare( $query, $user_table );
				$users 		= $wpdb->get_results( $sql_pre, ARRAY_A ); #WPS*/
				$users = $wpdb->get_results($query,ARRAY_A);				
				if(isset($wpdb->last_error) && !empty($wpdb->last_error)){
					$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
				}
				global  $wp_roles,$wpdb;
				if(sizeof($users ) > 0){
					$dfile = "pieregister_exported_users_".date_i18n("Y-m-d-H:i").".csv";
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
					die(); // ask baqar
				}
				else
				{
					$_POST['error_message'] = __("No Record Found","piereg");
				}
			}else{
				$_POST['error_message'] = __("Sorry, your nonce did not verify","piereg");
			}
		}
		function csv_to_array($filename='', $delimiter=';'){
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
					$csv_data = $this->csv_to_array($_FILES['csvfile']['tmp_name'],',');
				}
				if(!isset($csv_data[0]) or sizeof($csv_data[0]) < 2)
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
				if(is_array($csv_data) && !empty($csv_data))
				{
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
							$already_exist++;
							if(isset($_POST['update_existing_users']) && $_POST['update_existing_users'] == "yes"){
								unset($user_default_data['user_pass']);
								$user_id = wp_update_user($user_default_data);
								if(isset($user_id)){
									$this->update_user_meta_by_array($user_id,$user_meta_key);
								}
							}
						}else{
							if(get_user_by('ID',$user_default_data['ID'])){
								$already_exist++;
								if(isset($_POST['update_existing_users']) && $_POST['update_existing_users'] == "yes"){
									unset($user_default_data['user_pass']);
									$user_id = wp_update_user($user_default_data);
									if(isset($user_id)){
										$this->update_user_meta_by_array($user_id,$user_meta_key);
									}
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
		
		
		function SaveSettings()
		{
			if(isset($_POST['is_deactivate_plugin_license']) && $_POST['is_deactivate_plugin_license'] == "true")
			{
				$piereg_api_manager_menu = new Piereg_API_Manager_Example_MENU();
				
				if ( ! function_exists( 'get_plugins' ) ) {
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				}
				$activated_plugins 	= get_option('active_plugins');
				$list_all_plugins 	= get_plugins();
				
				foreach( $list_all_plugins as $key => $plugin )
				{
					if( in_array($key,$activated_plugins) && strpos($plugin['Name'],'Pie Register (Add on) - ') !== false )
					{
						$addon_name = str_replace(array('Pie Register (Add on) - ', '(', ')'), '', $plugin['Name']);
						$addon_name = str_replace(array(' ', '.'), '_', $addon_name);
						$is_addon	= "addon_" . $addon_name;
						
						$plugin_status 	= get_option( 'piereg_api_manager_'.$is_addon.'_activated' );
						
						if( $plugin_status == 'Activated' ) {
							$piereg_api_manager_menu->wc_am_license_key_deactivation( "on", array('is_addon'=>$is_addon,'is_addon_version'=>$plugin['Version']) );
						}
					}
				}
				
				$piereg_api_manager_menu->wc_am_license_key_deactivation( "on" );
			}
			
			if(isset($_POST['is_deactivate_addon']) && $_POST['is_deactivate_addon'] == "true")
			{
				$piereg_api_manager_menu = new Piereg_API_Manager_Example_MENU();
				$piereg_api_manager_menu->wc_am_license_key_deactivation( "on", array('is_addon'=>$_POST['is_deactivate_addon_license'],'is_addon_version'=>$_POST['addon_software_version']) );
			}
			
			$update = get_option(OPTION_PIE_REGISTER);
			if( isset($_POST['action']) && $_POST['action'] == 'pie_reg_update' )
			{
				if(isset($_POST['payment_gateway_page']))
				{
					$update["enable_paypal"]	= (isset($_POST['enable_paypal'])) ? intval($_POST['enable_paypal']) : "";
					$update["paypal_butt_id"]	= $this->disable_magic_quotes_gpc($_POST['piereg_paypal_butt_id']);
					$update["paypal_sandbox"]	= $_POST['piereg_paypal_sandbox'];
				}
				else if(isset($_POST['payment_gateway_general_settings'])){
					
					$payment_success_msg			= trim(((isset($_POST['payment_success_msg']) && !empty($_POST['payment_success_msg']))?$_POST['payment_success_msg']:__("Payment was successful.","piereg")));
					$update["payment_success_msg"]	= $this->disable_magic_quotes_gpc($payment_success_msg);
					
					$payment_faild_msg				= trim(((isset($_POST['payment_faild_msg']) && !empty($_POST['payment_faild_msg']))?$_POST['payment_faild_msg']:__("Payment failed.","piereg")));
					$update["payment_faild_msg"]	= $this->disable_magic_quotes_gpc($payment_faild_msg);
					
					$payment_renew_msg				= trim(((isset($_POST['payment_renew_msg']) && !empty($_POST['payment_renew_msg']))?$_POST['payment_renew_msg']:__("Account needs to be activated.","piereg")));
					$update["payment_renew_msg"]	= $this->disable_magic_quotes_gpc($payment_renew_msg);
					
					
					$payment_already_activate_msg	= trim(((isset($_POST['payment_already_activate_msg']) && !empty($_POST['payment_already_activate_msg']))?$_POST['payment_already_activate_msg']:__("Account is already active.","piereg")));
					$update["payment_already_activate_msg"]	= $this->disable_magic_quotes_gpc($payment_already_activate_msg);
				}
				else if(isset($_POST['admin_email_notification_page'])){
					$this->piereg_get_wp_plugable_file(true); // require_once pluggable.php
					if(isset($_POST['piereg_admin_email_notification']) && wp_verify_nonce( $_POST['piereg_admin_email_notification'], 'piereg_wp_admin_email_notification' ))
					{
						$update['enable_admin_notifications']	= intval($_POST['enable_admin_notifications']);
						$update['admin_sendto_email']			= trim($_POST['admin_sendto_email']);
						
						$admin_from_name						= mb_convert_encoding($_POST['admin_from_name'],'HTML-ENTITIES','utf-8');
						$update['admin_from_name']				= $this->disable_magic_quotes_gpc($admin_from_name);
						
						$update['admin_from_email']				= trim($_POST['admin_from_email']);
						$update['admin_to_email']				= trim($_POST['admin_to_email']);
						$update['admin_bcc_email']				= trim($_POST['admin_bcc_email']);
						
						$admin_subject_email					= mb_convert_encoding($_POST['admin_subject_email'],'HTML-ENTITIES','utf-8');
						$update['admin_subject_email']			= $this->disable_magic_quotes_gpc($admin_subject_email);
						
						$update['admin_message_email_formate']	= isset($_POST['admin_message_email_formate']) ? intval($_POST['admin_message_email_formate']) :0;;
						
						$admin_message_email					= mb_convert_encoding($_POST['admin_message_email'],'HTML-ENTITIES','utf-8');
						$update['admin_message_email']			= $this->disable_magic_quotes_gpc($admin_message_email);
					}else{
						$_POST['error'] = __("Sorry, your nonce did not verify","piereg");
					}
				}
				else if(isset($_POST['user_email_notification_page']))
				{
					$this->piereg_get_wp_plugable_file(true); // require_once pluggable.php
					if(isset($_POST['piereg_user_email_notification']) && wp_verify_nonce( $_POST['piereg_user_email_notification'], 'piereg_wp_user_email_notification' ))
					{
						$pie_user_email_types 	= get_option( 'pie_user_email_types'); 
						
						foreach ($pie_user_email_types as $val=>$type) 
						{
							$user_from_name = mb_convert_encoding(trim($_POST['user_from_name_'.$val]),'HTML-ENTITIES','utf-8');
							$update['user_from_name_'.$val]		= $this->disable_magic_quotes_gpc($user_from_name);
							
							$user_from_email = mb_convert_encoding(trim($_POST['user_from_email_'.$val]),'HTML-ENTITIES','utf-8');
							$update['user_from_email_'.$val]	= $this->disable_magic_quotes_gpc($user_from_email);
							
							$user_to_email = mb_convert_encoding(trim($_POST['user_to_email_'.$val]),'HTML-ENTITIES','utf-8');
							$update['user_to_email_'.$val]		= $this->disable_magic_quotes_gpc($user_to_email);
							
							$user_bcc_email = "";
							if( isset($_POST['user_bcc_email_'.$val]) ) 
							{
								$user_bcc_email = mb_convert_encoding(trim($_POST['user_bcc_email_'.$val]),'HTML-ENTITIES','utf-8');
							}
							$update['user_bcc_email_'.$val]		= $this->disable_magic_quotes_gpc($user_bcc_email);
							
							$user_subject_email = mb_convert_encoding(trim($_POST['user_subject_email_'.$val]),'HTML-ENTITIES','utf-8');
							$update['user_subject_email_'.$val] = $this->disable_magic_quotes_gpc($user_subject_email);
							
							$update['user_formate_email_'.$val]	= intval($_POST['user_formate_email_'.$val]);
							
							$user_message_email = mb_convert_encoding(trim($_POST['user_message_email_'.$val]),'HTML-ENTITIES','utf-8');
							$update['user_message_email_'.$val] = $this->disable_magic_quotes_gpc($user_message_email);	
						}
					}else{
						$_POST['error'] = __("Sorry, your nonce did not verify","piereg");
					}
				}
				else if( isset($_POST['piereg_restrict_users']) && wp_verify_nonce( $_POST['piereg_restrict_users'], 'piereg_wp_restrict_users' ) )
				{
					$this->piereg_get_wp_plugable_file(true); // require_once pluggable.php
					if( isset( $_POST['restrict_user_by_ip'] ) )
					{
						$update['enable_blockedips'] 		= isset($_POST['enable_blockedips']) ? intval($_POST['enable_blockedips']) : "";
						$update['piereg_blk_ip'] 			= $_POST['piereg_blk_ip'];
						
					} 
					else if( isset( $_POST['restrict_user_by_username'] ) )
					{
						$update['enable_blockedusername'] 	= isset($_POST['enable_blockedusername']) ? intval($_POST['enable_blockedusername']) : ""; 
						$update['piereg_blk_username'] 		= $this->disable_magic_quotes_gpc($_POST['piereg_blk_username']);
					}
					else if( isset( $_POST['restrict_user_by_email'] ) )
					{
						$update['enable_blockedemail'] 	=  isset($_POST['enable_blockedemail']) ? intval($_POST['enable_blockedemail']) : ""; 
						$update['piereg_blk_email'] 	= $this->disable_magic_quotes_gpc($_POST['piereg_blk_email']);
					}
					else
					{
						$_POST['error'] = __("Sorry, your nonce did not verify","piereg");
					}
				}
				
				update_option(OPTION_PIE_REGISTER, $update );
				PieReg_Base::set_pr_global_options(OPTION_PIE_REGISTER, $update );
				if( isset($error) && trim($error) != "" )
				{
					$_POST['PR_license_notice'] = $error;
				}
				if(!isset($_POST['error']) && empty($_POST['error']))
					$_POST['notice'] = apply_filters("piereg_settings_saved",__('Settings Saved', 'piereg'));

			}
		}
		
		
		/*
			*	Get Field's Name For Post
			*	Add this snipt at 17/10/2014
		*/
		function getPieregFieldName($field,$no,$field_type = ""){
			
			$fieldName = "";
			if(isset($field['type']) && !empty($field['type']) && isset($field['id']) )
			{
				switch($field['type']){
					case "username":
					case "password":
					case "pricing":	
						$fieldName = $field['type'];
					break;
					case "email":	
						$fieldName = "e_mail";
					break;
					case "default":	
						$fieldName = $field['field_name'];
					break;
					default:
						$fieldName = $field['type']."_".$field['id'];
					break;
				}
			}
			
			return $fieldName;
		}
		function addTextField($field,$no,$field_type)
		{
			$fieldPostName = $this->getPieregFieldName($field,$no,$field_type);
			
			$name 	= $this->createFieldName($field,$no);
			$id 	= $this->createFieldID($field,$no);
			
			echo '<input disabled="disabled" id="'.$id.'" name="'.$name.'" class="input_fields"  placeholder="'.((isset($field['placeholder']))?$field['placeholder']:"").'" type="text" value="'.((isset($field['default_value']))?$field['default_value']: "" ).'" data-field-post-name="'.$fieldPostName.'" />';

		}
		function addHoneyPotField($field,$no,$field_type)
		{
			$fieldPostName = $this->getPieregFieldName($field,$no,$field_type);
			
			$name 	= $this->createFieldName($field,$no);
			$id 	= $this->createFieldID($field,$no);
			
			echo '<img src="'.plugins_url('/images/honeypot.png', __FILE__).'" align="left" /><input disabled="disabled" id="'.$id.'" name="'.$name.'" class="input_fields"  placeholder="'.((isset($field['placeholder']))?$field['placeholder']:"").'" type="text" value="'.((isset($field['default_value']))?$field['default_value']: "" ).'" data-field-post-name="'.$fieldPostName.'" />';

		}
		function addInvitationField($field,$no)
		{		
			$fieldPostName = $this->getPieregFieldName($field,$no);
			$name 	= $this->createFieldName($field,$no);
			
			echo '<input type="hidden" id="default_'.$field['type'].'">';
			echo '<input disabled="disabled" id="invitation_field" name="'.$name.'" class="input_fields"  placeholder="'.$field['placeholder'].'" type="text" value="'.((isset($field['default_value']))?$field['default_value']: "" ).'" />';	

		}
		function addDefaultField($field,$no)
		{		
			$fieldPostName = $this->getPieregFieldName($field,$no);
			$name 	= $this->createFieldName($field,$no);
			$id 	= $this->createFieldID($field,$no);
			if($field['field_name']=="description")
			{
				echo '<textarea  rows="5" cols="73" disabled="disabled" data-field_id="piereg_field_'.$no.'" id="default_'.$field['field_name'].'" name="'.$name.'"  style="width:100%;"  data-field-post-name="'.$fieldPostName.'" ></textarea>';
			}
			else
			{
				echo '<input disabled="disabled" id="default_'.$field['field_name'].'" data-field_id="piereg_field_'.$no.'" name="'.$name.'" class="input_fields"  placeholder="'.(isset($field['placeholder'])? $field['placeholder']:"").'" type="text"  data-field-post-name="'.$fieldPostName.'" />';
			}
			echo '<input type="hidden" name="field['.$field['id'].'][id]" value="'.$field['id'].'" id="id_'.$field['id'].'">';
			echo '<input type="hidden" name="field['.$field['id'].'][type]" value="default" id="type_'.$field['id'].'">';
			echo '<input type="hidden" name="field['.$field['id'].'][label]" value="'.$field['label'].'" id="label_'.$field['id'].'">';
			echo '<input type="hidden" name="field['.$field['id'].'][field_name]" value="'.$field['type'].'" id="label_'.$field['id'].'">';
			echo '<input type="hidden" id="default_'.$field['type'].'">';
		}
		function addEmail($field,$no)
		{
			$fieldPostName = $this->getPieregFieldName($field,$no);
			$name 			= $this->createFieldName($field,$no);
			$id 			= $this->createFieldID($field,$no);
			$confirm_email = 'style="display:none;"';
			
			echo '<input disabled="disabled" id="'.$id.'" name="'.$name.'" data-field_id="piereg_field_'.$no.'" class="input_fields"  placeholder="'.((isset($field['placeholder']))?$field['placeholder']: "" ).'" type="text" value="'.((isset($field['default_value']))?$field['default_value']: "" ).'"  data-field-post-name="'.$fieldPostName.'" />';
			if(isset($field['confirm_email']))
			{
				$confirm_email	= "";
			}
			
			$label2 = (isset($field['label2']) and !empty($field['label2']))?$field['label2'] : __("Confirm E-Mail","piereg");
			echo '</div><div '.$confirm_email.' id="field_label2_'.$no.'" class="label_position confrim_email_label2"><label>'.$label2.'</label></div><div class="fields_position"><div id="confirm_email_field_'.$no.'" '.$confirm_email.' class="inner_fields"><input disabled="disabled" type="text" class="input_fields" placeholder="'.$field['placeholder'].'" > </div>';	
		}
		function addPassword($field,$no)
		{		
			$fieldPostName = $this->getPieregFieldName($field,$no);
			$name 			= $this->createFieldName($field,$no);
			$id 			= $this->createFieldID($field,$no);
			
			
			echo '<input disabled="disabled" id="'.$id.'" name="'.$name.'" class="input_fields"  placeholder="'.$field['placeholder'].'" type="text" value=""  data-field-post-name="'.$fieldPostName.'" />';
			
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
			  <div class="address2 state_div_'.$no.'" id="state_canada_'.$no.'" '.((isset($hide_canstate))?$hide_canstate:"").'>
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
			 if(isset($field['address_type']) && $field['address_type'] != "International")
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
			$fieldPostName = $this->getPieregFieldName($field,$no);
			$name 	= $this->createFieldName($field,$no);
			$id 	= $this->createFieldID($field,$no);
				
			echo '<textarea disabled="disabled" id="'.$id.'" name="'.$name.'" data-field_id="piereg_field_'.$no.'" rows="'.$field['rows'].'" cols="'.$field['cols'].'"   placeholder="'.$field['placeholder'].'"  style="width:100%;" data-field-post-name="'.$fieldPostName.'">'.$field['default_value'].'</textarea>';

		}
		
		function addDropdown($field,$no)
		{
			$fieldPostName = $this->getPieregFieldName($field,$no);
			
			$name 	= $this->createFieldName($field,$no);
			$id 	= $this->createFieldID($field,$no);
			
			$multiple = "";
			
			$data_f_post_name = 'data-field-post-name="'.$fieldPostName.'"';
			if($field['type']=="multiselect")
			{
				$multiple 	= 'multiple';	
				$name		.= "[]";
				$data_f_post_name = "";
			}
			echo '<select '.$multiple.' id="'.$name.'" name="'.$name.'" data-field_id="piereg_field_'.$no.'" disabled="disabled" '.$data_f_post_name.'>';
		
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
			$fieldPostName = $this->getPieregFieldName($field,$no);
			$name 	= $this->createFieldName($field,$no);
			$id 	= $this->createFieldID($field,$no);
			
			echo '<input disabled="disabled" id="'.$id.'" name="'.$name.'" class="input_fields"  placeholder="'.$field['placeholder'].'" min="'.$field['min'].'" max="'.$field['max'].'" type="number" value="'.$field['default_value'].'" data-field-post-name="'.$fieldPostName.'"/>';
		}
		function addCheckRadio($field,$no)
		{
			if(sizeof($field['value']) > 0)
			{
				$fieldPostName = $this->getPieregFieldName($field,$no);
				echo '<div class="radio_wrap">';
				$name 	= $this->createFieldName($field,$no);
				$id 	= $this->createFieldID($field,$no);
				
				echo '<input type="hidden"  data-field-post-name="'.$fieldPostName.'"/>';
				for($a = 0 ; $a < sizeof($field['value']) ; $a++)
				{
					$checked = '';
					if(isset($field['selected']) && is_array($field['selected']) && in_array($a,$field['selected']))
					{
						$checked = 'checked="checked"';	
					}				
					echo '<div class="wrapcheckboxes"><label>'.$field['display'][$a].'</label>';
					echo '<input '.$checked.' type="'.$field['type'].'" '.((isset($multiple))?$multiple:"").' name="'.$field['type'].'_'.$field['id'].'[]" class="radio_fields" disabled="disabled" ></div>';
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
					  <div class="time_fields" id="yyyy_'.$no.'">
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
		function piereg_get_small_string($string,$lenght=100,$atitional_string = "...."){
			$string = strip_tags(html_entity_decode( $string , ENT_COMPAT, 'UTF-8'));
			if(strlen($string) > $lenght){
				$string = wordwrap($string, $lenght, "<br />", true);
				$string = explode("<br />",$string);
				return $string[0].$atitional_string;
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
			echo '<input disabled="disabled" type="text" class="input_fields" data-field-post-name="first_name">';
			echo '<input type="hidden" id="default_'.$field['type'].'">';
			$label2 = (isset($field['label2']) and !empty($field['label2']))?$field['label2'] : __("Last Name","piereg");
			echo '</div><div id="field_label2_'.$no.'" class="label_position"><label>'.$label2.'</label></div><div class="fields_position">  <input disabled="disabled" type="text" class="input_fields" data-field-post-name="last_name">';
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
			
			if(isset($field['recaptcha_type']) && $field['recaptcha_type'] == 2)
				echo '<img id="captcha_img" src="'.plugins_url('pie-register').'/images/new-recatpcha.png" data-captcha-img-src="'.plugins_url('pie-register').'/images/" />';
			else
				echo '<img id="captcha_img" src="'.plugins_url('pie-register').'/images/recatpcha.jpg" data-captcha-img-src="'.plugins_url('pie-register').'/images/" />';
			
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
		function addPricing($field,$no)
		{
			$fieldPostName = $this->getPieregFieldName($field,$no);
			$name 	= $this->createFieldName($field,$no);
			$id 	= $this->createFieldID($field,$no);
			
			echo '<input type="hidden"  data-field-post-name="'.$fieldPostName.'"/>';
			echo '<input type="hidden" id="default_'.$field['type'].'">';
			if(sizeof($field['display']) > 0)
			{
				echo '<div class="piereg_pricing_radio radio_wrap" '.((isset($field['field_as']) && $field['field_as'] == 1)?'style="display: none;"':((!isset($field['field_as']))?'style="display: none;"':"")).'>';
				echo '<input type="hidden"  data-field-post-name="'.$fieldPostName.'"/>';
				for($a = 0 ; $a < sizeof($field['display']) ; $a++)
				{
					$checked = '';
					if(isset($field['selected']) && is_array($field['selected']) && in_array($a,$field['selected']))
					{
						$checked = 'checked="checked"';	
					}				
					echo '<label>'.$field['display'][$a].'</label>';
					echo '<input '.$checked.' type="radio" name="'.$field['type'].'_'.$field['id'].'[]" class="radio_fields" disabled="disabled" >';
				}
				echo '<input type="hidden" id="default_'.$field['type'].'">';
				echo '</div>';
			}
			
			echo '<div class="piereg_pricing_select select_wrap" '.((isset($field['field_as']) && $field['field_as'] != 1)?'style="display: none;"':"").'>';
			echo '<select '.$multiple.' id="'.$name.'" name="piereg_pricing" data-field_id="piereg_pricing_field_'.$no.'" disabled="disabled"  data-field-post-name="'.$fieldPostName.'">';
			if(sizeof($field["display"]) > 0)
			{
				for($a = 0 ; $a < sizeof($field["display"]) ; $a++)
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
			echo '</div>';
			
		}		
		function createFieldName($field,$no)
		{
			return "field_[".$field['id']."]";		
		}
		function createFieldID($field,$no)
		{
			return "field_".$field['id'];	
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
			$fp = fopen ( LOG_FILE , 'a+' );
			fwrite ( $fp, $this->ipn_status . "\r\n" );
			fclose ( $fp ); // close file
			chmod ( LOG_FILE , 0600 );
		}
		
		public function get_user_by_hash($user_data){		
			$data_array = explode("__",$user_data);
			$user		= get_user_by('id', $data_array[1]);
			return $user->user_email;		
		}
		
		public function validate_ipn() {
			
			/*
				*	IPN LOG
			*/
			$user_payment_log 				= array();
			$user_payment_log['method'] 	= "Paypal";
			$user_payment_log['type'] 		= "Hosted Button IPN ";
			$user_payment_log['responce'] 	= $_REQUEST;
			$user_payment_log['date'] 		= date_i18n("d-m-Y H:i:s");
			$user_email						= $this->get_user_by_hash($_REQUEST['custom']);
			$log_message = print_r( $user_payment_log, 1 );
			$this->pr_payment_log($log_message);
			$this->piereg_save_payment_log_option( $user_email, "PayPal", "Hosted Button IPN", $_REQUEST );
			unset($log_message);
			unset($user_payment_log);
			//IPN log end
			
			global $wpdb;
			$piereg = get_option(OPTION_PIE_REGISTER);
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
			if ($piereg['paypal_sandbox'] == "yes") {
				$fp = fsockopen ( 'tls://www.sandbox.paypal.com', "443", $err_num, $err_str, 60 );
				if(!$fp) {
					$fp = fsockopen ( 'ssl://www.sandbox.paypal.com', "443", $err_num, $err_str, 60 );
				}
			}else{
				$fp = fsockopen ( 'tls://www.paypal.com', "443", $err_num, $err_str, 60 );
	 			if(!$fp) {
					$fp = fsockopen ( 'ssl://www.paypal.com', "443", $err_num, $err_str, 60 );		
				}
			}
	 		
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
				fputs($fp, "User-Agent: Pie-Register IPN Validation Service\r\n");
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
				$this->ipn_status = "IPN NOT VERIFIED\n".print_r($this->ipn_response,1)."\n";
				$this->log_ipn_results(false);
				return false;
			} else {
				$this->ipn_status = "IPN VERIFIED";
				//////////// Verify User /////////////
				// paypal Variable our custom variable
				if( isset($_REQUEST['paypal']) && !empty($_REQUEST['paypal']) )
					$this->processPostPayment($_REQUEST['paypal']);
				//////////////////////////////////////
				$this->log_ipn_results(true); 
				header("HTTP/1.1 200 OK");
				die();
				return true;
			}
			header("HTTP/1.1 402 Payment Required");
			die();
		}
		function ValidPUser(){
			global $wpdb;
			//$piereg = get_option( 'pie_register' );
			$piereg = get_option(OPTION_PIE_REGISTER);
			
			if(isset($_POST['txn_id']) && $_GET['action'] == 'ipn_success'){
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
					//$option 		= get_option( 'pie_register' );
					$option 		= get_option(OPTION_PIE_REGISTER);
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
	
					if(!wp_mail($user_data->user_email, $subject, $message , $headers)){
						$this->pr_error_log("'The e-mail could not be sent. Possible reason: your host may have disabled the mail() function...'".($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
					}
					unset($user_data);
				}
				/******************************************************/
				$this->payment_success_cancel_after_register("payment=cancel");
			}else{
				return false;
			}
		}
		function processPostPayment( $custom_user_data )
		{
			if( empty($custom_user_data) )
				return false;
			
			add_filter( 'wp_mail_content_type', array($this,'set_html_content_type' ));
			
			$custom_user_data_decode = base64_decode($custom_user_data);
			$return_data = explode( "|", $custom_user_data_decode );
			$hash 		= $return_data[0];
			$user_id 	= $return_data[1];				 
			
			#get_usermeta deprecated
			$check_hash = get_user_meta( $user_id, "hash", true);
			
			if($check_hash != $hash)
				return false;
			
			if(!is_numeric($user_id ))
				return false;	
				
			$user 		= new WP_User($user_id);
			$option 	= get_option(OPTION_PIE_REGISTER);
			update_user_meta( $user_id, 'active',1);
			update_user_meta( $user_id, 'pie_paypal_txn_id',$_POST['txn_id']);
			update_user_meta( $user_id, 'pie_paypal_payer_id',$_POST['payer_id']);
			update_user_meta( $user_id, 'pie_paypal_ipn_response',serialize($_POST));
			
			//Sending E-Mail to newly active user
			$subject 		= html_entity_decode($option['user_subject_email_payment_success'],ENT_COMPAT,"UTF-8");
			$user_email 	= $user->data->user_email;
			$message_temp = "";
			if($option['user_formate_email_payment_success'] == "0"){
				$message_temp	= nl2br(strip_tags($option['user_message_email_payment_success']));
			}else{
				$message_temp	= $option['user_message_email_payment_success'];
			}
			
			$message		= $this->filterEmail($message_temp,$user);
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
			if(!wp_mail($user_email, $subject, $message , $headers)){
				$this->pr_error_log("'The e-mail could not be sent. Possible reason: your host may have disabled the mail() function...'".($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
			}
		}	
		
		function Add_payment_option_PaypalStandard($field_as)
		{
			$PR_options = "";
			$check_payment = PieRegister::get_pr_global_options();
			if( $check_payment["enable_paypal"] == 1 && !empty($check_payment['paypal_butt_id']) )
			{
				if( $field_as == 0 )
					$PR_options = '<div class="piereg_payment_selection piereg_payment_selection_paypalStandard"><label><input type="radio" name="select_payment_method" id="select_payment_method_PaypalStandard" value="PaypalStandard" data-img="'.plugins_url("images/PaypalStandard-logo.png",__FILE__).'" class="input_fields  radio_fields piereg_select_payment_method" /><img src="'.plugins_url("images/paypal_std_btn.png",__FILE__).'" /></label></div>';
				else
					$PR_options = '<option value="PaypalStandard" data-img="'.plugins_url("images/PaypalStandard-logo.png",__FILE__).'">'.__("Paypal (Standard)","piereg").'</option>';
			}
			return $PR_options;
		}
		
		function set_html_content_type() 
		{
			return 'text/html';
		}
		function deleteUsers($user_id = 0,$user_email = "",$user_registered = "")
		{
			$option 		= get_option(OPTION_PIE_REGISTER);
			
			if( isset($options['enable_paypal']) && $options['enable_paypal'] == 1 )
			{
				$grace			= ((int)$option['grace_period']);
			}
			else if(($this->check_enable_payment_method()) == "true" )
			{
				$grace			= ((int)$option['payment_setting_remove_user_days']);
			}
			else
			{
				$grace			= ((int)$option['grace_period']);
			}
			
			if( ($grace != 0 and $user_id != 0) and ($user_email != "" and $user_registered != "") and $register_type[0] != "admin_verify")
			{
				$date			= date_i18n("Y-m-d 00:00:00",strtotime("-{$grace} days"));
				
				if($user_registered < $date)
				{
					global $errors;
					$errors = new WP_Error();
					
					$this->wp_mail_send($user_email,"user_perm_blocked_notice");
					global $wpdb;
					$user_table = $wpdb->prefix."users";
					$user_meta_table = $wpdb->prefix."usermeta";
					if(!$wpdb->query( $wpdb->prepare("DELETE FROM `".$user_meta_table."` WHERE `user_id` =  %s", $user_id) )){
						$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
					}
					if(!$wpdb->query( $wpdb->prepare("DELETE FROM `".$user_table."` WHERE `ID` = %s", $user_id) )){
						$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
					}
					
					$errors->add('login-error',apply_filters("piereg_Invalid_username_or_password",__('Invalid username or password.','piereg')));
					
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
		function outputRegForm($fromwidget=false,$form_id = "0",$title="true",$description="true"){
			$form = new Registration_form_template();
			$success 	= '' ;
			$error 		= '' ;
			$option 	= get_option(OPTION_PIE_REGISTER);
			$registration_from_fields = '<div class="pieregformWrapper pieregWrapper">';
			$registration_from_fields .= '<div id="show_pie_register_error_js" class="piereg_entry-content"></div>';
			
			$IsWidgetForm = "";
			if($fromwidget)
				$IsWidgetForm = "widget_";
				
			$registration_from_fields .= '<div id="pie_register_reg_form">';
			
			/* Anyone can register */
			if($this->is_anyone_can_register() || $this->is_pr_preview){
				if(false === $this->is_pr_preview):
					$registration_from_fields .= '<form enctype="multipart/form-data" id="pie_'.(trim($IsWidgetForm)).'regiser_form" method="post" action="'.$_SERVER['REQUEST_URI'].'" data-form="'.$form_id.'">';
				else:
					$registration_from_fields .= '<div class="prRegFormPreview" id="pie_'.(trim($IsWidgetForm)).'regiser_form" data-form="'.$form_id.'">';
				endif;
				
					$output = $form->printFields($fromwidget,$form_id,$title,$description);
					if($form->countPageBreaks() > 1){
						$registration_from_fields .= '<div class="piereg_progressbar"></div>';
					}
					$registration_from_fields .= $output;
					if(false === $this->is_pr_preview):
						$registration_from_fields .= '</form>';
					else:
						$registration_from_fields .= '</div>';
					endif;
					
				}else{
					$registration_from_fields .= '<div class="alert alert-warning"><p class="piereg_warning">'.__("User registration is currently not allowed.","piereg").'</p></div>';
				}
				$registration_from_fields.='</div></div>';
				return $registration_from_fields;
			
			}
		function showForm($id="",$title="true",$description="true")
		{
			global $errors;
			$option 		= get_option(OPTION_PIE_REGISTER);
			add_filter( 'wp_mail_content_type', array($this,'set_html_content_type' ));
			$output = '<div class="piereg_container pieregWrapper">';
			if(isset($_POST['success']) && $_POST['success'] != "")
				$output .= '<p class="piereg_message">'.apply_filters('piereg_messages',__($_POST['success'],"piereg")).'</p>';
			if(isset($_POST['error']) && $_POST['error'] != "")
				$output .= '<p class="piereg_login_error">'.apply_filters('piereg_messages',__($_POST['error'],"piereg")).'</p>';
			
			if(isset($_POST['registration_success']) && $_POST['registration_success'] != ""){
				$output .= '<p class="piereg_message">'.apply_filters('piereg_messages',__($_POST['registration_success'],"piereg")).'</p>';
				unset($_POST);
			}
			if(isset($errors->errors) && sizeof($errors->errors) > 0)
			{
				$error = "";
				foreach($errors->errors as $key=>$err)
				{
					if($key != "login-error")
						$error .= $err[0] . "<br />";
				}
				if(!empty($error))
					$output .= '<p class="piereg_login_error">'.apply_filters('piereg_messages',__($error,"piereg")).'</p>';
			}
			$output .= $this->outputRegForm(FALSE,$id,$title,$description);
			$output .= '</div>';
			return $output;
		}
		function showLoginForm()
		{
			$this->piereg_ssl_template_redirect();
			global $errors,$pagenow;
			$option 		= $this->get_pr_global_options();
			
			
			if(isset($_GET['pr_renew_account']) && $_GET['pr_renew_account'] == true)
			{
				$_POST['warning'] = (__("Please renew your account","piereg"));
				if( file_exists(PIEREG_DIR_NAME . "/renew_account.php") )
					include_once("renew_account.php");
				
				$is_renew_after_auth = false;
				$user_array = array();
				
				if(isset($_GET['auth'], $_GET['auth_key']) && !empty($_GET['auth']) && !empty($_GET['auth_key']) )
				{
					$user_name = esc_sql(urldecode($_GET['auth']));
					$auth_key = esc_sql(urldecode($_GET['auth_key']));
					
					$user_name = urldecode(base64_decode($user_name));
					$user = get_user_by("login",$user_name);
					
					if(!empty($user))
					{
						$auth_key = esc_sql($_GET['auth_key']);
						$auth_key_hash = get_user_meta($user->ID,"pr_renew_account_hash", true);
						if(!empty($auth_key_hash))
						{
							if(trim($auth_key) == trim($auth_key_hash))
							{
								$is_renew_after_auth = true;
								$user_array['username'] = $user->data->user_login;
								$user_array['email'] = $user->data->user_email;
							}
						}
					}
				}
				
				$PR_show_renew_account = PR_show_renew_account($is_renew_after_auth,$user_array);
				return $PR_show_renew_account;
			}
			else{
				$this->set_pr_stats("login","view");
				if( file_exists(PIEREG_DIR_NAME . "/login_form.php") )
					include_once("login_form.php");
				$output = pieOutputLoginForm();
				return  $output;
			}
		}
		
		function showForgotPasswordForm()
		{
			$this->piereg_ssl_template_redirect();
			global $errors;
			
			$option 		= get_option(OPTION_PIE_REGISTER);
			if(is_user_logged_in() && $option['redirect_user']==1 )
			{
				$this->afterLoginPage();
				return "";	
			}	
			
			else
			{
				$this->set_pr_stats("forgot","view");
				
				if( file_exists(PIEREG_DIR_NAME . "/forgot_password.php") )
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
				
				global $current_user;			
				get_currentuserinfo();
				$form_id = get_user_meta($current_user->ID,"user_registered_form_id",true);
				if(isset($_GET['edit_user']) && $_GET['edit_user'] == "1"){
					$form 		= new Edit_form($current_user,$form_id);
					if(isset($_POST['pie_submit_update'])  ) {
						$form->error = "";
						$errors = new WP_Error();
						$errors = $form->validateRegistration($errors);	
						if(sizeof($errors->errors) > 0) {
							foreach($errors->errors as $err)
							{
								$form->error .= $err[0] . "<br />";	
							}		  	
						}	
						else
						{
							$user_data = array('ID' => $current_user->ID);
							if(isset($_POST['url'])) {
								$user_data["user_url"] =  $_POST['url'];	 
								$form->pie_success = 1;
							}
							 
							if($current_user->data->user_email != $_POST['e_mail']) {
								$user_data["user_email"] =  $_POST['e_mail'];
								$form->pie_success = 1;
							}
							if(wp_check_password( $_POST['old_password'], $current_user->data->user_pass, $current_user->ID ) && $_POST['password'] != '' && $_POST['password'] == $_POST['confirm_password'])
							{
								$user_data["user_pass"] =  $_POST['password'];
								$form->pie_success = 1;
							}
							
							# newlyAddedHookFilter 
							do_action( 'piereg_update_profile_event', $current_user->ID, $form_id, $_POST );
							
							$id = wp_update_user( $user_data );						
							$form->UpdateUser();
						}
								
					}
					$output = '';
					$output .= '<div class="piereg_container pieregWrapper">';	
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
					
					if( file_exists($this->plugin_dir."/edit_form.php") )
						require_once($this->plugin_dir."/edit_form.php");
					
					$output.= edit_userdata($form_id);
					$output .= '</div>';
					return $output;
				}
				else
				{
					$form_id = get_user_meta($current_user->ID,"user_registered_form_id",true);
					$profile_front = new Profile_front($current_user,$form_id);
					$profile_form_data = $profile_front->print_user_profile($form_id);
					return $profile_form_data;
				}
			}
			else
			{
				$notloggedinmsg = __('Please','piereg').' <a class="linkStyle1" href="'.wp_login_url().'">'. __('login','piereg').'</a> '.__('to see your profile','piereg');
				
				$notloggedinmsg = apply_filters('piereg_profile_if_not_loggedin',$notloggedinmsg); # newlyAddedHookFilter
				
				return $notloggedinmsg;
			}	
		}
		
		function show_renew_account()
		{
			$this->piereg_ssl_template_redirect();
			if( file_exists(PIEREG_DIR_NAME . "/renew_account.php") )
				include_once("renew_account.php");
			
			$show_renew_account = PR_show_renew_account();
			return $show_renew_account;
		}
		
		function afterLoginPage()
		{
			global $wpdb,$current_user;
			$option = $this->get_pr_global_options();		
			/*
				Get after Logged in url by current user role
				*/
			
			$redirecturi = $this->ifRedirectUrlSet($current_user);			
			if($redirecturi) {	
				$this->pie_after_login_page_redirect_url = $redirecturi;			
				$this->afterLoginPageRedirect($option['social_site_popup_setting'],$this->pie_after_login_page_redirect_url,true);
				exit;
			}
						
			$logged_in_url = "";
			$logged_in_page = "";
			
			if( $this->piereg_pro_is_activate ) {
				
				$piereg_table_name=$wpdb->prefix."pieregister_redirect_settings";
				$current_user = wp_get_current_user();
				$current_user_roles = "'".implode("','",$current_user->roles)."'";
				$sql = "SELECT `logged_in_url`,`logged_in_page_id` FROM {$piereg_table_name} WHERE `user_role` IN({$current_user_roles}) LIMIT 1";
				$db_result = $wpdb->get_results( $wpdb->prepare($sql, '') ); #WPS_IN
				if(isset($wpdb->last_error) && !empty($wpdb->last_error)){
					$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
				}
				
				if($db_result){
					foreach($db_result as $db_result_val){
						if(!empty($db_result_val->logged_in_url))
							$logged_in_url = trim(urldecode($db_result_val->logged_in_url));
						if(!empty($db_result_val->logged_in_page_id))
							$logged_in_page = intval($db_result_val->logged_in_page_id);
					}
				}
			}
			
			if(!empty($logged_in_url) && ($logged_in_page == 0 || $logged_in_page == "")){
				$this->pie_after_login_page_redirect_url = $logged_in_url;
				$this->afterLoginPageRedirect($option['social_site_popup_setting'],$this->pie_after_login_page_redirect_url,false);
				exit;
			}elseif(!empty($logged_in_page) && $logged_in_page > 0){
				$this->pie_after_login_page_redirect_url = get_permalink($logged_in_page);
				$this->afterLoginPageRedirect($option['social_site_popup_setting'],$this->pie_after_login_page_redirect_url,true);
				exit;
			}
			elseif( $option['after_login'] == 'url' && !empty($option['alternate_login_url']) ){
				$this->pie_after_login_page_redirect_url = $option['alternate_login_url'];
				$this->afterLoginPageRedirect($option['social_site_popup_setting'],$this->pie_after_login_page_redirect_url,false);
				exit;
			}
			elseif( $option['after_login'] > 0 ){
				if($option['after_login'] != 'url'){
					$this->pie_after_login_page_redirect_url = get_permalink($option['after_login']);
					$this->afterLoginPageRedirect($option['social_site_popup_setting'],$this->pie_after_login_page_redirect_url,true);
					exit;
				}
			}
			elseif( isset($_GET['redirect_to']) && $_GET['redirect_to'] != "" && !current_user_can( 'administrator' ) ){
				// When account login with activation link and not any login page assigned
				if( (isset($_GET['action']) && $_GET['action'] == "activate") && (isset($_GET['activation_key']) && $_GET['activation_key'] != "") ) {
					$this->pie_after_login_page_redirect_url = site_url();
					$this->afterLoginPageRedirect($option['social_site_popup_setting'],$this->pie_after_login_page_redirect_url,false);
				} else {
					$this->pie_after_login_page_redirect_url = $_GET['redirect_to'];
					$this->afterLoginPageRedirect($option['social_site_popup_setting'],$this->pie_after_login_page_redirect_url,false);
				}
				exit;					
			}else{
				$this->pie_after_login_page_redirect_url = site_url();
				$this->afterLoginPageRedirect($option['social_site_popup_setting'],$this->pie_after_login_page_redirect_url,false); 
				exit;
			}
			exit;
		}
		
		function ifRedirectUrlSet($user)
		{
			if ( isset( $_REQUEST['redirect_to'] ) ) {
				$redirect_to = $_REQUEST['redirect_to'];
				// Redirect to https if user wants ssl
				if ( $secure_cookie && false !== strpos($redirect_to, 'wp-admin') )
					$redirect_to = preg_replace('|^http://|', 'https://', $redirect_to);
			} else {
				$redirect_to = admin_url();
			}
			
			$requested_redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '';
			$redirect_to = apply_filters( 'login_redirect', $redirect_to, $requested_redirect_to, $user );
			$last_segment = basename($redirect_to);
			
			if( $last_segment == 'wp-admin' || $redirect_to == admin_url() )
			{
				return false;
			}
			
			return $redirect_to;
		}
		
		function afterLoginPageRedirect($social_site_popup_setting = 0,$url,$safe = true){
			if(
				   $social_site_popup_setting == 1 and 
				   $_POST['social_site']  == "true"
			   )
			{
				//Redirect thru JS File
			}
			else{
				if($safe)
					wp_safe_redirect($url);
				else
					wp_redirect($url);
			}
			exit;
		}
		
		function add_ob_start(){
			ob_start();
		}
		
		# noutusing
		//Add paypal
		function add_payment_method_script($methods){
			$check_payment = get_option(OPTION_PIE_REGISTER);
			if($check_payment["enable_paypal"] == 1 && !(empty($check_payment['paypal_butt_id'])) ){
				$paypal_regular = array('payment'=>'You Select Paypal (Standard) payment method','image'=>'<img src="'.plugins_url("images/PaypalStandard-logo.png",__FILE__).'" style="max-width: 150px;padding-top: 20px;" />');
				
				if( is_array($methods) )
				{
					array_push($methods,$paypal_regular);
				}
			}
			return $methods;
		}
		function add_select_payment_script(){
		}
		function get_payment_content_area()
		{
			$data = '<div id="show_payment_method_image"></div>';
			$data .= '<div id="show_payment_method"></div>';
			return $data;
		}
		function show_icon_payment_gateway() // for paypal
		{
			
			$button = get_option(OPTION_PIE_REGISTER);
			if(!(empty($button['paypal_butt_id'])) && $button['enable_paypal']==1)
			{
				?>
				  <div class="fields_options submit_field">
					<!--<img style="width:100%;" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" />-->
				  	<img style="width:100%;" src="<?php echo plugins_url('/images/btn_buynowCC_LG.gif',__FILE__); ?>" />
                  </div>
				<?php
			}
		}
		
		function renew_account()
		{
			global $errors;
			$errors = new WP_Error();
			
			if(isset($_POST['select_payment_method']) and trim($_POST['select_payment_method']) != "")
			{
				if(isset($_GET['auth'], $_GET['auth_key']) && !empty($_GET['auth']) && !empty($_GET['auth_key']) )
				{
					$user_name = esc_sql(urldecode($_GET['auth']));
					$auth_key = esc_sql(urldecode($_GET['auth_key']));
					$user_name = urldecode(base64_decode($user_name));
					$user = get_user_by("login",$user_name);
					if(!empty($user))
					{
						$auth_key = esc_sql($_GET['auth_key']);
						$auth_key_hash = get_user_meta($user->ID,"pr_renew_account_hash", true);
						if(!empty($auth_key_hash))
						{
							if(trim($auth_key) == trim($auth_key_hash))
							{
								if(isset($user->ID)){
									/*
										*	Check Pricing
									*/
									$user_id = $user->ID;
									
									$pricing_key_number = get_user_meta( $user_id , "piereg_pricing_key_number" , true );
									$piereg_user_registered_form_id = get_user_meta( $user_id, "user_registered_form_id", true );
									$piereg_use_starting_period = get_user_meta( $user_id , "use_starting_period" , true );
									$piereg_form_pricing_fields = get_option("piereg_form_pricing_fields");
									$piereg_pricing_fields = "";
									$user_array = (array) $user;
									if(isset($piereg_form_pricing_fields['form_id_'.(intval($piereg_user_registered_form_id))])){
										$piereg_pricing_fields = $piereg_form_pricing_fields['form_id_'.(intval($piereg_user_registered_form_id))];
										$user_array['piereg_pricing']['pricing_key_user_role'] = $piereg_pricing_fields['role'][$pricing_key_number];
										if(empty($piereg_use_starting_period)){
											$user_array['piereg_pricing']['pricing_payment_amount'] = $piereg_pricing_fields['starting_price'][$pricing_key_number];
										}else{
											$user_array['piereg_pricing']['pricing_payment_amount'] = $piereg_pricing_fields['then_price'][$pricing_key_number];
										}
									}
									$user = (object) $user_array;
								}
							}
							else
							{
								$user = new WP_Error("invalid_hash","User hash mismatch");
							}
						}
						else{
							$user = new WP_Error("piereg_invalid_auth_keys_hash",apply_filters("piereg_invalid_auth_keys_hash",__("Invalid auth keys hash","piereg")));
						}
					}
					else{
						$user = new WP_Error("piereg_invalid_auth_keys",apply_filters("piereg_invalid_auth_keys",__("Invalid User","piereg")));
					}
				}
				else{
					$user = $this->piereg_user_login($_POST['user_name'],$_POST['u_pass']);
					wp_logout();
				}
				
				$piereg = $this->get_pr_global_options();
				
				if ( is_wp_error($user))
				{
					$user_login_error = $user->get_error_message();
					if(strpos(strip_tags($user_login_error),'Invalid username',5) > 6){
						$user_login_error = '<b>'.ucwords(__("error","piereg")).'</b>: '.__("Invalid username","piereg");
					}
					else if(strpos(strip_tags($user_login_error),'password you entered',9) > 10){
						$user_login_error = '<strong>'.ucwords(__("error","piereg")).'</strong>: '.__("The password you entered for the username","piereg").' <strong>'.$_POST['user_name'].'</strong> '.__("is incorrect","piereg");
					}
					$errors->add('renew-account-error',apply_filters("piereg_renew_account_error",$user_login_error));
				}
				elseif($user->ID != 0 or $user->ID != ""){
					$user_meta = get_user_meta($user->ID);
					if($user_meta['active'][0] == 0){
						if(isset($_POST['select_payment_method']) and $_POST['select_payment_method'] != "" )//Goto payment method Like check_payment_method_paypal
						{
							$_POST['user_id'] = $user->data->ID;
							$_POST['e_mail'] = $user->data->user_email;
							$_POST['username'] = $user->data->user_login;
							$_POST['renew_account_msg'] = apply_filters("piereg_Renew_Account",__("Renew Account","piereg"));
							$_POST['renew_account'] = "Renew Account";
							do_action("piereg_before_renew_account_hook",$user);
							do_action("check_payment_method_".$_POST['select_payment_method'],$user);
						}
					}else{
						$_POST['success'] = apply_filters("you_are_already_active",__($piereg['payment_already_activate_msg'],"piereg"));
					}
				}
				else{
					$_POST['error'] = apply_filters("piereg_Invalid_Username_or_Password",__("Invalid Username or Password","piereg"));
				}
			}
			else{
				$_POST['error'] = apply_filters("piereg_Please_Select_any_payment_method",__("Please Select any payment method","piereg"));
			}
		}
		
		function piereg_user_login($username,$password,$remember = false){
			$result = array();
			if($username != "" && $password != ""){
				$creds = array();
				$creds['user_login'] 	= trim($username);
				$creds['user_password'] = trim($password);
				$creds['remember'] 		= ((!empty($remember))?true:false);
				$piereg_secure_cookie = $this->PR_IS_SSL();
				if($this->piereg_authentication($_POST['log'],$_POST['pwd'])){
					$user = wp_signon( $creds, $piereg_secure_cookie);
					if ( !is_wp_error($user) ){
						$this->piereg_delete_authentication();
					}
				}else{
					$user = new WP_Error('piereg_authentication_failed', __("We are sorry, but this IP range has been blocked due to too many recent failed login attempts.","piereg"));
				}
				
				if(isset($user->ID)){
					/*
						*	Check Pricing
					*/
					$user_id = $user->ID;
					
					$pricing_key_number 			= get_user_meta( $user_id, "piereg_pricing_key_number", true );
					$piereg_user_registered_form_id = get_user_meta( $user_id, "user_registered_form_id", true );
					$piereg_use_starting_period 	= get_user_meta( $user_id, "use_starting_period" , true );
					$piereg_form_pricing_fields 	= get_option("piereg_form_pricing_fields");
					$piereg_pricing_fields = "";
					$user_array = (array) $user;
					if(isset($piereg_form_pricing_fields['form_id_'.(intval($piereg_user_registered_form_id))])){
						$piereg_pricing_fields = $piereg_form_pricing_fields['form_id_'.(intval($piereg_user_registered_form_id))];
						$user_array['piereg_pricing']['pricing_key_user_role'] = $piereg_pricing_fields['role'][$pricing_key_number];
						if(empty($piereg_use_starting_period)){
							$user_array['piereg_pricing']['pricing_payment_amount'] = $piereg_pricing_fields['starting_price'][$pricing_key_number];
						}else{
							$user_array['piereg_pricing']['pricing_payment_amount'] = $piereg_pricing_fields['then_price'][$pricing_key_number];
						}
					}
					$user = (object) $user_array;
					
					/***** End Pricing *****/
				}
				$result = $user;
			}
			else{
				$result['error'] = apply_filters('piereg_Invalid_Fields', __("Invalid Field(s)",'piereg')); # newlyAddedHookFilter
			}
			return $result;
		}
		function wp_mail_send($to_email = "",$key = "",$additional_msg = "",$msg = "",$email_variable = array())
		{
			global $errors;
			$errors = new WP_Error();
			if(trim($to_email) != "" and trim($key) != "" )
			{
				$email_types = get_option(OPTION_PIE_REGISTER);
				
				$message_temp = "";
				if($email_types['user_formate_email_'.$key] == "0"){
					$message_temp	= nl2br(strip_tags($email_types['user_message_email_'.$key]));
				}else{
					$message_temp	= $email_types['user_message_email_'.$key];
				}
				
				$message  		= $this->filterEmail($message_temp,$to_email,"","",$email_variable);
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
					$headers .= "Return-Path: {$reply_to_email}\r\n";
				}else{
					$headers .= "Return-Path: {$from_email}\r\n";
				}
				
				if(!mail($to,$subject,$message,$headers))
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
			if(!$wpdb->query( $wpdb->prepare($sql, $array_ids) )){
				$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
			}
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
			
			$sql 	= "UPDATE `".$codetable."` SET `status`= %s WHERE `id` IN($format)";
			$args[] = $status;
			$args	= array_merge($args, $array_ids);
			
			if(!$wpdb->query( $wpdb->prepare($sql, $args) ) ){
				$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
			}
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
						$sql_res_sel = $wpdb->get_var( $wpdb->prepare( "SELECT `name` FROM `{$codetable}` WHERE BINARY `name` = %s", $_POST['data']['value']) );
						if(!$sql_res_sel)
							$sql = "UPDATE `{$codetable}` SET `name`=%s WHERE `id` = '{$inv_code_id}'";
						else{
							echo "duplicate";
							die();
						}
					}
					else if(trim($_POST['data']['type']) == "code_usage")
					{
						if(is_numeric($_POST['data']['value']) && $_POST['data']['value'] >= 0  && trim($_POST['data']['value']) != ''){
							$sql = "UPDATE `{$codetable}` SET `code_usage`=%s WHERE `id` = ".((int)$_POST['data']['id'])."";
						}
					}
					
					$result = $wpdb->query( $wpdb->prepare($sql, esc_sql($_POST['data']['value'])) );
					
					if(isset($wpdb->last_error) && !empty($wpdb->last_error)){
						$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
					}
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
		/*
			*	Restrict Widgets Function
		*/
		function piereg_restrict_widgets(){
			global $wp_registered_widgets;
			
			$piereg_widgets = get_option(PIEREGISTER_RW_OPTIONS);
			$piereg_widgets = $piereg_widgets ? $piereg_widgets : array();
			
			foreach($piereg_widgets as $id => $data){
				$restrict_widget = true;
				$rw_options = $data['pr_ristrict_widget'];
				foreach($rw_options as $key=>$val){
					if($key == "visibility_status" && $val == "Before Login")
					{
						$restrict_widget = (is_user_logged_in());
					}
					else
					{
						if(is_user_logged_in() && $restrict_widget){
							global $current_user;
							$current_user = wp_get_current_user();
							$restrict_widget = (!in_array($key,(array)$current_user->roles));
							
						}						
					}
				}
				
				/*
					*	False  = Show
					*	True = Hide
				*/
				if($restrict_widget){
					unset($wp_registered_widgets[$id]);
				}
			}
		}
		/*
			*	Pie-Register log file download
		*/
		function pr_logfile_download_or_view(){
			if(isset($_POST['piereg_download_log_file']) && $_POST['piereg_download_log_file'] != ""){
				$this->piereg_get_wp_plugable_file(true); // require_once pluggable.php
				if(isset($_POST['piereg_download_log_file_nonce']) && wp_verify_nonce( $_POST['piereg_download_log_file_nonce'], 'piereg_wp_download_log_file_nonce' ))
				{
					if (file_exists(PIEREG_DIR_NAME."/log/piereg_log.log")) {
						header('Content-Description: File Transfer');
						header('Content-Type: application/octet-stream');
						header('Content-Disposition: attachment; filename='.("PieRegister_logfile_".date_i18n("Y-m-d-H-i-s").".log"));
						header('Expires: 0');
						header('Cache-Control: must-revalidate');
						header('Pragma: public');
						header('Content-Length: ' . filesize(PIEREG_DIR_NAME."/log/piereg_log.log"));
						readfile(PIEREG_DIR_NAME."/log/piereg_log.log");
						exit;
					}
				}else{
					$_POST['error_message'] = __("Sorry, your nonce did not verify","piereg");
				}
			}
		}
		/*
			*	Pie-Register Log File View 
		*/
		function piereg_get_log_file(){
			//Log File Dir
			$file_dir = PIEREG_DIR_NAME."/log/piereg_log.log";
			$result = "";
			$logFileData = $this->read_upload_file($file_dir);
			$result = htmlentities($logFileData);
			return $result;
		}
		
		/*
			*	Export Database
		*/
		function piereg_export_db(){
			
			if(isset($_POST['import_export_settings']) && $_POST['import_export_settings']){
				$this->piereg_get_wp_plugable_file(true); // require_once pluggable.php
				//Export All Settings
				if(isset($_POST['export_general_settings'])){
					$this->export_default_options(true);
				}
				//Import All Settings
				elseif( isset($_POST['import_general_settings']) && isset($_FILES['import_general_settings_file']) && $_FILES['import_general_settings_file']['name'] != "" ){
					
					if(pathinfo($_FILES['import_general_settings_file']['name'],PATHINFO_EXTENSION) == "json"){
						$this->import_default_options();
					}else{
						$_POST['error'] = __("Invalid File","piereg");
					}
				}
				//Export Email Templates
				elseif(isset($_POST['export_email_template'])){
					$this->export_email_template(true);
				}
				//Import Email Templates
				elseif( isset($_POST['import_email_template']) && isset($_FILES['import_email_template_file']) && $_FILES['import_email_template_file']['name'] != "" ){
					if(pathinfo($_FILES['import_email_template_file']['name'],PATHINFO_EXTENSION) == "json"){
						$this->import_email_template();
					}else{
						$_POST['error'] = __("Invalid File","piereg");
					}
				}
				//Export Invitation Codes
				elseif(isset($_POST['export_invitations_codes'])){
					$this->export_invitation_codes(true);
				}
				//Import Invitation Codes
				elseif( isset($_POST['import_invitations_codes']) && isset($_FILES['import_invitations_codes_file']) && $_FILES['import_invitations_codes_file']['name'] != "" ){
					if(pathinfo($_FILES['import_invitations_codes_file']['name'],PATHINFO_EXTENSION) == "json"){
						$this->import_invitation_codes();
					}else{
						$_POST['error'] = __("Invalid File","piereg");
					}
				}
				//Export All Users with custom data
				elseif(isset($_POST['piereg_export_user_custom_data'])){
					$this->export_all_users_data_with_custom_fields(true);
				}
				//Import Invitation Codes
				elseif( isset($_POST['piereg_import_user_custom_data']) && isset($_FILES['import_all_users_data_with_custom_field']) && $_FILES['import_all_users_data_with_custom_field']['name'] != "" ){
					if(pathinfo($_FILES['import_all_users_data_with_custom_field']['name'],PATHINFO_EXTENSION) == "json"){
						$this->import_all_users_data_with_custom_fields();
					}else{
						$_POST['error'] = __("Invalid File","piereg");
					}
				}
				
			}
		}
		/*
			*	Export Default Options
		*/
		function export_default_options($is_download = true){
			global $wpdb;
			if(isset($_POST['piereg_export_general_settings']) && wp_verify_nonce( $_POST['piereg_export_general_settings'], 'piereg_wp_export_general_settings' ))
			{
				$piereg_plugin_db_version = get_option("piereg_plugin_db_version");
				$pieregister_restrict_widgets = get_option("pieregister_restrict_widgets");
				$piereg_currency = get_option("piereg_currency");
				$pie_can_states = get_option("pie_can_states");
				$pie_countries = get_option("pie_countries");
				$pie_us_states = get_option("pie_us_states");
				$pie_fields = get_option("pie_fields");
				$pie_fields = unserialize($pie_fields);
				$pie_fields_default = get_option("pie_fields_default");
				$pie_fields_meta = get_option("pie_fields_meta");
				$pie_register_active = get_option("pie_register_active");
				
				// Global Option 
				$pie_register = get_option("pie_register");
				unset($pie_register['enable_admin_notifications'],$pie_register['admin_sendto_email'],$pie_register['admin_from_name'],$pie_register['admin_from_email'],$pie_register['admin_to_email'],$pie_register['admin_bcc_email'],$pie_register['admin_subject_email'],$pie_register['admin_message_email_formate'],$pie_register['admin_message_email']);
				
				//Get email template types
				$email_type = get_option("pie_user_email_types");
				foreach($email_type as $email_temp_key=>$email_temp_val){
					unset($pie_register['user_from_name_'.$email_temp_key],$pie_register['user_from_email_'.$email_temp_key],$pie_register['user_to_email_'.$email_temp_key],$pie_register['user_subject_email_'.$email_temp_key],$pie_register['user_formate_email_'.$email_temp_key],$pie_register['user_message_email_'.$email_temp_key]);
				}
				
				
				$piereg_form_fields_id = get_option("piereg_form_fields_id");
				$piereg_forms_fields_options = array();
				$piereg_forms_fields_data = array();
				for($a=1;$a<=intval($piereg_form_fields_id);$a++)
				{
					$piereg_form_field_option = get_option("piereg_form_field_option_".$a);
					if($piereg_form_field_option){
						$piereg_forms_fields_options[$a] = $piereg_form_field_option;
					}
					$piereg_form_fields = get_option("piereg_form_fields_".$a);
					if($piereg_form_fields){
						$piereg_forms_fields_data[$a] = unserialize($piereg_form_fields);
					}
				}
				$piereg_form_pricing_fields = get_option("piereg_form_pricing_fields");
				
				
				$global_settings = array();
				$global_settings['piereg_plugin_db_version'] = $piereg_plugin_db_version;
				$global_settings['pieregister_restrict_widgets'] = $pieregister_restrict_widgets;
				$global_settings['piereg_currency'] = $piereg_currency;
				$global_settings['pie_can_states'] = $pie_can_states;
				$global_settings['pie_countries'] = $pie_countries;
				$global_settings['pie_us_states'] = $pie_us_states;
				$global_settings['pie_fields'] = $pie_fields;
				$global_settings['pie_fields'] = $pie_fields;
				$global_settings['pie_fields_default'] = $pie_fields_default;
				$global_settings['pie_fields_meta'] = $pie_fields_meta;
				$global_settings['pie_register_active'] = $pie_register_active;
				$global_settings['pie_register'] = $pie_register;
				
				$global_settings['piereg_form_fields'] = $piereg_forms_fields_data;
				$global_settings['piereg_form_field_option'] = $piereg_forms_fields_options;
				$global_settings['piereg_form_fields_id'] = $piereg_form_fields_id;
				$global_settings['piereg_form_pricing_fields'] = $piereg_form_pricing_fields;
				
				
				
				$redirect_settings_table_name = $wpdb->prefix."pieregister_redirect_settings";
				
				$sql = 'SELECT `id`, `user_role`, `logged_in_url`, `logged_in_page_id`, `log_out_url`, `log_out_page_id`, `status` FROM '.$redirect_settings_table_name.' ORDER BY `id` ASC';
				$result_redirect_settings = $wpdb->get_results( $wpdb->prepare($sql, '') ); #WPS
				if(isset($wpdb->last_error) && !empty($wpdb->last_error)){
					$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
				}
				
				$global_settings['pieregister_redirect_settings'] = $result_redirect_settings;
				
				if($is_download)
				{
					$json_global_settings = $this->piereg_array_to_json($global_settings);
					$this->set_json_header( 'pie-register-settings-'.date_i18n("Y-m-d") );
					echo $json_global_settings;
					exit;
				}
				
				return $global_settings;
			}else{
				$_POST['error_message'] = __("Sorry, your nonce did not verify","piereg");
			}
		}
		/*
			*	Import Default Options
		*/
		function import_default_options(){
			if(isset($_POST['piereg_import_general_settings']) && wp_verify_nonce( $_POST['piereg_import_general_settings'], 'piereg_wp_import_general_settings' ))
			{
				$json_file = $this->read_upload_file($_FILES['import_general_settings_file']['tmp_name']);
				$array_json_file = $this->piereg_json_to_array($json_file);
				
				if(isset($array_json_file['piereg_plugin_db_version']) && !empty($array_json_file['piereg_plugin_db_version'])){
					$import_file_db_version = $array_json_file['piereg_plugin_db_version'];
					$pie_plugin_db_version = get_option('piereg_plugin_db_version');
					if($pie_plugin_db_version != $import_file_db_version){
						$_POST['error'] = __("The file version is not compatible with this version","piereg");
						return false;
					}
				}else{
					$_POST['error'] = __("The file version is not compatible with this version","piereg");
					return false;
				}
				
				foreach($array_json_file as $arr_key=>$arr_val){
					switch($arr_key){
						case "pieregister_restrict_widgets":
						case "piereg_currency":
						case "pie_can_states":
						case "pie_countries":
						case "pie_us_states":
							if($arr_val){
								$db_option = get_option($arr_key);
								$result_merge_array = $this->piereg_array_replace_recursive($db_option, $arr_val);
								if(is_array($result_merge_array)){
									update_option($arr_key, $result_merge_array);
									$_POST['success_message'] = __("Successfully import all settings","piereg");
								}
								unset($db_option);
								unset($result_merge_array);
							}
						break;
						case "pie_fields":
							if($arr_val){
								$db_option = get_option($arr_key);
								$db_unserialize_option = unserialize($db_option);
								$result_merge_array = $this->piereg_array_replace_recursive($db_unserialize_option,$arr_val);
								if(is_array($result_merge_array)){
									$db_serialize_option = serialize($result_merge_array);
									update_option($arr_key,$db_serialize_option);
									$_POST['success_message'] = __("Successfully import all settings","piereg");
								}
								unset($db_option,$db_unserialize_option,$result_merge_array,$db_serialize_option);
							}
						break;
						case "pie_fields_default":
							if($arr_val){
								$db_option = get_option($arr_key);
								$result_merge_array = $this->piereg_array_replace_recursive($db_option,$arr_val);
								if($result_merge_array){
									update_option($arr_key,$result_merge_array);
									$_POST['success_message'] = __("Successfully import all settings","piereg");
								}
								unset($db_option);
								unset($result_merge_array);
							}
						break;
						case "pie_fields_meta":
							if($arr_val){
								$db_option = get_option($arr_key);
								$result_merge_array = $this->piereg_array_replace_recursive($db_option,$arr_val);
								if($result_merge_array){
									update_option($arr_key,$result_merge_array);
									$_POST['success_message'] = __("Successfully import all settings","piereg");
								}
								unset($db_option,$result_merge_array);
							}
						break;
						case "pie_register_active":
							if($arr_val){
								$db_option = get_option($arr_key);
								$result_merge_array = "";
								if(is_array($arr_val) && is_array($db_option)){
									$result_merge_array = $this->piereg_array_replace_recursive($db_option,$arr_val);
									if($result_merge_array){
										update_option($arr_key,$result_merge_array);
										$_POST['success_message'] = __("Successfully import all settings","piereg");
									}
								}else{
									$result_merge_array = ( ($arr_val != "")? $arr_val : $db_option );
									if($result_merge_array){
										update_option($arr_key,$result_merge_array);
										$_POST['success_message'] = __("Successfully import all settings","piereg");
									}
								}
								unset($db_option,$result_merge_array);
							}
						break;
						case "pie_register":
							if($arr_val){
								$db_option = get_option($arr_key);
								$result_merge_array = "";
								if( !empty($arr_val) ){
									$result_merge_array = $this->piereg_array_replace_recursive($db_option,$arr_val);
									if($result_merge_array){
										update_option($arr_key,$result_merge_array);
										$_POST['success_message'] = __("Successfully import all settings","piereg");
									}
								}
								unset($db_option,$result_merge_array);
							}
						break;
						case "piereg_form_fields":
							if($arr_val){
								if( is_array($arr_val) ){
									foreach($arr_val as $piereg_form_field_key=>$piereg_form_field_val){
										if(is_array($piereg_form_field_val)){
											$piereg_form_field_serialize = serialize($piereg_form_field_val);
											update_option( "piereg_form_fields_".$piereg_form_field_key , $piereg_form_field_serialize );
										}
									}
								}
							}
						break;
						case "piereg_form_field_option":
							if($arr_val){
								if( is_array($arr_val) ){
									foreach($arr_val as $piereg_form_field_key=>$piereg_form_field_val){
										if(is_array($piereg_form_field_val)){
											update_option( "piereg_form_field_option_".$piereg_form_field_key , $piereg_form_field_val );
											$_POST['success_message'] = __("Successfully import all settings","piereg");
										}
									}
								}
							}
						break;
						case "piereg_form_fields_id":
							$db_option = get_option($arr_key);
							$result_merge_array = "";
							if(is_array($arr_val) && is_array($db_option)){
								$result_merge_array = $this->piereg_array_replace_recursive($db_option,$arr_val);
								if($result_merge_array){
									update_option($arr_key,$result_merge_array);
									$_POST['success_message'] = __("Successfully import all settings","piereg");
								}
							}else{
								$result_merge_array = ( ($arr_val)? $arr_val : $db_option );
								if($result_merge_array){
									update_option($arr_key,$result_merge_array);
									$_POST['success_message'] = __("Successfully import all settings","piereg");
								}
							}
							unset($db_option);
							unset($result_merge_array);
						break;
						case "piereg_form_pricing_fields":
							if($arr_val){
								if(is_array($arr_val)){
									$db_option = get_option($arr_key);
									$result_merge_array = "";
									if(is_array($db_option)){
										$result_merge_array = $this->piereg_array_replace_recursive($db_option,$arr_val);
									}else{
										$result_merge_array = ( ($arr_val)? $arr_val : $db_option );
									}
									if($result_merge_array){
										update_option($arr_key,$result_merge_array);
										$_POST['success_message'] = __("Successfully import all settings","piereg");
									}
										
									unset($db_option,$result_merge_array);
								}
							}
						break;
						case "pieregister_redirect_settings":
							if(is_array($arr_val) && !empty($arr_val)){
								global $wpdb;
								$redirect_settings_table_name = $wpdb->prefix."pieregister_redirect_settings";
								$redirect_settings_sql = "CREATE TABLE IF NOT EXISTS `".$redirect_settings_table_name."` (
														  `id` int(11) NOT NULL AUTO_INCREMENT,
														  `user_role` varchar(100) NOT NULL,
														  `logged_in_url` text NOT NULL,
														  `logged_in_page_id` int(11) NOT NULL,
														  `log_out_url` text NOT NULL,
														  `log_out_page_id` int(11) NOT NULL,
														  `status` bit(1) NOT NULL DEFAULT b'1',
														  PRIMARY KEY (`user_role`),
														  UNIQUE KEY `id` (`id`)
														) ENGINE=MYISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
								
								if(!$wpdb->query($redirect_settings_sql))
								{
									$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
								}
								if(!$wpdb->query("DELETE FROM `".$redirect_settings_table_name."`"))
								{
									$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
								}
								$insert_sql = "INSERT INTO `".$redirect_settings_table_name."` (`id`, `user_role`, `logged_in_url`, `logged_in_page_id`, `log_out_url`, `log_out_page_id`, `status`) VALUES ";
								$_POST['success_message'] = __("Successfully import all settings","piereg");
								$count_query = 0;
								$query_values = "";
								foreach($arr_val as $inv_key=>$inv_val)
								{
									$count_query++;
									if(isset($inv_val['user_role']) && $inv_val['user_role'] != ""){
										$query_values .= "(".((isset($inv_val['id']) && $inv_val['id'] != "")?"'".$inv_val['id']."'": 'NULL').",";
										$query_values .= "'".($inv_val['user_role'])."',";
										$query_values .= "'".((isset($inv_val['logged_in_url']) && $inv_val['logged_in_url'] != "")?$inv_val['logged_in_url']: '')."',";
										$query_values .= "'".((isset($inv_val['logged_in_page_id']) && $inv_val['logged_in_page_id'] != "")?$inv_val['logged_in_page_id']: '0')."',";
										$query_values .= "'".((isset($inv_val['log_out_url']) && $inv_val['log_out_url'] != "")?$inv_val['log_out_url']: '')."',";
										$query_values .= "'".((isset($inv_val['log_out_page_id']) && $inv_val['log_out_page_id'] != "")?$inv_val['log_out_page_id']: '0')."',";
										$query_values .= "'".((isset($inv_val['status']) && $inv_val['status'] != "")?$inv_val['status']: '1')."'),";
									}
									if($count_query == 100){
										$count_query = 0;
										$query_values = rtrim($query_values,",");
										if(!empty($query_values))
										{
											if(!$wpdb->query($insert_sql.$query_values))
											{
												$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
											}
										}
										$query_values = "";
									}
								}
								if(!empty($query_values))
								{
									$query_values = rtrim($query_values,",");
									if(!$wpdb->query($insert_sql.$query_values))
									{
										$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
									}
								}
								unset($query_values,$insert_sql,$count_query,$redirect_settings_sql);
								$_POST['success_message'] = __("Successfully import all settings","piereg");
							}
						break;
					}
				}
			}else{
				$_POST['error_message'] = __("Sorry, your nonce did not verify","piereg");
			}
		}
		/*
			*	Export Invitaion's Code
		*/
		function export_invitation_codes($is_download = true){
			if(isset($_POST['piereg_export_invitations_codes']) && wp_verify_nonce( $_POST['piereg_export_invitations_codes'], 'piereg_wp_export_invitations_codes' ))
			{
				global $wpdb;
				$invitation_code_table_name = $wpdb->prefix."pieregister_code";
				$invitation_code_sql = "CREATE TABLE IF NOT EXISTS ".$invitation_code_table_name."(`id` INT( 5 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,`created` DATE NOT NULL ,`modified` DATE NOT NULL ,`name` TEXT NOT NULL ,`count` INT( 5 ) NOT NULL ,`status` INT( 2 ) NOT NULL ,`code_usage` INT( 5 ) NOT NULL) ENGINE = MYISAM ;";
				
				//Select Record(s) from database
				$sql = "SELECT `id`, `created`, `modified`, `name`, `count`, `status`, `code_usage` FROM {$invitation_code_table_name} ORDER BY `id` ASC";
				$result_invitaion_code = $wpdb->get_results( $wpdb->prepare($sql, '') ); #WPS
				if(isset($wpdb->last_error) && !empty($wpdb->last_error)){
					$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
				}

				if(!empty($result_invitaion_code)){
					$export_array_merge = array();
					$piereg_plugin_db_version = get_option("piereg_plugin_db_version");
					$export_array_merge['piereg_plugin_db_version'] = $piereg_plugin_db_version;
					$export_array_merge['invitation_codes'] = $result_invitaion_code;
					
					if($is_download)
					{
						$json_result_invitaion_code = $this->piereg_array_to_json($export_array_merge);
						$this->set_json_header( 'pie-register-invitaion-code-'.date_i18n("Y-m-d") );
						echo $json_result_invitaion_code;
						exit;
					}
					return $result_invitaion_code;
				}else{
					$_POST['error'] = __("Record(s) not found","piereg");
				}
			}else{
				$_POST['error_message'] = __("Sorry, your nonce did not verify","piereg");
			}
		}
		/*
			*	Export Invitaion Code
		*/
		function import_invitation_codes(){
			if(isset($_POST['piereg_import_invitations_codes']) && wp_verify_nonce( $_POST['piereg_import_invitations_codes'], 'piereg_wp_import_invitations_codes' ))
			{
				$json_file = $this->read_upload_file($_FILES['import_invitations_codes_file']['tmp_name']);
				$array_json_file = $this->piereg_json_to_array($json_file);
				
				if(isset($array_json_file['piereg_plugin_db_version']) && !empty($array_json_file['piereg_plugin_db_version'])){
					$import_file_db_version = $array_json_file['piereg_plugin_db_version'];
					$pie_plugin_db_version = get_option('piereg_plugin_db_version');
					if($pie_plugin_db_version != $import_file_db_version){
						$_POST['error'] = __("The file version is not compatible with this version","piereg");
						return false;
					}
				}else{
					$_POST['error'] = __("The file version is not compatible with this version","piereg");
					return false;
				}
				
				if(isset($array_json_file['invitation_codes']) && is_array($array_json_file['invitation_codes'])){
					global $wpdb;
					$invitation_code_table_name = $wpdb->prefix."pieregister_code";
					$invitation_code_sql = "CREATE TABLE IF NOT EXISTS ".$invitation_code_table_name."(`id` INT( 5 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,`created` DATE NOT NULL ,`modified` DATE NOT NULL ,`name` TEXT NOT NULL ,`count` INT( 5 ) NOT NULL ,`status` INT( 2 ) NOT NULL ,`code_usage` INT( 5 ) NOT NULL) ENGINE = MYISAM ;";
					
					if(!$wpdb->query($invitation_code_sql))
					{
						$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
					}
					
					if(!$wpdb->query("DELETE FROM `".$invitation_code_table_name."`"))
					{
						$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
					}
					
					$insert_sql = "INSERT INTO `".$invitation_code_table_name."` (`id`, `created`, `modified`, `name`, `count`, `status`, `code_usage`) VALUES ";
					
					$count_query = 0;
					$query_values = "";
					foreach($array_json_file['invitation_codes'] as $inv_key=>$inv_val)
					{
						$count_query++;
						if(isset($inv_val['name']) && $inv_val['name'] != ""){
							$query_values .= "(".((isset($inv_val['id']) && $inv_val['id'] != "")?"'".$inv_val['id']."'": 'NULL').",";
							$query_values .= "'".((isset($inv_val['created']) && $inv_val['created'] != "")?$inv_val['created']: date_i18n('Y-m-d'))."',";
							$query_values .= "'".((isset($inv_val['modified']) && $inv_val['modified'] != "")?$inv_val['modified']: date_i18n('Y-m-d'))."',";
							$query_values .= "'".($inv_val['name'])."',";
							$query_values .= "'".((isset($inv_val['count']) && $inv_val['count'] != "")?$inv_val['count']: 0)."',";
							$query_values .= "'".((isset($inv_val['status']) && $inv_val['status'] != "")?$inv_val['status']: 1)."',";
							$query_values .= "'".((isset($inv_val['code_usage']) && $inv_val['code_usage'] != "")?$inv_val['code_usage']: 0)."'),";
						}
						if($count_query == 100){
							$count_query = 0;
							$query_values = rtrim($query_values,",");
							if(!empty($query_values))
							{
								if(!$wpdb->query($insert_sql.$query_values))
								{
									$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
								}
							}
							$query_values = "";
						}
					}
					if(!empty($query_values))
					{
						$query_values = rtrim($query_values,",");
						if(!$wpdb->query($insert_sql.$query_values))
						{
							$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
						}
					}
					$_POST['success_message'] = __("Successfully import invitation codes","piereg");
				}
			}else{
				$_POST['error_message'] = __("Sorry, your nonce did not verify","piereg");
			}
		}
		/*
			*	Export Email Templates
		*/
		function export_email_template($is_download = true){
			if(isset($_POST['piereg_export_email_template']) && wp_verify_nonce( $_POST['piereg_export_email_template'], 'piereg_wp_export_email_template' ))
			{
				/*Get Default options*/
				$pie_register = get_option("pie_register");
				
				$email_template = array();
				$admin_email_template = array();
				/*Set Email Template to new array*/
				$admin_email_template['enable_admin_notifications'] = $pie_register['enable_admin_notifications'];
				$admin_email_template['admin_sendto_email'] = $pie_register['admin_sendto_email'];
				$admin_email_template['admin_from_name'] = $pie_register['admin_from_name'];
				$admin_email_template['admin_from_email'] = $pie_register['admin_from_email'];
				$admin_email_template['admin_to_email'] = $pie_register['admin_to_email'];
				$admin_email_template['admin_bcc_email'] = $pie_register['admin_bcc_email'];
				$admin_email_template['admin_subject_email'] = $pie_register['admin_subject_email'];
				$admin_email_template['admin_message_email_formate'] = $pie_register['admin_message_email_formate'];
				$admin_email_template['admin_message_email'] = $pie_register['admin_message_email'];
				
				$email_template['admin_email_template'] = $admin_email_template;
				/*Get email template types*/
				$email_type = get_option("pie_user_email_types");
				$user_email_template = array();
				foreach($email_type as $email_temp_key=>$email_temp_val)
				{
					$user_email_template['user_from_name_'.$email_temp_key] = $pie_register['user_from_name_'.$email_temp_key];
					$user_email_template['user_from_email_'.$email_temp_key] = $pie_register['user_from_email_'.$email_temp_key];
					$user_email_template['user_to_email_'.$email_temp_key] = $pie_register['user_to_email_'.$email_temp_key];
					$user_email_template['user_subject_email_'.$email_temp_key] = $pie_register['user_subject_email_'.$email_temp_key];
					$user_email_template['user_formate_email_'.$email_temp_key] = $pie_register['user_formate_email_'.$email_temp_key];
					$user_email_template['user_message_email_'.$email_temp_key] = $pie_register['user_message_email_'.$email_temp_key];
				}
				
				
				$piereg_plugin_db_version = get_option("piereg_plugin_db_version");
				$email_template['piereg_plugin_db_version'] = $piereg_plugin_db_version;
				$email_template['user_email_template'] = $user_email_template;
				$email_template['email_template_types'] = $email_type;
				
				if($is_download){
					$json_email_template = $this->piereg_array_to_json($email_template);
					$this->set_json_header( 'pie-register-email-templates-'.date_i18n("Y-m-d") );
					echo $json_email_template;
					exit;
				}
				return $email_template;
			}else{
				$_POST['error_message'] = __("Sorry, your nonce did not verify","piereg");
			}
		}
		/*
			*	Import Email templates
		*/
		function import_email_template(){
			if(isset($_POST['piereg_import_email_template']) && wp_verify_nonce( $_POST['piereg_import_email_template'], 'piereg_wp_import_email_template' ))
			{
				$json_file = $this->read_upload_file($_FILES['import_email_template_file']['tmp_name']);
				$array_json_file = $this->piereg_json_to_array($json_file);
				
				if(isset($array_json_file['piereg_plugin_db_version']) && !empty($array_json_file['piereg_plugin_db_version'])){
					$import_file_db_version = $array_json_file['piereg_plugin_db_version'];
					$pie_plugin_db_version = get_option('piereg_plugin_db_version');
					if($pie_plugin_db_version != $import_file_db_version){
						$_POST['error'] = __("The file version is not compatible with this version","piereg");
						return false;
					}
				}else{
					$_POST['error'] = __("The file version is not compatible with this version","piereg");
					return false;
				}
				
				if(isset($array_json_file['admin_email_template'],$array_json_file['user_email_template'])){
					if(is_array($array_json_file['admin_email_template']) && is_array($array_json_file['user_email_template'])){
						$email_templates = array();
						$email_templates = $this->piereg_array_replace_recursive($array_json_file['admin_email_template'],$array_json_file['user_email_template']);
						$db_option = get_option("pie_register");
						$merge_email_templates = $this->piereg_array_replace_recursive($db_option,$email_templates);
						if(is_array($merge_email_templates)){
							update_option("pie_register", $merge_email_templates);
							$_POST['success_message'] = __("Successfully import e-mail templates","piereg");
						}
					}
				}
			}else{
				$_POST['error_message'] = __("Sorry, your nonce did not verify","piereg");
			}
		}
		/*
			*	Export All Usaers Data With Custom Fields
		*/
		function export_all_users_data_with_custom_fields($is_download = false){
			
			if(isset($_POST['piereg_export_all_user_custom_data']) && wp_verify_nonce( $_POST['piereg_export_all_user_custom_data'], 'piereg_wp_export_all_user_custom_data' ))
			{
				$all_users = get_users();
				
				$this->set_all_users_data = array();
				$piereg_plugin_db_version = get_option("piereg_plugin_db_version");
				$this->set_all_users_data['piereg_plugin_db_version'] = $piereg_plugin_db_version;
				
				foreach($all_users as $key=>$user){
					$this->set_all_users_data( $user )->set_all_users_meta( $user->ID );
				}
				$json_all_users_data = $this->piereg_array_to_json( $this->set_all_users_data );
				$this->set_all_users_data = array();
				if($is_download){
					$this->set_json_header( 'all-users-data-with-custom-meta-'.date_i18n("Y-m-d") );
					echo $json_all_users_data;
					exit;
				}
				return $json_all_users_data;
			}else{
				$_POST['error_message'] = __("Sorry, your nonce did not verify","piereg");
			}
		}
		function set_all_users_data($user){
			$this->set_all_users_data[ $user->ID ]['user'] = $user;
			return $this;
		}
		function set_all_users_meta($user_id){
			$user_meta = get_user_meta( $user_id );
			$this->set_all_users_data[ $user_id ]['user_meta'] = $user_meta;
		}
		/*
			*	Import All Usaers Data With Custom Fields
		*/
		function import_all_users_data_with_custom_fields(){
			if(isset($_POST['piereg_import_user_custom_data']) && wp_verify_nonce( $_POST['piereg_import_all_user_custom_data'], 'piereg_wp_import_all_user_custom_data' )){
				$json_file = $this->read_upload_file($_FILES['import_all_users_data_with_custom_field']['tmp_name']);
				$all_users_data = $this->piereg_json_to_array($json_file);
				
				if( $this->check_json_file_db_version($all_users_data) ){
					unset($all_users_data['piereg_plugin_db_version']);
					$_POST['successfull_import_all_users_data'] 	= 0;
					$_POST['unsuccessfull_import_all_users_data'] 	= 0;
					foreach($all_users_data as $user_id=>$user_bunch){
						$this->import_user_from_json($user_bunch);
					}
				}else{
					$_POST['error'] = __("The file version is not compatible with this version","piereg");
				}
			}else{
				$_POST['error_message'] = __("Sorry, your nonce did not verify","piereg");
			}
		}
		function import_user_from_json($user){
			if( isset($user) && is_array($user) && !empty($user) ){
				/* Validate User */
				if( !username_exists($user['user']['data']['user_login']) && !email_exists($user['user']['data']['user_email']) ){
					$_POST['successfull_import_all_users_data'] = intval($_POST['successfull_import_all_users_data']) + 1;
				}else{
					$_POST['unsuccessfull_import_all_users_data'] = intval($_POST['unsuccessfull_import_all_users_data']) + 1;
					return false;
				}
				/* Insert User */
				$user_data 	= $user['user']['data'];
				$user_role	= $user['user']['roles'][0];
				
				$user_field = array();
				$user_field['user_login'] 			= $user_data['user_login'];
				$user_field['user_pass'] 			= ((isset($user_data['user_pass']) && !empty($user_data['user_pass']) )?$user_data['user_pass']:wp_generate_password(8) );
				$user_field['user_nicename'] 		= ((isset($user_data['user_nicename']) && !empty($user_data['user_nicename']) )?$user_data['user_nicename']:$user_data['user_login'] );
				$user_field['user_email'] 			= $user_data['user_email'];
				$user_field['user_url'] 			= ((isset($user_data['user_url']) && !empty($user_data['user_url']) )?$user_data['user_url']:"" );
				$user_field['user_registered'] 		= ((isset($user_data['user_registered']) && !empty($user_data['user_registered']) )?$user_data['user_registered']:date_i18n("Y-m-d H:i:s") );
				$user_field['user_activation_key'] 	= ((isset($user_data['user_activation_key']) && !empty($user_data['user_activation_key']) )?$user_data['user_activation_key']:"" );
				$user_field['user_status'] 			= ((isset($user_data['user_status']) && !empty($user_data['user_status']) )?$user_data['user_status']:0 );
				$user_field['display_name'] 		= ((isset($user_data['display_name']) && !empty($user_data['display_name']) )?$user_data['display_name']:$user_data['display_name'] );
				$user_field['role']			 		= (!empty($user_role)) ? $user_role : get_option('default_role');
								
				$user_id = wp_insert_user( $user_field );				
				
				/*Update Old Password*/
				if( isset($user_data['user_pass']) && !empty($user_data['user_pass']) ){
					global $wpdb;
					$wpdb->update( $wpdb->users, array('user_pass' => $user_data['user_pass']), array('ID' => $user_id ) );
				}
				
				/* Insert User Meta */
				$user_meta = $user['user_meta'];
				foreach($user_meta as $meta_name=>$meta_value){
					
					if(isset($meta_value,$meta_value[0]) && is_array($meta_value))
						$meta = $meta_value[0];
					elseif( isset($meta_value) && !empty($meta_value) )
						$meta = $meta_value;
					else
						$meta = "";
					
					update_user_meta($user_id, $meta_name, $meta );
				}
				
				// adding role to user.
				$added_user = new WP_User($user_id);
				$added_user->set_role($user_field['role']);
				
			}
		}
		
		function check_json_file_db_version($array_json_file){
			if(isset($array_json_file['piereg_plugin_db_version']) && !empty($array_json_file['piereg_plugin_db_version'])){
				$import_file_db_version = $array_json_file['piereg_plugin_db_version'];
				$pie_plugin_db_version = get_option('piereg_plugin_db_version');
				if($pie_plugin_db_version === $import_file_db_version)
					return true;
				else
					return false;
			}else
				return false;
		}
		function set_json_header($file_name){
			header('Content-disposition: attachment; filename='.($file_name).'.json');
			header('Content-type: application/json');
		}
		
		
		function piereg_plugin_row_meta( $links, $file ) {
			if ( $file == PIEREG_PLUGIN_BASENAME ) {
				$row_meta = array(
					'docs'		=>	'<a href="' . esc_url( apply_filters( 'pieregister_docs_url', 'http://pieregister.com/documentation/' ) ) . '" title="' . esc_attr( __( 'View Pie-Register Documentation', 'piereg' ) ) . '" target="_blank">' . __( 'Docs', 'piereg' ) . '</a>',
					'support'	=>	'<a href="' . esc_url( apply_filters( 'pieregister_support_url', 'http://pieregister.com/forum/' ) ) . '" title="' . esc_attr( __( 'Visit Customer Support Forum', 'piereg' ) ) . '" target="_blank">' . __( 'Support', 'piereg' ) . '</a>',
				);				
				
				return array_merge( $links, $row_meta );
			}
	
			return (array) $links;
		}
		function activate_license_key(){
			
			if( isset($_POST['piereg_activate_license_key'], $_POST['save_license_key_settings']) )
			{
				if(isset($_POST['piereg_license_key'], $_POST['piereg_license_email'], $_POST['piereg_license_key_nonce'] ) && !empty($_POST['piereg_license_key']) && !empty($_POST['piereg_license_email']) )
				{
					if(wp_verify_nonce( $_POST['piereg_license_key_nonce'], 'piereg_wp_license_key_nonce' )){
						$piereg_api_manager_menu = new Piereg_API_Manager_Example_MENU();
						$piereg_api_manager_menu->validate_options( array("api_key"=> trim($_POST['piereg_license_key']), "activation_email"=> trim($_POST['piereg_license_email']) ) );
					}
					else{
						$_POST['error'] = __("Sorry, your nonce did not verify","piereg");
					}
				}
			}
		}
		function activate_addon_license_key(){
			
			if( isset($_POST['piereg_activate_addon_license_key'], $_POST['save_addon_license_key_settings']) )
			{
				if(isset($_POST['piereg_addon_license_key_nonce']))
				{
					if(wp_verify_nonce( $_POST['piereg_addon_license_key_nonce'], 'piereg_wp_addon_license_key_nonce' )){
						$LK_options = get_option( PIEREG_LICENSE_KEY_OPTION );
						$piereg_api_manager_menu = new Piereg_API_Manager_Example_MENU();
						$piereg_api_manager_menu->validate_addon_options( array("api_key"=> $LK_options['api_key'], "activation_email"=> $LK_options['activation_email'], "api_addon"=> $_POST['is_activate_addon_license'], "api_addon_version"=> $_POST['addon_software_version']) );
					}
					else{
						$_POST['error'] = __("Sorry, your nonce did not verify","piereg");
					}
				}
			}
		}
		public function plugin_url() {
			if ( isset( $this->piereg_plugin_url ) ) return $this->piereg_plugin_url;
			return $this->piereg_plugin_url = plugins_url( '/', __FILE__ );
		}
		function is_anyone_can_register(){
			return get_option("users_can_register");
		}
		function piereg_ssl_template_redirect(){
			if ( !is_admin() && ( (defined("FORCE_SSL_ADMIN") && FORCE_SSL_ADMIN == true) && !is_ssl() ) ) { #checkbyM
				if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') {
					wp_redirect( preg_replace('|^http://|', 'https://', $this->piereg_get_current_url()) );
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
						
						$message		= $this->filterEmail($message_temp,$user_data_temp, "",false,$keys_array );
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
							$this->pr_error_log("'The e-mail could not be sent. Possible reason: your host may have disabled the mail() function...'".($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
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
					wp_safe_redirect($this->get_page_uri($global_options["alternate_profilepage"],"edit_user=1&pr_msg={$print_message}&type={$type}"));
					exit;
				}else{
					$_POST[$type] = $print_message;
				}
			}
		}
		/*
			*	Payment log file Download & Delete
		*/
		function piereg_payment_log_file_action(){
			if( !is_admin() )
				return false;
			
			if( isset($_POST['piereg_download_payment_log_file']) ){
				$this->piereg_get_wp_plugable_file();
				if(isset($_POST['piereg_payment_log']) && wp_verify_nonce( $_POST['piereg_payment_log'], 'piereg_wp_payment_log' )){
					$read_log_file = $this->read_upload_file(PIEREG_DIR_NAME."/log/payment-log.log");
					if( !empty($read_log_file) ){
						// Send file headers
						header('Content-disposition: attachment; filename=payment-log-'.date_i18n("d-m-y-H-i-s").'.log');
						header('Content-type: application/text');
						// Send the file contents.
						echo $read_log_file;
						exit;
					}else{
						$_POST['error'] = __("Empty Payment Log File","piereg");
					}
				}else{
					$_POST['error'] = __("Sorry, your nonce did not verify","piereg");
				}
			}elseif( isset($_POST['piereg_delete_payment_log_file']) ){
				$this->piereg_get_wp_plugable_file();
				if(isset($_POST['piereg_payment_log']) && wp_verify_nonce( $_POST['piereg_payment_log'], 'piereg_wp_payment_log' )){
					update_option( "piereg_payment_log_option", array() );
					$_POST['notice'] = __("Successfully remove payment log","piereg");
				}else{
					$_POST['error'] = __("Sorry, your nonce did not verify","piereg");
				}
			}
		}
	}
}

if( class_exists('PieRegister') ){
	$pie_register = new PieRegister();
	$GLOBALS['piereg_api_manager'] = $pie_register;
	if(isset($pie_register)){
		register_activation_hook( __FILE__, array(  &$pie_register, 'install_settings' ) );
		register_deactivation_hook( __FILE__, array(  &$pie_register, 'deactivation_settings' ) );
		
		if (!function_exists("pie_registration_url")) 
		{
			function pie_registration_url($url=false) 
			{
				return PieRegister::static_pie_registration_url($url);
			}
		}
		
		if (!function_exists("pie_login_url")) 
		{
			function pie_login_url($url=false) 
			{
				return PieRegister::static_pie_login_url($url);
			}
		}
		
		if (!function_exists("pie_lostpassword_url")) 
		{
			function pie_lostpassword_url($url=false)
			{
				return PieRegister::static_pie_lostpassword_url($url);
			}
		}
		
		if (!function_exists("piereg_logout_url")) 
		{	
			function piereg_logout_url($url=false)
			{
				return PieRegister::static_piereg_logout_url($url);
			}
		}
		
		if (!function_exists("pie_modify_custom_url"))
		{	
			function pie_modify_custom_url($url,$query_string=false)
			{
				return PieRegister::static_pie_modify_custom_url($url,$query_string);
			}
		}
		
		if (!function_exists("set_pr_stats")) 
		{
			function set_pr_stats($stats,$type)
			{
				return PieRegister::static_set_pr_stats($stats,$type);
			}
		}
	}
	
	if (!function_exists("uninstall_pr_ff"))
	{
		function uninstall_pr_ff() {
			global $pie_register;			
			if(!is_object($pie_register)) {
				$pie_register = new PieRegister();
			}			
			$pie_register->uninstall_settings();
		}
	}
	register_uninstall_hook( __FILE__, 'uninstall_pr_ff' ); 
}