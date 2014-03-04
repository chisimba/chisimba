<?php 

//security chech that must be put on ever page
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* 
* @copyright 2006 KEWL.NextGen
* @author Nsabagwa Mary
* #Author Kaddu Ismeal
* 
*/


 //pick the values from the table
$fixture_name = $this->objDbfixtures->getFixtureById($this->getParam('fixtureid',NULL));
		
//file to display the modification data for a fixture
$tournamentid = $this->getParam('tournamentid',NULL);
//echo $tournamentid."asa" ; exit;
$content = '';
//instances
$sportid = $this->getParam('sportid',NULL);
$this->objLanguage =& $this->getObject('language','language');
//$this->loadClass('form','htmlelements');
$this->loadClass('htmltable','htmlelements');
$this->loadClass('label','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('link','htmlelements');
$heading = & $this->getObject('htmlheading','htmlelements');
$objIcon = & $this->getObject('geticon','htmlelements');

//Fixtures can only be registered on a registered tournament
		//check if there are tournaments
		$sportid = $this->getParam('sportid',NULL);
		 $tournaments = $this->objDbtournament->getTournamentsById($sportid);
		 $objHeading =& $this->getObject('htmlheading','htmlelements');
		 
			 if(empty($tournaments)){
			$objHeading->str = $this->objLanguage->languageText('mod_sports_notournaments','sports'); 
			$addtournamentlink = $this->uri(array('action'=>'addtournament','sportid'=>$sportid,'redirect'=>'redirect'));
			$link = new link($addtournamentlink);
			$link->link = $this->objLanguage->languageText('mod_sportsadmin_addtournament','sportsadmin');
			$content .= $link->show();
			$content .= $objHeading->show();
			
			 }
			 else {
			 //pick the name of the tournament using the id
			 $tournamentid = $this->getParam('tournamentid',NULL);
			$to = $this->getObject('dbtournament'); 
			$tournament = $to->getTournamentNameById($tournamentid );
				 	
			
$defaultstartDate =date('Y-m-d H:m');

$this->objDbsports = & $this->getObject('dbsports');

$submituri= $this->uri(array('module'=>'sportsadmin','action'=>'modifyfixture','item'=>'fixtures','tournamentid'=>$tournamentid,'sportid'=>$sportid));

//$form = new form('addfixtures');
$form =& $this->getObject('form','htmlelements');
$form->name = 'addfixture';
$form->setAction($submituri);


$heading->str = $this->objLanguage->languageText('mod_sport_enterfixture','sports').'&nbsp;'.$this->objDbsports->getSportsById($sportid);
$heading->align = 'center';
$onclick =0;
$objIcon->seticon('select_date');
$dateurl = $this->uri(array('field'=>'document.addfixture.startDate','fieldvalue'=>date('Y-m-d H:m')), 'popupcalendar');
$onclick = "javascript:window.open('" .$dateurl."', 'popupcal', 'width=320, height=410, scrollbars=1, resize=yes')";
$startDateLink = new link('#');
$startDateLink->extra = "onclick=\"$onclick\"";
$startDateLink->link = $objIcon->show().' '.$this->objLanguage->languageText('mod_sportsadmin_selectDate','sportsadmin');



foreach($fixture_name as $fdata){

$team_a=$fdata['team_A'];
$team_b=$fdata['team_B'];
//get team names 
$team_A_name = $this->objDbteam->getTeamNameById($team_a);
$team_B_name = $this->objDbteam->getTeamNameById($team_b);

$tournament_name = "<strong>".$this->objLanguage->languageText('mod_sportsadmin_tournament','sportsadmin')."</strong> &nbsp ".$tournament;
//textinputs
$fixtureid = new textinput('fixtureid',$fdata['id'],'hidden');

$placeinput = new textinput('place',$fdata['place'],'',40);
$dateinput = new textinput('startDate',$fdata['matchDate'],'');
$dateinput->extra = 'readonly';
$timeinput = new textinput('time',$fdata['startTime'],'',40);
}//end of foreach

//submit button
$submitbuton = new button('submit',$this->objLanguage->languageText('word_submit'));
$submitbuton->setToSubmit();

//adding the items to the table
$table = new htmltable();
$table->align = 'right';
$table->cellspacing = 2;
$table->width = "50%";
//$table->border =1;


$table->startRow();
$table->addCell($fixtureid->show());
$table->endRow();


$table->startRow();
$table->addCell("<strong>".$this->objLanguage->languageText('mod_sportsadmin_teams','sportsadmin')."</strong> &nbsp ",'','','left');
$table->addCell($team_A_name."&nbsp; VS &nbsp;".$team_B_name);
$table->endRow();

$table->startRow();
$table->addCell("<strong>".$this->objLanguage->languageText('mod_sportsadmin_tournament','sportsadmin')."</strong> &nbsp ",'','','left');
$table->addCell($tournament);

$table->endRow();



$table->startRow();

$table->addCell('<strong>'.$this->objLanguage->languageText('mod_sports_place','sports').'</strong>','','','left');
$table->addCell($placeinput->show());
$table->endRow();

$table->startRow();
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_sports_matchdate','sports').'</strong>&nbsp;','','','left');
$table->addCell($dateinput->show().$startDateLink->show());
$table->endRow();

/*$table->startRow();
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_sports_starttime','sports').'</strong>','20%','','left');
$table->addCell($timeinput->show());
$table->endRow();*/

$table->startRow();
$table->addCell('&nbsp;');
$table->addCell($submitbuton->show(),'','','left');
$table->endRow();

//adding the table to the form
$form->addToForm($table->show());

$content .= $heading->show();
//echo $teams;
//echo $tournament_name;

$content .= $form->show();
}

echo $content;

?>