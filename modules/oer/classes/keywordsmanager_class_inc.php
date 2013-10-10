<?php

/**
 * contains util methods for managing keywords
 *
 * @author davidwaf
 */
class keywordsmanager extends object {

    public $objDbKeyWords;
    private $objLanguage;

    function init() {
        $this->objDbKeyWords = $this->getObject('dbkeywords', 'oer');
        $this->objLanguage = $this->getObject('language', 'language');
    }

    /**
     * Function returns keyword template
     *
     * @return string template
     */
    
    function addNewKeyWord() {
        $errors = array();
        $title = $this->getParam('title');
        if ($title == '') {
            $errors[] = $this->objLanguage->languageText('mod_oer_title', 'oer');
        }
        $umbrellaTheme = $this->getParam("umbrellatheme");
        if (count($errors) > 0) {
            $this->setVar('fieldsrequired', 'true');
            $this->setVar('errors', $errors);
            $this->setVar('title', $title);
            $this->setVar('mode', "fixup");

            return "addeditkeyword_tpl.php";
        } else {

            $this->objDbKeyWords->addKeyWord($title);

            return "keywords_tpl.php";
        }
    }

}

?>
