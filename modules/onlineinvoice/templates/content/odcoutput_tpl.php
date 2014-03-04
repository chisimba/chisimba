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
  $this->objMainheading->str='ODC expense Information';
  
  //create buttons
   $strsave = ucfirst($save);
   $this->loadclass('button','htmlelements');
   $this->objSave  = new button('submit', $strsave);
   $this->objSave->setToSubmit();
      
   $stredit = ucfirst($edit);
   $this->objEdit  = new button('edit', $stredit);
   $this->objEdit->setToSubmit();
/************************************************************************************************************************************************/  
  $sessionODC = $this->getSession('odcdetails');
 		if(!empty($sessionODC)){
//Create table to display itinerary details in session and the rates for breakfast, lunch and dinner and the total rate 
  $objODCTable =& $this->newObject('htmltable', 'htmlelements');
  $objODCTable->cellspacing = '2';
  $objODCTable->cellpadding = '2';
  $objODCTable->border='1';
  $objODCTable->width = '80%';
  
  $objODCTable->startHeaderRow();
  $objODCTable->addHeaderCell('Date ');
  $objODCTable->addHeaderCell('Vendor' );
  $objODCTable->addHeaderCell('Description');
  $objODCTable->addHeaderCell('Currency');
  $objODCTable->addHeaderCell('Cost');
  $objODCTable->addHeaderCell('Exchange Rate');
  $objODCTable->addHeaderCell('Exchange Rate Online Source');
  $objODCTable->addHeaderCell('Exchange Rate Document');
  $objODCTable->addHeaderCell('Receipt');
  $objODCTable->addHeaderCell('Affidavit');
//  $objODCTable->addHeaderCell('Total ODC Rate');
  
  $objODCTable->endHeaderRow();

  
  $rowcount = '0';
  
  foreach($sessionODC as $sesDat){
     
  $oddOrEven = ($rowcount == 0) ? "odd" : "even";
     
  $objODCTable->startRow();
  $objODCTable->addCell($sesDat['date'], '', '', '', $oddOrEven);
  $objODCTable->addCell($sesDat['vendorname'], '', '', '', $oddOrEven);
  $objODCTable->addCell($sesDat['odcdescription'], '', '', '', $oddOrEven);
  $objODCTable->addCell($sesDat['currency'], '', '', '', $oddOrEven);
  $objODCTable->addCell($sesDat['odccost'], '', '', '', $oddOrEven);
  $objODCTable->addCell($sesDat['exchangerate'], '', '', '', $oddOrEven);
  $objODCTable->addCell($sesDat['quotesource'], '', '', '', $oddOrEven);
  $objODCTable->addCell($sesDat['odcexchratefile'], '', '', '', $oddOrEven);
  $objODCTable->addCell($sesDat['attachreceipt'], '', '', '', $oddOrEven);
  $objODCTable->addCell($sesDat['affidavitfilename'], '', '', '', $oddOrEven);
//  $objODCTable->addCell($sesDat['affidavitfilename'], '', '', '', $oddOrEven);
//  $objODCTable->addCell('');
//  $objODCTable->addCell($sesDat['totroomrate'], '', '', '', $oddOrEven);            //need to fix up
  $objODCTable->endRow();
  
  /**$objODCTable->startRow();
  $objODCTable->addCell('');
  $objODCTable->addCell($sesDat['rateto'], '', '', '', $oddOrEven);
  $objODCTable->endRow();*/

  }
}
/*************************************************************************************************************************************************/
/**
 *create form to place save and edit button on
 */
$this->loadClass('form','htmlelements');
$objForm = new form('claiminfo',$this->uri(array('action'=>'odcoutput')));
$objForm->displayType = 3;
$objForm->addToForm($this->objSave->show() . ' ' . $this->objEdit->show());// . ' ' . $this->objNext->show());	


/*************************************************************************************************************************************************/
//display all headings
echo "<div align=\"center\">" . $this->objMainheading->show(). "</div>". '<br />' . '<br />';
if(!empty($sessionODC)){
  echo "<div align=\"left\">" . $objODCTable->show() . "</div>";
} 
echo '<br />' . '<br />'. '<br />'.'<br />';
echo "<div align=\"left\">" .$objForm->show(). "</div>"; 

?>
