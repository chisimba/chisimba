<?php
$this->loadClass('link','htmlelements');

$objH = $this->newObject('htmlheading','htmlelements');
$objH->type=2;
$objH->str = $this->objLanguage->languageText('mod_modulecatalogue_updates','modulecatalogue');

$h2 = $this->newObject('htmlheading','htmlelements');
$h2->type=3;
$h2->str = $this->objLanguage->languageText('mod_modulecatalogue_newupdates','modulecatalogue').':'; //'<p><br />Latest Updates: <hr /></p>';
$fname = $this->objConfig->getsiteRootPath().'modules/modulecatalogue/register.conf';
if (file_exists($fname)) {
	$uri = $this->uri(array('action'=>'patch','module'=>'m','patch'=>'p'),'modulecatalogue');
	$link = &new Link($uri);
	$link->link = $this->objLanguage->languageText('mod_modulecatalogue_applypatch','modulecatalogue');
	$pIcon = &$this->newObject('geticon','htmlelements');
	$pIcon->getLinkedIcon($uri,'update');
	$pIcon->alt = $link->link;
	$time = date("d/m/y",filemtime($fname));
	$str = "Module Catalogue - $time - ver 2.5 {$pIcon->show()}{$link->show()}"; 
}

$content = $objH->show().$h2->show().$str;
echo $content;

?>