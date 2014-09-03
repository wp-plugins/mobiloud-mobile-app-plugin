jQuery(document).ready ($)->
	$admob_div = $('#ml_admin_banners_admob')
	$html_div = $('#ml_admin_banners_html')

	$page = $('#ml_admin_banners')
	$select = $page.find('select#ml_admin_banners_service_select');
	$select.change ->
		v = $(@).val()
		$admob_div.hide()
		$html_div.hide()
		
		if v == 'admob'
			$admob_div.show()
		else if v == 'html'
			$html_div.show()
	
	# change selected
	$select.val($select.data('selected')).trigger('change')

	$('#ml_admin_banners_save_btn').click ->
		saving_label = $(@).data('saving-label')
		$(@).val(saving_label).attr('disabled', true)

		data = 
			action: 'ml_admin_banners_save'
			banners_service: $select.val()
			ml_banners_admob_phone_id: $('#admob_phone_id').val()
			ml_banners_admob_tablet_id: $('#admob_tablet_id').val()
			ml_banners_html_phone_top: $('#html_phone_top').val()
			ml_banners_html_phone_article_top: $('#html_phone_article_top').val()
			ml_banners_html_phone_article_bottom: $('#html_phone_article_bottom').val()			
			ml_banners_html_tablet_top: $('#html_tablet_top').val()
			ml_banners_html_tablet_article_top: $('#html_tablet_article_top').val()
			ml_banners_html_tablet_article_bottom: $('#html_tablet_article_bottom').val()

		$.post ajaxurl, data, (response) =>
			label = $(@).data('label')			
			$(@).attr('disabled', false)
			$(@).val label
