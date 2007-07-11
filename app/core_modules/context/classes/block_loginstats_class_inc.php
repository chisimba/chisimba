<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* The class that demonstrates how to use blocks
*
* @author Derek Keats

* 
* $Id$
*
*/
class block_loginstats extends object
{
    /**
    * @var string $title The title of the block
    */
    public   $title;
    
    /**
    * @var object $objLanguage String to hold the language object
    */
    protected $objLanguage;
    
    /**
    * @var object $objUser String to hold the user object
    */
    protected $objUser;
    
    /**
    * Standard init function to instantiate language and user objects
    * and create title
    */
    public function init()
    {
		//Create an instance of the user object
        $this->objUser =& $this->getObject('user','security');
		//Create an instance of the language object
        $this->objLanguage =& $this->getObject('language','language');
        //Set the title
        $this->title="Login Stats";//$this->objLanguage->languageText("mod_reports_loginstats");
    }
    
    /**
    * Standard block show method. It builds the output based
    * on data obtained via the user class
    */
    public function show()
	{
    
        // Build the display table
        $this->rTable = $this->newObject('htmltable', 'htmlelements'); 
        
        //The number of times the user has logged in
        $this->rTable->startRow();
        $this->rTable->addCell(
          $this->objLanguage->languageText("phrase_numberoflogins") 
          . ":<span class=\"highlight\"> " . $this->objUser->logins() . "</span>");
        $this->rTable->endRow();

        //The date and time of last login
        $lastOnDate = $this->objUser->getLastLoginDate();
        $lastOnDate = date("l, F jS Y - H:i:s", strtotime($lastOnDate));
        $this->rTable->startRow();
        $this->rTable->addCell("<font size=\"-2\"><b>" .
          $this->objLanguage->languageText("phrase_lastlogin") 
          . "</b>: <span class=\"highlight\">" . $lastOnDate . "</span></font>");
        $this->rTable->endRow();

        //The time the current user has been active
        $this->rTable->startRow();
        $this->rTable->addCell("<font size=\"-2\"><b>" .
          $this->objLanguage->languageText("phrase_timeactive") 
          . "</b>: <span class=\"highlight\">" . $this->objUser->myTimeOn() . " "
          . $this->objLanguage->languageText("mod_datetime_mins")
          . "</span></font>");
        $this->rTable->endRow();
        
        //Return the formatted table
        return $this->rTable->show();
    }
}
?>