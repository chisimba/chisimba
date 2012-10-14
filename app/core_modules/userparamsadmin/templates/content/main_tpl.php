<?php
/**
 * 
 * Template for displaying data for the current module
 *
 * @version $Id: main_tpl.php 17302 2010-03-28 13:09:34Z dkeats $
 * @copyright 2005
 * 
 **/


//View template for table: tbl_userparamsadmin
//Note that you will probably need to edit this to make it actually work


//Set up the button class to make the edit, add and delet icons
$objButtons = & $this->getObject('navbuttons', 'navigation');

//Create the centered area for display
$this->center = $this->objConfig = & $this->getObject('layer', 'htmlelements');

$allowAdmin = True; //You need to write your security here

// Add the heading to the content
$this->objH =& $this->getObject('htmlheading', 'htmlelements');
$this->objH->type=1; //Heading <h1>
if ($allowAdmin) {
    $paramArray = array('action' => 'add');
    $this->objH->str=$objLanguage->languageText("mod_userparamsadmin_title",'userparamsadmin')
      ."&nbsp;".$objButtons->linkedButton("add", $this->uri($paramArray));
  } else {  
        $this->objH->str=$objLanguage->languageText("mod_userparamsadmin_title",'userparamsadmin');
       }
$this->center->addToStr($this->objH->show());       
//Create a table
$this->Table = &$this->newObject('htmltable', 'htmlelements');
$tab =& $this->getObject('tabpane','htmlelements');

$this->Table->cellspacing="2";
$this->Table->cellpadding="2";
$this->Table->width="90%";
//Create the array for the table header
$tableRow=array();
$tableHd[]=$objLanguage->languageText("mod_userparamsadmin_pname",'userparamsadmin');
$tableHd[]=$objLanguage->languageText("mod_userparamsadmin_pvalue",'userparamsadmin');
$tableHd[]=$objLanguage->languageText("phrase_addedby");
//$tableHd[]=$objLanguage->languageText("phrase_dateadded");
//$tableHd[]=$objLanguage->languageText("phrase_modifiedby");
//$tableHd[]=$objLanguage->languageText("phrase_datemodified");
if($allowAdmin){
$tableHd[]=$objLanguage->languageText("mod_userparamsadmin_action",'userparamsadmin');
}

//Get the icon class and create an add, edit and delete instance
$objAddIcon = $this->newObject('geticon', 'htmlelements');
$objEditIcon = $this->newObject('geticon', 'htmlelements');
$objDelIcon = $this->newObject('geticon', 'htmlelements');

//Create the table header for display
$this->Table->addHeader($tableHd, "heading");

//Loop through and display the records
$rowcount = 0;
if (isset($ar['root']['Settings'][1])){
    $ar = $ar['root']['Settings'][0];
} else {
	$ar = $ar['root']['Settings'];
}
if (isset($ar)) {
    if (count($ar) > 0) {
        foreach ($ar as $key =>$value) {
            $oddOrEven = ($rowcount == 0) ? "odd" : "even";
            if($key=='creatorId'||$key=='dateCreated'){
            	continue;
            } else {
	        $tableRow[] = $key;
                $tableRow[]= $value;
                $tableRow[]=$this->objUser->fullName();
            	#----------------add to tableadmin
                //The URL for the edit link
                $editLink=$this->uri(array('action' => 'edit',
                  'key' => $key,
                  'value'=>$value), 'userparamsadmin');
                $rep = array('PARAM', $key);
                $objEditIcon->alt=$this->objLanguage->code2Txt("mod_userparams_edit",'userparamsadmin');
                $ed = $objEditIcon->getEditIcon($editLink);

                // The delete icon with link uses confirm delete utility
                $objDelIcon->setIcon("delete");
                $rep = array('PARAM' => $key);
                $objDelIcon->alt=$this->objLanguage->code2Txt("mod_userparams_delete",'userparamsadmin');
                $delLink = $this->uri(array(
                      'action' => 'delete',
                      'confirm' => 'yes',
                  'key' => $key,
                  'value'=>$value), 'userparamsadmin');
                $objConfirm=&$this->newObject('confirm','utilities');
                $rep = array('PARAM', $key);
                $objConfirm->setConfirm($objDelIcon->show(),
                   $delLink,$this->objLanguage->code2Txt("mod_userparams_confirmdelete",'userparamsadmin'));
                $conf = $objConfirm->show();
          
                if ($allowAdmin) {
                    $editArray = array('action' => 'edit',
                      'id' => $key);
                    $deleteArray = array('action' => 'delete',
                      'id' => $key);
                     $tableRow[]=$ed."&nbsp;".$conf;

                    //Add the row to the table for output
                   $this->Table->addRow($tableRow, $oddOrEven);
                   $tableRow=array(); // clear it out
                   // Set rowcount for bitwise determination of odd or even
                   $rowcount = ($rowcount == 0) ? 1 : 0;
                   //$tmp++;
                }
            }
        }
    }
}

//Add the table to the centered layer
$this->center->addToStr($this->Table->show());
// Create link to add template
$objAddLink = &$this->newObject('link', 'htmlelements');
$objAddLink->link($this->uri(array('action' => 'add')));
$objAddLink->link = $objLanguage->languageText('mod_userparamsadmin_addnew','userparamsadmin');
//Add the add link to the centered layer
$this->center->addToStr($objAddLink->show());
//Output the content to the page
$tab->addTab(array('name'=>'Userparams','url'=>'http://localhost','content' => $this->center->show()),'luna-tab-style-sheet');
echo $tab->show();
?>