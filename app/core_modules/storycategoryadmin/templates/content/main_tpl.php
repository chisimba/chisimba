<?php

//View template for table: tbl_storycategory

//Set up the button class to make the edit, add and delet icons
$objButtons = & $this->getObject('navbuttons', 'navigation');

// Create an instance of the css layout class
$cssLayout = & $this->newObject('csslayout', 'htmlelements');// Set columns to 2
$cssLayout->setNumColumns(2);

//Set the content of the left side column
$leftSideColumn = $objLanguage->languageText("mod_storycategoryadmin_leftinstructions", "storycategoryadmin");

$allowAdmin = True; //You need to write your security here

// Add Left column
$cssLayout->setLeftColumnContent($leftSideColumn);// Add the heading to the content
$this->objH =& $this->getObject('htmlheading', 'htmlelements');
$this->objH->type=1; //Heading <h1>
if ($allowAdmin) {
  $paramArray = array('action' => 'add');
  $this->objH->str=$objLanguage->languageText("mod_storycategoryadmin_title", "storycategoryadmin")."&nbsp;".$objButtons->linkedButton("add", $this->uri($paramArray));
 } else {
      $this->objH->str=$objLanguage->languageText("mod_storycategoryadmin_title", "storycategoryadmin");
     }
$rightSideColumn = $this->objH->show();

//Create a table
$this->Table = $this->newObject('htmltable', 'htmlelements');
$this->Table->cellspacing="2";
$this->Table->cellpadding="2";
$this->Table->width="90%";
//Create the array for the table header
$tableRow=array();
$tableHd[]=$objLanguage->languageText("mod_storycategoryadmin_category", "storycategoryadmin");
$tableHd[]=$objLanguage->languageText("mod_storycategoryadmin_titleth", "storycategoryadmin");
$tableHd[]=$objLanguage->languageText("mod_storycategoryadmin_datecreated", "storycategoryadmin");
$tableHd[]=$objLanguage->languageText("mod_storycategoryadmin_creatorid", "storycategoryadmin");
$tableHd[]=$objLanguage->languageText("mod_storycategoryadmin_datemodified", "storycategoryadmin");
$tableHd[]=$objLanguage->languageText("mod_storycategoryadmin_modifierid", "storycategoryadmin");
if($allowAdmin){
$tableHd[]=$objLanguage->languageText("mod_storycategoryadmin_action", "storycategoryadmin");
}
//Get the icon class and create an add, edit and delete instance
$objAddIcon = $this->newObject('geticon', 'htmlelements');
$objEditIcon = $this->newObject('geticon', 'htmlelements');
$objDelIcon = $this->newObject('geticon', 'htmlelements');
//Create the table header for display
$this->Table->addHeader($tableHd, "heading");

//Create an instance of the User object
//$this->objUser =  & $this->getObject("user", "security");

//Loop through and display the records
$rowcount = 0;
if (isset($ar)) {
    if (count($ar) > 0) {
        foreach ($ar as $line) {
            $oddOrEven = ($rowcount == 0) ? "odd" : "even";
            $tableRow[]=$line['category'];
            $tableRow[]=$line['title'];
            $tableRow[]=$line['dateCreated'];
            $tableRow[]=$this->objUser->fullName($line['creatorId']);
            $tableRow[]=$line['dateModified'];
            $modifierId = $line['modifierId'];
            if ($modifierId != "") {
                $tableRow[]= $this->objUser->fullName($modifierId);
            } else {
                $tableRow[]= "";
            }


            //The URL for the edit link
            $editLink=$this->uri(array('action' => 'edit',
              'id' =>$line['id']));
            $objEditIcon->alt=$this->objLanguage->languageText("mod_storycategory_editalt", "storycategoryadmin");
            $ed = $objEditIcon->getEditIcon($editLink);

            // The delete icon with link uses confirm delete utility
            $objDelIcon->setIcon("delete");
            $objDelIcon->alt=$this->objLanguage->languageText("mod_storycategoryadmin_delalt", "storycategoryadmin");
            $delLink = $this->uri(array(
              'action' => 'delete',
              'confirm' => 'yes',
              'id' => $line['id']));
            $objConfirm = & $this->newObject('confirm','utilities');
            $rep = array('ITEM' => $line['category']);
            $objConfirm->setConfirm($objDelIcon->show(),
            $delLink,$this->objLanguage->code2Txt("mod_storycategory_confirm", $rep));
            $conf = $objConfirm->show();
          if($allowAdmin){
            $tableRow[]=$ed."&nbsp;".$conf;
            }
            //Add the row to the table for output
            $this->Table->addRow($tableRow, $oddOrEven);
            $tableRow=array(); // clear it out
            // Set rowcount for bitwise determination of odd or even
            $rowcount = ($rowcount == 0) ? 1 : 0;

        }
    }
}
//Add the table to the centered layer
$rightSideColumn .= $this->Table->show();
//Create add text link
$objAddLink =& $this->getObject('link', 'htmlelements');
$objAddLink->link($this->uri(array('action'=>'add')));
$objAddLink->link=$objLanguage->languageText("mod_storycategoryadmin_addnew", "storycategoryadmin");
//Add the link to the centered layer
$rightSideColumn .= $objAddLink->show();
// Add Left column
$cssLayout->setLeftColumnContent($leftSideColumn);

// Add Right Column
$cssLayout->setMiddleColumnContent($rightSideColumn);

//Output the content to the page
echo $cssLayout->show();

?>
