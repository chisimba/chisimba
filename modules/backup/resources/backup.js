/* 
 * Javascript to support backup
 *
 * Written by Derek Keats derek@dkeats.com
 * STarted on: April 4, 2012, 5:08 pm
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
 * Do the backup and ajax update the appropriate divs.
 *
 */
jQuery(function() {
    jQuery(document).ready(function() {
        jQuery("#backuplink").click(function(e) {
            jQuery.ajax({
               beforeSend: function (request) {
                    if (!confirm("You really want to run a backup right now?")) {
                        return FALSE;
                    }
               },
               type: "POST",
               url: "index.php?module=backup&action=dobackup",
               cache: false,
               success: function(ret){
                   if(ret == "ERROR") {
                       alert('FAILED: '+ret);
                   } else {
                       jQuery("#results_area").html(ret);
                       //alert(ret);
                   }
              }
            });
            return false;
        });
    });
})