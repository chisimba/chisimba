/*
 * Javascript to support the wall module in Chisimba
 *
 * Written by Derek Keats based on ideas, some functions and
 * studying the code of 
 *
 */
jQuery(function() {

    // Function to do the oembed magic
    jQuery(".wall_post_content a").oembed(null, {
        embedMethod: "append",
        maxWidth: 480
    });
    jQuery(".wall_comment_content a").oembed(null, {
        embedMethod: "append",
        maxWidth: 480
    });
    
    
    
});