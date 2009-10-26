/*
 * Ext JS Library 3.0 RC2
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 * By Paul Mungai
 * Searching Plugin: Qhamani Fenama
 * wandopm@gmail.com
 */

var proxyContextStore = new Ext.data.HttpProxy({
            url:baseuri+'?module=context&action=jsongetcontexts&limt=25&start=0'
        });

   
var othercontextdata = new Ext.data.JsonStore({
        root: 'courses',
        totalProperty: 'othercontextcount',
        idProperty: 'code',
        remoteSort: false,        
        fields: ['code', 'contextcode', 'title', 'lecturertitle', 'lecturers', 'accesstitle','access' ],
        proxy:proxyContextStore,
        listeners:{ 
    		'loadexception': function(theO, theN, response){
    			//alert(response.responseText);
    		},
    		'load': function(){
    				//alert('load');	
    			}
    	}
	});
	 othercontextdata.setDefaultSort('title', 'asc');
	 
    // pluggable renders
    function renderTitle(value, p, record){
        return String.format(
        		'<b><a href="'+baseuri+'?module=context&action=joincontext&contextcode={1}">{0}</a></b>', value, record.data.code);
    }

    var othergrid = new Ext.grid.GridPanel({
        //el:'courses-grid',
        width:540,
        height:400,
       // title:'My Courses',
        store: othercontextdata,
        trackMouseOver:false,
        disableSelection:true,
        loadMask: true,
		emptyText:'No Courses Found',
		
        // grid columns
        columns:[
        {
            header: "Course Code",
            dataIndex: 'contextcode',
            width: 100,            
            sortable: true
        },{
            //id: 'code', // id assigned so we can apply custom css (e.g. .x-grid-col-topic b { color:#333 })
            header: "Title",
            dataIndex: 'title',
            width: 320,
            renderer: renderTitle,
            sortable: true
        },{
            header: "Lecturers",
            dataIndex: 'lecturers',
            width: 280,
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
                    p.body = '<p><b>'+record.data.accesstitle+' </b></p><p>'+record.data.access+'</p>';
                    return 'x-grid3-row-expanded';
                }
                return 'x-grid3-row-collapsed';
            }
        },
		    	
		plugins:[new Ext.ux.grid.Search({
				 iconCls:'zoom'
				 //,readonlyIndexes:['lecturers']
				 ,disableIndexes:['lecturers']
				 ,minChars:1
				 ,autoFocus:true
				 // ,menuStyle:'radio'
		 })],

        // paging bar on the bottom
        bbar: new Ext.PagingToolbar({
            pageSize: 500,
            store: othercontextdata,
            displayInfo: true,
            displayMsg: 'Courses {0} - {1} of {2}',
            emptyMsg: "No courses to display"
            
        })
    });
    /*
Ext.onReady(function(){
    // render it
    usergrid.render();

    // trigger the data store load
    usercontextdata.load({params:{start:0, limit:500}});
});*/
