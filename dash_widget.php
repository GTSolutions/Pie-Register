<?php
if( !class_exists('PieRegisterWidget') ){
	class PieRegisterWidget{
		
		function PieRegisterWidget() { //contructor
			// Add the widget to the dashboard
			add_action( 'wp_dashboard_setup', array($this, 'register_widget') );
			add_filter( 'wp_dashboard_widgets', array($this, 'add_widget') );		
		}
		
		function register_widget() {
			$piereg = get_option( 'pie_register_2' );
			
			if ( current_user_can('manage_options') )
				wp_register_sidebar_widget( 'piereg_invite_tracking', __('PR Code Tracking', 'piereg' ), array($this, 'widget'), array( 'settings' => 'options-general.php?page=pie-register' ) );
		}
		
		// Modifies the array of dashboard widgets and adds this plugin's
		function add_widget( $widgets ) {
			global $wp_registered_widgets,$wp_registered_widget_controls;
			$wp_registered_widget_controls['piereg_invite_tracking']['callback'] = '';
			if ( !isset($wp_registered_widgets['piereg_invite_tracking']) ) return $widgets;
			array_splice( $widgets, 2, 0, 'piereg_invite_tracking' );
			return $widgets;
		}
		
		// Output the widget contents
		function widget( $args ) {
			$before_widget	= "";
			$before_title	= "";
			$widget_name	= "";
			$after_title	= "";
			$after_widget	= "";
			@extract( $args, EXTR_SKIP );
			echo $before_widget;
			echo $before_title;
			echo $widget_name;
			echo $after_title;
			
			global $wpdb;
			$prefix=$wpdb->prefix."pieregister_";
			$codetable=$prefix."code";
			$usercodes = array();
			$users = $wpdb->get_results( "SELECT COUNT(user_id) as total_users,`meta_value` FROM $wpdb->usermeta WHERE meta_key='invite_code' GROUP BY `meta_value`" );
			
			$count = 0;
			echo '<div class="pieregister_dash_widget_style">
					<style type="text/css">
					table.piereg_dash_widget td h3{border:none;}
					table.piereg_dash_widget tr td a{display:none;}
					table.piereg_dash_widget tr:nth-child(even){background:#F9F9F9;}
					table.piereg_dash_widget tr:hover{background:#F3F3F3;}
					</style>
			</div>';
			echo '<table width="100%" class="piereg_dash_widget" cellspacing="0" cellpaddinig="10">';
			foreach($users as $user){
				$total_users = $user->total_users;
				$count++;
				$meta_value = $user->meta_value;
				if(!empty($meta_value)){
					  echo '<tr>';
					  echo '<td><h3>' . $meta_value . '</h3></td>';
					  echo '<td>' . $total_users.' ';
					  echo __("Users Registered","piereg");
					  echo '</td>';
					  echo '</tr>';
				}
			}
			echo '</table>';
				
			echo $after_widget;
		}
	}
} # End Class PieRegisterWidget
// Start this plugin once all other plugins are fully loaded
add_action( 'plugins_loaded', 'initialize_pr_dashwidget');
function initialize_pr_dashwidget(){
	$piereg_widget = new PieRegisterWidget();
}