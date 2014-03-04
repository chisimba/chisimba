<?php
$this->loadclass('link', 'htmlelements');
$objSysConfig = $this->getObject('altconfig', 'config');
$this->appendArrayVar('headerParams', '
<script type="text/javascript">
var pageSize = 30;
var uri = "' . str_replace('&amp;', '&', $this->uri(array(
    'module' => 'learningcontent',
    'action' => 'jsongetlogs','limit' => '30'
))) . '"; 
var title= "'.ucWords($this->objLanguage->code2Txt('mod_learningcontent_useractivitylogs','learningcontent'))." ".ucWords($this->objLanguage->code2Txt('mod_learningcontent_wordfor', 'learningcontent'))." ".$this->objContext->getTitle( $this->contextCode ).' ('.$this->contextCode.')";
var lang = new Array();
lang["usernames"] =   "' . ucWords($this->objLanguage->code2Txt('mod_learningcontent_username', 'learningcontent')) . '";
lang["pagetitle"] =   "' . ucWords($this->objLanguage->code2Txt('mod_learningcontent_pageorchaptertitle', 'learningcontent')) . '";
lang["startime"] =   "' . ucWords($this->objLanguage->code2Txt('mod_learningcontent_startime', 'learningcontent')) . '";
lang["endtime"] =   "' . ucWords($this->objLanguage->code2Txt('mod_learningcontent_endtime', 'learningcontent')) . '";
lang["type"] =   "' . ucWords($this->objLanguage->code2Txt('mod_learningcontent_type', 'learningcontent')) . '";
lang["nologstodisplay"] =   "' . ucWords($this->objLanguage->code2Txt('mod_learningcontent_nologstodisplay', 'learningcontent')) . '";
lang["displayingpage"] =   "' . $this->objLanguage->code2Txt('mod_learningcontent_displayingpage', 'learningcontent') . '";
lang["wordof"] =   "' . $this->objLanguage->code2Txt('mod_learningcontent_wordof', 'learningcontent') . '";
var baseuri = "' . $objSysConfig->getsiteRoot() . 'index.php";
 </script>');
//Ext stuff
$objExtJs = $this->getObject('extjs', 'ext');
$objExtJs->show();
$ext = "";
$ext .= '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ext-all.js', 'ext').'" type="text/javascript"></script>';
//page specific
$ext .= '<link rel="stylesheet" href="'.$this->getResourceUri('ext-3.0-rc2/shared/examples.css', 'ext').'" type="text/css" />';
$ext .= '<link rel="stylesheet" href="'.$this->getResourceUri('ext-3.0-rc2/examples/grid/grid-examples.css', 'ext').'" type="text/css" />';
//Extensions
$ext .= '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ux/CheckColumn.js', 'ext').'" type="text/javascript"></script>';
//page specific
$ext.= $this->getJavaScriptFile('getlogs.js', 'gradebook2');
$this->appendArrayVar('headerParams', $ext);
echo '<div id="gc-grid"></div>';

$fileName = $this->objContextActivityStreamer->csvContextLogs($this->contextCode);
echo "<a href='".$this->objConfig->getSitePath().$fileName."'>CSV File</a>";
?>
