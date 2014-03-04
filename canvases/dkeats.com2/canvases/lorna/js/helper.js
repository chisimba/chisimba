/*
 * Javascript to support dkeats.com2 canvas skin
 *
 * Written by Derek Keats
 *
 */

jQuery(document).ready(function() {
    // Uses http://plugins.jquery.com/project/ImageScale
    jQuery("#Canvas_Content_Body_Region2 img").imageScale({ maxWidth: 430 });
    return false;
});

jQuery(window).load(function() {
    // Make all the columns equal
    var h1 = jQuery("#Canvas_Content_Body_Region1").height();
    var h2 = jQuery("#Canvas_Content_Body_Region2").height();
    var h3 = jQuery("#Canvas_Content_Body_Region3").height();
    var maxHeight = Math.max(h1,h2,h3);
    jQuery("#Canvas_Content_Body_Region1").height(maxHeight);
    jQuery("#Canvas_Content_Body_Region2").height(maxHeight);
    jQuery("#Canvas_Content_Body_Region3").height(maxHeight);
    return false;
});

/**
 *
 * Various helper functions
 *
 */
jQuery(function() {

    return false;
});