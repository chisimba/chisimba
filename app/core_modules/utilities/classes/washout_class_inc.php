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
    * @var string array $classes An array to hold all the parser classes
    * @access public
    *
    */
    public $classes;

    /**
    *
    * @var string object $bbcode String to hold the bbcode parser object
    * @access public
    *
    */
    public $bbcode;

    /**
    *
    * @var string object $objUrl String to hold the url parser object
    * @access public
    *
    */
    public $objUrl;

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
        	$this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
            $this->useFilters = $this->objSysConfig->getValue ( 'usefilters', 'utilities' );
            if ($this->useFilters != 'yes') {
            	return;
            }
            $objFilters = $this->getObject('filterinfo', 'filters');
            
            $parsers = $objFilters->getFilters();
            foreach ($parsers as $parser)
            {
                $this->classes[] = str_replace("_class_inc.php", "", $parser);
            }
            $this->bbcode = $this->getObject('bbcodeparser', 'utilities');
            $this->objUrl = $this->getObject('url', 'strings');
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
    public function parseText($txt, $bbcode = TRUE, $excluded=NULL)
    {
    	//check the configs if the filters are enabled
    	  if ($this->useFilters != 'yes') {
        	return $txt;
        }
        //find all filters that match the format [FILTERNAME*]*[/FILTERNAME] where * is anything and replace
        $txt = preg_replace('/\\[(\w+)(.*?)\\](.*?)\\[\/\\1\\]/e',"\$this->getHTML('\\0', '\\1')", $txt);
        $txt = preg_replace('/\\[(\w+)(.*?)\\]/e', "\$this->getHTML('\\0', '\\1')", $txt);
              
        //all the filters that don't conform to [FILTERNAME*]*[/FILTERNAME]
        //manually execute them
        
        $class =  $this->getObject('parse4smileys', 'filters');
        $txt = $class->parse($txt);
        
        $class =  $this->getObject('parse4chiki', 'filters');
        $txt = $class->parse($txt);
        
        $class =  $this->getObject('parse4format', 'filters');
        $txt = $class->parse($txt);
        
        $class =  $this->getObject('parse4kngtext', 'filters');
        $txt = $class->parse($txt);
        
        $class =  $this->getObject('parse4wikipediawords', 'filters');
        $txt = $class->parse($txt);
        
        $txt = $this->bbcode->parse4bbcode($txt);
        // comment it out for now
        //return $this->objUrl->tagExtLinks($this->objUrl->makeClickableLinks($txt));
        //return $this->objUrl->makeClickableLinks($txt);
        return $txt;
    }
    
    
    
    /**
    * 
    * Checks the parser array if the $classname parser exists
    * 
    * @param string $classname THe name of the class being checked
    * @return boolean TRUE | FALSE
    * @access private
    * 
    */
    private function parserExists($classname) { //bisection search here would be faster as $this->classes is already sorted alphabetically
        if ($this->classes) { 
	       foreach ($this->classes as $existingClassname) {
	           if ($classname == $existingClassname) {
	               return true;
	           }
	       }
        }
        return false;
    }
    
    
    
    /**
    * 
    * Method used from within preg_replace. Sends an individual filter tag to the corresponding 
    * filter class
    * 
    * @param string $filterCode the filter code for the filter that appears in the brackets
    * @param string $filterName the name of the Filter (after parse4 in the filename)
    * @access public
    * @return string The filter code parsed.
    *
    */
    public function getHTML($filterCode, $filterName) {
        if ($this->parserExists('parse4' . strtolower($filterName))) {
            $objCurrentParser = $this->getObject('parse4' . strtolower($filterName), 'filters');
            //replace the entire filter with whatever the parser class specifies.
            return $objCurrentParser->parse(" " . $filterCode . " "); 
        } else {
            return $filterCode;
        }
    }
}
?>