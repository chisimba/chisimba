/* 
 * Javascript to support simpleblog
 *
 * Written by Derek Keats
 * Started on: January 17, 2011, 1:41 pm
 *
 *
 */

// Helper function for debugging
function seeData(data) {
    alert(data);
}

/**
 *
 * Put your jQuery code inside this function.
 *
 */
jQuery(function() {

    var id;
    var elocation;
    var hideWall;
    var showWall;
    var wallpoint;
    
    jQuery(document).ready(function() {
        jQuery('a').each( function(){
            var doPreview = jQuery(this).hasClass('snipsite');
            if (doPreview) {
                var link = jQuery(this).attr('href');
                var id = jQuery(this).attr('id');
                var uri = 'index.php?module=strings&action=parseurl&url=' + link;
                jQuery.get(uri, function (data) {
                    jQuery('#'+id).after(data);
                });
            }
        });
    });
    
    // Add borders to the active Title input
    jQuery(document).on("click", "#input_post_title", function(){
        jQuery("#input_post_title").css("border","2px dotted red");
    });
    jQuery(document).on("blur", "#input_post_title", function(){
        jQuery("#input_post_title").css("border","none");
    });
    /*jQuery(document).on("click", ".simpleblog_editicon", function(){
        id = jQuery(this).attr("id");
        elocation = '#wrapper_'+id;
        jQuery(elocation).load('index.php?module=simpleblog&action=geteditorajax&mode=edit&postid='+id);
        //alert(elocation);
    });*/
    jQuery(document).on("click", ".simpleblog_delicon", function(){
        id = jQuery(this).attr("id");
        var target='index.php?module=simpleblog';
        //alert(id);
        jQuery.ajax({
            url: target,
            type: "POST",
            data: "&action=delpost&postid="+id,
            success: function(msg) {
                //alert(msg);
                jQuery('#wrapper_'+id).slideUp('slow', function() {
                    jQuery('#wrapper_'+id).remove();
                })
            }
        });
    });

    // Show the wall the first time
    jQuery(document).on("click", ".wall_link", function(){
        id = jQuery(this).attr("id");
        id = id.replace("wall_link_", "");
        wallpoint = '#simpleblog_wall_'+id;
        var tmpTxt = jQuery("#simpleblog_wall_nav_"+id).html();
        hideWall = '<div class="togglewall" id="togglewall_'+id+'"><a href="javascript:void(0);" class="togglewall_link">Toggle wall</a></div>';
        jQuery("#simpleblog_wall_nav_"+id).html(hideWall);
        jQuery(wallpoint).load('index.php?module=wall&action=getsimpleblogwall&walltype=4&identifier='+id);
        //jQuery.get('index.php?module=wall&action=getsimpleblogwall&walltype=4&identifier='+id, seeData);
    });

    // Toggle the wall
    jQuery(document).on("click", ".togglewall", function(){
        id = jQuery(this).attr("id");
        fixedid = id.replace("togglewall_", "");
        wallpoint = '#simpleblog_wall_'+fixedid;
        jQuery(wallpoint).toggle('slow');
    });


});