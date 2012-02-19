<?php

// Create Side Bar Navigation
// End Side Bar Navigation

$toolbar = $this->getObject('contextsidebar');


$cssLayout = $this->newObject('csslayout', 'htmlelements');

$cssLayout->setLeftColumnContent($toolbar->show());

// Set the Content of middle column
$cssLayout->setMiddleColumnContent($this->getContent());

// Display the Layout
 $objModule = $this->getObject('modules', 'modulecatalogue');
$isRegistered = $objModule->checkIfRegistered('oer');
if ($isRegistered) {
    echo '<div id="threecolumn">' . $cssLayout->show() . '</div>';
} else {
    echo $cssLayout->show();
}
?>