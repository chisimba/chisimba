<?php

/**
 * Lists current keywords
 *
 * @author davidwaf
 */
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');

class block_keywords extends object {

    public $objLanguage;
    private $objDBKeyWords;

    function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objDBKeyWords = $this->getObject('dbkeywords', 'oer');
        $this->title = "";
    }

    function show() {
        return $this->createKeyWordsListingTable();
    }

    function createKeyWordsListingTable() {

        $header = new htmlheading();
        $header->type = 2;
        $header->str = $this->objLanguage->languageText('mod_oer_keywords', 'oer');

        $cp = '';


        $button = new button('newkeyword', $this->objLanguage->languageText('mod_oer_createkeyword', 'oer'));
        $uri = $this->uri(array("action" => "newkeyword"));
        $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
        $cp.=$button->show() . '&nbsp';

        $button = new button('back', $this->objLanguage->languageText('word_back', 'system'));
        $uri = $this->uri(array());
        $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
        $cp.=$button->show() . '&nbsp';

        $objTable = $this->getObject('htmltable', 'htmlelements');
        $objTable->startHeaderRow();
        $objTable->addHeaderCell($this->objLanguage->languageText('mod_oer_count', 'oer'), "10%");
        $objTable->addHeaderCell(ucfirst($this->objLanguage->languageText('mod_oer_keyword', 'oer')), "90%");

        $objTable->endHeaderRow();

        $keywords = $this->objDBKeyWords->getKeyWords();
        $count = 1;
        foreach ($keywords as $keyword) {
            $objTable->startRow();
            $objTable->addCell($count, "10%");
            $objTable->addCell($keyword['keyword'], "90%");

            $objTable->endRow();

            $count++;
        }
        return $header->show() . $cp . '<br/>' . $objTable->show();
    }

}

?>
