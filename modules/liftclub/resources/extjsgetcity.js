/*!
 * Ext JS Library 3.0.0
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
Ext.onReady(function(){  
    var dataStoreParts = new Ext.data.Store({
    proxy: new Ext.data.HttpProxy({  
     url:uri,
     method:'GET'
    }),
    reader: new Ext.data.JsonReader({
      root:'searchresults'
     },[
      {name:'id', mapping:'id' },
      {name:'city', mapping:'city' }
     ])
    });
    var resultTpl = new Ext.XTemplate('<tpl for="."><div class="search-item">','{city}','</div></tpl>');
         
    var jsearch2 = new Ext.form.ComboBox({
        store: dataStoreParts,
        displayField:'city',
        typeAhead: false,
        emptyText: 'Start typing (at least 4 letters)...',
        loadingText: 'Searching...',
        width: 352,
        pageSize:10,
        hideTrigger:false,
        hiddenName: 'query3',
        triggerAction: 'all',
        tpl: resultTpl,
        applyTo: 'input_citytownb',
        itemSelector: 'div.search-item',
        onSelect: function(record){
            jQuery("input[id='input_citytown']").val(record.data.id);
            jQuery("input[id='input_citytowna']").val(record.data.city);
            this.collapse();
        }
    });
});
