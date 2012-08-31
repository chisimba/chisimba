<?php
/**
 * CLI Script to Check Compatibility of chunk of PHP code
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   PHP
 * @package    PHP_CompatInfo
 * @author     Davey Shafik <davey@php.net>
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/PHP_CompatInfo
 * @since      File available since Release 0.8.0
 */

require_once 'PHP/CompatInfo.php';
require_once 'Console/Getargs.php';
require_once 'Console/Table.php';

/**
 * CLI Script to Check Compatibility of chunk of PHP code
 *
 * <code>
 * <?php
 *     require_once 'PHP/CompatInfo/Cli.php';
 *     $cli = new PHP_CompatInfo_Cli;
 *     $cli->run();
 * ?>
 * </code>
 *
 * @example docs/examples/Cli.php Example of using PHP_CompatInfo_Cli
 *
 * @category   PHP
 * @package    PHP_CompatInfo
 * @author     Davey Shafik <davey@php.net>
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @copyright  Copyright 2003 Davey Shafik and Synaptic Media. All Rights Reserved.
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 1.4.3
 * @link       http://pear.php.net/package/PHP_CompatInfo
 * @since      Class available since Release 0.8.0
 */

class PHP_CompatInfo_Cli extends PHP_CompatInfo
{
    /**
     * @var    array    Current CLI Flags
     * @since  0.8.0
     */
    var $opts = array();

    /**
     * @var    string   error message
     * @since  0.8.0
     */
    var $error;

    /**
     * @var    string   File to be Processed
     * @since  0.8.0
     */
    var $file;

    /**
     * @var    string   Directory to be Processed
     * @since  0.8.0
     */
    var $dir;

    /**
     * @var    int      filename column size (max length)
     * @since  1.3.1
     */
    var $split;

    /**
     * @var    string   string to indicate that filename continue on next line
     * @since  1.3.1
     */
    var $glue;

    /**
     * @var    object   Console_Getargs instance
     * @since  1.4.0
     */
    var $args;

    /**
     * @var    array    Current parser options
     * @since  1.4.0
     */
    var $options = array();

    /**
     * ZE2 Constructor
     * @since  0.8.0
     */
    function __construct($split = null, $glue = null)
    {
        $this->split = (isset($split) && is_int($split)) ? $split : 32;
        $this->glue  = (isset($glue) && is_string($glue)) ? $glue : '(+)';

        $this->opts = array(
            'dir' =>
                array('short' => 'd',
                      'desc'  => 'Parse DIR to get its compatibility info',
                      'default' => '',
                      'min'   => 0 , 'max' => 1),
            'file' =>
                array('short' => 'f',
                      'desc' => 'Parse FILE to get its compatibility info',
                      'default' => '',
                      'min'   => 0 , 'max' => 1),
            'verbose' =>
                array('short'   => 'v',
                      'desc'    => 'Set the verbose level',
                      'default' => 1,
                      'min'     => 0 , 'max' => 1),
            'no-recurse' =>
                array('short' => 'n',
                      'desc'  => 'Do not recursively parse files when using --dir',
                      'max'   => 0),
            'ignore-files' =>
                array('short'   => 'if',
                      'desc'    => 'Data file name which contains a list of '
                                 . 'file to ignore',
                      'default' => 'files.txt',
                      'min'     => 0 , 'max' => 1),
            'ignore-dirs' =>
                array('short'   => 'id',
                      'desc'    => 'Data file name which contains a list of '
                                 . 'directory to ignore',
                      'default' => 'dirs.txt',
                      'min'     => 0 , 'max' => 1),
            'ignore-functions' =>
                array('short'   => 'in',
                      'desc'    => 'Data file name which contains a list of '
                                 . 'php function to ignore',
                      'default' => 'functions.txt',
                      'min'     => 0 , 'max' => 1),
            'ignore-constants' =>
                array('short'   => 'ic',
                      'desc'    => 'Data file name which contains a list of '
                                 . 'php constant to ignore',
                      'default' => 'constants.txt',
                      'min'     => 0 , 'max' => 1),
            'ignore-extensions' =>
                array('short'   => 'ie',
                      'desc'    => 'Data file name which contains a list of '
                                 . 'php extension to ignore',
                      'default' => 'extensions.txt',
                      'min'     => 0 , 'max' => 1),
            'ignore-versions' =>
                array('short'   => 'iv',
                      'desc'    => 'PHP versions - functions to exclude '
                                 . 'when parsing source code',
                      'default' => '5.0.0',
                      'min'     => 0 , 'max' => 2),
            'help' =>
                array('short' => 'h',
                      'desc'  => 'Show this help',
                      'max'   => 0),
        );
        $this->args = & Console_Getargs::factory($this->opts);
        if (PEAR::isError($this->args)) {
            if ($this->args->getCode() === CONSOLE_GETARGS_HELP) {
                $this->error = '';
            } else {
                $this->error = $this->args->getMessage();
            }
            return;
        }

        // debug
        if ($this->args->isDefined('v')) {
            $v = $this->args->getValue('v');
            if ($v > 3) {
                $this->options['debug'] = true;
            }
        }

        // no-recurse
        if ($this->args->isDefined('n')) {
            $this->options['recurse_dir'] = false;
        }

        // dir
        if ($this->args->isDefined('d')) {
            $d = $this->args->getValue('d');
            if (file_exists($d)) {
                if ($d{strlen($d)-1} == '/' || $d{strlen($d)-1} == '\\') {
                    $d = substr($d, 0, -1);
                }
                $this->dir = str_replace('\\', '/', realpath($d));
            } else {
                $this->error = 'Failed opening directory "' . $d
                     . '". Please check your spelling and try again.';
                return;
            }
        }

        // file
        if ($this->args->isDefined('f')) {
            $f = $this->args->getValue('f');
            if (file_exists($f)) {
                $this->file = $f;
            } else {
                $this->error = 'Failed opening file "' . $f
                     . '". Please check your spelling and try again.';
                return;
            }
        }

        // ignore-files
        $if = $this->args->getValue('if');
        if (isset($if)) {
            if (file_exists($if)) {
                $options = file($if);
                $this->options['ignore_files'] = array_map('rtrim', $options);
            } else {
                $this->error = 'Failed opening file "' . $if
                     . '" (ignore-files option). '
                     . 'Please check your spelling and try again.';
                return;
            }
        }

        // ignore-dirs
        $id = $this->args->getValue('id');
        if (isset($id)) {
            if (file_exists($id)) {
                $options = file($id);
                $this->options['ignore_dirs'] = array_map('rtrim', $options);
            } else {
                $this->error = 'Failed opening file "' . $id
                     . '" (ignore-dirs option). '
                     . 'Please check your spelling and try again.';
                return;
            }
        }

        // ignore-functions
        $in = $this->args->getValue('in');
        if (isset($in)) {
            if (file_exists($in)) {
                $options = file($in);
                $this->options['ignore_functions'] = array_map('rtrim', $options);
            } else {
                $this->error = 'Failed opening file "' . $in
                     . '" (ignore-functions option). '
                     . 'Please check your spelling and try again.';
                return;
            }
        }

        // ignore-constants
        $ic = $this->args->getValue('ic');
        if (isset($ic)) {
            if (file_exists($ic)) {
                $options = file($ic);
                $this->options['ignore_constants'] = array_map('rtrim', $options);
            } else {
                $this->error = 'Failed opening file "' . $ic
                     . '" (ignore-constants option). '
                     . 'Please check your spelling and try again.';
                return;
            }
        }

        // ignore-extensions
        $ie = $this->args->getValue('ie');
        if (isset($ie)) {
            if (file_exists($ie)) {
                $options = file($ie);
                $this->options['ignore_extensions'] = array_map('rtrim', $options);
            } else {
                $this->error = 'Failed opening file "' . $ie
                     . '" (ignore-extensions option). '
                     . 'Please check your spelling and try again.';
                return;
            }
        }

        // ignore-versions
        $iv = $this->args->getValue('iv');
        if (isset($iv)) {
            if (!is_array($iv)) {
                $iv = array($iv);
            }
            $this->options['ignore_versions'] = $iv;
        }

        // file or directory options are minimum required to work
        if (!$this->args->isDefined('f') && !$this->args->isDefined('d')) {
            $this->error = 'ERROR: You must supply at least one file or directory to process';
        }
    }

    /**
     * ZE1 PHP4 Compatible Constructor
     * @since  0.8.0
     */
    function PHP_CompatInfo_Cli($split = null, $glue = null)
    {
        $this->__construct($split, $glue);
    }

    /**
     * Run the CLI Script
     *
     * @return void
     * @access public
     * @since  0.8.0
     */
    function run()
    {
        if (isset($this->error)) {
            $this->_printUsage($this->error);
        } else {
            if (isset($this->dir)) {
                $this->_parseDir();
            } elseif (isset($this->file)) {
                $this->_parseFile();
            }
        }
    }

    /**
     * Parse Directory Input
     *
     * @return void
     * @access private
     * @since  0.8.0
     */
    function _parseDir()
    {
        $info = $this->parseDir($this->dir, $this->options);
        if ($info === false) {
            $err = 'No valid files into directory "' . $this->dir
               . '". Please check your spelling and try again.';
            $this->_printUsage($err);
            return;
        }
        $table = new Console_Table();
        $table->setHeaders(array('Path', 'Version', 'Extensions', 'Constants/Tokens'));
        $filter = array(&$this, '_splitFilename');
        $table->addFilter(0, $filter);

        $ext   = implode("\r\n", $info['extensions']);
        $const = implode("\r\n", $info['constants']);

        $dir = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $this->dir);
        $table->addRow(array($dir . DIRECTORY_SEPARATOR . '*', $info['version'], $ext, $const));

        unset($info['max_version']);
        unset($info['version']);
        unset($info['extensions']);
        unset($info['constants']);

        $ignored = $info['ignored_files'];

        unset($info['ignored_files']);

        foreach ($info as $file => $info) {
            if ($info === false) {
                continue;  // skip this (invalid) file
            }
            $ext   = implode("\r\n", $info['extensions']);
            $const = implode("\r\n", $info['constants']);

            $file = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $file);
            $table->addSeparator();
            $table->addRow(array($file, $info['version'], $ext, $const));
        }

        $output = $table->getTable();

        // verbose level
        $v = $this->args->getValue('v');

        // command line resume
        if ($v & 1) {
            $output .= "\nCommand Line resume :\n\n";

            $table = new Console_Table();
            $table->setHeaders(array('Option', 'Value'));

            $opts = $this->args->getValues();
            if (is_array($opts)) {
                foreach($opts as $key => $raw) {
                    if (is_array($raw)) {
                        $raw = implode(', ', $raw);
                    }
                    $contents = array($key, $raw);
                    $table->addRow($contents);
                }
            }

            $output .= $table->getTable();
        }

        // parser options resume
        if ($v & 2) {
            $output .= "\nParser options :\n\n";

            $table = new Console_Table();
            $table->setHeaders(array('Option', 'Value'));

            $opts = $this->options;
            if (is_array($opts)) {
                foreach($opts as $key => $raw) {
                    if ($key == 'debug' || $key == 'recurse_dir') {
                        $raw = ($raw === true) ? 'TRUE' : 'FALSE';
                    }
                    if (is_array($raw)) {
                        $raw = implode("\r\n", $raw);
                    }
                    $contents = array($key, $raw);
                    $table->addRow($contents);
                }
            }

            $output .= $table->getTable();
        }

        echo $output;
    }

    /**
     * Parse File Input
     *
     * @return void
     * @access private
     * @since  0.8.0
     */
    function _parseFile()
    {
        $info = $this->parseFile($this->file, $this->options);
        if ($info === false) {
            $err = 'Failed opening file "' . $this->file
               . '". Please check your spelling and try again.';
            $this->_printUsage($err);
            return;
        }
        $table = new Console_Table();
        $table->setHeaders(array('File', 'Version', 'Extensions', 'Constants/Tokens'));
        $filter = array(&$this, '_splitFilename');
        $table->addFilter(0, $filter);

        $ext   = implode("\r\n", $info['extensions']);
        $const = implode("\r\n", $info['constants']);

        $table->addRow(array($this->file, $info['version'], $ext, $const));

        $output = $table->getTable();

        // verbose level
        $v = $this->args->getValue('v');

        // command line resume
        if ($v & 1) {
            $output .= "\nCommand Line resume :\n\n";

            $table = new Console_Table();
            $table->setHeaders(array('Option', 'Value'));

            $opts = $this->args->getValues();
            if (is_array($opts)) {
                foreach($opts as $key => $raw) {
                    if (is_array($raw)) {
                        $raw = implode(', ', $raw);
                    }
                    $contents = array($key, $raw);
                    $table->addRow($contents);
                }
            }

            $output .= $table->getTable();
        }

        // parser options resume
        if ($v & 2) {
            $output .= "\nParser options :\n\n";

            $table = new Console_Table();
            $table->setHeaders(array('Option', 'Value'));

            $opts = $this->options;
            if (is_array($opts)) {
                foreach($opts as $key => $raw) {
                    if ($key == 'debug' || $key == 'recurse_dir') {
                        $raw = ($raw === true) ? 'TRUE' : 'FALSE';
                    }
                    if (is_array($raw)) {
                        $raw = implode("\r\n", $raw);
                    }
                    $contents = array($key, $raw);
                    $table->addRow($contents);
                }
            }

            $output .= $table->getTable();
        }

        // extra information
        if ($v & 4) {
            $output .= "\nDebug:\n\n";

            $table = new Console_Table();
            $table->setHeaders(array('Version', 'Function', 'Extension'));

            unset($info['max_version']);
            unset($info['version']);
            unset($info['constants']);
            unset($info['extensions']);

            foreach ($info as $version => $functions) {
                foreach ($functions as $func) {
                    $table->addRow(array($version, $func['function'], $func['extension']));
                }
            }

            $output .= $table->getTable();
        }

        echo $output;
    }

    /**
     * The Console_Table filter callback limits output to 80 columns.
     *
     * @param  string $data  content of filename column (0)
     * @return string
     * @access private
     * @since  1.3.0
     */
    function _splitFilename($data)
    {
        $str = '';
        if (strlen($data) > 0) {
            $sep = DIRECTORY_SEPARATOR;
            $base = basename($data);
            $padding = $this->split - strlen($this->glue);

            if (isset($this->dir)) {
                $dir = str_replace(array('\\', '/'), $sep, $this->dir);
            } else {
                $dir = str_replace(array('\\', '/'), $sep, dirname($data));
            }
            $str = str_replace($dir, '[...]', dirname($data)) . $sep;

            if (strlen($str) + strlen($base) > $this->split) {
                 $str = str_pad($str, $padding) . $this->glue . "\r\n";
                 if (strlen($base) > $this->split) {
                     $str .= '[*]' . substr($base, (3 - $this->split)) ;
                 } else {
                     $str .= substr($base, -1 * $padding) ;
                 }
            } else {
                 $str .= $base;
            }
        }
        return $str;
    }

    /**
     * Show full help information
     *
     * @return void
     * @access private
     * @since  0.8.0
     */
    function _printUsage($footer = '')
    {
        $header = 'Usage: ' . basename($_SERVER['SCRIPT_NAME']) . " [options]\n\n";
        echo Console_Getargs::getHelp($this->opts, $header, "\n$footer\n", 78, 2)."\n";
    }
}
?>