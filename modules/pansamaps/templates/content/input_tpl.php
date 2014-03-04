<?php
header ( "Content-Type: text/html;charset=utf-8" );
$cssLayout = $this->newObject ( 'csslayout', 'htmlelements' );
$cssLayout->setNumColumns ( 1 );

// get the sidebar object
$this->objOps       = $this->getObject('pansaops');

$middleColumn = NULL;

if(isset($message) && !empty($message) && $message != '' && is_object($message)) {
    $middleColumn .= $message->show();
}
//$middleColumn .= $this->objOps->viewLocMap("-33", "19", $zoom = 15); 
//$middleColumn .= $this->objOps->inputForm();
if(!isset($editparams) || empty($editparams)) { 
    $editparams = NULL;
}
echo $this->objOps->inputForm($editparams);

$leftColumn = NULL;
$rightColumn = NULL;

$cssLayout->setMiddleColumnContent ( $middleColumn );
$cssLayout->setLeftColumnContent ( $leftColumn );
$cssLayout->setRightColumnContent ( $rightColumn );

echo $cssLayout->show ();
