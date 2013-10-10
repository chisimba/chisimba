/* 
 * Javascript to support schoolusers
 *
 * Written by Kevin Cyster kcyster@gmail.com
 * STarted on: March 21, 2012, 9:18 pm
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

    // jQuery autocomplet function to return schools.   
   jQuery( "#input_name" ).autocomplete({
        minLength: 3,
        source: function( request, response )
        {                     
            jQuery.ajax(
            {
                type: "POST",
                url: 'index.php?module=schoolusers&action=ajaxFindUser',
                data: {
                    term: request.term,
                    field: jQuery('input:radio[name=field]:checked').val()
                },       
                dataType: "json",   //return data in json format                                                                                                                                      
                success: function( data )
                {
                    response( jQuery.map( data, function( item )
                    {
                        return{
                            label: item.label,
                            value: item.value
                        }
                    }));
                }
            });               
        },
        select: function(event, ui) {
            jQuery('#input_name').val(ui.item.label);
            jQuery('#input_id').val(ui.item.value);
            return false;
        }
    });
    
    jQuery('#select').click( function() {
        id = jQuery('#input_id').val(); 
        if (id != '')
        {
            jQuery('#form_find').submit();
        }
        return false;
    });
    
    // jQuery ajax function to check the username.
    jQuery('#input_username').blur(function() {
        if (jQuery('#input_username').val() != '')
        {
            var username = jQuery('#input_username').val();
            var mydata = "username=" + username;
            jQuery.ajax({
                type: "POST",
                url: "index.php?module=schoolusers&action=ajaxUsername",
                data: mydata,
                success: function(ret) {
                    if (jQuery('#username_error').length > 0)
                    {
                        jQuery("#username_error").html(ret);
                    }
                    else
                    {
                        jQuery("#username").html(ret);
                        if (jQuery("#username > span").hasClass("error"))
                        {
                            jQuery('#input_username').select();
                        }
                    }
                }
            });
        }
    });

    // jQuery function to check if the passwords match.
    jQuery('#input_confirm_password').blur(function() {
        var password = jQuery('#input_password').val();
        var confirm = jQuery('#input_confirm_password').val();
        if (password != confirm)
        {
            alert(password_not_alike);
            jQuery('#input_confirm_password').val('');
            jQuery('#input_password').select();
        }
    });
    
    jQuery('#input_school').focus( function () {
        jQuery('#input_school').val('');
        jQuery('#input_school_id').val('');        
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
    
    jQuery('#redraw').click( function() {
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=schoolusers&action=ajaxCaptcha",
            success: function(ret) {
                jQuery("#captcha").html(ret);
                jQuery('#input_captcha').val('');
                jQuery('#input_captcha').focus();
            }
        });
    });

});

function doAdd(com, grid)
{
    window.location.href = jQuery(location).attr('href') + '&action=form';
}
