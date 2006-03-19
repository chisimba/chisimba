<?php


require_once 'DB.php';

/**
* DbHandler
*
* Handle certain things the installer needs to do per database. Adds an
* extra layer of abstraction over PEAR::DB
*
* @author mnyeholt
* @version $version$ - 1.0
* @package MySource_Matrix
* @subpackage install
*/
class DbHandler
{
	/**
	* The dsn to connect to
	* @var string
	*/
	var $dsn;
	
	/**
	* The database connection to query over.
	* @var object
	*/
	var $connection;
	
	/**
	* Errors this class has had.
	* @var array
	*/
	var $errors = Array();
	
	/**
	* Whether this handler has done its action successfully or not
	* @var boolean
	*/
	var $success = false;
	
	/**
	* Version of the db needed for matrix
	* @var string
	*/
	var $minVersion;
	
	
	/**
	* Get a db handler class for the given dsn
	* 
	* @param string $dsn The dsn to connect to.
	* @return object the db handler for the given database
	* @access public
	*/
	function &getDbHandler($dsn)
	{
		$dsn_details = DB::parseDSN($dsn);
		$db_type = $dsn_details['phptype'];
		
		$handler_class = $db_type.'handler';
		$class_file = INSTALL_DIR.'/dbhandlers/'.$handler_class.'.inc';
		
		if (!file_exists($class_file)) {
			$this->errors[] = 'Could not find handler for database '.$db_type.' in '.$class_file;
			$this->success = false;
			return $this;
		}
		
		@include_once($class_file);
		if (!class_exists($handler_class)) {
			$this->errors[] = 'Could not find class "'.$handler_class.'" in '.$class_file;
			$this->success = false;
			return $this;
		}
		
		$handler =& new $handler_class();
		
		if (!$handler->setDSN($dsn)) {
			$this->errors += $handler->errors;
			$handler->success = false;
		} 
		
		return $handler;
		
	}//end getDbHandler()
	
	
	/**
	* Abstract emptyDatabase method
	* 
	* @return boolean Whether the database could be emptied or not.
	* @access public
	*/
	function emptyDatabase() {}
	
	
	/**
	* Get the number of tables in the database
	* 
	* @return int The number of database tables.
	* @access public
	*/
	function getNumberOfTables()
	{
		return 0;
	}//end getNumberOfTables()
	
	
	/**
	* Check the version is allowed
	* 
	* @return string The version of the database
	* @access public
	*/
	function checkVersion() 
	{
		$sql = 'SELECT version()';
		
		// get a list of tables in the database
		$result = $this->connection->query($sql);
		
		if (DB::isError($result)) {
			$this->errors[] = $result->getMessage();
			$this->success = false;
			return 0;
		} 
		
		$row = $result->fetchRow();
		
		// just search for the first instance of three numbers
		$reg = "/([0-9]+\.[0-9]+\.[0-9]+)/is";
		preg_match($reg, $row[0], $matches);
		
		$ver = $matches[1];
		
		return version_compare($ver, $this->minVersion) >= 0;
		
	}//end version()
	
	
	/**
	* Set the dsn this db handler will use
	* 
	* @param string $dsn the dsn to use.
	* @return void
	* @access public
	*/
	function setDSN($dsn) {
		
		$this->dsn = $dsn;
		$this->connection =& DB::connect($dsn);
		
		if (DB::isError($this->connection)) {
			$this->errors[] = $this->connection->getMessage().' when connecting to database.';
			return false;
		}
		$this->success = true;
		
		return true;
		
	}//end setDSN()
	
	
}//end class
?>
