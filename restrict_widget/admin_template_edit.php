<div class="piereg_clear"></div>
<?php
global $wp_registered_sidebars, $wp_registered_widgets, $wp_registered_widget_controls;

if(!isset($_GET['pie_id']) || empty($wp_registered_widgets[$_GET['pie_id']]))	
	echo '<div class="error"><p><strong>Error! Could not find widget.</strong></p></div>';
	
else {	
	if(isset($_GET['reset_widget'])){		
		$this->piereg_rw_widgets[$_GET['pie_id']] = $this->info['blank'];		
		update_option(PIEREGISTER_RW_OPTIONS,$this->piereg_rw_widgets);		
	}
	
	$url = $this->pr_rw_admin_url . '&amp;act=edit&amp;pie_id=' . $_GET['pie_id'];	
	$widget = &$wp_registered_widgets[$_GET['pie_id']];	
	if(!empty($_GET['option'])){
	
		echo '<div class="pr_rw_breadcrums"><a href="', $this->pr_rw_admin_url ,'">'.(__("Custom widgets","piereg")).'</a> <span class="piereg_breadcrum_bulet">&nbsp;&nbsp;&nbsp;</span> '.(__("Customize","piereg")).' "'.$widget['name'].'"</div>';	
		$this->require_once_file(dirname(__FILE__) . '/show_in_use_restrict.php');
		
	}	
	
}
?>