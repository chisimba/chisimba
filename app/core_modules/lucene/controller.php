<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Controller class for the Lucene implementation of Chisimba
 *
 * @author Paul Scott
 * @copyright AVOIR UWC
 * @access public
 * @package lucene
 */

class lucene extends controller
{
	public $indexer;
	public $indexPath;
	public $index;
	public $doc;
	/**
	 * Constructor
	 */
	public function init()
	{
		// instantiate objects
        try{
			//the language object
        	$this->objLanguage = $this->getObject('language','language');

        }catch (customException $e){
       		echo customException::cleanUp($e);
        	exit();
        }

	}

	/**
	* The Dispatch  methed that the framework needs to evoke the controller
	*/
	public function dispatch()
	{
		try{

			$action = $this->getParam('action');
	        switch ($action){
	            case null:
	            case 'index':

	            	break;
	        }
		}
		catch (customException $e){
       		echo customException::cleanUp($e);
        	exit();
        }
	}
}
?>