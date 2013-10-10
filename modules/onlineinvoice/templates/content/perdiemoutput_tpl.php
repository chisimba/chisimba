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
  $this->objMainheading->str='Per Diem Information';
  
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
 $sessionPerdiem = $this->getSession('perdiemdetails');
 if(!empty($sessionPerdiem)){
//Create table to display itinerary details in session and the rates for breakfast, lunch and dinner and the total rate 
  $objExpByDateTable =& $this->newObject('htmltable', 'htmlelements');
  $objExpByDateTable->cellspacing = '2';
  $objExpByDateTable->cellpadding = '2';
  $objExpByDateTable->border='1';
  $objExpByDateTable->width = '100%';
  
  $objExpByDateTable->startHeaderRow();
  $objExpByDateTable->addHeaderCell('Date ');
  $objExpByDateTable->addHeaderCell('Breakfast Location' );
  $objExpByDateTable->addHeaderCell('Breakfast Rate');
  $objExpByDateTable->addHeaderCell('Lunch Location');
  $objExpByDateTable->addHeaderCell('Lunch Rate');
  $objExpByDateTable->addHeaderCell('Dinner Location');
  $objExpByDateTable->addHeaderCell('Dinner Rate');
  $objExpByDateTable->addHeaderCell('Total');
//  $objExpByDateTable->addHeaderCell('');
//  $objExpByDateTable->addHeaderCell('Per Diem Total');
  $objExpByDateTable->endHeaderRow();

  
  $rowcount = '0';
  
  foreach($sessionPerdiem as $sesDat){
     
  $oddOrEven = ($rowcount == 0) ? "odd" : "even";
     
  $objExpByDateTable->startRow();
  $objExpByDateTable->addCell($sesDat['date'], '', '', '', $oddOrEven);
  $objExpByDateTable->addCell($sesDat['blocation'], '', '', '', $oddOrEven);
  $objExpByDateTable->addCell($sesDat['btrate'], '', '', '', $oddOrEven);
  $objExpByDateTable->addCell($sesDat['llocation'], '', '', '', $oddOrEven);
  $objExpByDateTable->addCell($sesDat['lRate'], '', '', '', $oddOrEven);
  $objExpByDateTable->addCell($sesDat['dlocation'], '', '', '', $oddOrEven);
  $objExpByDateTable->addCell($sesDat['drrate'], '', '', '', $oddOrEven);
  $objExpByDateTable->addCell($sesDat['total'], '', '', '', $oddOrEven);
//   $objExpByDateTable->addCell('', '', '', '', $oddOrEven);
//  $objExpByDateTable->addCell('Total:$' .$sesDat['finaltotal'],'','',$oddOrEven);
  $objExpByDateTable->endRow();
  
  
  }
}
/**************************************************************************************************************************************************/
$this->loadClass('tabbedbox', 'htmlelements');
$objcreatetab = new tabbedbox();
$objcreatetab->addTabLabel('Per Diem Expenses');
$objcreatetab->addBoxContent("<div align=\"right\">" .$displayhelp. "</div>".'<br />'  . $objExpByDateTable->show() . '<br />'. '<br />'  . $this->objSave->show() . ' ' . $this->objEdit->show());


/*************************************************************************************************************************************************/
/**
 *create form to place save and edit button on
 */
$this->loadClass('form','htmlelements');
$objForm = new form('claiminfo',$this->uri(array('action'=>'perdiemoutput')));
$objForm->displayType = 3;
$objForm->addToForm($objcreatetab->show());// . ' ' . $this->objNext->show());	


/*************************************************************************************************************************************************/
//display all headings
echo "<div align=\"center\">" . $this->objMainheading->show(). "</div>". '<br />' . '<br />';
if(!empty($sessionPerdiem)){
  echo "<div align=\"left\">" . $objForm->show() . "</div>";
} 
//echo '<br />' . '<br />'. '<br />'.'<br />';
//echo "<div align=\"left\">" .$objForm->show(). "</div>"; 

?>
