<h1>System Configuration Management</h1>
<?php

$table = $this->newObject('htmltable', 'htmlelements');
$table->cellpadding = 5;
$table->startHeaderRow();
$table->addHeaderCell('&nbsp;', 20);
$table->addHeaderCell('Module');
$table->addHeaderCell('Module Description');
$table->addHeaderCell('Parameters');
$table->endHeaderRow();

$objIcon = $this->getObject('geticon', 'htmlelements');

$this->loadClass('link', 'htmlelements');

$modules = array_reverse($modules);

if ($modules[0]['pmodule'] == '_site_') {
    $table->startRow();
    
    $link = new link ($this->uri(array('action'=>'step2', 'pmodule_id'=>$modules[0]['pmodule'])));
    $link->link = 'Configure site parameters';
    
    $table->addCell('&nbsp;');
    $table->addCell($link->show());
    $table->addCell('&nbsp;');
    $table->addCell($modules[0]['paramcount'], '10%');
    $table->endRow();
    
    // 
    // 
}
$modules = array_reverse($modules);
//$modules = array_pop($modules); 

foreach ($modules as $module)
{
    
    if ($module['pmodule'] != '_site_') {
        $table->startRow();
        
        $link = new link ($this->uri(array('action'=>'step2', 'pmodule_id'=>$module['pmodule'])));
        
        //if ($module['pmodule'] == '_site_') {
            // $link->link = 'Configure site parameters';
        // } else {
            $link->link = ucfirst($this->objLanguage->code2Txt('mod_' . $module['pmodule'] . '_name'));
        //}
        
        $objIcon->setModuleIcon($module['pmodule']);
        $table->addCell($objIcon->show());
        $table->addCell($link->show());
        $table->addCell(ucfirst($this->objLanguage->code2Txt('mod_' . $module['pmodule'] . '_desc')));
        $table->addCell($module['paramcount'], '10%');
        $table->endRow();
    }

}


echo $table->show();

?>