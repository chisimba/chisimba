<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
*  
* A class to run filters on text before display. This will call 
* all common parsers to parse audio links to display the file, etc.
* 
* It is called a washout because it id designed to parse the
* what comes out of the washing machine.
*
* @author Derek Keats
* @author Paul Scott
* @copyright UWC and AVOIR under the GPL
*/
class washout extends object
{
	/**
	* 
	* @var array parsers 
	* @access public
	*  
	*/
	public $classes;
	
	public $bbcode;

	/**
	 * Constructor method, builds an array of standard parsers,
	 * ones that for legacy reasons do not live in the outputparsers
	 * module.
	 *
	 * @access public
	 * @param void
	 * @return exception on error
	 * 
	 */
	public function init()
	{
		try {
			$this->objConfig = $this->getObject('altconfig', 'config');
			//load up all of the parsers from filters
			$filterDir = $this->objConfig->getsiteRootPath() . "core_modules/filters/classes/";
			chdir($filterDir);
			$parsers = glob("parse4*_class_inc.php");
			foreach ($parsers as $parser)
			{
				$this->classes[] = str_replace("_class_inc.php", "", $parser);
			}
			$this->bbcode = $this->getObject('bbcodeparser', 'utilities');
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}

	/**
	 * Method to parse the washing 
	 *
	 * @param string $txt
	 * @return string The text after it has been parsed
	 *  
	 */
	public function parseText($txt)
	{
		//Loop over all parsers and run them on $txt
		foreach ($this->classes as $parser) {
			try {
				$currentParser = $parser;
				//The timeline parser needs the timeline module's class
				if($currentParser == 'parse4timeline')
				{
					if(file_exists($this->objConfig->getModulePath() . 'timeline/classes/'))
					{
						$objCurrentParser = $this->getObject($currentParser, 'filters');
					}
					else {
						continue;
					}
				}
				//The simplemap parser needs the simplemap module's class
				if($currentParser == 'parse4simplemap')
				{
					if(file_exists($this->objConfig->getModulePath() . 'simplemap/classes/'))
					{
						$objCurrentParser = $this->getObject($currentParser, 'filters');
					}
					else {
						continue;
					}
				}
				$objCurrentParser = $this->getObject($currentParser, 'filters');
				$txt = $objCurrentParser->parse($txt);
			}
			catch (customException $e)
			{
				customException::cleanUp();
				exit;
			}
		}
		return $this->bbcode->parse4bbcode($txt);
	}
}
?>