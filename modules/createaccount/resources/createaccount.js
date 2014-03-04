/* 
 * Javascript to support createaccount
 *
 * Written by Administrative User admin@localhost.local
 * STarted on: January 26, 2011, 3:07 pm
 *
 * The following parameters need to be set in the
 * PHP code for this to work:
 *
 * @todo
 *   List your parameters here so you won't forget to add them
 *
 */

// A debugging function
function seeData(data) {
    alert(data);
}

/**
 *
 * Put your jQuery code inside this function.
 *
 */


jQuery(document).ready(function(){
    //jQuery('#register').validate();
    jQuery('#register').validate({
        rules: {
            username: "required",
            email: {
                required: true,
                email: true
	      },
	      password: {
	        required: true
	      }
	},
	messages: {
            email: "Please enter a valid email address."
	}
    });
    return false;
    //alert("TEST document.ready() FIRED");
});

jQuery(function() {

    // What happens when the Create account link is clicked
    jQuery("#reglinkbutton").live("click", function(){
        jQuery("#putregisterform").load('index.php?module=createaccount&action=getbasicform');
        //jQuery.get('index.php?module=createaccount&action=getbasicform', seeData);
        //alert(txt);
    });

    
    // What happens when the Register button is clicked on the form
    jQuery("#create-user").live("click", function(){
        // Oddly...if processing halts on this error, the validation works. No idea why.
        if ((userName.length = 0) || (passWord.length=0)) {
            err = true;
        }
        var dataString = 'username=' + jQuery('#username').val() +
                         '&password=' + jQuery('#password').val() +
                         '&request_captcha=' + jQuery('#request_captcha').val() +
                         '&captcha=' + jQuery("input[name=captcha]").val() +
                         '&email=' + jQuery('#email').val() +
                         '&module=userregistration&action=ajax_register';
        var email = jQuery('#email').val();
        var baseUri = 'http://localhost/ch/index.php?';

        jQuery.ajax({
            type: "POST",
            url: baseUri,
            data: dataString,
            success: function(data) {
                alert(data);
                var objData = jQuery.parseJSON(data);

                if(objData.success){
                    //formSuccess(email);
                    alert(objData.message);
                }else{
                    alert(objData.message);
                }
          }
       });
       return false;
    });
});