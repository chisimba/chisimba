/*!
 * Ext JS Library 3.0+
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
Ext.onReady(function(){

    var ds = new Ext.data.Store(
    {
        listeners:{ 
    		'loadexception': function(theO, theN, response){
							var searchCategory = jQuery("#input_category").val();
							var searchText = jQuery("#input_journalname2").val();
							var fulluri = uri+'&journal='+searchCategory+'&journcatid='+searchCategory;
    			//alert(fulluri);
    		 },
    		'load': function(){
							var searchCategory = jQuery("#input_category").val();
							var searchText = jQuery("#input_journalname2").val();
							var fulluri = uri+'&journal='+searchCategory+'&journcatid='+searchCategory;
    				//alert('load');	
    			}
    	},
        proxy: new Ext.data.ScriptTagProxy({
            url: 'http://extjs.com/forum/topics-remote.php'
        }),
        reader: new Ext.data.JsonReader({
            root: 'topics',
            totalProperty: 'totalCount',
            id: 'post_id'
        }, [
            {name: 'titlej', mapping: 'topic_title'},
            {name: 'topicId', mapping: 'topic_id'},
            {name: 'author', mapping: 'author'},
            {name: 'lastPost', mapping: 'post_time', type: 'date', dateFormat: 'timestamp'},
            {name: 'excerpt', mapping: 'post_text'}
        ])
    });

    // Custom rendering Template
    var resultTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
            '{titlej}',
        '</div></tpl>'
    );
    
    var jsearch = new Ext.form.ComboBox({
            store: ds,
        displayField:'titlej',
        typeAhead: false,
        emptyText: 'Start typing...',
        loadingText: 'Searching...',
        width: 350,
        pageSize:10,
        hideTrigger:false,
        tpl: resultTpl,
        applyTo: 'input_journalname2',
        itemSelector: 'div.search-item',
        onSelect: function(record){ // override default onSelect to do redirect]
            var currentData = record.data.titlej;
            jQuery("input[id='input_journalname']").val(record.data.titlej);
            //jQuery("input[id='input_journalname2']").val(record.data.titlej);
            this.collapse();
        }        
    });    
});
