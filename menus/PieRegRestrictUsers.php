<?php $piereg = PieReg_Base::get_pr_global_options(); ?>

<div class="pieregister-admin">
  <div class="settings">
    <h2 class="top_heading">
      <?php _e("Block Users",'piereg');?>
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
    <div id="blacklisted_tabs" class="hideBorder" style="display:none;">
      <div class="tabOverwrite">
        <div id="tabsSetting" class="tabsSetting">
          <ul class="tabLayer1">
            <li><a href="#piereg_block_by_username">
              <?php _e("Block Users by Username","piereg") ?>
              </a></li>
            <li><a href="#piereg_block_by_ip">
              <?php _e("Block Users by IP Address","piereg") ?>
              </a></li>
            <li><a href="#piereg_block_by_email">
              <?php _e("Block Users by Email Address","piereg") ?>
              </a></li>
          </ul>
        </div>
      </div>
      <div id="piereg_block_by_username">
        <div class="right_section">
          <div class="">
            <div class="pie-register-blocked-users">
              <form method="post" action="#piereg_block_by_username">
                <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_restrict_users', 'piereg_restrict_users'); ?>
                <div class="fields">
                  <div class="radio_fields">
                    <input id="enable_blockedusername" type="checkbox" name="enable_blockedusername" value="1" <?php echo (isset($piereg['enable_blockedusername']) && $piereg['enable_blockedusername']=="1")?'checked="checked"':''?>>
                  </div>
                  <label for="enable_blockedusername">
                    <?php _e("Do not allow users listed below to login to my site.","piereg"); ?>
                  </label>
                </div>
                <div class="fields">
                  <h3>
                    <?php _e("Usernames:","piereg");?>
                  </h3>
                  <textarea id="piereg_blk_username" name="piereg_blk_username"><?php echo isset($piereg['piereg_blk_username']) ? $piereg['piereg_blk_username'] : ""; ?></textarea>
                  <div class="note_parent">
                    <div class="note"> <strong>
                      <?php _e("Note","piereg");?>
                      :</strong>
                      <?php _e("For every single username
use new line","piereg");?>
                      . <span class="align_right"><strong>
                      <?php _e("Example","piereg");?>
                      :</strong> johnny<br />
                      cruz<br />
                      downey*<br />
                      <?php _e("Give (*) to block user containing that username","piereg");?>
                      </span> </div>
                  </div>
                </div>
                <div class="fields fields_submitbtn">
                  <input name="action" value="pie_reg_update" type="hidden" />
                  <input type="hidden" name="restrict_user_by_username" value="1" />
                  <input name="Submit" class="submit_btn" value="<?php _e('Save Changes','piereg');?>" type="submit" />
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <div id="piereg_block_by_ip">
        <div class="right_section">
          <div class="">
            <div class="pie-register-blocked-users">
              <form method="post" action="#piereg_block_by_ip">
                <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_restrict_users', 'piereg_restrict_users'); ?>
                <div class="fields">
                  <div class="radio_fields">
                    <input id="enable_blockedips" type="checkbox" name="enable_blockedips" value="1" <?php echo (isset($piereg['enable_blockedips']) && $piereg['enable_blockedips']=="1")?'checked="checked"':''?>>
                  </div>
                  <label for="enable_blockedips">
                    <?php _e("Do not allow access to my site from IP addresses and ranges listed below.","piereg"); ?>
                  </label>
                </div>
                <div class="fields">
                  <h3>
                    <?php _e("IP Addresses","piereg");?>
                  </h3>
                  <textarea id="piereg_blk_ip" name="piereg_blk_ip"><?php echo isset($piereg['piereg_blk_ip']) ? $piereg['piereg_blk_ip'] : ""; ?></textarea>
                  <div class="note_parent">
                    <div class="note"> <strong>
                      <?php _e("Note","piereg");?>
                      :</strong>
                      <?php _e("Enter each IP address on a new line","piereg");?>
                      . <span class="align_right"><strong>
                      <?php _e("Example","piereg");?>
                      :</strong> <br />
                      192.168.1.1<br/>
                      192.168.2.0/24</span> </div>
                  </div>
                </div>
                <div class="fields fields_submitbtn">
                  <input name="action" value="pie_reg_update" type="hidden" />
                  <input type="hidden" name="restrict_user_by_ip" value="1" />
                  <input name="Submit" class="submit_btn" value="<?php _e('Save Changes','piereg');?>" type="submit" />
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <div id="piereg_block_by_email">
        <div class="right_section">
          <div class="">
            <div class="pie-register-blocked-users">
              <form method="post" action="#piereg_block_by_email">
                <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_restrict_users', 'piereg_restrict_users'); ?>
                <div class="fields">
                  <div class="radio_fields">
                    <input id="enable_blockedemail" type="checkbox" name="enable_blockedemail" value="1" <?php echo (isset($piereg['enable_blockedemail']) && $piereg['enable_blockedemail']=="1")?'checked="checked"':''?>>
                  </div>
                  <label for="enable_blockedemail">
                    <?php _e("Do not allow access to my site for users using email address listed below .","piereg"); ?>
                  </label>
                </div>
                <div class="fields">
                  <h3>
                    <?php _e("Email Addresses:","piereg");?>
                  </h3>
                  <textarea id="piereg_blk_email" name="piereg_blk_email"><?php echo isset($piereg['piereg_blk_email']) ? $piereg['piereg_blk_email'] : ""; ?></textarea>
                  <div class="note_parent">
                    <div class="note"> <strong>
                      <?php _e("Note","piereg");?>
                      :</strong>
                      <?php _e("For every single email address
use new line","piereg");?>
                      . <span class="align_right"><strong>
                      <?php _e("Example","piereg");?>
                      :</strong> some@example.com<br />
                      @domain.com*<br />
                      <?php _e("Give (*) to block user containing that domain","piereg");?>
                      </span> </div>
                  </div>                  
                </div>
                <div class="fields fields_submitbtn">
                  <input name="action" value="pie_reg_update" type="hidden" />
                  <input type="hidden" name="restrict_user_by_email" value="1" />
                  <input name="Submit" class="submit_btn" value="<?php _e('Save Changes','piereg');?>" type="submit" />
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
