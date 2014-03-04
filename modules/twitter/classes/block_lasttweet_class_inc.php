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
class block_lasttweet extends object
{
    public $title;

    /**
    * Constructor for the class
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        $this->title=$this->objLanguage->languageText("mod_twitter_lasttweet", "twitter");
    }

    /**
    * Method to output a lastTweet block
    */
    public function show()
    {
        $objUserParams = $this->getObject("dbuserparamsadmin","userparamsadmin");
        //This enables the thing to work as a blog plugin
        $objGuess = $this->getObject('bestguess', 'utilities');
        $un = $objGuess->guessUserName();
        if ($un) {
            $objUserParams->setUid($un);
            $objUserParams->readConfig();
            $userName = $objUserParams->getValue("twittername");
            $password = $objUserParams->getValue("twitterpassword");
            if ($userName!==NULL && $password !==NULL) {
                $objTwitterRemote = $this->getObject('twitterremote', 'twitter');
                $objTwitterRemote->initializeConnection($userName, $password);
                return $objTwitterRemote->showStatus(TRUE, FALSE);
            } else {
                return $this->objLanguage->languageText("mod_twitter_nologonshort", "twitter");
            }
        } else {
            return $this->objLanguage->languageText("mod_twitter_cannotguess", "twitter");
        }
    }
}
?>