/*
 * Javascript to support the dkeats.clean canvas
 *
 * Written by Derek Keats derek@dkeats.com
 * Started on: August 27, 2012.
 *
 *
 */

jQuery(function() {
    jQuery(document).ready(function() {

        // Fix up some of the layout that gets broken because of the 2 column design.
        if (jQuery("#threecolumn").length > 0) {
            
            var keepit = jQuery('#threecolumn').contents();
            jQuery('#threecolumn').replaceWith(keepit);
            keepit=null;
        }
        if (jQuery("#Canvas_Content_Body_Region3").length > 0) {
            if (jQuery("#Canvas_Content_Body_Region1").length > 0) {
                var keepit2 = jQuery('#Canvas_Content_Body_Region1').contents();
                jQuery('#Canvas_Content_Body_Region3').prepend(keepit2);
                jQuery('#Canvas_Content_Body_Region1').remove();
                keepit2=null;
            }
            // Move turn editing on to the top
            if (jQuery("#modeswitch_wrapper").length > 0) {
                jQuery('#modeswitch_wrapper').detach().prependTo('#Canvas_Content_Body_Region3');
            }
        }
    });
});