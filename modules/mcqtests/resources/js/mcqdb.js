var sm2 = new Ext.grid.CheckboxSelectionModel({
    listeners: {
        rowselect: function(sm2, rowIdx, r) {
        }
    }
}),
myPageSize = 20,
courseTitle = 'Course Databank for - ' + courseName;
dataType = Ext.get('qnoption').getValue(),
    courses = "";

// Create the data store
var myStore = new Ext.data.Store({
    proxy: new Ext.data.HttpProxy({
        method: 'GET',
        url: myUrl
    }),
    reader: new Ext.data.JsonReader({
        root: 'results',
        totalProperty: 'totalcount',
        fields: [
        {
            name: 'id',
            mapping: 'id'
        },

        {
            name: 'name',
            mapping: 'name'
        },

        {
            name: 'question',
            mapping: 'question'
        }/*,

        {
            name: 'hint',
            mapping: 'hint'
        },

        {
            name: 'questiontype',
            mapping: 'questiontype'
        },
        {
            name: 'mark',
            mapping: 'mark'
        }*/
        ]
    })
});
var allCourses = new Ext.form.Checkbox({
    xtype: 'checkbox',
    name: 'allAssign',
    boxLabel: 'View All Courses',
    handler: function(checkbox, checked){
        if(checked) {
            // load data for all courses
            courses = 'all';
            getGridData();
        }
        else {
            courses = "";
            getGridData();
        }
    }
});
var showQuestionDB = function() {

    // create the Grid
    var grid = new Ext.grid.GridPanel({
        store: myStore,
        columns: [
        sm2,
        {
            header: 'id',
            width: 80,
            sortable: true,
            dataIndex: 'id',
            hidden: true
        },{
            id:'name',
            header: 'Course',
            width: 120,
            sortable: true,
            dataIndex: 'name'
        },{
            id: 'question',
            header: 'Question',
            width: 160,
            sortable: true,
            dataIndex: 'question'
        }/*, {
            id: 'mark',
            header: "Mark",
            width: 75,
            sortable: true,
            dataIndex: 'mark'
        },{
            header: 'Hint',
            width: 75,
            sortable: true,
            dataIndex: 'hint'
        },{
            header: 'Question Type',
            width: 100,
            sortable: true,
            dataIndex: 'questiontype'
        }*/],
        tbar: [allCourses],
        fbar: [{
            text: 'Save',
            iconCls: 'silk-add',
            listeners: {
                click: function() {
                    getSelections();
                }
            }
        }],
        bbar: new Ext.PagingToolbar({
            store: myStore,
            displayInfo: true,
            pageSize: myPageSize,
            displayMessage: "Displaying Questions {0} - {1} of {2}"
        }),
        sm: sm2,
        stripeRows: true,
        autoExpandColumn: 'question',
        height: 350,
        width: "100%",
        title: courseTitle,
        stateId: 'grid'
    });

    // render the grid to the specified div in the page
    grid.render('mcqGrid');
      
}

function getSelections() {
    var mySelections = sm2.getSelections();
    var ids = "";
    if(sm2.getCount() > 0) {
        //var confirmMsg = "Are you sure you want to submit these " + sm2.getCount() + " questions?";
        //Ext.MessageBox.confirm("Submitting IDs", confirmMsg, function(btn, text){
        //if (btn == 'yes'){
        for(i=0;i<parseInt(sm2.getCount());i++) {
            ids += mySelections[i].get('id');
            if( i < parseInt(sm2.getCount()) - 1) {
                ids += ",";
            }
        }
        // do a submit of these id's and go back to this page when done, with new data
        submitIDs(ids);
    //}
    //});
    }
    
    
}

function submitIDs(ids) {
    // Basic request
    if(ids.length > 0) {
        Ext.Ajax.request({
            url: submitUrl,
            success: ajaxSuccess,
            failure: ajaxFailure,
            params: {
                courseID: courseID,
                idData: ids
            }
        });
    }
}

var ajaxSuccess = function() {
    window.location.href = nextUrl;
}

var ajaxFailure = function() {
    Ext.MessageBox.alert("There was an error submitting your request. Please try again!");
}

function getGridData() {
    if(courses.length > 0) {
        myStore.setBaseParam('myParams', Ext.urlEncode({
            type: Ext.get('qnoption').getValue(),
            courses: courses
        }));
    }
    else {
        myStore.setBaseParam('myParams', Ext.urlEncode({
            type: Ext.get('qnoption').getValue()
            }));
    }
    myStore.load();
    jQuery("#mcqGrid").html("");
    showQuestionDB(myUrl);
}


function showQuestion(questionid){
    window.open( "?module=mcqtests&action=previewquestion&id="+questionid, "myWindow",
        "status = 1, height = 350, width = 800, resizable = 0" )
}

jQuery(document).ready(function() {
    // check the type of question selected.
    jQuery("#input_typemcq").bind('click',function(){
        myVal = jQuery("#input_options").val();
        jQuery("select[name=options] option[value=4]").attr("selected", true);
        jQuery("select[name=options]").attr("disabled", "");
    });

    jQuery("#input_typetf").bind('click',function(){
        myVal = jQuery("#input_options").val();
        jQuery("select[name=options] option[value=2]").attr("selected", true);
        jQuery("select[name=options]").attr("disabled", "disabled");
    });

    // when submitting the form, remove the hidden attribute if input type is true/false
    jQuery('form').bind('submit', function() {
        if(jQuery("#input_typetf:checked").val() == "tf") {
            jQuery(this).find(':input').removeAttr('disabled');
        }
    });
});
