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
$scLink->link($this->uri(null,'prelogin'));
$scLink->link = $this->objLanguage->languageText('mod_prelogin_preview','prelogin');
$scLink->extra = "class='pseudobutton' target='_blank'";
$objH = &$this->newObject('htmlheading','htmlelements');
$objH->type = 1;
$objH->str = $this->objLanguage->languageText('mod_prelogin_mainheader','prelogin');

//icon to add a block
$objAddIcon = $this->newObject('geticon', 'htmlelements');
$objLink = $this->uri(array('action' => 'addblock'));
$objAddIcon->setIcon("add", "gif");
$objAddIcon->alt = $this->objLanguage->languageText('mod_prelogin_addblock','prelogin');
$add = $objAddIcon->getAddIcon($objLink);
$head = &$this->newObject('htmlheading','htmlelements');
$head->type = 3;
$head->str = $this->objLanguage->languageText('mod_prelogin_availableblocks','prelogin').' '.$add;

//if data has been changed inform user
$submitMsg = '';
switch ($this->getParam('change')) {
	case '2':
		$submitMsg1 = &$this->newObject('timeoutMessage','htmlelements');
    	$submitMsg1->setMessage($this->objLanguage->languageText("mod_prelogin_success",'prelogin'));
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



/************* LEFT NAVBAR **************/
$table->startRow();
$table->addCell("<strong>".$this->objLanguage->languageText('mod_prelogin_leftbar','prelogin')."</strong>");
$table->endRow();
$leftBlocks = $this->objPLBlocks->getBlocks('left');
foreach ($leftBlocks as $block) {
	$upLink->link($this->uri(array('action'=>'moveup','id'=>$block['id'])));
	$upLink->link = $objUpIcon->show();
	$downLink->link($this->uri(array('action'=>'movedown','id'=>$block['id'])));
	$downLink->link = $objDownIcon->show();
	$ed = $edIconLink->getEditIcon($this->uri(array('action' => 'editblock','id' => $block['id'])));
	$delLink = array('action' => 'delete', 'id' => $block['id']);
    $deletephrase = $objLanguage->languageText('phrase_delete');
    $conf = $objDelIcon->getDeleteIconWithConfirm('', $delLink, 'prelogin', $deletephrase);
    $actions = $upLink->show().' '.$downLink->show().' '.$ed.' '.$conf;
	($block['visible'] == 't')? $visibile = TRUE : $visibile = FALSE;
	$visibility = &new checkBox($block['id'].'_vis',$block['title'],$visibile);
	$updated = date('d/m/y',strtotime($block['datelastupdated']));
	$table->startRow();
	$table->addCell($block['title'],NULL,NULL,'left',$style);
	$table->addCell($visibility->show(),NULL,NULL,'left',$style);
	$table->addCell($this->objUser->fullName($block['updatedby']),NULL,NULL,'left',$style);
	$table->addCell($updated,NULL,NULL,'left',$style);
	$table->addCell($actions,NULL,NULL,'left',$style);
	$table->endRow();
	($style == 'even')? $style = 'odd' : $style = 'even';
}

/************* MIDDLE CONTENT **************/
$table->startRow();
$table->addCell("<strong>".$this->objLanguage->languageText('mod_prelogin_middle','prelogin')."</strong>");
$table->endRow();
$middleBlocks = $this->objPLBlocks->getBlocks('middle');
foreach ($middleBlocks as $block) {
	$upLink->link($this->uri(array('action'=>'moveup','id'=>$block['id'])));
	$upLink->link = $objUpIcon->show();
	$downLink->link($this->uri(array('action'=>'movedown','id'=>$block['id'])));
	$downLink->link = $objDownIcon->show();
	$ed = $edIconLink->getEditIcon($this->uri(array('action' => 'editblock','id' => $block['id'])));
	$delLink = array('action' => 'delete', 'id' => $block['id']);
    $deletephrase = $objLanguage->languageText('phrase_delete');
    $conf = $objDelIcon->getDeleteIconWithConfirm('', $delLink, 'prelogin', $deletephrase);
    $actions = $upLink->show().' '.$downLink->show().' '.$ed.' '.$conf;
	($block['visible'] == 't')? $visibile = TRUE : $visibile = FALSE;
	$visibility = &new checkBox($block['id'].'_vis',$block['title'],$visibile);
	$updated = date('d/m/y',strtotime($block['datelastupdated']));
	$table->startRow();
	$table->addCell($block['title'],NULL,NULL,'left',$style);
	$table->addCell($visibility->show(),NULL,NULL,'left',$style);
	$table->addCell($this->objUser->fullName($block['updatedby']),NULL,NULL,'left',$style);
	$table->addCell($updated,NULL,NULL,'left',$style);
	$table->addCell($actions,NULL,NULL,'left',$style);
	$table->endRow();
	($style == 'even')? $style = 'odd' : $style = 'even';
}

/************* RIGHT NAVBAR **************/
$table->startRow();
$table->addCell("<strong>".$this->objLanguage->languageText('mod_prelogin_rightbar','prelogin')."</strong>");
$table->endRow();
$rightBlocks = $this->objPLBlocks->getBlocks('right');
foreach ($rightBlocks as $block) {
	$upLink->link($this->uri(array('action'=>'moveup','id'=>$block['id'])));
	$upLink->link = $objUpIcon->show();
	$downLink->link($this->uri(array('action'=>'movedown','id'=>$block['id'])));
	$downLink->link = $objDownIcon->show();
	$ed = $edIconLink->getEditIcon($this->uri(array('action' => 'editblock','id' => $block['id'])));
	$delLink = array('action' => 'delete', 'id' => $block['id']);
    $deletephrase = $objLanguage->languageText('phrase_delete');
    $conf = $objDelIcon->getDeleteIconWithConfirm('', $delLink, 'prelogin', $deletephrase);
    $actions = $upLink->show().' '.$downLink->show().' '.$ed.' '.$conf;
	($block['visible'] == 't')? $visibile = TRUE : $visibile = FALSE;
	$visibility = &new checkBox($block['id'].'_vis',$block['title'],$visibile);
	$updated = date('d/m/y',strtotime($block['datelastupdated']));
	$table->startRow();
	$table->addCell($block['title'],NULL,NULL,'left',$style);
	$table->addCell($visibility->show(),NULL,NULL,'left',$style);
	$table->addCell($this->objUser->fullName($block['updatedby']),NULL,NULL,'left',$style);
	$table->addCell($updated,NULL,NULL,'left',$style);
	$table->addCell($actions,NULL,NULL,'left',$style);
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
$link->link = $this->objLanguage->languageText('mod_prelogin_addblock','prelogin');

$content = $objH->show().$scLink->show().$submitMsg.$head->show().$objForm->show().$link->show();
echo $content;
?>