<?php

/**
 * dbTable Class
 *
 * Database abstraction layer.
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   core
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check


/**
 * Logging layer
 */
require_once "lib/logging.php";

/**
 * dbTable class
 *
 * This class encapsulates all database functions and methods. All modules that require database access should extend this base class.
 *
 * @category  Chisimba
 * @package   core
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class dbTable extends object
{
	/**
     * Whether or not to use prepared statements
     *
     * @access public
     * @var    string
     */
    public $USE_PREPARED_STATEMENTS = FALSE;

    /**
     * The current table name that we are working with
     *
     * @access public
     * @var    string - default NULL
     */
    public $_tableName = NULL;

    /**
     * The global error callback for dbTable errors
     *
     * @access public
     * @var    string
     */
    public $_errorCallback;

    /**
     * The database config object
     *
     * @access public
     * @var    object
     */
    public $objDBConfig;

    /**
     * The db object
     *
     * @access private
     * @var    object
     */
    private $_db = NULL;

    /**
     * Static variable that holds the system database type
     *
     * @var string
     */
    public static $dbType;

    /**
     * Are we in a transaction?
     *
     * @access private
     * @var    string
     */
    private $_inTransaction = FALSE;

    /**
     * property to hold the last id inserted into the db
     *
     * @access private
     * @var    string
     */
    private $_lastId = NULL;

    /**
     * Property to hold the portability object for queries against multiple RDBM's
     *
     * @access private
     * @var    object
     */
    private $_portability;

    /**
     * Property to handle the error reporting
     *
     * @access private
     * @var    string
     */
    private $debug = FALSE;

    /**
     * Description for public
     * @var    boolean
     * @access public
     */
    public $adm = FALSE;

    public $objMemcache = FALSE;
    
    public $objAPC = FALSE;

	protected $cacheTTL = 3600;
	
	public $objYaml;
	
	public $dbLayer;
	
	public $cachePrefix;
	
	public $nonmirrored = array(
								                'tbl_logger',
								                'tbl_sysconfig_properties',
								                // 'tbl_decisiontable_action',
								                // 'tbl_decisiontable_action_rule',
								                // 'tbl_decisiontable_condition',
								                // 'tbl_decisiontable_conditiontype',
								                // 'tbl_decisiontable_decisiontable',
								                // 'tbl_decisiontable_decisiontable_action',
								                // 'tbl_decisiontable_decisiontable_rule',
								                // 'tbl_decisiontable_rule',
								                // 'tbl_decisiontable_rule_condition',
								                // 'tbl_groupadmin_group',
								                // 'tbl_groupadmin_groupuser',
								                'tbl_menu_category',
								                'tbl_module_blocks',
								                'tbl_module_patches',
								                'tbl_modules',
								                'tbl_modules_dependencies',
								                'tbl_modules_owned_tables',
								                'tbl_permissions_acl',
								                'tbl_permissions_acl_description',
								                'tbl_prelogin_blocks',
								               );


    /**
    * Method to initialise the dbTable object.
    *
    * @access public
    * @param  string   $tableName     The name of the table this object encapsulates
    * @param  boolean  $mirror        Whether to mirror these operations (defaults to TRUE)
    * @param  PEAR     $              ::MDB2 $pearDb The PEAR::MDB2 object to use (defaults to use the global connection)
    * @param  callback $errorCallback The name of a custom error callback function (defaults to the global)
    * @return void
    */
    public function init($tableName, $pearDb = NULL, $errorCallback = "globalPearErrorCallback") {
    	$modname = $this->objEngine->_moduleName;
    	// global $_globalObjDb;
        $this->_tableName     = $tableName;
        $this->_errorCallback = $errorCallback;
        if ($pearDb == NULL) {
            $this->_db = $this->objEngine->getDbObj();
            $pearDb    = $this->_db;
        } else {
            $this->_db = $pearDb;
        }

        $this->objDBConfig = $this->getObject('altconfig','config');
        // check for memcache
		if(extension_loaded('memcache'))
		{
			require_once 'chisimbacache_class_inc.php';
			if($this->objDBConfig->getenable_memcache() == 'TRUE')
			{
				$this->objMemcache = TRUE;
			}
			else {
				$this->objMemcache = FALSE;
			}
			$this->cacheTTL = $this->objDBConfig->getcache_ttl();
		}
		// check for APC
		if(extension_loaded('apc'))
		{
			if($this->objDBConfig->getenable_apc() == 'TRUE')
			{
				$this->objAPC = TRUE;
			}
			else {
				$this->objAPC = FALSE;
			}
			$this->cacheTTL = $this->objDBConfig->getcache_ttl();
		}
		
        $this->_serverName = $this->objDBConfig->serverName();
        // set up the cache prefix for this instance
        $this->cachePrefix = $this->_serverName."_".$modname."_";
        
        //check if debugging is enabled
        if($this->objDBConfig->geterror_reporting() == "developer")
        {
        	$this->debug = TRUE;
        }
        if($this->objDBConfig->getenable_adm() == "TRUE")
        {
        	$this->adm = TRUE;
        }
        $this->dbLayer = $this->objDBConfig->getenable_dbabs();
        if($this->dbLayer === 'MDB2')
        {
        	$this->dbType = $this->_db->phptype;
        }
        elseif($this->dbLayer === 'PDO')
        {
        	$this->dbType = $this->objEngine->pdsn['phptype'];
        }
        
        // $this->dbLayer = $this->objDBConfig->getenable_dbabs();
        

    }

    /**
    * Method to evaluate if a particular value of a particular field exists in the database
    *
    * @access public
    * @param  string $field the name of the field to search
    * @param  mixed  $value the value to search for
    * @return bool   TRUE |FALSE if exists return TRUE, otherwise FALSE.
    */
    public function valueExists($field, $value, $table=NULL) {
        if ($table == NULL) {
        	$table = $this->_tableName;
        }
    	$sql = "SELECT COUNT(*) AS fcount FROM $table WHERE $field='$value'";
    	if($this->objMemcache == TRUE)
    	{
    		if(chisimbacache::getMem()->get(md5($this->cachePrefix.$sql)))
			{
				$cache = chisimbacache::getMem()->get(md5($this->cachePrefix.$sql));
				$ret   = unserialize($cache);
			}
			else {
        		if($this->debug == TRUE)
        		{
        			log_debug("$sql");
        		}
    			$rs = $this->query($sql);
        		if (!$rs) {
            		$ret = FALSE;
        		} else {
            		if ($rs[0]['fcount'] > 0) {
                		$ret = TRUE;
            		} else {
                		$ret = FALSE;
            		}
        		}
        		chisimbacache::getMem()->set(md5($this->cachePrefix.$sql), serialize($ret), MEMCACHE_COMPRESSED, $this->cacheTTL);
			}
    	}
    	elseif($this->objAPC == TRUE)
    	{
    		$ret = apc_fetch($this->cachePrefix.$sql);
    		if($ret == FALSE)
    		{
    			$rs = $this->query($sql);
        		if (!$rs) {
            		$ret = FALSE;
        		} else {
            		if ($rs[0]['fcount'] > 0) {
                		$ret = TRUE;
            		} else {
                		$ret = FALSE;
            		}
        		}
    			apc_store($this->cachePrefix.$sql, $ret, $this->cacheTTL);
    		}
    	}
		else {
        	if($this->debug == TRUE)
        	{
        		log_debug("$sql");
        	}
    		$rs = $this->query($sql);
        	if (!$rs) {
            	$ret = FALSE;
        	} else {
            	if ($rs[0]['fcount'] > 0) {
                	$ret = TRUE;
            	} else {
                	$ret = FALSE;
            	}
        	}
		}

        return $ret;
    }

    /**
    * Method to get all items from the table.
    * Override in derived classes to implement access restrictions
    *
    * @access public
    * @param  string $filter a SQL WHERE clause (optional)
    * @return array  |FALSE Rows as an array of associative arrays, or FALSE on failure
    */
    public function getAll($filter = NULL) {
		$stmt = "SELECT * FROM {$this->_tableName} $filter";
		if($this->objMemcache == TRUE)
		{
			if(chisimbacache::getMem()->get(md5($this->cachePrefix.$stmt)))
			{
				$cache = chisimbacache::getMem()->get(md5($this->cachePrefix.$stmt));
				$ret   = unserialize($cache);
			}
			else {
        		if($this->debug == TRUE)
        		{
        			log_debug($stmt);
        		}
        		$ret = $this->getArray($stmt);
        		chisimbacache::getMem()->set(md5($this->cachePrefix.$stmt), serialize($ret), MEMCACHE_COMPRESSED, $this->cacheTTL);
			}
		}
		elseif($this->objAPC == TRUE)
    	{
    		$ret = apc_fetch($this->cachePrefix.$stmt);
    		if($ret == FALSE)
    		{
    			$ret = $this->getArray($stmt);
    			apc_store($this->cachePrefix.$stmt, $ret, $this->cacheTTL);
    		}
    	}
		else {
			if($this->debug == TRUE)
        	{
        		log_debug($stmt);
        	}
        	$ret = $this->getArray($stmt);
		}

		return $ret;
    }

    /**
    * Method to fetch a row from the table.
    * Override in derived classes if necessary
    *
    * @access public
    * @return array  |FALSE A row as an associative array, or FALSE on failure
    * @param  string $pkfield the name of the primary key field
    * @param  mixed  $pkvalue the value of the primary key field for the record
    */
    public function getRow($pk_field, $pk_value) {
        $pk_value = addslashes($pk_value);
        $stmt     = "SELECT * FROM {$this->_tableName} WHERE {$pk_field}='{$pk_value}'";
        if($this->objMemcache == TRUE)
        {
        	if(chisimbacache::getMem()->get(md5($this->cachePrefix.$stmt)))
			{
				$cache = chisimbacache::getMem()->get(md5($this->cachePrefix.$stmt));
				$ret   = unserialize($cache);
			}
			else {
				if($this->debug == TRUE)
        		{
        			log_debug($stmt);
        		}
        		$ret = $this->_queryRow($stmt, array()); //, DB_FETCHMODE_ASSOC);
        		chisimbacache::getMem()->set(md5($this->cachePrefix.$stmt), serialize($ret), MEMCACHE_COMPRESSED, $this->cacheTTL);
			}
        }
        elseif($this->objAPC == TRUE)
    	{
    		$ret = apc_fetch($this->cachePrefix.$stmt);
    		if($ret == FALSE)
    		{
    			$ret = $this->_queryRow($stmt, array());
    			apc_store($this->cachePrefix.$stmt, $ret, $this->cacheTTL);
    		}
    	}
        else {
        	if($this->debug == TRUE)
        	{
        		log_debug($stmt);
        	}
        	$ret = $this->_queryRow($stmt, array()); //, DB_FETCHMODE_ASSOC);
        }
        return $ret;
    }

    /**
    * Method to execute a query against the database and return an array.
    *
    * @access public
    * @param  string $stmt the SQL query string
    * @return array  |FALSE Rows as an array of associate arrays, or FALSE on failure
    */
    public function getArray($stmt) {
    	if($this->objMemcache == TRUE)
    	{
    		if(chisimbacache::getMem()->get(md5($this->cachePrefix.$stmt)))
			{
				$cache = chisimbacache::getMem()->get(md5($this->cachePrefix.$stmt));
				$ret   = unserialize($cache);
			}
			else {
        		if($this->debug == TRUE)
        		{
        			log_debug($stmt);
        		}
    			//var_dump($this->_db);
    			$ret = $this->_queryAll($stmt, array()); //, MDB2_FETCHMODE_ASSOC);
        		if (PEAR::isError($ret)) {
            		$ret = FALSE;
        		}
        		chisimbacache::getMem()->set(md5($this->cachePrefix.$stmt), serialize($ret), MEMCACHE_COMPRESSED, $this->cacheTTL);
			}
    	}
    	elseif($this->objAPC == TRUE)
    	{
    		$ret = apc_fetch($this->cachePrefix.$stmt);
    		if($ret == FALSE)
    		{
    			$ret = $this->_queryAll($stmt, array()); //, MDB2_FETCHMODE_ASSOC);
        		if (PEAR::isError($ret)) {
            		$ret = FALSE;
        		}
    			apc_store($this->cachePrefix.$stmt, $ret, $this->cacheTTL);
    		}
    	}
    	else {
    		if($this->debug == TRUE)
    		{
    			log_debug($stmt);
    		}
    		//var_dump($this->_db);
    		$ret = $this->_queryAll($stmt, array()); //, MDB2_FETCHMODE_ASSOC);
    		if (PEAR::isError($ret)) {
    			$ret = FALSE;
    		}
    	}
    	return $ret;
    }

    /**
    * Method to execute a query against the database and return an array.
    *
    * @access public
    * @param  string $stmt  the SQL query string
    * @param  int    $first The first record to return
    * @param  int    $count The number of records to return
    * @return array  |FALSE Rows as an array of associate arrays, or FALSE on failure
    */
    public function getArrayWithLimit($stmt, $first, $count) {
    	if($this->objMemcache == TRUE)
    	{
    		if(chisimbacache::getMem()->get(md5($this->cachePrefix.$stmt)))
			{
				$cache = chisimbacache::getMem()->get(md5($this->cachePrefix.$stmt));
				$ret   = unserialize($cache);
			}
			else {
        		if($this->debug == TRUE)
        		{
        			log_debug($stmt);
        		}
    			$this->_db->setLimit($first, $count);
        		$rs = $this->_db->exec($stmt);
        		if (PEAR::isError($rs)) {
            		$ret = FALSE;
        		} else {
        			$ret = $rs;
        			chisimbacache::getMem()->set(md5($this->cachePrefix.$stmt), serialize($ret), MEMCACHE_COMPRESSED, $this->cacheTTL);
        		}
			}
    	}
    	elseif($this->objAPC == TRUE)
    	{
    		$ret = apc_fetch($this->cachePrefix.$stmt);
    		if($ret == FALSE)
    		{
    			$this->_db->setLimit($first, $count);
        		$rs = $this->_db->exec($stmt);
        		if (PEAR::isError($rs)) {
            		$ret = FALSE;
        		} else {
        			$ret = $rs;
    				apc_store($this->cachePrefix.$stmt, $ret, $this->cacheTTL);
        		}
    		}
    	}
    	else {
    		if($this->debug == TRUE)
    		{
    			log_debug($stmt);
    		}
    		$this->_db->setLimit($first, $count);
    		$rs = $this->_db->exec($stmt);
    		if (PEAR::isError($rs)) {
    			$ret = FALSE;
    		} else {
    			$ret = $rs;
    		}
    	}

       return $ret;
    }

    /**
    * Method to fetch all items from the table.
    * Override in derived classes to implement access restrictions
    *
    * @access     public
    * @return     db     _result|FALSE a PEAR::DB_Result object, or FALSE
    * @param      string $filter a SQL WHERE clause (optional)
    * @deprecated See getAll
    * @see        getAll
    */
    public function fetchAll($filter = NULL) {
        $stmt = "SELECT * FROM {$this->_tableName} " . $filter;
        if($this->objMemcache == TRUE)
    	{
    		if(chisimbacache::getMem()->get(md5($this->cachePrefix.$stmt)))
			{
				$cache = chisimbacache::getMem()->get(md5($this->cachePrefix.$stmt));
				$ret   = unserialize($cache);
			}
			else {
        		if($this->debug == TRUE)
        		{
        			log_debug($stmt);
        		}
        		$ret = $this->query($stmt);
        		chisimbacache::getMem()->set(md5($this->cachePrefix.$stmt), serialize($ret), MEMCACHE_COMPRESSED, $this->cacheTTL);
			}
    	}
    	elseif($this->objAPC == TRUE)
    	{
    		$ret = apc_fetch($this->cachePrefix.$stmt);
    		if($ret == FALSE)
    		{
    			$ret = $this->query($stmt);
    			apc_store($this->cachePrefix.$stmt, $ret, $this->cacheTTL);
    		}
    	}
    	else {
    		if($this->debug == TRUE)
    		{
    			log_debug($stmt);
    		}
    		$ret = $this->query($stmt);
    	}
    	return $ret;
    }

    /**
    * Method to return a count of records in the table.
    * Mainly to use in recordset paging
    *
    * @access public
    * @param  string $filter a SQL WHERE clause (optional)
    * @return int    number of records that matched
    */
    public function getRecordCount($filter = NULL) {
        $sql = "SELECT COUNT(*) AS rc FROM {$this->_tableName} " . $filter;
        if($this->objMemcache == TRUE)
    	{
    		if(chisimbacache::getMem()->get(md5($this->cachePrefix.$sql)))
			{
				$cache = chisimbacache::getMem()->get(md5($this->cachePrefix.$sql));
				$ret   = unserialize($cache);
			}
			else {
        		if($this->debug == TRUE)
        		{
        			log_debug($sql);
        		}
        		$rs = $this->query($sql);
	    		$ret   = $rs[0]['rc'];
	    		chisimbacache::getMem()->set(md5($this->cachePrefix.$sql), serialize($ret), MEMCACHE_COMPRESSED, $this->cacheTTL);
			}
    	}
    	elseif($this->objAPC == TRUE)
    	{
    		$ret = apc_fetch($this->cachePrefix.$sql);
    		if($ret == FALSE)
    		{
    			$rs  = $this->query($sql);
	    		$ret = $rs[0]['rc'];
    			apc_store($this->cachePrefix.$sql, $ret, $this->cacheTTL);
    		}
    	}
    	else {
    		if($this->debug == TRUE)
    		{
    			log_debug($sql);
    		}
    		$rs  = $this->query($sql);
    		$ret = $rs[0]['rc'];
    	}
    	return $ret;
    }

    /**
    * Method to begin a transaction.
    * Use a transaction where you are performing multiple inserts and/or updates as part
    * of a single conceptual operation.
    *
    * @access public
    * @param  void
    * @return void
    */
    public function beginTransaction() {
        if ($this->_db->supports('transactions')) {
            $this->_db->beginTransaction();
            $this->_inTransaction = TRUE;
        }
    }

    /**
    * Method to commit a transaction.
    *
    * @param  void
    * @return set    property _inTransaction
    * @access public
    */
    public function commitTransaction() {
        if ($this->_db->in_transaction)
        {
            $this->_db->commit();
            $this->_inTransaction = FALSE;
        }

    }

    /**
    * Method to rollback a transaction.
    *
    * @param  void
    * @return void
    * @access public
    */
    public function rollbackTransaction() {
        $this->_db->rollback();
        $this->_inTransaction = FALSE;
    }

    /**
    * Method to insert a new record into the table.
    *
    * @param  array  $fields    The new record as an associative array containing field names as keys,
    *                           field values as values. All non-NULL fields must be present
    * @param  string $tablename The table to insert into, if not the default (optional)
    * @return string |FALSE Generated PK ID on success, FALSE on failure
    * @access public
    */
    public function insert($fields, $tablename = '') {

        if (empty($tablename)) {
            $tablename = $this->_tableName;
        }
        if (empty($fields['id'])) {
            $id = $this->_serverName . "_" . rand(1000,9999) . "_" . time();
            $fields['id'] = $id;
        } else {
            $id = $fields['id'];
        }

        $fieldnames = '';
        $fieldValues = '';
        $params = '';

        $keys = array_keys($fields);
        $comma = "";
        foreach($keys as $key) {
        	$fieldnames .= "{$comma}{$key}";
	        $comma = ", ";
        }
        $comma = "";
        foreach ($fields as $fieldName => $fieldValue) {
			$fieldValue = $this->_db->quote($fieldValue);
        	$fieldValues .= "{$comma}{$fieldValue}";
	        $comma = ", ";
        }
		$fieldnames = "($fieldnames)";
        $fieldValues = "VALUES ($fieldValues)";
        $sql = "INSERT INTO {$tablename} {$fieldnames} {$fieldValues}";
        $this->_lastId = $id;
        if($this->debug == TRUE) {
        	log_debug("dbtable insert into {$tablename}");
	   		log_debug($fields);
        	log_debug($sql);
        	log_debug("dbtable insert into {$tablename}");
        }
        if($this->dbLayer === 'MDB2')
        {
        	if($this->_db->phptype == 'mysql')
        	{
        		$ret = $this->_execute($sql, $params);
        	}
        	else {
        		$ret = $this->_db->query($sql);
        	}
        }
        elseif($this->dbLayer === 'PDO')
        {
        	try{
        		$ret = $this->query($sql);
        	}
        	catch (PDOException $e)
    		{
    			throw new customException($e->getMessage());
    			customException::cleanUp();
    			exit;
    		}
        }
        if($this->adm == TRUE)
        {
        	if(!in_array($tablename, $this->nonmirrored))
        	{
				sql_log("[SQLDATA]".$sql."[/SQLDATA]");
        	}
        }
        if($this->objMemcache == TRUE)
    	{
    		chisimbacache::getMem()->flush();
    	}
    	if($this->objAPC == TRUE)
    	{
    		apc_clear_cache("user");
    	}
        return $ret ? $id : FALSE;
    }

    /**
    * Method to update an existing record in the table.
    *
    * @param  string $pkfield   the name of the primary key field
    * @param  mixed  $pkvalue   the value of the primary key field for the record to update
    * @param  array  $fields    The record as an associative array containing field names as keys and field values as values.
    * @param  string $tablename The name of the table to update, if not the default (optional)
    * @return TRUE   |FALSE TRUE on success, FALSE on failure
    * @access public
    */
    public function update($pkfield, $pkvalue, $fields, $tablename = '') {
        if (empty($tablename)) {
            $tablename = $this->_tableName;
        }
        $sql = "UPDATE {$tablename} SET ";
        $comma = "";
        $params = array();
        foreach ($fields as $fieldName => $fieldValue) {
            if ($this->USE_PREPARED_STATEMENTS) {
                $sql .= "{$comma}{$fieldName}=?";
                $params[] = $this->_db->quote($fieldValue);
            } else {
                $sql .= "{$comma}{$fieldName}=". $this->_db->quote($fieldValue);
            }
            $comma = ',';
        }
        $sql .= " WHERE {$pkfield}='{$pkvalue}'";
        if($this->debug == TRUE)
        {
        	log_debug($sql);
        }

        if($this->dbLayer === 'MDB2')
        {
        	if($this->_db->phptype == 'mysql')
        	{
        		$ret = $this->_execute($sql, $params);
        	}
        	else {
        		$ret = $this->_db->query($sql);
        	}
        }
        elseif($this->dbLayer === 'PDO')
        {
        	try {
        		$ret = $this->query($sql);
        	}
        	catch (PDOException $e)
    		{
    			throw new customException($e->getMessage());
    			customException::cleanUp();
    			exit;
    		}
        }
		if($this->adm == TRUE)
        {
        	if(!in_array($tablename, $this->nonmirrored))
        	{
				sql_log("[SQLDATA]".$sql."[/SQLDATA]");
        	}
        }

        if($this->objMemcache == TRUE)
    	{
    		chisimbacache::getMem()->flush();
    	}
    	if($this->objAPC == TRUE)
    	{
    		apc_clear_cache("user");
    	}
        return $ret;
    }

    /**
    * Method to delete a record from the table.
    *
    * @param  string $pkfield the name of the primary key field
    * @param  mixed  $pkvalue the value of the primary key field for the record to delete
    * @return TRUE   |FALSE TRUE on success, FALSE on failure
    * @access public
    */
    public function delete($pkfield, $pkvalue, $tablename = '') {
        if (empty($tablename)) {
            $tablename = $this->_tableName;
        }

        $params = '';

        $sql = "DELETE FROM {$tablename} WHERE {$pkfield}='{$pkvalue}'";

		if($this->debug == TRUE)
        {
        	log_debug($sql);
        }
        if($this->dbLayer === 'MDB2')
        {
        	if($this->_db->phptype == 'mysql')
        	{
        		$ret = $this->_execute($sql, $params);
        	}
        	else {
        		$ret = $this->_db->query($sql);
        	}
        }
        elseif($this->dbLayer === 'PDO')
        {
        	try {
        		$ret = $this->query($sql);
        	}
        	catch (PDOException $e)
    		{
    			throw new customException($e->getMessage());
    			customException::cleanUp();
    			exit;
    		}
        }
        
        else {
        	$ret = $this->_db->query($sql);
        }
        if($this->adm == TRUE)
        {
        	if(!in_array($tablename, $this->nonmirrored))
        	{
				sql_log("[SQLDATA]".$sql."[/SQLDATA]");
        	}
        }
        if($this->objMemcache == TRUE)
    	{
    		chisimbacache::getMem()->flush();
    	}
    	if($this->objAPC == TRUE)
    	{
    		apc_clear_cache("user");
    	}
        return $ret;
    }

    /**
    * Method to execute a SQL statement.
    *      Should not be used outside of methods of this class.
    *      Add methods here if you need functionality not available
    *      from them. Successful implementation of dynamic mirroring
    *      will depend on all database modifications being channeled
    *      through this class or its derived classes.
    *
    * @param  string $stmt   The SQL statement
    * @param  array  $params The parameters to the execute, defaults to empty
    * @return TRUE   |FALSE TRUE on success, FALSE on failure
    * @access public
    * @todo   create an alias for this for public use and mark it as private
    */
    public function _execute($stmt, $params = array()) {
        return $this->_db->query($stmt);
    	//$sh = $this->_db->prepare($stmt);
        //return ($sh->execute($params));
    }

    /**
    * Method to execute a SQL statement on an array of records (each an array itself).
    *      Should not be used outside of methods of this class for
    *      the same reasons as _execute.
    *
    * @param  string  $stmt The SQL statement
    * @param  array   $data The array of records (each arrays themselves)
    * @return TRUE    |FALSE TRUE on success, FALSE on failure
    * @access private
    */
    private function _executeMultiple($stmt, $data = array()) {
        if (!$this->condConnect()) {
            return FALSE;
        }
        $sh = $this->_db->prepare($stmt);
        $this->_db->loadModule('Extended');
        $this->_db->extended->executeMultiple($stmt, $data);
        return ($this->_db->executeMultiple($sh, $data));
    }

    /**
    * Method to fetch the primary key value of the last insert operation
    *
    * @return mixed  Primary key value of last insert.
    * @param  void
    * @access public
    */
    public function getLastInsertId() {
        return $this->_lastId;
    }

    /**
    * The error callback function, defers to configured error handler
    *
    * @param  string $error
    * @return void
    * @access public
    */
    public function errorCallback($error) {
        call_user_func($this->_errorCallback, $error);
    }

    /**
    * Method to execute a query against the database
    *
    * @param      string $stmt the SQL query string
    * @return     db     _result|FALSE a PEAR::DB_Result object, or FALSE on failure
    * @deprecated see execute()
    * @access     public
    */
    public function query($stmt) {
    	if($this->objMemcache == TRUE)
    	{
    		if(chisimbacache::getMem()->get(md5($this->cachePrefix.$stmt)))
			{
				$cache = chisimbacache::getMem()->get(md5($this->cachePrefix.$stmt));
				$ret = unserialize($cache);
			}
			else {
        		if($this->debug == TRUE)
        		{
        			log_debug($stmt);
        		}
    			$ret = $this->_queryAll($stmt);
        		if (PEAR::isError($ret)) {
            		$ret = FALSE;
        		}
        		chisimbacache::getMem()->set(md5($this->cachePrefix.$stmt), serialize($ret), MEMCACHE_COMPRESSED, $this->cacheTTL);
			}
    	}
    	elseif($this->objAPC == TRUE)
    	{
    		$ret = apc_fetch($this->cachePrefix.$stmt);
    		if($ret == FALSE)
    		{
    			$ret = $this->_queryAll($stmt);
        		if (PEAR::isError($ret)) {
            		$ret = FALSE;
        		}
    			apc_store($this->cachePrefix.$stmt, $ret, $this->cacheTTL);
    		}
    	}
    	else {
    		if($this->debug == TRUE)
    		{
    			log_debug($stmt);
    		}
    		$ret = $this->_queryAll($stmt);
    		if (PEAR::isError($ret)) {
    			$ret = FALSE;
    		}
    	}

        return $ret;
    }

    /**
    * Method to construct a sql join statement.
    *
    * The assumption is that the From table is the current table.<br>
    * An example: The join condition is (tblFrom.fk1 = tblTo.pk1)
    * <code>join('INNER JOIN', 'tblTo', array('fk1'=>'pk1')</code>
    *
    * @param  string $sqlJoinType the sql JOIN statement. (eg. 'INNER JOIN'|'LEFT JOIN')
    * @param  string $tblJoinTo   name of the table to join to.
    * @param  array  $join        the foriegn and primary keys of the two tables. (eg. array('fkfield' => 'pkfield') )
    * @param  string $tblJoinFrom name of the table being joined to.(optional)
    * @return string the sql JOIN statement.
    * @access public
    */
    public function join($sqlJoinType, $tblJoinTo, $join, $tblJoinFrom = NULL) {
        $tblJoinFrom = (is_NULL($tblJoinFrom)) ? $this->_tableName : $tblJoinFrom ;
        $strSQL = "{$sqlJoinType} {$tblJoinTo}";
        // Condtions of the join
        $insertON = TRUE;
        foreach ($join as $key => $value) {
            $strSQL .= ($insertON) ? " ON ( " : " OR ( ";
            $insertON = FALSE;
            $strSQL .= "{$tblJoinFrom}.{$key} = {$tblJoinTo}.{$value}";
            $strSQL .= " )";
        }
        if($this->debug == TRUE)
        {
        	log_debug($strSQL);
        }
        return $strSQL;
    }

    /**
    * Method to generate a unique ID for use as a primary key value
    * To ensure uniqueness amongst a mirroring cluster '@servername' is
    * appended.
    *
    * @return string The generated ID
    * @param  void
    * @access public
    */
    public function generateId() {
        $id = $this->_db->nextId($this->_tableName);
        $id = $this->_serverName . "_" . $id;
        return $id;
    }


    /**
    * Method to return the last entry in a table or
    * last entry that matches $filter
    *
    * @param string $filter     A SQL where clause
    * @param string $orderField The field to use as the
    *                           order field to mark the last entry, defaults to the
    *                           id field
    */
    public function getLastEntry($filter=NULL, $orderField="id") {
        $sql = "SELECT * FROM " . $this->_tableName . $filter  . " ORDER BY "
          . $orderField . " DESC ";
        if($this->debug == TRUE)
        {
        	log_debug($sql);
        }
        return $this->getArrayWithLimit($sql, 0, 1);
    }

    /**
     * Select a different database
     *
     * @param  string $name name of the database that should be selected
     * @return string name of the database previously connected to
     * @access public
     */
    public function setDatabaseTo($name) {
    	// PEAR::popErrorHandling();
        $ret = $this->_db->setDatabase($name);
        return $ret;
    }

    /**
     * Execute a stored procedure and return any results
     *
     * @param  string $name              string that identifies the function to execute
     * @param  mixed  $params            array that contains the paramaters to pass the stored proc
     * @param  mixed  $types             array that contains the types of the columns in
     *                                   the result set
     * @param  mixed  $result_class      string which specifies which result class to use
     * @param  mixed  $result_wrap_class string which specifies which class to wrap results in
     * @return mixed  a result handle or MDB2_OK on success, a MDB2 error on failure
     * @access public
     */
    public function executeStoredProc($name, $params = NULL, $types = NULL, $result_class = TRUE, $result_wrap_class = FALSE) {
    	return $this->_db->executeStoredProc($name, $params, $types, $result_class, $result_wrap_class);
    }

    /**
     * Return string to call a variable with the current timestamp inside an SQL statement
     * There are three special variables for current date and time:
     * - CURRENT_TIMESTAMP (date and time, TIMESTAMP type)
     * - CURRENT_DATE (date, DATE type)
     * - CURRENT_TIME (time, TIME type)
     *
     * @return string to call a variable with the current timestamp
     * @access public
     */
    public function now() {
    	if($this->dbLayer === 'MDB2')
    	{
    		return MDB2_Date::mdbNow();
    	}
    	elseif ($this->dbLayer === 'PDO')
    	{
    		return date('Y-m-d H:i:s');
    	}
    	
    }

    /**
     * list all tables in the current database
     *
     * @return mixed  data array on success, a MDB2 error on failure
     * @access public
     */
    public function listDbTables() {
    	if($this->dbLayer === 'MDB2')
    	{
    		$ret = $this->_db->mgListTables();
    		return $ret;
    	}
    	elseif ($this->dbLayer === 'PDO')
    	{
    		if($this->objEngine->pdsn['phptype'] == 'pgsql')
    		{
    			$sql = "select * from information_schema.tables where table_schema='public' and table_type='BASE TABLE'";
    			try {
    				$ret = $this->query($sql);
    			}
    			catch (PDOException $e)
    			{
    				throw new customException($e->getMessage());
    				customException::cleanUp();
    				exit;
    			}
    			foreach($ret as $tables)
    			{
    				$tbls[] = $tables['table_name'];
    			}
    			return $tbls;
    		}
    		elseif($this->objEngine->pdsn['phptype'] == 'mysql' || $this->objEngine->pdsn['phptype'] == 'mysqli')
    		{
    			$query = "SHOW /*!50002 FULL*/ TABLES";
       			if (!is_NULL($this->objEngine->pdsn['database'])) {
       				$database = $this->objEngine->pdsn['database'];
            		$query .= " FROM $database";
        		}
        		$query.= "/*!50002  WHERE Table_type = 'BASE TABLE'*/";
        		try {
        			$ret = $this->query($query);
        		}
        		catch (PDOException $e)
    			{
    				throw new customException($e->getMessage());
    				customException::cleanUp();
    				exit;
    			}
        		foreach($ret as $tables)
    			{
    				$tbls[] = $tables[0];
    			}
    			return $tbls;
    		}
    	}
    	
    }
    
     /**
     * Execute the specified query, fetch all the rows of the result set into
     * a two dimensional array and then frees the result set.
     *
     * @param   string  the SELECT query statement to be executed.
     * @param   array   optional array argument that specifies a list of
     *       expected datatypes of the result set columns, so that the eventual
     *       conversions may be performed. The default list of datatypes is
     *       empty, meaning that no conversion is performed.
     * @param   int     how the array data should be indexed
     * @param   bool    if set to TRUE, the $all will have the first
     *       column as its first dimension
     * @param   bool    used only when the query returns exactly
     *       two columns. If TRUE, the values of the returned array will be
     *       one-element arrays instead of scalars.
     * @param   bool    if TRUE, the values of the returned array is
     *       wrapped in another array.  If the same key value (in the first
     *       column) repeats itself, the values will be appended to this array
     *       instead of overwriting the existing values.
     *
     * @return  mixed   MDB2_OK or data array on success, a MDB2 error on failure
     *
     * @access  public
     */
    private function _queryAll($query, $types = array()) {
    	if($this->dbLayer === 'MDB2')
    	{
    		$ret = $this->_db->queryAll($query, $types);	
    		if (PEAR::isError($ret)) {
    			$ret = FALSE;
    		}
    	}
    	elseif ($this->dbLayer === 'PDO')
    	{
    		try {
    			$stmt = $this->_db->prepare($query);
    			$stmt->execute();
    			$ret = $stmt->fetchAll();
    			$stmt->closeCursor();
    		}
    		catch (PDOException $e)
    		{
    			throw new customException($e->getMessage());
    			customException::cleanUp();
    			exit;
    		}
    	}
    
    	return $ret;	
    }


    private function _queryRow($query) {
    	if($this->dbLayer === 'MDB2')
    	{
    		$ret = $this->_db->queryRow($query, array());
    		return $ret;
    	}
    	elseif($this->dbLayer === 'PDO')
    	{
    		try {
    			$stmt = $this->_db->prepare($query);
    			$stmt->execute();
    			$row = $stmt->fetch();
    			$stmt->closeCursor();
    		}
    		catch (PDOException $e)
    		{
    			throw new customException($e->getMessage());
    			customException::cleanUp();
    			exit;
    		}
    		return $row;
    	}
    }
    
    public function queryRow($query) {
    	return $this->_queryRow($query);
    }
}
?>