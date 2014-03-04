<?php
// Thi template displays the registered staff members

//Load HTMl Objet Classes

$objH = $this->newObject('htmlheading', 'htmlelements');
$link =  $this->newObject('link', 'htmlelements');
$objIcon =  $this->newObject('geticon', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$objLayer =$this->newObject('layer','htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$objLayer = $this->newObject('layer', 'htmlelements');

$authortable =  $this->newObject('htmltable', 'htmlelements');

$objIcon = $this->newObject('geticon', 'htmlelements');

//edit Icon
$objIcon->setIcon('edit');
$objIcon->alt = 'Edit';
$objIcon->title = 'Edit';
$editIcon = $objIcon->show();

//Delete Icon
$objIcon->setIcon('delete');
$objIcon->alt = 'Delete';
$objIcon->title = 'Delete';
//$deleteIcon = $objIcon->show();

//Add Icon
$objIcon->setIcon('add');
$objIcon->align = 'top';
$objIcon->alt = 'Add New Chapter In a Book';
$objIcon->title = 'Add Chapter In a Book';

$link = new link($this->uri(array('action'=>'Chapter In a Book')));
$link->link = $objIcon->show();

$addlink = new link($this->uri(array('action'=>'Chapter In a Book')));
$addlink->link = 'Add New Chapter In a Book';

$table = new htmltable();
$table->cellspacing = '2';
$table->cellpadding = '5';

//setup the table headings
$h3 = $this->getObject('htmlheading', 'htmlelements');
$objLayer = $this->newObject('layer', 'htmlelements');

$h3->str =$this->objLanguage->languageText('word_text', 'rimfhe', 'Chapter in a Books Details');

$objLayer->str = $h3->show();
$objLayer->border = '; float:left; align: left; margin:0px; padding:0px;';
$header = $objLayer->show();

$display = '<p>'.$header.'&nbsp;&nbsp;&nbsp; '.$link->show().'</p><hr />';

//Show Header
echo $display;

//update notification
$updateComment = $this->getParam('comment');
if(!empty($updateComment)){
    echo '<span style="color:#D00000 ">'.$updateComment.'</span>';
    echo '<br /><br />';
}

//delete notification
$deleteComment = $this->getParam('deletecomment');
if(!empty($deleteComment)){
    echo '<span style="color:#D00000;">'.$deleteComment.'</span>';
    echo '<br /><br />';
}

//Set up fields heading
$table->startHeaderRow();
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_booktitle2', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_isbn', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_editors','rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_publisher', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_chaptertitle', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_authors', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_chapterfirstpageno', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_chapterlastpageno', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_pagetotal', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_peer', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_editlink', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_deletelink', 'rimfhe'));
$table->endHeaderRow();

$rowcount = 0;

//setup the tables rows  and loop though the records
if ( count($arrDisplayBooks) > 0) {

    foreach($arrDisplayBooks as $chapterinbook) {
        //Set odd even row colour
        $oddOrEven = ($rowcount == 0) ? "even" : "odd";
        $tableRow = array();

        $tableRow[] = $chapterinbook['booktitle'];
        $tableRow[] = $chapterinbook['isbn'];
        $tableRow[] = $chapterinbook['bookeditors'];
        $tableRow[] = $chapterinbook['publisher'];
        $tableRow[] = $chapterinbook['chaptertitle'];
        $tableRow[] = $chapterinbook['authorname'];
        $tableRow[] = $chapterinbook['chapterfirstpageno'];
        $tableRow[] = $chapterinbook['chapterlastpageno'];
        $tableRow[] = $chapterinbook['pagetotal'];
        $tableRow[] = $chapterinbook['peerreviewed'];
        $editlink = new link($this->uri(array('action'=>'Edit Chapter In Book', 'id'=> $chapterinbook['id'])));
        $editlink->link = $editIcon;
        $tableRow[] = $editlink->show();

        $delArray = array('action' => 'deletechapterinbook', 'confirm'=>'yes', 'id'=>$chapterinbook['id']);
        $title = $chapterinbook['chaptertitle'];
        $rep = array('TITLE' => $title);
        $deletephrase = $this->objLanguage->code2Txt('mod_confirm_delete', 'rimfhe', $rep );
        $deleteIcon = $objIcon->getDeleteIconWithConfirm($chapterinbook['id'], $delArray,'rimfhe',$deletephrase);
        $tableRow[] = $deleteIcon;

        $table->addRow($tableRow, $oddOrEven);

        $rowcount = ($rowcount == 0) ? 1 : 0;
    }
}
else{
    echo  '<div class="noRecordsMessage">'.$objLanguage->languageText('mod_rimfhe_norecord', 'rimfhe', 'No record has been entered').'</div>';
}
echo $table->show();
echo '<p>'.'&nbsp;'.'</p>';
echo '<p>'.$addlink->show().'</p>';
echo '<p>'.'&nbsp;'.'</p>';
?>

