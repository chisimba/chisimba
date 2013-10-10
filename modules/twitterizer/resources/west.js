

var westPanel = new Ext.Panel({
	region: 'west',
    id: 'Tweet Tools', // see Ext.getCmp() below
    //title: 'West',
    split: true,
    width: 200,
    minSize: 175,
    //border:false,
    maxSize: 400,
    collapsible: true,
     unstyled: true,

    margins: '0 0 0 5',
    /*layout: {
        type: 'accordion',
       
    },*/
     cmargins: '5 5 0 5',

    items: [ 
    	{	    	
	        title: 'Tracking Terms',
	        html: '<p class="warning">Terms (keywords, hashtags etc) currently being tracked:<br>#SITACLOUD<br>	#sitacloud</p>',
	        border: true,
	        iconCls: 'settings'
	    },
	    
	    {              
	        //contentEl: 'west',
	        title: 'SIOC export',
	        html:'<img src="http://tweetgator.peeps.co.za/skins/_common/icons/sioc.gif">',
	       // border: false,
	        iconCls: 'nav' // see the HEAD section for style used
	    }, {	    	
	        title: 'Stats',
	        html: '<p class="warning">Total posts so far: 44<br>Total contributors: 5.</p>',
	        border: true,
	        iconCls: 'settings'
	    },
	    
	    {	    	
	        title: 'Find something',
	       	//items: [searchForm]
	        border: true,
	        iconCls: 'settings'
	    }
	    
	 ]
    
});