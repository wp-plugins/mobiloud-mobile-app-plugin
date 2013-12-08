$(document).ready ->
	$lazy_info = $('#mobiloud_lazy_load')
	url = $lazy_info.data('url')
	post_id = $lazy_info.data('post_id')

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