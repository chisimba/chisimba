/*
 * Ext JS Library 3.0 RC2
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */
Ext.onReady(function(){

    Ext.QuickTips.init();

    var xg = Ext.grid;

    // reader
    var contextdata = new Ext.data.JsonStore({
        root: 'activities',
        totalProperty: 'totalCount',
        idProperty: 'contextcode',
        remoteSort: false,        
        fields: ['contextcode', 'title', 'description', 'createdon', 'createdby','module' ],
        proxy: new Ext.data.HttpProxy({ 
            	url: uri
        })
	   });
	   contextdata.setDefaultSort('createdon', 'desc');
    // row expander
    var expander = new Ext.ux.grid.RowExpander({
        tpl : new Ext.Template(
            '<p><b>Title:</b> {title}</p><br>',
            '<p><b>Description:</b> {description}</p>'
            '<p><b>Course Code:</b> {contextcode}</p>'
        )
    });

    var grid = new xg.GridPanel({
        columnLines: true,
        width: 600,
        height: 300,
        plugins: expander,
        collapsible: true,
        animCollapse: false,
        el:'topic-grid',
        title: 'List of Activities',
        store: contextdata,        
        cm: new xg.ColumnModel({
            defaults: {
                width: 20,
                sortable: true
            },
            columns: [
                expander,
                {id:'title',header: "Title", width: 40, dataIndex: 'title'},
                {header: "Date Created", dataIndex: 'createdon'},
                {header: "Post Date", renderer: Ext.util.Format.dateRenderer('m/d/Y'), dataIndex: 'createdon'},
                {header: "Posted By", dataIndex: 'createdby'}
            ]
        }),
        viewConfig: {
            forceFit:true
            enableRowBody:true,
            showPreview:false,
            getRowClass : function(record, rowIndex, p, store){
                if(this.showPreview){
                    p.body = '<p>'+record.data.excerpt+'</p>';
                    return 'x-grid3-row-expanded';
                }
                return 'x-grid3-row-collapsed';
            }
        }
        //iconCls: 'icon-grid',
        //renderTo: document.body
    });
        // paging bar on the bottom
        bbar: new Ext.PagingToolbar({
            pageSize: 10,
            store: contextdata,
            displayInfo: true,
            displayMsg: 'Displaying topics {0} - {1} of {2}',
            emptyMsg: "No topics to display",
            items:[
                '-', {
                pressed: false,
                enableToggle:true,
                text: 'Show About',
                cls: 'x-btn-text-icon details',
                toggleHandler: function(btn, pressed){
                    var view = grid.getView();
                    view.showPreview = pressed;
                    view.refresh();
                }
            }]
        })
    // render it
    grid.render();

    // trigger the data store load
    contextdata.load({params:{start:0, limit:10}});
});
