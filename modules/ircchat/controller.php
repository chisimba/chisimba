<?php
/* -------------------- stories class extends controller ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Module class to serve the real-time communication tools
 *
 * @author Nic Appleby
 * @author Jeremy O'Connor
 *
 */
class ircchat extends controller
{
    /*
    Variables for creating the user, language object, etc
    */
    var $objUser;
    var $objLanguage;

    /**
     * Intialiser for the realtime object
    */
    function init()
    {
        $this->objUser = &$this->getObject('user','security');
        $this->objLanguage = &$this->getObject('language','language');
        $this->setLayoutTemplate('layout_tpl.php');

    }
    /**
     * *The standard dispatch method for the module. The dispatch() method must
     * return the name of a page body template which will render the module
     * output (for more details see Modules and templating)
     */
    function dispatch($action)
    {
        // retrieve the mode from the querystring
        switch ($action) {
            case 'enabled':
                return "chat_tpl.php";
            case 'notenabled':
                return "nochat_tpl.php";
            default:
                return "default_tpl.php";
        }
    }

    function requiresLogin() {
        return FALSE;
    }
}
?>