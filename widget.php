<?php
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
		$option = get_option( 'pie_register_2' );
		if(is_user_logged_in() && $option['redirect_user']==1 ){
			//do nothing here
		}else{
			global $errors;		
			$error = "";
			//$form 		= new Registration_form();
			$success 	= '' ;
			$title = apply_filters( 'widget_title', $instance['title'] );
	
			echo $args['before_widget'];
			
			//$this->pie_frontend_enqueu_scripts();				
		
			include_once("register_form.php");
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
			$output .= outputRegForm(true);
			echo $output;
			echo $args['after_widget'];
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
class Pie_Login_Widget extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	function __construct() 
	{
		parent::__construct(
			'pie_login_widget', // Base ID
			__('Pie Register - Login Form', 'pie_login'), // Name
			array( 'description' => __( 'Display Pie-Register Login Form on Sidebar', 'pie_login' ), ) // Args
		);		
		
	}
	public function widget( $args, $instance ){
		$option = get_option( 'pie_register_2' );
		global $errors;
		
		echo $args['before_widget'];
		$before_title = apply_filters( 'widget_title', $instance['before_title'] );
		$after_title = apply_filters( 'widget_title', $instance['after_title'] );
		
		
		if ( !is_user_logged_in() ) 
		{
			if ( ! empty( $before_title ) )
			echo $args['before_title'] . $before_title . $args['after_title'];
			include_once("login_form.php");
			$output = pieOutputLoginForm(true);
			echo $output;
		}else{
			global $current_user;
			get_currentuserinfo();
			//$current_user = wp_get_current_user();
			if ( ! empty( $after_title ) )
			echo $args['before_title'] . $after_title . $args['after_title'];
			
			$profile_pic_array = get_user_meta($current_user->ID);
			
			foreach($profile_pic_array as $key=>$val)
			{
				if(strpos($key,'profile_pic') !== false){
					$profile_pic = trim($val[0]);
				}
			}
			/*if(!preg_match('/(http|https):\/\/(www\.)?[\w-_\.]+\.[a-zA-Z]+\/((([\w-_\/]+)\/)?[\w-_\.]+\.(png|gif|jpg|jpeg|xpng|bmp))/',$profile_pic)){
				$profile_pic = plugin_dir_url(__FILE__).'images/userImage.png';
			}*/
			
			$profile_pic = apply_filters("piereg_profile_image_url",$profile_pic,$current_user);
			
			echo '<div class="logged-In">';
			$profile_link = get_permalink($option['alternate_profilepage']);
			$user_avater = get_avatar(get_current_user_id(),75);
			$profile_avatar = ((!empty($profile_pic))?('<img src="'.$profile_pic.'" style="max-width:75px;max-height:75px;"/>'):$user_avater);
			
			//$profile_image_html = '<a href="'.$profile_link.'"><img src="'.$profile_pic.'" style="max-width:75px;max-height:75px;"/></a>';
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
		// Widget admin form
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'before_title' ); ?>"><?php _e( 'Before Login Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'before_title' ); ?>" name="<?php echo $this->get_field_name( 'before_title' ); ?>" type="text" value="<?php echo esc_attr( $before_title ); ?>" />
        <label for="<?php echo $this->get_field_id( 'after_title' ); ?>"><?php _e( 'After Login Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'after_title' ); ?>" name="<?php echo $this->get_field_name( 'after_title' ); ?>" type="text" value="<?php echo esc_attr( $after_title ); ?>" />
		</p>
		<?php 
	}
		
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['before_title'] = ( ! empty( $new_instance['before_title'] ) ) ? strip_tags( $new_instance['before_title'] ) : '';
		$instance['after_title'] = ( ! empty( $new_instance['after_title'] ) ) ? strip_tags( $new_instance['after_title'] ) : '';
		return $instance;
	}
	
}
class Pie_Forgot_Widget extends WP_Widget 
{
	function __construct() 
	{
		parent::__construct(
			'pie_forgot_widget', // Base ID
			__('Pie Register - Forgot Password Form', 'pie_forgot'), // Name
			array( 'description' => __( 'Forgot Password Form', 'pie_forgot' ), ) // Args
		);	
	}
	public function widget( $args, $instance ) 
	{
		$option = get_option( 'pie_register_2' );
		if(is_user_logged_in() && $option['redirect_user']==1 ){
			//do nothing here
		}else{
			global $errors;
			$title = ($instance['title'])?$instance['title']:__( 'Forgot password', 'piereg' );
			echo $args['before_widget'];
			if ( ! empty( $title ) )
				echo $args['before_title'] . $title . $args['after_title'];
			
			include_once("forgot_password.php");
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
	public function update( $new_instance, $old_instance ){
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
}