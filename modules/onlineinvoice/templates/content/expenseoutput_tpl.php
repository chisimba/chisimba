<?php

  /**
   *template to display the travel expense output
   */
/******************************************************************************************************************************/   
    /**
      *load all classes / create objects of all classes
      */
   //  $claimantinfo  = & $this->getObject('dbtev','onlineinvoice');
   //  $display = $claimantinfo->showclaimant();
     
     $odc = & $this->getObject('dbodc','onlineinvoice');
     $showodc = $odc->showOdc();
     
     $supplies  = & $this->getObject('dbsupplies','onlineinvoice');
     $showsupplies = $supplies->showSupply();
     
     $equipment = & $this->getObject('dbequipment','onlineinvoice');
     $showequipment = $equipment->showEquipment();
     
  //   $incident  = & $this->getObject('dbincident','onlineinvoice');
  //   $showincident  = $incident->showincident();
     
     $output =  $showsupplies . '<br />' . $showequipment . '<br />' . $showodc;
/*******************************************************************************************************************************/

      /**
       *define all language elements
       */
       
       $expensesheet  = $this->objLanguage->languageText('mod_onlineinvoice_travelsheet','onlineinvoice');
       $travelerdetails = $this->objLanguage->languageText('mod_onlineinvoice_travelerinfo','onlineinvoice');
       $save  = $this->objLanguage->languageText('word_save');
       $edit  = $this->objLanguage->languageText('word_edit');
       
       
/*******************************************************************************************************************************/
      /**
       *create all form headings
       */             

      $strheading  = strtoupper($expensesheet);
       $objtravelsheet  = new htmlHeading();
       $objtravelsheet->type  = 1;
       $objtravelsheet->str = $strheading;
       
       
/********************************************************************************************************************************/  

/**
 *create a tabbed box
 */
 
$this->loadClass('tabbedbox', 'htmlelements');

/*$objcreateinvtab = new tabbedbox();
$objcreateinvtab->addTabLabel('Lodge Information');
$objcreateinvtab->addBoxContent('<br />' .$showlodge);*/

/*$objcreatetraveler = new tabbedbox();
$objcreatetraveler->addTabLabel('Traveler Information');
$objcreatetraveler->addBoxContent('<br />' .$display);*/

$objcreateodc = new tabbedbox();
$objcreateodc->addTabLabel('ODC expenses Information');
$objcreateodc->addBoxContent('<br />' .$showodc);

$objcreatesupplies = new tabbedbox();
$objcreatesupplies->addTabLabel('Supplies Information');
$objcreatesupplies->addBoxContent('<br />' .$showsupplies);

$objcreateequip = new tabbedbox();
$objcreateequip->addTabLabel('Equipment Information');
$objcreateequip->addBoxContent('<br />' .$showequipment);

     
/***************************************************************************************************************************************************************/

  /**
       *create links to move to next section
       */
       
   /* $urltext = 'travel expense voucher';
    $content = 'complete a travel voucher for the traveler';
    $caption = '';
    $url = $this->uri(array('action'=>'createtev'));
    $this->objinvsupplies  = & $this->newObject('mouseoverpopup','htmlelements');
    $this->objinvsupplies->mouseoverpopup($urltext,$content,$caption,$url); */                      
    
  /*  $urltext = 'itinerary';
    $content = 'complete an itinerary for the travel';
    $caption = '';
    $url = $this->uri(array('action'=>'createitinerary'));
    $this->objinvitinerary  = & $this->newObject('mouseoverpopup','htmlelements');
    $this->objinvitinerary->mouseoverpopup($urltext,$content,$caption,$url); */
    
  /*  $urltext = 'per diem expenses';
    $content = 'complete all per diem expenses';
    $caption = '';
    $url = $this->uri(array('action'=>'showperdiem'));
    $this->objinvperdiem  = & $this->newObject('mouseoverpopup','htmlelements');
    $this->objinvperdiem->mouseoverpopup($urltext,$content,$caption,$url); */
    
  /*  $urltext = 'lodge expenses';
    $content = 'complete all lodge expenses';
    $caption = '';
    $url = $this->uri(array('action'=>'createlodge'));
    $this->objinvlodge  = & $this->newObject('mouseoverpopup','htmlelements');
    $this->objinvlodge->mouseoverpopup($urltext,$content,$caption,$url);  */
     
  /*  $urltext = 'incident expenses';
    $content = 'complete all incident expenses';
    $caption = '';
    $url = $this->uri(array('action'=>'showincident'));
    $this->objinvincidents  = & $this->newObject('mouseoverpopup','htmlelements');
    $this->objinvincidents->mouseoverpopup($urltext,$content,$caption,$url); */
    
    $urltext = 'Supplies expenses';
    $content = 'complete a supplies invoice';
    $caption = '';
    $url = $this->uri(array('action'=>'showsupplies'));
    $this->objinvsupplies  = & $this->newObject('mouseoverpopup','htmlelements');
    $this->objinvsupplies->mouseoverpopup($urltext,$content,$caption,$url);  
    
    $urltext = 'Equipment expenses';
    $content = 'complete a Equipment invoice';
    $caption = '';
    $url = $this->uri(array('action'=>'showequipment'));
    $this->objinvequipment  = & $this->newObject('mouseoverpopup','htmlelements');
    $this->objinvequipment->mouseoverpopup($urltext,$content,$caption,$url); 
    
    $urltext = 'ODC expenses';
    $content = 'complete a ODC invoice';
    $caption = '';
    $url = $this->uri(array('action'=>'showodc'));
    $this->objinvodc  = & $this->newObject('mouseoverpopup','htmlelements');
    $this->objinvodc->mouseoverpopup($urltext,$content,$caption,$url);                        
                           
                          
    
    
    $myTab = $this->newObject('htmltable','htmlelements');
    $myTab->width='100%';
    $myTab->border='0';
    $myTab->cellspacing='5';
    $myTab->cellpadding='5';
   
    $myTab->startRow();
    $myTab->addCell(ucfirst($this->objinvsupplies->show()));
    $myTab->endRow();
    
    $myTab->startRow();
    $myTab->addCell(ucfirst($this->objinvequipment->show()));
    $myTab->endRow();
    
    $myTab->startRow();
    $myTab->addCell(ucfirst($this->objinvodc->show()));
    $myTab->endRow();
    
  /*  $myTab->startRow();
    $myTab->addCell(ucfirst($this->objinvlodge->show()));
    $myTab->endRow();
    
    $myTab->startRow();
    $myTab->addCell(ucfirst($this->objinvincidents->show()));
    $myTab->endRow();*/
    
/****************************************************************************************************************************************************************/
     /**
      *get per diem final total value
      */           
   /*  $perdiemtot  = $this->getSession('perdiemdetails');
                if(!empty($perdiemtot)){
                     foreach($perdiemtot as $sesDat){
                        $tot =  $sesDat['finaltotal'];
                    }
                }/*
     /**
      *get Lodging final tot value
      */
   /*  $lodgetot  = $this->getSession('lodgedetails');
                if(!empty($lodgetot)){
                     foreach($lodgetot as $sesLodge){
                        $totlodge =  $sesLodge['totroomrate'];
                        //var_dump($totlodge);                               
                        
                    }
                }*/
                
      /**
       *get Incident final totals
       */
    /*   $incidenttot  = $this->getSession('incidentdetails');
                if(!empty($incidenttot)){
                     foreach($incidenttot as $sesIncident){
                        $totincident =  $sesIncident['inidentexepense'];
                        //var_dump($totlodge);                                
                        
                    }
                }*/
     
                    
     $grandtot  =   $tot +  $totlodge + $totincident;         
    
    /**
     *display all total values
     */
    
    $myTabtot = $this->newObject('htmltable','htmlelements');
    $myTabtot->width='100%';
    $myTabtot->border='1';
    $myTabtot->cellspacing='2';
    $myTabtot->cellpadding='2';
   
    $myTabtot->startRow();
    $myTabtot->addCell('<b>' . 'PER DIEM TOTAL');
    $myTabtot->addCell('<b>' . 'LODGING TOTAL');
    $myTabtot->addCell('<b>' . 'INCIDENTAL TOTAL');
    $myTabtot->addCell('<b>' . 'GRAND TOTAL');
    $myTabtot->endRow();
    
    $myTabtot->startRow();
    $myTabtot->addCell('$' . $tot);
    $myTabtot->addCell('$' . $totlodge);
    $myTabtot->addCell('$' . $totincident);
    $myTabtot->addCell('$'. $grandtot);
    $myTabtot->endRow();
    
   
/****************************************************************************************************************************************************************/
/**
 *create a tabpane
 */   
$output = '<br />' . $objcreateodc->show()  . '<br />' . $objcreatesupplies->show() . '<br />' . $objcreateequip->show();
$objElement =& $this->newObject('tabpane', 'htmlelements');
$objElement->addTab(array('name'=>'Travel Output','content' => $output));
$objElement->addTab(array('name'=>'Edit a Section','content' => $myTab->show()));

    
     
/***************************************************************************************************************************************************************/

  echo  "<div align=\"center\">" .  $objtravelsheet->show() . "</div>";
  echo '<br />' . $objElement->show();          
?>
