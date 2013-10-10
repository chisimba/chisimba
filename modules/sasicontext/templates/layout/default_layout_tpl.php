<?php

$toolbar = $this->getObject('contextsidebar', 'context');

$cssLayout = $this->newObject('csslayout', 'htmlelements');

$cssLayout->setLeftColumnContent($toolbar->show());

// Set the Content of middle column
$cssLayout->setMiddleColumnContent($this->getContent());

// Display the Layout
echo $cssLayout->show();

?>
