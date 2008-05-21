<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* 
* A wide block class to display the site stories
*
* @author Nic Appleby
* 
* $Id$
*
*/
class block_stories extends object
{
    /**
    * @var object $objLanguage String to hold the language object
    */
    private $objLanguage;
    
    /**
    * @var string $title The title of the block
    */
    public $title;

    /**
    * Standard init function to instantiate language object
    * and create title, etc
    */
    public function init()
    {
    	try {
    		$this->objLanguage =  $this->getObject('language', 'language');
    		$this->title = ucwords($this->objLanguage->code2Txt('word_stories', 'stories', NULL, '[-stories-]'));
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
    		$objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
    		$useSummaries = $objSysConfig->getValue('USESUMMARIES', 'stories');
        	$objStories = $this->getObject('sitestories', 'stories');
        	
        	if($useSummaries == 'Y'){
        		return $objStories->fetchPreLoginCategory('prelogin', 3);
        	} else {
        		return $objStories->fetchCategory('prelogin');
        	}
        } catch (customException $e) {
    		customException::cleanUp();
    	}
    }
}
?>