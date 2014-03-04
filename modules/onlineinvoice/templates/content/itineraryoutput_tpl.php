<?php

/**
 *create claimant info output
 */
  //language elements
  $save  = $this->objLanguage->languageText('word_submit');
  $edit  = $this->objLanguage->languageText('word_edit');
  $next =  $this->objLanguage->languageText('phrase_next');
  
  // heading 
  $this->objMainheading =& $this->getObject('htmlheading','htmlelements');
  $this->objMainheading->type=1;
  $this->objMainheading->str='Intinerary Information';
  
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
   
/******************************************************************************************************************************/ 
 
 $sessionItinerary = $this->getSession('addmultiitinerary');
 
 if(!empty($sessionItinerary)){
//Create table to display dates in session and the rates for breakfast, lunch and dinner and the total rate 
  $objItineraryTable =& $this->newObject('htmltable', 'htmlelements');
  $objItineraryTable->cellspacing = '10';
  $objItineraryTable->cellpadding = '2';
  $objItineraryTable->border='2';
  $objItineraryTable->width = '100%';
  
  $objItineraryTable->startHeaderRow();
  $objItineraryTable->addHeaderCell('Departure Date');
  $objItineraryTable->addHeaderCell('Departure Time' );
  $objItineraryTable->addHeaderCell('Departure City');
  $objItineraryTable->addHeaderCell('Arrival Date');
  $objItineraryTable->addHeaderCell('Arrival Time');
  $objItineraryTable->addHeaderCell('Arrival City');
  $objItineraryTable->endHeaderRow();

  
  $rowcount = '0';
  
  foreach($sessionItinerary as $sesItinerary){
     
     $oddOrEven = ($rowcount == 0) ? "odd" : "even";
     
     $objItineraryTable->startRow();
     $objItineraryTable->addCell($sesItinerary['departuredate'], '', '', '', $oddOrEven);
     $objItineraryTable->addCell($sesItinerary['departuretime'], '', '', '', $oddOrEven);
     $objItineraryTable->addCell($sesItinerary['departurecity'], '', '', '', $oddOrEven);
     $objItineraryTable->addCell($sesItinerary['arrivaledate'], '', '', '', $oddOrEven);
     $objItineraryTable->addCell($sesItinerary['arrivaltime'], '', '', '', $oddOrEven);
     $objItineraryTable->addCell($sesItinerary['arrivalcity'], '', '', '', $oddOrEven);
     $objItineraryTable->endRow();
  }
}
/*************************************************************************************************************************************************/
$this->loadClass('tabbedbox', 'htmlelements');
$objcreateinvtab = new tabbedbox();
$objcreateinvtab->addTabLabel('Traveler Itinerary');
$objcreateinvtab->addBoxContent("<div align=\"right\">" .$displayhelp. "</div>".'<br />'  . $objItineraryTable->show() . '<br />'. '<br />'  . $this->objSave->show() . ' ' . $this->objEdit->show());
/*************************************************************************************************************************************************/
/**
 *create form to place save and edit button on
 */
$this->loadClass('form','htmlelements');
$objForm = new form('initineraryout',$this->uri(array('action'=>'itineraryoutput')));
$objForm->displayType = 3;
$objForm->addToForm($objcreateinvtab->show());// . ' ' . $this->objNext->show());	

echo "<div align=\"center\">" . $this->objMainheading->show(). "</div>". '<br />' . '<br />';

if(!empty($sessionItinerary)){
  echo "<div align=\"left\">" . $objForm->show() . "</div>";
}

echo '<br />' . '<br />'. '<br />'.'<br />';
//echo "<div align=\"left\">" .$objForm->show(). "</div>"; 
 
?>
