<?php
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
$this->loadClass('form','htmlelements');
$this->loadClass('label','htmlelements');

$this->loadClass('textinput','htmlelements');

$data=array();
$title='';
$action=$this->uri(array('action'=>'savearticle','topicid'=>$topicid));
if($mode == 'edit'){
  $data=$this->articles->getArticle($articleid);

  $title=$data['title'];
  $action=$this->uri(array('action'=>'updatearticle','articleid'=>$articleid,'topicid'=>$topicid));

}

$objForm = new form('articleform',$action);
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
