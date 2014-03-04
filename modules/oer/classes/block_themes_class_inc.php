<?php

/**
 * Lists current product themes
 *
 * @author davidwaf
 */


class block_themes extends object {

    function init() {
       $this->title="";
    }

    function show() {
        $objThemeManager=$this->getObject("thememanager","oer");
        return $objThemeManager->createThemeListingTable();
    }

    

}

?>
