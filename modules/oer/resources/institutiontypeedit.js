/* 
 * Javascript to support institution edit
 *
 * Written by Derek Keats derek@dkeats.com
 * Started on: December 18, 2011, 8:48 am
 *
 *
 */


var data_string;


/**
 *
 * jQuery code belongs inside this function.
 *
 */
jQuery(function() {

    // Things to do on loading the page.
    jQuery(document).ready(function() {
        // Add jQuery Validation to form
        jQuery("#form_institutionTypeEditor").validate();
    });

    // Function for saving the institutional data
    jQuery("#form_institutionTypeEditor").submit(function(e) {
        if(jQuery("#form_institutionTypeEditor").valid()){ 
            e.preventDefault();
            jQuery("#submitType").attr("disabled", "disabled");
            jQuery("#save_results").html('<img src="skins/_common/icons/loading_bar.gif" alt=""Saving..." />');
            data_string = jQuery("#form_institutionTypeEditor").serialize();
            jQuery.ajax({
                url: 'index.php?module=oer&action=institutiontypesave',
                type: "POST",
                data: data_string,
                success: function(msg) {
                    jQuery("#submitInstitution").attr("disabled", "");
                    if(msg !== "ERROR_DATA_INSERT_FAIL") {
                        // Update the information area 
                        // (msg is the id of the record on success)
                        jQuery("#save_results").html('<span class="success">' + status_success + ": " + msg + '</span>');
                        // Change the id field to be the id that is returned as msg & mode to edit
                        jQuery("#id").val(msg);
                        jQuery("#mode").val('edit');
                    } else {
                        //alert(msg);
                        jQuery("#save_results").html('<span class="error">' + status_fail + ": " + msg + '</span>');
                    }
                }
            });
        }
    });
});