<?php

$legend = $this->objLanguage->languageText('mod_filemanager_accessdenied_legend', 'filemanager');
$accessDenied = $this->objLanguage->languageText('mod_filemanager_accessdenied_txt', 'filemanager');

$fieldset = new fieldset();
$fieldset->setLegend($legend);
$fieldset->addContent($accessDenied);


$heading = $this->newObject('htmlheading', 'htmlelements');
$heading->htmlheading($legend, 1);
echo $heading->show();
echo $fieldset->show();
?>
