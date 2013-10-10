/* 
 * Javascript to support bookmarks
 *
 * Written by Kevin Cyster kcyster@gmail.com
 * STarted on: March 28, 2012, 12:34 pm
 *
 * The following parameters need to be set in the
 * PHP code for this to work:
 *
 * @todo
 *   List your parameters here so you won't forget to add them
 *
 */

/**
 *
 * Put your jQuery code inside this function.
 *
 */
jQuery(function() {
   
    // Things to do on loading the page.
    jQuery(document).ready(function() {
    });

    jQuery('#root').click(function() {
        jQuery('.ui-state-highlight').removeAttr('class');
        jQuery('#root').addClass('ui-state-highlight');
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=bookmarks&action=ajaxGetBookmarks",
            success: function(ret) {
                jQuery("#folder_bookmarks").html(ret); 
            }
        });
    });
    
    jQuery('[id^="icon_"]').each(function() {
        var source;
        var id = this.id.replace("icon_", "");
        var icon = jQuery("#icon_" + id);
        var image = icon.children(':first');
        icon.click( function() {
            if (image.attr('title') == "Expand")
            {
                source = image.attr('src');
                image.attr('title', 'Collapse');
                image.attr('alt', 'Collapse');
                source = source.replace('expand', 'collapse'); 
                image.attr('src', source);
                jQuery('[id$="' + id + '"]').show();
            }
            else if (image.attr('title') == "Collapse")
            {
                image.attr('title', 'Expand');
                image.attr('alt', 'Expand');
                source = image.attr('src');
                source = source.replace('collapse', 'expand');
                image.attr('src', source)
                
                var rows = jQuery('[id^="row_"][id*="' + id + '"]');                
                rows.each(function() {                                                           
                    var childImage = jQuery('img', this);
                    childImage.eq(0).attr('title', 'Expand');
                    childImage.eq(0).attr('alt', 'Expand');
                    source = childImage.eq(0).attr('src');
                    source = source.replace('collapse', 'expand');
                    childImage.eq(0).attr('src', source)
                    jQuery(this).hide();
                });
            }
        });
    });
    
    jQuery('[id^="link_"]').each(function() {
        var id = this.id.replace("link_", "");
        var link = jQuery("#link_" + id);
        link.click( function() {
            jQuery('.ui-state-highlight').removeAttr('class');
            link.addClass('ui-state-highlight');
            jQuery.ajax({
                type: "POST",
                url: "index.php?module=bookmarks&action=ajaxGetBookmarks",
                data: 'id=' + id,
                success: function(ret) {
                    jQuery("#folder_bookmarks").html(ret); 
                }
            });
        });
    });

    jQuery('#form_bookmarks').ready(function() {
        var fullUrl = jQuery(location).attr('href');
        var domain = jQuery(location).attr('hostname');
        var path = jQuery(location).attr('pathname');

        var temp = fullUrl.replace('http://' + domain, '');
        var bookmark = temp.replace(path + '?', '');
        jQuery('#input_location').val(bookmark);
        jQuery('#input_visible_location').val(bookmark);        
    });

    jQuery('[class^="child_"]').each(function() {
        jQuery(this).hide();
    });

    jQuery(document).on('click', '[class^="contextcode_"]', function() {
        var contextcode = jQuery(this).attr('class').replace('contextcode_', '');
        var uri = jQuery(this).html();
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=bookmarks&action=ajaxSetContext",
            data: 'contextcode=' + contextcode,
            success: function(ret) {
                if (ret == 'true')
                {
                    uri = uri.replace(/&amp;/g, '&');
                    window.location = uri;
                }
                else
                {
                    return false;
                }
            }
        });
    });
    
});