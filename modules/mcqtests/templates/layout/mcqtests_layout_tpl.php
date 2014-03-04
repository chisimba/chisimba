<?php
/**
 * @package mcqtests
 * Layout template for the mcqtests module
 */
$cssLayout = &$this->newObject('csslayout', 'htmlelements');
$leftMenu = &$this->getObject('contextsidebar', 'context');
$objHead = &$this->newObject('htmlheading', 'htmlelements');
if (!isset($heading)) {
    $heading = $objLanguage->languageText('mod_mcqtests_name', 'mcqtests');
}
$objHead->str = $heading;
$objHead->type = 1;
$head = $objHead->show();
$left = $leftMenu->show();
$middle = $head.$this->getContent();
$middle = "<div class='mcq_main'>$middle</div>";
$cssLayout->setLeftColumnContent($left);
$cssLayout->setMiddleColumnContent($middle);
echo $cssLayout->show();
?>