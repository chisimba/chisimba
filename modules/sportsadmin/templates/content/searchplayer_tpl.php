<?php 
//security chech that must be put on ever page
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

// end security check

/*
* Interface for displaying the search results for the players
* @Author : Nsabagwa Mary
*/
$content = "";
$searchfield_entry = $this->getParam('searchfield',NULL);
$sportid = $this->getParam('sportid',NULL);
$searchoption = $this->getParam('searchoption',NULL);
$this->loadClass('textinput','htmlelements');
$this->objDbteam = & $this->getObject('dbteam');
$this->loadClass('link','htmlelements');

//echo $searchoption; exit;
 switch($searchoption){
 
			case "name": 
			   $option = "name";
			   
			   break;
			 case "role":
			   $option = "position";
			 break; 
			 
			 case "sport":
			    $option = "sportId";
			 break; 
			 
			 case "team":					
			 $searchfield = $this->getParam('searchfield',NULL); 			 
			 //get the id of the team to be searched for
			 $team_id = $this->objDbteam->getTeamId($searchfield);		
			 $option = 'team';
			 $searchfield_entry = $team_id;
			
			 break; 
			 /*case "tournament":
			    $option = "";
			 break; */
			
			}
			
//form 
$this->loadClass('form','htmlelements');
$this->loadClass('button','htmlelements');
$searchoption = $this->getObject('radio','htmlelements');

//create the search form
$searchuri = $this->uri(array('action'=>'search','sportid'=>$sportid));
$searchform = new form('searchform',$searchuri);

//submit button
$searchsubmit = new button('searchsubmit',$this->objLanguage->languageText('word_submit'));
$searchsubmit->setToSubmit();

$searchoption->name = "searchoption";
$searchoption->addOption('name',$this->objLanguage->languageText('word_name'));
$searchoption->addOption('role',$this->objLanguage->languageText('mod_sportsadmin_position','sportsadmin'));
$searchoption->addOption('sport',$this->objLanguage->languageText('mod_sportsadmin_sport','sportsadmin'));
$searchoption->addOption('team',$this->objLanguage->languageText('mod_sportsadmin_team','sportsadmin'));
$searchoption->addOption('tournament',$this->objLanguage->languageText('mod_sportsadmin_tournament','sportsadmin'));
$searchoption->setSelected('name');

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

$content.= $searchform->show()."<br/><br/>";
	

$heading = $this->getObject('htmlheading','htmlelements');
$this->objDbplayer =& $this->getObject('dbplayer'); 
$this->loadClass('htmltable','htmlelements');
$table = new htmltable();
$table->width = "100%";



$heading->str = $this->objLanguage->languageText('mod_useradmin_searchresultsfor','sportsadmin')."&nbsp;".$searchfield_entry;
$heading->align = "center";

$content .=$heading->show();

//Acess tbl_players and display the results
$platerdata = $this->objDbplayer->searchForPlayer($searchfield_entry,$option); 

if(!empty($platerdata)){
 $class = 'even';
		  
  $table->startRow();
  $table->addHeaderCell($this->objLanguage->languageText('word_name'));
  $table->addHeaderCell($this->objLanguage->languageText('mod_sportsadmin_team','sportsadmin'));
  $table->addHeaderCell($this->objLanguage->languageText('mod_sportsadmin_position','sportsadmin'));
  $table->addHeaderCell($this->objLanguage->languageText('mod_sportsadmin_dateofbirth','sportsadmin'));
  $table->endRow();
$i = 0;
  foreach($platerdata as $p){
  
  $nameuri = $this->uri(array('action'=>'playerdetails','playerid'=>$p['id'],'sportid'=>$sportid));
  $namelink = new link($nameuri);
  $namelink->link = $p['name']; 
  
  
  $class = ($class == 'odd') ? 'even':'odd';
  $teamname = $this->objDbteam->getTeamNameById($p['team']);
  $table->startRow();
  $table->addCell($namelink->show(),'','','',$class);
  $table->addCell($teamname,'','','',$class);
  $table->addCell($p['position'],'','','',$class);
  $table->addCell($p['dateOfBirth'],'','','',$class);
  $table->endRow();
   $i++;
  }///closing foreach
$content .=$table->show();

$content .= "<br><br>".$i."&nbsp;".$this->objLanguage->languageText('mod_sportsamin_rowsreturned','sportsadmin');

}
else {
 $content .= $this->objLanguage->languageText('mod_sportsadmin_noresults','sportsadmin')."&nbsp;(".$searchfield_entry.")";
}



echo $content;
?>