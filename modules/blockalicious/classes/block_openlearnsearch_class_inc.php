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
class block_openlearnsearch extends object
{
    public $title;
    public $objLanugage;
    
    /**
    * Constructor for the class
    */
    public function init()
    {
    	//Instantiate the language object
    	$this->objLanguage = &$this->getObject('language', 'language');
        //Set the title - 
        $this->title=$this->objLanguage->languageText("mod_blockalicious_openlearnsearch_title", "blockalicious");
    }
    
    /**
    * Method to output a block with information on how help works
    */
    public function show()
	{
		$searchForm="<form method=\"post\" " 
		  . "action=\"http://www3.open.ac.uk/sitesearch/bin/search.dll?SEARCH\">"
		  . "<input type=\"hidden\" name=\"site\" value=\"20060808-144359\"/>"
		  . "<input type=\"tex\" name=\"q\" id=\"q\"/>"
		  . "<input type=\"submit\" class=\"button\" value=\""
		  . $this->objLanguage->languageText("word_go") . "\"/><br />"
		  . "<a href=\"http://openlearn.open.ac.uk/\">"
		  . $this->objLanguage->languageText("mod_blockalicious_openlearnbrowse", "blockalicious")
		  . "</a></form>";
       	return $searchForm;
    }
}
?>