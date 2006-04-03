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

class MDB2_Reverse_TestCase extends MDB2_TestCase
{
    function setUp() {
        parent::setUp();
        $this->db->loadModule('Reverse', null, true);
    }

    /**
     * Test tableInfo('table_name')
     */
    function testTableInfo()
    {
        if (!$this->methodExists($this->db->reverse, 'tableInfo')) {
            return;
        }

        $table_info = $this->db->reverse->tableInfo('users');
        if (PEAR::isError($table_info)) {
            $this->assertTrue(false, 'Error in tableInfo(): '.$table_info->getMessage());
        } else {
            $this->assertEquals(count($this->fields), count($table_info), 'The number of fields retrieved is different from the expected one');
            foreach ($table_info as $field_info) {
                $this->assertEquals('users', $field_info['table'], "the table name is not correct");
                if (!array_key_exists(strtolower($field_info['name']), $this->fields)) {
                    $this->assertTrue(false, 'Field names do not match ('.$field_info['name'].' is unknown)');
                }
                //expand test, for instance adding a check on types...
            }
        }
    }

    /**
     * Test getTableFieldDefinition($table, $field)
     */
    function testGetTableFieldDefinition()
    {
        if (!$this->methodExists($this->db->reverse, 'getTableFieldDefinition')) {
            return;
        }

        //test integer not null
        $field_info = $this->db->reverse->getTableFieldDefinition('files', 'id');
        if (PEAR::isError($field_info)) {
            $this->assertTrue(false, 'Error in getTableFieldDefinition(): '.$field_info->getMessage());
        } else {
            $field_info = array_shift($field_info);
            $this->assertEquals('integer', $field_info['type'], 'The field type is different from the expected one');
// length is not really supported for integers
#            $this->assertEquals(4, $field_info['length'], 'The field length is different from the expected one');
            $this->assertTrue($field_info['notnull'], 'The field can be null unlike it was expected');
            $this->assertEquals('0', $field_info['default'], 'The field default value is different from the expected one');
        }

        //test blob
        $field_info = $this->db->reverse->getTableFieldDefinition('files', 'picture');
        if (PEAR::isError($field_info)) {
            $this->assertTrue(false, 'Error in getTableFieldDefinition(): '.$field_info->getMessage());
        } else {
            $field_info = array_shift($field_info);
            $this->assertEquals('blob', $field_info['type'], 'The field type is different from the expected one');
            $this->assertFalse($field_info['notnull'], 'The field cannot be null unlike it was expected');
        }

        //test varchar(100) not null
        $field_info = $this->db->reverse->getTableFieldDefinition('users', 'user_name');
        if (PEAR::isError($field_info)) {
            $this->assertTrue(false, 'Error in getTableFieldDefinition(): '.$field_info->getMessage());
        } else {
            $field_info = array_shift($field_info);
            $this->assertEquals('text', $field_info['type'], 'The field type is different from the expected one');
            $this->assertEquals(12, $field_info['length'], 'The field length is different from the expected one');
            $this->assertFalse($field_info['notnull'], 'The field can be null unlike it was expected');
            $this->assertNull($field_info['default'], 'The field default value is different from the expected one');
        }
    }

    /**
     * Test getTableIndexDefinition($table, $index)
     */
    function testGetTableIndexDefinition()
    {
        if (!$this->methodExists($this->db->reverse, 'getTableIndexDefinition')) {
            return;
        }

        //setup
        $this->db->loadModule('Manager', null, true);
        $fields = array(
            'id' => array(
                'type'     => 'integer',
                'unsigned' => 1,
                'notnull'  => 1,
                'default'  => 0,
            ),
            'somename' => array(
                'type'   => 'text',
                'length' => 12,
            ),
            'somedescription' => array(
                'type'   => 'text',
                'length' => 12,
            ),
            'sex' => array(
                'type' => 'text',
                'length' => 1,
                'default' => 'M',
            ),
        );
        $table = 'newtable';
        if ($this->tableExists($table)) {
            $result = $this->db->manager->dropTable($table);
            $this->assertFalse(PEAR::isError($result), 'Error dropping table');
        }
        $result = $this->db->manager->createTable($table, $fields);
        $this->assertFalse(PEAR::isError($result), 'Error creating table');
        $indices = array(
            'someindex' => array(
                'fields' => array(
                    'somename' => array(
                        'sorting' => 'ascending',
                    ),
                ),
                'unique' => false,
            ),
            'multipleindex' => array(
                'fields' => array(
                    'somedescription' => array(
                        'sorting' => 'ascending',
                    ),
                    'sex' => array(
                        'sorting' => 'ascending',
                    ),
                ),
            ),
        );
        foreach ($indices as $index_name => $index) {
            $result = $this->db->manager->createIndex($table, $index_name, $index);
            $this->assertFalse(PEAR::isError($result), 'Error creating index: '.$index_name);
            if (PEAR::isError($result)) {
                unset($indices[$index_name]);
            }
        }

        //test
        foreach ($indices as $index_name => $index) {
            $result = $this->db->reverse->getTableIndexDefinition($table, $index_name);
            if (PEAR::isError($result)) {
                $this->assertFalse(true, 'Error getting table index definition');
            } else {
                $field_names = array_keys($index['fields']);
                $this->assertEquals($field_names, array_keys($result['fields']), 'Error listing index fields');
                if (!empty($index['unique'])) {
                    $this->assertFalse($result['unique'], 'Error: no UNIQUE constraint expected');
                }
            }
        }
    }

    /**
     * Test getSequenceDefinition($sequence)
     */
    function testGetSequenceDefinition() {
        //setup
        $this->db->loadModule('Manager', null, true);
        $sequence = 'test_sequence';
        $sequences = $this->db->manager->listSequences();
        if (!in_array($sequence, $sequences)) {
            $result = $this->db->manager->createSequence($sequence);
            $this->assertFalse(PEAR::isError($result), 'Error creating a sequence');
        }

        //test
        $start = $this->db->nextId($sequence);
        $def = $this->db->reverse->getSequenceDefinition($sequence);
        $this->assertEquals($start+1, $def['start'], 'Error getting sequence definition');

        //cleanup
        $result = $this->db->manager->dropSequence($sequence);
        $this->assertFalse(PEAR::isError($result), 'Error dropping a sequence');
    }
}
?>