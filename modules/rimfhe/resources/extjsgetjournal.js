/*!
 * Ext JS Library 3.0.0
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
Ext.onReady(function(){  
    var dataStoreParts = new Ext.data.Store({
     listeners:{ 
    		'loadexception': function(theO, theN, response){
							 var searchCategory = jQuery("#input_category").val();
    		},
    		'load': function(){
							 var searchCategory = jQuery("#input_category").val();
    		}
    	},
     proxy: new Ext.data.HttpProxy({  
     url: uri, method: 'GET'
    }),
     reader: new Ext.data.JsonReader({  
      root: 'searchresults',
     },[  
      {name: 'jid', mapping :'jid' },  
      {name: 'jname', mapping : 'jname' }  
     ])
    });
    var resultTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
            '{jname}',
        '</div></tpl>'
    );
    
    var jsearch = new Ext.form.ComboBox({
        store: dataStoreParts,
        displayField:'jname',
        typeAhead: false,
        emptyText: 'Start typing (at least 4 letters)...',
        loadingText: 'Searching...',
        width: 372,
        pageSize:10,
        hideTrigger:false,
        hiddenName: 'query2',
        triggerAction: 'all',
        tpl: resultTpl,
        applyTo: 'input_journalname2',
        itemSelector: 'div.search-item',
        onSelect: function(record){ // override default onSelect to do redirect]
            jQuery("input[id='input_journalname']").val(record.data.jid);
            jQuery("input[id='input_journalname1']").val(record.data.jname);
            this.collapse();
        }        
    });
});
