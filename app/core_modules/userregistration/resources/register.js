//$(function() {

    
$(document).ready(function() {

		// a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
		$("#dialog").dialog("destroy");
		
		var name = $("#name"),
			email = $("#email"),
			password = $("#password"),
			allFields = $([]).add(name).add(email).add(password),
			tips = $(".validateTips");

		function updateTips(t) {
			tips
				.text(t)
				.addClass('ui-state-highlight');
			setTimeout(function() {
				tips.removeClass('ui-state-highlight', 1500);
			}, 500);
		}

		function checkLength(o,n,min,max) {

			if ( o.val().length > max || o.val().length < min ) {
				o.addClass('ui-state-error');
				updateTips("Length of " + n + " must be between "+min+" and "+max+".");
				return false;
			} else {
				return true;
			}

		}

		function checkRegexp(o,regexp,n) {

			if ( !( regexp.test( o.val() ) ) ) {
				o.addClass('ui-state-error');
				updateTips(n);
				return false;
			} else {
				return true;
			}

		}		
    
		$("#dialog").dialog({
			autoOpen: false,
			height: 440,
			show: 'slide',
			hide: 'slide',
			width: 350,
			modal: true,
			buttons: {
				'Register': function() {  
				
    					var bValid = true;
    					allFields.removeClass('ui-state-error');
    
    					bValid = bValid && checkLength(name,"username",3,16);
    					bValid = bValid && checkLength(email,"email",6,80);
    					bValid = bValid && checkLength(password,"password",5,16);
    
    					bValid = bValid && checkRegexp(name,/^[a-z]([0-9a-z_])+$/i,"Username may consist of a-z, 0-9, underscores, begin with a letter.");
    					// From jquery.validate.js (by joern), contributed by Scott Gonzalez: http://projects.scottsplayground.com/email_address_validation/
    					bValid = bValid && checkRegexp(email,/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,"eg. ui@jquery.com");
    					bValid = bValid && checkRegexp(password,/^([0-9a-zA-Z])+$/,"Password field only allow : a-z 0-9");
    					
    					if (bValid) {		
    					    if($('#dialog_form').is(':hidden') == false){
    						  submitForm();
    					    }
    					}
    				},
    				//Cancel: //function() {
    					   //$(this).dialog('close');
    				    //}
    			},
    			close: function() {
    				allFields.val('').removeClass('ui-state-error');
    				
    			}
		});	
		
		$('#create-user')
			.button()
			.click(function() {			
				$('#dialog').dialog('open');
			});

	
    	
    function submitForm()
    {	    
        
       // alert('form submitted');
        var dataString = 'username=' + $('#name').val() + 
                         '&password=' + $('#password').val() + 
                         '&request_captcha=' + $('#request_captcha').val() +
                         '&captcha=' + $("input[name=captcha]").val() +
                         '&email=' + $('#email').val() +
                         '&module=userregistration&action=ajax_register';
        var email = $('#email').val();
       
       $.ajax({
          type: "POST",
          url: baseUri,
          data: dataString,
          success: function(data) {
            var objData = $.parseJSON(data);
            
            if(objData.success){                 
                formSuccess(email);
            }else{
                updateTips(objData.message);
            }
            
          }
         });
         
         return false;
         
    }
    
    function formSuccess(email){
        $('#dialog_form').hide();
        
       
        //$('#dialog_form').html("<div id='message'></div>");
            $('#message').html("<h2><img id='checkmark' src='skins/_common/icons/check.png' />Success!</h2><h3>")
            .append("<p>  An email has been sent to <b>"+ email +"</b> with your details</p><p>You may now proceed to login</p>")
            .hide()
            .fadeIn(10500, function() {
              $('#message').append("");
                })
            .fadeOut('slow', function(){
                    $('#dialog').dialog('close');
                    $('#dialog_form').show();
                     $('#message').hide();
                })
            ;
    }
    $('#redraw')
        .button()
		.click(function() {			
		      redrawfn();
		      $('#request_captcha').val('');
			});
    
    function redrawfn () {
        var url = 'index.php';
        var pars = 'module=security&action=generatenewcaptcha';
        var myAjax = new Ajax.Request( url, {method: 'get', parameters: pars, onComplete: showResponse} );
    }
    function showLoad () {
        $('load').style.display = 'block';
    }
    function showResponse (originalRequest) {
        var newData = originalRequest.responseText;
        $('#captchaDiv').html(newData);
    }
	
});
/*
function init () {
    $('input_redraw').onclick = function () {
        redraw();
    }
}*/
