/*
 * Ext JS Library 3.0 RC2
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 * By Paul Mungai
 * Searching Plugin: Paul Mungai
 * wandopm@gmail.com
 */
Ext.onReady(function(){
var logdata = new Ext.data.JsonStore({
        root: 'contextlogs',
        totalProperty: 'logcount',
        idProperty: 'id',
        remoteSort: false,        
        fields: ['id', 'userid', 'usernames', 'contextcode', 'modulecode','contextitemid', 'pageorchapter', 'contextitemtitle', 'datecreated','description', 'starttime', 'endtime'],
        proxy: new Ext.data.HttpProxy({ 
            	url: uri
        }),
        listeners:{ 
    	 'loadexception': function(theO, theN, response){
    	 },
    	 'load': function(){
    	  //alert('load');	
    	 }
    	}
	});
	logdata.setDefaultSort('datecreated','asc');
    var grid = new Ext.grid.GridPanel({
        el:'lc-grid',
        width:'100%',
        height:400,
        title:title,
        store: logdata,
        trackMouseOver:false,
        disableSelection:true,
        loadMask: true,
        columns:[
        {
            id: 'id',
            header: lang['usernames'],
            dataIndex: 'usernames',
            width: 135,
            hidden: false,
            sortable: true
        },{
            header: lang["pagetitle"],
            dataIndex: 'contextitemtitle',
            width: 300,
            hidden: false,
            sortable: true
        },{
            header: lang["type"],
            dataIndex: 'pageorchapter',
            width: 60,
            hidden: false,
            sortable: true
        },{
            header: lang["startime"],
            dataIndex: 'starttime',
            width: 120,
            hidden: false,
            sortable: true
        },{
            header: lang["endtime"],
            dataIndex: 'endtime',
            width: 120,
            hidden: false,
            sortable: true
        }],
        // customize view config
        viewConfig: {
            forceFit:true,
            enableRowBody:true,
            showPreview:false,
            getRowClass : function(record, rowIndex, p, store){
                return 'x-grid3-row-collapsed';
            }
        },
	plugins:[new Ext.ux.grid.Search({
	 iconCls:'zoom'
	 ,disableIndexes:['id']
	 ,minChars:1
	 ,autoFocus:true
	})],
        bbar: new Ext.PagingToolbar({
            pageSize: pageSize,
            store: logdata,
            displayInfo: true,
            displayMsg: lang["displayingpage"]+' {0} '+lang["wordof"]+' {1}',
            emptyMsg: lang["nologstodisplay"]
        })
    });
    // render it
    grid.render();

    // trigger the data store load
    logdata.load({params:{start:0, limit:pageSize}});
});
