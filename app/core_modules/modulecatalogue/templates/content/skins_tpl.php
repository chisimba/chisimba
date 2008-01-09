<?php
$this->appendArrayVar('headerParams',"<script type='text/javascript' src='core_modules/modulecatalogue/resources/remote.js'></script>");
$this->loadClass('checkbox','htmlelements');
$this->loadClass('link','htmlelements');

$objH = $this->getObject('htmlheading','htmlelements');
$objH->type=2;
$objH->str = $this->objLanguage->languageText('mod_modulecatalogue_heading','modulecatalogue');


var_dump($skins);

?>