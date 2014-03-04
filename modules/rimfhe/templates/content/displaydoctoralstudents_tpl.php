<?php
/*
* This template displays Graduate Doctoral Students
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
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$objLayer = $this->newObject('layer', 'htmlelements');

$authortable =  $this->newObject('htmltable', 'htmlelements');

$objIcon = $this->newObject('geticon', 'htmlelements');

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
$objIcon->alt = 'Add New Doctoral Student';
$objIcon->title = 'Add New Doctoral Student';

$link = new link($this->uri(array('action'=>'Graduating Doctoral Student')));
$link->link = $objIcon->show();

$addlink = new link($this->uri(array('action'=>'Graduating Doctoral Student')));
$addlink->link = 'Add New Graduated Doctoral Student';

//link to Graduating Doctoral Students Summary Page
$objGradDocStudSummar = new link($this->uri(array('action'=>'Graduating Doctoral Students Summary')));
$objGradDocStudSummar->link='Doctoral Students Summary';

$table = new htmltable();
$table->cellspacing = '2';
$table->cellpadding = '5';

//setup the table headings

$h3 = $this->getObject('htmlheading', 'htmlelements');
$objLayer = $this->newObject('layer', 'htmlelements');

$h3->str =$this->objLanguage->languageText('mod_rimfhe_', 'rimfhe','Graduated Doctoral Students Information' );

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
$table->addHeaderCell($this->objLanguage->languageText('word_surname', 'system'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_initials', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('phrase_firstname', 'system'));
$table->addHeaderCell($this->objLanguage->languageText('word_gender', 'system', 'Gender'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_nationality', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_race', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_studentnumber', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_department', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_faculty', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_thesistitle', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_supervisor', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_degree', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_editlink', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_deletelink', 'rimfhe'));
$table->endHeaderRow();

$rowcount = 0;

//setup the tables rows  and loop though the records
if ( count($arrDisplayDoctoral) > 0) {

    foreach($arrDisplayDoctoral as $doctoralstudents) {
        //Set odd even row colour
        $oddOrEven = ($rowcount == 0) ? "even" : "odd";
        $tableRow = array();

        $tableRow[] = $doctoralstudents['surname'];
        $tableRow[] = $doctoralstudents['initials'];
        $tableRow[] = $doctoralstudents['firstname'];
        $tableRow[] = $doctoralstudents['gender'];
        $tableRow[] = $doctoralstudents['nationality'];
        $tableRow[] = $doctoralstudents['race'];
        $tableRow[] = $doctoralstudents['regnumber'];
        $tableRow[] = $doctoralstudents['deptschoool'];
        $tableRow[] = $doctoralstudents['faculty'];
        $tableRow[] = $doctoralstudents['thesistitle'];
        $tableRow[] = $doctoralstudents['supervisorname'];
        $tableRow[] = $doctoralstudents['degree'];
        $editlink = new link($this->uri(array('action'=>'Edit Graduating Doctoral Student', 'id'=> $doctoralstudents['id'])));
        $editlink->link = $editIcon;
        $tableRow[] = $editlink->show();

        $delArray = array('action' => 'deletedoctoralstudents', 'confirm'=>'yes', 'id'=>$doctoralstudents['id']);
        $title = $doctoralstudents['thesistitle'];
        $rep = array('TITLE' => $title);
        $deletephrase = $this->objLanguage->code2Txt('mod_confirm_delete', 'rimfhe', $rep );
        $deleteIcon = $objIcon->getDeleteIconWithConfirm($doctoralstudents['id'], $delArray,'rimfhe',$deletephrase);
        $tableRow[] = $deleteIcon;

        $table->addRow($tableRow, $oddOrEven);

        $rowcount = ($rowcount == 0) ? 1 : 0;
    }
}
else{
    echo  '<div class="noRecordsMessage">'.$objLanguage->languageText('mod_rimfhe_norecord', 'rimfhe').'</div>';

}
echo $table->show();
echo '<p>'.'&nbsp;'.'</p>';
echo '<p>'.$addlink->show().'</p>';
echo '<p>'.$objGradDocStudSummar->show().'</p>';
echo '<p>'.'&nbsp;'.'</p>';
?>
