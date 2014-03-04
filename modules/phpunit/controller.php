<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
	die("You cannot view this page directly");
}
// end security check

/**
 *
 * The controller that extends the base controller for the phpunit 
 *
 * @package phpunit
 * @category chisimba
 * @copyright AVOIR
 * @license GNU GPL
 * @author Charl Mert <charl.mert@gmail.com>
 *
 */

class phpunit extends controller
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
	 * @var string object $objGenerator The object that handles the code generation.
	 * @access protected
	 *
	 */
	protected $objGenerate;


	/**
	 * Code Analyzer Object
	 *
	 * @var object
	 */
	public $objCodeAnalyzer;

	/**
	 * objMdb2
	 * Contains the MDB2 analyzer that logically extracts Data Managment methods agaist the MDB2 implementation.
	 *
	 * @var object
	 */
	public $objMdb2;

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

			$this->objCodeAnalyzer = $this->getObject('codeanalyzer', 'phpunit');
			$this->objMdb2 =$this->getObject('cp_mdb2', 'phpunit');
			$this->objQuery = $this->getObject('jquery', 'jquery');
			$this->objUi = $this->getObject('ui', 'phpunit');
			$this->objGenerate = $this->getObject('generate', 'phpunit');
			$this->objLanguage =  $this->newObject('language', 'language');
			$this->objConf = $this->getObject('altconfig', 'config');

			//Get the activity logger class and log this module call
			$objLog = $this->getObject('logactivity', 'logger');
			$objLog->log();

			//Loading phpunit Common Styles
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
				return FALSE;
				break;
		}
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
			$this->setLayoutTemplate('phpunit_layout_tpl.php');
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
	 * This method will return a template with a log of all unit tested modules
	 *
	 * @access private
	 * @return string The populated cms_section_tpl.php template
	 *
	 */
	private function _list()
	{
		return "phpunit_list_tpl.php";
	}

	/**
	 * This method will return the log in JSON notation for input to the jQuery grid
	 *
	 * @access private
	 * @return string The JSON formatted grid
	 *
	 */
	private function _getjsondata()
	{
		$this->setContentType('text/html');
		return "phpunit_json_tpl.php";
	}

	/**
	 * This method will return the ajax form popup specified
	 *
	 * @access private
	 * @return string The JSON formatted grid
	 *
	 */
	private function _getform()
	{
		$this->setContentType('text/html');
		return "phpunit_ajax_forms_tpl.php";
	}

	/**
	 * This method will handle the PHPUnit test case code generation.
	 * The result of this will be a fully logical separation of your classes functions
	 * into a checklist.php, runChecklist.php and a checklisttest.php (with PHPUnit Asserts)
	 *
	 * @access private
	 * @return boolean TRUE if operation successful FALSE if failed.
	 *
	 */
	private function _gentestcase()
	{
		//var_dump($_REQUEST);	
		$moduleName = $this->getParam('mod');
		$modulePath = $this->getParam('modpath');

		//Generate checklist and phpunit test files.
		$this->objGenerate->generateChecklistClass($modulePath, $moduleName);

		return "phpunit_list_tpl.php";
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
		return "phpunit_edit_tpl.php";
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