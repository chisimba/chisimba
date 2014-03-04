<?php

/**
 *template for per incident output
 */
 
 //language elements
  $save  = $this->objLanguage->languageText('word_submit');
  $edit  = $this->objLanguage->languageText('word_edit');
  $next =  $this->objLanguage->languageText('phrase_next');
  
  // heading 
  $this->objMainheading =& $this->getObject('htmlheading','htmlelements');
  $this->objMainheading->type=1;
  $this->objMainheading->str='Incident Information';
  
  //create buttons
   $strsave = ucfirst($save);
   $this->loadclass('button','htmlelements');
   $this->objSave  = new button('submit', $strsave);
   $this->objSave->setToSubmit();
      
   $stredit = ucfirst($edit);
   $this->objEdit  = new button('edit', $stredit);
   $this->objEdit->setToSubmit();
   
   $formexplanation = $this->objLanguage->languageText('mod_onlineinvoice_formexplanation','onlineinvoice');
   $submitaction  = $this->objLanguage->languageText('mod_onlineinvoice_submitaction','onlineinvoice');
   $editaction  = $this->objLanguage->languageText('mod_onlineinvoice_editaction','onlineinvoice');
   
   $helpstring  = $formexplanation . '<br />' .$submitaction . '<br />'  . $editaction;
   $this->objHelp=& $this->getObject('helplink','help');
   $displayhelp  = $this->objHelp->show($helpstring);  
/************************************************************************************************************************************************/  
  $sessionIncident  = $this->getSession('incidentdetails');
 if(!empty($sessionIncident)){
//Create table to display itinerary details in session and the rates for breakfast, lunch and dinner and the total rate
 
  $objTable = & $this->newObject('htmltable', 'htmlelements');
  $objTable->cellspacing = '2';
  $objTable->cellpadding = '2';
  $objTable->border='1';
  $objTable->width = '100%';
  
  $objTable->startHeaderRow();
  $objTable->addHeaderCell('Date ');
  $objTable->addHeaderCell('Vendor' );
  $objTable->addHeaderCell('Description');
  $objTable->addHeaderCell('Rate');
  $objTable->addHeaderCell('Currency');
  $objTable->addHeaderCell('Exchange Rate');
  $objTable->addHeaderCell('Exchange File');
  $objTable->addHeaderCell('Exchange Online Source');
 // $objTable->addHeaderCell('Incident Rate File');
  $objTable->addHeaderCell('Receipt');
  $objTable->addHeaderCell('Affidavit');
  $objTable->endHeaderRow();

  
  $rowcount = '0';
  
  foreach($sessionIncident as $sesIncident){
     
  $oddOrEven = ($rowcount == 0) ? "odd" : "even";
     
  $objTable->startRow();
  $objTable->addCell($sesIncident['date'], '', '', '', $oddOrEven);
  $objTable->addCell($sesIncident['vendor'], '', '', '', $oddOrEven);
  $objTable->addCell($sesIncident['description'], '', '', '', $oddOrEven);
  $objTable->addCell($sesIncident['cost'], '', '', '', $oddOrEven);
  $objTable->addCell($sesIncident['currency'], '', '', '', $oddOrEven);
  $objTable->addCell($sesIncident['exchangerate'], '', '', '', $oddOrEven);
  $objTable->addCell($sesIncident['incidentratefile'], '', '', '', $oddOrEven);
  $objTable->addCell($sesIncident['quotesource'], '', '', '', $oddOrEven);
  //$objTable->addCell($sesIncident['incidentratefile'], '', '', '', $oddOrEven);
  $objTable->addCell($sesIncident['receiptfiles'], '', '', '', $oddOrEven);
  $objTable->addCell($sesIncident['affidavitfiles'], '', '', '', $oddOrEven);
  $objTable->endRow();
  }
}

/**************************************************************************************************************************************************/
$this->loadClass('tabbedbox', 'htmlelements');
$objcreatetab = new tabbedbox();
$objcreatetab->addTabLabel('Incident Information');
$objcreatetab->addBoxContent("<div align=\"right\">" .$displayhelp. "</div>".'<br />'  . $objTable->show() . '<br />'. '<br />'  . $this->objSave->show() . ' ' . $this->objEdit->show());

/*************************************************************************************************************************************************/
/**
 *create form to place save and edit button on
 */
$this->loadClass('form','htmlelements');
$objForm = new form('incidentinfo',$this->uri(array('action'=>'incidentoutput')));
$objForm->displayType = 3;
$objForm->addToForm($objcreatetab->show());// . ' ' . $this->objNext->show());	


/*************************************************************************************************************************************************/
//display all headings
echo "<div align=\"center\">" . $this->objMainheading->show(). "</div>". '<br />' . '<br />';

//echo '<br />' . '<br />'. '<br />'.'<br />';
echo "<div align=\"left\">" .$objForm->show(). "</div>"; 

?>
