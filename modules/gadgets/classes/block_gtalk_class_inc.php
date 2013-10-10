<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* 
* A block class to create a gtalk gadget
* Now you can chat to your friends while on a chisimba site
*
* @author Wesley Nitsckie
* 
* 
*
*/
class block_gtalk extends object
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
    		$str = '<script src="http://www.gmodules.com/ig/ifr?url=http://www.google.com/ig/modules/googletalk.xml&amp;synd=open&amp;w=220&amp;h=351&amp;title=&amp;lang=en&amp;country=US&amp;border=0&amp;output=js"></script>';    		return $str;
    	} catch (customException $e) {
    		customException::cleanUp();
    	}
    }
}


