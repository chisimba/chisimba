<?php

if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// Load classes
$this->loadClass("form", "htmlelements");
$this->loadClass("textinput", "htmlelements");
$this->loadClass("hiddeninput", "htmlelements");
$this->loadClass("button", "htmlelements");
$this->loadClass("radio", "htmlelements");
$this->loadClass("htmltable", 'htmlelements');

//Add page heading
$objHeading = &$this->getObject('htmlheading', 'htmlelements');
$objHeading->type = 1;
$objHeading->str = $objLanguage->languageText("mod_podcaster_addevent", 'podcaster', "Add event");

echo $objHeading->show();
if ($mode == 'edit') {
    $action = 'editeventconfirm';
} else {
    $action = 'addeventconfirm';
}
$form = new form("add", $this->uri(array(
                    'module' => 'podcaster',
                    'action' => $action
                )));

$objTable = new htmltable();
$objTable->width = '30';
$objTable->attributes = " align='center' border='0'";
$objTable->cellspacing = '12';
$row = array(
    "<b>" . $objLanguage->languageText("word_name") . ":</b>"
);
$objTable->addRow($row, NULL);
$row = array(
    $objUser->fullName()
);
$objTable->addRow($row, NULL);


$form->addRule('event', $objLanguage->languageText("mod_podcaster_typeeventname", 'podcaster', "You need to type in the event name"), 'required');
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_podcaster_eventname", 'podcaster', 'Event name') . ":</b>"
);
$objTable->addRow($row, NULL);

if ($mode == 'edit') {
    $eventTxt = new hiddeninput("eventId", $eventId);
    $eventTxt = $eventTxt->show();
} else {
    $eventTxt = "";
    $eventName = "";
}
//Add event lable and text input box
$event = new textinput("event", $eventName);
$event->size = 60;
$event = $event->show();

$row = array(
    $event . $eventTxt
);
$objTable->addRow($row, NULL);

$row = array(
    "<b>" . $objLanguage->languageText("mod_podcaster_event", 'podcaster', 'Event') . " " . strtolower($objLanguage->languageText("mod_podcaster_category", 'podcaster', 'Category')) . ":</b>"
);
$objTable->addRow($row, NULL);

$category = new radio('category');
$category->setBreakSpace('<br />');
foreach ($categoriesList as $thisCat) {
    $category->addOption($thisCat['id'], '<strong>' . $thisCat['category'] . '</strong> - <span class="caption">' . $thisCat['description'] . '</span>');
}
if ($mode == 'edit') {
    $category->setSelected($eventsData['categoryid']);
}
$row = array(
    $category->show()
);
$objTable->addRow($row, NULL);


$row = array(
    "<b>" . $objLanguage->languageText("mod_podcaster_accesslevel", 'podcaster', 'Access level') . ":</b>"
);
$objTable->addRow($row, NULL);

$access = new radio('access');
$access->setBreakSpace('<br />');
$access->addOption('public', '<strong>' . $this->objLanguage->languageText('word_public', 'system', 'Public') . '</strong> - <span class="caption">' . $this->objLanguage->languageText('mod_podcaster_publicexplained', 'podcaster', 'Event can be accessed by all users, including anonymous users') . '</span>');
$access->addOption('open', '<strong>' . $this->objLanguage->languageText('word_open', 'system', 'Open') . '</strong> - <span class="caption">' . $this->objLanguage->languageText('mod_podcaster_openexplained', 'podcaster', 'Event can be accessed by all users that are logged in') . '</span>');
$access->addOption('private', '<strong>' . $this->objLanguage->languageText('word_private', 'system', 'Private') . '</strong> - <span class="caption">' . $this->objLanguage->languageText('mod_podcaster_privateexplained', 'podcaster', 'Only the members of this event can view') . '<span class="caption">');

if ($mode == 'edit') {
    $access->setSelected($eventsData['access']);
} else {
    $access->setSelected('public');
}
$row = array(
    $access->show()
);
$objTable->addRow($row, NULL);

$row = array(
    "<b>" . $objLanguage->languageText("mod_podcaster_publishedstatus", 'podcaster', 'Published status') . ":</b>"
);
$objTable->addRow($row, NULL);

$publish = new radio('publish');
$publish->setBreakSpace('<br />');
$publish->addOption('published', '<strong>' . $this->objLanguage->languageText('mod_podcaster_published', 'podcaster', 'Published'));
$publish->addOption('unpublished', '<strong>' . $this->objLanguage->languageText('mod_podcaster_unpublished', 'podcaster', 'Unpublished'));

if ($mode == 'edit') {
    $publish->setSelected($eventsData['publish_status']);
} else {
    $publish->setSelected('unpublished');
}
$row = array(
    $publish->show()
);
$objTable->addRow($row, NULL);

//Save button
$button = new button("submit", $objLanguage->languageText("word_save")); //word_save
$button->setToSubmit();
// Show the cancel link
$buttonCancel = new button("submit", $objLanguage->languageText("word_cancel"));
$objCancel = &$this->getObject("link", "htmlelements");
$objCancel->link($this->uri(array(
            'module' => 'podcaster',
            'action' => 'configure_events'
        )));
$objCancel->link = $buttonCancel->show();
$linkCancel = $objCancel->show();
$row = array(
    $button->show() . '  ' . $linkCancel
);
$objTable->addRow($row, NULL);
$form->addToForm($objTable->show());
echo $form->show();
?>