jQuery(document).ready ($) ->
	$post_div = $('#ml_admin_post')
	$post_page = $('#ml_admin_post_page')
	$customization_select = $post_page.find('select').first()
	preview_window = null
	$preview_window = null

	$('#ml_admin_post_page_preview .open_preview_btn').click ->
		src = $('#preview_popup_post_select').val()
		if $(@).parent().hasClass('iphone5s-device-btn')
			openPreviewPopup(src,'iphone')
		else if $(@).parent().hasClass('ipadmini-device-btn')
			openPreviewPopup(src,'ipad')

	$('#ml_admin_post_page_preview select#preview_popup_post_select').change ->
		src = $(@).val()
		$preview_window?.find('iframe').attr('src',src)

	openPreviewPopup = (src,device='iphone',width=465,height=894)->
		$preview_content = $('#preview_popup_content .iphone5s_device')

		if device == 'ipad'
			$preview_content = $('#preview_popup_content .ipadmini_device')
			width = 540
			height = 750

		#initialize preview window
		preview_css_links = $(document).find('link')
		preview_window = window.open("", "popupWindow", "width=#{width}, height=#{height}, scrollbars=no, location=no,titlebar=no,toolbar=no,status=no,menubar=no,resizable=no");
		$preview_window = $(preview_window.document.body)
		$preview_window.html("")

		preview_css_links.each (idx,e)->
			$preview_window.append($(e).clone())


		$preview_window.append($preview_content.clone())
		$preview_window.css('width',width);
		$preview_window.css('height',height);
		$preview_window.css('min-width','auto');
		$preview_window.css('min-height','auto');
		#load the iframe
		console.log "src: #{src}"
		$preview_window.find('iframe').attr('src',src)
 		
	$customization_select.change ->
		action_name = $(@).find('option:selected').val()
		code = $post_page.find("textarea[name='#{action_name}']").val()
		code = stripslashes(code)
		$('#ml_admin_post_textarea').val(code)

	saveCode = (before,after) ->
		before?()
		code = $('#ml_admin_post_textarea').val()
		data = 
			action: 'ml_admin_post_save_code'
			customization_name: $customization_select.find('option:selected').val()
			code: code

		$.post ajaxurl, data, (response) ->
			action_name = $customization_select.find('option:selected').val()
			$post_page.find("textarea[name='#{action_name}']").val(code)

			after?()
			win.location.reload()			

	$post_div.find("input[type='submit']").click ->
		label = $(@).data('label')
		saving_label = $(@).data('saving-label')

		saveCode(	
			=>
				$(@).val(saving_label).attr('disabled', true)
			=>
				$(@).val(label).attr('disabled', false)
		)

	$checkboxes = $("#ml_admin_post_options input[type='checkbox']")
	saveOptions = ->
		data = 
			action: 'ml_admin_post_save_options'

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
	#init checkboxes
	for e in $checkboxes
		checked = $(e).data('checked')
		if checked
			$(e).attr('checked',true)
		else
			$(e).attr('checked',false)
			$(e).removeAttr('checked')

	$("#ml_admin_post_options input[type='submit']").click ->
		saveOptions()
