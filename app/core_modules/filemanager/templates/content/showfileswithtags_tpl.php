<?php

$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');

$objIcon = $this->newObject('geticon', 'htmlelements');

$this->appendArrayVar('headerParams', $this->getJavascriptFile('selectall.js', 'htmlelements'));

echo '<h1>Files with tag: '.$tag.'</h1>';

if (count($files) > 0) {
    $form = new form('deletefiles', $this->uri(array('action'=>'multidelete')));
    $form->addToForm($table);

    $button = new button ('submitform', 'Delete Selected Items');
    $button->setToSubmit();

    $selectallbutton = new button ('selectall', 'Select All');
    $selectallbutton->setOnClick("javascript:SetAllCheckBoxes('deletefiles', 'files[]', true);");

    $deselectallbutton = new button ('deselectall', 'Deselect All');
    $deselectallbutton->setOnClick("javascript:SetAllCheckBoxes('deletefiles', 'files[]', false);");

    $form->addToForm($button->show().' &nbsp; &nbsp; '.$selectallbutton->show().' '.$deselectallbutton->show());
    
    echo $form->show();
} else {
    echo $table;
}


?>