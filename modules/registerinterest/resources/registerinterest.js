/* 
 * Javascript to support registerinterest
 *
 * Written by Monwabisi Sifumba Monwabisi@thumbzup.com
 * STarted on: November 28, 2012, 8:24 am
 *
 * The following parameters need to be set in the
 * PHP code for this to work:
 *
 * @todo
 *   List your parameters here so you won't forget to add them
 *
 */

/**
 *@var array The variable to contain the interests selected by the user
 **/
//var interests = [];

/**
 *
 * Put your jQuery code inside this function.
 *
 */
jQuery(function() {

    
    // Things to do on loading the page.
    jQuery(document).ready(function() {
        //prevent form submission via enter key
        jQuery('#form_frmRegisterList, #form_edituser').submit(function(e){
            e.preventDefault();
        });
        //handling the popup div
        jQuery('.registerlistHidable').hide();
    });
    //show the hiden dialog whe hovering over the email input or on  focus
    jQuery('#input_email').hover(function(){
        jQuery('.registerlistHidable').show('slow');
    });
    //peform the same action when focused
    jQuery('#input_email').focus(function(){
        jQuery('.registerlistHidable').show('slow');
    });
        
    //hide the dialog on mouse out
    jQuery('.registerlistHidable').hover(function(){
        return false;
    },function(){
        //hide the div element on mouse out
        jQuery(this).hide('slow');
    });
        
    
    // Save the interest
    jQuery(document).on("click", "#ri_save_button", function(e){
        e.preventDefault();
        interests_values = jQuery("#form_frmRegisterList").serialize();
        contact_values = jQuery("#form_edituser").serialize();
        //combine the two arrays
        data_string = contact_values.concat(interests_values);
        if(interests_values.length > 13){
            jQuery.ajax({
                url: 'index.php?module=registerinterest&action=save&table=1',
                type: "POST",
                data: data_string,
                success: function() {
                    var msg;
                    var fullName =  jQuery("#input_fullname").val();
                    var email =  jQuery("#input_email").val()+'&';
                    if(jQuery.trim(fullName).length != 0){
                        if(fullName.length > 1){
                            if(jQuery.trim(email).length != 0){
                                if(email.indexOf('@') != -1 && email.length > 2){
                                    msg = "<span class='success' >Thank you for registering your interest.</span>";
                                    jQuery("#input_email").val('');
                                    jQuery("#input_fullname").val('');
                                    //uncheck checked checkboxes
                                    jQuery(":checked").attr("checked", false);
                                }else{
                                    msg = "<span class='error' >Enter valid email address</span>";
                                }
                            }else{
                                msg = "<span class='error' >Enter valid email address</span>";
                                jQuery("#input_fullname").html(fullName);
                            }
                        }else{
                            msg = "<span class='error' >Enter valid full names</span>";
                        }
                    }else{
                        msg = "<span class='error' >Enter valid full names</span>";
                        jQuery("#input_email").html(email);
                    }
                    jQuery("#ri_save_button").attr("disabled", "");
                    jQuery("#before_riform").html(msg);
                }
            });
        }else{
            jQuery("#before_riform").html("<p><span class='error' >To register you have to select atleast a single interest</span></p>");
        };
    });
    
    //edit values on the list
    jQuery("label[for='interestEmail']").click(function(){
        var value = jQuery(this).attr('value');
        var textBox = jQuery("<input type='text' class='newvalue' />");
        jQuery(textBox).val(value);
        jQuery(this).html(textBox);
        jQuery(textBox).focus();
    });
    
    //send update request when the textbox looses focus
    jQuery("label[for='interestEmail']").focusout(function(){
        var id = jQuery(this).attr('id');
        var newValue = jQuery("input[class='newvalue'] ").val();
        //send the value
        jQuery.ajax({
            url: 'index.php?module=registerinterest&action=update',
            type: 'POST',
            data: {
                'newValue': newValue,
                'id':id
            },
            success: function(){
                jQuery("#before_riform").html("<span class='success' >Update request sent successfully</span>");
                setTimeout(function(){
                    jQuery("#before_riform").text('')
                },5000)
            }
        })
        jQuery(this).html(newValue);
        jQuery(this).attr('value',newValue);
    });
    
    // Send the message
    jQuery(document).on("click", "#ri_savemsg_button", function(e){
        var noSubject = false;
        //alert("Not ready yet");
        if(jQuery("#input_subject").val().length == 0){
            if(confirm("Are you sure you want to send the email without a subject?")){
                noSubject = true;
            }else{
                e.preventDefault();
                noSubject = false;
                jQuery("#input_subject").focus();
            }
        }else{
            noSubject = true;
        }
        data_string = jQuery("#form_editmsg").serialize();
        if(noSubject){
            var span = document.createElement('span');
            jQuery.ajax({
                url: 'index.php?module=registerinterest&action=sendmessage',
                type: "POST",
                data: data_string,
                success: function() {
                    jQuery("#ri_savemsg_button").attr("disabled", "");
                    jQuery("#before_riform").html('<span class="success">Message send.</span>');
                    jQuery(span).html('');
                    jQuery("#form_editmsg").show('slow');
                }
            });
        };
    });
    
    
    //save intest "TOPICS"
    jQuery(document).on('click', '#btnSaveInterest', function(e){
        e.preventDefault();
        value = jQuery('#form_frmRegisterList').serialize();
        //value = jQuery('#input_txtValue').val();
        //send ajax call only when there is a value entered
        if(value.length > 0){
            jQuery(this).hide('slow');
            jQuery.ajax({
                url: 'index.php?module=registerinterest&action=save&table=2',
                type: 'POST',
                data: value,
                success: function(){
                    jQuery('#indicatorHeader').html("<span class='caution' >Please wait....</span>");
                    //show success message after 5 seconds
                    setTimeout(function(){
                        jQuery('#indicatorHeader').html("<span class='success' >Request sent successfully </span>");
                    }, 5000);
                    //reset to default text after 10 seconds
                    setTimeout(function(){
                        jQuery('#indicatorHeader').html("<span >Enter interest below.</span>");
                    },10000);
                }
            });
        }else{
            jQuery("#indicatorHeader").html("<span class='error' >Enter interests below.</span>");
        }
        //show the save button and clear the entered value
        setTimeout(function(){
            jQuery('#btnSaveInterest').show('slow');
            jQuery('#input_txtValue').val('');
        },5000);
    });
    
    
});