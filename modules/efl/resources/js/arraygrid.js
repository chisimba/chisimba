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
           {name: 'title'},
           {name: 'lastChange'}
        ]
    });
    store.loadData(myData);

    // create the Grid
    var grid = new Ext.grid.GridPanel({
        store: store,
        columns: [
            {id:'title',header: "Title", width: 160, sortable: true, dataIndex: 'title'},
            {header: "Submit Date", width: 150, sortable: true, dataIndex: 'lastChange'}
        ],
        stripeRows: true,
        autoExpandColumn: 'title',
        height:350,
        width:600,
        title:'Submition of Essays'
    });
    grid.render('grid-example');
}