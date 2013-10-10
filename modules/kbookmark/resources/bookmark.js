/*
 * Ext JS Library 3.0 RC2
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 * By Qhamani Fenama
 * qfenama@gmail.com/qfenama@uwc.ac.za
 */
	var bookmarkpanel; 
	var addPanel;
	var editPanel;
	var editButton
	var addButton;
    var node1;
    var filepath;  
    var fileid;
    var filename;
    var addwin;
	var editwin;
    var selectedfolder;
    var fp;
	var bmid;
	var tree;
	var dirbrowser;
    //Ext.QuickTips.init();

	// turn on validation errors beside the field globally
	Ext.form.Field.prototype.msgTarget = 'side';

    var myMask = new Ext.LoadMask(Ext.getBody(), {msg:"Please wait..."});
	      
    var Tree = Ext.tree;

    var newIndex = 3;

	addButton = new Ext.Button({
	text:'Add Bookmark',
	tooltip:'Add Bookmark',
	iconCls: 'sexy-add',
	disabled: true,
	handler: function (){
		if(!addwin){
	            addwin = new Ext.Window({
	            layout:'fit',
				width:"35%",
				height:240,
				closeAction:'hide',
				plain: true,					
				items: [addPanel]	
	             });
		        }
				addwin.show(this);
	
        }});

    var tb = new Ext.Toolbar({
	items:[{
	    text: 'New Folder',
    	iconCls: 'sexy-newf',
	    disabled: true,
            handler: function(){

		createNewDir(node1);
	    }
	},{
	    text: 'Delete Folder',
    	iconCls: 'sexy-deletef',
	    disabled: true,
            handler: function(){
			Ext.MessageBox.confirm('Delete Bookmark Folder', "Are you sure you want to delete the selected Bookmark folder and it's content?", function	(btn, text){
				if (btn == 'yes')
				{
					deleteDir(node1);
	    		}})
			}
	}]
	});

	// create the Data Store
    var datastore = new Ext.data.JsonStore({
        root: 'bookmarks',
        totalProperty: 'totalCount',
        idProperty: 'id',
		remoteSort: true,
		fields: [
	    'id',
        'title', 
        'url',
	    'description'],
        proxy: new Ext.data.HttpProxy({
            url: baseuri+'?module=kbookmark&action=getBookmarks'}) 
    });

    tree = new Tree.TreePanel({
	animate:true, 
    autoScroll:true,
    loader: new Tree.TreeLoader({dataUrl: baseuri+'?module=kbookmark&action=getDir'}),
    enableDD:true,
    containerScroll: true,
    border: false,
    width: 250,
    height: 300,
    dropConfig: {appendOnly:true},
	tbar: tb,
	listeners: {
            'render': function(tp){
		
        tp.getSelectionModel().on('selectionchange', function(tree, node){
		node1 = node
		addButton.enable();
		tb.enable();
		selectedfolder = node.id;
		datastore.load({params:{id:selectedfolder}});
	})
            }},
	root: new Tree.AsyncTreeNode({
        text: 'root', 
        draggable:false, 
        id: defId
    })});
    
    // add a tree sorter in folder mode
    new Tree.TreeSorter(tree, {folderSort:true});
    
        
    function createNewDir(node){
	 	 
	var treeEditor =  new Ext.tree.TreeEditor(tree, {
		allowBlank:false
		,cancelOnEsc:true
		,completeOnEnter:true
		,ignoreNoChange:true
		,selectOnFocus:true
		});
	var newNode;
	 
	// get node to append the new directory to
	var appendNode = node.isLeaf() ? node.parentNode : node;
	 
	// create new folder after the appendNode is expanded
	appendNode.expand(false, false, function(n) {
	// create new node
	newNode = n.appendChild(new Ext.tree.AsyncTreeNode({text:'New Folder', iconCls:'folder'}));
	 
	// setup one-shot event handler for editing completed
	treeEditor.on("complete",
	function(o,newText,oldText){
	
	//post to server
		Ext.Ajax.request({
		    url: baseuri,
		    method: 'POST',
		    params: {
		       	module: 'kbookmark',
		   		action: 'creatFolder',
		   		parentfolder: selectedfolder,
				foldername: newText
		    },
		    success: function(response) {
		    var jsonData = Ext.util.JSON.decode(response.responseText);
		    if(jsonData.error)
			{
			Ext.Msg.alert('Error', jsonData.error);
			n.removeChild(newNode);
			treeEditor.destroy();
			}
		    else{
			newNode.setId(jsonData.data);
			treeEditor.destroy();
			}
		    },
		    failure: function(xhr,params) {
		}
		});
			
	}, this, true
	); 		  
	// start editing after short delay
	(function(){treeEditor.triggerEdit(newNode);}.defer(10));
	// expand callback needs to run in this context
	}.createDelegate(this));
    }

	function deleteDir(node){
		//post to server
		Ext.Ajax.request({
		    url: baseuri,
		    method: 'POST',
		    params: {
		       	module: 'kbookmark',
		   		action: 'deleteFolder',
		   		folderid: node.id
		    },
		    success: function(response) {
			//while(node.firstChild) {
    		//node.removeChild(node.firstChild);
			//}
			tree.getNodeById(defId).select();
			node.remove();
		    },
		    failure: function(xhr,params) {
		}
		});
		}
