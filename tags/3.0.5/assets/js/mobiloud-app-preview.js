jQuery(document).ready(function() {

    checkHomeScreenSelect();
    alignPreviewHolderLogo();
    
    jQuery("input[name='homepagetype']").click(function() {
        checkHomeScreenSelect();
    });

    var _custom_media = true,
            _orig_send_attachment = wp.media.editor.send.attachment;

    jQuery('#ml_preview_upload_image_button').click(function(e) {
        var send_attachment_bkp = wp.media.editor.send.attachment;
        var button = jQuery(this);
        var id = button.attr('id').replace('_button', '');
        _custom_media = true;
        wp.media.editor.send.attachment = function(props, attachment) {
            if (_custom_media) {
                jQuery("#" + id).val(attachment.url);  
                loadPreviewImage();                
                ml_loadPreview();
            } else {
                return _orig_send_attachment.apply(this, [props, attachment]);
            }            
        };

        wp.media.editor.open(button);
        return false;
    });
    
    jQuery(".ml-preview-image-remove-btn").click(function(e) {
        e.preventDefault();
        var confirmRemove = confirm('Are you sure you want to remove the image?');
        if(confirmRemove) {
            jQuery(".ml-preview-upload-image-row").hide();
            jQuery(".ml-preview-image-holder img").attr('src', '');   
            jQuery("#ml_preview_upload_image").val('');
            ml_loadPreview();
        }
    });
    
    jQuery("#ml_preview_upload_image").keyup(function() {
        loadPreviewImage();
        ml_loadPreview();
    });
    
    jQuery("#ml_preview_os_ios").click(function() {
        ml_loadPreview();
    });
    jQuery("#ml_preview_os_android").click(function() {
        ml_loadPreview();
    });

    var link_color = jQuery('#ml_preview_theme_color');
    link_color.wpColorPicker({
        change: function(event, ui) {
            pickColor(link_color.wpColorPicker('color'));
            _veroq.push(['track', "menubar_color_change"]);
            Intercom("trackUserEvent", "menubar_color_change");
            ml_loadPreview();
        },
        clear: function() {
            pickColor('');
        }
    });
    jQuery('#ml_preview_theme_color').click(toggle_text);

    toggle_text();
    
    ml_loadPreview();
});

var alignPreviewHolderLogo = function() {
    var imageHolder = jQuery(".ml-preview-image-holder");
    var image = jQuery("img", imageHolder);
    if(imageHolder.length && image.length) {
        if(image.height > image.width) {
            image.height = '100%';
            image.width = 'auto';
        }
    }   
};

var checkHomeScreenSelect = function() {
    if(jQuery("#ml_home_page_enabled").is(':checked') || jQuery("#ml_home_url_enabled").is(':checked')) {
        jQuery(".ml-home-screen-label").show();
    } else {
        jQuery(".ml-home-screen-label").hide();
    }
};

var articleScrol;
var loadIScroll = function() {
    articleScroll = new IScroll('.ml-preview-article-list', {
        scrollbars: true,
        fadeScrollbars: true,
        mouseWheel: true
    });
};

var default_color = '1e73be';

function pickColor(color) {
    jQuery('#ml_preview_theme_color').val(color);
}
function toggle_text() {
    link_color = jQuery('#ml_preview_theme_color');
    if(link_color.length) {
        if (link_color.val() === '' || '' === link_color.val().replace('#', '')) {
            link_color.val(default_color);
            pickColor(default_color);
        } else {
            pickColor(link_color.val());
        }
    }
}

var alignPreviewLogo = function(logo) {
    var logoHeight = jQuery(logo).height();
    var logoWidth = jQuery(logo).width();
    var holderHeight = jQuery(logo).parent().height();
    var holderWidth = jQuery(logo).parent().width();
    jQuery(logo).css('margin-top', (holderHeight - logoHeight) / 2);
    if(jQuery(".ml-preview").hasClass('ios')) {
        jQuery(logo).css('margin-left', (holderWidth - logoWidth) / 2);
    } else {
        jQuery(logo).css('margin-left', '0');
    }
};

var ml_loadPreview = function() {
    var data = {
        action: 'ml_preview_app_display',
        ml_preview_upload_image: jQuery("#ml_preview_upload_image").val(),
        ml_preview_theme_color: jQuery("#ml_preview_theme_color").val(),
        ml_preview_os: jQuery("input[name='ml_preview_os']:checked").val()
    };
    jQuery(".ml-preview-app").append(jQuery("#ml_preview_loading"));

    jQuery.post(ajaxurl, data, function(response) {
        //saving the result and reloading the div
        jQuery(".ml-preview-app").html(response).fadeIn().slideDown(500, function() {
            jQuery('.ml-preview-logo').load(function() {
                alignPreviewLogo(jQuery('.ml-preview-logo'));
            });
            jQuery('.ml-preview-img').load(function() {
                var cropWidth = 253;
                if(jQuery("input[name='ml_preview_os']:checked").val() === 'android') {
                    cropWidth = 287;
                }
                
                cropPostImages(cropWidth);
            });
           
            
            loadIScroll();
        });
        
        
    });			
};

var cropPostImages = function(width) {
    jQuery('img.ml-preview-img').resizecrop({
      width:width,
      height:100,
      vertical:"middle"
    });  
};

var loadPreviewImage = function() {
    if(jQuery("#ml_preview_upload_image").val().length > 0) {
        jQuery(".ml-preview-upload-image-row").show();
        jQuery(".ml-preview-image-holder img").attr('src', jQuery("#ml_preview_upload_image").val());    
        alignPreviewHolderLogo();
    } else {
        jQuery(".ml-preview-upload-image-row").hide();
    } 
};