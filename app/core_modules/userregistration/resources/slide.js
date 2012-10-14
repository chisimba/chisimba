$(document).ready(function() {
	
	// Expand Panel
	$("#open").click(function(){
		$("div#panel").slideDown("slow");
	
	});	
	
	$("#openl").click(function(){
		$("div#logoutpanel").slideDown("slow");
	
	});	
	
	// Collapse Panel
	$("#close").click(function(){
		$("div#panel").slideUp("slow");	
	});	

	$("#closel").click(function(){
		$("div#logoutpanel").slideUp("slow");	
	});
	
	$("#cancel").click(function(){
		$("div#logoutpanel").slideUp("slow");	
	});
	
	$("#logout").click(function(){
	    $(this).html('Bye Bye...').fadeTo(900,1);  
	   // alert('bye bye');
	    jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=security&action=logoff",
            success: function(msg){              
                 document.location=successUrl;
				/*jQuery('#'+groupId+'_list').html(msg);				
                if ('function' == typeof window.adjustLayout) {
                    adjustLayout();
                }*/
            },
			/*beforeSend: function(msg){    				
                jQuery('#'+groupId+'_list').html(showLoading());
            },
			complete: function(msg){              
                getGroupName(groupId)
            }*/
        });	
	});
	
	// Switch buttons from "Log In | Register" to "Close Panel" on click
	$("#toggle a").click(function () {
		$("#toggle a").toggle();
	});		
	
	$("#login_form").submit(function()
    {       
            //remove all the class add the messagebox classes and start fading
           // $("#msgbox").removeClass().addClass('messagebox').text('Validating....').fadeIn(1000);
            //check the username exists or not from ajax
            $("#submit").fadeTo(200,0.1,function() //start fading the messagebox
                {
                  //add message and change the class of the box and start fading
                  $(this).html('Validating...').fadeTo(900,1);                
                 
                });
                //,rand:Math.random() 
            $.post(loginUrl,{ username:$('#username').val(),password:$('#password').val()} ,function(data)
            {
              
              if(data=='yes') //if correct login detail
              {
                    $("#submit").fadeTo(200,0.1,function()  //start fading the messagebox
                    {
                      //add message and change the class of the box and start fading
                      $(this).html('Logging in.....').fadeTo(900,1,
                      function()
                      {
                         //redirect to secure page
                         document.location=successUrl;
                      });
                    });
              }
              else
              {                    
                    $("#submit").fadeTo(200,0.1,function() //start fading the messagebox
                    {
                      //add message and change the class of the box and start fading
                      $(this).html('Login Failed!!').fadeTo(900,1);
                      setTimeout( function() { $("#submit").html('Try Again').fadeTo(900,1); }, 5000);
                     
                    });
              }
           });
           return false;//not to post the  form physically
    });
    
    $("#password").blur(function()
    {
            $("#login_form").trigger('submit');
    });
		
});

function logout(groupId)
    {
           
        jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=groupadmin&action=ajaxgetgroupcontent&groupid="+groupId,
            success: function(msg){              
				jQuery('#'+groupId+'_list').html(msg);				
                if ('function' == typeof window.adjustLayout) {
                    adjustLayout();
                }
            },
			beforeSend: function(msg){    				
                jQuery('#'+groupId+'_list').html(showLoading());
            },
			complete: function(msg){              
                getGroupName(groupId)
            }
        });	
        
    }