<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* 
* Brightkite block
*
* @author Autor left DWK as author
*
*/
class block_brightkite extends object
{
    public $title;
    public $objUserParams;
    public $objModules;
    public $objLanguage;
    
    /**
    * Constructor for the class
    */
    public function init()
    {
        // Use this object to check if the feed module is registered.
        $this->objModules = $this->getObject('modules','modulecatalogue');
        // Get userparams object to lookup the brightkite_id
        $this->objUserParams = $this->getObject ('dbuserparamsadmin', 'userparamsadmin' );
        // Get the language object.
        $this->objLanguage = $this->getObject('language', 'language');
        //Set the title - 
        $this->title=$this->objLanguage->languageText("mod_blockalicious_whereami", "blockalicious", "Where am I?");
    }
    
    /**
    *
    * Method to output a block with information on how help works
    * 
    * @return STRING the brightkite feed block
    * @access Public
    * 
    */
    public function show()
    {
       return $this->fetchBrightKiteFeed(4);
    }
    
    /**
    * 
    * Method to fetch the RSS feed from brightkite for the 
    * appropriate user
    * 
    * @param $limit INTEGER The number of of items to return
    * $return STRING The formatted list of updates
    * @access Public
    *
    */
    public function fetchBrightKiteFeed($limit=NULL)
    {
        // Check that the feed module is present and registered, else dont parse the tag
        if ($this->objModules->checkIfRegistered('feed')==FALSE) {
            return $this->objLanguage->languageText("mod_blockalicious_errornofeed", "blockalicious");
        } else {
            $feedUrl = $this->getFeedUrl();
            if (!$feedUrl == NULL) {
                // Grab an instance of the simple pie class from the feed module
                $feed = $this->getObject('spie', 'feed');
                // Set the data to force being a feed (there is a problem with brightkite feeds)
                $feed->forceFeed(TRUE);
                // Set the fields we wish to retrieve
                $fields=array('description', 'pubDate');
                // We have to call a method that will display only certain fields
                $retStr = $feed->getFields($feedUrl, $fields);
                unset($feed);
                return $retStr;
            } else {
                return $this->objLanguage->languageText("mod_blockalicious_errornobkid", "blockalicious");
            }
        }
    }
    
    /**
    * 
    * Method to guess and obtain the usercode
    * 
    * @param $limit INTEGER The number of of items to return
    * @return STRING userID on BrightKite  or NULL if one is not stored or cannot be guessed
    * @access Public
    *
    */
    public function getUserCode()
    {
        //This enables the thing to work as a blog plugin
        $objGuess = $this->getObject('bestguess', 'utilities');
        $un = $objGuess->guessUserName();
        if ($un) {
            $this->objUserParams->setUid($un);
            $this->objUserParams->readConfig();
            $bkUser = $this->objUserParams->getValue ("brightkite_id");
            if ($bkUser!==NULL) {
                return $bkUser;
            } else {
                return NULL;
            }
        } else {
            return NULL;
        }       
    }
    
    
    /**
    * 
    * Method to get the feed
    * 
    * @return STRING the feed from Brightkite or NULL if the UserId is not stored or cannot be guessed
    * @access Public
    *
    */
    public function getFeedUrl()
    {
        $bkUser = $this->getUserCode();
        if ($bkUser!==NULL) {
            return "http://brightkite.com/people/" . $bkUser . "/objects.rss";
        } else {
            return NULL;
        }
    }
}
?>