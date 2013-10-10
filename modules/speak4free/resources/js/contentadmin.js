/*!
 * Ext JS Library 3.0.0
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
Ext.onReady(function(){
    ButtonPanel = Ext.extend(Ext.Panel, {

        layout:'table',
        defaultType: 'button',
        baseCls: 'x-plain',
        cls: 'btn-panel',
        menu: undefined,
        split: true,
        bodyStyle:'margin-top:2em;margin-bottom:2em;',
        constructor: function(buttons){
            for(var i = 0, b; b = buttons[i]; i++){
                b.menu = this.menu;
                b.enableToggle = this.enableToggle;
                b.split = this.split;
                b.arrowAlign = this.arrowAlign;
            }
            var items = buttons;

            ButtonPanel.superclass.constructor.call(this, {
                items: items
            });
        }
    });
    ButtonPanel.override({
     renderTo : 'sections'
      });

 var buttons= new ButtonPanel(

        [{
            iconCls: 'commentadd',
            text:'Add content',

            handler: function(){
                showAddCommentsWin(phaseTitle, commentsSaveUrl);
            }
        }
        ]
        );

    var tree = new Ext.ux.tree.ColumnTree({
        width: 650,
        height: 300,
        rootVisible:false,
        autoScroll:true,
        title: 'Topics',
       // applyTo: 'sections',
        columns:[{
            header:'Topic',
            width:330,
            dataIndex:'topics'
        },{
            header:'Owner',
            width:100,
            dataIndex:'owner'
        }],

        loader: new Ext.tree.TreeLoader({
            dataUrl:window.location.href+'?module=speak4free&action=viewsections',
            uiProviders:{
                'col': Ext.ux.tree.ColumnNodeUI
            }
        }),

        root: new Ext.tree.AsyncTreeNode({
            text:'Topics'
        })
    });

    var sectionsform = new Ext.form.FormPanel({

        baseCls: 'x-plain',
        width:750,
        applyTo: 'sections',
        items:[
            buttons,
            tree
            ]
       
    });

});