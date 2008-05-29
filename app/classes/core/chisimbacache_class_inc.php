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
	static private $objServers = array();
	static private $objAPC = NULL;
	
	/**
	 * Singleton method for memcache servers
	 * 
	 * The Servers array should contain arrays of servers (IP and Port)
	 *
	 * @param array $servers
	 * @return memcahed instance
	 */
	static function getMem()
	{
		$servers = self::getServers();
		if(!empty($servers))
		{
			if(self::$objMem == NULL)
			{
				self::$objMem = new Memcache;
				// connect to the memcache server(s)
				foreach($servers as $cache)
				{
					self::$objMem->addServer($cache['ip'], (int)$cache['port']);
				}		
			}
		}
		
		return self::$objMem;
	}
	
	public function getServers()
	{
		$filename = 'cache.config';
		if(!file_exists($filename))
		{
			touch($filename);
			chmod($filename, 0777);
		}
		$handle = fopen($filename, "r");
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    		$num = count($data);
    		for ($c=0; $c < $num; $c++) {
					$cache[] = array('ip' => $data[0], 'port' => $data[1]); 
    		}
		}
		fclose($handle);
		if(empty($cache))
		{
			$cache = array('ip' => 'localhost', 'port' => 11211);
			$cacherec = array($cache);
			$handle = fopen($filename, 'wb');
			foreach($cacherec as $rec)
			{
				fputcsv($handle, $rec);
			}
			fclose($handle);
		}
		return $cache;
	}
}
?>