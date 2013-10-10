

var filtername;
var filterTag;
var filterInstructions;
var filterType;
var filterParamName;
var filterParamValue;
var filterInputValue;
var defaultValue;
var preinputValue;

var conn = new Ext.data.Connection();

function initContextTools(url,contexturl,filtersurl,baseurl,storyurl,inputurl){

    var tabs = new Ext.TabPanel({
        activeTab:0,
        renderTo: 'contexttools',
        frame:false,
        defaults:{
            autoHeight: true
        },
        items:[
        {
            contentEl:'contextlist',
            title: 'Contexts'
        },

        {
            contentEl:'filterlist',
            title: 'Filters'
        }
        ]
    });

    var contextreader=new Ext.data.JsonReader({
        root: "usercourses",
        id: "id"
    },[
    {
        name:'code'
    },

    {
        name:'title'
    }
        
    ]);
    
    var contextds = new Ext.data.Store({
        autoLoad:true,
        proxy: new Ext.data.HttpProxy({
            url: url,
            method: 'GET'
        }),
        reader: contextreader
    });


    var contextlistfield = new Ext.form.ComboBox({
        store: contextds,
        displayField:'title',
        fieldLabel:'Context links',
        typeAhead: true,
        mode: 'local',
        editable:false,
        forceSelection: true,
        triggerAction: 'all',
        emptyText:'Select context...',
        selectOnFocus:true,
        valueField:'code',
        hiddenName : 'contextlistfield'

    });

    var contextlistform = new Ext.form.FormPanel({

        baseCls: 'x-plain',
        width:550,
        labelWidth: 135,
        bodyStyle:'margin-left:2em;margin-top:2em;margin-bottom:2em;background-color:transparent;',
        renderTo: 'contextlist',
        collapsible: true,
        buttonAlign: 'left',
        border:false,
        items:[
        {
            xtype: 'fieldset',
            title: 'Context Links',
            autoHeight: true,
            bodyStyle:'margin-top:2em;margin-bottom:2em;',
            layout:'column',
            width:500,
            items: [
            contextlistfield

            ]
        }],


        buttons: [
        {
            iconCls: 'insert',
            text:'Insert',

            handler: function(){
                var contextcode=contextlistfield.value;
                var selectedText="";
                if(CKEDITOR.env.ie)
                {
                    CKEDITOR.instances[instancename].getSelection().unlock(true);
                    selectedText = CKEDITOR.instances[instancename].getSelection().getNative().createRange().text;
                }
                else
                {
                    selectedText=window.opener.CKEDITOR.instances[instancename].getSelection().getNative();
                }
                var link='<a href="'+contexturl+"&contextcode="+contextcode+'">'+selectedText+'</a>';
                window.opener.CKEDITOR.instances[instancename].insertHtml(link);
                window.close();
            }
        },        {
            iconCls: 'cancel',
            text:'Cancel',

            handler: function(){
                window.close();
            }
        }

        ]

    });

    var filtersreader=new Ext.data.JsonReader({
        root: "filters",
        id: "id4"
    },
    [
    {
        name:'name'
    },

    {
        name:'type'
    },
    {
        name:'label'
    },
    {
        name:'instructions'
    },
    {
        name:'tag'
    },
    {
        name:'defaultvalue'
    },{
        name:'preinputvalue'
    }
    ]);

    var filterparamsreader=new Ext.data.JsonReader({
        root: "params",
        id: "id4"
    },
    [
    {
        name:'name'
    },

    {
        name:'value'
    }
    ]);
    var filtersds = new Ext.data.Store({
        autoLoad:true,
        proxy: new Ext.data.HttpProxy({
            url: filtersurl,
            method: 'GET'
        }),
        reader: filtersreader
    });
    var paramsProxy=new Ext.data.HttpProxy({
        url: baseurl,
        method: 'GET'
    });
    var filterparamssds = new Ext.data.Store({
        autoLoad:false,
        proxy: paramsProxy,
        reader: filterparamsreader
    });
    var instructionsfield=new Ext.form.DisplayField(
    {
       
        }
        );


    var filterlistfield = new Ext.form.ComboBox({
        store: filtersds,
        displayField:'label',
        fieldLabel:'Filter list',
        typeAhead: true,
        mode: 'local',
        width: 250,
        editable:false,
        forceSelection: true,
        triggerAction: 'all',
        emptyText:'Select filter...',
        selectOnFocus:true,
        valueField:'filtername',
        hiddenName : 'filterlistfield',
        listeners:{
            select: function(combo, record, index){
                filterTag= record.data.tag;
                filterInstructions=record.data.instructions;
                filterType=record.data.type;
                filtername=record.data.name;
                defaultValue=record.data.defaultvalue;
                preinputValue=record.data.preinputvalue;
                instructionsfield.setValue('<h4>'+filterInstructions+'</h4>' );
                filterparamssds.proxy=new Ext.data.HttpProxy({
                    url: baseurl+"&filtername="+filtername,
                    method: 'GET'
                });

                Ext.getCmp('xparamlistfield').disabled=false;
                filterparamssds.load();
                if(filterType=='basicinput' || filterType == 'directpasteinput'
                    || filterType == 'pasteinput_singletag'){
                    conn.request({
                        url: inputurl+"&filtername="+filtername,
                        method: 'GET',
                        success: function(responseObject) {

                            showInputForm(responseObject.responseText);
                        }
                    });
                }
                if(filterType=='parametizedall'){
                    Ext.getCmp('xparamlistfield').disabled=true;
                    Ext.MessageBox.alert('Instructions', filterInstructions);
                    var paramList="";
                    var filter="";
                    filterparamssds.each(function(r) {
                        paramList+= r.data['name']+"="+r.data['value']+",";

                    });
                    if(paramList.endsWith(",")){
                        paramList=paramList.substring(0,paramList.length-1);
                    }
                    filter='['+filterTag+':'+paramList+']' +selectedText+'[/'+filterTag+']';
                    window.opener.CKEDITOR.instances[instancename].insertHtml(filter);
                    window.close();
                }

            }

        }

    });


    var filterparamfield = new Ext.form.ComboBox({
        store: filterparamssds,
        displayField:'value',
        fieldLabel:'Parameters list',
        typeAhead: true,
        mode: 'local',
        editable:false,
        width: 250,
        forceSelection: true,
        triggerAction: 'all',
        emptyText:'Select parameter...',
        selectOnFocus:true,
        valueField:'name',
        hiddenName : 'paramlistfield',
        id:"xparamlistfield",
        listeners:{
            select: function(combo, record, index){
                var xprm=record.data.name;
                var prm=xprm.split(":");
                if(prm.length > 0){
                    filterParamName=prm[0];
                }else{
                    filterParamName=xprm;
                }
                filterParamValue=record.data.value;
                
            }

        }

    });



    var filterlistform = new Ext.form.FormPanel({
        baseCls: 'x-plain',
        width:550,
        bodyStyle:'margin-left:2em;margin-top:2em;margin-bottom:2em;background-color:transparent;',
        renderTo: 'filterlist',
        collapsible: false,
        buttonAlign: 'right',
        border:false,
        items:[
       
        {
            html:'<b><font color="red">NOTE: The filter will modify the text selected in the editor</font></b>',
            border:false,
            bodyStyle:'margin-bottom:3em'
        }
        ,
              
        {
            xtype: 'fieldset',
            title: 'Filters',
            autoHeight: true,
            width:500,
            items:[
            filterlistfield,
            filterparamfield,
            instructionsfield
            ]
        }
         
        ],


        buttons: [
        {
            iconCls: 'insert',
            text:'Insert filter',
            id:'insertFilterButton',

            handler: function(){
                
                var selectedText="";

                if(CKEDITOR.env.ie)
                {
                    CKEDITOR.instances[instancename].getSelection().unlock(true);
                   
                    selectedText = CKEDITOR.instances[instancename].getSelection().getNative().createRange().text;
                }else{
                    selectedText=  window.opener.CKEDITOR.instances[instancename].getSelection().getNative();
                }
                var filter='';
                if(filterType=='parametized'){
                    filter='['+filterTag+':'+filterParamName+'='+filterParamValue+']' +selectedText+'[/'+filterTag+']';
                }else if(filterType=='parametizedall'){

                    paramList="";
                    filterparamssds.each(function(r) {
                        paramList+= r.data['name']+"="+r.data['value']+",";

                    });
                    if(paramList.endsWith(",")){
                        paramList=paramList.substring(0,paramList.length-1);
                    }
                    filter='['+filterTag+':'+paramList+']' +selectedText+'[/'+filterTag+']';
                    window.opener.CKEDITOR.instances[instancename].insertHtml(filter);
                    window.close();


                }else if(filterType=='basicinput'){
                    filter='['+filterTag+']' +filterInputValue+'[/'+filterTag+']';
                }
                else{
                    filter='['+filterTag+']' +selectedText+'[/'+filterTag+']';
                }
                window.opener.CKEDITOR.instances[instancename].insertHtml(filter);
                window.close();
            }
        },        {
            iconCls: 'cancel',
            text:'Cancel',

            handler: function(){
                window.close();
            }
        }

        ]

    });

  
   
}

function showInputForm(xparams){

    var params=xparams.split("#");
  
    for(i=0;i<params.length;i++){
        var xparam=params[i];
        var param=xparam.split(":");
        if(param.length > 0){
            var label=param[1];
            label=label.trim();
            if(label != ''){
                Ext.MessageBox.prompt(filtername, label, showResult);
            }
        }
    }
}

function showResult(btn,text){
    if(btn == 'ok'){
        filterInputValue=text;
        filter='['+filterTag+']' +filterInputValue+'[/'+filterTag+']';
        if(filterType == 'directpasteinput'){
            filter=filterInputValue;
        }
        if(filterType == 'pasteinput_singletag'){
            filter='['+filterTag+': '+preinputValue+filterInputValue+', '+defaultValue+"]";
        }
        window.opener.CKEDITOR.instances[instancename].insertHtml(filter);
        window.close();
    }else{
        filterInputValue=null;
    }
}
