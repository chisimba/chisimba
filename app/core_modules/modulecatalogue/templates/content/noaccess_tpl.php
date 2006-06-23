<?php
$objHeader = &$this->getObject('htmlheading','htmlelements');
$objHeader->str = $this->objLanguage->languageText('mod_modulecatalogue_notadmin','modulecatalogue');
echo $objHeader->show();
?>