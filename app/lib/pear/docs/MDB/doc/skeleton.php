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
// There may be methods missing as this skeleton is based on the methods
// implemented by the MySQL and PostGreSQL drivers in MDB.
// Some methods may not have to be implemented in the driver, because the
// implementation in common.php is compatible with the given RDBMS.
// In each of the listed methods I have added comments that tell you where
// to look for a "reference" implementation.
// Some of these methods have been expanded or changed slightly in MDB.
// Looking in the relevant MDB Wrapper should give you some pointers, some
// other difference you will only discover by looking at one of the existing
// MDB driver or the common implementation in common.php.
// One thing that will definately have to be modified in all "reference"
// implementations of Metabase methods is the error handling.
// Anyways don't worry if you are having problems: Lukas Smith is here to help!

require_once('MDB/Common.php');

/**
 * MDB XXX driver
 *
 * @package MDB
 * @category Database
 * @author  YOUR NAME <YOUR EMAIL>
 */
class MDB_xxx extends MDB_Common
{
// Most of the class variables are taken from the corresponding Metabase driver.
// Few are taken from the corresponding PEAR DB driver.
// Some are MDB specific.
    var $connection = 0;
    var $connected_host;
    var $connected_user;
    var $connected_password;
    var $connected_port;
    var $opened_persistent = '';

    var $escape_quotes = "\\";
    var $decimal_factor = 1.0;

    var $highest_fetched_row = array();
    var $columns = array();

    // }}}
    // {{{ constructor

    /**
    * Constructor
    */
    function MDB_xxx()
    {
        $this->MDB_common();
        $this->phptype = 'xxx';
        $this->dbsyntax = 'xxx';

        // most of the following codes needs to be taken from the corresponding Metabase driver setup() methods
        
        // the error code maps from corresponding PEAR DB driver constructor

        // also please remember to "register" all driver specific options here like so
        // $this->options['option_name'] = 'non NULL default value';
    }

    // }}}
    // {{{ errorNative()

    /**
     * Get the native error code of the last error (if any) that
     * occured on the current connection.
     *
     * @access public
     *
     * @return int native XXX error code
     */
    function errorNative()
    {
        // take this method from the corresponding PEAR DB driver: errorNative()
    }

    // }}}
    // {{{ xxxRaiseError()

    /**
     * This method is used to communicate an error and invoke error
     * callbacks etc.  Basically a wrapper for MDB::raiseError
     * that checks for native error msgs.
     *
     * @param integer $errno error code
     * @param string  $message userinfo message
     * @return object a PEAR error object
     * @access public
     * @see PEAR_Error
     */
    function xxxRaiseError($errno = NULL, $message = NULL)
    {
        // take this method from the corresponding PEAR DB driver: xxxRaiseError()
    }

    // }}}
    // {{{ autoCommit()

    /**
     * Define whether database changes done on the database be automatically
     * committed. This function may also implicitly start or end a transaction.
     *
     * @param boolean $auto_commit    flag that indicates whether the database
     *                                changes should be committed right after
     *                                executing every query statement. If this
     *                                argument is 0 a transaction implicitly
     *                                started. Otherwise, if a transaction is
     *                                in progress it is ended by committing any
     *                                database changes that were pending.
     *
     * @access public
     *
     * @return mixed MDB_OK on success, a MDB error on failure
     */
    function autoCommit($auto_commit)
    {
        // take this from the corresponding Metabase driver: AutoCommitTransactions()
        // the MetabaseShutdownTransactions function is handled by the PEAR desctructor
    }

    // }}}
    // {{{ commit()

    /**
     * Commit the database changes done during a transaction that is in
     * progress. This function may only be called when auto-committing is
     * disabled, otherwise it will fail. Therefore, a new transaction is
     * implicitly started after committing the pending changes.
     *
     * @access public
     *
     * @return mixed MDB_OK on success, a MDB error on failure
     */
    function commit()
    {
        // take this from the corresponding Metabase driver: CommitTransaction()
    }

    // }}}
    // {{{ rollback()

    /**
     * Cancel any database changes done during a transaction that is in
     * progress. This function may only be called when auto-committing is
     * disabled, otherwise it will fail. Therefore, a new transaction is
     * implicitly started after canceling the pending changes.
     *
     * @access public
     *
     * @return mixed MDB_OK on success, a MDB error on failure
     */
    function rollback()
    {
        // take this from the corresponding Metabase driver: RollbackTransaction()
    }

    // }}}
    // {{{ connect()

    /**
     * Connect to the database
     *
     * @return TRUE on success, MDB_Error on failure
     **/
    function connect()
    {
        // take this from the corresponding Metabase driver: Connect() and Setup()
        if (PEAR::isError(PEAR::loadExtension($this->phptype))) {
            return(PEAR::raiseError(NULL, MDB_ERROR_NOT_FOUND,
                NULL, NULL, 'extension '.$this->phptype.' is not compiled into PHP',
                'MDB_Error', TRUE));
        }
    }

    // }}}
    // {{{ _close()
    /**
     * all the RDBMS specific things needed close a DB connection
     *
     * @access private
     *
     */
    function _close()
    {
        // take this from the corresponding Metabase driver: Close()
    }

    // }}}
    // {{{ query()

    /**
     * Send a query to the database and return any results
     *
     * @access public
     *
     * @param string  $query  the SQL query
     * @param array   $types  array that contains the types of the columns in
     *                        the result set
     *
     * @return mixed a result handle or MDB_OK on success, a MDB error on failure
     */
    function query($query, $types = NULL)
    {
        // take this from the corresponding Metabase driver: Query()
    }

    // }}}
    // {{{ subSelect()

    /**
     * simple subselect emulation for Mysql
     *
     * @access public
     *
     * @param string $query the SQL query for the subselect that may only
     *                      return a column
     * @param string $quote determines if the data needs to be quoted before
     *                      being returned
     *
     * @return string the query
     */
    function subSelect($query, $quote = FALSE)
    {
        // This is a new method that only needs to be added if the RDBMS does
        // not support sub-selects. See the MySQL driver for an example
    }

    // }}}
    // {{{ replace()

    /**
     * Execute a SQL REPLACE query. A REPLACE query is identical to a INSERT
     * query, except that if there is already a row in the table with the same
     * key field values, the REPLACE query just updates its values instead of
     * inserting a new row.
     *
     * The REPLACE type of query does not make part of the SQL standards. Since
     * practically only MySQL implements it natively, this type of query is
     * emulated through this method for other DBMS using standard types of
     * queries inside a transaction to assure the atomicity of the operation.
     *
     * @access public
     *
     * @param string $table name of the table on which the REPLACE query will
     *  be executed.
     * @param array $fields associative array that describes the fields and the
     *  values that will be inserted or updated in the specified table. The
     *  indexes of the array are the names of all the fields of the table. The
     *  values of the array are also associative arrays that describe the
     *  values and other properties of the table fields.
     *
     *  Here follows a list of field properties that need to be specified:
     *
     *    Value:
     *          Value to be assigned to the specified field. This value may be
     *          of specified in database independent type format as this
     *          function can perform the necessary datatype conversions.
     *
     *    Default:
     *          this property is required unless the Null property
     *          is set to 1.
     *
     *    Type
     *          Name of the type of the field. Currently, all types Metabase
     *          are supported except for clob and blob.
     *
     *    Default: text
     *
     *    Null
     *          Boolean property that indicates that the value for this field
     *          should be set to NULL.
     *
     *          The default value for fields missing in INSERT queries may be
     *          specified the definition of a table. Often, the default value
     *          is already NULL, but since the REPLACE may be emulated using
     *          an UPDATE query, make sure that all fields of the table are
     *          listed in this function argument array.
     *
     *    Default: 0
     *
     *    Key
     *          Boolean property that indicates that this field should be
     *          handled as a primary key or at least as part of the compound
     *          unique index of the table that will determine the row that will
     *          updated if it exists or inserted a new row otherwise.
     *
     *          This function will fail if no key field is specified or if the
     *          value of a key field is set to NULL because fields that are
     *          part of unique index they may not be NULL.
     *
     *    Default: 0
     *
     * @return mixed MDB_OK on success, a MDB error on failure
     */
    function replace($table, $fields)
    {
        // take this from the corresponding Metabase driver: Replace()
    }

    // }}}
    // {{{ getColumnNames()

    /**
     * Retrieve the names of columns returned by the DBMS in a query result.
     *
     * @param resource   $result    result identifier
     * @return mixed                an associative array variable
     *                              that will hold the names of columns. The
     *                              indexes of the array are the column names
     *                              mapped to lower case and the values are the
     *                              respective numbers of the columns starting
     *                              from 0. Some DBMS may not return any
     *                              columns when the result set does not
     *                              contain any rows.
     *
     *                              a MDB error on failure
     * @access public
     */
    function getColumnNames($result)
    {
        // take this from the corresponding Metabase driver: GetColumnNames()
    }

    // }}}
    // {{{ numCols()

    /**
     * Count the number of columns returned by the DBMS in a query result.
     *
     * @param resource    $result        result identifier
     * @access public
     * @return mixed integer value with the number of columns, a MDB error
     *                       on failure
     */
    function numCols($result)
    {
        // take this from the corresponding Metabase driver: NumberOfColumns()
    }

    // }}}
    // {{{ endOfResult()

    /**
    * check if the end of the result set has been reached
    *
    * @param resource    $result result identifier
    * @return mixed TRUE or FALSE on sucess, a MDB error on failure
    * @access public
    */
    function endOfResult($result)
    {
        // take this from the corresponding Metabase driver: EndOfResult()
    }

    // }}}
    // {{{ _retrieveLob()

    /**
     * fetch a float value from a result set
     *
     * @param int $lob handle to a lob created by the createLob() function
     * @return mixed MDB_OK on success, a MDB error on failure
     * @access private
     */
    function _retrieveLob($lob)
    {
        // take this from the corresponding Metabase driver: RetrieveLOB()
    }

    // }}}
    // {{{ endOfResultLob()

    /**
     * Determine whether it was reached the end of the large object and
     * therefore there is no more data to be read for the its input stream.
     *
     * @param int    $lob handle to a lob created by the createLob() function
     * @return mixed TRUE or FALSE on success, a MDB error on failure
     * @access public
     */
    function endOfResultLob($lob)
    {
        // take this from the corresponding Metabase driver: EndOfResultLOB()
    }

    // }}}
    // {{{ _readResultLob()

    /**
     * Read data from large object input stream.
     *
     * @param int $lob handle to a lob created by the createLob() function
     * @param blob $data reference to a variable that will hold data to be
     *      read from the large object input stream
     * @param int $length integer value that indicates the largest ammount of
     *      data to be read from the large object input stream.
     * @return mixed length on success, a MDB error on failure
     * @access private
     */
    function _readResultLob($lob, &$data, $length)
    {
        // take this from the corresponding Metabase driver: ReadResultLOB()
    }

    // }}}
    // {{{ _destroyResultLob()

    /**
     * Free any resources allocated during the lifetime of the large object
     * handler object.
     *
     * @param int $lob handle to a lob created by the createLob() function
     * @access private
     */
    function _destroyResultLob($lob)
    {
        // take this from the corresponding Metabase driver: DestroyResultLOB()
    }

    // }}}
    // {{{ fetch()

    /**
    * fetch value from a result set
    *
    * @param resource    $result result identifier
    * @param int    $row    number of the row where the data can be found
    * @param int    $field    field number where the data can be found
    * @return mixed string on success, a MDB error on failure
    * @access public
    */
    function fetch($result, $row, $field)
    {
        // take this from the corresponding Metabase driver: FetchResult()
    }

    // }}}
    // {{{ fetchClob()

    /**
    * fetch a clob value from a result set
    *
    * @param resource    $result result identifier
    * @param int    $row    number of the row where the data can be found
    * @param int    $field    field number where the data can be found
    * @return mixed content of the specified data cell, a MDB error on failure,
    *               a MDB error on failure
    * @access public
    */
    function fetchClob($result, $row, $field)
    {
        // take this from the corresponding Metabase driver: FetchCLOB()
    }

    // }}}
    // {{{ fetchBlob()

    /**
    * fetch a blob value from a result set
    *
    * @param resource    $result result identifier
    * @param int    $row    number of the row where the data can be found
    * @param int    $field    field number where the data can be found
    * @return mixed content of the specified data cell, a MDB error on failure
    * @access public
    */
    function fetchBlob($result, $row, $field)
    {
        // take this from the corresponding Metabase driver: FetchBLOB()
    }

    // }}}
    // {{{ resultIsNull()

    /**
     * Determine whether the value of a query result located in given row and
     *   field is a NULL.
     *
     * @param resource    $result result identifier
     * @param int    $row    number of the row where the data can be found
     * @param int    $field    field number where the data can be found
     * @return mixed TRUE or FALSE on success, a MDB error on failure
     * @access public
     */
    function resultIsNull($result, $row, $field)
    {
        // take this from the corresponding Metabase driver: ResultIsNull()
    }

    // }}}
    // {{{ convertResult()

    /**
    * convert a value to a RDBMS indepdenant MDB type
    *
    * @param mixed  $value   value to be converted
    * @param int    $type    constant that specifies which type to convert to
    * @return mixed converted value
    * @access public
    */
    function convertResult($value, $type)
    {
        // take this from the corresponding Metabase driver: ConvertResult()
    }

    // }}}
    // {{{ numRows()

    /**
    * returns the number of rows in a result object
    *
     * @param ressource $result a valid result ressouce pointer
    * @return mixed MDB_Error or the number of rows
    * @access public
    */
    function numRows($result)
    {
        // take this from the corresponding Metabase driver: NumberOfRows()
    }

    // }}}
    // {{{ freeResult()

    /**
     * Free the internal resources associated with $result.
     *
     * @param $result result identifier
     * @return bool TRUE on success, FALSE if $result is invalid
     * @access public
     */
    function freeResult($result)
    {
        // take this from the corresponding Metabase driver: FreeResult()
    }

    // }}}
    // {{{ get*Declaration()

    // take phpdoc comments from MDB common.php: get*Declaration()

    function get*Declaration($name, $field)
    {
        // take this from the corresponding Metabase driver: Get*FieldValue()
    }

    // }}}
    // {{{ get*Value()

    /**
     * Convert a text value into a DBMS specific format that is suitable to
     * compose query statements.
     *
     * @param resource  $prepared_query query handle from prepare()
     * @param           $parameter
     * @param           $clob
     * @return string  text string that represents the given argument value in
     *                 a DBMS specific format.
     * @access public
     */
    function get*Value($prepared_query, $parameter, $clob)
    {
        // take this from the corresponding Metabase driver: Get*FieldValue()
    }

    // }}}
    // {{{ getClobValue()

    /**
     * Convert a text value into a DBMS specific format that is suitable to
     * compose query statements.
     *
     * @param resource  $prepared_query query handle from prepare()
     * @param           $parameter
     * @param           $clob
     * @return string  text string that represents the given argument value in
     *                 a DBMS specific format.
     * @access public
     */
    function getClobValue($prepared_query, $parameter, $clob)
    {
        // take this from the corresponding Metabase driver: GetCLOBFieldValue()
    }

    // }}}
    // {{{ freeClobValue()

    /**
     * free a chracter large object
     *
     * @param resource  $prepared_query query handle from prepare()
     * @param string    $clob
     * @param string    $value
     * @return MDB_OK
     * @access public
     */
    function freeClobValue($prepared_query, $clob, &$value)
    {
        // take this from the corresponding Metabase driver: FreeClobValue()
    }

    // }}}
    // {{{ getBlobValue()

    /**
     * Convert a text value into a DBMS specific format that is suitable to
     * compose query statements.
     *
     * @param resource  $prepared_query query handle from prepare()
     * @param           $parameter
     * @param           $blob
     * @return string  text string that represents the given argument value in
     *                 a DBMS specific format.
     * @access public
     */
    function getBlobValue($prepared_query, $parameter, $blob)
    {
        // take this from the corresponding Metabase driver: GetBLOBFieldValue()
    }

    // }}}
    // {{{ freeBlobValue()

    /**
     * free a binary large object
     *
     * @param resource  $prepared_query query handle from prepare()
     * @param string    $blob
     * @return MDB_OK
     * @access public
     */
    function freeBlobValue($prepared_query, $blob)
    {
        // take this from the corresponding Metabase driver: FreeBlobValue()
    }

    // }}}
    // {{{ nextId()

    /**
     * returns the next free id of a sequence
     *
     * @param string  $seq_name name of the sequence
     * @param boolean $ondemand when true the seqence is
     *                          automatic created, if it
     *                          not exists
     *
     * @return mixed MDB_Error or id
     * @access public
     */
    function nextId($seq_name, $ondemand = FALSE)
    {
        // take this from the corresponding PEAR DB driver: nextId()
    }


    // }}}
    // {{{ currId()

    /**
     * returns the current id of a sequence
     *
     * @param string  $seq_name name of the sequence
     * @return mixed MDB_Error or id
     * @access public
     */
    function currId($seq_name)
    {
        // take this from the corresponding Metabase driver: GetSequenceCurrentValue()
    }

    // }}}
    // {{{ fetchInto()

    /**
     * Fetch a row and insert the data into an existing array.
     *
     * @param resource  $result     result identifier
     * @param int       $fetchmode  how the array data should be indexed
     * @param int       $rownum     the row number to fetch
     * @return int data array on success, a MDB error on failure
     * @access public
     */
    function fetchInto($result, $fetchmode = MDB_FETCHMODE_DEFAULT, $rownum = 0)
    {
        // take this from the corresponding Metabase driver: FetchResultArray()
        // possibly you also need to take code from Metabases FetchRow() method
        // note Metabases FetchRow() method should not be confused with MDB's fetchRow()
    }

    // }}}
    // {{{ nextResult()

    /**
     * Move the internal mysql result pointer to the next available result
     * Currently not supported
     *
     * @param a valid result resource
     * @return true if a result is available otherwise return false
     * @access public
     */
    function nextResult($result)
    {
        // take this from the corresponding PEAR DB driver: nextResult()
    }

    // }}}
    // {{{ tableInfo()

    /**
    * returns meta data about the result set
    *
    * @param resource    $result    result identifier
    * @param mixed $mode depends on implementation
    * @return array an nested array, or a MDB error
    * @access public
    */
    function tableInfo($result, $mode = NULL) {
        // take this from the corresponding PEAR DB driver: tableInfo()
    }
}

?>