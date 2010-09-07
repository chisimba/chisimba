//the list of lecturer
var win2;

var lecturerdata = new Ext.data.JsonStore({
    root: 'users',
    totalProperty: 'totalCount',
    idProperty: 'id',
    remoteSort: false,
    fields: ['username',
    'firstname',
    'surname',
    'email',
    'isactive',
    'userid' ],
    proxy: new Ext.data.HttpProxy({
        url: baseUri+"?module=contextgroups&action=json_getlecturers"
    }),
    listeners:{
        'loadexception': function(theO, theN, response){
            alert(baseUri);
            alert(response.responseText);
        },
        'load': function(){
        //alert('load');
        }
    }
});
lecturerdata.setDefaultSort('surname', 'asc');
	 
// pluggable renders
function renderTitle(value, p, record){
    return String.format(
        '<b><a href="'+baseuri+'?module=context&action=joincontext&contextcode={1}">{0}</a></b>', value, record.data.code);
}

function renderIsActive(value, p, record)
{
    var isActive = record.data.isactive;
    var img = 'accept'
    if (isActive != 1){
        img = 'decline';
    }
	    
    return '<img src="skins/_common2/css/images/sexybuttons/icons/silk/'+img+'.png" border="0" />';
}
	
//the remove button
var rmButtonLecturer = new Ext.Button({
    text:'Remove User',
    tooltip:'Remove the selected User',
    iconCls:'silk-delete',
    id:'rmlecture',
    // Place a reference in the GridPanel
    ref: '../../removeButton',
    disabled: true,
    handler: function(){
        doRemoveUsers('lecturers');
    }
});	 
	 
// The toolbar for the user grid
var tBar = new Ext.Toolbar({
    items:[{
        text:'Add User',
        tooltip:'Add a User to this group',
        iconCls: 'silk-add',
        handler: function (){
            if(!win2){
                win2 = new Ext.Window({
	 		                
                    layout:'fit',
                    width:"60%",
                    height:350,
                    closeAction:'hide',
                    plain: true,
                    items: [usersGridPanel]
	 		                
                });
            }
            addtype = 'lecturers';
            win2.show(this);
            userStore.load({
                params:{
                    start:0,
                    limit:25
                }
            });
        }
    },rmButtonLecturer]
});
//the checkbox object for selecting all
var sm4 = new Ext.grid.CheckboxSelectionModel({
    listeners: {
        // On selection change, set enabled state of the removeButton
        // which was placed into the GridPanel using the ref config
        selectionchange: function(sm) {
            if (sm.getCount()) {
                rmButtonLecturer.enable();
            } else {
                rmButtonLecturer.disable();
            }
        }
    }
});

//the page navigation for the users in a group
var pageNavigation2 = new Ext.PagingToolbar({
    pageSize: 25,
    store: lecturerdata,
    displayInfo: true,
    displayMsg: 'Displaying Users {0} - {1} of {2}',
    emptyMsg: "No Users to display",
    listeners:{
        beforechange: function(ptb, params){
            userOffset = params.start;
            proxyStudentData.setUrl(baseUri+'?module=contextgroups&action=json_getstudents&limit='+params.start+'&offset='+params.start);
        }
    }
});
	
//lecturer grid
var lecturergrid = new Ext.grid.GridPanel({
    width:"100%",
    height:400,
    bbar: pageNavigation2,
    store: lecturerdata,
    trackMouseOver:false,
    disableSelection:true,
    loadMask: true,
    tbar: tBar,
    emptyText:"get out",
    sm: sm4,
    // grid columns
    cm: new Ext.grid.ColumnModel([
        sm4,
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
        }]),

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
    }
});
