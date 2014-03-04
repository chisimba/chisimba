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

function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }

    alert(out);

    // or, if you wanted to avoid alerts...

    //var pre = document.createElement('pre');
    //pre.innerHTML = out;
    //document.body.appendChild(pre)
}


/**
 *
 * Put your jQuery code inside this function.
 *
 */
jQuery(function() {
  
    // Things to do on loading the page.
    jQuery(document).ready(function() {
        var src;
        var fullsrc;
        var imgLinked;
        var basUri = "http://api.flickr.com/services/rest/?method=flickr.photos.search";
        var format = "json";
        var nojsoncallback="1";
        var url=basUri+"&api_key="+apiKey+"&text="+searchTerm+"&format="+format+"&nojsoncallback="+nojsoncallback;
        jQuery.getJSON(url,function(data){
            // Now start cycling through our array of Flickr photo details
            jQuery.each(data.photos.photo, function(i,item) {
                src = "http://farm"+ item.farm +".static.flickr.com/"+ item.server +"/"+ item.id +"_"+ item.secret +"_m.jpg";
                fullsrc = "http://www.flickr.com/photos/"+item.owner+"/"+item.id;
                imgLinked = "<a title='View "+item.title+" on Flickr' href='"+fullsrc+"'>"+"<img src='"+src+"'></a>";
                //jQuery("<img/>").attr("src", src).appendTo("#speciesimages");
                jQuery(imgLinked).appendTo("#speciesimages");
                //id: 7268806836
                //owner: 62877922@N08
                //secret: 47edfc42b4
                //server: 8022
                //farm: 9
                //title: Lesser Swamp Warbler (Acrocephalus gracilirostris )
                //ispublic: 1
                //isfriend: 0
                //isfamily: 0
                //dump(item);
                if ( i == 3 ) return false;
            });
        });

    });
});