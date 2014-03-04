<?php
 $objAltConfig = $this->getObject('altconfig','config');
 
$destinationDir = $objAltConfig->getsiteRoot()."/usrfiles/practicals/submissions/export/$filename.xls";
$backLink = new link ($this->uri(array("action"=>"view","id"=>$filename)));
$backLink->link = $this->objLanguage->languageText('mod_practicals_backtolist', 'practicals', 'Back to List of Practicals');

echo  "<a href=\"$destinationDir\">Click here to download the file</a><br/>".$backLink->show();


?>