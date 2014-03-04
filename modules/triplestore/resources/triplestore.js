/*
 * Ext JS Library 3.0 RC2
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 * By Qhamani Fenama
 * qfenama@gmail.com/qfenama@uwc.ac.za
 */
Ext.QuickTips.init();
// turn on validation errors beside the field globally
Ext.form.Field.prototype.msgTarget = 'side';
var selectedfolder;
var selectedid;
var selectedData;
var winup;
var winadd;
var winupdt;
var Tree = Ext.tree;
var maintree;
var loadingMask = new Ext.LoadMask(Ext.getBody(), {
    msg:"Please wait..."
});
var txtfield = new Ext.form.TextField({
    id: 'txtfield',
    width: 30,
    fieldLabel: 'Delimiter',
    name: 'delimiter',
    allowBlank: false,
    value: ',',
    enableKeyEvents: true
});

Ext.apply(Ext.form.VTypes, {
    extension : function(val, field) {
        if (field != '') {
            var str = val.substring(val.length - 3, val.length);
            if(str == 'csv' || str == 'xml'){
                return true;
            }
            return false;
        }
        return true;
    },
    extensionText : 'Invalid File Extension'
});

//Uploading form
var uploadform = new Ext.form.FormPanel({
    fileUpload: true,
    url: uri + '?module=triplestore&action=upload',
    frame: true,
    title: 'Upload File',
    width: 450,
    labelWidth: 50,
    autoHeight: true,
    defaultType: 'textfield',
    bodyStyle: 'padding: 10px 10px 0 10px;',
    items: [{
        xtype: 'radiogroup',
        name: 'filetype_chooser',
        fieldLabel: 'FileType',
        items: [
        {
            xtype: 'radio',
            labelSeparator: '',
            name: 'filetype',
            boxLabel: 'csv',
            inputValue: 'csv',
            checked: true,
            handler: function () {
                var f = uploadform.findById('txtfield');
				
                f.container.up('div.x-form-item').hide();
            }
        },
        {
            xtype: 'radio',
            labelSeparator: '',
            name: 'filetype',
            boxLabel: 'xml',
            inputValue: 'xml',
            handler: function () {
                var f = uploadform.findById('txtfield');
                f.container.up('div.x-form-item').show();
            }
        }
        ]
    },
    txtfield,
    {
        xtype: 'fileuploadfield',
        vtype: 'extension',
        id: 'form-file',
        emptyText: 'Select a file',
        fieldLabel: 'File',
        name: 'path1',
        buttonText: 'Browse...',
        allowBlank: false
    }],
    buttons: [{
        text: 'Upload File',
        handler: function () {
            loadingMask.show();
            if (uploadform.url)
            {
                uploadform.getForm().getEl().dom.action = uploadform.url;
            }
            uploadform.getForm().submit({
                success: function (fp, o) {
                    winup.hide();
                    reload();
		    //uploadform.getForm().reset();
                    loadingMask.hide();
                },
                failure: function(xhr,params) {
                    loadingMask.hide();
                }
            });
        }
    }]

});
function hideField(field)
{
    field.disable();// for validation
    field.hide();
    field.getEl().up('.x-form-item').setDisplayed(false); // hide label
}

function showField(field)
{
    field.enable();
    field.show();
    field.getEl().up('.x-form-item').setDisplayed(true);// show label
}

var addform = new Ext.FormPanel({
    frame: true,
    url: uri + '?module=triplestore&action=save&mode=add',
    title: 'Add Triple',
    width: 320,
    autoHeight: true,
    bodyStyle: 'padding: 10px 10px 0 10px;',
    labelWidth: 50,
    defaultType: 'textfield',
    items: [{
	id: 'subject',
        fieldLabel: 'Subject',
        name: 'subject',
        allowBlank: false
    },
    {	
	id: 'predicate',
        fieldLabel: 'Predicate',
        name: 'predicate',
        allowBlank: false
    },
    {
	id: 'object',
        fieldLabel: 'Object',
        name: 'object',
        allowBlank: false
    }],
    buttons: [{
        text: 'Add Triple',
        handler: function () {
            loadingMask.show();
            if (addform.url)
            {
                addform.getForm().getEl().dom.action = addform.url;
            }
            addform.getForm().submit({
                success: function (fp, o) {
                    winadd.hide();
                    reload();
		    addform.getForm().reset();
                    loadingMask.hide();
                },
                failure: function(xhr,params) {
                    loadingMask.hide();
                }
            });
        }
    }]
});

var updtform = new Ext.FormPanel({
    frame: true,
    title: 'Update Triple',
    width: 320,
    labelWidth: 50,
    autoHeight: true,
    bodyStyle: 'padding: 10px 10px 0 10px;',
    defaultType: 'textfield',
    items: [{
        fieldLabel: 'Subject',
        name: 'subject',
        allowBlank: false
    },
    {
        fieldLabel: 'Predicate',
        name: 'predicate',
        allowBlank: false
    },
    {
        fieldLabel: 'Object',
        name: 'object',
        allowBlank: false
    }],
    buttons: [{
        text: 'Update Triple',
        handler: function () {
            loadingMask.show();
            updtform.getForm().getEl().dom.action = uri + '?module=triplestore&action=save&mode=edit&id='+selectedid;
            updtform.getForm().submit({
                success: function (fp, o) {
                    winupdt.hide();
                    reload();
		    updtform.getForm().reset();
                    loadingMask.hide();
                },
                failure: function(xhr,params) {
                    loadingMask.hide();
                }
            });
        }
    }]
});

var addButton = new Ext.Button({
    text: 'Add',
    tooltip: 'Add Triple',
    iconCls: 'sexy-add',
    handler: function () {
	addform.getForm().reset();
        winadd = new Ext.Window({
            layout: 'fit',
            width: 320,
            autoHeight: true,
            closeAction: 'hide',
            plain: true,
            items: [addform]
        });	
        winadd.show(this);
    }
});

var upButton = new Ext.Button({
    text: 'Upload',
    tooltip: 'Upload csv/xml',
    iconCls: 'sexy-upload',
    handler: function () {
        winup = new Ext.Window({
            layout: 'fit',
            width: 320,
            autoHeight: true,
            closeAction: 'hide',
            plain: true,
            items: [uploadform]
        });
        winup.show(this);
    }
});

var dltButton = new Ext.Button({
    text: 'Delete',
    tooltip: 'Delete Triple(s)',
    iconCls: 'sexy-delete',
    disabled: true,
    handler: function () {
        Ext.MessageBox.confirm('Delete Triple(s)', "Are you sure you want to remove the selected Triple(s)?",
            function(btn, text){
                if (btn == 'yes')
                {

                    loadingMask.show();
                    //get the selected files
                    var selArr = maingrid.getSelectionModel().getSelections();

                    //get the selected id's
                    var idString = "";

                    Ext.each( selArr, function( r )
                    {
                        idString = r.id +'|'+ idString ;
                    });
                    // execute an XHR to send id of the triplestore you want to delete to the server
                    Ext.Ajax.request({
                        url: baseuri,
                        method: 'POST',
                        params: {
                            module: 'triplestore',
                            action: 'removeTriples',
                            id: idString
                        },
                        success: function(obj) {
                            reload();
                            loadingMask.hide();
                        },
                        failure: function(xhr,params) {
                            loadingMask.hide();
                        }
                    });
                }
            });
    }
});

var updButton = new Ext.Button({
    text: 'Update',
    tooltip: 'Update Triple',
    iconCls: 'sexy-pencil',
    disabled: true,
    handler: function () {
        winupdt = new Ext.Window({
            layout: 'fit',
            width: 320,
            autoHeight: true,
            closeAction: 'hide',
            plain: true,
            items: [updtform]
        });
	updtform.getForm().reset();
        winupdt.show(this);
        updtform.getForm().doAction('load',{
            url:baseuri,
            params: {
                module: 'triplestore',
                action: 'getsingletriples',
                id:	selectedid
            },
            waitMsg:'Loading...',
            success: function(form, action) {
            },
            failure:function(form, action) {
            }
        });
    }
});

var toolBar = new Ext.Toolbar({
    items: [addButton, upButton, updButton, dltButton]
});

maintree = new Tree.TreePanel({
    id: 'tree',
    rootVisible:false,
    autoScroll: true,
    loader: new Tree.TreeLoader({
        dataUrl: baseuri + '?module=triplestore&action=gettree'
    }),
    enableDD: true,
    containerScroll: true,
    border: false,
    width: "100%",
    height: 300,
    dropConfig: {
        appendOnly: true
    },
    listeners: {
        'render': function (tp) {
            tp.getSelectionModel().on('selectionchange', function (tree, node) {
                selectedfolder = node.id;
                datastore.load({
                    params: {
                        node: selectedfolder
                    }
                });
            })
        }
    }
});

// add a tree sorter in folder mode
new Tree.TreeSorter(maintree, {
    folderSort: true
});

// set the root node
var root = new Tree.AsyncTreeNode({
    iconCls: 'sexy-add',
    text: 'Triple Store',
    id: 'subject|root',
    expanded:true
});
var ctxmenu = new Ext.menu.Menu({
id:'ctxmenu',
items: [{
    iconCls:'sexy-add',
    text:'Add Triple',
    handler:function(){
	loadForm(selectedData);
}
},'-',{
    iconCls:'sexy-cancel',
    text:'Cancel',
    handler: function(){
	ctxmenu.hide();
}
}]
});

function showContextMenu(node){
	selectedData = node.id;//alert(node.ui.getEl());
    	ctxmenu.show(node.ui.getEl());

}

maintree.setRootNode(root);

maintree.on('contextMenu', showContextMenu, this);

root.expand(false, true);

// create the Data Store
var datastore = new Ext.data.JsonStore({
    root: 'data',
    totalProperty: 'totalCount',
    idProperty: 'id',
    remoteSort: true,
    fields: [
    'id',
    'subject',
    'predicate',
    'object'],
    proxy: new Ext.data.HttpProxy({
        url: baseuri + '?module=triplestore&action=getdata'
    })
});

var fm = Ext.form;

var westpanel = new Ext.Panel({
    id: 'left-panel',
    region: 'west',
    width: "25%",
    height: 350,
    margins: '5 5 5 5',
    layout: {
        type: 'accordion',
        animate: true
    },
    items: [maintree]
});

var sm2 = new Ext.grid.CheckboxSelectionModel({
    listeners: {
        selectionchange: function(sm) {
            if(sm.getCount() == 1){
                dltButton.enable();
                updButton.enable();
            }
            else{
                dltButton.enable();
                updButton.disable();
            }
        }
    }
});

var pageNavigation = new Ext.PagingToolbar({
    pageSize: 25,
    store: datastore,
    displayInfo: true,
    displayMsg: 'Displaying triple {0} - {1} of {2}',
    emptyMsg: "No Triple to display",
    items:[]
});	


var maingrid = new Ext.grid.EditorGridPanel({
    region: 'center',
    id: 'center-panel',
    margins: '5 5 5 5',
    width: "70%",
    height: 350,
    ds: datastore,
    sm: sm2,
    viewConfig: {
        emptyText: 'No Triple Found'
    },
    cm: new Ext.grid.ColumnModel([
    {
        id: 'subject',
        header: "Subject",
        dataIndex: 'subject',
        width: 250,
        sortable: true
    },

    {
        id: 'predicate',
        header: "Predicate",
        dataIndex: 'predicate',
        width: 250,
        sortable: true,
        editor: new fm.TextField({
            allowBlank: false
        })
    },

    {
        id: 'object',
        header: "Object",
        dataIndex: 'object',
        width: 200,
        sortable: true,
        editor: new fm.TextField({
            allowBlank: false
        })
    }
    ]),
    // paging bar on the bottom
    bbar: pageNavigation
});

maingrid.on('afteredit', afterEdit, this );

function afterEdit(e){
    // execute an XHR to send/commit data to the server, in callback do (if successful):
    Ext.Ajax.request({
        url: baseuri,
        method: 'POST',
        params: {
            module: 'triplestore',
            action: 'saveinline',
            id: e.field+"|"+e.record.id,
            value: e.value
        },
        success: function(response) {
            e.record.commit();
        },
        failure: function(xhr,params) {
        }
    });
}

function concatObject(obj) {
    var str='';
    for(prop in obj)
    {
        str += prop + " value :"+ obj[prop]+"\n";
    }
   return str;
} 

var main = new Ext.Panel({
    id: 'main',
    el: 'mainpanel',
    layout: 'border',
    width: "95%",
    height: 350,
    tbar: toolBar,
    title: 'Triple Store',
    items: [maingrid, westpanel]
});

maingrid.getSelectionModel().on('rowselect',
    function(sm, ri, record)
    {
        selectedid = record.data.id;
    });

function reload(){
    loadingMask.show();
    maintree.root.reload();
    datastore.load({
        params:{
            node: selectedfolder
        }
    });
loadingMask.hide();
}

function loadForm(id){
	loadingMask.show();
	Ext.Ajax.request({
        url: baseuri,
        method: 'POST',
        params: {
            module: 'triplestore',
            action: 'getformdata',
            id: id
        },
        success: function(response) {
	var jsonData = Ext.util.JSON.decode(response.responseText);
	addform.findById('subject').setValue(jsonData.data.subject);
	addform.findById('predicate').setValue(jsonData.data.predicate);
	winadd = new Ext.Window({
            layout: 'fit',
            width: 320,
            autoHeight: true,
            closeAction: 'hide',
            plain: true,
            items: [addform]
        });
	//addform.getForm().reset();
        winadd.show(this);
        loadingMask.hide();
        },
        failure: function(xhr,params) {
            loadingMask.hide();
        }
    });}

Ext.onReady(function () {
    //render mainpanel
    main.render();
    datastore.load({
        params:{
            node: selectedfolder
        }
    });
});
