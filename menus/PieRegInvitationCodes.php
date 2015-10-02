<?php
global $piereg_dir_path;
include_once( PIEREG_DIR_NAME."/classes/invitation_code_pagination.php");

if(isset($_POST['notice']) && $_POST['notice'] ){
	echo '<div id="message" class="updated fade"><p><strong>' . $_POST['notice'] . '.</strong></p></div>';
}elseif(isset($_POST['error']) && $_POST['error'] ){
	echo '<div id="error" class="error fade"><p><strong>' . $_POST['error'] . '.</strong></p></div>';
}
?>
<script type="text/javascript">
function confirmDel(id,code)
{
	var conf = window.confirm("Are you sure to delete this ("+code+") code?");
	if(conf)
	{
		document.getElementById("invi_del_id").value = id;
		document.getElementById("del_form").submit();
	}
}
function changeStatus(id,code,status)
{
	var conf = window.confirm("Are you sure to change status of this ("+code+") code?");
	if(conf)
	{
		document.getElementById("status_id").value = id;
		document.getElementById("status_form").submit();
	}
}
var piereg = jQuery.noConflict();

piereg(document).ready(function(){
	piereg( document ).tooltip({
		track: true
	});
});
</script>
<form method="post" action="" id="del_form">
	<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_invitation_nonce','piereg_invitation_nonce'); ?>
  <input type="hidden" id="invi_del_id" name="invi_del_id" value="0" />
</form>
<form method="post" action="" id="status_form">
<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_invitation_nonce','piereg_invitation_nonce'); ?>
  <input type="hidden" id="status_id" name="status_id" value="0" />
</form>
<div id="container" class="pieregister-admin">
  <div class="right_section">
    <div class="invitation">
      <h2><?php  _e("Invitation Codes",'piereg'); ?></h2>
      <form method="post" action="">
	      <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg_wp_invitation_nonce','piereg_invitation_nonce'); ?>
        <ul>
          <li>
            <div class="fields">
              <h2><?php  _e("Guideline",'piereg'); ?></h2>
              <p><?php  _e("Protect your privacy. If you want your blog to be exclusive, enable Invitation Codes and keep track of your users.",'piereg'); ?></p>
            </div>
          </li>
          <li>
            <div class="fields">
              <label><?php _e("Enable Invitation Codes","piereg");?></label>
              <div class="radio_fields">
                <input id="enable_invitation_codes_yes" type="radio" value="1" name="enable_invitation_codes" <?php echo ($piereg['enable_invitation_codes']=="1")?'checked="checked"':''?> />
                <label for="enable_invitation_codes_yes"><?php _e("Yes","piereg");?></label>
                <input id="enable_invitation_codes_no" type="radio" value="0" name="enable_invitation_codes" <?php echo ($piereg['enable_invitation_codes']=="0")?'checked="checked"':''?> />
                <label for="enable_invitation_codes_no"><?php _e("No","piereg");?></label>
              </div>
              <span class="quotation"><?php _e("Set this to Yes if you want users to register only by your defined invitaion codes. You will have to add invitation code field in the form editor","piereg");?>.</span> </div>
          </li>
          <li>
            <p class="submit">
              <input name="Submit" style="background: #464646;color: #ffffff;border: 0;cursor: pointer;padding: 5px 0px 5px 0px;margin-top: 15px;
min-width: 113px;float:right;" value="<?php _e('Save Settings','piereg');?>" type="submit" />
            </p>
          </li>
          <li>
            <div class="fields">
              <h3><?php _e("Insert Code","piereg");?></h3>
              <textarea id="piereg_codepass" name="piereg_codepass"></textarea>
              <span class="note"><strong><?php _e("Note","piereg");?>:</strong> <?php _e("Each Code will be on a Separate Line","piereg");?>.</span> </div>
          </li>
          <li>
            <div class="fields">
              <h3><?php _e("Usage","piereg");?></h3>
              <input style="float:left;" value=""  type="text" name="invitation_code_usage" class="input_fields2" />
               <span style="float:left;clear:both;" class="note"><?php _e("Number of time a particular code can be used for registration","piereg");?>.</span> 
            </div>
          </li>
          <li>
            <p class="submit">
              <input name="Submit" style="background: #464646;color: #ffffff;border: 0;cursor: pointer;padding: 5px 0px 5px 0px;margin-top: 15px;
min-width: 113px;float:right;" value="<?php _e('Add Code','piereg');?>" type="submit" />
            </p>
          </li>
        </ul>
      </form>
<style type="text/css">
.widefat th, .widefat th a{ color:#fefefe !important;font-weight:normal !important; text-shadow:none !important;}
.widefat th{background:#64727C !important;}
.widefat th:nth-child(1){width:35px;}
.name.column-name > input[type=text], .usage.column-usage > input[type=text]{width:100%;}
.name.column-name > span, .code_usage.column-code_usage > span {cursor: pointer;}
</style>

<script type="text/javascript" language="javascript">
function get_selected_box_ids()
{
	var checked_id = "";
	piereg(".invitaion_fields_class").each(function(i,obj){
		if( (piereg( obj ).prop('checked')) == true )
		{
			checked_id = checked_id + piereg( obj ).attr("value") + ",";
		}
		
	});
	if(checked_id.trim() != "" && piereg("#invitaion_code_bulk_option").val().trim() != "" && piereg("#invitaion_code_bulk_option").val() != "0")
	{
		if(confirm("Are you sure to "+piereg("#invitaion_code_bulk_option").val().replace("unactive","deactivate")+" selected invitation code(s).?") == true)
		{
			checked_id = checked_id.slice(0,-1);
			piereg("#select_invitaion_code_bulk_option").val(checked_id);
			return true;
		}
		else{return false;}
	}
	else{
		piereg("#invitaion_code_error").css("display","block");
		return false;
	}
}
function select_all_invitaion_checkbox()
{
	var status = document.getElementById("select_all_invitaion_checkbox").value;
	if(status.trim() == "true" ){
		piereg(".select_all_invitaion_checkbox").val("false");
		piereg(".invitaion_fields_class").attr("checked",false);
		piereg(".select_all_invitaion_checkbox").attr("checked",false);
	}
	else{
		piereg(".select_all_invitaion_checkbox").val("true");
		piereg(".invitaion_fields_class").attr("checked",true);
		piereg(".select_all_invitaion_checkbox").attr("checked",true);
	}
}
function show_field(crnt,val)
{
	piereg("#"+crnt.id).css("display","none");
	piereg("#"+val).css("display","block");
	piereg("#"+val).focus();
}
function hide_field(crnt,val)
{
	piereg("#"+crnt.id).css("display","none");
	piereg("#"+val).css("display","block");
	current = piereg("#"+crnt.id).val();
	value = piereg("#"+val).html();
	piereg("#"+val).html("Please Wait...");
	id = piereg("#"+crnt.id).attr("data-id-invitationcode");
	type = piereg("#"+crnt.id).attr("data-type-invitationcode");
	
	var inv_code_data = {
		method : "post",
		action: 'pireg_update_invitation_code',
		data: ({"value":piereg("#"+crnt.id).val(),"id":id,"type":type})
	};
	piereg.post(ajaxurl, inv_code_data, function(response) {
		if(response.trim() == "done")
		{
			piereg("#"+val).html(piereg("#"+crnt.id).val());
		}
		else if(response.trim() == "duplicate")
		{
			if(current != value)
			{
				alert("This code ("+current+") already exist");
			}
			piereg("#"+val).html(value);
			piereg("#"+crnt.id).val(value);
		}
		else/* if(response.trim() == "error")*/
		{
			piereg("#"+val).html(value);
			piereg("#"+crnt.id).val(value);
		}
	});
}

piereg(document).ready(function(){
	piereg("#invitation_code_per_page_items").change(function(){
		piereg("#form_invitation_code_per_page_items").submit();
	});
});
</script>

<div style="clear:both;float:left;border-right:#ccc 1px solid;padding-right:5px;margin-right:5px;">
    <form method="post" id="form_invitation_code_per_page_items">
    	<?php _e("Per-Page Item","piereg"); ?>
        <select name="invitation_code_per_page_items" id="invitation_code_per_page_items" title="<?php _e("Select Per-Page Invitaion code","piereg"); ?>">
        	<?php
			$opt = get_option("pie_register_2");
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
            <option selected="selected" value="0"><?php _e("Bulk Actions","piereg"); ?></option>
            <option value="delete"><?php _e("Delete","piereg"); ?></option>
            <option value="active"><?php _e("Activate","piereg"); ?></option>
            <option value="unactive"><?php _e("Deactivate","piereg"); ?></option>
        </select>
        <input type="submit" value="<?php _e("Apply","piereg"); ?>" class="button action" id="doaction" name="btn_submit_invitaion_code_bulk_option">
    </form>
    <span style="color:#F00;display:none;" id="invitaion_code_error"><?php _e("Select Bulk Option and also Invitation Code","piereg");?></span>
</div>
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
