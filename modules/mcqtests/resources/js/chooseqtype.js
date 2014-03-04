Ext.onReady(function() {
    
});

var addQuestionPanel = new Ext.FormPanel({
    //standardSubmit: true,
    frame:true,
    title: 'Edit a Group',
    bodyStyle:'padding:5px 5px 0',
    width: 300,
    waitMsgTarget: true,
    defaultType: 'textfield',
    layout: 'form',
    items: [
    {
        fieldLabel: 'Group Name',
        name: 'groupname',
        itemId: 'groupname',
        allowBlank:false,
        vtype: 'groupname',
        invalidText:'This group is already taken'

    }],

    buttons: [{
        text: 'Add Question',
        id:'addquestion',
        handler: function (){

        }
    }]
});

var choosequestiontype = function(questionTypeUrl) {
    //Ext.Msg.alert("Choose Question Type", "Which question do you want to choose?");
    window.location.href = questionTypeUrl;
    alert(questonTypeUrl);
    var win;
    
    if(!addwin){
        addwin = new Ext.Window({
            layout:'fit',
            width:400,
            height:150,
            closeAction:'hide',
            plain: true
        });
    }
    addwin.show(this);
}