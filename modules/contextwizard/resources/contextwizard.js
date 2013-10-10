/* 
 * Javascript to support contextwizard
 *
 * Written by Kevin Cyster kcyster@gmail.com
 * STarted on: April 15, 2012, 8:32 pm
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
    
    jQuery('#level_next').live('click', function(){
        var group_id = jQuery('#input_group_id').val(); 
        if (group_id == '')
        {
            alert(no_level);
            return false;
        }
        else
        {
            if (jQuery('#input_group_id').hasClass('WCHhider'))
            {
                jQuery.ajax({
                    type: 'POST',
                    url: 'index.php?module=contextwizard&action=ajaxAddToGroup',
                    data: 'group_id=' + jQuery('#input_group_id').val(),
                    success: function(ret) {
                        jQuery.ajax({
                            type: 'POST',
                            url: 'index.php?module=contextwizard&action=ajaxGetSubjects',
                            data: 'group_id=' + jQuery('#input_group_id').val(),
                            success: function(ret) {
                                jQuery('#dialog_wizard_level').dialog('close');
                                jQuery('#subject_layer').html(ret);                    
                                jQuery('#dialog_wizard_subject').dialog('open'); 
                                return false;
                            }
                        });
                    }
                });
            }
            else
            {
                jQuery.ajax({
                    type: 'POST',
                    url: 'index.php?module=contextwizard&action=ajaxGetSubjects',
                    data: 'group_id=' + jQuery('#input_group_id').val(),
                    success: function(ret) {
                        jQuery('#dialog_wizard_level').dialog('close');
                        jQuery('#subject_layer').html(ret);                    
                        jQuery('#dialog_wizard_subject').dialog('open'); 
                        return false;
                    }
                });
            }
        }
    });
    
    jQuery('#subject_back').live('click', function() {
       jQuery('#dialog_wizard_subject').dialog('close');
       jQuery('#dialog_wizard_level').dialog('open');
       return false;
    });
    
    jQuery('#subject_next').live('click', function() {
        var subject_id = jQuery('#input_subject_id').val(); 
        if (subject_id == '')
        {
            alert(no_subject);
            return false;
        }
        else
        {
            jQuery.ajax({
                type: 'POST',
                url: 'index.php?module=contextwizard&action=ajaxGetStrands',
                data: 'subject_id=' + jQuery('#input_subject_id').val(),
                success: function(ret) {
                    jQuery('#dialog_wizard_subject').dialog('close');
                    jQuery('#strand_layer').html(ret);                    
                    jQuery('#dialog_wizard_strand').dialog('open'); 
                    return false;
                }
            });
        }
    });

    jQuery('#strand_back').live('click', function() {
       jQuery('#dialog_wizard_strand').dialog('close');
       jQuery('#dialog_wizard_subject').dialog('open');
       return false;
    });
    
    jQuery('#strand_next').live('click', function() {
        var strand_id = jQuery('#input_strand_id').val(); 
        if (strand_id == '')
        {
            alert(no_strand);
            return false;
        }
        else
        {
            jQuery.ajax({
                type: 'POST',
                url: 'index.php?module=contextwizard&action=ajaxGetContexts',
                data: 'strand_id=' + jQuery('#input_strand_id').val(),
                success: function(ret) {
                    jQuery('#dialog_wizard_strand').dialog('close');
                    jQuery('#context_layer').html(ret);                    
                    jQuery('#dialog_wizard_context').dialog('open'); 
                    return false;
                }
            });
        }
    });

    jQuery('#context_back').live('click', function() {
       jQuery('#dialog_wizard_context').dialog('close');
       jQuery('#dialog_wizard_strand').dialog('open');
       return false;
    });
    
    jQuery('#context_enter').live('click', function() {
        var context_id = jQuery('#input_contextcode').val(); 
        if (context_id == '')
        {
            alert(no_context);
            return false;
        }
        else
        {
            jQuery('#form_wizard_context').submit().
            jQuery('#dialog_wizard_context').dialog('close');
            return false;
        }
    });

});