<?php
/**
* @package pbladmin
*/

/**
* Template for creating a new pbl case.
* @param array $data Array containing the number of the next scene and the id of the previous scene
*/
$this->setLayoutTemplate('admin_layout_tpl.php');

// set up html elements
$objLayer = $this->newObject('layer','htmlelements');
$this->loadClass('label','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('textarea','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('form','htmlelements');
$this->loadClass('link', 'htmlelements');

// set up language items
$head = $this->objLanguage->languageText('mod_pbladmin_createpblcase', 'pbladmin');
$caseLabel = $this->objLanguage->languageText('phrase_casename');
$nameLabel = $this->objLanguage->languageText('phrase_scenename');
$sceneLabel = $this->objLanguage->languageText('word_scene');
$taskLabel = $this->objLanguage->languageText('word_task');
$optionalLabel = $this->objLanguage->languageText('word_optional');
$continueLabel = $this->objLanguage->languageText('word_continue');
$finishLabel = $this->objLanguage->languageText('word_finish');
$exitLabel = $this->objLanguage->languageText('word_cancel');
$mcqLabel = $this->objLanguage->languageText('mod_pbladmin_addmcq', 'pbladmin');
$choiceLabel = $this->objLanguage->languageText('mod_pbladmin_addcaq', 'pbladmin');

// Heading
$this->setVarByRef('heading',$head);

echo '<p>&nbsp;</p>';

// Form elements
$objLabel = new label('<b>'.$caseLabel.':</b>','input_casename');
$caseInput = '<p>'.$objLabel->show();

if(empty($data['minfo'])){
    $objInput = new textinput('casename');
    $caseInput .= '&nbsp;&nbsp;&nbsp;'.$objInput->show().'</p>';
}else{ 
    $caseInput .= '&nbsp;&nbsp;&nbsp;'.$data['casename'].'</p>';
}

$objLabel = new label('<b>'.$nameLabel.':</b>','input_scenename');
$sceneidInput = '<p>'.$objLabel->show();

$objInput = new textinput('scenename');
$sceneidInput .= '&nbsp;&nbsp;&nbsp;'.$objInput->show().'</p>';

$num=$data['num'];

// Text input for a scene
$objLabel = new label('<b>'.$sceneLabel.' '.$num.':</b>','input_scene');
$sceneInput = $objLabel->show();

$objText = new textarea('scene', '', 10, 80);
$sceneInput .= '<p>'.$objText->show().'</p>';

$objLink = new link('#');
$objLink->link = $mcqLabel;
$objLink->extra = "onclick=\"javascript:window.open('".$this->uri(array('action'=>'addmcq'),'','',TRUE)."','addmcq', 'width=350, height=450, scrollbars=1, resizable=1')\"";
$mcqBtn = $objLink->show();

$objLink = new link('#');
$objLink->link = $choiceLabel;
$objLink->extra = "onclick=\"javascript:window.open('".$this->uri(array('action'=>'addcaq'),'','',TRUE)."','addcaq','width=350, height=300, scrollbars=1, resizable=1')\"";
$mcqBtn .= '&nbsp;&nbsp;|&nbsp;&nbsp;'.$objLink->show();

// Text input for a task
$objLabel = new label('<b>'.$taskLabel.' '.$num.':</b>','input_task');
$taskInput = '<br />'.$objLabel->show().'&nbsp;&nbsp;( '.$mcqBtn.' )';

$objText = new textarea('task', '', 10, 80);
$taskInput .= '<p>'.$objText->show().'</p>';

// hidden elements
$objInput = new textinput('num',$num, 'hidden');
$hidden = $objInput->show();

$objInput = new textinput('minfo',$data['minfo'], 'hidden');
$hidden .= $objInput->show();

if(!empty($data['minfo'])){
    $objInput = new textinput('casename',$data['casename'], 'hidden');
    $hidden .= $objInput->show();

    $objInput = new textinput('caseid',$data['caseid'], 'hidden');
    $hidden .= $objInput->show();
}

// Buttons
$objButton = new button('continue', $continueLabel);
$objButton->setToSubmit();
$objButton->setIconClass("forward");
$btns = $objButton->show();

$objButton = new button('finish', $finishLabel);
$objButton->setToSubmit();
$objButton->setIconClass("save");
$btns .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$objButton->show();

$objButton = new button('cancel', $exitLabel);
$objButton->setToSubmit();
$objButton->setIconClass("cancel");
$btns .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$objButton->show();

$objLayer->str = $btns;
$submitBtns = $objLayer->show();

$objForm = new form('create', $this->uri(array('action'=>'create')));
$objForm->addToForm('<P>'.$caseInput.'</P>');
$objForm->addToForm('<P>'.$sceneidInput.'</P>');
$objForm->addToForm('<P>'.$sceneInput.'</P>');
$objForm->addToForm('<P>'.$taskInput.'</P><br />');
$objForm->addToForm($hidden);
$objForm->addToForm($submitBtns);

echo $objForm->show();
?>