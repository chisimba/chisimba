<?php
// Create Side Bar Navigation
$toolbar = $this->getObject('contextsidebar');
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setLeftColumnContent($toolbar->show());

// Set the Content of middle column
$ret = "<div class='context_cpanel'>" . $this->getContent() . "</div>";
$cssLayout->setMiddleColumnContent($ret);
// Display the Layout
echo $cssLayout->show();
?>