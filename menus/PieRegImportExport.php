<div class="pieregister-admin">
  <div class="settings" style="padding-bottom:0px;">
    <h2>
      <?php  _e("Import/Export",'piereg') ?>
    </h2>
  </div>
  <div style="clear: both;float: none;">
    <?php
       if( isset($_POST['error_message']) && !empty( $_POST['error_message'] ) )
	        echo '<p class="error">' . $_POST['error_message']  . "</p>";
       if( isset($_POST['error']) && !empty( $_POST['error'] ) )
    	    echo '<p class="error">' . $_POST['error']  . "</p>";
       if(isset( $_POST['success_message'] ) && !empty( $_POST['success_message'] ))
	        echo '<p class="success">' . $_POST['success_message']  . "</p>";
        ?>
  </div>
  <div class="settings" style="padding-bottom:0px;">
    <?php if($this->piereg_pro_is_activate){ ?>
    <div class="right_section importexport">
      <fieldset class="piereg_fieldset_area">
        <legend>
        <?php _e("All Settings",'piereg') ?>
        </legend>
        <div class="fields">
          <form method="post" action="">
            <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_export_general_settings','piereg_export_general_settings'); ?>
            <label>
              <?php _e("Export",'piereg') ?>
            </label>
            <input type="hidden" name="import_export_settings" value="1" />
            <input type="submit" name="export_general_settings" value=" <?php _e("Export","piereg"); ?> " class="button button-primary button-large"  />
          </form>
        </div>
        <div class="fields">
          <form method="post" action="" enctype="multipart/form-data">
            <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_import_general_settings','piereg_import_general_settings'); ?>
            <label>
              <?php _e("Import",'piereg') ?>
            </label>
            <div class="file_container">
                <input type="file" name="import_general_settings_file" class="import_general_settings_file" />
                <input type="hidden" name="import_export_settings" value="1" />
                <input type="submit" name="import_general_settings" value=" <?php _e("Import","piereg"); ?> " 
                					 onclick="validImportForm(this.form, '.import_general_settings_file')" 
                                	 class="button button-primary button-large" 
                                	 />            
                <span class="quotation"><strong>
                <?php _e("Warning","piereg"); ?>
                </strong>:
                <?php _e("Only supports json format. Importing data will remove all your existing settings","piereg"); ?>
                </span>
            </div>
          </form>
        </div>
      </fieldset>
      <fieldset class="piereg_fieldset_area">
        <legend>
        <?php _e("E-mail Templates",'piereg') ?>
        </legend>
        <div class="fields">
          <form method="post" action="">
            <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_export_email_template','piereg_export_email_template'); ?>
            <label>
              <?php _e("Export",'piereg') ?>
            </label>
            <input type="hidden" name="import_export_settings" value="1" />
            <input type="submit" name="export_email_template" value=" <?php _e("Export","piereg"); ?> " class="button button-primary button-large"  />
          </form>
        </div>
        <div class="fields">
          <form method="post" action="" enctype="multipart/form-data">
            <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_import_email_template','piereg_import_email_template'); ?>
            <label>
              <?php _e("Import",'piereg') ?>
            </label>
            <div class="file_container">
                <input type="file" name="import_email_template_file" class="import_email_template_file" />
                <input type="hidden" name="import_export_settings" value="1" />
                <input type="submit" name="import_email_template" value=" <?php _e("Import","piereg"); ?> " 
                			onclick="validImportForm(this.form, '.import_email_template_file')" 
                            class="button button-primary button-large" />
                <span class="quotation"><strong>
                <?php _e("Warning","piereg"); ?>
                </strong>:
                <?php _e("Only supports json format. Importing data will remove all your existing email templates","piereg"); ?>
                </span>
            </div>
          </form>
        </div>
      </fieldset>
      <fieldset class="piereg_fieldset_area">
        <legend>
        <?php _e("Invitation Codes",'piereg') ?>
        </legend>
        <div class="fields">
          <form method="post" action="">
            <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_export_invitations_codes','piereg_export_invitations_codes'); ?>
            <label>
              <?php _e("Export",'piereg') ?>
            </label>
            <input type="hidden" name="import_export_settings" value="1" />
            <input type="submit" name="export_invitations_codes" value=" <?php _e("Export","piereg"); ?> " class="button button-primary button-large"  />
          </form>
        </div>
        <div class="fields">
          <form method="post" action="" enctype="multipart/form-data">
            <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_import_invitations_codes','piereg_import_invitations_codes'); ?>
            <label>
              <?php _e("Import",'piereg') ?>
            </label>
            <div class="file_container">
                <input type="file" name="import_invitations_codes_file" class="import_invitations_codes_file" />
                <input type="hidden" name="import_export_settings" value="1" />
                <input type="submit" name="import_invitations_codes" value=" <?php _e("Import","piereg"); ?> " 
                				onclick="validImportForm(this.form, '.import_invitations_codes_file')"  
                                class="button button-primary button-large"  />
                <span class="quotation"><strong>
                <?php _e("Warning","piereg"); ?>
                </strong>:
                <?php _e("Only supports json format. Importing data will remove all your existing invitation codes","piereg"); ?>
                </span>
            </div>
          </form>
        </div>
      </fieldset>
      <fieldset class="piereg_fieldset_area">
        <legend>
        <?php _e("All Users Data With Custom Fields",'piereg') ?>
        </legend>
        <?php
			if(isset( $_POST['successfull_import_all_users_data'] ) && !empty( $_POST['successfull_import_all_users_data'] ))
				echo '<p class="success">' . sprintf( __("Successfully Imported (%d) User(s)","piereg"), intval($_POST['successfull_import_all_users_data']) ) . "</p>";
			if(isset( $_POST['unsuccessfull_import_all_users_data'] ) && !empty( $_POST['unsuccessfull_import_all_users_data'] ))
				echo '<p class="error">' . sprintf( __("Already Registered (%d) User(s)","piereg"), intval($_POST['unsuccessfull_import_all_users_data']) ) . "</p>";
			?>
        <div class="fields">
          <form method="post" action="">
            <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_export_all_user_custom_data','piereg_export_all_user_custom_data'); ?>
            <label>
              <?php _e("Export",'piereg') ?>
            </label>
            <input type="hidden" name="import_export_settings" value="1" />
            <input type="submit" name="piereg_export_user_custom_data" value=" <?php _e("Export","piereg"); ?> " class="button button-primary button-large"  />
          </form>
          
        </div>
        <div class="fields">
          <form method="post" action="" enctype="multipart/form-data">
            <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_import_all_user_custom_data','piereg_import_all_user_custom_data'); ?>
            <label>
              <?php _e("Import",'piereg') ?>
            </label>
            <input type="file" name="import_all_users_data_with_custom_field" class="import_all_users_data_with_custom_field" />
            <input type="hidden" name="import_export_settings" value="1" />
            <input type="submit" name="piereg_import_user_custom_data" value="<?php _e("Import","piereg"); ?>" 
            					onclick="validImportForm(this.form, '.import_all_users_data_with_custom_field')" 
                                class="button button-primary button-large"  />
          </form>
          <label>&nbsp;</label><div class="file_container">
          <span class="quotation"><strong>
                <?php _e("Warning","piereg"); ?>
                </strong>:
                <?php _e("Only supports json format","piereg"); ?>
                </span></div>
        </div>
      </fieldset>
    </div>
    <?php } ?>
  </div>
  <div class="notifications">
    <div class="settings importexport" style="padding-bottom:0px;">
      <h3>
        <?php  _e("User Entries",'piereg') ?>
      </h3>
      <div class="export">
        <form method="post" action="" id="export">
          <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_exportusers_nonce','piereg_export_users_nonce'); ?>
          <ul>
            <li>
              <div class="fields">
                <h2>
                  <?php  _e("Export",'piereg'); ?>
                </h2>
                <p>
                  <?php  _e("Now you can export all users with custom fields within a date range to a CSV file! Simply select the fields and select your Date Range. The Date Range feature is optional, if you do not select a date range then all entries will be exported. Click on the Download CSV File to complete the operation.",'piereg'); ?>
                </p>
              </div>
            </li>
            <li>
              <div class="fields select_checkbox">
                <h2>
                  <?php _e("Select Fields",'piereg'); ?>
                </h2>
                <div class="export_field">
                  <input id="field_selectall" type="checkbox" class="checkbox selectall" />
                  <label for="field_selectall">
                    <?php _e("Select All","piereg"); ?>
                  </label>
                </div>
                <div class="export_field">
                  <input id="field_user_login" type="checkbox" class="checkbox meta_key" name="pie_fields_csv[user_login]" value="Username"  />
                  <label for="field_user_login">
                    <?php _e("Username","piereg"); ?>
                  </label>
                </div>
                <div class="export_field">
                  <input id="field_first_name" type="checkbox" class="checkbox meta_key" name="pie_meta_csv[first_name]" value="First Name" />
                  <label for="field_first_name">
                    <?php _e("First Name","piereg"); ?>
                  </label>
                </div>
                <div class="export_field">
                  <input id="field_last_name" type="checkbox" class="checkbox meta_key" name="pie_meta_csv[last_name]" value="Last Name" />
                  <label for="field_last_name">
                    <?php _e("Last Name","piereg"); ?>
                  </label>
                </div>
                <div class="export_field">
                  <input id="field_nickname" type="checkbox" class="checkbox meta_key" name="pie_meta_csv[nickname]" value="Nickname" />
                  <label for="field_nickname">
                    <?php _e("Nickname","piereg"); ?>
                  </label>
                </div>
                <div class="export_field">
                  <input id="field_display_name" type="checkbox" class="checkbox meta_key" name="pie_fields_csv[display_name]" value="Display name" />
                  <label for="field_display_name">
                    <?php _e("Display name","piereg"); ?>
                  </label>
                </div>
                <div class="export_field">
                  <input id="field_user_email" type="checkbox" class="checkbox meta_key" name="pie_fields_csv[user_email]" value="E-mail" />
                  <label for="field_user_email">
                    <?php _e("E-mail","piereg"); ?>
                  </label>
                </div>
                <div class="export_field">
                  <input id="field_user_url" type="checkbox" class="checkbox meta_key" name="pie_fields_csv[user_url]" value="Website" />
                  <label for="field_user_url">
                    <?php _e("Website","piereg"); ?>
                  </label>
                </div>
                <div class="export_field">
                  <input id="field_description" type="checkbox" class="checkbox meta_key" name="pie_meta_csv[description]" value="Biographical Info" />
                  <label for="field_description">
                    <?php _e("Biographical Info","piereg"); ?>
                  </label>
                </div>
                <div class="export_field">
                  <input id="field_role" type="checkbox" class="checkbox meta_key" name="pie_meta_csv[wp_capabilities]" value="Role" />
                  <label for="field_role">
                    <?php _e("Role","piereg"); ?>
                  </label>
                </div>
                <div class="export_field">
                  <input id="field_user_registered" type="checkbox" class="checkbox meta_key" name="pie_fields_csv[user_registered]" value="User Registered" />
                  <label for="field_user_registered">
                    <?php _e("User Registered","piereg"); ?>
                  </label>
                </div>
              </div>
            </li>
            <li>
              <div class="fields date">
                <h2>
                  <?php _e("Select User Registration Date Range","piereg"); ?>
                </h2>
                <div class="start_date">
                  <label for="field_">
                    <?php _e("Start","piereg"); ?>
                  </label>
                  <input id="date_start" name="date_start" type="text" class="input_fields date_start" />
                  <img id="start_icon" src="<?php echo plugins_url('pie-register'); ?>/images/calendar_img.jpg" width="22" height="22" alt="calendar" class="calendar_img" /> </div>
                <div class="end_date">
                  <label for="field_">
                    <?php _e("End","piereg"); ?>
                  </label>
                  <input id="date_end" name="date_end" type="text" class="input_fields date_start" />
                  <img id="end_icon" src="<?php echo plugins_url('pie-register'); ?>/images/calendar.png" width="22" height="22" alt="calendar" class="calendar_img" /> </div>
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
                <h2>
                  <?php _e("Select File","piereg"); ?>
                </h2>
                <input style="margin-left:0px;" name="csvfile" type="file" class="input_fields" />
              </div>
            </li>
            <li>
              <div class="fields">
                <input type="checkbox" id="update_existing_users" value="yes" name="update_existing_users" />
                <label for="update_existing_users" style="margin-top:0px;" >
                  <?php _e("Update Existing Users","piereg"); ?>
                </label>
              </div>
            </li>
            <li>
              <div class="fields"> <span style="float:left"><?php echo sprintf( __( 'You may want to see', 'piereg').' <a target="_blank" href="%s"> '.__('the example of the CSV file', 'piereg').'</a>.' , plugin_dir_url(__FILE__).'examples/example.csv'); ?></span>
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
</div>