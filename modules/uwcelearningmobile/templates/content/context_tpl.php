<?php

//Content of a context tamplates
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('link', 'htmlelements');
echo '<b>&nbsp;' . ucWords($this->objLanguage->code2Txt('mod_uwcelearningmobile_wordcourse', 'uwcelearningmobile')) . ': </b>' . $conexttitle;
$objFields = new fieldset();
$objFields->setLegend('<b>' . ucWords($this->objLanguage->code2Txt('mod_uwcelearningmobile_wordcoursetools', 'uwcelearningmobile')) . '</b>');

foreach ($tools as $tool) {
    $toolLink = new link($this->URI(array('action' => $tool)));
    $toolLink->link = ucwords($this->objLanguage->code2Txt('mod_' . $tool . '_name', $tool));
    $objFields->addContent('<p>' . $toolLink->show() . '</p>');
}
echo $objFields->show();
echo $this->homeAndBackLink;
?>
