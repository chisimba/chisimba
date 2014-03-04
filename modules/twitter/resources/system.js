jQuery(document).ready(function() {
    jQuery.Juitter.start({
        searchType:"searchWord", // needed, you can use "searchWord", "fromUser", "toUser"
            searchObject:"dkeats", // needed, you can insert a username here or a word to be searched for, if you wish multiple search, separate the words by comma.
            // The values below will overwrite the ones on the Juitter default configuration.
            // They are optional here.
            // I'm changing here as a example only
            lang:"en", // restricts the search by the given language
            live:"live-120", // the number after "live-" indicates the time in seconds to wait before request the Twitter API for updates.
            placeHolder:"juitterContainer", // Set a place holder DIV which will receive the list of tweets example <div id="juitterContainer"></div>
            loadMSG: "Loading messages...", // Loading message, if you want to show an image, fill it with "image/gif" and go to the next variable to set which image you want to use on
            imgName: "loader.gif", // Loading image, to enable it, go to the loadMSG var above and change it to "image/gif"
            total: 20, // number of tweets to be show - max 100
            readMore: "Read it on Twitter", // read more message to be show after the tweet content
            nameUser:"image", // insert "image" to show avatar of "text" to show the name of the user that sent the tweet
            openExternalLinks:"newWindow", // here you can choose how to open link to external websites, "newWindow" or "sameWindow"
            filter:"sex->*BAD word*,porn->*BAD word*,fuck->*BAD word*,shit->*BAD word*"  // insert the words you want to hide from the tweets followed by what you want to show instead example: "sex->censured" or "porn->BLOCKED WORD" you can define as many as you want, if you don't want to replace the word, simply remove it, just add the words you want separated like this "porn,sex,fuck"... Be aware that the tweets will still be showed, only the bad words will be removed
    });
    jQuery("#aJuitter").click(function(){
        jQuery(".jLinks").removeClass("on");
        jQuery(this).addClass("on");
        jQuery.Juitter.start({
                searchType:"searchWord",
                searchObject:"Juitter",
                live:"live-180" // it will be updated every 180 seconds/3 minutes
        });
    });
    jQuery("#juitterSearch").submit(function(){
        jQuery.Juitter.start({
            searchType:"searchWord",
            searchObject:jQuery(".juitterSearch").val(),
            live:"live-180", // it will be updated every 180 seconds/3 minutes
            filter:"sex->*BAD word*,porn->*BAD word*,fuck->*BAD word*,shit->*BAD word*"
        });
        return false;
    });
    jQuery(".juitterSearch").blur(function(){
        if(jQuery(this).val()=="") jQuery(this).val("Type a word and press enter");
    });
    jQuery(".juitterSearch").click(function(){
        if(jQuery(this).val()=="Type a word and press enter") jQuery(this).val("");
    });
});