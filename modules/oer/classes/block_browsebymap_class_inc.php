<?php

/**
 * renders browse by map 
 *
 * @author davidwaf
 */
class block_browsebymap extends object {

    function init() {
        $objLanguage=  $this->getObject("language", "language");
       
        $this->title = $objLanguage->languageText("mod_oer_browsebymap","oer");
    }

    function show() {
        $mapFactory = $this->getObject("mapfactory", "oer");
        return $mapFactory->getBrowseByMap();
    }

}

?>
