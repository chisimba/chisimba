<?php

/**
 * renders the most adapted, rated and commented product
 *
 * @author davidwaf
 */
class block_mostarc extends object {

    function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->title = $this->objLanguage->languageText('mod_oer_most', 'oer');
    }

    function show() {
        $objProductManager = $this->getObject("productmanager", "oer");
        return $objProductManager->getMostARC();
    }

}

?>
