/* 
 * Javascript to support the textblock module: edit
 *
 * Written by Derek Keats derek@dkeats.com
 * STarted on: January 4, 2012, 3:53 pm
 *
 * The following parameters need to be set in the
 * PHP code for this to work:
 *
 *
 */

/**
 *
 * Put your jQuery code inside this function.
 *
 */
jQuery(function() {
   
    // Add jQuery Validation to form
    jQuery("#form_blockeditor").validate({
        //errorLabelContainer: jQuery("#RegisterErrors"),
        rules: {
            title: {
                required: true
            }
        },
        messages: {
            title: {
                required: titlereq
            }
        }
    });
    // Things to do on loading the page.
    jQuery(document).ready(function() {

    });
});