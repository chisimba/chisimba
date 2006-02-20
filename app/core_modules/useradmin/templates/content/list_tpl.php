<?

    
    $objLanguage=& $this->getObject('language', 'language'); 
    
    $objButtons=& $this->getObject('navbuttons','navigation');

    $addlink=$this->uri(array('module'=>'useradmin','action'=>'Add'));
    $newlink=$this->objButtons->linkedButton("add",$addlink);
    
    $header= $this->getObject('htmlheading', 'htmlelements');
    $header->str = $objLanguage->languageText('mod_useradmin_name').' '.$newlink;
    $header->type = 1;
    
    echo $header->show();
    echo $title;
    
    echo $this->userAdminMenu();
?>