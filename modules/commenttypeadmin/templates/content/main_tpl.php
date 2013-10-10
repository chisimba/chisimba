<?php 
// View template for table: tbl_commenttype

// Set up the button class to make the edit, add and delet icons
$objButtons = &$this->getObject('navbuttons', 'navigation');
// Get the icon class and create an add, edit and delete instance
$objAddIcon = $this->newObject('geticon', 'htmlelements');
$objEditIcon = $this->newObject('geticon', 'htmlelements');
$objDelIcon = $this->newObject('geticon', 'htmlelements');

//Added 2006/07/24 by Serge Meunier for icon upload
$objUploadIcon = $this->newObject('geticon', 'htmlelements');
$objExistIcon = $this->newObject('geticon', 'htmlelements');

//$objModule = &$this->getObject('modulesadmin','modulelist');
$objModule = &$this->getObject('modules','modulecatalogue');
// Create the header
$this->objH = &$this->getObject('htmlheading', 'htmlelements');
$this->objH->type = 1; //Set heading <h1>
$this->objH->str = $objLanguage->languageText("mod_commenttypeadmin_title",'commenttypeadmin');
// Create a table
$this->Table = $this->newObject('htmltable', 'htmlelements');
$this->Table->cellspacing = "2";
$this->Table->cellpadding = "2";
$this->Table->width = "90%";
$this->Table->attributes = "border='0'";
// Create the array for the table header
$tableRow = array();
$tableHd[] = $this->objLanguage->languageText('mod_commenttypeadmin_type','commenttypeadmin');
$tableHd[] = $this->objLanguage->languageText('mod_commenttypeadmin_title','commenttypeadmin');
$tableHd[] = $this->objLanguage->languageText('mod_commenttypeadmin_datecreatedhd','commenttypeadmin');
$tableHd[] = $this->objLanguage->languageText('mod_commenttypeadmin_creatoridhd','commenttypeadmin');
$tableHd[] = $this->objLanguage->languageText('mod_commenttypeadmin_datemodifiedhd','commenttypeadmin');
$tableHd[] = $this->objLanguage->languageText('mod_commenttypeadmin_modifieridhd','commenttypeadmin');
$tableHd[] = $this->objLanguage->languageText('mod_commenttypeadmin_actionhd','commenttypeadmin');
//Added 2006/07/24 by Serge Meunier  for icon upload
$tableHd[] = $this->objLanguage->languageText('mod_commenttypeadmin_iconhd','commenttypeadmin');
// Create the table header for display
$this->Table->addHeader($tableHd, "heading");
// Loop through and display the records
$rowcount = 0;

//------------
//Added 2006/07/25 Serge Meunier - To make sure you cannot delete a type with comments attached to it
if ($objModule->checkIfRegistered('comment', 'comment')){
    $comReg=TRUE;
} else {
    $comReg=FALSE;
}

if ($comReg){
    $objDbComment = &$this->getObject('dbcomment', 'comment');
}
//-------------

if (isset($ar)) {
    if (count($ar) > 0) {

    
        foreach ($ar as $line) {
            $oddOrEven = ($rowcount == 0) ? "odd" : "even";
            $tableRow[] = $line['type'];
            $tableRow[] = $line['title'];
            $tableRow[] = $line['datecreated'];
            $tableRow[] = $this->objUser->fullName($line['creatorid']);
            $tableRow[] = $line['datemodified'];
            $modifierId = $line['modifierid'];
            if ($modifierId != "") {
                $tableRow[] = $this->objUser->fullName($modifierId);
            } else {
                $tableRow[] = "";
            } 
            
            //-----------------
            //Modified 2006/07/25 Serge Meunier - To make sure you cannot delete a type with comments attached to it
            if ($comReg)
            {
                $where = "WHERE type = '" . $line['type'] . "'";
                $commentCount = $objDbComment->getRecordCount($where);
            }
            else
            {
                $commentCount = 0;
            }

            // The URL for the edit link
            $editLink = $this->uri(array('action' => 'edit',
                   'id' => $line['id']));
            $objEditIcon->alt = $this->objLanguage->languageText("mod_commenttypeadmin_editalt",'commenttypeadmin');
            $ed = $objEditIcon->getEditIcon($editLink);
                
            if ($commentCount > 0)
            {
                $tableRow[] = $ed;
            }else{
                // The delete icon with link uses confirm delete utility
                $objDelIcon->setIcon("delete");
                $objDelIcon->alt = $this->objLanguage->languageText("mod_commenttypeadmin_delalt",'commenttypeadmin');
                $delLink = $this->uri(array('action' => 'delete',
                    'confirm' => 'yes',
                    'id' => $line['id']));
                $objConfirm = &$this->newObject('confirm', 'utilities');
                $rep = array('ITEM' => $line['title']);
                $objConfirm->setConfirm($objDelIcon->show(),
                $delLink, $this->objLanguage->code2Txt("mod_commenttypeadmin_confirm",'commenttypeadmin', $rep));
                $conf = $objConfirm->show();
                $tableRow[] = $ed . $conf;
            }
            //--------------
            
            //--------------
            //Added 2006/07/24 by Serge Meunier - to allow icon upload for types
            $path = $this->objConfig->getskinRoot() . "/_common/icons/";
            $filename = "comment" . $line['type'] . ".gif";
            if (file_exists($path . $filename)){
                $iconname = "comment" . $line['type'];
                $objExistIcon->setIcon($iconname);
                $objExistIcon->alt = $line['title'];
                $exist = $objExistIcon->Show();

                $uploadLink = $this->uri(array('action' => 'upload',
                    'id' => $line['id']));
                $objUploadIcon->alt = $this->objLanguage->languageText("mod_commenttypeadmin_upload",'commenttypeadmin');
                $upload = $objUploadIcon->getUploadIcon($uploadLink);
            }else{
                $objExistIcon->setIcon("redcross");
                $objExistIcon->alt = $this->objLanguage->languageText("word_yes");
                $exist = $objExistIcon->Show();
                
                $uploadLink = $this->uri(array('action' => 'upload', 'type' => $line['type'],
                    'id' => $line['id']));
                $objUploadIcon->alt = $this->objLanguage->languageText("mod_commenttypeadmin_upload",'commenttypeadmin');
                $upload = $objUploadIcon->getUploadIcon($uploadLink);
            }
            $tableRow[] = $exist . "&nbsp;" . $upload;
            //--------------
            
            // Add the row to the table for output
            $this->Table->addRow($tableRow, $oddOrEven);
            $tableRow = array(); // clear it out 
            // Set rowcount for bitwise determination of odd or even
            $rowcount = ($rowcount == 0) ? 1 : 0;
        } 
    } 
} 
"<span class='noRecordsMessage'>" . $this->objLanguage->languageText('mod_commenttypeadmin_nodata','commenttypeadmin') . "</span>";

$allowAdmin = true; //You need to write your security here
if ($allowAdmin) {
    $paramArray = array('action' => 'add');
    $addButton = $objButtons->linkedButton("add", $this->uri($paramArray)); 
    // Create the header with add icon
    $this->objHAdd = &$this->getObject('htmlheading', 'htmlelements');
    $this->objHAdd->type = 1; //Set heading <h1>
    $this->objHAdd->str = $objLanguage->languageText("mod_commenttypeadmin_title",'commenttypeadmin') . "\n" . $addButton; 
    // Create link to add template
    $objAddLink = &$this->newObject('link', 'htmlelements');
    $objAddLink->link($this->uri(array('action' => 'add')));
    $objAddLink->link = $this->objLanguage->languageText('mod_commenttypeadmin_add','commenttypeadmin'); 
    // Add the header with add icon to the centered layer
    $rightSideColumn = $this->objHAdd->show(); 
    // Add the table to the centered layer
    $rightSideColumn .= $this->Table->show(); 
    // Add no data message
    if (empty($ar)) {
        $rightSideColumn .= "<span class='noRecordsMessage'>" . $this->objLanguage->languageText('mod_commenttypeadmin_nodata','commenttypeadmin') . "</span>";
    } 
    // Add the add link to the centered layer
    $rightSideColumn .= $objAddLink->show();
} else {
    // Add the header to the centered layer
    $rightSideColumn = $this->objH->show(); 
    // Add the table to the centered layer
    $rightSideColumn .= $this->Table->show(); 
    // Add no data message
    if (empty($ar)) {
        $rightSideColumn .= "<span class='noRecordsMessage'>" . $this->objLanguage->languageText('mod_commenttypeadmin_nodata','commenttypeadmin') . "</span>";
    } 
} 
// Set the content of the left side column
$leftSideColumn = $objLanguage->languageText("mod_commenttypeadmin_leftinstructions",'commenttypeadmin');
// Create an instance of the css layout class
$cssLayout = &$this->newObject('csslayout', 'htmlelements');
// Set columns to 2
$cssLayout->setNumColumns(2);
// Add Left column
$cssLayout->setLeftColumnContent($leftSideColumn);
// Add Right Column
$cssLayout->setMiddleColumnContent($rightSideColumn);
// Output the content to the page
echo $cssLayout->show();

?>
