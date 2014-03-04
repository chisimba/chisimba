<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of langadmin
 *
 * @author davidwaf
 */
class langutil extends object {

    function init() {
        $this->objLanguage = $this->getObject('language', 'language');
       
    }
    
    
    function addLanguage($langData) {
      
        $this->objLanguage->addLanguage($langData);
    }

}

?>
