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

//lets check to see if the Var_Dump PEAR object exists for debugging,
//else we just use plain 'ol var_dump
@include_once 'Var_Dump.php';

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
     * The non Schema DB Object
     * We are instantiating this again, in case of first time install
     * This way, we can create the tables through the magic __call method
     * and get away with it
     *
     *@access private
     * @var object
     */
    private $_db = NULL;

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
    public function init($dbName = NULL, $pearDbManager = null,
        $errorCallback = "globalPearErrorCallback")
    {
        $this->_errorCallback = $errorCallback;
        if ($pearDbManager == null) {
            $this->_dbmanager = $this->objEngine->getDbManagementObj();
            $this->_db = $this->objEngine->getDbObj();
        } else {
            $this->_dbmanager = $pearDbManager;
            $this->_db = $pearDb;
        }

        //check for PEAR Var_dump and initialise it,
        //otherwise just use regular PHP var_dump();
        if (class_exists('Var_Dump')) {
            $var_dump = array('Var_Dump', 'display');
        } else {
            $var_dump = 'var_dump';
        }

        //Load up the config object and get the servername
        $this->objDBConfig=&$this->getObject('dbconfig','config');
        $this->_serverName = $this->objDBConfig->serverName();

        //call_user_func($var_dump, $this->_dbmanager);
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
        return $this->_dbmanager->parseDatabaseDefinitionFile($input_file, $variables,
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
        $this->_dbmanager->getDefinitionFromDatabase();
    }

    /**
     * Method to dump the database to a specified schema file
     * There are three options as to how to dump the db to file
     * 1. Structure only
     * 2. Content Only
     * 3. All - both Structure and content
     *
     * @access public
     * @param string $option
     * @param string $dumptype
     * @param string $dumpfile
     * @return bool
     */
    public function dumpDatabaseToFile($option = 'dump', $dumptype = 'all', $dumpfile)
    {
        //lets set a time limit on this
        set_time_limit(0);

        if ($option == 'dump')
        {
            switch ($dumptype)
            {
                case 'structure':
                    $dump_what = MDB2_SCHEMA_DUMP_STRUCTURE;
                    break;

                case 'content':
                    $dump_what = MDB2_SCHEMA_DUMP_CONTENT;
                    break;

                default:
                    $dump_what = MDB2_SCHEMA_DUMP_ALL;
                    break;
            }

            $dump_config = array(
                'output_mode' => 'file',
                'output' => $dumpfile
            );

            $operation = $this->_dbmanager->dumpDatabase($dump_config, $dump_what);
            if (PEAR::isError($operation)) {
                die($operation->getMessage() . ' ' . $operation->getUserInfo());
            }
            return TRUE;
        }//if

        return FALSE;
    }//func

    /**
     * Method to get the debug strings from queries if neccessary
     *
     * @access private
     * @param reference to the management object $db
     * @param string $scope
     * @param string $message
     * @return string message
     */
    private function printQueries(&$db, $scope, $message)
    {
        if ($scope == 'query')
        {
            return $message.$db->getOption('log_line_break');
        }
        MDB2_defaultDebugOutput($db, $scope, $message);
    }


    /**
     * Method to create a table
     * <pre>
     * $fields = array(
     *   'id' => array(
     *   'type'     => 'char',
     *   'length'   => 32
     *   'unsigned' => true,
     *   'autoincrement'  => false,
     *  ),
     *  'somename' => array(
     *   'type'     => 'text',
     *   'length'   => 12,
     *  ),
     * 'somedate'  => array(
     *   'type'     => 'date',
     *  ),
     * );
     * $table = 'sometable';
     * </pre>
     *
     * since we are on php5 we can use the magic __call() method to:
     * - load the manager module: $_db->loadModule('Manager', null, true);
     * - redirect the method call to the manager module: $_db->manager->createTable('sometable', $fields);
     *
     * @param string $tableName
     * @param array $fields
     */
    public function createTable($tableName, $fields, $options, $index)
    {
        if($this->_db->phptype == 'mysql' || $this->_db->phptype == 'mysqli')
        {
            $this->_db->setOption('default_table_type', 'INNODB');
            //do the table create.
            //we call on the actual MDB object, NOT the MDB::Schema object to do this.
            $this->_db->mgCreateTable($tableName, $fields, $options);
            //return a true, simply because MDB::CreateTable returns void (wtf?)
            return TRUE;
        }
        else {
            $this->_db->mgCreateTable($tableName, $fields, $options);
            return TRUE;
        }

    }

    /**
     * Method to create an index on the table
     *
     * @access public
     * @param string $tableName
     * @param string $keyname
     * @param array $index
     * @return bool true on success | False on failure
     */
    public function createTableIndex($tableName, $keyname, $index)
    {
        $this->_db->mgCreateIndex($tableName,$keyname,$index);
        return TRUE;
    }

    /**
     * Method to describe a pseudo PK id
     * Most RDBMS's besides MySQL do not have support for PK's so we fake it.
     *
     * @access public
     * @param unknown_type $tableName
     * @return bool true
     */
    public function createPK($tableName)
    {
        $pk = array(
                'fields' => array(
                'id' => array()
                 )
              );
        $this->_db->mgCreateIndex($tableName,'id_idx',$pk);
        return TRUE;

    }



}
?>