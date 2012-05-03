/*
 * Javascript to support the kenga-elearn2 skin
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
        jQuery('#Canvas_Content_Body_Region3').height(maxHeight);
        jQuery("iframe").each(function(){
            var ifr_source = jQuery(this).attr('src');
            var wmode = "wmode=transparent";
            if(ifr_source.indexOf('?') != -1) {
                var getQString = ifr_source.split('?');
                var oldString = getQString[1];
                var newString = getQString[0];
                jQuery(this).attr('src',newString+'?'+wmode+'&'+oldString);
            }
            else jQuery(this).attr('src',ifr_source+'?'+wmode);
        });
    });
});