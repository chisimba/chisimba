<?php
 $objAltConfig = $this->getObject('altconfig','config');
 
$destinationDir = $objAltConfig->getsiteRoot()."/usrfiles/assignment/submissions/export/$filename.xls";
$backLink = new link ($this->uri(array("action"=>"view","id"=>$filename)));
$backLink->link = $this->objLanguage->languageText('mod_assignment_backtolist', 'assignment', 'Back to List of Assignments');

echo  "<a href=\"$destinationDir\">Click here to download the file</a><br/>".$backLink->show();


?>