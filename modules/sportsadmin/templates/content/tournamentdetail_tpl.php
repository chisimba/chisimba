<?php
/*
* File to give the details of tournament
*  Author : Nsabagwa Mary
*
*/

$objLayout = $this->getObject('csslayout','htmlelements');

$objLayout->setNumColumns(3);

$content = "";
$this->objDbfixture = & $this->getObject('dbfixtures');
$sportid = $this->getParam('sportid',NULL);
$tournamentid = $this->getParam('tournamentid',NULL);
$this->objLanguage =& $this->getObject('language','language');
$this->loadClass('htmlheading','htmlelements');
$this->objDbsport = & $this->getObject('dbsports');
$this->loadClass('htmltable','htmlelements');
$this->objDbtournament = & $this->getObject('dbtournament');
$this->loadClass('link','htmlelements');
$this->objTeam = $this->getObject('dbteam');
$objIcon = & $this->getObject('geticon','htmlelements');
$objConfirm =& $this->newObject('confirm','utilities');
$objLink =& $this->getObject("link","htmlelements");
$this->objDbscores =& $this->getObject('dbscores');
$this->objUser = & $this->getObject('user','security');

$fixtureuri = $this->uri(array('action'=>'addfixtures','item'=>'fixtures','sportid'=>$sportid,'tournamentid'=>$tournamentid));

$links = "";
$fixturelink =new link($fixtureuri);
$fixturelink->link = $this->objLanguage->languageText('mod_sportsadmin_addfixture','sportsadmin');

$playeradduri = $this->uri(array('module'=>'sportsadmin','action'=>'addplayertotournament','item'=>'tournament'));

$playersadd = new link($playeradduri);
$playersadd->link = $this->objLanguage->languageText('mod_sportsadmin_tournamentplayers','sportsadmin');

 $number = $this->objDbteam->getTeamNumber($sportid);

 if($number >2){
  $links .= $fixturelink->show();
  }
  
  $teamadduri = $this->uri(array('sportid'=>$sportid,'action'=>'addteam','tournamentdetails'=>'1'));
  $teamadd = new link($teamadduri);
  $teamadd->link = $this->objLanguage->languageText('mod_sportsadmin_addteam','sportsadmin');
  
$links .= "&nbsp;".$teamadd->show(); 
//pick the tournament
$tournament =  $this->objDbtournament->getTournamentdetails($sportid,$tournamentid);
$sportsname = $this->objDbsport->getSportsById($this->getParam('sportid'));

$sportname = new htmlheading();
$sportname->str = $this->objLanguage->languageText('mod_sportsadmin_sportname','sportsadmin').':&nbsp;'.$sportsname;
$sportname->align = 'left';


$tournamentname = new htmlheading();
$tournamentname->str = $this->objLanguage->languageText('mod_sportsadmin_tournamentname','sportsadmin').':&nbsp;'.$tournament;
$tournamentname->align = 'left';

//Collect all the fixtures in the tournament to display
$fixturelist = $this->objDbfixture->getFixturesForTournament($tournamentid,$sportid);

$table = new htmltable();
$table->width = '100%';
$table->cellspacing = '2';
$class = 'even';

if(!empty($fixturelist)){

  $table->startRow();
  $table->addHeaderCell($this->objLanguage->languageText('word_name','system'));
  $table->addHeaderCell($this->objLanguage->languageText('mod_sportsadmin_field','sportsadmin'));
  $table->addHeaderCell($this->objLanguage->languageText('word_date','system'));
  $table->addHeaderCell($this->objLanguage->languageText('word_time','system'));
	 if($this->objUser->isAdmin()){
  $table->addHeaderCell($this->objLanguage->languageText('word_action','system'));
	}
	$table->endRow();

   foreach($fixturelist as $fixtures){
	$class = ($class == 'odd') ? 'even':'odd';
	
	//get name of team by id
	$teamAname = $this->objTeam->getTeamNameById($fixtures['team_a']);
	$teamBname = $this->objTeam->getTeamNameById($fixtures['team_b']);
	
	$teamdetails = new link($this->uri(array('module'=>'sportsadmin','action'=>'showteamfixturedetails','fixtureid'=>$fixtures['id'],'tournamentid'=>$tournamentid,'sportid'=>$sportid)));
	$teamdetails->link = $teamAname."&nbsp;VS &nbsp;".$teamBname;
	
	// Show the edit link
		$iconEdit = $this->getObject('geticon','htmlelements');
		$iconEdit->setIcon('edit');
		$iconEdit->alt = $objLanguage->languageText('mod_sportsadmin_edit_tourn','sportsadmin');
		$iconEdit->align=false;
		
		$objLink->link($this->uri(array('module'=>'sportsadmin','item'=>'fixtures','action'=>'editfixture','fixtureid'=>$fixtures['id'],'sportid'=>$sportid,'tournamentid'=>$fixtures['tournamentId'])));
		$objLink->link = $iconEdit->show();
	
	// Show the delete link
    $iconDelete = $this->getObject('geticon','htmlelements');
    $iconDelete->setIcon('delete');
    $iconDelete->alt = $objLanguage->languageText("word_delete",'system');
    $iconDelete->align=false;

    $objConfirmdelete =& $this->getObject("link","htmlelements");
    $objConfirmdelete=&$this->newObject('confirm','utilities');
    $objConfirmdelete->setConfirm(
    $iconDelete->show(),
    $this->uri(array('module'=>'sportsadmin','action'=>'deletefixture','confirm'=>'yes','fixtureid'=>$fixtures["id"],'sportid'=>$sportid,'tournamentid'=>$tournamentid)),
    $objLanguage->languageText('mod_sportsadmin_surefixturedelete','sportsadmin'));
	
	$fixtureid = $fixtures['id'];
	//get the scores for the fixture if entered
	$teamavalue = $this->objDbscores->getFixtureScore($fixtureid);

$match_date = $fixtures['matchdate'];
$date_time = explode(" ",$match_date);

$time = $date_time['1'];

$start_time = substr($time,0,5);

	$table->startRow();
	$table->addCell($teamdetails->show(),'','','',$class);
	$table->addCell($fixtures['place'],'','','',$class);
	$table->addCell($date_time['0'],'','','',$class);
	$table->addCell($start_time,'','','',$class);
	if($this->objUser->isAdmin()){
	  $table->addCell($objLink->show()."&nbsp;".$objConfirmdelete->show(),'','','',$class);
	}
	$table->endRow();
  }

}
$content .=$sportname->show();
$content .=$tournamentname->show();
$content .= $table->show()."&nbsp;";

$content .= "<br/><br />".$links; 
echo $content;


?>