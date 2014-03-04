/* 
 * Javascript to support ckeditor2
 *
 * Written by Shushu Sifumba wsifumba@gmail.com
 * STarted on: August 30, 2013, 6:10 pm
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
      jQuery("#middledynamic_area").load('packages/ckeditor2/resources/sample.txt');
    });

});