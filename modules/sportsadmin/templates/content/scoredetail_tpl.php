<?php
//@ author nsabagwa mary

//security check that must be put on ever page
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

//a template to show the details of the scores
$this->loadClass('htmltable','htmlelements');
$this->objDbscores = $this->getObject('dbscores');
$this->objDbfixtures = $this->getObject('dbfixtures');
$this->objDbtournament = $this->getObject('dbtournament');
$this->objDbteam = $this->getObject('dbteam');
$this->objDbplayer = $this->getObject('dbplayer');
$this->objDbscores = $this->getObject('dbscores');
$heading = $this->getObject('htmlheading','htmlelements');
$sportid = $this->getParam('sportid',NULL);
$fixtureid = $this->getParam('fixtureid',NULL);
$teamid = $this->getParam('teamid',NULL);
$tournamentid = $this->getParam('tournamentid',NULL);
$content = "";

$fixtureteam = $this->objDbfixtures->getFixtureforgame($sportid,$fixtureid);
	foreach($fixtureteam  as $f){
	 $team_a = $f['team_a'];
	 $team_b = $f['team_b'];
	}
	
$heading->str = $this->objDbteam->getTeamNameById($team_a)."&nbsp;VS&nbsp;".$this->objDbteam->getTeamNameById($team_b)."&nbsp;[".$this->objDbtournament->getTournamentNameById($tournamentid)."]";
$heading->align = "center";

$content .= $heading->show();

$content .= $this->objLanguage->languageText('mod_sportsadmin_scoredetailsfor','sportsadmin')."&nbsp;".$this->objDbteam->getTeamNameById($teamid);

$scoreresults = $this->objDbscores->getscoredetails($teamid,$fixtureid);
$scorestable = new htmltable();
$scorestable->width = "50%";
if(!empty($scoreresults)){
  foreach($scoreresults as $s){
   $scorestable->startRow();
 
  $scorestable->addCell($this->objDbplayer->getPlayerNameById($s['playerid']));
  
   $scorestable->addCell($s['time']);
  $scorestable->endRow();
  
  
  }//clsoing foreach
$content .=  $scorestable->show();
}

else {
$scorestable->startRow();
$scorestable->addCell($this->objLanguage->languageText('mod_sportsadmin_noscoredforteam','sportsadmin')."&nbsp;".$this->objDbteam->getTeamNameById($teamid));
$scorestable->endRow();

$content .= $scorestable->show();
}

//add a link to take user back to main tournaments page
$tournamentsmain = new link($this->uri(array('item'=>'tournament','sportid'=>$sportid,'tournamentid'=>$tournamentid,'action'=>'tournamentdetails')));
$tournamentsmain->link = $this->objDbtournament->getTournamentNameById($tournamentid);

$content .= "<br/><br/>".$tournamentsmain->show();
echo $content;
?>