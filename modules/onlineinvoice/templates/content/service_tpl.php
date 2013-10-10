<?php
  /**
   *create a template for service expenses
   */     
/****************************************************************************************************************************************/   
   /**
    *load all classes
    */
   $this->objnextlink  = & $this->newObject('mouseoverpopup','htmlelements');     
    $this->loadClass('tabbedbox', 'htmlelements');
    
/****************************************************************************************************************************************/    
   /**
    *create all language items
    */       
   $serviceinputs = $this->objLanguage->languageText('mod_onlinveinvoice_serviceinputs','onlineinvoice');
   $strserviceinfo  = ucfirst($serviceinputs);
   $nextCategory  = $this->objLanguage->languageText('phrase_nomovetonextcategory');
   $nextcatcontent = $this->objLanguage->languageText('mod_onlineinvoice_nextcaption','onlineinvoice');
/****************************************************************************************************************************************/
   
   $this->objMainheading =& $this->getObject('htmlheading','htmlelements');
   $this->objMainheading->type=1;
   $this->objMainheading->str=$objLanguage->languageText('mod_onlineinvoice_webbasedinvoicingsystem','onlineinvoice');
/****************************************************************************************************************************************/
   
   
   /**
    *create all link elements
    */
    
    $urltext = 'YES - Go to service';
    $content = 'Complete any supply expenses';
    $caption = '';
    $url = $this->uri(array('action'=>'displayserviceinfo'));
    $this->objnextlink->mouseoverpopup($urltext,$content,$caption,$url);
    
    $urltext = $nextCategory;
    $content = $nextcatcontent;
    $caption = '';
    $url = $this->uri(array('action'=>'showsupplies'));
    $this->objnextcat  = & $this->newObject('mouseoverpopup','htmlelements');
    $this->objnextcat->mouseoverpopup($urltext,$content,$caption,$url);
    
    /*create table to place form elements in */        

        $myTab=$this->newObject('htmltable','htmlelements');
        $myTab->width='60%';
        $myTab->border='0';
        $myTab->cellspacing='10';
        $myTab->cellpadding='10';

        $myTab->startRow();
        $myTab->addCell($this->objnextlink->show());
        $myTab->addCell($this->objnextcat->show());
        $myTab->endRow();


/****************************************************************************************************************************************/    
    /**
     *create tabbed box
     */
     
     $objcreateinvtab = new tabbedbox();
     $objcreateinvtab->addTabLabel('Service Information');
     $objcreateinvtab->addBoxContent('<br>'  . '<b>'. $strserviceinfo . '<b />' .'<br />'.  '<br />' . $myTab->show(). '<br />');
/****************************************************************************************************************************************/              
    /**
     *display screen output
     */
     echo  "<div align=\"center\">" . $this->objMainheading->show() . "</div>";
     echo '<br />';
     echo $objcreateinvtab->show();
     //echo '<br />' . $this->objnextlink->show();
     
                                   
   
   
?>
