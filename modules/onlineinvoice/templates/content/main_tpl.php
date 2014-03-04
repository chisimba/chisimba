<?php

/**
 *set the template page layout
 */
  
$cssLayout =& $this->newObject('csslayout', 'htmlelements');
$this->sideMenuBar=& $this->getObject('sidemenu','toolbar');

$cssLayout->setNumColumns(2);
$sideMenuBar=& $this->getObject('sidemenu','toolbar');

/**
 *create template for main screen into the online invoice system
 */
 
 /**
  *load all classes
  */
  
  $this->loadClass('htmlheading','htmlelements');
  $this->loadClass('button','htmlelements');
  $this->loadclass('textinput','htmlelements');
  $this->loadClass('mouseoverpopup','htmlelements');
  $this->loadClass('tabbedbox','htmlelements');
  $cssLayout =& $this->newObject('csslayout', 'htmlelements');
  $this->loadClass('featurebox','navigation');
 /********************************************************************************************************************************************/
  /**
   *create all language elements
   */
   
   $username  = $this->objLanguage->languageText('mod_onlineinvoice_username','onlineinvoice');     
   $password  = $this->objLanguage->languageText('mod_onlineinvoice_password','onlineinvoice');
   $login    = ucfirst($this->objLanguage->languageText('word_login'));
   $fiscalyr  = $this->objLanguage->languageText('mod_onlineinvoice_fiscalyr','onlineinvoice');
   $submitinfo  = $this->objLanguage->languageText('mod_onlineinvoice_submitinfo','onlineinvoice');
   $labelinfo   = $this->objLanguage->languageText('mod_onlineinvoice_labelinfo','onlineinvoice');
   $websitelocation = $this->objLanguage->languageText('mod_onlineinvoice_check','onlineinvoice');
   $gsawebsite  = '<a href="http://www.gsa.gov/Portal/gsa/ep/contentView.do?contentId=17943&contentType=GSA_BASIC/><class="\pseudobutton\"/>www.gsa.gov/Portal/gsa</a>';
   $value = $websitelocation . ' ' . $gsawebsite;
/********************************************************************************************************************************************/  
  /**
   *create all form headings
   */    
   
  $objlodgeHeading  = new htmlheading('Mainheading'); 
  $objlodgeHeading->type = 1;
  $objlodgeHeading->str=ucfirst($objLanguage->languageText('mod_onlineinvoice_mainheading','onlineinvoice'));
  
  
/********************************************************************************************************************************************/
  /**
   *create all link elements
   */     
 /* $urltext = 'Postlogin page';
  $content = 'Postlogin page';
  $caption = '';
  $url = $this->uri(array('action'=>'postlogin'));
  $this->objnextlink  = & $this->newObject('mouseoverpopup','htmlelements');
  $this->objnextlink->mouseoverpopup($urltext,$content,$caption,$url);*/

/********************************************************************************************************************************************/  
  /**
   *create all form buttons
   */
  
  $this->objsubmit  = new button('submit', $login);
  $this->objsubmit->setToSubmit();
/********************************************************************************************************************************************/

  /**
   *create all templates
   */
   
   $this->loadClass('textinput', 'htmlelements');

  $this->objtxtusername = new textinput('txtusername');
  $this->objtxtusername->id = 'txtusername';
  $this->objtxtusername->size = 10;
  $this->objtxtpassword = new textinput('txtpassword');
  $this->objtxtpassword->id = 'txtpassword';
  //$this->objtxtpassword->type = 'password';       
/********************************************************************************************************************************************/
  /**
   *create all link elements
   */       
  
/********************************************************************************************************************************************/
  /**
   *create a table to place all form elements
   */
   
   $myTable = $this->newObject('htmltable','htmlelements');
   $myTable->width='70%';
   $myTable->border='0';
   $myTable->cellspacing='5';
   $myTable->cellpadding='5';
   
   $myTable->startRow();
   $myTable->addCell(ucfirst($username));
   $myTable->addCell($this->objtxtusername->show());
   $myTable->endRow();
   
   $myTable->startRow();
   $myTable->addCell(ucfirst($password));
   $myTable->addCell($this->objtxtpassword->show());
   $myTable->endRow();
   
   $myTable->startRow();
   $myTable->addCell($this->objsubmit->show());
   $myTable->endRow();  
   
   /*********create a table for label elements**********/
   
   $myTablab = $this->newObject('htmltable','htmlelements');
   $myTablab->width='80%';
   $myTablab->border='0';
   $myTablab->cellspacing='5';
   $myTablab->cellpadding='5';
   
   $myTablab->startRow();
   $myTablab->addCell(ucfirst($fiscalyr));
   $myTablab->endRow();
   
   $myTablab->startRow();
   $myTablab->addCell(ucfirst($submitinfo));
   $myTablab->endRow();  
   
   $myTab = $this->newObject('htmltable','htmlelements');
   $myTab->width='80%';
   $myTab->border='0';
   $myTab->cellspacing='5';
   $myTab->cellpadding='5';
   
   $myTab->startRow();
   $myTab->addCell(ucfirst($labelinfo));
   $myTab->endRow();  
   
   $myTab->startRow();
   $myTab->addCell(ucfirst($value));
   $myTab->endRow();  
        
  
/********************************************************************************************************************************************/
  /**
   *create a tabbed box to place all form elements in
   */
   
   $objtabbedmain = new tabbedbox();
   $objtabbedmain->addBoxContent("<div align=\"center\">" . '<br />' . $myTablab->show()  .   "</div>" );
   
   $objtabbed = new tabbedbox();
   $objtabbed->addBoxContent("<div align=\"center\">" . '<br />' . $myTab->show()  .   "</div>" );
/********************************************************************************************************************************************/   
    /**
     * create a feature box to place login details on
     */
   // public $val = null;
    //public $val2 = null;  
    $user = ucfirst($username);
    $objfeature = new featurebox($val,$val2);
    $objfeature->show('<b />'. ucfirst($username) . ' ' . '<b />'. $this->objtxtusername->show() . '<br />'  . '<b />'. ucfirst($password) . ' ' . '<b />'. $this->objtxtusername->show() .'<br />' . '<b />'.$this->objsubmit->show());
    
    /**
     *  create a feature box to place information details on
     */
     
    //$objheadingfeature = new featurebox($values,$values1);
    //$objinfofeature  = new   featurebox($key,$key1);           
     
     
              
/********************************************************************************************************************************************/  
  /**
   * add form elements to variables
   */
   $leftcolumn = $objfeature->show(ucfirst($username) . ' ' .$this->objtxtusername->show() . '<br />'  .  ucfirst($password) . ' ' . $this->objtxtusername->show() .'<br />'.$this->objsubmit->show());      
  //$leftcolumn = '<b />'. ucfirst($username) . ' ' . '<b />'. $this->objtxtusername->show() . '<br />'  . '<b />'. ucfirst($password) . ' ' . '<b />'. $this->objtxtusername->show() .'<br />' . '<b />'.$this->objsubmit->show();
  $midcolumn  = '<b />'. ucfirst("<div align=\"center\">" . '<br />'  . '<b />'. $objlodgeHeading->show() .   "</div>" );
  $information  = '<br />' . '<b />' . 'This wensite is used to capture all expenses:' . '<br />' . '<b />' . 'This includes: Travel expeneses'. '<br />' . '<b />' . 'Service Expenses'  . '<b />' . ' '. 'Supplies expenses';
/********************************************************************************************************************************************/
  /**
   * set form layouts
   */
           
  $cssLayout->setLeftColumnContent($leftcolumn);
  //$cssLayout->setRightColumnContent('hello');
  $cssLayout->setMiddleColumnContent("<div class=\"wrapperLightBkg\">". $midcolumn ."</div>" . '<br />'  . '<br />'  .  '<br />'  . '<br />'  . "<div align=\"center\">" . $information .   "</div>"  . '<br />'  . '<br />'  .  '<br />'  . '<br />' . $objtabbedmain->show()  . $objtabbed->show());  
  
/********************************************************************************************************************************************/

  $this->loadClass('form','htmlelements');
  $objmainForm = new form('main',$this->uri(array('action'=>'verifylogin')));  //set action to -> display the intial invoice template
  $objmainForm->id = 'main';
  $objmainForm->displayType = 3;
  $objmainForm->addToForm($cssLayout->show() .  '<br>' );
/************************************************************************************************************************************************/ 

  /**
   *display output to screen
   */
  
  echo  $objmainForm->show();

?>    

  


