// Generated by CoffeeScript 1.6.3
(function() {
  jQuery(document).ready(function($) {
    var $checkboxes, $customization_select, $post_div, $post_page, $preview_window, checked, e, openPreviewPopup, preview_window, saveCode, saveOptions, _i, _len;
    $post_div = $('#ml_admin_post');
    $post_page = $('#ml_admin_post_page');
    $customization_select = $post_page.find('select').first();
    preview_window = null;
    $preview_window = null;
    $('#ml_admin_post_page_preview .open_preview_btn').click(function() {
      var src;
      src = $('#preview_popup_post_select').val();
      if ($(this).parent().hasClass('iphone5s-device-btn')) {
        return openPreviewPopup(src, 'iphone');
      } else if ($(this).parent().hasClass('ipadmini-device-btn')) {
        return openPreviewPopup(src, 'ipad');
      }
    });
    $('#ml_admin_post_page_preview select#preview_popup_post_select').change(function() {
      var src;
      src = $(this).val();
      return $preview_window != null ? $preview_window.find('iframe').attr('src', src) : void 0;
    });
    openPreviewPopup = function(src, device, width, height) {
      var $preview_content, preview_css_links;
      if (device == null) {
        device = 'iphone';
      }
      if (width == null) {
        width = 465;
      }
      if (height == null) {
        height = 894;
      }
      $preview_content = $('#preview_popup_content .iphone5s_device');
      if (device === 'ipad') {
        $preview_content = $('#preview_popup_content .ipadmini_device');
        width = 540;
        height = 750;
      }
      preview_css_links = $(document).find('link');
      preview_window = window.open("", "popupWindow", "width=" + width + ", height=" + height + ", scrollbars=no, location=no,titlebar=no,toolbar=no,status=no,menubar=no,resizable=no");
      $preview_window = $(preview_window.document.body);
      $preview_window.html("");
      preview_css_links.each(function(idx, e) {
        return $preview_window.append($(e).clone());
      });
      $preview_window.append($preview_content.clone());
      $preview_window.css('width', width);
      $preview_window.css('height', height);
      $preview_window.css('min-width', 'auto');
      $preview_window.css('min-height', 'auto');
      console.log("src: " + src);
      return $preview_window.find('iframe').attr('src', src);
    };
    $customization_select.change(function() {
      var action_name, code;
      action_name = $(this).find('option:selected').val();
      code = $post_page.find("textarea[name='" + action_name + "']").val();
      code = stripslashes(code);
      return $('#ml_admin_post_textarea').val(code);
    });
    saveCode = function(before, after) {
      var code, data;
      if (typeof before === "function") {
        before();
      }
      code = $('#ml_admin_post_textarea').val();
      data = {
        action: 'ml_admin_post_save_code',
        customization_name: $customization_select.find('option:selected').val(),
        code: code
      };
      return $.post(ajaxurl, data, function(response) {
        var action_name;
        action_name = $customization_select.find('option:selected').val();
        $post_page.find("textarea[name='" + action_name + "']").val(code);
        if (typeof after === "function") {
          after();
        }
        return win.location.reload();
      });
    };
    $post_div.find("input[type='submit']").click(function() {
      var label, saving_label,
        _this = this;
      label = $(this).data('label');
      saving_label = $(this).data('saving-label');
      return saveCode(function() {
        return $(_this).val(saving_label).attr('disabled', true);
      }, function() {
        return $(_this).val(label).attr('disabled', false);
      });
    });
    $checkboxes = $("#ml_admin_post_options input[type='checkbox']");
    saveOptions = function() {
      var checked, data, e, name, _i, _len;
      data = {
        action: 'ml_admin_post_save_options'
      };
      for (_i = 0, _len = $checkboxes.length; _i < _len; _i++) {
        e = $checkboxes[_i];
        name = $(e).attr('name');
        checked = $(e).is(':checked');
        data[name] = checked;
      }
      console.log(data);
      return $.ajax({
        url: ajaxurl,
        data: data,
        type: 'POST',
        dataType: 'html',
        success: function(response) {}
      });
    };
    for (_i = 0, _len = $checkboxes.length; _i < _len; _i++) {
      e = $checkboxes[_i];
      checked = $(e).data('checked');
      if (checked) {
        $(e).attr('checked', true);
      } else {
        $(e).attr('checked', false);
        $(e).removeAttr('checked');
      }
    }
    return $("#ml_admin_post_options input[type='submit']").click(function() {
      return saveOptions();
    });
  });

}).call(this);