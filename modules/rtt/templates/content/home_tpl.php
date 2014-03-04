<?php

$this->loadClass("link", "htmlelements");
$objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
$moduleUri = $this->objAltConfig->getModuleURI();
$siteRoot = $this->objAltConfig->getSiteRoot();
$codebase = $siteRoot . "/" . $moduleUri . '/realtime/resources/';
$appImg = '<img src="' . $siteRoot . '/' . $moduleUri . '/rtt/resources/images/collaboration.png" align="left">';
$callImg = '<img src="' . $siteRoot . '/' . $moduleUri . '/rtt/resources/images/call.png" align="left">';

$link = new link($this->uri(array("action" => "runjnlp")));
$cssLayout = $this->newObject('csslayout', 'htmlelements'); // Set columns to 2
$cssLayout->setNumColumns(2);
$title = $this->objLanguage->languageText('mod_realtime_filtertitle', 'realtime', 'To join Virtual Classroom, click on image below.');
//$str = '<hr class="realtime-hr"><center><p class="realtime-title">' . $title . '</p><p class="realtime-img"><a href = "' . $link->href . '">' . $imgLink . '</a></p></center><hr class="realtime-hr">';


$str = '<h1 class="rtt-heading">Realtime Tools Launch Center</h1>';

/*$callLink = new link($this->uri(array("action" => "voiceapp")));
$callLink->link = $callImg . '&nbsp;Call Conference';

$str.='<br/>' . $callLink->show();
*/

$appLink = new link($this->uri(array("action" => "runjnlp")));
$appLink->link = '<fieldset>Launch Collaboration Environment</fieldset>';

$str.='<br/><br/>' . $appLink->show();
/*
  $postLoginSideMenu = $objSysConfig->getValue('SIDEMENU', 'postlogin');
  switch (strtolower($postLoginSideMenu)) {
  case 'elearnpostlogin':
  $elearnPostLoginMenu = $this->newObject('postloginmenu_elearn', 'toolbar');
  $cssLayout->setLeftColumnContent($elearnPostLoginMenu->show());
  break;
  default:
  $postLoginMenu = $this->newObject('postloginmenu', 'toolbar');
  $cssLayout->setLeftColumnContent($postLoginMenu->show());
  break;
  }
 */
$toolbar = $this->getObject('contextsidebar', 'context');
$cssLayout->setLeftColumnContent($toolbar->show());

// Add Right Column
$cssLayout->setMiddleColumnContent($str);

//Output the content to the page
echo $cssLayout->show();
?>
