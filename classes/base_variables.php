<?php
/*
	*	Defaine PR Global option's name
*/
if(!defined('OPTION_PIE_REGISTER'))
	define('OPTION_PIE_REGISTER','pie_register');
	
/*
	*	Define PR DB Version Name
*/
if(!defined('PIEREG_DB_VERSION'))
	define('PIEREG_DB_VERSION','3.0');

/*
	*	Define Restrict Widgets Option Name
*/
if(!defined("PIEREGISTER_RW_OPTIONS"))
	define("PIEREGISTER_RW_OPTIONS","pieregister_restrict_widgets");


/*
	*	Define name of Pie-Register's Stats
*/
if(!defined("PIEREG_STATS_OPTION"))
	define("PIEREG_STATS_OPTION","pieregister_stats_option");
	
/*
	*	Define name of Currency Name with Code
*/
if(!defined("PIEREG_CURRENCY_OPTION"))
	define("PIEREG_CURRENCY_OPTION","piereg_currency");

/*
	*	Define name of Currency Name with Code
*/
if(!defined("PIEREG_DIR_NAME"))
	define("PIEREG_DIR_NAME",plugin_dir_path(__FILE__));
	
/*
	*	Define Plugin Base name
*/
if(!defined("PIEREG_PLUGIN_BASENAME"))
define( 'PIEREG_PLUGIN_BASENAME', "pie-register/pie-register.php" );
	
/*
	*	Define License Key opeion's name
*/
if(!defined("PIEREG_LICENSE_KEY_OPTION"))
define( 'PIEREG_LICENSE_KEY_OPTION', 'api_manager_example' );

if( !class_exists('PieRegisterBaseVariables') ){
	class PieRegisterBaseVariables
	{
		var $user_table;		
		var $user_meta_table; 
		var $plugin_dir;
		var	$plugin_url;
		var	$pie_success;
		var	$pie_error;
		var	$pie_error_msg;
		var	$pie_success_msg;
		var $piereg_global_options;// deprecated
		var $PR_GLOBAL_OPTIONS;
		var $pr_wp_db_prefix;
		
		public $upgrade_url = 'http://store.genetech.co/';
		public $version 	= '1.0';
		
		public $piereg_api_manager_version_name 	= 'pie-register'; //plugin_api_manager_example_version
		public $piereg_plugin_url;
		
		public $piereg_text_domain 	= 'piereg';
		
		var $piereg_pro_is_activate = false;
		
		function __construct(){
			
			/*
				*	Get PR Options from DB
			*/
			global $piereg_global_options;// deprecated
			global $PR_GLOBAL_OPTIONS;
			
			$PR_GLOBAL_OPTIONS = get_option(OPTION_PIE_REGISTER);
			$piereg_global_options = $PR_GLOBAL_OPTIONS;
			
			/*
				*	Get Wp DB Prefix
			*/
			global $wpdb,$pr_wp_db_prefix;
			$pr_wp_db_prefix = $wpdb->prefix;
			/*
				*	check is activate plugins
			*/
			$options = get_option( PIEREG_LICENSE_KEY_OPTION );
			$activated = get_option( 'piereg_api_manager_activated' );
			$instance = get_option( 'piereg_api_manager_instance' );
			
			if(isset($options['api_key']) && isset($options['activation_email']) && !empty($options['api_key']) && !empty($options['activation_email']) && $activated == "Activated" && !empty($instance)){
				$this->piereg_pro_is_activate = true;
				if(!defined("PIEREG_IS_ACTIVE"))
					define( 'PIEREG_IS_ACTIVE', true );
			}else{
				$this->piereg_pro_is_activate = false;
				if(!defined("PIEREG_IS_ACTIVE"))
					define( 'PIEREG_IS_ACTIVE', false );
			}			
		}
		
		function piereg_pro_is_activate(){
			/*
				*	check is activate plugins
			*/
			if($this->piereg_pro_is_activate == true)
				return true;
			
			$options 	= get_option( PIEREG_LICENSE_KEY_OPTION );
			$activated 	= get_option( 'piereg_api_manager_activated' );
			$instance 	= get_option( 'piereg_api_manager_instance' );
			
			if(isset($options['api_key'], $options['activation_email']) && !empty($options['api_key']) && !empty($options['activation_email']) && $activated == "Activated" && !empty($instance)){
				$this->piereg_pro_is_activate = true;
				if(!defined("PIEREG_IS_ACTIVE"))
					define( 'PIEREG_IS_ACTIVE', true );
				return true;
			}else{
				$this->piereg_pro_is_activate = false;
				if(!defined("PIEREG_IS_ACTIVE"))
					define( 'PIEREG_IS_ACTIVE', false );
				return false;
			}
		}
		
	}
}