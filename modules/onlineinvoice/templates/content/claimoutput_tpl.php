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
  $this->objMainheading->str='Claimant Information';
  
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
 
 $sessionClaimant []= $this->getSession('claimantdata');
 
 if(!empty($sessionClaimant)){
//Create table to display dates in session and the rates for breakfast, lunch and dinner and the total rate 
  $objClaimantTable =& $this->newObject('htmltable', 'htmlelements');
  $objClaimantTable->cellspacing = '1';
  $objClaimantTable->cellpadding = '2';
  $objClaimantTable->border='1';
  $objClaimantTable->width = '100%';
  $objClaimantTable->cssClass = 'webfx-tab-style-sheet';
  $objClaimantTable->footing = 'Please submit or edit information';
  
  $objClaimantTable->startHeaderRow();
  $objClaimantTable->addHeaderCell('Name');
  $objClaimantTable->addHeaderCell('Title' );
  $objClaimantTable->addHeaderCell('Address');
  $objClaimantTable->addHeaderCell('City');
  $objClaimantTable->addHeaderCell('Province');
  $objClaimantTable->addHeaderCell('Postal Code');
  $objClaimantTable->addHeaderCell('Country');
  $objClaimantTable->addHeaderCell('Travel Purpose');
  $objClaimantTable->endHeaderRow();

  
  $rowcount = '0';
  
  foreach($sessionClaimant as $sesClaim){
     
     $oddOrEven = ($rowcount == 0) ? "odd" : "even";
     
     $objClaimantTable->startRow();
     $objClaimantTable->addCell($sesClaim['name'], '', '', '', $oddOrEven);
     $objClaimantTable->addCell($sesClaim['title'], '', '', '', $oddOrEven);
     $objClaimantTable->addCell($sesClaim['address'], '', '', '', $oddOrEven);
     $objClaimantTable->addCell($sesClaim['city'], '', '', '', $oddOrEven);
     $objClaimantTable->addCell($sesClaim['province'], '', '', '', $oddOrEven);
     $objClaimantTable->addCell($sesClaim['postalcode'], '', '', '', $oddOrEven);
     $objClaimantTable->addCell($sesClaim['country'], '', '', '', $oddOrEven);
     $objClaimantTable->addCell($sesClaim['travelpurpose'], '', '', '', $oddOrEven);
     $objClaimantTable->endRow();
  }
}
/***************************************************************************************************************************************************************/
/**
 *create a tabbed box
 */
 
$this->loadClass('tabbedbox', 'htmlelements');
$objcreateinvtab = new tabbedbox();
$objcreateinvtab->addTabLabel('Traveler Information');
$objcreateinvtab->addBoxContent("<div align=\"right\">" . $displayhelp . "</div>".'<br />' .$objClaimantTable->show() . '<br />' . "<div align=\"left\">" . $this->objSave->show() . ' ' . $this->objEdit->show()  ."</div>" . '<br />');
 

/***************************************************************************************************************************************************************/ 
/**
 *create form to place save and edit button on
 */
$this->loadClass('form','htmlelements');
$objForm = new form('claiminfo',$this->uri(array('action'=>'claimantoutput')));
$objForm->displayType = 3;
$objForm->addToForm($objcreateinvtab->show());// . ' ' . $this->objEdit->show());// . ' ' . $this->objNext->show());	

echo "<div align=\"center\">" . $this->objMainheading->show(). "</div>". '<br />' . '<br />';

if(!empty($sessionClaimant)){
  echo "<div align=\"left\">" . $objForm->show() . "</div>";
}

//echo '<br />' . '<br />'. '<br />'.'<br />';
//echo "<div align=\"left\">" .$objForm->show(). "</div>"; 
 

?>
