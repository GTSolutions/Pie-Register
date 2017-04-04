<?php
if( !class_exists('PieRegisterDashWidgetStats') ){
	class PieRegisterDashWidgetStats{
		function PieRegisterDashWidgetStats() { //contructor
			// Add the widget to the dashboard
			add_action( 'wp_dashboard_setup', array($this, 'register_widget') );
			add_filter( 'wp_dashboard_widgets', array($this, 'add_widget') );		
		}
		function register_widget() {
			$piereg = get_option(OPTION_PIE_REGISTER);
			if ( current_user_can('manage_options') )
				wp_register_sidebar_widget( 'piereg_form_stats', __(' Pie-Register Stats','piereg'), array($this, 'widget'), array( 'settings' => 'options-general.php?page=pie-register' ) );			
		}
		// Modifies the array of dashboard widgets and adds this plugin's
		function add_widget( $widgets ) {
			global $wp_registered_widgets,$wp_registered_widget_controls;
			$wp_registered_widget_controls['piereg_form_stats']['callback'] = '';
	
			if ( !isset($wp_registered_widgets['piereg_form_stats']) ) return $widgets;
	
			array_splice( $widgets, 2, 0, 'piereg_form_stats' );
	
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
			$users = $wpdb->get_results( $wpdb->prepare("SELECT COUNT(user_id) as total_users,`meta_value` FROM $wpdb->usermeta WHERE meta_key=%s GROUP BY `meta_value`",'invite_code' ) );
			
			$count = 0;
			?>

<div class="pieregister_dash_widget_style" style="display:none;">
  <style type="text/css">
    .pieregister-admin .piereg_stats_row{display:inline-block;width:99%;}
    .pieregister-admin .piereg_stats_row .piereg_stats_col{float:left;width:45%;}
    .pieregister-admin .piereg_stats_row .piereg_stats_counter span{color: rgb(37, 37, 37);display: inline-block;font-family: "MyraidPro-Bold",MyraidPro,serif;/*font-size: 102px;*/font-size: 30px;font-weight: 300;/*line-height: 90px;*/margin-bottom: 10px;}
    .pieregister-admin .piereg_stats_row .piereg_stats_label span{/*font-size:18px;*/font-weight:bold;}
    .pieregister-admin .piereg_stats_row .piereg_stats_counter,
    .pieregister-admin .piereg_stats_row .piereg_stats_label{text-align:center;}
    .pieregister-admin .piereg_stats_row .piereg_stats_title h1{color: rgb(255, 255, 255);font-size: 36px;font-weight: 300;background-color: rgb(73, 73, 73);color: rgb(255, 255, 255);font-family: "MyraidPro-Bold",MyraidPro,serif;font-size: 20px;font-weight: 300;text-align: center;line-height:1.6;}
	.pieregister-admin .piereg_stats_row .piereg_stats_title{margin-bottom: 15px;}
    </style>
</div>
<div class="pieregister-admin">
  <div class="piereg_stats_row">
    <div class="piereg_form_stats">
      <?php
            $piereg_stats = get_option(PIEREG_STATS_OPTION);
            foreach($piereg_stats as $pr_stats_key=>$pr_stats_val){ ?>
      <div class="piereg_stats_row" <?php echo (($pr_stats_key == "login" || $pr_stats_key == "forgot")?'style="display:none"':''); ?> >
        <?php
                if($pr_stats_key == "login"){
                    echo '<div class="piereg_stats_title"><h1>'.(__("Login","piereg")).'</h1></div>';
                }
                elseif($pr_stats_key == "forgot"){
                    echo '<div class="piereg_stats_title"><h1>'.(__("Forgot Password","piereg")).'</h1></div>';
                }
                elseif($pr_stats_key == "register"){
                    echo '<div class="piereg_stats_title"><h1>'.(__("Registration","piereg")).'</h1></div>';
                }
                
                if(isset($pr_stats_val['view']) && isset($pr_stats_val['used']))
                {
                    ?>
        <div class="piereg_stats_col">
          <div class="piereg_stats_counter"> <span> <?php echo ((strlen(intval($pr_stats_val['view'])) == 1)? "0".intval($pr_stats_val['view']):intval($pr_stats_val['view'])); ?> </span> </div>
          <div class="piereg_stats_label"> <span>
            <?php _e("Views","piereg");?>
            </span> </div>
        </div>
        <div class="piereg_stats_col">
          <div class="piereg_stats_counter"> <span> <?php echo ((strlen(intval($pr_stats_val['used'])) == 1)? "0".intval($pr_stats_val['used']):intval($pr_stats_val['used'])); ?> </span> </div>
          <div class="piereg_stats_label"> <span>
            <?php _e("Conversions","piereg"); ?>
            </span> </div>
        </div>
        <?php
                } ?>
      </div>
      <?php }?>
    </div>
  </div>
</div>
<?php
			
			echo $after_widget;
		}
	}
} # End Class PieRegisterDashWidgetStats


// Start this plugin once all other plugins are fully loaded
add_action( 'plugins_loaded', 'initialize_pr_dashwidget_stats');

function initialize_pr_dashwidget_stats(){
	$piereg_widget = new PieRegisterDashWidgetStats();
}

?>
