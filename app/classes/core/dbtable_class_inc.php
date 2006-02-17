<?PHP
/**
 * Class dbTable
 * This class will provide data access to the modules
 * I will attempt to adapt the MDB API to mirror the
 * PHP4 implementation of KINKY for compatibility sake
 *
 * @package core
 * @author Paul Scott
 * $Id$
 */

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
class dbTable extends object
{
    public $USE_PREPARED_STATEMENTS = true;

    public $_db = null;
    public $objEngine;
    protected $_tableName = null;
    protected $_errorCallback;
    protected $_inTransaction = false;
    protected $_serverName = "";
    protected $_lastId = null;
    // The database config object
    public  $objDBConfig;

    /**
    * Method to initialise the dbTable object.
    *
    * @param string $tableName The name of the table this object encapsulates
    * @param boolean $mirror Whether to mirror these operations (defaults to TRUE)
    * @param PEAR $ ::MDB $pearMDb The PEAR::MDB object to use (defaults to use the global connection)
    * @param callback $errorCallback The name of a custom error callback function (defaults to the global)
    */

    public function __construct($tableName, $pearMDb = NULL, $errorCallback = "globalPearErrorCallback")
    {
        $this->_tableName = $tableName;
        $this->_errorCallback = $errorCallback;
        if ($pearMDb == null) {
            $this->_db = $this->$objEngine->getDbObj();
        } //else {
        //    $this->_db = $pearMDb;
        //}

        echo "db: =" .$this->_db;

    }

    /**
    * Method to evaluate if a particular value of a particular field exists in the database
    *
    * @param string $field the name of the field to search
    * @param mixed $value the value to search for
    * @return TRUE |FALSE if exists return TRUE, otherwise FALSE.
    */
    public function valueExists($field, $value)
    {
        $sql = "SELECT COUNT(*) AS fCount FROM $this->_tableName WHERE $field='$value'";
        $rs = $this->query($sql);
        if (!$rs) {
            $ret = false;
        } else {
            $line = $rs->fetchRow();
            if ($line['fCount'] > 0) {
                $ret = true;
            } else {
                $ret = false;
            }
        }
        return $ret;
    }

    /**
    * Method to get all items from the table.
    * Override in derived classes to implement access restrictions
    *
    * @param string $filter a SQL WHERE clause (optional)
    * @return array |FALSE Rows as an array of associative arrays, or FALSE on failure
    */
    function getAll($filter = null)
    {
        $stmt = "SELECT * FROM {$this->_tableName} $filter";
        return $this->_db->queryAll($stmt);
    }

    /**
    * Method to fetch a row from the table.
    * Override in derived classes if necessary
    *
    * @return array |FALSE A row as an associative array, or FALSE on failure
    * @param string $pkfield the name of the primary key field
    * @param mixed $pkvalue the value of the primary key field for the record
    */
    public function getRow($pk_field, $pk_value)
    {
        $pk_value = addslashes($pk_value);
        $stmt = "SELECT * FROM {$this->_tableName} WHERE {$pk_field}='{$pk_value}'";
        return $this->_db->queryRow($stmt, array(), DB_FETCHMODE_ASSOC);
    }






}
?>