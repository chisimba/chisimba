/* 
 * Javascript to support the makemodle functionality
 * in Chisimba
 *
 * Written by Derek Keats
 *
 * The following parameters need to be set in the
 * PHP code for this to work:
 *
 * @todo
 * 
 *
 */

/**
 *
 * Act on form events
 *
 */
jQuery(function() {

    var loadingImage = '<img src="skins/_common/icons/loading_bar.gif" alt="Loading..." />';
    
    // Things to do on loading the page
    jQuery(document).ready(function() {
      // Set up the right content.
        jQuery("#middledynamic_area").load(packages+'makemodule/resources/forms/startform.html');
    });

    jQuery(document).on("change", '#templatetype', function(){
        if (jQuery("#templatetype").val() == 'dynamiccanvas') {
            //alert("Dynamic canvas is not ready yet");
            jQuery("#canvastyes").load(packages+'makemodule/resources/forms/dynamictype.html')
        }
    });

    // When the createmodule button is clicked
    jQuery(document).on("click", "#createmodule", function(){
        // Submit the form
        var modulecode=jQuery("#modulecode").val();
        var modulename=jQuery("#modulename").val();
        var description=jQuery("#description").val();
        var templatetype=jQuery("#templatetype").val();
        if (jQuery("#templatetype").val() == 'dynamiccanvas') {
            templatetype=jQuery("#canvastype").val();
        }
        //alert(templatetype);
        //alert(jQuery("#canvastype").val());
        var mydata = "modulecode="+modulecode+"&modulename="+modulename+"&description="+description+"&templatetype="+templatetype;
        jQuery("#form_wrapper").html(loadingImage);
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=makemodule&action=save",
            data: mydata,
            success: function(ret) {
                //alert(mydata);
                switch(ret) {
                    case "ok":
                        var txt = "<div class='warning'>Your module has been created. You can now go to module catalogue and install it. Make sure that you click 'Update catalogue' before you search for your new module.</div>"
                        jQuery("#form_wrapper").html(txt);
                        break;
                    default:
                        failure='&nbsp;&nbsp;<div class=\'error\'>Unknown error: ' + ret + '</div>';
                        jQuery("#form_wrapper").html(ret);
                        jQuery("#form_wrapper").append(failure);
                        break;
                }
            }
        });
    });


});