<?php
  /**
   *create a template for service expenses
   */     
   
   /**
    *create all language items
    */       
   $odcinputs = $this->objLanguage->languageText('mod_onlinveinvoice_odcinputs','onlineinvoice');
  // $supplieslink = $this->objLanguage->LanguageText('mod_onlineinvoice_nextcategory','onlineinvoice');
  // $catlink = $this->objLanguage->LaguageText('mod_onlineinvoice_nextcategory','onlineinvoice');
   $strodcinfo  = strtoupper($odcinputs);
   
   /**
    *load all classes
    */
    $this->objnextlink  = & $this->newObject('mouseoverpopup','htmlelements');
    $this->objnextcat = & $this->newObject('mouseoverpopup','htmlelements');     
    
    $this->loadClass('tabbedbox', 'htmlelements');
   /**
    *create all link elements
    */
    
   
   
    $urltext = 'YES - Go to ODCs';
    $content = 'Complete any ODC expenses';
    $caption = '';
    $url = $this->uri(array('action'=>'showodcexpenses'));
    $this->objnextlink->mouseoverpopup($urltext,$content,$caption,$url);

    $urltext = 'NO - Review itemized output';
    $content = 'Complete any equipment expenses';
    $caption = '';
    $url = $this->uri(array('action'=>'outputexpenses'));
    $this->objnextcat->mouseoverpopup($urltext,$content,$caption,$url);
    
    
    /**
     *create tabbed box
     */
     
     $objcreateinvtab = new tabbedbox();
     $objcreateinvtab->addTabLabel('Service Information');
     $objcreateinvtab->addBoxContent('<br>'  . $strodcinfo . '<br />'.  '<br />' . $this->objnextlink->show() . '    ' . $this->objnextcat->show() . '<br />');         
    /**
     *display screen output
     */
     
     echo $objcreateinvtab->show();
     //echo '<br />' . $this->objnextlink->show();
     
                                   
   
   
?>
