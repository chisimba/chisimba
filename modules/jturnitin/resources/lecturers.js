/*!
 * Ext JS Library 3.0.0
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 * The lecturer view displays a list of assignments in a grid
 * to view the submissions of the assignments the user can click
 * on the row  
 */

/**
* The validation of the date fields
* and utility functions
*
*/

var contextCode,paperTitle;

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
    
var win;

Ext.apply(Ext.form.VTypes, {
    daterange : function(val, field) {
        var date = field.parseDate(val);

        if(!date){
            return;
        }
        if (field.startDateField && (!this.dateRangeMax || (date.getTime() != this.dateRangeMax.getTime()))) {
            var start = Ext.getCmp(field.startDateField);
            start.setMaxValue(date);
            start.validate();
            this.dateRangeMax = date;
        } 
        else if (field.endDateField && (!this.dateRangeMin || (date.getTime() != this.dateRangeMin.getTime()))) {
            var end = Ext.getCmp(field.endDateField);
            end.setMinValue(date);
            end.validate();
            this.dateRangeMin = date;
        }
        /*
         * Always return true since we're only using this vtype to set the
         * min/max allowed values (these are tested for after the vtype test)
         */
        return true;
    }
});
    
// The action
var action = new Ext.Action({
    text: 'Add Assessment',
    handler: function(){
        if(!win){
            win = new Ext.Window({
	                
                layout:'fit',
                width:500,
                height:450,
                closeAction:'hide',
                plain: true,
	
                items:[addForm],
	
                buttons: [advancedbutton,submitbutton]
            });
        }
        win.show(this);
    },
    iconCls: 'blist'
});
   


    
//upload form panel
var fp = new Ext.FormPanel({
    //renderTo: 'fi-form',
    fileUpload: true,
    width: 500,
    frame: true,
    title: 'File Upload Form',
    autoHeight: true,
    bodyStyle: 'padding: 10px 10px 0 10px;',
    labelWidth: 50,
    defaults: {
        anchor: '95%',
        allowBlank: false,
        msgTarget: 'side'
    },
    items: [],
    buttons: [{
        text: 'Save',
        handler: function(){
            if(fp.getForm().isValid()){
                fp.getForm().submit({
                    url: 'file-upload.php',
                    waitMsg: 'Uploading your photo...',
                    success: function(fp, o){
                        msg('Success', 'Processed file "'+o.result.file+'" on the server');
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

//array for assinment types
var assTypeStore = new Ext.data.ArrayStore({
    fields: ['assgid','name'],
    data : [['1','Paper Assignment']]
});
var assTypeCombo = new Ext.form.ComboBox({
    store: assTypeStore,
    displayField:'name',
    width:350,
    typeAhead: true,
    mode: 'local',
    fieldLabel:'Assessment Type',
    forceSelection: true,
    triggerAction: 'all',
    emptyText:'Select assessment type...',
    selectOnFocus:true
});
/**
* The add form
*
*/
var addForm = new Ext.FormPanel({
    labelWidth: 75, // label settings here cascade unless overridden
    url: baseuri+'?module=jturnitin&action=ajax_addassignment',
    frame:true,
    //layout:'column',
    shadow: true,
    title: 'Add Assessment',
    bodyStyle:'padding:5px 5px 0',
    width: 350,
    defaults: {
        width: 230
    },
    defaultType: 'textfield',
		
    items: [{
        fieldLabel: 'Title',
        name: 'title',
        emptyText: 'Title of the paper...',
        anchor:'95%',
        allowBlank:false
    },
  
    assTypeCombo  ,

    {
		        
        xtype:'fieldset',
        fieldLabel: 'Dates',
			        	
        //columnWidth: 0.5,
        // layout:'column',
        defaults:{
            bodyStyle:'padding:10px'
        },

        collapsible: false,
        autoHeight:true,
        defaults: {
            anchor: '-20' // leave room for error icon
        },
        defaultType: 'textfield',
						
        //items on the fieldset
        items :[
        new Ext.form.DateField({
            fieldLabel: 'Start Date',
            anchor:'95%',
            name: 'startdt',
            id: 'startdt',
            format: 'o-m-d',
            allowBlank:false,
            //
            // vtype: 'daterange',
            //emptyText: 'Start Date...',
            blankText: 'The Start Date is a Required Field!',
            endDateField: 'enddt'
        }),
        new Ext.form.DateField({
            fieldLabel: 'End Date',
            type: 'datefield',
            format: 'o-m-d',
            name: 'duedt',
            id: 'enddt',
            allowBlank:false,
            startDateField: 'startdt' // id of the start date field
        })

        ]
    },{
        xtype:'htmleditor',
        id:'instructions',
        name:'instructions',
        fieldLabel:'Special Instructions',
        height:100,
        anchor:'98%'
    }
    ]
       		 
});

/**
    * The action associated with adding a new assignment
    *
    */
// explicit add
var submitbutton = new Ext.Button({
    text: 'Save',
        
    handler: function(){
        if(addForm.getForm().isValid()){
      
            addForm.getForm().submit({
                url:baseuri,
                waitMsg:'Creating new Assignment...',
                timeout:10,
                
                params: {
                    module: 'jturnitin',
                    action: 'ajax_addassignment',

                    report_gen_speed:reportGenSpeed,
                    exclude_biblio:excludeBiographicMaterial,
                    exclude_quoted:excludeQuotedMaterial,
                    exclude_value:excludeSmallMatches,
                    late_accept_flag:allowLateSubmissions,
                    submit_papers_to:submitPapersTo,
                    s_paper_check:s_paper_check,
                    journal_check:journal_check,
                    internet_check:internet_check,
                    s_view_report:studentsViewOriginalityReports

                },
                success: function(form, action) {
                    win.hide();
                    addForm.getForm().reset();
                    assStore.load({
                        params:{
                            start:0,
                            limit:25
                        }
                    });
                    msg('Success!', action.result.msg);
				  
                },
                failure: function(form, action) {
                    msg('Error', action.result.msg);
			    	
                }
            });
        }
    }
});

var advancedbutton = new Ext.Button({
    text: 'Advanced Options',
        
    handler: function(){
        showAdvancedOptions();
       
    }
        
});
var but =  new Ext.Button(action);

/**
* The assignment data store used by the assGrid
*
*/
var assStore = new Ext.data.JsonStore({
    root: 'assignments',
    totalProperty: 'totalCount',
    idProperty: 'puid',
    fields: [
    {
        name: 'title'
    },
    {
        name: 'id'
    },

    {
        name: 'duedate'
    },
    {
        name: 'contextcode'
    },
    {
        name: 'instructoremail'
    },
    {
        name: 'submissions'
    },
    {
        name: 'instructions'
    }
    ],
    proxy: new Ext.data.HttpProxy({
        url: baseuri+'?module=jturnitin&action=json_getassessments'
    })
});
assStore.setDefaultSort('duedate', 'desc');
    
//store for the submissions
submissionsStore =new Ext.data.JsonStore({
    root: 'submissions',
    totalProperty: 'totalCount',
    idProperty: 'username',
    fields: [
    {
        name: 'firstname'
    },

    {
        name: 'lastname'
    },

    {
        name: 'title'
    },

    {
        name: 'score'
    },
    {
        name: 'userid'
    },
    {
        name: 'objectid'
    },
    
    {
        name: 'downloadassignment'
    },
    {
        name: 'dateposted'
    }
    ],
    proxy: new Ext.data.HttpProxy({
        url: baseuri+'?module=jturnitin&action=json_getsubmissions'
    })
});
    
submissionsStore.setDefaultSort('dateposted', 'desc');
/**
* The assigments grid
* the grid will use ajax-json to get the data from
* server
*/

var assGrid = new Ext.grid.GridPanel({
    width:"100%",
    height:400,
    title:'Assignments',
    store: assStore,
    trackMouseOver:true,
    disableSelection:true,
    loadMask: true,
    stripeRows: true,
    sm: new Ext.grid.RowSelectionModel({
        singleSelect:true
    }),
    region:'center',


    // grid columns
    columns:[new Ext.grid.RowNumberer(),{
        id: 'topic', // id assigned so we can apply custom css (e.g. .x-grid-col-topic b { color:#333 })
        header: "Topic",
        dataIndex: 'title',
        width: 420,
        sortable: true
    },{
        header: "Submissions",
        dataIndex: 'submissions',
        width: 60,
        align: 'middle',
        
        sortable: true
    },
    {
        header: "Submit",
        dataIndex: 'submitassignment',
        width: 60,
        align: 'middle',
        renderer:renderSubmitAssignmentLink,
        sortable: true
    },
    {
        header: "Edit",
        dataIndex: 'editassignment',
        width: 60,
        align: 'middle',
        renderer:renderEditLink,
        sortable: true
    },
    {
        header: "Delete",
        dataIndex: 'deleteassignment',
        width: 60,
        align: 'middle',
        renderer:renderDeleteAssignmentLink,
        sortable: true
    }
    ,{
        id: 'last',
        header: "Due Date",
        dataIndex: 'duedate',
        width: 150,

        sortable: true
    }],
    tbar :[ but ],
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
        pageSize: 25,
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
/**
* Submissions grid 
*/

var submissionsGrid =  new Ext.grid.GridPanel({
    width:"100%",
    height:200,
    title:'Submissions',
    store: submissionsStore,
    trackMouseOver:true,
    loadMask: true,
    stripeRows: true,
    minHeight: 100,
    maxHeight: 200,

    columns:[new Ext.grid.RowNumberer(),
    {
        id: 'firstame',
        header: "First Name",
        dataIndex: 'firstname',
        width: 100,
        sortable: true
    },{
        id: 'lastname',
        header: "Last Name",
        dataIndex: 'lastname',
        width: 100,
        sortable: true
    },{
        id: 'title',
        header: "Title",
        dataIndex: 'title',
        width: 200,
        sortable: true
    },{
        id: 'score',
        header: "Score",
        dataIndex: 'score',
        renderer:renderScore,
        width: 100
    },


    
    {
        id: 'dateposted',
        header: "Date Submitted",
        dataIndex: 'dateposted',
        width: 100
    }]
        
});
	
var submissionsPanel = new Ext.Panel({
    width:600,
    height:200,
    layout:'fit',
    margins: '5 5 0',
    region: 'south',
    split: true,
    //hidden:true,
    minHeight: 100,
    maxHeight: 200,
    collapsed : true,
    collapsible: true,
    titleCollapse: true,

    items:[submissionsGrid]
})

Ext.onReady(function(){

    // render it
    //assGrid.render('topic-grid');
    Ext.QuickTips.init();
    
    var layout = new Ext.Panel({
       
        layout: 'border',
        layoutConfig: {
            columns: 1
        },
        width:"100%",
        height: 400,
        items: [assGrid, submissionsPanel]
    });
    layout.render('topic-grid');

    assStore.load({
        params:{
            start:0,
            limit:25
        }
    });

    
    assGrid.getSelectionModel().on('rowselect', function(sm, ri, record){
        
        submissionsPanel.expand(true);
        contextCode=record.data.contextcode;
        paperTitle=record.data.title;
       
        submissionsStore.load({
            params: {
                title:record.data.title
            }
        });
        submissionsGrid.setTitle('Submissions for - '+record.data.title);
    
    });
});
function renderEditLink(value, p, record){

    return String.format('<a href="#"  onClick="window.open(\''+baseuri+'?module=jturnitin&action=editassignment&contextcode={0}&title={1}&instructoremail={2}&id={3}\',\'rview\',\'height=768,width=1024,location=0,menubar=0,resizable=0,scrollbars=0,titlebar=0,toolbar=0,status=0\');return false;" >Edit</a>',
        record.data.contextcode, record.data.title,record.data.instructoremail,record.data.id);
    
}
function renderSubmitAssignmentLink(value, p, record){

    return String.format("<a href=\"#\" onClick=\"showStudentUploadFormPanel('"+record.data.contextcode+"','"+record.data.title+"')\";return false;\">Submit</a> ");//"'+baseuri+'?module=turnitin&action=deleteassignment&contextcode='+record.data.contextcode+'&title='+record.data.title+'">Delete<a/>');
}
function renderDeleteAssignmentLink(value, p, record){

    return String.format("<a href=\"#\" onClick=\"deleteAssignment('"+record.data.contextcode+"','"+record.data.title+"')\";return false;\">delete</a> ");//"'+baseuri+'?module=turnitin&action=deleteassignment&contextcode='+record.data.contextcode+'&title='+record.data.title+'">Delete<a/>');
}
//xobjectid, xpaperTitle,xnames
function renderDeleteSubmissionLink(value, p, record){
    var names=record.data.firstname+", "+record.data.lastname;
    return  String.format("<a href=\"#\" onClick=\"deleteSubmission('"+record.data.objectid+"','"+paperTitle+"','"+names+"')\";return false;\">delete</a> ");//"'+baseuri+'?module=turnitin&action=deleteassignment&contextcode='+record.data.contextcode+'&title='+record.data.title+'">Delete<a/>');
}
function renderScore(value, p, record){
    var cid = 'green';
    	
    if (value > 20 && value < 40)
    {
        cid = 'yellow';
    } else if (value >= 40) {
        cid = 'red';
    }else if(value == 0){
        cid="blue";
    }else if (value < 0){
        cid = 'pending';
    }
    if (value < 0)
    {
        value = '--&nbsp;';
    } else {
        value = value+'%';
    }

    return String.format('<a href="#" class="'+cid+'" onClick="window.open(\''+baseuri+'?module=jturnitin&mode=lecturer&action=returnreport&objectid={2}&userid={3}\',\'rview\',\'height=768,width=1024,location=no,menubar=no,resizable=yes,scrollbars=yes,titlebar=no,toolbar=no,status=no\');" ><span class="white">{0}</span> </a>',
        value, record.data.contextcode, record.data.objectid,record.data.userid);
        

}

function deleteAssignment(xcontextCode,xpaperTitle){
    Ext.MessageBox.confirm('Confirm', 'Are you sure you delete '+xpaperTitle+'?',     function showResult(btn){

        if(btn == 'yes'){
            Ext.MessageBox.show({
                msg: 'Deleting assessment, please wait...',
                progressText: 'Deleting...',
                width:300,
                wait:true,
                waitConfig: {
                    interval:200
                }
            });

            Ext.Ajax.request({
                url : "?",
                method:'POST',
                params :'module=jturnitin&action=deleteassignment&contextcode='+xcontextCode+'&title='+xpaperTitle,
                success: function(form, action) {

                    assStore.load({
                        params:{
                            start:0,
                            limit:25
                        }
                    });
                    window.location.reload();
                    Ext.MessageBox.hide();
                // msg("Delete","It might take a while before the deleted assignment dissappears from the list");

                },
                failure: function(form, action) {
                //  msg('Error', action.result.msg);

                }

            });
        }
    });
}

 
    
function deleteSubmission(xobjectid, xpaperTitle,xnames){
    
    Ext.MessageBox.confirm('Confirm', 'Are you sure you delete "<b>'+xpaperTitle+'</b>" submission for "<b>'+xnames+'</b>"?',     function showResult(btn){
       
        if(btn == 'yes'){
            Ext.MessageBox.show({
                msg: 'Deleting submision, please wait...',
                progressText: 'Deleting...',
                width:300,
                wait:true,
                waitConfig: {
                    interval:200
                }
            });

            Ext.Ajax.request({
                url : "?",
                method:'POST',
                params :'module=jturnitin&action=deletesubmission&oid='+xobjectid+'&papertitle='+paperTitle,
                success: function(form, action) {
                    submissionsStore.load({
                        params: {
                            title:paperTitle
                        }
                    });
                    Ext.MessageBox.hide();

                },
                failure: function(form, action) {
                //  msg('Error', action.result.msg);
			    	
                }

            });
        }
    });
}


function getDeleteParams(){
    return 'module=jturnitin&action=deleteassignment&contextcode='+contextCode+'&title='+paperTitle;

}
