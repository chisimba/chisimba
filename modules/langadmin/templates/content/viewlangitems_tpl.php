<?php
// set up html elements
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->objLangText = $this->getObject("dblangaugetext");
$texts = $this->objLangText->getLanguageTextItems();
$table = $this->getObject('htmltable', 'htmlelements');
$table->startHeaderRow();
$table->addHeaderCell($this->objLanguage->languageText('mod_langadmin_code', 'langid'));
$table->addHeaderCell($this->objLanguage->languageText('mod_langadmin_description', 'langid'));
$table->addHeaderCell($this->objLanguage->languageText('mod_langadmin_translation', 'langid'));
$table->endHeaderRow();
foreach ($texts as $text) {

    $table->startRow();
    $code = $text['code'];
    $link = new link($this->uri(array("action" => "editTranslation", "code" => $code)));

    $link->link = $code;
    $table->addCell($link->show());
    $table->addCell($text['description']);

    $arrName = explode("_", $code);
    $module = $arrName[1];
    if ($module == 'unesco') {
        $module = $module . "_" . $arrName[2];
    }
    $table->addCell($this->objLanguage->languageText($code, $module));
    $table->endRow();
}
?>
<fieldset>
    <?php echo $table->show(); ?>
</fieldset>
?>
