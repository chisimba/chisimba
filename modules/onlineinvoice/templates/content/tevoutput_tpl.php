<?php

  /**
   *template to display the travel expense output
   */
/******************************************************************************************************************************/   
    /**
      *load all classes / create objects of all classes
      */
     $claimantinfo  = & $this->getObject('dbtev','onlineinvoice');
     $display = $claimantinfo->showclaimant();
     
     $itinerary = & $this->getObject('dbitinerary','onlineinvoice');
     $results = $itinerary->showtitinerary();
     
     $perdiem = & $this->getObject('dbperdiem','onlineinvoice');
     $showperdiem = $perdiem->showperdiem();
     
     $lodge = & $this->getObject('dblodging','onlineinvoice');
     $showlodge = $lodge->showlodge();
     
     $incident  = & $this->getObject('dbincident','onlineinvoice');
     $showincident  = $incident->showincident();
     
     $output = $display  . '<br />'  . $results . '<br />' . $showperdiem . '<br />' . $showlodge . '<br />' . $showincident;
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
$objcreateinvtab = new tabbedbox();
$objcreateinvtab->addTabLabel('Lodge Information');
$objcreateinvtab->addBoxContent('<br />' .$showlodge);

$objcreatetraveler = new tabbedbox();
$objcreatetraveler->addTabLabel('Traveler Information');
$objcreatetraveler->addBoxContent('<br />' .$display);

$objcreateitinerary = new tabbedbox();
$objcreateitinerary->addTabLabel('Itinerary Information');
$objcreateitinerary->addBoxContent('<br />' .$results);

$objcreateperdiem = new tabbedbox();
$objcreateperdiem->addTabLabel('Per Diem Information');
$objcreateperdiem->addBoxContent('<br />' .$showperdiem);

$objcreateincident = new tabbedbox();
$objcreateincident->addTabLabel('Incident Information');
$objcreateincident->addBoxContent('<br />' .$showincident);

     
/***************************************************************************************************************************************************************/

  /**
       *create links to move to next section
       */
       
    $urltext = 'travel expense voucher';
    $content = 'complete a travel voucher for the traveler';
    $caption = '';
    $url = $this->uri(array('action'=>'createtev'));
    $this->objinvdates  = & $this->newObject('mouseoverpopup','htmlelements');
    $this->objinvdates->mouseoverpopup($urltext,$content,$caption,$url);                        
    
    $urltext = 'itinerary';
    $content = 'complete an itinerary for the travel';
    $caption = '';
    $url = $this->uri(array('action'=>'createitinerary'));
    $this->objinvitinerary  = & $this->newObject('mouseoverpopup','htmlelements');
    $this->objinvitinerary->mouseoverpopup($urltext,$content,$caption,$url);
    
    $urltext = 'per diem expenses';
    $content = 'complete all per diem expenses';
    $caption = '';
    $url = $this->uri(array('action'=>'showperdiem'));
    $this->objinvperdiem  = & $this->newObject('mouseoverpopup','htmlelements');
    $this->objinvperdiem->mouseoverpopup($urltext,$content,$caption,$url);
    
    $urltext = 'lodge expenses';
    $content = 'complete all lodge expenses';
    $caption = '';
    $url = $this->uri(array('action'=>'createlodge'));
    $this->objinvlodge  = & $this->newObject('mouseoverpopup','htmlelements');
    $this->objinvlodge->mouseoverpopup($urltext,$content,$caption,$url);
     
    $urltext = 'incident expenses';
    $content = 'complete all incident expenses';
    $caption = '';
    $url = $this->uri(array('action'=>'showincident'));
    $this->objinvincidents  = & $this->newObject('mouseoverpopup','htmlelements');
    $this->objinvincidents->mouseoverpopup($urltext,$content,$caption,$url);
    
    $myTab = $this->newObject('htmltable','htmlelements');
    $myTab->width='100%';
    $myTab->border='0';
    $myTab->cellspacing='5';
    $myTab->cellpadding='5';
   
    $myTab->startRow();
    $myTab->addCell(ucfirst($this->objinvdates->show()));
    $myTab->endRow();
    
    $myTab->startRow();
    $myTab->addCell(ucfirst($this->objinvitinerary->show()));
    $myTab->endRow();
    
    $myTab->startRow();
    $myTab->addCell(ucfirst($this->objinvperdiem->show()));
    $myTab->endRow();
    
    $myTab->startRow();
    $myTab->addCell(ucfirst($this->objinvlodge->show()));
    $myTab->endRow();
    
    $myTab->startRow();
    $myTab->addCell(ucfirst($this->objinvincidents->show()));
    $myTab->endRow();
    
/****************************************************************************************************************************************************************/
     /**
      *get per diem final total value
      */           
     $perdiemtot  = $this->getSession('perdiemdetails');
                if(!empty($perdiemtot)){
                     foreach($perdiemtot as $sesDat){
                        $tot =  $sesDat['finaltotal'];
                    }
                }
     /**
      *get Lodging final tot value
      */
     $lodgetot  = $this->getSession('lodgedetails');
                if(!empty($lodgetot)){
                     foreach($lodgetot as $sesLodge){
                        $totlodge =  $sesLodge['totroomrate'];
                        //var_dump($totlodge);                                
                        
                    }
                }
                
      /**
       *get Incident final totals
       */
       $incidenttot  = $this->getSession('incidentdetails');
                if(!empty($incidenttot)){
                     foreach($incidenttot as $sesIncident){
                        $totincident =  $sesIncident['inidentexepense'];
                        //var_dump($totlodge);                                
                        
                    }
                }
     
                    
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
$output = '<br />' . $objcreatetraveler->show() .  '<br />' . $objcreateitinerary->show() . '<br />' . $objcreateperdiem->show()  . '<br />' . $objcreateinvtab->show() . '<br />' . $objcreateincident->show() . $myTabtot->show();
$objElement =& $this->newObject('tabpane', 'htmlelements');
$objElement->addTab(array('name'=>'Travel Output','content' => $output));
$objElement->addTab(array('name'=>'Edit a Section','content' => $myTab->show()));

    
     
/***************************************************************************************************************************************************************/

  echo  "<div align=\"center\">" .  $objtravelsheet->show() . "</div>";
  echo '<br />' . $objElement->show();          
?>
