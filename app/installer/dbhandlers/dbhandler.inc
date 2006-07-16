<?php

require_once 'MDB2.php';

require_once INSTALL_DIR.'/dbhandlers/dbhandlermanager.inc';


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

class DbHandler extends dbHandlermanager 

{

	/**

	* The dsn to connect to

	* @var string

	*/

	public  $dsn;

	

	/**

	* The database connection to query over.

	* @var object

	*/

	private $schema;

	
    private $dbmanager;
	/**

	* Errors this class has had.

	* @var array

	*/

	public  $errors = Array();

	

	/**

	* Whether this handler has done its action successfully or not

	* @var boolean

	*/

	public $success = false;

	

	/**

	* Version of the db needed for 5ive

	* @var string

	*/

	public $minVersion;

	
 	

	/**

	* Get a db handler class for the given dsn

	* 

	* @param string $dsn The dsn to connect to.

	* @return object the db handler for the given database

	* @access public

	*/

	public function &getDbHandler($dsn)

	{
		try{
			$this->schema =&MDB2::factory($dsn, array('debug' => 2, 'log_line_break' => '<br>'));
			$this->schema->setOption('portability', MDB2_PORTABILITY_FIX_CASE);
			MDB2::loadFile('Date');
			MDB2::loadFile('Iterator');
			
	   		if (PEAR::isError($this->schema)) {
	           	throw new Exception();
	        	
	   		}
	   		$_SESSION['pearDbManager'] =$this->schema; 
	   		$this->dbmanager = new dbHandlermanager();
	   		$this->schema->disconnect();
	   		$this->success =true;	
			return $this;
		}catch (Exception $e){
			
			return false;
		}

	}//end getDbHandler()

	
	/**

	* Abstract emptyDatabase method

	* 

	* @return boolean Whether the database could be emptied or not.

	* @access public

	*/

	public function emptyDatabase($db,$dsn) {
		
		try {
			$this->schema =& MDB2_Schema::factory($dsn, array('debug' => true, 'log_line_break' => '<br>'));	
			$ret = $this->schema->db->CreateDatabase($db);
			
			if (PEAR::isError($ret)) {
	        	$ret = $this->schema->db->DropDatabase($db);
	        	        	
	        	$ret = $this->schema->db->CreateDatabase($db);
	        	
	   		}
	   		$this->schema->disconnect();
	   		$this->success = true;
			return true;
		}catch (Exception $e){
			return false;
		}
	}

	

	

	/**

	* Get the number of tables in the database

	* 

	* @return int The number of database tables.

	* @access public

	*/

	public function getNumberOfTables()

	{
		return $this->dbmanager->listDbTables();

	}//end getNumberOfTables()

	

	

	/**

	* Check the version is allowed

	* 

	* @return string The version of the database

	* @access public

	*/

	public function checkVersion($dsn) 

	{
		try{

			$sql = 'SELECT version()';
	
			$this->schema =&MDB2::factory($dsn, array('debug' => true, 'log_line_break' => '<br>'));
			
	   		if (PEAR::isError($this->schema)) {
	        	$error = $this->schema->getMessage() . ' ' . $this->schema->getUserInfo();
	        	throw new Exception();
	   		}
	
			// get a list of tables in the database
					
			$result =  $this->schema->getServerVersion(false);
			if (MDB2::isError($this->schema)) {
	
				$this->errors[] =  $this->schema->getMessage();
				 $schema->disconnect();
				$this->success = false;
				throw new Exception();
	
			} 
	
						
			$ver = $result[0];
					
	
			return version_compare($ver, $this->minVersion) >= 0;
		}catch (Exception $e){
			
			return false;
		}
		

	}//end version()

	

	

	/**

	* Set the dsn this db handler will use

	* 

	* @param string $dsn the dsn to use.

	* @return void

	* @access public

	*/

	public function setDSN($dsn) {

	try{	

		$this->dsn = $dsn;

		$schema =& MDB2_Schema::factory($dsn, array('debug' => true, 'log_line_break' => '<br>'));
   		if (PEAR::isError($schema)) {
        	$error = $schema->getMessage() . ' ' . $schema->getUserInfo();
			$this->errors[] = $$error.' when connecting to database.';

			throw new Exception("Exception caught");

		}

		$this->success = true;

		

		return true;
	}catch (Exception $e){
		
		return false;
	}

		

	}//end setDSN()

	

}//end class

?>

