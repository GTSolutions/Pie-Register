var piereg = jQuery.noConflict();
function set_add_del_code(){
	piereg('.remove_code').show();
	piereg('.add_code').hide();
	piereg('.add_code:last').show();
	piereg(".code_block:only-child > .remove_code").hide();
}
function selremcode(clickety){
	piereg(clickety).parent().remove(); 
	set_add_del_code(); 
	return false;
}
function seladdcode(clickety){
	piereg('.code_block:last').after(
    	piereg('.code_block:last').clone());
	piereg('.code_block:last input').attr('value', '');

	set_add_del_code(); 
	return false;
}
function set_add_del(){
	piereg('.remove_row').show();
	piereg('.add_row').hide();
	piereg('.add_row:last').show();
	piereg(".row_block:only-child > .remove_row").hide();
}
function selrem(clickety){
	piereg(clickety).parent().parent().remove(); 
	set_add_del(); 
	return false;
}
function seladd(clickety){
	piereg('.row_block:last').after(
    	piereg('.row_block:last').clone());
	piereg('.row_block:last input.custom').attr('value', '');
	piereg('.row_block:last input.extraops').attr('value', '');
	var custom = piereg('.row_block:last input.custom').attr('name');
	var reg = piereg('.row_block:last input.reg').attr('name');
	var profile = piereg('.row_block:last input.profile').attr('name');
	var req = piereg('.row_block:last input.required').attr('name');
	var fieldtype = piereg('.row_block:last select.fieldtype').attr('name');
	var extraops = piereg('.row_block:last input.extraops').attr('name');
	var c_split = custom.split("[");
	var r_split = reg.split("[");
	var p_split = profile.split("[");
	var q_split = req.split("[");
	var f_split = fieldtype.split("[");
	var e_split = extraops.split("[");
	var split2 = c_split[1].split("]");
	var index = parseInt(split2[0]) + 1;
	var c_name = c_split[0] + '[' + index + ']';
	var r_name = r_split[0] + '[' + index + ']';
	var p_name = p_split[0] + '[' + index + ']';
	var q_name = q_split[0] + '[' + index + ']';
	var f_name = f_split[0] + '[' + index + ']';
	var e_name = e_split[0] + '[' + index + ']';
	piereg('.row_block:last input.custom').attr('name', c_name);
	piereg('.row_block:last input.reg').attr('name', r_name);
	piereg('.row_block:last input.profile').attr('name', p_name);
	piereg('.row_block:last input.required').attr('name', q_name);
	piereg('.row_block:last select.fieldtype').attr('name', f_name);
	piereg('.row_block:last input.extraops').attr('name', e_name);
	set_add_del(); 
	return false;
}
function toggleVerificationType(first_type,second_type){
	if(piereg(second_type).is(":checked")){
		piereg(second_type).attr("checked",false);
	}
}

// Declare jQuery Object to $.
$ = jQuery;