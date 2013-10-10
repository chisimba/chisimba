<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* 
* The class provides a show how many users are currently logged in
*
* @author Derek Keats
*
*/
class block_usersonnow extends object
{
    public $title;
    public $objLanguage;
    
    /**
    * Constructor for the class
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->title = $this->objLanguage->languageText(
          "mod_blockalicious_usersontitle", "blockalicious"
        );
        $this->appendArrayVar('headerParams',
          $this->getJavaScriptFile('usercount.js',
          'blockalicious')
        );
    }
    
    /**
    * Method to output block
    */
    public function show()
    {
       return $this->objLanguage->languageText(
         "mod_blockalicious_userson", 
         "blockalicious") . ": <span id='usercount'>" 
         . $this->getData() . "</span>";
    }
    
    /**
    *
    * Get a count of online users
    * 
    * @return string The user count 
    * @access private
    * 
    */
    public function getData()
    {
        $objDb = $this->getObject('loggedinusers', 'security');
        return $objDb->getActiveUserCount();
    }
}
?>