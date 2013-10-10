<?php

// Create an Instance of the CSS Layout
$cssLayout = $this->newObject('csslayout', 'htmlelements');

$sideMenu = $this->getObject('sidemenu', 'contextstats');

$cssLayout->setLeftColumnContent($sideMenu->show($pagesize, $fromdate, $todate).$sideMenu->showSummary($pagesize, $fromdate, $todate));

// Set the Content of middle column
$cssLayout->setMiddleColumnContent($this->getContent());

// Display the Layout
echo $cssLayout->show();
?>
