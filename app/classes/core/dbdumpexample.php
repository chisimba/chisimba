<?php
// +----------------------------------------------------------------------+
// | PHP versions 4 and 5                                                 |
// +----------------------------------------------------------------------+
// | Copyright (c) 1998-2004 Manuel Lemos, Tomas V.V.Cox,                 |
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
    'sqlite' => 'SQLite',
);

if (isset($_REQUEST['submit']) && $_REQUEST['file'] != '') {
    require_once 'MDB2/Schema.php';
    $dsn = $_REQUEST['type'].'://'.$_REQUEST['user'].':'.$_REQUEST['pass'].'@'.$_REQUEST['host'].'/'.$_REQUEST['name'];

    $schema =& MDB2_Schema::factory($dsn, array('debug' => true, 'log_line_break' => '<br>'));
    if (PEAR::isError($schema)) {
        $error = $schema->getMessage() . ' ' . $schema->getUserInfo();
    } elseif (array_key_exists('action', $_REQUEST)) {
        set_time_limit(0);
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
            $operation = $schema->dumpDatabase($dump_config, $dump_what);
            call_user_func($var_dump, $operation);
        } elseif ($_REQUEST['action'] == 'create') {
            $disable_query = false;
            if (isset($_REQUEST['dumpsql']) && $_REQUEST['dumpsql']) {
                $debug_tmp = $schema->db->getOption('debug');
                $schema->db->setOption('debug', true);
                $debug_handler_tmp = $schema->db->getOption('debug_handler');
                $schema->db->setOption('debug_handler', 'printQueries');
                $disable_query = true;
            }
            $operation = $schema->updateDatabase($_REQUEST['file']
                , $_REQUEST['file'].'.old', array(), $disable_query
            );
            if (isset($_REQUEST['dumpsql']) && $_REQUEST['dumpsql']) {
                $schema->db->setOption('debug', $debug_tmp);
                $schema->db->setOption('debug_handler', $debug_handler_tmp);
            }
            if (PEAR::isError($operation)) {
                echo $operation->getMessage() . ' ' . $operation->getUserInfo();
                call_user_func($var_dump, $operation);
            } else {
                call_user_func($var_dump, $operation);
            }
        } else {
            $error = 'no action selected';
        }
        $warnings = $schema->getWarnings();
        if (count($warnings) > 0) {
            echo('Warnings<br>');
            call_user_func($var_dump, $operation);
        }
        if ($schema->db->getOption('debug')) {
            echo('Debug messages<br>');
            echo($schema->db->getDebugOutput().'<br>');
        }
        echo('Database structure<br>');
        call_user_func($var_dump, $operation);
        $schema->disconnect();
    }
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
                echo '<option value="' . $key . '"';
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
        <td><input type="text" name="user" id="user" value="<?php echo (isset($_REQUEST['user']) ? $_REQUEST['user'] : '') ?>" /></td>
    </tr>
    <tr>
        <td><label for="pass">Password:</label></td>
        <td><input type="text" name="pass" id="pass" value="<?php echo (isset($_REQUEST['pass']) ? $_REQUEST['pass'] : '') ?>" /></td>
    </tr>
    <tr>
        <td><label for="host">Host:</label></td>
        <td><input type="text" name="host" id="host" value="<?php echo (isset($_REQUEST['host']) ? $_REQUEST['host'] : '') ?>" /></td>
    </tr>
    <tr>
        <td><label for="name">Databasename:</label></td>
        <td><input type="text" name="name" id="name" value="<?php echo (isset($_REQUEST['name']) ? $_REQUEST['name'] : '') ?>" /></td>
    </tr>
    <tr>
        <td><label for="file">Filename:</label></td>
        <td><input type="text" name="file" id="file" value="<?php echo (isset($_REQUEST['file']) ? $_REQUEST['file'] : '') ?>" /></td>
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
        <td><label for="file">Dump SQL:</label></td>
        <td><input type="checkbox" name="dumpsql" id="dumpsql" value="1" /></td>
    </tr>
    </table>
    <p><input type="submit" name="submit" value="ok" /></p>
    </fieldset>
<?php } ?>
</form>
</body>
</html>
