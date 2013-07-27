/* 
 * Javascript to support the login functionality
 * in Chisimba
 *
 * Written by Derek Keats
 *
 * The following parameters need to be set in the
 * PHP code for this to work:
 *
 * loadingImage
 * 
 *
 */

/**
 *
 * Act on form events
 *
 */
jQuery(function() {
    
    // Show the login block as a drop down, roll up
    jQuery(document).on("click", '.LOGIN_DROP', function(){
        jQuery('#LOGIN_BLOCK').slideToggle('slow', function() {
            // Anything else we want to do?
        });
    });

    // Set the initial value of remember to off
    jQuery("#input_remember").val('off');

    jQuery(document).on("click", "#loginButton", function(){
        // Capture what's in the div so we can restore it
        var tmp = jQuery("#login_block_wrapper").html();
        var username = jQuery("#input_username").val();
        var password = jQuery("#input_password").val();
        var remember = jQuery("#input_remember").val();
        var nonce = jQuery("#nonce").val();
        jQuery("#login_block_wrapper").html(loadingImage);
        var mydata = "username="+username+"&password="+password+"&remember="+remember
        //alert(mydata);
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=login&theModule="+theModule+"&action=ajaxlogin&nonce="+nonce,
            data: mydata,
            success: function(ret) {
                var errMsg;
                //alert(ret);
                switch(ret) {
                    case "yes":
                        window.location = 'index.php?module='+theModule;
                        break;
                    // All below are failures
                    case "accountinactive":
                        failure='&nbsp;&nbsp;<div class=\'error\'>'+accountinactive+'</div>';
                        jQuery("#login_block_wrapper").html(tmp);
                        jQuery('.error').remove();
                        jQuery("#login_block_wrapper").append(failure);
                        break;
                    case "wrongpassword":
                        failure='&nbsp;&nbsp;<div class=\'error\'>'+wrongpassword+'</div>';
                        jQuery("#login_block_wrapper").html(tmp);
                        jQuery('.error').remove();
                        jQuery("#login_block_wrapper").append(failure);
                        break;
                    case "noldap":
                        failure='&nbsp;&nbsp;<div class=\'error\'>'+noldap+'</div>';
                        jQuery("#login_block_wrapper").html(tmp);
                        jQuery('.error').remove();
                        jQuery("#login_block_wrapper").append(failure);
                        break;
                    case "noaccount":
                        failure='&nbsp;&nbsp;<div class=\'error\'>'+noaccount+'</div>';
                        jQuery("#login_block_wrapper").html(tmp);
                        jQuery('.error').remove();
                        jQuery("#login_block_wrapper").append(failure);
                        break;
                        
                    case "no":
                        failure='&nbsp;&nbsp;<div class=\'error\'>'+lino+'</div>';
                        jQuery("#login_block_wrapper").html(tmp);
                        jQuery('.error').remove();
                        jQuery("#login_block_wrapper").append(failure);
                        break;

                    case "nononceindb":
                        failure='&nbsp;&nbsp;<div class=\'error\'>'+nononceindb+'</div>';
                        jQuery("#login_block_wrapper").html(tmp);
                        jQuery('.error').remove();
                        jQuery("#login_block_wrapper").append(failure);
                        break;

                    case  "noncemissing":
                        failure='&nbsp;&nbsp;<div class=\'error\'>'+noncemissing+'</div>';
                        jQuery("#login_block_wrapper").html(tmp);
                        jQuery('.error').remove();
                        jQuery("#login_block_wrapper").append(failure);
                        break;

                    case  "loginsdisabled":
                        //failure='&nbsp;&nbsp;<div class=\'error\'>Logins are disabled.'+captcha+'</div>';
                        jQuery('#login_block_wrapper').load('index.php?module=login&action=getcapajax');
                        //jQuery('.error').remove();
                        //jQuery("#login_block_wrapper").html(failure);
                        break;

                    default:
                        failure='&nbsp;&nbsp;<div class=\'error\'>'+lino+'</div>';
                        jQuery("#login_block_wrapper").html(tmp);
                        jQuery('.error').remove();
                        jQuery("#login_block_wrapper").append(failure);
                        break;
                }

            }
        });
    });

        // For the username box
    jQuery(document).on("click", "#input_username", function(){
        jQuery(this).css("background","white");
        jQuery(this).css("border","1px dotted red");
    });
    jQuery(document).on("blur", "#input_username", function(){
        jQuery(this).css("background","#F8F8F8");
        jQuery(this).css("border","1px solid #D8D8D8");
    });


    // For the password box
    jQuery(document).on("click", "#input_password", function(){
        jQuery(this).css("background","white");
        jQuery(this).css("border","1px dotted red");
    });
    jQuery(document).on("blur", "#input_password", function(){
        jQuery(this).css("background","#F8F8F8");
        jQuery(this).css("border","1px solid #D8D8D8");
    });

    // For the password box
    jQuery(document).on("click", "#input_remember", function(){
        jQuery("#input_remember").val('on')
    });

    // For the captcha.
    jQuery(document).on("click", "#captchaButton", function(){
        var captcha = jQuery("#captcha").val();
        jQuery.ajax({
            type: "POST",
            data: "captcha="+captcha,
            url: "index.php?module=login&action=verifycaptcha",
            success: function(ret) {
                switch(ret) {
                    case "ok":
                        jQuery('#human_wrapper').load('index.php?module=login&action=loginboxajax');
                        break;
                    case "notok":
                        jQuery('#human_wrapper').append('&nbsp;&nbsp;<div class=\'error\'>Invalid captcha.</div>');
                        setInterval(function() {
                            // sleep 2 seconds
                            jQuery('.error').fadeOut('slow');
                        }, 2000 );
                        break;
                }
            }
        });
    });
});