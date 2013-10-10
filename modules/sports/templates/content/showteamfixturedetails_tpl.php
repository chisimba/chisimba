<?php
//security check that must be put on ever page

if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

/*author nsabagwa mary
* A template to display the details of the fixture including the players of the matches
*/

$content = "";
$sportid = $this->getParam('sportid',NULL);
$heading =& $this->getObject('htmlheading','htmlelements');
$this->objLanguage =& $this->getObject('language','language'); 
$fixtureid = $this->getParam('fixtureid',NULL);
$tournamentid = $this->getParam('tournamentid',NULL);
$this->loadClass('form','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->objDbfixtures = & $this->getObject('dbfixtures','sportsadmin');
$addIcon = & $this->getObject('geticon','htmlelements');
$this->objDbplayer = $this->getObject('dbplayer','sportsadmin');
$this->objDbscores = $this->getObject('dbscores','sportsadmin');
$this->loadClass('htmltable','htmlelements');
$this->loadClass('htmltable','htmlelements');
$this->objSports = $this->getObject('dbsports','sportsadmin');
$this->objDbteam = $this->getObject('dbteam','sportsadmin');
$this->objDbmatchplayers = $this->getObject('dbmatchplayer','sportsadmin');
$this->objDbtournament = $this->getObject('dbtournament','sportsadmin');
$this->loadClass('link','htmlelements');
//$addicon = $this->getObject('geticon','htmlelements');
$evaluationMode = $this->objSports->getevaluation($sportid);

$tournament_label = $this->objDbtournament->getTournamentdetails($sportid,$tournamentid);
$tournamentDuration = $this->objDbtournament->pickTournamentduration($sportid,$tournamentid);

if(!empty($tournamentDuration)){
  foreach($tournamentDuration as $td){
	$startDate = $td['startDate'];
	$endDate = $td['endDate'];
	}
}	
$sport = $this->objSports->getSportsById($sportid);

$heading->str = $sport."&nbsp;:&nbsp;".$tournament_label."&nbsp;(".$startDate."&nbsp;".$this->objLanguage->languageText('word_to','system')."&nbsp;".$endDate.")";


$content .= $heading->show();


$searchform = new form('search',$this->uri(array(NULL)));

$searchtable->width="40%";
$searchtable->cellspacing= 2;
$searchtable->cellpadding= 2;

//adding what is displayed under players
$playertable = new htmltable();
$playertable->width = "70%";

 $playertable->startRow();
 $playertable->addHeaderCell($this->objLanguage->languageText('word_name','system'));
 $playertable->endRow();


//get the the two teams in the fixture
$fixture_teams = $this->objDbfixtures->getFixtureforgame($sportid,$fixtureid);

//get the two teams in the tournament
if(!empty($fixture_teams)){
  foreach($fixture_teams as $f){
  $a = $this->objDbteam->getTeamNameById($f['team_A']);
  $b = $this->objDbteam->getTeamNameById($f['team_B']);
  
    $content .=  "&nbsp;"."<u>".$a  ."&nbsp; vs &nbsp ".$b."&nbsp;".$this->objLanguage->languageText('mod_sportsadmin_players','sportsadmin')."</u>";
  
  $teamaid = $f['team_A'];
  $teambid = $f['team_B'];
  }//closing for each
}
else {

    $content .= $this->objLanguage->languageText('mod_sportsadmin_nofixtures','sportsadmin');
}

//Display players scheduled for a given match for a given team
$table =new htmltable();
$table->width ='50%';
$table->cellspacing =2;

$table->startRow();
$table->addHeaderCell($a);
$table->addHeaderCell($b);
$table->endRow();


//get the players from each of the teams in the fixture
$playerlist_A = $this->objDbmatchplayers->getPlayersForTeamInFixture($teamaid ,$fixtureid);
$playerlist_B = $this->objDbmatchplayers->getPlayersForTeamInFixture($teambid ,$fixtureid);


$list_A = new htmltable();
$list_A->cellspacing = 2;
if(!empty($playerlist_A)){

  $list_A->startRow();
  $list_A->addCell("<strong>".$this->objLanguage->languageText('word_name','system')."</strong>","", NULL, NULL, "even");
  $list_A->addCell("<strong>".$this->objLanguage->languageText('mod_sportsadmin_scores','sportsadmin')."</strong>","", NULL, NULL, "even");
  $list_A->endRow();
 
  $class = 'even';
    foreach($playerlist_A as $pl){
  
   
  
  $class = ($class == 'odd') ? 'even':'odd';
    
 //get the name of the player using the id
 $name = $this->objDbplayer->getPlayerNameById($pl['playerId']);
 
 //pick the scores for the player
 $playerscores = $this->objDbscores->getscoresForPlayer($pl['playerId'],$fixtureid);
  
  $list_A->startRow();
  $list_A->addCell($name,"", NULL, NULL, $class);
  $list_A->addCell($playerscores,"", NULL, NULL, $class);
  $list_A->endRow();
 
  }

}

else{ $list_A->startRow();
  $list_A->addCell($this->objLanguage->languageText('mod_sportsadmin_nomatchplayers','sportsadmin'),"", NULL, NULL, $class);
  $list_A->endRow();}
  
  
$list_B = new htmltable();
$list_B->cellspacing = 2;

if(!empty($playerlist_B)){

$list_B->startRow();
  $list_B->addCell("<strong>".$this->objLanguage->languageText('word_name')."</strong>","", NULL, NULL, "even");
  $list_B->addCell("<strong>".$this->objLanguage->languageText('mod_sportsadmin_scores','sportsadmin')."</strong>","", NULL, NULL, "even");
  $list_B->addCell("<strong>".$this->objLanguage->languageText('word_action','system')."</strong>","", NULL, NULL, "even");
  $list_B->endRow();
$class = 'even';

  foreach($playerlist_B as $p){
  //link for adding scores for every player
  $uri4b = $this->uri(array('action'=>'addscores','sportid'=>$sportid,'fixtureid'=>$fixtureid,'tournamentid'=>$tournamentid,'teamid'=>$teambid,'playerid'=>$p['playerId']));
  $addscore4b= new link($uri4b);
  
  $addscore4b->link = $this->objLanguage->languageText('mod_sportsadmin_enterscores','sportsadmin');
  
  //pick the scores for the player
 $playerbscores = $this->objDbscores->getscoresForPlayer($p['playerId'],$fixtureid);
  
   $class = ($class == 'odd') ? 'even':'odd';
  
  $name = $this->objDbplayer->getPlayerNameById($p['playerId']);  
  $list_B->startRow();
  $list_B->addCell($name,"", NULL, NULL, $class);
  $list_B->addCell($playerbscores ,"", NULL, NULL, $class);
  $list_B->endRow();
 
  }

}

else{ $list_A->startRow();
  $list_B->addCell($this->objLanguage->languageText('mod_sportsadmin_nomatchplayers','sportsadmin'),"", NULL, NULL, $class);
  $list_A->endRow();}
  

//add the list to the main table
$table->startRow();
$table->addCell($list_A->show(),"50%");
$table->addCell($list_B->show(),"50%");
$table->endRow();

//create another table to put the totals of the two teams
$totaltable = new htmltable();
$totaltable->border='0';
$totaltable->width='50%';

$teamatotal = $this->objDbscores->getscoresForteam($teamaid,$fixtureid);
$teambtotal = $this->objDbscores->getscoresForteam($teambid,$fixtureid);

$totaltable->startRow();

$totaltable->addCell($this->objLanguage->languageText('word_total','system'),'20%');
$totaltable->addCell("<strong>".$teamatotal."&nbsp;".$evaluationMode."</strong>",'27%','','right');
$totaltable->addCell("<strong>".$teambtotal."&nbsp;".$evaluationMode."</strong>",'','','right');
$totaltable->endRow();

//backlink 
$backlink = & $this->getObject('link','htmlelements');
$backlink->href = "javascript:history.back()";
$backlink->link = $this->objLanguage->languageText('word_back','system');
$content .=$table->show();

$content .= $totaltable->show();

$content .=  "<br /><br />".$backlink->show();

echo $content;


?>