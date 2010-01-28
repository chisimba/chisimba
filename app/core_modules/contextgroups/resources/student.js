 var studentdata = new Ext.data.JsonStore({
        root: 'lecturers',
        totalProperty: 'totalCount',
        idProperty: 'username',
        remoteSort: false,        
        //fields: ['code', 'coursecode', 'title', 'lecturertitle', 'lecturers', 'accesstitle','access' ],
        fields: ['username', 'firstname', 'surname', 'email', 'isactive', 'staffnumber' ],
        //fields: ['firstname' ],
        proxy: new Ext.data.HttpProxy({        	 	
            	url: baseUri+"?module=contextgroups&action=json_getstudents"
        }),
        listeners:{ 
    		'loadexception': function(theO, theN, response){
    		    alert(baseUri);
    			alert(response.responseText);
    		},
    		'load': function(){
    			//	alert('load');	
    			}
    	}
	});
	 studentdata.setDefaultSort('surname', 'asc');
	 
   
function renderIsActive(value, p, record)
	{
	    var isActive = record.data.isactive;
	    var img = 'accept'
	    if (isActive != 1){
	           img = 'decline';
	    }
	    
	    return '<img src="skins/_common2/css/images/sexybuttons/icons/silk/'+img+'.png" border="0" />';
		//return String.format('<img src="skins/_common2/css/images/sexybuttons/icons/silk/'+img+'.png" border="0" />',record.data.id);
	}
	
	
//student grid
var studentgrid = new Ext.grid.GridPanel({
        //el:'courses-grid',
        width:"100%",
        height:400,

       // title:'My Courses',
        store: studentdata,
        trackMouseOver:false,
        disableSelection:true,
        loadMask: true,
        emptyText:"get out",
        // grid columns
        columns:[
        {
            header: 'Student Number',
            dataIndex: 'staffnumber',
            id:'firstname',
            width: 50,
            sortable: true
        },
        {
            header: 'FirstName',
            dataIndex: 'firstname',
            id:'firstname',
            width: 50,
            sortable: true
        },{
            id: 'surname', // id assigned so we can apply custom css (e.g. .x-grid-col-topic b { color:#333 })
            header: "Surname",
            dataIndex: 'surname',
            width: 50,
            //renderer: renderTitle,
            sortable: true
        },{
            header: 'Is Active',
            dataIndex: 'isactive',
            renderer:renderIsActive,
            id:'isactive',
            width: 30,
            hidden: false,
            sortable: false
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
       /* bbar: new Ext.PagingToolbar({
            pageSize: 500,
            store: studentdata,
            displayInfo: true,
            displayMsg: 'Displaying '+lang['lecturers']+' {0} - {1} of {2}',
            emptyMsg: "No "+lang['lecturers']+" to display",
            /*items:[
                '-', {
                pressed: false,
                enableToggle:true,
                text: 'Show Access Details',
                cls: 'x-btn-text-icon details',
                toggleHandler: function(btn, pressed){
                    var view = usergrid.getView();
                    view.showPreview = pressed;
                    view.refresh();
                }
            }]
        })*/
    });