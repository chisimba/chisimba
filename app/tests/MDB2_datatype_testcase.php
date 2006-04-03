<?php
// +----------------------------------------------------------------------+
// | PHP versions 4 and 5                                                 |
// +----------------------------------------------------------------------+
// | Copyright (c) 1998-2005 Lukas Smith, Lorenzo Alberton                |
// | All rights reserved.                                                 |
// +----------------------------------------------------------------------+
// | MDB2 is a merge of PEAR DB and Metabases that provides a unified DB  |
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
// | Author: Lorenzo Alberton <l dot alberton at quipo dot it>            |
// +----------------------------------------------------------------------+
//
// $Id$

require_once 'MDB2_testcase.php';

class MDB2_Datatype_TestCase extends MDB2_TestCase
{
    //test table name (it is dynamically created/dropped)
    var $table = 'datatypetable';

    function setUp() {
        parent::setUp();
        $this->db->loadModule('Manager', null, true);
        $this->fields = array(
            'id' => array(
                'type'       => 'integer',
                'unsigned'   => true,
                'notnull'    => true,
                'default'    => 0,
            ),
            'textfield'      => array(
                'type'       => 'text',
                'length'     => 12,
            ),
            'booleanfield'   => array(
                'type'       => 'boolean',
            ),
            'decimalfield'   => array(
                'type'       => 'decimal',
            ),
            'floatfield'     => array(
                'type'       => 'float',
            ),
            'datefield'      => array(
                'type'       => 'date',
            ),
            'timefield'      => array(
                'type'       => 'time',
            ),
            'timestampfield' => array(
                'type'       => 'timestamp',
            ),
        );
        if (!$this->tableExists($this->table)) {
            $this->db->manager->createTable($this->table, $this->fields);
        }
    }

    function tearDown() {
        if ($this->tableExists($this->table)) {
            $this->db->manager->dropTable($this->table);
        }
        $this->db->popExpect();
        unset($this->dsn);
        if (!PEAR::isError($this->db->manager)) {
            $this->db->disconnect();
        }
        unset($this->db);
    }
    
    /**
     * Get the types of each field given its name
     *
     * @param array $names list of field names
     * @return array $types list of matching field types
     */
    function getFieldTypes($names) {
        $types = array();
        foreach ($names as $name) {
            foreach ($this->fields as $fieldname => $field) {
                if ($name == $fieldname) {
                    $types[$name] = $field['type'];
                }
            }
        }
        return $types;
    }
    
    /**
     * Insert the values into the sample table
     *
     * @param array $values associative array (name => value)
     */
    function insertValues($values) {
        $types = $this->getFieldTypes(array_keys($values));
        
        $result = $this->db->exec('DELETE FROM '.$this->table);
        if (PEAR::isError($result)) {
            $this->assertTrue(false, 'Error emptying table: '.$result->getMessage());
        }
        
        $query = sprintf('INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', array_keys($values)),
            implode(', ', array_fill(0, count($values), '?'))
        );
        $stmt = $this->db->prepare($query, $types, false);
        if (PEAR::isError($stmt)) {
            $this->assertTrue(false, 'Error creating prepared query: '.$stmt->getMessage());
        }
        $result = $stmt->execute(array_values($values));
        if (PEAR::isError($result)) {
            $this->assertTrue(false, 'Error executing prepared query: '.$result->getMessage());
        }
        $stmt->free();
    }

    /**
     * Select the inserted row from the db and check the inserted values
     * @param array $values associative array (name => value) of inserted data
     */
    function selectAndCheck($values) {
        $types = $this->getFieldTypes(array_keys($values));
    
        $query = 'SELECT '. implode (', ', array_keys($values)). ' FROM '.$this->table;
        $result = $this->db->queryRow($query, $types, MDB2_FETCHMODE_ASSOC);
        foreach ($values as $name => $value) {
            $this->assertEquals($result[$name], $values[$name], 'Error in '.$types[$name].' value: incorrect conversion');
        }
    }

    /**
     * Test the TEXT datatype for incorrect conversions
     */
    function testTextDataType() {
        $data = array(
            'id'        => 1,
            'textfield' => 'test',
        );
        $this->insertValues($data);
        $this->selectAndCheck($data);
    }

    /**
     * Test the DECIMAL datatype for incorrect conversions
     */
    function testDecimalDataType() {
        $data = array(
            'id'           => 1,
            'decimalfield' => 10.35,
        );
        $this->insertValues($data);
        $this->selectAndCheck($data);
    }

    /**
     * Test the FLOAT datatype for incorrect conversions
     */
    function testFloatDataType() {
        $data = array(
            'id'         => 1,
            'floatfield' => 10.35,
        );
        $this->insertValues($data);
        $this->selectAndCheck($data);
    }

    /**
     * Test the BOOLEAN datatype for incorrect conversions
     */
    function testBooleanDataType() {
        $data = array(
            'id'           => 1,
            'booleanfield' => true,
        );
        $this->insertValues($data);
        $this->selectAndCheck($data);
        
        $data = array(
            'id'           => 2,
            'booleanfield' => false,
        );
        $this->insertValues($data);
        $this->selectAndCheck($data);
    }

    /**
     * Test the DATE datatype for incorrect conversions
     */
    function testDateDataType() {
        $data = array(
            'id'        => 1,
            'datefield' => date('Y-m-d'),
        );
        $this->insertValues($data);
        $this->selectAndCheck($data);
    }

    /**
     * Test the TIME datatype for incorrect conversions
     */
    function testTimeDataType() {
        $data = array(
            'id'        => 1,
            'timefield' => date('H:i:s'),
        );
        $this->insertValues($data);
        $this->selectAndCheck($data);
    }

    /**
     * Test the TIMESTAMP datatype for incorrect conversions
     */
    function testTimestampDataType() {
        $data = array(
            'id'             => 1,
            'timestampfield' => date('Y-m-d H:i:s'),
        );
        $this->insertValues($data);
        $this->selectAndCheck($data);
    }
}

?>