<?php
//Set up the CSS Layout
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);





//Insert the left column content
$desc = $this->objLanguage->languageText("help_sysconfig_about",'sysconfig');
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

$table->startRow();
$this->loadClass('link', 'htmlelements');
$link = new link ($this->uri(array('action'=>'step2', 'pmodule_id'=>'_site_')));
$link->link = 'Configure site parameters';
$objIcon->setIcon('computer');
$table->addCell($objIcon->show());
$table->addCell('<span style="font-size: 140%; font-weight: bolder;">'.$link->show().'</span><br /><br />', NULL, NULL, NULL, NULL, 'colspan="2"');
$table->endRow();

//Loop and give the module configs
//$modules = array_reverse($modules);
foreach ($modules as $module)
{
    if ($module['pmodule'] != '_site_') {
        $table->startRow();

        $link = new link ($this->uri(array('action'=>'step2', 'pmodule_id'=>$module['pmodule'])));
        $link->link = ucfirst($this->objLanguage->code2Txt('mod_' . $module['pmodule'] . '_name',$module['pmodule']));
        $objIcon->setModuleIcon($module['pmodule']);
        $table->addCell($objIcon->show());
        $table->addCell($link->show());
        $table->addCell(ucfirst($this->objLanguage->code2Txt('mod_' . $module['pmodule'] . '_desc',$module['pmodule'])));
        $table->addCell($module['paramcount'], '10%');
        $table->endRow();
    }
}

//Set up the title
$this->loadClass('htmlheading', 'htmlelements');
$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText("help_sysconfig_about_title",'sysconfig');

$cssLayout->setMiddleColumnContent($header->show() . "<br />" . $table->show());
echo $cssLayout->show();
?>