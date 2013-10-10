

var msg = function(title, msg){
        Ext.Msg.show({
            title: title,
            msg: msg,
            minWidth: 200,
            modal: true,
            icon: Ext.Msg.INFO,
            buttons: Ext.Msg.OK
        });
    };

/**
* The upload action
*/
/*
var action = new Ext.Action({
    text: 'Add Assessment',
    handler: function(){
        //Ext.example.msg('Click','You clicked on "Action 1".');
        if(!win){
            win = new Ext.Window({
                
                layout:'fit',
                width:500,
                height:400,
                closeAction:'hide',
                plain: true,

                items:[addForm],

                buttons: [submitbutton,
	                {
	                    text: 'Close',
	                    handler: function(){
	                        win.hide();
	                    }
	                }]
            });
        }
        win.show(this);
        },
    iconCls: 'blist'
});

/**
* The assignment data store used by the assGrid
*
*/
var assStore = new Ext.data.JsonStore({
        root: 'assignments',
        totalProperty: 'totalCount',
        idProperty: 'puid',
        //remoteSort: true,

        fields: [
            {name: 'title'},
            {name: 'duedate'},
            {name: 'score'},
            {name: 'assid'},
            {name: 'contextcode'},
            {name: 'objectid'},
            {name: 'instructoremail'},
        ],
        proxy: new Ext.data.HttpProxy({
            url: storeUri
        })
    });
    assStore.setDefaultSort('duedate', 'desc');
    
    
    
/**
* The assignment Grid/List
*/
var assGrid = new Ext.grid.GridPanel({
        width:700,
        height:200,
        title:'Assignments',
        store: assStore,
        trackMouseOver:true,
        disableSelection:true,
        loadMask: true,
        stripeRows: true,
		sm: new Ext.grid.RowSelectionModel({singleSelect:true}),
		region:'center',


        // grid columns
        columns:[new Ext.grid.RowNumberer(),{
            id: 'topic', // id assigned so we can apply custom css (e.g. .x-grid-col-topic b { color:#333 })
            header: "Topic",
            dataIndex: 'title',
            width: 420,
            //renderer: renderTopic,
            sortable: true
        },{
            header: "Score",
            dataIndex: 'score',
            renderer:renderScore,
            width: 120,
            align: 'center',
            sortable: true
        },{
            id: 'last',
            header: "Due Date",
            dataIndex: 'duedate',
            width: 150,
            //renderer: renderLast,
            sortable: true
        }],
		//tbar :[ but ],
        // customize view config
        viewConfig: {
            forceFit:true,
            enableRowBody:true,
            showPreview:false,
            getRowClass : function(record, rowIndex, p, store){
                if(this.showPreview){
                    p.body = '<p>'+record.data.instructions+'</p>';
                    return 'x-grid3-row-expanded';
                }
                return 'x-grid3-row-collapsed';
            }
        },
        // paging bar on the bottom
        bbar: new Ext.PagingToolbar({
            pageSize: 10,
            store: assStore,
            displayInfo: true,
            displayMsg: 'Displaying topics {0} - {1} of {2}',
            emptyMsg: "No topics to display",
            items:[
                '-', {
                pressed: false,
                enableToggle:true,
                text: 'Show Instructions',
                cls: 'x-btn-text-icon details',
                toggleHandler: function(btn, pressed){
                    var view = assGrid.getView();
                    view.showPreview = pressed;
                    view.refresh();
                }
            }]
        })

        
});


Ext.onReady(function(){
	
	Ext.QuickTips.init();

	//student must a list of assignments ordered by due date
	// this can be done by using a grid
	assGrid.render('topic-grid');
	
	// trigger the data store load
    assStore.load({params:{start:0, limit:10}});
    
    
	
});



 function renderScore(value, p, record){
    	var cid = 'green';
    	
    	var v; 
    	v = value;
    	
    	if (value > 20 && value < 40)
    	{
    		cid = 'yellow';
    	} else if (value >= 40) {
    		cid = 'red';
    	} else if (value < 1){
    		cid = 'pending';    		
    	}
    	if (value < 1)
    	{
    		value = '--&nbsp;';
    	} else {
    		value = value+'%';
    	}
    	
    	if(v == '')
    	{
    		
    		//var ret = String.format('<a href="#"  onClick="uploadFormPanel(\'{0}\', \'{1}\');" >Submit </a>', 	record.data.assid, record.data.contextcode);
        	var ret = String.format('<a href="#" onclick="window.open(\'{0}?module=turnitin&action=sub&title=mytitle&instructoremail={1}&title={2}&assid={3}\')" >Submit</a>',baseUri, record.data.instructoremail,record.data.title, record.data.assid );
    		//alert(ret);
    		return ret;
        	//return  new Ext.Button('titititle');
        	/*return new Ext.Button({
    			text:'Submit',
    			iconCls: 'add16',
                iconAlign: 'right'

    		});*/
    	}else {
    	
        	return String.format('<a href="#" class="'+cid+'" onClick="window.open(\''+baseUri+'?module=turnitin&action=returnreport&objectid={2}\',\'rview\',\'height=768,width=1024,location=no,menubar=no,resizable=yes,scrollbars=yes,titlebar=no,toolbar=no,status=no\');" ><span class="white">{0}</span> </a>', 
        	value, record.data.contextcode, record.data.objectid);
    	}
    }
    
function uploadWindow(assId, contextCode) {
	Ext.MessageBox.alert(contextCode);
	
}


/**
* Upload an assignment
*/
function uploadFormPanel(assId, contextCode){
	var win;

	if(!win){
        win = new Ext.Window({           
            layout:'fit',
            width:500,
            height:200,
            closeAction:'hide',
            plain: true,
            items: [fp]
            
        });
    }
    win.show(this);


}

  
   var fu = new Ext.form.TextField({
          inputType: 'file',
          fieldLabel: 'File',
          name: 'file',
          emptyText: 'Select a paper'
          
        })

 var fp = new Ext.FormPanel({
         fileUpload: true,
        //width: 600,        
        frame: true,
        title: 'Submit a Paper',
        autoHeight: true,
        bodyStyle: 'padding: 10px 10px 0 10px;',
        labelWidth: 50,
        defaults: {
            //anchor: '95%',
            allowBlank: false,
            width:200,
            msgTarget: 'side'
        },
        items: [{
	            xtype: 'textfield',
	            width:350,
	            name: 'assignmenttitle',
	            emptyText: 'Give your paper a title ...',
	            fieldLabel: 'Title'
        	},fu
        	],
        
        buttons: [{
            text: 'Submit',
            handler: function(){
                if(fp.getForm().isValid()){
	                fp.getForm().submit({
	                    url: baseuri +'?module=turnitin&action=ajax_sumbitassessment',
	                    params:  {'module' : 'turnitin', 'action': 'ajax_sumbitassessment'},
	                    waitMsg: 'Uploading your paper...',
	                    success: function(fp, action){
	                        msg('Success', 'File was successfully uploaded <br/>'+action.result.msg);
	                       // fp.hide();
	                    },
	                    failure: function(fp, action){
	                    	msg('Error!', action.result.msg);
	                    }
	                    	
	                });
                }
            }
        },{
            text: 'Reset',
            handler: function(){
                fp.getForm().reset();
            }
        }]
    });

