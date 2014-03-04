/* 
 * Javascript to support stripe
 *
 * Written by Derek Keats derek@dkeats.com
 * STarted on: April 6, 2012, 10:44 am
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
        jQuery('#stripe_users').bind('click', function(e) {
            jQuery("#stripe_userson").load("index.php?module=blockalicious&action=userslastfive");
            jQuery('#stripe_userson').slideToggle();
        });
        var refreshId = setInterval(function() {
            jQuery("#stripe_users p").load("index.php?module=blockalicious&action=usercount");
        }, 10000);
        jQuery.ajaxSetup({ cache: false });
    });

});