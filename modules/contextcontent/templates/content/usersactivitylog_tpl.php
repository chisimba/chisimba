<?php
$this->loadclass('link', 'htmlelements');
$link=new link($this->uri(array(
    "action"=>'jsongetlogs',
    "limit"=>'30',
    "startdate"=>$startdate,
    "enddate"=>$enddate,
    "studentsonly"=>$studentsonly
    )));
$objSysConfig = $this->getObject('altconfig', 'config');
$this->appendArrayVar('headerParams', '
<script type="text/javascript">
var pageSize = 30;
var startdate="'.$startdate.'";
var enddate="'.$enddate.'";
var uri = "' . str_replace('&amp;', '&', $link->href) . '";
var title= "'.ucWords($this->objLanguage->code2Txt('mod_contextcontent_useractivitylogs','contextcontent',NULL,'User activity logs'))." ".ucWords($this->objLanguage->code2Txt('mod_contextcontent_wordfor', 'contextcontent',NULL,"for"))." ".$this->objContext->getTitle( $this->contextCode ).' ('.$this->contextCode.')";
var lang = new Array();
lang["usernames"] =   "' . ucWords($this->objLanguage->code2Txt('mod_contextcontent_username', 'contextcontent',NULL,'Username')) . '";
lang["pagetitle"] =   "' . ucWords($this->objLanguage->code2Txt('mod_contextcontent_pageorchaptertitle', 'contextcontent',NULL,'Chapter')) . '";
lang["startime"] =   "' . ucWords($this->objLanguage->code2Txt('mod_contextcontent_startime', 'contextcontent',NULL,"Start time")) . '";
lang["endtime"] =   "' . ucWords($this->objLanguage->code2Txt('mod_contextcontent_endtime', 'contextcontent',NULL,"End time")) . '";
lang["type"] =   "' . ucWords($this->objLanguage->code2Txt('mod_contextcontent_type', 'contextcontent',NULL,"Type")) . '";
lang["nologstodisplay"] =   "' . ucWords($this->objLanguage->code2Txt('mod_contextcontent_nologstodisplay', 'contextcontent',NULL,"No logs to display")) . '";
lang["displayingpage"] =   "' . $this->objLanguage->code2Txt('mod_contextcontent_displayingpage', 'contextcontent',NULL,"Display image") . '";
lang["wordof"] =   "' . $this->objLanguage->code2Txt('mod_contextcontent_wordof', 'contextcontent',NULL,"of") . '";
var baseuri = "' . $objSysConfig->getsiteRoot() . 'index.php";

 </script>');
//Ext stuff
$objExtJs = $this->getObject('extjs', 'ext');
$objExtJs->show();
$ext = "";
$ext.= $this->getJavaScriptFile('Ext.ux.grid.Search.js', 'contextcontent');
$ext.= $this->getJavaScriptFile('getlogs.js', 'contextcontent');
$this->appendArrayVar('headerParams', $ext);
echo '<div id="lc-grid"></div>';


$fileName = $this->objContextActivityStreamer->csvContextLogs($this->contextCode);
echo "<a href='".$this->objConfig->getSitePath().$fileName."'>".$this->objLanguage->code2Txt('mod_contextcontent_downloadcsvlogs', 'contextcontent',NULL,"Download CSV Logs")."</a>";
$timetakenincontext = $this->objContextActivityStreamer->getTimeTakenByEachMember($this->contextCode,$startdate,$enddate);
?>
