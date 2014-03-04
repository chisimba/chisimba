/* 
 * Javascript to support schools
 *
 * Written by Kevin Cyster kcyster@gmail.com
 * STarted on: February 29, 2012, 8:26 pm
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
    
    if (jQuery('#input_schools').length > 0)
    {
        jQuery('#input_schools').focus(function() {
            jQuery('#input_sid').val('');
        });
    }
        
    if (jQuery('#input_schools').length > 0)
    {
        // jQuery autocomplet function to return schools.
        jQuery( "#input_schools" ).autocomplete({
            minLength: 3,
            source: 'index.php?module=schools&action=ajaxFindSchools',
            select: function(event, ui) {
                jQuery('#input_schools').val(ui.item.label);
                jQuery('#input_sid').val(ui.item.value);
                return false;
            }
        });
    }    
    
    if (jQuery('[name=select]').length > 0)
    {
        jQuery('[name=select]').click(function() {
            if (jQuery('#input_sid').val() == '')
            {
                alert(no_school);
                return false;
            }
            else
            {
                jQuery('#form_detail').submit();
            }
        });
    }

    if (jQuery('#input_province_id').length > 0)
    {
        // jQuery ajax function to return the districts dropdown.
        jQuery('#input_province_id').change(function() {
            var pid = jQuery("#input_province_id").val();
            if (pid != '')
            {
                var mydata = "pid=" + pid;
                jQuery.ajax({
                    type: "POST",
                    url: "index.php?module=schools&action=ajaxGetDistricts",
                    data: mydata,
                    success: function(ret) {
                        jQuery("#district").html(ret); 
                    }
                });
            }
            else
            {
                jQuery("#district").html(''); 
            }
        });
    }
    
    if (jQuery('#input_province').length > 0)
    {
        // jQuery ajax function to return the district dropdown.
        jQuery('#input_province').change(function() {
            var pid = jQuery("#input_province").val();
            if (pid != '')
            {
                var mydata = "pid=" + pid;
                jQuery.ajax({
                    type: "POST",
                    url: "index.php?module=schools&action=ajaxManageDistricts",
                    data: mydata,
                    success: function(ret) {
                        jQuery("#district").html(ret); 
                    }
                });
            }
            else
            {
                jQuery("#district").html(''); 
            }
        });
    }

    // jQuery ajax function to return the add district form.
    jQuery(document).on("click", '#adddistrict', function() {
        var pid = jQuery("#input_province").val();
        var mydata = "pid=" + pid;
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=schools&action=ajaxAddEditDistrict",
            data: mydata,
            success: function(ret) {
                jQuery("#adddistrictdiv").html(ret); 
                jQuery('#districtdiv').toggle();
            }
        });
    });
    
    // jQuery function to cancel the district form.
    jQuery(document).on("click", '#cancel_district', function() {
        jQuery("#adddistrictdiv").html(''); 
        jQuery('#districtdiv').toggle();
        return false;
    });

    // jQuery function to submit the district form.
    jQuery(document).on("submit", '#form_district', function() {
        if (!jQuery.trim(jQuery('#input_name').val()))
        {
            alert(no_district);
            jQuery('#input_name').val('');
            jQuery('#input_name').focus();
            return false;
        }
        else
        {
            return true;
        }
    });

    // jQuery ajax function to return the edit district form.
    jQuery(document).on("click", '#editdistrict', function() {
        var id = jQuery(this).attr('class');
        var mydata = 'id=' + id;
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=schools&action=ajaxAddEditDistrict",
            data: mydata,
            success: function(ret) {
                jQuery("#adddistrictdiv").html(ret); 
                jQuery("#districtdiv").toggle(); 
            }
        });
    });

    // jQuery ajax function to return the add province form.
    jQuery(document).on("click", '#addprovince', function() {
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=schools&action=ajaxAddEditProvince",
            success: function(ret) {
                jQuery("#addprovincediv").html(ret); 
                jQuery('#provincediv').toggle();
            }
        });
    });

    // jQuery function to cancel the province form.
    jQuery(document).on("click", '#cancel_province', function() {
        jQuery("#addprovincediv").html(''); 
        jQuery('#provincediv').toggle();
        return false;
    });

    // jQuery function to submit the province form.
    jQuery(document).on("submit", '#form_province', function() {
        if (!jQuery.trim(jQuery("#input_name").val()))
        {
            alert(no_province);
            jQuery('#input_name').val('');
            jQuery('#input_name').focus();
            return false;
        }
        else
        {
            return true;
        }
    });

    // jQuery ajax function to return the edit district form.
    jQuery(document).on("click", '#editprovince', function() {
        var id = jQuery(this).attr('class');
        var mydata = 'id=' + id;
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=schools&action=ajaxAddEditProvince",
            data: mydata,
            success: function(ret) {
                jQuery("#addprovincediv").html(ret); 
                jQuery("#provincediv").toggle(); 
            }
        });
    });

    if (jQuery('#input_principal').length > 0)
    {
        jQuery('#input_principal').focus(function() {
            jQuery('#input_id').val('');
        });
    }
    
    if (jQuery('#input_principal').length > 0)
    {
        // jQuery autocomplet function to return schools.
        jQuery( "#input_principal" ).autocomplete({
            minLength: 3,
            source: function( request, response )
            {                     
                jQuery.ajax(
                {
                    type: "POST",
                    url: 'index.php?module=schools&action=ajaxFindPrincipals',
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
                jQuery('#input_principal').val(ui.item.label);
                jQuery('#input_id').val(ui.item.value);
                return false;
            }
        });
    }

    if (jQuery('[name=add_save]').length > 0)
    {
        jQuery('[name=add_save]').click(function() {
            if (jQuery('#input_id').val() == '')
            {
                alert(select_principal);
                return false;
            }
            else
            {
                jQuery('#form_findprincipal').submit();
            }
        });
    }
    
    if (jQuery('#addprincipal').length > 0)
    {
        jQuery('#addprincipal').click(function() {
            jQuery('#form_findprincipal').toggle();
            jQuery('#form_addprincipal').toggle();
        });
    }

    if (jQuery('#findprincipal').length > 0)
    {
        jQuery('#findprincipal').click(function() {
            jQuery('#form_findprincipal').toggle();
            jQuery('#form_addprincipal').toggle();
        });
    }
    
    if (jQuery('#input_username').length > 0)
    {
        // jQuery ajax function to check the username.
        jQuery('#input_username').blur(function() {
            if (jQuery('#input_username').val() != '')
            {
                var username = jQuery('#input_username').val();
                var mydata = "username=" + username;
                jQuery.ajax({
                    type: "POST",
                    url: "index.php?module=schools&action=ajaxUsername",
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
    }

    if (jQuery('#input_confirm_password').length > 0)
    {
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
    }

});