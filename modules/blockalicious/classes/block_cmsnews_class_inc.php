<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* 
* The class provides a hello world block to demonstrate
* how to use blockalicious
*
* @author Derek Keats
*
*/
class block_cmsnews extends object
{
    public $title;
    public $objLanguage;
    public $messageForBlock;
    
    /**
    * Constructor for the class
    */
    function init()
    {
    	//Instantiate the language object
    	$this->objLanguage = $this->getObject('language', 'language');
        $this->modules= $this->getObject('modules','modulecatalogue');
        if ($this->modules->checkIfRegistered('cmsadmin')){
            $this->_objContent = $this->getObject('dbcontent', 'cmsadmin');
            $titlesArr = $this->_objContent->getTitles("News");
        } else {
            $titlesArr=array();
        }
        $this->messageForBlock = "";
        if (count($titlesArr == 0 )) {
            $this->messageForBlock = $this->objLanguage->languageText("mod_blockalicious_cmsnews_norecords", "blockalicious");
	} else {
	    foreach ($titlesArr as $item) {
	        $this->messageForBlock .= $item['title'] . "<br />";
	    }
	}
        //Set the title - 
        $this->title=$this->objLanguage->languageText("mod_blockalicious_cmsnews_title", "blockalicious");
    }
    
    /**
    * Method to output a block with all news stories
    */
    function show()
	{
       return $this->messageForBlock;
    }
}
?>
