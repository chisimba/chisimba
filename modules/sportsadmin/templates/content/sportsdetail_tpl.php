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


$objConfirm =& $this->newObject('confirm','utilities');
 $this->loadClass('link','htmlelements');
 $this->objDbplayer = & $this->getObject('dbplayer');
 $this->objDbsport = & $this->getObject('dbsports');
 $this->objDbtournament = & $this->getObject('dbtournament');
 $this->objDbplayer = & $this->getObject('dbplayer');
//variable to hold the content of the page
 $pagecontent = '';
$sportid = $this->getParam('sportid',NULL);
//instance of the language class
$this->objLanguage = & $this->getObject('language','language');

$this->loadClass('textinput','htmlelements');
$country = & $this->getObject('countries','utilities');

//table
$this->loadClass('htmltable','htmlelements');

//dropdown
$this->loadClass('dropdown','htmlelements');

//htmheading
$this->loadClass('htmlheading','htmlelements');
$this->loadClass('htmltable','htmlelements');
$contentTable = new htmltable();

//instance of the user class
$this->objUser =& $this->getObject('user','security');

$AddIcon =& $this->getObject('geticon','htmlelements');

//instance of the link class
$link =& $this->getObject('link','htmlelements');

//instance of dbtournaments
$this->dbTournaments = & $this->getObject('dbtournament','sportsadmin');

//instance of the tbl_fixtures
$this->dbFixtures =& $this->getObject('dbfixtures');

//instance of dbTeam
$this->objDbteam = & $this->getObject('dbteam');

//heading instance
$heading =& $this->getObject('htmlheading','htmlelements');

//instance of the sports class
$this->objDbsports =& $this->getObject('dbsports');
//form 
$this->loadClass('form','htmlelements');
$this->loadClass('button','htmlelements');
$searchoption = $this->getObject('radio','htmlelements');

//create the search form
$searchuri = $this->uri(array('action'=>'search','sportid'=>$sportid));
$searchform = new form('searchform',$searchuri);

//submit button
$searchsubmit = new button('searchsubmit',$this->objLanguage->languageText('word_submit','system'));
$searchsubmit->setToSubmit();

$searchoption->name = "searchoption";

$searchoption->addOption('name',$this->objLanguage->languageText('word_name','system'));
$searchoption->addOption('role',$this->objLanguage->languageText('mod_sportsadmin_position','sportsadmin'));
$searchoption->addOption('sport',$this->objLanguage->languageText('mod_sportsadmin_sport','sportsadmin'));
$searchoption->addOption('team',$this->objLanguage->languageText('mod_sportsadmin_team','sportsadmin'));
$searchoption->addOption('tournament',$this->objLanguage->languageText('mod_sportsadmin_tournament','sportsadmin'));
$searchoption->selected ="name";


$searchtable = new htmltable();
$searchtable->width="100%";

$searchfield = new textinput('searchfield');

$searchtable->startRow();
$searchtable->addCell($this->objLanguage->languageText('mod_sportsadmin_searchplayer','sportsadmin'));
$searchtable->addCell($searchfield->show());
$searchtable->addCell($searchoption->show());
$searchtable->addCell($searchsubmit->show());
$searchtable->endRow();

$searchform->addToForm($searchtable->show());

$pagecontent.= $searchform->show()."<br /><br />";

$item = $this->getParam('item',NULL);
		if($item==''){		
		
		//Link to add tournament to  sport
		$tornuri = $this->uri(array('module'=>'sportsadmin','action'=>'addtournament','sportid'=>$sportactivity['id'],'item'=>'tournament','sportid'=>$sportid));
        $tournamentadduri = new link($tornuri);		
		$tournamentadduri->link = $this->objLanguage->languageText('mod_sportsadmin_addtournament','sportsadmin');
	   
	    //link to add teams to a sport
	   $teamuri = $this->uri(array('module'=>'sportsadmin','action'=>'addteam','sportid'=>$sportactivity['id'],'item'=>'team','sportid'=>$sportid));
       $teamadduri = new link( $teamuri );		
	   $teamadduri->link = $this->objLanguage->languageText('mod_sportsadmin_addteam','sportsadmin');
					
		 //link to add players to a sport
	   $playersuri = $this->uri(array('module'=>'sportsadmin','action'=>'addplayer','sportid'=>$sportactivity['id'],'item'=>'player','sportid'=>$sportid));
       $playeradduri = new link( $playersuri);		
	   $playeradduri->link = $this->objLanguage->languageText('mod_sportsadmin_addplayer','sportsadmin');		
	
		$heading->str = $this->objDbsports->getSportsById($this->getParam('sportid',NULL))."&nbsp;&nbsp;".$this->objLanguage->languageText('mod_sports_mainpage','sports');
		$heading->align ='center';
				
		//Pick the description if any for display at the interface
		// instructions for usning the module
		$description = $this->objDbsport->getSportsDescriptionById($sportid);
		$instructions = $this->objLanguage->languageText('mod_sportsadmin_instructions','sportsadmin');
        $pagecontent .= '<strong>'.$instructions.'</strong>';
		$pagecontent .='<ul><li />'
		.$tournamentadduri->show().
		'<li />' . $teamadduri->show().
		'<li />' . $playeradduri->show().		
		'</ul>';
		
		
		if(!empty($description)){			
		$pagecontent .= '<br /><br />';
		$pagecontent .= $description.'<br /><br />';	
			
		}
		
		//links to get to view details of the sport
		$tvewlink = $this->uri(array('item'=>'tournament','action'=>'sportdetails','sportid'=>$sportid));
		$tournamentsview = new link($tvewlink);
		$tournamentsview->link = $this->objLanguage->languageText('mod_sportsadmin_viewtournaments','sportsadmin');
		
		//links to get to view registered teams
		$teamvewlink = $this->uri(array('item'=>'teams','action'=>'sportdetails','sportid'=>$sportid));
		$teamsview = new link($teamvewlink);
		$teamsview->link = $this->objLanguage->languageText('mod_sportsadmin_viewteams','sportsadmin');
		
		//links to view players
		$playervewlink = $this->uri(array('item'=>'players','action'=>'sportdetails','sportid'=>$sportid));
		$playerview = new link($playervewlink);
		$playerview->link = $this->objLanguage->languageText('mod_sportsadmin_viewplayers','sportsadmin');
		
		//show the link if there are already some registered tournaments
		 $tournaments = $this->objDbtournament->getTournamentsById($sportid);
			if(!empty($tournaments)){
			$pagecontent .= $tournamentsview->show().'&nbsp;|';
			}
		//show the details for the teams if there are any
		$teamslist = $this->objDbteam->getAll($sportid);	
			
			if(!empty($teamslist)){
			$pagecontent .= $teamsview->show().'&nbsp;|';
			}
			
		//show the link for players registered
		$playerlist = $this->objDbplayer->geAll($sportid);
		
		if(!empty($playerlist)){
		
		$pagecontent .= $playerview->show().'&nbsp;|';
		
		}	
			
		
		}
		
		else if($item=='tournament') {
		
		
	    $addtoururi = $this->uri(array('module'=>'sportsadmin','sportid'=>$sportid,'addurl'=>'addurl','action'=>'addtournament','item'=>'tournament'));
        $AddIcon->getAddIcon($addtoururi);
		$heading->str = $this->objLanguage->languageText('mod_sports_tournamentsfor','sports').'&nbsp;'.$this->objDbsports->getSportsById($this->getParam('sportid',NULL)).'&nbsp;'.$AddIcon->getAddIcon($addtoururi);
		$heading->align ='center';
		
		}
        
		else if($item=='fixtures') {
		
		$addtoururi = $this->uri(array('module'=>'sportsadmin','sportid'=>$sportid,'addurl'=>'addurl','action'=>'addfixtures','item'=>'fixtures'));
        $AddIcon->getAddIcon($addtoururi);
		
		$heading->str = $this->objLanguage->languageText('mod_sportsadmin_fixturesfor','sportsadmin').'&nbsp;'.$this->objDbsports->getSportsById($this->getParam('sportid',NULL)).'&nbsp;'.$AddIcon->getAddIcon($addtoururi);
		$heading->align ='center';
		
		}
        
		else if($item=='teams') {
		$addtoururi = $this->uri(array('module'=>'sportsadmin','sportid'=>$sportid,'addurl'=>'addurl','action'=>'addteam','item'=>'teams'));
        $AddIcon->getAddIcon($addtoururi);
		$heading->str = $this->objLanguage->languageText('mod_sports_teamsfor','sports').'&nbsp;'.$this->objDbsports->getSportsById($this->getParam('sportid',NULL)).'&nbsp;'.$AddIcon->getAddIcon($addtoururi);
		$heading->align ='center';
		
		}
		
        else if($item=='players') {
		$addtoururi = $this->uri(array('module'=>'sportsadmin','sportid'=>$sportid,'addurl'=>'addurl','action'=>'addplayer','item'=>'players'));
        $AddIcon->getAddIcon($addtoururi);
		
		$heading->str = $this->objLanguage->languageText('mod_sports_playersfor','sports').'&nbsp;'.$this->objDbsports->getSportsById($this->getParam('sportid',NULL)).'&nbsp;'.$AddIcon->getAddIcon($addtoururi);
		$heading->align ='center';
		
		}	
		
$table = new htmltable();
$table->cellspacing = '2';

//getting the comments put on the sport
$sportid = $this->getParam('sportid',NULL);
$comment = $this->objDbsports->getSportsDescriptionById($sportid);


$tornuri = $this->uri(array('module'=>'sportsadmin','action'=>'sportdetails','sportid'=>$sportactivity['id'],'item'=>'tournament','sportid'=>$sportid));
$tournamentlink = new link($tornuri);

$fixuri = $this->uri(array('module'=>'sportsadmin','action'=>'sportdetails','sportid'=>$sportactivity['id'],'item'=>'fixtures','sportid'=>$sportid));
$fixturelink = new link($fixuri);

$teamuri = $this->uri(array('module'=>'sportsadmin','action'=>'sportdetails','sportid'=>$sportactivity['id'],'item'=>'teams','sportid'=>$sportid));
$teamlink = new link($teamuri);

$matchuri = $this->uri(array('module'=>'sportsadmin','action'=>'sportdetails','sportid'=>$sportactivity['id'],'item'=>'matches','sportid'=>$sportid));
$matchlink = new link($matchuri);

$platersuri = $this->uri(array('module'=>'sportsadmin','action'=>'sportdetails','sportid'=>$sportactivity['id'],'item'=>'players','sportid'=>$sportid ));
$playerlink = new link($platersuri);

$data = '';
if($this->getParam('item',NULL) == 'tournament'){
       	   
	  //Check in the database to see if there are registered tournaments for the sport
	  $checktournaments = $this->dbTournaments->getTournamentsById($this->getParam('sportid',NULL));
      if(empty($checktournaments)){
	  
	  //creating the add tournament link for admins only
	  $getAddIcon =& $this->getObject('geticon','htmlelements');

	  $adduri = $this->uri(array('module'=>'sportsadmin','sportid'=>$sportid,'addurl'=>'addurl','action'=>'addtournament','item'=>'tournament'));

	   $noresultheading = new htmlheading();
		
				
       $table->startRow();
       $table->addCell($this->objLanguage->languageText('mod_sportsadmin_notournaments','sportsadmin')."&nbsp;".$this->objDbsport->getSportsById($sportid));
			 $table->endRow();
			             	  
	    }//if no tournaments
		
		
	 else {
					  
		   $table->startRow();
		   $table->addHeaderCell($this->objLanguage->languageText('word_name','system'));
		   $table->addHeaderCell($this->objLanguage->languageText('mod_sportsadmin_sponsorname','sportsadmin'));
		   $table->addHeaderCell($this->objLanguage->languageText('mod_sportsadmin_startdate','sportsadmin'));
		   $table->addHeaderCell($this->objLanguage->languageText('mod_sportsadmin_enddate','sportsadmin'));
		   $table->addHeaderCell($this->objLanguage->languageText('word_action','system'),'');
		   $table->endRow();
		   	 
		   $class = 'even';
		  
	       foreach ($checktournaments as $data){
		    
		   $class = ($class == 'odd') ? 'even':'odd';
		   
		   $tournaments = $tournament[0]['name'];
		  
		    $deletelink =& $this->getObject('geticon','htmlelements');
		    $deletelink->setIcon('delete');
		    $deletelink->alt = $objLanguage->languageText('mod_sports_delete','sports');
			
		    //modify link 
		    // Show the edit link
			$iconEdit = $this->getObject('geticon','htmlelements');
			$iconEdit->setIcon('edit');
			$iconEdit->alt = $objLanguage->languageText('mod_sportsadmin_edit_tourn','sportsadmin');
			$iconEdit->align=false;
			$objLink =& $this->getObject("link","htmlelements");
			$objLink->link($this->uri(array('item'=>'tournament','action'=>'addtournament','useEdit'=>'1','tournamentid'=>$data["id"],'sportid'=>$sportid)));
			$objLink->link = $iconEdit->show();		
			
			   
		    $objConfirm =& $this->getObject("link","htmlelements");
            $objConfirm=&$this->newObject('confirm','utilities');
			
			$iconDelete = $this->getObject('geticon','htmlelements');
			//delete link
			$iconDelete->setIcon('delete');
			$iconDelete->alt = $objLanguage->languageText("mod_sportsadmin_deletetournamentinfo",'sportsadmin');
			$objConfirm->setConfirm(
            $deletelink->show(),
            $this->uri(array('module'=>'sportsadmin','tournamentid'=>$data["id"],'sportid'=>$sportid,'item'=>'tournament','action'=>'deletetournament','confirm'=>'yes')),
            $objLanguage->languageText('mod_sports_suredelete','sports'));
            
			//link to view deails of tournament
			$detailsuri = $this->uri(array('action'=>'tournamentdetails','item'=>'tournament','sportid'=>$sportid,'tournamentid'=>$data['id']));
			$tournamentdetails = new link($detailsuri);
			$tournamentdetails->link = $data['name'];
			
		//print_r($checktournaments);  
				   
		   $table->startRow();
		   $table->addCell($tournamentdetails->show(),'','','',$class);
		   $table->addCell($data['sponsorname'],'','','',$class);
		   $table->addCell($data['startdate'],'','','',$class);
		   $table->addCell($data['enddate'],'','','',$class);
		   $table->addCell($objConfirm->show()."&nbsp;".$objLink->show(),'18%','','',$class);
		   $table->endRow();
		    
		  $data = $table->show();
		   
		   }
		   	      
	    }	
	    

   }//tournament item 
else if($this->getParam('item',NULL) == 'fixtures'){
     $table = new htmltable();
	  $table->cellspacing = 2;
	
 
    //Checking if there are entries in the fixtures table
     $fixturesArray = $this->dbFixtures->getFixturesBySportId($sportid);
 
    $sportid = $this->getParam('sportid',NULL);
    //if there are no results returned
		 if(empty($fixturesArray)){
			 $addIcon =& $this->getObject('geticon','htmlelements');
			 $adduri = $this->uri(array('module'=>'sportsadmin','sportid'=>$sportid,'addurl'=>'addurl','action'=>'addtournament','item'=>'fixtures'));
			 $nofixtures =& $this->getObject('htmlheading','htmlelements');
			 $adduri = $this->uri(array('module'=>'sportsadmin','sportid'=>$sportid,'addurl'=>'addurl','action'=>'addfixtures','item'=>'fixtures'));
		
			 $nofixtures->str = $this->objLanguage->languageText('mod_sports_nofixtures','sports') ."&nbsp;". $this->objDbsports->getSportsById($sportid).'&nbsp;'.$addIcon->getAddIcon($adduri);
			 $nofixtures->align = 'center';
			 	 
		 }
	 //if there are some results returned
	 else {
	 	   $teamA = $this->objLanguage->languageText('mod_sports_teama','sports');
           $teamB = $this->objLanguage->languageText('mod_sports_teamb','sports');
	       
		   $class = 'even';
		   $table->startRow();
			$table->addHeaderCell($this->objLanguage->languageText('mod_sports_teams','sports'));
			$table->addHeaderCell($this->objLanguage->languageText('mod_sports_place','sports'));
			//$table->addHeaderCell($this->objLanguage->languageText('mod_sportsadmin_tournamentword','sportsadmin'));
			$table->addHeaderCell($this->objLanguage->languageText('word_date','system'));
			$table->addHeaderCell($this->objLanguage->languageText('word_time','system'));
			$table->addHeaderCell($this->objLanguage->languageText('word_action','system'),'','','center');
			$table->endRow();
			
					   
		   foreach($fixturesArray as $fixturedata){
		   $class = ($class == 'odd')?'even':'odd';
		   
		   // Show the edit link
			$iconEdit = $this->getObject('geticon','htmlelements');
			$iconEdit->setIcon('edit');
			$iconEdit->alt = $objLanguage->languageText('mod_sportsadmin_edit_tourn','sportsadmin');
			$iconEdit->align=false;
			$objLink =& $this->getObject("link","htmlelements");
			$objLink->link($this->uri(array('module'=>'sportsadmin','item'=>'fixtures','sportid'=>$sportid,'action'=>'editfixture','fixtureid'=>$fixturedata["id"],'tournamentid'=>$fixturedata['tournamentId'])));
			$objLink->link = $iconEdit->show();
			//$linkEdit = $objLink->show();
			
			$deletelink =& $this->getObject('geticon','htmlelements');
		    $deletelink->setIcon('delete');
		    $deletelink->alt = $objLanguage->languageText('mod_sportsadmin_delete_tourn','sportsadmin');
		    $objConfirm =& $this->getObject("link","htmlelements");
            $objConfirm=&$this->newObject('confirm','utilities');
            $objConfirm->setConfirm(
            $deletelink->show(),
            $this->uri(array('module'=>'sportsadmin','fixtureId'=>$fixturedata["id"],'sportid'=>$sportid,'item'=>'fixtures','action'=>'deletefixture','confirm'=>'yes')),
            $objLanguage->languageText('mod_sports_suredelete','sports'));
             		   
		   $deletelink =& $this->getObject('geticon','htmlelements');
		   $deletelink->setIcon('delete');
		   $deletelink->alt = $objLanguage->languageText('mod_sportsadmin_delete_tourn','sportsadmin');
			//get the name of the team using the id
			$teamaId = $fixturedata['team_a'];
			$teambId =  $fixturedata['team_b'];
			$team_A = $this->objDbteam->getTeamNameById($teamaId);
		    $team_B = $this->objDbteam->getTeamNameById($teambId);
			//$tourney = $this->objDbtournament->getTournamentNameById($sportid);

		   $table->startRow();
		   $table->addCell( $team_A.'&nbsp;<strong>Vs</strong>&nbsp;'.$team_B,'','','',$class);		   
		   $table->addCell($fixturedata['place'],'','','',$class);
		   //$table->addCell($tourney,'','','',$class);
		   $table->addCell($fixturedata['matchdate'],'','','',$class);
		   $table->addCell($fixturedata['starttime'],'','','',$class);
		   $table->addCell($objLink->show().'&nbsp;'.$objConfirm->show(),'','','',$class);
           

		   $table->endRow();
		   $data =  $table->show();
		   
		   }
		  
	  }
   
  }//closing if for item fixtures
 
 else if($this->getParam('item',NULL) == 'teams'){
 //check if there are teams registered
$teamlist =  $this->objDbteam->getAll($sportid);
    if(empty($teamlist)){
	//$data = $this->objLanguage->languageText('mod_sportsadmin_noteams','sportsadmin');
	$table->startRow();
	$table->addCell($this->objLanguage->languageText('mod_sportsadmin_noteams','sportsadmin')."&nbsp;".$this->objDbsport->getSportsById($sportid));
	$table->endRow();

    }
	else {
	 
	$table->startRow();
	$table->addHeaderCell($this->objLanguage->languageText('mod_sportsadmin_teamname','sportsadmin'));
	$table->addHeaderCell($this->objLanguage->languageText('mod_sportsadmin_homeground','sportsadmin'));
	$table->addHeaderCell($this->objLanguage->languageText('word_action','system'));
    $table->endRow();
	$modifylink =& $this->getObject('link','htmlelements');
	$link = $this->getObject('link','htmlelements');	
	   
		$class = 'odd';
		foreach($teamlist as $t){
		//modify link 
			$iconEdit = $this->getObject('geticon','htmlelements');
			$iconEdit->setIcon('edit');
			$iconEdit->alt = $objLanguage->languageText('mod_sportsadmin_editteam','sportsadmin');
			$iconEdit->align=false;
						
			$objLink = & $this->getObject('link','htmlelements');
			$objLink->href = $this->uri(array('module'=>'sportsadmin','item'=>'teams','action'=>'addteam','useEdit'=>'1','teamid'=>$t['id'],'sportid'=>$sportid));
			$objLink->link = $iconEdit->show();
		    $linkEdit = $objLink->show(); 
		    
		
		$link->href= $this->uri(array('module'=>'sportsadmin','action'=>'teamdetails','teamid'=>$t['id'],'sportid'=>$sportid));
        $deletelink =& $this->getObject('geticon','htmlelements');
		$deletelink->setIcon('delete');
		$deletelink->alt = $objLanguage->languageText('mod_sportsadmin_deleteteam','sportsadmin');
		$objConfirm->setConfirm(
            $deletelink->show(),
            $this->uri(array('module'=>'sportsadmin','teamid'=>$t['id'],'sportid'=>$sportid,'item'=>'teams','action'=>'deleteteam','confirm'=>'yes')),$objLanguage->languageText('mod_sportsadmin_suredeleteteam','sportsadmin'));
			
		   
		$link->link = $t['name'];
		$class = ($class=='odd')?'even':'odd';
		$table->startRow();
		$table->addCell($link->show(),'','','',$class);
		$table->addCell($t['homeground'],'','','',$class);
	    $table->addCell($objConfirm->show()."&nbsp;". $linkEdit,'','','',$class);
		$table->endRow();
		
		}
		$data .=  $table->show();
	
	     
	}
      
 

  }//closing item team
  
 else if($this->getParam('item',NULL) == 'matches'){
//building the block for match information using information entered in tbl_sport and tbl_sixtures

$standingslink = $this->uri(array('action'=>'tablestandings','item'=>'matches','sportid'=>$sportid));
$tablestandings = new link($standingslink);
$standingsview = $this->objLanguage->languageText('mod_sports_viewstandings','sportsadmin');
$tablestandings->link = $standingsview;
$submiturl = $this->uri(array('action'=>'pickscoredetails','item'=>'matches','sportid'=>$sportid));
$form = new form('matchdata',$submiturl);
$table = new htmltable();

//checking for teams to put in dropdown box
$teams = $this->dbFixtures->getFixturesBySportId($sportid);

$table->startRow();
$teamslist = new dropdown('fixtureid');
$teamslist->addOption('select',$this->objLanguage->languageText('mod_sports_selectfixture','sportsadmin'));
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_sports_selectfixture','sportsadmin').'</strong>');
if(!empty($teams)){
//selecting the fixture from the table

foreach($teams as $teamoption){
$teamaId = $teamoption['team_a'];
$teambId = $teamoption['team_b'];
$team_A = $this->objDbteam->getTeamNameById($teamaId);
$team_B = $this->objDbteam->getTeamNameById($teambId);

$teamslist->addOption($teamoption['id'],$team_A.'&nbsp;VS &nbsp;'.$team_B);

}
}

$teamslist->extra = "onChange=document.matchdata.submit()";

$table->startRow();
$table->addCell($teamslist->show());
$table->addCell($details);
$table->endRow();

$table->startRow();
$table->addCell('');
$table->endRow();

$fixtureid = $this->getParam('fixtureid',NULL);
$fixturedata = $this->objDbfixtures->getFixtureforgame($sportid,$fixtureid );


$detailsform = new form('details');

if(!empty($fixturedata)){

//create another form
$detailsformuri = $this->uri(array('action'=>'savescores','item'=>'matches','sportid'=>$this->getParam('sportid',NULL)));
$detailsform->setAction($detailsformuri);
$detailstable = new htmltable();
foreach($fixturedata as $f){

$team_a = new textinput('teamascores');
$team_b = new textinput('teambscores');
$tournamentId = new textinput('tournamentId',$f['tournamentId'],'hidden');
$teamAId = new textinput('team_a',$f['team_a'],'hidden');
$teamBId = new textinput('team_b',$f['team_b'],'hidden');

$detailstable->startRow();
$detailstable->addCell($tournamentId->show());
$detailstable->addCell($teamBId->show());
$detailstable->addCell($teamAId->show());
$detailstable->endRow();

$detailstable->startRow();
$detailstable->addCell('<strong>'.$team_A.'&nbsp;'.$this->objLanguage->languageText('mod_sportsadmin_scores','sportsadmin').'</strong>');
$detailstable->addCell($team_a->show());
$detailstable->endRow();

$detailstable->startRow();
$detailstable->addCell('');
$detailstable->endRow();

$detailstable->startRow();
$detailstable->addCell('<strong>'.$team_B.'&nbsp;'.$this->objLanguage->languageText('mod_sportsadmin_scores','sportsadmin').'</strong>');
$detailstable->addCell($team_b->show());
$detailstable->endRow();


// create a button to submit
$submitbutton = & $this->getObject('button','htmlelements');
$submitbutton->name = 'submit';
$submitbutton->setValue($this->objLanguage->languageText('word_submit','system'));
$submitbutton->setTosubmit();

$detailstable->startRow();
$detailstable->addCell($submitbutton->show());
$detailstable->endRow();

$detailsform->addToForm($detailstable->show());
}
}


$form->addToForm($Ttable->show());

$data = $tablestandings->show();
$data .= $form->show();
$data .= $detailsform->show();

  }
    
 else if($this->getParam('item',NULL) == 'players'){
 
  $teamid = $this->getParam('teamid',NULL);
 
  //pick all players from all the teams if team id is not set 
  //else pick only those values for the selected team
  if(!empty($teamid)){
   $players = $this->objDbplayer->getTeamMembers($teamid,$sportid);
  }
  
  else { 
  
  $players = $this->objDbplayer->getPlayersForSport($sportid);
  
  }
  
  
  if(!empty($players)){
    
	 		$playerlist =  $this->objDbplayer->getPlayersForSport($this->getParam('sportid',NULL));
	
    $table->startRow();
	$table->addHeaderCell($this->objLanguage->languageText('mod_sportsadmin_playername','sportsadmin'));
	$table->addHeaderCell($this->objLanguage->languageText('mod_sportsadmin_dateofbirth','sportsadmin'));
	$table->addHeaderCell($this->objLanguage->languageText('mod_sportsadmin_team','sportsadmin'));
	$table->addHeaderCell($this->objLanguage->languageText('word_country','system'));
	$table->addHeaderCell($this->objLanguage->languageText('mod_sportsadmin_position','sportsadmin'));
	$table->addHeaderCell($this->objLanguage->languageText('word_action','system'));
	$table->endRow();
	
	
   $linkuri = $this->objLanguage->languageText('mod_sportsadmin_viewplayeralb','sportsadmin');
    $albumlink = "<a href='index.php?module=sportsadmin&amp;action=viewplayeralbum&amp;sportid=".$sportid."' >".$linkuri."</a>";
		
  $class ='odd';
  foreach($players as $p){
  
  
  // Show the edit link
    $iconEdit = $this->getObject('geticon','htmlelements');
    $iconEdit->setIcon('edit');
    $iconEdit->alt = $objLanguage->languageText('mod_sportsadmin_editplayer','sportsadmin');
    $iconEdit->align=false;
    $objLink =& $this->getObject("link","htmlelements");
	
    $objLink->link($this->uri(array('module'=>'sportsadmin','sportid'=>$sportid,'action'=>'addplayer','useEdit'=>'1','playerid'=>$p["id"])));
    $objLink->link = $iconEdit->show();
    $linkEdit = $objLink->show();
  
  // Show the delete link for the player data
    $iconDelete = $this->getObject('geticon','htmlelements');
    $iconDelete->setIcon('delete');
    $iconDelete->alt = $objLanguage->languageText('mod_sportsadmin_deleteplayer','sportsadmin');
    $iconDelete->align=false;
	
	$objConfirm =& $this->getObject("link","htmlelements");
    $objConfirm=&$this->newObject('confirm','utilities');
    $objConfirm->setConfirm(
    $iconDelete->show(),
    $this->uri(array('module'=>'sportsadmin','action'=>'deleteplayer','confirm'=>'yes','playerid'=>$p["id"],'item'=>'players','sportid'=>$sportid)),
    $objLanguage->languageText('mod_sports_suredelete','sportsadmin'));
	
  
  $class = ($class=='odd')?'even':'odd';
  $teamId = $p['team'];
  $team = $this->objDbteam->getTeamNameById($teamId);
  //get the names of the countries
  $countrycode = $p['country']; 
 $countyname = $country->getCountryName($countrycode);
  $playerdetails = $this->getObject('link','htmlelements');
  $playerdetails->href= $this->uri(array('module'=>'sportsadmin','action'=>'playerdetails','playerid'=>$p['id'],'sportid'=>$sportid));
  $playerdetails->link =$p['name'];  
   $table->startRow(); 
   $table->addCell($playerdetails->show(),'','','',$class);
   $table->addCell($p['dateofbirth'],'','','',$class);
   $table->addCell($team,'','','',$class);
   $table->addCell($countyname,'','','',$class);
   $table->addCell($p['position'],'','','',$class);
   $table->addCell($linkEdit."&nbsp;".$objConfirm ->show(),'','','',$class);
   $table->endRow();  
   
	
	}//closing foreach
   $table->startRow();
   $table->addCell("");
   $table->endRow();
	
  $table->startRow();
   $table->addCell($albumlink);
   $table->endRow();

  }//closing if
  
  //if there are no players in a given sport
  else {
  
  $table->startRow();
  $table->addCell($this->objLanguage->languageText('mod_sportsadmin_noplayersfor','sportsadmin')."&nbsp;".$this->objDbsport->getSportsById($sportid));
  $table->endRow();
  
  }
    
  }  
  
 

//adding contents to the table
$contentTable->startRow();
$contentTable->addCell($data);
$contentTable->endRow();

$pagecontent .= $table->show();
$pagecontent .= "";

echo $heading->show();
echo $pagecontent;

?>