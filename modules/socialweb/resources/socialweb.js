/* 
 * Javascript to support socialweb
 *
 * Written by Derek Keats derekkeats@gmail.com
 * STarted on: September 16, 2012, 3:52 pm
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
      jQuery('body').prepend('<div id="fb-root"></div>');
    });

});