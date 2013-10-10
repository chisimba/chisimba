<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package LRS
* @author Nic Appleby
* @version $id$
*/

$heading = $this->newObject('htmlheading','htmlelements');
$heading->type = 2;
$heading->str = $this->objLanguage->languageText('mod_lrspostlogin_conditions', 'award');
if ($message) {
    $to = $this->newObject('timeoutMessage', 'htmlelements');
    $to->setMessage($this->objLanguage->languageText('mod_award_conditionsupdated', 'award')."<br />");
    $msg = $to->show();
} else {
    $msg = '';
}
$resource = $this->getResourceURI('admin.js');
$this->appendArrayVar('headerParams', "<script type='text/javascript' src='$resource'></script>");
echo $heading->show().$msg.$this->objTemplates->getAgreeConditionsAdmin($agreeId);
?>