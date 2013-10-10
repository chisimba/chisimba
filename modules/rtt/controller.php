<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

// end security check
/**
 * Realtime Controller
 * This class controls all functionality to run the realtime module.
 * @package realtime
 * 
 */
class rtt extends controller {

    function init() {

        $this->objUser = $this->newObject('user', 'security');
        $this->objRttUtil = $this->getObject('rttutil', 'rtt');
        $this->objDbRtt = $this->getObject('dbrtt', 'rtt');
        $this->objDbRttUser = $this->getObject('dbrttusers', 'rtt');
        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objAltConfig = $this->getObject('altconfig', 'config');
        $this->objDBRttJnlp = $this->getObject("dbrttjnlp");
    }

    public function dispatch($action) {
        $demomode = array('demo', 'joindemo');
        if (in_array($action, $demomode)) {
            
        } else {
            if (!$this->objContext->isInContext()) {
                //       return "needtojoin_tpl.php";
            }
        }


        /*
         * Convert the action into a method (alternative to
         * using case selections)
         */
        $method = $this->getMethod($action);
        /*
         * Return the template determined by the method resulting
         * from action
         */
        return $this->$method();
    }

    /**
     *
     * Method to convert the action parameter into the name of
     * a method of this class.
     *
     * @access private
     * @param string $action The action parameter passed byref
     * @return string the name of the method
     *
     */
    function getMethod(& $action) {
        if ($this->validAction($action)) {
            return '__' . $action;
        } else {
            return '__home';
        }
    }

    /**
     *
     * Method to check if a given action is a valid method
     * of this class preceded by double underscore (__). If it __action
     * is not a valid method it returns FALSE, if it is a valid method
     * of this class it returns TRUE.
     *
     * @access private
     * @param string $action The action parameter passed byref
     * @return boolean TRUE|FALSE
     *
     */
    function validAction(& $action) {
        if (method_exists($this, '__' . $action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Takes us to default home page
     * @return <type>
     */
    function __home() {
        $this->objRttUtil->checkSipParams();
        return "home_tpl.php";
    }

    /**
     * Launches the demo mode
     */
    function __demo() {
        //$this->setVar('pageSuppressBand', TRUE);
        // $this->setVar('suppressFooter', TRUE);
        // $this->setVar('pageSuppressToolbar', TRUE);
        return "demohome_tpl.php";
    }

    function __joindemo() {
        /* $this->setVar('pageSuppressBanner', TRUE);
          $this->setVar('suppressFooter', TRUE);
          $this->setVar('pageSuppressToolbar', TRUE); */
        $nickname = $this->getParam("name");
        $username = $this->objRttUtil->genRandomString();
        // $nickname="demo";
        //$username="demo";
        $this->objRttUtil->generateDemoJNLP($nickname, $username);
        $this->setVarByRef("username", $username);
        return "launchdemo_tpl.php";
    }

    function __restservice() {
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="RTT Realm"');
            header('HTTP/1.0 401 Unauthorized');

            exit;
        } else {
            $username = $_SERVER['PHP_AUTH_USER'];
            $password = $_SERVER['PHP_AUTH_PW'];
            if ($this->objDbRttUser->authenticateUser($username, $password)) {
                $params = $this->objDBRttJnlp->getParams($username);

                $result = "";
                foreach ($params as $param) {
                    //if ($param['jnlp_key'] = ! '' || $param['jnlp_value'] != '') {
                    $result.=$param['jnlp_key'] . '=' . $param['jnlp_value'] . '!';
                    //}
                }
                echo $result;
            } else {
                header('WWW-Authenticate: Basic realm="RTT Realm"');
                header('HTTP/1.0 401 Authentication Failed');
            }
        }

        die();
    }

    function __runjnlp() {
        $this->objRttUtil->writeVideoAppJNLP();
        return $this->objRttUtil->runJNLP();
    }

    function __voiceapp() {
        return $this->objRttUtil->generateVoiceAppJNLP();
    }

    /**
     * Method to turn off login requirement for certain actions
     */
    public function requiresLogin($action) {
        $requiresLogin = array('demo', 'joindemo', 'restservice');
        if (in_array($action, $requiresLogin)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

}

?>
