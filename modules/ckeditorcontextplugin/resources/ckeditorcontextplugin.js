/* 
 * Javascript to support ckeditorcontextplugin
 *
 * Written by Kevin Cyster kcyster@gmail.com
 * STarted on: May 7, 2012, 10:31 pm
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

    jQuery('#input_contextcode').change(function() {
        jQuery('#plugins').html('');
        jQuery('#contextcontent').html('');
        jQuery('#viewcontent').html('');
        var contextcode = jQuery('#input_contextcode').val();
        if (contextcode != '')
        {
            jQuery.ajax({
                type: "POST",
                url: "index.php?module=ckeditorcontextplugin&action=ajaxGetPlugins",
                data: 'contextcode=' + contextcode,
                success: function(ret) {
                    jQuery("#plugins").html(ret); 
                }
            });
        }
    });

    jQuery(document).on('change', '#input_plugins', function() {
        jQuery('#contextcontent').html('');
        jQuery('#viewcontent').html('');
        var plugin = jQuery('#input_plugins').val();
        if (plugin == 'contextcontent')
        {
            jQuery.ajax({
                type: "POST",
                url: "index.php?module=ckeditorcontextplugin&action=ajaxGetContentOptions",
                success: function(ret) {
                    jQuery("#contextcontent").html(ret); 
                }
            });
        }
    });

    jQuery(document).on('change', '#input_option', function() {
        jQuery('#viewcontent').html('');
        var option = jQuery('#input_option').val();
        if (option == 'view')
        {
            jQuery.ajax({
                type: "POST",
                url: "index.php?module=ckeditorcontextplugin&action=ajaxGetChapters",
                data: "contextcode=" + jQuery('#input_contextcode').val(),
                success: function(ret) {
                    jQuery("#viewcontent").html(ret);
                }
            });
        }
    });
    
    jQuery('#input_filter').change(function() {
        var filter = jQuery('#input_filter').val();
        if (filter != '')
        {
            jQuery.ajax({
                type: "POST",
                url: "index.php?module=ckeditorcontextplugin&action=ajaxGetParams",
                data: "filter=" + filter,
                success: function(ret) {
                    jQuery("#parameters").html(ret);
                }
            });
        } 
    });
    
    jQuery('#context_cancel, #filters_cancel').click(function(){
        jQuery('#dialog_ckeditorcontextplugin').dialog('close');
    });

});