<?php

/* ------------icon request template----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

$this->loadClass('checkbox','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('form','htmlelements');

//main heading
$scLink = &$this->newObject('link','htmlelements');
$scLink->link($this->uri(null,'eportfolio'));
$scLink->link = $this->objLanguage->languageText('mod_eportfolio_preview','eportfolio')." ".$this->objLanguage->languageText('mod_eportfolio_wordEportfolio','eportfolio');
$scLink->extra = "class='pseudobutton' target='_blank'";
$objH = &$this->newObject('htmlheading','htmlelements');
$objH->type = 1;
$objH->str = $this->objLanguage->languageText('mod_eportfolio_eportfoliosetup','eportfolio');

//icon to add a block
$objAddIcon = $this->newObject('geticon', 'htmlelements');
$objLink = $this->uri(array('action' => 'addblock'));
$objAddIcon->setIcon("add", "gif");
$objAddIcon->alt = $this->objLanguage->languageText('mod_eportfolio_addblock','eportfolio');
$add = $objAddIcon->getAddIcon($objLink);
$head = &$this->newObject('htmlheading','htmlelements');
$head->type = 3;
//Hide add block
//$head->str = $this->objLanguage->languageText('mod_prelogin_availableblocks','prelogin').' '.$add;
$head->str = $this->objLanguage->languageText('mod_prelogin_availableblocks','prelogin');

//if data has been changed inform user
$submitMsg = '';
switch ($this->getParam('change')) {
    case '2':
        $submitMsg1 = &$this->newObject('timeoutMessage','htmlelements');
        $submitMsg1->setMessage($this->objLanguage->languageText("mod_eportfolio_updatesuccess",'eportfolio'));
        $submitMsg = '<br/>'.$submitMsg1->show().'<br />';
        break;
}

//setup table headings
$tableHd[] = $objLanguage->languageText('word_block');
$tableHd[] = $objLanguage->languageText('word_visible');
$tableHd[] = $objLanguage->languageText('phrase_updatedby');
$tableHd[] = $objLanguage->languageText('phrase_updated');
$tableHd[] = $objLanguage->languageText('word_action');

//initialise table
$table = &$this->newObject('htmltable','htmlelements');
$table->addHeader($tableHd,'heading','align="left"');
$table->cellspacing = "2";
$table->cellpadding = "2";
$table->width = "60%";
$style = 'even';

//links for the buttons to edit and move a block
$edIconLink = &$this->newObject('geticon', 'htmlelements');
$downLink = &$this->newObject('link', 'htmlelements');
$upLink = &$this->newObject('link', 'htmlelements');
//load delete block icon
$objDelIcon = &$this->newObject('geticon', 'htmlelements');
//load movement icons
$objUpIcon = &$this->newObject('geticon', 'htmlelements');
$objUpIcon->setIcon('mvup');
$objUpIcon->alt = $this->objLanguage->languageText('phrase_moveup');
$objDownIcon = &$this->newObject('geticon', 'htmlelements');
$objDownIcon->setIcon('mvdown');
$objDownIcon->alt = $this->objLanguage->languageText('phrase_movedown');



/************* MAIN TAB **************/
$table->startRow();
$table->addCell("<strong>".$this->objLanguage->languageText('mod_eportfolio_mainblock','eportfolio')."</strong>");
$table->endRow();
//PATCH.Update Visiblity. Some records were updated by others besides the logged in user
$wrongBlocks = $this->objEPBlocks->getWrongUpdates();
foreach ($wrongBlocks as $block) {
    $this->objEPBlocks->updateVisibility($block['id'], $block['visible']);
}

$mainTab = $this->objEPBlocks->getBlocks('main');

foreach ($mainTab as $block) {
    $upLink->link($this->uri(array('action'=>'moveup','id'=>$block['id'])));
    $upLink->link = $objUpIcon->show();
    $downLink->link($this->uri(array('action'=>'movedown','id'=>$block['id'])));
    $downLink->link = $objDownIcon->show();
    $ed = $edIconLink->getEditIcon($this->uri(array('action' => 'editblock','id' => $block['id'])));
    $delLink = array('action' => 'delete', 'id' => $block['id']);
    $deletephrase = $objLanguage->languageText('phrase_delete');
    $conf = $objDelIcon->getDeleteIconWithConfirm('', $delLink, 'eportfolio', $deletephrase);
    //Hide edit and delete actions
    $actions = $upLink->show().' '.$downLink->show();
    //$actions = $upLink->show().' '.$downLink->show().' '.$ed.' '.$conf;
    ($block['visible'] == $this->TRUE)? $visibile = TRUE : $visibile = FALSE;
    $visibility = &new checkBox($block['id'].'_vis',$block['title'],$visibile);
    $updated = date('d/m/y',strtotime($block['datelastupdated']));
    $table->startRow();
    $table->addCell($block['title'],NULL,NULL,'left',$style);
    $table->addCell($visibility->show(),NULL,NULL,'center',$style);
    $table->addCell($this->objUser->fullName($block['updatedby']),NULL,NULL,'left',$style);
    $table->addCell($updated,NULL,NULL,'left',$style);
    $table->addCell($actions,NULL,NULL,'center',$style);
    $table->endRow();
    ($style == 'even')? $style = 'odd' : $style = 'even';
}

/************* IDENTIFICATION TAB **************/
$table->startRow();
$table->addCell("<strong>".$this->objLanguage->languageText('mod_eportfolio_identificationblock','eportfolio')."</strong>");
$table->endRow();
$identityTab = $this->objEPBlocks->getBlocks('identity');
foreach ($identityTab as $block) {
    $upLink->link($this->uri(array('action'=>'moveup','id'=>$block['id'])));
    $upLink->link = $objUpIcon->show();
    $downLink->link($this->uri(array('action'=>'movedown','id'=>$block['id'])));
    $downLink->link = $objDownIcon->show();
    $ed = $edIconLink->getEditIcon($this->uri(array('action' => 'editblock','id' => $block['id'])));
    $delLink = array('action' => 'delete', 'id' => $block['id']);
    $deletephrase = $objLanguage->languageText('phrase_delete');
    $conf = $objDelIcon->getDeleteIconWithConfirm('', $delLink, 'eportfolio', $deletephrase);
    //Hide edit and delete actions
    $actions = $upLink->show().' '.$downLink->show();
    //$actions = $upLink->show().' '.$downLink->show().' '.$ed.' '.$conf;
    ($block['visible'] == $this->TRUE)? $visibile = TRUE : $visibile = FALSE;
    $visibility = &new checkBox($block['id'].'_vis',$block['title'],$visibile);
    $updated = date('d/m/y',strtotime($block['datelastupdated']));
    $table->startRow();
    $table->addCell($block['title'],NULL,NULL,'left',$style);
    $table->addCell($visibility->show(),NULL,NULL,'center',$style);
    $table->addCell($this->objUser->fullName($block['updatedby']),NULL,NULL,'left',$style);
    $table->addCell($updated,NULL,NULL,'left',$style);
    $table->addCell($actions,NULL,NULL,'center',$style);
    $table->endRow();
    ($style == 'even')? $style = 'odd' : $style = 'even';
}

$submitButton = &new button('update',$this->objLanguage->languageText('word_update'));
$submitButton->setToSubmit();
$objForm = &new form('vis_form',$this->uri(array('action'=>'update')));
$objForm->addToForm($table->show());
$objForm->addToForm($submitButton);
$link = &$this->getObject('link','htmlelements');
$link->link($objLink);
$link->link = $this->objLanguage->languageText('mod_eportfolio_addblock','eportfolio');

//$content = $objH->show().$scLink->show().$submitMsg.$head->show().$objForm->show().$link->show();
//Hide add block
$content = $objH->show().$scLink->show().$submitMsg.$head->show().$objForm->show();
echo $content;
?>
