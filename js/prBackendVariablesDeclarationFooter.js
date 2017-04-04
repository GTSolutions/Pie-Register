jQuery(document).ready(function(){
	jQuery( document ).tooltip({
		track: true
	});
	jQuery(".tabsSetting .tabLayer1 > li" ).hover(
		  function() {
				jQuery('ul.tabLayer2').each(function(){
					jQuery(this).css('display' , 'none');
				})
				jQuery(this).children('ul.tabLayer2').css('display' , 'block');
		  }, function() {
				jQuery('ul.tabLayer2').each(function(){
					jQuery(this).css('display' , '');
				});
		  }
	);
	jQuery(".piereg_restriction_type").on("click",function(){
		if( jQuery(".piereg_input #redirect").is(":checked") ){
			jQuery(".pieregister_block_content_area").hide();
			jQuery("#pieregister_restriction_url_area").show();
		}else if( jQuery(".piereg_input #block_content").is(":checked") ){
			jQuery("#pieregister_restriction_url_area").hide();
			jQuery(".pieregister_block_content_area").show();
		}
	});

	jQuery("form#post").submit(function(){
		if(jQuery("#piereg_post_visibility").val().trim() != "default")
		{
			<!--//	Set Defaule Color	-->
			jQuery(".pieregister_restriction_type_area").removeAttr("style");
			
			var piereg_validate_fields = false;
			<!--//	Validate Restriction Type	-->
			var restriction_type = false;
			var restriction_value = 0;
			jQuery(".piereg_restriction_type").each(function(i,obj){
				var check_local = jQuery(obj).is(":checked");
				if(check_local){
					restriction_type = check_local;
					restriction_value = jQuery(obj).val();
				}
			});
			if(!restriction_type)
			{
				jQuery(".pieregister_restriction_type_area").css({"color":"rgb(250, 0, 0)"});
				piereg_validate_fields = true;
			}
			
			<!--//	Validate Redirect	-->
			var piereg_redirect_url = "";
			var piereg_redirect_page = "";
			if(restriction_value == 0){
				piereg_redirect_url = jQuery("#piereg_redirect_url").val();
				piereg_redirect_page = jQuery("#piereg_redirect_page").val();
			}
			if(piereg_redirect_url != "" || piereg_redirect_page != -1){
				
			}else{
				jQuery(".pieregister_restriction_url_area").css({"color":"rgb(250, 0, 0)"});
				piereg_validate_fields = true;
			}
			
			<!--//	Validate Block Content	-->
			if(jQuery("#piereg_block_content").val().trim() == "" && jQuery(".piereg_input #block_content").is(":checked"))
			{
				jQuery(".pieregister_block_content_area").css({"color":"rgb(250, 0, 0)"});
				piereg_validate_fields = true;
			}
			
			<!--//	Show Validation Message	-->
			if(piereg_validate_fields){
				
				var piereg_container = jQuery('html,body'),piereg_scrollTo = jQuery('.pie_register-admin-meta');
				
				piereg_container.animate({
					scrollTop: piereg_scrollTo.offset().top - piereg_container.offset().top + piereg_container.scrollTop()
				});
				alert(pie_pr_backend_dec_vars.inValidFields);
				return false;
			}
		}
		
	});
	var $toplevel_page_pie_register, $toplevel_page_pie_register;
	$toplevel_page_pie_register = document.getElementById('toplevel_page_pie-register');
	$toplevel_page_pie_register = document.getElementsByClassName('toplevel_page_pie-register');
	if($toplevel_page_pie_register != null && $toplevel_page_pie_register.length != 0 && pie_pr_backend_dec_vars.isPRFormEditor){
		jQuery("#toplevel_page_pie-register, .toplevel_page_pie-register").removeClass("wp-not-current-submenu").addClass("wp-has-current-submenu wp-menu-open");
		jQuery("#toplevel_page_pie-register li.wp-first-item, #toplevel_page_pie-register a.wp-first-item").addClass("current");
	}
});
function addForm(){
	var form_id = jQuery("#pie_forms").val();
	if(form_id == null || form_id == ""){
		alert(pie_pr_backend_dec_vars.plsSelectForm);
		return;
	}
	window.send_to_editor(form_id);
}
// RegFormEdit
var hintNum = 0;
jQuery(document).ready(function(e) {
   
    var displayhints = pie_pr_backend_dec_vars.display_hints;
	
	if(displayhints=="1")
	{
		if(sessionStorage.getItem("hint") != "abc")
		{
			jQuery("#hint_"+hintNum).delay(500).fadeIn();	
		}
		jQuery(".thanks").click(function() 
		{
			jQuery(this).parents(".fields_hint").delay(100).fadeOut();			
			hintNum++;
			jQuery("#hint_"+hintNum).delay(500).fadeIn();
			sessionStorage.setItem("hint","abc");			
		});	
	}
	else
	{
		jQuery(".fields_hint").remove();
	}
});
var defaultMeta = Array();
defaultMeta = pie_pr_backend_dec_vars.defaultMeta;
//////////////////
var append_form_conditionals_fields_start = pie_pr_backend_dec_vars.appFormCondFldsStart;
var append_form_conditionals_fields_end = pie_pr_backend_dec_vars.appFormCondFldsEnd;
		
jQuery(document).ready(function(){
	
	/*Append Fields*/
	jQuery(".add_conditional_value_fields").on("click",function(){
		var cl_fields = "";
		var fields_name_and_value_dropdown = "";
		fields_name_and_value_dropdown = get_fields_name_and_value_dropdown(); // if conditional logics enable yes and add more conditions in form settings. 
		cl_fields = append_form_conditionals_fields_start + fields_name_and_value_dropdown + append_form_conditionals_fields_end;
		jQuery(".pie_wrap #form_conditional_area").append(cl_fields);
	});
	
	/*Delete Fields*/ 
	jQuery(document).on("click", ".delete_conditional_value_fields", function(){
		jQuery(this).closest(".advance_fields").remove();
	});
});

function get_fields_name_and_value_dropdown(){
	var $option = "";
	jQuery(".fields_position").each(function(i,obj){
		$fieldName = "";
		$fieldValue = "";
		if(jQuery(obj).children().attr("data-field-post-name"))
			$fieldName = jQuery(obj).children().attr("data-field-post-name");
		
		if(jQuery(obj).prev(".label_position").children("label").html()){
			$fieldValue = jQuery(obj).prev(".label_position").children("label").html();
		}else{
			$id_arr = jQuery(obj).attr("id").split("_");
			$fieldValue = jQuery("#field_label_"+$id_arr[2]).children("label").html();
		}
		if($fieldName != "" && $fieldValue != ""){
			$selected_opt = "";
			if(jQuery("#form_selected_field_value").val() != "" && jQuery("#form_selected_field_value").val() == $fieldName)
			{
				$selected_opt = 'selected="selected"';
			}
			$option = $option + '<option value="'+$fieldName+'" '+ $selected_opt +'>'+$fieldValue+'</option>';
		}
	}); 
	
	
	return $option;
}
var $fillvalNum = 0;
$fillvalNum = pie_pr_backend_dec_vars.fillvalNum;
if($fillvalNum > 0){
	for(i=0;i<$fillvalNum;i++){
		fillValues(pie_pr_backend_dec_vars.fillvalValue[i],pie_pr_backend_dec_vars.fillvalKey[i]);
	}
	no = pie_pr_backend_dec_vars.fillvalNo;
}
//////////////////////////////////////////////
jQuery(document).ready(function() {
	var $num, $option;
	
	if (jQuery(".strength_meter").val() == "1") {
		jQuery(".strength_labels_div").fadeIn();
	} else {
		jQuery(".strength_labels_div").fadeOut();
	}

	jQuery(".strength_meter").on("change", function() {
		if (jQuery(this).val() == "1") {
			jQuery(".strength_labels_div").fadeIn();
		} else {
			jQuery(".strength_labels_div").fadeOut();
		}
	});
	
	$num = $option = "";
	
	jQuery(".piereg_registration_form_fields .fields_optionsbg .label_position").each(function(i, obj) {
		$num = jQuery(obj).attr("data-field_id");
		if (piereg.isNumeric($num)) {
			$id = jQuery(obj).attr("id");
			$value = (jQuery("#" + $id + " label").html());
			$option = $option + '<option value="' + $num + '">' + $value + '</option>';
		}

		
		jQuery("select.selected_field").html($option);
	});

	jQuery(".piereg_registration_form_fields .selected_field").live("change", function() {
		jQuery("#" + jQuery(this).attr("data-selected_field")).val(jQuery(this).val());
	});


	jQuery(".piereg_registration_form_fields .enabel_conditional_logic").live("change", function() {
		if (jQuery(this).val() == "1") {
			var val_conditional_logic_selected_field = jQuery(this).parent().parent().find("select.selected_field").val();    
			jQuery("#" + jQuery(this).parent().parent().find("select.selected_field").attr("data-selected_field")).val(val_conditional_logic_selected_field);
		}
	});


	jQuery(".piereg_registration_form_fields .selected_field_input").each(function() {
		$id = jQuery(this).attr("id");
		if (jQuery(this).val() != "") {
			jQuery("select[data-selected_field=" + $id + "]").val(jQuery(this).val());
		}
	});

	jQuery(".piereg_registration_form_fields .enabel_conditional_logic").live("change", function() {
		if (jQuery("#" + jQuery(this).attr("id")).val() == "1") {
			jQuery("div#" + jQuery(this).attr("data-conditional_area")).fadeIn();
			set_field_dropdown("div#" + jQuery(this).attr("data-conditional_area")); // if conditional logics enable change to yes in fields settings. 
			jQuery(this).parent().parent().find(".required").after('<span class="message_required"><strong> Note:</strong> Conditional logic, if used, will override this (required) setting. The field will be viewable only when conditional logic is true. Therefore making the field  \'required\' when conditional logic is false has no effect.</span>');
			
		} else {
			jQuery("div#" + jQuery(this).attr("data-conditional_area")).fadeOut();
			jQuery(this).parent().parent().find(".required").next('.message_required').remove();
		}
	});
	jQuery(".piereg_registration_form_fields .enabel_conditional_logic").each(function(i, obj) {
		if (jQuery("#" + jQuery(this).attr("id")).val() == "1") {
			jQuery("div#" + jQuery(this).attr("data-conditional_area")).css("display", "block");
			set_field_dropdown("div#" + jQuery(this).attr("data-conditional_area"));  // if conditional logics enabled already in fields settings. 
			jQuery(this).parent().parent().find(".required").after('<span class="message_required"><strong> Note:</strong> Conditional logic, if used, will override this (required) setting. The field will be viewable only when conditional logic is true. Therefore making the field  \'required\' when conditional logic is false has no effect.</span>');
		} else {
			jQuery("div#" + jQuery(this).attr("data-conditional_area")).css("display", "none");
			jQuery(this).parent().parent().find(".required").next('.message_required').remove();
		}
	});

	/* Allow Only come */
	jQuery("input[type=text].conditional_value_input").keypress(function(e) {
		if (jQuery(this).val().indexOf(",") >= 0) {
			if (e.which == 44 || e.keyCode == 44) {
				return false;
			}
		}
	});


	jQuery(".piereg_registration_form_fields .fields").each(function() {
		var find_class = jQuery(".field_rule_operator_select");
		var select_rule_field = jQuery(this).find(find_class);

		if (jQuery(select_rule_field).val() == "range") {
			jQuery(select_rule_field).next(".wrap_cond_value").children(".conditional_value_input").after('<span class="form_editor_quotation">Separate by comma ( , )</span>');
		}

		if (jQuery(select_rule_field).val() == "empty" || jQuery(select_rule_field).val() == "not_empty") {
			jQuery(select_rule_field).next(".wrap_cond_value").hide();
		}
	});

	jQuery("select.field_rule_operator_select").change(function() {
		if (jQuery(this).val() == "range") {
			jQuery(this).next(".wrap_cond_value").children(".conditional_value_input").after('<span class="form_editor_quotation">Separate by comma ( , )</span>');
		} else {
			jQuery(this).next(".wrap_cond_value").children(".conditional_value_input").next('.form_editor_quotation').remove();
		}

		if (jQuery(this).val() == "empty" || jQuery(this).val() == "not_empty") {
			jQuery(this).next(".wrap_cond_value").hide();
		} else {
			jQuery(this).next(".wrap_cond_value").show();
		}
	});

	

	jQuery("select.piereg_field_as").on("change", function() {
		/*
			1	=	Dropdown
			0	=	Radio
		*/
		if (jQuery(this).val() == 1) {
			jQuery(".piereg_pricing_radio").hide();
			jQuery(".piereg_pricing_select").show();
		} else {
			jQuery(".piereg_pricing_select").hide();
			jQuery(".piereg_pricing_radio").show();
		}
	});

	/*
	 *	Form Conditional Logic's Script
	 */
	$option = "";
	jQuery(".piereg_registration_form_fields .fields_optionsbg .label_position").each(function(i, obj) {

		$num = jQuery(obj).attr("data-field_id");
		if (piereg.isNumeric($num)) {
			$id = jQuery(obj).attr("id");
			$value = (jQuery("#" + $id + " label").html());
			$option = $option + '<option value="' + $num + '">' + $value + '</option>';
		}
	});

	jQuery(".pie_wrap .enabel_conditional_logic_area .enabel_conditional_logic").live("change", function() {
		if (jQuery(this).val() == "1") {
			jQuery("div#" + jQuery(this).attr("data-conditional_area")).fadeIn();
			set_field_dropdown("div#" + jQuery(this).attr("data-conditional_area")); // if change conditional logics enable change to yes in form settings. 
			jQuery(this).parent().parent().find(".required").after('<span class="message_required"><strong> Note:</strong> Conditional logic, if used, will override this (required) setting. The field will be viewable only when conditional logic is true. Therefore making the field  \'required\' when conditional logic is false has no effect.</span>');
		} else {
			jQuery("div#" + jQuery(this).attr("data-conditional_area")).fadeOut();
			jQuery(this).parent().parent().find(".required").next('.message_required').remove();
		}
	});

	jQuery(".pie_wrap .enabel_conditional_logic_area .selected_field").live("change", function() {
		jQuery("#" + jQuery(this).attr("data-selected_field")).val(jQuery(this).val());
	});

	jQuery(".pie_wrap .enabel_conditional_logic_area .selected_field_input").each(function() {
		$id = jQuery(this).attr("id");
		if (jQuery(this).val() != "") {
			jQuery("select[data-selected_field=" + $id + "]").val(jQuery(this).val());
		}
	});


	$option = "";
	jQuery(".fields_position").each(function(i, obj) {
		$fieldName = "";
		$fieldValue = "";
		if (jQuery(obj).children().attr("data-field-post-name"))
			$fieldName = jQuery(obj).children().attr("data-field-post-name");

		if (jQuery(obj).prev(".label_position").children("label").html()) {
			$fieldValue = jQuery(obj).prev(".label_position").children("label").html();
		} else {
			$id_arr = jQuery(obj).attr("id").split("_");
			$fieldValue = jQuery("#field_label_" + $id_arr[2]).children("label").html();
		}
		if ($fieldName != "" && $fieldValue != "") {
			$selected_opt = "";

			if (jQuery("#form_selected_field_value").val() != "" && jQuery("#form_selected_field_value").val() == $fieldName) {
				$selected_opt = 'selected="selected"';
			}
			$option = $option + '<option value="' + $fieldName + '" ' + $selected_opt + '>' + $fieldValue + '</option>';
		}
		
		
		//form_selected_field
		jQuery("select.form_selected_field").html($option); // Add options to conditional logics select field if applied in form settings. 
	});

	if (jQuery(".iscCnditionalOn").length) {
		jQuery("select.form_selected_field").each(function(i, obj) {
			var selectedVal = jQuery(this).prev('#form_selected_field_value').val();
			jQuery(this).val(selectedVal);

			var ruleOperator = jQuery(obj).next('#form_field_rule_operator');
			if (ruleOperator.val() == "empty") {
				jQuery(ruleOperator).next(".wrap_cond_value").hide();
			}
		});
	}

});

jQuery(".edit_btn").live("click", function() {
	var curr_conditional_select_field = jQuery(this).closest(".fields").find("select.selected_field");
	if (curr_conditional_select_field) {
		
		if ( (typeof curr_conditional_select_field.val() !== 'undefined') && (curr_conditional_select_field.val() == "" || curr_conditional_select_field.val() == null) ) {
			$field_option = "";
			jQuery(".piereg_registration_form_fields .fields_optionsbg .label_position").each(function(i, obj) {
				
				$num = jQuery(obj).attr("data-field_id");
				if (piereg.isNumeric($num)) {
					$id = jQuery(obj).attr("id");
					$value = (jQuery("#" + $id + " label").html());
					$field_option = $field_option + '<option value="' + $num + '">' + $value + '</option>';
				}
				
				
				curr_conditional_select_field.html($field_option); // add options to condtional select fields when click on edit button if empty. 
			});
			$field_option = "";
		} else if (jQuery(this).closest(".fields").find("select.selected_field").val() != "" && jQuery(this).closest(".fields").find("select.selected_field").val() != null) {
			$num = "";
			if (jQuery(this).closest(".fields").find(".delete_btn").attr("rel")) {
				$num = jQuery(this).closest(".fields").find(".delete_btn").attr("rel");
			} else {
				$num = jQuery(this).closest(".fields").find(".label_position").attr("data-field_id");
			}
			jQuery(this).closest(".fields").find("select.selected_field option").each(function(i, obj) {
				if ($num == jQuery(obj).val()) {
					jQuery(obj).remove();
				}
			});
		}
	}
});

function set_field_dropdown(id) {
	var conditional_select_fields = jQuery(id + "  select.selected_field");
	if (conditional_select_fields.val() == "" || conditional_select_fields.val() == null ) {
		
		//console.log(jQuery(id + "  select.selected_field").val());
		$option = "";
		jQuery(".piereg_registration_form_fields .fields_optionsbg .label_position").each(function(i, obj) {
			$num = jQuery(obj).attr("data-field_id");
			if (piereg.isNumeric($num)) {
				$id = jQuery(obj).attr("id");
				$value = (jQuery("#" + $id + " label").html());
				$option = $option + '<option value="' + $num + '">' + $value + '</option>';
			}
		});
		
		jQuery(conditional_select_fields).find("select.selected_field").html($option);  
		//jQuery(conditional_select_fields + "select.selected_field").html($option); 
	}
}
// End RegFormEdit
// Import Export page
jQuery(document).ready(function(e) {
  jQuery(".pieregister-admin .selectall").change(function (){
		if(jQuery(this).attr("checked")=="checked")
		{
			jQuery(".meta_key").attr("checked","checked")
		}
		else
		{
			jQuery(".meta_key").removeAttr("checked");		
		}	  
 	}); 
	jQuery(".pieregister-admin .meta_key").change(function () {
		if (jQuery('.meta_key:checked').length == jQuery('.meta_key').length) {
      		jQuery(".selectall").attr("checked","checked");
    	} 
		else
		{
			jQuery(".selectall").removeAttr("checked");		
		} 
 	});
    jQuery('.pieregister-admin #date_start,.pieregister-admin #date_end').datepicker({
        dateFormat : 'yy-mm-dd',	
		 maxDate: "M D"
    });
	jQuery(".pieregister-admin #start_icon").on("click", function() {
    	jQuery("#date_start").datepicker("show");
	});
	jQuery(".pieregister-admin #end_icon").on("click", function() {
    	jQuery("#date_end").datepicker("show");
	});
	jQuery(".pieregister-admin #export").on("submit", function() {
    	if(jQuery('.meta_key:checked').length < 1)
		{
			alert("Please select at least one field to export.");
			return false;
		}
	});
});
//End Import Export Page
//Invitation Page
function get_selected_box_ids()
{
	var checked_id = "";
	jQuery(".invitaion_fields_class").each(function(i,obj){
		if( (jQuery( obj ).prop('checked')) == true )
		{
			checked_id = checked_id + jQuery( obj ).attr("value") + ",";
		}
		
	});
	if(checked_id.trim() != "" && jQuery("#invitaion_code_bulk_option").val().trim() != "" && jQuery("#invitaion_code_bulk_option").val() != "0")
	{
		var status_str	= jQuery("#invitaion_code_bulk_option").val();
		
		if( status_str == "unactive" ) {
			status_str = "deactivate";
		} else if( status_str == "active" ) {
			status_str = "activate";
		}
		
		if(confirm("Are you sure you want to "+status_str+" selected invitation code(s).?") == true)
		{
			checked_id = checked_id.slice(0,-1);
			jQuery("#select_invitaion_code_bulk_option").val(checked_id);
			return true;
		}
		else{return false;}
	}
	else{
		jQuery("#invitaion_code_error").css("display","block");
		return false;
	}
}
function select_all_invitaion_checkbox()
{
	var status = document.getElementById("select_all_invitaion_checkbox").value;
	if(status.trim() == "true" ){
		jQuery(".select_all_invitaion_checkbox").val("false");
		jQuery(".invitaion_fields_class").attr("checked",false);
		jQuery(".select_all_invitaion_checkbox").attr("checked",false);
	}
	else{
		jQuery(".select_all_invitaion_checkbox").val("true");
		jQuery(".invitaion_fields_class").attr("checked",true);
		jQuery(".select_all_invitaion_checkbox").attr("checked",true);
	}
}
function show_field(crnt,val)
{
	jQuery("#"+crnt.id).css("display","none");
	jQuery("#"+val).css("display","block");
	jQuery("#"+val).focus();
}
function hide_field(crnt,val)
{
	jQuery("#"+crnt.id).css("display","none");
	jQuery("#"+val).css("display","block");
	current = jQuery("#"+crnt.id).val();
	value = jQuery("#"+val).html();
	jQuery("#"+val).html("Please Wait...");
	id = jQuery("#"+crnt.id).attr("data-id-invitationcode");
	type = jQuery("#"+crnt.id).attr("data-type-invitationcode");
	
	var inv_code_data = {
		method : "post",
		action: 'pireg_update_invitation_code',
		data: ({"value":jQuery("#"+crnt.id).val(),"id":id,"type":type})
	};
	piereg.post(ajaxurl, inv_code_data, function(response) {
		if(response.trim() == "done")
		{
			jQuery("#"+val).html(jQuery("#"+crnt.id).val());
		}
		else if(response.trim() == "duplicate")
		{
			if(current != value)
			{
				alert("This code ("+current+") already exist");
			}
			jQuery("#"+val).html(value);
			jQuery("#"+crnt.id).val(value);
		}
		else
		{
			jQuery("#"+val).html(value);
			jQuery("#"+crnt.id).val(value);
		}
	});
}
function confirmDelInviteCode(id,code)
{
	var conf = window.confirm("Are you sure you want to delete this ("+code+") code?");
	if(conf)
	{
		document.getElementById("invi_del_id").value = id;
		document.getElementById("del_form").submit();
	}
}
function changeStatusCode(id,code,status)
{
	var conf = window.confirm("Are you sure you want to "+ status +" this ("+ code +") code ?");
	if(conf)
	{
		document.getElementById("status_id").value = id;
		document.getElementById("status_form").submit();
	}
}

jQuery(document).ready(function(){
	jQuery("#invitation_code_per_page_items").change(function(){
		jQuery("#form_invitation_code_per_page_items").submit();
	});
});
//End Invitation Page
//Notification Page
function changediv(){
	var value = jQuery('#piereg_user_notification #user_email_type').val();
	jQuery(".hide-div").hide();
	jQuery("."+value).show();
	
	if( (jQuery("."+value).css('display') == 'list-item') || (jQuery("."+value).css('display') == 'block') )
	{
		jQuery("#piereg_user_notification .btnvisibile").show();
	}
}
jQuery(document).ready(function(){
	changediv();
	
});
if(jQuery('.ckeditor').length){
	jQuery('.ckeditor').each(function() {
		var $this = this;
		CKEDITOR.replace(jQuery($this).attr("id"),{removeButtons: 'About'});
		
	});
}jQuery(document).ready(function(){
	jQuery(".piereg_replacement_keys").change(function(){
		//get the ckeditor
		var $current_ckeditor_id = $(this).closest(".fields").find("textarea.ckeditor").prop("id");
		//console.log($current_ckeditor_id);
		CKEDITOR.instances[$current_ckeditor_id].insertHtml(jQuery(this).val().trim());
		jQuery(this).val('select');
	});
});
//End Notification page
//Payment gateway
function numbersonly(myfield, e, dec){
	var key;
	var keychar;
	
	if (window.event){
	   key = window.event.keyCode;
	}else if (e){
	   key = e.which;
	}else{
	   return true;
	}
	keychar = String.fromCharCode(key);
	
	// control keys
	if ((key==null) || (key==0) || (key==8) || 
		(key==9) || (key==13) || (key==27) ){
	   return true;
	
	// numbers
	}else if ((("0123456789").indexOf(keychar) > -1)){
	   return true;
	
	/* decimal point jump
	else if (dec && (keychar == "."))
	   {
	   myfield.form.elements[dec].focus();
	   return false;
	   }*/
	}else{
	   return false;
	}
}

jQuery(document).ready(function(){
	jQuery(".piereg-payment-log-table").on("click","tbody tr",function(e){
		jQuery("." + jQuery(this).attr("data-piereg-id") ).fadeToggle(1000);
	});
});
//End payment gateway
//RegForm
function confrm_box(msg,url)
{
	if(confirm(msg) == true)
	{
		window.location.href = url;
	}
}
function previrw_form(msg,url)
{
	if(confirm(msg) == true)
	{
		window.open(url,"_blank","toolbar=no,scrollbars=yes,menubar=no,resizable=no,location=no,width="+screen.width+",height="+screen.height+"");
	}
}
//End egForm
//Setting All users
jQuery(document).ready(function(){
	jQuery("#after_login").change(function(){
									   
		if( jQuery(this).val() == "url" ) {		
			jQuery(this).parent().next(".fields").show();
		} else {
			jQuery(this).parent().next(".fields").hide();
		}
	})
	jQuery("#alternate_logout").change(function(){
		if( jQuery(this).val() == "url" ) {						  
			jQuery(this).parent().next(".fields").show();
		} else {
			jQuery(this).parent().next(".fields").hide();
		}
	})
})

function validateSettings()
{
	var block_wp_login	= pie_pr_backend_dec_vars.block_wp_login;	
	if( block_wp_login == 1 && document.getElementById("alternate_login").value == "-1" ) {
		alert("Please select an alternate login page.");
		return false;	
	}
	
	if( block_wp_login == 1 && document.getElementById("alternate_register").value == "-1" ) {
		alert("Please select an alternate register page.");
		return false;	
	}

	if( block_wp_login == 1 && document.getElementById("alternate_forgotpass").value == "-1" ) {
		alert("Please select an alternate forgot password page.");
		return false;	
	}
}
//End Settings All users
//Setting RoleBased
jQuery(document).ready(function(e) {
								
	var length = jQuery('#piereg_user_role').children('option').length;
	if( length == 0) {
		jQuery('#piereg_user_role').prop('disabled', true);
	}
	
	jQuery("#invitation_code_per_page_items").change(function(){
		jQuery("#form_invitation_code_per_page_items").submit();
	});
	
	/*Color Change Disable record*/
	jQuery(".inactive").closest("tr").css({"background":"rgb(237, 234, 234)"});
	
	jQuery("#log_in_page").change(function(){
									   
		if( jQuery(this).val() == "0" ) {		
			jQuery(this).parent().next(".fields").show();
		} else {
			jQuery(this).parent().next(".fields").hide();
		}
	})
	jQuery("#log_out_page").change(function(){
		if( jQuery(this).val() == "0" ) {						  
			jQuery(this).parent().next(".fields").show();
		} else {
			jQuery(this).parent().next(".fields").hide();
		}
	})

});
function changeStatus(id,code,status)
{
	var conf = window.confirm("Are you sure you want to "+status+" this record?");
	if(conf)
	{
		document.getElementById("redirect_settings_status_id").value = id;
		document.getElementById("redirect_settings_status_form").submit();
	}
}
function confirmDel(id,code)
{
	var conf = window.confirm("Are you sure you want to delete this ("+code+") record?");
	if(conf)
	{
		document.getElementById("redirect_settings_del_id").value = id;
		document.getElementById("redirect_settings_del_form").submit();
	}
}
//End Setting RoleBased
//Setting Security Basic
function validateSettings()
{
	return piereg_recaptcha_validate();
}
function piereg_recaptcha_validate(){
	
	var is_error = false;
	
	if(!jQuery("#captcha_in_login_value_0").is(":checked") && jQuery("#piereg_capthca_in_login").val() != 2){
		if(jQuery("#piereg_reCAPTCHA_Public_Key").val() == ""){
			jQuery("#piereg_reCAPTCHA_Public_Key_error").show();
			jQuery("#piereg_reCAPTCHA_Public_Key").css({"border-color":"red"});
			jQuery("#piereg_reCAPTCHA_Public_Key").focus();
			is_error = true;
		}else if(jQuery("#piereg_reCAPTCHA_Private_Key").val() == ""){
			jQuery("#piereg_reCAPTCHA_Public_Key_error").show();
			jQuery("#piereg_reCAPTCHA_Private_Key").focus();
			is_error = true;
		}
	}else if(!jQuery("#captcha_in_forgot_value_0").is(":checked") && jQuery("#piereg_capthca_in_forgot_pass").val() != 2){
		if(jQuery("#piereg_reCAPTCHA_Public_Key").val() == ""){
			jQuery("#piereg_reCAPTCHA_Public_Key_error").show();
			jQuery("#piereg_reCAPTCHA_Public_Key").focus();
			is_error = true;
		}else if(jQuery("#piereg_reCAPTCHA_Private_Key").val() == ""){
			jQuery("#piereg_reCAPTCHA_Public_Key_error").show();
			jQuery("#piereg_reCAPTCHA_Private_Key").css({"border-color":"red"});
			jQuery("#piereg_reCAPTCHA_Private_Key").focus();
			is_error = true;
		}
	}else{
		jQuery("#piereg_reCAPTCHA_Public_Key_error").hide();
		jQuery("#piereg_reCAPTCHA_Private_Key").css({"border-color":""});
	}
	
	if(jQuery("#piereg_reCAPTCHA_Private_Key").val() != "" || jQuery("#piereg_reCAPTCHA_Public_Key").val()){
		var patt1 = /[0-9a-zA-Z_-]{40}/;
		
		
		if(!jQuery("#piereg_reCAPTCHA_Public_Key").val().match(patt1)){
			
			if(!jQuery("#tabs_5").is(":visible")){
				jQuery("#ui-id-5").click();
			}
			
			jQuery("#piereg_reCAPTCHA_Public_Key").css({"color":"red"});
			jQuery("#piereg_reCAPTCHA_Public_Key").focus();
			jQuery("#piereg_reCAPTCHA_Public_Key_error").show();
			is_error = true;
		}
		else if(!jQuery("#piereg_reCAPTCHA_Private_Key").val().match(patt1)){
			if(!jQuery("#tabs_5").is(":visible")){
				jQuery("#ui-id-5").click();
			}
			jQuery("#piereg_reCAPTCHA_Private_Key").css({"color":"red"});
			jQuery("#piereg_reCAPTCHA_Private_Key").focus();
			jQuery("#piereg_reCAPTCHA_Public_Key_error").show();
			is_error = true;
		}
	}
	
	if(is_error){
		return false;
	}else{
		return true;
	}
}
jQuery(document).ready(function(){
	/* Validate recaptcha error */
	jQuery(".piereg-gs-menu-btn").on("click",function(){
		return piereg_recaptcha_validate();		
	});
	
	/* Login Form Captcha */
	jQuery(".captcha_in_login_value").on("change",function(){
		captcha_show();
	});
	captcha_show();
	function captcha_show(){
		if(jQuery("#captcha_in_login_value_1").is(":checked")){
			jQuery(".piereg_captcha_label_show").fadeIn(1000);
			jQuery(".piereg_captcha_type_show").fadeIn(1000);
			if(jQuery("#piereg_capthca_in_login").val() == 1 && !jQuery("#captcha_in_login_value_0").is(":checked")){
				jQuery(".piereg_recapthca_skin_login").fadeIn(1000);
				jQuery("#note_quotation").fadeIn(1000);
			}
		}else if(jQuery("#captcha_in_login_value_0").is(":checked")){
			jQuery(".piereg_captcha_label_show").fadeOut(1000);
			jQuery(".piereg_captcha_type_show").fadeOut(1000);
			jQuery(".piereg_recapthca_skin_login").fadeOut(1000);
			jQuery("#note_quotation").fadeOut(1000);
		}
	}
		
	/* Login Form Captcha Type */
	jQuery("#piereg_capthca_in_login").on("change",function(){
		if(jQuery(this).val() == 1 && !jQuery("#captcha_in_login_value_0").is(":checked")){
			jQuery(".piereg_recapthca_skin_login").fadeIn(1000);
			jQuery("#note_quotation").fadeIn(1000);
		}else{
			jQuery(".piereg_recapthca_skin_login").fadeOut(1000);
			jQuery("#note_quotation").fadeOut(1000);
		}
	});
	
	/* Forgot Password Form Captcha */
	jQuery(".captcha_in_forgot_value").on("change",function(){
		captcha_forgot_show();
	});
	captcha_forgot_show();
	function captcha_forgot_show(){
		if(jQuery("#captcha_in_forgot_value_1").is(":checked")){
			jQuery(".piereg_capthca_forgot_pass_label_show").fadeIn(1000);
			jQuery(".piereg_captcha_forgot_pass_type_show").fadeIn(1000);
			if(jQuery("#piereg_capthca_in_forgot_pass").val() == 1 && !jQuery("#captcha_in_forgot_value_0").is(":checked")){
				jQuery(".piereg_recapthca_skin_forgot_pas").fadeIn(1000);
				jQuery("#for_note_quotation").fadeIn(1000);
			}
		}else if(jQuery("#captcha_in_forgot_value_0").is(":checked")){
			jQuery(".piereg_capthca_forgot_pass_label_show").fadeOut(1000);
			jQuery(".piereg_captcha_forgot_pass_type_show").fadeOut(1000);
			jQuery(".piereg_recapthca_skin_forgot_pas").fadeOut(1000);
			jQuery("#for_note_quotation").fadeOut(1000);
		}
	}
	
	/* Forgot Password Form Captcha Type */
	jQuery("#piereg_capthca_in_forgot_pass").on("change",function(){
		if(jQuery(this).val() == 1 && !jQuery("#captcha_in_forgot_value_0").is(":checked")){
			jQuery(".piereg_recapthca_skin_forgot_pas").fadeIn(1000);
			jQuery("#for_note_quotation").fadeIn(1000);
		}else{
			jQuery(".piereg_recapthca_skin_forgot_pas").fadeOut(1000);
			jQuery("#for_note_quotation").fadeOut(1000);
		}
	});
});
//End Setting Security basic
//RegUX
jQuery(document).ready(function(){
	jQuery("#outputjquery_ui_no").on("click",function(){
		var pr_confrimation = confirm("Warning: Turning off this script here can stop Pie-Register to work properly! Are you sure?");
		if(!pr_confrimation)
		{
			jQuery("#outputjquery_ui_yes").click();
		}
	});
});

/*************************************************/
///////////////// CUSTOM LOGO /////////////////////
jQuery(document).on("click", "#pie_custom_logo_button", function() {
	var $Width = window.innerWidth - 100;
	var $Height = window.innerHeight - 100;
	formfield = jQuery("#pie_custom_logo_url").prop("name");
	tb_show(pie_pr_backend_dec_vars.selectLogoText, pie_pr_backend_dec_vars.mediaUploadURL+"?post_id=0&amp;type=image&amp;context=custom-logo&amp;TB_iframe=1&amp;height="+$Height+"&amp;width="+$Width);
});

window.send_to_editor = function(html) {
	var imgsrc;
	if(jQuery(html).is("img"))
	{
		imgsrc = jQuery(html).attr("src");
	}	
	//jQuery("#pie_custom_logo_url").val(jQuery("img", html).attr("src"));
	jQuery("#pie_custom_logo_url").val(imgsrc);
	tb_remove();
}
//EndRegUX