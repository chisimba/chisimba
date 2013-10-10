<?php

$headerParams = $this->getJavascriptFile('new_sorttable.js', 'htmlelements');
$this->appendArrayVar('headerParams', $headerParams);
$headerParams = $this->getJavascriptFile('selectall.js', 'htmlelements');
$this->appendArrayVar('headerParams', $headerParams);

$this->loadClass('form', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('button', 'htmlelements');

//Get Group Name
$groupName = $this->_objGroupAdmin->getName($groupId);
$groupName = explode("^^", $groupName);
$groupName = $groupName[1];

$heading = new htmlheading();

//Get uploader name
$uploader = $this->objUser->getUserDetails($userId);
$eventPageTitle = $this->objLanguage->languageText("mod_podcaster_affiliatepodcaststo", "podcaster", 'Affiliate podcasts to') . " - " . $groupName;
$heading->str = $eventPageTitle;

$heading->type = 1;

echo $heading->show();

if (count($files) == 0) {
    echo '<div class="noRecordsMessage">' . $this->objLanguage->languageText("mod_podcaster_nouploadsbyu", "podcaster", 'You have not Uploaded any files yet. Click on uploads link to upload a podcast') . '</div>';
} else {
    $sortOptions = array(
        'datecreated_desc' => $this->objLanguage->languageText("mod_podcaster_newestfirst", "podcaster", 'Newest First'),
        'datecreated_asc' => $this->objLanguage->languageText("mod_podcaster_oldestfirst", "podcaster", 'Oldest First'),
        'title_asc' => $this->objLanguage->languageText("mod_podcaster_alphabetical", "podcaster", 'Alphabetical'),
        'artist_asc' => $this->objLanguage->languageText("mod_podcaster_author", "podcaster", 'Author')
    );

    echo '<p><strong>' . $this->objLanguage->languageText("mod_podcaster_sortby", "podcaster", 'Sort by') . ':</strong> ';

    $divider = '';
    foreach ($sortOptions as $sortOption => $optionText) {
        if ($sortOption == $sort) {
            echo $divider . $optionText;
        } else {
            $sortLink = new link($this->uri(array('action' => 'manage_event', 'id' => $groupId, 'sort' => $sortOption)));
            $sortLink->link = $optionText;

            echo $divider . $sortLink->show();
        }

        $divider = ' | ';
    }

    echo '</strong></p>';

    $objViewer = $this->getObject('viewer');
    $table = $objViewer->addUserPodcastsToEvent($files, $groupId);
    $podForm = new form('eventpodcastsmgr', $this->uri(array(
                        'action' => 'eventpodcastsmgr',
                        'groupId' => $groupId
                    )));
    $podForm->addToForm($table);

    $objButton = new button('select', $this->objLanguage->languageText('mod_podcaster_selectall', 'podcaster', 'Select All'));
    $objButton->extra = 'onclick="javascript:SetAllCheckBoxes(\'eventpodcastsmgr\', \'fileid[]\', true)"';
    $buttons = $objButton->show();

    $objButton = new button('unselect', $this->objLanguage->languageText('mod_podcaster_unselectall', 'podcaster', 'Unselect All'));
    $objButton->extra = 'onclick="javascript:SetAllCheckBoxes(\'eventpodcastsmgr\', \'fileid[]\', false)"';
    $buttons.= '&nbsp;&nbsp;&nbsp;&nbsp;' . $objButton->show();

    $button = new button('submitform', $this->objLanguage->languageText('mod_podcaster_updateevent', 'podcaster', 'Update event'));
    $button->setToSubmit();

    $buttons.= '&nbsp;&nbsp;&nbsp;&nbsp;' . $button->show();
    $podForm->addToForm($buttons);

    echo $podForm->show();
}

$homeLink = new link($this->uri(array('action' => 'configure_events')));
$homeLink->link = $this->objLanguage->languageText("mod_podcaster_backtoevents", "podcaster", 'Back to events');

echo '<p>' . $homeLink->show() . '</p>';
?>