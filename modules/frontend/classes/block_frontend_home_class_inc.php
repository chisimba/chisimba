<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of block_frontend_home
 *
 * @author monwabisi
 */
class block_frontend_home extends object {
        //put your code here
        var $objDom;
        var $objLanguage;

        function init() {
                $this->title = "This is the developer frontend";
                $this->objLanguage = $this->getObject("language", "language");
        }

        function buildform() {
                $this->objDom = new DOMDocument('utf-8');
                $this->objDom->loadHTMLFile('packages/frontend/resources/index.html');
                return $this->objDom->saveHTML();
        }

        function show() {
                return $this->buildform();
        }

}

?>
