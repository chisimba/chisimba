

var westPanel = new Ext.Panel({
	region: 'east',
    id: 'Tweet Tools', // see Ext.getCmp() below
    //title: 'West',
    split: true,
    width: 200,
    minSize: 175,
    border:true,
    maxSize: 400,
    collapsible: true,
     unstyled: true,

    margins: '25 10 1 1',
    layout: {
        type: 'accordion',
       
    },
     cmargins: '5 5 0 5',

    items: [ 
    	
	    
	    {              
	        contentEl: 'about',
	        title: abouthead,
	       
	        iconCls: 'nav' // see the HEAD section for style used
	    }, 
            {	    	
	        title: poweredHead,
	        //html: '<p class="warning">Terms (keywords, hashtags etc) currently being tracked:<br>#SITACLOUD<br>	#sitacloud</p>',
	        border: true,
	        contentEl:'poweredby',
	        iconCls: 'settings'
	    }, 
            {              
	        contentEl: 'tweetthis',
	        title: tweetThisHead,
	       
	        iconCls: 'nav' // see the HEAD section for style used
	    }, {              
	        contentEl: 'feeds',
	        title: 'Feeds',
	        //html:'<img src="http://tweetgator.peeps.co.za/skins/_common/icons/sioc.gif">',
	       // border: false,
	        iconCls: 'nav' // see the HEAD section for style used
	    },  {	    	
	        title: fhead,
	        contentEl:'ad3',
	        border: true,
	        iconCls: 'settings'
	    },
	    {	    	
	        title: adhead1,
	        contentEl:'ad1',
	        border: true,
	        iconCls: 'settings'
	    },
	     {	    	
	        title: adhead2,
	        contentEl:'ad2',
	        border: true,
	        iconCls: 'settings'
	    },
	    {	    	
	        title: 'Disclaimer',
	       	contentEl:'disclaimer',
	        border: true,
	        iconCls: 'settings'
	    }
	    
	 ]
    
});