jQuery(document).ready(function() {
    jQuery(".ml-menu-holder").sortable({
        update: function( event, ui ) {
            jQuery("#get_started_menu_config form").trigger('setDirty.areYouSure');
        }
    });
    
    jQuery(".ml-add-category-btn").click(function(e) {
        e.preventDefault();
        var selected_cat = jQuery(".ml-select-add[name='ml-category']").val();
        var selected_cat_text = jQuery(".ml-select-add[name='ml-category'] option:selected").text();
        if(selected_cat !== '' && jQuery(".ml-menu-categories-holder li[rel='"+selected_cat+"']").length <= 0) {
            var new_li = jQuery("<li>")
                    .attr('rel', selected_cat)
                    .html("<span class='dashicons-before dashicons-menu'></span>"+selected_cat_text)
                    .appendTo(jQuery(".ml-menu-categories-holder"));
            jQuery("<input/>")
                    .attr('name', 'ml-menu-categories[]')
                    .attr('value', selected_cat)
                    .attr('type', 'hidden')
                    .appendTo(new_li);
            jQuery("<a>")
                    .attr('href', '#')
                    .attr('class', 'dashicons-before dashicons-trash ml-item-remove')
                    .appendTo(new_li);
        }
    });
    
    jQuery(".ml-add-page-btn").click(function(e) {
        e.preventDefault();
        var selected_cat = jQuery(".ml-select-add[name='ml-page']").val();
        var selected_cat_text = jQuery(".ml-select-add[name='ml-page'] option:selected").text();
        if(selected_cat !== '' && jQuery(".ml-menu-pages-holder li[rel='"+selected_cat+"']").length <= 0) {
            var new_li = jQuery("<li>")
                    .attr('rel', selected_cat)
                    .html("<span class='dashicons-before dashicons-menu'></span>"+selected_cat_text)
                    .appendTo(jQuery(".ml-menu-pages-holder"));
            jQuery("<input/>")
                    .attr('name', 'ml-menu-pages[]')
                    .attr('value', selected_cat)
                    .attr('type', 'hidden')
                    .appendTo(new_li);
            jQuery("<a>")
                    .attr('href', '#')
                    .attr('class', 'dashicons-before dashicons-trash ml-item-remove')
                    .appendTo(new_li);
        }
    });
    
    jQuery(".ml-add-link-btn").click(function(e) {
        e.preventDefault();
        var link_title = jQuery("#ml_menu_url_title").val();
        var link_url = jQuery("#ml_menu_url").val();
        if(link_title !== '' && link_url !== '' && jQuery(".ml-menu-links-holder li[rel='"+link_title+"']").length <= 0) {
            var new_li = jQuery("<li>")
                    .attr('rel', link_url)
                    .html("<span class='dashicons-before dashicons-menu'></span>"+link_title+" - <span class='ml-sub-title'>"+trim_string(link_url, 50)+"</span>")
                    .appendTo(jQuery(".ml-menu-links-holder"));
            jQuery("<input/>")
                    .attr('name', 'ml-menu-links[]')
                    .attr('value', link_title + ":=:" + link_url)
                    .attr('type', 'hidden')
                    .appendTo(new_li);
            jQuery("<a>")
                    .attr('href', '#')
                    .attr('class', 'dashicons-before dashicons-trash ml-item-remove')
                    .appendTo(new_li);
        }
    });
    
    jQuery(".ml-item-remove").live('click', function(e) {
        e.preventDefault();
        jQuery(this).parents('li').remove();
    });
});

var trim_string = function(string, length) {
    if(string.length <= length) {
        return string;
    } else {
        return string.substring(0, length) + '...';
    }
};