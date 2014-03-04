var win;

function showWin(action, t){
	win = new Ext.Window({	                
	                layout:'fit',
	                id:action,
	                width:550,
	                height:480,
	                closeAction:'hide',
	                plain: true,
					title:t,
			        items:[{
    					width: '90%',    					
    					autoLoad:baseUri+'?module=brandmonday&action='+action,
    				}],
    				buttons: [
		                {
		                    text: 'Close',
		                    handler: function(){
		                        win.hide();
		                    }
		                }]
	})
	win.show(this);
};

var bestservalltimeBut = new Ext.Button({
        text:'All Time',       
        iconCls:'silk-rss',		    
        handler: function(){
        	showWin('bestservalltime', 'All Time');       	
        }
	}); 
var bestservthisweekButton = new Ext.Button({
        text:'This Week',       
        iconCls:'silk-rss',
		//id:'SIOC',        
        handler: function(){
        	showWin('bestservthisweek', 'This Week');
        }
});


var worstalltimeButton = new Ext.Button({
        text:'All Time',       
        iconCls:'silk-rss',		 
        handler: function(){
        	showWin('worstalltime', 'All Time');       	
        }
	}); 
var worstthisweekButton = new Ext.Button({
        text:'This Week',       
        iconCls:'silk-rss',
		//id:'SIOC',        
        handler: function(){
        	showWin('worstthisweek', 'This Week');
        }
});

var awardsTab = new Ext.TabPanel({
                	//plain:true,
                	//layout:'border',
                	activeTab:0,
                	defaults:{	//autoScroll: false, 
                				region:'center',
                				height: 400,
                				autoScroll: true
                	},
                	items:[
                                        {
                				title:'Best Service',
                				plain:false,
                				autoScroll:true,
                				tbar: [ bestservalltimeBut, bestservthisweekButton  ],
                				defaults:{		                				
	                				margins:'5 5 5 5',
	                				autoScroll: true
	                			},
                				layout:'border',                				  
                				items:[{
                					//height: 120,
                					region:'center',
                					//title:"Cloud",
                					autoLoad:baseUri+'?module=brandmonday&action=bestservcloud',
                				}]
                				           				
                			},
                			{
                				title:'Worst Service',
                				plain:false,
                				autoScroll:true,
                				tbar: [ worstalltimeButton, worstthisweekButton  ],
                				defaults:{		                				
	                				margins:'5 5 5 5',
	                				autoScroll: true
	                			},
                				layout:'border',                				  
                				items:[{
                					//height: 120,
                					region:'center',
                					//title:"Cloud",
                					autoLoad:baseUri+'?module=brandmonday&action=worstcloud',
                				}]
                				           				
                			},
                			{               				
                				title:'#BrandPLUS Activists',
                				autoLoad:baseUri+'?module=brandmonday&action=happypeeps',  
                				region:'center',
                				
                			},
                			
                			{
                				title:'#BrandMINUS Activists',
                				autoLoad:baseUri+'?module=brandmonday&action=sadpeeps',
                				
                			},
                			{
                				title:'Most Active BrandMonday Activists',
                				autoLoad:baseUri+'?module=brandmonday&action=activepeeps',
                				autoScroll:true,
                				
                			},
                			
                			
                			{
                				title:'Most Mentioned BrandMonday Activists',
                				autoLoad:baseUri+'?module=brandmonday&action=mentions',                				
                			}
                			]
                });
                
var Awards = new Ext.Panel({
	width: 850,
    height: 400,
    plain:true,
    items: awardsTab,
    //html:'adsfasdf0',
    margins: '10 10 10 10',
    padding: '10 10 10 10',    	
	//autoScroll: true,
	region:'center'	
});