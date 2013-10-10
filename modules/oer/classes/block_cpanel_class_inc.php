<?php

/**
 * creates the control panel
 *
 * @author davidwaf
 */
$this->loadClass('link', 'htmlelements');

class block_cpanel extends object {

    public $objLanguage;
    public $objConfig;

    function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->title="";
    }

    function show() {
       $objCPanel=$this->getObject("cpanel", "oer");
       return $objCPanel->createPanel();
    }

}

?>
