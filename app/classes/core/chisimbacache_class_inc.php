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
        			$serv = explode('|', $data[$c]);
					$cache[] = array('ip' => $serv[0], 'port' => $serv[1]); 
    		}
		}
		fclose($handle);
		if(empty($cache))
		{
			$cache = array('ip' => 'localhost', 'port' => 11211);
			$cacherec = array("localhost|11211");
			$handle = fopen($filename, 'wb');
			fputcsv($handle, $cacherec);
			fclose($handle);
		}
		/*
		$servarr = //file($filename); //maybe do this as a csv? speed issues?
		if(empty($servarr))
		{
			$cache = array(array('ip' => 'localhost', 'port' => 11211));
			return $cache;
		}
		foreach($servarr as $servers)
		{
			$serv = explode(', ', $servers);
			$cache[] = array('ip' => $serv[0], 'port' => $serv[1]);
		} */
		//print_r($cache); die();
		return $cache;
	}
}
?>
