<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* A block to return the last 10 podcasts entries
*
* @author Derek Keats
* @author  Jameel Sauls
*
* $Id: block_dictionary_class_inc.php 8017 2008-02-19 13:28:54Z tohir $
*
*/
class block_dictionary extends object
{
    /**
    * @var string $title The title of the block
    */
    public $title;

    /**
    * @var object $objDicIntf String to hold the dictionary interface object
    */
    public $objDicIntf;

    /**
    * @var object $objLanguage String to hold the language object
    */
    public $objLanguage;

    /**
    * Standard init function to instantiate language and user objects
    * and create title
    */
    public function init()
    {
        //Create an instance of the language object
        $this->objLanguage = &$this->getObject("language", "language");
        //add the title
        $this->title = $this->objLanguage->languageText("mod_dictionary_title", "dictionary");
        //Create an instance of the database class for this module
        $this->objDicIntf = & $this->getObject('dicinterface');
    }

    /**
    * Standard block show method. It builds the output based
    * on data obtained
    */
    public function show()
	{
        return  $this->objDicIntf->makeSearch();
    }
}
?>