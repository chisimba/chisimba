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
    
    jQuery('#input_block_folder_id').change(function () {
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=bookmarks&action=ajaxGetBlockBookmarks",
            data: 'id=' + jQuery('#input_block_folder_id').val(),
            success: function(ret) {
                jQuery("#bookmarks_block_layer").html(ret); 
            }
        });
    });
    
    jQuery(document).on('click', '[class^="block_contextcode_"]', function() {
        var domain = jQuery(location).attr('hostname');
        var path = jQuery(location).attr('pathname');

        var contextcode = jQuery(this).attr('class').replace('block_contextcode_', '');
        var uri = jQuery(this).html();

        jQuery.ajax({
            type: "POST",
            url: "index.php?module=bookmarks&action=ajaxSetContext",
            data: 'contextcode=' + contextcode,
            success: function(ret) {
                if (ret == 'true')
                {
                    uri = uri = uri.replace(/&amp;/g, '&');
                    window.location = 'http://' + domain + path + '?' + uri;
                }
                else
                {
                    return false;
                }
            }
        });
    });

});