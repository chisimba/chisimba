<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(1);

// get the sidebar object
$this->leftMenu = $this->newObject('usermenu', 'toolbar');
$this->loadClass('htmlheading', 'htmlelements');
        
$middleColumn = NULL;
$leftColumn = NULL;

// Add in a heading
$headern = new htmlHeading();
$headern->str = $row['prodname'];
$headern->type = 1;

$middleColumn .= $headern->show();
$middleColumn .= $row['longdesc'];

//$leftColumn .= $this->leftMenu->show();

$cssLayout->setMiddleColumnContent($middleColumn);
//$cssLayout->setLeftColumnContent($leftColumn);
echo $cssLayout->show();
