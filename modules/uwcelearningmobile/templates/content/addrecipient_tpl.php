<?php

//Add recipient tamplate
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');

$link = new link($this->URI(array('action' => 'showbooks')));
$link->link = 'Address Book';
echo '<p>' . $link->show() . '</p>';

$searchform = new form('uwcelearningmobile', $this->uri(array(
                    'action' => 'calladdrecipient'
                )));

$searchtext = new textinput('search');

if (isset($search)) {
    $searchtext->value = $search;
}
$searchform->addToForm($searchtext->show());

$objButton = '<input type="submit" value="' . $this->objLanguage->languageText("word_search", "system") . '" />';
$searchform->addToForm($objButton);

$objTable = new htmltable();

$objTable->startHeaderRow();
$objTable->addHeaderCell('<strong>Search and Add Recipients</strong>', '100%');
$objTable->endHeaderRow();

$objTable->startRow();
$objTable->addCell('<br/>' . $searchform->show(), '', '', '', '', '', 3);
$objTable->endRow();

if (!empty($users)) {
    foreach ($users as $user) {
        //prepare add link
        $link = new link($this->URI(array('action' => 'compose', 'userId' => $user['userid'])));
        $link->link = 'Add Recipient';

        $objTable->startRow();
        $objTable->addCell('<p>' . $this->objUser->fullname($user['userid']) . '</p><p>' . $link->show() . '</p>', '', '', '', '', '', 3);
        $objTable->endRow();
    }
} else {
    $objTable->startRow();
    $objTable->addCell('<i>No Users found.</i>', 'center', 'center', '', '', '', 3);
    $objTable->endRow();
}
echo $objTable->show();
echo '<br/><div style="border: 1px solid #808080; padding: 0px;margin-left: 5px;margin-right: 5px;"></div><br/>';
$backLink = new link($this->URI(array('action' => 'compose')));
$backLink->link = $this->objLanguage->languageText('word_back', 'system');
echo $this->homeAndBackLink . ' - ' . $backLink->show() . '</br>';
?>
