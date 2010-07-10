<?php
/**
*
* A class to run filters on text before display. This will call
* all common parsers to parse audio links to display the file, etc.
*
* It is called a washout because it id designed to parse the
* what comes out of the 'washing machine'.
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

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
* A class to run filters on text before display. This will call
* all common parsers to parse audio links to display the file, etc.
*
* It is called a washout because it id designed to parse the
* what comes out of the 'washing machine'.
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
    * @var string array $excluded An array of excluded filters
    * @access private
    */
    private $excluded;

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
     * @param string $txt The string to be parsed
     * @param boolean $bbcode Whether or not to parse for BBCODE
     * @param string array $excluded An array of filters to exclude from parsing
     * @return string The text after it has been parsed
     *
     */
    public function parseText($txt, $bbcode = TRUE, $excluded=NULL)
    {
    	// Check the configs if the filters are enabled.
    	if ($this->useFilters != 'yes') {
            return $txt;
        }

        // Set the excluded array as a class property.
        if ($excluded !== NULL) {
            $this->excluded = $excluded;
        }
       
        // Find all filters that match the format [FILTERNAME*]*[/FILTERNAME]
        //    where * is anything and replace.
        $txt = preg_replace('/\\[(\\w+)(\\W([^\\]\\[]*?)|)\\](?s:.*?)(\\[\\/\\1\\])/Ueim',
          "\$this->getHTML('\\0', '\\1')", $txt);
        // @todo Author please explain what this does
        if (PREG_NO_ERROR !== preg_last_error()){
            $this->pcre_error_decode();
        }
        $txt = preg_replace('/\\[(\\w+)(\\W([^\\]\\[]*?)|)\\]/ie',
          "\$this->getHTML('\\0', '\\1')", $txt);
              
        // All the filters that don't conform to [FILTERNAME*]*[/FILTERNAME],
        //  manually execute them.
        
        
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

        $class =  $this->getObject('parse4blocks', 'filters');
        $txt = $class->parse($txt);
        
        $txt = $this->bbcode->parse4bbcode($txt);
        return $txt;
    }

    /**
     *
     * Decode any regular expression errors found.
     *
     * @access private
     * @return VOID
     *
     */
    private function pcre_error_decode()
    {
        switch (preg_last_error()) {
            case PREG_NO_ERROR:
                print "pcre_error: PREG_NO_ERROR!\n";
                break;
            case PREG_INTERNAL_ERROR:
                print "pcre_error: PREG_INTERNAL_ERROR!\n";
                break;
            case PREG_BACKTRACK_LIMIT_ERROR:
                print "pcre_error: PREG_BACKTRACK_LIMIT_ERROR!\n";
                break;
            case PREG_RECURSION_LIMIT_ERROR:
                print "pcre_error: PREG_RECURSION_LIMIT_ERROR!\n";
                break;
            case PREG_BAD_UTF8_ERROR:
                print "pcre_error: PREG_BAD_UTF8_ERROR!\n";
                break;
            case PREG_BAD_UTF8_OFFSET_ERROR:
                print "pcre_error: PREG_BAD_UTF8_OFFSET_ERROR!\n";
                break;
        }
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
    private function parserExists($classname) {
        //bisection search here would be faster as $this->classes is already sorted alphabetically
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
    * @access private
    * @return string The filter code parsed.
    *
    */
    private function getHTML($filterCode, $filterName) {
        $filterName = strtolower($filterName);
        if ($this->shouldParse($filterName)) {
            if ($this->parserExists('parse4' . $filterName)) {
                $objCurrentParser = $this->getObject('parse4' . strtolower($filterName), 'filters');
                //replace the entire filter with whatever the parser class specifies.
                return $objCurrentParser->parse(" " . $filterCode . " ");
            } else {
                return $filterCode;
            }
        } else {
            return $filterCode;
        }
    }

    /**
     *
     * Evaluate whether a given filter should be parsed based on
     * what was supplied in the $excluded parameter of the parseText
     * method.
     *
     * @param string $filterName THe filter to evaluate in lower case
     * @return boolean TRUE | FALSE
     * @access private
     * 
     */
    private function shouldParse($filterName)
    {
        if (isset($this->excluded)) {
            if (!in_array($filterName, $this->excluded)) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return TRUE;
        }
    }
}
?>