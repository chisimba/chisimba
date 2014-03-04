/* 
 * Javascript to support user edit
 *
 * Written by Derek Keats derek@dkeats.com
 * Started on: December 18, 2011, 8:48 am
 *
 *
 */


var data_string;
var response;
var captcha;
var captcha_img;
var allow_submit=true;
var redirect=false;
var redirection;

/**
 *
 * jQuery code belongs inside this function.
 *
 */
jQuery(function() {
    jQuery(document).ready(function() {
        jQuery('#register_success').hide();
        jQuery('#register_fail').hide();
        jQuery('#update_success').hide();
    });
    function showRegisterSuccess(){
        jQuery('#register_success').dialog({
            width:350
        });
    };
    function showUpdateSuccess(){
        jQuery('#update_success').dialog({
            width:350
        });
    };
    function showRegisterFail(){
        jQuery('#register_fail').dialog({
            width:350
        });
    };   
    
    //Create an Ajax method to validate user name is not used
    jQuery.validator.addMethod("uniqueUserName", function(value, element) {
        response=true;
        if (jQuery("#edmode").val()!=='edit') {
            jQuery.ajax({
                type: "POST",
                url: "index.php?module=oeruserdata&action=checkusernameajax",
                data: "username="+value,
                dataType:"html",
                success: function(msg)
                {
                    //If username exists, set response to true
                    if (msg == 'true') {
                        jQuery("#usernameerror").text(usernametaken);
                        jQuery("#usernameerror").attr("class", "error");
                        response = false;
                    } else {                        
                        jQuery("#usernameerror").text("");
                        jQuery("#usernameerror").attr("class", "errors");
                        response = true;
                    }
                }
            })
        }
        return response;
    }, "Username is already taken.");
    jQuery.validator.addMethod('notNone', 
        function(value, element) {
            return (value != 'none');
        }, 'Please select an option.'
        );
    // Things to do on loading the page.
    jQuery(document).ready(function() {        
        // Add jQuery Validation to form
        jQuery("#form_edituser").validate({
            errorLabelContainer: jQuery("#RegisterErrors"),
            // JUST MAKE ALL THE MESSAGES SIMPLE, SHORT - required
            rules: {
                title: {
                    notNone: true
                }, 
                firstname: {
                    required: true,
                    minlength: 2
                }, 
                surname: {
                    required: true,
                    minlength: 2
                },
                username: {
                    required: true,
                    minlength: 2,
                    uniqueUserName: true
                },
                password: {
                    required: true,
                    minlength: 8
                },
                confirmpassword: {
                    required: true,
                    minlength: 8,
                    equalTo: "#password"
                },
                email: {
                    required: true,
                    email: true
                },
                birthdate: {
                    required: true,
                    minlength: 2
                },
                address: {
                    required: true,
                    minlength: 8
                },
                city: {
                    required: true,
                    minlength: 2
                },
                state: {
                    required: true,
                    minlength: 2
                },
                postalcode: {
                    required: true,
                    minlength: 2
                },
                country: {

                },
                orgcomp: {
                    required: true,
                    minlength: 2
                },
                jobtitle: {
                    required: true,
                    minlength: 2
                },
                occupationtype: {
                    required: true,
                    minlength: 2
                },
                workphone: {
                    required: true,
                    minlength: 2
                },
                mobilephone: {
                    required: true,
                    minlength: 2
                },
                description: {
                    required: true
                }
            },
            
            messages: {
                title: {
                    notNone: nofirstchoice
                },
                firstname: {
                    required: required_field, 
                    minlength: min2
                },
                surname: {
                    required: required_field,
                    minlength: min2
                },
                username: {
                    required: required_field,
                    minlength: min6,
                    uniqueUserName: usernametaken
                },
                password: {
                    required: required_field,
                    minlength: min8
                },
                confirmpassword: {
                    required: required_field,
                    minlength: min8,
                    pwEqual: passnomatch
                },
                email: {
                    required: required_field,
                    email: validemail
                },
                birthdate: {
                    required: required_field
                },
                address: {
                    required: required_field,
                    minlength: min8
                },
                city: {
                    required: required_field
                },
                state: {
                    required: required_field
                },
                postalcode: {
                    required: required_field
                },
                orgcomp: {
                    required: required_field
                },
                jobtitle: {
                    required: required_field
                },
                occupationtype: {
                    required: required_field
                },
                workphone: {
                    required: required_field
                },
                mobilephone: {
                    required: required_field
                },
                description: {
                    required: min100
                }
            }
        });
    });

    // Function for saving the user data
    jQuery("#form_edituser").submit(function(e) {
        if(jQuery("#form_edituser").valid()){ 
            e.preventDefault();
            if (jQuery("#captcha").length){
                redirect = true;
                redirection = '';
                // The captcha has not been entered so disable submit.
                if (jQuery("#captcha").val() == "") {
                    //alert("novalue");
                    jQuery("#save_results").html('<span class="error">Please enter the captcha. You left it blank.</span>');//.fadeOut('5000');
                    allow_submit=false;
                // The captcha has been entered so verify it
                } else {
                    //alert(jQuery("#captcha").val())
                    captcha = jQuery("#captcha").val();

                    jQuery.ajax({
                        url: 'index.php?module=oeruserdata&action=verifycaptcha',
                        type: "POST",
                        data: 'captcha='+captcha,
                        success: function(msg) {
                            if (msg !== 'ok') {
                                allow_submit=false;
                                jQuery("#save_results").html('<span class="error">Invalid captcha: Please try again.</span>');//.fadeOut('5000');
                                // Reset the captcha.
                                captcha_img = "index.php?module=oeruserdata&action=showcaptcha";
                                jQuery("#img_captcha").attr("src", captcha_img);
                                jQuery("#captcha").val("");
                            // Now how do I stop the form from submitting???????/
                            } else {
                                allow_submit=true;
                            }

                        }

                    });
                }
            }
            if (allow_submit==true) {
                jQuery("#submitUser").attr("disabled", "disabled");
                jQuery("#save_results").html('<img src="skins/_common/icons/loading_bar.gif" alt=""Saving..." />');
                data_string = jQuery("#form_edituser").serialize();
                jQuery.ajax({
                    url: 'index.php?module=oeruserdata&action=userdetailssave',
                    type: "POST",
                    data: data_string,
                    success: function(msg) {    
                        //if the result is this wrong, then there is trouble
                        if(msg.length > 100){
                            jQuery("#save_results").html("");
                            showRegisterFail();
                            return;
                        }
                        jQuery("#submitUser").attr("disabled", "");
                        if(msg == 'SUCCESS') {
                            if (redirect == true) {
                                jQuery("#id").val(msg);
                               
                                jQuery("#save_results").html("");
                               
                                jQuery("#mode").val('edit');
                                //Show success dialog
                                if (jQuery("#edmode").val()!=='edit'){
                                    showRegisterSuccess();
                                } else {
                                    showUpdateSuccess();
                                }
                                
                                // Redirect after anonymous save
                                window.location = 'index.php?module=oer&action=login';
                            } else {
                                // Update the information area 
                                // (msg is the id of the record on success)
                                //jQuery("#save_results").html('<span class="success">' + status_success + ": " + msg + '</span>');//.fadeOut('5000');
                                // Change the id field to be the id that is returned as msg & mode to edit
                                jQuery("#id").val(msg);
                              
                                jQuery("#save_results").html("");
                                //Show success dialog
                               
                                jQuery("#mode").val('edit');
                                if (jQuery("#edmode").val()!=='edit'){
                                    showRegisterSuccess();
                                } else {
                                    showUpdateSuccess();
                                }
                                
                            }
                        } else {
                            jQuery("#save_results").html("");
                            showRegisterFail();
                        //jQuery("#save_results").html('<span class="error">' + status_fail + ": " + msg + '</span>');//.fadeOut('5000');
                        }
                    }
                });
            }
        }
    });
});