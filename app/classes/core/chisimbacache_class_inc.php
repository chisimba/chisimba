<?php

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

class chisimbacache extends Memcache
{
	static private $objMem = NULL;
	
	static function getMem($servers = array())
	{
		if(self::$objMem == NULL)
		{
			self::$objMem = new Memcache;
			//connect to the memcache server(s)
			self::$objMem->addServer('localhost', 11211);
			//self::$objMem->addServer('localhost', 11212);
			//self::$objMem->addServer('localhost', 11213);
			//self::$objMem->addServer('localhost', 11214);
			//self::$objMem->addServer('localhost', 11215);
			//self::$objMem->addServer('localhost', 11216);
		}
		
		return self::$objMem;
	}
}
?>