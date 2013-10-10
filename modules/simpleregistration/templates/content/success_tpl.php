
<?php
$maincss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/simpleregistration.css').'"/>';
$extbase = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/adapter/ext/ext-base.js','htmlelements').'" type="text/javascript"></script>';
$extalljs = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ext-all.js','htmlelements').'" type="text/javascript"></script>';
$extallcss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('ext-3.0-rc2/resources/css/ext-all.css','htmlelements').'"/>';
$maincss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/simpleregistration.css').'"/>';

$this->appendArrayVar('headerParams', $extbase);
$this->appendArrayVar('headerParams', $extalljs);
$this->appendArrayVar('headerParams', $extallcss);
$this->appendArrayVar('headerParams', $maincss);

$table=$this->getObject('htmltable','htmlelements');
$table->cellpadding = 5;
$table->cellpadding = 5;
$regformObj = $this->getObject('formmanager');

$objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');

$objWashout = $this->getObject('washout', 'utilities');
$title1=$objWashout->parseText($eventcontent['event_lefttitle1']);
$title2=$objWashout->parseText($eventcontent['event_lefttitle2']);
$footer=$objWashout->parseText($eventcontent['event_footer']);
$contactemail=$objWashout->parseText($eventcontent['event_emailcontact']);

$rightTitle='<h1>'.$rightTitle1.'</h1>';
$rightTitle.='<h3>'.$rightTitle2.'</h3>';
$leftTitle.=$title1.'<br/>';
$leftTitle.=$title2;


//$home = new link ($this->uri(array('action'=>'home','shortname'=>$shhortname)));
//$home->link= $this->objLanguage->languageText('mod_simpleregistration_home', 'simpleregistration');
$home='<a href="'.$_SERVER['HTTP_REFERER'].'">Home</a>';
$table->startRow();
$table->addCell($leftTitle);
$table->addCell($rightTitle);
$table->endRow();

$objWashout = $this->getObject('washout', 'utilities');
$content=$eventcontent['event_content'];
$pagecontent= $objWashout->parseText($content);

$table->startRow();
$table->addCell($pagecontent);
$table->addCell($this->objLanguage->languageText('mod_simpleregistration_contactemail', 'simpleregistration').'<br>'.$contactemail);
$table->endRow();

$admin = new link ($this->uri(array('action'=>'memberlist','eventid'=>$eventid)));
$admin->link= $this->objLanguage->languageText('mod_simpleregistration_admin', 'simpleregistration');

$admin=$this->objUser->isAdmin() ?$admin->show():"";
$table->startRow();
$table->addCell('<br/><br/><br/><br/>&nbsp;'.$admin);
$table->endRow();

$table->startRow();
$table->addCell('<hr/><br>');
$table->endRow();



$table->startRow();
//addCell($str, $width=null, $valign="top", $align=null, $class=null, $attrib=Null,$border = '0')
$table->addCell($footer,null,null,null,'colspan="2"');
$table->endRow();
echo '<div id="wrap">'.$table->show().'</div>';


?>
