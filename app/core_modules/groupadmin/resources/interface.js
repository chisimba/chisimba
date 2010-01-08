
var pageSize = 25;
var userOffset = 0;
var selectedTab = "A";
var selectedGroupId;
var win;
var addwin;
var editwin;
var edituri;
var myMask = new Ext.LoadMask(Ext.getBody(), {msg:"Please wait..."});

//the main function 
Ext.onReady(function(){
	Ext.QuickTips.init();
	
	// turn on validation errors beside the field globally
	Ext.form.Field.prototype.msgTarget = 'side';
	
	alphaGroupStore.load({params:{start:0, limit:25}});
	myBorderPanel.render('mainPanel');
	
	SiteAdminGrid.setVisible(false);

	groupsGrid.getSelectionModel().on('rowselect', function(sm, ri, record)
	{ 
		
       SiteAdminGrid.setVisible(true);
       selectedGroupId = record.data.id;
       loadGroup(record.data.id, record.data.group_define_name, record.data.title);
      
    });       

});

var proxyGroupStore = new Ext.data.HttpProxy({
            url: baseUri+'?module=groupadmin&action=json_getgroupsbysearch&limit=25&start=0'
        });

        
 var alphaGroupStore = new Ext.data.JsonStore({
        root: 'groups',
        totalProperty: 'totalCount',
        idProperty: 'id',
        remoteSort: true,
		baseParams: [{'letter':selectedTab}],
        fields: [
        	'id',
            'group_define_name', 
            'title'           
        ],
        
		listeners:{ 
    		'loadexception': function(theO, theN, response){
    			//alert(response.responseText);
    		},
    		'beforeload': function(thisstore, options){
    			//thisstore.setBaseParam('letter', selectedTab);
    		},
    		'load': function(){
    				//alert('alphagroup store load');
					//loadGroups(tabPanel, tab);	
    			}
    	},
        // load using script tags for cross domain, if the data in on the same domain as
        // this page, an HttpProxy would be better
        proxy:proxyGroupStore 
    });

alphaGroupStore.setDefaultSort('group_define_name', 'asc');
    
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
        ],
        listeners:{ 
    		'loadexception': function(theO, theN, response){
    			//alert(response.responseText);
    		},
    		'load': function(thestore, records){    				
    				//alert('user group loaded');
    		}
		},

        // load using script tags for cross domain, if the data in on the same domain as
        // this page, an HttpProxy would be better
        proxy:proxyStore 
    });
        
    var proxySubGroupStore = new Ext.data.HttpProxy({
            url: baseUri+'?module=groupadmin&action=json_getsubgroups'
        });
        
    var subGroupStore = new Ext.data.JsonStore({
		proxy:proxySubGroupStore, 
		idProperty: 'groupid',
		root: 'subgroups',
		fields: ['groupid', 'name'],
		listeners:{ 
    		'loadexception': function(theO, theN, response){
    			//alert('subGroupStore error\n'+response.responseText);
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
            pageSize: pageSize,
            store: alphaGroupStore,
            displayInfo: true,
            displayMsg: 'Groups {0} - {1} of {2}',
            emptyMsg: "No Groups to display"
      });

// the list of sugroups in the form of a dropdown
var subGroupsCombo = new Ext.form.ComboBox({
	displayField:'name',
	valueField: 'groupid',
	typeAhead: true,
	triggerAction: 'all',
	forceSelection:true,

	emptyText:'Select a Sub Group...',
	selectOnFocus:true,
	listeners:{
		select: function(item, record) {
                loadSubgroup(record.data.groupid)
            }
	}
});

//the dropdown for the subgroups
var scrollMenu = new Ext.menu.Menu();

var rmButton = new Ext.Button({
            text:'Remove User',
            tooltip:'Remove the selected User',
            iconCls:'silk-delete',
			id:'rmgroup',
            // Place a reference in the GridPanel
            ref: '../../removeButton',
            disabled: true,
            handler: function(){
            	doRemoveUsers();
            }
        });
        

var addGroupButton = new Ext.Button({
	text:'Add Group',
    tooltip:'Add a new Group',
    iconCls: 'silk-add',
    handler: function (){
	        	if(!addwin){
		            addwin = new Ext.Window({
		                
		                layout:'fit',
		                width:400,
		                height:150,
		                closeAction:'hide',
		                plain: true,						
		                items: [addNewGroupPanel]		
		                
		            });
		        }
		        addwin.show(this);
		        
            }
})

// The toolbar for the user grid
var toolBar = new Ext.Toolbar({
	items:[{
            text:'Add User',
            tooltip:'Add a User to this group',
            iconCls: 'silk-add',
            handler: function (){
	        	if(!win){
		            win = new Ext.Window({
		                
		                layout:'fit',
		                width:615,
		                height:350,
		                closeAction:'hide',
		                plain: true,						
		                items: [usersGridPanel]		
		                
		            });
		        }
		        win.show(this);
		        
            }
        }, '-',rmButton, 
        '-', 
        {
        	iconCls: 'blist',
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
function renderEdit(value, p, record)
	{
		return String.format('<b><a href="javascript:showEditForm(\'{0}\',\'{1}\')"><img src="skins/_common/css/extjs/silk/fam/pencil.png" border="0" alt="Edit Group" title="Edit Group" /></a></b>',record.data.id, record.data.group_define_name);
    }

//the top level groups grid
var groupsGrid = new Ext.grid.GridPanel({
		region: 'west',
		split:true,
		margins: '10 10 10 10',
	 	collapsible: true,   // make collapsible
    	cmargins: '10 10 10 10', // adjust top margin when collapsed
    	id: 'west-region-container',
    	layout: 'fit',
		width:460,
        height:300,
        store: alphaGroupStore,
        title:'Search Group',
        iconCls:'icon-grid',
        loadMask: true,
		sm: new Ext.grid.RowSelectionModel({singleSelect:true}),
        // grid columns  
        tbar:[addGroupButton],         
    	bbar: groupsPageNavigation,
        columns:[{
	            //id: 'group_define_name', // id assigned so we can apply custom css (e.g. .x-grid-col-topic b { color:#333 })
	            header: "Group Name",
	            dataIndex: 'group_define_name',
	            width: 100,
	            align: 'left',
	            sortable: true
	        },
	        {
	            //id: 'title', // id assigned so we can apply custom css (e.g. .x-grid-col-topic b { color:#333 })
	            header: "Title",
	            dataIndex: 'title',
	            width: 280,
	            align: 'left',
	            sortable: true
	        },
			{
	            header: "Edit",
	            dataIndex: 'group_define_name',
	            width: 50,
	            align: 'center',
	            renderer: renderEdit,
	            sortable: true
	        }],
	    	viewConfig: {
            emptyText: 'No Groups found'

        	}, plugins:[new Ext.ux.grid.Search({
				 iconCls:'zoom'
				 ,minChars:2
				 ,position:'top'
				 ,autoFocus:true
				 ,minCharsTipText:'Type at least 2 characters'
			})],
			listeners:{ 
    		'loadexception': function(theO, theN, response){
    			//alert(response.responseText);
    		},
    		'beforeload': function(thisstore, options){
    			//thisstore.setBaseParam('letter', selectedTab);
    		},    		

    		'load': function(){
    				loadGroups(tabPanel, tab)
					//loadGroups(tabPanel, tab);	
    			}
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
    width:400,
    height:300,   
    store: abstractStore,    
    iconCls:'icon-grid',
    loadMask: true,
	sm: sm2,
	
    // grid columns
    cm: new Ext.grid.ColumnModel([
            sm2,{
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
    width: 950,
    height: 400,
    margins: '10 10 10 10',
    padding: '10 10 10 10',
    title: 'Group Administration',
    layout: 'border',
    items: [groupsGrid, SiteAdminGrid]
});

var gid;
var gname;
///////////// Form Panels

var editGroupPanel = new Ext.FormPanel({ 
			//standardSubmit: true,
			frame:true,
			title: 'Edit a Group',
			bodyStyle:'padding:5px 5px 0',
			width: 300,
			waitMsgTarget: true,
			defaultType: 'textfield',
			layout: 'form',
			items: [
					{
						fieldLabel: 'Group Name',
						name: 'groupname',
						itemId: 'groupname',
						allowBlank:false,
						vtype: 'groupname',
						invalidText:'This group is already taken'			
		        
					}],
					
			buttons: [{
				text: 'Update Group',
				id:'editgroup',
				handler: function (){
					//edituri = baseUri+"?module=groupadmin&action=json_editgroup&id="+gid;
					//editGroupPanel.getForm().getEl().dom.action = edituri;
					//editGroupPanel.getForm().submit();	

					if(editGroupPanel.getForm().isValid())
					{
						var v = editGroupPanel.get('groupname').getValue();

						editGroupPanel.getForm().submit({
								url: baseUri,
								params:{
										module: 'groupadmin',
										action: 'json_editgroup',
										oldgroupname: gname,
										id: gid
								},
								success: function(action){
								alphaGroupStore.load({params:{start:0, limit:25, query:v, 
fields: '["group_define_name"]'}});
									editGroupPanel.getForm().reset();	
								},        	
				            	failure:function(action){}
								});

						editwin.hide();
					}						
				}
			}]
				
});

var addNewGroupPanel = new Ext.FormPanel({ 
			//standardSubmit: true,
			//url: baseUri+"?module=groupadmin&action=json_addgroup",
			frame:true,
			title: 'Add a new Group',
			bodyStyle:'padding:5px 5px 0',
			width: 300,
			waitMsgTarget: true,
			defaultType: 'textfield',
			layout: 'form',
			items: [
					{
						fieldLabel: 'Group Name',
						name: 'groupname',
						itemId: 'groupname',
						allowBlank:false,
						vtype: 'groupname'
						//invalidText:'This group is already taken'			
		        
					}],
					
			buttons: [{
				text: 'Add Group',
				id:'addnewgroup',
				handler: function (){
					/*if(addNewGroupPanel.url)
					{
						//addNewGroupPanel.getForm().getEl().dom.action = addNewGroupPanel.url;
					}
					alert(addNewGroupPanel.getForm().isValid());*/
					
					if(addNewGroupPanel.getForm().isValid())
					{
						var v = addNewGroupPanel.get('groupname').getValue();

						addNewGroupPanel.getForm().submit({
								url: baseUri,
								params:{
										module: 'groupadmin',
										action: 'json_addgroup'
								},
								success: function(action){
								alphaGroupStore.load({params:{start:0, limit:25, query:v, 
fields: '["group_define_name"]'}});
									addNewGroupPanel.getForm().reset();	
								},        	
				            	failure:function(action){}
								});

						addwin.hide();
					}
				}
			}]
});

var val2 = "0";
Ext.apply(Ext.form.VTypes, {	

	groupname : function(val, field){
		if (field != "")
		{	
			groupAvailable(val);
			return (val2 == "1");		
		}else{		
			return false;
		}
	},
	groupNameText: 'Group ID arleady exist'
});

function showEditForm(groupid, groupname)
{
	gid = groupid;
	gname = groupname;
	
	if(!editwin){
		            editwin = new Ext.Window({
		                
		                layout:'fit',
		                width:400,
		                height:150,
		                closeAction:'hide',
		                plain: true,						
		                items: [editGroupPanel]		
		                
		            });
		        }
		        editwin.show(this);
				editGroupPanel.getForm().load({
								url: baseUri,
								params:{
										module: 'groupadmin',
										action: 'json_getgroup',
										id: gid},
								waitMsg:'Loading...',
								success: function(action){
								},        	
				            	failure:function(action){
								}
							});
}
////////////////////////////////
//// HELPER METHODS ////////////
///////////////////////////////

//this function will be called when 
//the group is selected in the groups grid
function loadGroup(nodeId, groupname, grouptitle){
	
	//load the subgroups
	subGroupStore.load({params:{start:0, limit:25, groupid: nodeId}});	
	
	SiteAdminGrid.setTitle(groupname+" - " +grouptitle);
	SiteAdminGrid.render('groupusers');
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
	for (var i = 0; i < records.length; ++i){
        scrollMenu.add({
            text: records[i].data.name,
             iconCls:'groups',
            itemId: records[i].data.groupid,
            handler: onSubGroupClick
        });
    }	
}

function onSubGroupClick(item){	
	proxyStore.setUrl(baseUri+'?module=groupadmin&action=json_getgroupusers&groupid='+item.getItemId());
	abstractStore.load({params:{start:0, limit:25}}); 
	selectedGroupId = item.getItemId();
}

//method that removes users from a group
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
		idString = r.id +','+ idString;		
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

 
 function groupAvailable(val)
	{   
		Ext.Ajax.request({
		    url: baseUri,
		    method: 'POST',
		    params: {
		           module: 'groupadmin',
		           action: 'checkgroup',
		           groupname: val
		    },
		    success: function(response) {
				var jsonData = Ext.util.JSON.decode(response.responseText);
		    	val2 = jsonData.data;
		    },
		    failure: function(xhr,params) {
		    	return false;
		    }
		});
}
