
<?php
if($this->objUser->isLoggedIn()){
//    $this->nextAction('expresssignin');
}
$maincss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/fossad.css').'"/>';
$this->appendArrayVar('headerParams', $maincss);

$table=$this->getObject('htmltable','htmlelements');
$table->cellpadding = 5;
$table->cellpadding = 5;
$regformObj = $this->getObject('formmanager');
$objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
$title1=$objSysConfig->getValue('LEFT_TITLE1', 'fossad');
$title2=$objSysConfig->getValue('LEFT_TITLE2', 'fossad');
$message='"'.$this->objLanguage->languageText('mod_fossad_isopen', 'fossad').'"';
if($mode == 'edit'){
    $message='<font color="red">'.$this->objLanguage->languageText('mod_fossad_emailinuse', 'fossad').'</font>';
}

if($mode == 'loginagain'){
    $message='<font color="red">Please sign in again to complete registration</font>';
}
$rightTitle='<h1>'.$this->objLanguage->languageText('mod_fossad_registration', 'fossad').'</h1>';
$rightTitle.='<h3>'.$message.'</h3>';
$leftTitle.='<h1>'.$title1.'</h1>';
$leftTitle.='<h4>'.$title2.'</h4>';

$expressLink =new link($this->uri(array('action'=>'expresssignin')));
$expressLink->link= '<h3>'.$this->objLanguage->languageText('mod_fossad_express', 'fossad');

$programLink =new link($this->uri(array('action'=>'expresssignin')));
$programLink->link= '<h3>The Program</h3>';


$table->startRow();
$table->addCell($leftTitle);
$table->addCell($rightTitle);
$table->endRow();

$table->startRow();
$table->addCell('');
$table->addCell($expressLink->show());//.'<img src="'.$this->getResourceUri('images/line.png').'">');
$table->endRow();

$objWashout = $this->getObject('washout', 'utilities');
$content=$objSysConfig->getValue('CONTENT', 'fossad');
$pagecontent= $objWashout->parseText($content);

$table->startRow();
$table->addCell($pagecontent);
$table->addCell($regformObj->createRegisterForm($editfirstname,$editlastname,$editcompany,$editemail,$mode));
$table->endRow();

$admin = new link ($this->uri(array('action'=>'admin')));
$admin->link= $this->objLanguage->languageText('mod_fossad_admin', 'fossad');


if($this->objUser->isLoggedIn()){
    $table->startRow();
    $table->addCell($admin->show());
    $table->endRow();
}


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

echo '<div id="wrap">'.$error.$table->show().'</div>';
?>
