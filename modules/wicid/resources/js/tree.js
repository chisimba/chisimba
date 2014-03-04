/*
var showLatestUploads = function(url) {

    var tree = new Ext.ux.tree.ColumnTree({
        autoWidth: true,
        autoHeight: true,
        rootVisible:false,
        autoScroll:true,
        title: 'Last 10 Uploads',
        renderTo: 'recent-uploads',

        columns:[{
            id: 'test',
            header:'Filename',
            width: 250,
            dataIndex:'filename'
        },{
            header:'File Type',
            width: 80,
            dataIndex:'duration'
        },{
            header:'View Details',
            width: 125,
            dataIndex:'details'
        }, {
            header:'Date Last Modified',
            width: 100,
            dataIndex:'modified'
        }, {
            header:'Status',
            width: 50,
            dataIndex:'status'
        }],
        loader: new Ext.tree.TreeLoader({
            dataUrl: url,
            uiProviders:{
                'col': Ext.ux.tree.ColumnNodeUI
            }
        }),
        contextMenu: new Ext.menu.Menu({
            items: [{
                id: 'select-node',
                text: 'Go to Next Page'
            }],
            listeners: {
                itemclick: function(item) {
                    switch (item.id) {
                        case 'select-node':
                            var n = item.parentMenu.contextNode;
                            if (n.parentNode) {
                                deleteNode(n.parentNode);
                                //Ext.Msg.alert("HELLO WORLD", n.text);
                            }
                            break;
                    }
                }
            }
        }),
        listeners: {
            dblclick: function(node, event) {
                Ext.Msg.alert('Navigation Tree Click', 'You Double clicked: ' + node.attributes.text);
            },
            contextmenu: function(node, e) {
                node.select();
                var c = node.getOwnerTree().contextMenu;
                c.contextNode = node;
                c.showAt(e.getXY());
            }
        },
        root: new Ext.tree.AsyncTreeNode({
            text:'Filename'
        })
    });
}*/