var userField;
var addMemberForm;


function deleteTopic(topicid){

Ext.MessageBox.confirm('Delete Topic?', 'Are you sure you want to delete this topic?', function(btn){

  if (btn == 'yes') {
    window.location.href='?module=speak4free&action=deletetopic'+'&topicid='+topicid;
  }

});
}

function deleteArticle(articleid,topicid){

Ext.MessageBox.confirm('Delete Article?', 'Are you sure you want to delete this article?', function(btn){

  if (btn == 'yes') {
    window.location.href='?module=speak4free&action=deletearticle'+'&articleid='+articleid+"&topicid="+topicid;
  }

});
}
function showTopics(data){

var xg = Ext.grid;
// shared reader
var reader = new Ext.data.ArrayReader({}, [
   {name: 'title'},
   {name: 'members'},
   {name: 'preview'},
   {name: 'edit'}
]);
// Array data for the grids
Ext.grid.Data = data;

var grid = new xg.GridPanel({
    store: new Ext.data.GroupingStore({
    reader: reader,
    data: xg.Data,
    sortInfo:{field: 'title', direction: "ASC"},
    groupField:'title'
    }),

    columns: [
        {id:'title',header: "Title", width: 500, dataIndex: 'title'},
        {header: "Members", width: 100, dataIndex: 'members'},
        {header: "Preview", width: 100, dataIndex: 'preview'},
        {header: "Edit", width: 100, dataIndex: 'edit'}
    ],

    view: new Ext.grid.GroupingView({
        forceFit:true,
        groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? \"Topics\" : \"Topic\"]})'
    }),

    frame:false,
    width: 800,
    height: 350,
    x: 20,
    collapsible: false,
    animCollapse: false,
    renderTo: 'grouping-grid'

});


}
function showArticles(data){
var xg = Ext.grid;
// shared reader
var reader = new Ext.data.ArrayReader({}, [
   {name: 'title'},
   {name: 'edit'}
]);
// Array data for the grids
Ext.grid.Data = data;

var grid = new xg.GridPanel({
    store: new Ext.data.GroupingStore({
    reader: reader,
    data: xg.Data,
    sortInfo:{field: 'title', direction: "ASC"},
    groupField:'title'
    }),

    columns: [
        {id:'title',header: "Title", width: 500, dataIndex: 'title'},
        {header: "Edit", width: 100, dataIndex: 'edit'}
    ],

    view: new Ext.grid.GroupingView({
        forceFit:true,
        groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? \"Articles\" : \"Article\"]})'
    }),

    frame:false,
    width: 800,
    height: 350,
    x: 20,
    collapsible: false,
    animCollapse: false,
    renderTo: 'grouping-grid'
   
});


}
function showSessionDetails(membersdata){
ButtonPanel = Ext.extend(Ext.Panel, {

    layout:'table',
    defaultType: 'button',
    baseCls: 'x-plain',
    cls: 'btn-panel',
    menu: undefined,
    split: true,
    bodyStyle:'margin-top:2em;margin-bottom:2em;',
    constructor: function(buttons){
        for(var i = 0, b; b = buttons[i]; i++){
            b.menu = this.menu;
            b.enableToggle = this.enableToggle;
            b.split = this.split;
            b.arrowAlign = this.arrowAlign;
        }
        var items = buttons;

        ButtonPanel.superclass.constructor.call(this, {
            items: items
        });
    }
});

var xg = Ext.grid;
// shared reader
var reader = new Ext.data.ArrayReader({}, [
   {name: 'names'},
   {name: 'group'},
   {name: 'edit'}
]);
// Array data for the grids
Ext.grid.Data = membersdata;

var grid = new xg.GridPanel({
    store: new Ext.data.GroupingStore({
    reader: reader,
    data: xg.Data,
    sortInfo:{field: 'names', direction: "ASC"},
    groupField:'group'
    }),

    columns: [
        {id:'title',header: "Title", width: 300, dataIndex: 'names'},
        {header: "Group", width: 100, dataIndex: 'group'},
        {header: "Edit", width: 100, dataIndex: 'edit'}
    ],

    view: new Ext.grid.GroupingView({
        forceFit:true,
        groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? \"Members\" : \"Member\"]})'
    }),

    frame:false,
    width: 600,
    height: 350,
    x: 20,
    collapsible: false,
    animCollapse: false
    
});    ButtonPanel.override({
     //renderTo : 'buttons-layer'
      });
  var buttons= new ButtonPanel(

       [
       {
            
            text:'Add Member',
            handler: function(){
           showAddMemberWin();
            }
        }

       ]
    );

var form = new Ext.form.FormPanel({
        baseCls: 'x-plain',
        labelWidth: 100,
        fieldWidth:144,
        bodyStyle:'padding:5px 5px 0',
        standardSubmit: true,
        
        defaultType: 'textfield',
        renderTo: 'grouping-grid',
        //[meetingDate,timeFrom,timeTo,editUrl,sessionTitle,deleteUrl];
        items: [
        buttons,
        grid
     ]

});

}
function deleteSchedule(sessionid){

Ext.MessageBox.confirm('Delete Schedule?', 'Are you sure you want to delete this schedule?', function(btn){

  if (btn == 'yes') {
    window.location.href='?module=realtime&action=deletesession'+'&sessionid='+sessionid;
  }

});
}
function initAddMember(userlist,url){
    var userdatastore = new Ext.data.ArrayStore({
        fields: ['userid','name'],
        data : userlist
    });
    userField = new Ext.form.ComboBox({
        store: userdatastore,
        displayField:'name',
        valueField: 'userid',
        fieldLabel:'Names',
        typeAhead: true,
        mode: 'local',
        editable:true,
        allowBlank:false,
        forceSelection: true,
        triggerAction: 'all',
        emptyText:'Select user...',
        selectOnFocus:true,
        hiddenName : 'userfield'

    });
    addMemberForm = new Ext.form.FormPanel({
        baseCls: 'x-plain',
        labelWidth: 55,
        bodyStyle:'padding:5px 5px 0',
        standardSubmit: true,
        url:url,
        defaultType: 'textfield',
        items: userField

    });

}

function showAddMemberWin(){
    var addMemberWin;
       if(!addMemberWin){
            addMemberWin = new Ext.Window({
                applyTo:'addsession-win',
                layout:'fit',
                width:500,
                height:250,
                x:250,
                y:50,
                closeAction:'destroy',
                plain: true,

               items: addMemberForm,

                buttons: [{
                    text:'Save',
                    handler: function(){
                        if (addMemberForm.url){
                            addMemberForm.getForm().getEl().dom.action = addMemberForm.url+'&userid='+userField.value;
                          }
                        addMemberForm.getForm().submit();
                    }
                },{
                    text: 'Close',
                    handler: function(){
                       addMemberWin.hide();
                    }
                }]
            });
        }
        addMemberWin.show(this);

}

