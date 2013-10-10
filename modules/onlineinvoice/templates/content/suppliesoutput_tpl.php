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
  $this->objMainheading->str='Supplies expense Information';
  
  //create buttons
   $strsave = ucfirst($save);
   $this->loadclass('button','htmlelements');
   $this->objSave  = new button('submit', $strsave);
   $this->objSave->setToSubmit();
      
   $stredit = ucfirst($edit);
   $this->objEdit  = new button('edit', $stredit);
   $this->objEdit->setToSubmit();
/************************************************************************************************************************************************/  
  $sessionSupplies = $this->getSession('supplydetails');
 		if(!empty($sessionSupplies)){
//Create table to display itinerary details in session and the rates for breakfast, lunch and dinner and the total rate 
  $objSuppliesTable =& $this->newObject('htmltable', 'htmlelements');
  $objSuppliesTable->cellspacing = '2';
  $objSuppliesTable->cellpadding = '2';
  $objSuppliesTable->border='1';
  $objSuppliesTable->width = '80%';
  
  $objSuppliesTable->startHeaderRow();
  $objSuppliesTable->addHeaderCell('Date ');
  $objSuppliesTable->addHeaderCell('Vendor' );
  $objSuppliesTable->addHeaderCell('Description');
  $objSuppliesTable->addHeaderCell('Currency');
  $objSuppliesTable->addHeaderCell('Cost');
  $objSuppliesTable->addHeaderCell('Exchange Rate');
  $objSuppliesTable->addHeaderCell('Exchange Rate Online Source');
  $objSuppliesTable->addHeaderCell('Exchange Rate Document');
  $objSuppliesTable->addHeaderCell('Receipt');
  $objSuppliesTable->addHeaderCell('Affidavit');
//  $objSuppliesTable->addHeaderCell('Total Supplies Rate');
  
  $objSuppliesTable->endHeaderRow();

  
  $rowcount = '0';
  
  foreach($sessionSupplies as $sesDat){
     
  $oddOrEven = ($rowcount == 0) ? "odd" : "even";
     
  $objSuppliesTable->startRow();
  $objSuppliesTable->addCell($sesDat['date'], '', '', '', $oddOrEven);
  $objSuppliesTable->addCell($sesDat['vendorname'], '', '', '', $oddOrEven);
  $objSuppliesTable->addCell($sesDat['itemdescription'], '', '', '', $oddOrEven);
  $objSuppliesTable->addCell($sesDat['currency'], '', '', '', $oddOrEven);
  $objSuppliesTable->addCell($sesDat['supplycost'], '', '', '', $oddOrEven);
  $objSuppliesTable->addCell($sesDat['exchangerate'], '', '', '', $oddOrEven);
  $objSuppliesTable->addCell($sesDat['quotesource'], '', '', '', $oddOrEven);
  $objSuppliesTable->addCell($sesDat['supplyexchratefile'], '', '', '', $oddOrEven);
  $objSuppliesTable->addCell($sesDat['attachreceipt'], '', '', '', $oddOrEven);
  $objSuppliesTable->addCell($sesDat['affidavitfilename'], '', '', '', $oddOrEven);
//  $objSuppliesTable->addCell($sesDat['affidavitfilename'], '', '', '', $oddOrEven);
//  $objSuppliesTable->addCell('');
//  $objSuppliesTable->addCell($sesDat['totroomrate'], '', '', '', $oddOrEven);            //need to fix up
  $objSuppliesTable->endRow();
  
  /**$objSuppliesTable->startRow();
  $objSuppliesTable->addCell('');
  $objSuppliesTable->addCell($sesDat['rateto'], '', '', '', $oddOrEven);
  $objSuppliesTable->endRow();*/

  }
}
/*************************************************************************************************************************************************/
/**
 *create form to place save and edit button on
 */
$this->loadClass('form','htmlelements');
$objForm = new form('claiminfo',$this->uri(array('action'=>'suppliesoutput')));
$objForm->displayType = 3;
$objForm->addToForm($this->objSave->show() . ' ' . $this->objEdit->show());// . ' ' . $this->objNext->show());	


/*************************************************************************************************************************************************/
//display all headings
echo "<div align=\"center\">" . $this->objMainheading->show(). "</div>". '<br />' . '<br />';
if(!empty($sessionSupplies)){
  echo "<div align=\"left\">" . $objSuppliesTable->show() . "</div>";
} 
echo '<br />' . '<br />'. '<br />'.'<br />';
echo "<div align=\"left\">" .$objForm->show(). "</div>"; 

?>
