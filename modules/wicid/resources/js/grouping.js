var showLatestUploads = function(gridData) {
    var xg = Ext.grid;

    // shared reader
    var reader = new Ext.data.ArrayReader({}, [
       {name: 'filename'},
       {name: 'status'},
       {name: 'lastChange'},
       {name: 'filetype'},
       {name: 'filetypedesc'},
       {name: 'details'}
    ]);

    var grid = new xg.GridPanel({
        store: new Ext.data.GroupingStore({
            reader: reader,
            data: gridData,
            sortInfo:{field: 'filename', direction: "ASC"},
            groupField:'filetype'
        }),

        columns: [
            {id:'filename',header: "Filename", width: 40, sortable: true, dataIndex: 'filename'},
            {header: "Status", width: 10, sortable: true, dataIndex: 'status'},
            {header: "Date Last Modified", width: 10, sortable: true, dataIndex: 'lastChange'},
            {header: "FT", width: 10, sortable: true, dataIndex: 'filetype'},
            {header: "File Type Description", width: 15, sortable: true, dataIndex: 'filetypedesc'},
            {header: "Details", width: 15, sortable: true, dataIndex: 'details'}
        ],

        view: new Ext.grid.GroupingView({
            forceFit:true,
            groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Items" : "Item"]})',
            startCollapsed: true,
            hideGroupedColumn: true
        }),

        frame:false,
        width: "100%",

        height: 450,
        border:false,
        collapsible: true,
        animCollapse: false,
        //title: 'Last 10 Uploaded files',
        renderTo: 'recent-uploads'
    });
}