<?php
/*
* This template displays Entire Books
*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check
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

/*****
*New Stuff Added
*/
$objIcon = $this->newObject('geticon', 'htmlelements');
//Edit Icon
$objIcon->setIcon('edit');
$objIcon->alt = 'Edit';
$objIcon->title = 'Edit';
$editIcon = $objIcon->show();

//Delete Icon
$objIcon->setIcon('delete');
$objIcon->alt = 'Delete';
$objIcon->title = 'Delete';
$deleteIcon = $objIcon->show();

//Add Icon
$objIcon->setIcon('add');
$objIcon->align = 'top';
$objIcon->alt = 'Add New Book/Monogragh';
$objIcon->title = 'Add New Book/Monogragh';

$link = new link($this->uri(array('action'=>'Entire Book/Monogragh')));
$link->link = $objIcon->show();

$addlink = new link($this->uri(array('action'=>'Entire Book/Monogragh')));
$addlink->link = $this->objLanguage->languageText('mod_rimfhe_booktitle', 'rimfhe');

/*
*End New Stuf
*/

$table = new htmltable();
$table->cellspacing = '2';
$table->cellpadding = '5';

//setup the table headings


$h3 = $this->getObject('htmlheading', 'htmlelements');
$objLayer = $this->newObject('layer', 'htmlelements');

$h3->str = $this->objLanguage->languageText('mod_rimfhe_pgheadingdisplayentirebook', 'rimfhe');

$objLayer->str = $h3->show();
$objLayer->border = '; float:left; align: left; margin:0px; padding:0px;';
$header = $objLayer->show();

$display = '<p>'.$header.'&nbsp;&nbsp;&nbsp; '.$link->show().'</p><hr />';

//Show Header
echo $display;

//update notification
$updateComment = $this->getParam('comment');
if(!empty($updateComment)){
    echo '<span style="color:#D00000">'.$updateComment.'</span>';
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
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_booktitle', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_isbn', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_publisher', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_authors', 'rimfhe'), 120);
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_firstchapterpageno', 'rimfhe'), 80);
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_lastchapterpageno', 'rimfhe'), 80);
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_pagetotal', 'rimfhe'), 80);
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_peer', 'rimfhe') , 80);
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_editlink', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_deletelink', 'rimfhe'));
$table->endHeaderRow();

$rowcount = 0;

//setup the tables rows  and loop though the records
if ( count($arrDisplayBooks) > 0) {

    foreach($arrDisplayBooks as $entirebook) {
        //Set odd even row colour
        $oddOrEven = ($rowcount == 0) ? "even" : "odd";
        $tableRow = array();

        $tableRow[] = $entirebook['booktitle'];
        $tableRow[] = $entirebook['isbn'];
        $tableRow[] = $entirebook['publishinghouse'];
        $tableRow[] = $entirebook['authorname'];
        $tableRow[] = $entirebook['firstchapterpageno'];
        $tableRow[] = $entirebook['lastchapterpageno'];
        $tableRow[] = $entirebook['totalpages'];
        $tableRow[] = $entirebook['peerreviewed'];
        $editlink = new link($this->uri(array('action'=>'Edit Book', 'id'=> $entirebook['id'])));
        $editlink->link = $editIcon;
        $tableRow[] = $editlink->show();

        $delArray = array('action' => 'deleteentirebook', 'confirm'=>'yes', 'id'=>$entirebook['id']);
        $title = $entirebook['booktitle'];
        $rep = array('TITLE' => $title);
        $deletephrase = $this->objLanguage->code2Txt('mod_confirm_delete', 'rimfhe', $rep );
        $deleteIcon = $objIcon->getDeleteIconWithConfirm($entirebook['id'], $delArray,'rimfhe',$deletephrase);
        $tableRow[] = $deleteIcon;

        $table->addRow($tableRow, $oddOrEven);

        $rowcount = ($rowcount == 0) ? 1 : 0;
    }
}
else{
    $table->startRow();
    $table->addCell('<div class="noRecordsMessage">'.$objLanguage->languageText('mod_rimfhe_norecord', 'rimfhe' ).'</div>',NULL,NULL,NULL,NULL,'colspan="10"');
    $table->endRow();
}

echo $table->show();
echo '<p>'.'&nbsp;'.'</p>';
echo '<p>'.$addlink->show().'</p>';
echo '<p>'.'&nbsp;'.'</p>';
?>
