/* 
 * Javascript to support species
 *
 * Written by Derek Keats derek@localhost.local
 * STarted on: August 17, 2012, 2:22 pm
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
        
        var url="http://en.wikipedia.org/w/api.php?action=parse&format=json&page=" + fullName + "&redirects&prop=text&callback=?";
        jQuery.getJSON(url,function(data){
            wikiHTML = data.parse.text["*"];
            wikiDOM = jQuery("<document>"+wikiHTML+"</document>");
            var res = wikiDOM.find('.infobox').html();
            var res2 =  res.replace(/\/wiki\//g,"http://en.wikipedia.org/wiki/"); 
            jQuery("#wikioverviewcontents").append(res2);
        });
    });
});