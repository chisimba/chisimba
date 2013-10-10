
<?php
$extbase = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/adapter/ext/ext-base.js','htmlelements').'" type="text/javascript"></script>';
$extalljs = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ext-all.js','htmlelements').'" type="text/javascript"></script>';
$extallcss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('ext-3.0-rc2/resources/css/ext-all.css','htmlelements').'"/>';

$maincss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/simpleregistration.css').'"/>';
$searchfieldjs = '<script language="JavaScript" src="'.$this->getResourceUri('js/SearchField.js').'" type="text/javascript"></script>';

$styleSheet="
   <style type=\"text/css\">
.search-item {
    font:normal 11px tahoma, arial, helvetica, sans-serif;
    padding:3px 10px 3px 10px;
    border:1px solid #fff;
    border-bottom:1px solid black;
    white-space:normal;
    color:#555;
    background:white
}
.search-item h3 {
    display:block;
    font:inherit;
    font-weight:bold;
    color:#222;

}

.search-item h3 span {
    float: right;
    font-weight:normal;
    margin:0 0 5px 5px;
    width:100px;
    display:block;
    clear:none;

}
        #search-results a {
            color: #385F95;
            font:bold 11px tahoma, arial, helvetica, sans-serif;
            text-decoration:none;
        }
        #search-results a:hover {
            text-decoration:underline;
        }
        #search-results .search-item {
            padding:5px;
        }
        #search-results p {
            margin:3px !important;
        }
        #search-results {
            border-bottom:1px solid #ddd;
            margin: 0 1px;
            height:300px;
            overflow:auto;
           background:#ffffff;
        }
        #search-results .x-toolbar {
            border:0 none;
        }
    </style>

    ";

$this->appendArrayVar('headerParams', $extbase);
$this->appendArrayVar('headerParams', $extalljs);
$this->appendArrayVar('headerParams', $extallcss);
$this->appendArrayVar('headerParams', $maincss);
$this->appendArrayVar('headerParams', $styleSheet);
$this->appendArrayVar('headerParams', $gridsearchjs);
$this->appendArrayVar('headerParams', $searchfieldjs);

$table=$this->getObject('htmltable','htmlelements');
$table->cellpadding = 5;
$table->cellpadding = 5;
$regformObj = $this->getObject('formmanager');
$objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
$allowExternalReg=$objSysConfig->getValue('ALLOW_EXTERNAL_REG', 'simpleregistration');
$eventcontent=array();

if(count($content) > 0){
    $eventcontent=$content[0];
}else{

}

$objWashout = $this->getObject('washout', 'utilities');
$title1=$objWashout->parseText($eventcontent['event_lefttitle1']);
$title2=$objWashout->parseText($eventcontent['event_lefttitle2']);
$footer=$objWashout->parseText($eventcontent['event_footer']);
$timevenue=$objWashout->parseText($eventcontent['event_timevenue']);


$programLink =new link($this->uri(array('action'=>'expresssignin')));
$programLink->link= '<h3>The Program</h3>';


$table->startRow();
$table->addCell($leftTitle);
$table->addCell($rightTitle);
$table->endRow();

$table->startRow();
$table->addCell('');
$table->endRow();

$content=$eventcontent['event_content'];
$pagecontent= $objWashout->parseText($content);
$disqus=$this->getObject('disquselems','disqus');

$table->startRow();
$table->addCell($pagecontent);
$table->addCell($disqus->addWidget());
$table->endRow();

$admin = new link ($this->uri(array('action'=>'memberlist','shortname'=>$shortname)));
$admin->link= $this->objLanguage->languageText('mod_simpleregistration_admin', 'simpleregistration');

$mainjs="

    var ds = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url:location.href+\"?module=simpleregistration&action=searchcomments\"
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
        '<div class=\"search-item\">',
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
            emptyMsg: \"No  comments to display\"
        })
    });

    ds.load({
        params:{
            start:0,
            limit:10,
            userId: 1
        }
});

";
if(count($content) > 0){
$content= '<div id="wrap">'.$table->show().'   </div>';
$content.= "<script type=\"text/javascript\">".$mainjs."</script>";

echo $content;
}else{
    echo '<font color="red"><h1>No conference with the shortname suggested exist</h1></font>';
}

?>
