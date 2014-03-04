var showMyGrid = function(myData) {


    // example of custom renderer function
    function change(val){
        if(val > 0){
            return '<span style="color:green;">' + val + '</span>';
        }else if(val < 0){
            return '<span style="color:red;">' + val + '</span>';
        }
        return val;
    }

    // example of custom renderer function
    function pctChange(val){
        if(val > 0){
            return '<span style="color:green;">' + val + '%</span>';
        }else if(val < 0){
            return '<span style="color:red;">' + val + '%</span>';
        }
        return val;
    }

    // create the data store
    var store = new Ext.data.ArrayStore({
        fields: [
           {name: 'firstName'},
           {name: 'surname'}
        ]
    });
    store.loadData(myData);

    // create the Grid
    var grid = new Ext.grid.GridPanel({
        store: store,
        columns: [
            {id:'firstName',header: "First Name", width: 160, sortable: true, dataIndex: 'firstName'},
            {header: "Surname", width: 150, sortable: true, dataIndex: 'surname'}
        ],
        stripeRows: true,
        autoExpandColumn: 'firstName',
        height:350,
        width:500,
        title:'Essay Members'
    });
    grid.render('grid-example');
}