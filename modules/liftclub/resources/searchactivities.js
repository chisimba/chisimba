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
var liftdata = new Ext.data.JsonStore({
        root: 'searchresults',
        totalProperty: 'activitycount',
        idProperty: 'id',
        remoteSort: false,        
        fields: ['id', 'module', 'title', 'description', 'createdon','createdby', 'link'],
        proxy: new Ext.data.HttpProxy({ 
            	url: uri
        }),
        listeners:{ 
    		'loadexception': function(theO, theN, response){
    			//alert(response.responseText);
    		},
    		'load': function(){
    				//alert('load');	
    			}
    	}
	});
	liftdata.setDefaultSort('createdon', 'desc');
 // pluggable renders
 function renderTitle(value, p, record){
     if(record.data.link == null ){
     return String.format('<b>{0}</b>', record.data.title, record.data.link);
     }else{
     return String.format('<b><a href="'+baseuri+'?{1}">{0}</a></b>', record.data.title, record.data.link);
     }		
 }
    var grid = new Ext.grid.GridPanel({
        el:'activities-grid',
        width:700,
        height:400,
        title:liftitle,
        store: liftdata,
        trackMouseOver:false,
        disableSelection:true,
        loadMask: true,

        // grid columns
        columns:[
        {
            id: 'id',
            header: lang["description"],
            dataIndex: 'id',
            width: 550,
            renderer: renderTitle,            
            hidden: false,
            sortable: true
        },{
            header: lang["datecreated"],
            dataIndex: 'createdon',
            width: 150,
            hidden: false,
            sortable: true
        }],

        // customize view config
        viewConfig: {
            forceFit:true,
            enableRowBody:true,
            showPreview:false,
            getRowClass : function(record, rowIndex, p, store){
                if(this.showPreview){
                    //return 'x-grid3-row-expanded';
                }
                return 'x-grid3-row-collapsed';
            }
        },
								plugins:[new Ext.ux.grid.Search({
											iconCls:'zoom'
											,disableIndexes:['id','createdby']
											,minChars:1
											,autoFocus:true
									})],
        // paging bar on the bottom
        bbar: new Ext.PagingToolbar({
            pageSize: 5,
            store: liftdata,
            displayInfo: true,
            displayMsg: lang["displayingpage"]+' {0} - {1} '+lang["wordof"]+' {2}',
            emptyMsg: lang["noliftsclubactivities"],
            items:[
                /*'-', {
                pressed: false,
                enableToggle:true,
                text: 'Show/Hide Details',
                cls: 'x-btn-text-icon details',
                toggleHandler: function(btn, pressed){
                    var view = grid.getView();
                    view.showPreview = pressed;
                    view.refresh();
                }
            }, {
              xtype:'checkbox'
              ,boxLabel:'Lifts Offered '
              ,checked:true
            }, {
              xtype:'checkbox'
              ,boxLabel:'Lifts Wanted'
              ,checked:false
            }*/]
        })
    });
    // render it
    grid.render();

    // trigger the data store load
    liftdata.load({params:{start:0, limit:5}});
	
});
