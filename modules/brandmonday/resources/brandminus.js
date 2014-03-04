var dsBrandMinus = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
           url: baseUri+'?module=brandmonday&action=json_getbrandminus'
        }),
        reader: new Ext.data.JsonReader({
            root: 'tweets',
            totalProperty: 'totalCount',
            
            id: 'puid'
        }, [
            {name: 'postId', mapping: 'puid'},           
            {name: 'image', mapping: 'image'},
            {name: 'name', mapping: 'name'},
            {name: 'tstamp', mapping: 'tstamp'},
            {name: 'screen_name', mapping: 'from_user'},
            {name: 'lastPost', mapping: 'createdat'},
            {name: 'excerpt', mapping: 'tweet'}
        ]),
        
    });
//'<span>{lastPost:date("M j, Y")}<br />@ {lastPost:date("g:i a")}</span>', 
    // Custom rendering Template for the View
    var resultTplMinus = new Ext.XTemplate(
        '<tpl for=".">',
        '<div class="search-item">', 
        	'<a href="http://www.twitter.com/{screen_name}" target="_blank"><img height="48px" width="48px" src="{image}" /></a>',    	     	
            '<span>{lastPost}</span>',                       
            '<div style="margin-top:8px;padding-left:5px;"><a href="http://www.twitter.com/{screen_name}" target="_blank">{screen_name}</a>',           
            '<p>{excerpt}</p></div>',
        '</div></tpl>'
    );
 var dvBrandMinus = new Ext.DataView({
            tpl: resultTplMinus,
            loadingText:'Fetching #BrandMinus Tweets...',
            store: dsBrandMinus,
            itemSelector: 'div.search-item'
        });
        
        
var BrandMinus = new Ext.Panel({
	width: '50%',
    height: 400,
    items: dvBrandMinus,
    margins: '10 10 10 10',
    padding: '10 10 10 10',    
	title:'#BrandMinus',
	autoScroll: true,
	region:'center'	
});