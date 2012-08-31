<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Class to generate an ajax tagging input
 *
 * This class generates a text input for tags with support for
 * auto complete, tag suggestions.
 *
 * @author TOhir Solomons
 * @filesource
 * @copyright AVOIR
 * @category chisimbacssId
 * @access public
 */

class ajaxtags extends object
{

    /**
     * Standard init function - Class Constructor
     *
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        $this->loadClass('textinput', 'htmlelements');
        $this->name = 'tags';
        $this->defaultValue = '';
    }
    
    public function show()
    {
        $textinput = new textinput($this->name, $this->defaultValue);
        $textinput->cssId = 'ajaxtags_'.$this->name;
        
        $this->loadJavaScript('ajaxtags_'.$this->name);
        
        return $textinput->show();
    }
    
    private function loadJavaScript($cssId)
    {
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('tag.js'));
        
        $this->appendArrayVar('bodyOnLoad', "
jQuery('#{$cssId}').tagSuggest({
    url: 'index.php',
    separator: ', '
});");
    }
}
?>