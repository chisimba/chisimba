<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* Module class to display a splashscreen - a prelogin module

*/
class splashscreen extends controller
{
    /**
    *
    */
    var $objSkin;

    /**
    * Constructor for the class
    */
    function init()
    {
        $this->objSkin = &$this->getObject('skin', 'skin');
    }

    /**
    * Method to set login requirement to False
    * Required to be false. prelogin screen
    */
    function requiresLogin()
    {
        return FALSE;
    }

    /**
	* Method to process actions to be taken
    */
    function dispatch()
    {
        // Only one possible action to be taken at the moment - show login screen
        return $this->showSplashScreen();
    }


    /**
	* Method to process the splashscreen and display it
	*/
    function showSplashScreen()
    {
        $skinDetailsFile = $this->objSkin->getSkinUrl().'skinsplashscreeninfo.php';

        // Load JavaScript to validate login and place in header
        $js='lib/javascript/find_validate.js';
        $this->setVar('jsLoad', array($js));

        // Suppress Toolbar - user isn't logged in yet
        $this->setVar('pageSuppressToolbar', TRUE);

        //Suppress IM
        $this->setVar('pageSuppressBanner', TRUE);

        // Set Layout Template To Null
        $this->setLayoutTemplate(NULL);

        // Check if splash screen information file exists
        if (file_exists($skinDetailsFile)) {
            @include($skinDetailsFile); // @ is unnecessary - though all it does is suppress errors
        }

        return 'login_tpl.php';
    }



}

?>