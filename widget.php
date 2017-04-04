<?php
if( file_exists( dirname(__FILE__) . '/classes/registration_form.php') ) 
	require_once('classes/registration_form.php');

class Pie_Register_Widget extends WP_Widget 
{
	function __construct() 
	{
		parent::__construct(
			'pie_widget', // Base ID
			__('Pie Register - Registration Form', 'piereg'), // Name
			array( 'description' => __( 'Display Your Pie-Register Registration Form on Sidebar', 'piereg' ), ) // Args
		);		
	}
	public function widget( $args, $instance ){
		$option = get_option(OPTION_PIE_REGISTER);
		$pie_register = new PieRegister();
		$pie_register->piereg_ssl_template_redirect();
		if(is_user_logged_in() && $option['redirect_user']==1 ){
			//do nothing here
		}elseif( !$pie_register->piereg_pro_is_activate && ($instance['form_id'] != $pie_register->regFormForFreeVers()) ) {
			//do nothing here			
			
		}else{
			global $errors;
			$success 	= '' ;
			$title 		= apply_filters( 'widget_title', $instance['title'] );
			$form_id 	= $instance['form_id'];
			$form_title = $instance['form_title'];
			$form_desc 	= $instance['form_desc'];
			
			echo $args['before_widget'];
			
			$output = '';
			if(isset($_POST['success']) && $_POST['success'] != "")
				$output .= '<p class="piereg_message">'.apply_filters('piereg_messages',__($_POST['success'],"piereg")).'</p>';
			if(isset($_POST['error']) && $_POST['error'] != "")
				$output .= '<p class="piereg_login_error">'.apply_filters('piereg_messages',__($_POST['error'],"piereg")).'</p>';
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
			$output .= $pie_register->outputRegForm(true,$form_id,$form_title,$form_desc);
			echo $output;
			echo $args['after_widget'];
			set_pr_stats("register","view");
			
		}
	}
	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Pie Registration Form', 'pie_forgot' );
		}
		$form_id = ((isset($instance['form_id']))?$instance['form_id']:"");
		$form_title = ((isset($instance['form_title']))?$instance['form_title']:"true");
		$form_desc = ((isset($instance['form_desc']))?$instance['form_desc']:"true");
		// Widget admin form
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
            <label for="<?php echo $this->get_field_id( 'form_id' ); ?>"><?php _e( 'Form:' );?></label>
            <select class="widefat" id="<?php echo $this->get_field_id( 'form_id' ); ?>" name="<?php echo $this->get_field_name( 'form_id' ); ?>">
				<?php
                $fields_id = get_option("piereg_form_fields_id");
                for($a=1;$a<=$fields_id;$a++)
                {
                    $option = get_option("piereg_form_field_option_".$a);
                    if($option != "" && (!isset($option['IsDeleted']) || trim($option['IsDeleted']) != 1) )
                    {
                        echo '<option '.((!empty($form_id) && $form_id == $option['Id'])?'selected="selected"':'').' value="'.$option['Id'].'" >'.$option['Title'].'</option>';
                    }
                }?>
            </select>
		</p>
        <p>
            <label for="<?php echo $this->get_field_id( 'form_title' ); ?>"><?php _e( 'Form Title:' );?></label>
            <select class="widefat" id="<?php echo $this->get_field_id( 'form_title' ); ?>" name="<?php echo $this->get_field_name( 'form_title' ); ?>">
            <?php
		        echo '<option '.((!empty($form_title) && $form_title == "true")?'selected="selected"':'').' value="true" >Show</option>';
		        echo '<option '.((!empty($form_title) && $form_title == "false")?'selected="selected"':'').' value="false" >Hide</option>';
			?>
            </select>
		</p>
        <p>
            <label for="<?php echo $this->get_field_id( 'form_desc' ); ?>"><?php _e( 'Form Description:' );?></label>
            <select class="widefat" id="<?php echo $this->get_field_id( 'form_desc' ); ?>" name="<?php echo $this->get_field_name( 'form_desc' ); ?>">
            <?php
		        echo '<option '.((!empty($form_desc) && $form_desc == "true")?'selected="selected"':'').' value="true" >Show</option>';
		        echo '<option '.((!empty($form_desc) && $form_desc == "false")?'selected="selected"':'').' value="false" >Hide</option>';
			?>
            </select>
		</p>
		<?php 
	}
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['form_id'] = ( ! empty( $new_instance['form_id'] ) ) ? strip_tags( $new_instance['form_id'] ) : '';
		$instance['form_title'] = ( ! empty( $new_instance['form_title'] ) ) ? strip_tags( $new_instance['form_title'] ) : '';
		$instance['form_desc'] = ( ! empty( $new_instance['form_desc'] ) ) ? strip_tags( $new_instance['form_desc'] ) : '';
		return $instance;
	}
}




/*
	*	Pie-Register Login Widgets
*/
class Pie_Login_Widget extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	function __construct() 
	{
		parent::__construct(
			'pie_login_widget', // Base ID
			__('Pie Register - Login Form', 'pie_login'), // Name
			array( 'description' => __( 'Display Pie-Register Login Form on Sidebar', 'piereg' ), ) // Args
		);		
	}
	public function widget( $args, $instance ){
		$option = get_option(OPTION_PIE_REGISTER);
		$pie_register = new PieRegister();
		$pie_register->piereg_ssl_template_redirect();
		
		global $errors;
		echo $args['before_widget'];
		$before_title = apply_filters( 'widget_title', $instance['before_title'] );
		$after_title = apply_filters( 'widget_title', $instance['after_title'] );
		$social_login = ( (isset($instance['social_login'])) ? apply_filters( 'widget_title', $instance['social_login'] ) : 0 );
		if ( !is_user_logged_in() ) 
		{
			if ( ! empty( $before_title ) )
			echo $args['before_title'] . $before_title . $args['after_title'];
			set_pr_stats("login","view");
			if( file_exists(PIEREG_DIR_NAME . "/login_form.php") )
				include_once("login_form.php");
			$output = pieOutputLoginForm(true);
			if(intval($social_login) > 0 )
			{
				$social_site_data = "";
				$social_site_data .= apply_filters("get_enable_social_sites_button_widgets",$social_site_data);
				$output .= $social_site_data;
			}
			echo $output;
		}else{
			$current_user = wp_get_current_user();
			if ( ! empty( $after_title ) )
			echo $args['before_title'] . $after_title . $args['after_title'];
			$profile_pic_array = get_user_meta($current_user->ID);
			foreach($profile_pic_array as $key=>$val)
			{
				if(strpos($key,'profile_pic') !== false){
					$profile_pic = trim($val[0]);
				} else {
					$profile_pic = "";	
				}
			}
			
			$profile_pic = apply_filters("piereg_profile_image_url",$profile_pic,$current_user);
			echo '<div class="logged-In">';
			$user_avater = get_avatar(get_current_user_id(),75);
			$profile_link = get_permalink($option['alternate_profilepage']);
			$profile_avatar = ((!empty($profile_pic))?('<img src="'.$profile_pic.'" style="max-width:75px;max-height:75px;"/>'):$user_avater);
			$profile_image_html = '<a href="'.$profile_link.'">'.$profile_avatar.'</a>';
			echo apply_filters('pie_profile_image_frontend_widget',$profile_image_html,$profile_link,$profile_pic);
			////////////////////////////
			$first_name = get_user_meta($current_user->ID,"first_name",true);
			$last_name = get_user_meta($current_user->ID,"last_name",true);
			if( !empty($first_name) && !empty($last_name) )
				$profile_text = $first_name . "&nbsp;" . $last_name;
			elseif( !empty($current_user->display_name) )
				$profile_text = $current_user->display_name;
			else
				$profile_text = $current_user->user_login;
			
			$profile_text_html = '<a href="'.$profile_link .'">' . $profile_text . '</a>';
			echo '';
			echo '<div class="member_div"><h4>';
			echo apply_filters('pie_profile_username_frontend_widget',$profile_text_html,$profile_link,$profile_text);
			echo '</h4>';
			echo '<a href="'.wp_logout_url().'" class="logout-link" title="Logout">'.__("Logout","piereg").'</a></div></div>';
		}
		echo $args['after_widget'];
	}
	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'before_title' ] ) ) {
			$before_title = $instance[ 'before_title' ];
		}
		else {
			$before_title = __( 'Pie Login', 'pie_login' );
		}
		if ( isset( $instance[ 'after_title' ] ) ) {
			$after_title = $instance[ 'after_title' ];
		}
		else {
			$after_title = __( 'Welcome User', 'pie_login' );
		}
		if ( isset( $instance[ 'social_login' ] ) ) {
			$social_login = $instance[ 'social_login' ];
		}
		else {
			$social_login = 0;
		}
		// Widget admin form
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'before_title' ); ?>"><?php _e( 'Before Login Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'before_title' ); ?>" name="<?php echo $this->get_field_name( 'before_title' ); ?>" type="text" value="<?php echo esc_attr( $before_title ); ?>" />
        <label for="<?php echo $this->get_field_id( 'after_title' ); ?>"><?php _e( 'After Login Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'after_title' ); ?>" name="<?php echo $this->get_field_name( 'after_title' ); ?>" type="text" value="<?php echo esc_attr( $after_title ); ?>" />
        <?php
		if(is_plugin_active('pie-register-social-site/pie-register-social-site.php')):
		?>
            <label for="<?php echo $this->get_field_id( 'social_login' ); ?>"><?php _e( 'Social Login:' ); ?></label> 
            <select class="widefat" name="<?php echo $this->get_field_name( 'social_login' ); ?>" id="<?php echo $this->get_field_id( 'social_login' ); ?>">
                <option value="0" <?php echo (esc_attr( $social_login ) == 0)?'selected="selected"':''; ?>><?php _e("Disable","piereg"); ?></option>
                <option value="1" <?php echo (esc_attr( $social_login ) == 1)?'selected="selected"':''; ?>><?php _e("Enable","piereg"); ?></option>
            </select>
		<?php
		endif;
		?>
		</p>
		<?php 
	}
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['before_title'] = ( ! empty( $new_instance['before_title'] ) ) ? strip_tags( $new_instance['before_title'] ) : '';
		$instance['after_title'] = ( ! empty( $new_instance['after_title'] ) ) ? strip_tags( $new_instance['after_title'] ) : '';
		$instance['social_login'] = ( isset( $new_instance['social_login'] ) && ! empty( $new_instance['social_login'] ) ) ? intval( $new_instance['social_login'] ) : '';
		return $instance;
	}
}

class Pie_Forgot_Widget extends WP_Widget 
{
	function __construct() 
	{
		parent::__construct(
			'pie_forgot_widget', // Base ID
			__('Pie Register - Forgot Password Form', 'piereg'), // Name
			array( 'description' => __( 'Forgot Password Form', 'piereg' ), ) // Args
		);	
	}
	public function widget( $args, $instance ) 
	{
		$option = get_option(OPTION_PIE_REGISTER);
		$pie_register = new PieRegister();
		$pie_register->piereg_ssl_template_redirect();
		if(is_user_logged_in() && $option['redirect_user']==1 ){
			//do nothing here
		}else{
			global $errors;
			$title = ($instance['title'])?$instance['title']:__( 'Forgot password', 'piereg' );
			echo $args['before_widget'];
			if ( ! empty( $title ) )
				echo $args['before_title'] . $title . $args['after_title'];
			if( file_exists(PIEREG_DIR_NAME . "/forgot_password.php") )	
				include_once("forgot_password.php");
			set_pr_stats("forgot","view");
			echo pieResetFormOutput(true);
			echo $args['after_widget'];
		}
	}
	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Forgot password', 'piereg' );
		}
		// Widget admin form
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
}