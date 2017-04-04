var piereg = jQuery.noConflict();
var $prPasswordStrength		= 0;
var $prPasswordUserStrength	= 0;
function prProcessPasswordStrength(elem){
	var $form	= piereg(elem).closest('form');
	var $pass1	= $form.find('.prPass1').val();
	var $pass2	= $form.find('.prPass2').val();
	var $usern	= $form.find('input[type="username"]');
	var $meter	= $form.find('.prPasswordStrengthMeter');
	
	//console.log($pass1);
	$prPasswordStrength = passwordStrength($pass1,$usern,$pass2);
	//console.log($strength);
	updateStrength($prPasswordStrength,$pass1,$pass2,$meter);
	/////////
	
}
/******************************** New Password Strength Meter Code **************************************/
piereg(document).ready(function(){
	var $prStrengthMeterInstances = piereg(".prPasswordStrengthMeter");
	piereg('.piereg_container').on('keyup','.prPass1,.prPass2',function(){
		prProcessPasswordStrength(this);
		//// Check the strength
	});
	piereg('.piereg_container form').on('submit',function(e){
		var $form	= piereg(this);
		//$prPasswordUserStrength	= $form.find('.prMinimumPasswordStrengthlength').val();
		//console.log($prPasswordStrength, $prPasswordUserStrength);
		
		if($prPasswordStrength < $prPasswordUserStrength ){
			var restrict_strength_message = piereg(this).find(".prMinimumPasswordStrengthMessage").html();
			$form.find('.prPass2').closest(".fieldset").append('<div class="legend_txt"><span class="legend error">'+restrict_strength_message+'</span></div>').addClass("error");
			return false;
		}else{
			$form.find('.prPass2').closest(".fieldset").remove('.legend_txt').removeClass("error");
			return true;
		}
		//// Check the strength
	});
	/*piereg('.piereg_container').on('click','.pie_next, .pie_prev',function(e){
		var $minLen	= pireg(this).closest("form").find('.prMinimumPasswordStrengthlength').val();
		if($prPasswordStrength < $minLen){
			var restrict_strength_message = pireg(this).closest("form").find(".prMinimumPasswordStrengthMessage").html();
			pireg(this).closest("form").find("#password_2").closest(".fieldset").append('<div class="legend_txt"><span class="legend error">'+restrict_strength_message+'</span></div>').addClass("error");;
			return false;
		}else{
			return true;
		}
	});*/
	
	return false;
});


/*
	*	Add Restrict Password Strength meater since 2.0.13
*/

piereg(document).ready(function(){
	piereg("#pie_widget_regiser_form").on("submit",function(){
		
		var pass1 = "";
		if(piereg(".widget #password_2_widget").val().trim() != "")
		{
			pass1 = piereg(".widget #password_2_widget").val();
		}
		
		var pass2 = "";
		if(piereg(".widget #confirm_password_password_2_widget").val().trim() != "")
		{
			pass2 = piereg(".widget #confirm_password_password_2_widget").val();
		}
		
		var username = "";
		if(piereg(".widget #username_widget").val().trim() != "")
		{
			username = piereg(".widget #username_widget").val();
		}
		
		var strength = passwordStrength(pass1,username,pass2);
		//if(strength != 4)
		var current_form_id = piereg(this).attr("data-form");
		var current_password_strength_meter = piereg("#password_strength_meter_"+current_form_id).val();
		var password_strength_meter = current_password_strength_meter;
		if(password_strength_meter == 0 || password_strength_meter == 1)
		{
			return true;
		}
		if(strength < password_strength_meter)
		{
			/*piereg("#pie_regiser_form #password_2_widget").closest(".fieldset").addClass("error");
			piereg("#pie_regiser_form #password_2_widget").closest(".fieldset").append('<div class="legend_txt"><span class="legend error">'+piereg_restrict_pass_string[password_strength_meter]+'</span></div>');*/
			piereg(this).find("#password_2_widget").closest(".fieldset").addClass("error");
			/*piereg(this).find("#password_2_widget").closest(".fieldset").append('<div class="legend_txt"><span class="legend error">'+piereg_restrict_pass_string[password_strength_meter]+'</span></div>');*/
			var restrict_strength_message = piereg(this).find("#password_strength_message_"+current_form_id).html();
			piereg(this).find("#password_2_widget").closest(".fieldset").append('<div class="legend_txt"><span class="legend error">'+restrict_strength_message+'</span></div>');
			return false;
		}
	});
});


function updateStrength(strength,pass1,pass2,dom){
	var status = new Array('piereg_pass','piereg_pass_v_week', 'piereg_pass_week', 'piereg_pass_medium', 'piereg_pass_strong', 'piereg_pass_v_week');
    //var dom = piereg(elem);
	
	if(pass1 == "" && pass2 == ""){
		//dom.removeClass().addClass(status[0]).text(piereg_pass_str_meter_string[0]);
		
		// To remove all class and add default class with default text. Line of code above was'nt working to remove all classes. 
		//removeallclasses(dom);	
		dom.addClass(status[0]).addClass('prPasswordStrengthMeter').text(piereg_pass_str_meter_string[0]);
		
		return false;
	}
	//removeallclasses(dom);
	dom.removeClass(function(){
		return $( this ).attr( "class","" )
	}).addClass('piereg_pass prPasswordStrengthMeter mohsin');
	//dom.addClass('prPasswordStrengthMeter')
    switch(strength){
		case 1:
		  dom.addClass(status[1]).text(piereg_pass_str_meter_string[1]);
		  break;
		case 2:
		  dom.addClass(status[2]).text(piereg_pass_str_meter_string[2]);
		  break;
		case 3:
		  dom.addClass(status[3]).text(piereg_pass_str_meter_string[3]);
		  break;
		case 4:
		  dom.addClass(status[4]).text(piereg_pass_str_meter_string[4]);
		  break;
		case 5:
		  dom.addClass(status[5]).text(piereg_pass_str_meter_string[5]);
		  break;
		default:
		  dom.addClass(status[1]).text(piereg_pass_str_meter_string[1]);
		  break;
    }
}

function removeallclasses(dom){
	dom.removeClass();
}
/*
	*	Add Restrict Password Strength meater since 2.0.13
*/
piereg(document).ready(function(){
	piereg("#pie_regiser_form").on("submit",function(){
		
		var pass1 = "";
		if( piereg("#password_2").length > 0 && piereg("#password_2").val().trim() != "")
		{
			pass1 = piereg("#password_2").val();
		}
		
		var pass2 = "";
		if( piereg("#confirm_password_password_2").length > 0 && piereg("#confirm_password_password_2").val().trim() != "")
		{
			pass2 = piereg("#confirm_password_password_2").val();
		}
		
		var username = "";
		if( piereg("#username").lenght > 0 && piereg("#username").val().trim() != "")
		{
			username = piereg("#username").val();
		}else{
			username = piereg("input[name=e_mail]").val();
		}
		var strength = passwordStrength(pass1,username,pass2);
		/*if(strength != 4)*/
		var current_form_id = piereg(this).attr("data-form");
		var current_password_strength_meter = piereg("#password_strength_meter_"+current_form_id).val();
		var password_strength_meter = current_password_strength_meter;
		if(password_strength_meter == 0 || password_strength_meter == 1)
		{
			return true;
		}
		if(strength < password_strength_meter)
		{
			/*piereg("#pie_regiser_form #password_2").closest(".fieldset").addClass("error");
			piereg("#pie_regiser_form #password_2").closest(".fieldset").append('<div class="legend_txt"><span class="legend error">'+piereg_restrict_pass_string[password_strength_meter]+'</span></div>');*/
			piereg(this).find("#password_2").closest(".fieldset").addClass("error");
			/*piereg(this).find("#password_2").closest(".fieldset").append('<div class="legend_txt"><span class="legend error">'+piereg_restrict_pass_string[password_strength_meter]+'</span></div>');*/
			var restrict_strength_message = piereg(this).find("#password_strength_message_"+current_form_id).html();
			piereg(this).find("#password_2").closest(".fieldset").append('<div class="legend_txt"><span class="legend error">'+restrict_strength_message+'</span></div>');
			
			return false;
		}
	});
});


// Declare jQuery Object to $.
$ = jQuery;