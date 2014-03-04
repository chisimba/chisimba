<?php 
//-----sports class extends controller---------

//security check that must be put on ever page
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

/*
*  A template file for adding scores for a given fixture
* Author Nsabagwa Mary
*/
$content ="";
$fixtureid = $this->getParam('fixtureid',NULL);
$teamid = $this->getParam('teamid',NULL);
$tournamentid = $this->getParam('tournamentid',NULL);
$sportid = $this->getParam('sportid',NULL);
$playerid = $this->getParam('playerid',NULL);
$this->objDbteam = $this->getObject('dbteam');
$form = & $this->getObject('form','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('htmltable','htmlelements');
$heading = & $this->getObject('htmlheading','htmlelements');
$this->loadClass('link','htmlelements');
$this->objDbfixtures = & $this->getObject('dbfixtures');
$this->loadClass('button','htmlelements');
$this->objDbfixture = $this->getObject('dbfixtures');
$this->objDbsport = $this->getObject('dbsports');


//echo $playerid; exit;

//get the fixture 
$fixture = $this->objDbfixtures->getFixtureById($fixtureid);

foreach($fixture as $fi){
	$teama_name = $this->objDbteam->getTeamNameById($fi['team_a']);
	$teamb_name = $this->objDbteam->getTeamNameById($fi['team_b']);
}


$form->setAction($this->uri(array('action'=>'savescores')));

$table = new htmltable();
$table->align = "center";
$table->width = "50%";

$heading->str = $teama_name."&nbsp;Vs&nbsp;".$teamb_name."&nbsp;".$this->objLanguage->languageText('mod_sportsadmin_scores','sportsadmin');
$heading->align = "center";
$content .= $heading->show();

//field for entering team a scores
$teamfield = new textinput('teamid',$teamid,'hidden');
$timefield = new textinput('time');

//hidden elements
$sportid_field = new textinput('sportid',$this->getParam('sportid',NULL),'hidden');
$tournament_field = new textinput('tournamentid',$tournamentid,'hidden');
$fixture_field = new textinput('fixtureid',$fixtureid,'hidden');
$player_field = new textinput('playerid',$playerid,'hidden');

$submitbutton = new button('submit',$this->objLanguage->languageText('word_submit'));
$submitbutton->setToSubmit();

$evaluationMode = $this->objDbsport->getevaluation($sportid);

$table->startRow();
$table->addCell($evaluationMode."&nbsp;".$this->objLanguage->languageText('mod_sportsadmin_ascoredby','sportsadmin'));
$table->addCell($this->objDbplayer->getPlayerNameById($playerid));
$table->endRow();

$table->startRow();
$table->addCell($this->objLanguage->LanguageText('word_time'));
$table->addCell($timefield->show());
$table->endRow();

 
//add hidden fields to the table $teamfield 
$table->startRow();
$table->addCell($sportid_field ->show());
$table->addCell($tournament_field->show());
$table->addCell($fixture_field->show());
$table->addCell($teamfield ->show());
$table->addCell($player_field->show());
$table->endRow();

$table->startRow();
$table->addCell("");
$table->addCell($submitbutton->show());
$table->endRow();


$form->addToForm($table->show());
//add the table to content 
$content .= $form->show();

$backlink = & $this->getObject('link','htmlelements');
$backlink->href = "javascript:history.back()";
$backlink->link = $this->objLanguage->languageText('word_back');

$content .= "<br/><br/>".$backlink->show();

echo $content;

?>