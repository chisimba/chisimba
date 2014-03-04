<?php
$this->loadclass('link', 'htmlelements');
$objSysConfig = $this->getObject('altconfig', 'config');
$link=new link($this->uri(array(
    "action"=>'jsoncontextcontentusage',
    "limit"=>'30',
    "startdate"=>$startdate,
    "enddate"=>$enddate,
    "studentsonly"=>$studentsonly
    )));
$this->appendArrayVar('headerParams', '
<script type="text/javascript">
var pageSize = 30;
var uri = "' . str_replace('&amp;', '&', $link->href) . '";
var title= "'.ucWords($this->objLanguage->code2Txt('mod_contextcontent_viewcontextcontentusage','contextcontent',NULL,'[-context-] usage'))." ".ucWords($this->objLanguage->code2Txt('mod_contextcontent_wordfor', 'contextcontent',NULL,"for"))." ".$this->objContext->getTitle( $this->contextCode ).' ('.$this->contextCode.')";
var lang = new Array();
lang["username"] =   "' . ucWords($this->objLanguage->languageText('mod_contextcontent_loginname', 'contextcontent','Username')) . '";
lang["fullname"] =   "' . ucWords($this->objLanguage->languageText('mod_contextcontent_fullname', 'contextcontent',"Names")) . '";
lang["duration"] =   "' . ucWords($this->objLanguage->languageText('mod_contextcontent_logins', 'contextcontent','Access count')) . '";
lang["nologstodisplay"] =   "' . ucWords($this->objLanguage->languageText('mod_contextcontent_nologstodisplay', 'contextcontent','No logs to display')) . '";
lang["displayingpage"] =   "' . $this->objLanguage->languageText('mod_contextcontent_displayingpage', 'contextcontent') . '";
lang["wordof"] =   "' . $this->objLanguage->languageText('mod_contextcontent_wordof', 'contextcontent',"of") . '";
var baseuri = "' . $objSysConfig->getsiteRoot() . 'index.php";
 </script>');
//Ext stuff
$objExtJs = $this->getObject('extjs', 'ext');
$objExtJs->show();
$ext = "";
$ext.= $this->getJavaScriptFile('Ext.ux.grid.Search.js', 'contextcontent');
$ext.= $this->getJavaScriptFile('contextcontentusage.js', 'contextcontent');
$this->appendArrayVar('headerParams', $ext);
echo '<div id="contextcontentusage-grid"></div>';


$fileName = $this->objContextActivityStreamer->csvContextContentUsage($this->contextCode);
echo "<a href='".$this->objConfig->getSitePath().$fileName."'>".$this->objLanguage->code2Txt('mod_contextcontent_downloadcsvlogs', 'contextcontent',NULL,'Download CSV Logs')."</a>";
?>
