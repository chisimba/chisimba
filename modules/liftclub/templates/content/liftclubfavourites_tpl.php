<?php
$this->loadclass('link', 'htmlelements');
$objSysConfig = $this->getObject('altconfig', 'config');
$this->appendArrayVar('headerParams', '
<script type="text/javascript">
var pageSize = 25;
var uri = "' . str_replace('&amp;', '&', $this->uri(array(
    'module' => 'liftclub',
    'action' => 'jsongetfavs'
))) . '"; 
var liftitle= "My Favourites";
var usrneed= "offer";
var lang = new Array();
lang["triporigin"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_triporigin', 'liftclub', NULL, 'Origin(Suburb)')) . '";
lang["tripdestiny"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_tripdestiny', 'liftclub', NULL, 'Destiny(Suburb)')) . '";
lang["findoffer"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_findoffer', 'liftclub', NULL, 'Find/Offer')) . '";
lang["datecreated"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_datecreated', 'liftclub', NULL, 'Date')) . '";
lang["needtype"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_needtype', 'liftclub', NULL, 'Type')) . '";
lang["tripdays"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_tripdays', 'liftclub', NULL, 'Trip Days')) . '";
lang["wordview"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_view', 'liftclub', NULL, 'View Lift')) . '";
lang["wordcreated"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_wordcreated', 'liftclub', NULL, 'Created')) . '";
lang["wordof"] =   "' . $this->objLanguage->code2Txt('mod_liftclub_wordof', 'liftclub', NULL, 'of') . '";
lang["noliftstodisplay"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_noliftstodisplay', 'liftclub', NULL, 'No Lifts To Display')) . '";
lang["displayingpage"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_displayingpage', 'liftclub', NULL, 'Displaying Page')) . '";
var baseuri = "' . $objSysConfig->getsiteRoot() . 'index.php";
 </script>');
//Ext stuff
$objExtJs = $this->getObject('extjs', 'ext');
$objExtJs->show();
$ext = "";
$ext.= $this->getJavaScriptFile('Ext.ux.grid.Search.js', 'liftclub');
$ext.= $this->getJavaScriptFile('searchlifts.js', 'liftclub');
$this->appendArrayVar('headerParams', $ext);
echo '<div id="find-grid"></div>';
?>
