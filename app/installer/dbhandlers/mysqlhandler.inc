<?php

/**
* +--------------------------------------------------------------------+
* | Squiz.net Open Source Licence                                      |
* +--------------------------------------------------------------------+
* | Copyright (c), 2003 Squiz Pty Ltd (ABN 77 084 670 600).            |
* +--------------------------------------------------------------------+
* | This source file may be used subject to, and only in accordance    |
* | with, the Squiz Open Source Licence Agreement found at             |
* | http://www.squiz.net/licence.                                      |
* | Make sure you have read and accept the terms of that licence,      |
* | including its limitations of liability and disclaimers, before     |
* | using this software in any way. Your use of this software is       |
* | deemed to constitute agreement to be bound by that licence. If you |
* | modify, adapt or enhance this software, you agree to assign your   |
* | intellectual property rights in the modification, adaptation and   |
* | enhancement to Squiz Pty Ltd for use and distribution under that   |
* | licence.                                                           |
* +--------------------------------------------------------------------+
* 
* $Id$
* $Name$
*/

require_once INSTALL_DIR.'/dbhandlers/dbhandler.inc';

/**
* MySQLHandler
*
* Handle non-trivial database logic for mysql
*
* @author mnyeholt
* @version $version$ - 1.0
* @package MySource_Matrix
* @subpackage install
*/
class MySQLHandler extends DbHandler
{
	function MySQLHandler()
	{
		$this->minVersion = '4.0.15';
	}//end MySQLHandler()
	
	
	/**
	* To empty a database in mysql, simply drop then create it again.
	* 
	* @return void
	* @access public
	*/
	function emptyDatabase()
	{
		// get the tables needed.
		$dbname = $_SESSION['db_name'];
		
		$sql = 'SHOW TABLES';
		
		// get a list of tables in the database
		$result = $this->connection->query($sql);
		
		if (DB::isError($result)) {
			$this->errors[] = $result->getMessage();
			$this->success = false;
			return;
		} 
		
		$num_rows = $result->numRows();
		if ($num_rows <= 0) {
			$this->errors[] = 'Expected to find tables, found none.';
			//return false;
		} else {
			// now go through each table and drop it
			while ($row = $result->fetchRow()) {
				$table_name = $row[0];
				$sql = "DROP TABLE $table_name";
				$table_res = $this->connection->query($sql);
				if (DB::isError($table_res)) {
					$this->errors[] = $table_res->getMessage();
					$this->success = false;
				}
			}
		}
		
		$this->success = true;
		
	}//end emptyDatabase()
	
	
	/**
	* Get the number of tables in the database
	* 
	* @return int The number of database tables.
	* @access public
	*/
	function getNumberOfTables()
	{
		$sql = 'SHOW TABLES';
		
		// get a list of tables in the database
		$result = $this->connection->query($sql);
		
		if (DB::isError($result)) {
			$this->errors[] = $result->getMessage();
			$this->success = false;
			return 0;
		} 
		
		$num_rows = $result->numRows();
		
		return $num_rows;
	
	}//end getNumberOfTables()
	
	
}
?>
