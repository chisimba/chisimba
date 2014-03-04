<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
*
* The controller that extends the base controller for the librarysearch 
*
* @package librarysearch
* @category chisimba
* @copyright AVOIR
* @license GNU GPL
* @author Charl Mert <charl.mert@gmail.com>
*
*/

class librarysearch extends controller
{
       /**
		*
        * @var string object $objLog The object that allows logging of module test case creation
        * @access protected
        *
        */
        protected $objLog;

       /**
		*
        * @var string object $objReport The object that allows reporting of module code errors and optimization targets.
        * @access protected
        *
        */
        protected $objReport;

       /**
        *
        * The standard init method to initialise the  object and assign some of
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

                $this->objQuery = $this->getObject('jquery', 'jquery');
                $this->objLanguage =  $this->newObject('language', 'language');
                $this->objConf = $this->getObject('altconfig', 'config');

                $this->objSources = $this->getObject('dbsources', 'librarysearch');
                $this->objClusters = $this->getObject('dbclusters', 'librarysearch');

                $this->objWorkflow = $this->getObject('workflow', 'webworkflow');

                $this->objLanguage =$this->newObject('language', 'language');
                $this->loadClass('textinput', 'htmlelements');
                $this->loadClass('checkbox', 'htmlelements');
                $this->loadClass('radio', 'htmlelements');
                $this->loadClass('dropdown', 'htmlelements');
                $this->loadClass('form', 'htmlelements');
                $this->loadClass('button', 'htmlelements');
                $this->loadClass('link', 'htmlelements');
                $this->loadClass('label', 'htmlelements');
                $this->loadClass('hiddeninput', 'htmlelements');
                $this->loadClass('textarea','htmlelements');
                $this->loadClass('htmltable','htmlelements');
                $this->loadClass('layer', 'htmlelements');

                //Get the activity logger class and log this module call
                $objLog = $this->getObject('logactivity', 'logger');
                $objLog->log();

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
			return FALSE;
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
                $this->setLayoutTemplate('default_layout_tpl.php');
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
         * This method will return the child items for the particular
         * section for use the the jquery Simple Tree Menu (Ajax)
         *
         * @access private
         * @return string The populated cms_section_tpl.php template
         *
         */
        private function _search()
        {
            $searchKey = $this->getParam('search_key');
            $cluster = $this->getParam('subject_cluster');

            $this->setVarByRef('searchKey', $searchKey);
            $this->setVarByRef('cluster', $cluster);

            //The default clusters will always be searched
            $defaultClusters = $this->objClusters->getDefaultClusters();
            $searchCluster = $this->objClusters->getClusters();
            
            $this->setVarByRef('defaultClusters', $defaultClusters);
            $this->setVarByRef('searchCluster', $searchCluster);
            
            return "librarysearch_result_tpl.php";
        }


        /**
         * This method will return the child items for the particular
         * section for use the the jquery Simple Tree Menu (Ajax)
         *
         * @access private
         * @return string The populated cms_section_tpl.php template
         *
         */
        private function _execworkflow()
        {
            $id = $this->getParam('id');
            $searchKey = $this->getParam('search_key');
            $cluster = $this->getParam('subject_cluster');

            $src = $this->objSources->getSource($id);
            if ($src['workflow'] != '') {
                $wfCode = str_replace("[[[SEARCHTERM]]]", "$searchKey", $src['workflow']);
                $result = $this->objWorkflow->getDocument($wfCode);

                //Scrapping for OIAster
				/*
                if ($src['id'] == 'init_4') {
                    $regex = '/Your search retrieved ([0-9]+) records/';
                    preg_match($regex, $result, $matches);
                    $stats = '<b>' . $matches[1] . '</b>' . ' Results found';
                }
				*/

                //Scrapping for OIAster
                if ($src['id'] == 'init_4') {
                    $regex = '/Your search retrieved ([0-9]+) records/';
                    preg_match($regex, $result, $matches);
                    $stats = '<b>' . $matches[1] . '</b>' . ' Results found';
                }

                //Scrapping for OIAster
                if ($src['id'] == 'init_5') {
                    $regex = '/([0-9]+) books found/';
                    preg_match($regex, $result, $matches);
                    $stats = '<b>' . $matches[1] . '</b>' . ' Results found';
                }

                
                log_debug($result);
				//*/
            }

            $this->setVarByRef('stats',$stats);
            
            $this->setContentType('text/html');
            return "scrape_result_tpl.php";
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
	            return "_search";
	        }
	    }

	    private function _releaselock()
	    {
	    	$this->nextAction('',array('action' => 'search'), 'librarysearch');
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
	
		//Hopefully we'll be able to store the indexes in our very own rdf store.
		//This will help expose our data via XMLRPC interface
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