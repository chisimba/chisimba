<?PHP
/* -------------------- dbTable class for dbmanagerdb ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
* Descendant of modules which adds administrative functionality.
* Dividing the class in two like this avoids loading this
* file when only the basic user functionality is needed.
* @author Nic Appleby
* @copyright GPL UWC 2006
* @version $Id$ 
*/

class modulesadmin extends dbTableManager
{
	/**
	 * object to manipulate the modules table
	 *
	 * @var object $objModules
	 */
	protected $objModules;
	
    public function init()
    {
        try {
        	parent::init('tbl_modules');
            $this->objModules = &$this->getObject('modules');
        } catch (Exception $e) {
        	echo customException::cleanUp($e);
        }
    }

    public function checkDependency($moduleId) {
    	try {
    		return $this->objModules->checkIfRegistered($moduleId);
    	} catch (Exception $e) {
        	echo customException::cleanUp($e);
        }
    }
    
    /**
    * This method looks at the registration data and tries to create any tables specified
    * @param string $table The name of the table to be created
    * @param string $moduleId The id of the module
    * @returns boolean TRUE|FALSE
    */
    private function makeTable($table,$moduleId='NONE')
    {
        try {
        	$this->objKeyMaker=&$this->newObject('primarykey');
        	$this->objTableInfo=&$this->newObject('tableinfo');
        	if ($moduleId=='NONE'){
        		$moduleId=$this->MODULE_ID;
        	}
        	$this->objTableInfo->tablelist();
        	if ($this->objTableInfo->checktable($table))
        	{
        		return TRUE; // table already exists, don't try to create it over again!
        	}
        	$sqlfile=$this->objConfig->getsiteRootPath().'/modules/'.$moduleId.'/'.$table.'.sql';
        	if (!file_exists($sqlfile)){
        		$sqlfile=$this->objConfig->getsiteRootPath().'/modules/'.$moduleId.'/sql/'.$table.'.sql';
        	}
        	if (!file_exists($sqlfile)){
        		throw new Exception("<b>$sqlfile</b>".$this->objLanguage->languageText('phrase_notfound')."<br />");
        	}
        	include($sqlfile);
        	$this->createTable($tablename,$fields,$options);
        	$this->createTableIndex($tablename,$name,$indexes);
        	return TRUE;
        } catch (Exception $e) {
        	echo customException::cleanUp($e);
        }
    }
    
    /**
    * function loadData()
    * This is a method to read data from a file and use it to populate (not create) a table.
    * @author James Scoble
    * @param $tablefile the name of the file
    * @param string $moduleId the id of the module to be used
    * @returns boolean TRUE or FALSE
    */
    private function loadData($tablefile,$moduleId='NONE')
    {
        if ($moduleId=='NONE'){
            $moduleId=$this->MODULE_ID;
        }
        $sqlfile=$this->objConfig->siteRootPath().'/modules/'.$moduleId.'/'.$tablefile.'.sql';
        if (!file_exists($sqlfile)){
            $sqlfile=$this->objConfig->siteRootPath().'/modules/'.$moduleId.'/sql/'.$tablefile.'.sql';
        }
        if (!file_exists($sqlfile))
        {
            throw new Exception("<b>$sqlfile</b>".$this->objLanguage->languageText('phrase_notfound')."<br />");
        }
        ini_set('max_execution_time','120');
        $handle=fopen($sqlfile,'r');
        while (!feof($handle))
        {
            $line=fgets($handle,16384); // 16KB
            $line=str_replace('PKVALUE',($this->objKeyMaker->newkey($tablefile)),$line);
            $this->localQuery($line);
        }
        fclose($handle);
        return TRUE;
    }
}
?>