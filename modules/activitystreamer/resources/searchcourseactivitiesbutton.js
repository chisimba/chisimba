/*
 * Ext JS Library 3.0 RC2
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */

var mycontextdata = new Ext.data.JsonStore({
        root: 'activities',
        totalProperty: 'totalCount',
        idProperty: 'id',
        remoteSort: false,        
        fields: ['id','contextcode', 'title', 'contextpath', 'datecreated', 'createdby'],
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
    	},
	});
	 mycontextdata.setDefaultSort('datecreated', 'desc');
	 
    // pluggable renders
    function renderTopic(value, p, record){
        return String.format(
        		'{0}', value, record.data.contextcode);
    }
    function renderLast(value, p, r){
        return String.format('{0}<br/>by {1}', value.dateFormat('M j, Y, g:i a'), r.data['lastposter']);
    }
    function renderItemPath(value, p, record){
        return String.format('<a href="'+baseuri+'?{1}"><b>{0}</b></a>', record.data.title, record.data.contextpath, record.data.contextcode);
    }
    var grid = new Ext.grid.GridPanel({
        //el:'courseactivities-topic-grid',
        width:700,
        height:200,
        title:'List of course pages from the most recently added/updated downwards',
        store: mycontextdata,
        trackMouseOver:false,
        disableSelection:true,
        loadMask: true,

        // grid columns
        columns:[
        {
            id: 'topic', // id assigned so we can apply custom css (e.g. .x-grid-col-topic b { color:#333 })
            header: "Title",
            dataIndex: 'title',
            width: 460,
            renderer: renderItemPath,            
            hidden: false,
            sortable: true
        },{
            header: "By",
            dataIndex: 'createdby',
            width: 120,
            hidden: false,
            sortable: true
        },{
            header: "Date",
            dataIndex: 'datecreated',
            width: 120,
            align: 'right',
            sortable: true
        }],

        // customize view config
        viewConfig: {
            forceFit:true,
            enableRowBody:true,
            showPreview:false,
            getRowClass : function(record, rowIndex, p, store){
                if(this.showPreview){
                    p.body = '<p>'+record.data.title+'</p>';
                    return 'x-grid3-row-expanded';
                }
                return 'x-grid3-row-collapsed';
            }
        },

        // paging bar on the bottom
        bbar: new Ext.PagingToolbar({
            pageSize: 5,
            store: mycontextdata,
            displayInfo: true,
            displayMsg: 'Displaying pages {0} - {1} of {2}',
            emptyMsg: "No pages to display",
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
    });

    // render it
    //grid.render();

    // trigger the data store load
    //mycontextdata.load({params:{start:0, limit:5}});
Ext.onReady(function(){
	 var win;
    var button = Ext.get('course-activities-btn');

    button.on('click', function(){
        // create the window on the first click and reuse on subsequent clicks
        if(!win){
            win = new Ext.Window({
                applyTo:'course-activities-view',
                layout:'fit',
                width:800,
                height:320,
                autoHeight:true,
                closeAction:'hide',
                plain: true,
		items:[grid],
                buttons: [
                 {
                  text: 'Close',
                  handler: function(){
                  win.hide();
                 }
                }]
            });
            mycontextdata.load({params:{start:0, limit:5}})
        }
        win.show(this);
    });
});
