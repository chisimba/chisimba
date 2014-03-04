<?php

/**
 *template for per diem output
 */
 
 //language elements
  $save  = $this->objLanguage->languageText('word_submit');
  $edit  = $this->objLanguage->languageText('word_edit');
  $next =  $this->objLanguage->languageText('phrase_next');
  
  // heading 
  $this->objMainheading =& $this->getObject('htmlheading','htmlelements');
  $this->objMainheading->type=1;
  $this->objMainheading->str='Equipment expense Information';
  
  //create buttons
   $strsave = ucfirst($save);
   $this->loadclass('button','htmlelements');
   $this->objSave  = new button('submit', $strsave);
   $this->objSave->setToSubmit();
      
   $stredit = ucfirst($edit);
   $this->objEdit  = new button('edit', $stredit);
   $this->objEdit->setToSubmit();
/************************************************************************************************************************************************/  
  $sessionEquipment [] = $this->getSession('equipmentdetails');
 		if(!empty($sessionEquipment)){
//Create table to display itinerary details in session and the rates for breakfast, lunch and dinner and the total rate 
  $objEquipmentTable =& $this->newObject('htmltable', 'htmlelements');
  $objEquipmentTable->cellspacing = '2';
  $objEquipmentTable->cellpadding = '2';
  $objEquipmentTable->border='1';
  $objEquipmentTable->width = '80%';
  
  $objEquipmentTable->startHeaderRow();
  $objEquipmentTable->addHeaderCell('Date ');
  $objEquipmentTable->addHeaderCell('Vendor' );
  $objEquipmentTable->addHeaderCell('Description');
  $objEquipmentTable->addHeaderCell('Currency');
  $objEquipmentTable->addHeaderCell('Cost');
  $objEquipmentTable->addHeaderCell('Exchange Rate');
  $objEquipmentTable->addHeaderCell('Exchange Rate Online Source');
  $objEquipmentTable->addHeaderCell('Exchange Rate Document');
  $objEquipmentTable->addHeaderCell('Receipt');
  $objEquipmentTable->addHeaderCell('Affidavit');
//  $objEquipmentTable->addHeaderCell('Total Equipment Rate');
  
  $objEquipmentTable->endHeaderRow();

  
  $rowcount = '0';
  
  foreach($sessionEquipment as $sesDat){
     
  $oddOrEven = ($rowcount == 0) ? "odd" : "even";
     
  $objEquipmentTable->startRow();
  $objEquipmentTable->addCell($sesDat['date'], '', '', '', $oddOrEven);
  $objEquipmentTable->addCell($sesDat['vendorname'], '', '', '', $oddOrEven);
  $objEquipmentTable->addCell($sesDat['equipdescription'], '', '', '', $oddOrEven);
  $objEquipmentTable->addCell($sesDat['currency'], '', '', '', $oddOrEven);
  $objEquipmentTable->addCell($sesDat['equipcost'], '', '', '', $oddOrEven);
  $objEquipmentTable->addCell($sesDat['exchangerate'], '', '', '', $oddOrEven);
  $objEquipmentTable->addCell($sesDat['quotesource'], '', '', '', $oddOrEven);
  $objEquipmentTable->addCell($sesDat['equipexchratefile'], '', '', '', $oddOrEven);
  $objEquipmentTable->addCell($sesDat['attachreceipt'], '', '', '', $oddOrEven);
  $objEquipmentTable->addCell($sesDat['affidavitfilename'], '', '', '', $oddOrEven);
//  $objEquipmentTable->addCell($sesDat['affidavitfilename'], '', '', '', $oddOrEven);
//  $objEquipmentTable->addCell('');
//  $objEquipmentTable->addCell($sesDat['totroomrate'], '', '', '', $oddOrEven);            //need to fix up
  $objEquipmentTable->endRow();
  
  /**$objEquipmentTable->startRow();
  $objEquipmentTable->addCell('');
  $objEquipmentTable->addCell($sesDat['rateto'], '', '', '', $oddOrEven);
  $objEquipmentTable->endRow();*/

  }
}
/*************************************************************************************************************************************************/
/**
 *create form to place save and edit button on
 */
$this->loadClass('form','htmlelements');
$objForm = new form('claiminfo',$this->uri(array('action'=>'Equipmentoutput')));
$objForm->displayType = 3;
$objForm->addToForm($this->objSave->show() . ' ' . $this->objEdit->show());// . ' ' . $this->objNext->show());	


/*************************************************************************************************************************************************/
//display all headings
echo "<div align=\"center\">" . $this->objMainheading->show(). "</div>". '<br />' . '<br />';
if(!empty($sessionEquipment)){
  echo "<div align=\"left\">" . $objEquipmentTable->show() . "</div>";
} 
echo '<br />' . '<br />'. '<br />'.'<br />';
echo "<div align=\"left\">" .$objForm->show(). "</div>"; 

?>
