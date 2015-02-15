jQuery(document).ready(function() {
    
    jQuery(".ml-menu-holder").sortable({
        update: function( event, ui ) {
            jQuery("#get_started_menu_config form").trigger('setDirty.areYouSure');
        }
    });
    
    jQuery("select[name='ml-tax-group']").change(function() {
        var group = jQuery(this).val();
        if(group !== '') {
            jQuery("select[name='ml-terms']").find("option[value!='']").remove();
            jQuery(".ml-tax-group-row").hide();
            var data = {
                action: 'ml_tax_list',
                group: group
            };
            jQuery.post(ajaxurl, data, function(response) {
                if(response.terms !== undefined) {
                    for(term_id in response.terms) {
                        var term = response.terms[term_id];
                        jQuery("select[name='ml-terms']").append(jQuery('<option></option>').val(term.id).attr('title', term.title).html(term.fullname));
                    }
                    jQuery(".ml-tax-group-row").show();
                }
            });
        } else {
            jQuery(".ml-tax-group-row").hide();
            jQuery("select[name='ml-terms']").find('option').remove();
        }
    });
    
    jQuery(".ml-add-term-btn").click(function(e) {
        e.preventDefault();
        var selected_term = jQuery(".ml-select-add[name='ml-terms']").val();
        var selected_term_text = jQuery(".ml-select-add[name='ml-terms'] option:selected").attr('title');
        var selected_tax = jQuery(".ml-select-add[name='ml-tax-group']").val();
        if(selected_term !== '' && jQuery(".ml-menu-terms-holder li[rel='"+selected_term+"']").length <= 0) {
            var new_li = jQuery("<li>")
                    .attr('rel', selected_term)
                    .html("<span class='dashicons-before dashicons-menu'></span>"+selected_term_text)
                    .appendTo(jQuery(".ml-menu-terms-holder"));
            jQuery("<input/>")
                    .attr('name', 'ml-menu-terms[]')
                    .attr('value', selected_tax + "=" + selected_term)
                    .attr('type', 'hidden')
                    .appendTo(new_li);
            jQuery("<a>")
                    .attr('href', '#')
                    .attr('class', 'dashicons-before dashicons-trash ml-item-remove')
                    .appendTo(new_li);
        }
    });
    
    jQuery(".ml-add-tag-btn").click(function(e) {
        e.preventDefault();
        var selected_term = jQuery(".ml-select-add[name='ml-tags']").val();
        var selected_term_text = jQuery(".ml-select-add[name='ml-tags'] option:selected").text();
        if(selected_term !== '' && jQuery(".ml-menu-tags-holder li[rel='"+selected_term+"']").length <= 0) {
            var new_li = jQuery("<li>")
                    .attr('rel', selected_term)
                    .html("<span class='dashicons-before dashicons-menu'></span>"+selected_term_text)
                    .appendTo(jQuery(".ml-menu-tags-holder"));
            jQuery("<input/>")
                    .attr('name', 'ml-menu-tags[]')
                    .attr('value', selected_term)
                    .attr('type', 'hidden')
                    .appendTo(new_li);
            jQuery("<a>")
                    .attr('href', '#')
                    .attr('class', 'dashicons-before dashicons-trash ml-item-remove')
                    .appendTo(new_li);
        }
    });
    
    jQuery(".ml-add-category-btn").click(function(e) {
        e.preventDefault();
        var selected_cat = jQuery(".ml-select-add[name='ml-category']").val();
        var selected_cat_text = jQuery(".ml-select-add[name='ml-category'] option:selected").attr('title');
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