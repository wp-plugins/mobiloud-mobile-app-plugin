makePageSpinner = ->
	opts =
		lines: 13
		length: 20
		width: 10
		radius: 30
		corners: 1
		rotate: 0
		direction: 1
		color: '#000'
		speed: 1
		trail: 60
		shadow: false
		hwaccel: false
		className: 'spinner'
		zIndex: 2e9
		top: 'auto'
		left: 'auto'
	target = document.getElementById('lazy_content_spinner')
	spinner = new Spinner(opts).spin(target)


$(document).ready ->
	$lazy_info = $('#mobiloud_lazy_load')
	url = $lazy_info.data('url')
	post_id = $lazy_info.data('post_id')

	makePageSpinner()

	$.ajax
		type: 'GET'
		url: url
		contentType: 'application/json'
		dataType: 'jsonp'
		data: 
			post_id: post_id
		error: (data,status,error) ->
			console.log error			
		success: (data)->
			$body_content = $(data.body_content)
			$('#lazy_body').hide().html($body_content).show()
			mobiloud_mobile_init()
		complete:(jqXHR)->
			if jqXHR.status != 200
				data = $.parseJSON(jqXHR.responseText)
				$body_content = $(data.body_content)
				$('#lazy_body').html($body_content).show()
				mobiloud_mobile_init()