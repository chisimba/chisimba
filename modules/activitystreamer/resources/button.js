/*
 * Ext JS Library 3.0 RC2
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */

var contextdata = new Ext.data.JsonStore({
        root: 'activities',
        totalProperty: 'totalCount',
        idProperty: 'id',
        remoteSort: false,        
        fields: ['id','contextcode', 'title', 'description', 'createdon', 'createdby','module' ],
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
	 contextdata.setDefaultSort('createdon', 'desc');
	 
    // pluggable renders
    function renderTopic(value, p, record){
        return String.format(
        		'{0}', value, record.data.contextcode);
    }
    function renderLast(value, p, r){
        return String.format('{0}<br/>by {1}', value.dateFormat('M j, Y, g:i a'), r.data['lastposter']);
    }
    function renderTitledescription(value, p, record){
        return String.format('<b>{1}</b>',value, record.data.title, record.data.description);
    }
    var grid = new Ext.grid.GridPanel({
//        el:'activity-topic-grid',
        width:770,
        height:200,
        title:'List of Activities',
        store: contextdata,
        trackMouseOver:false,
        disableSelection:true,
        loadMask: true,

        // grid columns
        columns:[
        {
            id: 'topic', // id assigned so we can apply custom css (e.g. .x-grid-col-topic b { color:#333 })
            header: "Activity",
            dataIndex: 'title',
            width: 470,
            renderer: renderTitledescription,            
            sortable: true
        },/*
        ,{
            header: "Description",
            dataIndex: 'description',
            width: 220,
            //renderer: renderTopic,
            sortable: true
        },*/
        {
            header: "Course Code",
            dataIndex: 'contextcode',
            width: 100,
            hidden: false,
            sortable: true
        },{
            header: "Date Created",
            dataIndex: 'createdon',
            width: 120,
            align: 'right',
            sortable: true
        },{
            header: "Created By",
            dataIndex: 'createdby',
            width: 150,
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
                    p.body = '<p>'+record.data.description+'</p>';
                    return 'x-grid3-row-expanded';
                }
                return 'x-grid3-row-collapsed';
            }
        },

        // paging bar on the bottom
        bbar: new Ext.PagingToolbar({
            pageSize: 5,
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
    });

    // render it
    //grid.render();

    // trigger the data store load
    //contextdata.load({params:{start:0, limit:5}});

Ext.onReady(function(){
	 var win;
    var button = Ext.get('show-btn-siteupdates');

    button.on('click', function(){
        // create the window on the first click and reuse on subsequent clicks
        if(!win){
            win = new Ext.Window({
                applyTo:'hello-win-siteupdates',
                layout:'fit',
                width:800,
                height:320,
                autoHeight:true,
                closeAction:'hide',
                plain: true,

                /*items: new Ext.TabPanel({
                    applyTo: 'hello-tabs',
                    autoTabs:true,
                    activeTab:0,
                    deferredRender:false,
                    border:false
                }),*/
                
																items:[grid],
                buttons: [/*{
                    text:'Submit',
                    disabled:true
                },*/{
                    text: 'Close',
                    handler: function(){
                        win.hide();
                    }
                }]
            });
            contextdata.load({params:{start:0, limit:5}})
        }
        win.show(this);
    });
});
