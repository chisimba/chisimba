/* 
 * Javascript to support the announcement viewer
 *
 * Written by Derek Keats derek [@@@@] dkeats.com
 * Started on: July 21, 2012, 6:52 pm
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
        
    });
    jQuery(document).on("click", ".an_vw_msg", function(e) {
        var dId = jQuery(this).attr("id");
        jQuery("#msg_"+dId).slideToggle(700);
        return false;
    });
});