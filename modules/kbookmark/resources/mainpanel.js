/*
 * Ext JS Library 3.0 RC2
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 * By Qhamani Fenama
 * qfenama@gmail.com/qfenama@uwc.ac.za
 */

addPanel = new Ext.FormPanel({
			frame:true,
			title: 'Add Bookmark',
			bodyStyle:'padding:5px 5px 0',
			width: "35%",
			defaultType: 'textfield',
			items: [
					{
						fieldLabel: 'Title',
						name: 'add_title',
						allowBlank:false
					},{
						fieldLabel: 'Location',
						name: 'add_url',
						allowBlank:false,
						vtype: 'url'
					},{
						fieldLabel: 'Description',
						name: 'add_description',
						allowBlank:false
					},{
						fieldLabel: 'Tags',
						name: 'add_tags',
						emptyText: 'Separate tags with commas'
					}],
			buttons: [{
				text: 'Submit Bookmark',
				handler: function (){
					if(addPanel.getForm().isValid() )
					{
						adduri = baseuri+"?module=kbookmark&action=addBookmark&folderid="+selectedfolder;
						addPanel.getForm().submit({
						url: adduri,
						success: function(action){
							addPanel.getForm().reset();
							datastore.load({params:{id:selectedfolder}});	
						},        	
			        	failure:function(action){
							addPanel.getForm().reset();
						}});
					addwin.hide();
					}
					}
			}]
		});

	editPanel = new Ext.FormPanel({
			frame:true,
			title: 'Update Bookmark',
			bodyStyle:'padding:5px 5px 0',
			width: "35%",
			defaultType: 'textfield',
			items: [
					{	fieldLabel: 'Title',
						name: 'edit_title',
						allowBlank:false
					},{
						fieldLabel: 'Location',
						name: 'edit_url',
						allowBlank:false,
						vtype: 'url'
					},{
						fieldLabel: 'Description',
						name: 'edit_description'
					},{
						fieldLabel: 'Tags',
						name: 'edit_tags'
					}],
			buttons: [{
				text: 'Update Bookmark',
				handler: function (){
					adduri = baseuri+"?module=kbookmark&action=upadateBookmark&id="+bmid;
					editPanel.getForm().submit({
					url: adduri,
					success: function(action){
						editPanel.getForm().reset();
						datastore.load({params:{id:selectedfolder}});	
					},        	
	            	failure:function(action){
						editPanel.getForm().reset();
					}});
				editwin.hide();
				}
			}]
		});
   
    
    editButton = new Ext.Button({
	text:'Edit Bookmark',
	tooltip:'Edit Bookmark',
	iconCls: 'sexy-pencil',
	disabled: true,
	handler: function (){
		if(!editwin){
	            editwin = new Ext.Window({
	            layout:'fit',
				width:"35%",
				height:240,
				closeAction:'hide',
				plain: true,					
				items: [editPanel]	
	             });
		        }
			editwin.show(this);
			bmid = dirbrowser.getSelectionModel().getSelected().get('id');
			editPanel.getForm().doAction('load',{
				url:baseuri,
				params: {
					module: 'kbookmark',
					action: 'getSingleBookmark',
					id:	bmid
					},
				waitMsg:'Loading...',
				success: function(form, action) {}, 
            	failure:function(form, action) {}
			});
	
        }});
	
    var deleteButton = new Ext.Button({
	text:'Delete Bookmark',
	tooltip:'Delete File(s)',
	iconCls: 'sexy-delete',
	disabled: true,
	handler: function (){
	
	Ext.MessageBox.confirm('Delete Bookmark(s)', "Are you sure you want to delete the selected Bookmark(s)?", function	(btn, text){
	if (btn == 'yes')
	{
		myMask.show();
		//get the selected files
		var selArr = dirbrowser.getSelectionModel().getSelections();

		//get the selected id's
		var idString = "";

		Ext.each( selArr, function( r ) 
		{
			idString = r.id +','+ idString ;		
		}); 
			
		//post to server
		Ext.Ajax.request({
		    url: baseuri,
		    method: 'POST',
		    params: {
		       	module: 'kbookmark',
		   		action: 'deleteBookmark',
		   		ids: idString
		    },
		    success: function(xhr,params) {
			datastore.load({params:{id:selectedfolder}});
			myMask.hide();
		    },
		    failure: function(xhr,params) {
			alert('Failure!\n'+xhr.responseText);
			myMask.hide();
		    }});    	
	}});
	}});
    
	var toolBar = new Ext.Toolbar({
	items:[addButton, deleteButton, editButton]});
	 
    function renderUrl(value, p, record)
    {
	return String.format('<b><a href="'+baseuri+'?module=kbookmark&action=openPage&pageid={0}">{1}</a></b>', record.data.id, record.data.url);
    }

    
    var sm2 = new Ext.grid.CheckboxSelectionModel({
        listeners: {
            // On selection change, set enabled state of the removeButton
	    selectionchange: function(sm) {
		deleteButton.disable();
		
		if (sm.getCount()) {
			deleteButton.enable();
		    if (sm.getCount() == 1){
		    	editButton.enable();
			}
			else
			{
				editButton.disable();
			}
    	 }
	     else{
		    deleteButton.disable();
		    editButton.disable();
	     }
	    }
        }
    });

   dirbrowser = new Ext.grid.GridPanel({
	region: 'center',
	id: 'center-panel', 
	split: true,
	width: 200,
	minSize: 175,
	maxSize: 400,
	margins: '0 0 0 5',
	frame:true,
	layout: 'fit',
	tbar: toolBar,        
	store: datastore,    
	iconCls:'icon-grid',
	loadMask: true,
	sm: sm2,
	viewConfig: {
	emptyText: 'No Bookmarks found'
	},

	// grid columns
	cm: new Ext.grid.ColumnModel([
	{
	id: 'title',
	header: "Title",
	dataIndex: 'title',
	width: 120           
	},
	{
	id: 'url',
	header: "Location",
	dataIndex: 'url',
	width: 250,
	renderer: renderUrl,            
	sortable: true
	},
	{
	id: 'description',
	header: "Description",
	dataIndex: 'description',
	width: 250,            
	sortable: true
	}
	])
    });
    

    bookmarkpanel = new Ext.Panel({
	id:'main',
	el:'mainpanel',
	layout: 'border',
	width: "70%",
	height: 350,
	title:'My Bookmark',
	items: [
	{
	region: 'west',
	width: 200,
	minSize: 175,
	maxSize: 400,
	collapsible: true,
	margins: '0 0 0 5',
	items: [tree]
	},dirbrowser]
	});

Ext.onReady(function(){
	bookmarkpanel.render();
	tree.getNodeById(defId).select();

});
