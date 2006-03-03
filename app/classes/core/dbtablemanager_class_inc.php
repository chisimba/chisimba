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



}
?>