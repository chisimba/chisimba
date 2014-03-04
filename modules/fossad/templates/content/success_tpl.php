
<?php
$maincss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/fossad.css').'"/>';
$extbase = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/adapter/ext/ext-base.js','htmlelements').'" type="text/javascript"></script>';
$extalljs = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ext-all.js','htmlelements').'" type="text/javascript"></script>';
$extallcss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('ext-3.0-rc2/resources/css/ext-all.css','htmlelements').'"/>';
$maincss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/fossad.css').'"/>';

$this->appendArrayVar('headerParams', $extbase);
$this->appendArrayVar('headerParams', $extalljs);
$this->appendArrayVar('headerParams', $extallcss);
$this->appendArrayVar('headerParams', $maincss);

$table=$this->getObject('htmltable','htmlelements');
$table->cellpadding = 5;
$table->cellpadding = 5;
$regformObj = $this->getObject('formmanager');

$objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
$contactemail=$objSysConfig->getValue('CONTACT_EMAIL', 'fossad');
$title1=$objSysConfig->getValue('LEFT_TITLE1', 'fossad');
$title2=$objSysConfig->getValue('LEFT_TITLE2', 'fossad');

$rightTitle='<h1>'.$rightTitle1.'</h1>';
$rightTitle.='<h3>'.$rightTitle2.'</h3>';
$leftTitle.='<h1>'.$title1.'</h1>';
$leftTitle.='<h4>'.$title2.'</h4>';


$home = new link ($this->uri(array('action'=>'home')));
$home->link= $this->objLanguage->languageText('mod_fossad_home', 'fossad');

$table->startRow();
$table->addCell($leftTitle);
$table->addCell($rightTitle);
$table->endRow();

$objWashout = $this->getObject('washout', 'utilities');
$content=$objSysConfig->getValue('CONTENT', 'fossad');
$pagecontent= $objWashout->parseText($content);

$table->startRow();
$table->addCell($pagecontent);
$table->addCell($this->objLanguage->languageText('mod_fossad_contactemail', 'fossad').'<br>'.$contactemail);
$table->endRow();

$admin = new link ($this->uri(array('action'=>'admin')));
$admin->link= $this->objLanguage->languageText('mod_fossad_admin', 'fossad');

$admin=$this->objUser->isAdmin() ?$admin->show():"";
$table->startRow();
$table->addCell('<br/><br/><br/><br/>'.$home->show().'&nbsp;'.$admin);
$table->endRow();

$table->startRow();
$table->addCell('<hr/><br>');
$table->endRow();




$table->startRow();
$table->addCell('<h6><a href="http://openoffice.org">Open Office</a>,
 <a href="http://avoir.uwc.ac.za">Chisimba</a>,
 <a href="http://presentations.wits.ac.za">Realtime tools</a>,
 <a href="http://kim.wits.ac.za/elearndemo">Podcasting,
<a href="http://www.ubuntu.com">Ubuntu,
<a href="http://www.gimp.org">GIMP,
<a href="http://www.blender.org">Blender</a>,
<a href="http://www.icecast.org">IceCast</a>,
<a href="http://audacity.sourceforge.net/">Audacity</a></6>');
$table->endRow();


echo '<div id="wrap">'.$table->show().'</div>';

?>
