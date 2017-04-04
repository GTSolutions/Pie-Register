////////////////// ReCaptcha ///////////////////////////
var is_widget;
var not_widget;
var is_forgot_widget;
var not_forgot_widget;
var $regforms = [];
var $form_ids = pie_pr_dec_vars.prRegFormsIds;
//var $captcha_login_script = pie_pr_dec_vars.captcha_login_script;
var $reCaptcha_public_key = pie_pr_dec_vars.reCaptcha_public_key;
var prRecaptchaCallBack = function() {
	var $_not_widget;
	$_not_widget = document.getElementById("not_widget");
	if($_not_widget != null && $_not_widget.length != 0 && $_not_widget.classList.contains("piereg_recaptcha_widget_div")) {
		not_widget = grecaptcha.render("not_widget", {
		  "sitekey" : $reCaptcha_public_key,
		  'theme' : pie_pr_dec_vars.not_widgetTheme
		});
	}
	var $_is_widget;
	$_is_widget = document.getElementById("is_widget");
	if($_is_widget != null && $_is_widget.length != 0&& $_is_widget.classList.contains("piereg_recaptcha_widget_div")) {
		is_widget = grecaptcha.render("is_widget", {
		  "sitekey" : $reCaptcha_public_key,
		  'theme' : pie_pr_dec_vars.is_widgetTheme
		});
	}
	var $_not_forgot_widget;
	$_not_forgot_widget = document.getElementById("not_forgot_widget");
	if($_not_forgot_widget != null && $_not_forgot_widget.length != 0 && $_not_forgot_widget.classList.contains("piereg_recaptcha_widget_div")) {
		not_widget = grecaptcha.render("not_forgot_widget", {
		  "sitekey" : $reCaptcha_public_key,
		  'theme' : pie_pr_dec_vars.not_forgot_widgetTheme
		});
	}
	var $_is_forgot_widget;
	$_is_forgot_widget = document.getElementById("is_forgot_widget");
	if($_is_forgot_widget != null && $_is_forgot_widget.length != 0 && $_is_forgot_widget.classList.contains("piereg_recaptcha_widget_div")) {
		is_widget = grecaptcha.render("is_forgot_widget", {
		  "sitekey" : $reCaptcha_public_key,
		  'theme' : pie_pr_dec_vars.is_forgot_widgetTheme
		});
	}
	
	for(i=0;i<=$form_ids.length;i++){
		var $_reg_form_id;
		$_reg_form_id = document.getElementById($form_ids[i]);
		if($_reg_form_id != null && $_reg_form_id.length != 0 && $_reg_form_id.classList.contains("piereg_recaptcha_reg_div")) {
			$regforms[i] = grecaptcha.render($form_ids[i], {
				"sitekey" : $reCaptcha_public_key,
				'theme' : pie_pr_dec_vars.reg_forms_theme[$form_ids[i]]
			});
		}
	}
};
////////////////// Match Captcha ///////////////////////
var prMathCaptchaID,is_forgot_widget,not_forgot_widget,pieregister_math_captha_widget,pieregister_math_captha,is_login_widget,not_login_widget,$pr_math_captcha;
$pr_math_captcha = document.getElementsByClassName("prMathCaptcha");
if($pr_math_captcha != null && $pr_math_captcha.length != 0){
	for(i=0;i<=$pr_math_captcha.length;i++){
		if(typeof $pr_math_captcha[i] != "undefined"){
			var $cookiename;
			//console.log($pr_math_captcha[i]);
			$cookiename = "piereg_math_captcha_"+$pr_math_captcha[i].dataset.cookiename;
			//console.log($cookiename);
			document.cookie= $cookiename+"="+pie_pr_dec_vars.matchCapResult1+"|"+pie_pr_dec_vars.matchCapResult2+"|"+pie_pr_dec_vars.matchCapResult3;
			prMathCaptchaID = $pr_math_captcha[i].getElementsByTagName('div')[0].id;
			//console.log(prMathCaptchaID);
			var $prMathCaptcha;
			$prMathCaptcha = document.getElementById(prMathCaptchaID);
			if($prMathCaptcha != null && $prMathCaptcha.length != 0){
				$prMathCaptcha.style.color=pie_pr_dec_vars.matchCapImgColor;
				$prMathCaptcha.style.backgroundImage = "url('"+pie_pr_dec_vars.matchCapImgURL+"')";
				$prMathCaptcha.innerHTML=pie_pr_dec_vars.matchCapHTML;
			}
		}
	}
}
//////////////////// End Math Captcha ////////////////////
//////////////// Social Login Redirect //////////////////
if(pie_pr_dec_vars.is_socialLoginRedirect){
	window.opener.location = pie_pr_dec_vars.socialLoginRedirectRenewAccount;
	window.close();
}
if(pie_pr_dec_vars.isSocialLoginRedirectOnLogin){
	window.opener.location = pie_pr_dec_vars.socialLoginRedirectOnLoginURL;
	window.close();
}
//
///////////// End Social Login Redirect /////////////////
////////////// Registration Form ///////////////////////
//piereg_regform_total_pages
var $pieregformWrapper,$piereg_progressbar,$piereg_regform_total_pages;
$pieregformWrapper = document.getElementsByClassName('pieregformWrapper');
$piereg_progressbar = document.getElementsByClassName('piereg_progressbar');
$piereg_regform_total_pages = jQuery('.piereg_regform_total_pages').val();
if($pieregformWrapper != null && $pieregformWrapper.length != 0 && $piereg_regform_total_pages > 1){
	pieHideFields();
	if(window.location.hash){
		var hash = window.location.hash.substring(1); //Puts hash in variable, and removes the # character 
		var elms = document.getElementsByClassName("pageFields_"+hash);
		for(a = 0 ; a < elms.length ; a++){
			elms[a].style.display = "";
		}
	}else{
		var elms = document.getElementsByClassName("pageFields_1");
		for(a = 0 ; a < elms.length ; a++){
			elms[a].style.display = "";
		}
	}
	if($piereg_progressbar != null && $piereg_progressbar.length != 0){
		 jQuery( ".piereg_progressbar" ).progressbar({
		  value:  1 /$piereg_regform_total_pages * 100
		});
	}
}
/////////////////// End Registration Form //////////////
/////////////////////// Renew Account //////////////////
jQuery(document).ready(function(){
	var $select_payment_method;
	$select_payment_method = document.getElementById("select_payment_method");
	if($select_payment_method != null && $select_payment_method.length != 0){
		jQuery("#select_payment_method").change(function(){
			var $sel_method = '';
			$sel_method = jQuery(this).val();
			if($sel_method != ""){
				var payment = "", image = "";
				//pie_pr_dec_vars.pie_payment_methods_data
				payment	= pie_pr_dec_vars.pie_payment_methods_data[$sel_method]['payment'];
				image	= pie_pr_dec_vars.pie_payment_methods_data[$sel_method]['image'];
				jQuery("#show_payment_method").html(payment);
				jQuery("#show_payment_method_image").html(image);
			}
			else
			{
				jQuery("#show_payment_method").html("");
				jQuery("#show_payment_method_image").html("");
			}
		});
	}
	$piereg_select_payment_method = document.getElementsByClassName("piereg_select_payment_method");
	if($piereg_select_payment_method != null && $piereg_select_payment_method.length != 0){
		jQuery("input.piereg_select_payment_method").on("click",function(){
			var $sel_method = '';
			$sel_method = jQuery(this).val();
			if($sel_method != ""){
				var payment = "", image = "";
				//pie_pr_dec_vars.pie_payment_methods_data
				payment	= pie_pr_dec_vars.pie_payment_methods_data[$sel_method]['payment'];
				image	= pie_pr_dec_vars.pie_payment_methods_data[$sel_method]['image'];
				jQuery("#show_payment_method").html(payment);
				jQuery("#show_payment_method_image").html(image);
			}
			else
			{
				jQuery("#show_payment_method").html("");
				jQuery("#show_payment_method_image").html("");
			}
		});
	}
});
/////////////////// End Renew Account //////////////////
////////////////// Reg Form ////////////////////////////
function prCheckCondition($val1,$val2,$operator){
	if($operator == '=='){
		return ($val1 == $val2)?true:false;
	}else if($operator == '!='){
		return ($val1 != $val2)?true:false;
	}else if($operator == '>'){
		return (parseInt($val1) > parseInt($val2))?true:false;
	}else if($operator == '<'){
		return (parseInt($val1) < parseInt($val2))?true:false;
	}else if($operator == 'contains'){
		return ($val1!= null && $val1.indexOf($val2) > -1)?true:false;
	}else if($operator == 'starts_with'){
		return ($val1!= null && $val1.indexOf($val2) === 0)?true:false;
	}else if($operator == 'ends_with'){
		return ($val1!= null && $val1.indexOf($val2,$val1.length - $val2.length) >= 0)?true:false;
	}else if($operator == 'empty'){
		return ($val1 == '' || $val1 == null)?true:false;
	}else if($operator == 'not_empty'){
		return ($val1 != '' && $val1 != null)?true:false;
	}else if($operator == 'range'){
		$res = $val2.split(",");		
		return (parseInt( $val1 ) >= parseInt( $res[0]) && parseInt( $val1 ) <= parseInt ( $res[1] )) ?true:false;
		//return ($val1 >= $res[0] && $val1 <= $res[1])?true:false;
	}
}
function prExecuteCond(elem,$field_trig_data,$data_to_match,$match_operator,$display){
	var $on_true,$on_false;
	if($display == 'show' || $display == 'block' || $display === 1){
		$on_true = "block";$on_false = "none";
	}else{
		$on_true = "none";$on_false = "block";
	}
	if(prCheckCondition($field_trig_data,$data_to_match,$match_operator)){
		//console.log('Matching',$on_true,$on_false,$display);
		jQuery(elem).closest(".fields").css("display",$on_true);
	}else{
		//console.log('Not Matching',$on_true,$on_false,$display);
		jQuery(elem).closest(".fields").css("display",$on_false);
	}	
}
function prConditionalLogics(){
	jQuery('.hasConditionalLogic').each(function(i,element) {
		//console.log(element);
		var $field_triggers_did	= jQuery(element).data("triggerid");
		var $field_triggers_id	= jQuery("*[data-field_id="+$field_triggers_did+"]").attr("id");
		var $nodename			= jQuery('#'+$field_triggers_id).prop('nodeName').toLowerCase();
		var $field_trig_data	= '';//jQuery("#"+$field_triggers_id).val();
		var $data_to_match		= jQuery(element).data("content");
		var $match_operator		= jQuery(element).data("operator");
		var $display_on_match	= jQuery(element).data("display");
		var $submitbtn			= jQuery(element).closest("form").find(":submit");
		var $resetbtn			= jQuery(element).closest("form").find(":reset");
		
		if($nodename == 'input'){
			var $inputtype = jQuery('#'+$field_triggers_id).prop('type');
			if($inputtype == 'checkbox' || $inputtype == 'radio'){
				$field_trig_data	= jQuery("#"+$field_triggers_id+':checked').val();
			}else{
				$field_trig_data	= jQuery("#"+$field_triggers_id).val();
			}
		}else if($nodename == 'select'){
			$field_trig_data	= jQuery("#"+$field_triggers_id).val();
		}else if($nodename == 'textarea'){
			$field_trig_data	= jQuery("textarea#"+$field_triggers_id).val();
		}
		//console.log($field_triggers_did);
		//console.log(element,$field_trig_data,$data_to_match,$match_operator,$display_on_match);
		prExecuteCond(element,$field_trig_data,$data_to_match,$match_operator,$display_on_match);
		//console.log($nodename);
		jQuery('.piereg_container').on("change",'#'+$field_triggers_id,function(e){
			if($nodename == 'input'){
				var $inputtype = jQuery('#'+$field_triggers_id).prop('type');
				if($inputtype == 'checkbox' || $inputtype == 'radio'){
					$field_trig_data	= jQuery("#"+$field_triggers_id+':checked').val();
				}else{
					$field_trig_data	= jQuery("#"+$field_triggers_id).val();
				}
			}else if($nodename == 'select'){
				$field_trig_data	= jQuery("#"+$field_triggers_id).val();
			}else if($nodename == 'textarea'){
				$field_trig_data	= jQuery("textarea#"+$field_triggers_id).val();
			}
			//console.log($field_trig_data);
			prExecuteCond(element,$field_trig_data,$data_to_match,$match_operator,$display_on_match);
		});
	});
}
jQuery(document).ready(function(){
	jQuery('.prTimedField').val(pie_pr_dec_vars.prTimedFieldVal);
	prConditionalLogics();
	jQuery('.piereg_container').on('click','.pie_next, .pie_prev',function(e){
		prConditionalLogics();
	});
});
////////////////////// End Reg Form ////////////////////