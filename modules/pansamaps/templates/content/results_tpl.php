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

$table = $this->newObject('htmltable', 'htmlelements');
if(!empty($results)) {
    foreach($results as $res) {
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
		$delIcon = $this->objIcon->getDeleteIconWithConfirm($res['id'], array(
                 'module' => 'pansamaps',
                 'action' => 'deleterecord',
                 'recid' => $res['id']
               ) , 'pansamaps');
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        $edIcon = $this->objIcon->getEditIcon($this->uri(array(
            'action' => 'editrecords',
            'recid' => $res['id'],
            'module' => 'pansamaps'
            )));
        $table->startRow();
        $table->addCell($res['venuename']);
        $table->addCell($res['city']);
        $table->addCell($res['phonecode']." ".$res['phone']);
        $table->addCell($res['contactperson']);
        $table->addCell($res['venuedescription']);
        $table->addCell($edIcon." ".$delIcon);
        $table->endRow();    
    }
    $middleColumn .= $table->show();
}
else {
    $middlecolumn = "<h1>".$this->objLanguage->languageText("mod_pansamaps_noresults", "pansamaps")."</h1>";
}


$middleColumn .= $this->objOps->searchBox(); 

$leftColumn = NULL;
$rightColumn = NULL;

$cssLayout->setMiddleColumnContent ( $middleColumn );
$cssLayout->setLeftColumnContent ( $leftColumn );
$cssLayout->setRightColumnContent ( $rightColumn );

echo $cssLayout->show ();
