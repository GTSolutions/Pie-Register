<?php 
$options = $this->get_pr_global_options();
global $piereg_dir_path;
if( file_exists(PIEREG_DIR_NAME."/classes/pie_redirect_settings.php") )
	include_once( PIEREG_DIR_NAME."/classes/pie_redirect_settings.php");
?>
<div id="role_based_redirects">
<p><strong><?php _e("Note",'piereg') ?>:</strong> <?php _e("Page settings on the Role Based Redirect tab will always override page settings on the All Users tab",'piereg') ?>.</p>
<div class="settings piereg_added_area roles_container" style="padding-bottom:0px;margin-left:0px;">
<fieldset class="piereg_fieldset_area">
  <?php if((isset($_GET['action']) && $_GET['action'] == "edit") && (isset($_GET['pie_id']) && $_GET['pie_id'] != "")){ ?>
      <legend><?php _e("Edit Record",'piereg') ?></legend>
  <?php }else{ ?>
      <legend><?php _e("Add Record",'piereg') ?></legend>
  <?php } ?>
  <form method="post" id="redirect_form">
    <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_redirect_settings_nonce','piereg_redirect_settings_nonce'); ?>
    <?php
		$input_user_role = $input_logged_in = $logged_in_page_id = $input_logout = $log_out_page_id = "";
		$is_add_new = true;
		if((isset($_GET['action']) && $_GET['action'] == "edit") && (isset($_GET['pie_id']) && $_GET['pie_id'] != ""))
		{
			global $wpdb;
			$prefix=$wpdb->prefix."pieregister_";
			$piereg_table_name =$prefix."redirect_settings";
			
			$sql 	= "SELECT * FROM `".$piereg_table_name."` WHERE `id` = %d";
			$result = $wpdb->get_results( $wpdb->prepare($sql, intval($_GET['pie_id'])) );
			if(isset($wpdb->last_error) && !empty($wpdb->last_error)){
				$this->pr_error_log($wpdb->last_error.($this->get_error_log_info(__FUNCTION__,__LINE__,__FILE__)));
			}
			echo '<input type="hidden" name="id" value="'.$result[0]->id.'" />';
			$is_add_new = false;
			$input_user_role = ((isset($_POST['piereg_user_role']))?$_POST['piereg_user_role']:$result[0]->user_role);
			$input_logged_in = ((isset($_POST['logged_in_url']))?$_POST['logged_in_url']:$result[0]->logged_in_url);
			$logged_in_page_id = ((isset($_POST['log_in_page']))?$_POST['log_in_page']:$result[0]->logged_in_page_id);
			$input_logout = ((isset($_POST['log_out_url']))?$_POST['log_out_url']:$result[0]->log_out_url);
			$log_out_page_id = ((isset($_POST['log_out_page']))?$_POST['log_out_page']:$result[0]->log_out_page_id);			
		}
		
		if(isset($_POST['redirect_settings_add_new'])){
			$input_user_role = ((isset($_POST['piereg_user_role']))?$_POST['piereg_user_role']:"");
			$input_logged_in = ((isset($_POST['logged_in_url']))?$_POST['logged_in_url']:"");
			$logged_in_page_id = ((isset($_POST['log_in_page']))?$_POST['log_in_page']:"");
			$input_logout = ((isset($_POST['log_out_url']))?$_POST['log_out_url']:"");
			$log_out_page_id = ((isset($_POST['log_out_page']))?$_POST['log_out_page']:"");
		}
		?>
    <div class="fields" style="width:100%;">
      <div class="fields">
        <label for="piereg_user_role"><?php _e("User Role",'piereg') ?></label>
        <?php
			$PieRedirectSettings = new PieRedirectSettings();
			$PieRedirectSettings->set_order();
			$PieRedirectSettings->set_orderby();
			$all_user_roles = $PieRedirectSettings->get_sql_results("`user_role`");
			$saved_user_roles = array();
			foreach($all_user_roles as $val) {
				if($val->user_role)
					$saved_user_roles[$val->user_role] = $val->user_role;
			}
			
			$user_role = "";
			
			if(!empty($input_user_role)) {
				$user_role = $input_user_role;
				
			} 
			?>
        <select id="piereg_user_role" name="piereg_user_role" <?php echo (((isset($_GET['action']) && $_GET['action'] == "edit") && (isset($_GET['pie_id']) && $_GET['pie_id'] != "") && !empty($user_role))?'disabled="disabled"':""); ?> >
        <?php
			global $wp_roles;
			//$role = get_option("wp_user_roles");
			$role = $wp_roles->roles;
			
			$piereg_user_role = (!empty($user_role))?$user_role:"";
			foreach($role as $key=>$value) {
				if(in_array($key,$saved_user_roles) && ($piereg_user_role != $key))
					continue;
				
				echo '<option value="'.$key.'"';
				echo ($piereg_user_role == $key) ? ' selected="selected" ' : '';
				echo '>'.$value['name'].'</option>';
			}
			?>
        </select>
      </div>
    </div>
    <div class="fields" style="width:100%;">
      <div class="fields">
        <label for="log_in_page">
          <?php _e("After Log In Page",'piereg') ?>
        </label>
        <?php 
			$args 	= array("show_option_no_change"=>"None","id"=>"log_in_page","name"=>"log_in_page","selected"=>$logged_in_page_id,"echo"=>false);
			$pages	= wp_dropdown_pages( $args );
			$url	= '<option value="0"'; 
			if($logged_in_page_id == "0") $url.=' selected="selected"'; 
			$url.='>&lt;URL&gt;</option></select>';
			$pages	= str_replace('</select>', $url, $pages);
			echo $pages;			
			?>
      </div>
      <div class="fields <?php echo ($logged_in_page_id == "0") ? "": "hide"; ?>">
        <label for="logged_in_url"></label>
        <input type="url" name="logged_in_url" id="logged_in_url" value="<?php echo urldecode($input_logged_in); ?>" class="input_fields" />
      </div>
    </div>
    <div class="piereg_clear"></div>
    <div class="fields" style="width:100%;">
      <div class="fields">
        <label for="log_out_page">
          <?php _e("After Log out Page",'piereg') ?>
        </label>
        <?php 
			$args 	= array("show_option_no_change"=>"None","id"=>"log_out_page","name"=>"log_out_page","selected"=>$log_out_page_id,"echo"=>false);
			$pages2	= wp_dropdown_pages( $args );
			$url2	= '<option value="0"'; 
			if($log_out_page_id == "0") $url2.=' selected="selected"'; 
			$url2.='>&lt;URL&gt;</option></select>';
			$pages2	= str_replace('</select>', $url2, $pages2);
			echo $pages2;  
			?>
      </div>
      <div class="fields <?php echo ($log_out_page_id == "0") ? "": "hide"; ?>">
        <label for="log_out_url"></label>
        <input type="url" name="log_out_url" id="log_out_url" value="<?php echo urldecode($input_logout); ?>" class="input_fields" />
      </div>
    </div>
    <div class="fields">
      <?php if(!$is_add_new){ ?>
          <input type="submit" class="submit_btn submit_btn_mar_ryt2" name="redirect_settings_update" value=" <?php _e("Update","piereg");?> " />
          <a href="<?php echo admin_url('admin.php?page=pie-settings&tab=pages&subtab=role-based') ?>" style="float:right;margin:19px 10px 0 0;">Go back to add new record</a>
      <?php }else{?>
      	<input type="submit" class="submit_btn submit_btn_mar_ryt2" name="redirect_settings_add_new" value=" <?php _e("Save Record","piereg");?> " />
      <?php } ?>
    </div>
  </form>
</fieldset>
</div>
<div class="piereg_clear"></div>
<div class="piereg_clear"></div>
<div class="invitation" style="margin-left:0px;">
  <div style="clear:both;float:left;padding-right:5px;margin-right:5px;">
    <form method="post" id="form_invitation_code_per_page_items">
      <?php _e("Per-Page Item","piereg"); ?>
      <select name="invitation_code_per_page_items" id="invitation_code_per_page_items" title="<?php _e("Select Per-Page User Role Redirection","piereg"); ?>">
        <?php
			$per_page = (isset($_POST['invitation_code_per_page_items']))? intval($_POST['invitation_code_per_page_items']) : 10;
			
			for($per_page_item = 10; $per_page_item <= 50; $per_page_item +=10)
			{
				$checked = ($per_page == $per_page_item)? 'selected="selected"':'';
				echo '<option value="'.$per_page_item.'" '.$checked.'>'.$per_page_item.'</option>';
			}
			echo '<option value="75" '.(($per_page == "75")? 'selected="selected"':'').' >75</option>';
			echo '<option value="100" '.(($per_page == "100")? 'selected="selected"':'').' >100</option>';
			?>
      </select>
    </form>
  </div>
	<?php
		/*
		*	Add Table
		*/
		$PieRedirectSettings = new PieRedirectSettings();
		$PieRedirectSettings->set_order();
		$PieRedirectSettings->set_orderby();
		$PieRedirectSettings->prepare_items();
		$PieRedirectSettings->display();
		?>
</div>
<form method="post" action="" id="redirect_settings_del_form">
  <input type="hidden" id="redirect_settings_del_id" name="redirect_settings_del_id" value="0" />
  <input type="submit" style="display:none;" />
</form>
<form method="post" action="" id="redirect_settings_status_form">
  <input type="hidden" id="redirect_settings_status_id" name="redirect_settings_status_id" value="0" />
  <input type="submit" style="display:none;" />
</form>
</div>