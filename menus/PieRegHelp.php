<?php 
	# Define variable's default values
	$action = $subaction = "";
	$active	= 'class="active"';
	
	if(isset($_GET['tab']))
		$action	= $_GET['tab'];
	if(isset($_GET['subtab']))
		$subaction	= $_GET['subtab'];	
	
	//$zclip = plugins_url( 'zcjs/ZeroClipboard.js', __FILE__ );
	//$zclip_swf = plugins_url( 'zcjs/ZeroClipboard.swf', __FILE__ );
	
	global $errors;
	$license_key_errors = "";
	
	if(isset($errors->errors['piereg_license_error']) && !empty($errors->errors['piereg_license_error']))
	{
		foreach($errors->errors['piereg_license_error'] as $error_val){
			$license_key_errors .= "<p><strong>".$error_val."</strong></p>";
		}
	}
	?>
<div id="container"  class="pieregister-admin">
  <div class="right_section">
    <div class="settings">
      <h2 class="headingwidth"><?php _e("Help",'piereg') ?></h2>   
      <?php 
	  	if( empty($license_key_errors) )
		{
			if( isset($_POST['notice']) && !empty($_POST['notice']) ){
				echo '<div id="message" class="updated fade msg_belowheading"><p><strong>' . $_POST['notice'] . '</strong></p></div>';
			}
			else if( isset($_POST['error']) && !empty($_POST['error']) ){
				echo '<div id="error" class="error fade msg_belowheading"><p><strong>' . $_POST['error'] . '</strong></p></div>';
			}
			if(  isset($_POST['success']) && !empty($_POST['success']) ){
				echo '<div id="message" class="updated fade msg_belowheading"><p><strong>' . $_POST['license_success'] . '.</strong></p></div>';
			}
		}
		?>
        <div id="tabsSetting" class="tabsSetting">
            <div class="whiteLayer"></div>
        	<ul class="tabLayer1">
            	<li <?php echo ($action == "documentation" || $action == "") ? $active :""; ?> >
                	<a href="admin.php?page=pie-help&tab=documentation"><?php _e("Documentation",'piereg') ?></a></li>
                <li <?php echo ($action == "shortcodes") ? $active :""; ?>>
                	<a href="admin.php?page=pie-help&tab=shortcodes"><?php _e("Shortcodes",'piereg') ?></a></li>
                <li <?php echo ($action == "license") ? $active :""; ?>>
                	<a href="admin.php?page=pie-help&tab=license"><?php _e("License",'piereg') ?></a></li>
            	<li <?php echo ($action == "version") ? $active :""; ?> >
                	<a href="admin.php?page=pie-help&tab=version"><?php _e("Version",'piereg') ?></a>
                	<ul class="tabLayer2">
                    	<li <?php echo ($subaction == "environment" || $action == "version" && $subaction == "" ) ? $active :""; ?>>
                        	<a href="admin.php?page=pie-help&tab=version&subtab=environment"><?php _e("Environment",'piereg') ?></a></li>
                            <li><img src="<?php echo $this->plugin_url ?>images/settingTabSeperator.jpg"/></li>    
                    	<li <?php echo ($subaction == "plugins-themes") ? $active :""; ?>>
                        	<a href="admin.php?page=pie-help&tab=version&subtab=plugins-themes"><?php _e("Plugins and Themes",'piereg') ?></a></li>
                            <li><img src="<?php echo $this->plugin_url ?>images/settingTabSeperator.jpg"/></li>    
                        <li <?php echo ($subaction == "error-log") ? $active :""; ?> >
                        	<a href="admin.php?page=pie-help&tab=version&subtab=error-log"><?php _e("Error Log",'piereg') ?></a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="wrapper-forms">
        	<?php if($action == "documentation" || $action == ""){ ?>
            	<p class="pieHelpPara">
                <div style="clear:both;">
                <?php _e("Welcome to the Pie-Register&acute;s Customer Support Page. Many of your installation and setup related queries are answered in our FAQ&acute;s, Documentation and Forums sections listed below. It is suggested that before you submit a support ticket, please review the mentioned sections for a clear and better understanding of Pie-Register. This will reduce the Support Volume for a timely execution of the Support Process","piereg"); ?>
                </div>
                <br /><br />
                <?php _e("If you still have any query, feel free to contact us by submitting a support ticket form on the right","piereg"); ?></p>
                <div class="pieHelpMenuButtonContaner">
                    <ul class="pieHelpMenuButton">
                        <li><a href="http://pieregister.com/faqs/" target="_blank_pieHelp_1"><?php _e("Browse Frequently Asked Questions","piereg"); ?></a></li>
                        <li><a href="http://pieregister.com/get-support/" target="_blank_pieHelp_2"><?php _e("Pie-Register v2.0 Beta Problems","piereg"); ?></a></li>
                        <li><a href="http://pieregister.com/forum/" target="_blank_pieHelp_3"><?php _e("Go To Forums","piereg"); ?></a></li>
                        <li><a href="http://pieregister.com/using-pie-register/" target="_blank_pieHelp_4"><?php _e("Review Documentation","piereg"); ?></a></li>
                        <li><a href="http://pieregister.com/getting-started/" target="_blank_pieHelp_5"><?php _e("Getting Started","piereg"); ?></a></li>
                        <li><a href="http://pieregister.com/setting-up-pie-register/" target="_blank_pieHelp_6"><?php _e("Setting up Pie-Register","piereg"); ?></a></li>
                        <li><a href="http://pieregister.com/getting-started/" target="_blank_pieHelp_7"><?php _e("Installation Problems","piereg"); ?></a></li>
                        <li><a href="http://pieregister.com/using-pie-register/" target="_blank_pieHelp_8"><?php _e("Using Pie-Register","piereg"); ?></a></li>
                        <li><a href="http://pieregister.com/forums/forumd/news-announcements/" target="_blank_pieHelp_9"><?php _e("News and Announcements","piereg"); ?></a></li>
                    </ul>
                </div>
            <?php }elseif($action == "shortcodes"){ ?>
            	<p class="pieHelpPara">
			<?php _e("Pie Register allows you to easily embed Login, Registration, Forgot Password and Profile pages anywhere using Shortcodes. You can embed these pages inside a post, page, custom post type or even in a widgets by using the following Shortcodes","piereg"); ?></p>
            	<table id="PR_table_Short_Code" cellspacing="0" cellpadding="10" >
                    <tr>
                        <td><strong><?php _e("Forms","piereg"); ?></strong></td>
                        <td><strong><?php _e("Short Code","piereg"); ?></strong></td>
                    </tr>
                    <?php
                    $fields_id = get_option("piereg_form_fields_id");
					$form_on_free	= get_option("piereg_form_free_id");
					$count = 0;
                    for($a=1;$a<=intval($fields_id);$a++)
                    {
                        $option = get_option("piereg_form_field_option_".$a);
                        if( !empty($option) && is_array($option) && isset($option['Id']) && (!isset($option['IsDeleted']) || trim($option['IsDeleted']) != 1) )
                        {
                            echo '
                            <tr>
                                <td><label for="F_R_F_U_'.$a.'">'.($option['Title']).' : </label></td>
                                <td>
                                    <textarea readonly="readonly" class="PR_short_code_input piereg-select-all-text-onclick" id="F_R_F_U_'.$a.'">[pie_register_form id="'.$option['Id'].'" title="true" description="true"]</textarea>
                                </td>
                            </tr>';
                            //echo '';
							$count++;
							
							if( $count == 1 && !PIEREG_IS_ACTIVE )
							{
								if( !$form_on_free )
								{
									update_option('piereg_form_free_id', $option['Id']);
									$form_on_free .= $option['Id'];
								}
								break;
							}
                        }
                    }
                    ?>
                    <tr>
                        <td><label for="F_L_F_U"><?php _e("Login Form","piereg"); ?> : </label></td>
                        <td>
                            <input type="text" id="F_L_F_U" value="[pie_register_login]" readonly="readonly" class="PR_short_code_input piereg-select-all-text-onclick" />
                        </td>
                    </tr>
                    <tr>
                        <td><label for="F_F_P_F_U"><?php _e("Forgot Password Form","piereg"); ?> : </label></td>
                        <td>
                            <input type="text" id="F_F_P_F_U" value="[pie_register_forgot_password]" readonly="readonly" class="PR_short_code_input piereg-select-all-text-onclick" />
                        </td>
                    </tr>
                    <tr>
                        <td><label for="F_P_P_U"><?php _e("Profile Page","piereg"); ?> : </label></td>
                        <td>
                            <input type="text" id="F_P_P_U" value="[pie_register_profile]" readonly="readonly" class="PR_short_code_input piereg-select-all-text-onclick" />
                        </td>
                    </tr>
                    <?php #PIEREG_IS_ACTIVE
						if(false){ ?>
                        <tr>
                            <td><label for="F_R_A"><?php _e("Renew Account Page","piereg"); ?> : </label></td>
                            <td>
                                <input type="text" id="F_R_A" value="[pie_register_renew_account]" readonly="readonly" class="PR_short_code_input piereg-select-all-text-onclick" />
                            </td>
                        </tr>
                     <?php } ?>
                    <?php do_action("pieregister_print_shortcode"); ?>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            <?php
				}elseif($action == "license"){
				$is_pro_active =false;
                if(PIEREG_IS_ACTIVE){
					$is_pro_active = true;
					$LK_options = get_option( PIEREG_LICENSE_KEY_OPTION );
            		?>
            	<form method="post" action="" id="piereg_form_general_settings_page">
                    <div class="fields">
                        <label for="custom_logo_title"><?php _e( 'License Key', 'piereg' ); ?></label>
                        <input type="text" class="input_fields" value="<?php echo $LK_options['api_key'];?>" disabled="disabled" readonly="readonly" />
                        <span class="piereg_license_key_icon_span"><img class="piereg_license_key_icon" title="" src="<?php echo plugins_url("images/complete.png", dirname(__FILE__) ); ?>"></span>
                    </div>
                    
                    <div class="fields">
                        <label for="custom_logo_title"><?php _e( 'Email Address', 'piereg' ); ?></label>
                        <input type="text" class="input_fields" value="<?php echo $LK_options['activation_email'];?>" disabled="disabled" readonly="readonly" />
                        <span class="piereg_license_key_icon_span"><img class="piereg_license_key_icon" title="" src="<?php echo plugins_url("images/complete.png", dirname(__FILE__) ); ?>"></span>
                    </div>
                    <div class="fields fields2">
                        <input type="hidden" id="is_deactivate_plugin_license" name="is_deactivate_plugin_license" value="true" />
                        <input name="action" value="pie_reg_update" type="hidden" />
                      <input type="submit" class="submit_btn" value=" <?php _e("Deactivate","piereg");?> " onclick="return window.confirm('Are you sure you want to deactivate the plugin license?');" />
                      <p><?php _e("Deactivate this license to use on another site",'piereg') ?>.</p>
                    </div>
                </form>
                
                <?php
				}else{ 
					
                    $this->require_once_file( plugin_dir_path( dirname(__FILE__) ) .'/menus/PieLicenseKeyPage.php');
                }
				?>
                <div class="pie_addons" style="clear:both">
                	<h3><?php _e("Pie Register Add-ons",'piereg') ?>:</h3>
                <?php
					if( (isset($license_key_errors) && !empty($license_key_errors)) &&  $is_pro_active ){
						echo '<div id="error" class="error fade msg_belowheading">' . $license_key_errors . '</div>';
					}
					
					do_action("pieregister_addons_listing");
				?>
                </div>
            <?php }elseif($action == "version" && $subaction == "" || $action == "version" && $subaction == "environment"){ ?>
            		<?php
                    	$pr_ver = get_plugins();
						if($pr_ver['pie-register/pie-register.php'] != ''){
						?>
                    <div class="fields">
                      <label><?php _e("Pie Register Version",'piereg') ?></label>
                      <?php
						echo '<span class="installation_status">'.$pr_ver['pie-register/pie-register.php']['Name'].' '.$pr_ver['pie-register/pie-register.php']['Version'].'</span>';
                      ?>
                    </div>
                    <?php
                    	}
					?>
                    <div class="fields">
                      <label><?php _e("PHP Version",'piereg') ?></label>
                      <?php if(version_compare(phpversion(),  "5.0") == 1)
                      {
                          echo '<span class="installation_status">'.phpversion().'</span>';
                      }
                      else
                      {
                          echo '<span class="installation_status_faild">'.phpversion().'</span>';
                          echo '<span class="quotation">'.__("Sorry, Pie-Register requires PHP 5.0 or higher. Please deactivate Pie-Register","piereg").'</span>';
                      }
                      ?>
                    </div>
                    <div class="fields">
                      <label><?php _e("MySQL Version",'piereg') ?></label>
                      <?php
                        global $wpdb;
                        $piereg_mytsql_version_info = $wpdb->db_version();
                        if(version_compare($piereg_mytsql_version_info,  "5.0") == 1)
                        {
                            echo '<span class="installation_status">'.$piereg_mytsql_version_info.'</span>';
                        }
                        else
                        {
                            echo '<span class="installation_status_faild">'.$piereg_mytsql_version_info.'</span>';
                            echo '<span class="quotation">'.__("Sorry, Pie-Register requires MySQL 5.0 or higher. Please deactivate Pie-Register","piereg").'</span>';
                        }
                        ?>
                      
                    </div>
                    <div class="fields">
                      <label><?php _e("Wordpress Version",'piereg') ?></label>
                      <?php if(version_compare(get_bloginfo('version'),  "3.5") == 1)
                      {
                          echo '<span class="installation_status">'.get_bloginfo('version').'</span>';
                      }
                      else
                      {
                          echo '<span class="installation_status_faild">'.get_bloginfo('version').'</span>';
                          echo '<span class="quotation">'.__("Sorry, Pie-Register requires Wordpress 3.5 or higher. Please deactivate Pie-Register","piereg").'</span>';
                      }
                      ?>
                    </div>
                    <div class="fields">
                      <label><?php _e("Curl",'piereg') ?></label>
                      <?php if(function_exists('curl_version'))
                      {
                          echo '<span class="installation_status">'.__("Enable","piereg").'</span>';
                      }
                      else
                      {
                          echo '<span class="installation_status_faild">'.__("Disable","piereg").'</span>';
                          echo '<span class="quotation">'.__("Please install CURL on server","piereg").'</span>';
                      }
                      ?>
                    </div>
                    <div class="fields">
                      <label><?php _e("File Get Contents",'piereg') ?></label>
                      <?php if(function_exists('file_get_contents'))
                      {
                          echo '<span class="installation_status">'.__("Enable","piereg").'</span>';
                      }
                      else
                      {
                          echo '<span class="installation_status_faild">'.__("Disable","piereg").'</span>';
                          echo '<span class="quotation">'.__("Please install File Get Contents on server","piereg").'</span>';
                      }
                      ?>
                    </div>
                    <div class="fields">
                      <label><?php _e("MB String",'piereg') ?></label>
                      <?php if (extension_loaded('mbstring'))
                      {
                          echo '<span class="installation_status">'.__("Enable","piereg").'</span>';
                      }
                      else
                      {
                          echo '<span class="installation_status_faild">'.__("Disable","piereg").'</span>';
                          echo '<span class="quotation">'.__("Please install File Get Contents on server","piereg").'</span>';
                      }
                      ?>
                    </div>
                    <?php if ( function_exists( 'ini_get' ) ){ ?>
                      
                            <div class="fields">
                                  <label><?php _e("PHP Post Max Size",'piereg') ?></label>
                                  <?php
                                  echo '<span class="installation_status installation_status_no_bg">'.(ini_get('post_max_size')).'</span>';
                              ?>
                            </div>
                            <div class="fields">
                                  <label><?php _e("PHP Time Limit",'piereg') ?></label>
                                  <?php
                                  echo '<span class="installation_status installation_status_no_bg">'.(ini_get('max_execution_time')).'</span>';
                              ?>
                            </div>
                            
                    <?php } else {?>
                            <div class="fields">
                                 <label><?php _e("ini_get",'piereg') ?></label><?php
                                 echo '<span class="installation_status_faild">'.__("Disable","piereg").'</span>';
                                 echo '<span class="quotation">'.__("Please install ini_get on server","piereg").'</span>';
                              ?>
                            </div>
                    <?php } ?>
                    <div class="fields">
                      <label><?php _e("WP Memory Limit",'piereg') ?></label>
                      <?php
                      echo '<span class="installation_status installation_status_no_bg">'.WP_MEMORY_LIMIT.'</span>';
                      ?>
                    </div>
                    <div class="fields">
                      <label><?php _e("WP Debug Mode",'piereg') ?></label>
                      <?php
                      if ( defined('WP_DEBUG') && WP_DEBUG ) echo '<span class="installation_status installation_status_no_bg">' . __( 'Yes', 'piereg' ) . '</span>'; else echo '<span class="installation_status installation_status_no_bg">' . __( 'No', 'piereg' ) . '</span>';
                      ?>
                    </div>
                    <div class="fields">
                      <label><?php _e("WP Language",'piereg') ?></label>
                      <?php
                      echo '<span class="installation_status installation_status_no_bg">' . get_locale() . '</span>';
                      ?>
                    </div>
                    <div class="fields">
                      <label><?php _e("WP Max Upload Size",'piereg') ?></label>
                      <?php
                      echo '<span class="installation_status installation_status_no_bg">' . size_format( wp_max_upload_size() ) . '</span>';
                      ?>
                    </div>
                    <textarea id="piereg_log3_view_area" name="piereg_log3_view_area" style="display:none;"><?php
                    	//PieRegisterVersion
						echo "Pie Register Version: ".$pr_ver['Name'].' '.$pr_ver['Version']."\r\n\r\n";
						//PhpVersion
						if(version_compare(phpversion(),  "5.0") == 1)
						{
							echo "PHP Version: ".phpversion()."\r\n\r\n";
						}
						else
						{
							echo "PHP Version: ".phpversion()." (Sorry, Pie-Register requires PHP 5.0 or higher. Please deactivate Pie-Register) \r\n\r\n";
						}
						//MySqlVersion
						if(version_compare($piereg_mytsql_version_info,  "5.0") == 1)
                        {
                            echo "MySQL Version: ".$piereg_mytsql_version_info."\r\n\r\n";
                        }
                        else
                        {
                            echo "MySQL Version: ".$piereg_mytsql_version_info." (Sorry, Pie-Register requires MySQL 5.0 or higher. Please deactivate Pie-Register) \r\n\r\n";
                        }
						//WordpressVersion
						if(version_compare(get_bloginfo('version'),  "3.5") == 1)
                      	{
							echo "Wordpress Version: ".get_bloginfo('version')."\r\n\r\n";
                      	}
                      	else
                      	{
                       	   echo "Wordpress Version: ".get_bloginfo('version')." (Sorry, Pie-Register requires Wordpress 3.5 or higher. Please deactivate Pie-Register) \r\n\r\n";
                      	}
						//CurlVersion
						if(function_exists('curl_version'))
                     	{
							echo "Curl: Enable \r\n\r\n";
                      	}
                      	else
                      	{
						   echo "Curl: Disable (Please install CURL on server) \r\n\r\n";
                      	}
						//FileGetContents
						if(function_exists('file_get_contents'))
                  	    {
                   	       echo "File Get Contents: Enable \r\n\r\n";
                      	}
                      	else
                      	{
                       	   echo "File Get Contents: Disable (Please install File Get Contents on server) \r\n\r\n";
                      	}
						//MbString
						if (extension_loaded('mbstring'))
                      	{
                       	   echo "MB String: Enable \r\n\r\n";
                      	}
                      	else
                      	{
                       	   echo "MB String: Disable (Please install MB String on server) \r\n\r\n";
                      	}
						//Php-ini_get
						if ( function_exists( 'ini_get' ) )
						{
                      		echo "PHP Post Max Size: ".(ini_get('post_max_size'))." \r\n\r\n";
							echo "PHP Time Limit: ".(ini_get('max_execution_time'))." \r\n\r\n";
                        }
						else
						{
							echo "ini_get: Disable (Please install ini_get on server) \r\n\r\n";
                    	}
						//WpMemoryLimit
						echo "WP Memory Limit: ".WP_MEMORY_LIMIT." \r\n\r\n";
						//WpDebug
						if ( defined('WP_DEBUG') && WP_DEBUG )
						{
							echo "WP Debug Mode: Yes \r\n\r\n";
						}
						else
						{
							echo "WP Debug Mode: No \r\n\r\n";
						}
						//WpLanguage
						echo "WP Language: ".get_locale()." \r\n\r\n";
						//WpMaxUploadSize
						echo "WP Max Upload Size: ".size_format( wp_max_upload_size() ); ?></textarea>
                         
                        
            <?php }elseif($action == "version" && $subaction == "plugins-themes"){ ?>
            			<textarea id="piereg_log2_view_area" name="piereg_log2_view_area" style="max-width:100%;min-width:50%;width:100%;height:300px;" readonly="readonly"><?php 
				
								$themes = wp_get_themes();
								#$current_theme = get_current_theme(); get_current_theme() is deprecated since version 3.4!
								$current_theme = wp_get_theme();
								echo "================= Themes =================\r\n\r\n";
								foreach($themes as $theme){
									if( $current_theme == $theme['Name'] )
										echo $theme['Name']." [ACTIVATED]\r\n";
									else
										echo $theme['Name']." [DEACTIVATED]\r\n";
								}
								
								$activate_plugins 	= get_option('active_plugins');
								$all_plugins 		= get_plugins();
								echo "\r\n\r\n================= Plugins (".count($activate_plugins)."/".count($all_plugins).") =================\r\n\r\n";
								foreach($all_plugins as $key=>$plugin){
									if( in_array($key,$activate_plugins) )
										echo $plugin['Name']." [ACTIVATED]\r\n";
									else
										echo $plugin['Name']." [DEACTIVATED]\r\n";
								}
			  ?></textarea>
             
            <?php }elseif($action == "version" && $subaction == "error-log"){ ?>
            	<div class="piereg_log_file_download">
        
            <div class="piereg_log_file_view">
                <textarea id="piereg_log_file_view_textarea" name="piereg_log_file_view_textarea" readonly="readonly" style="max-width:100%;min-width:50%;width:100%;height:300px;"><?php echo $this->piereg_get_log_file();  ?></textarea>
            </div>
    </div>
            <?php } ?>            
        </div>
    </div>
  </div>
</div>