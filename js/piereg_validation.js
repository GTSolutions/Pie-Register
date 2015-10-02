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
					piereg_validate = ValidateField(rules,this,true,".widget ");
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
					piereg_validate = ValidateField(rules,this,piereg_validate,"");
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
		
		
		
	})(jQuery);
});


function ValidateField(rules,option,piereg_validate,IsWidget){
	var getAllRules = getRegexAndErrorMsg();
	var breakLoop = false;
	for (var i = 0; i < rules.length; i++) {
		switch(rules[i]){
			case "required":
				switch(piereg(option).attr("type")){
					case "radio":
					case "checkbox":

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
						
					break;
					
					default:
						if(piereg(option).val().trim() == ""){
							ShowErrorMsg(option,getCustomFieldMessage(option,getAllRules.required.alertText));
							breakLoop = true;
						}else{
							RemoveErrorMsg(option);
						}
					break;
				}
					
				
			break;
			case "equals":
				i++;
				if(piereg(option).val().trim() != piereg("#"+rules[i]).val().trim()){
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
					case "alphabetic":
					case "url":
					case "phone_standard":
					case "phone_international":
					case "month":
					case  "day":
					case  "year":
						if(piereg(option).val().trim() != "")
						{
							console.log( rules[i] );
							if(!piereg(option).val().trim().match(new RegExp(getAllRules[rules[i]].regex),piereg(option).val().trim()))
							{
								ShowErrorMsg(option,getCustomFieldMessage(option,getAllRules[rules[i]].alertText));
								breakLoop = true;
							}else{
								RemoveErrorMsg(option);
							}
						}
					break;
				}
				
			break;
			
			/*case "username":
				if(!piereg(option).val().trim().match(new RegExp(getAllRules.username.regex),piereg(option).val().trim()))
				{
					ShowErrorMsg(option,getCustomFieldMessage(option,getAllRules.username.alertText));
					breakLoop = true;
				}else{
					RemoveErrorMsg(option);
				}
			break;*/
			
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
	piereg(elem).closest('.pieregformWrapper').find('form .fields').css('display','none');
	var id 		= piereg(elem).attr("id");
	//var pageNo 	= piereg("#"+id+"_curr").val();
	var pageNo = piereg(elem).closest('form').find("#"+id+"_curr").val();
	var totalPages = piereg(elem).closest('form').find('.piereg_regform_total_pages').val();
	//var pageNo = piereg("#"+id+"_curr").val();
	//var elms = document.getElementsByClassName('pageFields_'+pageNo);
	piereg(elem).closest('form').find('.pageFields_'+pageNo).css('display','block');/*
	for(a = 0 ; a < elms.length ; a++)
	{
		elms[a].style.display = "";	
	} */
	
	//
	
	/*piereg('html, body').animate({
        scrollTop: piereg(".piereg_progressbar").offset().top
    }, 0);*/
	//alert("pageno"+pageNo+" Total Pages:"+totalPages);
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

function ShowErrorMsg(field,promptText){
	piereg(field).closest("li").find(".legend_txt").remove();
	piereg(field).closest("li").find(".fieldset").addClass("error");
	piereg(field).closest("div.fieldset").append('<div class="legend_txt"><span class="legend error">'+promptText+'</span></div>');
}


function RemoveErrorMsg(field){
	piereg(field).closest("li").find(".legend_txt").remove();
	piereg(field).closest("div.fieldset").removeClass("error");
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
			"regex": "none",
			"alertText": piereg_validation_engn[53]//"* Invalid Username"
		},
		"ext": {
			"regex": "none",
			"alertText": piereg_validation_engn[54]//"* Invalid File"
		},
		"alphanumeric": {
			"regex": /^[a-zA-Z0-9]+$/,
			"alertText": piereg_validation_engn[53]//"* Invalid Username"
		},
		"alphabetic": {
			"regex": /^[a-zA-Z\s]+$/,
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
			"regex": /^(\+\d{1,2}\s)?\(?\d{3}\)?[\s.-]\d{3}[\s.-]\d{4}$/,
			"alertText": piereg_validation_engn[23]//"* Allowed Format (xxx) xxx-xxxx"
		},
		"phone_international": {
			// credit: jquery.h5validate.js / orefalo
			"regex": /^\d{10,16}$/,
			"alertText": piereg_validation_engn[24]//"* Minimum 10 Digits starting with Country Code"
		},
		"email": {
			// Shamelessly lifted from Scott Gonzalez via the Bassistance Validation plugin http://projects.scottsplayground.com/email_address_validation/
			"regex": /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,
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