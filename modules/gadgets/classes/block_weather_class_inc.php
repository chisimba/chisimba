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
class block_weather extends object
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
    		$str = '<script src="http://www.gmodules.com/ig/ifr?url=http://hosting.gmodules.com/ig/gadgets/file/118365362218057595736/worldweathergadget.xml&amp;up_degree_unit_type=1&amp;up_city_code=800288&amp;up_zip_code=none&amp;synd=open&amp;w=320&amp;h=200&amp;title=&amp;border=%23ffffff%7C3px%2C1px+solid+%23999999&amp;output=js"></script>';
			return $str;
    	} catch (customException $e) {
    		customException::cleanUp();
    	}
    }
}

?>