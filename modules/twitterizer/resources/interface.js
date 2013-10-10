 //1259225301;

Ext.onReady(function(){
	
	/**** Polling the server for new conversations ****/
      
	//twitPoll.disconnect();

	
	var viewport = new Ext.Viewport({
            layout: 'border',
            items: [
            	// create instance immediately
	            new Ext.BoxComponent({
	                region: 'north',
	                height: 32, // give north and south regions a height
	                autoEl: {
	                    tag: 'div'
	                    //html:'<p>north - generally for menus, toolbars and/or advertisements</p>'
	                }
	            }),
	            new Ext.BoxComponent({
	                region: 'south',
	                height: 32, // give north and south regions a height
	                autoEl: {
	                    tag: 'div'
	                    //html:'<p>north - generally for menus, toolbars and/or advertisements</p>'
	                }
	            }),
	             /*{
	                // lazily created panel (xtype:'panel' is default)
	                region: 'south',
	                contentEl: 'south',
	                split: true,
	                height: 100,
	                minSize: 100,
	                maxSize: 200,
	                collapsible: true,
	                title: 'South',
	                margins: '0 0 0 0'
	            },*/
	            
	           // westPanel,    
	            middlePanel
             ] 
        });       
		ds.load({params:{start:0, limit:20, forumId: 4}});
		
		//alert(dts.format("U"));
    });

    
   