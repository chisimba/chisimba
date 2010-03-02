/*
 * Ext JS Library 3.0 RC2
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 * By Qhamani Fenama
 * qfenama@gmail.com/qfenama@uwc.ac.za
 */
    var proxyUserStore = new Ext.data.HttpProxy({
		        url:baseuri+'?module=useradmin&action=jsongetusers&start=0&limit=25'
		    });
	   
	var userdata = new Ext.data.JsonStore({
		    root: 'users',
		    totalProperty: 'usercount',
		    idProperty: 'id',
		    remoteSort: false,        
		    fields: ['id'
					,'userid'
					,'staffnumber'
					,'username' 
					,'title'
					,'firstname'
					,'surname'
					,'emailaddress'
					,'howcreated'
					,'isactive'],

		    proxy: proxyUserStore
	});


	// pluggable renders
    function renderDelete(value, p, record)
	{
		return String.format('<b><a href="javascript:deleteUser(\'{0}\')"><img src="skins/_common/css/extjs/silk/fam/delete.gif" border="0" alt="Delete User" title="Delete User" /></a></b>',record.data.id);
	}


	function renderEdit(value, p, record)
	{
		return String.format('<b><a href="javascript:showForm(\'{0}\')"><img src="skins/_common/css/extjs/silk/fam/user_edit.png" border="0" alt="Edit User" title="Edit User" /></a></b>',record.data.id);
    }
	
	userdata.setDefaultSort('surname', 'asc');

	//custom column plugin example
    var activecheckColumn = new Ext.grid.CheckColumn({            
            header: "Active",
            dataIndex: 'isactive',
            width: 40,
            sortable: true
        });

	var LDAPcheckColumn = new Ext.grid.CheckColumn({
            header: "LDAP",
            dataIndex: 'howcreated',
			editable : true,
            width: 40,
            sortable: true
		});

	var toolBar = new Ext.Toolbar({
	items:[{
            text:'Add User',
            tooltip:'Add User',
            iconCls: 'silk-add',
            handler: function (){
	        	if(!addwin){
		            addwin = new Ext.Window({
		            layout:'fit',
					width:"65%",
					height:320,
					closeAction:'hide',
					plain: true,					
					items: [adduser]	
		             });
		        }
				addwin.show(this);
				
			}
	}]});

	function deleteUser(sid)
	{
		Ext.MessageBox.confirm('Delete User', "Are you sure you want to delete the user?", function(btn, text) 			{
			if (btn == 'yes')
			{
				Ext.Ajax.request({
				url: baseuri,
				method: 'POST',
				params: {
				       module: 'useradmin',
				       action: 'deleteuser',
				       id: sid
				},
				success: function(response) {
				userdata.load({params:{start:0, limit:25}});
				},
				failure: function(xhr,params) {
				}});
			}
		});
	}

	function showForm(sid)
	{
		vid = sid;
		//edituser.getForm().reset();
		if(!editwin){
			editwin = new Ext.Window({
				
				layout:'fit',
				width:"65%",
				height:320,
				closeAction:'hide',
				plain: true,					
				items: [edituser]		
			 });
		}
	
		editwin.show(this);
		edituser.getForm().doAction('load',{
								url:baseuri,
								params: {
									module: 'useradmin',
									action: 'jsongetSingleUser',
									id:	sid
									},
								waitMsg:'Loading...',
								success: function(form, action) {
								//set the radio buttons								
								var fID = 'sex_' + action.result.data.useradmin_sex;
								Ext.getCmp(fID).setValue(true);
								fID = 'active_' + action.result.data.accountstatus;
								Ext.getCmp(fID).setValue(true);
								}, 
       	
				            	failure:function(form, action) {
								}
							});
	}
	var cm = new Ext.grid.ColumnModel([{
		header: "Identification No.",
            dataIndex: 'staffnumber',
            width: 100,            
            sortable: true
        },{
            header: "Username",
            dataIndex: 'username',
            width: 85,
            sortable: true
        },{
            header: "Title",
            dataIndex: 'title',
            width: 70,
            hidden: false,
            sortable: true
		},{            
            header: "First Name",
            dataIndex: 'firstname',
            width: 100,
            sortable: true
		},{
            header: "Surname",
            dataIndex: 'surname',
            width: 100,
            sortable: true
		},{
            header: "Email",
            dataIndex: 'emailaddress',
            width: 100,
            sortable: true
		},
			LDAPcheckColumn
		,
			activecheckColumn
		,{            
            header: "Delete",
            dataIndex: 'id',
			renderer: renderDelete,
            width: 40,
            sortable: true
        },{            
            header: "Edit",
			dataIndex: 'id',
            renderer: renderEdit,
            width: 40,
            sortable: true
        }]);

   var usergrid = new Ext.grid.EditorGridPanel({
        el: 'user-grid',
		cm: cm,
        width: "100%",
        height: 400,
	frame: true,
	title:'Browse Users',
        store: userdata,
        trackMouseOver:false,
        disableSelection:true,
        loadMask: true,
		emptyText:'No Users Found',
		//clicksToEdit: 1,
		tbar: toolBar,
		       
        // customize view config
        plugins: [new Ext.ux.grid.Search({
				 iconCls:'zoom'
				 ,disableIndexes:['isactive', 'howcreated', 'delete', 'edit']
				 ,minChars:1
				 ,position:'top'
				 ,autoFocus:true
		})],

        // paging bar on the bottom
        bbar: new Ext.PagingToolbar({
            pageSize: 25,
            store: userdata,
            displayInfo: true,
            displayMsg: 'Users {0} - {1} of {2}',
            emptyMsg: "No Users to display"
            
        })
    });

Ext.onReady(function(){
	//trigger the data store load
    userdata.load({params:{start:0, limit:25}});
	
	// render it
    usergrid.render();});
