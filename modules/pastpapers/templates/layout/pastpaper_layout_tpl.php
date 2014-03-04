<?php

$this->_objDBContext = $this->getObject('dbcontext','context');
$cssLayout =& $this->newObject('csslayout', 'htmlelements');
    
$instructions = $this->objLanguage->languageText('mod_pastpapers_instructions','pastpapers');
$contextCode = $this->_objDBContext->getContextCode();

if($contextCode){
$contextName = $this->_objDBContext->getTitle($contextCode);}
else {

$contextName = $this->objLanguage->languageText('mod_pastpapers_lobby','pastpapers');
}

$content = "";
$content .= $contextName."<br/><br/>";
$content .= $instructions;

//add link that will nbe used throughout navigation
$addlink = new link($this->uri(array('action'=>'add')));
$addlink->link = $this->objLanguage->languageText('mod_pastpapers_addpaper','pastpapers');

$mainlink = new link($this->uri(array('action'=>NULL)));
$mainlink->link = $this->objLanguage->languageText('mod_pastpapers_main','pastpapers');

$content .= "<br/>".$addlink->show();
if($this->getParam('action',NULL)){
$content .= "<br/>".$mainlink->show();}

$cssLayout->setLeftColumnContent($content);
$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show();


?>