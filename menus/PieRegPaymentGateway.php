<div class="pieregister-admin">
<div id="payment_gateway_tabs">
<ul>
    <li><a href="#piereg_paypal_payment_gateway"><?php _e("Paypal (one time subscription)","piereg") ?></a></li><!--for Paypal-->
<?php //pie_register_Authorize_Net_paymentgateways_menus
	do_action('pie_register_payment_setting_menus'); //<!--for Authorize.Net-->
?>
</ul>

	<?php
    $piereg = get_option( 'pie_register_2' );
    $piereg_custom = get_option( 'pie_register_custom' );
    if(isset($_POST['notice']) && $_POST['notice'] ){
        echo '<div id="message" class="updated fade"><p><strong>' . $_POST['notice'] . '.</strong></p></div>';
    }
    elseif(isset($_POST['error']) && $_POST['error'] ){
        echo '<div id="error" class="error fade"><p><strong>' . $_POST['error'] . '.</strong></p></div>';
    }
    ?>
    <!-- start Paypal pament gateway -->
    <div id="piereg_paypal_payment_gateway">
        <div id="container">
          <div class="right_section">
            <div class="settings">
              <h2>
                <?php _e('Payment Gateway Settings', 'piereg');?>
              </h2>
              
				<!-- Add Start -->
				<?php
                //$PieReg_Adds = new PieReg_Adds();
                //$PieReg_Adds->get_add("payment_gateways");
                ?>
                <!-- Add End -->
              
              <div id="pie-register">
                <form method="post" action="#paypal_payment_gateway" enctype="multipart/form-data">
                <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_paypal_settings_nonce','piereg_paypal_settings_nonce'); ?>
                <h3><?php _e("Paypal Information","piereg"); ?></h3>
                <div class="fields">
                  <label for="enable_paypal_yes"><?php _e("Enable Paypal","piereg"); ?></label>
                  <div class="radio_fields" style="margin-left:85px;">
                    <input id="enable_paypal_yes" type="radio" value="1" name="enable_paypal" <?php echo ($piereg['enable_paypal']=="1")?'checked="checked"':''?> />
                    <label for="enable_paypal_yes"><?php _e("Yes","piereg"); ?></label>
                    <input id="enable_paypal_no" type="radio" value="0" name="enable_paypal" <?php echo ($piereg['enable_paypal']=="0")?'checked="checked"':''?> />
                    <label for="enable_paypal_no"><?php _e("No","piereg"); ?></label>
                  </div>
                </div>
                <div class="fields">
                  <label for="paypal_butt_id">
                    <?php _e('Enter Your Paypal hosted button ID.', 'piereg');?>
                  </label>
                  <input type="text" name="piereg_paypal_butt_id" class="input_fields" id="paypal_butt_id" style=" margin-left:20px;" value="<?php echo $piereg['paypal_butt_id'];?>" />
                </div>
                <?php /*?><div class="label"><?php _e('Paypal PDT Token', 'piereg');?></div>
        
            <div class="input"><input type="text" name="piereg_paypal_pdt" id="paypal_pdt" style="width:300px;" value="<?php echo $piereg['paypal_pdt'];?>" /></div><?php */?>
                <div class="fields">
                  <label style="min-width:256px;" for="paypal_sandbox">
                    <?php _e('Paypal Mode', 'piereg');?>
                  </label>
                  <select name="piereg_paypal_sandbox" id="paypal_sandbox">
                    <option value="no" <?php if($piereg['paypal_sandbox'] == "no") echo 'selected="selected"';?>><?php _e("Live","piereg"); ?></option>
                    <option value="yes" <?php if($piereg['paypal_sandbox'] == "yes") echo 'selected="selected"';?>><?php _e("Sandbox","piereg"); ?></option>
                  </select>
                  <div class="fields">
                    <input name="Submit" style="margin:0;" class="submit_btn" value="<?php _e('Save Changes','piereg');?>" type="submit" />
                  </div>
                </div>
                <h3><?php _e("Steps","piereg"); ?></h3>
                <div style="width:1px;height:20px;"></div>
                <div class="fields">
                <p><strong>
                  <?php _e('Please follow the steps below to create and set the required Options.', 'piereg');?>
                  </strong></p>
                <ol>
                <li><?php _e("Login to your","piereg"); ?> <a href="https://www.paypal.com/"><?php _e("Paypal account","piereg"); ?></a>.</li>
                <li><?php _e("Go to Merchant Services and Click on","piereg"); ?> <a href="https://www.paypal.com/ae/cgi-bin/webscr?cmd=_web-tools"><?php _e("Buy Now","piereg"); ?></a> <?php _e("button","piereg"); ?>.</li>
                <li><?php _e("Give Your Button a Name. i.e: Website Access fee and Set Price.","piereg"); ?></li>
                <li><?php _e('Click on Step3: Customize advance features (optional) Tab, select "Add advanced variables" checkbox and add the following snippet',"piereg"); ?>:

<textarea readonly="readonly" onfocus="this.select();" onclick="this.select();" onkeypress="this.select();" style="height:100px;min-height:auto;" >rm=2<?php echo "\r\n"; ?>
notify_url=<?php echo ''.trailingslashit(get_bloginfo("url")).'';?>?action=ipn_success<?php echo "\r\n"; ?>
cancel_return=<?php echo ''.trailingslashit(get_bloginfo("url")).'';?>?action=payment_cancel<?php echo "\r\n"; ?>
return=<?php echo ''.trailingslashit(get_bloginfo("url")).'' ;?>?action=payment_success</textarea>

                  
                </li>
                <li><?php _e("Click Create button, On the next page, you will see the generated button code snippet like the following","piereg"); ?>:
                    <xmp style="cursor:text;width:100%;white-space:pre-line; margin:0;">
                        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                          <input type="hidden" name="cmd" value="_s-xclick">
                          <input type="hidden" name="hosted_button_id" value="XXXXXXXXXX">
                          <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                          <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                        </form>
                    </xmp>
                </li>
                <li><?php _e("Copy the snippet into any text editor, extract and put the hosted_button_id value (XXXXXXXXXX) into the Above Field.","piereg"); ?></li>
                <li><?php _e("Save Changes, You're done!","piereg"); ?></li>
                </ol>
                <input name="action" value="pie_reg_update" type="hidden" />
                <input type="hidden" name="payment_gateway_page" value="1" />
                <div class="fields">
                  <input name="Submit" class="submit_btn" value="<?php _e('Save Changes','piereg');?>" type="submit" />
                </div>
              </div>
              </form>
            </div>
          </div>
        </div>
        </div>
    </div>
    <!--End Paypal-->
	
	<?php
		do_action("pie_register_Authorize_Net_paymentgateways");//Get Authorize.Net Page
    ?>

</div>
</div>