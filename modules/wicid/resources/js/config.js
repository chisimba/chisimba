/*!
 * Ext JS Library 3.0.0
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */

var typeURL;
var win;

var showButtons = function() {
    // buttons
    var p = new Ext.Panel({
        layout: 'table',
        autoWidth: true,
        style: 'marginRight: 10px',
        baseCls: 'x-plain',
        cls: 'btn-panel',
        border: false,
        defaultType: 'button',
        id: 'myButtons',
        items: [{
            text: 'Back',
            scale: 'medium',
            baseCls: 'x-plain',
            cls: 'btn-panel',
            handler: function() {
                goBack();
            }
        },{
            text: 'Add File Type',
            scale: 'medium',
            baseCls: 'x-plain',
            cls: 'btn-panel',
            handler: function() {
                goAddType();
            }
        }]
    });
    p.render("buttons");
}

var showTabs = function() {
    //var args=showTabs.arguments;
    var selectedTab=0; //args[0];

    // basic tabs, first tabs contains the Course details, second tabs contains course history
    var tabs = new Ext.TabPanel({
        renderTo: 'tabs',
        width:600,
        autoHeight:true,
        activeTab: parseInt(selectedTab),
        frame:false,

        defaults:{autoHeight: true},
        items:[
            {contentEl:'filetype', title: 'File Type'}
        ]
    });
}

var showFileType = function(fileTypeData) {
    
    // create the data store
    var store = new Ext.data.ArrayStore({
        fields: [{
            name: 'filename'
        },{
            name: 'filetype'
        },
        {
            name: 'delete'
        }],
        sortInfo: {
            field: 'filetype',
            direction: 'ASC'
        }
    });

    store.loadData(fileTypeData);

    // create the Grid
    var grid = new Ext.grid.GridPanel({
        store: store,
        columns: [{
            id:'filename',
            header: "File Name",
            sortable: true,
            dataIndex: 'filename'
        },{
            id:'filetype',
            header: "File Type",
            sortable: true,
            dataIndex: 'filetype'
        },{
            header: "Delete",
            dataIndex: 'delete'
        }],
        sort: 'filetype',
        stripeRows: true,
        autoExpandColumn: 'filename',
        autoHeight: true,
        width: 400
    });
    
    grid.render('filetype');
}

var fileTypeAddForm = [{
    fieldLabel: 'File Type Description',
    name: 'filetypedesc',
    id: 'addfiletypedesc_title',
    allowBlank: false,
    width: 250
},{
    fieldLabel: 'File Type Extension',
    name: 'filetypeext',
    id: 'addfiletypeext_title',
    allowBlank: false,
    width: 250
}];

var goAddType = function() {
    

    var myForm = new Ext.FormPanel({
        standardSubmit: true,
        labelWidth: 125,
        url: typeURL,
        frame:true,
        bodyStyle:'padding:5px 5px 0',
        width: 350,
        defaults: {width: 230},
        defaultType: 'textfield',
        items: fileTypeAddForm
    });

    if(!win){
        win = new Ext.Window({
            applyTo:'addtype-win',
            layout:'fit',
            width: 500,
            height: 200,
            x: 100,
            y: 100,
            closeAction:'hide',
            plain: true,
            items: myForm,

            buttons: [{
                text: 'Add',
                handler: function(){
                    if (myForm.url)
                        myForm.getForm().getEl().dom.action = myForm.url;

                    myForm.getForm().submit();
                }
            },{
                text: 'Cancel',
                handler: function(){
                    win.hide();
                }
            }]
        });
    }
    win.show(this);
}

var goBack = function() {
    history.back(1);
}