jQuery(document).ready ($) ->
	$subscriptions_div = $('#ml_admin_subscriptions')

	$checkboxes = $("#ml_admin_subscriptions_options input[type='checkbox']")
	saveOptions = ->
		data = 
			action: 'ml_admin_subscriptions_save_options'
		
		for e in $checkboxes
			name = $(e).attr('name')
			checked = $(e).is(':checked')
			data[name] = checked

		console.log data
		$.ajax 
			url: ajaxurl
			data: data
			type: 'POST'
			dataType: 'html'
			success: (response)->
				#console.log response
	
	$("#ml_admin_subscriptions_options input[type='submit']").click ->
		saveOptions()

	#init checkboxes
	for e in $checkboxes
		checked = $(e).data('checked')
		if checked
			$(e).attr('checked',true)
		else
			$(e).attr('checked',false)
			$(e).removeAttr('checked')
