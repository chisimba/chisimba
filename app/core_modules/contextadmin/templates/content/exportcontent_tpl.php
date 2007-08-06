<?php

    $this->objH = $this->getObject('htmlheading', 'htmlelements');
    $this->objH->type=1;
    $this->objH->str=$this->objLanguage->languageText("mod_contextadmin_exportcontent",'contextadmin');
 
    $center =     $this->objH->show();
        
    $center  .= $list;
    
    $this->setVar('footerStr', $this->getContextLinks().$this->getContentLinks());
    $cssLayout = $this->newObject('csslayout', 'htmlelements');
    $leftMenu = $this->newObject('contextmenu','toolbar');
    
    $cssLayout->setLeftColumnContent($leftMenu->show());
    $cssLayout->setMiddleColumnContent($center);
    
    echo $cssLayout->show();
?>