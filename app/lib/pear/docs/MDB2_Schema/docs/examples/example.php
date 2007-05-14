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
// | Author: Lukas Smith <smith@pooteeweet.org>                           |
// +----------------------------------------------------------------------+
//
// $Id$
//

/**
 * MDB2 reverse engineering of xml schemas script.
 *
 * This is all rather ugly code, thats probably very much XSS exploitable etc.
 * However the idea was to keep the magic and dependencies low, to just
 * illustrate the MDB2_Schema API a bit.
 *
 * @package MDB2
 * @category Database
 * @author  Lukas Smith <smith@pooteeweet.org>
 */

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
      <html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
      <head><title>MDB2_Schema example</title></head>
<body>
<?php

@include_once 'Var_Dump.php';
if (class_exists('Var_Dump')) {
    $var_dump = array('Var_Dump', 'display');
} else {
    $var_dump = 'var_dump';
}

function printQueries(&$db, $scope, $message)
{
    if ($scope == 'query') {
        echo $message.$db->getOption('log_line_break');
    }
    MDB2_defaultDebugOutput($db, $scope, $message);
}

$databases = array(
    'mysql'  => 'MySQL',
    'mysqli' => 'MySQLi',
    'pgsql'  => 'PostGreSQL',
    'sqlite' => 'SQLite'
);

$options = array(
    'log_line_break' => '<br>',
    'idxname_format' => '%s',
    'debug' => true,
    'quote_identifier' => true,
    'force_defaults' => false,
    'portability' => false
);

if (isset($_REQUEST['submit']) && $_REQUEST['file'] != '') {
    require_once 'MDB2/Schema.php';

    foreach ($options as $k => $v) {
        if ((isset($_REQUEST[$k])) && (!empty($_REQUEST[$k]))) {
            $options[$k] = $_REQUEST[$k];
        } elseif ($v) {
            $options[$k] = false;
        }
    }

    if (isset($_REQUEST['disable_query']) && $_REQUEST['disable_query']) {
        $disable_query = true;
    } else {
        $disable_query = false;
    }
    
    $dsn = $_REQUEST['type'].'://'.$_REQUEST['user'].':'.$_REQUEST['pass'].'@'.$_REQUEST['host'].'/'.$_REQUEST['name'];

    $schema =& MDB2_Schema::factory($dsn, $options);
    if (PEAR::isError($schema)) {
        $error = $schema->getMessage() . ' ' . $schema->getUserInfo();
    } elseif (array_key_exists('action', $_REQUEST)) {
        set_time_limit(0);

        /* DUMP DATABASE */
        if ($_REQUEST['action'] == 'dump' && array_key_exists('dumptype', $_REQUEST)) {
            switch ($_REQUEST['dumptype']) {
            case 'structure':
                $dump_what = MDB2_SCHEMA_DUMP_STRUCTURE;
                break;
            case 'content':
                $dump_what = MDB2_SCHEMA_DUMP_CONTENT;
                break;
            default:
                $dump_what = MDB2_SCHEMA_DUMP_ALL;
                break;
            }
            $dump_config = array(
                'output_mode' => 'file',
                'output' => $_REQUEST['file']
            );

            $definition = $schema->getDefinitionFromDatabase();
            if (PEAR::isError($definition)) {
                $error = $definition->getMessage() . ' ' . $definition->getUserInfo();
            } else {
                $operation = $schema->dumpDatabase($definition, $dump_config, $dump_what);
                if (PEAR::isError($operation)) {
                    $error = $operation->getMessage() . ' ' . $operation->getUserInfo();
                } else {
                    call_user_func($var_dump, $operation);
                }
            }

        /* UPDATE DATABASE */
        } elseif ($_REQUEST['action'] == 'update') {
            if ($disable_query) {
                $debug_tmp = $schema->db->getOption('debug');
                $schema->db->setOption('debug', true);
                $debug_handler_tmp = $schema->db->getOption('debug_handler');
                $schema->db->setOption('debug_handler', 'printQueries');
            }

            $dump_config = array(
                'output_mode' => 'file',
                'output' => $_REQUEST['file'].'.old'
            );
            $definition = $schema->getDefinitionFromDatabase();
            if (PEAR::isError($definition)) {
                $error = $definition->getMessage() . ' ' . $definition->getUserInfo();
            } else {
                $operation = $schema->dumpDatabase($definition, $dump_config, MDB2_SCHEMA_DUMP_ALL);
                if (PEAR::isError($operation)) {
                    $error = $operation->getMessage() . ' ' . $operation->getUserInfo();
                } else {
                    call_user_func($var_dump, $operation);

                    $operation = $schema->updateDatabase($_REQUEST['file']
                        , $_REQUEST['file'].'.old', array(), $disable_query
                    );
                    if (PEAR::isError($operation)) {
                        $error = $operation->getMessage() . ' ' . $operation->getUserInfo();
                    } else {
                        call_user_func($var_dump, $operation);
                    }
                }
            }

            if ($disable_query) {
                $schema->db->setOption('debug', $debug_tmp);
                $schema->db->setOption('debug_handler', $debug_handler_tmp);
            }

        /* CREATE DATABASE */
        } elseif ($_REQUEST['action'] == 'create') {
            if ($disable_query) {
                $debug_tmp = $schema->db->getOption('debug');
                $schema->db->setOption('debug', true);
                $debug_handler_tmp = $schema->db->getOption('debug_handler');
                $schema->db->setOption('debug_handler', 'printQueries');
            }

            $definition = $schema->parseDatabaseDefinition(
                $_REQUEST['file'], false, array(), $schema->options['fail_on_invalid_names']
            );
            if (PEAR::isError($definition)) {
                $error = $definition->getMessage() . ' ' . $definition->getUserInfo();
            } else {
                $schema->db->setOption('disable_query', $disable_query);
                $operation = $schema->createDatabase($definition);
                $schema->db->setOption('disable_query', false);

                if (PEAR::isError($operation)) {
                    $error = $operation->getMessage() . ' ' . $operation->getUserInfo();
                } else {
                    call_user_func($var_dump, $operation);
                }
            }

            if ($disable_query) {
                $schema->db->setOption('debug', $debug_tmp);
                $schema->db->setOption('debug_handler', $debug_handler_tmp);
            }
        }

        /* INITIALIZE DATABASE
         *
         * To be used when we split initialization and
         * definition into two diferent structures
         *
         */
        /*} elseif ($_REQUEST['action'] == 'initialize') {
            if ($disable_query) {
                $debug_tmp = $schema->db->getOption('debug');
                $schema->db->setOption('debug', true);
                $debug_handler_tmp = $schema->db->getOption('debug_handler');
                $schema->db->setOption('debug_handler', 'printQueries');
            }

            $definition = $schema->parseDatabaseDefinition(
                $_REQUEST['file'], false, array(), $schema->options['fail_on_invalid_names']
            );
            
            $schema->db->setOption('disable_query', $disable_query);
            if (isset($definition['tables'])
                && is_array($definition['tables'])
            ) {
                foreach ($definition['tables'] as $table_name => $table) {
                    $operation = $schema->initializeTable($table_name, $table);
                    if (PEAR::isError($operation)) {
                        echo $operation->getMessage() . ' ' . $operation->getUserInfo();
                    } else {
                        call_user_func($var_dump, $operation);
                    }
                }
            }
            $schema->db->setOption('disable_query', false);

            if ($disable_query) {
                $schema->db->setOption('debug', $debug_tmp);
                $schema->db->setOption('debug_handler', $debug_handler_tmp);
            }*/

    /* NO ACTION */
    } else {
        $error = 'Script Error: no action selected';
    }

    $warnings = $schema->getWarnings();
    if (count($warnings) > 0) {
        echo('<h1>Warnings</h1>');
        call_user_func($var_dump, $operation);
    }

    if ($schema->db->getOption('debug')) {
        echo('<h1>Debug messages</h1>');
        echo($schema->db->getDebugOutput().'<br>');
    }

    if (isset($_REQUEST['show_structure'])
        && $_REQUEST['show_structure']
        && isset($definition)
        && is_array($definition)
    ) {
        echo('<h1>Database structure</h1>');
        call_user_func($var_dump, $definition);
    }

    $schema->disconnect();
}

if (!isset($_REQUEST['submit']) || isset($error)) {
    if (isset($error) && $error) {
        echo '<div id="errors"><ul>';
        echo '<li>' . $error . '</li>';
        echo '</ul></div>';
    }
?>
    <form action="<?php echo strip_tags($_SERVER['PHP_SELF']); ?>" method="get">
    <fieldset>
    <legend>Database information</legend>

    <table>
    <tr>
    <td><label for="type">Database Type:</label></td>
        <td>
        <select name="type" id="type">
<?php
    foreach ($databases as $key => $name) {
        echo str_repeat(' ', 8).'<option value="' . $key . '"';
        if (isset($_REQUEST['type']) && $_REQUEST['type'] == $key) {
            echo ' selected="selected"';
        }
        echo '>' . $name . '</option>' . "\n";
    }
    ?>
        </select>
        </td>
    </tr>
    <tr>
        <td><label for="user">Username:</label></td>
        <td><input type="text" name="user" id="user" value="<?php echo (isset($_REQUEST['user']) ? $_REQUEST['user'] : 'root') ?>" /></td>
    </tr>
    <tr>
        <td><label for="pass">Password:</label></td>
        <td><input type="text" name="pass" id="pass" value="<?php echo (isset($_REQUEST['pass']) ? $_REQUEST['pass'] : '') ?>" /></td>
    </tr>
    <tr>
        <td><label for="host">Host:</label></td>
        <td><input type="text" name="host" id="host" value="<?php echo (isset($_REQUEST['host']) ? $_REQUEST['host'] : 'localhost') ?>" /></td>
    </tr>
    <tr>
        <td><label for="name">Databasename:</label></td>
        <td><input type="text" name="name" id="name" value="<?php echo (isset($_REQUEST['name']) ? $_REQUEST['name'] : 'MDB2Example') ?>" /></td>
    </tr>
    <tr>
        <td><label for="file">Filename:</label></td>
        <td><input type="text" name="file" id="file" value="<?php echo (isset($_REQUEST['file']) ? $_REQUEST['file'] : 'schema.xml') ?>" /></td>
    </tr>
    <tr>
        <td><label for="dump">Dump:</label></td>
        <td><input type="radio" name="action" id="dump" value="dump" <?php if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'dump') {echo (' checked="checked"');} ?> />
        <select id="dumptype" name="dumptype">
            <option value="all"<?php if (isset($_REQUEST['dumptype']) && $_REQUEST['dumptype'] == 'all') {echo (' selected="selected"');} ?>>All</option>
            <option value="structure"<?php if (isset($_REQUEST['dumptype']) && $_REQUEST['dumptype'] == 'structure') {echo (' selected="selected"');} ?>>Structure</option>
            <option value="content"<?php if (isset($_REQUEST['dumptype']) && $_REQUEST['dumptype'] == 'content') {echo (' selected="selected"');} ?>>Content</option>
        </select>
        </td>
    </tr>
    <tr>
        <td><label for="create">Create:</label></td>
        <td><input type="radio" name="action" id="create" value="create" <?php if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'create') { echo 'checked="checked"';} ?> /></td>
    </tr>
    <tr>
        <td><label for="update">Update:</label></td>
        <td><input type="radio" name="action" id="update" value="update" <?php if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'update') { echo 'checked="checked"';} ?> /></td>
    </tr>
<!--    <tr>
        <td><label for="update">Initialize:</label></td>
        <td><input type="radio" name="action" id="initialize" value="initialize" <?php if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'initialize') { echo 'checked="checked"';} ?> /></td>
    </tr>-->
    </table>
    </fieldset>

    <fieldset>
    <legend>Options</legend>
    <table>
    <tr>
        <td><label for="log_line_break">Log line break:</label></td>
        <td><input type="text" name="log_line_break" id="log_line_break" value="<br>" /></td>
    </tr>
    <tr>
        <td><label for="idxname_format">Index Name Format:</label></td>
        <td><input type="text" name="idxname_format" id="idxname_format" value="%s" /></td>
    </tr>
    <tr>
        <td><label for="debug">Debug:</label></td>
        <td><input type="checkbox" name="debug" id="debug" value="1" <?php if ((isset($options['debug'])) && ($options['debug'])) {echo (' checked="checked"');} ?> /></td>
    </tr>
    <tr>
        <td><label for="quote_identifier">Quote Identifier:</label></td>
        <td><input type="checkbox" name="quote_identifier" id="quote_identifier" value="1" <?php if ((isset($options['quote_identifier'])) && ($options['quote_identifier'])) {echo (' checked="checked"');} ?> /></td>
    </tr>
    <tr>
        <td><label for="force_defaults">Force Defaults:</label></td>
        <td><input type="checkbox" name="force_defaults" id="force_defaults" value="1" <?php if ((isset($options['force_defaults'])) && ($options['force_defaults'])) {echo (' checked="checked"');} ?> /></td>
    </tr>
    <tr>
        <td><label for="portability">Portability:</label></td>
        <td><input type="checkbox" name="portability" id="portability" value="1" <?php if ((isset($options['portability'])) && ($options['portability'])) {echo (' checked="checked"');} ?> /></td>
    </tr>
    <tr>
        <td><label for="show_structure">Show database structure:</label></td>
        <td><input type="checkbox" name="show_structure" id="show_structure" value="1" <?php if ((isset($show_structure)) && ($show_structure)) {echo (' checked="checked"');} ?> /></td>
    </tr>
    <tr>
        <td><label for="disable_query">Do not modify database:</label></td>
        <td><input type="checkbox" name="disable_query" id="disable_query" value="1" <?php if ((isset($disable_query)) && ($disable_query)) {echo (' checked="checked"');} ?> /></td>
    </tr>
    </table>
    </fieldset>

    <p><input type="submit" name="submit" value="ok" /><input type="button" value="reset" onClick="JavaScript:window.location.href=''" /></p>
<?php } ?>
    </form>
</body>
</html>
