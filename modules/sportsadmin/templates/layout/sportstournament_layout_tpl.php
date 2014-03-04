<?php 
///db tournament
$this->objTournament = $this->getObject('dbtournament');

//dbfixtures
$this->objfixtures = $this->getObject('dbfixtures');

//dbteam
$this->objteam = $this->getObject('dbteam');

//dbscores 
$this->objscores = $this->getObject('dbscores');

//instance of the language class
$this->objLanguage =& $this->getObject('language','language');

//instance of dbsports
$this->objDbsports =& $this->getObject('dbsports');

//instnace of user class to check for permissions
$this->objUser =& $this->getObject('user','security');

$tournamentid = $this->getParam('tournamentid',NULL);
$sportid = $this->getParam('sportid',NULL);

//creating an instance of the css class to determine the number of rows
$cssLayout = & $this->newObject('csslayout','htmlelements');
$cssLayout->setNumColumns(3);
$table = $this->newObject('htmltable','htmlelements');

$linkuri = $this->objLanguage->languageText('mod_sports','sports');
$link = "<a href='index.php?module=sports'>".$linkuri."</a>";


//building the menu for the sports
$heading =& $this->getObject('htmlheading','htmlelements');
$heading->str = $this->objLanguage->languageText('mod_sports','sports');
$heading->align = 'center';

//switch menu
$menu1 = $this->getObject('switchmenu','blocks');

//adding elements of sports that are registered
$sportactivities = $this->objDbsports->listAllSports();

//adding the heading to the table
$table->startRow();
$table->addCell($heading->show());
$table->endRow();

$table->startRow();
if($this->objUser->isAdmin()){
$table->addCell($link.'<br/><br/>');
}
else {$table->addCell('<br/><br/>');
}
$table->endRow();

$table->startRow();
$table->addCell($menu);
$table->endRow();


//linking the sports activities
$this->loadClass('link','htmlelements');

if(!empty($sportactivities)){
		foreach($sportactivities as $sportactivity){
			$linkuri = $this->uri(array('module'=>'sports','sportid'=>$sportactivity['id'],'action'=>'sportdetails'));
			$sportlink = new link($linkuri);
			$sportlink->link = $sportactivity['name'];
			
			//link to view the tournaments of soccer
			$tournamentlink = new link($this->uri(array('item'=>'tournament','action'=>'sportdetails','sportid'=>$sportactivity['id'])));
			$tournamentlink->link = $this->objLanguage->languageText('mod_sportsadmin_tournament','sportsadmin');
			
			//link for displaying the fixtures
			$fixturelink = new link($this->uri(array('item'=>'tournament','action'=>'sportdetails','sportid'=>$sportactivity['id'])));
$fixturelink->link = $this->objLanguage->languageText('mod_sportsadmin_fixtures','sportsadmin');

//link for the teams
$teamlink = new link($this->uri(array('item'=>'teams','action'=>'sportdetails','sportid'=>$sportactivity['id'])));
$teamlink->link = $this->objLanguage->languageText('mod_sportsadmin_team','sportsadmin');
			
//create a link to view or add players
$playerlink = new link($this->uri(array('item'=>'players','action'=>'sportdetails','sportid'=>$sportactivity['id'])));	
$playerlink->link = $this->objLanguage->languageText('mod_sportsadmin_players','sportsadmin');
			
//should display the fixtures only if a tournament exists
$fixtures = $this->objDbfixtures->getFixturesBySportId($sportactivity['id']);
if(!empty($fixtures)){
   $fixture_link = $fixturelink->show();
}

else {$fixture_link = "";}
			
					  			
$menu1->addBlock($sportlink->show(),$tournamentlink->show()."<br/> &nbsp; &nbsp;".$fixture_link ."<br/>".$teamlink->show()."<br/>".$playerlink->show());
			
	 $sportid = $this->getParam('sportid',NULL);
			
		}
}//closing the if statement

$menu = $menu1->show();


$table->startRow();
$table->addCell($menu);
$table->endRow();

$cssLayout->setLeftColumnContent($table->show());

//set the contents of the middle column
$cssLayout->setMiddleColumnContent($this->getContent());
$rightsidecontent = "";

$rightsidecontent = $this->objTournament->getTournamentNameById($tournamentid)."&nbsp;".$this->objLanguage->languageText('mod_sportsadmin_standings','sportsadmin');

//get all the fixtures and indicate the scores
$scoretable = new htmltable();
$fixturelist = $this->objfixtures->getFixturesForTournament($tournamentid,$sportid);
if(!empty($fixturelist )){
  foreach ($fixturelist  as $f){
   $at = $f['team_a'];
   $bt = $f['team_b'];
   
 
  $teamascores = $this->objscores->getscoresForteam($at,$f['id']);   
  $teambscores = $this->objscores->getscoresForteam($bt,$f['id']);
    
    
$teamblink = $this->getObject('link','htmlelements'); 
$teamblink->href = $this->uri(array('action'=>'scoredetails','fixtureid'=>$f['id'],'tournamentid'=>$tournamentid,'sportid'=>$sportid,'teamid'=>$f['team_b']));
$teamblink->link = $teambscores;   
   
$f_id = $f['id']; 
$t_id =  $f['team_a']; 

 
$scoretable->startRow();
$scoretable->addCell($this->objteam->getTeamNameById($f['team_a'])); 


$alinkscores = "<a href='index.php?module=sportsadmin&amp;action=scoredetails&amp;fixtureid=$f_id&amp;tournamentid=$tournamentid&amp;sportid=$sportid&amp;teamid=$t_id'>".$teamascores."</a>";
//<a href='index.php?module=sports' 
$scoretable->addCell($alinkscores );
$scoretable->addCell($teamblink->show() );
$scoretable->addCell($this->objteam->getTeamNameById($f['team_b']));
$scoretable->endRow();
  
  }

}

$rightsidecontent .=  $scoretable->show();

//table for standings
$standings = new htmltable();
$standings->cellspacing = 1;
$standings->startRow();
$standings->addHeaderCell( $this->objLanguage->languageText('mod_sportsadmin_team','sportsadmin'));
$standings->addHeaderCell("G");
$standings->addHeaderCell("W");
$standings->addHeaderCell("L");
$standings->addHeaderCell("D");
$standings->endRow();

//get all the participating teams
$teams = $this->objfixtures->getTournamentteams($tournamentid);


//get the members of the array one by one and pick out only unique members
$uniquemembers = array();

if(!empty($teams)){
foreach($teams as $ts){
//if the member is not yet in the array, then add it
if(!in_array($ts['team_a'],$uniquemembers)){
$uniquemembers[] = $ts['team_a'];

}
//check if the second member has been added to the list in the array and if not add it
	if(!in_array($ts['team_b'],$uniquemembers)){
	$uniquemembers[] = $ts['team_b'];
	
	}
}

}
//go through the arraya if there are some teams 
if(!empty($teams)){
while( $element = each($uniquemembers))
{
 
 $standings->startRow();
 $standings->addCell($this->objteam->getTeamNameById($element[ "value" ]));
 $standings->addCell($this->objscores->getGoalForTeam($tournamentid,$element[ "value" ]));
 $standings->addCell($this->objscores->getWinsForTeam($element[ "value" ],$tournamentid));
 $standings->addCell($this->objscores->getLossesForTeam($element[ "value" ],$tournamentid));
 $standings->addCell($this->objscores->getDrawsForTeam($element[ "value" ],$tournamentid));
 $standings->endRow();
 
}

}

else  {
$standings->startRow();
$standings->addCell($this->objLanguage->languageText('mod_sportsadmin_noteamintournament','sportsadmin'));
$standings->endRow();

} 



$key = "<br/><strong>".$this->objLanguage->languageText('mod_sportsadmin_key','sportsadmin')."</strong>";

  

$key .= "<br/><strong>G</strong>&nbsp;--&nbsp;".$this->objLanguage->languageText('mod_sportsadmin_goals','sportsadmin');
$key .= "<br/><strong>W</strong>&nbsp;--&nbsp;".$this->objLanguage->languageText('mod_sportsadmin_wins','sportsadmin');
$key .= "<br/><strong>L</strong>&nbsp;--&nbsp;".$this->objLanguage->languageText('mod_sportsadmin_losses','sportsadmin');
$key .= "<br/><strong>D</strong>&nbsp;--&nbsp;".$this->objLanguage->languageText('mod_sportsadmin_draws','sportsadmin');
  
$rightsidecontent .= $standings->show();
$rightsidecontent .= $key ;
$cssLayout->setRightColumnContent($rightsidecontent);

echo $cssLayout->show();


?>