//the list of students
var win;


var proxyStudentData = new Ext.data.HttpProxy({
	url: baseUri+"?module=contextgroups&action=json_getstudents"
});

var studentdata = new Ext.data.JsonStore({
        root: 'users',
        totalProperty: 'totalCount',
        idProperty: 'id',
        remoteSort: false,        
        //fields: ['code', 'coursecode', 'title', 'lecturertitle', 'lecturers', 'accesstitle','access' ],
        fields: ['username', 
                 'firstname', 
                 'surname', 
                 'email', 
                 'isactive', 
                 'userid' ],
        //fields: ['firstname' ],
        proxy: proxyStudentData,
        listeners:{ 
    		'loadexception': function(theO, theN, response){
    		    alert(baseUri);
    			alert(response.responseText);
    		},
    		'load': function(){
    			//	alert('load');	
    			}
    	}
	});
	 studentdata.setDefaultSort('surname', 'asc');
	 
	 //the remove button
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
	 		                width:"60%",
	 		                height:350,
	 		                closeAction:'hide',
	 		                plain: true,						
	 		                items: [usersGridPanel]		
	 		                
	 		            });
	 		        }
	 		        win.show(this);
	 		        userStore.load({params:{start:0, limit:25}});
	 		        
	             }
	         },rmButton]
	 });
	 
	 //the checkbox object for selecting all
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
   
function renderIsActive(value, p, record)
	{
	    var isActive = record.data.isactive;
	    var img = 'accept'
	    if (isActive != 1){
	           img = 'decline';
	    }
	    
	    return '<img src="skins/_common2/css/images/sexybuttons/icons/silk/'+img+'.png" border="0" />';
		//return String.format('<img src="skins/_common2/css/images/sexybuttons/icons/silk/'+img+'.png" border="0" />',record.data.id);
	}
	
//the page navigation for the users in a group
var pageNavigation = new Ext.PagingToolbar({
            pageSize: pageSize,
            //store: abstractStore,
            store: studentdata,
            displayInfo: true,
            
            displayMsg: 'Displaying Users {0} - {1} of {2}',
            emptyMsg: "No Users to display",
            listeners:{ 	    		
	    		beforechange: function(ptb, params){	
	    			userOffset = params.start; 			
	    			proxyStudentData.setUrl(baseUri+'?module=contextgroups&action=json_getstudents&limit='+params.start+'&offset='+params.start);
	    		}  
            }
            
        });

//student grid
var studentgrid = new Ext.grid.GridPanel({
        //el:'courses-grid',
        width:"100%",
        height:400,
        tbar: toolBar,
        bbar: pageNavigation,
       // title:'My Courses',
        store: studentdata,
        trackMouseOver:false,
        disableSelection:true,
        loadMask: true,
        emptyText:"get out",
        sm: sm2,
     // grid columns
        cm: new Ext.grid.ColumnModel([
        // grid columns
       // columns:[
        sm2,{
            header: 'Student Number',
            dataIndex: 'userid',
            id:'firstname',
            width: 50,
            sortable: true
        },
        {
            header: 'FirstName',
            dataIndex: 'firstname',
            id:'firstname',
            width: 50,
            sortable: true
        },{
            id: 'surname', // id assigned so we can apply custom css (e.g. .x-grid-col-topic b { color:#333 })
            header: "Surname",
            dataIndex: 'surname',
            width: 50,
            //renderer: renderTitle,
            sortable: true
        },{
            header: 'Is Active',
            dataIndex: 'isactive',
            renderer:renderIsActive,
            id:'isactive',
            width: 30,
            hidden: false,
            sortable: false
        }]),

        // customize view config
        viewConfig: {
            forceFit:true,
            enableRowBody:true,
            showPreview:false,
            getRowClass : function(record, rowIndex, p, store){
                if(this.showPreview){
                    p.body = '<p><b>'+record.data.accesstitle+' </b></p><p>'+record.data.access+'</p>';
                    return 'x-grid3-row-expanded';
                }
                return 'x-grid3-row-collapsed';
            }
        },

        // paging bar on the bottom
       /* bbar: new Ext.PagingToolbar({
            pageSize: 500,
            store: studentdata,
            displayInfo: true,
            displayMsg: 'Displaying '+lang['lecturers']+' {0} - {1} of {2}',
            emptyMsg: "No "+lang['lecturers']+" to display",
            /*items:[
                '-', {
                pressed: false,
                enableToggle:true,
                text: 'Show Access Details',
                cls: 'x-btn-text-icon details',
                toggleHandler: function(btn, pressed){
                    var view = usergrid.getView();
                    view.showPreview = pressed;
                    view.refresh();
                }
            }]
        })*/
    });

//method that removes users from a group
function doRemoveUsers()
{	
	myMask.show();
	//get the selected users
	var selArr = studentgrid.getSelectionModel().getSelections();
	
	//get the selected id's
	var idString = "";
	
	Ext.each( selArr, function( r ) 
	{
		idString = r.id +','+ idString ;		
	});   	
	console.log(idString);
		//post to server
	Ext.Ajax.request({
	    url: baseUri,
	    method: 'POST',
	    params: {
	       	module: 'contextgroups',
	   		action: 'json_removestudents',
	   		//groupid: selectedGroupId,
	   		ids: idString
	    },
	    success: function(xhr,params) {
	        //alert('Success!\n'+xhr.responseText);
	    	studentdata.load({
	        	params:{
	        			start:userOffset, 
	        			limit:pageSize,
	        			//groupid:selectedGroupId,
	        			module:'contextgroups',
	        			action:'json_getstudents'
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
	console.log(idString);
		//post to server
	Ext.Ajax.request({
	    url: baseUri,
	    method: 'POST',
	    params: {
	       	module: 'contextgroups',
	   		action: 'json_addstudents',
	   		//groupid: selectedGroupId,
	   		ids: idString
	    },
	    success: function(xhr,params) {
	        //alert('Success!\n'+xhr.responseText);
	    	studentdata.load({
	        	params:{
	        			start:userOffset, 
	        			limit:pageSize,
	        			//groupid:selectedGroupId,
	        			module:'contextgroups',
	        			action:'json_getstudents'
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
