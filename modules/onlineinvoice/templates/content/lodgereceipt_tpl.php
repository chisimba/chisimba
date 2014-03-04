<?php

  /**
   *create a template for uploading the lodge receipts
   */
   
   /*create template for lodging expenses*/
    $this->objlodgeHeading = $this->newObject('htmlheading','htmlelements');
    $this->objlodgeHeading->type = 2;
    $this->objlodgeHeading->str=$objLanguage->languageText('mod_onlineinvoice_travellodgingexpenses','onlineinvoice');
    
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
     
     //$urltext = 'www.myAffidavit.com';
     //$content = 'Fill out an Affidavit of lost receipt';
     //$caption = '';
     //$url = $this->uri(array('action'=>'<a href=http://www.myAffidavit.com/>www.myAffidavit.com</a>'));
     //$this->objaffidavit  = & $this->newObject('mouseoverpopup','htmlelements');
     //$this->objaffidavit->mouseoverpopup($urltext,$content,$caption,$url);

     
/*********************************************************************************************************************************************************************/     
     /**
      *create all text inputs
      */

/**
 *create all file info 
 */
 $objReceiptFile = $this->newObject('selectfile', 'filemanager');
 $objReceiptFile->name = 'receiptfile'; 
 $objReceiptFile->context = false;
 $objReceiptFile->workgroup = false;
 
 $objAffidavitFile = $this->newObject('selectfile', 'filemanager');
 $objAffidavitFile->name = 'affidavitfile'; 
 $objAffidavitFile->context = false;
 $objAffidavitFile->workgroup = false;
 
   
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
        $myTabReceipt->width='100%';
        $myTabReceipt->border='0';
        $myTabReceipt->cellspacing = '10';
        $myTabReceipt->cellpadding ='10';
        
        
        $myTabReceipt->startRow();
        $myTabReceipt->addCell('Upload Receipt');
        $myTabReceipt->addCell($objReceiptFile->show());
        $myTabReceipt->endRow();
        
        $myTabReceipt->startRow();
        $myTabReceipt->addCell('Create Affidavit');
        $myTabReceipt->addCell($link);
        $myTabReceipt->endRow();
        
        $myTabReceipt->startRow();
        $myTabReceipt->addCell('Upload Affidavit');
        $myTabReceipt->addCell($objAffidavitFile->show());
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
     
     $objLodgeReceipt = new form('lodging',$this->uri(array('action'=>'submitlodgereceipt')));
     $objLodgeReceipt->displayType = 3;
     $objLodgeReceipt->addToForm('<br> />'.$objtabreceipt->show().'<br />' . "<div align=\"center\">" . $this->objBack->show(). $this->objnext->show() . ' ' . $this->objexit->show()."</div>");
     //$objLodgeReceipt->addRule('upload','You have to insert a file','required');	
    // $objLodgeReceipt->extra="enctype='multipart/form-data'";      
     
     /**
      *create a form to place the file upload for affidavit application 
      */
     //$objAffidavit  = new form('affidavit',$this->uri(array('action'=>'submitlodgereceipt')));
     //$objAffidavit->displayType = 3;
     //$objAffidavit->addToForm($objtxtcreateaffidavit->show());            
/*********************************************************************************************************************************************************************/     
     echo "<div align=\"center\">" . $this->objlodgeHeading->show()  . "</div>";
     echo "<div align=\"center\">"."<div class=\"error\">" .'<br />'  . $lodgeExpenditures . "</div>";
     echo  "<div align=\"left\">"  . $objLodgeReceipt->show() . '<br />' ."</div>";   
?>
