$(document).ready ->
	mobiloud_mobile_init()

window.mobiloud_mobile_init = ->
	$('#loading_spinner').remove()

	#creating image containers and preload it
	#this is done because we need a parent to center the image
	$('img, embed, object, video, iframe').wrap("<div class='media-container'></div>")

	loadImages = ->
		for img in $('img')
			$(img).removeAttr('width').removeAttr('height').removeAttr('class')
			$(img).closest("[id^=attachment]").removeAttr('style').removeAttr('class')
			$(img).show()

			
	loadImages()

	makeFluidMedia()

	#wrap the text outside tags
	$filtered_content = $('.post-content').contents().filter ->
		return @.nodeType == 3
	$filtered_content.wrap("<div class='post-text'></div>")

	#spinner
	makePageSpinner()

updateMedia = ->
	$fluidEl = $('.post-content')
	newWidth = $fluidEl.width() - 30
	$medias = $('embed, object, video, iframe')
	console.log newWidth
	$medias.each ->
		$el = $(@)
		$el.attr('width',newWidth)
		$el.attr('height',newWidth * $el.data('aspect-ratio'))

console.log 'here'
makeFluidMedia = ->
	$fluidEl = $('.post-content')

	$medias = $('embed, object, video, iframe')
	$medias.each ->
		$(@).data('aspect-ratio',@.height/@.width)
		$(@).removeAttr('width')
		$(@).removeAttr('height')

	$(window).resize ->
		updateMedia()

	$(window).resize()
	updateMedia()

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
	target = document.getElementById('loading_spinner');
	spinner = new Spinner(opts).spin(target);

