<?php
// Initialise variables
$userContexts=array();
$allAnn = "";
$courseAnn ="";
$content="";

// Load the viewer javascript
$this->appendArrayVar('headerParams',
$this->getJavaScriptFile('announceview.js',
    'announcements'));

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('add');

$addLink = new link ($this->uri(array('action'=>'add')));
$addLink->link = $objIcon->show();

$cc = $this->objContext->getContextCode();
if ($cc != '') {
    $numContextAnnouncements = $this->objAnnouncements->getNumContextAnnouncements($cc);
    $header = new htmlHeading();
    $header->type = 1;
    $header->str = ucwords($this->objLanguage->code2Txt('mod_announcements_contextannouncements',
      'announcements', NULL, '[-context-] Announcements'))
      . ' - <span class="coursetitle">' . $this->objContext->getTitle($cc)
      . '</span> ('.$numContextAnnouncements.')';

    if ($isAdmin || count($lecturerContext) > 0) {
        $header->str .= ' '.$addLink->show();
    }

    $courseAnn .=  $header->show();

    $objPagination = $this->newObject('pagination', 'navigation');
    $objPagination->module = 'announcements';
    $objPagination->action = 'getcontextajax';
    $objPagination->id = 'pagenavigation_context';

    $itemsPerPage = ($numContextAnnouncements - ($numContextAnnouncements % $this->itemsPerPage)) / $this->itemsPerPage;
    if ($numContextAnnouncements % $this->itemsPerPage != 0) {
        $itemsPerPage++;
    }

    $objPagination->numPageLinks = $itemsPerPage;

    $courseAnn .= $objPagination->show();
    $courseAnn = "\n<div class='outerwrapper'>$courseAnn</div>\n";
    // Course announcements rendered here.
    $content.= $courseAnn;
}

// All announcements below.
$userId = $this->objUser->userId();
$objUserContext = $this->getObject('usercontext', 'context');
if(!empty($userId)){
    $userContext = $objUserContext->getUserContext($this->userId);
    // Remove the current context from the array.
    foreach ($userContext as $context) {
        if ($context !== $cc) {
            $userContexts[] = $context;
        }
    }
    unset($userContext);
}
$numAnnouncements = $this->objAnnouncements->getNumAnnouncements($userContexts);
$header = new htmlHeading();
$header->type = 1;
$header->str = $this->objLanguage->languageText(
  'mod_announcements_myannouncements', 'announcements',
  'All My Announcements'
).' ('.$numAnnouncements.')';

if ($isAdmin || count($lecturerContext) > 0) {
    $header->str .= ' '.$addLink->show();
}

$allAnn .= $header->show();

$objPagination = $this->newObject('pagination', 'navigation');
$objPagination->module = 'announcements';
$objPagination->action = 'getajax';
$objPagination->id = 'pagenavigation_all';

$itemsPerPage = ($numAnnouncements - ($numAnnouncements % $this->itemsPerPage)) / $this->itemsPerPage;
if ($numAnnouncements % $this->itemsPerPage != 0) {
    $itemsPerPage++;
}
$objPagination->numPageLinks = $itemsPerPage;
$allAnn .= $objPagination->show();
$allAnn  = "\n<div class='outerwrapper'>$allAnn </div>\n";
$addLink = new link ($this->uri(array('action'=>'add')));
$addLink->link = $this->objLanguage->languageText('mod_announcements_postnewannouncement', 'announcements', 'Post New Announcement');
$content.= $allAnn;

// Add new announcement link
if ($isAdmin || count($lecturerContext) > 0) {
    $content.= "<div class='linkwrapper'><div class='adminadd'></div><div class='adminaddlink'>" . $addLink->show() . "</div></div>";
}

$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

$toolbar = $this->getObject('contextsidebar', 'context');

// Initialize left column
$leftSideColumn = $toolbar->show();
$this->objFeatureBox = $this->newObject('featurebox', 'navigation');
//$leftSideColumn=$this->objFeatureBox->show($blocktitle, $leftSideColumn);
$cssLayout->setLeftColumnContent($leftSideColumn);
$cssLayout->setMiddleColumnContent(
  "<div class='announcements'>"
  . $content . "</div>");
//$cssLayout->setRightColumnContent($this->objAnnouncementsTools->getRightBlocks());

echo $cssLayout->show();
?>
