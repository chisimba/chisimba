<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* 
* A block to render a google scholar search form
*
* @author Derek Keats

* 
* $Id: block_scholarg_class_inc.php 4690 2006-11-02 14:10:10Z jameel $
*
*/
class block_scholarg extends object
{
    /**
    * @var string $title The title of the block
    */
    public $title;
    
    /**
    * @var object $objLanguage String to hold the language object
    */
    public $objLanguage;
    
    /**
    * Standard init function to instantiate language object
    * and create title
    */
    public function init()
    {
        $this->objLanguage=&$this->getObject('language', 'language');
        $this->title=$this->objLanguage->languageText("mod_websearch_scholarg", "websearch");
    }
    
    /**
    * Standard block show method. It uses the search
    * class to render the search interface
    */
    public function show()
	{
	    $objGoogle = $this->getObject('search', 'websearch');
        return $objGoogle->renderScholarGoogleForm();
    }
} #class
?>