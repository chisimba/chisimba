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
class block_todo extends object
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
    		$str = '<script src="http://www.gmodules.com/ig/ifr?url=http://www.labpixies.com/campaigns/todo/todo.xml&amp;up_todos=&amp;up_saved_tasks=&amp;synd=open&amp;w=220&amp;h=230&amp;title=&amp;lang=all&amp;country=ALL&amp;border=%23ffffff%7C3px%2C1px+solid+%23999999&amp;output=js"></script>';
			return $str;
    	} catch (customException $e) {
    		customException::cleanUp();
    	}
    }
}


