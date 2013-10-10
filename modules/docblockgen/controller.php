<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}

ini_set("max_execution_time", -1);
// end security check

/**
 * Demonstration module
 * 
 * This is a simple demo module that we will be creating over the next few hours, it will not do much
 * but will be a good example of how to create a module in the Chisimba Framework
 * 
 * @author Paul Scott <pscott@uwc.ac.za>
 * @author somebody else <someone@example.com>
 * @package demo
 * @category chisimba
 * @filesource
 */

class docblockgen extends controller
{
	
	public $objLanguage;
	public $objConfig;
	public $objUser;

	public $objDocblock;
	
	/**
     * Constructor method to instantiate objects and get variables
     */
	public function init()
	{
		try {
			$this->objLanguage = $this->getObject('language', 'language');
			$this->objConfig = $this->getObject('altconfig', 'config');
			$this->objUser = $this->getObject('user', 'security');
			$this->objDocblock = $this->getObject('docblock');
		}
		catch(customException $e) {
			echo customException::cleanUp();
			die();
		}
	}
	/**
     * Method to process actions to be taken
     *
     * @param string $action String indicating action to be taken
     */
	public function dispatch($action = Null)
	{
		switch ($action) {
			default:	
				$mod = $this->getParam('mod');
                if ($mod == '') {
                    return 'error_tpl.php';
                    exit;
                } else {
                    try {
                        $this->objDocblock->genDocs($mod);
                        return 'success_tpl.php';
                    }
                    catch(customException $e) {
                        customException::cleanUp();
                    }
                }
		}
	}
}
?>