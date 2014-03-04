<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * This class will serve to import Chisimba Web Part project artifacts
 *
 * e.g.to import datastructures from an existing application
 *
 * @category  Chisimba
 * @package   webparts
 * @author    Charl Mert <charl.mert@gmail.com>
 *
 */

class import extends object
{

    /**
    * objParser- The Custom XML Parser and Utility class
    *
    * @access private
    * @var object
    */
    protected $objParser;



    /**
    * Class Constructor
    *
    * @access public
    * @return void
    */

    public function init()
    {
		// Load Config Object
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objParser = $this->getObject('xmlparser', 'webparts');

		$this->wpModuleName = '';
	
		$this->objParser->setWpBasePath($this->wpBasePath);
    }

    /**
     * This method sets the Wep Parts Module Name
     * 
     */
    public function setWpModuleName($value) {
 
		$this->wpModuleName = $value;
		$this->objParser->setWpModuleName($value);		
		$this->wpBasePath = $this->objConfig->getcontentBasePath()."webparts/$this->wpModuleName/sql/";
	}

    /**
     * Method to write the sql table code
     *
     * @param $fileName The name of the template file, recommeded tbl_modulename_tablename.sql
     * @access public
     * @return HTML
     */
    public function saveSqlTemplate($fileName, $moduleName, $code)
    {
        //Ensuring the base directory exists or will exist
        if(!file_exists($this->wpBasePath))
        {
            mkdir($this->wpBasePath, 0777, true);
        }

        //Writting the file to disk
        $fp = fopen($this->wpBasePath . $fileName, 'w') or log_debug('Web Parts: Creating Sql Table: Could\'nt Save file: ['.$this->wpBasePath.$fileName.']');
        fwrite($fp, $code);
        fclose($fp);

        return TRUE;
    }


    /**
     * public method to import a mysql datastructure and generate MDB2 formated php code for use with Chisimba Modules/Web Parts 
     *
     * @param string $mysqlXmlDump The XML formatted mysqldump export file or string
     * @return void
     * @access public
     */
    public function generateMdb2DataStruct($mysqlXmlDump)
    {
        $result = $this->objParser->mysqlToMdb2($mysqlXmlDump);
        return $result;
    }
    
}
?>
