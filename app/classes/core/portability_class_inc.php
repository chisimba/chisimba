<?php

/**
 * Methods to improve database portability
 *
 * @package    core
 * @author     Daniel Convissor <danielc@analysisandsolutions.com>
 * @author     Paul Scott - Modifications to allow use within the 5ive (KINKY2) Framework
 * @copyright  2004-2005 The Analysis and Solutions Company
 * @license    http://www.analysisandsolutions.com/software/license.txt  Simple Public License
 * @link       http://www.analysisandsolutions.com/presentations/portability/
 */
class portability {

    /**
     * Establishes the settings the Portability class needs
     *
     * @param   object   $db  the PEAR DB object you're using
     * @return  void
     */
    function portability($db) {
        $this->phptype  = $db->dsn['phptype'];
        $this->dbsyntax = $db->dsn['dbsyntax'];

        $this->setAsKeyword();
        $this->setBooleanType();
        $this->setClobType();
        $this->setConcatenationQueryOperator();
        $this->setDateQueryFormat();
        $this->setDateType();
        $this->setDateLiteralType();
        $this->setDecimalType();
        $this->setDropCascade();
        $this->setDropRestrict();
        $this->setNullKeyword();
        $this->setTimestampQueryFormat();
        $this->setTimestampSettingQuery();
        $this->setTimestampType();
        $this->setTimestampLiteralType();

        $this->executeTimestampSettingQuery($db);
    }

    /**
     * Converts boolean-like input into true boolean values
     *
     * @param   mixed    $in   the boolean-like input to be converted
     * @return  boolean  true or false, depending upon the input.
     *                    Returns NULL if the input isn't boolean-like.
     */
    function castToBoolean($in) {
        if (empty($bool_cast_types)) {
            $bool_cast_types = array(
                1       => true,
                't'     => true,
                'True'  => true,
                0       => false,
                'f'     => false,
                'False' => false,
            );
        }
        if (!isset($bool_cast_types[$in])) {
            if (empty($in)) {
                return null;
            }
            die('>' . $in . '< unknown datatype');
        }
        return $bool_cast_types[$in];
    }

    /**
     * Executes the SQL query needed to modify this DBMS's timestamp format
     * setting
     *
     * @param   object   $db   the PEAR DB object you're using
     * @return  bool     true if no problems happen or a query doesn't
     *                    need execution
     */
    function executeTimestampSettingQuery($db) {
        if (!$this->TimestampSettingQuery) {
            return true;
        }
        $res = $db->query($this->TimestampSettingQuery);
        if (MDB2::isError($res)) {
            if ($db->getOption('debug') >= 2) {
                die($res->getUserInfo());
            } else {
                die($res->getMessage());
            }
        }
        return true;
    }

    /**
     * Turns the items you submit into a concatenate phrase
     *
     * @param   array|string  $in   the items you want to concatenate
     * @return  string        the query fragment your DBMS needs
     */
    function formatConcatenationQuery($in) {
        $in = (array) $in;
        if ($this->ConcatenationQueryOperator == 'CONCAT()') {
            return ' CONCAT(' . implode(', ', $in) . ') ';
        } else {
            return implode(' ' . $this->ConcatenationQueryOperator . ' ', $in);
        }
    }

    /**
     * Returns the query string fragment needed for the current DBMS to
     * produce an ISO formatted date from a date column
     *
     * @param   string   $col  the database column to get the data from
     * @return  string   the query fragment your DBMS needs
     */
    function formatDateQuery($col) {
        return sprintf($this->DateQueryFormat, $col);
    }

    /**
     * Returns the query string fragment needed for the current DBMS to
     * produce an ISO formatted timestamp from a timestamp column
     *
     * @param   string   $col  the database column to get the data from
     * @return  string   the query fragment your DBMS needs
     */
    function formatTimestampQuery($col) {
        return sprintf($this->TimestampQueryFormat, $col);
    }

    /**
     * Returns the SQL keyword signifying identifier aliases
     *
     * @return  string   the query fragment your DBMS needs
     */
    function getAsKeyword() {
        return $this->AsKeyword;
    }

    /**
     * Returns the SQL keyword for BOOLEAN data types in CREATE TABLE
     * statements
     *
     * @return  string   the query fragment your DBMS needs
     */
    function getBooleanType() {
        return $this->BooleanType;
    }

    /**
     * Returns the SQL keyword for CLOB data types in CREATE TABLE
     * statements
     *
     * @return  string   the query fragment your DBMS needs
     */
    function getClobType() {
        return $this->ClobType;
    }

    /**
     * Returns the SQL keyword indicating the literal following it is a date
     *
     * @return  string   the query fragment your DBMS needs
     */
    function getDateLiteralType() {
        return $this->DateLiteralType;
    }

    /**
     * Returns the SQL keyword for DATE data types in CREATE TABLE statements
     *
     * @return  string   the query fragment your DBMS needs
     */
    function getDateType() {
        return $this->DateType;
    }

    /**
     * Returns the regular expression needed for DECIMAL data types
     *
     * @return  string   the regular expression needed
     */
    function getDecimalType() {
        return $this->DecimalType;
    }
    /**
     * Returns the SQL fragment needed for creating DECIMAL data types
     * in CREATE TABLE statements
     *
     * @return  string   the query fragment your DBMS needs
     */
    function formatDecimalType($precision, $scale) {
        return preg_replace('/(\(\d+, *\d+\))/',
                            $this->DecimalType,
                            "($precision, $scale)");
    }

    /**
     * Returns the SQL keyword indicating a DROP TABLE statement should
     * CASCADE to related tables
     *
     * @return  string   the query fragment your DBMS needs
     */
    function getDropCascade() {
        return $this->DropCascade;
    }

    /**
     * Returns the SQL keyword indicating a DROP TABLE statement should
     * RESTRICT itself to the present table
     *
     * @return  string   the query fragment your DBMS needs
     */
    function getDropRestrict() {
        return $this->DropRestrict;
    }

    /**
     * Returns the SQL keyword used in CREATE TABLE statements to indicate
     * a column should allow NULL values
     *
     * @return  string   the query fragment your DBMS needs
     */
    function getNullKeyword() {
        return $this->NullKeyword;
    }

    /**
     * the sprintf() format string needed to modify a SELECT query statement
     * so it returns a DATE column in YYYY-MM-DD HH:MM:SS format
     *
     * @return  string   the query fragment your DBMS needs
     */
    function getTimestampLiteralType() {
        return $this->TimestampLiteralType;
    }

    /**
     * Returns the SQL keyword for TIMESTAMP data types in CREATE TABLE
     * statements
     *
     * @return  string   the query fragment your DBMS needs
     */
    function getTimestampType() {
        return $this->TimestampType;
    }

    /**
     * Sets the named property according to which DBMS is in use
     *
     * @return boolean  true if successful.  Does die() if the DBMS is unkown.
     */
    function setAsKeyword() {
        switch ($this->phptype . ':' . $this->dbsyntax) {
            case 'fbsql:fbsql':
            case 'ibase:firebird':
            case 'mssql:mssql':
            case 'mysql:mysql':
            case 'mysqli:mysqli':
            case 'odbc:access':
            case 'odbc:db2':
            case 'pgsql:pgsql':
            case 'sqlite:sqlite':
            case 'sybase:sybase':
                $this->AsKeyword = ' AS ';
                break;
            case 'oci8:oci8':
                $this->AsKeyword = ' ';
                break;
            default:
                $this->AsKeyword = false;
                die('unknown phptype/dbsyntax in setAsKeyword()');
        }
        return true;
    }

    /**
     * Sets the named property according to which DBMS is in use
     *
     * @return boolean  true if successful.  Does die() if the DBMS is unkown.
     */
    function setBooleanType() {
        switch ($this->phptype . ':' . $this->dbsyntax) {
            case 'fbsql:fbsql':
            case 'pgsql:pgsql':
                $this->BooleanType = ' BOOLEAN ';
                break;
            case 'oci8:oci8':
                $this->BooleanType = ' NUMBER(1) ';
                break;
            case 'ibase:firebird':
            case 'odbc:access':
            case 'odbc:db2':
            case 'sqlite:sqlite':
                $this->BooleanType = ' SMALLINT ';
                break;
            case 'mssql:mssql':
            case 'mysql:mysql':
            case 'mysqli:mysqli':
            case 'sybase:sybase':
                $this->BooleanType = ' TINYINT ';
                break;
            default:
                $this->BooleanType = false;
                die('unknown phptype/dbsyntax in setBooleanType()');
        }
        return true;
    }

    /**
     * Sets the named property according to which DBMS is in use
     *
     * @return boolean  true if successful.  Does die() if the DBMS is unkown.
     */
    function setClobType() {
        switch ($this->phptype . ':' . $this->dbsyntax) {
            case 'ibase:firebird':
                $this->ClobType = ' BLOB ';
                break;
            case 'fbsql:fbsql':
            case 'oci8:oci8':
            case 'odbc:db2':
            case 'sqlite:sqlite':
                $this->ClobType = ' CLOB ';
                break;
            case 'mysql:mysql':
            case 'mysqli:mysqli':
            case 'odbc:access':
            case 'pgsql:pgsql':
            case 'mssql:mssql':
            case 'sybase:sybase':
                $this->ClobType = ' TEXT ';
                break;
            default:
                $this->ClobType = false;
                die('unknown phptype/dbsyntax in setClobType()');
        }
        return true;
    }

    /**
     * Sets the named property according to which DBMS is in use
     *
     * @return boolean  true if successful.  Does die() if the DBMS is unkown.
     */
    function setConcatenationQueryOperator() {
        switch ($this->phptype . ':' . $this->dbsyntax) {
            case 'fbsql:fbsql':
            case 'ibase:firebird':
            case 'oci8:oci8':
            case 'odbc:db2':
            case 'pgsql:pgsql':
            case 'sqlite:sqlite':
            case 'sybase:sybase':
                $this->ConcatenationQueryOperator = ' || ';
                break;
            case 'odbc:access':
            case 'mssql:mssql':
                $this->ConcatenationQueryOperator = ' + ';
                break;
            case 'mysql:mysql':
            case 'mysqli:mysqli':
                $this->ConcatenationQueryOperator = 'CONCAT()';
                break;
            default:
                $this->ConcatenationQueryOperator = false;
                die('unknown phptype/dbsyntax in setConcatenationQueryOperator()');
        }
        return true;
    }

    /**
     * Sets the named property according to which DBMS is in use
     *
     * @return boolean  true if successful.  Does die() if the DBMS is unkown.
     */
    function setDateLiteralType() {
        switch ($this->phptype . ':' . $this->dbsyntax) {
            case 'fbsql:fbsql':
            case 'oci8:oci8':
                $this->DateLiteralType = ' DATE ';
                break;
            case 'ibase:firebird':
            case 'mssql:mssql':
            case 'mysql:mysql':
            case 'mysqli:mysqli':
            case 'odbc:access':
            case 'odbc:db2':
            case 'pgsql:pgsql':
            case 'sqlite:sqlite':
            case 'sybase:sybase':
                $this->DateLiteralType = ' ';
                break;
            default:
                $this->DateLiteralType = false;
                die('unknown phptype/dbsyntax in setDateLiteralType()');
        }
        return true;
    }

    /**
     * Sets the named property according to which DBMS is in use
     *
     * @return boolean  true if successful.  Does die() if the DBMS is unkown.
     */
    function setDateQueryFormat() {
        switch ($this->phptype . ':' . $this->dbsyntax) {
            case 'fbsql:fbsql':
            case 'ibase:firebird':
            case 'mysql:mysql':
            case 'mysqli:mysqli':
            case 'odbc:db2':
            case 'pgsql:pgsql':
            case 'sqlite:sqlite':
                $this->DateQueryFormat = ' %s ';
                break;
            case 'odbc:access':
                $this->DateQueryFormat = " FORMAT(%s, 'yyyy-mm-dd') ";
                break;
            case 'oci8:oci8':
                $this->DateQueryFormat = " TO_CHAR(%s, 'YYYY-MM-DD') ";
                break;
            case 'mssql:mssql':
                $this->DateQueryFormat = ' CONVERT(CHAR(10), %s, 120) ';
                break;
            case 'sybase:sybase':
                $this->DateQueryFormat = ' STR_REPLACE('
                        . " CONVERT(CHAR(10), %s, 102), '.', '-') ";
                break;
            default:
                $this->DateQueryFormat = false;
                die('unknown phptype/dbsyntax in setDateQueryFormat()');
        }
        return true;
    }

    /**
     * Sets the named property according to which DBMS is in use
     *
     * @return boolean  true if successful.  Does die() if the DBMS is unkown.
     */
    function setDateType() {
        switch ($this->phptype . ':' . $this->dbsyntax) {
            case 'fbsql:fbsql':
            case 'ibase:firebird':
            case 'mysql:mysql':
            case 'mysqli:mysqli':
            case 'oci8:oci8':
            case 'odbc:db2':
            case 'pgsql:pgsql':
            case 'sqlite:sqlite':
                $this->DateType = ' DATE ';
                break;
            case 'mssql:mssql':
            case 'odbc:access':
            case 'sybase:sybase':
                $this->DateType = ' DATETIME ';
                break;
            default:
                $this->DateType = false;
                die('unknown phptype/dbsyntax in setDateType()');
        }
        return true;
    }

    /**
     * Sets the named property according to which DBMS is in use
     *
     * @return boolean  true if successful.  Does die() if the DBMS is unkown.
     */
    function setDecimalType() {
        switch ($this->phptype . ':' . $this->dbsyntax) {
            case 'odbc:access':
                $this->DecimalType = ' NUMERIC ';
                break;
            case 'fbsql:fbsql':
            case 'ibase:firebird':
            case 'mssql:mssql':
            case 'mysql:mysql':
            case 'mysqli:mysqli':
            case 'oci8:oci8':
            case 'odbc:db2':
            case 'pgsql:pgsql':
            case 'sqlite:sqlite':
            case 'sybase:sybase':
                $this->DecimalType = ' DECIMAL\\1 ';
                break;
            default:
                $this->DecimalType = false;
                die('unknown phptype/dbsyntax in setDecimalType()');
        }
        return true;
    }

    /**
     * Sets the named property according to which DBMS is in use
     *
     * @return boolean  true if successful.  Does die() if the DBMS is unkown.
     */
    function setDropCascade() {
        switch ($this->phptype . ':' . $this->dbsyntax) {
            case 'fbsql:fbsql':
                $this->DropCascade = ' CASCADE ';
                break;
            case 'ibase:firebird':
            case 'mssql:mssql':
            case 'mysql:mysql':
            case 'mysqli:mysqli':
            case 'oci8:oci8':
            case 'odbc:access':
            case 'odbc:db2':
            case 'pgsql:pgsql':
            case 'sqlite:sqlite':
            case 'sybase:sybase':
                $this->DropCascade = ' ';
                break;
            default:
                $this->DropCascade = false;
                die('unknown phptype/dbsyntax in setDropCascade()');
        }
        return true;
    }

    /**
     * Sets the named property according to which DBMS is in use
     *
     * @return boolean  true if successful.  Does die() if the DBMS is unkown.
     */
    function setDropRestrict() {
        switch ($this->phptype . ':' . $this->dbsyntax) {
            case 'fbsql:fbsql':
                $this->DropRestrict = ' RESTRICT ';
                break;
            case 'ibase:firebird':
            case 'mssql:mssql':
            case 'mysql:mysql':
            case 'mysqli:mysqli':
            case 'oci8:oci8':
            case 'odbc:access':
            case 'odbc:db2':
            case 'pgsql:pgsql':
            case 'sqlite:sqlite':
            case 'sybase:sybase':
                $this->DropRestrict = ' ';
                break;
            default:
                $this->DropRestrict = false;
                die('unknown phptype/dbsyntax in setDropRestrict()');
        }
        return true;
    }

    /**
     * Sets the named property according to which DBMS is in use
     *
     * @return boolean  true if successful.  Does die() if the DBMS is unkown.
     */
    function setNullKeyword() {
        switch ($this->phptype . ':' . $this->dbsyntax) {
            case 'mssql:mssql':
            case 'mysql:mysql':
            case 'mysqli:mysqli':
            case 'oci8:oci8':
            case 'odbc:access':
            case 'pgsql:pgsql':
            case 'sqlite:sqlite':
            case 'sybase:sybase':
                $this->NullKeyword = ' NULL ';
                break;
            case 'fbsql:fbsql':
            case 'ibase:firebird':
            case 'odbc:db2':
                $this->NullKeyword = ' ';
                break;
            default:
                $this->NullKeyword = false;
                die('unknown phptype/dbsyntax in setNullKeyword()');
        }
        return true;
    }

    /**
     * Sets the named property according to which DBMS is in use
     *
     * @return boolean  true if successful.  Does die() if the DBMS is unkown.
     */
    function setTimestampLiteralType() {
        switch ($this->phptype . ':' . $this->dbsyntax) {
            case 'fbsql:fbsql':
            case 'oci8:oci8':
                $this->TimestampLiteralType = ' TIMESTAMP ';
                break;
            case 'ibase:firebird':
            case 'mssql:mssql':
            case 'mysql:mysql':
            case 'mysqli:mysqli':
            case 'odbc:access':
            case 'odbc:db2':
            case 'pgsql:pgsql':
            case 'sqlite:sqlite':
            case 'sybase:sybase':
                $this->TimestampLiteralType = ' ';
                break;
            default:
                $this->TimestampLiteralType = false;
                die('unknown phptype/dbsyntax in setTimestampLiteralType()');
        }
        return true;
    }

    /**
     * Sets the named property according to which DBMS is in use
     *
     * @return boolean  true if successful.  Does die() if the DBMS is unkown.
     */
    function setTimestampQueryFormat() {
        switch ($this->phptype . ':' . $this->dbsyntax) {
            case 'fbsql:fbsql':
            case 'ibase:firebird':
            case 'mysql:mysql':
            case 'mysqli:mysqli':
            case 'oci8:oci8':
            case 'odbc:access':
            case 'pgsql:pgsql':
            case 'sqlite:sqlite':
                $this->TimestampQueryFormat = ' %s ';
                break;
            case 'mssql:mssql':
                $this->TimestampQueryFormat = ' CONVERT(CHAR(19), %s, 120) ';
                break;
            case 'odbc:db2':
                $this->TimestampQueryFormat = " TO_CHAR(%s, 'YYYY-MM-DD HH24:MI:SS') ";
                break;
            case 'sybase:sybase':
                $this->TimestampQueryFormat = ' STR_REPLACE('
                        . " CONVERT(CHAR(10), %1\$s, 102), '.', '-')"
                        . " + ' ' + CONVERT(CHAR(8), %1\$s, 20) ";
                break;
            default:
                $this->TimestampQueryFormat = false;
                die('unknown phptype/dbsyntax in setTimestampQueryFormat()');
        }
        return true;
    }

    /**
     * Sets the named property according to which DBMS is in use
     *
     * @return boolean  true if successful.  Does die() if the DBMS is unkown.
     */
    function setTimestampSettingQuery() {
        switch ($this->phptype . ':' . $this->dbsyntax) {
            case 'fbsql:fbsql':
            case 'ibase:firebird':
            case 'mysql:mysql':
            case 'mysqli:mysqli':
            case 'odbc:access':
            case 'odbc:db2':
            case 'sqlite:sqlite':
                $this->TimestampSettingQuery = '';
                break;
            case 'oci8:oci8':
                $this->TimestampSettingQuery = 'ALTER SESSION SET'
                        . " NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'";
                break;
            case 'pgsql:pgsql':
                $this->TimestampSettingQuery = "SET DATESTYLE = 'ISO'";
                break;
            case 'mssql:mssql':
            case 'sybase:sybase':
                $this->TimestampSettingQuery = 'SET DATEFORMAT ymd';
                break;
            default:
                $this->TimestampSettingQuery = false;
                die('unknown phptype/dbsyntax in setTimestampSettingQuery()');
        }
        return true;
    }

    /**
     * Sets the named property according to which DBMS is in use
     *
     * @return boolean  true if successful.  Does die() if the DBMS is unkown.
     */
    function setTimestampType() {
        switch ($this->phptype . ':' . $this->dbsyntax) {
            case 'oci8:oci8':
                $this->TimestampType = ' DATE ';
                break;
            case 'mysql:mysql':
            case 'mysqli:mysqli':
            case 'mssql:mssql':
            case 'odbc:access':
            case 'sybase:sybase':
                $this->TimestampType = ' DATETIME ';
                break;
            case 'fbsql:fbsql':
            case 'ibase:firebird':
            case 'odbc:db2':
            case 'sqlite:sqlite':
                $this->TimestampType = ' TIMESTAMP ';
                break;
            case 'pgsql:pgsql':
                $this->TimestampType = ' TIMESTAMP(0) ';
                break;
            default:
                $this->TimestampType = false;
                die('unknown phptype/dbsyntax in setTimestampType()');
        }
        return true;
    }

}
?>