<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

class yaml extends object 
{
	public $objYaml;
	
	public function init()
	{
		include($this->getResourcePath('yaml/spyc.php'));
		$this->objYaml = new Spyc;
	}
	
	public function parseYaml($file)
	{
		if(file_exists($file))
		{
			return $this->objYaml->load($file);
		}
		else {
			return FALSE;
		}
	}
	
	public function saveYaml($array,$indent = false,$wordwrap = false)
	{
		return $this->objYaml->dump($array, $indent, $wordwrap);
	}
}