<?php
global $piereg_dir_path;
if( file_exists(PIEREG_DIR_NAME."/classes/invitation_code_pagination.php") )
	include_once( PIEREG_DIR_NAME."/classes/invitation_code_pagination.php");
$piereg = get_option(OPTION_PIE_REGISTER);
?>
<form method="post" action="" id="del_form">
  <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_invitation_code_nonce','piereg_invitation_code_nonce'); ?>
  <input type="hidden" id="invi_del_id" name="invi_del_id" value="0" />
</form>
<form method="post" action="" id="status_form">
  <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_invitation_code_nonce','piereg_invitation_code_nonce'); ?>
  <input type="hidden" id="status_id" name="status_id" value="0" />
</form>
<div id="container" class="pieregister-admin">
  <div class="right_section">
    <div class="invitation settings">
      <h2 class="headingwidth">
        <?php _e("Invitation Codes",'piereg'); ?>
      </h2>
      <?php
	   if(isset($_POST['notice']) && !empty($_POST['notice']) ){
			echo '<div id="message" class="updated fade msg_belowheading"><p><strong>' . $_POST['notice'] . '.</strong></p></div>';
	   }	
       if( isset($_POST['error_message']) && !empty( $_POST['error_message'] ) )
            echo '<div style="clear: both;float: none;"><p class="error">' . $_POST['error_message']  . "</p></div>";
       if( isset($_POST['error']) && !empty( $_POST['error'] ) )
            echo '<div style="clear: both;float: none;"><p class="error">' . $_POST['error']  . "</p></div>";
       if(isset( $_POST['success_message'] ) && !empty( $_POST['success_message'] ))
            echo '<div style="clear: both;float: none;"><p class="success">' . $_POST['success_message']  . "</p></div>";
      ?>
      <form method="post" action="">
        <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_invitation_code_nonce','piereg_invitation_code_nonce'); ?>
        <ul>
          <li>
            <div class="fields">
              <p>
                <?php _e("Protect your privacy. If you want your blog to be exclusive, enable Invitation Codes to allow users to register by invitation only",'piereg'); ?>
                .<br />
                <br />
                <strong><?php _e("Note",'piereg') ?> :</strong> <?php _e("You must add the invitation code field to your registration form",'piereg') ?>.</p>
            </div>
          </li>
          <li>
            <div class="fields">
              <div class="radio_fields">
                <input type="checkbox" name="enable_invitation_codes" id="enable_invitation_codes" value="1" <?php echo ($piereg['enable_invitation_codes']=="1")?'checked="checked"':''?> />
              </div>
              <label for="enable_invitation_codes" class="labelaligned">
                <?php _e("Enable Invitation Codes","piereg");?>
              </label>
            </div>
          </li>
          <li>
            <div class="fields"> <span class="quotation" style="margin-left:0px;">
              <?php _e("Set this to Yes if you want users to register when they use an invitation code you provide. To use Invitation codes, you will need to add the Invitation Code field to registration form in the form editor","piereg");?>
              .</span> </div>
          </li>
          <li>
            <div class="fields fields_submitbtn">
              <input name="save_submit" class="submit_btn" value="<?php _e('Save Settings','piereg');?>" type="submit" />
            </div>
          </li>
          <li>
            <div class="fields">
              <h3>
                <?php _e("Insert Codes","piereg");?>
              </h3>
              <textarea id="piereg_codepass" name="piereg_codepass"><?php echo (isset($_POST['piereg_codepass'])?$_POST['piereg_codepass']:''); ?></textarea>
              <span class="note"><strong>
              <?php _e("Note","piereg");?>
              :</strong>
              <?php _e("Each Code will be on a Separate Line.","piereg");?>
              <br/>
              <?php _e("Special Characters are not allowed.","piereg");?>
              </span> </div>
          </li>
          <li>
            <div class="fields">
              <h3>
                <?php _e("Usage","piereg");?>
              </h3>
              <input style="float:left;" value="<?php echo (isset($_POST['invitation_code_usage'])?$_POST['invitation_code_usage']:''); ?>" type="text" name="invitation_code_usage" class="input_fields2" />
              <span style="text-align:left;" class="note pie_usage_note">
              <?php _e("Number of times a single code can be used to register","piereg");?>
              .</span> </div>
          </li>
          <li>
            <div class="fields fields_submitbtn">
              <input name="add_code" class="submit_btn" value="<?php _e('Add Code','piereg');?>" type="submit" />
            </div>
          </li>
        </ul>
      </form>
	  <div style="clear:both;float:left;border-right:#ccc 1px solid;padding-right:5px;margin-right:5px;">
        <form method="post" id="form_invitation_code_per_page_items">
          <?php _e("Per-Page Item","piereg"); ?>
          <select name="invitation_code_per_page_items" id="invitation_code_per_page_items" title="<?php _e("Select Per-Page Invitation code","piereg"); ?>">
            <?php
			//$opt = get_option("pie_register");
			$opt = get_option(OPTION_PIE_REGISTER);
			$per_page = ( ((int)$opt['invitaion_codes_pagination_number']) != 0)? (int)$opt['invitaion_codes_pagination_number'] : 10;
			
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
      <div style="float:left;">
        <form method="post" onsubmit="return get_selected_box_ids();" >
          <input type="hidden" value="" name="select_invitaion_code_bulk_option" id="select_invitaion_code_bulk_option">
          <select name="invitaion_code_bulk_option" id="invitaion_code_bulk_option">
            <option selected="selected" value="0">
            <?php _e("Bulk Actions","piereg"); ?>
            </option>
            <option value="delete">
            <?php _e("Delete","piereg"); ?>
            </option>
            <option value="active">
            <?php _e("Activate","piereg"); ?>
            </option>
            <option value="unactive">
            <?php _e("Deactivate","piereg"); ?>
            </option>
          </select>
          <input type="submit" value="<?php _e("Apply","piereg"); ?>" class="button action" id="doaction" name="btn_submit_invitaion_code_bulk_option">
        </form>
        <span style="color:#F00;display:none;" id="invitaion_code_error">
        <?php _e("Select Bulk Option and also Invitation Code","piereg");?>
        </span> </div>
      <?php	
			$Pie_Invitation_Table = new Pie_Invitation_Table();
			$Pie_Invitation_Table->set_order();
			$Pie_Invitation_Table->set_orderby();
			$Pie_Invitation_Table->prepare_items();
			$Pie_Invitation_Table->display();
	  		?>
    </div>
  </div>
</div>