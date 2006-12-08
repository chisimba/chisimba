<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* 
* A block class to produce a language chooser
*
* @author Nic Appleby
* 
* $Id$
*
*/
class block_w3link extends object
{
    /**
    * @var string $title The title of the block
    */
    public $title;
    
    /**
     * Varaible to override the block type;
     *
     * @var string
     */
    public $blockType;
    
    /**
    * @var object $objLanguage String to hold the language object
    */
    private $objLanguage;

    /**
    * Standard init function to instantiate language object
    * and create title, etc
    */
    public function init()
    {
    	try {
    		$this->objLanguage = & $this->getObject('language', 'language');
    		$this->blockType = 'none';
    	} catch (customException $e) {
    		customException::cleanUp();
    	}
    }
    
    /**
    * Standard block show method. It uses the renderform
    * class to render the login box
    */
    public function show()
    {
    	try {
    		$icon = &$this->getObject('geticon','htmlelements');
    		$icon->alt = "Valid XHTML 1.0 Transitional";
    		$icon->extra = ' height="31" width="88"';
    		return "<center>".$icon->getLinkedIcon("http://validator.w3.org/check?uri=referer",'valid-xhtml10','png')."</center>";
    	} catch (customException $e) {
    		customException::cleanUp();
    	}
    }
}
?>