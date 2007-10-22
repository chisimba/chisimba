<?php
/**
 * Check Compatibility of chunk of PHP code
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
 * @since      File available since Release 0.7.0
 */

/**
 * An array of function init versions and extension
 */
require_once 'PHP/CompatInfo/func_array.php';

/**
 * An array of constants and their init versions
 */
require_once 'PHP/CompatInfo/const_array.php';

/**
 * Check Compatibility of chunk of PHP code
 *
 * @example docs/examples/checkConstants.php Example that shows minimum version with Constants
 * @example docs/examples/parseFile.php Example on how to parse a file
 * @example docs/examples/parseDir.php Example on how to parse a directory
 * @example docs/examples/parseArray.php Example on using using parseArray() to parse a script
 * @example docs/examples/parseString.php Example on how to parse a string
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
 * @since      Class available since Release 0.7.0
 */

class PHP_CompatInfo
{
    /**
     * @var string Earliest version of PHP to use
     * @since  0.7.0
     */
    var $latest_version = '3.0.0';

    /**
     * @var string Last version of PHP to use
     */
    var $earliest_version = '';

    /**
     * @var boolean Toggle parseDir recursion
     * @since  0.7.0
     */
    var $recurse_dir = true;

    /**
     * Parse a file for its Compatibility info
     *
     * @param  string $file    Path of File to parse
     * @param  array  $options An array of options where:
     *  - 'debug'            Contains a boolean to control whether
     *                       extra ouput is shown.
     *  - 'ignore_functions' Contains an array of functions to ignore
     *                       when calculating the version needed.
     *  - 'ignore_constants' Contains an array of constants to ignore
     *                       when calculating the version needed.
     *  - 'ignore_extensions' Contains an array of php extensions to ignore
     *                       when calculating the version needed.
     *  - 'ignore_versions'  Contains an array of php versions to ignore
     *                       when calculating the version needed.
     * @access public
     * @return Array
     * @since  0.7.0
     */
    function parseFile($file, $options = array())
    {
        $options = array_merge(array('debug' => false), $options);
        $tokens = $this->_tokenize($file);
        if (is_array($tokens) && count($tokens) > 0) {
            return $this->_parseTokens($tokens, $options);
        }
        return false;
    }

    /**
     * Parse a string for its Compatibility info
     *
     * @param  string $string  PHP Code to parses
     * @param  array  $options An array of options where:
     *  - 'debug'            Contains a boolean to control whether
     *                       extra ouput is shown.
     *  - 'ignore_functions' Contains an array of functions to ignore
     *                       when calculating the version needed.
     *  - 'ignore_constants' Contains an array of constants to ignore
     *                       when calculating the version needed.
     *  - 'ignore_extensions' Contains an array of php extensions to ignore
     *                       when calculating the version needed.
     *  - 'ignore_versions'  Contains an array of php versions to ignore
     *                       when calculating the version needed.
     * @access public
     * @return Array
     * @since  0.7.0
     */
    function parseString($string, $options = array())
    {
        $options = array_merge(array('debug' => false), $options);
        $tokens = $this->_tokenize($string, true);
        if (is_array($tokens) && count($tokens) > 0) {
            return $this->_parseTokens($tokens, $options);
        }
        return false;
    }

    /**
     * Parse a directory recursively for its Compatibility info
     *
     * @see PHP_CompatInfo::_fileList()
     * @param  string $dir     Path of folder to parse
     * @param  array  $options An array of options where:
     *  - 'file_ext'         Contains an array of file extensions to parse
     *                       for PHP code. Default: php, php4, inc, phtml
     *  - 'recurse_dir'      Boolean on whether to recursively find files
     *  - 'debug'            Contains a boolean to control whether
     *                       extra ouput is shown.
     *  - 'ignore_functions' Contains an array of functions to ignore
     *                       when calculating the version needed.
     *  - 'ignore_constants' Contains an array of constants to ignore
     *                       when calculating the version needed.
     *  - 'ignore_files'     Contains an array of files to ignore.
     *                       File names are case insensitive.
     *  - 'ignore_dirs'      Contains an array of directories to ignore.
     *                       Directory names are case insensitive.
     *  - 'ignore_extensions' Contains an array of php extensions to ignore
     *                       when calculating the version needed.
     *  - 'ignore_versions'  Contains an array of php versions to ignore
     *                       when calculating the version needed.
     * @access public
     * @return array
     * @since  0.8.0
     */
    function parseDir($dir, $options = array())
    {
        $files_valid = 0;
        $files_parsed = array();
        $latest_version = $this->latest_version;
        $earliest_version = $this->earliest_version;
        $extensions = array();
        $constants = array();
        $ignored = array();
        $default_options = array(
            'file_ext' => array('php', 'php4', 'inc', 'phtml'),
            'recurse_dir' => true,
            'debug' => false,
            'ignore_files' => array(),
            'ignore_dirs' => array()
            );
        $options = array_merge($default_options, $options);

        if (is_dir($dir) && is_readable($dir)) {
            if ($dir{strlen($dir)-1} == '/' || $dir{strlen($dir)-1} == '\\') {
                $dir = substr($dir, 0, -1);
            }
            $options['file_ext'] = array_map('strtolower', $options['file_ext']);
            $options['ignore_files'] = array_map('strtolower', $options['ignore_files']);
            $options['ignore_dirs'] = array_map('strtolower', $options['ignore_dirs']);
            $files_raw = $this->_fileList($dir, $options);
            foreach ($files_raw as $file) {
                if (in_array(strtolower($file), $options['ignore_files'])) {
                    $ignored[] = $file;
                    continue;
                }
                $file_info = pathinfo($file);
                if (isset($file_info['extension']) &&
                    in_array(strtolower($file_info['extension']), $options['file_ext'])) {
                    $tokens = $this->_tokenize($file);
                    if (is_array($tokens) && count($tokens) > 0) {
                        $files_parsed[$file] = $this->_parseTokens($tokens, $options);
                        $files_valid++;
                    } else {
                        $files_parsed[$file] = false;
                    }
                }
            }
            foreach ($files_parsed as $file) {
                if ($file === false) {
                    continue;  // skip this file
                }
                $cmp = version_compare($latest_version, $file['version']);
                if ($cmp === -1) {
                    $latest_version = $file['version'];
                }
                if ($file['max_version'] != '') {
                    $cmp = version_compare($earliest_version, $file['max_version']);
                    if ($earliest_version == '' || $cmp === 1) {
                        $earliest_version = $file['max_version'];
                    }
                }
                foreach ($file['extensions'] as $ext) {
                    if (!in_array($ext, $extensions)) {
                        $extensions[] = $ext;
                    }
                }
                foreach ($file['constants'] as $const) {
                    if (!in_array($const, $constants)) {
                        $constants[] = $const;
                    }
                }
            }

            if (count($files_parsed) == 0 || $files_valid == 0) {
                return false;
            }

            $files_parsed['constants'] = $constants;
            $files_parsed['extensions'] = $extensions;
            $files_parsed['version'] = $latest_version;
            $files_parsed['max_version'] = $earliest_version;
            $files_parsed['ignored_files'] = $ignored;
            $files_parsed = array_reverse($files_parsed);
            return $files_parsed;
        } else {
            return false;
        }
    }

    /**
     * Alias of parseDir
     *
     * @param  string $folder  Path of folder to parse
     * @param  array  $options An array of options
     * @uses   PHP_CompatInfo::parseDir()
     * @access public
     * @since  0.7.0
     */
    function parseFolder($folder, $options = array())
    {
        return $this->parseDir($folder, $options);
    }

    /**
     * Parse an Array of Files
     *
     * You can parse an array of Files or Strings, to parse
     * strings, $options['is_string'] must be set to true
     *
     * @param  array $files   Array of file names or code strings
     * @param  array $options An array of options where:
     *  - 'file_ext'         Contains an array of file extensions to parse
     *                       for PHP code. Default: php, php4, inc, phtml
     *  - 'debug'            Contains a boolean to control whether
     *                       extra ouput is shown.
     *  - 'ignore_functions' Contains an array of functions to ignore
     *                       when calculating the version needed.
     *  - 'ignore_constants' Contains an array of constants to ignore
     *                       when calculating the version needed.
     *  - 'ignore_files'     Contains an array of files to ignore.
     *                       File names are case insensitive.
     *  - 'is_string'        Contains a boolean which says if the array values
     *                       are strings or file names.
     *  - 'ignore_extensions' Contains an array of php extensions to ignore
     *                       when calculating the version needed.
     *  - 'ignore_versions'  Contains an array of php versions to ignore
     *                       when calculating the version needed.
     * @access public
     * @return array|false
     * @since  0.7.0
     */
    function parseArray($files, $options = array())
    {
        $files_parsed = array();
        $latest_version = $this->latest_version;
        $earliest_version = $this->earliest_version;
        $extensions = array();
        $constants = array();
        $options = array_merge(array(
            'file_ext' => array('php', 'php4', 'inc', 'phtml'),
            'is_string' => false,
            'debug' => false,
            'ignore_files' => array()
            ), $options);
        $options['ignore_files'] = array_map('strtolower', $options['ignore_files']);
        foreach ($files as $file) {
            if ($options['is_string'] === false) {
                $pathinfo = pathinfo($file);
                if (!in_array(strtolower($file), $options['ignore_files']) &&
                     in_array($pathinfo['extension'], $options['file_ext'])) {
                    $tokens = $this->_tokenize($file, $options['is_string']);
                    if (is_array($tokens) && count($tokens) > 0) {
                        $files_parsed[$file] = $this->_parseTokens($tokens, $options);
                    } else {
                        $files_parsed[$file] = false;
                    }
                } else {
                    $ignored[] = $file;
                }
            } else {
                $tokens = $this->_tokenize($file, $options['is_string']);
                if (is_array($tokens) && count($tokens) > 0) {
                    $files_parsed[] = $this->_parseTokens($tokens, $options);
                } else {
                    $files_parsed[] = false;
                }
            }
        }

        foreach ($files_parsed as $file) {
            if ($file === false) {
                continue;  // skip this file
            }
            $cmp = version_compare($latest_version, $file['version']);
            if ($cmp === -1) {
                $latest_version = $file['version'];
            }
            if ($file['max_version'] != '') {
                $cmp = version_compare($earliest_version, $file['max_version']);
                if ($earliest_version == '' || $cmp === 1) {
                    $earliest_version = $file['max_version'];
                }
            }
            foreach ($file['extensions'] as $ext) {
                if (!in_array($ext, $extensions)) {
                    $extensions[] = $ext;
                }
            }
            foreach ($file['constants'] as $const) {
                if (!in_array($const, $constants)) {
                    $constants[] = $const;
                }
            }
        }

        $files_parsed['constants'] = $constants;
        $files_parsed['extensions'] = $extensions;
        $files_parsed['version'] = $latest_version;
        $files_parsed['max_version'] = $earliest_version;
        $files_parsed['ignored_files'] =  isset($ignored) ? $ignored : array();
        $files_parsed = array_reverse($files_parsed);
        return $files_parsed;
    }

    /**
     * Load components list for a PHP version or subset
     *
     * @param  string           $min     PHP minimal version
     * @param  string|boolean   $max     (optional) PHP maximal version
     * @return array            An array of php function names to ignore
     * @access public
     * @static
     * @since  1.2.0
     */
    function loadVersion($min, $max = false)
    {
        $keys = array();
        foreach ($GLOBALS['_PHP_COMPATINFO_FUNCS'] as $func => $arr) {
            $keep = false;
            if (version_compare($arr['init'], $min) < 0) {
                continue;
            }
            if ($max) {
                $end = (isset($arr['end'])) ? $arr['end'] : $arr['init'];

                if (version_compare($end, $max) < 1) {
                    $keys[] = $func;
                }
            } else {
                $keys[] = $func;
            }
        }
        return $keys;
    }

    /**
     * Parse the given Tokens
     *
     * The tokens are those returned by
     * token_get_all() which is nicely
     * wrapped in PHP_CompatInfo::_tokenize
     *
     * @param  array   $tokens Array of PHP Tokens
     * @param  boolean $debug  Show Extra Output
     * @access private
     * @return array
     * @since  0.7.0
     */
    function _parseTokens($tokens, $options)
    {
        static $akeys;

        $functions = array();
        $functions_version = array();
        $latest_version = $this->latest_version;
        $earliest_version = $this->earliest_version;
        $extensions = array();
        $constants = array();
        $constant_names = array();
        $udf = array();

        if (isset($options['ignore_constants'])) {
            $options['ignore_constants'] = array_map('strtoupper', $options['ignore_constants']);
        } else {
            $options['ignore_constants'] = array();
        }
        if (isset($options['ignore_extensions'])) {
            $options['ignore_extensions'] = array_map('strtolower', $options['ignore_extensions']);
        } else {
            $options['ignore_extensions'] = array();
        }
        if (isset($options['ignore_versions'][0])) {
            $min_ver = $options['ignore_versions'][0];
        } else {
            $min_ver = false;
        }
        if (isset($options['ignore_versions'][1])) {
            $max_ver = $options['ignore_versions'][1];
        } else {
            $max_ver = false;
        }

        $token_count = sizeof($tokens);
        $i = 0;
        while ($i < $token_count) {
            $found_func = true;
            if (is_array($tokens[$i]) && (token_name($tokens[$i][0]) == 'T_FUNCTION')) {
                $found_func = false;
            }
            while ($found_func == false) {
                $i += 1;
                if (is_array($tokens[$i]) && (token_name($tokens[$i][0]) == 'T_STRING')) {
                    $found_func = true;
                    $udf[] = $tokens[$i][1];
                }
            }
            if (is_array($tokens[$i]) && (token_name($tokens[$i][0]) == 'T_STRING')) {
                if (isset($tokens[$i + 1]) && ($tokens[$i + 1][0] == '(')) {
                    if ((is_array($tokens[$i - 1])) &&
                       (token_name($tokens[$i - 1][0]) != 'T_DOUBLE_COLON') &&
                       (token_name($tokens[$i - 1][0]) != 'T_OBJECT_OPERATOR')) {
                        $functions[] = strtolower($tokens[$i][1]);
                    } elseif (!is_array($tokens[$i - 1])) {
                        $functions[] = strtolower($tokens[$i][1]);
                    }
                }
            }
            if (is_array($tokens[$i])) {
                if (!isset($akeys)) {
                    // build contents one time only (static variable)
                    $akeys = array_keys($GLOBALS['_PHP_COMPATINFO_CONST']);
                }
                $const = strtoupper($tokens[$i][1]);
                $found = array_search($const, $akeys);
                if ($found !== false) {
                    if (is_string($tokens[$i-1]) || (token_name($tokens[$i-1][0]) == 'T_ENCAPSED_AND_WHITESPACE')) {
                        // PHP 5 constant tokens found into a string
                    } else {
                        if (!in_array($const, $options['ignore_constants'])) {
                            if (!PHP_CompatInfo::_ignore($GLOBALS['_PHP_COMPATINFO_CONST'][$const]['init'],
                                $min_ver, $max_ver)) {
                                $constants[] = $const;
                                $latest_version = $GLOBALS['_PHP_COMPATINFO_CONST'][$const]['init'];
                            }
                        }
                    }
                }
            }
            $i += 1;
        }

        $functions = array_unique($functions);
        if (isset($options['ignore_functions'])) {
            $options['ignore_functions'] = array_map('strtolower', $options['ignore_functions']);
        } else {
            $options['ignore_functions'] = array();
        }
        foreach ($functions as $name) {
            if (!isset($GLOBALS['_PHP_COMPATINFO_FUNCS'][$name])) {
                continue;  // skip this unknown function
            }

            // retrieve if available the extension name
            if ((isset($GLOBALS['_PHP_COMPATINFO_FUNCS'][$name]['ext'])) &&
                ($GLOBALS['_PHP_COMPATINFO_FUNCS'][$name]['ext'] != 'ext_standard') &&
                ($GLOBALS['_PHP_COMPATINFO_FUNCS'][$name]['ext'] != 'zend')) {
                $extension = substr($GLOBALS['_PHP_COMPATINFO_FUNCS'][$name]['ext'], 4);
                if ($extension{0} == '_') {
                    $extension = substr($extension, 1);
                }
            } else {
                $extension = false;
            }

            if ((!in_array($name, $udf))
                && (!in_array($name, $options['ignore_functions']))) {

                if ($extension && in_array($extension, $options['ignore_extensions'])) {
                    continue;  // skip this extension function
                }

                if (PHP_CompatInfo::_ignore($GLOBALS['_PHP_COMPATINFO_FUNCS'][$name]['init'],
                    $min_ver, $max_ver)) {
                    continue;  // skip this function version
                }

                if ($options['debug'] == true) {
                    $functions_version[$GLOBALS['_PHP_COMPATINFO_FUNCS'][$name]['init']][] = array(
                        'function' => $name,
                        'extension' => $GLOBALS['_PHP_COMPATINFO_FUNCS'][$name]['ext']
                        );
                }
                $cmp = version_compare($latest_version, $GLOBALS['_PHP_COMPATINFO_FUNCS'][$name]['init']);
                if ($cmp === -1) {
                    $latest_version = $GLOBALS['_PHP_COMPATINFO_FUNCS'][$name]['init'];
                }
                if (array_key_exists('end', $GLOBALS['_PHP_COMPATINFO_FUNCS'][$name])) {
                    $cmp = version_compare($earliest_version, $GLOBALS['_PHP_COMPATINFO_FUNCS'][$name]['end']);
                    if ($earliest_version == '' || $cmp === 1) {
                        $earliest_version = $GLOBALS['_PHP_COMPATINFO_FUNCS'][$name]['end'];
                    }
                }

                if ($extension && !in_array($extension, $extensions)) {
                    $extensions[] = $extension;
                }
            }
        }

        $constants = array_unique($constants);
        foreach ($constants as $constant) {
            if (PHP_CompatInfo::_ignore($GLOBALS['_PHP_COMPATINFO_CONST'][$constant]['init'],
                $min_ver, $max_ver)) {
                continue;  // skip this constant version
            }

            $cmp = version_compare($latest_version, $GLOBALS['_PHP_COMPATINFO_CONST'][$constant]['init']);
            if ($cmp === -1) {
                $latest_version = $GLOBALS['_PHP_COMPATINFO_CONST'][$constant]['init'];
            }
            if (array_key_exists('end', $GLOBALS['_PHP_COMPATINFO_CONST'][$constant])) {
                $cmp = version_compare($earliest_version, $GLOBALS['_PHP_COMPATINFO_CONST'][$constant]['end']);
                if ($earliest_version == '' || $cmp === 1) {
                    $earliest_version = $GLOBALS['_PHP_COMPATINFO_CONST'][$name]['end'];
                }
            }
            if (!in_array($GLOBALS['_PHP_COMPATINFO_CONST'][$constant]['name'], $constant_names)) {
                $constant_names[] = $GLOBALS['_PHP_COMPATINFO_CONST'][$constant]['name'];
            }
        }

        ksort($functions_version);

        $functions_version['constants'] = $constant_names;
        $functions_version['extensions'] = $extensions;
        $functions_version['version'] = $latest_version;
        $functions_version['max_version'] = $earliest_version;
        $functions_version = array_reverse($functions_version);
        return $functions_version;
    }

    /**
     * Checks if function which has $init version should be keep
     * or ignore (version is between $min_ver and $max_ver).
     *
     * @param  string $init     version of current function
     * @param  string $min_ver  minimum version of function to ignore
     * @param  string $max_ver  maximum version of function to ignore
     * @access private
     * @return boolean True to ignore function/constant, false otherwise
     * @since  1.4.0
     * @static
     */
    function _ignore($init, $min_ver, $max_ver)
    {
        if ($min_ver) {
            $cmp = version_compare($init, $min_ver);
            if ($max_ver && $cmp >= 0) {
                $cmp = version_compare($init, $max_ver);
                if ($cmp < 1) {
                    return true;
                }
            } elseif ($cmp === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Token a file or string
     *
     * @param  string  $input     Filename or PHP code
     * @param  boolean $is_string Whether or note the input is a string
     * @access private
     * @return array|false
     * @since  0.7.0
     */
    function _tokenize($input, $is_string = false)
    {
        if ($is_string === false) {
            $input = @file_get_contents($input, true);
            if (is_string($input)) {
                return token_get_all($input);
            }
            return false;
        } else {
            return token_get_all($input);
        }
    }

    /**
     * Retrieve a listing of every file in $directory and
     * all subdirectories. Taken from PEAR_PackageFileManager_File
     *
     * @param  string $directory full path to the directory you want the list of
     * @access private
     * @return array list of files in a directory
     * @since  0.7.0
     */
    function _fileList($directory,$options)
    {
        $ret = false;
        if (@is_dir($directory) &&
            (!in_array(strtolower($directory), $options['ignore_dirs']))) {
            $ret = array();
            $d = @dir($directory);
            while ($d && $entry = $d->read()) {
                if ($entry{0} != '.') {
                    if (is_file($directory . DIRECTORY_SEPARATOR . $entry)) {
                        $ret[] = $directory . DIRECTORY_SEPARATOR . $entry;
                    }
                    if (is_dir($directory . DIRECTORY_SEPARATOR . $entry) &&
                        ($options['recurse_dir'] != false)) {
                        $tmp = $this->_fileList($directory . DIRECTORY_SEPARATOR . $entry, $options);
                        if (is_array($tmp)) {
                            foreach ($tmp as $ent) {
                                $ret[] = $ent;
                            }
                        }
                    }
                }
            }
            if ($d) {
                $d->close();
            }
        } else {
            return false;
        }

        return $ret;
    }
}
?>