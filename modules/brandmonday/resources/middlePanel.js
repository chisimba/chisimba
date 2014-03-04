


var BrandPanel = new Ext.Panel({
	//width: 950,
    height: 400,
    plain:true,
	layout:'border',	
    items:[BrandPlus, BrandMinus]
});

var MentionsPanel = new Ext.Panel({
	//width: '90%',
    height: 400,
    plain:true,
	layout:'border',	
    items:[MentionsSubPanel]
});

var AwardsPanel = new Ext.Panel({
	//width: '90%',
    height: 400,
    plain:true,
	layout:'border',	
    items:[Awards]
});

var middlePanel = new Ext.TabPanel({
	//title: "#BrandMonday: Tweet about Brands on Mondays",
    region: 'center',
    plain:true,
    
    margins:'0 20px 0 20px',
	width: '75%',
	activeTab: 0,
	border:false,	
	//padding: '5px',
	frame:true,
	
	defaults:{
		autoScroll: false,
		border:false,
		plain:true
	},
	//autoScroll: true,
	loadMask: true,
	items:[{
                title: 'Brands',  
                items: BrandPanel
            },
            {
                title: 'Mentions',
                items: MentionsPanel
            },
            {
                title: 'Awards',                  
                items: AwardsPanel,
                autoScroll:false
            }]
	
	
});