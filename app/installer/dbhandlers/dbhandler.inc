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

	public $_globalObjDb;

	public $_globalObjDbManager;

	public function __construct()
	{

			$this->_objDbConfig = $_SESSION['dsn'];
			// Connect to the database
            require_once 'MDB2.php';
            //MDB2 has a factory method, so lets use it now...
            $this->_globalObjDb = &MDB2::factory($_SESSION['dsn']);
            //var_dump($this->_globalObjDb);
            //$this->_globalObjDb->setOption('portability', MDB2_PORTABILITY_FIX_CASE); // ^ MDB2_PORTABILITY_EMPTY_TO_NULL);
			MDB2::loadFile('Date');
			MDB2::loadFile('Iterator');
			// install the error handler with our custom callback on error

            // set the default fetch mode for the DB to assoc, as that's
            // a much nicer mode than the default MDB2_FETCHMODE_ORDERED
            $this->_globalObjDb->setFetchMode(MDB2_FETCHMODE_ASSOC);
            $this->_globalObjDb->setOption('portability',MDB2_PORTABILITY_FIX_CASE);
            $this->_globalObjDb->setOption('portability', MDB2_PORTABILITY_ALL);
			$_SESSION['DbObj'] =  $this->_globalObjDb;
		 	require_once 'MDB2/Schema.php';
            //MDB2 has a factory method, so lets use it now...
             $this->_globalObjDbManager = &MDB2_Schema::factory($_SESSION['dsn']);

             $_SESSION['pearDbManager'] = $this->_globalObjDbManager;
             $this->dbmanager = new dbHandlermanager();
             parent::__construct();

	}

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
	 * Create Tables for new 5ive installations
	 *
	 * @return TRUE / FALSE boolean
	 * @access Public
	 */
	public function createTables($tablename,$fields,$options)
	{
		try {

			$ret = $this->dbmanager->createTable($tablename, $fields, $options);
			if (!$ret) {

	        		throw new Exception('Error while creating table');
	   		}

	   		return true;
		}catch (Exception $e){
			return false;
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
    	try {

	        $ret = $this->dbmanager->createTableIndex($tableName,$keyname,$index);
	        if (PEAR::isError($ret)) {

	        		throw new Exception('Error while creating table indexes');
	   		}
	        return TRUE;
    	}catch (Exception $e){
			return $e->getMessage();
		}
    }

	/**
     * create a new database
     *
     * @param string $db name of the database that should be created
     * @return bool true on success
     * @access public
     */
    public function createDb($db)
    {
    	$ret = $this->dbmanager->createDb($db);
    	return $ret;
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

