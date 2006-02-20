<?php
/* -------------------- security class extends module ----------------*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* Module class to handle displaying the module list
*
* @author Sean Legassick
*
* $Id$
*/
class security extends controller
{
    var $objUser;
    var $objLanguage;

    function init()
    {
        $this->objUser =& $this->getObject('user');
        $this->objLanguage =& $this->getObject('language','language');
        //Get an instance of the skin
        $this->objSkin = &$this->getObject('skin', 'skin');
        $this->objConfig = &$this->getObject('config', 'config');
        $this->setLayoutTemplate(NULL);
    }

    function requiresLogin($action)
    {
        return $action != 'showlogin' && $action != 'login' && $action != 'error';
    }

    function dispatch($action)
    {
        switch ($action) {
        case 'login':
            return $this->doLogin();
        case 'logoff':
            return $this->doLogoff();
        case 'error':
            return $this->errorMessages();
        default:
        case 'showlogin':
            return $this->showPreLoginModule();
        }

    }

    /**
    * Login method, handles login logic.
    * @return string Name of template to display
    */
    function doLogin()
    {
        $username = $this->getParam('username', '');
        $password = $this->getParam('password', '');

        if ($this->objUser->authenticateUser($username, $password)) {

            // we hold off creating a new session until successful
            // (only is we didn't already have a session on the go,
            //  as if so it will already have been started in index.php)
            if (!isset($_REQUEST[session_name()])) {
                $this->objEngine->sessionStart();
            }
            else {
                // php version must be >=4.3.3 for this to work
                session_regenerate_id();
            }
            $this->objUser->storeInSession();

            //Validate the current skin Session or set it if not present
            //Skin is also passed as a hidden input
            $this->objSkin->validateSkinSession();

            // Redirect to logged in page so that user can refresh it
            // without being hassled by browser about resubmitting
            // form details
            return $this->nextAction(NULL, NULL, '_default');
        }
        if (defined('STATUS')&& STATUS=='inactive'){
            //user account is inactive. Contact the SysAdmin if you need it re-enabled.
            // still to be developed
            return $this->nextAction('error', array('message'=>'inactive'));
        } else {
            // unsuccessful authentication of user

            // Further checks to support the user
            // Check if the username exists
            if ($this->objUser->valueExists('username', $username)) {
                $message = 'wrongpassword'; // send a message that the password was wrong
            } else {
                $message = 'noaccount'; // send a message that the username doesn't exist
            }
            // Check for LDAP error
            if ($this->getSession('ldaperror')=='FAIL'){
                $this->setSession('ldaperror','');
                $message = 'no_ldap'; // send a message that the LDAP server cannot be contacted.
            }
            return $this->nextAction('error', array('message'=>$message));
        }
    }

    /**
     * Logoff method, handle logoff logic.
     * @return string Name of template to display
     */
    function doLogoff()
    {
        $this->objUser->logout();
        return $this->showPreLoginModule();
    }

    /**
    * Method to show the Pre Login Module
    */
    function showPreLoginModule()
    {
        // Validate the skin, checks if it exists or changed
        $this->objSkin->validateSkinSession();

        return $this->nextAction(NULL, NULL, $this->objConfig->getValue('KEWL_PRELOGIN_MODULE'));
    }

    function errorMessages()
    {
        $this->setVar('pageSuppressToolbar', TRUE);
        return 'error_message.php';
    }



}

?>