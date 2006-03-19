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
* PgSQLHandler
*
* Handle non-trivial database logic for pgsql
*
* @author mnyeholt
* @version $version$ - 1.0
* @package MySource_Matrix
* @subpackage 
*/
class PgSQLHandler extends DbHandler
{
	
	/**
	* Set the minimum version of psql required
	* 
	* @return void
	* @access public
	*/
	function PgSQLHandler()
	{
		$this->minVersion = '7.3.4';
	}//end PgSQLHandler()
	
	
	/**
	* Empty out everything from the database.
	* 
	* @return boolean whether or not the emptying succeeded
	* @access public
	*/
	function emptyDatabase() 
	{
		$db_owner = $_SESSION['db_user'];
		$seq_sql = "SELECT ".
					"c.relname, ".
					"u.usename ".
					"FROM pg_class c, pg_user u WHERE u.usename='$db_owner' AND c.relkind = 'S' ORDER BY relname";
		
		// get all the sequences
		$result = $this->connection->query($seq_sql);
		if (DB::isError($result)) {
			$this->errors[] = $result->getMessage();
			$this->success = false;
			return;
		} 
		
		$num_rows = $result->numRows();
		if ($num_rows <= 0) {
			$this->errors[] = 'Expected to find sequences, found none.';
			//return false;
		} else {
			// now go through each sequence and drop it
			while ($row = $result->fetchRow()) {
				$seq_name = $row[0];
				$sql = "DROP SEQUENCE \"$seq_name\"";
				$seq_res = $this->connection->query($sql);
				if (DB::isError($seq_res)) {
					$this->errors[] = $seq_res->getMessage();
					$this->success = false;
					return;
				}
			}
		}
		
		// get all the tables
		$table_sql = "SELECT tablename FROM pg_tables WHERE tableowner='$db_owner'";
		
		$result = $this->connection->query($table_sql);
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
				$sql = "DROP TABLE \"$table_name\"";
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
		$db_owner = $_SESSION['db_user'];
		$seq_sql = "SELECT ".
					"c.relname, ".
					"u.usename ".
					"FROM pg_class c, pg_user u WHERE u.usename='$db_owner' AND c.relkind = 'S' ORDER BY relname";
		
		// get all the sequences
		$result = $this->connection->query($seq_sql);
		if (DB::isError($result)) {
			$this->errors[] = $result->getMessage();
			$this->success = false;
			return;
		} 
		
		return $result->numRows();
		
	}//end getNumberOfTables()
	
	
}//end class
?>
