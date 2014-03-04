/* 
 * Javascript to support slate
 *
 * Written by Derek Keats derek@dkeats.com
 * STarted on: January 25, 2012, 3:29 pm
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
        
    });
    
    // The function for deleting a post
    jQuery(document).on("click", ".dellink", function(e) {
        var dId = jQuery(this).attr("id");
        //alert('clicked ' + dId);
        jQuery.ajax({
           beforeSend: function (request) {
                if (!confirm("You really want to delete?")) {
                    return FALSE;
                }
           },
           type: "POST",
           url: "index.php?module=slate&action=delete&id=" + dId,
           cache: false,
           success: function(ret){
               if(ret == "RECORD_DELETED") {
                   //alert("#ROW_"+dId);
                   jQuery("#ROW_"+dId).slideUp('slow', function() {
                       jQuery("#ROW_"+dId).remove();
                   })
               } else {
                   alert(ret);
               }
          }
        });
        return false;
    });
});