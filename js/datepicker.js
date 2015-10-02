var piereg = jQuery.noConflict();



piereg(document).ready(function(e) {
	
	
	
	/*piereg(".pieregWrapper #pie_register_reg_form .fieldset .piereg_time .time_fields .piereg_select_year").on("click",function(){
		if(piereg(this).val().trim() == "" && $('option:selected', this).attr('data-empty-vlue'))
		{
			piereg(this).val(piereg_current_date);
		}
	});*/
	
	/*piereg(".pieregformWrapper form").validationEngine();
	piereg("#lostpasswordform").validationEngine();
	piereg("#resetpassform").validationEngine();	
	piereg("#loginform").validationEngine();*/
	
	/*piereg('.date_start').datepicker({
        dateFormat : 'yy-mm-dd',
		changeMonth: true,
		changeYear: true,
		yearRange: piereg_startingDate+":"+piereg_endingDate
	});*/
	
	
		
	///////////////////////// DATE PICKER //////////////////////////////////
	
	piereg('.date_start').on("focus",function(){
		if (!piereg("#ui-datepicker-div").closest('.pieregWrapper').length) {
			piereg("#ui-datepicker-div").wrap("<div class='pieregWrapper pieregister-admin'></div>");
		}
	});
	piereg('.date_start').each(function(index, element) {
       
	    var id = piereg(this).attr("id");
		
		//Setting date Format
		var formatid = id + "_format";
		var format = piereg("#"+formatid).val();
		piereg( "#"+id ).datepicker({
										dateFormat : format,
										changeMonth: true,
										changeYear: true,
										yearRange: piereg_startingDate+":"+piereg_endingDate,
										showAnim: "fadeIn"
									});
		
		//First day of a week
		var formatid = id + "_firstday";
		var format = piereg("#"+formatid).val();
		piereg( "#"+id ).datepicker({
										firstDay : format,
										changeMonth: true,
										changeYear: true,
										yearRange: piereg_startingDate+":"+piereg_endingDate,
										showAnim: "fadeIn"
									});
		
		//Min date		
		var formatid = id + "_startdate";
		var format = piereg("#"+formatid).val();
		piereg( "#"+id ).datepicker({
										minDate : format,
										changeMonth: true,
										changeYear: true,
										yearRange: piereg_startingDate+":"+piereg_endingDate,
										showAnim: "fadeIn"
									});
		piereg("#ui-datepicker-div").hide();
    });
	
	piereg(".calendar_icon").on("click", function() {
    	var id = piereg(this).attr("id");		
		id = id.replace("_icon","");		
		piereg("#"+id).datepicker("show");
	});
	///////////////////////////////////////////////////////////////////
	/*piereg(".pie_next").click(function () 
	{  
		var validate = piereg(this).closest('.pieregformWrapper').find('form').validationEngine('validate')
		//var validate = piereg("#pie_regiser_form").validationEngine('validate');

		if(validate)
		{
			//var id 		= piereg(this).attr("id");
			//var pageNo 	= piereg("#"+id+"_curr").val();		
			//pieNextPage(pageNo);
			pieNextPage(this);	
		}  
	}); 
	
	piereg(".pie_prev").click(function () 
	   {  
		//var id 		= piereg(this).attr("id");
		//var pageNo 	= piereg("#"+id+"_curr").val();
		//pieNextPage(pageNo);
		pieNextPage(this);
		  
	}); */
	
	//piereg("#comments,.entry-meta").hide();
	
});
/*function passwordStrength(password)
{
	var desc = new Array();
	desc[0] = "Very Weak";
	desc[1] = "Weak";
	desc[2] = "Better";
	desc[3] = "Medium";
	desc[4] = "Strong";
	desc[5] = "Strongest";

	var score   = 0;

	//if password bigger than 6 give 1 point
	if (password.length > 6) score++;

	//if password has both lower and uppercase characters give 1 point	
	if ( ( password.match(/[a-z]/) ) && ( password.match(/[A-Z]/) ) ) score++;

	//if password has at least one number give 1 point
	if (password.match(/\d+/)) score++;

	//if password has at least one special caracther give 1 point
	if ( password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) )	score++;

	//if password bigger than 12 give another 1 point
	if (password.length > 12) score++;

	 document.getElementById("piereg_passwordDescription").innerHTML = desc[score];
	 document.getElementById("piereg_passwordStrength").className = "strength" + score;
}*/



/**************************************************************************************/
//Old Code
/* piereg(document).ready(function(){
      if(piereg("#pie_widget-2 #piereg_passwordStrength").length > 0){
		piereg("#pie_widget-2 #password_2").bind("keyup", function(){
			var pass1 = piereg("#pie_widget-2 #password_2").val();
			var pass2 = "";
			if(piereg("#pie_widget-2 #confirm_password_password_2").val().trim() != ""){
				pass2 = piereg("#pie_widget-2 #confirm_password_password_2").val();
			}
			
			var username = "";
			if(piereg("#pie_widget-2 #username").val() != ""){
				username = piereg("#pie_widget-2 #username").val();
			}
			
			var strength = passwordStrength(pass1,username,pass2);
			widget_updateStrength(strength,pass1,pass2);
		});
		piereg("#pie_widget-2 #confirm_password_password_2").bind("keyup", function(){
			var pass1 = piereg("#pie_widget-2 #password_2").val();
			var pass2 = piereg("#pie_widget-2 #confirm_password_password_2").val();
			var username = "";
			if(piereg("#pie_widget-2 #username").val().trim() != "")
				username = piereg("#pie_widget-2 #username").val();
			
			var strength = passwordStrength(pass1, username, pass2);
			
			widget_updateStrength(strength,pass1,pass2);
		});
	}
});*/








/**************************************************/








/*
piereg(document).ready(function(){
      if(piereg(".widget #piereg_passwordStrength_widget").length > 0){
		piereg(".widget #password_2_widget").bind("keyup", function(){
			var pass1 = piereg(".widget #password_2_widget").val();
			var pass2 = "";
			if(piereg(".widget #confirm_password_password_2_widget").val().trim() != ""){
				pass2 = piereg(".widget #confirm_password_password_2_widget").val();
			}
			
			var username = "";
			if(piereg(".widget #username_widget").val() != ""){
				username = piereg(".widget #username_widget").val();
			}
			var strength = passwordStrength(pass1,username,pass2);
			widget_updateStrength(strength,pass1,pass2);
		});
		piereg(".widget #confirm_password_password_2_widget").bind("keyup", function(){
			var pass1 = piereg(".widget #password_2_widget").val();
			var pass2 = piereg(".widget #confirm_password_password_2_widget").val();
			var username = "";
			if(piereg(".widget #username_widget").val().trim() != ""){
				username = piereg(".widget #username_widget").val();
			}
			var strength = passwordStrength(pass1, username, pass2);
			
			widget_updateStrength(strength,pass1,pass2);
		});
	}
});
function widget_updateStrength(strength,pass1,pass2){
    var status = new Array('piereg_pass','piereg_pass_v_week', 'piereg_pass_week', 'piereg_pass_medium', 'piereg_pass_strong', 'piereg_pass_v_week');
    var dom = piereg(".widget #piereg_passwordStrength_widget");
	
	if(pass1 == "" && pass2 == ""){
		dom.removeClass().addClass(status[0]).text(piereg_pass_str_meter_string[0]);
		return false;
	}
	
    switch(strength){
    case 1:
      dom.removeClass().addClass(status[1]).text(piereg_pass_str_meter_string[1]);
      break;
    case 2:
      dom.removeClass().addClass(status[2]).text(piereg_pass_str_meter_string[2]);
      break;
    case 3:
      dom.removeClass().addClass(status[3]).text(piereg_pass_str_meter_string[3]);
      break;
    case 4:
     dom.removeClass().addClass(status[4]).text(piereg_pass_str_meter_string[4]);
      break;
    case 5:
      dom.removeClass().addClass(status[5]).text(piereg_pass_str_meter_string[5]);
      break;
    default:
      dom.removeClass().addClass(status[1]).text(piereg_pass_str_meter_string[1]);
      break;
    }
}





piereg(document).ready(function(){
  if(piereg("#piereg_passwordStrength").length > 0){
		piereg("#password_2").bind("keyup", function(){
			var pass1 = piereg("#password_2").val();
			var pass2 = "";
			if(piereg("#confirm_password_password_2").val().trim() != ""){
				pass2 = piereg("#confirm_password_password_2").val();
			}
			
			var username = "";
			if(piereg("#username").val() != ""){
				username = piereg("#username").val();
			}
			var strength = passwordStrength(pass1,username,pass2);
			updateStrength(strength,pass1,pass2,"");
			
		});
		piereg("#confirm_password_password_2").bind("keyup", function(){
			var pass1 = piereg("#password_2").val();
			var pass2 = piereg("#confirm_password_password_2").val();
			var username = "";
			if(piereg("#username").val().trim() != "")
				username = piereg("#username").val();
			
			var strength = passwordStrength(pass1, username, pass2);
			updateStrength(strength,pass1,pass2,"");
		});
	}
});

function updateStrength(strength,pass1,pass2,widje){
    var status = new Array('piereg_pass','piereg_pass_v_week', 'piereg_pass_week', 'piereg_pass_medium', 'piereg_pass_strong', 'piereg_pass_v_week');
    var dom = piereg("#piereg_passwordStrength");
	
	if(pass1 == "" && pass2 == ""){
		dom.removeClass().addClass(status[0]).text(piereg_pass_str_meter_string[0]);
		return false;
	}
	
    switch(strength){
    case 1:
      dom.removeClass().addClass(status[1]).text(piereg_pass_str_meter_string[1]);
      break;
    case 2:
      dom.removeClass().addClass(status[2]).text(piereg_pass_str_meter_string[2]);
      break;
    case 3:
      dom.removeClass().addClass(status[3]).text(piereg_pass_str_meter_string[3]);
      break;
    case 4:
     dom.removeClass().addClass(status[4]).text(piereg_pass_str_meter_string[4]);
      break;
    case 5:
      dom.removeClass().addClass(status[5]).text(piereg_pass_str_meter_string[5]);
      break;
    default:
      dom.removeClass().addClass(status[1]).text(piereg_pass_str_meter_string[1]);
      break;
    }
}*/

// Declare jQuery Object to $.
$ = jQuery;
