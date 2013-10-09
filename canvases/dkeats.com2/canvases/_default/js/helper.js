/*
 * Javascript to support dkeats.com2 canvas skin
 *
 * Written by Derek Keats
 *
 */

/**
 * Check if the browser supports borderRadius
 */
function hasBorderRadius() {
    var d = document.createElement("div").style;
    if (typeof d.borderRadius !== "undefined") return true;
    if (typeof d.WebkitBorderRadius !== "undefined") return true;
    if (typeof d.MozBorderRadius !== "undefined") return true;
    return false;
};

jQuery(document).ready(function() {
    // Uses http://plugins.jquery.com/project/ImageScale
    jQuery("#Canvas_Content_Body_Region2 img").imageScale({ maxWidth: 460 });

    return false;
});

jQuery(window).load(function() {
    //jQuery("#exposure").removeAttr('style');
    //jQuery("#exposure").css('background-color', 'pink');
    var curModule = (getParameterByName( 'module' ));
    if (curModule != 'simplegal') {
        if (hasBorderRadius()) {
            // Adjust Flickr images because the default is ugly
            var counter=0;
            jQuery("img").each(function(){
                var img = jQuery(this);
                var imgSrc = img.attr("src");
                var imgHeight = img.height();
                var imgWidth = img.width();
                // If the image has flickr in the SRC url
                if (imgSrc.indexOf("flickr") != -1) {
                    counter=counter+1;
                    jQuery(this).addClass('flickr_img');
                    jQuery(this).wrap('<div id="flickr_img_'+counter+'" class="flickr_img_wrapper" />');
                    jQuery('#flickr_img_'+counter)
                      .css('width', imgWidth+"px")
                      .css('height', imgHeight+"px")
                      .css('background-image', 'url(' + imgSrc + ')');
                    img.remove();
                }
            })
        }
        // Make all the columns equal
        var h1 = jQuery("#Canvas_Content_Body_Region1").height();
        var h2 = jQuery("#Canvas_Content_Body_Region2").height();
        var h3 = jQuery("#Canvas_Content_Body_Region3").height();
        var maxHeight = Math.max(h1,h2,h3);
        jQuery("#Canvas_Content_Body_Region1").height(maxHeight);
        jQuery("#Canvas_Content_Body_Region2").height(maxHeight);
        jQuery("#Canvas_Content_Body_Region3").height(maxHeight);
    }
    return false;
});

/**
 *
 * Various helper functions
 *
 */
jQuery(function() {
    jQuery(document).on("click", "#toggle_left", function(){

    });
    return false;
});

/**
 *
 * Get querystring parameters
 *
 */
function getParameterByName( name )
{
  name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
  var regexS = "[\\?&]"+name+"=([^&#]*)";
  var regex = new RegExp( regexS );
  var results = regex.exec( window.location.href );
  if( results == null )
    return "";
  else
    return decodeURIComponent(results[1].replace(/\+/g, " "));
}
