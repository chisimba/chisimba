<?php
/**
* @package redirect
*/

/**
* Layout template for the redirect module
*/

$cssLayout = $this->newObject('csslayout', 'htmlelements');

if(isset($menu)){
    $leftMenu = $this->newObject($menu.'menu','toolbar');
}else{
    $leftMenu = $this->newObject('usermenu','toolbar');
}

$cssLayout->setLeftColumnContent($leftMenu->show());
$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show();
?>