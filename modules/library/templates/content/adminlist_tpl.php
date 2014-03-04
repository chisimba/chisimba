<?php
//View template for table: tbl_library

//Set up the button class to make the edit, add and delet icons
$objButtons = & $this->getObject('navbuttons', 'navigation');

//Create the centered area for display
$this->center = $this->objConfig = & $this->getObject('layer', 'htmlelements');
//$this->center->align="center";

// Add the heading to the content
$this->objH =& $this->getObject('htmlheading', 'htmlelements');
$this->objH->type=3; //Heading <h3>
$this->objH->str=$objLanguage->languageText("mod_library_title",'library');

if ($this->isValid('add')) {
	    // Display add button.
	    $icon = $this->getObject('geticon','htmlelements');
	    $icon->setIcon('add');
	    $icon->alt = "Create";
	    $icon->align=false;	
        
        $this->objH->str .= '<a href="'.
        $this->uri(array('action'=>'add'))
    .'">' . $icon->show() . '</a>';
}

$this->center->addToStr($this->objH->show());

//Create a table
$this->Table = $this->newObject('htmltable', 'htmlelements');
$this->Table->cellspacing="7";
$this->Table->cellpadding="7";
$this->Table->width="90%";
//$this->Table->attributes="align=\"center\"";
//Create the array for the table header
$tableRow=array();
$tableHd[]=$objLanguage->languageText("word_title");
$tableHd[]=$objLanguage->languageText("word_description");
$tableHd[]=$objLanguage->languageText("word_url");
$tableHd[]=$objLanguage->languageText("phrase_addedby");
$tableHd[]=$objLanguage->languageText("phrase_dateadded");
$tableHd[]=$objLanguage->languageText("phrase_updatedby");
$tableHd[]=$objLanguage->languageText("phrase_datemodified");
$tableHd[]="&nbsp;";
$tableHd[]="&nbsp;";

//Get the icon class and create an add, edit and delete instance
$objAddIcon = $this->newObject('geticon', 'htmlelements');
$objEditIcon = $this->newObject('geticon', 'htmlelements');
$objDelIcon = $this->newObject('geticon', 'htmlelements');
//Create the table header for display
$this->Table->addHeader($tableHd, "heading");

//Loop through and display the records
$rowcount = 0;
if (isset($ar)) {
    if (count($ar) > 0) {
        foreach ($ar as $line) {
            $oddOrEven = ($rowcount == 0) ? "odd" : "even";
            $tableRow[]=$line['title'];
            $tableRow[]=$line['description'];
            $tableRow[]=$line['url'];
            $tableRow[]=$this->objUser->fullName($line['creatorid']);
            $tableRow[]=$line['datecreated'];
            $userId=$line['modifierid'];
            if ($userId !== NULL) {
                $tableRow[]=$this->objUser->fullName($userId);
            } else {
                $tableRow[]="";
            }
            $tableRow[]=$line['datemodified'];

            //The URL for the edit link
            if ($this->isValid('edit')) {
                $editLink=$this->uri(array('action' => 'edit',
                  'id' =>$line['id']));
                $objEditIcon->alt=$this->objLanguage->languageText("word_edit");
                $ed = $objEditIcon->getEditIcon($editLink);
            } else { 
                $ed = "&nbsp;";
            }

             // The delete icon with link uses confirm delete utility
            if ($this->isValid('delete')) {
                $objDelIcon->setIcon("delete");
                $rep = array('title' => addslashes($line['title']));
                $objDelIcon->alt=$this->objLanguage->code2Txt("mod_library_confirmdel",'library', $rep);
                $delLink = $this->uri(array(
                  'action' => 'delete',
                  'confirm' => 'yes',
                  'id' => $line['id']));
                $objConfirm = & $this->newObject('confirm','utilities');
                $objConfirm->setConfirm($objDelIcon->show(), $delLink, $this->objLanguage->code2Txt("mod_library_confirmdel",'library', $rep));
                $conf = $objConfirm->show();
            } else {
                $conf = "&nbsp;";
            }
            $tableRow[]=$objEditIcon->getEditIcon($editLink);
            $tableRow[]=$conf;
            //Add the row to the table for output
            $this->Table->addRow($tableRow, $oddOrEven);
            $tableRow=array(); // clear it out
            // Set rowcount for bitwise determination of odd or even
            $rowcount = ($rowcount == 0) ? 1 : 0;

        }
    } else {
    
        $this->Table->startRow();       
        $this->Table->addCell("<div class=\"noRecordsMessage\">" . $objLanguage->languageText('phrase_norecordsfound') . "</div>", "null", "", "", "", 'colspan="9"');
        $this->Table->endRow();
    }    
}
    
//Add the table to the centered layer
$this->center->addToStr($this->Table->show());

//Output the content to the page
echo $this->center->show();

//Create a table
$this->Table = $this->newObject('htmltable', 'htmlelements');
$this->Table->cellspacing="7";
$this->Table->cellpadding="7";
$this->Table->width="90%";
$this->Table->attributes="align=\"center\"";

$options =  "<br /> <a href=\"" . 
$this->uri(array(
    'module'=>'library',
    'action'=>'view'
    ))
. "\">" . $objLanguage->languageText("word_library") . "</a>";
$options .=  "&nbsp;";

$this->Table->startRow();
$this->Table->addCell($options, "null", "", "center", "", null);
$this->Table->endRow();

echo $this->Table->show();

?>