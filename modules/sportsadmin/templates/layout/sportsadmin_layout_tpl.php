<?php 

//instance of the language class
$this->objLanguage =& $this->getObject('language','language');

//instance of dbsports
$this->objDbsports =& $this->getObject('dbsports');

//instnace of user class to check for permissions
$this->objUser =& $this->getObject('user','security');

//creating an instance of the css class to determine the number of rows
$cssLayout = & $this->newObject('csslayout','htmlelements');

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

if($this->objUser->isAdmin()){
	$table->startRow();
	$table->addCell($link.'<br /><br />');
	$table->endRow();
}

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
			
		
					  			
$menu1->addBlock($sportlink->show(),$tournamentlink->show()."<br />".$teamlink->show()."<br />".$playerlink->show());
	
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



echo $cssLayout->show();


?>