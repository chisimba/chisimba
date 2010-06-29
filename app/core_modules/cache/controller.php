<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
	die("You cannot view this page directly");
}
// end security check
class cache extends controller
{
	public $objLog;
	public $objLanguage;
	public $objConfig;
	public $objYaml;



	/**
     * Constructor method to instantiate objects and get variables
     */
	public function init()
	{
		try {
			$this->objLanguage = $this->getObject('language', 'language');
			$this->objConfig = $this->getObject('altconfig', 'config');
			$this->objYaml = $this->getObject('yaml', 'utilities');
			//Get the activity logger class
			$this->objLog = $this->newObject('logactivity', 'logger');
			//Log this module call
			$this->objLog->log();
		}
		catch(customException $e) {
			echo customException::cleanUp();
			die();
		}
	}

	/**
     * Method to process actions to be taken
     *
     * @param string $action String indicating action to be taken
     */
	public function dispatch($action = Null)
	{
		switch ($action) {
			default:
				// get the cache config file
				$filename = 'cache.config';
				if(!file_exists($filename))
				{
					touch($filename);
					chmod($filename, 0777);
				}
				$handle = fopen($filename, "r");
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					$num = count($data);
					//echo $num;
					for ($c=0; $c < $num; $c++) {
						$cache[] = array('ip' => $data[0], 'port' => $data[1]);
					}
				}
				fclose($handle);
				if(empty($cache))
				{
					$cachenew = array('ip' => 'localhost', 'port' => 11211);
					$cacherec = array($cachenew);
					$handle = fopen($filename, 'wb');
					foreach($cacherec as $rec)
					{
						fputcsv($handle, $rec);
					}
					fclose($handle);
				}
				
				$this->setVarByRef('cache', $cache);
				
				return 'edit_tpl.php';
				break;

			case 'addserver':
				$ip = $this->getParam('ip');
				$port = $this->getParam('port');
				$filename = 'cache.config';
				$handle = fopen($filename, "r");
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					$cache[] = array('ip' => $data[0], 'port' => $data[1]);
				}
				fclose($handle);
				$adder = array(array('ip' => $ip, 'port' => $port));
				$all = array_merge($adder, $cache);
				// re-write the cache config file
				unlink($filename);
				$filename2 = 'cache.config';
				$handle = fopen($filename2, 'wb');
				foreach($all as $servers)
				{
					fputcsv($handle, $servers);
				}
				fclose($handle);
				$this->setVarByRef('cache', $all);
				return 'edit_tpl.php';
				
			case 'displaystats':
				$filename = 'cache.config';
				$handle = fopen($filename, "r");
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					$cache[] = array('ip' => $data[0], 'port' => $data[1]);
				}
				fclose($handle);
				$id = $this->getParam('id');
				$machine = explode(":", $id);
				$memcache = new Memcache;
				$memcache->addServer($machine[0], $machine[1]);
				$stats = $memcache->getStats();
				$this->setVarByRef('stats', $stats);
				$this->setVarByRef('cache', $cache);
				$this->setVarByRef('machine', $machine);
				return 'edit_tpl.php';
				break;
		}
	}
}
?>