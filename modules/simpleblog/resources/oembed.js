/* 
 * Javascript to support simpleblog
 *
 * Written by Derek Keats
 * Started on: January 17, 2011, 1:41 pm
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

    // Function to do the oembed magic
    jQuery(".simpleblog_post_content a").oembed(null, {
        embedMethod: "append",
        maxWidth: 480
    });
});