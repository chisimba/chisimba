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
        // Initialize variable
        $doParse = TRUE;
        // Loop over all parsers and run them on $txt.
        foreach ($this->classes as $parser) {
            try {
                $currentParser = $parser;
                // Make sure that there are no exclusions.
                if ($excluded && is_array($excluded)) {
                    $parserPat = strtolower(str_replace("parse4", "", $parser));
                	if (in_array($parserPat, $excluded)) {
                 	    $doParse = FALSE;
                    } else {
                        $doParse = TRUE;
                    }
                } else {
                    $doParse = TRUE;
                }
                if ($doParse == TRUE) {
                    $objCurrentParser = $this->getObject($currentParser, 'filters');
                    $txt = $objCurrentParser->parse($txt);
                }
            }
            catch (customException $e)
            {
                customException::cleanUp();
                exit;
            }
        }
        $txt = $this->bbcode->parse4bbcode($txt);
        // comment it out for now
        //return $this->objUrl->tagExtLinks($this->objUrl->makeClickableLinks($txt));
        //return $this->objUrl->makeClickableLinks($txt);
        return $txt;
    }
}
?>