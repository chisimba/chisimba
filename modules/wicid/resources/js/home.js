var currentPath,
createFolderUrl,
renameFolderUrl,
deleteFolderUrl,
uploadUrl,
settingsUrl,
uploadWindow,
filesUrl;
var conn = new Ext.data.Connection();
var filesstore;
var filesPanel;

function showHome(dataurl,
    xcreateFolderUrl,
    xrenameFolderUrl,
    xdeleteFolderUrl,
    xuploadUrl,
    xsettingsUrl,
    xfileUrl,
    modPath){
    createFolderUrl=xcreateFolderUrl;
    renameFolderUrl = xrenameFolderUrl;
    deleteFolderUrl = xdeleteFolderUrl;
    uploadUrl = xuploadUrl;
    settingsUrl = xsettingsUrl;
    filesUrl=xfileUrl;
    var Tree = Ext.tree;
  
    var tpl = new Ext.XTemplate(
        '<tpl for=".">',

        '<div class="thumb-wrap" id="{text}">',
        '<div class="thumb"><img src="'+modPath+'/{ext}.png" title="{text}"></div>',
        '<span class="x-editable">{shortName}</span></div>',
        '</tpl>',
        '<div class="x-clear"></div>'
        );
    var tb = new Ext.Toolbar();
    tb.render('toolbar');
    tb.add({
        text:'Upload',
        iconCls: 'upload',
        handler: function() {
            showUploadForm();
        }

    });

    tb.add({
        text:'Settings',
        iconCls: 'settings',
        handler: function() {
            goSettings();
        }

    });

    tb.add({
        text:'Help',
        iconCls: 'helpimg',
        handler: function() {
            alert('Help!!');
        }
           
    });

    tb.doLayout();

   
    var tree = new Tree.TreePanel({
        useArrows: true,
        autoScroll: true,
        animate: true,
        enableDD: false,
        containerScroll: true,
        border: false,
        // auto create TreeLoader
        dataUrl: dataurl,
        listeners: {
            'render': function(tp){
                tp.getSelectionModel().on('selectionchange', function(tree, node){
                    currentPath=node.attributes.parent;
                    if(currentPath == null){
                        currentPath="/";
                    }else{
                        currentPath+="/"+node.text;
                    }
                    filesstore = new Ext.data.JsonStore({
                        url:filesUrl+"&node="+currentPath,
                        root: 'files',
                        fields: ['text', 'id','ext',
                        {
                            name:'size',
                            type: 'float'
                        }, {
                            name:'lastmodified',
                            type:'date',
                            dateFormat:'timestamp'
                        }
                        ]
                    });
                    filesstore.load();

                    filesPanel.bindStore(filesstore);
                    filesPanel.refresh();
                   
                })
            }

        },

        root: {
            nodeType: 'async',
            text: 'Folders/Files',
            draggable: false,
            id: '/'
        }
    });


    
    tree.on('contextmenu', function(node){
       
        if(node && node.leaf){
        //do nothing
        }else{
            //Set up some buttons
            var createFolder = new Ext.menu.Item({
                text: "New Folder",
                iconCls: 'folderadd',
                handler: function() {
                    var name = prompt( "Please enter folder name:");
                    if (name != '' && name != null) {
                        window.location.href=createFolderUrl+"&foldername="+name+"&folderpath="+currentPath;
                    }
                }
            });
            var renameFolder = new Ext.menu.Item({
                text: "Rename",
                handler: function() {
                    var name = prompt( "Please enter folder name:");
                    if (name != '' && name != null) {
                        window.location.href=renameFolderUrl+"&foldername="+name+"&folderpath="+currentPath;
                    }
                }
            });
            var deleteFolder = new Ext.menu.Item({
                text: "Delete",
                iconCls: 'delete',
                handler: function() {
                    Ext.Msg.show({
                        title:'Delete Folder?',
                        msg: 'Are you sure you want to delete this folder?',
                        buttons: Ext.Msg.YESNO,
                        fn: deletefolder
                    });
                }
            });
            //Create the context menu to hold the buttons
            var contextMenu = new Ext.menu.Menu();
            contextMenu.add(createFolder, renameFolder,deleteFolder);
            //Show the menu
            contextMenu.show(node.ui.getAnchor());
        }
    });
    tree.getRootNode().expand();
    filesPanel=new Ext.DataView({
        store: filesstore,
        tpl: tpl,
        region: 'center',
        margins:'3 3 3 0',
        id:'images-view',
        autoHeight:true,
        multiSelect: true,
        overClass:'x-view-over',
        itemSelector:'div.thumb-wrap',
        emptyText: 'No files to display',
        bodyStyle:"background:#ffffff;",
        plugins: [
        new Ext.DataView.DragSelector()
        ],
        
        prepareData: function(data){
            data.shortName = Ext.util.Format.ellipsis(data.text, 15);
            data.sizeString = Ext.util.Format.fileSize(data.size);
            data.dateString = data.lastmodified.format("m/d/Y g:i a");
            return data;
        },

        listeners: {
            selectionchange: {
                fn: function(dv,nodes){
                    var l = nodes.length;
                    var s = l != 1 ? 's' : '';
                    filesPanel.setTitle('Simple DataView ('+l+' item'+s+' selected)');
                }
            }
            ,
            contextmenu:{
                fn:function(evt,nodes){
                     evt.stopEvent();

                    //Set up some buttons
                    var createFolder = new Ext.menu.Item({
                        text: "Upload new file",
                        iconCls: 'upload',
                        handler: function() {
                            showUploadForm();
                        }
                    });
                    //Create the context menu to hold the buttons
                    var contextMenu = new Ext.menu.Menu();
                    contextMenu.add(createFolder);
                    //Show the menu
                    contextMenu.show(filesPanel.el);
                }
            }
        }
    });



    // Panel for the west
    var nav = new Ext.Panel({
        title: 'Navigation',
        region: 'west',
        split: true,
        width: 200,
        collapsible: true,
        margins:'3 0 3 3',
        cmargins:'3 3 3 3',
        items:[tree]
    });
    // buttons
    var p = new Ext.Panel({
        layout: 'border',
        autoWidth: true,
        style: 'marginRight: 10px',
        baseCls: 'x-plain',
        cls: 'btn-panel',
        border: false,
        height:600,
        plain:true,
        
        items: [nav,filesPanel]

    });
    p.render("mainContent");
}

function createFolder(){
    Ext.MessageBox.prompt('New folder', 'Please enter folder name', handleCreateFolder);

}

function handleCreateFolder(btn, text){
    if(btn == 'ok'){
        window.location.href=createFolderUrl+"&foldername="+text+"&folderpath="+currentPath;
    }
}

function deletefolder(btn, text) {
    if(btn == 'yes') {
        window.location.href=deleteFolderUrl+"&folderpath="+currentPath;
    }
}

function accessRights(){
    
}

function goSettings() {
    window.location.href = settingsUrl;
}

function showUploadForm(){
    var fibasic = new Ext.ux.form.FileUploadField({
        id: 'form-file',
        emptyText: 'Select a file',
        fieldLabel: 'File',
        name: 'filename',
        width: 300
    });
    var type= [
    ['Public'],
    ['Private']
    ];

    var typestore = new Ext.data.ArrayStore({
        fields: ['type'],
        data : type
    });

    var typefield = new Ext.form.ComboBox({
        store: typestore,
        displayField:'type',
        fieldLabel:'Access type:',
        typeAhead: true,
        mode: 'local',
        editable:false,
        forceSelection: true,
        triggerAction: 'all',
        emptyText:'Select access...',
        selectOnFocus:true,
        allowBlank:false,
        name : 'permissions'

    });

    var fp = new Ext.FormPanel({
        standardSubmit: true,
        url: uploadUrl,
        fileUpload: true,
        frame: true,
        title: 'File Upload Form',
        autoHeight: true,
        labelWidth: 80,
        items: [fibasic,typefield],
        buttons: [{
            text:'Upload',
            handler: function(){
                if(fp.getForm().isValid()){
                    if (fp.url) {
                        fp.getForm().getEl().dom.action = fp.url + '&path=' + currentPath;
                    }
                    fp.getForm().submit();
                }
            }
        },{
            text: 'Cancel',
            handler: function(){
                uploadWindow.hide();
            }
        }]
    })

    if(!uploadWindow){
        uploadWindow = new Ext.Window({
            applyTo:'upload-win',
            width:500,
            autoHeight: true,
            x:125,
            y:50,
            closeAction:'destroy',
            plain: true,
            labelWidth: 155,
            items: [
            fp
            ]
        });
    }
    uploadWindow.show(this);
}

function showContextDVMenu(view, index, node, e){

    //Set up some buttons
    var createFolder = new Ext.menu.Item({
        text: "Upload new file",
        iconCls: 'upload',
        handler: function() {
                    
        }
    });
    //Create the context menu to hold the buttons
    var contextMenu = new Ext.menu.Menu();
    contextMenu.add(createFolder);
    //Show the menu
    contextMenu.show(view.ui.getAnchor());
        
}

