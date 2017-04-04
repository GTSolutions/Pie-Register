<?php $piereg = PieReg_Base::get_pr_global_options();
if( !isset($_GET['act']) || !isset($_GET['pie_id']) || !isset($_GET['option']) )
{
	?>
    <form name="frm_settings_security_advanced" action="" method="post">
    <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_settings_security_advanced','piereg_settings_security_advanced'); ?>
        <h2 class="hydin_without_bg"><?php _e("Registration Form",'piereg') ?></h2>
        <div class="fields">
        	<div class="piereg_box_style_1">
             <input type="checkbox" name="reg_form_submission_time_enable" id="reg_form_submission_time_enable" value="1" <?php echo ($piereg['reg_form_submission_time_enable']=="1")?'checked="checked"':''?> /> 
             <?php _e("Time form submission, reject form if submitted within ",'piereg') ?>
             <input type="text" name="reg_form_submission_time" 
             		style="width:auto;"
                    id="reg_form_submission_time" 
                    value="<?php echo ( (isset($piereg['reg_form_submission_time']) && !empty($piereg['reg_form_submission_time'])) ? intval($piereg['reg_form_submission_time']) : 0 ); ?>" 
                    class="input_fields submissionfield" 
                    />
                    <?php _e("seconds or less.",'piereg') ?>
            <span class="quotation" style="margin-left:0px;"><?php _e("Security enhancement for forms (timed submission)",'piereg') ?></span> 
            </div>
        </div>
        <div class="fields">
            <input name="action" value="pie_reg_settings" type="hidden" />
            <input type="submit" class="submit_btn flt_none" value="<?php _e("Save Settings","piereg"); ?>" />
        </div>
    </form>
<hr class="seperator" />    
<?php 
}
	?>
<h2 class="hydin_without_bg"><?php _e("Restrict Widgets",'piereg') ?></h2>
<div class="piereg_clear"></div>
<?php $this->require_once_file($this->plugin_dir.'/restrict_widget/pie_register_widget_class.ini.php'); ?>