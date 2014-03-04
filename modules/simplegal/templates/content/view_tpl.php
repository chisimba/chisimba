<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(1);

// get the sidebar object
$this->leftMenu = $this->newObject('usermenu', 'toolbar');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('href', 'htmlelements');
//$this->objWallOps    = $this->getObject('wallops', 'wall');
        
$mainColumn = NULL;

// send the data from controller to an operation function to build the gallery now
$mainColumn .= $this->objOps->formatData($data);

//$mainColumn .= $this->objWallOps->showWall(0, 10);

$objApps = $this->getObject('fbapps', 'facebookapps');
$mainColumn .= '<div class="FB_comment">' . $objApps->getComments() . '</div>';

$cssLayout->setMiddleColumnContent($mainColumn);
echo $cssLayout->show();
