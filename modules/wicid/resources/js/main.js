var url,
searchURL,
adminURL;
Ext.onReady(function() {
    url = Ext.get('uploadURL').dom.value;
    searchURL = Ext.get('searchURL').dom.value,
    adminURL = Ext.get('adminURL').dom.value;

    var p = new Ext.Panel({
        layout: 'table',
        autoWidth: true,
        style: 'marginRight: 10px',
        baseCls: 'x-plain',
        cls: 'btn-panel',
        border: false,
        defaultType: 'button',
        id: 'upload-button',
        items: [{
            text: 'Upload File',
            iconCls: 'add16',
            iconAlign: 'right',
            scale: 'medium',
            baseCls: 'x-plain',
            cls: 'btn-panel',
            handler: function() {
                goUploadPage();
            }
        },{
            text: 'Search',
            scale: 'medium',
            baseCls: 'x-plain',
            cls: 'btn-panel',
            handler: function() {
                goSearchPage();
            }
        },{
            text: 'Admin',
            scale: 'medium',
            baseCls: 'x-plain',
            cls: 'btn-panel',
            handler: function() {
                goAdminPage();
            }
        }]
    });

    p.render("buttons");
});

var goUploadPage = function() {
    window.location.href = url;
}

var goSearchPage = function() {
    window.location.href = searchURL;
}

var goAdminPage = function() {
    window.location.href = adminURL;
}