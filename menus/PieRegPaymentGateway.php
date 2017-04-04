<?php
$piereg = PieReg_Base::get_pr_global_options();

$paypal_live = $paypal_sandbox = false;
if($piereg['paypal_sandbox'] == "no"){
	$paypal_live 	= true;
} elseif ($piereg['paypal_sandbox'] == "yes" || !$piereg['paypal_sandbox']) {
	$paypal_sandbox = true;   
} 
				  
?>
<div class="pieregister-admin" >
<div id="payment_gateway_tabs" class="hideBorder" style="display:none;">
    <div class="settings">
        <h2 class="headingwidth"><?php _e("Payment Gateways",'piereg') ?></h2>
        <?php 
		if(isset($_POST['notice']) && $_POST['notice'] ){
			echo '<div id="message" class="updated fade msg_belowheading"><p><strong>' . $_POST['notice'] . '.</strong></p></div>';
		}else if( isset($_POST['error']) && !empty($_POST['error']) ){
			echo '<div id="error" class="error fade msg_belowheading"><p><strong>' . $_POST['error'] . '</strong></p></div>';
		}
		?>
        <div class="tabOverwrite">
            <div id="tabsSetting" class="tabsSetting">            
                <ul class="tabLayer1">
                    <li><a href="#piereg_general_settings_payment_gateway"><?php _e("General Settings","piereg") ?></a></li><!--Add General Settings Menu-->
                    <li><a href="#piereg_paypal_payment_gateway"><?php _e("PayPal Standard","piereg") ?></a></li><!--Add Paypal-->
                    <?php //pie_register_Authorize_Net_paymentgateways_menus
                        do_action('pie_register_payment_setting_menus'); //<!--for Authorize.Net-->
                    ?>
                    <li><a href="#piereg_payment_log"><?php _e("Payment Log","piereg") ?></a></li><!--Add Payment Log-->
                </ul>
            </div>
        </div>
    </div>
    <!-- start Paypal pament gateway -->
    <div id="piereg_paypal_payment_gateway">
        <div id="container">
          <div class="right_section">
            <div class="settings">
              <?php echo '<a href="http://www.paypal.com/payments-standard" target="_blank"><img class="logo-payment-align" src="'.$this->plugin_url."/images/paypal-standard-logo.png".'" /></a>'; ?>
              <div id="pie-register">
                <form method="post" action="#piereg_paypal_payment_gateway" enctype="multipart/form-data">
                <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg-update-options'); ?>
                <div class="fields">
                  <div class="radio_fields">
                  	<input type="checkbox" name="enable_paypal" id="enable_paypal" value="1" <?php echo ($piereg['enable_paypal']=="1")?'checked="checked"':''?> />
                  </div>
                  <label for="enable_paypal" class="label_mar_top"><?php _e("Enable PayPal Standard","piereg"); ?></label>
                </div>
                <div class="fields">
                  <label for="paypal_butt_id"><?php _e('PayPal Hosted Button ID', 'piereg');?></label>
                  <input type="text" name="piereg_paypal_butt_id" class="input_fields" id="paypal_butt_id" value="<?php echo $piereg['paypal_butt_id'];?>" />
                </div>
                <div class="fields">
                  <label for="paypal_sandbox">
                    <?php _e('Paypal Mode', 'piereg');?>
                  </label>
                  <select name="piereg_paypal_sandbox" id="paypal_sandbox">
                    <option <?php echo ($paypal_live) ? 'selected="selected"' : "";?> value="no"><?php _e('Live', 'piereg');?></option>
                    <option <?php echo ($paypal_sandbox) ? 'selected="selected"' : "";?> value="yes"><?php _e('Sandbox', 'piereg');?></option>
                  </select>
                </div>
                <div class="fields fields_submitbtn">
                	  <input name="action" value="pie_reg_update" type="hidden" />
                	<input type="hidden" name="payment_gateway_page" value="1" />
                    <input name="Submit" class="submit_btn" value="<?php _e('Save Changes','piereg');?>" type="submit" />
                  </div>
                <h3><?php _e("Instructions","piereg"); ?></h3>
                <div style="width:1px;height:20px;"></div>
                <div class="fields">
                <p><strong>
                  <?php _e('Please click the link below to follow the instructions.', 'piereg');?>
                  </strong></p>
                <p><a href="http://pieregister.com/paypal-authorize-net-2checkout-steps/#paypal-standard" target="_blank">http://pieregister.com/paypal-authorize-net-2checkout-steps/</a></p>
              </div>
              </form>
            </div>
          </div>
        </div>
        </div>
    </div>
    <!--End Paypal-->
    
    <!-- start pament gateway General Settings page-->
    <div id="piereg_general_settings_payment_gateway" style="display:inline-block;min-width:95%;">
        <div id="container">
            <div class="right_section">
                <div class="settings">
                    <div id="pie-register">
                       <form method="post" action="#piereg_general_settings_payment_gateway">
                            <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg-update-options'); ?>
                            
                            <h3><?php _e("Messages",'piereg'); ?></h3>
                            <!--	Payment success Message	-->
                            <div class="fields">
                                <label for="payment_success_msg"><?php _e('Payment Success', 'piereg');?></label>
                                <input type="text" class="input_fields" name="payment_success_msg" id="payment_success_msg" value="<?php echo ((isset($piereg['payment_success_msg']) && !empty($piereg['payment_success_msg']))?$piereg['payment_success_msg']:__("Payment was successful.","piereg"));?>" />
                            </div>
                            <!--	Payment Failed Message	-->
                            <div class="fields">
                                <label for="payment_faild_msg"><?php _e('Payment Failed', 'piereg');?></label>
                                <input type="text" class="input_fields" name="payment_faild_msg" id="payment_faild_msg" value="<?php echo ((isset($piereg['payment_faild_msg']) && !empty($piereg['payment_faild_msg']))?$piereg['payment_faild_msg']:__("Payment failed.","piereg"));?>" />
                            </div>
                            <!--	Renew Account Message	-->
                            <div class="fields">
                                <label for="payment_renew_msg"><?php _e('Reactivate Account', 'piereg');?></label>
                                <input type="text" class="input_fields" name="payment_renew_msg" id="payment_renew_msg" value="<?php echo ((isset($piereg['payment_renew_msg']) && !empty($piereg['payment_renew_msg']))?$piereg['payment_renew_msg']:__("Account needs to be activated.","piereg"));?>" />
                            </div>
                            <!--	Alreact Activate Message	-->
                            <div class="fields">
                                <label for="payment_already_activate_msg"><?php _e('Already Active', 'piereg');?></label>
                                <input type="text" class="input_fields" name="payment_already_activate_msg" id="payment_already_activate_msg" value="<?php echo ((isset($piereg['payment_already_activate_msg']) && !empty($piereg['payment_already_activate_msg']))?$piereg['payment_already_activate_msg']:__("Account is already active.","piereg"));?>" />
                            </div>
                            
			                <input name="action" value="pie_reg_update" type="hidden" />
                            <input type="hidden" name="payment_gateway_general_settings" value="1" />
                            <!-- style="background:#0C6;"-->
                            <div class="fields fields_submitbtn">
                                <input name="Submit" class="submit_btn" value="<?php _e('Save Changes','piereg');?>" type="submit" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--End General Settings-->
	
	<?php
		do_action("pie_register_Authorize_Net_paymentgateways");//Depricate
		do_action("pie_register_PaymentGateways");//Get Payment Gateways Page
    ?>

	 <!-- start pament log page-->
    <div id="piereg_payment_log" style="display:inline-block;min-width:95%;">
    	<div id="pie-register-payment-log">
            <div class="settings" style="margin: 0px;width: 99%;">
               <div class="piereg-payment-log-area">
                	<table class="wp-list-table widefat fixed tableexamples piereg-payment-log-table">
                    	<thead>
                            <tr>
                                <th><?php _e("User Email","piereg"); ?></th>
                                <th><?php _e("Method","piereg"); ?></th>
                                <th><?php _e("Type","piereg"); ?></th>
                                <th><?php _e("Date","piereg"); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
						$data = get_option("piereg_payment_log_option");
						$x = 0;
						if(!empty($data) && is_array($data)){
							
							usort($data, function( $a, $b ) {
								return strtotime($b["date"]) - strtotime($a["date"]);
							});
							
							foreach( $data as $k_data=>$v_data){?>
								<tr <?php echo ( ($x % 2)?'class="alternate"':'' ); ?> data-piereg-id="piereg-id-<?php echo md5( $v_data['date']." | " . $v_data['email'] ); ?>" >
									<td><?php echo $v_data['email']; ?></td>
									<td><?php echo $v_data['method']; ?></td>
									<td><?php echo $v_data['type']; ?></td>
									<td><?php echo $v_data['date']; ?></td>
								</tr>
								<tr style="display:none;" class="piereg-payment-log-desc piereg-id-<?php echo md5( $v_data['date']." | " . $v_data['email'] ); ?>" >
									<td colspan="4"><pre><?php print_r( $v_data['responce'] ); ?></pre></td>
								</tr>
							<?php 
							$x++;
							}
						}else{?>
							<tr class="piereg-payment-log-desc" >
                                <td colspan="4" align="center" ><?php _e("No Record Found","piereg"); ?></td>
                            </tr>
						<?php } ?>
                        </tbody>
                        <tfoot>
                        	<tr>
                                <th><?php _e("User Email","piereg"); ?></th>
                                <th><?php _e("Method","piereg"); ?></th>
                                <th><?php _e("Type","piereg"); ?></th>
                                <th><?php _e("Date","piereg"); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="fields" style="width:99.9%;">
                	
                    <form action="#piereg_payment_log" method="post" onsubmit="return confirm('<?php _e("Are you sure you want to clear the payment log?","piereg"); ?>');">
                    	<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_payment_log','piereg_payment_log'); ?>
	                    <input name="piereg_delete_payment_log_file" style="margin:0;" class="submit_btn" value="<?php _e('Clear All','piereg');?>" type="submit" />
                    </form>
                    
                    <form action="#piereg_payment_log" method="post">
                    	<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_payment_log','piereg_payment_log'); ?>
	                    <input name="piereg_download_payment_log_file" style="margin:0;margin-right:10px;" class="submit_btn" value="<?php _e('Download','piereg');?>" type="submit" />
                    </form>
                    
                </div>
             </div>
        </div>
    </div>
    <!--End General Settings-->

</div>
</div>