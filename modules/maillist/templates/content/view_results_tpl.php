<?php 
/**
* Template for viewing list subscribers
* 
* @package mailistadmin
*/

//Set layout template
$this->setLayoutTemplate('layout_tpl.php');

//Load classes 
$this->loadClass('button', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('tabbedbox', 'htmlelements');

//Create form to change the current list and view another lists members
// Set form params
$paramArray = array('action' => 'viewsubscribers');
$formAction = $this->uri($paramArray);

$objViewSubscribersForm = new form('viewsubscribers', $formAction);
$objViewSubscribersForm->displayType = 3;

//Create dropdown for the input of list
$objListDropdown = new dropdown('list2');
$objListDropdown->addOption(NULL, $this->objLanguage->languageText('mod_maillistadmin_selectlist'));
//Populate dropdown with lists
foreach($lists as $ar){
  if($ar != $list){
    $objListDropdown->addOption($ar, $ar);
  }  
}  

//Create label for list dropdown
$listLabel = new label($this->objLanguage->languageText('mod_maillistadmin_selectlist'), 'input_list');

// Create a submit button
$objSubmit = new button('submit'); 
// Set the button type to submit
$objSubmit->setToSubmit(); 
// Use the language object to add the word
$objSubmit->setValue(' ' . $this->objLanguage->languageText("word_view") . ' ');

//Create table for form elements
$objFormTable =& $this->newObject('htmltable', 'htmlelements');
$objFormTable->cellspacing = '2';
$objFormTable->cellpadding = '2';
$objFormTable->width = '70%';



//Create table to display data
$objTable =& $this->newObject('htmltable', 'htmlelements');
$objTable->cellspacing = '2';
$objTable->cellpadding = '2';
$objTable->width = '70%';

// Create the array for the table header
$tableRow = array();
$tableHd[] = $this->objLanguage->languageText('mail_listname');
$tableHd[] = $this->objLanguage->languageText('mail_listemail');
// Create the table header for display
$objTable->addHeader($tableHd, "heading");
    
        
//Create htmlheading for title
$objH =& $this->newObject('htmlheading', 'htmlelements');
$objH->type = '1';
$str = 'mod_maillist_success';
$arrOfRep = array('LIST' => $listname);
$objH->str = $this->objLanguage->code2Txt($str, $arrOfRep);

//Create htmlheading for email
$objH1 =& $this->newObject('htmlheading', 'htmlelements');
//$objH1->type = '3';
$str = 'mod_maillist_listemail';
$arrOfRep1 = array('EMAIL' => $listemail);
$objH1->str = $this->objLanguage->code2Txt($str, $arrOfRep1);


//Create link back to default page
$objBack =& $this->newObject('link', 'htmlelements');
$objBack->link($this->uri(array('action'=>''),'maillistadmin'));
$objBack->link = $this->objLanguage->languageText('mod_maillist_back');

//Add content to the output layer
$middleColumnContent = '';
$middleColumnContent .= $objH->show();

$middleColumnContent .= $objH1->show();

//$middleColumnContent .= '<p>'.$objTable->show().'</p>';
$middleColumnContent .= '<p>'.$objBack->show().'</p>';

echo $middleColumnContent;
?>
