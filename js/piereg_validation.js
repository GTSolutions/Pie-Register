/*
 * Inline Form Validation Engine 1.0, jQuery plugin
 *
 * Copyright(c) 2014
 *
 * 1.0 Rewrite by Baqar Hassan
 *
 */
var piereg = jQuery.noConflict();

piereg(document).ready(function($){
	(function($){
		var IsWidgetFields = false;
		/* Validate On Blur widget Field(s) */
		piereg(".widget .pieregWrapper .fieldset .input_fields").on("blur",function(){
			var piereg_validate = true;
			var rulesParsing = piereg(this).attr("class");
			if(rulesParsing !== null)
			{
				var getRules = /piereg_validate\[(.*)\]/.exec(rulesParsing);
				if(getRules !== null){
					var str = getRules[1];
					var rules = str.split(/\[|,|\]/);
					
					if( piereg(this).hasClass('hasDatepicker') && piereg(this).val() == "" )
					{
						// skip validate on blur if datepicker on empty.
					} else {
						piereg_validate = ValidateField(rules,this,true,".widget ");
					}
					
					if(piereg_validate){
						
					}else{
					}
				}
			}

		});
		/* Validate On Blur Field(s) */
		piereg(".pieregWrapper .fieldset .input_fields").on("blur",function(){
			var piereg_validate = true;
			var rulesParsing = piereg(this).attr("class");
			if(rulesParsing !== null)
			{
				var getRules = /piereg_validate\[(.*)\]/.exec(rulesParsing);
				if(getRules !== null){
					var str = getRules[1];
					var rules = str.split(/\[|,|\]/);
					
					if( piereg(this).hasClass('hasDatepicker') && piereg(this).val() == "" )
					{
						// skip validate on blur if datepicker on empty.
					} else {
						piereg_validate = ValidateField(rules,this,piereg_validate,"");
						
					}	
				}
			}																  
																  
			/*
			var getRules = /piereg_validate\[(.*)\]/.exec(rulesParsing);
			
			var str = getRules[1];
			var rules = str.split(/\[|,|\]/);
			
			//console.log(rules.length);
			//console.log(rules);
			
			ValidateField(rules,this,true,"");*/

		});
		
		/*piereg("div.pieregWrapper div.fieldset textarea").on("blur",function(){
			
			var rulesParsing = piereg(this).attr("class");
			var getRules = /piereg_validate\[(.*)\]/.exec(rulesParsing);
			var str = getRules[1];
			var rules = str.split(/\[|,|\]/);
			ValidateField(rules,this);
		});*/
		
		/* Validate On Submit Renew Account's Field(s) */
		piereg("#piereg_loginform").on("submit",function(){
			var piereg_validate = true;
			piereg("#piereg_loginform ul li .input_fields").each(function(i,obj){
				var rulesParsing = piereg(obj).attr("class");
				if(rulesParsing !== null)
				{
					var getRules = /piereg_validate\[(.*)\]/.exec(rulesParsing);
					if(getRules !== null){
						var str = getRules[1];
						var rules = str.split(/\[|,|\]/);
						piereg_validate = ValidateField(rules,obj,piereg_validate,"");
					}
				}
			});
			if(!piereg_validate)
				return false;
		});
		
		/* Validate On Submit widget Field(s) */
		piereg("#pie_widget_regiser_form").on("submit",function(){
			var piereg_validate = true;
			piereg( "#pie_widget_regiser_form ul .input_fields" ).each(function(i,obj) {
				
				var rulesParsing = piereg(obj).attr("class");
				if(rulesParsing !== null)
				{
					var getRules = /piereg_validate\[(.*)\]/.exec(rulesParsing);
					if(getRules !== null){
						var str = getRules[1];
						var rules = str.split(/\[|,|\]/);
						piereg_validate = ValidateField(rules,obj,piereg_validate,"");
					}
				}
			});
			if(!piereg_validate)
				return false;
		});
		
		/* Remove validation and strength meter default on Reset (Widget) */
		piereg("#pie_widget_regiser_form input[type='reset']").on("click",function(){
			piereg( "#pie_widget_regiser_form ul .input_fields" ).each(function(i,obj) {
				piereg(obj).closest("li").find(".legend_txt").remove();
				piereg(obj).closest("div.fieldset").removeClass("error");
			});				
			if( piereg('#piereg_passwordStrength_widget').length > 0  ) {
				piereg( "#piereg_passwordStrength_widget" ).removeAttr('class').attr('class', '');
				piereg( "#piereg_passwordStrength_widget" ).addClass("piereg_pass");
				piereg( "#piereg_passwordStrength_widget" ).html("Strength Indicator");
			}
		});
		
		/* Remove validation and strength meter default on Reset */
		piereg("#pie_regiser_form input[type='reset']").on("click",function(){
			piereg( "#pie_regiser_form ul .input_fields" ).each(function(i,obj) {
				piereg(obj).closest("li").find(".legend_txt").remove();
				piereg(obj).closest("div.fieldset").removeClass("error");
			});
			
			if( piereg('#piereg_passwordStrength').length > 0  ) {
				piereg( "#piereg_passwordStrength" ).removeAttr('class').attr('class', '');
				piereg( "#piereg_passwordStrength" ).addClass("piereg_pass");
				piereg( "#piereg_passwordStrength" ).html("Strength Indicator");
			}
		});
		
		/* Validate On Submit Field(s) */
		piereg("#pie_regiser_form").on("submit",function(){
			
			var piereg_validate = true;
			piereg( "#pie_regiser_form ul .input_fields" ).each(function(i,obj) {
				var rulesParsing = piereg(obj).attr("class");
				if(rulesParsing !== null)
				{
					var getRules = /piereg_validate\[(.*)\]/.exec(rulesParsing);
					if(getRules !== null){
						var str = getRules[1];
						var rules = str.split(/\[|,|\]/);
						piereg_validate = ValidateField(rules,obj,piereg_validate,"");
					}
				}
			});
			
			if(!piereg_validate)
				return false;
		});
		
		/* Validate Next Page widget Field(s) */
		piereg(".widget #pie_widget_regiser_form .piewid_pie_next").click(function(){
			var piereg_validate = true;
			piereg( ".widget #pie_widget_regiser_form ul .input_fields" ).each(function(i,obj) {
				if(piereg(obj).closest("li").is(':visible'))
				{
					var rulesParsing = piereg(obj).attr("class");
					if(rulesParsing !== null)
					{
						var getRules = /piereg_validate\[(.*)\]/.exec(rulesParsing);
						if(getRules !== null){
							var str = getRules[1];
							var rules = str.split(/\[|,|\]/);
							piereg_validate = ValidateField(rules,obj,piereg_validate,".widget ");
							//console.log(i + " | " + piereg_validate);
						}
					}
				}
			});
			if(piereg_validate)
			{
				pieNextPage(this);	
			}
		});
																
		/* Validate Next Page Field(s) */
		piereg("#pie_regiser_form .pie_next").click(function(){
			var piereg_validate = true;
			piereg( "#pie_regiser_form ul .input_fields" ).not(".widget #pie_regiser_form ul .input_fields").each(function(i,obj) {
				if(piereg(obj).closest("li").is(':visible'))
				{
					var rulesParsing = piereg(obj).attr("class");
					if(rulesParsing !== null)
					{
						var getRules = /piereg_validate\[(.*)\]/.exec(rulesParsing);
						if(getRules !== null){
							var str = getRules[1];
							var rules = str.split(/\[|,|\]/);
							piereg_validate = ValidateField(rules,obj,piereg_validate,"");
							//console.log(i + " | " + piereg_validate);
						}
					}
				}
			});
			if(piereg_validate)
			{
				pieNextPage(this);	
			}  
		});
		
		piereg("#pie_regiser_form .pie_prev").click(function (){  
			pieNextPage(this);
		}); 
		
		piereg('input[name="pie_reset"]').click(function(){
			piereg("#pie_register").find(".legend_txt").remove();
			piereg("#pie_register").find("div.fieldset").removeClass("error");
		});
		
	})(jQuery);
	
	
	
	piereg("input.piereg_username_input_field").alphanum({
		allow              : '_',    // Allow extra characters
		disallow           : '',    // Disallow extra characters
		allowSpace         : false,  // Allow the space character
		allowNumeric       : true,  // Allow digits 0-9
		allowUpper         : true,  // Allow upper case characters
		allowLower         : true,  // Allow lower case characters
		allowCaseless      : true,  // Allow characters that do not have both upper & lower variants
									// eg Arabic or Chinese
		allowLatin         : true,  // a-z A-Z
		allowOtherCharSets : true,  // eg é, Á, Arabic, Chinese etc
		forceUpper         : false, // Convert lower case characters to upper case
		forceLower         : false, // Convert upper case characters to lower case
		maxLength          : NaN    // eg Max Length
	});
	
	
	piereg("input.piereg_name_input_field").alphanum({
		allow              : '',    // Allow extra characters
		disallow           : '',    // Disallow extra characters
		allowSpace         : true,  // Allow the space character
		allowNumeric       : false,  // Allow digits 0-9
		allowUpper         : true,  // Allow upper case characters
		allowLower         : true,  // Allow lower case characters
		allowCaseless      : true,  // Allow characters that do not have both upper & lower variants
									// eg Arabic or Chinese
		allowLatin         : true,  // a-z A-Z
		allowOtherCharSets : true,  // eg é, Á, Arabic, Chinese etc
		forceUpper         : false, // Convert lower case characters to upper case
		forceLower         : false, // Convert upper case characters to lower case
		maxLength          : NaN    // eg Max Length
	});
	/* Restrict username of Upprecase & space (start)*/ // Edited by AHMED 120515
	//piereg('input.piereg_username_input_field').keypress(function(e) {
		/*if (this.value.match(/[^a-zA-Z0-9]/g)) {
			//console.log(this.value);
			this.value = this.value.replace(/[^a-zA-Z0-9]/g, '');
		}
		var charCode = 0;
		if(key.which > 0) {
			charCode = key.which;
		} else if(key.keyCode > 0){
			//charCode = key.keyCode;
		} else{			
			charCode = key.charCode;
		}
		var charString = String.fromCharCode(charCode);
		if(piereg.inArray( charString , ['%', ' ','~','~','!','@','#','$','%','^','&','*','(',')','+','=','{','}','[',']','\\','|',':',';','"',"'",'<','>','/','?','*','-','+','.'] ) >= 0){
			return false;
		}*/
	//});
	
	/*piereg('input.piereg_username_input_field').keyup(function(key,obj) {
		if( key.keyCode == 17 || (key.ctrlKey == true && key.keyCode == 65) ) {
			return false;
		} else {
			var currpostion	= getCursorPosition('.piereg_username_input_field');
			piereg(this).val( piereg(this).val().toLowerCase() );
			piereg(this).setCursorPosition(currpostion, false);
		}
	});
	/* Restrict username of Upprecase & space (end)*/
	
});

function getCursorPosition(classname){
	var ctl = document.querySelector(classname);
    var startPos = ctl.selectionStart;
    var endPos = ctl.selectionEnd;
    return endPos;  
}

piereg.fn.setCursorPosition = function(start, end) {
    if(!end) end = start; 
    return this.each(function() {
        if (this.setSelectionRange) {
            this.focus();
            this.setSelectionRange(start, end);
        } else if (this.createTextRange) {
            var range = this.createTextRange();
            range.collapse(true);
            range.moveEnd('character', end);
            range.moveStart('character', start);
            range.select();
        }
    });
};

function validImportForm(form, inputclass){
	
	if(inputclass)	 
	{
		if(piereg(inputclass).val().length == "0")
		{ 
			alert("No file selected.");
			return false;
		}		
	} 
	
	if( window.confirm("Are you sure, you want to overwrite all your existing settings?") )
	{
		form.submit();	
	}
}

function ValidateField(rules,option,piereg_validate,IsWidget){
	var getAllRules = getRegexAndErrorMsg();
	var breakLoop = false;
	var $this_fields_is_display = piereg(option).closest('.fields').css('display');
	//var $thisIsGroup = false;
	//var $hasError = false;
	//if(piereg(option).closest('.fieldset').hasClass('piegroup')){
		//$thisIsGroup = true;
	//}
	//if(piereg(option).closest('.fieldset').hasClass('error')){
		//$hasError = true;
	//}
	for (var i = 0; i < rules.length; i++) {
		switch(rules[i]){
			case "required":
				switch(piereg(option).attr("type")){
					case "radio":
					case "checkbox":
						if($this_fields_is_display == 'none'){
							//if(!thisIsGroup && $hasError)
							RemoveErrorMsg(option);
						}else{
						//piereg("input[data-map-field-by-class="+(piereg(option).attr("data-map-field-by-class"))+"]").each(function(i,obj) {
							pieregchecked = false;
							var $checked = false;
							//piereg("input[data-map-field-by-class=radio_14]").each(function(i,obj) {
							piereg(IsWidget+"input[data-map-field-by-class="+(piereg(option).attr("data-map-field-by-class"))+"]").each(function(i,obj) {
								if(piereg(obj).prop("checked"))
								{
									$checked = true;
								}
							});
													
							if(!$checked) 
							{
								ShowErrorMsg(option,getCustomFieldMessage(option,getAllRules.required.alertText));
								breakLoop = true;
							}else{
								RemoveErrorMsg(option);
							}
						}
					break;
					default:
						if( piereg(option).attr("data-type") == "list" )
						{
							var hasError = 0;
							var totalFields = piereg(".pie_list_cover").find(".input_fields").length;
							piereg(".pie_list_cover").find("input[type=text]").each(function(i,obj){
								if(piereg(this).val() !== "" )
								{
									err_remain = hasError;	
								}else
								{
									hasError++;
								}

							});
							
							if(hasError<totalFields)
							{
								RemoveErrorMsg(option);
														
							}else{
								ShowErrorMsg(option,getCustomFieldMessage(option,getAllRules.list.alertText));
								breakLoop = true;
								
							}			
						}
						else{
							
							if($this_fields_is_display == 'none'){
								RemoveErrorMsg(option);
							}else{
								if(piereg(option).val().trim() == ""){
									ShowErrorMsg(option,getCustomFieldMessage(option,getAllRules.required.alertText));
									breakLoop = true;
								}else{
									RemoveErrorMsg(option);
								}
							}
						}
						
						
					break;
				}
					
				
			break;
			case "equals":
				i++;
				
				var field_confirm 	= piereg(option).val().trim();
				var field_value 	= piereg("#"+rules[i]).val().trim();
					
				// To check if rule is email
				if(rules[i].indexOf('email_') !== -1) {
					var field_confirm 	= field_confirm.toLowerCase();
					var field_value 	= field_value.toLowerCase();
				} 
				
				if(field_confirm != field_value){
					ShowErrorMsg(option,getCustomFieldMessage(option,getAllRules.equals.alertText));
					breakLoop = true;
				}/*else if(piereg(option).val().trim() == "" ){
					ShowErrorMsg(option,getCustomFieldMessage(option,getAllRules.equals.alertText));
					breakLoop = true;
				}*/else{
					RemoveErrorMsg(option);
				}
			break;
			case "custom":
				i++;
				var regex,alertText;
				switch(rules[i]){
					case "email":
					case "number":
					case "alphanumeric":
					case "url":
					case "phone_standard":
					case "phone_international":
					case "month":
					case "day":
					case "year":
						
						//console.log(getAllRules[rules[i]]);
						if(!piereg(option).val().trim().match(new RegExp(getAllRules[rules[i]].regex),piereg(option).val().trim()))
						{
							if(piereg(option).val().trim() == "") {
								RemoveErrorMsg(option);
							} else {
								ShowErrorMsg(option,getCustomFieldMessage(option,getAllRules[rules[i]].alertText));
								breakLoop = true;
							}
						}else{
							RemoveErrorMsg(option);
						}
						
					break;
				}
				
			break;			
			case "username":
			case "alphabetic":
				
				if(piereg(option).val().trim().match(new RegExp(getAllRules.username.regex),piereg(option).val().trim()))
				{
					ShowErrorMsg(option,getCustomFieldMessage(option,getAllRules.username.alertText));
					breakLoop = true;
				}else{
					RemoveErrorMsg(option);
				}
				
			break;			
			case "minSize":
				i++;
				if(piereg(option).val().trim() != ""){
					var strlen = piereg(option).val().trim();
					if(rules[i] > strlen.length)
					{
						ShowErrorMsg(option,getCustomFieldMessage(option,getAllRules.minSize.alertText+" "+rules[i]+" "+getAllRules.minSize.alertText2));
						breakLoop = true;
					}else{
						RemoveErrorMsg(option);
					}
				}
			break;
			case "maxSize":
				i++;
				if(piereg(option).val().trim())
				{
					var strlen = piereg(option).val().trim();
					if(rules[i] < strlen.length)
					{
						ShowErrorMsg(option,getCustomFieldMessage(option,getAllRules.maxSize.alertText+" "+rules[i]+" "+getAllRules.maxSize.alertText2));
						breakLoop = true;
					}else{
						RemoveErrorMsg(option);
					}
				}
			break;
			case "ext":
				i++;
				var regex;
				regex = "(.*?)\.("+rules[i]+")$";
				var regexExpression ="(?!(?:[^<]+>|[^>]+<\\/a>))\\b(" + rules[i] + ")\\b";
				if(piereg(option).val().trim() != "")
				{
					if(!piereg(option).val().trim().match(new RegExp(regex),piereg(option).val().trim()))
					{
						ShowErrorMsg(option,getCustomFieldMessage(option,getAllRules.ext.alertText));
						breakLoop = true;
					}else{
						RemoveErrorMsg(option);
					}
				}
			break;
			case "min":
				i++;
				if(piereg(option).val() != "")
				{
					$value = parseInt(piereg(option).val());
					if($value < rules[i])
					{
						ShowErrorMsg(option,getCustomFieldMessage(option,getAllRules.min.alertText+" "+rules[i]));
						breakLoop = true;
					}else{
						RemoveErrorMsg(option);
					}
				}
			break;
			case "max":
				i++;
				if(piereg(option).val())
				{
					$value = parseInt(piereg(option).val());
					if($value > rules[i])
					{
						ShowErrorMsg(option,getCustomFieldMessage(option,getAllRules.max.alertText+" "+rules[i]));
						breakLoop = true;
					}else{
						RemoveErrorMsg(option);
					}
				}
			break;
			
		}
		
		if(breakLoop)
		{
			piereg_validate = false;
			break;
		}
	}
	
	return piereg_validate;
}

function pieNextPage(elem)
{
	//pieHideFields();
	piereg(elem).closest('.pieregformWrapper').find('#pie_register .fields,form .fields').css('display','none');
	var id 		= piereg(elem).attr("id");
	var pageNo = piereg(elem).closest('form,#pie_regiser_form').find("#"+id+"_curr").val();
	var totalPages = piereg(elem).closest('form,#pie_regiser_form').find('.piereg_regform_total_pages').val();
	piereg(elem).closest('form,#pie_regiser_form').find('.pageFields_'+pageNo).css('display','block');
	piereg(elem).closest('.pieregformWrapper').find(".piereg_progressbar" ).progressbar( "option", {
	  value: pageNo / totalPages * 100
	});
}

function pieHideFields()
{
	/*var elms = document.getElementsByClassName('fields');
	for(a = 0 ; a < elms.length ; a++)
	{
		elms[a].style.display = "none";	
	}*/
	piereg('.pieregformWrapper .fields').css('display','none');
}
function getCustomFieldMessage(option,message){
	
	if(piereg(option).attr("data-errormessage-value-missing"))
	{
		return piereg(option).attr("data-errormessage-value-missing");
	}
	return message;
}

/*
function validateBeforeRemove( option )
{
	var err_remain		= 0; 
	var field_class 	= piereg(option).attr('class');
	var field_value 	= piereg.trim(this.value);
	var getAllRules 	= getRegexAndErrorMsg();
	
	if (field_value == "" && field_class.indexOf("required") >= 0) {
		err_remain++;
	}
	else if(field_class !== null)
	{
		
		var getRules		= /piereg_validate\[(.*)\]/.exec(field_class);		
		if(getRules !== null){
			var str = getRules[1];
			var rules = str.split(/\[|,|\]/);			
			for (var i = 0; i < rules.length; i++) {
				
				switch(rules[i]){
					case "custom":
					break;
				}
			}
		}
	}
	return err_remain;
}
*/

function ShowErrorMsg(field,promptText){
	piereg(field).closest("li").find(".legend_txt").remove();
	piereg(field).closest("li").find(".fieldset").addClass("error");
	piereg(field).closest("div.fieldset").append('<div class="legend_txt"><span class="legend error">'+promptText+'</span></div>');
}



function RemoveErrorMsg(field){
	var field_name = piereg(field).attr('name');
	var err_remain = 0;
	var container	= piereg(field).closest('.fieldset');
	
	/*
	if( typeof field_name == "string" && (field_name.indexOf("address_") >= 0 || field_name.indexOf("date_") >= 0 || field_name.indexOf("time_") >= 0) )
	{
		validateBeforeRemove(field)
	}
	else {
		piereg(field).closest("li").find(".legend_txt").remove();
		piereg(field).closest("div.fieldset").removeClass("error");
	}
	*/
	
	
	if (typeof field_name == "string" && field_name.indexOf("address_") >= 0 ) {
		
		piereg(container).find(".input_fields").each(function(){
			var field_class = piereg(this).attr('class');
			var field_value = piereg.trim(this.value);
			if (field_value == "" && field_class.indexOf("required") >= 0) {
				err_remain++;
			}else if( field_class.indexOf("alphabetic") >= 0 ) {
				var regex = /^[a-zA-Z\s]+$/;
				if(!regex.test(field_value)) {
					err_remain++;
				}
			}
		})
			
	}
	else if(typeof field_name == "string" && field_name.indexOf("date_") >= 0 )
	{
		piereg(container).find(".input_fields").each(function(i,obj) {
																					
			var rulesParsing 	= piereg(obj).attr("class");
			var field_class 	= piereg(obj).attr('class');
			var field_value 	= piereg.trim(this.value);
			
			if (field_value == "" && field_class.indexOf("required") >= 0) {
				err_remain++;
			} 
			else if(rulesParsing !== null)
			{
				var getAllRules 	= getRegexAndErrorMsg();
				var getRules		= /piereg_validate\[(.*)\]/.exec(rulesParsing);
				
				if(getRules !== null){
					var str = getRules[1];
					var rules = str.split(/\[|,|\]/);
					
					for (var i = 0; i < rules.length; i++) {
						
						if(	rules[i] == 'custom' )
						{
							i++;
							if(!piereg(obj).val().trim().match(new RegExp(getAllRules[rules[i]].regex),piereg(obj).val().trim()))
							{
								if(piereg(obj).val().trim() !== "") {
									err_remain++;
									break;
								}
							}
						}
						
					}
					
					if(err_remain != 0){
						return false;
					}
					
				}
			}
			
		})
		
	}
	else if(typeof field_name == "string" && field_name.indexOf("time_") >= 0 ){
		
		piereg(container).find(".input_fields").each(function(i,obj) {
																					
			var rulesParsing 	= piereg(obj).attr("class");
			var field_class 	= piereg(obj).attr('class');
			var field_value 	= piereg.trim(this.value);
			
			if (field_value == "" && field_class.indexOf("required") >= 0) {
				err_remain++;
			} 
			else if(rulesParsing !== null)
			{
				var getAllRules 	= getRegexAndErrorMsg();
				var getRules		= /piereg_validate\[(.*)\]/.exec(rulesParsing);
				
				if(getRules !== null){
					var str = getRules[1];
					var rules = str.split(/\[|,|\]/);
					
					for (var i = 0; i < rules.length; i++) {
						
						if(	rules[i] == 'custom' )
						{
							i++;
							if(!piereg(obj).val().trim().match(new RegExp(getAllRules[rules[i]].regex),piereg(obj).val().trim()))
							{
								if(piereg(obj).val().trim() !== "") {
									err_remain++;
									break;
								}
							}
						}
						
						if( rules[i] == 'minSize' )
						{
							i++;
							var strlen = piereg(obj).val().trim();
							if(rules[i] > strlen.length)
							{
								err_remain++;
							}
						}
						if( rules[i] == 'maxSize' )
						{
							i++;
							var strlen = piereg(obj).val().trim();
							if(rules[i] < strlen.length)
							{
								err_remain++;
							}
						}
						if( rules[i] == 'min' )
						{
							i++;
							$value = parseInt(piereg(obj).val());
							if($value < rules[i])
							{
								err_remain++;
							}
						}
						
						if( rules[i] == 'max' )
						{
							i++;
							$value = parseInt(piereg(obj).val());
							if($value > rules[i])
							{
								err_remain++;
							}
						}
						
					}
					
					if(err_remain != 0){
						return false;
					}
					
				}
			}
			
		})
	}
	else {
		piereg(field).closest("li").find(".legend_txt").remove();
		piereg(field).closest("div.fieldset").removeClass("error");
	}
	
	if( err_remain == 0 ) {
		piereg(field).closest("li").find(".legend_txt").remove();
		piereg(field).closest("div.fieldset").removeClass("error");
	}
}

function getRegexAndErrorMsg(){
	var allRules ={
		"required": { // Add your regex rules here, you can take telephone as an example
			"regex": "none",
			"alertText": piereg_validation_engn[1],//"* This field is required",
			"alertTextCheckboxMultiple": piereg_validation_engn[2],//"* Please select an option",
			"alertTextCheckboxe": piereg_validation_engn[3],//"* This checkbox is required",
			"alertTextDateRange": piereg_validation_engn[4]//"* Both date range fields are required"
		},
		"username": {
			"regex": /\s+/g,
			"alertText": piereg_validation_engn[53]//"* Invalid Username"
		},		
		"list":
		{
			"regex": /^[A-Za-z ]+$/,
			"alertText": piereg_validation_engn[1]//"* At least one field is required"
		},
		"ext": {
			"regex": "none",
			"alertText": piereg_validation_engn[54]//"* Invalid File"
		},
		"alphanumeric": {
			//"regex": /^[a-zA-Z0-9]+$/,
			"regex": /^[a-zA-Z0-9 ]+$/,
			"alertText": piereg_validation_engn[57]//"* Only Alphanumeric characters are allowed"
		},
		"alphabetic": {
			//"regex": /^[a-zA-Z\s]+$/,
			"regex": /\s+/g,
			"alertText": piereg_validation_engn[56]//"* Alphabetic Letters only"
		},
		"requiredInFunction": { 
			"func": function(field, rules, i, options){
				return (field.val() == "test") ? true : false;
			},
			"alertText": piereg_validation_engn[5]//"* Field must equal test"
		},
		"dateRange": {
			"regex": "none",
			"alertText": piereg_validation_engn[6],//"* Invalid ",
			"alertText2": piereg_validation_engn[7]//"Date Range"
		},
		"dateTimeRange": {
			"regex": "none",
			"alertText": piereg_validation_engn[6],//"* Invalid ",
			"alertText2": piereg_validation_engn[8]//"Date Time Range"
		},
		"minSize": {
			"regex": "none",
			"alertText": piereg_validation_engn[9],//"* Minimum ",
			"alertText2": piereg_validation_engn[10]//" characters required"
		},
		"maxSize": {
			"regex": "none",
			"alertText": piereg_validation_engn[11],//"* Maximum ",
			"alertText2": piereg_validation_engn[12]//" characters allowed"
		},
		"groupRequired": {
			"regex": "none",
			"alertText": piereg_validation_engn[13]//"* You must fill one of the following fields"
		},
		"min": {
			"regex": "none",
			"alertText": piereg_validation_engn[14]//"* Minimum value is "
		},
		"max": {
			"regex": "none",
			"alertText": piereg_validation_engn[55]//"* Maximum value is "
		},
		"past": {
			"regex": "none",
			"alertText": piereg_validation_engn[15]//"* Date prior to "
		},
		"future": {
			"regex": "none",
			"alertText": piereg_validation_engn[16]//"* Date past "
		},	
		"maxCheckbox": {
			"regex": "none",
			"alertText": piereg_validation_engn[9],//"* Maximum ",
			"alertText2": piereg_validation_engn[17]//" options allowed"
		},
		"minCheckbox": {
			"regex": "none",
			"alertText": piereg_validation_engn[18],//"* Please select ",
			"alertText2": piereg_validation_engn[19]//" options"
		},
		"equals": {
			"regex": "none",
			"alertText": piereg_validation_engn[20]//"* Fields do not match"
		},
		"creditCard": {
			"regex": "none",
			"alertText": piereg_validation_engn[21]//"* Invalid credit card number"
		},
		"phone": {
			// credit: jquery.h5validate.js / orefalo
			"regex": /^([\+][0-9]{1,3}[\ \.\-])?([\(]{1}[0-9]{2,6}[\)])?([0-9\ \.\-\/]{3,20})((x|ext|extension)[\ ]?[0-9]{1,4})?$/,
			"alertText": piereg_validation_engn[22]//"* Invalid phone number"
		},
		 "phone_standard": {
			// credit: jquery.h5validate.js / orefalo
			"regex": /^(\+\d{1,2}\s)?\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4}$/,
			"alertText": piereg_validation_engn[23]//"* Allowed Format (xxx) xxx-xxxx"
		},
		"phone_international": {
			// credit: jquery.h5validate.js / orefalo
			"regex": /^\d{10,16}$/,
			"alertText": piereg_validation_engn[24]//"* Minimum 10 Digits starting with Country Code"
		},
		"email": {
			// Shamelessly lifted from Scott Gonzalez via the Bassistance Validation plugin http://projects.scottsplayground.com/email_address_validation/
			/*"regex": /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,*/
			"regex": /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i,
			"alertText": piereg_validation_engn[25]//"* Invalid email address"
		},
		"integer": {
			"regex": /^[\-\+]?\d+$/,
			"alertText": piereg_validation_engn[26]//"* Not a valid integer"
		},
		"number": {
			// Number, including positive, negative, and floating decimal. credit: orefalo
			"regex": /^[\-\+]?((([0-9]{1,3})([,][0-9]{3})*)|([0-9]+))?([\.]([0-9]+))?$/,
			"alertText": piereg_validation_engn[27]//"* Invalid number"
		},
		"month": {
			"regex": /^(0[1-9]|1[0-2])$/,
			"alertText": piereg_validation_engn[28]//"* Invalid month"
		},
		"day": {
			"regex": /^(0[1-9]|1\d|2\d|3[01])$/,
			"alertText": piereg_validation_engn[29]//"* Invalid day"
		},
		"year": {
			"regex": /^[12][0-9]{3}$/,
			"alertText": piereg_validation_engn[30]//"* Invalid year"
		},
		"file": {
			"regex": /(\.bmp|\.gif|\.jpg|\.jpeg)$/i,
			"alertText": piereg_validation_engn[31]//"* Invalid file extension"
		},
		"date": {                    
			//	Check if date is valid by leap year
			"func": function (field) {
				var pattern = new RegExp(/^(\d{4})[\/\-\.](0?[1-9]|1[012])[\/\-\.](0?[1-9]|[12][0-9]|3[01])$/);
				var match = pattern.exec(field.val());
				if (match == null)
				   return false;
	
				var year = match[1];
				var month = match[2]*1;
				var day = match[3]*1;					
				var date = new Date(year, month - 1, day); // because months starts from 0.
	
				return (date.getFullYear() == year && date.getMonth() == (month - 1) && date.getDate() == day);
			},                		
			"alertText": piereg_validation_engn[32]//"* Invalid date, must be in YYYY-MM-DD format"
		},
		"ipv4": {
			"regex": /^((([01]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))[.]){3}(([0-1]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))$/,
			"alertText": piereg_validation_engn[33]//"* Invalid IP address"
		},
		"url": {
			"regex": /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i,
			"alertText": piereg_validation_engn[34]//"* Invalid URL"
		},
		"onlyNumberSp": {
			"regex": /^[0-9\ ]+$/,
			"alertText": piereg_validation_engn[35]//"* Numbers only"
		},
		"onlyLetterSp": {
			"regex": /^[a-zA-Z\ \']+$/,
			"alertText": piereg_validation_engn[36]//"* Letters only"
		},
		"onlyLetterNumber": {
			"regex": /^[0-9a-zA-Z]+$/,
			"alertText": piereg_validation_engn[37]//"* No special characters allowed"
		},
		// --- CUSTOM RULES -- Those are specific to the demos, they can be removed or changed to your likings
		"ajaxUserCall": {
			"url": "wp-admin/admin-ajax.php",
			"extraDataDynamic": ['#username'],
			// you may want to pass extra data on the ajax call
			 "extraData": "action=check_username",
			"alertText": piereg_validation_engn[38],//"* This user is already taken",
			"alertTextLoad": piereg_validation_engn[39]//"* Validating, please wait"
		},
		"ajaxUserCallPhp": {
			"url": "wp-admin/admin-ajax.php",
			"extraDataDynamic": ['#username'],
			// you may want to pass extra data on the ajax call
			"extraData": "action=check_username",
			// if you provide an "alertTextOk", it will show as a green prompt when the field validates
			"alertTextOk": piereg_validation_engn[40],//"* This username is available",
			"alertText": piereg_validation_engn[41],//"* This user is already taken",
			"alertTextLoad": piereg_validation_engn[42]//"* Validating, please wait"
		},
		"ajaxNameCall": {
			// remote json service location
			"url": "ajaxValidateFieldName",
			// error
			"alertText": piereg_validation_engn[43],//"* This name is already taken",
			// if you provide an "alertTextOk", it will show as a green prompt when the field validates
			"alertTextOk": piereg_validation_engn[44],//"* This name is available",
			// speaks by itself
			"alertTextLoad": piereg_validation_engn[45]//"* Validating, please wait"
		},
		 "ajaxNameCallPhp": {
				// remote json service location
				"url": "phpajax/ajaxValidateFieldName.php",
				// error
				"alertText": piereg_validation_engn[46],//"* This name is already taken",
				// speaks by itself
				"alertTextLoad": piereg_validation_engn[39]//"* Validating, please wait"
			},
		"validate2fields": {
			"alertText": piereg_validation_engn[47]//"* Please input HELLO"
		},
		//tls warning:homegrown not fielded 
		"dateFormat":{
			"regex": /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$|^(?:(?:(?:0?[13578]|1[02])(\/|-)31)|(?:(?:0?[1,3-9]|1[0-2])(\/|-)(?:29|30)))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^(?:(?:0?[1-9]|1[0-2])(\/|-)(?:0?[1-9]|1\d|2[0-8]))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^(0?2(\/|-)29)(\/|-)(?:(?:0[48]00|[13579][26]00|[2468][048]00)|(?:\d\d)?(?:0[48]|[2468][048]|[13579][26]))$/,
			"alertText": piereg_validation_engn[48]//"* Invalid Date"
		},
		//tls warning:homegrown not fielded 
		"dateTimeFormat": {
			"regex": /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])\s+(1[012]|0?[1-9]){1}:(0?[1-5]|[0-6][0-9]){1}:(0?[0-6]|[0-6][0-9]){1}\s+(am|pm|AM|PM){1}$|^(?:(?:(?:0?[13578]|1[02])(\/|-)31)|(?:(?:0?[1,3-9]|1[0-2])(\/|-)(?:29|30)))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^((1[012]|0?[1-9]){1}\/(0?[1-9]|[12][0-9]|3[01]){1}\/\d{2,4}\s+(1[012]|0?[1-9]){1}:(0?[1-5]|[0-6][0-9]){1}:(0?[0-6]|[0-6][0-9]){1}\s+(am|pm|AM|PM){1})$/,
			"alertText": piereg_validation_engn[49],//"* Invalid Date or Date Format",
			"alertText2": piereg_validation_engn[50],//"Expected Format: ",
			"alertText3": piereg_validation_engn[51],//"mm/dd/yyyy hh:mm:ss AM|PM or ", 
			"alertText4": piereg_validation_engn[52]//"yyyy-mm-dd hh:mm:ss AM|PM"
		}
	}
	
	return allRules; 
}










function checkExtensions(field, rules, i, options)
{
	var ext;
	for (var i=0;i<rules.length;i++)
	{
		 if( rules[i]=="ext"  )
		 {
			ext 		= rules[i+1].split("|");
			break	 
		 }
	}
	if(ext == "")
	return false;
	
	var uploadedExt = 	field.val().split('.').pop();
	if(ext.length > 0)
	{
		for(a = 0 ; a < ext.length ; a++)
		{
			if(uploadedExt == ext[a])
			return true;		
		}
	}
	
	return "* Invalid Extension";
	///(\.bmp|\.gif|\.jpg|\.jpeg)$/i
	//console.log(rules[5]);	
}
function addList(total,classname)
{
	for(a = 1 ; a <= total ; a++)
	{
		if(document.getElementsByClassName("list_"+classname+"_"+a)[0].style.display=="none")
		{
			document.getElementsByClassName("list_"+classname+"_"+a)[0].style.display = "";
			return false;
		}
	}
}
function removeList(total,classname,a)
{
	document.getElementsByClassName("list_"+classname+"_"+a)[0].style.display = "none";
	jQuery(".list_"+classname+"_"+a+" input[type=text]").val("");
}

// Declare jQuery Object to $.
$ = jQuery;