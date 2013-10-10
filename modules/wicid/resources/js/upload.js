var showUploadForm = function(myUrl) {
    showButtons();

    var fp = new Ext.FormPanel({
        standardSubmit: true,
        url: myUrl,
        renderTo: 'fi-form',
        fileUpload: true,
        width: 500,
        frame: true,
        title: 'File Upload Form',
        autoHeight: true,
        bodyStyle: 'padding: 10px 10px 0 10px;',
        labelWidth: 80,
        defaults: {
            anchor: '95%',
            allowBlank: false,
            msgTarget: 'side'
        },
        items: [{
            xtype: 'fileuploadfield',
            id: 'form-file',
            emptyText: 'Select a file',
            fieldLabel: 'File',
            name: 'filename'
        },
        {
            xtype: 'radiogroup',
            fieldLabel: 'Permissions',
            items: [
                {boxLabel: 'Public', name: 'permissions', inputValue: 1, checked: true},
                {boxLabel: 'Private', name: 'permissions', inputValue: 2}
            ]
        }],
        buttons: [{
            text: 'Save',
            handler: function(){
                if(fp.getForm().isValid()){
	                if (fp.url) {
                            fp.getForm().getEl().dom.action = fp.url;
                        }
                        fp.getForm().submit();
                }
            }
        },{
            text: 'Cancel',
            handler: function(){
                goBack();
            }
        }]
    });
}

var showButtons = function() {
    var p = new Ext.Panel({
        layout: 'table',
        autoWidth: true,
        style: 'marginRight: 10px',
        baseCls: 'x-plain',
        cls: 'btn-panel',
        border: false,
        defaultType: 'button',
        id: 'upload-button',
        items: [{
            text: 'Back',
            scale: 'medium',
            baseCls: 'x-plain',
            cls: 'btn-panel',
            handler: function() {
                goBack();
            }
        }]
    });

    p.render("buttons");
}


var goBack = function() {
    history.back(1);
}