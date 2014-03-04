<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* 
* A block class to view your facebook account
*
* @author Wesley Nitsckie
* 
* 
*
*/
class block_googledocs extends object
{


	public function init()
	{
	
	}
	
	/**
    * Standard block show method. It fetches the gtalk gadget from google
    */
    public function show()
    {
    	try {
    		$str = '<script src="http://www.gmodules.com/ig/ifr?url=http://www.google.com/ig/modules/docs.xml&amp;up_numDocuments=5&amp;synd=open&amp;w=180&amp;h=50&amp;title=Google+Docs&amp;lang=en&amp;country=ALL&amp;border=%23ffffff%7C3px%2C1px+solid+%23999999&amp;output=js"></script>';
			return $str;
    	} catch (customException $e) {
    		customException::cleanUp();
    	}
    }
}


