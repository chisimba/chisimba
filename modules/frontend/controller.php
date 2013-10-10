<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of controller
 *
 * @author monwabisi
 */
class frontend extends controller {

        function init() {
                
        }

        function requiresLogin() {
                
        }

        function dispatch($action = null) {
                $this->setLayoutTemplate('frontend_home.php');
                switch ($action) {
                        case NULL:
                                return $this->__home();
                }
        }

        function __home() {
//                $objFrontEnd = $this->getObject('block_frontend_home');
                return 'frontend_default.php';
        }

}

?>
