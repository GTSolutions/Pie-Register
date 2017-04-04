var piereg = jQuery.noConflict();
piereg(document).ready(function(){
	piereg("#pr_ristrict_widget_after_login").on("click",function(){
		if(piereg(this).is(':checked')){
			
			var check_all_checkboxes = true;
			piereg("#piereg_user_roles_area .pie_register_rw_checkboxs").each(function(i,obj){
				if(piereg(obj).is(':checked')){
					check_all_checkboxes = false;
				}
			});
			
			if(check_all_checkboxes == true)
			{
				piereg("#piereg_user_roles_area .pie_register_rw_checkboxs").each(function(i,obj){
					piereg(obj).attr('checked','checked');
				});
			}
			
			piereg("#pr_ristrict_widget_before_login").removeAttr('checked');
			piereg("#pr_ristrict_widget_off").removeAttr('checked');
			piereg("#piereg_user_roles_area").slideDown();
			piereg("#pr_ristrict_widget_off_hidden").val(piereg(this).val());
		}else{
			piereg("#pr_ristrict_widget_off").click();
		}
	});
	piereg("#pr_ristrict_widget_before_login").on("click",function(){
		if(piereg(this).is(':checked')){
			
			piereg("#piereg_user_roles_area .pie_register_rw_checkboxs").each(function(i,obj){
				if(piereg(obj).attr("data-pr_rw_select") == "unchecked"){
					piereg(obj).removeAttr('checked');
				}
			});
			
			piereg("#pr_ristrict_widget_after_login").removeAttr('checked');
			piereg("#pr_ristrict_widget_off").removeAttr('checked');
			piereg("#piereg_user_roles_area").slideUp();
			piereg("#pr_ristrict_widget_off_hidden").val(piereg(this).val());
		}else{
			piereg("#pr_ristrict_widget_off").click();
		}
	});
	piereg("#pr_ristrict_widget_off").on("click",function(){
		if(piereg(this).is(':checked')){
			piereg("#pr_ristrict_widget_before_login").removeAttr('checked');
			piereg("#pr_ristrict_widget_after_login").removeAttr('checked');
			
			piereg("#piereg_user_roles_area").slideUp();
			piereg("#pr_ristrict_widget_off_hidden").val(piereg(this).val());
		}else{
			piereg("#piereg_user_roles_area").slideUp();
			piereg("#pr_ristrict_widget_off_hidden").val(piereg("#pr_ristrict_widget_off").val());
			piereg("#pr_ristrict_widget_off").click();
		}
	});
	
	
	if(piereg("#pr_ristrict_widget_before_login").is(':checked')){
		piereg("#piereg_user_roles_area").slideUp();
	}
	
	if(piereg("#pr_ristrict_widget_after_login").is(':checked')){
		piereg("#piereg_user_roles_area").slideDown();
		piereg("#pr_ristrict_widget_off_hidden").val(piereg("#pr_ristrict_widget_after_login").val());
	}
	else if(piereg("#pr_ristrict_widget_before_login").is(':checked')){
		piereg("#piereg_user_roles_area").slideUp();
		piereg("#pr_ristrict_widget_off_hidden").val(piereg("#pr_ristrict_widget_before_login").val());
	}
	else if(piereg("#pr_ristrict_widget_off").is(':checked')){
		piereg("#piereg_user_roles_area").slideUp();
		piereg("#pr_ristrict_widget_off_hidden").val(piereg("#pr_ristrict_widget_off").val());
	}
	
	piereg("#pieregister_rw_select_all").on("click",function(){
		piereg("#piereg_user_roles_area .pie_register_rw_checkboxs").each(function(i,obj){
			piereg(obj).attr('checked','checked');
		});
	});


	piereg("#pieregister_rw_unselect_all").on("click",function(){
		piereg("#piereg_user_roles_area .pie_register_rw_checkboxs").each(function(i,obj){
			piereg(obj).removeAttr('checked');
		});
	});
});