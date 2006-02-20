<?php
/* -------------------- postlogin class extends controller ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
/**
* Default class to handle what happens after the user logs in.
* The post login module can be set by changing the constant
* KEWL_POSTLOGIN_MODULE from 'postlogin' to the name of any other
* module.
*
* @author Derek Keats
*/
class postlogin extends controller
{
    var $objButtons;
    var $objUser;
    var $objLanguage;
    var $objConfig;
    var $objStories;
    var $objLayers;
    var $objDBContext;
    var $objContext;
    var $layerClass;
    var $objModule;
    var $objForm;

    /**
    * init method to instantiate the class
    */
    function init()
    {
        // Create an instance of the user object
        $this->objUser = &$this->getObject('user', 'security');
        // Create an instance of the module object
        $this->objModule = &$this->getObject('modulesadmin', 'modulelist');
        // Create an instance of the buttons object
        $this->objButtons = &$this->getObject('navbuttons', 'navigation');
        // Create an instance of the language object
        $this->objLanguage = &$this->getObject('language', 'language');
        // Create the configuration object
        $this->objConfig = &$this->getObject('config', 'config');
        // Create an instance of the skin object
        $this->objSkin = &$this->getObject('skin', 'skin');

        $this->ContextAdminUtils = & $this->getObject('contextadminutils', 'contextadmin');

        if ($this->objModule->checkIfRegistered('', 'stories')) {
            // Create an instance of the stories class
            $this->objStories = &$this->getObject('sitestories', 'stories');
        }
        if ($this->objModule->checkIfRegistered('', 'context')) {
            // Create an instance of the dbcontext object
            $this->objDBContext = &$this->getObject('dbcontext', 'context');
            // Create a Context Object
            //$this->objContext= & $this->getObject('contextutil','context');
        }

        $this->loadClass('tabbedbox', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        // Create a Form object
        $this->Form = &$this->newObject('form', 'htmlelements');
    }

    /**
    * Dispatch method to return the template populated with
    * the output
    */
    function dispatch()
    {
        if ($this->getParam('action') == 'leavecontext')
            $this->objDBContext->leaveContext();
        return 'main_tpl.php';
    }
}
?>