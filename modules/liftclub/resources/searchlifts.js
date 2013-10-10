/*
 * Ext JS Library 3.0 RC2
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 * By Paul Mungai
 * Searching Plugin: Paul Mungai
 * wandopm@gmail.com
 */
Ext.onReady(function(){
var liftdata = new Ext.data.JsonStore({
        root: 'searchresults',
        totalProperty: 'liftcount',
        idProperty: 'detid',
        remoteSort: false,        
        fields: ['detid', 'orid', 'desid', 'detuserid', 'times','additionalinfo', 'specialoffer', 'emailnotifications', 'userneed','needtype', 'daterequired', 'createdormodified', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'selectedays', 'oriuserid', 'oristreet', 'orisuburb', 'desuserid', 'destreet', 'desuburb'],
        proxy: new Ext.data.HttpProxy({ 
            	url: uri
        }),
        listeners:{ 
    	 'loadexception': function(theO, theN, response){
    	 },
    	 'load': function(){
    	  //alert('load');	
    	 }
    	}
	});
	liftdata.setDefaultSort('createdormodified','asc');
 // pluggable renders
 function renderTitle(value, p, record){
     return String.format('<b><a href="'+baseuri+'?module=liftclub&action=viewlift&liftuserid={0}">View</a></b>', record.data.detuserid, record.data.orisuburb, record.data.desuburb);
 }
 function renderDetails(record){
     return String.format('<p>{0} ( {1} )<br />'+lang['wordcreated']+': {2}<br /> {3}{4} {5}</p>', record.data.needtype, record.data.userneed, record.data.createdormodified, record.data.selectedays, record.data.daterequired, record.data.times);
 }
    var grid = new Ext.grid.GridPanel({
        el:'find-grid',
        width:'100%',
        height:400,
        title:liftitle,
        store: liftdata,
        trackMouseOver:false,
        disableSelection:true,
        loadMask: true,
        columns:[
        {
            header: lang['triporigin'],
            dataIndex: 'orisuburb',
            width: 135,
            hidden: false,
            sortable: true
        },{
            header: lang["tripdestiny"],
            dataIndex: 'desuburb',
            width: 135,
            hidden: false,
            sortable: true
        },{
            header: lang["needtype"],
            dataIndex: 'needtype',
            width: 60,
            hidden: false,
            sortable: true
        },{
            header: lang["tripdays"],
            dataIndex: 'selectedays',
            width: 300,
            hidden: false,
            sortable: true
        },{
            id: 'detuserid',
            header: lang["wordview"],
            dataIndex: 'detuserid',
            width: 60,
            renderer: renderTitle,            
            hidden: false,
            sortable: true
        }],
        // customize view config
        viewConfig: {
            forceFit:true,
            enableRowBody:true,
            showPreview:false,
            getRowClass : function(record, rowIndex, p, store){
                if(this.showPreview){
                //<p>{0} ( {1} )<br />Created: {2}<br /> {3}{4} {5}</p>', record.data.needtype, record.data.userneed, record.data.createdormodified, record.data.selectedays, record.data.daterequired, record.data.times
                 if(record.data.daterequired == null){
                    p.body = '<p>'+record.data.needtype+' ('+record.data.userneed+')<br />'+lang["datecreated"]+' '+lang["wordcreated"]+': '+record.data.createdormodified+'<br />'+record.data.selectedays+' '+record.data.times+' </p>';
                 }else{
                    p.body = '<p>'+record.data.needtype+' ('+record.data.userneed+')<br />'+lang["datecreated"]+' '+lang["wordcreated"]+': '+record.data.createdormodified+'<br />'+record.data.daterequired+' '+record.data.times+' </p>';
                 }   
                    return 'x-grid3-row-expanded';
                }
                return 'x-grid3-row-collapsed';
            }
        },
	plugins:[new Ext.ux.grid.Search({
	 iconCls:'zoom'
	 ,disableIndexes:['detuserid','createdormodified','selectedays','userneed']
	 ,minChars:1
	 ,autoFocus:true
	})],
        bbar: new Ext.PagingToolbar({
            pageSize: pageSize,
            store: liftdata,
            displayInfo: true,
            displayMsg: lang["displayingpage"]+' {0} '+lang["wordof"]+' {1}',
            emptyMsg: lang["noliftstodisplay"]
        })
    });
    // render it
    grid.render();

    // trigger the data store load
    liftdata.load({params:{start:0, limit:15, usrneed:usrneed}});
});
