
var pageSize = 25;
var userOffset = 0;
var selectedTab = "S";
var selectedGroupId;
var myMask = new Ext.LoadMask(Ext.getBody(), {msg:"Please wait..."});


///////////////////////////
///// Tab Panels //////////
////////////////////////////




//alphabet tabs
var alphaTab = new Ext.TabPanel({
	//plain:true,
	region: 'north',
	id:'mainTabPanel',
	width: 800,
    height: 20,
    margins: '10 10 10 10',
    split:true,
    loadMask: true,
    activeTab: selectedTab,
    enableTabScroll:true,
    tabPosition:'top',
   // renderTo:'alphabet',
	listeners: {
                'render': function(tabPanel){                    
                    //loadGroups(tabPanel)
                }, 
                'tabchange': function(tabPanel , tab){
                	//load the data for the selected tab
                	selectedTab = tab.id;
                	loadGroups(tabPanel, tab);                	
                }
                
            },
	items:[ {title:'0',id:'0'},{title:'1',id:'1'},{title:'2',id:'2'},
			{title:'3',id:'3'},{title:'4',id:'4'},{title:'5',id:'5'},{title:'6',id:'6'},{title:'7',id:'7'},{title:'8',id:'8'},
			{title:'9',id:'9'},
			{title:'A',id:'A'},{title:'B',id:'B'},{title:'C',id:'C'},{title:'D',id:'D'},{title:'E',id:'E'},{title:'F',id:'F'},
			{title:'G',id:'G'},{title:'H',id:'H'},{title:'I',id:'I'},{title:'J',id:'J'},{title:'K',id:'K'},{title:'L',id:'L'},
			{title:'M',id:'M'},{title:'N',id:'N'},{title:'O',id:'O'},{title:'P',id:'P'},{title:'Q',id:'Q'},
			{title:'R',id:'R'},{title:'S',id:'S'},{title:'T',id:'T'},{title:'U',id:'U'},{title:'V',id:'V'},{title:'W',id:'W'},
			{title:'X',id:'X'},	{title:'Y',id:'Y'},{title:'Z',id:'Z'}
	
	]
	
	
});
////////////////////////
/// Data Stores ////////
////////////////////////
/*var GroupStore = new Ext.data.JsonStore({
    // store configs
    autoDestroy: true,
    url: baseUri+'?module=groupadmin&action=json-getgroups',
    storeId: 'GroupStore',
    // reader configs
    root: 'images',
    idProperty: 'name',  
    fields: ['name', 'url', {name:'size', type: 'float'}, {name:'lastmod', type:'date'}]
});*/
var proxyGroupStore = new Ext.data.HttpProxy({
            url: baseUri+'?module=groupadmin&action=json_getallgroups&filter=0&limit=25&offset=0'
        });

        
 var alphaGroupStore = new Ext.data.JsonStore({
        root: 'groups',
        totalProperty: 'totalCount',
        idProperty: 'id',
        remoteSort: true,
		baseParams: [{'letter':selectedTab}],
        fields: [
        	'id',
            'groupname', 
            'grouptitle'           
        ],
        
		listeners:{ 
    		'loadexception': function(theO, theN, response){
    			alert(response.responseText);
    		},
    		'beforeload': function(thisstore, options){
    			thisstore.setBaseParam('letter', selectedTab);
    		},
    		'load': function(){
    				//alert('alphagroup store load');	
    			}
    	},
        // load using script tags for cross domain, if the data in on the same domain as
        // this page, an HttpProxy would be better
        proxy:proxyGroupStore 
    });
    
var proxyStore = new Ext.data.HttpProxy({
            url: baseUri+'?module=groupadmin&action=json_getgroupusers&groupid='
        });
        



  // create the Data Store
 var abstractStore = new Ext.data.JsonStore({
        root: 'users',
        totalProperty: 'totalCount',
        idProperty: 'id',
        remoteSort: true,
		
        fields: [
            'username', 
            'firstname', 
            'surname', 
            'userid',           
            'lastloggedin',
            'emailaddress'
            //{name: 'lastloggedin', mapping: 'lastloggedin', type: 'date', dateFormat: 'timestamp'}
        ],
        listeners:{ 
    		'loadexception': function(theO, theN, response){
    			alert(response.responseText);
    		},
    		'load': function(thestore, records){    				
    				//alert('user group loaded');
    				
    			}
		},

        // load using script tags for cross domain, if the data in on the same domain as
        // this page, an HttpProxy would be better
        proxy:proxyStore 
    });
    //store.setDefaultSort('lastpost', 'desc');
    
    var proxySubGroupStore = new Ext.data.HttpProxy({
            url: baseUri+'?module=groupadmin&action=json_getsubgroups'
        });
        
    var subGroupStore = new Ext.data.JsonStore({
		//autoLoad: true,
		//url: baseUri+'?module=groupadmin&action=json_getsubgroups&groupid=1433',
		proxy:proxySubGroupStore, 
		idProperty: 'groupid',
		//baseParams:[{'groupid': selectedTab}],
		root: 'subgroups',
		fields: ['groupid', 'name'],
		listeners:{ 
    		'loadexception': function(theO, theN, response){
    			alert('subGroupStore error\n'+response.responseText);
    		},
    		'load': function(thestore, records){    				
    				//alert('subgroupstore loaded');
    				loadSubgroupMenu(records);			
    			}
		}
	});
   
 //////////////////////////////////////////////////////////
   
///////////////////////
/// Toolbars //////////
////////////////////////



//the page navigation for the users in a group
var pageNavigation = new Ext.PagingToolbar({
            pageSize: pageSize,
            store: abstractStore,
            displayInfo: true,
            
            displayMsg: 'Displaying Users {0} - {1} of {2}',
            emptyMsg: "No Users to display",
            listeners:{ 	    		
	    		beforechange: function(ptb, params){	
	    			userOffset = params.start; 			
	    			proxyStore.setUrl(baseUri+'?module=groupadmin&action=json_getgroupusers&groupid='+selectedGroupId+'&limit='+params.start+'&offset='+params.start);
	    		}  
            }
            
        });

//the page navigation for the top level groups        
var groupsPageNavigation = new Ext.PagingToolbar({
            pageSize: 25,
            store: alphaGroupStore,
            displayInfo: true,
            
            displayMsg: 'Displaying Groups {0} - {1} of {2}',
            emptyMsg: "No Groups to display",
            listeners:{ 
            	beforechange: function(ptb, params){	    			
	    			proxyGroupStore.setUrl(baseUri+'?module=groupadmin&action=json_getallgroups&filter='+selectedTab+'&limit='+params.start+'&offset='+params.start);	    			
	    		}            
            }
            
        });

// the list of sugroups in the form of a dropdown
var subGroupsCombo = new Ext.form.ComboBox({
	//store: subGroupStore,
	displayField:'name',
	valueField: 'groupid',
	typeAhead: true,
	//mode: 'local',
	triggerAction: 'all',
	forceSelection:true,

	emptyText:'Select a Sub Group...',
	selectOnFocus:true,
	listeners:{
		select: function(item, record) {
                //alert(record.data.groupid);
                alert('subgroup select listener');
                loadSubgroup(record.data.groupid)
            }
	}
	//applyTo: 'local-states'
});

//the dropdown for the subgroups
var scrollMenu = new Ext.menu.Menu();

var rmButton = new Ext.Button({
            text:'Remove User',
            tooltip:'Remove the selected User',
            iconCls:'remove',
			id:'rmgroup',
            // Place a reference in the GridPanel
            ref: '../../removeButton',
            disabled: true,
            handler: function(){
            	doRemoveUsers();
            }
        });
        
var win;

// The toolbar for the user grid
var toolBar = new Ext.Toolbar({
	items:[{
            text:'Add User',
            tooltip:'Add a User to this group',
            iconCls:'add',
            handler: function (){
	        	if(!win){
		            win = new Ext.Window({
		                
		                layout:'fit',
		                width:615,
		                height:350,
		                closeAction:'hide',
		                plain: true,						
		                items: [usersGridPanel],		
		                
		            });
		        }
		        win.show(this);
		        userStore.load({params:{start:0, limit:pageSize}})
            }
        }, '-',rmButton, 
        '-', 
        {
        	icon: 'preview.png',        
        	cls: 'x-btn-text-icon',
        	text: 'Sub Groups',
        	menu: scrollMenu
        }]
});


  var sm2 = new Ext.grid.CheckboxSelectionModel({
        listeners: {
            // On selection change, set enabled state of the removeButton
            // which was placed into the GridPanel using the ref config
            selectionchange: function(sm) {
                if (sm.getCount()) {
                    rmButton.enable();
                } else {
                    rmButton.disable();
                }
            }
        }
    });


////////////////////////////////
//// Grids ////////////////////
////////////////////////////////

//the top level groups grid
var groupsGrid = new Ext.grid.GridPanel({
		region: 'west',
		//closable:true,
		split:true,
		
		margins: '10 10 10 10',
	 	collapsible: true,   // make collapsible
    	cmargins: '10 10 10 10', // adjust top margin when collapsed
    	id: 'west-region-container',
    	//layout: 'fit',
		
		width:450,
        height:300,
       // frame:true,
        store: alphaGroupStore,
        title:'Groups starting with ',
        iconCls:'icon-grid',
        loadMask: true,
		stripeRows: true,
		sm: new Ext.grid.RowSelectionModel({singleSelect:true}),
        // grid columns            
    	bbar: groupsPageNavigation,
        
        columns:[{
	            id: 'groupname', // id assigned so we can apply custom css (e.g. .x-grid-col-topic b { color:#333 })
	            header: "Group Name",
	            dataIndex: 'groupname',
	            width: 100,
	            //renderer: renderTopic,
	            sortable: true
	        },
	        {
	            id: 'grouptitle', // id assigned so we can apply custom css (e.g. .x-grid-col-topic b { color:#333 })
	            header: "Title",
	            dataIndex: 'grouptitle',
	            width: 320,
	            //renderer: renderTopic,
	            sortable: true
	        }],
	    viewConfig: {
            //forceFit:true,
             emptyText: 'No Groups found'

        	}
	
});

var SiteAdminGrid = new Ext.grid.GridPanel({
	title:'Site Administrators',
	region: 'center',
	split:true,
	frame:true,
    layout: 'fit',
    margins: '10 10 10 10',	 
	tbar: toolBar,        
    bbar:pageNavigation,    
    width:600,
    height:300,   
    store: abstractStore,    
    iconCls:'icon-grid',
    loadMask: true,
	sm: sm2,
	
    // grid columns
    cm: new Ext.grid.ColumnModel([
            sm2,{

   // columns:[{
            header: "Last Name",
            dataIndex: 'surname',
            width: 150,            
            sortable: true
        },{
            id: 'firstname', // id assigned so we can apply custom css (e.g. .x-grid-col-topic b { color:#333 })
            header: "First Name",
            dataIndex: 'firstname',
            width: 150,            
            sortable: true
        },{
            header: "Username",
            dataIndex: 'username',
            width: 100,           
            sortable: true
        },{
            header: "Email",
            dataIndex: 'emailaddress',
            width: 150           
        },{
            id: 'last',
            header: "Last Logged In",
            dataIndex: 'lastloggedin',
            width: 100,            
             align: 'right',
            sortable: true
   		 }]),
    
     viewConfig: {
        forceFit:true,
        emptyText: 'No Record found - Try the sub groups'

    	}
});

var myBorderPanel = new Ext.Panel({
    //renderTo: document.body,
    width: 1024,
    height: 400,
    margins: '10 10 10 10',
    padding: '10 10 10 10',
    title: 'Group Administration',
    layout: 'border',
    items: [alphaTab, groupsGrid, SiteAdminGrid]
});

//the main function 
Ext.onReady(function(){
	Ext.QuickTips.init();
	


	
	//render the alphabet list
	//alphaTab.render('alphabet');	 
	myBorderPanel.render('mainPanel');
	SiteAdminGrid.setVisible(false);
	//navigationTabs.render('main-interface');
	alphaGroupStore.on('beforeload', function(opt){
		//alert(opt.params);
		//alphaGroupStore.setBaseParam('params', {start});
		//alphaGroupStore.load({params:{limit:25, offset:0, letter: selectedTab}});
	});
	
	groupsGrid.getSelectionModel().on('rowselect', function(sm, ri, record)
	{ 
        
        //submissionsPanel.expand(true);
        //submissionsGrid.setVisible(true);
       //alert('Groups selected'+record.data.id);
       SiteAdminGrid.setVisible(true);
       selectedGroupId = record.data.id;
       loadGroup(record.data.id, record.data.groupname, record.data.grouptitle);
        //submissionsStore.load({params: {title:record.data.title}});
        //submissionsGrid.setTitle('Submissions for - '+record.data.title);
        //Ext.example.msg('Success!', record.data.title);
    });
       

});


////////////////////////////////
//// HELPER METHODS ////////////
///////////////////////////////


//this function will be called when the
//page is loaded and when the an tab on the
//alphabet list is clicked
function loadGroups(tabPanel, tab){
	//load the groups
	groupsGrid.setTitle('Groups starting with \''+tab.id+'\'');
	//groupsGrid.render('main-interface');
	
	alphaGroupStore.load({params:{limit:25, offset:0, letter: tab.id}});	
}

//this function will be called when 
//the group is selected in the groups grid
function loadGroup(nodeId, groupname, grouptitle){
	
	//load the subgroups
	subGroupStore.load({params:{start:0, limit:25, groupid: nodeId}});	
	SiteAdminGrid.setTitle(groupname+" - " +grouptitle);
	SiteAdminGrid.render('groupusers');
	//alert(nodeId);
	proxyStore.setUrl(baseUri+'?module=groupadmin&action=json_getgroupusers&groupid='+nodeId);
	//load the data for this group
	abstractStore.load({params:{start:0, limit:25}}); 	
}

function loadSubgroup1(groupId)
{
	proxyStore.setUrl(baseUri+'?module=groupadmin&action=json_getgroupusers&groupid='+groupId);
	abstractStore.load({params:{start:0, limit:25}});
}

function loadSubgroupMenu(records){
	scrollMenu.removeAll();
	//var scrollMenu = new Ext.menu.Menu();
    for (var i = 0; i < records.length; ++i){
        scrollMenu.add({
            text: records[i].data.name,
            itemId: records[i].data.groupid,
            handler: onSubGroupClick
        });
    }	
}

function onSubGroupClick(item){	
	proxyStore.setUrl(baseUri+'?module=groupadmin&action=json_getgroupusers&groupid='+item.getItemId());
	abstractStore.load({params:{start:0, limit:25}}); 
	selectedGroupId = item.getItemId();
	//SiteAdminGrid.setTitle(SiteAdminGrid.getTitle()+' - '+item.getText());
}

//method tht removes users from a group
function doRemoveUsers()
{	
	myMask.show();
	//get the selected users
	var selArr = SiteAdminGrid.getSelectionModel().getSelections();
	
	//get the selected id's
	var idString = "";
	
	Ext.each( selArr, function( r ) 
	{
		idString = r.id +','+ idString ;		
	});   	
				//alert(userOffset);
		//post to server
	Ext.Ajax.request({
	    url: baseUri,
	    method: 'POST',
	    params: {
	       	module: 'groupadmin',
	   		action: 'json_removeusers',
	   		groupid: selectedGroupId,
	   		ids: idString
	    },
	    success: function(xhr,params) {
	        //alert('Success!\n'+xhr.responseText);
	        abstractStore.load({
	        	params:{
	        			start:userOffset, 
	        			limit:pageSize,
	        			groupid:selectedGroupId,
	        			module:'groupadmin',
	        			action:'json_getgroupusers'
	        	}
	        }); 
	        myMask.hide();
	    },
	    failure: function(xhr,params) {
	        alert('Failure!\n'+xhr.responseText);
	        myMask.hide();
	    }
	});
	
}


 function doAddUsers(){
 	myMask.show();
	//get the selected users
	var selArr = usersGridPanel.getSelectionModel().getSelections();
	
	//get the selected id's
	var idString = "";
	
	Ext.each( selArr, function( r ) 
	{
		idString = r.id +','+ idString ;		
	});   	
			
		//post to server
	Ext.Ajax.request({
	    url: baseUri,
	    method: 'POST',
	    params: {
	       	module: 'groupadmin',
	   		action: 'json_addusers',
	   		groupid: selectedGroupId,
	   		ids: idString
	    },
	    success: function(xhr,params) {
	        //alert('Success!\n'+xhr.responseText);
	        abstractStore.load({
	        	params:{
	        			start:userOffset, 
	        			limit:pageSize,
	        			groupid:selectedGroupId,
	        			module:'groupadmin',
	        			action:'json_getgroupusers'
	        	}
	        }); 
	        win.hide();
	        myMask.hide();
	    },
	    failure: function(xhr,params) {
	        alert('Failure!\n'+xhr.responseText);
	        myMask.hide();
	    }
	});
	
 }