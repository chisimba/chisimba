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
* @version 0.1
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
	 * Method to get a list of all modules on the system
	 *
	 * @param string $filter the category of modules wanted
	 * @return array a list of the modules
	 */
	public function getModuleList($filter='all') {
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
                		if ($filter != 'all') {
							//get only category
							if ($hasRegFile=($this->checkForFile($lookdir.'/'.$line,'register.conf')+$this->checkForFile($lookdir.'/'.$line,'register.php'))) {
								if ($this->moduleCategory($line) == strtolower($filter)) {
                					$hasController=$this->checkForFile($lookdir.'/'.$line,'controller.php');
                					$modulelist[$line]['hasController']=$hasController;
                					$modulelist[$line]['hasRegFile']=$hasRegFile;
                					$modulelist[$line]['hasClasses']=$this->checkForFile($lookdir.'/'.$line,'classes');
                					//$isReg=in_array($line,$regmodules);
                					//$modulelist[$line]['isReg']=$isReg;
								}
							}
            			} else {
            				//get all
            				$hasController=$this->checkForFile($lookdir.'/'.$line,'controller.php');
                			$hasRegFile=($this->checkForFile($lookdir.'/'.$line,'register.conf')+$this->checkForFile($lookdir.'/'.$line,'register.php'));
                			$modulelist[$line]['hasController']=$hasController;
                			$modulelist[$line]['hasRegFile']=$hasRegFile;
                			$modulelist[$line]['hasClasses']=$this->checkForFile($lookdir.'/'.$line,'classes');
                			//$isReg=in_array($line,$regmodules);
                			//$modulelist[$line]['isReg']=$isReg;
                		}
            		}
        	}
        }
        return $modulelist;
    }
    
    /**
     * Function to extract the module catalogue from the register file
     *
     * @param string $module the name of the module to check
     * @return string|false the category name if it exists or false
     */
    public function moduleCategory($module) {
    	if ($fn = $this->findregisterfile($module)) {
    		$fh = fopen($fn,"r");
    		$content = fread($fh,filesize($fn));
    		if (preg_match('/MODULE_CATEGORY:\s*(.*)/i',$content,$match)) {
    			fclose($fh);
    			return strtolower($match[1]);
    		} else {
    			fclose($fh);
    			return false;
    		}
    	} else {
    		return false;
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
        $dirObj = dir($file);
        while (false !== ($entry = $dirObj->read()))
        {
            $list[]=$entry;
        }
        $dirObj->close();
        return $list;
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
        if (file_exists($where."/".$fname))
        {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /** This is a method to check for existance of registration file
    * @author James Scoble
    * @param string modname
    * @returns FALSE on error, string filepatch on success
    */
    function findregisterfile($modname)
    {
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
    }
}
?>