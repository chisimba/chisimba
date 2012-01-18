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
    jQuery('.LOGIN_DROP').live("click", function(){
        jQuery('#LOGIN_BLOCK').toggle('slow', function() {
            // Animation complete, add a red border
            //jQuery('#LOGIN_BLOCK').css("border", "1px dashed red")
        });
    });

    // Set the initial value of remember to off
    jQuery("#input_remember").val('off');

    jQuery("#loginButton").live("click", function(){
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
                //alert(ret);
                var failure;
                switch(ret) {
                    case "yes":
                        window.location = 'index.php?module='+theModule;
                        break;
                        
                    case "no":
                        failure='&nbsp;&nbsp;<div class=\'error\'>'+failedMsg+'</div>';
                        jQuery("#login_block_wrapper").html(tmp);
                        jQuery('.error').remove();
                        jQuery("#login_block_wrapper").append(failure);
                        break;

                    case "nononceindb":
                        failure='&nbsp;&nbsp;<div class=\'error\'>Illegal login attempt - type 1</div>';
                        jQuery("#login_block_wrapper").html(tmp);
                        jQuery('.error').remove();
                        jQuery("#login_block_wrapper").append(failure);
                        break;

                    case  "noncemissing":
                        failure='&nbsp;&nbsp;<div class=\'error\'>Illegal login attempt - type 2</div>';
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
                        failure='&nbsp;&nbsp;<div class=\'error\'>Unknown error.</div>';
                        jQuery("#login_block_wrapper").html(tmp);
                        jQuery('.error').remove();
                        jQuery("#login_block_wrapper").append(failure);
                        break;
                }
            }
        });
    });

        // For the username box
    jQuery("#input_username").live("click", function(){
        jQuery(this).css("background","white");
        jQuery(this).css("border","1px dotted red");
    });
    jQuery("#input_username").live("blur", function(){
        jQuery(this).css("background","#F8F8F8");
        jQuery(this).css("border","1px solid #D8D8D8");
    });


    // For the password box
    jQuery("#input_password").live("click", function(){
        jQuery(this).css("background","white");
        jQuery(this).css("border","1px dotted red");
    });
    jQuery("#input_password").live("blur", function(){
        jQuery(this).css("background","#F8F8F8");
        jQuery(this).css("border","1px solid #D8D8D8");
    });

    // For the password box
    jQuery("#input_remember").live("click", function(){
        jQuery("#input_remember").val('on')
    });

    // For the captcha.
    jQuery("#captchaButton").live("click", function(){
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