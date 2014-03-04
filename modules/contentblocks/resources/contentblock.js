/* 
 * Javascript to support the textblock module
 *
 * Written by Derek Keats derek@dkeats.com
 * STarted on: January 4, 2012, 3:53 pm
 *
 * The following parameters need to be set in the
 * PHP code for this to work:
 *
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
    
    // Show the blocks as a drop down, roll up
    jQuery(document).on("click", '.BLOCK_TITLE', function(){
        id=jQuery(this).attr("id");
        fixedid = id.replace("BLOCK_", "");
        cntid = '#BLCONT_'+fixedid;
        jQuery(cntid).toggle('slow', function() {
            // Animation complete, add a red border
            jQuery(cntid).css("border", "1px dashed red")
        });
    });
    
    // The function for deleting a post
    jQuery(".dellink").click(function(e) {
        var dId = jQuery(this).attr("id");
        //alert('clicked ' + dId);
        jQuery.ajax({
           beforeSend: function (request) {
                if (!confirm("You really want to delete?")) {
                    return false;
                }
           },
           type: "POST",
           url: "index.php?module=contentblocks&action=deleteajax&id=" + dId,
           cache: false,
           success: function(ret){
               if(ret == "RECORD_DELETED") {
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