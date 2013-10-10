<?php

/**
 * renders custom login frame
 *
 * @author davidwaf
 */
class block_login extends object {

    function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->title = $this->objLanguage->languageText("word_login", "system");
    }

    function show() {
        $loginInterface = $this->newObject('logininterface', 'security');
        $objUser = $this->getObject('user', 'security');
        $objAltConfig = $this->getObject('altconfig', 'config');
        if ($objUser->isLoggedIn()) {
            header('Location: ' . $objAltConfig->getsiteRoot());
        } else {
            return $loginInterface->renderLoginBox('oer');
        }
    }

}

?>
