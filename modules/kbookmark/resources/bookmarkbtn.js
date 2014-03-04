/*
 * Ext JS Library 3.0 RC2
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 * By Qhamani Fenama
 * qfenama@gmail.com/qfenama@uwc.ac.za
 */

var win;
var comboxWithTree = new Ext.form.ComboBox({
		store:new Ext.data.SimpleStore({fields:[],data:[[]]}),
		fieldLabel:'Folder',
		width: "2%",
		editable:false,
		shadow:false,
		mode: 'local',
		allowBlank:false,
		triggerAction:'all',
		maxHeight: 200,
		tpl: '<tpl for="."><div style="height:200px"><div id="tree"></div></div></tpl>',
		selectedClass:'',
		onSelect:Ext.emptyFn
	});

	tree.on('click',function(node){
  		comboxWithTree.setValue(node.text);
  		comboxWithTree.collapse();
	});

	comboxWithTree.on('expand',function(){
		tree.render('tree');
	});

var formPanel = new Ext.FormPanel({
			frame:true,
			title: 'Add a Bookmark',
			bodyStyle:'padding:5px 5px 0',
			width: "35%",
			defaultType: 'textfield',
			items: [
					{fieldLabel: 'Title',
						name: 'add_title',
						allowBlank:false,
					   	value: vtitle
					},{
						fieldLabel: 'Location',
						name: 'add_url',
						allowBlank:false,
						vtype: 'url',
					   	value: vurl
					},comboxWithTree,{
						fieldLabel: 'Description',
						name: 'add_description',
						value: vdescription
					},{
						fieldLabel: 'Tags',
						name: 'add_tags',
						emptyText: 'Separate tags with commas',
					   	value: vtags
					}],
			buttons: [{
				text: 'Submit Bookmark',
				handler: function (){
					if(formPanel.getForm().isValid())
					{
						adduri = baseuri+"?module=kbookmark&action=addBookmark&folderid="+selectedfolder;
						formPanel.getForm().submit({
						url: adduri,
						success: function(action){
							formPanel.getForm().reset();
						},        	
			        	failure:function(action){
							formPanel.getForm().reset();
						}});
					win.hide();
					}
				}
			}]
		});

var btn = new Ext.Button({
	el:'btn',
	iconCls: 'sexy-bookmark',
	text:'Bookmark this page',
	style:'margin:20px 0 0 0',
	handler:function() {
		if(!win){
	            win = new Ext.Window({
	            layout:'fit',
				width:"40%",
				height:240,				
				closeAction:'hide',
				plain: true,					
				items: [formPanel]	
	             });
		        }
				win.show(this);		
	}});

    Ext.onReady(function(){
    //render btn
	btn.render();
    });
