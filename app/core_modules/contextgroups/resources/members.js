var myMask = new Ext.LoadMask(Ext.getBody(), {
    msg:"Please wait..."
});
var pageSize = 25;
var userOffset = 0;

Ext.onReady(function(){
    tabs.render();    
    lecturerdata.load();
    studentdata.load();	
});
    
// basic tabs 1, built from existing content
var tabs = new Ext.TabPanel({
    el: 'memberbrowser',
    width:"100%",
    activeTab: 0,
    plain:true,
    frame:true,
    defaults:{
        autoHeight: true
    },
    items:[
    {
        //html:' other courses goes here',
        items: [lecturergrid],
        itemId: 'my_courses',
        title: lang["lecturers"]
    },{
        //html:' other courses goes here',
        items:[studentgrid],       
        title: lang["students"]
    },{
        html:'Import users goes here',
        //items:[studentgrid],       
        title: 'Import Users'
    }
    ]
});
