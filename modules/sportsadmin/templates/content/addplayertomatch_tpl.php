<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
*
* @author Nsabagwa Mary, Kaddu Ismael
* @
*/
?>
<?php
// Page headers and layout template
$this->setLayoutTemplate('sportsadmin_layout_tpl.php');
 $this->appendArrayVar('headerParams', '<script src="core_modules/groupadmin/resources/selectbox.js" language="JavaScript" type="text/javascript"></script>');

?>
<?php


$this->loadClass('textinput','htmlelements');
$this->loadClass('hiddeninput','htmlelements');
$sportid = $this->getParam('sportid',NULL);
$item = $this->getParam('item',NULL);
$fixtureid = $this->getParam('fixtureid',NULL);
$teamid = $this->getParam('teamid',NULL);
$this->objDbsports = & $this->getObject('dbsports');
$this->objDbtournament = & $this->getObject('dbtournament');
$this->objDbfixture = & $this->getObject('dbfixtures');
$this->objDbteam = & $this->getObject('dbteam');
$sportid = $this->getParam('sportid',NULL);
$tournamentid = $this->getParam('tournamentid',NULL);
$this->loadClass('hiddeninput','htmlelements');

//get the name of the fixture
$fixturename = $this->objDbfixture ->getFixtureforgame($sportid,$fixtureid);

if(!empty($fixturename)){
  foreach($fixturename as $fn){
  $team_a = $fn['team_a'];
  $team_b = $fn['team_b'];
  }

}


$durations = $this->objDbtournament->pickTournamentduration($sportid,$tournamentid);


foreach($durations as $d){
	  $startTime = $d['startdate'];
	  $end =$d['enddate'];	  
	}

//Get the names of the players
$teamAname = $this->objDbteam->getTeamNameById($team_a);
$teamBname = $this->objDbteam->getTeamNameById($team_b);

$heading = & $this->getObject('htmlheading','htmlelements');	
$heading->str = $this->objDbtournament->getTournamentNameById($tournamentid)."&nbsp;(".$startTime."&nbsp;-&nbsp;".$end.")&nbsp;".$teamAname ."&nbsp;VS &nbsp;".$teamBname;
$heading->align = 'center';

$tblLayout = &$this->newObject( 'htmltable', 'htmlelements');
$tblLayout->row_attributes = 'align="center"';
$tblLayout->width = '70%';

$tblLayout->startRow();
$tblLayout->addCell($memberstable->show());
$tblLayout->addCell($buttons."<br/>".$buttons_b,NULL,NULL );
$tblLayout->addCell($selected->show());
$tblLayout->endRow();
	
	
//a list of the required hidden fields
$sportidfield  = new hiddeninput('sportid',$sportid);
$fixtureidfield  = new hiddeninput('fixtureid',$fixtureid);
$tournamentidfield = new hiddeninput('tournamentid',$tournamentid);
$teamidfield = new hiddeninput('teamid',$teamid);
	
$frmManage->addToForm($fixtureidfield->show()."<br/>");
$frmManage->addToForm($tournamentidfield->show()."<br/>");
$frmManage->addToForm($heading->show()."<br/>");
$frmManage->addToForm($sportidfield->show()."<br/>");
$frmManage->addToForm($teamidfield->show()."<br/>");
$frmManage->addToForm("<div id='blog-content'>".$tblLayout->show()."</div>");
$frmManage->addToForm("<div id='blog-footer'>".$btnSave->show()."&nbsp;&nbsp;". $lnkCancel->show()."</div>");
?>
<DIV style='padding:1em;'>
        <?php  echo $frmManage->show(); ?>
</DIV>
<?php

?>