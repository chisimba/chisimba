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
class block_nearby extends object
{
    var $title;

    /**
    * Constructor for the class
    */
    function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->title=$this->objLanguage->languageText("mod_twitter_nearby", "twitter");
    }

    /**
    * Method to output a Tweet block
    */
    function show($distance='25km')
    {
        $objRtt = $this->getObject('jqrtt', 'twitter');
        $objRtt->loadRttPlugin();
        $objUserParams = $this->getObject("dbuserparamsadmin","userparamsadmin");
        //This enables the thing to work as a blog plugin
        $objGuess = $this->getObject('bestguess', 'utilities');
        $un = $objGuess->guessUserName();
        if ($un) {
            $objUserParams->setUid($un);
            $objUserParams->readConfig();
            $twitterName = $objUserParams->getValue("twittername");
            $latitude = $objUserParams->getValue("latitude");
            $longitude = $objUserParams->getValue("longitude");
            if ($latitude && $longitude) {
                return $objRtt->loadNearbyDiv($latitude, $longitude, $distance);
            } else {
                return NULL;
            }
        }
    }
}
?>