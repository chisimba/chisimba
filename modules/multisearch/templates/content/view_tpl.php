<?php
header ( "Content-Type: text/html;charset=utf-8" );
$cssLayout = $this->newObject ( 'csslayout', 'htmlelements' );
$objSideBar = $this->newObject('usermenu', 'toolbar');
$cssLayout->setNumColumns ( 1 );

$googleColumn = NULL;
$yahooColumn = NULL;
$bingColumn = NULL;
$tabbox = NULL;

$tabbox = $form."<br />";

if(isset($output)) {
    $googleColumn .= $output['google'];
    $yahooColumn .= $output['yahoo'];
    $bingColumn .= $output['bing'];

    $tabs = $this->getObject('tabber', 'htmlelements');
    $tabs->addTab(array('name' => "Google", 'content' => $googleColumn, 'onclick' => ''));
    $tabs->addTab(array('name' => "Yahoo!", 'content' => $yahooColumn, 'onclick' => ''));
    $tabs->addTab(array('name' => "Bing", 'content' => $bingColumn, 'onclick' => ''));
    $tabbox .= $tabs->show(); 
}

$cssLayout->setLeftColumnContent($objSideBar->show());
$cssLayout->setMiddleColumnContent ( $tabbox );
echo $cssLayout->show();