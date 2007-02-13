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
* @copyright UWC and AVOIR under the GPL
*/
class washout extends object
{
	/**
	* 
	* @var array parsers The parsers loaded from the XML file
	* @access public
	*  
	*/
	public parsers;

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
			//create an array of all known standard parsers and their location
			$this->parsers=array(
				'parse4mindmap' => 'filters',
				'parse4mathml' => 'filters',
				'parse4mmedia' => 'filters',
				'parse4referece' => 'filters',
				'parse4smileys' => 'filters',
				'parse4timeline' => 'filters'
			)
			$this->getListing();
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
		foreach ($this->parsers as $parser=>classlocation) {
			try {
				$currentParser = $parser->classname;
				$classLocation = $parser->classlocation;
				$objCurrentParser = $this->getObject($currentParser, $classLocation);
				$txt = $objCurrentParser->show($txt);
			}
			catch (customException $e)
			{
				customException::cleanUp();
				exit;
			}
		}
		return $txt;
	}
	
	/**
    * Get an array of files in a directory and convert to an arry
    * of outputparsers
    * @return   array of classes and locations
    */ 
	function getListing()
	{
	    //Create the config reader and get the location of demo maps
        $objSconfig =  $this->getObject('altconfig', 'config');
        $dirpath =  $objSconfig->getItem('MODULE_URI') . "outputparsers/classes/";
		try {
		    $handle = opendir($dirpath);
		    while (false !== ($file = readdir($handle))) {
	            if ($file != "." && $file != ".." && $file !="CVS") {
	                $arTmp = explode("_", $file);
	                $className = $arTmp[0];
	                array_push($this->parsers, $className => 'outputparsers');
	            }
		    }
		    closedir($handle);
		} 
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	    return $classNameAr;
	}
}
?>