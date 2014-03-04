<?php

$this->loadClass('form','htmlelements');
$this->loadClass('label','htmlelements');
$this->loadClass('radio','htmlelements');

$this->loadClass('textinput','htmlelements');

$data=array();
$title='';
$action=$this->uri(array('action'=>'savetopic'));
if($mode == 'edit'){
  $data=$this->dbtopics->getTopic($topicid);

  $title=$data['title'];
  $action=$this->uri(array('action'=>'updatetopic','topicid'=>$topicid));
}

$objForm = new form('topicform',$action);
$titleField=new textinput('titlefield',$title, NULL, 150);

$table=$this->getObject('htmltable','htmlelements');
$table->cellspacing = '20';

$table->startRow();
$table->addCell('Title', 60, NULL, 'left');
$table->addCell($titleField->show(), 600);
$table->endRow();

$htmlarea = $this->newObject('htmlarea', 'htmlelements');
$htmlarea->setName('pagecontent');
$htmlarea->context = TRUE;
if ($mode == 'add') {
    $htmlarea->setContent('');
} else {
    $htmlarea->setContent($data['content']);
}


$activeRadio = new radio ('activefield');
$activeRadio->addOption('1', 'Active');
$activeRadio->addOption('0', 'Inactive');
$activeRadio->setBreakSpace(' &nbsp; ');

if ($mode == 'edit') {
    $activeRadio->setSelected($data['active']);
} else {
    $activeRadio->setSelected('1');
}

$table->startRow();
$table->addCell('Status'.'&nbsp;');
$table->addCell($activeRadio->show());
$table->endRow();

$label = new label ('Content', 'input_htmlarea');

$table->startRow();
$table->addCell($label->show());
$table->addCell($htmlarea->show());
$table->endRow();



$button = new button('submitform', 'Save');
$button->setToSubmit();

$table->startRow();
$table->addCell('&nbsp;');
$table->addCell($button->show());
$table->endRow();

$objForm->addToForm($table->show());
$objForm->addRule('titlefield', 'Topic title is required', 'required');

// Create an instance of the css layout class
$cssLayout = & $this->newObject('csslayout', 'htmlelements');// Set columns to 2
$cssLayout->setNumColumns(1);

$cssLayout->setLeftColumnContent($leftSideColumn);
//$rightSideColumn='<h1>'.$coursedata['title'].'</h1>';
$rightSideColumn='<div id="gtx"></div><div style="padding:10px;">';

//Add the table to the centered layer
$//rightSideColumn .= /$objForm->show();//
$rightSideColumn.= '</div>';
// Add Right Column
$cssLayout->setMiddleColumnContent($rightSideColumn);
echo $objForm->show();
?>
