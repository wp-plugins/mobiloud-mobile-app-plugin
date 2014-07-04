jQuery(document).ready ($) ->
	$license_div = $('#ml_admin_license')
	$license_page = $('#ml_admin_license_page')

	$license_div.find("input[type='submit']").click ->
		
		saving_label = $(@).data('saving-label')
		$(@).val(saving_label).attr('disabled', true)
		$license_page.css('opacity','0.5')

		$input_api_key = $license_div.find("input[name='api_key']").first()
		$input_secret_key = $license_div.find("input[name='secret_key']").first()

		data = 
			action: 'ml_admin_license_keys'
			ml_api_key: $input_api_key.val()
			ml_secret_key: $input_secret_key.val()

		$.post ajaxurl, data, (response) =>
			apply_label = $(@).data('apply-label')
			$license_page.html(response).fadeIn()
			
			$(@).attr('disabled', false)
			$(@).val apply_label
			$license_page.css("opacity","1.0")