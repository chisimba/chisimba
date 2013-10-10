<?php
      
  /**
    *create a layout for the postlogin form to display 3 columns
    */
       
       
    $cssLayout =& $this->newObject('csslayout', 'htmlelements');
    $this->sideMenuBar=& $this->getObject('sidemenu','toolbar');
    
    $objskin  = & $this->newObject('skin','skin');
    $showlinklogout = $objskin->putLogout();

    $cssLayout->setNumColumns(3);
    $sideMenuBar=& $this->getObject('sidemenu','toolbar');
    
    
/************************************************************************************************************************************************/   
    /**
     *create all language elements
     */
    
     $todaydate = $this->objLanguage->languageText('mod_onlineinvoice_todaydate','onlineinvoice');
     $logout    = ucfirst($this->objLanguage->languageText('word_logout'));
     $createinvoice = ucfirst($this->objLanguage->languageText('mod_onlineinvoice_createinvoice','onlineinvoice'));
     $createfinance = ucfirst($this->objLanguage->languageText('mod_onlineinvoice_createfinance','onlineinvoice'));
     $archives      = ucfirst($this->objLanguage->languageText('mod_onlineinvoice_archives','onlineinvoice'));
     $submittedinvoices = ucfirst($this->objLanguage->languageText('mod_onlineinvoice_submittedinvoice','onlineinvoice'));
     $subagreementdoc = ucfirst($this->objLanguage->languageText('mod_onlineinvoice_subagreementdoc','onlineinvoice'));
     $email  = ucfirst($this->objLanguage->languageText('phrase_emailadmin'));
     $FAQ = ucfirst($this->objLanguage->languageText('word_FAQ'));
/************************************************************************************************************************************************/
  /**
   *create all link elements
   */     
    
    $urltext = $createinvoice;
    $content = $createinvoice;
    $caption = '';
    $url = $this->uri(array('action'=>'initialinvoice'));
    $this->objcreateinvoice  = & $this->newObject('mouseoverpopup','htmlelements');
    $this->objcreateinvoice->mouseoverpopup($urltext,$content,$caption,$url);

    
    $urltext = $createfinance;
    $content = $createfinance;
    $caption = '';
    $url = $this->uri(array('action'=>NULL));
    $this->objcreatefinance  = & $this->newObject('mouseoverpopup','htmlelements');
    $this->objcreatefinance->mouseoverpopup($urltext,$content,$caption,$url);

    $urltext = 'Complete pending invoice';
    $content = 'click the link to return to complete invoice expenses';
    $caption = '';
    $url = $this->uri(array('action'=>'showinvpending'));
    $this->objinvpending  = & $this->newObject('mouseoverpopup','htmlelements');
    $this->objinvpending->mouseoverpopup($urltext,$content,$caption,$url);

    $urltext = $archives;
    $content = $archives;
    $caption = '';
    $url = $this->uri(array('action'=>NULL));
    $this->objarchives  = & $this->newObject('mouseoverpopup','htmlelements');
    $this->objarchives->mouseoverpopup($urltext,$content,$caption,$url);


    $urltext = $submittedinvoices;
    $content = $submittedinvoices;
    $caption = '';
    $url = $this->uri(array('action'=>NULL));
    $this->objsubmitinvoice  = & $this->newObject('mouseoverpopup','htmlelements');
    $this->objsubmitinvoice->mouseoverpopup($urltext,$content,$caption,$url);

    $urltext = $subagreementdoc;
    $content = $subagreementdoc;
    $caption = '';
    $url = $this->uri(array('action'=>NULL));
    $this->objsubagreement  = & $this->newObject('mouseoverpopup','htmlelements');
    $this->objsubagreement->mouseoverpopup($urltext,$content,$caption,$url);
    
    $urltext = $email;
    $content = $email;
    $caption = '';
    $url = $this->uri(array('action'=>'sendemail'));
    $this->objemail  = & $this->newObject('mouseoverpopup','htmlelements');
    $this->objemail->mouseoverpopup($urltext,$content,$caption,$url);

    $urltext = $FAQ;
    $content = $FAQ;
    $caption = '';
    $url = $this->uri(array('action'=>NULL));
    $this->objfaq  = & $this->newObject('mouseoverpopup','htmlelements');
    $this->objfaq->mouseoverpopup($urltext,$content,$caption,$url);


              
/************************************************************************************************************************************************/         
     /**
      *table to place all link elements in
      */
                 
     $myTab = $this->newObject('htmltable','htmlelements');
     $myTab->width='100%';
     $myTab->border='0';
     $myTab->cellspacing='5';
     $myTab->cellpadding='5';
   
     $myTab->startRow();
     $myTab->addCell(ucfirst($todaydate . ' '.date('d-m-Y')));
     $myTab->endRow();
                
     $myTab->startRow();
    // $myTab->addCell($showlinklogout);
     $myTab->endRow();
     
     
     
     $myTab->startRow();
     $myTab->addCell($this->objcreateinvoice->show());
     $myTab->endRow();
     
     $myTab->startRow();
     $myTab->addCell($this->objinvpending->show());
     $myTab->startRow();
     $myTab->addCell($this->objcreatefinance->show());
     $myTab->endRow();
     
     $myTab->startRow();
     $myTab->addCell($this->objarchives->show());
     $myTab->endRow();
     
     $myTab->startRow();
     $myTab->addCell($this->objsubmitinvoice->show());
     $myTab->endRow();
     
     $myTab->startRow();
     $myTab->addCell($this->objsubagreement->show());
     $myTab->endRow();
     
     $myTab->startRow();
     $myTab->addCell($this->objemail->show());
     $myTab->endRow();
     
     $myTab->startRow();
     $myTab->addCell($this->objfaq->show());
     $myTab->endRow();
/***********************************************************************************************************************/
    
     
     $rightcolumn  = $myTab->show();
     
     $this->loadClass('featurebox','navigation');
     $objfeature = new featurebox($val,$val);
     $display = $objfeature->show($rightcolumn);
    
     $objfeatureleft = new featurebox($val,$val);
     $featureleft = $objfeatureleft->show($this->sideMenuBar->userDetails());
    
    

     $cssLayout->setLeftColumnContent($featureleft);
     $cssLayout->setRightColumnContent($display);
     $cssLayout->setMiddleColumnContent($this->getContent()); 
    
    echo  $cssLayout->show();

                            
?>
