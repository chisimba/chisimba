var assgTitle="";
var paperTitle="";
var filePath="";
var fileName="";
var returnCode=""
var win;
var msg = function(title, msg){
    Ext.Msg.show({
        title: title,
        msg: msg,
        minWidth: 200,
        modal: true,
        fn:showResult,
        icon: Ext.Msg.INFO,
        buttons: Ext.Msg.OK
    });
};
function getParams(){
    return "module=jturnitin&action=submit_assessment&papertitle="+paperTitle+"&assignmenttitle="+assgTitle+"&filepath="+filePath;
}
function getRefreshParams(){
    return "module=jturnitin";
}
function showResult(){
    Ext.MessageBox.show({
        msg: 'Posting assessment, please wait...',
        progressText: 'Posting...',
        width:300,
        wait:true,
        waitConfig: {
            interval:200
        }
    });

  
    Ext.Ajax.request({
        url : "?",
        timeout : 300000,
        method:'POST',
        params :"module=jturnitin&action=submit_assessment&papertitle="+paperTitle+"&assignmenttitle="+assgTitle+"&filepath="+filePath+"&filename="+fileName,
        success: function ( result, request ) {
            Ext.MessageBox.hide();
            var obj = eval('(' + result.responseText + ')');
            Ext.MessageBox.alert('Result',obj.msg,autoRefreshScreen);
        },
        failure:function(){
            Ext.MessageBox.alert('Result',"It is likely your assignment was not posted correctly.<br/>However,this might be a false alarm. Please check this page in approximately 5-10 minutes to view the results.",null);
        }

    });
}

function updateResults(){
    assStore.load({
        params:{
            start:0,
            limit:10
        }
    });

}
function autoRefreshScreen(){
    var task = {
        run: updateResults,
        interval: 1000  * 180
    }
    var runner = new Ext.util.TaskRunner();
    runner.start(task);

    // equivalent using TaskMgr
    Ext.TaskMgr.start({
        run: updateResults,
        interval: 1000 * 180
    });
}
function refreshPage(){
    Ext.MessageBox.show({
        msg: 'Checking result, please wait...',
        progressText: 'Results...',
        width:300,
        wait:true,
        waitConfig: {
            interval:200
        }
    });
  
    Ext.Ajax.request({
        url : "?",
        method:'GET',

        params :getRefreshParams,
        success: function ( result, request ) {
            Ext.MessageBox.hide();
        }
    });
}
var assStore = new Ext.data.JsonStore({
    root: 'assignments',
    totalProperty: 'totalCount',
    idProperty: 'puid',
    //remoteSort: true,

    fields: [
    {
        name: 'title'
    },

    {
        name: 'duedate'
    },

    {
        name: 'score'
    },
    {
        name: 'submitted'
    },
    {
        name: 'resubmit'
    },

    {
        name: 'assid'
    },
    {
        name: 'instructions'
    },
    {
        name: 'datestart'
    },
    {
        name: 'contextcode'
    },

    {
        name: 'objectid'
    },

    {
        name: 'instructoremail'
    }
    ],
    proxy: new Ext.data.HttpProxy({
        url: storeUri,
        timeout:180000
    })
});
assStore.setDefaultSort('duedate', 'desc');
    
    
    
/**
* The assignment Grid/List
*/
var assGrid = new Ext.grid.GridPanel({
    width:"100%",
    height:400,
    title:'Assignments',
    store: assStore,
    trackMouseOver:true,
    //disableSelection:true,
    loadMask: true,
    stripeRows: true,
    timeout:100,
    sm: new Ext.grid.RowSelectionModel({
        singleSelect:true
    }),


    region:'center',


    // grid columns
    columns:[new Ext.grid.RowNumberer(),{
        id: 'topic', 
        header: "Topic",
        dataIndex: 'title',
        width: 420,

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

assGrid.getSelectionModel().on('rowselect', function(sm, ri, record){
    assgTitle=record.data.title;
    
});
Ext.onReady(function(){
	 
    Ext.QuickTips.init();

    //student must a list of assignments ordered by due date
    // this can be done by using a grid
    assGrid.render('topic-grid');
	
    // trigger the data store load
    assStore.load({
        params:{
            start:0,
            limit:10
        }
    });
   

});



function renderScore(value, p, record){
    try{
        var cid = 'green';
    
        var dueDateStr=record.data.duedate;
        var startDateStr=record.data.datestart;

        if(startDateStr == null){
            startDateStr="2010-06-10 00:00:00.0";
        }
        var dueDateArray=dueDateStr.split("-");
        var dueDate=new Date();
   
        var startDateArray=startDateStr.split("-");
        var startDate=new Date();

        var today = new Date();
        today.setDate(today.getDate()+1);
        var today2 = new Date();

        var dayStr=dueDateArray[2];
        var day=dayStr.split(" ");
    
        var dayStr2=startDateArray[2];
        var day2=dayStr2.split(" ");

        dueDate.setYear(dueDateArray[0]);
        dueDate.setMonth(dueDateArray[1])
        dueDate.setDate(day[0]);
        dueDate.setMonth(dueDate.getMonth()-1);

        startDate.setYear(startDateArray[0]);
        startDate.setMonth(startDateArray[1])
        startDate.setDate(day2[0]);
        startDate.setMonth(startDate.getMonth()-1);


        var v;
        v = value;
    	
        if (value > 20 && value < 40)
        {
            cid = 'yellow';
        } else if (value >= 40) {
            cid = 'red';
        }else if(value == 0){
            cid="blue";
        }
        else if (value < 0){
            cid = 'pending';
        }

        if (value < 0)
        {
            value = '--&nbsp;';
        } else {
            value = value+'%';
        }

     
        if(cid == 'pending' && v != '')
        {

            var ret = String.format('Pending');
            return ret;
        }else if(value  < 0){
            
            //alert(record.data.title+" "+v+" , "+value);
            var ret = String.format('<a href="#"  onClick="uploadFormPanel(\''+record.data.title+'\', \''+record.data.contextcode+'\');" >Submit </a>');
            if (v =='' && record.data.submitted == 'Y'){
                ret="--&nbsp;";
            }
            if (dueDate < today){
                ret="Too late to submit";
            }
       
            if (startDate > today2){
                ret="Too early to submit";
            }

            return ret;

        }else {
          
            var ct='<a href="#" class="'+cid+'" onClick="window.open(\''+baseUri+'?module=jturnitin&action=returnreport&objectid={2}\',\'rview\',\'height=768,width=1024,location=no,menubar=no,resizable=yes,scrollbars=yes,titlebar=no,toolbar=no,status=no\');" ><span class="white">{0}</span> </a>';
            if(record.data.resubmit == '1'){
                ct+=String.format('<a href="#"  onClick="uploadFormPanel(\''+record.data.title+'\', \''+record.data.contextcode+'\');" >Resubmit </a>');
            }
            var retVal= String.format(ct,
                value, record.data.contextcode, record.data.objectid);

            if (value < 0 && record.data.submitted == 'Y'){
                ret="--&nbsp;";
            }
            if (dueDate < today){
                retVal="Too late to submit";
            }

            if (startDate > today2){
                ret="Too early to submit";
            }
            return retVal;
 
        }
    }catch(err)
    {

    }
}
    
function uploadWindow(assId, contextCode) {
    Ext.MessageBox.alert(contextCode);
	
}


/**
* Upload an assignment
*/
function uploadFormPanel(xassTitle, contextCode){
	

    
    if(!win){
        win = new Ext.Window({           
            layout:'fit',
            width:500,
            height:200,
            closeAction:'destroy',
            plain: true,
            items: [fp]
            
        });
    }
    try
    {
        win.show(this);
    }
    catch(err)
    {
        Ext.MessageBox.alert("Error", "It appears the upload dialog did not show. Please try again",null);
    }
  


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
        name: 'papertitle',
        emptyText: 'Give your paper a title ...',
        fieldLabel: 'Title'
    },fu
    ],
        
    buttons: [{
        text: 'Submit',
        handler: function(){
            
            if(fp.getForm().isValid()){
                try{
                   

                    fp.getForm().submit({
                        url: '?',
                        params:  {
                            'module' : 'jturnitin',
                            'action': 'ajax_uploadassessment'
                        },
                        waitMsg: 'Uploading your paper...',
                        success: function(fp, action){
                            
                            var successMessage=action.result.msg.split('|');
                            filePath=successMessage[0];
                            paperTitle=successMessage[1];
                            fileName=successMessage[2];
                            msg('Success', fileName+' was successfully uploaded <br/>');
                            win.destroy();
                        },
                        failure: function(fp, action){
                            msg('Error!', action.result.msg);
                        }
	                    	
                    });
                }catch(ex){

                }
            }
        }
    },{
        text: 'Reset',
        handler: function(){
            fp.getForm().reset();
        }
    }]
});




