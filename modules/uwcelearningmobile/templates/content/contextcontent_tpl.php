<?php

//MCQ Tests template
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$newImgPath = $this->getResourceUri('img/new.png', 'uwcelearningmobile');

echo '<b>' . ucWords($this->objLanguage->code2Txt('mod_uwcelearningmobile_wordcourse', 'uwcelearningmobile')) . ': </b>' . $this->contextTitle;

$objFields = new fieldset();
$objFields->setLegend('<b>' . ucWords($this->objLanguage->code2Txt('mod_uwcelearningmobile_wordnewcontent', 'uwcelearningmobile')) . '</b>');

if (!empty($content)) {
    foreach ($content as $con) {
        $link = new link($this->URI(array('action' => 'viewcontextcontent', 'id' => $con['chapterid'])));
        $link->link = $con['chaptertitle'];
        $objFields->addContent('<p>' . $link->show() . ' <img src="' . $newImgPath . '" border="0" alt="New" title="New"></p>');
    }
} else {
    $objFields->addContent($this->objLanguage->code2Txt('mod_uwcelearningmobile_wordnocontent', 'uwcelearningmobile'));
}
echo $objFields->show();
echo $this->homeAndBackLink;
?>
