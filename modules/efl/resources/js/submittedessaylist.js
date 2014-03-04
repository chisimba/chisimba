
function showSubmittedEssays(data){

var xg = Ext.grid;
// shared reader
var reader = new Ext.data.ArrayReader({}, [
   {name: 'from'},
   {name: 'date'}
]);
// Array data for the grids
Ext.grid.Data = data;

var grid = new xg.GridPanel({
    store: new Ext.data.GroupingStore({
    reader: reader,
    data: xg.Data,
    sortInfo:{field: 'from', direction: "ASC"},
    groupField:'from'
    }),

    columns: [
        {id:'from',header: "From", width: 500, dataIndex: 'from'},
        {header: "Date", width: 100, dataIndex: 'date'}
    ],

    view: new Ext.grid.GroupingView({
        forceFit:true,
        groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? \"Submissions\" : \"Submission\"]})'
    }),

    frame:false,
    width: 750,
    height: 350,
    x: 20,
    collapsible: false,
    animCollapse: false,
    renderTo: 'grouping-grid'

});
}

function showSubmitDetails(detailsdata){
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
Ext.grid.Data = detailsdata;

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
        groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? \"details\" : \"Member\"]})'
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