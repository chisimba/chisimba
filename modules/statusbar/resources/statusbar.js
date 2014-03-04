/* 
 * Javascript to support statusbar
 *
 * Written by Kevin Cyster kcyster@gmail.com
 * STarted on: May 17, 2012, 10:54 am
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

    jQuery(document).ready(function() {
        var period = ajax_poll * 1000;
        setInterval(function() { 
            var bookmarks = jQuery("#dialog_add_bookmark").dialog('isOpen');
            var settings = jQuery("#dialog_statusbar_settings").dialog('isOpen');
            var message = jQuery("#dialog_statusbar_message").dialog('isOpen');
            var calendar = jQuery("#dialog_statusbar_calendar_alert_show").dialog('isOpen');
            var content = jQuery("#dialog_statusbar_content_alert_show").dialog('isOpen');

            if (bookmarks == false && settings == false && message == false && calendar == false && content == false)
            {
                jQuery.ajax({
                    type: "POST",
                    url: "index.php?module=statusbar&action=ajaxShowMain",
                    success: function(ret) {
                        jQuery("#statusbar").html(ret);
                    }
                });
            }
        }, period);
    });
    
    jQuery(document).on('click', '#statusbar_settings', function() {
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=statusbar&action=ajaxShowSettings",
            success: function(ret) {
                var obj = jQuery.parseJSON(ret)
                jQuery('#statusbar_orientation_div').html(obj.orientation);
                jQuery('#statusbar_interval_div').html(obj.interval);
                var orientation = jQuery('#input_statusbar_orientation').val();
                jQuery.ajax({
                    type: "POST",
                    url: "index.php?module=statusbar&action=ajaxShowPosition",
                    data: 'orientation=' + orientation,
                    success: function(ret) {
                        jQuery("#statusbar_position_div").html(ret);
                        jQuery.ajax({
                            type: "POST",
                            url: "index.php?module=statusbar&action=ajaxShowPosition",
                            data: 'orientation=' + orientation,
                            success: function(ret) {
                                jQuery("#statusbar_display_div").html(ret);
                                jQuery('#dialog_statusbar_settings').dialog('open');
                            }
                        });
                    }
                });
            }
        });
    });

    jQuery(document).on('click', '#statusbar_settings_cancel', function() {
        jQuery('#dialog_statusbar_settings').dialog('close');
        return false;
    });
    
    jQuery(document).on('change', '#input_statusbar_orientation', function() {
        var orientation = jQuery('#input_statusbar_orientation').val();
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=statusbar&action=ajaxShowPosition",
            data: 'orientation=' + orientation,
            success: function(ret) {
                jQuery("#statusbar_position_div").html(ret);
            }
        });
    });
    
    jQuery(document).on('click', '#statusbar_settings_save', function() {
        var orientation = jQuery("#input_statusbar_orientation").val();
        var position = jQuery("#input_statusbar_position").val();
        var alerts = jQuery("#input_statusbar_alert").val();
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=statusbar&action=ajaxSaveSettings",
            data: "orientation=" + orientation + "&position=" + position + "&alert=" + alerts,
            success: function(ret) {
                jQuery.ajax({
                    type: "POST",
                    url: "index.php?module=statusbar&action=ajaxShowMain",
                    success: function(ret) {
                        jQuery("#statusbar").html(ret);
                        if (orientation == 'horizontal')
                        {
                            jQuery("#statusbar").dialog("option", "minHeight", 35);
                        }
                        else
                        {
                            jQuery("#statusbar").dialog("option", "minHeight", "auto");
                        }
                        jQuery("#statusbar").dialog("option", "position", position);
                        jQuery("#dialog_statusbar_settings").dialog("close");
                    }
                });
            }
        });
    });
    
    jQuery(document).on('click', "#statusbar_display_hide", function() {
        var position = jQuery("#input_statusbar_position").val();
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=statusbar&action=ajaxSaveSettings",
            data: "display=no",
            success: function(ret) {
                jQuery.ajax({
                    type: "POST",
                    url: "index.php?module=statusbar&action=ajaxShowMain",
                    success: function(ret) {
                        jQuery("#statusbar").html(ret);
                        jQuery("#statusbar").dialog("close");
                        jQuery("#statusbar").dialog("option", "minHeight", 35);
                        jQuery("#statusbar").dialog("option", "position", position);
                        jQuery("#statusbar").dialog("open");
                    }
                });
            }
        });
    });

    jQuery(document).on('click', "#statusbar_display_show", function() {
        var orientation = jQuery("#input_statusbar_orientation").val();
        var position = jQuery("#input_statusbar_position").val();
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=statusbar&action=ajaxSaveSettings",
            data: "display=yes",
            success: function(ret) {
                jQuery.ajax({
                    type: "POST",
                    url: "index.php?module=statusbar&action=ajaxShowMain",
                    success: function(ret) {
                        jQuery("#statusbar").html(ret);
                        jQuery("#statusbar").dialog("close");
                        if (orientation == 'horizontal')
                        {
                            jQuery("#statusbar").dialog("option", "minHeight", 35);
                        }
                        else
                        {
                            jQuery("#statusbar").dialog("option", "minHeight", "auto");
                        }
                        jQuery("#statusbar").dialog("option", "position", position);
                        jQuery("#statusbar").dialog("open");
                    }
                });
            }
        });
    });
    
    jQuery(document).on("click", ".statusbar_buddies_on", function() {
        jQuery("#dialog_statusbar_message").dialog("open");
    });

    jQuery(document).on("click", "#statusbar_message_cancel", function() {
        jQuery("#dialog_statusbar_message").dialog("close");
    });
    
    jQuery(document).on("click", "#statusbar_message_send", function() {
        var recipient = jQuery("#input_statusbar_to").val();
        var message = jQuery("#input_statusbar_message").val();
        if (recipient == '')
        {
            alert(no_recipient);
            return false;
        }
        if (message == '')
        {
            alert(no_message);
            return false;
        }
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=statusbar&action=ajaxSaveMessage",
            data: "recipient=" + recipient + "&message=" + message,
            success: function(ret) {
                jQuery("#dialog_statusbar_message").dialog("close");
                jQuery("#dialog_statusbar_message_confirm").dialog("open");
            }
        });
    });
    
    jQuery(document).on("click", ".statusbar_messaging_on", function() {
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=statusbar&action=ajaxShowMessage",
            success: function(ret) {
                var obj = jQuery.parseJSON(ret)
                jQuery('#input_statusbar_message_id').val(obj.id);
                jQuery('#statusbar_message_from').html(obj.from);
                jQuery('#statusbar_message_message').html(obj.message);
                jQuery("#dialog_statusbar_message_show").dialog("open");
            }
        });
    });

    jQuery(document).on("click", ".statusbar_alert_on", function() {
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=statusbar&action=ajaxShowCalendarAlerts",
            success: function(ret) {
                var obj = jQuery.parseJSON(ret)
                jQuery('#input_statusbar_calendar_id').val(obj.id);
                jQuery('#input_statusbar_alert_type').val(obj.type);
                jQuery('#statusbar_alert_title').html(obj.title);
                jQuery('#statusbar_alert_from').html(obj.from);
                jQuery("#dialog_statusbar_calendar_alert_show").dialog("open");
            }
        });
    });
    
    jQuery(document).on("click", ".statusbar_email_on", function() {
        var path = jQuery(location).attr('pathname');
        window.location.assign(path + '?module=internalmail');
    });

    jQuery(document).on("click", ".statusbar_document_on", function() {
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=statusbar&action=ajaxShowContentAlerts",
            success: function(ret) {
                var obj = jQuery.parseJSON(ret)
                jQuery('#input_statusbar_content_id').val(obj.id);
                jQuery('#input_statusbar_contextcode').val(obj.context);
                jQuery('#statusbar_alert_description').html(obj.description);
                jQuery('#statusbar_alert_link').html(obj.link);
                jQuery("#dialog_statusbar_content_alert_show").dialog("open");
            }
        });
    });
    
    jQuery(document).on('click', '[class^="contextcode_"]', function() {
        var contextcode = jQuery(this).attr('class').replace('contextcode_', '');
        var uri = jQuery(this).html();
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=statusbar&action=ajaxSetContext",
            data: 'contextcode=' + contextcode,
            success: function(ret) {
                if (ret == 'true')
                {
                    uri = uri.replace(/&amp;/g, '&');
                    var id = jQuery("#input_statusbar_content_id").val();
                    jQuery.ajax({
                        type: "POST",
                        url: "index.php?module=statusbar&action=ajaxUpdateContentAlert",
                        data: "id=" + id,
                        success: function (data) {
                            jQuery(".statusbar_document_count").html(data);
                        }
                    });    
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

var updateInstantMessages = function(myDialog)
{
    var id = jQuery("#input_statusbar_message_id").val();
    jQuery.ajax({
        type: "POST",
        url: "index.php?module=statusbar&action=ajaxUpdateMessage",
        data: "id=" + id,
        success: function (data) {
            jQuery(".statusbar_message_count").html(data);
            if (data == 0)
            {
                jQuery.ajax({
                    type: "POST",
                    url: "index.php?module=statusbar&action=ajaxShowMain",
                    success: function(ret) {
                        jQuery("#statusbar").html(ret);
                    }
                });
            }
//            if (data > 0)
//            {
//                jQuery.ajax({
//                    type: "POST",
//                    url: "index.php?module=statusbar&action=ajaxShowMessage",
//                    success: function(ret) {
//                        var obj = jQuery.parseJSON(ret)
//                        jQuery('#input_statusbar_message_id').val(obj.id);
//                        jQuery('#statusbar_message_from').html(obj.from);
//                        jQuery('#statusbar_message_message').html(obj.message);
//                        jQuery("#dialog_statusbar_message_show").dialog("open");
//                    }
//                });
//            }
        }
    });    
}

var updateCalendarAlert = function(myDialog)
{
    var id = jQuery("#input_statusbar_calendar_id").val();
    var type = jQuery("#input_statusbar_alert_type").val();
    jQuery.ajax({
        type: "POST",
        url: "index.php?module=statusbar&action=ajaxUpdateCalendarAlert",
        data: "id=" + id + "&type=" + type,
        success: function (data) {
            jQuery(".statusbar_alert_count").html(data);
            if (data > 0)
            {
                jQuery.ajax({
                    type: "POST",
                    url: "index.php?module=statusbar&action=ajaxShowCalendarAlerts",
                    success: function(ret) {
                        var obj = jQuery.parseJSON(ret)
                        jQuery('#input_statusbar_alert_id').val(obj.id);
                        jQuery('#input_statusbar_alert_type').val(obj.type);
                        jQuery('#statusbar_alert_title').html(obj.title);
                        jQuery('#statusbar_alert_from').html(obj.from);
                        jQuery("#dialog_statusbar_alert_show").dialog("open");
                    }
                });
            }
            else
            {
                jQuery.ajax({
                    type: "POST",
                    url: "index.php?module=statusbar&action=ajaxShowMain",
                    success: function(ret) {
                        jQuery("#statusbar").html(ret);
                    }
                });
            }
        }
    });    
}

var updateContentAlert = function(myDialog)
{
    var id = jQuery("#input_statusbar_content_id").val();
    jQuery.ajax({
        type: "POST",
        url: "index.php?module=statusbar&action=ajaxUpdateContentAlert",
        data: "id=" + id,
        success: function (data) {
            jQuery(".statusbar_document_count").html(data);
            if (data > 0)
            {
                jQuery.ajax({
                    type: "POST",
                    url: "index.php?module=statusbar&action=ajaxShowContentAlerts",
                    success: function(ret) {
                        var obj = jQuery.parseJSON(ret)
                        jQuery('#input_statusbar_content_id').val(obj.id);
                        jQuery('#statusbar_alert_description').html(obj.description);
                        jQuery('#statusbar_alert_link').html(obj.link);
                        jQuery("#dialog_statusbar_content_alert_show").dialog("open");
                    }
                });
            }
            else
            {
                jQuery.ajax({
                    type: "POST",
                    url: "index.php?module=statusbar&action=ajaxShowMain",
                    success: function(ret) {
                        jQuery("#statusbar").html(ret);
                    }
                });
            }
        }
    });    
}

function ajax_polling()
{
    
}
