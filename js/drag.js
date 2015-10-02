var currHTML, endHtml, dragType,fieldMeta;

var no = 3;
var piereg = jQuery.noConflict();


function getStyle(type) {

	var meta = "";

		var html = piereg('<div/>').addClass("fields_options fields_optionsbg").html('<a href="javascript:;" class="edit_btn"></a><div class="label_position"  id="field_label_' + no + '"><label><!--Untitled -->' + type + '</label></div><a rel="' + no + '" href="javascript:;" class="delete_btn">X</a><input type="hidden" name="field[' + no + '][id]" value="' + no + '" id = "id_' + no + '"><div id="field_position_' + no + '" class="fields_position"></div>');

	

	if (type == "text") {

		html.find("#field_position_" + no).html('<input type="text" disabled="disabled"  id="field_' + no + '" class="input_fields">');

	} else if (type == "textarea") {

		html.find("#field_position_" + no).html('<textarea disabled="disabled" id="field_' + no + '" rows="5" style="width:100%;"> </textarea>');

	} else if (type == "dropdown") {

		html.find("#field_position_" + no).html('<select disabled="disabled" id="field_' + no + '"><option></option></select>');

	} else if (type == "multiselect") {

		html.find("#field_position_" + no).html('<select disabled="disabled" id="field_' + no + '" multiple="multiple"><option></option></select>');

	} else if (type == "number") {

		html.find("#field_position_" + no).html('<input disabled="disabled" id="field_' + no + '" type="number" class="input_fields" />');

	} else if (type == "checkbox") {

		html.find("#field_position_" + no).html('<div class="radio_wrap"><label>My Label</label><input disabled="disabled" id="field_' + no + '" type="checkbox" class="radio_fields" /><label>My Label</label><input disabled="disabled" id="field_' + no + '" type="checkbox" class="radio_fields" /></div>');

	} else if (type == "hidden") {

		html.find("#field_position_" + no).html('<input type="text" disabled="disabled"  id="field_' + no + '" class="input_fields">');

	} else if (type == "radio") {

		html.find("#field_position_" + no).html('<div class="radio_wrap"><label>My Label</label><input disabled="disabled" id="field_' + no + '" type="radio" class="radio_fields" /><label>My Label</label><input disabled="disabled" id="field_' + no + '" type="radio" class="radio_fields" /></div>');

	} else if (type == "html") {

		html.find("#field_position_" + no).html('<div id="field_' + no + '" class="htmldiv">');

	} else if (type == "sectionbreak") {

		html.find("#field_position_" + no).html('<div style="width:100%;float:left;border: 1px solid #aaaaaa;margin-top:25px;"></div>');

	} else if (type == "username") {
		html.find("#field_label_" + no + " label").html("Username");
		html.find("#field_position_" + no).html('<input type="text" disabled="disabled"  id="field_' + no + '" class="input_fields">');
	}

	if (type == "pagebreak") {

		html.find("#field_position_" + no).html('<img src="'+piereg_wp_pie_register_url+'/images/pagebreak.png" style="max-width:100%;" />');

	} else if (type == "time") {

		html.find("#field_position_" + no).html('<div class="time"><div class="time_fields"><input disabled="disabled" type="text" class="input_fields"><label>HH</label></div><span class="colon">:</span><div class="time_fields"><input disabled="disabled" type="text" class="input_fields"><label>MM</label></div><div id="time_format_field_' + no + '" class="time_fields"><select disabled="disabled"><option>AM</option><option>PM</option></select></div></div>');

	} else if (type == "website") {

		html.find("#field_position_" + no).html('<input disabled="disabled" type="text" id="field_' + no + '" class="input_fields">');

	} else if (type == "name") {

		

		html.find("#field_label_" + no + " label").html("First Name");

		html.find("#field_position_" + no).html('<input disabled="disabled" type="text" class="input_fields">');

		html.find("#field_position_" + no).after('<div id="last_name_field" class="fields_position"> <input disabled="disabled" type="text" class="input_fields"></div>');

		html.find("#field_position_" + no).after('<div class="label_position"><label>Last Name</label></div>');

		html.find("#field_position_" + no).after('<input type="hidden" name="field[' + no + '][label]" id="label_' + no + '" value="First Name">');

	} else if (type == "captcha") {

		html.find("#field_label_" + no + " label").html("Re-Captcha");
		html.find("#field_position_" + no).html('<img id="captcha_img" src="'+piereg_wp_pie_register_url+'/images/recatpcha.jpg" />');

	} else if (type == "math_captcha") {

		html.find("#field_label_" + no + " label").html("Math Captcha");
		html.find("#field_position_" + no).html('<img id="captcha_img" src="'+piereg_wp_pie_register_url+'/images/math_catpcha.png" />');
		

	} else if (type == "upload" || type == "profile_pic") {
		if(type == "profile_pic"){
			html.find("#field_label_" + no + " label").html("Profile Picture");
		}
		else if(type == "upload"){
			html.find("#field_label_" + no + " label").html("Upload");
		}
		html.find("#field_position_" + no).html('<input disabled="disabled" type="file" id="field_' + no + '" class="input_fields">');

	} else if (type == "address") {

		html.find("#field_position_" + no).html('<div id="address_fields" class="address">  <input type="text" class="input_fields">  <label>Street Address</label></div><div class="address" id="address_address2_' + no + '">  <input type="text" class="input_fields">  <label>Address Line 2</label></div><div class="address">  <div class="address2"><input type="text" class="input_fields"><label>City</label>  </div>  <div  class="address2 state_div_' + no + '" id="state_' + no + '"><input type="text" class="input_fields"><label>State / Province / Region</label>  </div>  <div  class="address2 state_div_' + no + '" id="state_us_' + no + '" style="display:none;"><select id="state_us_field_' + no + '"><option value="" selected="selected"></option><option value="Alabama">Alabama</option><option value="Alaska">Alaska</option><option value="Arizona">Arizona</option><option value="Arkansas">Arkansas</option><option value="California">California</option><option value="Colorado">Colorado</option><option value="Connecticut">Connecticut</option><option value="Delaware">Delaware</option><option value="District of Columbia">District of Columbia</option><option value="Florida">Florida</option><option value="Georgia">Georgia</option><option value="Hawaii">Hawaii</option><option value="Idaho">Idaho</option><option value="Illinois">Illinois</option><option value="Indiana">Indiana</option><option value="Iowa">Iowa</option><option value="Kansas">Kansas</option><option value="Kentucky">Kentucky</option><option value="Louisiana">Louisiana</option><option value="Maine">Maine</option><option value="Maryland">Maryland</option><option value="Massachusetts">Massachusetts</option><option value="Michigan">Michigan</option><option value="Minnesota">Minnesota</option><option value="Mississippi">Mississippi</option><option value="Missouri">Missouri</option><option value="Montana">Montana</option><option value="Nebraska">Nebraska</option><option value="Nevada">Nevada</option><option value="New Hampshire">New Hampshire</option><option value="New Jersey">New Jersey</option><option value="New Mexico">New Mexico</option><option value="New York">New York</option><option value="North Carolina">North Carolina</option><option value="North Dakota">North Dakota</option><option value="Ohio">Ohio</option><option value="Oklahoma">Oklahoma</option><option value="Oregon">Oregon</option><option value="Pennsylvania">Pennsylvania</option><option value="Rhode Island">Rhode Island</option><option value="South Carolina">South Carolina</option><option value="South Dakota">South Dakota</option><option value="Tennessee">Tennessee</option><option value="Texas">Texas</option><option value="Utah">Utah</option><option value="Vermont">Vermont</option><option value="Virginia">Virginia</option><option value="Washington">Washington</option><option value="West Virginia">West Virginia</option><option value="Wisconsin">Wisconsin</option><option value="Wyoming">Wyoming</option><option value="Armed Forces Americas">Armed Forces Americas</option><option value="Armed Forces Europe">Armed Forces Europe</option><option value="Armed Forces Pacific">Armed Forces Pacific</option></select><label>State</label>  </div>  <div class="address2 state_div_' + no + '" id="state_canada_' + no + '" style="display:none;"><select id="state_canada_field_' + no + '"><option value="" selected="selected"></option><option value="Alberta">Alberta</option><option value="British Columbia">British Columbia</option><option value="Manitoba">Manitoba</option><option value="New Brunswick">New Brunswick</option><option value="Newfoundland &amp; Labrador">Newfoundland &amp; Labrador</option><option value="Northwest Territories">Northwest Territories</option><option value="Nova Scotia">Nova Scotia</option><option value="Nunavut">Nunavut</option><option value="Ontario">Ontario</option><option value="Prince Edward Island">Prince Edward Island</option><option value="Quebec">Quebec</option><option value="Saskatchewan">Saskatchewan</option><option value="Yukon">Yukon</option></select></select><label>Province</label>  </div></div><div class="address">  <div class="address2"><input type="text" class="input_fields"><label>Zip / Postal Code</label>  </div>  <div id="address_country_' + no + '" class="address2"><select disabled="disabled"><option></option></select><label>Country</label>  </div></div>');

	} else if (type == "phone") {

		html.find("#field_position_" + no).html('<input type="text" id="field_' + no + '" class="input_fields">');

	} else if (type == "date") {

		html.find("#field_position_" + no).html('<div class="time date_format_field" id="datefield_' + no + '">  <div class="time_fields" id="mm_' + no + '">    <input disabled="disabled" type="text" class="input_fields">    <label>MM</label>  </div>  <div class="time_fields" id="dd_' + no + '">    <input disabled="disabled" type="text" class="input_fields">    <label>DD</label>  </div>  <div class="time_fields" id="yy_' + no + '">    <input disabled="disabled" type="text" class="input_fields">    <label>YYYY</label>  </div></div><div class="time date_format_field" id="datepicker_' + no + '"  style="display:none;">  <input type="text" class="input_fields">  <img src="'+ piereg_wp_pie_register_url +'/images/calendar.png" id="calendar_image_' + no + '" style="display:none;" /> </div><div class="time date_format_field" id="datedropdown_' + no + '"  style="display:none;">  <div class="time_fields" id="month_' + no + '"><select disabled="disabled">      <option>Month</option>    </select></div>    <div class="time_fields" id="day_' + no + '"><select disabled="disabled">      <option>Day</option>    </select>  </div>   <div class="time_fields" id="year_' + no + '"><select disabled="disabled">      <option>Year</option>    </select> </div></div>');

	} else if (type == "list") {

		html.find("#field_position_" + no).html('<img src="'+piereg_wp_pie_register_url+'/images/plus.png" /><input type="text" id="field_' + no + '" class="input_fields" disabled="disabled">');

	} else if (type == "invitation") {

		html.find("#field_position_" + no).html('<input type="text" disabled="disabled"  id="invitation_field" class="input_fields">');

	} else if (type == "url" || type == "aim" || type == "yim" || type == "jabber" || type == "phone" || type == "description") {

		var label = piereg('a[name="' + type + '"]').html();

		if (type == "description") {

			html.find("#field_position_" + no).html('<textarea disabled="disabled" id="default_' + type + '" rows="5" style="width:100%;" cols="73"> </textarea>');

		} else {

			html.find("#field_position_" + no).html('<input type="text" disabled="disabled"  id="default_' + type + '" class="input_fields">');

		}

		html.find("#field_label_" + no + " label").html(label);

		//html.find(".edit_btn").remove();

		meta = '<input type="hidden" name="field[' + no + '][id]" value="' + no + '" id="id_' + no + '">';

		meta += '<input type="hidden" name="field[' + no + '][type]" value="default" id="type_' + no + '">';

		meta += '<input type="hidden" name="field[' + no + '][label]" value="' + label + '" id="label_' + no + '">';

		meta += '<input type="hidden" name="field[' + no + '][field_name]" value="' + type + '" id="label_' + no + '">';

	}

	return piereg("<div/>").append(html.clone()).html() + meta;

}



function getOptions(no, optionType) {

	var html = piereg('<div class="advance_fields  sel_options_' + no + '"/>');

	html.append('<label for="display_' + no + '">Display Value</label>');

	html.append('<input type="text" name="field[' + no + '][display][]" id="display_' + no + '" class="input_fields character_fields select_option_display" />');

	html.append('<label for="value_' + no + '">Value</label>');

	html.append('<input type="text" name="field[' + no + '][value][]" id="value_' + no + '" class="input_fields character_fields select_option_value" />');

	html.append('<label>Checked</label>');

	html.append('<input type="' + optionType + '" value="0" id="check_' + no + '" name = "field[' + no + '][selected][]" class="select_option_checked">');

	html.append('<a style="color:white" href="javascript:;" onclick="addOptions(' + no + ',\'' + optionType + '\',piereg(this));">+</a><a style="color:white;font-size: 13px;margin-left: 2px;" href="javascript:;" onclick="piereg(this).parent().remove();">x</a></div>');

	piereg("#field_" + no).append("<option></option>");

	return html;

}



function getEditor() {

	var html = piereg('<div class="advance_fields"/>');

	html.append('<textarea rows="8" id="htmlbox_' + no + '" class="ckeditor" name="field[' + no + '][html]" cols="16"></textarea>');
	
	return html;

}



function changeParaText(id) {

	var textarea = piereg("#paragraph_textarea_" + id).val();

	piereg("#paragraph_" + id).html(textarea);

}

//This will add options for select or multiselect

function addOptions(id, type, elem) {

	var html = piereg("<div/>").append(getOptions(id, type).clone()).html();

	if (!elem) {

		piereg(".sel_options_" + id).last().after(html);

	} else {

		piereg(elem).parent().after(html);

	}

}

//Function to check numeric value

function isNumeric(val) {

	var numberRegex = /^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/;

	if (!numberRegex.test(val) || val < 1) {

		return false;

	}

}



function changeDropdown(elm) {

	var id = elm.attr("id");

	id = id.replace("display_", "");

	id = id.replace("value_", "");

	id = id.replace("check_", "");

	piereg("#field_" + id).html("");

	piereg('.sel_options_' + id).each(function (a, b) {

		var html = piereg('.sel_options_' + id).eq(a).find("input.select_option_display").val();

		var val = piereg('.sel_options_' + id).eq(a).find("input.select_option_value").val();

		piereg('.sel_options_' + id).eq(a).find("input.select_option_checked").val(a);

		var option = piereg("<option/>").attr("value", val).html(html);

		if (piereg('.sel_options_' + id).eq(a).find("input.select_option_checked").is(':checked')) {

			option.attr('selected', 'selected');

		}

		option.appendTo("#field_" + id);

	});

}



function checkEvents(elm, target) {

	if (elm.checked) {

		piereg("#" + target).hide();

		piereg("." + target).hide();

	} else {

		piereg("#" + target).show();

		piereg("." + target).show();

	}

}

var pie_scroll_counter1 = false,pie_scroll_counter2 = false,pie_scroll_counter3 = false;

function add_scroll_dragable_area(id) // for piereg scroll in right side accordion

{

	piereg(id).mCustomScrollbar({

		scrollButtons:{

			enable:true

		}

	});

}

function bindButtons() {

	//Adding Functionalities to right menu

	piereg(".right_menu_heading").live("click", function (e) {

		if (piereg(this).parent().find("ul").is(':visible')) {

			piereg(this).parent().find("ul").slideUp();

			return;

		}

		piereg("ul.picker").slideUp();

		piereg(this).parent().find("ul").slideDown().promise().done(function(){

			 var id = piereg(this).attr("id");

			 if(pie_scroll_counter1 == false && id == "content_1")

			 {

				 add_scroll_dragable_area("ul#content_1");

				 pie_scroll_counter1 = true;

			 }

			 else 

			 if(pie_scroll_counter2 == false && id == "content_2")

			 {

				 add_scroll_dragable_area("ul#content_2");

				 pie_scroll_counter2 = true;

			 }

			 else 

			 if(pie_scroll_counter3 == false && id == "content_3")

			 {

				 add_scroll_dragable_area("ul#content_3");

				 pie_scroll_counter3 = true;

			 }

		 });

		

		e.preventDefault();

	});

	//Adding Functionalities to Edit buttons

	piereg(".edit_btn").live("click", function (e) {

		piereg(this).parents(".fields").find(".fields_main").toggle();

		e.preventDefault();

	});

	//Adding Functionalities to delete (X) buttons

	piereg(".delete_btn").live("click", function () {

		var delId = piereg(this).attr("rel");

		var delType = piereg("#type_" + delId).val();

		var field = piereg("input[name='field[" + delId + "][field_name]']").val();

		piereg(this).parents("li").fadeOut(function () {

			piereg(this).remove();
			/*
				* Add snipt in 2.0.12
			*/
			try{
				if(piereg(this).closest('li').find('textarea.ckeditor').attr('id'))
				{
					var piereg_ckeditorid;
					piereg_ckeditorid = piereg(this).closest('li').find('textarea.ckeditor').attr('id');
					if(piereg_ckeditorid.length > 0){
						CKEDITOR.remove(CKEDITOR.instances[piereg_ckeditorid]);
					}
				}
			}catch(e){
				  console.log(e);
			}
			
			if (delType == "default" || delType == "name" || delType == "address" || delType == "captcha" || delType == "math_captcha" || delType == "invitation" || delType == "username") {

				piereg("ul.controls li a[name='" + field + "']").parent().show();

				piereg("ul.controls li a[name='" + delType + "']").parent().show();

			}

		});

	});

	//Change Label ehile editing label field

	piereg(".field_label").live("keyup", function () {

		var id = piereg(this).attr("id");

		var val = piereg(this).val();

		if (val != "") {

			piereg('#field_' + id + ' label').html(piereg(this).val());

		}

	});
	
	

	piereg(".field_label2").live("keyup", function () {

		var id = piereg(this).attr("id");

		var val = piereg(this).val();

		if (val != "") {

			piereg('#field_' + id + ' label').html(piereg(this).val());

		}

	});

	//Change Field length

	piereg(".field_length").live("keyup", function () {

		var id = piereg(this).attr("id");

		id = id.replace("length_", "");

		var val = piereg(this).val();

		if (val != "") {

			piereg('#field_' + id).attr("maxlength", val);

		} else {

			piereg('#field_' + id).removeAttr("maxlength");

		}

	});

	//Change Field default value

	piereg(".field_default_value").live("keyup", function () {

		var id = piereg(this).attr("id");

		id = id.replace("default_value_", "");

		var val = piereg(this).val();

		piereg('#field_' + id).val(val);

	});

	//Change Field placeholder

	piereg(".field_placeholder").live("keyup", function () {

		var id = piereg(this).attr("id");

		id = id.replace("placeholder_", "");

		var val = piereg(this).val();

		piereg('#field_' + id).attr("placeholder", val);

	});

	//Change Field rows

	piereg(".field_rows").live("keyup", function () {

		var id = piereg(this).attr("id");

		id = id.replace("rows_", "");

		var val = piereg(this).val();

		piereg('textarea#field_' + id).attr("rows", val);

	});

	//Change Field Cols

	piereg(".field_cols").live("keyup", function () {

		var id = piereg(this).attr("id");

		id = id.replace("cols_", "");

		var val = piereg(this).val();

		piereg('textarea#field_' + id).attr("cols", val);

	});

	//Next Button

	piereg(".next_button").live("change", function () {

		if (piereg(this).attr('checked')) {

			var val = piereg(this).val();

			var id = piereg(this).attr("id");

			id = id.replace("next_button_", "");

			id = id.replace("_" + val, "");

			if (val == "text") {

				piereg("#next_button_url_container_" + id).hide();

				piereg("#next_button_text_container_" + id).show();

			} else if (val == "url") {

				piereg("#next_button_url_container_" + id).show();

				piereg("#next_button_text_container_" + id).hide();

			}

		}

	});

	//Previous Button

	piereg(".prev_button").live("change", function () {

		if (piereg(this).attr('checked')) {

			var val = piereg(this).val();

			var id = piereg(this).attr("id");

			id = id.replace("prev_button_", "");

			id = id.replace("_" + val, "");

			if (val == "text") {

				piereg("#prev_button_url_container_" + id).hide();

				piereg("#prev_button_text_container_" + id).show();

			} else if (val == "url") {

				piereg("#prev_button_url_container_" + id).show();

				piereg("#prev_button_text_container_" + id).hide();

			}

		}

	});

	//Calendar Icon

	piereg(".calendar_icon").live("change", function () {

		if (piereg(this).attr('checked')) {

			var val = piereg(this).val();

			var id = piereg(this).attr("id");

			id = id.replace("calendar_icon_", "");

			id = id.replace("_" + val, "");

			if (val == "none") {

				piereg("#icon_url_container_" + id).hide();

				piereg("#calendar_image_" + id).hide();

			} else if (val == "calendar") {

				piereg("#icon_url_container_" + id).hide();

				piereg("#calendar_image_" + id).show();

			} else if (val == "custom") {

				piereg("#icon_url_container_" + id).show();

				piereg("#calendar_image_" + id).hide();

			}

		}

	});

	piereg("select.date_type").live("change", function () {

		var id = piereg(this).attr("id");

		id = id.replace("date_type_", "");

		var val = piereg(this).val();

		piereg("#datefield_" + id).hide();

		piereg("#datepicker_" + id).hide();

		piereg("#datedropdown_" + id).hide();

		piereg("#icon_div_" + id).hide();

		if (val == "datefield") {

			piereg("#datefield_" + id).show();

		} else if (val == "datepicker") {

			piereg("#datepicker_" + id).show();

			piereg("#icon_div_" + id).show();

		} else if (val == "datedropdown") {

			piereg("#datedropdown_" + id).show();

		}

	});

	//Change Date Format

	piereg("select.date_format").live("change", function () {

		var id = piereg(this).attr("id");

		id = id.replace("date_format_", "");

		var val = piereg(this).val();
		if (val.charAt(0) == "m" && val.charAt(val.length - 1) == "y") {

			piereg("#dd_" + id).insertBefore(piereg("#yy_" + id));

			piereg("#mm_" + id).insertBefore(piereg("#dd_" + id));

			piereg("#day_" + id).insertBefore(piereg("#year_" + id));

			piereg("#month_" + id).insertBefore(piereg("#day_" + id));

		} else if (val.charAt(0) == "d" && val.charAt(val.length - 1) == "y") {

			piereg("#mm_" + id).insertBefore(piereg("#yy_" + id));

			piereg("#dd_" + id).insertBefore(piereg("#mm_" + id));

			piereg("#month_" + id).insertBefore(piereg("#year_" + id));

			piereg("#day_" + id).insertBefore(piereg("#month_" + id));

		} else if (val.charAt(0) == "y" && val.charAt(val.length - 1) == "d") {
			piereg("#mm_" + id).insertBefore(piereg("#dd_" + id));
			piereg("#yy_" + id).insertBefore(piereg("#mm_" + id));
			piereg("#month_" + id).insertBefore(piereg("#day_" + id));
			piereg("#year_" + id).insertBefore(piereg("#month_" + id));
		}
	});

	//Change Time Format

	piereg("select.time_format").live("change", function () {

		var id = piereg(this).attr("id");

		id = id.replace("time_type_", "");

		var val = piereg(this).val();

		if (val == "12")

			piereg("#time_format_field_" + id).show();

		else if (val == "24")

			piereg("#time_format_field_" + id).hide();

	});

	//Change Address Type

	piereg("select.address_type").live("change", function () {

		var id = piereg(this).attr("id");

		id = id.replace("address_type_", "");

		var val = piereg(this).val();

		if (val == "International") {

			piereg("#default_country_div_" + id).show();

			piereg("#address_country_" + id).show();

			piereg("#state_" + id).show();

			piereg("#state_us_" + id).hide();

			piereg("#state_canada_" + id).hide();

			piereg("#default_state_div_" + id).hide();

		} else if (val == "United States") {

			piereg("#default_country_div_" + id).hide();

			piereg("#address_country_" + id).hide();

			piereg("#state_" + id).hide();

			piereg("#state_us_" + id).show();

			piereg("#state_canada_" + id).hide();

			piereg("#default_state_div_" + id).show();

			piereg(".can_states_" + id).hide();

			piereg(".us_states_" + id).show();

		} else if (val == "Canada") {

			piereg("#default_country_div_" + id).hide();

			piereg("#address_country_" + id).hide();

			piereg("#state_" + id).hide();

			piereg("#state_us_" + id).hide();

			piereg("#state_canada_" + id).show();

			piereg("#default_state_div_" + id).show();

			piereg(".can_states_" + id).show();

			piereg(".us_states_" + id).hide();

		}

		if (document.getElementById("hide_state_" + id).checked) {

			piereg(".state_div_" + id).hide();

			piereg("#default_state_div_" + id).hide();

		}

	});

	//Change Default Country

	piereg("select.default_country").live("change", function () {

		var id = piereg(this).attr("id");

		id = id.replace("default_country_", "");

		var val = piereg(this).val();

		piereg("#address_country_" + id + " select").html('<option>' + val + '<option>');

	});

	//Change Name Format 

	piereg("select.name_format").live("change", function () {

		var id = piereg(this).attr("id");

		id = id.replace("name_format", "");

		var val = piereg(this).val();

		if (val == "normal") {

			piereg("#field_label" + id + " label").hide();

			piereg('#first_name_field input').appendTo('#first_name_field');

			piereg('#last_name_field input').appendTo('#last_name_field');

		} else if (val == "extended") {

			piereg("#field_label" + id + " label").show();

			piereg('#first_name_field label').appendTo('#first_name_field');

			piereg('#last_name_field label').appendTo('#last_name_field');

		}

	});

	//Adding option Display Value

	piereg("input.select_option_display,input.select_option_value").live("keyup", function () {

		changeDropdown(piereg(this));

	});

	piereg("input.select_option_checked").live("click", function () {

		changeDropdown(piereg(this));

	});

	piereg(".paypal").live("click", function () {

		piereg("#paypal_button").remove();

		piereg("#submit_ul").hide();

		piereg("#submit_ul").after('<ul id="paypal_button"><li class="fields"><div class="fields_options submit_field"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" /></div> <input  name="field[submit][type]" value="paypal" type="hidden" /></li></ul>');

	});

	piereg(".submit_button").live("click", function () {

		piereg("#paypal_button").remove();

		piereg("#submit_ul").show();

	});

	piereg("ul.controls li a.default").live("click", function () {

		piereg(this).parent().hide();

	});

	piereg("ul.controls li a.default").each(function () {

		var type = piereg(this).attr("name");

		if (document.getElementById("default_" + type))

			piereg(this).parent().hide();

	});

	//Allow only numeric on Length, Rows, Cols, Length Field 

	piereg(".numeric").live("keydown", function (event) {

		if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 ||

			// Allow: Ctrl+A

			(event.keyCode == 65 && event.ctrlKey === true) ||

			// Allow: home, end, left, right

			(event.keyCode >= 35 && event.keyCode <= 39)) {

			// let it happen, don't do anything

			return;

		} else {

			// Ensure that it is a number and stop the keypress

			if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105)) {

				event.preventDefault();

			}

		}

	});

	//Submit Button Properties Icon

	piereg(".reg_success").live("change", function () {

		piereg(".submit_meta").hide();

		piereg(".reg_success").each(function (index, element) {

			if (piereg(this).attr('checked')) {

				var val = piereg(this).val();

				piereg(".submit_meta_" + val).show();

			}

		});

	});

	//default state

	piereg(".default_state").live("change", function () {

		var id = piereg(this).attr("id");

		id = id.replace("can_states_", "");

		id = id.replace("us_states_", "");

		var val = piereg(this).val();

		piereg("#state_canada_field_" + id).html('<option>' + val + '</option>');

		piereg("#state_us_field_" + id).html('<option>' + val + '</option>');

	});

	piereg(".hide_state").live("change", function () {

		var id = piereg(this).attr("id");

		id = id.replace("hide_state_", "");

		var val = piereg(this).val();

		if (piereg(this).attr('checked')) {

			piereg(".state_div_" + id).hide();

			piereg("#default_state_div_" + id).hide();

		} else {

			var val = piereg("#address_type_" + id).val();

			if (val == "International") {

				piereg("#default_country_div_" + id).show();

				piereg("#address_country_" + id).show();

				piereg("#state_" + id).show();

				piereg("#state_us_" + id).hide();

				piereg("#state_canada_" + id).hide();

				piereg("#default_state_div_" + id).hide();

			} else if (val == "United States") {

				piereg("#default_country_div_" + id).hide();

				piereg("#address_country_" + id).hide();

				piereg("#state_" + id).hide();

				piereg("#state_us_" + id).show();

				piereg("#state_canada_" + id).hide();

				piereg("#default_state_div_" + id).show();

				piereg(".can_states_" + id).hide();

				piereg(".us_states_" + id).show();

			} else if (val == "Canada") {

				piereg("#default_country_div_" + id).hide();

				piereg("#address_country_" + id).hide();

				piereg("#state_" + id).hide();

				piereg("#state_us_" + id).hide();

				piereg("#state_canada_" + id).show();

				piereg("#default_state_div_" + id).show();

				piereg(".can_states_" + id).show();

				piereg(".us_states_" + id).hide();

			}

		}

	});

	piereg("#confirm_email").live("change", function () {

		if (piereg(this).attr('checked')) {
			/*piereg("#confirm_email_label_1").show();*/
			piereg(".confrim_email_label2").fadeIn(1000);
			piereg("#confirm_email_field_1").fadeIn(1000);
		} else {
			/*piereg("#confirm_email_label_1").hide();*/
			piereg(".confrim_email_label2").fadeOut(1000);
			piereg("#confirm_email_field_1").fadeOut(1000);
		}

	});

	piereg(".right_menu_heading").parent().find("ul").slideUp();

}

piereg(document).ready(function () {

	//Hiding Advanced Options By default	

	piereg(".fields_main").hide();

	//Click functions on control

	piereg(".controls li a").click(function () {

		dragType = piereg(this).attr("name");

		piereg(this).parent().addClass("fields");

		

		

		var response = defaultMeta[dragType];

		endHtml		 = getStyle(dragType);

		if(response)

		{

			

			response = response.split("%d%").join(no);			

			endHtml 	+= response;

			endHtml		+= '<input value = "'+dragType+'" type="hidden"  name="field['+no+'][type]" id="type_'+no+'">';

		

		}

		if (endHtml) 

		{

			endHtml = '<li class="fields">' + endHtml + '</li>';

			piereg("#elements").append(endHtml);

			if (dragType == "html") {

				CKEDITOR.replace("htmlbox_" + no, {});

			}

			piereg(".swap_class").trigger("change");

			no++;

			dragType = "";

			endHtml = "";

		}

		

		

		

	});

	piereg("form#formeditor").submit(function (e) {

		piereg(".field_label").each(function (index, element) {
			
			/*if (dragType == "url" || dragType == "aim" || dragType == "yim" || dragType == "jabber"  || dragType == "description")
			{
				
			}*/

			if (piereg.trim(piereg(this).val()) == '') {

				var id = piereg(this).attr("id");

				id = id.replace("label_", "");

				var type = piereg("#type_" + id).val();
				
				if(type != "html" && type != 'sectionbreak')
					piereg(this).val(type);

			}

		});

		

		piereg(".greaterzero").each(function (index, element) 

		{

			if (piereg.trim(piereg(this).val()) == '' || piereg.trim(piereg(this).val()) < 1 || piereg.trim(piereg(this).val()) == "0") {

				

				piereg(this).val("1");

			}	

		});

		

		

	});

	//This will handle sorting and after drop changes		

	piereg("#elements").droppable({

		// accept: ".controls li p",

		drop: function (event, ui) {

			ui.draggable.addClass("fields");

			if (endHtml) {			

			

			endHtml += fieldMeta;	

			fieldMeta = "";

			ui.draggable.html(endHtml);				

				

				

				no++;

				piereg(".swap_class").trigger("change")

				if (dragType == "invitation" || dragType == "name" || dragType == "captcha" || dragType == "math_captcha" || dragType == "aim" || dragType == "yim" || dragType == "jabber" || dragType == "description" || dragType == "url" || delType == "username") {

					piereg('ul.controls li a[name="' + dragType + '"]').parent().hide();

				}

				dragType = "";

				endHtml = "";

				

			}

		}

	}).sortable({

		cursor: 'crosshair',

		placeholder: "sort-state-highlight",

		start: function (event, ui) {

			for (var i in CKEDITOR.instances) {

				CKEDITOR.instances[i].destroy();

			}

		},

		stop: function (event, ui) {

			piereg('.ckeditor').each(function () {

				var id = piereg(this).attr("id");

				CKEDITOR.replace(id, {});

			});

			dragType = "";

			endHtml = "";

		}

	});

	//Selecting and Dragging control from the list

	piereg(".controls li").draggable({

		connectToSortable: "#elements",

		helper: "clone",

		revert: "invalid"

	});

	

	//This will change the html of a control while dragging

	piereg(".controls li").on("dragstart", function (event, ui) {

		ui.helper.attr("class", "");

		fieldMeta	= "";

		dragType = ui.helper.find("a").attr("name");

		//currHTML = ui.helper.html();

		endHtml = getStyle(dragType);

		

		if (dragType == "url" || dragType == "aim" || dragType == "yim" || dragType == "jabber"  || dragType == "description")

		{
			fieldMeta 	  = defaultMeta[dragType];		

			fieldMeta	  = fieldMeta.split("%d%").join(no);

		}

		else

		{
			fieldMeta 	  = defaultMeta[dragType];		

			fieldMeta	  = fieldMeta.split("%d%").join(no);	

		}

		

		

		fieldMeta	  += '<input value = "'+dragType+'" type="hidden"  name="field['+no+'][type]" id="type_'+no+'">';		

		

		//console.log(fieldMeta);

		

		if (endHtml) {

			ui.helper.addClass("fields");

		}

	});

	

	

	

	//This will change the html of a control while dragging

	piereg(".controls li").on("drag", function (event, ui) {

		ui.helper.attr("class", "");	

		endHtml = getStyle(dragType);		

		if (endHtml) {

			ui.helper.addClass("fields");

		}

	});

	bindButtons();

	showHideReset();

});

piereg(window).load(function () {

	piereg("select.date_type").trigger("change");

	piereg("select.date_format").trigger("change");

	piereg(".calendar_icon").trigger("change");

	piereg(".swap_class").trigger("change");

	piereg(".submit_meta").hide();

	piereg(".reg_success").trigger("change");

	piereg("select.address_type").trigger("change");

	piereg(".next_button").trigger("change");

	piereg(".prev_button").trigger("change");

	//piereg(".name_format").trigger("change");

});



function fillValues(data, num) //Function to fill meta data

{

	var field = unserialize(data);

	var mt = "field[" + num + "][type]";

	var maintype = piereg('*[name="' + mt + '"]').val();

	for (index in field) {

		var fieldname = "field[" + num + "][" + index + "]";

		var fieldvalue = field[index];

		var fieldtag = piereg('*[name="' + fieldname + '"]').prop('tagName');

		var fieldtype = piereg('*[name="' + fieldname + '"]').prop('type');

		if (fieldtag == "SELECT" || fieldtag == "TEXTAREA" || (fieldtag == "INPUT" && (fieldtype == "text" || fieldtype == "hidden"))) //For textfields , select textareas

		{

			piereg('*[name="' + fieldname + '"]').val(fieldvalue);

		} else if (fieldtag == "INPUT" && (fieldtype == "radio" || fieldtype == "checkbox")) //For Radio && checkbox

		{

			if (fieldvalue != "") {

				piereg('*[name="' + fieldname + '"][value="' + fieldvalue + '"]').attr('checked', 'checked');

			} else {

				piereg('*[name="' + fieldname + '"]').attr('checked', 'checked');

			}

		} else if ((maintype == "dropdown" || maintype == "multiselect" || maintype == "radio" || maintype == "checkbox") && index == "display") {

			if (maintype == "dropdown" || maintype == "radio")

				var subtype = "radio";

			else if (maintype == "multiselect" || maintype == "checkbox")

				var subtype = "checkbox";

			for (a = 1; a < field["display"].length; a++) {

				addOptions(num, subtype);

			}

			for (a = 0; a < field["display"].length; a++) {

				var fieldname = "field[" + num + "][value][]";

				piereg('input[name="' + fieldname + '"]').eq(a).val(field['value'][a]);

				var fieldname = "field[" + num + "][display][]";

				piereg('input[name="' + fieldname + '"]').eq(a).val(field['display'][a]);

				var fieldname = "field[" + num + "][selected][]";

				piereg('input[name="' + fieldname + '"]').eq(a).val(a);

			}

			if (field["selected"]) {

				//If Item is selected

				var fieldname = "field[" + num + "][selected][]";

				fieldname = piereg('input[name="' + fieldname + '"]');

				for (a = 0; a < fieldname.length; a++) {

					for (b = 0; b < field['selected'].length; b++) {

						if (a == field['selected'][b]) {

							fieldname.eq(a).attr("checked", "checked");

						}

					}

				}

			}

		}

	}

}



function createckeditor() {

	piereg(".ckeditor").ckeditor();

}



function swapClass(val) {

	if (val == "top") {

		piereg(".fields_position").addClass("fields_position_top");

		piereg(".fields_position_top").removeClass("fields_position");

		piereg(".label_position").addClass("label_position_top");

		piereg(".label_position_top").removeClass("label_position");

	} else if (val == "left") {

		piereg(".fields_position_top").addClass("fields_position");

		piereg(".fields_position").removeClass("fields_position_top");

		piereg(".label_position_top").addClass("label_position");

		piereg(".label_position").removeClass("label_position_top");

	}

}

function showHideReset()

{

	var val = piereg("#show_reset").val();

	var elm = piereg("#reset_btn");

	if(val == 0 )

		elm.hide();

	else

		elm.show();	

}


// Declare jQuery Object to $.
$ = jQuery;