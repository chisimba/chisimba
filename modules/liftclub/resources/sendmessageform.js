/*!
 * Ext JS Library 3.0.0
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */

Ext.onReady(function(){

    Ext.QuickTips.init();

    // turn on validation errors beside the field globally
    Ext.form.Field.prototype.msgTarget = 'side';

    var sendmsgFormPanel = new Ext.FormPanel({
        labelWidth: 75, // label settings here cascade unless overridden
        url:baseUri+'?module=liftclub&action=sendmessage',
        frame:true,
        title: lang["sendmsgform"],
        bodyStyle:'padding:5px 5px 0',
        width: 350,
        defaults: {width: 230},
        defaultType: 'textfield',
        items: [{
                fieldLabel: lang["title"],
                name: 'msgtitle',
                allowBlank:false
            },{
                fieldLabel: lang["message"],
                name: 'msgbody',
                allowBlank:false
            }
        ],
        buttons: [{
            text: lang["wordsave"]
        },{
            text: lang["wordcancel"]
        }]
    });


    sendmsgFormPanel.render(document.body);
});

