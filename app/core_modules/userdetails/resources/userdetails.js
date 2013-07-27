/* 
 * Javascript to support userdetails
 *
 * Written by Kevin Cyster kcyster@gmail.com
 * STarted on: April 17, 2012, 10:44 am
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
    
    jQuery(document).on('click', '#grade_select', function() {
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=userdetails&action=ajaxChangeGrade",
            data: 'new_name=' + jQuery('#input_new_name').val() + '&old_name=' + jQuery('#input_old_name').val(),
            success: function(ret) {
                if (ret == 1)
                {
                    jQuery("#dialog_grade_success").dialog('open');
                }
                else
                {
                    jQuery("#dialog_grade_error").dialog('open');
                }
                return false;
            }
        });
    });

    jQuery(document).on('click', '#update_image', function() {
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=userdetails&action=ajaxChangeImage",
            data: 'imageselect=' + jQuery('#hidden_imageselect').val(),
            success: function(data) {
                if (data == 'nopicturegiven')
                {
                    jQuery("#dialog_nopicturegiven").dialog('open');
                    return false;
                }
                else if (data == 'imagedoesnotexist')
                {
                    jQuery("#dialog_imagedoesnotexist").dialog('open');
                    return false;
                }
                else if (data == 'fileisnotimage')
                {
                    jQuery("#dialog_fileisnotimage").dialog('open');
                    return false;
                }
                else
                {
                    var obj = jQuery.parseJSON(data);
                    jQuery('#bizcard').html(obj.card);
                    jQuery('.toolbar_userimage').html(obj.image);
                    jQuery('#hidden_imageselect').val('');
                    jQuery('#imagepreview_imageselect').attr('src', 'skins/_common/icons/imagepreview.gif');
                    jQuery("#dialog_imagechanged").dialog('open');
                    return false;
                }
            }
        });
    });
    
    jQuery(document).on('submit', '#form_updateimage', function() {
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=userdetails&action=ajaxResetImage",
            success: function(data) {
                var obj = jQuery.parseJSON(data);
                jQuery('#bizcard').html(obj.card);
                jQuery('.toolbar_userimage').html(obj.image);
                jQuery("#dialog_imagereset").dialog('open');
                return false;
            }
        });
        return false;
    });
        
    // jQuery autocomplet function to return schools.
    jQuery( "#input_school" ).autocomplete({
        minLength: 3,
        source: 'index.php?module=schoolusers&action=ajaxFindSchools',
        select: function(event, ui) {
            jQuery('#input_school').val(ui.item.label);
            jQuery('#input_school_id').val(ui.item.value);
            return false;
        }
    });

});

var resetSession = function(myDialog)
{
    jQuery.ajax({
        type: "POST",
        url: "index.php?module=userdetails&action=ajaxResetSession",
        success: function (data) {
            return false;
        }
    });    
};