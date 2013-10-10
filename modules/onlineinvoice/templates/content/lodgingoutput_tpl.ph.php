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
  $this->objMainheading->str='Lodge Information';
  
  //create buttons
   $strsave = ucfirst($save);
   $this->loadclass('button','htmlelements');
   $this->objSave  = new button('submit', $strsave);
   $this->objSave->setToSubmit();
      
   $stredit = ucfirst($edit);
   $this->objEdit  = new button('edit', $stredit);
   $this->objEdit->setToSubmit();
   
   $formexplanation = $this->objLanguage->languageText('mod_onlineinvoice_formexplanation');
   $submitaction  = $this->objLanguage->languageText('mod_onlineinvoice_submitaction');
   $editaction  = $this->objLanguage->languageText('mod_onlineinvoice_editaction');
   
   $helpstring  = $formexplanation . '<br />' .$submitaction . '<br />'  . $editaction;
   $this->objHelp=& $this->getObject('helplink','help');
   $displayhelp  = $this->objHelp->show($helpstring);
/************************************************************************************************************************************************/  
  $sessionLodge = $this->getSession('lodgedetails');
 if(!empty($sessionLodge)){
//Create table to display itinerary details in session and the rates for breakfast, lunch and dinner and the total rate 
  $objLodgeTable =& $this->newObject('htmltable', 'htmlelements');
  $objLodgeTable->cellspacing = '2';
  $objLodgeTable->cellpadding = '2';
  $objLodgeTable->border='1';
  $objLodgeTable->width = '100%';
  
  $objLodgeTable->startHeaderRow();
  $objLodgeTable->addHeaderCell('Date ');
  $objLodgeTable->addHeaderCell('Vendor' );
  $objLodgeTable->addHeaderCell('Room Rate');
  $objLodgeTable->addHeaderCell('Currency');
  $objLodgeTable->addHeaderCell('Exchange Rate');
  $objLodgeTable->addHeaderCell('Exchange Document');
  $objLodgeTable->addHeaderCell('Exchange Online Source');
  $objLodgeTable->addHeaderCell('Receipt');
  $objLodgeTable->addHeaderCell('Affidavit');
  $objLodgeTable->endHeaderRow();

  
  $rowcount = '0';
  
  foreach($sessionLodge as $sesDat){
     
  $oddOrEven = ($rowcount == 0) ? "odd" : "even";
     
  $objLodgeTable->startRow();
  $objLodgeTable->addCell($sesDat['date'], '', '', '', $oddOrEven);
  $objLodgeTable->addCell($sesDat['vendor'], '', '', '', $oddOrEven);
  $objLodgeTable->addCell($sesDat['cost'], '', '', '', $oddOrEven);
  $objLodgeTable->addCell($sesDat['currency'], '', '', '', $oddOrEven);
  $objLodgeTable->addCell($sesDat['exchangerate'], '', '', '', $oddOrEven);
  $objLodgeTable->addCell($sesDat['quotesource'], '', '', '', $oddOrEven);
  $objLodgeTable->addCell($sesDat['exchangefile'], '', '', '', $oddOrEven);
  $objLodgeTable->addCell($sesDat['receiptfilename'], '', '', '', $oddOrEven);
  $objLodgeTable->addCell($sesDat['affidavitfilename'], '', '', '', $oddOrEven);
  //$objLodgeTable->addCell($sesDat['totroomrate'], '', '', '', $oddOrEven);            need to fix up
  $objLodgeTable->endRow();
  
  /**$objLodgeTable->startRow();
  $objLodgeTable->addCell('');
  $objLodgeTable->addCell($sesDat['rateto'], '', '', '', $oddOrEven);
  $objLodgeTable->endRow();*/

  }
}

$this->loadClass('tabbedbox', 'htmlelements');
$objcreatetab = new tabbedbox();
$objcreatetab->addTabLabel('Lodge Information');
$objcreatetab->addBoxContent("<div align=\"right\">" .$displayhelp. "</div>".'<br />'  . $objLodgeTable->show() . '<br />'. '<br />'  . $this->objSave->show() . ' ' . $this->objEdit->show());
/*************************************************************************************************************************************************/
/**
 *create form to place save and edit button on
 */
$this->loadClass('form','htmlelements');
$objForm = new form('lodgeoutput',$this->uri(array('action'=>'lodgeoutput')));
$objForm->displayType = 3;
$objForm->addToForm($objcreatetab->show());// . ' ' . $this->objNext->show());	


/*************************************************************************************************************************************************/
//display all headings
echo "<div align=\"center\">" . $this->objMainheading->show(). "</div>". '<br />' . '<br />';
//if(!empty($sessionLodge)){
//  echo "<div align=\"left\">" . $objLodgeTable->show() . "</div>";
//} 
//echo '<br />' . '<br />'. '<br />'.'<br />';
echo "<div align=\"left\">" .$objForm->show(). "</div>"; 

?>
