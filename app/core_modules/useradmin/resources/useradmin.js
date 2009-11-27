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
		    idProperty: 'userid',
		    remoteSort: false,        
		    fields: ['userId',
					,'staffnumber'
					,'username' 
					,'title'
					,'firstname'
					,'surname'
					,'emailaddress'
					,'howcreated'
					,'isactive'
					,
					{
					  name: 'delete'
		    		},
					{
					  name: 'edit'
		    		}],

		    proxy: proxyUserStore
	});

	 userdata.setDefaultSort('surname', 'asc');

	//custom column plugin example
    var activecheckColumn = new Ext.grid.CheckColumn({            
            header: "Active",
            dataIndex: 'isactive',
            width: 55,
            sortable: true
        });

	var LDAPcheckColumn = new Ext.grid.CheckColumn({
            header: "LDAP",
            dataIndex: 'howcreated',
            width: 55,
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
					width:750,
					height:320,
					closeAction:'hide',
					plain: true,					
					items: [adduser]	
		             });
		        }
				addwin.show(this);
				
			}
	}]});

	function showForm(sid)
	{
		vid = sid;
		if(!editwin){
			editwin = new Ext.Window({
				
				layout:'fit',
				width:750,
				height:320,
				closeAction:'hide',
				plain: true,					
				items: [edituser]		
			 });
		}
	
		editwin.show(this);
		edituser.getForm().load({
								url:baseuri,
								params: {
									module: 'useradmin',
									action: 'jsongetSingleUser',
									id:	sid
									},
								waitMsg:'Loading',

								success:function(form, action) {
								}, 
       	
				            	failure:function(form, action) {
								}
							});
				
	}

    var usergrid = new Ext.grid.GridPanel({
        el: 'user-grid',
        width: 900,
        height: 400,
		frame: true,
		title:'Browse Users',
        store: userdata,
        trackMouseOver:false,
        disableSelection:true,
        loadMask: true,
		emptyText:'No Users Found',
		tbar: toolBar,
		
        //grid columns
        columns:[
        {
            header: "Staff/Stud No.",
            dataIndex: 'staffnumber',
            width: 100,            
            sortable: true
        },{
            header: "Username",
            dataIndex: 'username',
            width: 120,
            sortable: true
        },{
            header: "Title",
            dataIndex: 'title',
            width: 55,
            hidden: false,
            sortable: true
		},{            
            header: "First Name",
            dataIndex: 'firstname',
            width: 120,
            sortable: true
		},{
            header: "Surname",
            dataIndex: 'surname',
            width: 120,
            sortable: true
		},{
            header: "Email",
            dataIndex: 'emailaddress',
            width: 120,
            sortable: true
        },
			LDAPcheckColumn
		,
			activecheckColumn
		,{            
            header: "Delete",
            dataIndex: 'delete',
            width: 55,
            sortable: true
        },{            
            header: "Edit",
            dataIndex: 'edit',
            width: 55,
            sortable: true
        }],

        // customize view config
        plugins:[new Ext.ux.grid.Search({
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
