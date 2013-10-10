/* 
 * Javascript to support oeruserdata for delete action
 * on user list
 *
 * Written by Derek Keats derek@dkeats.com
 * STarted on: February 1, 2012, 8:34 am
 *
 */
var id;
var next;
var prev;
var newnext;
var newprev;
var pages;

function showDelConfirm(id){
    jQuery.ajax({
        beforeSend: function (request) {
            if (!confirm("You really want to delete?")) {
                return false;
            }
        },
        type: "POST",
        url: "index.php?module=oeruserdata&action=delete&id=" + id,
        cache: false,
        success: function(ret){
            if(ret == "RECORD_DELETED") {
                jQuery("#ROW_"+id).slideUp('slow', function() {
                    jQuery("#ROW_"+id).remove();
                })
            } else {
                alert(ret);
            }
        }
    });
    return false;
};
/**
 *
 * Put your jQuery code inside this function.
 *
 */
jQuery(function() {    
    // Things to do on loading the page.
    jQuery(document).ready(function() {        
        });
    
    jQuery(".nav_next").delegate("img", "click", function() {
        id=jQuery(".nav_next").attr("id");
        next = id.replace("next_", "");
        //alert(next);
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=oeruserdata&action=userlistajax&page=" + next,
            cache: false,
            success: function(ret){
                jQuery("#userlisting").html(ret);
                jQuery("#current_page").html(next);
                newnext = parseInt(next)+1;
                //alert(newnext);
                jQuery(".nav_next").attr("id", 'next_'+newnext);
            }
        });
        return false;
    });
    
    jQuery(".nav_prev").delegate("img", "click", function() {
        id=jQuery(".nav_prev").attr("id");
        prev = id.replace("prev_", "");
        alert(prev);
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=oeruserdata&action=userlistajax&page=" + prev,
            cache: false,
            success: function(ret){
                jQuery("#userlisting").html(ret);
                jQuery("#previous_page").html(prev);
                newprev = parseInt(prev)-1;
                //alert(newnext);
                jQuery(".nav_prev").attr("id", 'prev_'+newprev);
            }
        });
        return false;
        
    });
});