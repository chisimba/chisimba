<?php
require_once 'PHPUnit/TestCase.php';

class MDB2_TestCase extends PHPUnit_TestCase {
    //contains the dsn of the database we are testing
    var $dsn;
    //contains the options that should be used during testing
    var $options;
    //contains the name of the database we are testing
    var $database;
    //contains the MDB2 object of the db once we have connected
    var $db;
    // contains field names from the test table
    var $fields;
    // contains the types of the fields from the test table
    var $types;

    function MDB2_TestCase($name) {
        $this->PHPUnit_TestCase($name);
    }

    function setUp() {
        $this->dsn = $GLOBALS['dsn'];
        $this->options  = $GLOBALS['options'];
        $this->database = $GLOBALS['database'];
        $this->db =& MDB2::factory($this->dsn, $this->options);
        $this->db->setDatabase($this->database);
        $this->db->expectError(MDB2_ERROR_UNSUPPORTED);
        $this->fields = array(
            'user_name' => 'text',
            'user_password' => 'text',
            'subscribed' => 'boolean',
            'user_id' => 'integer',
            'quota' => 'decimal',
            'weight' => 'float',
            'access_date' => 'date',
            'access_time' => 'time',
            'approved' => 'timestamp',
        );
        $this->clearTables();
    }

    function tearDown() {
        $this->clearTables();
        $this->db->popExpect();
        unset($this->dsn);
        if (!PEAR::isError($this->db)) {
            $this->db->disconnect();
        }
        unset($this->db);
    }

    function clearTables() {
        if ($this->tableExists('users') && PEAR::isError($this->db->exec('DELETE FROM users'))) {
            $this->assertTrue(false, 'Error deleting from table users');
        }
        if ($this->tableExists('files') && PEAR::isError($this->db->exec('DELETE FROM files'))) {
            $this->assertTrue(false, 'Error deleting from table users');
        }
    }

    function supported($feature) {
        if (!$this->db->supports($feature)) {
            $this->assertTrue(false, 'This database does not support '.$feature);
            return false;
        }
        return true;
    }

    function verifyFetchedValues(&$result, $rownum, &$data) {
        $row = $result->fetchRow(MDB2_FETCHMODE_DEFAULT, $rownum);
        reset($row);
        foreach ($this->fields as $field => $type) {
            $value = current($row);
            if ($type == 'float') {
                $delta = 0.0000000001;
            } else {
                $delta = 0;
            }

            $this->assertEquals($value, $data[$field], "the value retrieved for field \"$field\" doesn't match what was stored into the row $rownum", $delta);
            next($row);
        }
    }

    function getSampleData($row) {
        $data = array();
        $data['user_name']     = 'user_' . $row;
        $data['user_password'] = 'somepassword';
        $data['subscribed']    = $row % 2 ? true : false;
        $data['user_id']       = $row;
        $data['quota']         = strval($row/100);
        $data['weight']        = sqrt($row);
        $data['access_date']   = MDB2_Date::mdbToday();
        $data['access_time']   = MDB2_Date::mdbTime();
        $data['approved']      = MDB2_Date::mdbNow();
        return $data;
    }

    function methodExists(&$class, $name) {
        if (is_object($class)
            && in_array(strtolower($name), array_map('strtolower', get_class_methods($class)))
        ) {
            return true;
        }
        $this->assertTrue(false, 'method '. $name.' not implemented in '.get_class($class));
        return false;
    }

    function tableExists($table) {
        $this->db->loadModule('Manager', null, true);
        $tables = $this->db->manager->listTables();
        return in_array(strtolower($table), array_map('strtolower', $tables));
    }
}

?>