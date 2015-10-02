
jQuery(document).ready(function(){

	var payment = "", image = "";

	jQuery("#select_payment_method").change(function(){

		if(jQuery(this).val() != "")

		{

			if(jQuery(this).val() == "authorizeNet")

			{

				payment = '<div id="show_payment_method"><h3>Credit Card Information</h3><ul id="pie_register"><li class="fields"><div class="fieldset"><label for="x_card_num">Card Number</label><input type="text" required="true" aria-invalid="false" aria-required="true" value="" autocomplete="off" maxlength="16" name="x_card_num" id="x_card_num" class="input_fields" required="true"><span class="required">*</span><span class="field_note">(enter number without spaces or dashes)</span></div></li><li class="fields"><div class="fieldset"><label for="x_exp_date">Expiration Date</label><input type="text" required="true" aria-invalid="false" aria-required="true" value="" autocomplete="off" maxlength="4" name="x_exp_date" id="x_exp_date" class="input_fields" required="true"><span class="required">*</span><span class="field_note">(mmyy)</span></div></li></ul></div>'

			}

			jQuery("#show_payment_method").html(payment);

			jQuery("#show_payment_method_image").html(image);

		}

		else

		{

			jQuery("#show_payment_method").html("");

		}

	});

});

			

			

function payment_conditions(value_)

{

	

}