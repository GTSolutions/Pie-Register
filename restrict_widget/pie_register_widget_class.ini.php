<?php
class pie_register_restrict_widget extends PieRegister{
	/*
		*	Define Global Variables
	*/
	var $info;
	var $piereg_rw_widgets;
	var $pr_rw_dir_name;
	var $pr_rw_admin_url;
	var $pr_rw_url;
	var $pr_rw_posts_per_page;
	
	function __construct(){
		parent::__construct();
		
		// Main plugin options
		$this->info = array(
			'blank'	=> array(
						'pr_ristrict_widget' => array()
					)
			);
		
		/*
			* Set Per Page items
		*/
		$this->pr_rw_posts_per_page = 10;
		/*
			* Set Dir name
		*/
		$this->pr_rw_dir_name = basename(dirname(__FILE__));
		
		/*
			*	Set Admin Url
		*/
		$this->pr_rw_admin_url 	= '?page=' . (!empty($_GET['page']) ? $_GET['page'] : false);
		if( $_GET['page'] == "pie-settings" )
		{
			$this->pr_rw_admin_url .= '&amp;tab=security&amp;subtab=sadvanced';
		}
		/*
			*	Reset All Widgets
		*/
		if(isset($_GET['piereg_reset_widgets']))
			update_option(PIEREGISTER_RW_OPTIONS,array());
		
		// Compute option page link
		$this->pr_rw_url =  $this->plugin_url . $this->pr_rw_dir_name;
		
		
		// Compute plugin images link
		$this->info['images'] = $this->pr_rw_url . '/images';
		
		$this->piereg_rw_widgets = get_option(PIEREGISTER_RW_OPTIONS);
		$this->piereg_rw_widgets = $this->piereg_rw_widgets ? $this->piereg_rw_widgets : array();
		
		$this->admin_page();
	}
	
	
	function admin_page() {
		// If data was sent through POST, process it
		if(!empty($_POST))
			require(dirname(__FILE__) . '/admin_actions.php');
			
		// Compute current admin sub page
		$_GET['act'] = !empty($_GET['act']) ? $_GET['act'] : 'main';
		
		// Find current page
		if(!file_exists( $require_file = dirname(__FILE__) . '/admin_template_' . $_GET['act'] . '.php' ))	{
			$_GET['act'] = 'main';
			$require_file = dirname(__FILE__) . '/admin_template_' . $_GET['act'] . '.php';
		}
		
		//Render current page		
		echo '<div class="wrap">';
			$this->require_once_file($require_file);
		echo '</div>';
		}
}
$piereg_restrict_widget = new pie_register_restrict_widget;