<?php
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1998-2004 Manuel Lemos, Tomas V.V.Cox,                 |
// | Stig. S. Bakken, Lukas Smith                                         |
// | All rights reserved.                                                 |
// +----------------------------------------------------------------------+
// | MDB is a merge of PEAR DB and Metabases that provides a unified DB   |
// | API as well as database abstraction for PHP applications.            |
// | This LICENSE is in the BSD license style.                            |
// |                                                                      |
// | Redistribution and use in source and binary forms, with or without   |
// | modification, are permitted provided that the following conditions   |
// | are met:                                                             |
// |                                                                      |
// | Redistributions of source code must retain the above copyright       |
// | notice, this list of conditions and the following disclaimer.        |
// |                                                                      |
// | Redistributions in binary form must reproduce the above copyright    |
// | notice, this list of conditions and the following disclaimer in the  |
// | documentation and/or other materials provided with the distribution. |
// |                                                                      |
// | Neither the name of Manuel Lemos, Tomas V.V.Cox, Stig. S. Bakken,    |
// | Lukas Smith nor the names of his contributors may be used to endorse |
// | or promote products derived from this software without specific prior|
// | written permission.                                                  |
// |                                                                      |
// | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS  |
// | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT    |
// | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS    |
// | FOR A PARTICULAR PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE      |
// | REGENTS OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,          |
// | INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, |
// | BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS|
// |  OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED  |
// | AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT          |
// | LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY|
// | WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE          |
// | POSSIBILITY OF SUCH DAMAGE.                                          |
// +----------------------------------------------------------------------+
// | Author: YOUR NAME <YOUR EMAIL>                                       |
// +----------------------------------------------------------------------+
//
// $Id$
//

// This is just a skeleton MDB driver.

// In each of the listed methods I have added comments that tell you where
// to look for a "reference" implementation.
// Many of the methods below are taken from Metabase. Most of the methods
// marked as "new in MDB" are actually taken from the latest beta files of
// Metabase. However these beta files only include a version for MySQL.
// Some of these methods have been expanded or changed slightly in MDB.
// Looking in the relevant MDB Wrapper should give you some pointers, some
// other difference you will only discover by looking at one of the existing
// MDB driver or the common implementation in common.php.
// One thing that will definately have to be modified in all "reference"
// implementations of Metabase methods is the error handling.
// Anyways don't worry if you are having problems: Lukas Smith is here to help!

if(!defined('MDB_MANAGER_XXX_INCLUDED'))
{
    define('MDB_MANAGER_XXX_INCLUDED',1);

require_once('MDB/Modules/Manager/Common.php');

/**
 * MDB Xxx driver for the management modules
 *
 * @package MDB
 * @category Database
 * @author  YOUR NAME <YOUR EMAIL>
 */
class MDB_Manager_xxx_ extends MDB_Manager_Common
{
    // }}}
    // {{{ createDatabase()

    /**
     * create a new database
     *
     * @param object    $dbs        database object that is extended by this class
     * @param string $name name of the database that should be created
     * @return mixed MDB_OK on success, a MDB error on failure
     * @access public
     */
    function createDatabase(&$db, $name)
    {
        // take this from the corresponding Metabase driver: CreateDatabase()
    }

    // }}}
    // {{{ dropDatabase()

    /**
     * drop an existing database
     *
     * @param object    $dbs        database object that is extended by this class
     * @param string $name name of the database that should be dropped
     * @return mixed MDB_OK on success, a MDB error on failure
     * @access public
     */
    function dropDatabase(&$db, $name)
    {
        // take this from the corresponding Metabase driver: DropDatabase()
    }

    // }}}
    // {{{ createTable()

    /**
     * create a new table
     *
     * @param object    $dbs        database object that is extended by this class
     * @param string $name     Name of the database that should be created
     * @param array $fields Associative array that contains the definition of each field of the new table
     *                        The indexes of the array entries are the names of the fields of the table an
     *                        the array entry values are associative arrays like those that are meant to be
     *                         passed with the field definitions to get[Type]Declaration() functions.
     *
     *                        Example
     *                        array(
     *
     *                            'id' => array(
     *                                'type' => 'integer',
     *                                'unsigned' => 1
     *                                'notnull' => 1
     *                                'default' => 0
     *                            ),
     *                            'name' => array(
     *                                'type' => 'text',
     *                                'length' => 12
     *                            ),
     *                            'password' => array(
     *                                'type' => 'text',
     *                                'length' => 12
     *                            )
     *                        );
     * @return mixed MDB_OK on success, a MDB error on failure
     * @access public
     */
    function createTable(&$db, $name, $fields)
    {
        // take this from the corresponding Metabase driver: CreateTable()
    }

    // }}}
    // {{{ alterTable()

    /**
     * alter an existing table
     *
     * @param object    $dbs        database object that is extended by this class
     * @param string $name         name of the table that is intended to be changed.
     * @param array $changes     associative array that contains the details of each type
     *                             of change that is intended to be performed. The types of
     *                             changes that are currently supported are defined as follows:
     *
     *                             name
     *
     *                                New name for the table.
     *
     *                            AddedFields
     *
     *                                Associative array with the names of fields to be added as
     *                                 indexes of the array. The value of each entry of the array
     *                                 should be set to another associative array with the properties
     *                                 of the fields to be added. The properties of the fields should
     *                                 be the same as defined by the Metabase parser.
     *
     *                                Additionally, there should be an entry named Declaration that
     *                                 is expected to contain the portion of the field declaration already
     *                                 in DBMS specific SQL code as it is used in the CREATE TABLE statement.
     *
     *                            RemovedFields
     *
     *                                Associative array with the names of fields to be removed as indexes
     *                                 of the array. Currently the values assigned to each entry are ignored.
     *                                 An empty array should be used for future compatibility.
     *
     *                            RenamedFields
     *
     *                                Associative array with the names of fields to be renamed as indexes
     *                                 of the array. The value of each entry of the array should be set to
     *                                 another associative array with the entry named name with the new
     *                                 field name and the entry named Declaration that is expected to contain
     *                                 the portion of the field declaration already in DBMS specific SQL code
     *                                 as it is used in the CREATE TABLE statement.
     *
     *                            ChangedFields
     *
     *                                Associative array with the names of the fields to be changed as indexes
     *                                 of the array. Keep in mind that if it is intended to change either the
     *                                 name of a field and any other properties, the ChangedFields array entries
     *                                 should have the new names of the fields as array indexes.
     *
     *                                The value of each entry of the array should be set to another associative
     *                                 array with the properties of the fields to that are meant to be changed as
     *                                 array entries. These entries should be assigned to the new values of the
     *                                 respective properties. The properties of the fields should be the same
     *                                 as defined by the Metabase parser.
     *
     *                                If the default property is meant to be added, removed or changed, there
     *                                 should also be an entry with index ChangedDefault assigned to 1. Similarly,
     *                                 if the notnull constraint is to be added or removed, there should also be
     *                                 an entry with index ChangedNotNull assigned to 1.
     *
     *                                Additionally, there should be an entry named Declaration that is expected
     *                                 to contain the portion of the field changed declaration already in DBMS
     *                                 specific SQL code as it is used in the CREATE TABLE statement.
     *                            Example
     *                                array(
     *                                    'name' => 'userlist',
     *                                    'AddedFields' => array(
     *                                        'quota' => array(
     *                                            'type' => 'integer',
     *                                            'unsigned' => 1
     *                                            'Declaration' => 'quota INT'
     *                                        )
     *                                    ),
     *                                    'RemovedFields' => array(
     *                                        'file_limit' => array(),
     *                                        'time_limit' => array()
     *                                        ),
     *                                    'ChangedFields' => array(
     *                                        'gender' => array(
     *                                            'default' => 'M',
     *                                            'ChangeDefault' => 1,
     *                                            'Declaration' => "gender CHAR(1) DEFAULT 'M'"
     *                                        )
     *                                    ),
     *                                    'RenamedFields' => array(
     *                                        'sex' => array(
     *                                            'name' => 'gender',
     *                                            'Declaration' => "gender CHAR(1) DEFAULT 'M'"
     *                                        )
     *                                    )
     *                                )
     *
     * @param boolean $check     indicates whether the function should just check if the DBMS driver
     *                             can perform the requested table alterations if the value is true or
     *                             actually perform them otherwise.
     * @access public
     *
      * @return mixed MDB_OK on success, a MDB error on failure
     */
    function alterTable(&$db, $name, $changes, $check)
    {
        // take this from the corresponding Metabase driver: AlterTable()
    }

    // }}}
    // {{{ listDatabases()

    /**
     * list all databases
     *
     * @param object    $dbs        database object that is extended by this class
     * @return mixed data array on success, a MDB error on failure
     * @access public
     */
    function listDatabases(&$db)
    {
        // new in MDB
    }

    // }}}
    // {{{ listUsers()

    /**
     * list all users
     *
     * @param object    $dbs        database object that is extended by this class
     * @return mixed data array on success, a MDB error on failure
     * @access public
     */
    function listUsers(&$db)
    {
        // new in MDB
    }

    // }}}
    // {{{ listTables()

    /**
     * list all tables in the current database
     *
     * @param object    $dbs        database object that is extended by this class
     * @return mixed data array on success, a MDB error on failure
     * @access public
     */
    function listTables(&$db)
    {
        // new in MDB
    }

    // }}}
    // {{{ listTableFields()

    /**
     * list all fields in a tables in the current database
     *
     * @param object    $dbs        database object that is extended by this class
     * @param string $table name of table that should be used in method
     * @return mixed data array on success, a MDB error on failure
     * @access public
     */
    function listTableFields(&$db, $table)
    {
        // new in MDB
    }

    // }}}
    // {{{ getTableFieldDefinition()

    /**
     * get the stucture of a field into an array
     *
     * @param object    $dbs        database object that is extended by this class
     * @param string    $table         name of table that should be used in method
     * @param string    $field_name     name of field that should be used in method
     * @return mixed data array on success, a MDB error on failure
     * @access public
     */
    function getTableFieldDefinition(&$db, $table, $field_name)
    {
        // new in MDB
    }

    // }}}
    // {{{ createIndex()

    /**
     * get the stucture of a field into an array
     *
     * @param object    $dbs        database object that is extended by this class
     * @param string    $table         name of the table on which the index is to be created
     * @param string    $name         name of the index to be created
     * @param array     $definition        associative array that defines properties of the index to be created.
     *                                 Currently, only one property named FIELDS is supported. This property
     *                                 is also an associative with the names of the index fields as array
     *                                 indexes. Each entry of this array is set to another type of associative
     *                                 array that specifies properties of the index that are specific to
     *                                 each field.
     *
     *                                Currently, only the sorting property is supported. It should be used
     *                                 to define the sorting direction of the index. It may be set to either
     *                                 ascending or descending.
     *
     *                                Not all DBMS support index sorting direction configuration. The DBMS
     *                                 drivers of those that do not support it ignore this property. Use the
     *                                 function support() to determine whether the DBMS driver can manage indexes.

     *                                 Example
     *                                    array(
     *                                        'FIELDS' => array(
     *                                            'user_name' => array(
     *                                                'sorting' => 'ascending'
     *                                            ),
     *                                            'last_login' => array()
     *                                        )
     *                                    )
     * @return mixed MDB_OK on success, a MDB error on failure
     * @access public
     */
    function createIndex(&$db, $table, $name, $definition)
    {
        // take this from the corresponding Metabase driver: CreateIndex()
    }

    // }}}
    // {{{ dropIndex()

    /**
     * drop existing index
     *
     * @param object    $dbs        database object that is extended by this class
     * @param string    $table         name of table that should be used in method
     * @param string    $name         name of the index to be dropped
     * @return mixed MDB_OK on success, a MDB error on failure
     * @access public
     */
    function dropIndex(&$db, $table, $name)
    {
        // take this from the corresponding Metabase driver: DropIndex()
    }

    // }}}
    // {{{ listTableIndexes()

    /**
     * list all indexes in a table
     *
     * @param object    $dbs        database object that is extended by this class
     * @param string    $table      name of table that should be used in method
     * @return mixed data array on success, a MDB error on failure
     * @access public
     */
    function listTableIndexes(&$db, $table)
    {
        // new in MDB
    }

    // }}}
    // {{{ getTableIndexDefinition()

    /**
     * get the stucture of an index into an array
     *
     * @param object    $dbs        database object that is extended by this class
     * @param string    $table      name of table that should be used in method
     * @param string    $index_name name of index that should be used in method
     * @return mixed data array on success, a MDB error on failure
     * @access public
     */
    function getTableIndexDefinition(&$db, $table, $index_name)
    {
        // new in MDB
    }

    // }}}
    // {{{ createSequence()

    /**
     * create sequence
     *
     * @param object    $dbs        database object that is extended by this class
     * @param string    $seq_name     name of the sequence to be created
     * @param string    $start         start value of the sequence; default is 1
     * @return mixed MDB_OK on success, a MDB error on failure
     * @access public
     */
    function createSequence(&$db, $seq_name, $start)
    {
        // take this from the corresponding Metabase driver: CreateSequence()
    }

    // }}}
    // {{{ dropSequence()

    /**
     * drop existing sequence
     *
     * @param object    $dbs        database object that is extended by this class
     * @param string    $seq_name     name of the sequence to be dropped
     * @return mixed MDB_OK on success, a MDB error on failure
     * @access public
     */
    function dropSequence(&$db, $seq_name)
    {
        // take this from the corresponding Metabase driver: DropSequence()
    }

    // }}}
    // {{{ listSequences()

    /**
     * list all sequences in the current database
     *
     * @param object    $dbs        database object that is extended by this class
     * @return mixed data array on success, a MDB error on failure
     * @access public
     */
    function listSequences(&$db)
    {
        // new in MDB
    }

    // }}}
    // {{{ getSequenceDefinition()

    /**
     * get the stucture of a sequence into an array
     *
     * @param object    $dbs        database object that is extended by this class
     * @param string    $sequence   name of sequence that should be used in method
     * @return mixed data array on success, a MDB error on failure
     * @access public
     */
    function getSequenceDefinition(&$db, $sequence)
    {
        // new in MDB
    }
}

}
?>