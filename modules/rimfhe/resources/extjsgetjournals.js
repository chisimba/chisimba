/*!
 * Ext JS Library 3.0.0
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
Ext.onReady(function(){
    var proxyuri = null;
				function updateHttpProxy (){
					var searchCategory = jQuery("#input_category").val();
					var searchText = jQuery("#input_journalname2").val();
				 proxyuri = uri+'&query='+searchText;
				 dataStoreParts.proxy = new Ext.data.HttpProxy({
				  //url: proxyuri
				  url: uri, method: 'GET'//, params: {query:jQuery("#input_journalname2").val(), journalcat:jQuery("#input_category").val()}
				 });
				}    
    var dataRecordParts = new Ext.data.Record.create([  
     {name: 'jid'},  
     {name: 'jname'}  
    ]); 
    var dataReaderParts = new Ext.data.JsonReader({  
      root: 'searchresults',
      totalProperty: 'journalcount',
      id: 'jid'
     },
     dataRecordParts  
    );      
    var dataProxyParts = new Ext.data.HttpProxy({  
     url: uri//, method: 'GET'
    });
    
    var dataStoreParts = new Ext.data.Store({
        /*autoLoad:true,
        listeners:{ 
    		'loadexception': function(theO, theN, response){
							var searchCategory = jQuery("#input_category").val();
							var searchText = jQuery("#input_journalname2").val();
							//var fulluri = uri+'&journal='+searchText+'&journcatid='+searchCategory;
    		 },
    		'load': function(){
							 var searchCategory = jQuery("#input_category").val();
							 var searchText = jQuery("#input_journalname2").val();
							 //uri = uri+'&myjournal='+searchText+'&myjourncatid='+searchCategory;
        //alert(searchText);
    			}
    	},
    	proxy: new Ext.data.HttpProxy({
    	   url: uri, method: 'GET'
        //url: uri, method: 'POST', params: {query:jQuery("#input_journalname2").val(), journalcat:jQuery("#input_category").val()}
     }),*/
     //baseParams = {query:'Accounting', journalcat:'searchCategory'},
     proxy: dataProxyParts,
     reader: dataReaderParts
    });
    //dataStoreParts.load = {params:{query:'searchText'}};
    //dataStoreParts.baseParams = {query:jQuery("#input_journalname2").val(), journalcat:jQuery("#input_category").val()};
    //dataStoreParts.on("reload", updateHttpProxy);
    // Custom rendering Template
    var resultTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
            '{jname}',
        '</div></tpl>'
    );
    
    var jsearch = new Ext.form.ComboBox({
        store: dataStoreParts,
        //id: 'jsearchid',
        displayField:'jname',
        //hiddenName: 'journal',
        //textField: 'jname',
        typeAhead: false,
        emptyText: 'Start typing...',
        loadingText: 'Searching...',
        width: 352,
        pageSize:10,
        hideTrigger:false,
        tpl: resultTpl,
        //valueField: 'jid',
        applyTo: 'input_journalname2',
        itemSelector: 'div.search-item',
        onSelect: function(record){ // override default onSelect to do redirect]
            var currentData = record.data.jname;
            jQuery("input[id='input_journalname']").val(record.data.jname);
            //jQuery("input[id='input_journalname2']").val(record.data.titlej);
            this.collapse();
        }        
    });
});
