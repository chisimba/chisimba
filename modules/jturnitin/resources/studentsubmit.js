var swin;
var sfilePath;
var spaperTitle;
var sfileName;
var pfirstname;
var plastname;

var sfu = new Ext.form.TextField({
    inputType: 'file',
    fieldLabel: 'File',
    name: 'file',
    emptyText: 'Select a paper'

})

var sfp = new Ext.FormPanel({
    fileUpload: true,
    //width: 600,
    frame: true,
    title: 'Submit a Paper',
    autoHeight: true,
    bodyStyle: 'padding: 10px 10px 10px 10px;',
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
    },
    {
        xtype: 'textfield',
        width:350,
        name: 'firstname',
        emptyText: 'First name here ...',
        fieldLabel: 'First Name'
    },
    {
        xtype: 'textfield',
        width:350,
        name: 'lastname',
        emptyText: 'Last name here ...',
        fieldLabel: 'Last Name'
    }

    ,sfu
    ],

    buttons: [{
        text: 'Submit',
        handler: function(){

            if(sfp.getForm().isValid()){
                try{


                    sfp.getForm().submit({
                        url: '?',
                        params:  {
                            'module' : 'jturnitin',
                            'action': 'ajax_lectureruploadassessment'
                        },
                        waitMsg: 'Uploading your paper...',
                        success: function(fp, action){

                            var successMessage=action.result.msg.split('|');
                            sfilePath=successMessage[0];
                            spaperTitle=successMessage[1];
                            sfileName=successMessage[2];
                            pfirstname=successMessage[3];
                            plastname=successMessage[4];
                            processUploadResult('Success', sfileName+' was successfully uploaded by lecturer<br/>');
                            swin.hide();
                        },
                        failure: function(sfp, action){
                            Ext.MessageBox.alert('Error!', action.result.msg,null);
                        }

                    });
                }catch(ex){

                }
            }
        }
    },{
        text: 'Reset',
        handler: function(){
            sfp.getForm().reset();
        }
    }]
});

function showStudentUploadFormPanel(assgTitle, contextCode){



    if(!swin){
        swin = new Ext.Window({
            layout:'fit',
            width:500,
            height:300,
            closeAction:'hide',
            plain: true,
            items: [sfp]

        });
    }
    try
    {
        swin.show(this);
    }
    catch(err)
    {
        Ext.MessageBox.alert("Error", "It appears the upload dialog did not show. Please try again",null);
    }
}

var processUploadResult = function(title, msg){
    Ext.Msg.show({
        title: title,
        msg: msg,
        minWidth: 200,
        modal: true,
        fn:showLecturerResult,
        icon: Ext.Msg.INFO,
        buttons: Ext.Msg.OK
    });
};
function showLecturerResult(){
 
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
        params :"module=jturnitin&action=lecturer_submit_assessment&papertitle="+spaperTitle+"&assignmenttitle="+assgTitle+"&filepath="+sfilePath+"&filename="+sfileName+"&firstname="+pfirstname+"&lastname="+plastname,
        success: function ( result, request ) {
            Ext.MessageBox.hide();
            var obj = eval('(' + result.responseText + ')');

            Ext.MessageBox.alert('Result',obj.msg,null);

        },
        failure:function(){
            Ext.MessageBox.alert('Result',"It is likely your assignment was not posted correctly.<br/>However,this might be a false alarm. Please check this page in approximately 5-10 minutes to view the results.",null);
        }

    });
}