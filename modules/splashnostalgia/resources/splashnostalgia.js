/* 
 * Javascript to support splashnostalgia
 *
 * Written by Derek Keats derek@localhost.local
 * STarted on: March 10, 2012, 10:55 pm
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
 * Put your jQuery code inside this function.
 *
 */
jQuery(function() {

    
    // Things to do on loading the page.
    jQuery(document).ready(function() {
      // Load some demo content into the middle dynamic area.
      jQuery("#navigation").css("background", "transparent");
      jQuery(".ChisimbaCanvas").css("height", "540px");
      jQuery(".featurebox").css("background", "transparent");
      jQuery(".featurebox").css("width", "200px");
      jQuery(".featurebox").css("border", "none");
      jQuery(".featurebox").css("margin", "auto");
      jQuery(".featurebox").css("margin-top", "14px");
      jQuery(".featureboxheader").css("background", "transparent");
      jQuery(".featureboxheader").css("visibility", "hidden");
      jQuery("#input_username").css("width", "130px");
      jQuery("#input_password").css("width", "130px");
    });
});