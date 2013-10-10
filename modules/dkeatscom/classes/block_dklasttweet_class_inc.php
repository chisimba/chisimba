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
class block_dklasttweet extends object
{
    var $title;

    /**
    * Constructor for the class
    */
    function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        $this->title=$this->objLanguage->languageText("mod_dkeatscom_lasttweet", "dkeatscom");
    }

    /**
    * Method to output a Tweet block
    */
    function show()
	{
        $objCf = $this->getObject('dbsysconfig', 'sysconfig');
        $userName= $objCf->getValue('mod_dkeatscom_twittername', 'dkeatscom');
        $password = $objCf->getValue('mod_dkeatscom_twitterpassword', 'dkeatscom');
        if ($userName!==NULL && $password !==NULL) {
            $objTwitterRemote = $this->getObject('twitterremote', 'twitter');
            $objTwitterRemote->initializeConnection($userName, $password);
            return $objTwitterRemote->showStatus(TRUE, FALSE);
        } else {
            return $this->objLanguage->languageText("mod_twitter_nologonshort", "twitter");
        }
    }
}
?>