/* 
 * Javascript to support imagegallery
 *
 * Written by Kevin Cyster kcyster@gmail.com
 * STarted on: June 19, 2012, 8:18 pm
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
    var path = jQuery(location).attr('pathname');
    
    // Things to do on loading the page.
    jQuery(document).ready(function() {
        setInterval(function(){
            var obj = jQuery.parseJSON(random_images);            
            var image_number = Math.floor((Math.random() * obj.length) + 1);               

            jQuery("div.random_image").attr('id', "#" + obj[image_number].image_id);
            var href = path + "?module=imagegallery&action=view&image_id=" + obj[image_number].image_id + "&tabs=1|&shared=true";
            jQuery("a.random_image").attr('href', href);
            jQuery("a.random_image").attr('title', obj[image_number].caption);
            jQuery("img.random_image").attr('src', obj[image_number].source);
            jQuery("p.random_image").html(obj[image_number].owner);
            jQuery(function() {
                jQuery("a.random_image").tooltip({
                    delay: 0,
                    track: true,
                    showUrl: false,
                    showBody: " - ",
                    pngFix: true
                });
            });
        }, 5000);    
    });
});