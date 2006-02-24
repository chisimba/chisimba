<?php
//View template for table: tbl_stories

//Set up the button class to make the edit, add and delet icons
$objButtons = & $this->getObject('navbuttons', 'navigation');

// Create an instance of the css layout class
$cssLayout = & $this->newObject('csslayout', 'htmlelements');// Set columns to 2
$cssLayout->setNumColumns(2);

// Check if the text should be changed.
$textModule = 'stories';

//Set the content of the left side column
$leftSideColumn = $this->objLanguage->code2Txt("mod_".$textModule."_mainleftside");

// Add Left column
$cssLayout->setLeftColumnContent($leftSideColumn);// Add the heading to the content
$this->objH =& $this->getObject('htmlheading', 'htmlelements');
$this->objH->type=3; //Heading <h3>
$this->objH->str=ucwords($objLanguage->code2Txt("mod_".$textModule."_title"));

$rightSideColumn = "<div align=\"center\">" . $this->objH->show() . "</div>";

//Create a table
$this->Table = $this->newObject('htmltable', 'htmlelements');
$this->Table->cellspacing="2";
$this->Table->width="80%";
$this->Table->attributes="align=\"center\"";
//Create the array for the table header
$tableRow=array();
//$tableHd[]="id";
$tableHd[]=$this->objLanguage->languageText("word_category");
$tableHd[]=$this->objLanguage->languageText("word_author");
//$tableHd[]="parentId";
$tableHd[]=$this->objLanguage->languageText('word_language');
$tableHd[]=$this->objLanguage->languageText("word_title");
$tableHd[]=$this->objLanguage->languageText('phrase_dateposted');
$tableHd[]=$this->objLanguage->code2Txt('phrase_expirationdate');

$tableHd[]=$this->objLanguage->code2Txt("mod_stories_alwaysontop");
$tableHd[]=$this->objLanguage->code2Txt("phrase_isactive");
$tableHd[]="&nbsp;";

$allowAdmin = True; //You need to write your security here

//Get the icon class and create an add, edit and delete instance
$objAddIcon = $this->newObject('geticon', 'htmlelements');

$objAddIcon->alt = $this->objLanguage->code2Txt('mod_'.$textModule.'_addalt');

if ($allowAdmin) {
    $addLink = $this->uri(array('action' => 'add'));
    $tableHd[] = $objAddIcon->getAddIcon($addLink);
    $tableHd[]="&nbsp;";
} else {
    $tableHd[]="&nbsp;";
}


$objEditIcon = $this->newObject('geticon', 'htmlelements');
$objDelIcon = $this->newObject('geticon', 'htmlelements');

$objDelIcon->alt = $this->objLanguage->code2Txt('mod_'.$textModule.'_delalt');

// Get Icon for Active / InActive?
$objGetIcon = $this->newObject('geticon', 'htmlelements');

//Icon for translate
$objTrIcon = $this->newObject('geticon', 'htmlelements');

$objTrIcon->alt = $this->objLanguage->code2Txt('mod_'.$textModule.'_translate');

$objTrIcon->setIcon('translate');
$objTrLink = $this->newObject('link', 'htmlelements');

//Create the table header for display
$this->Table->addHeader($tableHd, "heading");
//Instantiate the classe for checking expiration
$objExp = & $this->getObject('datetime', 'utilities');
//Loop through and display the records
$rowcount = 0;
if (isset($ar)) {
    if (count($ar) > 0) {
        foreach ($ar as $line) {
            $oddOrEven = ($rowcount == 0) ? "odd" : "even";

            //Check for isactive and replace with icon
            $isActive = $line['isactive'];
            if ($isActive == 1) {
                $objGetIcon->setIcon('online');
                $objGetIcon->alt = $this->objLanguage->code2Txt('mod_'.$textModule.'_isactivealt');
            } else {
                $objGetIcon->setIcon('offline');
                $objGetIcon->alt = $this->objLanguage->code2Txt('mod_'.$textModule.'_isnotactivealt');
            }
            $isActive = $objGetIcon->show();


            //Check is sticky and replace with isSticky icon
            $isSticky = $line['issticky'];
            if ($isSticky == 1) {
                $objGetIcon->setIcon('sticky_yes');
                $objGetIcon->alt = ucfirst($this->objLanguage->code2Txt('mod_'.$textModule.'_alwaysontopalt'));
            } else {
                $objGetIcon->setIcon('sticky_no');
                $objGetIcon->alt = ucfirst($this->objLanguage->code2Txt('mod_'.$textModule.'_notalwaysontopalt'));
            }
            $isSticky = $objGetIcon->show();

            //Check expiration and replace with icon and warning text
            $expirationDate = $this->formatDate($line['expirationdate']);
            if ( $objExp->hasExpired($expirationDate) ) {
                 $expirationDate = '<span class="error"><strong>' . $expirationDate
                . '<strong></span> '. $objExp->getExpiredIcon();
            }
            //$tableRow[]=$line['id'];
            $tableRow[]=$line['category'];
            $tableRow[]=$this->objUser->fullName($line['creatorid']);
            //$tableRow[]=$line['parentId'];
            $tableRow[]=$line['language'];
            $tableRow[]=$line['title'];
            $tableRow[]=$this->formatDate($line['datecreated']);
            $tableRow[]=$expirationDate;
            $tableRow[]=$isSticky;
            $tableRow[]=$isActive;

            //The translate link
            $trLink = $this->uri(array(
              'action' => 'translate',
              'category' => $line['category'],
              'parentid' => $line['id']), 'stories');
            //Make it a link
            $objTrLink->href=$trLink;
            $objTrLink->link=$objTrIcon->show();
            $tableRow[]=$objTrLink->show();

            //The URL for the edit link
            $editLink=$this->uri(array('action' => 'edit',
              'id' =>$line['id']));
            $objEditIcon->alt = $this->objLanguage->code2Txt('mod_'.$textModule.'_editalt');
            $ed = $objEditIcon->getEditIcon($editLink);

            // The delete icon with link uses confirm delete utility
            $objDelIcon->setIcon("delete");
            $delLink = $this->uri(array(
              'action' => 'delete',
              'confirm' => 'yes',
              'id' => $line['id']));
            $objConfirm = & $this->newObject('confirm','utilities');
            $rep = array('ITEM' => $line['id']);
            $delText = $this->objLanguage->code2Txt("mod_stories_confirm", $rep);
            $objConfirm->setConfirm($objDelIcon->show(),$delLink,$delText);
            $conf = $objConfirm->show();
            $tableRow[]=$ed;
            $tableRow[]=$conf;            //Add the row to the table for output
            $this->Table->addRow($tableRow, $oddOrEven);
            $tableRow=array(); // clear it out
            // Set rowcount for bitwise determination of odd or even
            $rowcount = ($rowcount == 0) ? 1 : 0;
        }
    }
}
//Add the table to the centered layer
$rightSideColumn .= $this->Table->show();

// Add Left column
$cssLayout->setLeftColumnContent($leftSideColumn);

// Add Right Column
$cssLayout->setMiddleColumnContent($rightSideColumn);

//Output the content to the page
echo $cssLayout->show();

?>
