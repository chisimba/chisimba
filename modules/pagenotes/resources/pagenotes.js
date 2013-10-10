/* 
 * Javascript to support pagenotes
 *
 * Written by Derek Keats <derek@dkeats.com>
 * STarted on: February 23, 2012, 12:06 pm
 *
 */
/**
 *
 * Put your jQuery code inside this function.
 *
 */

var curMode = 'add';

jQuery(function() {
    
    // Things to do on loading the page.
    jQuery(document).ready(function() {
        // Add jQuery Validation to form
        jQuery("#form_noteEditor").validate();
    });
    
    // Edit a page note
    jQuery(document).on("click", ".pagenote_editicon", function(e) {
        curMode='edit';
        var dId = jQuery(this).attr("id");
        jQuery.get('index.php?module=pagenotes&action=ajaxgetrawnote&id='+dId, function(data){
            jQuery("#pagenote_notearea").val(data);
            jQuery("#pagenotes_mode").val('edit');
            jQuery("#id").val(dId);
        });
    });
    
    // Delete a page note
    jQuery(document).on("click", ".pagenote_delicon", function(e) {
        var dId = jQuery(this).attr("id");
        //alert('clicked ' + dId);
        jQuery.ajax({
           beforeSend: function (request) {
                if (!confirm("You really want to delete?")) {
                    return FALSE;
                }
           },
           type: "POST",
           url: "index.php?module=pagenotes&action=ajaxdeletenote&id=" + dId,
           cache: false,
           success: function(ret){
               if(ret == "RECORD_DELETED") {
                   jQuery("#"+dId).slideUp('slow', function() {
                       jQuery("#"+dId).remove();
                   })
               } else {
                   alert(ret);
               }
          }
        });
        return false;
    });
    
    // Function for saving the link data
    jQuery("#form_noteEditor").submit(function(e) {
        if(jQuery("#form_noteEditor").valid()){ 
            e.preventDefault();
            jQuery("#submitNote").attr("disabled", "disabled");
            data_string = jQuery("#form_noteEditor").serialize();
            jQuery.ajax({
                url: 'index.php?module=pagenotes&action=savenote',
                type: "POST",
                data: data_string,
                success: function(msg) {
                    jQuery("#submitNote").attr("disabled", "");
                    if(msg !== "ERROR_DATA_INSERT_FAIL") {
                        // Update the information area 
                        // (msg is the id of the record on success)
                        jQuery("#save_results_note").html('<span class="success">' + status_success + '</span>');
                        // Change the id field to be the id that is returned as msg & mode to edit
                        jQuery("#pagenotes_mode").val('edit');
                        if (curMode == 'add') {
                            jQuery.get('index.php?module=pagenotes&action=ajaxgetnotebyid&id='+msg, function(data){
                                jQuery("#pagenotes_all").prepend(data);
                            });
                        } else {
                            jQuery.get('index.php?module=pagenotes&action=ajaxgetnotebyid&id='+msg, function(data){
                                jQuery("#"+msg).replaceWith(data);
                            });
                        }
                        curMode='edit'
                        jQuery("#id").val(msg);
                        jQuery(".success").delay(3000).fadeOut();
                    } else {
                        jQuery("#save_results_note").html('<span class="error">' + status_fail + ": " + msg + '</span>');
                    }
                }
            });
        }
    });
});