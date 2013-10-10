<?php
/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check

 /**
 * 
 * @package skinsuploader
 * @copyright 2007, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @author Jarrett Jordaan
 * @version $Id: 
 **/
class skinsuploader extends controller 
{
	/**
	 * File handler
	 *
	 * @var object
	*/
	public $objConf;

	/**
	 * Unzip handler
	 *
	 * @var object
	*/
	public $objUnzipFile;

	/**
	 * Language item handler
	 *
	 * @var object
	*/
	public $objLanguage;

	/**
	 * The constructor
	 */
	public function init()
	{
		$this->objConf = & $this->getObject('altconfig','config');
		$this->objLanguage = & $this->newObject('language', 'language');
		$this->objUnzipFile = & $this->newObject('unzipskins','skinsuploader');
	}
    

	/**
	 * The standard dispatch function
	 */
	public function dispatch()
	{
		$action = $this->getParam('action');
        	//echo $action;
		switch ($action)
		{
	        case 'default':
			$this->setLayoutTemplate('uploadSkin_tpl.php');

			return 'uploadSkin_tpl.php';

		case 'uploadSkin':

			return $this->nextAction($this->objUnzipFile->doAll($_FILES));

		case 'error':
			$this->setLayoutTemplate('filerreaderror_tpl.php');

			return 'filerreaderror_tpl.php';

		case 'success':
			$this->setLayoutTemplate('success_tpl.php');

			return 'success_tpl.php';

		case 'filewriterror':
			$this->setLayoutTemplate('filewriteerror_tpl.php');

			return 'filewriteerror_tpl.php';

		default:
			return $this->nextAction('default');
        	}
    	}

}
