<?php
/*
* @Author : nsabagwa mary
* Page for adding newsupdates to a team
*/

$this->loadClass('form','htmlelements');
$heading = & $this->getObject('htmlheading','htmlelements');
$this->loadClass('htmltable','htmlelements');
$this->objDbteam = $this->getObject('dbteam');
$teamid = $this->getParam('teamid',NULL);
$this->loadClass('textarea','htmlelements');
$submitbutton = & $this->getObject('button','htmlelements');
$sportid = $this->getParam('sportid',NULL);

$formurl = $this->uri(array('action'=>'savenews','teamid'=>$teamid,'sportid'=>$sportid));
$form = new form('news',$formurl);


$heading->str = $this->objLanguage->languageText('mod_sportsadmin_addsportsnews','sportsadmin')."&nbsp;".$this->objDbteam->getTeamNameById($teamid);

$table = new htmltable();

$table->width = "70%";
$table->align = "center";

$news_text = new textarea('news','',4,40);
$submitbutton->name = 'submit';
$submitbutton->value = $this->objLanguage->languageText('word_submit','system');
$submitbutton->setToSubmit();


$canceluri = $this->uri(array('action'=>'teamdetails','sportid'=>$sportid,'teamid'=>$teamid));
$cancel = new button('cancel',$this->objLanguage->languageText('word_cancel','system'));
$cancel->setOnClick("window.location = '{$canceluri}'");

$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_sportsadmin_enternews','sportsadmin'));
$table->addCell($news_text->show());
$table->endRow();

$table->startRow();
$table->addCell("&nbsp;");
$table->addCell($submitbutton->show()."&nbsp;".$cancel->show());
$table->endRow();

$form->addToForm($table->show());

echo $heading->show();
echo $form->show();
?>