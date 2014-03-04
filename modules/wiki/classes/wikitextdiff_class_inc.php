<?php
/* ----------- wikitextdiff class extends object ----------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class wikiTextParser to abstract the Text_Wiki PEAR package so that it can be used in Chisimba
* 
* @author Kevin Cyster 
* @package wiki
* @subpackage Text_Diff PEAR package author Geoffrey T. Dairiki
*/
class wikitextdiff extends object 
{ 
    /**
    * @var object $objLanguage: The language class in the language module
    * @access public
    */
    public $objLangauge;
    
    /**
    * Method to check if Text_Diff PEAR package is installed and define the class
    * 
    * @access public
    * @return 
    */
    public function init()
    {
    	$this->objLanguage = $this->getObject('language', 'language');
         $errorLabel = $this->objLanguage->languageText('mod_wiki_missingpear_1', 'wiki');
        
        if (!@include_once('Text/Diff.php')) {
    		throw new customException($errorLabel);
    		return FALSE;
    	}
        if (!@include_once('Text/Diff/Renderer/inline.php')) {
    		throw new customException($errorLabel);
    		return FALSE;
    	}
    }
    
    /**
    * Method to get the diff between two sets of text
    *
    * @access public
    * @param array $previous: The previous text
    * @param array $current: The final text
    * @return array $diff: The differences
    */
    public function getDiffs($previous, $current)
    {
        $linesToDiff = array();
        $linesToDiff[] = $previous;
        $linesToDiff[] = $current;
        
        $this->objDiff = new Text_Diff('auto', $linesToDiff);
        
        $this->objRender = new Text_Diff_Renderer_inline();
        
        $textDiff = $this->objRender->render($this->objDiff);
        
        return $textDiff;
    }
} 
?>