 
 var twitPoll = new Ext.direct.PollingProvider({
		    type:'polling',
		    url: baseUri,
		    baseParams:{
		    	'action':'twit_poll', 
		    	'module':'twitterizer',
		    	'lastTimeCheck':lastTimeCheck
		    },
		    interval:20000 //the interval for the polling the server ... should be made configurable
		});
		Ext.Direct.addProvider(twitPoll);
		
		//check for new conversations
		Ext.Direct.on('twit_updates', function(e){
	    	doTwitUpdates(e.data);   	
	    	//alert('twit polled');
		});
var ds = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
           url: baseUri+'?module=twitterizer&action=json_gettweets'
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
            {name: 'screen_name', mapping: 'screen_name'},
            {name: 'lastPost', mapping: 'tstamp', type: 'date', dateFormat: 'timestamp'},
            {name: 'excerpt', mapping: 'tweet'}
        ]),
        
        listeners:{ 
        	load:function(st, recs ){
        		//alert('add recs to the data store..');
        		//set the last time
        		//lastTimeCheck = dts.format("U");
        		st.sort('tstamp','DESC');
        	}
        },
		sortInfo: {
			    field: 'tstamp',
			    direction: 'DESC' // or 'DESC' (case sensitive for local sorting)
			},
		
        baseParams: {limit:20, forumId: 4}
    });
//'<span>{lastPost:date("M j, Y")}<br />@ {lastPost:date("g:i a")}</span>', 
    // Custom rendering Template for the View
    var resultTpl = new Ext.XTemplate(
        '<tpl for=".">',
        '<div class="search-item">', 
        	'<a href="http://www.twitter.com/{screen_name}" target="_blank"><img src="{image}"></a>',    	     	
            '<span>{lastPost:date("M j, Y")}<br />@ {lastPost:date("g:i:s a")}</span>',                       
            '<div style="margin-top:8px;padding-left:5px;"><a href="http://www.twitter.com/{screen_name}" target="_blank">{name}</a>',           
            '<p>{excerpt}</p></div>',
        '</div></tpl>'
    );

    var statsButton  = new Ext.Button({
            text:'Stats',
            tooltip:'View the current stats...',
            iconCls:'silk-user-comment',
			id:'statsbut',
            // Place a reference in the GridPanel
            //ref: '../../removeButton',
            //disabled: true,
            handler: function(){
            	//doAddUsers();
            	alert('still coming..');
            	

            }
        });
        
      var SIOCButton  = new Ext.Button({
            text:'SIOC export',
            tooltip:'SIOC export...',
            iconCls:'silk-rss',
			id:'SIOC',
            // Place a reference in the GridPanel
            //ref: '../../removeButton',
            //disabled: true,
            handler: function(){
            	//doAddUsers();
            	Ext.Msg.Alert('still coming ..');
            }
        });
        
   /* var stBar =   new Ext.ux.StatusBar({
			        defaultText: 'Default status',
		            id: 'basic-statusbar',
			        items: [{
			            text: 'A Button'
			        }, '-', 'Plain Text', ' ', ' ']
		        })*/

     var dv = new Ext.DataView({
            tpl: resultTpl,
            loadingText:'Fetch Tweets...',
            store: ds,
            itemSelector: 'div.search-item'
        });
        
    var middlePanel = new Ext.Panel({
       // applyTo: 'search-panel',
        title: terms,
        region: 'center',
        margins:'0 20px 0 20px',
		width: 800,
		padding: '5px',	
		autoScroll: true,
		loadMask: true,		

        items: dv,

        tbar: [
	       
            'Search: ', ' ',
            new Ext.ux.form.SearchField({
                store: ds,
                iconCls:'zoom',
                width:320
            }), '-',  statsButton, 
	        SIOCButton
        ],

        bbar: new Ext.PagingToolbar({
            store: ds,
            pageSize: 20,
            displayInfo: true,
            displayMsg: 'Topics {0} - {1} of {2}',
            emptyMsg: "No topics to display",
            listeners: {
            	change: function(PTb, data){
            		
            		if(data.activePage == 1){
            			twitPoll.connect();
            		}else{
            			twitPoll.disconnect();
            		}
            	}
            }
        })
        
        
    });

    
