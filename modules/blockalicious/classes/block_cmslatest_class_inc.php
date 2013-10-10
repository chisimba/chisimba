<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* 
* The latest content in CMS
*
* @author Derek Keats
*
*/
class block_cmslatest extends object
{
    public $title;
    public $objLanguage;
    public $messageForBlock;
    private $_objContent;
    
    /**
    * Constructor for the class
    */
    function init()
    {
    	//Instantiate the language object
    	$this->objLanguage = $this->getObject('language', 'language');
        $this->modules = $this->getObject('modules','modulecatalogue');
        if ($this->modules->checkIfRegistered('cmsadmin') 
          && $this->modules->checkIfRegistered('cms')){
    	    $this->_objContent = $this->getObject('dbcontent', 'cmsadmin');
	        $titlesArr = $this->_objContent->getLatestTitles(5);
        } else {
            $titlesArr=array();
        }
    	$this->messageForBlock = "";
        $this->expose = TRUE;
    	if (count($titlesArr, 1) == 0 ) {
    	    $this->messageForBlock = $this->objLanguage->languageText("mod_blockalicious_cmslatest_norecords", "blockalicious");
    	} else {
    	    foreach ($titlesArr as $item) {
    	        $id = $item['id'];
        		$title = $item['title'];
        		$paramArray = array('action' => 'showfulltext', 'id' => $id);
        		$url = $this->uri($paramArray, "cms");
        		$link = "<a href=\"" . $url . "\">" . $title ."</a>";
        		$this->messageForBlock .= $link . "<br />";
    	    }
    	}
            //Set the title - 
            $this->title = $this->objLanguage->languageText("mod_blockalicious_cmslatest_title", "blockalicious");
    }
        
    /**
    * Method to output a block with information on how help works
    */
    function show()
    {
       return $this->messageForBlock;
    }
}
?>
