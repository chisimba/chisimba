/* 
 * Javascript to support themes managed
 *
 * Written by David Wafula
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
        jQuery("#form_umbrellaThemesForm").validate();
        jQuery("#form_themesForm").validate();
      
    });
    
    
    
        // Function for saving the institutional data
    jQuery("#form_umbrellaThemesForm").submit(function(e) {
      
        if(jQuery("#form_umbrellaThemesForm").valid()){ 
           
            e.preventDefault();
            jQuery("#submitUmbrellaTheme").attr("disabled", "disabled");
            jQuery("#save_results").html('<img src="skins/_common/icons/loading_bar.gif" alt=""Saving..." />');
            data_string = jQuery("#form_umbrellaThemesForm").serialize();
            jQuery.ajax({
                url: 'index.php?module=oer&action=saveumbrellatheme',
                type: "POST",
                data: data_string,
                success: function(msg) {
                    jQuery("#submitUmbrellaTheme").attr("disabled", "");
                    if(msg !== "ERROR_DATA_INSERT_FAIL") {
                        // Update the information area 
                        // (msg is the id of the record on success)
                        jQuery("#save_results").html('<span class="success">' + status_success + ": " + msg + '</span>');
                        // Change the id field to be the id that is returned as msg & mode to edit
                        jQuery("#id").val(msg);
                        jQuery("#mode").val('edit');
                        
                        window.location="?module=oer&action=viewthemes";
                          
                    } else {
                        //alert(msg);
                        alert(status_fail);
                    }
                }
            });
        }
    });
    
    
    
    
            // Function for saving the institutional data
    jQuery("#form_themesForm").submit(function(e) {
      
        if(jQuery("#form_themesForm").valid()){ 
           
            e.preventDefault();
            jQuery("#submitTheme").attr("disabled", "disabled");
            jQuery("#save_results").html('<img src="skins/_common/icons/loading_bar.gif" alt=""Saving..." />');
            data_string = jQuery("#form_themesForm").serialize();
            jQuery.ajax({
                url: 'index.php?module=oer&action=savetheme',
                type: "POST",
                data: data_string,
                success: function(msg) {
                    jQuery("#submitTheme").attr("disabled", "");
                    if(msg !== "ERROR_DATA_INSERT_FAIL") {
                        // Update the information area 
                        // (msg is the id of the record on success)
                        jQuery("#save_results").html('<span class="success">' + status_success + ": " + msg + '</span>');
                        // Change the id field to be the id that is returned as msg & mode to edit
                        jQuery("#id").val(msg);
                        jQuery("#mode").val('edit');
                        
                        window.location="?module=oer&action=viewthemes";
                          
                    } else {
                        //alert(msg);
                        alert(status_fail);
                    }
                }
            });
        }
    });

});

