<?php 
//-----sports class extends controller---------

//security chech that must be put on ever page
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check
/**
*  Template for showing the details of theteam
* @copyright 2006 KEWL.NextGen
* @author Nsabagwa Mary
*
* 
*/
$content ='';

$addnews = & $this->getObject('geticon','htmlelements');


//new for the team

//$news = "<strong>".$this->objLanguage->languageText('mod_sportsadmin_sportsnews','sportsadmin')."</strong>&nbsp;".$addnews->getAddIcon($addnewsurl);



$this->loadClass('htmltable','htmlelements');
$t_id = $this->getParam('teamid',NULL);
$imagepath = $this->objConfig->getcontentRoot()."teamlogos/";
$this->objConfig = $this->getObject('config','config');
$this->loadClass('geticon','htmlelements');
$this->objDbsport = & $this->getObject('dbsports');
$objHeading = & $this->getObject('htmlheading','htmlelements');
$this->objDbfixtures = & $this->getObject('dbfixtures');
// get the datails of the team
$this->objDbteam = $this->getObject('dbteam');
$this->objDbplayer = & $this->getObject('dbplayer');
$this->objDbsportsnews = & $this->getObject('sportsnews');

$sportid = $this->getParam('sportid',NULL);


//get the name of the sport to which the team belongs
$objHeading->str = $this->objDbsport->getSportsById($sportid)."&nbsp;".$this->objLanguage->languageText('mod_sportsadmin_team','sportsadmin');
$objHeading->align = "center";


$table =new htmltable(); 
$table->width= '100%';
$table->align = 'center';


$outertable = $this->getObject('htmltable','htmlelements');

$teamdata = $this->objDbteam->getTeamDetails($t_id);

//go through the list and display
foreach($teamdata as $p){
$name = $p['name'];
$fileld = $p['homeground'];
//$coach= $p['coach'];
$logofile = $p['logofile'];
$sportId = $p['sportid'];
$motto = $p['motto'];
$teamid = $p['id'];
}

// create instance for the link class
$linkview = $this->getObject('link','htmlelements');
$linkview->href = $this->uri(array('module'=>'sportadmin','action'=>'sportdetails','sportid'=>$sportId,'teamid'=>$teamid,'item'=>'players'));
$linkview->link = $this->objLanguage->languageText('mod_sportadmin_viewteammembers','sportsadmin');


//get the name of the sports
$this->objDbSports = $this->getObject('dbsports');
$sportname = $this->objDbSports->getSportsById($sportId);

$content .= "<strong><h2>".$sportname."&nbsp;". $teamlabel."</h2></strong>";

$uuuu = $imagepath.$logofile;    
$path = str_replace('\\', '/',$uuuu);


$image = '<p><img src="'.$path.'" /></p>'; 

$table->startRow();
$table->addCell("<strong>".$this->objLanguage->languageText('word_name','system')."</strong>");
$table->addCell($name);
$table->addCell("");
$table->endRow();


$table->startRow();
$table->addCell("<strong>".$this->objLanguage->languageText('mod_sportsadmin_homeground','sportsadmin')."</strong>");
$table->addCell($fileld);
$table->addCell("");
$table->endRow();

$teamname = $this->objDbteam->getTeamNameById($teamid);

$table->startRow();
$table->addCell("<strong>".$this->objLanguage->languageText('mod_sportsadmin_motto','sportsadmin')."</strong>",'20%');
$table->addCell($motto);
$table->addCell("");
$table->endRow();

$addnewsurl = $this->uri(array('action'=>'addnews','teamid'=>$teamid,'sportid'=>$sportid));
  $table->startRow();
  $table->addCell("<strong>".$this->objLanguage->languageText('mod_sportsadmin_sportsnews','sportsadmin')."    </strong>&nbsp;".$addnews->getAddIcon($addnewsurl));
  $table->endRow();

$news_list = $this->objDbsportsnews->getLatestNewsForTeam($teamid,$sportid);

if(!empty($news_list)){


  foreach($news_list  as $n){
  $table->startRow();
   $table->addCell("");
  $table->addCell($n['news']); 
  $table->endRow();  
  
  }
}


/*$table->startRow();
$table->addCell("");
$table->endRow();*/


$table->startRow();
$table->addCell('');
$table->endRow();

$table->startRow();
$table->addCell('');
$table->endRow();

//draw a new table for the scheduled mathes
 $this->loadClass('htmltable','htmlelements');
 $matchtable = new htmltable();
$matchtable->width = "80%";
$matchtable->cellspacing = 3;

$fixtureheader = "<strong>".$this->objLanguage->languageText('mod_sportsadmin_fixtures','sportsadmin')."</strong>";

//get the fixtures that the team is participating 
$tournaments = $this->objDbfixtures->getAllFixturesForTournament($teamid);

if(!empty($tournaments)){
    $matchtable->startRow();
    $matchtable->addHeaderCell($this->objLanguage->languageText('word_date','system'));
    $matchtable->addHeaderCell($this->objLanguage->languageText('mod_sportsadmin_opponent','sportsadmin'));
    $matchtable->addHeaderCell($this->objLanguage->languageText('mod_sportsadmin_field','sportsadmin'));
    $matchtable->addHeaderCell($this->objLanguage->languageText('mod_sportsadmin_tournament','sportsadmin'));
   $matchtable->endRow();   

  $class = 'even';
   foreach($tournaments as $t){
	   if($t['team_A'] ==$teamid){
	   $opponent = $t['team_B'];	   
	   }
	   else { $opponent = $t['team_A'];  }
	   
   //get the name of the team from the id
    $team_name = $this->objDbteam->getTeamNameById($opponent); 
	
	//get the name of the tournament
    $tournamentname = $this->objDbtournament->getTournamentNameById($t['tournamentId']);
	
     $class = ($class == 'odd') ? 'even':'odd';
     $matchtable->startRow();
	 $matchtable->addCell($t['matchDate'],'','','',$class);
	 $matchtable->addCell( $team_name,'','','',$class);
	 $matchtable->addCell( $t['place'],'','','',$class);
	 $matchtable->addCell($tournamentname,'','','',$class);
	 $matchtable->endRow();   
   
   }
}else { 

		$matchtable->startRow();
		$matchtable->addCell($this->objLanguage->languageText('mod_sportsadmin_nofixturesforteam','sportsadmin')."&nbsp;".$teamname);
		$matchtable->endRow();

   }
   

$outertable->startRow();
$outertable ->addCell("");
$outertable->addCell("");
$outertable ->endRow();


$outertable->startRow();
$outertable ->addCell($image,'','','');
$outertable->addCell($table->show());
$outertable ->endRow();

$addplayerulrl = $this->uri(array('module'=>'sportadmin','action'=>'addplayer','item'=>'player','teamid'=>$teamid,'sportid'=>$sportid));

$addicon =& $this->getObject('geticon','htmlelements');


//display the players in the table
$players_label = "<strong>".$this->objLanguage->languageText('mod_sportsadmin_players','sportsadmin')."</strong>&nbsp;".$addicon->getAddIcon($addplayerulrl);

//check if there are players for a given team
$playerlist = $this->objDbplayer->getPlayersForTeam($teamid);
$playertable = new htmltable();

if(!empty($playerlist)){


   $playertable->startRow();
   $playertable->addHeaderCell($this->objLanguage->languageText('word_name','system'));
   $playertable->addHeaderCell($this->objLanguage->languageText('mod_sportsadmin_position','sportsadmin'));
   $playertable->addHeaderCell($this->objLanguage->languageText('word_action','system'));
   $playertable->endRow();
   
   $class = 'even';
    foreach($playerlist as $p){
	
	$playerdetail =& $this->getObject('link','htmlelements');
	$playerdetail->href = $this->uri(array('action'=>'playerdetails','playerid'=>$p['id'],'sportid'=>$sportid));
	$playerdetail->link = $this->objLanguage->languageText('mod_sportsadmin_playerdetails','sportsadmin');
	
	 $class = ($class == 'odd') ? 'even':'odd';
	  $playertable->startRow();
	  $playertable->addCell($p['name'],'','','',$class);
	  $playertable->addCell($p['position'],'','','',$class);
	  $playertable->addCell($playerdetail->show(),'','','',$class);
	  $playertable->endRow();
	
	}//closing foreach

}

else {
    $playertable->startRow();
    $playertable->addCell($this->objLanguage->languageText('mod_sportadmin_noplayersfor','sportsadmin')."&nbsp;<strong>".$name."</strong>");
    $playertable->endRow();
 
 }


	

$detailstable = new htmltable();

//back link 
$back = & $this->getObject('link','htmlelements');
$back->href = "javascript:history.back()"; 
$back->link = $this->objLanguage->languageText('word_back','system');

$detailstable->startRow();
$detailstable->addCell($fixtureheader);
$detailstable->endRow();

$detailstable->startRow();
$detailstable->addCell($matchtable->show());
$detailstable->endRow();

$detailstable->startRow();
$detailstable->addCell($players_label);
$detailstable->endRow();

 
$detailstable->startRow();
$detailstable->addCell($playertable->show());
$detailstable->endRow();


$detailstable->startRow();
$detailstable->addCell("");
$detailstable->endRow();

$detailstable->startRow();
$detailstable->addCell("");
$detailstable->endRow();
/*
$detailstable->startRow();
$detailstable->addCell($news);
$detailstable->endRow();*/

/*
$detailstable->startRow();
$detailstable->addCell($newstable->show());
$detailstable->endRow();*/

echo $objHeading->show();
echo $outertable->show();

echo $detailstable->show();

echo $back->show();

?>