<?php
//Set up the CSS Layout
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);





//Insert the left column content
$desc = $this->objLanguage->languageText("help_sysconfig_about",'sysconfig');
$desc = "<div class='sysconfig_left'>$desc</div>";
$cssLayout->setLeftColumnContent($desc);
unset($desc);



//Create the navigation table for config elements
$table = $this->newObject('htmltable', 'htmlelements');
$table->cellpadding = 5;
$table->startHeaderRow();
$table->addHeaderCell('&nbsp;', 20);
$table->addHeaderCell($this->objLanguage->languageText("mod_sysconfig_module",'sysconfig'));
$table->addHeaderCell($this->objLanguage->languageText("mod_sysconfig_moduledesc",'sysconfig'));
$table->addHeaderCell($this->objLanguage->languageText("mod_sysconfig_parameters",'sysconfig'));
$table->endHeaderRow();

$objIcon = $this->getObject('geticon', 'htmlelements');


//$modules = array_reverse($modules);

$table->startRow("sysconfig_configsite");
$this->loadClass('link', 'htmlelements');
$link = new link ($this->uri(array('action'=>'step2', 'pmodule_id'=>'_site_')));
$link->link = 'Configure site parameters';
$objIcon->setIcon('computer');
$table->addCell($objIcon->show());
$table->addCell($link->show(), NULL, NULL, NULL, NULL, 'colspan="3"');
$table->endRow();

//Loop and give the module configs
//$modules = array_reverse($modules);
$rClass = 'even';
foreach ($modules as $module) {
    if ($module['pmodule'] != '_site_') {
        $rClass = ($rClass == 'odd')? 'even' : 'odd';
        $table->startRow();
        $link = new link ($this->uri(array('action'=>'step2', 'pmodule_id'=>$module['pmodule'])));
        $link->link = ucfirst($this->objLanguage->code2Txt('mod_' . $module['pmodule'] . '_name',$module['pmodule']));
        $objIcon->setModuleIcon($module['pmodule']);
        $table->addCell($objIcon->show(), NULL, 'top', NULL, $rClass);
        $table->addCell($link->show(), NULL, 'top', NULL, $rClass);
        $table->addCell(ucfirst($this->objLanguage->code2Txt('mod_'
          . $module['pmodule'] . '_desc',$module['pmodule'])), NULL, 'top', NULL, $rClass);
        $table->addCell($module['paramcount'], '10%', 'top', NULL, $rClass);
        $table->endRow();
    }
}

//Set up the title
$this->loadClass('htmlheading', 'htmlelements');
$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText("help_sysconfig_about_title",'sysconfig');

$ret = $header->show() . "<br />" . $table->show();

$ret = "<div class='sysconfig_main'>$ret</div>";
$cssLayout->setMiddleColumnContent($ret);
echo $cssLayout->show();
?>