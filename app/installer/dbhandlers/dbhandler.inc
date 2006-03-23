<?php

require_once 'MDB2/Schema.php';




/**

* DbHandler

*

* Handles installer db queries for installer

*

* @author Prince Mbekwa

* @version $version$ - 1.0

* @package 5ive

* @subpackage installer

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

	var $schema;

	

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
		$this->schema =& MDB2_Schema::factory($dsn, array('debug' => true, 'log_line_break' => '<br>'));
   		if (PEAR::isError($this->schema)) {
        	$error = $this->schema->getMessage() . ' ' . $this->schema->getUserInfo();
			return false;
   		}
   		
   		$handler->success = true;	
		return $this;
		

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
		
		$result =  $this->schema->db->getServerVersion(false);

		

		if (PEAR::isError($result)) {

			$this->errors[] = $result->getMessage();

			$this->success = false;

			return false;
			exit();

		} 

		

		

		// just search for the first instance of three numbers

		$reg = "/([0-9]+\.[0-9]+\.[0-9]+)/is";

		preg_match($reg, $result[0], $matches);

		

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

		$schema =& MDB2_Schema::factory($dsn, array('debug' => true, 'log_line_break' => '<br>'));
   		if (PEAR::isError($schema)) {
        	$error = $schema->getMessage() . ' ' . $schema->getUserInfo();
			$this->errors[] = $$error.' when connecting to database.';

			return false;

		}

		$this->success = true;

		

		return true;

		

	}//end setDSN()

	

	

}//end class

?>

