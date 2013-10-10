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
class block_date extends object
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
    		$str = '<script src="http://www.gmodules.com/ig/ifr?url=http://artinbastani.googlepages.com/facebook.xml&amp;synd=open&amp;w=420&amp;h=400&amp;title=&amp;border=0&amp;output=js"></script>';
			return $str;
    	} catch (customException $e) {
    		customException::cleanUp();
    	}
    }
}


