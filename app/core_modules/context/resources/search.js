/*
 * Ext JS Library 3.0 RC2
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */

Ext.onReady(function(){
var pageSize = 25;
   
var contextdata = new Ext.data.JsonStore({
        root: 'courses',
        totalProperty: 'totalCount',
        idProperty: 'contextcode',
        remoteSort: false,        
        fields: ['contextcode', 'title', 'author', 'datecreated', 'lastupdated','excerpt' ],
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
	 contextdata.setDefaultSort('lastupdated', 'desc');
	 
    // pluggable renders
    function renderTopic(value, p, record){
        return String.format(
        		'<b><a href="'+baseuri+'?module=context&action=joincontext&contextcode={1}">{0}</a></b>', value, record.data.contextcode);
                /*'<b><a href="http://localhost/eteach/index.php?module=context&action=joincontext&contextcode={2}" target="_blank">{0}</a></b>
                <a href="http://extjs.com/forum/forumdisplay.php?f={3}" target="_blank">{1} Forum</a>',
                value, record.data.title, record.id, record.data.contextcode);*/
    }
    function renderLast(value, p, r){
        return String.format('{0}<br/>by {1}', value.dateFormat('M j, Y, g:i a'), r.data['lastposter']);
    }

    var grid = new Ext.grid.GridPanel({
        el:'topic-grid',
        width:700,
        height:400,
        title:'Browse Courses',
        store: contextdata,
        trackMouseOver:false,
        disableSelection:true,
        loadMask: true,
        autoScroll:true,

        // grid columns
        columns:[
        {
            header: "Code",
            dataIndex: 'contextcode',
            width: 100,            
            sortable: true
        },{
            id: 'topic', // id assigned so we can apply custom css (e.g. .x-grid-col-topic b { color:#333 })
            header: "Course",
            dataIndex: 'title',
            width: 220,
            renderer: renderTopic,
            sortable: true
        },{
            header: "Creator",
            dataIndex: 'author',
            width: 100,
            hidden: false,
            sortable: true
        },{
            header: "Date Created",
            dataIndex: 'datecreated',
            width: 70,
            align: 'right',
            sortable: true
        },{
            header: "Last Updated",
            dataIndex: 'lastupdated',
            width: 100,
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
                    p.body = '<p>'+record.data.excerpt+'</p>';
                    return 'x-grid3-row-expanded';
                }
                return 'x-grid3-row-collapsed';
            }
        },

        // paging bar on the bottom
        bbar: new Ext.PagingToolbar({
            pageSize: pageSize,
            store: contextdata,
            displayInfo: true,
            displayMsg: 'Displaying courses {0} - {1} of {2}',
            emptyMsg: "No courses to display",
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
    grid.render();

    // trigger the data store load
    contextdata.load({params:{start:0, limit:pageSize}});
});
