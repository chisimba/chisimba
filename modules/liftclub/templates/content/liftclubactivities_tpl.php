<?php
$this->loadclass('link', 'htmlelements');
$objSysConfig = $this->getObject('altconfig', 'config');
$this->appendArrayVar('headerParams', '
<script type="text/javascript">
var pageSize = 25;
var uri = "' . str_replace('&amp;', '&', $this->uri(array(
    'module' => 'liftclub',
    'action' => 'jsongetactivities'
))) . '"; 
var liftitle= "LiftClub Activities";
var lang = new Array();
lang["datecreated"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_datecreated', 'liftclub', NULL, 'Date')) . '";
lang["description"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_description', 'liftclub', NULL, 'Description')) . '";
lang["wordof"] =   "' . $this->objLanguage->code2Txt('mod_liftclub_wordof', 'liftclub', NULL, 'of') . '";
lang["noliftsclubactivities"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_noliftsclubactivities', 'liftclub', NULL, 'No LiftClub Activities To Display')) . '";
lang["displayingpage"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_displayingpage', 'liftclub', NULL, 'Displaying Page')) . '";
var baseuri = "' . $objSysConfig->getsiteRoot() . 'index.php";
 </script>');
//Ext stuff
$objExtJs = $this->getObject('extjs', 'ext');
$objExtJs->show();
$ext = "";
$ext.= $this->getJavaScriptFile('Ext.ux.grid.Search.js', 'liftclub');
$ext.= $this->getJavaScriptFile('searchactivities.js', 'liftclub');
$this->appendArrayVar('headerParams', $ext);
echo '<div id="activities-grid"></div>';
?>
