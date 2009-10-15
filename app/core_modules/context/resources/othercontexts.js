/*
 * Ext JS Library 3.0 RC2
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 * By Paul Mungai
 * wandopm@gmail.com
 */

var selectedTab = "A";

var proxyContextStore = new Ext.data.HttpProxy({
            url:baseuri+'?module=context&action=jsonlistallcontext&filter=0&limit=10&offset=0'
        });
   
var othercontextdata = new Ext.data.JsonStore({

		root: 'courses',
        totalProperty: 'othercontextcount',
        idProperty: 'code',
        remoteSort: false, 
		baseParams: [{'letter':selectedTab}],       
        fields: ['code', 'coursecode', 'title', 'lecturertitle', 'lecturers', 'accesstitle','access' ],
        proxy:proxyContextStore,
        listeners:{ 
    		'loadexception': function(theO, theN, response){
    			//alert(response.responseText);
    		},
			'beforeload': function(thisstore, options){
    			thisstore.setBaseParam('letter', selectedTab);
    		},
    		'load': function(){
    				//alert('load');
					}
				},
					
    			});



	 othercontextdata.setDefaultSort('title', 'desc');
	 
    // pluggable renders
    function renderTitle(value, p, record){
        return String.format(
        		'<b><a href="'+baseuri+'?module=context&action=joincontext&contextcode={1}">{0}</a></b>', value, record.data.code);
    }

    var othergrid = new Ext.grid.GridPanel({
        el:'courses-grid',
        width:700,
        height:350,
        title:'Courses',
        store: othercontextdata,
        trackMouseOver:false,
        disableSelection:true,
        loadMask: true,

        // grid columns
        columns:[
        {
            header: "Course Code",
            dataIndex: 'code',
            width: 100,            
            sortable: true
        },{
            id: 'code', // id assigned so we can apply custom css (e.g. .x-grid-col-topic b { color:#333 })
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


		

        // paging bar on the bottom
        bbar: new Ext.PagingToolbar({
            pageSize: 500,
            store: othercontextdata,
            displayInfo: true,
            displayMsg: 'Displaying courses {0} - {1} of {2}',
            emptyMsg: "No courses to display",
            items:[
                '-', {
                pressed: false,
                enableToggle:true,
                text: 'Show Access Details',
                cls: 'x-btn-text-icon details',
                toggleHandler: function(btn, pressed){
                    var view = othergrid.getView();
                    view.showPreview = pressed;
                    view.refresh();
                }
            }]
        })
    });
	//alphabet tabs
		var alphaTab = new Ext.TabPanel({
			//plain:true,
			region: 'north',
			id:'mainTabPanel',
			width: 700,
			defaults:{autoHeight: true},
			//height: 20,
			margins: '10 10 10 10',
			split:true,
			loadMask: true,
			activeTab: selectedTab,
			enableTabScroll:true,
			tabPosition:'top',
		   // renderTo:'alphabet',
			listeners: {
				        'render': function(tabPanel){                    
				            //loadGroups(tabPanel)
				        }, 
				        'tabchange': function(tabPanel , tab){
				        	//load the data for the selected tab
				        	selectedTab = tab.id;
				        	loadContexts(tabPanel, tab);                	
				        }
				        
				    },
			items:[ {title:'0',id:'0'},{title:'1',id:'1'},{title:'2',id:'2'},
					{title:'3',id:'3'},{title:'4',id:'4'},{title:'5',id:'5'},{title:'6',id:'6'},{title:'7',id:'7'},{title:'8',id:'8'},
					{title:'9',id:'9'},
					{title:'A',id:'A'},{title:'B',id:'B'},{title:'C',id:'C'},{title:'D',id:'D'},{title:'E',id:'E'},{title:'F',id:'F'},
					{title:'G',id:'G'},{title:'H',id:'H'},{title:'I',id:'I'},{title:'J',id:'J'},{title:'K',id:'K'},{title:'L',id:'L'},
					{title:'M',id:'M'},{title:'N',id:'N'},{title:'O',id:'O'},{title:'P',id:'P'},{title:'Q',id:'Q'},
					{title:'R',id:'R'},{title:'S',id:'S'},{title:'T',id:'T'},{title:'U',id:'U'},{title:'V',id:'V'},{title:'W',id:'W'},
					{title:'X',id:'X'},	{title:'Y',id:'Y'},{title:'Z',id:'Z'}
	
			]
	
	
		});

	var myBorderPanel = new Ext.Panel({
    //renderTo: document.body,
    width: 700,
	//defaults:{autoHeight: true},
    height: 400,
    margins: '10 10 10 10',
    padding: '10 10 10 10',
    title: 'Other Courses',
    layout: 'border',
    items: [othergrid, alphaTab]
	});

function loadContexts(tabPanel, tab){
	//load the groups
	myBorderPanel.setTitle('Courses starting with \''+tab.id+'\'');
	//groupsGrid.render('main-interface');
	
	othercontextdata.load({params:{limit:10, offset:0, letter: tab.id}});	
}


Ext.onReady(function(){
    // render it
    othergrid.render();

    // trigger the data store load
    othercontextdata.load({params:{start:0, limit:500}});
});
