jQuery(document).ready(function($){
	$('ul.ml-home-menu').sortable();
	$('ul.ml-home-menu').disableSelection();

	function mobiloudHomeMenuCreateItemFormInit(){
		var $form = $('#ml_create_home_menu_item');
		var $submit = $form.find("input[type='submit']");
		var $select_type = $form.find("select[name='menu[type]']");
		var $form_fields = $form.find('.ml-home-menu-fields');
		var $page_div = $form.find('.page');
		var $image_hidden_field = $form.find(".image input[name='menu[image]']");
		var $img_thumb = $form.find(".image img");

		$submit.click(function(){
			var title = $form.find("input[name='menu[title]']").val();
			var menu_type = $form.find("select[name='menu[type]']").val();

			var data = {
				action: 'ml_menu_add_item',
				title: title,
				menu_type: menu_type
			};
			$.post(ajaxurl,data,function(response){
				$('.ml-home-menu').append(response);
			});

		});

		//select type
		$select_type.change(function(){
			var t = $(this).val()
			if(t == 'page') {
				$form_fields.show();
				$page_div.show();
			}
		});

		$('#ml_create_home_menu_item .image .upload-btn').click(function() {
 			tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true&step=1&height=700&width=700');
 			return false;
		});

		window.send_to_editor = function(html) {
 			imgurl = $('img',html).attr('src');
 			$image_hidden_field.val(imgurl);
 			image = new Image();
 			image.onload = function(){
 				//checks if the image is squared
 				var w = image.width;
 				var h = image.height;
	 			$img_thumb.attr('src',imgurl);
 			}
 			image.src = imgurl;
			tb_remove();
		}

	}

	$('#menu-tabs').tabs({active:0});

	mobiloudHomeMenuCreateItemFormInit();


});

