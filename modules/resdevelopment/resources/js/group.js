var win;

function showGrid(typeURL, myData) {
    // buttons
    var p = new Ext.Panel({
        layout: 'table',
        autoWidth: true,
        style: 'marginRight: 10px',
        baseCls: 'x-plain',
        cls: 'btn-panel',
        border: false,
        defaultType: 'button',
        id: 'myButtons',
        items: [{
            text: 'Add Group',
            scale: 'medium',
            baseCls: 'x-plain',
            cls: 'btn-panel',
            handler: function() {
                editGroup(typeURL);
            }
        }]
    });
    p.render('buttons');

    // create the data store
    var store = new Ext.data.ArrayStore({
        fields: [
           {name: 'name'},
           {name: 'edit'},
           {name: 'delete'},
           {name: 'date'},

        ]
    });

    // manually load local data
    store.loadData(myData);

    // create the Grid
    var grid = new Ext.grid.GridPanel({
        store: store,
        columns: [
            {id:'name',header: 'Name', width: 160, sortable: true},
            {header: 'Edit', width: 75, sortable: true},
            {header: 'Delete', width: 75, sortable: true},
            {header: 'Date', width:75, sortable: true}
        ],
        stripeRows: true,
        autoExpandColumn: 'name',
        height: 350,
        width: 600,
        title: 'Array Grid'
    });

    // render the grid to the specified div in the page
    grid.render('grid-example');
}

function editGroup() {
    typeURL = arguments[0];
    myID = arguments[1];
    var groupName=arguments[2];
    btnText = 'Add';
    if(typeof(myID) != "undefined") {
        typeURL = typeURL + "&id=" + myID;
        btnText = 'Edit';
    }

    var fileTypeEditForm = [{
        fieldLabel: 'Group Name',
        name: 'groupname',
        value:groupName,
        id: 'groupname_title',
        allowBlank: false,
        width: 250
    }];

    var myForm = new Ext.FormPanel({
        standardSubmit: true,
        labelWidth: 125,
        url: typeURL,
        frame:true,
        bodyStyle:'padding:5px 5px 0',
        width: 350,
        defaults: {width: 230},
        defaultType: 'textfield',
        items: fileTypeEditForm
    });

    if(!win){
        win = new Ext.Window({
            applyTo:'addtype-win',
            layout:'fit',
            width: 500,
            height: 200,
            x: 100,
            y: 100,
            closeAction:'hide',
            plain: true,
            items: myForm,

            buttons: [{
                text: btnText,
                handler: function(){
                    if (myForm.url)
                        myForm.getForm().getEl().dom.action = myForm.url;

                    myForm.getForm().submit();
                }
            },{
                text: 'Cancel',
                handler: function(){
                    win.hide();
                }
            }]
        });
        
    }
    win.show(this);
}

function goEdit(typeURL, myID,groupName) {
    editGroup(typeURL, myID,groupName);
}

function goDelete(myURL) {
    Ext.Msg.confirm('Confirmation Of Group Deletion', 'Are you sure you want to delete this group?', function(btn){
        if (btn == 'yes'){
            window.location.href = myURL;
        }
    });
}