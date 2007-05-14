<?php
// +----------------------------------------------------------------------+
// | PHP versions 4 and 5                                                 |
// +----------------------------------------------------------------------+
// | Copyright (c) 1998-2006 Manuel Lemos, Tomas V.V.Cox,                 |
// | Stig. S. Bakken, Lukas Smith                                         |
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
// | Author: Igor Feghali <ifeghali@php.net>                              |
// +----------------------------------------------------------------------+
//
// $Id$
//

require_once 'XML/Unserializer.php';
require_once 'MDB2/Schema/Validate.php';

if (empty($GLOBALS['_MDB2_Schema_Reserved'])) {
    $GLOBALS['_MDB2_Schema_Reserved'] = array();
}

/**
 * Parses an XML schema file
 *
 * @package MDB2_Schema
 * @category Database
 * @access protected
 * @author Igor Feghali <ifeghali@php.net>
 */
class MDB2_Schema_Parser2 extends XML_Unserializer
{
    var $database_definition = array('tables' => array(), 'sequences' => array());
    var $database_loaded = array();
    var $variables = array();
    var $error;
    var $structure = false;
    var $validator;
    var $options = array();

    function __construct($variables, $fail_on_invalid_names = true, $structure = false, $valid_types = array(), $force_defaults = true)
    {
        // force ISO-8859-1 due to different defaults for PHP4 and PHP5
        // todo: this probably needs to be investigated some more and cleaned up
        $this->options['encoding'] = 'ISO-8859-1';
        $this->options['XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE'] = true;
        $this->options['XML_UNSERIALIZER_OPTION_ATTRIBUTES_ARRAYKEY'] = false;
        $this->options['forceEnum'] = array('table', 'field', 'index', 'insert', 'update', 'delete');
        /*
         * todo: find a way to force the following items not to be parsed as arrays
         * as it cause problems in functions with multiple arguments
         */
        //$this->options['forceNEnum'] = array('value', 'column');
        $this->variables = $variables;
        $this->structure = $structure;
        $this->validator =& new MDB2_Schema_Validate($fail_on_invalid_names, $valid_types, $force_defaults);
        parent::XML_Unserializer($this->options);
    }

    function MDB2_Schema_Parser2($variables, $fail_on_invalid_names = true, $structure = false, $valid_types = array(), $force_defaults = true)
    {
        $this->__construct($variables, $fail_on_invalid_names, $structure, $valid_types, $force_defaults);
    }

    function parse()
    {
        $result = $this->unserialize($this->filename, true);

        if (PEAR::isError($result)) {
            return $result;
        } else {
            $this->database_definition = $this->getUnserializedData();
            $this->fixDatabaseKeys();

            if (!empty($this->database_definition['sequences']) && is_array($this->database_definition['sequences'])) {
                foreach ($this->database_definition['sequences'] as $k => $v) {
                    $result = $this->validator->validateSequence($this->database_definition['sequences'], $v, $v['name']);
                    if (PEAR::isError($result)) {
                        $this->raiseError($result->getUserinfo(), 0, $xp, $result->getCode());
                    }
                }
            }

            $result = $this->validator->validateDatabase($this->database_definition);
            if (PEAR::isError($result)) {
                $this->raiseError($result->getUserinfo(), 0, $xp, $result->getCode());
            }
        }

        return MDB2_OK;
    }

    function setInputFile($filename)
    {
        $this->filename = $filename;
        return MDB2_OK;
    }

    function renameKey(&$arr, $oKey, $nKey)
    {
        $arr[$nKey] = &$arr[$oKey];
        unset($arr[$oKey]);
    }

    function fixDatabaseKeys()
    {
        if (!empty($this->database_definition['table']) && is_array($this->database_definition['table'])) {
            $this->renameKey($this->database_definition, 'table', 'tables');
            foreach ($this->database_definition['tables'] as $k => $v) {
                $this->fixTableKeys($k);
            }
        }

        if (!empty($this->database_definition['sequence']) && is_array($this->database_definition['sequence'])) {
            $this->renameKey($this->database_definition, 'sequece', 'sequences');
            foreach ($this->database_definition['sequences'] as $k => $v) {
                $this->fixSequenceKeys($k);
            }
        }
    }

    function fixTableKeys($k)
    {
        $table = $this->database_definition['tables'][$k]['name'];
        unset($this->database_definition['tables'][$k]['name']);
        $this->renameKey($this->database_definition['tables'], $k, $table);

        $this->fixTableFieldKeys($table);
        $this->fixTableIndexKeys($table);

        foreach ($this->database_definition['tables'][$table]['declaration'] as $k => $v) {
            $this->database_definition['tables'][$table][$k] = $v;
        }
        unset($this->database_definition['tables'][$table]['declaration']);

        $this->fixTableInitializationKeys($table);
    }

    function fixTableFieldKeys($table)
    {
        foreach ($this->database_definition['tables'][$table]['declaration']['field'] as $k => $v) {
            $nKey = $this->database_definition['tables'][$table]['declaration']['field'][$k]['name'];
            unset($this->database_definition['tables'][$table]['declaration']['field'][$k]['name']);
            $this->renameKey($this->database_definition['tables'][$table]['declaration']['field'], $k, $nKey);
        }
        $this->renameKey($this->database_definition['tables'][$table]['declaration'], 'field', 'fields');
    }

    function fixTableIndexKeys($table)
    {
        if (!empty($this->database_definition['tables'][$table]['declaration']['index']) && is_array($this->database_definition['tables'][$table]['declaration']['index'])) {
            foreach ($this->database_definition['tables'][$table]['declaration']['index'] as $k => $v) {
                $nKey = $this->database_definition['tables'][$table]['declaration']['index'][$k]['name'];
                unset($this->database_definition['tables'][$table]['declaration']['index'][$k]['name']);
                $this->renameKey($this->database_definition['tables'][$table]['declaration']['index'], $k, $nKey);
            }
        }
        $this->renameKey($this->database_definition['tables'][$table]['declaration'], 'index', 'indexes');
    }

    function fixTableInitializationKeys($table)
    {
        $dml = array( 'insert', 'update', 'delete' );
        $init = array();

        foreach ($dml as $type) {
            if (!empty($this->database_definition['tables'][$table]['initialization'][$type]) && is_array($this->database_definition['tables'][$table]['initialization'][$type])) {
                foreach ($this->database_definition['tables'][$table]['initialization'][$type] as $k => $v) {
                    $this->fixTableInitializationDataKeys($v);
                    $init[] = array( 'type' => $type, 'data' => $v);
                }
            }
        }
        unset($this->database_definition['tables'][$table]['initialization']);
        $this->database_definition['tables'][$table]['initialization'] = &$init;
    }

    function fixTableInitializationDataKeys(&$element)
    {
        if (!empty($element['field']) && is_array($element['field'])) {
            foreach ($element['field'] as $k => $v) {
                $name = $v['name'];
                unset($v['name']);

                $this->setExpression($v);
                $field = array( 'name' => $name, 'group' => $v );
                $element['field'][$k] = $field;
            }
        }
        if (!empty($element['where']) && is_array($element['where'])) {
            $this->setExpression($element['where']);
        }
    }

    function setExpression(&$arr)
    {
        $element = each($arr);
        $arr = array( 'type' => $element['key'] );
        $element = $element['value'];

        switch ($arr['type']) {
            case 'null':
            case 'value':
            case 'column':
                $arr['data'] = $element;
            break;
            case 'function':
                if (!empty($element)
                    && is_array($element)
                ) {
                    $arr['data'] = array( 'name' => $element['name'], 'arguments' => array() );
                    unset($element['name']);

                    foreach ($element as $k => $v) {
                        $argument = array( $k => $v );
                        $this->setExpression($argument);
                        $arr['data']['arguments'][] = $argument;
                    }
                }
            break;
            case 'expression':
                $arr['data'] = array( 'operator' => $element['operator'], 'operants' => array() );
                unset($element['operator']);

                foreach ($element as $k => $v) {
                    $argument = array( $k => $v );
                    $this->setExpression($argument);
                    $arr['data']['operants'][] = $argument;
                }
            break;
        }
    }

    function fixSequenceKeys($k)
    {
        $seq = $this->database_definition['sequences'][$k]['name'];
        unset($this->database_definition['sequences'][$k]['name']);
        $this->renameKey($this->database_definition['sequences'], $k, $seq);
    }

    /* blindly copied from original parser. to be implemented yet. */
    function &raiseError($msg = null, $xmlecode = 0, $xp = null, $ecode = MDB2_SCHEMA_ERROR_PARSE)
    {
        if (is_null($this->error)) {
            $error = '';
            if (is_resource($msg)) {
                $error.= 'Parser error: '.xml_error_string(xml_get_error_code($msg));
                $xp = $msg;
            } else {
                $error.= 'Parser error: '.$msg;
                if (!is_resource($xp)) {
                    $xp = $this->parser;
                }
            }
            if ($error_string = xml_error_string($xmlecode)) {
                $error.= ' - '.$error_string;
            }
            if (is_resource($xp)) {
                $byte = @xml_get_current_byte_index($xp);
                $line = @xml_get_current_line_number($xp);
                $column = @xml_get_current_column_number($xp);
                $error.= " - Byte: $byte; Line: $line; Col: $column";
            }
            $error.= "\n";
            $this->error =& MDB2_Schema::raiseError($ecode, null, null, $error);
        }
        return $this->error;
    }
}

?>
