<?php
// set up html elements
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('link', 'htmlelements');

// setup and show heading
$header = new htmlHeading();
$header->str = $this->objLanguage->languageText('mod_langadmin_title', 'langadmin');
$header->type = 2;
echo '<div id="langcontrols">';
echo $header->show();


//echo $link->show();
$addbutton = new button('addNewLangButton', $this->objLanguage->languageText('mod_langadmin_addlanguage', 'langadmin'));
$uri = $this->uri(array('action' => 'showNewLangTemplate'));
$addbutton->setOnClick('javascript: window.location=\'' . $uri . '\'');


echo $addbutton->show();
$objConfig = $this->getObject("altconfig", "config");
echo '&nbsp;|&nbsp;<a href="' . $objConfig->getsiteRoot() . '/packages/langadmin/resources/ChisimbaLangTranslator/dist/ChisimbaLangTranslator.jar">' . $this->objLanguage->languageText('mod_langadmin_downloadclient', 'langadmin') . '</a>';
echo '</div>';

$table = $this->getObject('htmltable', 'htmlelements');
$table->startHeaderRow();
$table->addHeaderCell($this->objLanguage->languageText('mod_langadmin_langid', 'langadmin'));
$table->addHeaderCell($this->objLanguage->languageText('mod_langadmin_langname', 'langadmin'));
$table->addHeaderCell($this->objLanguage->languageText('mod_langadmin_export', 'langadmin'));
$table->addHeaderCell($this->objLanguage->languageText('mod_langadmin_import', 'langadmin'));
$table->addHeaderCell($this->objLanguage->languageText('mod_langadmin_status', 'langadmin'));
$table->endHeaderRow();

$hide = $this->objLanguage->languageText('mod_langadmin_hide', 'langadmin');
$show = $this->objLanguage->languageText('mod_langadmin_show', 'langadmin');
$langs = $this->objLanguage->getLangs();
$hiddenlangs = $this->getObject("dbhiddenlangs");

foreach ($langs as $id => $name) {

    $table->startRow();
    $table->addCell($id);
    $table->addCell($name);
    $link = new link($this->uri(array("action" => "exportLangItems", "langid" => $id)));
    $link->link = $this->objLanguage->languageText('mod_langadmin_export', 'langadmin');
    $table->addCell($link->show());

    $link = new link($this->uri(array("action" => "uploadFile", "langid" => $id)));
    $link->link = $this->objLanguage->languageText('mod_langadmin_import', 'langadmin');
    $table->addCell($link->show());


    if ($hiddenlangs->isHidden($id)) {
        $link = new link($this->uri(array("action" => "unhide", "langid" => $id)));
        $link->link = $show;
    } else {
        $link = new link($this->uri(array("action" => "hide", "langid" => $id)));
        $link->link = $hide;
    }
    $table->addCell($link->show());


    $table->endRow();
}
?>
<fieldset>
    <?php echo $table->show(); ?>
</fieldset>
<?php
?>
