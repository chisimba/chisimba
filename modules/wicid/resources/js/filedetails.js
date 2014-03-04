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
        }]
    });
    p.render("buttons");
}

function showSummary(data) {
    var summaryform = new Ext.form.FormPanel({
        baseCls: 'x-plain',
        width:750,
        labelWidth: 120,
        bodyStyle:'padding:5px 5px 0;',
        renderTo: 'summary',
        collapsible: true,
        defaults: {width: 400},
        items: {
            xtype: 'fieldset',
            title: 'File Details',
            autoHeight: true,
            items:[
                new Ext.form.DisplayField({
                fieldLabel: 'Owner',
                value: data[0]
                }),
                new Ext.form.DisplayField({
                  fieldLabel: 'File Name' ,
                  value: data[1]
                }),
                new Ext.form.DisplayField({
                fieldLabel: 'File Type',
                value: data[2]
                }),
                new Ext.form.DisplayField({
                fieldLabel: 'Date Uploaded',
                value: data[3]
                }),
                new Ext.form.DisplayField({
                fieldLabel: 'Permissions',
                value: data[4]
                })
            ]
         }
    });
}

var goBack = function() {
    history.back(1);
}

