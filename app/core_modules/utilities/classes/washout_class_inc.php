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
* @category  Chisimba
* @author Derek Keats
* @author Paul Scott <pscott@uwc.ac.za>
* @copyright UWC and AVOIR under the GPL
* @package   utilities
* @copyright 2007 AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
* @version   $Id$
* @link      http://avoir.uwc.ac.za
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

	/**
	*
	* @var $bbcode
	* @access public
	*
	*/
	public $bbcode;

    /**
    * @var object $objModules: The modules class in the modulecatalogue module
    * @access private
    */
    private $objModules;

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
			$this->objModules = $this->getObject('modules', 'modulecatalogue');
			// save cwd
			$savedDir = getcwd();
			//load up all of the parsers from filters
			$filterDir = $this->objConfig->getsiteRootPath() . "core_modules/filters/classes/";
			chdir($filterDir);
			$parsers = glob("parse4*_class_inc.php");
			// restore path
			chdir($savedDir);
			$mathMlLoaded = $this->objModules->checkIfRegistered('mathml');

			foreach ($parsers as $parser)
			{
				if($parser == 'parse4mathml_class_inc.php'){
				    if($mathMlLoaded != FALSE){
                        $this->classes[] = str_replace("_class_inc.php", "", $parser);
                    }
                }else{
                    $this->classes[] = str_replace("_class_inc.php", "", $parser);
                }
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
                // Note: check for timeline and simplemap removed as it is now in the filter iteself
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