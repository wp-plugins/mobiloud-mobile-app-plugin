makePageSpinner = ->
	opts =
		lines: 10
		length: 10
		width: 5
		radius: 10
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
	#data is a json object
	mobiloudLazyLoadContent=(data)->
		$body_content = $(data.body_content)
		$('#lazy_body').hide().html($body_content).show()
		mobiloud_mobile_init()

	$.ajax
		type: 'GET'
		url: url
		contentType: 'application/json'
		dataType: 'jsonp'
		jsonpCallback: 'mobiloudLazyLoadContent'
		data: 
			post_id: post_id
		error: (data,status,error) ->
			console.log error			
		success: (data)->
			console.log "lazy_load: success"
			#mobiloudLazyLoadContent(data)
		complete:(jqXHR)->
			console.log "lazy_load: complete (status #{jqXHR.status})"
			if jqXHR.status != 200
				eval(jqXHR.responseText)
				