<?php
/* ------------iconrequest class extends controller ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
* Module to provide default pre-login environment
* @category Chisimba
* @package gadgets
* @author Wesley Nitsckie
* @copyright GNU/GPL UWC 2006
*/

class gadgets extends controller {

	

	/**
	 * The language management object
	 *
	 * @var object
	 */
	public $objLanguage;

	/**
	 * Standard Chisimba init function
	 *
	 */


	public function init() {
		try {
				//this->objConfig = $this->getObject('altconfig', 'config');
			$this->objBlocks = $this->getObject('blocks','blocks');
			//print $this->objConfig->getModulePath();
		} catch (customException $e) {
			customException::cleanUp();
		}
	}

	/**
	 * Standard Chisimba dispatch function
	 *
	 * @return string The template to display
	 */
	public function dispatch($action) {
		try {
				
				$leftMenu = $this->objBlocks->showBlock('gtalk','gadgets', 'none');
				$leftMenu .= $this->objBlocks->showBlock('todo','gadgets', 'none');
				
				$right = $this->objBlocks->showBlock('weather','gadgets', 'none');
				$right .= $this->objBlocks->showBlock('googledocs','gadgets', 'none');
				//$right .= $this->objBlocks->showBlock('twitter','gadgets', 'none');
				$this->setVar('left', $leftMenu);
				$this->setVar('right',$right);
			$this->setLayoutTemplate('layout_tpl.php');
			return 'default_tpl.php';
		} catch (customException $e) {
			customException::cleanUp();
		}
	}

	
}

?>