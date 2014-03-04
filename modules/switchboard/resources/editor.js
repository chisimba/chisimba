/* 
 * Javascript to support switchboard
 *
 * Written by Derek Keats derek@dkeats.com
 * STarted on: January 4, 2012, 3:53 pm
 *
 * The following parameters need to be set in the
 * PHP code for this to work:
 *
 * @todo
 *   List your parameters here so you won't forget to add them
 *
 */

/**
 *
 * Put your jQuery code inside this function.
 *
 */
jQuery(function() {

    
    // Things to do on loading the page.
    jQuery(document).ready(function() {
      // Add jQuery Validation to form
        jQuery("#form_linkEditor").validate();
    });
    
    // Function for saving the link data
    jQuery("#form_linkEditor").submit(function(e) {
        if(jQuery("#form_linkEditor").valid()){ 
            e.preventDefault();
            jQuery("#submitLink").attr("disabled", "disabled");
            jQuery("#save_results").html('<img src="skins/_common/icons/loading_bar.gif" alt=""Saving..." />');
            data_string = jQuery("#form_linkEditor").serialize();
            jQuery.ajax({
                url: 'index.php?module=switchboard&action=linksave',
                type: "POST",
                data: data_string,
                success: function(msg) {
                    jQuery("#submitLink").attr("disabled", "");
                    if(msg !== "ERROR_DATA_INSERT_FAIL") {
                        // Update the information area 
                        // (msg is the id of the record on success)
                        jQuery("#save_results").html('<span class="success">' + status_success + ": " + msg + '</span>');
                        // Change the id field to be the id that is returned as msg & mode to edit
                        jQuery("#id").val(msg);
                        jQuery("#mode").val('edit');
                        jQuery(".conditional_add").css('visibility', 'visible');
                    } else {
                        //alert(msg);
                        jQuery("#save_results").html('<span class="error">' + status_fail + ": " + msg + '</span>');
                    }
                }
            });
        }
    });

});