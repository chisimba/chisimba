<?PHP
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
require_once "lib/logging.php";

/**
* Class to encapsulate management operations on [a] database(s).
* It is highly recommended that you create a derived version
* of this class for each table, rather than using it directly.
*
* @author Paul Scott
* @filesource
* @copyright (c) 2000-2006, GNU/GPL AVOIR UWC
* @package core
* @subpackage dbtable
* @version 0.1
* @since 03 March 2006
* @example dbdumpexample.php
*
* $Id$
*/

class dbTableManager extends object
{
    /**
     * The current table name that we are working with
     *
     * @access public
     * @var string - default null
     */
    public $_tableName = null;

    /**
     * The current database that we are working with
     *
     * @access public
     * @var string - default null
     */
    public $_dbName = null;

    /**
     * The global error callback for dbTable errors
     *
     * @access public
     * @var string
     */
    public $_errorCallback;

    /**
     * The database config object
     *
     * @access public
     * @var object
     */
    public $objDBConfig;

    /**
     * The db object
     *
     * @access private
     * @var object
     */
    private $_dbmanager = null;

   /**
    * Method to initialise the dbTableManager object.
    *
    * @access public
    * @param string $tableName The name of the table this object encapsulates
    * @param string $dbName The database name
    * @param PEAR $ ::MDB2_Schema $pearDb The PEAR::MDB2_Schema object to use (defaults to use the global connection)
    * @param callback $errorCallback The name of a custom error callback function (defaults to the global)
    * @return void
    */
    public function init($tableName, $dbName, $pearDbManager = null,
        $errorCallback = "globalPearErrorCallback")
    {
        $this->_tableName = $tableName;
        $this->_dbName = $dbname;
        $this->_errorCallback = $errorCallback;
        if ($pearDbManager == null) {
            $this->_dbmanager = $this->objEngine->getDbManagementObj();
        } else {
            $this->_dbmanager = $pearDbManager;
        }

        $this->objDBConfig=&$this->getObject('dbconfig','config');
        $this->_serverName = $this->objDBConfig->serverName();

        //var_dump($this->_dbmanager);
    }

    /**
     * Method to parse a database definition file by creating a Metabase schema format
     * parser object and passing the file contents as parser input data stream.
     *
     * @param string $input_file the path of the database schema file.
     * @param array $variables an associative array that the defines the text
     * string values that are meant to be used to replace the variables that are
     * used in the schema description.
     * @param bool $fail_on_invalid_names (optional) make function fail on invalid
     * names
     * @return mixed true on success, or a MDB2 error object
     * @access public
     */
    public function parseDbDefFile($input_file, $variables = array(), $fail_on_invalid_names = TRUE)
    {
        return $this->parseDatabaseDefinitionFile($input_file, $variables,
        $fail_on_invalid_names, $structure = false);

    }

    /**
     * Method to attempt to reverse engineer a schema structure from an existing MDB2
     * This method can be used if no xml schema file exists yet.
     * The resulting xml schema file may need some manual adjustments.
     *
     * @return mixed MDB2_OK or array with all ambiguities on success, or a MDB2 error object
     * @access public
     */
    public function getDefFromDb()
    {
        return $this->getDefinitionFromDatabase();
    }



}
?>