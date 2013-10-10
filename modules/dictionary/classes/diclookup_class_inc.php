<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
*
* This is a class to look up definitions of words on dict.org
*
* @author Derek Keats
* @author Jameel Sauls
* @version $Id: diclookup_class_inc.php 12330 2009-02-05 09:21:24Z MusaM $
* @copyright 2005 GNU GPL
*
**/
class diclookup extends object
{

    /**
    *
    * @var array $languages The languages that babelfish supports
    *
    */
    public $languages;

    /**
    *
    * @var string $err The error code
    *
    */
    public $err;

    /**
    *
    * @var string $err The error message
    *
    */
    public $errMsg;

    /**
    *
    * Standard constructor which provides the default language and path
    * information.
    *
    */
    public function init()
    {
        //Instantiate the language object
        $this->objLanguage = & $this->getObject("language", "language");
    }

    /**
    *
    * Method to use CURL to connect to dict.org and do the
    * actual definition lookup
    *
    * @param string $word The word to be looked up
    * @return The definition as string
    *
    */
    public function lookup($word)
    {
        if ($word == '') {
            $this->err=TRUE;
            $this->errMsg = $this->objLanguage->languageText("mod_dictionary_noword");
        } else {
            return $this->getWithCurl($word);
        }
    }

    /**
    *
    * Use the cURL library to connect to dict.org and retrieve
    * the definition. If there is a firewall, the default dict
    * port needs to be open. The default is port 2628 on server
    * dict.org
    *
    *
    */
    public function getWithCurl(& $word)
    {
        // initialise the session
        $ch = curl_init();
        // Set the URL, which includes $word, and is of the dict protocol
        curl_setopt($ch, CURLOPT_URL, "dict://dict.org/d:($word)");
        // Return the output from the cURL session rather than displaying in the browser.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //Execute the session, returning the results to $definition, and close.
        $definition = curl_exec($ch);
        curl_close($ch);
        //return the definition
        return htmlentities("Returned Value");
    }

}

?>
