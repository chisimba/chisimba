<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_lrs_genderheading','award');

$genderContent = $this->objTemplates->getGenderSummary($sicId,$aggregate,$agreeTypeId,$year);
$genderTab = "<div id='genderDiv'>$genderContent</div>";

echo $header->show().$genderTab;
?>