<?php

//Mobile Prelogin template
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$objFields = new fieldset();
$objFields->setLegend('<b>' . ucWords($this->objLanguage->code2Txt('mod_uwcelearningmobile_wordmycourse', 'uwcelearningmobile')) . '</b>');

if (!empty($usercontexts)) {
    foreach ($usercontexts as $context) {
        $con = $this->dbContext->getContext($context);
        $link = new link($this->URI(array('action' => 'context', 'contextcode' => $con['contextcode'])));
        $link->link = $con['title'];
        $objFields->addContent('<p>' . $link->show() . '</p>');
    }
} else {
    $objFields->addContent(ucWords($this->objLanguage->code2Txt('mod_uwcelearningmobile_wordnocourse', 'uwcelearningmobile')));
}
echo $objFields->show();
//Tools that can be access before you enter the course
$objFieldsTools = new fieldset();

if (!empty($tools)) {
    foreach ($tools as $mod) {
        $link = new link($this->URI(array('action' => $mod)));
        $link->link = ucwords($this->objLanguage->code2Txt('mod_' . $mod . '_name', $mod));
        $objFieldsTools->addContent('<p>' . $link->show() . '</p>');
    }
    echo $objFieldsTools->show();
}
?>
