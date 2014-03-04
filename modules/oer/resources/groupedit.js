/* 
 * Javascript to support group edit
 *
 * Written by Derek Keats derek@dkeats.com
 * Started on: December 18, 2011, 8:48 am
 *
 *
 */

/**
 *
 * jQuery code belongs inside this function.
 *
 */
jQuery(function() {

    // Things to do on loading the page.
    jQuery(document).ready(function() {
        // Add jQuery Validation to form
        jQuery("#form_groupFrom1").validate();
    });

    // Function for saving the institutional data
    jQuery("#form_groupEditor").submit(function(e) {
        if(jQuery("#form_groupEditor").valid()){ 
            e.preventDefault();
            jQuery("#submitGroup").attr("disabled", "disabled");
            jQuery("#save_results").html('<img src="skins/_common/icons/loading_bar.gif" alt=""Saving..." />');
            data_string = jQuery("#form_institutionEditor").serialize();
            jQuery.ajax({
                url: 'index.php?module=oer&action=groupsave',
                type: "POST",
                data: data_string,
                success: function(msg) {
                    jQuery("#submitGroup").attr("disabled", "");
                    if(msg !== "ERROR_DATA_INSERT_FAIL") {
                        // Update the information area 
                        // (msg is the id of the record on success)
                        jQuery("#save_results").html('<span class="success">' + status_success + ": " + msg + '</span>');
                        // Change the id field to be the id that is returned as msg & mode to edit
                        jQuery("#id").val(msg);
                        jQuery("#mode").val('edit');
                    } else {
                        //alert(msg);
                        alert(status_fail);
                    }
                }
            });
        }
    });
});