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
 $this->appendArrayVar('headerParams', '<script src="chisimba_modules/sportsadmin/resources/sorttable.js" language="JavaScript" type="text/javascript"></script>');

?>
<?php

$this->loadClass('textinput','htmlelements');
$this->loadClass('hiddeninput','htmlelements');
$sportid = $this->getParam('sportid',NULL);
$item = $this->getParam('item',NULL);
$this->objDbsports = & $this->getObject('dbsports');

//other fields 
$placeinput = new textinput('place');
$defaultstartDate =date('Y-m-d H:m');
$this->objPopupcal = &$this->getObject('datepickajax', 'popupcalendar');
$startDate = $this->objPopupcal->show('startDate', 'yes', 'yes',$defaultstartDate);


   $heading = & $this->getObject('htmlheading','htmlelements');	
    $heading->str = $this->objLanguage->languageText('mod_sport_enterfixture','sports').'&nbsp;'.$this->objDbsports->getSportsById($sportid);
$heading->align = 'center';

    $tblLayout = &$this->newObject( 'htmltable', 'htmlelements');
    $tblLayout->row_attributes = 'align="center"';
    $tblLayout->width = '70%';
	
	
    $tblLayout->startRow();
    $tblLayout->addCell($allteams->show());
    $tblLayout->addCell($buttons."<br/>".$buttons_b,NULL,NULL );
	$tblLayout->addCell("<span id='valuea'> </span>");
	$tblLayout->addCell("<span id='valueb'> </span>");
    $tblLayout->addCell($teamAmember->show().'<br/>'.$teamBmember->show());
    $tblLayout->endRow();
	
$tblLayout->startRow();
$tblLayout->addCell('<strong>'.$this->objLanguage->languageText('mod_sports_place','sports').'</strong>','','','left');
$tblLayout->addCell($placeinput->show());
$tblLayout->endRow();

$tblLayout->startRow();
$tblLayout->addCell('<strong>'.$this->objLanguage->languageText('mod_sportsadmin_matchdatetime','sportsadmin').'</strong>&nbsp;','','','left');
$tblLayout->addCell($startDate );
$tblLayout->endRow();	



$frmManage->addToForm($heading->show()."<br/>");
$frmManage->addToForm("<div id='blog-content'>".$tblLayout->show()."</div>");
$frmManage->addToForm("<div id='blog-footer'>".implode(' / ', $ctrlButtons)."</div>");
$frmManage->addToForm($team_a->show());
$frmManage->addToForm($team_b->show());
$frmManage->addToForm("<div id='blog-footer'>". $lnksubmit->show()."</div>");
?>
<DIV style='padding:1em;'>
     
</DIV>
<?php
echo $frmManage->show();
?>