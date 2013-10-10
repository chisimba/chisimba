var dsMentions = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
           url: baseUri+'?module=brandmonday&action=json_getmentions'
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
    var resultTplMentions = new Ext.XTemplate(
        '<tpl for=".">',
        '<div class="search-item">', 
        	'<a href="http://www.twitter.com/{screen_name}" target="_blank"><img height="48px" width="48px" src="{image}" /></a>',    	     	
            '<span>{lastPost}</span>',                       
            '<div style="margin-top:8px;padding-left:5px;"><a href="http://www.twitter.com/{screen_name}" target="_blank">{screen_name}</a>',           
            '<p>{excerpt}</p></div>',
        '</div></tpl>'
    );
 var dvMentions = new Ext.DataView({
            tpl: resultTplMentions,
            loadingText:'Fetching Mentions...',
            store: dsMentions,
            itemSelector: 'div.search-item'
        });
        
        
var MentionsSubPanel = new Ext.Panel({
	width: 850,
    height: 400,
    items: dvMentions,
    margins: '10 10 10 10',
    padding: '10 10 10 10',    
	title:'Who\'s talking about #BrandMonday',
	autoScroll: true,
	region:'center'	
});