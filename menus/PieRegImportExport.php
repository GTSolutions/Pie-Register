<script type="text/javascript">
var piereg = jQuery.noConflict();
piereg(document).ready(function(e) {
  piereg(".selectall").change(function (){
		if(piereg(this).attr("checked")=="checked")
		{
			piereg(".meta_key").attr("checked","checked")
		}
		else
		{
			piereg(".meta_key").removeAttr("checked");		
		}	  
	
 	}); 
	
	piereg(".meta_key").change(function () {
		
		if (piereg('.meta_key:checked').length == piereg('.meta_key').length) {
      		piereg(".selectall").attr("checked","checked");
    	} 
		else
		{
			piereg(".selectall").removeAttr("checked");		
		} 
	
 	});
	
	
    piereg('#date_start,#date_end').datepicker({
        dateFormat : 'yy-mm-dd',	
		 maxDate: "M D"
    });
	
	
	piereg("#start_icon").on("click", function() {
    	piereg("#date_start").datepicker("show");
	});
	
	piereg("#end_icon").on("click", function() {
    	piereg("#date_end").datepicker("show");
	});
	
	piereg("#export").on("submit", function() {
    	if(piereg('.meta_key:checked').length < 1)
		{
			alert("Please select at least one field to export.");
			return false;
		}
	});
	
});
</script>

<div class="pieregister-admin">
<div class="notifications">
  <h2>
    <?php  _e("Import/Export User Entries",'piereg') ?>
  </h2>
  
   <!-- Add Start -->
    <?php
    //$PieReg_Adds = new PieReg_Adds();
	//$PieReg_Adds->get_add("import_export");
	?>
    <!-- Add End -->
  
  <div style="clear: both;float: none;">
	  <?php
       if(!empty( $_POST['error_message'] ))
        echo '<p class="error">' . $_POST['error_message']  . "</p>";
        
         if(!empty( $_POST['success_message'] ))
        echo '<p class="success">' . $_POST['success_message']  . "</p>";
        ?>
  </div>
  <div class="export">
    
    <form method="post" action="" id="export">
    	<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_exportusers_nonce','piereg_export_users_nonce'); ?>
      <ul>
        <li>
          <div class="fields">
            <h2>
              <?php  _e("Export",'piereg'); ?>
            </h2>
            <p><?php  _e("Now you can export default user fields with a particular date range in a CSV file! Simply select your fields and select your Date Range. The Date Range feature is optional which means that if you do not select a date range then all entries will be exported. Click on the Download Export Files to complete the operation.",'piereg'); ?> </p>
          </div>
        </li>
        <li>
          <div class="fields select_checkbox">
            <h2>
              <?php _e("Select Fields",'piereg'); ?>
            </h2>
            
            <div class="export_field">
	            <input id="field_selectall" type="checkbox" class="checkbox selectall" name="piereg_select_all_checkboxes" <?php echo ((isset($_POST['piereg_select_all_checkboxes']))?'checked="checked"':''); ?>/>
    	        <label for="field_selectall"><?php _e("Select All","piereg"); ?></label>
            </div>
            <div class="export_field">
                <input id="field_user_login" type="checkbox" class="checkbox meta_key" name="pie_fields_csv[user_login]" value="Username" <?php echo ((isset($_POST['pie_fields_csv']['user_login']))?'checked="checked"':''); ?>/>
                <label for="field_user_login"><?php _e("Username","piereg"); ?></label>
            </div>
            <div class="export_field">
                <input id="field_first_name" type="checkbox" class="checkbox meta_key" name="pie_meta_csv[first_name]" value="First Name" <?php echo ((isset($_POST['pie_meta_csv']['first_name']))?'checked="checked"':''); ?> />
                <label for="field_first_name"><?php _e("First Name","piereg"); ?></label>
            </div>
            <div class="export_field">
                <input id="field_last_name" type="checkbox" class="checkbox meta_key" name="pie_meta_csv[last_name]" value="Last Name" <?php echo ((isset($_POST['pie_meta_csv']['last_name']))?'checked="checked"':''); ?> />
                <label for="field_last_name"><?php _e("Last Name","piereg"); ?></label>
            </div>
            <div class="export_field">
                <input id="field_nickname" type="checkbox" class="checkbox meta_key" name="pie_meta_csv[nickname]" value="Nickname" <?php echo ((isset($_POST['pie_meta_csv']['nickname']))?'checked="checked"':''); ?> />
                <label for="field_nickname"><?php _e("Nickname","piereg"); ?></label>
            </div>
            <div class="export_field">
                <input id="field_display_name" type="checkbox" class="checkbox meta_key" name="pie_fields_csv[display_name]" value="Display name" <?php echo ((isset($_POST['pie_fields_csv']['display_name']))?'checked="checked"':''); ?> />
                <label for="field_display_name"><?php _e("Display name","piereg"); ?></label>
            </div>
            <div class="export_field">
                <input id="field_user_email" type="checkbox" class="checkbox meta_key" name="pie_fields_csv[user_email]" value="E-mail" <?php echo ((isset($_POST['pie_fields_csv']['user_email']))?'checked="checked"':''); ?> />
                <label for="field_user_email"><?php _e("E-mail","piereg"); ?></label>
            </div>
            <div class="export_field">
                <input id="field_user_url" type="checkbox" class="checkbox meta_key" name="pie_fields_csv[user_url]" value="Website" <?php echo ((isset($_POST['pie_fields_csv']['user_url']))?'checked="checked"':''); ?> />
                <label for="field_user_url"><?php _e("Website","piereg"); ?></label>
            </div>
            <div class="export_field">
                <input id="field_description" type="checkbox" class="checkbox meta_key" name="pie_meta_csv[description]" value="Biographical Info" <?php echo ((isset($_POST['pie_meta_csv']['description']))?'checked="checked"':''); ?> />
                <label for="field_description"><?php _e("Biographical Info","piereg"); ?></label>
            </div>
            <div class="export_field">
                <input id="field_role" type="checkbox" class="checkbox meta_key" name="pie_meta_csv[wp_capabilities]" value="Role" <?php echo ((isset($_POST['pie_meta_csv']['wp_capabilities']))?'checked="checked"':''); ?> />
                <label for="field_role"><?php _e("Role","piereg"); ?></label>
            </div>
            <div class="export_field">
                <input id="field_user_registered" type="checkbox" class="checkbox meta_key" name="pie_fields_csv[user_registered]" value="User Registered" <?php echo ((isset($_POST['pie_fields_csv']['user_registered']))?'checked="checked"':''); ?> />
                <label for="field_user_registered"><?php _e("User Registered","piereg"); ?></label>
            </div>
          </div>
        </li>
        <li>
          <div class="fields date">
            <h2><?php _e("Select User Registration Date Range","piereg"); ?></h2>
            <div class="start_date">
              <label for="field_"><?php _e("Start","piereg"); ?></label>
              <input id="date_start" name="date_start" type="text" class="input_fields date_start" value="<?php echo ((isset($_POST['date_start'])?$_POST['date_start']:"")); ?>" />
              <img id="start_icon" src="<?php echo plugins_url('pie-register'); ?>/images/calendar_img.jpg" width="22" height="22" alt="calendar" class="calendar_img" />
              </div>
            <div class="end_date">
              <label for="field_"><?php _e("End","piereg"); ?></label>
              <input id="date_end" name="date_end" type="text" class="input_fields date_start" value="<?php echo ((isset($_POST['date_end'])?$_POST['date_end']:"")); ?>" />
              <img id="end_icon" src="<?php echo plugins_url('pie-register'); ?>/images/calendar.png" width="22" height="22" alt="calendar" class="calendar_img" />
              </div>
            <?php _e("Date Range is optional, if no date range is selected all entries will be exported.","piereg"); ?>
            <div class="piereg_clear"></div>
            <input type="submit" class="submit_btn" value="<?php _e("Download CSV File","piereg")?>" />
          </div>
        </li>
      </ul>
    </form>
  </div>
  <div class="import">
    <form method="post" action="" enctype="multipart/form-data">
    	<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_importusers_nonce','piereg_import_users_nonce'); ?>
      <ul>
        <li>
          <div class="fields">
            <h2>
              <?php _e("Import",'piereg'); ?>
            </h2>
            <p>
              <?php _e("Select the  CSV file you would like to import. When you click the import button below, Pie Register will import the users. Please see the example of CSV file before the import operartion.",'piereg'); ?>
            </p>
          </div>
        </li>
        <li>
          	<div class="fields">
                <h2><?php _e("Select File","piereg"); ?></h2>
                <input name="csvfile" type="file" class="input_fields" />
            </div>
        </li>
        <li>
          	<div class="fields">
            	<input type="checkbox" id="update_existing_users" value="yes" name="update_existing_users" />
	            <label for="update_existing_users" ><?php _e("Update Existing Users","piereg"); ?></label>
            </div>
        </li>
        <li>
          	<div class="fields">
                <span style="float:left"><?php echo sprintf( __( 'You may want to see', 'piereg').' <a target="_blank" href="%s"> '.__('the example of the CSV file', 'piereg').'</a>.' , plugin_dir_url(__FILE__).'examples/example.csv'); ?></span>
                <div class="piereg_clear"></div>
            </div>
        </li>
        <li>
          	<div class="fields">
                <input type="submit" class="submit_btn submit_btn2" value="<?php _e("Import","piereg")?>" />
            </div>
        </li>
      </ul>
    </form>
  </div>
</div>
</div>