<?php
header ( "Content-Type: text/html;charset=utf-8" );
$cssLayout = $this->newObject ( 'csslayout', 'htmlelements' );
$cssLayout->setNumColumns ( 3 );

// get the sidebar object
$this->leftMenu     = $this->newObject ( 'usermenu', 'toolbar' );
//$this->objDbEvents  = $this->getObject('dbevents');
$this->objOps       = $this->getObject('pansaops');
//$this->objDia       = $this->getObject('jqdialogue', 'htmlelements');

$middleColumn = NULL;

if(isset($message) && !empty($message) && $message != '' && is_object($message)) {
    $middleColumn .= $message->show();
}
$middleColumn .= $this->objOps->viewLocMap("-33", "19", $zoom = 15); 
$middleColumn .= $this->objOps->searchBox(); 

$leftColumn = NULL;
$rightColumn = NULL;

$cssLayout->setMiddleColumnContent ( $middleColumn );
$cssLayout->setLeftColumnContent ( $leftColumn );
$cssLayout->setRightColumnContent ( $rightColumn );

echo $cssLayout->show ();
