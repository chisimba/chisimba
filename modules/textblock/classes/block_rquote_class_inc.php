<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* A block to display a random quote
*
* @author Paul Mungai paulwando@gmail.com
*
*
*/
class block_rquote extends object
{
    /**
    * @var string $title The title of the block
    */
    public $title;

    /**
    * @var object $objLanguage String to hold the language object
    */
    public $objLanguage;

    /**
    * Standard init function to instantiate language object
    * and create title
    */
    public function init()
    {
        $this->objLanguage = & $this->getObject("language", "language");
        $this->title=$this->objLanguage->languageText("mod_quotes_rquotetitle","quotes");
        //Create an instance of the database class for this module
        $this->objDbquotes = & $this->getObject("dbquotes");
    }

    /**
    * Standard block show method. It uses the renderform
    * class to render the guestbook interface
    */
    public function show()
	{  try{
            $ar = $this->objDbquotes->getRandom();
            return $ar['quote'] . "<br />&nbsp;&nbsp;--" . $ar['whosaidit'];
        }
       catch (customException $e)
        {
        	echo customException::cleanUp($e);
        	die();
        }
    }
}
?>