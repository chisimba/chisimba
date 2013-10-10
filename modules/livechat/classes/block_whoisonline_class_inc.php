<?php

class block_whoisonline extends object {

    function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objAltConfig = $this->getObject('altconfig', 'config');
        $this->objLoggedInUsers=  $this->getObject("loggedinusers","security");
        $this->title = "Who is online"; // $this->$objLanguage->languageText('mod_livechat_title', 'livechat', 'Live Chat');
    }

    function show() {
        return "Disabled";//$this->objLoggedInUsers->getActiveUserCount().' users online';
    }

}
?>
