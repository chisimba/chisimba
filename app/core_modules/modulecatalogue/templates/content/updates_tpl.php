<?php
$this->loadClass('link','htmlelements');

$objH = $this->newObject('htmlheading','htmlelements');
$objH->type=2;
$objH->str = $this->objLanguage->languageText('mod_modulecatalogue_updates','modulecatalogue');

$h2 = $this->newObject('htmlheading','htmlelements');
$h2->type=3;
$h2->str = $this->objLanguage->languageText('mod_modulecatalogue_newupdates','modulecatalogue').':';
$fname = $this->objModFile->findregisterfile($patch['modname']);
$patchArray=array(array('modname'=>'modulecatalogue','ver'=>'1.2','regfile'=>$this->objModFile->findregisterfile('modulecatalogue'),'desc'=>'update of critical stuff'),
				array('modname'=>'ircchat','ver'=>'3.8','regfile'=>$this->objModFile->findregisterfile('ircchat'),'desc'=>'Text elements updated'));

foreach ($patchArray as $patch) {
	$uri = $this->uri(array('action'=>'patch','mod'=>$patch['modname'],'patchver'=>$patch['ver']),'modulecatalogue');
	$link = &new Link($uri);
	$link->link = $this->objLanguage->languageText('mod_modulecatalogue_applypatch','modulecatalogue');
	$pIcon = &$this->getObject('geticon','htmlelements');
	$pIcon->getLinkedIcon($uri,'update');
	$pIcon->alt = $link->link;
	$time = date("d/m/y",filemtime($patch['regfile']));
	$str .= '<b>'.ucwords($patch['modname'])."</b> - $time - <b>Version:</b> {$patch['ver']} {$pIcon->show()}{$link->show()}<br />
			<b>{$this->objLanguage->languageText('word_description','modulecatalogue')}:</b> {$patch['desc']}<hr />";
}

$content = $objH->show().$h2->show().$str;
echo $content;

?>