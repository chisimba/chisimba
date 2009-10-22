
 var userDataProxy = new Ext.data.HttpProxy({
            url: baseUri+'?module=groupadmin&action=json_allusers&groupid='
        });
        



  // create the Data Store
 var userStore = new Ext.data.JsonStore({
        root: 'users',
        totalProperty: 'totalCount',
        idProperty: 'id',
        remoteSort: true,
		
        fields: [
            'username', 
            'firstName', 
            'surname', 
            'userid',           
            'lastloggedin',
            'emailAddress'
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
        proxy:userDataProxy 
    });
    
var userNavigation = new Ext.PagingToolbar({
            pageSize: 25,
            store: userStore,
            displayInfo: true,
            
            displayMsg: 'Displaying Users {0} - {1} of {2}',
            emptyMsg: "No Users to display",
            listeners:{ 	    		
	    		beforechange: function(ptb, params){	
	    			userOffset = params.start; 			
	    			proxyStore.setUrl(baseUri+'?module=groupadmin&action=json_allusers&groupid='+selectedGroupId+'&limit='+params.start+'&offset='+params.start);
	    		}  
            }
             
            
        });
    
var sm3 = new Ext.grid.CheckboxSelectionModel({
        listeners: {
            // On selection change, set enabled state of the removeButton
            // which was placed into the GridPanel using the ref config
            selectionchange: function(sm) {
                if (sm.getCount()) {
                    addButton.enable();
                } else {
                    addButton.disable();
                }
            }
        }
    });

var addButton  = new Ext.Button({
            text:'Add Users',
            tooltip:'Add the selected User',
            iconCls:'silk-add',
			id:'addtogroup',
            // Place a reference in the GridPanel
            //ref: '../../removeButton',
            disabled: true,
            handler: function(){
            	doAddUsers();
            
            }
        });

var usertoolBar = new Ext.Toolbar({
	items:[addButton]
});
var usersGridPanel = new Ext.grid.GridPanel({
	title:'Search Users',

	frame:true,
    layout: 'fit',
    margins: '10 10 10 10',	 
	tbar: usertoolBar,        
    bbar:userNavigation,    
    width:600,
    height:300,   
    store: userStore,    
    iconCls:'icon-grid',
    loadMask: true,
	sm: sm3,
	
    // grid columns
    cm: new Ext.grid.ColumnModel([
          sm3,{
            header: "Last Name",
            dataIndex: 'surname',
            width: 150,            
            sortable: true
        },{
            id: 'firstName', // id assigned so we can apply custom css (e.g. .x-grid-col-topic b { color:#333 })
            header: "First Name",
            dataIndex: 'firstName',
            width: 150,            
            sortable: true
        },{
            header: "Username",
            dataIndex: 'username',
            width: 100,           
            sortable: true
        },{
        	id: 'emailAddress',
            header: "Email",
            dataIndex: 'emailAddress',
            width: 150           
        }]),
		
    viewConfig: {
        forceFit:true,
        emptyText: 'No Users found'
    	},
    	
	plugins:[new Ext.ux.grid.Search({
				 iconCls:'zoom'
				 //,readonlyIndexes:['emailaddress']
				 //,disableIndexes:['username']
				 ,minChars:3
				 ,autoFocus:true
				 ,position:'top'
				 // ,menuStyle:'radio'
		 })]
	
});

