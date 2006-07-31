<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

// Include the HTML interface class
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
* Example:
*       $objHighlightLabels = $this->getObject('highlightlabels', 'htmlelements');
*       echo $objHighlightLabels->show();
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
    * Method to display the color picker
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