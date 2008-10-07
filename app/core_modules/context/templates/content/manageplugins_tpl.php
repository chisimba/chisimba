<?php

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('label', 'htmlelements');


$header = new htmlheading();
$header->type = 1;
$header->str = $contextTitle.': '.$this->objLanguage->code2Txt('mod_context_managepluginsabs', 'context', array('plugins'=>'plugins'), 'Manage [-plugins-]');

echo $header->show();

echo '<p>'.$this->objLanguage->code2Txt('mod_context_selectcontextpluginsabs', 'context', array('plugins'=>'plugins'), 'Select the [-plugins-] you would like to use in your [-context-]').':</p>';

$form = new form('updateplugins', $this->uri(array('action'=>'updateplugins')));

$table = $this->newObject('htmltable', 'htmlelements');

$objIcon = $this->newObject('geticon', 'htmlelements');

foreach ($plugins as $plugin)
{
    $checkbox = new checkbox('plugins[]');
    $checkbox->setValue($plugin['module_id']);
    $checkbox->setId('module_'.$plugin['module_id']);
    
    if (in_array($plugin['module_id'], $contextModules)) {
        $checkbox->setChecked(TRUE);
    }
    
    $objIcon->setModuleIcon($plugin['module_id']);
    
    $table->startRow();
    $table->addCell($checkbox->show(), 20);
    
    $label = new label ($objIcon->show(), 'module_'.$plugin['module_id']);
    
    $table->addCell($label->show(), 30);
    
    $label = new label ('<strong>'.$plugin['title'].'</strong><br />'.$plugin['description'], 'module_'.$plugin['module_id']);
    $table->addCell($label->show().'<br /><br />');
    $table->endRow();
}

$form->addToForm($table->show());

$button =  new button ('submitform', $this->objLanguage->code2Txt('mod_context_savepluginsabs', 'context', array('plugins'=>'plugins'), 'Save [-plugins-]'));
$button->setToSubmit();

$form->addToForm($button->show());

echo $form->show();



?>