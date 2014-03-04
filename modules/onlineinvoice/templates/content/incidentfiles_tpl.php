<?php

  /**
   *create a template for uploading the lodge receipts
   */
   
   /*create template for lodging expenses*/
    $this->objlodgeHeading = $this->newObject('htmlheading','htmlelements');
    $this->objlodgeHeading->type = 2;
    $this->objlodgeHeading->str=$objLanguage->languageText('mod_onlineinvoice_incidentexpense','onlineinvoice');
    
    /**
     *create all language items
     */
     $lodgeExpenditures = $this->objLanguage->languageText('mod_onlineinvoice_itemizedexpenditures','onlineinvoice');
     $receipt  = $this->objLanguage->languageText('mod_onlineinvoice_uploadreceipts','onlineinvoice');
     $next = ucfirst($this->objLanguage->languageText('phrase_next'));
     $exit = ucfirst($this->objLanguage->languageText('phrase_exit'));
     $back = ucfirst($this->objLanguage->languageText('word_back'));
     $link = '<a href=http://www.myAffidavit.com/>www.myAffidavit.com</a>';
     $createaffidavit = ucfirst($this->objLanguage->languageText('phrase_create'));

     
/*********************************************************************************************************************************************************************/     
     /**
      *create all text inputs
      */
      
   //$this->objtxtquotesource  = new textinput('txtquotesource', ' ','text');
   //$this->objtxtquotesource->id = 'txtquotesource';
   
   //$this->loadClass('textinput', 'htmlelements');
   //$this->objtxtfilereceipt = new textinput('upload',' ','FILE');
   //$this->objtxtfilereceipt->id = 'txtfilereceipt';
   
    /**
      *create all file info 
      */
       $objIncidentreceipt = $this->newObject('selectfile', 'filemanager');
       $objIncidentreceipt->name = 'incidentreceipt'; 
       $objIncidentreceipt->context = false;
       $objIncidentreceipt->workgroup = false; 
       
       $objIncidentaffidavit = $this->newObject('selectfile', 'filemanager');
       $objIncidentaffidavit->name = 'incidentaffidavit'; 
       $objIncidentaffidavit->context = false;
       $objIncidentaffidavit->workgroup = false; 
   
/*********************************************************************************************************************************************************************/   
   /*create all button elements*/


  $this->loadclass('button','htmlelements');
  $this->objnext  = new button('next', $next);
  $this->objnext->setToSubmit();

  $this->objexit  = new button('exit', $exit);
  $this->objexit->setToSubmit();

  $this->objBack  = new button('back', $back);
  $this->objBack->setToSubmit();        
     
/*********************************************************************************************************************************************************************/


/*create table for receipt information*/        

        $myTabReceipt  = $this->newObject('htmltable','htmlelements');
        $myTabReceipt->width='75%';
        $myTabReceipt->border='0';
        $myTabReceipt->cellspacing = '10';
        $myTabReceipt->cellpadding ='10';
        
        
        $myTabReceipt->startRow();
        $myTabReceipt->addCell('Upload Receipt');
        $myTabReceipt->addCell($objIncidentreceipt->show());
        $myTabReceipt->endRow();
        
        $myTabReceipt->startRow();
        $myTabReceipt->addCell('Create Affidavit');
        $myTabReceipt->addCell($link);
        $myTabReceipt->endRow();
        
        $myTabReceipt->startRow();
        $myTabReceipt->addCell('Upload Affidavit');
        $myTabReceipt->addCell($objIncidentaffidavit->show());
        $myTabReceipt->endRow();
        
        $myTabReceipt->startRow();
        $myTabReceipt->addCell(' ');
//        $myTabReceipt->addCell($this->objButtonUploadReceipt->show());
        $myTabReceipt->endRow();
        
        $myTabReceipt->startRow();
        $myTabReceipt->endRow();
        
        

/*********************************************************************************************************************************************************************/         
      /**
       *create a tabbed box to place table and elements in
       */
      $this->loadClass('tabbedbox', 'htmlelements'); 
      $objtabreceipt = new tabbedbox();
      $objtabreceipt->addTabLabel('Receipt Information');
      $objtabreceipt->addBoxContent('<br>'  . '<b />' .$receipt  . '<br>' ."<div align=\"left\">"  .$myTabReceipt->show());
      
      
                    
 
/*********************************************************************************************************************************************************************/ 
    /**
     *create a form to place all elements in
     */
     
     $objLodgeReceipt = new form('incidentfiles',$this->uri(array('action'=>'submitincidentreceipt')));
     $objLodgeReceipt->displayType = 3;
     $objLodgeReceipt->addToForm('<br> />'.$objtabreceipt->show().'<br />' . "<div align=\"center\">" . $this->objBack->show(). $this->objnext->show() . ' ' . $this->objexit->show()."</div");	
     $objLodgeReceipt->extra="enctype='multipart/form-data'";      
/*********************************************************************************************************************************************************************/     
     echo "<div align=\"center\">" . $this->objlodgeHeading->show()  . "</div>";
     echo "<div align=\"center\">"."<div class=\"error\">" . '<br />'  . $lodgeExpenditures . "</div>";
     echo "<div align=\"left\">"  . $objLodgeReceipt->show() . "</div>";   
?>
