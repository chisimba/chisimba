<?php
error_reporting(E_ALL|E_STRICT);
//require_once ('PHP/Beautifier.php');
//require_once ('PHP/Beautifier/Batch.php');

class addbeauty extends object
{
	public function init()
	{
		$this->objConfig = $this->getObject('altconfig','config');
		require_once($this->getResourcePath('Beautifier.php'));
		require_once($this->getResourcePath('Beautifier/Batch.php'));
	}

	public function beautify($module)
	{
		try {
    		$path = $this->objConfig->getModulePath();
        	$oBeaut = new PHP_Beautifier();
        	$oBatch = new PHP_Beautifier_Batch($oBeaut);
        	$oBatch->setRecursive(TRUE);
        	$oBatch->addFilter('ArrayNested');
        	$oBatch->addFilter('ListClassFunction');
        	$oBatch->addFilter('Pear');
        	//set the input file
        	$oBatch->setInputFile($path . $module . '/*.php');
        	$oBatch->setOutputFile($path . $module .'_beautified/');
        	$beautiful = $path . $module .'_beautified/';
        	$oBatch->process();
        	if (php_sapi_name()=='cli') {
            	$oBatch->show();
        	} else {
        		
            	$oBatch->save();
            	//echo "saved";
            	//var_dump($oBatch->save());
            	return $beautiful;
        	}
    }
    catch(Exception $oExp) {
        echo ($oExp);
    }


	}
}
?>