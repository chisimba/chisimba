/* 
 * Javascript to support mynotes
 *
 * Written by Nguni Phakela nguni52@gmail.com
 * Started on: March 16, 2012, 7:33 am
 *
 */

jQuery(function() {
     
    // Things to do on loading the page.
    jQuery(document).ready(function() {
        var viewAll = jQuery("#viewall").val();
        var nextPage = jQuery("#nextpage").val();
        var prevPage = jQuery("#prevpage").val();
        
        if(viewAll === undefined) {
            // Load notes into the dynamic area. These are the latest two notes
            // with the first 200 words showing
            if(isNaN(nextPage)) {
                nextPage="7";
            } else {
                nextPage =parseInt(nextPage) + 6;
            }
            if(isNaN(prevPage)) {
                if(!isNaN(nextPage)) {
                    prevPage = parseInt(nextPage)-5;
                } else {
                    prevPage="2";
                }
            } else {
                nextPage = prevPage-1;
                prevPage = parseInt(prevPage) - 6;
            }
            
            jQuery.ajax({
                type: "GET",
                url: "index.php?module=mynotes&action=ajaxGetNotes",
                data: "nextnotepage="+nextPage+"&prevnotepage="+ prevPage,
                success: function(ret) {
                    jQuery("#middledynamic_area").html(ret); 
                }
            });
        } else {
            // this is a list of all the notes that I have.
            jQuery.ajax({
                type: "GET",
                url: "index.php?module=mynotes&action=ajaxGetNotes&viewall=true",
                success: function(ret) {
                    jQuery("#middledynamic_area").html(ret); 
                }
            });
        }
    });
    
    //Change these values to style your modal popup
    var align = 'center';       //Valid values; left, right, center
    var top = 100; 		//Use an integer (in pixels)
    var width = 500; 		//Use an integer (in pixels)
    var padding = 10;		//Use an integer (in pixels)
    var backgroundColor = '#FFFFFF'; 	//Use any hex code
    var source = '<h1>Hello World!</h1><br />'; 		//Refer to any page on your server, external pages are not valid e.g. http://www.google.co.uk
    var borderColor = '#333333'; 	//Use any hex code
    var borderWeight = 4; 		//Use an integer (in pixels)
    var borderRadius = 5; 		//Use an integer (in pixels)
    var fadeOutTime = 300; 		//Use any integer, 0 = no fade
    var disableColor = '#666666'; 	//Use any hex code
    var disableOpacity = 40; 		//Valid range 0-100
    var loadingImage = 'packages/mynotes/resources/images/loading.gif';		//Use relative path from this page
	
    //This method initialises the modal popup
    jQuery(".modal").click(function() {
        modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, source, loadingImage);
    });
		
    //This method hides the popup when the escape key is pressed
    jQuery(document).keyup(function(e) {
        if (e.keyCode == 27) {
            closePopup(fadeOutTime);
        }
    });
});

function confirmDelete() {
    var message = "Are you sure you want to delete this note?";
    var answer = confirm(message);
    if (answer){
        window.location.href = jQuery("#delete").attr("href") + "&confirm=yes";
        return false;
    }
    
    return false;  
}