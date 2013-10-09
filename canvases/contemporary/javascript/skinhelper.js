/*
 * Javascript to support the contemporary skin
 *
 * Written by Derek Keats derek@dkeats.com
 * Started on: April 28, 2012, 12:19 PM
 *
 *
 */
jQuery(function() {
    // Things to do on loading the page.
    jQuery(document).ready(function() {
        // Set equal height for the columns
        var maxHeight = Math.max(jQuery('#Canvas_Content_Body_Region1').height(),jQuery('#Canvas_Content_Body_Region2').height(),jQuery('#Canvas_Content_Body_Region3').height());
        jQuery('#Canvas_Content_Body_Region1').height(maxHeight);
        //jQuery('#Canvas_Content_Body_Region2').height(maxHeight);
        jQuery('#Canvas_Content_Body_Region3').height(maxHeight);
        /* We need to detect when there is a resize and run it again
        jQuery("#Canvas_Content_Body_Region2").bind("change", function() {
            var newHeight = jQuery('#Canvas_Content_Body_Region2').height();
            //alert(newHeight);
            jQuery('#Canvas_Content_Body_Region1').height(newHeight);
            jQuery('#Canvas_Content_Body_Region3').height(newHeight);
            //alert(jQuery("#Canvas_Content_Body_Region2").height() );
        });*/

    });
});