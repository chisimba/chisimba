<?php

/**
 * Short description for file
 * 
 * Long description (if any) ...
 * 
 * PHP version 5
 * 
 * The license text...
 * 
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

// Include the HTML interface class

/**
 * Description for require_once
 */
require_once("ifhtml_class_inc.php");

/**
* Highlight Labels
*
* When designing HTML forms it's important to correlate a field's label with it's input, select or textarea element.
* With this is mind, this page demos some javascript which enhances the UI of a form which uses correctly marked-up inputs and
* labels. The javascript simply highlights a checkbox or radio's corresponding label, giving each label a nice visual clue to
* the selected options.
*
* - Philip Lindsay
*
* This class works with a javascript file "highlightLabels.js" found in the resources folder.
* Another item needed is a CSS class called ".checked"
*
* http://www.xlab.co.uk/weblog/623
*
* @author Tohir Solomons
*         
*         Example:
*         $objHighlightLabels = $this->getObject('highlightlabels', 'htmlelements');
*         echo $objHighlightLabels->show();
*/
class highlightlabels extends object implements ifhtml
{
    /**
    * @var string $css CSS Declaration for the .checked style
    */
    public $css;
    
    /**
    * Constructor
    */
    public function init()
    { 
        // Define a style for the .checked style
        $this->css = '<style type="text/css" title="text/css">
.checked {
	background-color:yellow; border: 1px solid green;
	padding: 3px;
}
</style>';
    }
    
    /**
    * Method to load the highlight labels functionality in a page.
    */
    public function show()
    {
        // Send the JavaScript to the header
        $this->appendArrayVar('headerParams', $this->getJavascriptFile('highlightLabels.js', 'htmlelements'));
        
        // Send the CSS to the header
        $this->appendArrayVar('headerParams', $this->css);
        
        // Setup Body onLoad
        $this->appendArrayVar('bodyOnLoad', 'setUpLabelHighlight();');
        
    }
}
?>