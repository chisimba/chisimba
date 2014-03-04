<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package LRS SOC
*/

/**
* Soc name list template for the LRS SOC
* Author Warren Windvogel
*/


//Create htmlheading for page header
$objH = $this->newObject('htmlheading', 'htmlelements');
$objH->type = '1';
$objH->str = $this->objLanguage->languageText('mod_lrssoc_searchresultsfor','award')." '$searchTerm'";

//Load the form class
$this->loadClass('form', 'htmlelements');
//Load the textarea class
$this->loadClass('textarea', 'htmlelements');
//Load the textinput class
$this->loadClass('textinput', 'htmlelements');
//Load the button class
$this->loadClass('button', 'htmlelements');
//Load the tabbed box class
$this->loadClass('tabbedbox', 'htmlelements');
//Load the label class
$this->loadClass('label', 'htmlelements');
//Load the dropdown class
$this->loadClass('dropdown', 'htmlelements');

//Create htmltable for selectmajorgroup form elements
$objSelectTable =$this->newObject('htmltable', 'htmlelements');
$objSelectTable->cellspacing = '2';
$objSelectTable->cellpadding = '2';
$objSelectTable->width = '100%';

//Set table header row
$objSelectTable->startHeaderRow();
$objSelectTable->addHeaderCell($this->objLanguage->languageText('mod_lrssoc_occupationname','award'), '', '', '', '');
$objSelectTable->addHeaderCell($this->objLanguage->languageText('mod_lrssoc_majorgroup','award'), '', '', '', '');
$objSelectTable->addHeaderCell($this->objLanguage->languageText('mod_lrssoc_submajorgroup','award'), '', '', '', '');
$objSelectTable->addHeaderCell($this->objLanguage->languageText('mod_lrssoc_minorgroup','award'), '', '', '', '');
$objSelectTable->addHeaderCell($this->objLanguage->languageText('mod_lrssoc_unitgroup','award'), '', '', '', '');
$objSelectTable->addHeaderCell('','5%', '' ,'' ,'');
$objSelectTable->endHeaderRow();

if(count($socNames) > '0'){
  $rowcount = '0';
  foreach($socNames as $socName){
     $rowcount++; 
     // Set odd even colour scheme
     $class = ($rowcount % 2 == 0)?'odd':'even'; 
     //Get group description
     $socNameName = $socName['name'];
     //Get group id
     $socNameId = $socName['id'];
     //Get major group data
     //Get group id
     $majorGroupId = $socName['major_groupid'];
     //Get array of major group data
     $majorGroup = $this->objDbSocMajorGroup->getRow('id', $majorGroupId);
     //Get group desc 
     $majorGroupDesc = $majorGroup['description'];
     //Get sub major group data
     //Get group id
     $subMajorGroupId = $socName['submajor_groupid'];
     //Get array of major group data
     $subMajorGroup = $this->objDbSubMajorGroups->getRow('id', $subMajorGroupId);
     //Get group desc 
     $subMajorGroupDesc = $subMajorGroup['description'];
     //Get minor group data
     //Get group id
     $minorGroupId = $socName['minor_groupid'];
     //Get array of minor group data
     $minorGroup = $this->objDbMinorGroups->getRow('id', $minorGroupId);
     //Get group desc 
     $minorGroupDesc = $minorGroup['description'];
     //Get unit group data
     //Get group id
     $unitGroupId = $socName['unit_groupid'];
     //Get array of unit group data
     $unitGroup = $this->objDbUnitGroups->getRow('id', $unitGroupId);
     //Get group desc 
     $unitGroupDesc = $unitGroup['description'];
     // Create delete icon
     $param = array('action' => 'deletesocname', 'socNameId' => $socNameId,'selected'=>'init_10');
     $objDelIcon = $this->newObject('geticon', 'htmlelements');
     $deletephrase = $this->objLanguage->languageText('mod_lrssoc_deletesocname','award');
     $deleteIcon = $objDelIcon->getDeleteIconWithConfirm($socNameId, $param, 'lrssoc', $deletephrase); 
     // Create edit icon
     $param = $this->uri(array('action'=>'editsocname', 'socNameId'=>$socNameId,
                               'selected'=>'init_10', 'unitGroupId'=>$unitGroupId,
                               'minorGroupId'=>$minorGroupId, 'subMajorGroupId'=>$subMajorGroupId,
                               'majorGroupId'=>$majorGroupId, 'searchterm'=>$searchTerm));
     $objEditIcon = $this->newObject('geticon', 'htmlelements');
     $objEditIcon->alt = $this->objLanguage->languageText('mod_lrssoc_editsocname','award');
     $editIcon = $objEditIcon->getEditIcon($param); 

     //Add minor groups to table
     $objSelectTable->startRow($class);
     $objSelectTable->addCell($socNameName);
     $objSelectTable->addCell($majorGroupDesc);
     $objSelectTable->addCell($subMajorGroupDesc);
     $objSelectTable->addCell($minorGroupDesc);
     $objSelectTable->addCell($unitGroupDesc);
     $objSelectTable->addCell($editIcon);//.'&nbsp;'.$deleteIcon);
     $objSelectTable->endRow();

  }
 } else {
     //Add no records message to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($this->objLanguage->languageText('mod_lrssoc_noresultsforsearch','award'), '', '', '', 'noRecordsMessage', 'colspan=6');
     $objSelectTable->endRow();
}

// set up exit link
$objLink = $this->newObject('link', 'htmlelements');
$objLink->link($this->uri(array('action'=>'admin')));
$objLink->link = $this->objLanguage->languageText('word_exit');
$exitLink = $objLink->show();
$objLink->link($this->uri(array('action'=>'selectmajorgroup', 'selected'=>'init_10')));
$objLink->link = $this->objLanguage->languageText('word_back');
$backLink = $objLink->show();

//Add content to the output layer
$middleColumnContent = $objH->show().$objSelectTable->show()."$backLink &nbsp;&nbsp; $exitLink";

echo $middleColumnContent;

?>
