<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
*
* The controller that extends the base controller for the cms module
*
* @package shorturl
* @category chisimba
* @copyright AVOIR
* @license GNU GPL
* @author Charl Mert
*
*/

class shorturl extends controller
{
        /**
		*
        * @var string object $_objContextCore A string to hold an instance of the contextcore object which ....
        * @access protected
        *
        */
        protected $objMap;

        /**
        *
        * The standard init method to initialise the cms object and assign some of
        * the objects used in all action derived methods.
        *
        * @access public
        *
        */
        public function init()
        {
            try {

                // Supressing Prototype and Setting jQuery Version with Template Variables
                $this->setVar('SUPPRESS_PROTOTYPE', true);
                $this->setVar('SUPPRESS_JQUERY', false);
                $this->setVar('JQUERY_VERSION', '1.2.6');

                // instantiate the database object for sections
                $this->objQuery = $this->getObject('jquery', 'jquery');
                $this->objMap = $this->getObject('dbmap', 'shorturl');
                $this->objUi = $this->getObject('ui', 'shorturl');
                $this->objLanguage =  $this->newObject('language', 'language');
                $this->objConf = $this->getObject('altconfig', 'config');

                //Get the activity logger class and log this module call
                $objLog = $this->getObject('logactivity', 'logger');
                $objLog->log();

                //Loading shorturl Common Styles
                $this->appendArrayVar('headerParams', '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('_common.css'.'">'));

                //Loading the pngFix
                $this->objQuery->loadPngFixPlugin();
        	}
        	catch (customException $e)
        	{
        		customException::cleanUp();
        		exit;
        	}
        }

       /**
        *
        * This is a method that overrides the parent class to stipulate whether
        * the current module requires login. Having it set to false gives public
        * access to this module including all its actions.
        *
        * @access public
        * @return bool FALSE
        */
        public function requiresLogin()
        {
            $action = $this->getParam("action", NULL);
            switch($action){
                case 'redirect':
                    return FALSE;
                break;
    
                default:
                    return TRUE;
                break;
            }
        }



	/**
	 * Method to handle the redirects triggered by the shorturl module
	 *
	 * @author Charl Mert
	 */
	 function redirectTo()
	{
	    //Supressing the template
	    $this->setContentType('text/html');
	    return 'shorturl_redirect_tpl.php';
	}


       /**
        *
        * This is a method that overrides the parent class to stipulate whether
        * the current module requires PHPSESSID header to be sent.
        *
        * This will ensure that the server sends a header with the current PHPSESSID.
        * This module uses that custom header to check the current session via AJAX so
        * that we can handle session expiry properly.
        *
        * @access public
        * @return bool FALSE
        */
        public function sendSessionIdHeader()
        {
            return TRUE;
        }

        /**
         *
         * A standard method to handle actions from the querystring.
         * The dispatch function converts action values to function
         * names, and then calls those functions to perform the action
         * that was specified.
         *
         * @access public
         * @return string The results of the method denoted by the action
         *   querystring parameter. Usually this will be a template populated
         *   with content.
         *
         */
        public function dispatch()
        {
            try {
                $this->setLayoutTemplate('shorturl_layout_tpl.php');
                $this->action = $this->getParam('action','');

            	switch ($this->action) {
            		default:
            			$method = $this->_getMethod();
            			break;
            	}

            } catch (Exception $e) {
                throw customException($e->getMessage());
			    //customException::cleanUp();
			    exit;
		    }
        	/*
        	 * Return the template determined by the method resulting
        	 * from action
        	 */
        	return $this->$method();
        }

        /**
         * This method will redirect the browser to the matching URL
         *
         * @access private
         */
        private function _redirect()
        {   
            //Supressing the template
            $this->setContentType('text/html');
            return 'shorturl_redirect_tpl.php';
        }

        /**
         * This method will return the child items for the particular
         * section for use the the jquery Simple Tree Menu (Ajax)
         *
         * @access private
         * @return string The populated cms_section_tpl.php template
         *
         */
        private function _list()
        {
            return "shorturl_list_tpl.php";
        }

        /**
         * This method will return the Grid's content in JSON notation
         *
         * @access private
         * @return string The JSON formatted grid
         *
         */
        private function _getjsondata()
        {
            $this->setContentType('text/html');
            return "shorturl_json_tpl.php";
        }

        /**
         * This method will return the Grid's content in JSON notation
         *
         * @access private
         * @return string The JSON formatted grid
         *
         */
        private function _getform()
        {
            $this->setContentType('text/html');
            return "shorturl_ajax_forms_tpl.php";
        }


        /**
         * This method will handle the creation AND editing of mappings.
         *
         * @access private
         * @return boolean TRUE if operation successful FALSE if failed.
         *
         */
        private function _editmapping()
        {
	    $mappingId = $this->getParam('id', '');
	    $this->objMap->addEditMapping($mappingId);
	    return "shorturl_list_tpl.php";
	}

        /**
         * This method will handle the deleting of mappings.
         *
         * @access private
         * @return boolean TRUE if operation successful FALSE if failed.
         *
         */
        private function _deletemapping()
        {
	    $mappingId = $this->getParam('id');
	    $this->objMap->deleteMapping($mappingId);
	    return "shorturl_list_tpl.php";
	}

        /**
         * This method will handle the editing for the grid control
         *
         * @access private
         * @return string The populated cms_section_tpl.php template
         *
         */
        private function _edit()
        {
            //This is envoked via AJAX so nothing really needs to be returned as a template.

            $op = $this->getParam('oper','');
            $id = $this->getParam('id','');

            switch ($op){
                case 'del':
                    $this->objMap->deleteMapping($id);
                break;

                case 'add':
                    $this->objMap->addEditMapping();
                break;

                case 'edit':
                    $this->objMap->addEditMapping($id);
                break;

                default:

                break;
            }

            $this->setContentType('text/html');
            return "shorturl_edit_tpl.php";
        }


	    /**
	    *
	    * Method to convert the action parameter into the name of
	    * a method of this class.
	    *
	    * @access private
	    * @param string $action The action parameter
	    * @return stromg the name of the method
	    *
	    */
	    private function _getMethod()
	    {


	        if ($this->_validAction()) {
	            return "_" . $this->action;
	        } else {
                //Returning Default Action
	            return "_list";
	        }
	    }

	    private function _releaselock()
	    {
	    	$this->nextAction('',array('action' => 'viewsection'), 'cmsadmin');
	    }

	    /**
	    *
	    * Method to check if a given action is a valid method
	    * of this class preceded by double underscore (_). If the action
	    * is not a valid method it returns FALSE, if it is a valid method
	    * of this class it returns TRUE.
	    *
	    * @access private
	    * @param string $action The action parameter
	    * @return boolean TRUE|FALSE
	    *
	    */
	    private function _validAction()
	    {
	        if (method_exists($this, "_".$this->action)) {
	            return TRUE;
	        } else {
	            return FALSE;
	        }
	    }

	    /**
	    *
	    * Method to return an error when the action is not a valid
	    * action method
	    *
	    * @access private
	    * @return string The dump template populated with the error message
	    *
	    */
	    private function _actionError()
	    {

	        $this->setVar('str', $this->objLanguage->languageText("mod_cms_errorbadaction", 'cms') . ": <em>". $this->action . "</em>");
	        return 'dump_tpl.php';
	    }


	    private function _serverpc()
	    {
	    	// cannot require any login, as remote clients use this. Auth is done internally
            $this->requiresLogin();

            // start the server.
            $this->objRPC->serve();
            // break to be pedantic, although not strictly needed.
            // break;
	    }

}

?>