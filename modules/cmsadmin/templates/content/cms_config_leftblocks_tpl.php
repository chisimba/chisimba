<?php
/**
* Template to display the list of menu items
*/

$objHead = $this->newObject('htmlheading', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');
$objLayer =$this->newObject('layer','htmlelements');
$objRound = $this->newObject('roundcorners','htmlelements');
$objFeatureBox = $this->newObject('featurebox', 'navigation');
$objEditor = $this->newObject('htmlarea', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('label', 'htmlelements');

$head = $this->objLanguage->languageText('mod_cmsadmin_menu', 'cmsadmin');
$hdConfigure = $this->objLanguage->languageText('mod_cmsadmin_configureleftblocks', 'cmsadmin');
$hdCreate = $this->objLanguage->languageText('mod_cmsadmin_createblock', 'cmsadmin');
$lbHeading = $this->objLanguage->languageText('phrase_blockheading');
$lbContent = $this->objLanguage->languageText('phrase_blockcontent');
$btnSave = $this->objLanguage->languageText('word_save');

/* ** page heading ** */
$objIcon->setIcon('menu', 'png', 'icons/cms/');

$objHead->str = $objIcon->show().'&nbsp;'.$head;
$objHead->type = 1;
$objLayer->str = $objHead->show();
//$objLayer->border = '; float:left; align: left; margin:0px; padding:0px;';
$objLayer->id = 'cms_header_left';
$headStr = $objLayer->show();
$objLayer->str = $topNav;
//$objLayer->border = '; float:right; align:right; margin:0px; padding:0px;';
$objLayer->id='cms_header_right';
$headStr .= $objLayer->show();

$objLayer->str = '';
//$objLayer->border = '; clear:both; margin:0px; padding:0px;';
$objLayer->id = 'cms_header_clear';
$objLayer->cssClass='clearboth';
$navStr = $objLayer->show();

$str = $objRound->show($headStr.$navStr);

/* ** page content ** */

$objHead->str = $hdConfigure;
$objHead->type = 3;
$leftStr = $objHead->show();

// Create user defined block
$objHead->str = $hdCreate;
$objHead->type = 3;
$rightStr = $objHead->show();

$heading = ''; $content = ''; $id = '';
if(!empty($block)){
    $heading = $block['heading'];
    $content = $block['content'];
    $id = $block['id'];
}

$objLabel = new label($lbHeading, 'input_heading');
$objInput = new textinput('heading', $heading);
$objInput->extra = "maxsize = '100'";

$formStr = '<p>'.$objLabel->show().': <br />'.$objInput->show().'</p>';

$objLabel = new label($lbContent, 'input_content');
$objEditor->init('content', $content, 20, 50);
$objEditor->setBasicToolBar();

$formStr .= '<p>'.$objLabel->show().': <br />'.$objEditor->show().'</p>';

if(!empty($id)){
    $objInput = new textinput('id', $id, 'hidden');
    $formStr .= $objInput->show();
}

$objButton = new button('save', $btnSave);
$objButton->setToSubmit();
$formStr .= '<p>'.$objButton->show().'</p>';

$objForm = new form('createblock', $this->uri(array('action' => 'createblock')));
$objForm->addToForm($formStr);
$rightStr .= $objForm->show();

if(!empty($block)){
    $rightStr .= '<br />'.$objFeatureBox->show($heading, $content);
}

// Display
$objTable = new htmltable();
$objTable->startRow();
//$objTable->addCell($leftStr, '40%');
//$objTable->addCell('', '2%');
$objTable->addCell($rightStr);
$objTable->endRow();

$str .= $objTable->show();

echo $str;
?>
