<?php
/*
* Template for iframe containing learning issues and hypothesis.
* @package pbl
*/

/*
* Template for iframe containing learning issues and hypothesis.
*/

// Suppress Page Variables
$this->setVar('pageSuppressContainer', TRUE);
$this->setVar('pageSuppressBanner', TRUE);
$this->setVar('pageSuppressToolbar', TRUE);
$this->setVar('suppressFooter', TRUE);
$this->setVar('pageSuppressIM', TRUE);

$bodyParams='class="container" '; 
$this->setVarByRef('bodyParams',$bodyParams);

// set up html elements objects
//$this->loadClass('multitabbedbox', 'htmlelements');
$objTab = $this->newObject('tabcontent', 'htmlelements');
$objMessage = $this->newObject('timeoutmessage','htmlelements');
$objMessage->setTimeout(5000);

// set up language items
$liLabel = $this->objLanguage->languageText('phrase_learningissues');
$hypLabel = $this->objLanguage->languageText('word_hypothesis');


// get written content from db
$li = '<b>'.strtoupper($liLabel) .'</b><br />';
if(!empty($msgli)){
    $objMessage->setMessage($msgli.' '.$liLabel.'<br />');
    $li .= $objMessage->show();
}
$li .= $this->classroom->writeNotes('li');

$hypothesis = '<b>'.strtoupper($hypLabel).'</b><br />';
if(!empty($msghyp)){
    $objMessage->setMessage($msghyp.' '.$hypLabel.'<br />');
    $hypothesis .= $objMessage->show();
}
$hypothesis .= $this->classroom->writeNotes('hypothesis');

/* Create a multitabbed box containing the content
$objBox = new multitabbedBox('224px', '97%');
$tab1['name']=$liLabel;
$tab1['content']=$li;
$tab1['default']=TRUE;
$objBox->addTab($tab1);
$tab2['name']=$hypLabel;
$tab2['content']=$hypothesis;
$objBox->addTab($tab2);
*/

$objTab->init();
$objTab->addTab($liLabel, $li);
$objTab->addTab($hypLabel, $hypothesis);

echo '<br style="line-height:1px;" />'.$objTab->show();
?>