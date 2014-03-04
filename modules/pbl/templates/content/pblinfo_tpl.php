<?php
/**
* Template for displaying information about PBL to the user.
* @package pbl
*/

/**
* Template for displaying information about PBL to the user.
*/

// Suppress Page Variables
//$this->setVar('pageSuppressContainer', TRUE);
$this->setVar('pageSuppressBanner', TRUE);
$this->setVar('pageSuppressToolbar', TRUE);
//$this->setVar('suppressFooter', TRUE);
$this->setVar('pageSuppressIM', TRUE);

$bodyParams='class="container" '; 
$this->setVarByRef('bodyParams',$bodyParams);

$objHead =& $this->newObject('htmlheading','htmlelements');
$objLayer =& $this->newObject('layer','htmlelements');
$objIcon =& $this->newObject('geticon','htmlelements');
$this->loadClass('link', 'htmlelements');

$heading = $this->objLanguage->languageText('mod_pbl_pbl', 'pbl');
$objHead->type = 1;
$objHead->str = $heading;
$heading = $objHead->show();

$objLink = new link('http://www.cil.co.za');
$objLink->link = $this->objLanguage->languageText('mod_pbl_cil','pbl');
$objLink->title = $this->objLanguage->languageText('mod_pbl_cil','pbl');

$introBody = '<p>'.$this->objLanguage->code2Txt('mod_pbl_paraIntro', 'pbl', array('readonlys'=>'students'));

    $introBody.=' '.$this->objLanguage->code2Txt('mod_pbl_paraSetting', 'pbl', array('readonlys'=>'students'));

    $introBody.=' '.$this->objLanguage->languageText('mod_pbl_paraRole', 'pbl').'</p>';

    $introBody.='<p>'.$this->objLanguage->languageText('mod_pbl_paraAim', 'pbl');

    $introBody.=' '.$this->objLanguage->code2Txt('mod_pbl_paraServer', 'pbl', array('readonlys'=>'students'));

    $introBody.=' '.$this->objLanguage->languageText('mod_pbl_paraXML', 'pbl').'</p>';

    $introBody.='<p>'.$this->objLanguage->languageText('mod_pbl_paraCIL', 'pbl') .' '.$objLink->show() .' ';

    $introBody.=$this->objLanguage->languageText('mod_pbl_paraInProgress', 'pbl').'</p>';
    
    echo $heading.$introBody;
    
    $objIcon->setIcon('close');
    $objIcon->extra=" onclick='javascript:window.close()'";
    
    $objLayer->padding = '10px';
    $objLayer->align='center';
    $objLayer->str=$objIcon->show();
    
    echo $objLayer->show();
?>