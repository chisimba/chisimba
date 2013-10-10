<?php

$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$objDateTime = $this->getObject('dateandtime', 'utilities');

$header = new htmlHeading();
$header->type = 1;
$header->str = $announcement['title'];
$outStr="";
// Check if User has permission
if ($this->checkPermission($announcement['id'])) {
    $objIcon = $this->newObject('geticon', 'htmlelements');
    $objIcon->setIcon('edit');
    $editLink = new link ($this->uri(array('action'=>'edit', 'id'=>$announcement['id'])));
    $editLink->link = $objIcon->show();
    $header->str .= ' '.$editLink->show();
}
if ($this->checkPermission($announcement['id'])) {
    $objIcon = $this->newObject('geticon', 'htmlelements');
    $objIcon->setIcon('delete');
    $editLink = new link ($this->uri(array('action'=>'edit', 'id'=>$announcement['id'])));
    $editLink->link = $objIcon->show();
    //Removed by Wesley Nitscke .. cannot edit an announcement once it has been sent out.. post a new one rather
   // $header->str .= ' '.$editLink->show();
    $deleteArray = array('action'=>'delete', 'id'=>$announcement['id']);
    $deleteLink = $objIcon->getDeleteIconWithConfirm($announcement['id'], $deleteArray, 'announcements');
    $header->str .= ' '.$deleteLink;
}
$outStr = $header->show();
$outStr .=  '<p><strong>By:</strong> '.$this->objUser->fullName($announcement['createdby']).' - '.$objDateTime->formatDate($announcement['createdon']);
if ($announcement['contextid'] == 'site') {
    $outStr .= ' - <strong>'
      . $this->objLanguage->languageText('word_type', 'system', 'Type')
      .':</strong> '.$this->objLanguage->languageText(
        'mod_announcements_siteannouncement', 'announcements', 'Site Announcement'
      ) . '</p>';
} else {
    $outStr .= '<br /><strong>'
      . $this->objLanguage->languageText(
        'mod_announcements_announcementtype', 'announcements', 'Announcement Type'
      ) . ':</strong> ' . ucwords($this->objLanguage->code2Txt(
        'mod_announcements_contextannouncement', 'announcements',
        NULL, '[-context-] Announcement')
      ).' - ';
    $contexts = $this->objAnnouncements->getMessageContexts($announcement['id']);
    if (count($contexts) > 0) {
        $divider = '';
        foreach ($contexts as $context)
        {
            $outStr .=  $divider . $this->objContext->getTitle($context);
            $divider = ', ';
        }
        $outStr .= '</p>';
    }
}
$outStr .=  $announcement['message'];
// Render the outer wrapped layer
$objWashOut = $this->getObject('washout', 'utilities');
$ret = $objWashOut->parseText($outStr);

$backLink = new link ($this->uri(NULL));
$backLink->link = $this->objLanguage->languageText('mod_announcements_back', 'announcements', 'Back to Announcements');
$addLink = new link ($this->uri(array('action'=>'add')));
$addLink->link = $this->objLanguage->languageText('mod_announcements_postnewannouncement', 'announcements', 'Post New Announcement');
$outStr = "<div class='linkwrapper'><div class='modulehome'></div><div class='modulehomelink'>" . $backLink->show() . "</div></div>";
if ($isAdmin || count($lecturerContext) > 0) {
    $outStr .=  "<div class='linkwrapper'><div class='adminadd'></div><div class='adminaddlink'>" . $addLink->show() . "</div></div>";
}
echo  "<div class='announcements'><div class='outerwrapper'>"
      . $ret .  $outStr . "</div></div>";
?>