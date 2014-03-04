/*!
 * Ext JS Library 3.0.0
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
var panel;
var courseId;
var sendProposalUrl;

function showComments(){
    var args = showSearchWinX.arguments;
    courseId=args[0];
    sendProposalUrl=args[1];
    
    var ds = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url:location.href+"?module=simpleregistration&action=searchcomments"
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'totalCount',
            id: 'userid'
        }, [
        {
            name: 'userId',
            mapping: 'userid'
        },

        {
            name: 'firstname',
            mapping: 'firstname'
        },

        {
            name: 'lastname',
            mapping: 'lastname'
        }
        
        ]),

        baseParams: {
            limit:20,
            userId: 1
        }
    });

    // Custom rendering Template for the View
    var resultTpl = new Ext.XTemplate(
        '<tpl for=".">',
        '<div class="search-item">',
        '<h3>{firstname} {lastname}</h3>',
        '<p><spane>{comment}</span></p>',
         
        '</div></tpl>'
        );

    panel = new Ext.Panel({
         renderTo:'commentssurface',
        height:300,
        autoScroll:true,
        bodyCssClass:  'search-item',
        items: new Ext.DataView({
            tpl: resultTpl,
            store: ds,
            itemSelector: 'div.search-item'
        }),

        tbar: [
        'Search: ', ' ',
        new Ext.ux.form.SearchField({
            store: ds,
            width:320
        })
        ],

        bbar: new Ext.PagingToolbar({
            store: ds,
            pageSize: 21,
            displayInfo: true,
            displayMsg: 'User {0} - {1} of {2}',
            emptyMsg: "No  comments to display"
        })
    });

    ds.load({
        params:{
            start:0,
            limit:10,
            userId: 1
        }
        });

}

function forwardProposal(){

    var args=forwardProposal.arguments;
    var url=args[0];
    var email=args[1];
    var courseid=args[2];


  Ext.MessageBox.confirm('Forward Proposal?', 'Are you sure you want to forward the proposal to '+email+'?', function(btn){

  if (btn == 'yes') {
    window.location.href='?module=ads&action=sendproposal'+'&email='+email+'&courseid='+courseid;
  }


});
}

function forwardProposalToModerator(){
  var args=forwardProposalToModerator.arguments;
  var courseid=args[0];
  Ext.MessageBox.confirm('Forward Proposal?', 'Are you sure you want to forward the proposal to moderator?', function(btn){
  if (btn == 'yes') {
    window.location.href='?module=ads&action=sendproposaltomoderator&courseid='+courseid;
  }
});
}

