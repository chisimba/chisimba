<?php
// Thi template displays the registered staff members

//Load HTMl Objet Classes
$table =  $this->newObject('htmltable', 'htmlelements');
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

$table = new htmltable();
$table->cellspacing = '2';
$table->cellpadding = '5';

$h3 = $this->getObject('htmlheading', 'htmlelements');
$objLayer = $this->newObject('layer', 'htmlelements');

$h3->str = $objIcon->show().'&nbsp;'. $this->objLanguage->languageText('mod_rimfhe_pgheadingdisplaystaff', 'rimfhe');

$objLayer->str = $h3->show();
$objLayer->border = '; float:left; align: left; margin:0px; padding:0px;';
$header = $objLayer->show();


$display = '<p>'.$header.'</p><hr />';
//Show Header
echo $display;

//setup the table headings
$table->startHeaderRow();
$table->addHeaderCell($this->objLanguage->languageText('word_surname', 'system'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_initials', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('phrase_firstname', 'system'));
$table->addHeaderCell($this->objLanguage->languageText('word_title', 'system'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_rank', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_appointment', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_department', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('mod_rimfhe_faculty', 'rimfhe'));
$table->addHeaderCell($this->objLanguage->languageText('phrase_staffnumber', 'system'));
$table->addHeaderCell($this->objLanguage->languageText('phrase_emailaddress', 'system'));
$table->endHeaderRow();

$rowcount = 0;

//setup the tables rows  and loop though the records
if (count($arrDisplayStaff) > 0) {

    //Loop through $arrDisplayStaff and set data in rows
    foreach($arrDisplayStaff as $staffmember) {
        //Set odd even row colour
        $oddOrEven = ($rowcount == 0) ? "even" : "odd";

        //Setuo table rows
        $tableRow = array();
        $tableRow[] = $staffmember['surname'];
        $tableRow[] = $staffmember['initials'];
        $tableRow[] = $staffmember['firstname'];
        $tableRow[] = $staffmember['tiltle'];
        $tableRow[] = $staffmember['rank'];
        $tableRow[] = $staffmember['appointmenttype'];
        $tableRow[] = $staffmember['department'];
        $tableRow[] = $staffmember['faculty'];
        $tableRow[] = $staffmember['staffnumber'];
        $tableRow[] = $staffmember['email'];

        //add to table
        $table->addRow($tableRow, $oddOrEven);

        $rowcount = ($rowcount == 0) ? 1 : 0;
    }
}
else{
    //When no data has been entered
    echo  '<div class="noRecordsMessage">'.$objLanguage->languageText('mod_rimfhe_norecord', 'rimfhe').'</div>';

}
echo $table->show();

?>
