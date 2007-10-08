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
	
	/**
	 * Singleton method for memcache servers
	 * 
	 * The Servers array should contain arrays of servers (IP and Port)
	 *
	 * @param array $servers
	 * @return memcahed instance
	 */
	static function getMem($servers = array(array('ip' => 'localhost', 'port' => 11211)))
	{
		if(self::$objMem == NULL)
		{
			self::$objMem = new Memcache;
			foreach($servers as $cache)
			{
				self::$objMem->addServer($cache['ip'], $cache['port']);
			}
			//connect to the memcache server(s)
			//self::$objMem->addServer('172.16.65.208', 11211);
            //self::$objMem->addServer('172.16.65.208', 11212);
			//self::$objMem->addServer('172.16.65.208', 11213);
			//self::$objMem->addServer('172.16.65.208', 11214);
			//self::$objMem->addServer('172.16.65.208', 11215);
			//self::$objMem->addServer('172.16.65.208', 11216);
			//self::$objMem->addServer('localhost', 11211);
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
