<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class for manipulating modules on the filesystem
*
* @author Nic Appleby
* @copyright (c)2006 UWC
* @category Chisimba
* @package Modulecatalogue
* @version $Id
*/

class modulefile extends object {
	
	/**
	 * Configuration object
	 *
	 * @var object $config
	 */
	protected $config;
	
	/**
	 * Standard init function
	 *
	 */
	public function init() {
		$this->config = &$this->getObject('altconfig','config');
	}
	
	/**
	 * Method to get a list of all modules currently on the system
	 *
	 * @return array a list of the modules
	 */
	public function getLocalModuleList() {
        try {
        	$lookdir=$this->config->getSiteRootPath()."modules";
        	$modlist=$this->checkdir($lookdir);
        	natsort($modlist);
        	$modulelist = array();
        	foreach ($modlist as $line) {
            	switch ($line) {
            		case '.':
            		case '..':
            		case 'CVS':
            			break; // don't bother with system-related dirs
            		default:
            			if (is_dir($lookdir.'/'.$line)) {
            				$modulelist[] = $line;
            			}
            	}
        	}
        	return $modulelist;
		} catch (Exception $e) {
			$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();	
		}
	}
	
	//not used. will it ever be?
    public function getCategories() {
    	try {
    		$lookdir=$this->config->getSiteRootPath()."modules";
    		$modlist=$this->checkdir($lookdir);
    		//natsort($modlist);
    		$categorylist = array();
    		foreach ($modlist as $line) {
    			switch ($line) {
    				case '.':
    				case '..':
    				case 'CVS':
    					break; // don't bother with system-related dirs
    				default:
    					if (is_dir($lookdir.'/'.$line)) {
    						if ($hasRegFile=($this->checkForFile($lookdir.'/'.$line,'register.conf')+$this->checkForFile($lookdir.'/'.$line,'register.php'))) {
    							if ($cat = $this->moduleCategory($line)) {
    								array_push($categorylist,$cat);
    							}
    						}
    					}
    			}
    		}
    		$categorylist = array_unique($categorylist);
    		sort($categorylist);
    		return $categorylist;
		} catch (Exception $e) {
			$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();	
		}
    }
    
    /**
     * Function to extract the module catalogue from the register file
     *
     * @param string $module the name of the module to check
     * @return string|false the category name if it exists or false
     */
    public function moduleCategory($module) {
    	try {
    		if ($fn = $this->findregisterfile($module)) {
    			$fh = fopen($fn,"r");
    			$content = fread($fh,filesize($fn));
    			if (preg_match('/MODULE_CATEGORY:\s*([a-z\-_]*)/i',$content,$match)) {
    				fclose($fh);
    				return strtolower($match[1]);
    			} else {
    				fclose($fh);
    				return false;
    			}
    		} else {
    			return false;
    		}
		} catch (Exception $e) {
			$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();	
		}    	
    }
    
    /**
    * This method takes one parameter, which it treats as a directory name.
    * It returns an array - listing the files in the specified dir.
    * @author James Scoble
    * @param string $file - directory/folder
    * @return array $list
    */
    protected function checkdir($file)
    {
        try {
        	$dirObj = dir($file);
        	while (false !== ($entry = $dirObj->read()))
        	{
        		$list[]=$entry;
        	}
        	$dirObj->close();
        	return $list;
		} catch (Exception $e) {
			$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();	
		}
    }
    
     /**
    * Boolean test for the existance of file $fname, in directory $where
    * @author James Scoble
    * @param string $where file path
    * @param string $fname file name
    * @return boolean TRUE or FALSE
    */
    function checkForFile($where,$fname)
    {
        try {
        	if (file_exists($where."/".$fname))
        	{
        		return TRUE;
        	} else {
        		return FALSE;
        	}
		} catch (Exception $e) {
			$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();	
		}
    }
    
    /** This is a method to check for existance of registration file
    * @author James Scoble
    * @param string modname
    * @returns FALSE on error, string filepatch on success
    */
    function findregisterfile($modname)
    {
        try {
        	$endings=array('php','conf');
        	$path=$this->config->getSiteRootPath()."/modules/".$modname."/register.";
        	foreach ($endings as $line)
        	{
        		if (file_exists($path.$line))
        		{
        			return $path.$line;
        		}
        	}
        	return FALSE;
		} catch (Exception $e) {
			$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();	
		}
    }
    
    /** This is a method to check for existance of registration file
    * @param string modname
    * @return FALSE on error, string filepatch on success
    */
    function findController($modname)
    {
        try {
        	$path=$this->config->getSiteRootPath()."/modules/".$modname."/controller.php";
        	if (file_exists($path.$line)) {
        			return $path.$line;
        	} else {
        		return FALSE;
        	}
		} catch (Exception $e) {
			$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();	
		}
    }
}
?>