<?php
/**
 * Utility static class
 * @package Error_Raise
 * @version 0.2.2
 * @author Greg Beaver cellog@php.net
 */
/**
 * Error used if a class passed to initialize hasn't been defined yet
 *
 * {@link initialize()} allows
 */
define('ERROR_UTIL_ERROR_CLASS_DOESNT_EXIST', 1);
/**
 * Error used if a method doesn't exist in a callback passed in
 */
define('ERROR_UTIL_ERROR_METHOD_DOESNT_EXIST', 2);
/**
 * Error used if a function doesn't exist in a callback passed in
 */
define('ERROR_UTIL_ERROR_FUNCTION_DOESNT_EXIST', 3);
/**
 * Error used when parameters to functions don't match expected types
 */
define('ERROR_UTIL_ERROR_INVALID_INPUT', 4);
/**
 * Error used when an internal function is passed as a callback - this is never
 * allowed.
 */
define('ERROR_UTIL_ERROR_INTERNAL_FUNCTION', 5);
/**
 * Utility functions for Error display
 *
 * This class is used for advanced retrieval of context information, and for
 * callback validation.  It also has a few miscellaneous functions for processing
 * display of variables.
 * @package Error_Raise
 * @version 0.2.2
 * @author Greg Beaver cellog@php.net
 * @static
 */
class Error_Util {
    /**
     * Extracted from {@link http://mojavelinux.com/forum/viewforum.php?f=4}
     * originally by Dan Allen
     * @param string full path to source file that triggered a PHP error
     * @param string line number of error
     * @static
     */
    function getErrorContext($file, $line, $contextLines = 5)
    {
        if (!file_exists($file) || !is_readable($file)) {
            return array(
                'start'        => 0,
                'end'        => 0,
                'source'    => '',
                'variables'    => array(),
            );
        }

        $sourceLines = file($file);
        $offset = max($line - 1 - $contextLines, 0);
        $numLines = 2 * $contextLines + 1;
        $sourceLines = array_slice($sourceLines, $offset, $numLines);
        $numLines = count($sourceLines);
        // add line numbers
        foreach ($sourceLines as $index => $line) {
            $sourceLines[$index] = ($offset + $index + 1)  . ': ' . $line;
        }
    
        $source = Error_Util::_addPhpTags(join('', $sourceLines));
        preg_match_all(';\$([[:alnum:]]+);', $source, $matches);
        $variables = array_values(array_unique($matches[1]));
        return array(
            'start'        => $offset + 1,
            'end'        => $offset + $numLines,
            'source'    => $source,
            'variables'    => $variables,
        );
    }

    /**
     * Extracted from {@link http://mojavelinux.com/forum/viewforum.php?f=4}
     * originally by Dan Allen
     * @param array list of variables found in code context
     * @param array list of variables from trigger_error context
     * @param boolean if false, then all variables in $variables will be dumped
     * @param array list of classes to exclude from var_export
     * @param string line number of error
     * @return string|false
     * @static
     */
    function exportVariables($variables, $contextVariables, $strict = true,
        $excludeClasses = array())
    {
        $variableString = '';
        foreach ($variables as $name => $contents) {
            // if we are using strict context and this variable is
            // not in the context, skip it
            if ($strict && !in_array($name, $contextVariables)) {
                continue;
            }

            // if this is an object and the class is in the exclude list, skip it
            if (is_object($contents) && in_array(get_class($contents),
                  $excludeClasses)) {
                continue;
            }

            $variableString .= '$' . $name . ' = ' .
                Error_Util::var_export2($contents, true) . ';' . "\n";
        }

        if (empty($variableString)) {
            return false;
        } else {
            return "\n" . $variableString;
        }
    }
    

    /**
     * Extracted from {@link http://mojavelinux.com/forum/viewforum.php?f=4}
     * originally by Dan Allen
     * @param string string that should be escaped for JS
     * @return string
     * @static
     */
    function escapeJavascript($string)
    {
        return strtr($string, array(
            "\t" => '\\t',
            "\n" => '\\n',
            "\r" => '\\r',
            '\\' => '&#092;',
            "'" => '&#39;'));
    }

    /**
     * Copied from {@link http://www.php.net/strrpos}, comment by
     * DONT SPAM vardges at iqnest dot com
     * @param string full string
     * @param string search string
     * @param integer offset from the start of the string to begin searching
     *                from
     * @static
     */
    function strrpos_str ($string, $searchFor, $startFrom = 0)
    {
        $addLen = strlen ($searchFor);
        $endPos = $startFrom - $addLen;

        while (true) {
            if (($newPos = strpos ($string, $searchFor,
                  $endPos + $addLen)) === false) {
                break;
            }
            $endPos = $newPos;
        }

        return ($endPos >= 0) ? $endPos : false;
    }

    /**
     * Add PHP Tags if necessary to variable context PHP for highlighting
     *
     * Extracted from {@link http://mojavelinux.com/forum/viewforum.php?f=4}
     * originally by Dan Allen
     * @param string source code for context around an error
     * @static
     */
    function _addPhpTags($source)
    {
        $startTag  = '<?php';
        $endTag = '?>';

        if (($pos = strpos($source, $startTag)) !== false) {
            $firstStartPos = $pos;
        } else {
            $firstStartPos = -1;
        }
        if (($pos = strpos($source, $endTag)) !== false) {
            $firstEndPos = $pos;
        } else {
            $firstEndPos = -1;
        }

        // no tags found then it must be solid php since
        // html can't throw a php error
        if ($firstStartPos < 0 && $firstEndPos < 0) {
            return $startTag . "\n" . $source . "\n" . $endTag;
        }

        // found an end tag first, so we are missing a start tag
        if ($firstEndPos >= 0 &&
              ($firstStartPos < 0 || $firstStartPos > $firstEndPos)) {
            $source = $startTag . "\n" . $source;
        }

        $sourceLength = strlen($source);
        if (($pos = Error_Util::strrpos_str($source, $startTag)) !== false) {
            $lastStartPos = $pos;
        } else {
            $lastStartPos = $sourceLength + 1;
        }
        if (($pos = Error_Util::strrpos_str($source, $endTag)) !== false) {
            $lastEndPos = $pos;
        } else {
            $lastEndPos = $sourceLength + 1;
        }

        if ($lastEndPos < $lastStartPos || ($lastEndPos > $lastStartPos
              && $lastEndPos > $sourceLength)) {
            $source .= $endTag;
        }

        return $source;
    }

    /**
     * More advanced var_export for HTML/Javascript, private recursive function
     *
     * Extracted from {@link http://mojavelinux.com/forum/viewforum.php?f=4}
     * originally by Dan Allen
     * @param mixed variable to var_export
     * @param string indentation
     * @param boolean is an array portion of the variable
     * @param integer recursion level
     * @access private
     * @static
     */
    function &_var_export2(&$variable, $arrayIndent = '', $inArray = false,
        $level = 0)
    {
        static $maxLevels = 5, $followObjectReferences = false;
        if ($inArray != false) {
            $leadingSpace = '';
            $trailingSpace = ',' . "\n";
        } else {
            $leadingSpace = $arrayIndent;
            $trailingSpace = '';
        }

        $result = '';
        switch (gettype($variable))
        {
            case 'object':
                if ($inArray && !$followObjectReferences)
                {
                    $result = '*' . get_class($variable) . ' REFERENCE*';
                    $trailingSpace = "\n";
                    break;
                }
            case 'array':
                if ($maxLevels && $level >= $maxLevels) {
                    $result = '** truncated, too much recursion **';
                } else {
                    $result = "\n" . $arrayIndent . 'array (' . "\n";
                    foreach ($variable as $key => $value) {
                        $result .= $arrayIndent . '  ' . (is_int($key)
                          ? $key : ('\'' . 
                          str_replace('\'', '\\\'', $key) . '\'')) . ' => ' . 
                          Error_Util::_var_export2($value,
                            $arrayIndent . '  ', true, $level + 1);
                    }

                    $result .= $arrayIndent . ')';
                }
            break;

            case 'string':
                $result = '\'' . str_replace('\'', '\\\'', $variable) . '\'';
            break;

            case 'boolean':
                $result = $variable ? 'true' : 'false';
            break;

            case 'NULL':
                $result = 'NULL';
            break;

            case 'resource':
                $result = get_resource_type($variable);
            break;

            default:
                $result = $variable;
            break;
        }

        return $leadingSpace . $result . $trailingSpace;
    }

    /**
     * More advanced var_export for HTML/Javascript, private recursive function
     *
     * Extracted from {@link http://mojavelinux.com/forum/viewforum.php?f=4}
     * originally by Dan Allen
     * @param mixed variable to var_export
     * @param boolean prints output by default, set to true to return a string
     * @static
     */
    function var_export2(&$variable, $return = false)
    {
        $result =& Error_Util::_var_export2($variable);
        if ($return) {
            return $result;
        } else {
            echo $result;
        }
    }
    
    /**
     * calls {@link file_exists()} for each value in include_path,
     * then calls {@link is_readable()} when it finds the file.
     *
     * This doesn't really belong here, but is useful
     * @param string
     * @return boolean
     */
    function isIncludeable($filename)
    {
        $ip = get_include_path();
        if (substr(PHP_OS, 0, 3) == 'WIN') {
            $ip = explode(';', $ip);
        } else {
            $ip = explode(':', $ip);
        }
        foreach($ip as $path) {
            if ($a = realpath($path . DIRECTORY_SEPARATOR . $filename)) {
                if (is_readable($a)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Default file/line number grabber function
     *
     * This function uses a backtrace generated from {@link debug_backtrace()}
     * and so will not work at all in PHP < 4.3.0.  The frame should
     * reference the frame that contains the source of the error.  See how
     * raise() implements this in the source code for a very specific idea
     * of how to do this in your own code, if you won't be using one of the
     * standard error-throwing methods
     * @return array|false either array('_file' => file, '_line' => line,
     *         '_function' => function name, '_class' => class name) or
     *         if this doesn't work, then false
     * @param array Results of debug_backtrace()
     * @param integer backtrace frame.
     */
    function _getFileLine($backtrace = null, $frame = 0, $functionframe = 1)
    {
        if (isset($backtrace) && is_array($backtrace) &&
              isset($backtrace[$frame])) {
            if (!isset($backtrace[$frame]['file'])) {
                $frame++;
            }
            $funcbacktrace = $backtrace[$functionframe];
            $filebacktrace = $backtrace[$frame];
            $ret = array('_file' => $filebacktrace['file'],
                         '_line' => $filebacktrace['line']);
            // rearrange for eval'd code or create function errors
            if (preg_match(';^(.*?)\((\d+)\) : (.*?)$;', $filebacktrace['file'],
                  $matches)) {
                $ret['_file'] = $matches[1];
                $ret['_line'] = $matches[2] + 0;
            }
            if (isset($funcbacktrace['function'])) {
                $ret['_function'] = $funcbacktrace['function'];
            }
            if (isset($funcbacktrace['class'])) {
                $ret['_class'] = $funcbacktrace['class'];
            }
            return $ret;
        }
        return false;
    }
    
    function formatStackTrace($trace)
    {
        
    }
    
    /**
     * Parse a backtrace to retrieve the calling frame
     *
     * WARNING: do not attempt to use this in any code outside of
     * Error_Raise::raise(), it just won't work at all
     * @access private
     * @param array debug_backtrace() output from {@link Error_Raise::raise()}
     * @param string warning/error/notice/exception
     * @param Error_Raise_Error error object
     */
    function _parseBacktrace($trace, $errorType, &$error)
    {
        if (!isset($trace[1])) {
            return array(0, 0);
        }
        $functionframe = $frame = 1; // get calling function backtrace
        if (isset($trace[1]['class'])) {
            if (isset($trace[1]['function']) &&
                  $trace[1]['function'] != $errorType) {
                // raise was called directly
                $frame = 0;
            }
            $testclass = $trace[$functionframe]['class'];
            if (str_replace($error->getPackage(), '', $testclass) ==
                '_raise' && isset($trace[$functionframe])) {
                $functionframe++;
            }
        }
        while (isset($trace[$functionframe]['function']) &&
              in_array($trace[$functionframe]['function'],
              array('eval', '__lambda_func')) &&
              isset($trace[$functionframe + 1])) {
            $functionframe++;
        }
        return array($frame, $functionframe);
    }

    /**
     * Verify that $callback is a valid function callback
     *
     * This is used to be absolutely sure a callback is valid before registering
     * it, to avoid later errors on the throwing of an error
     * @param string|array
     * @return true|Error_Raise_Error
     * @throws ERROR_UTIL_ERROR_FUNCTION_DOESNT_EXIST If the callback is a
     *         string and isn't the name of any function
     * @throws ERROR_UTIL_ERROR_INTERNAL_FUNCTION If the callback is the name
     *         of an internal, pre-defined function like "function_exists"
     * @throws ERROR_UTIL_ERROR_INVALID_INPUT If the callback is neither
     *         a string, an array(classname, method), or an array(object, method)
     * @throws ERROR_UTIL_ERROR_METHOD_DOESNT_EXIST if the callback is an
     *         array, and the method is not a method of the class
     * @access private
     */
    function _validCallback($callback)
    {
        static $init = false;
        if (!$init) {
            $init = true;
            Error_Raise::setErrorMsgGenerator('Error_Util',
                array('Error_Util', 'genErrorMessage'));
        }
        if (is_string($callback)) {
            if (!function_exists($callback)) {
                return Error_Raise::exception('Error_Util',
                    ERROR_UTIL_ERROR_FUNCTION_DOESNT_EXIST,
                    array('function' => $callback));
            }
            $a = get_defined_functions();
            if (in_array($callback, $a['internal'])) {
                return Error_Raise::exception('Error_Util',
                    ERROR_UTIL_ERROR_INTERNAL_FUNCTION,
                    array('function' => $callback));
            }
            return true;
        }
        if (is_array($callback)) {
            if (!isset($callback[0]) || !isset($callback[1])) {
                return Error_Raise::exception('Error_Util',
                    ERROR_UTIL_ERROR_INVALID_INPUT,
                    array('expected' => array(0, 1),
                          'was' => array_keys($callback),
                          'var' => 'array_keys($callback)',
                          'paramnum' => 1));
            }
            if (is_string($callback[0])) {
                if (!is_string($callback[1])) {
                    return Error_Raise::exception('Error_Util',
                        ERROR_UTIL_ERROR_INVALID_INPUT,
                        array('expected' => 'string',
                              'was' => gettype($callback[1]),
                              'var' => '$callback[1]',
                              'paramnum' => 1));
                }
                if (!class_exists($callback[0])) {
                    return Error_Raise::exception('Error_Util',
                        ERROR_UTIL_ERROR_CLASS_DOESNT_EXIST,
                        array('class' => $callback[0]));
                }
                if (!in_array(strtolower($callback[1]),
                      get_class_methods($callback[0]))) {
                    return Error_Raise::exception('Error_Util',
                        ERROR_UTIL_ERROR_METHOD_DOESNT_EXIST,
                        array('method' => $callback[1],
                              'class' => $callback[0]));
                }
                return true;
            } elseif (is_object($callback[0])) {
                if (!method_exists($callback[0], $callback[1])) {
                    return Error_Raise::exception('Error_Util',
                        ERROR_UTIL_ERROR_METHOD_DOESNT_EXIST,
                        array('method' => $callback[1],
                              'class' => get_class($callback[0])));
                }
                return true;
            } else {
                return Error_Raise::exception('Error_Util',
                    ERROR_UTIL_ERROR_INVALID_INPUT,
                    array('expected' => array('array', 'string'),
                          'was' => gettype($callback[0]),
                          'var' => '$callback[0]',
                          'paramnum' => 1));
            }
            // is a callback method
            return true;
        }
        return Error_Raise::exception('error_util',
                ERROR_UTIL_ERROR_INVALID_INPUT,
                array('expected' => array('array', 'string'),
                      'was' => gettype($callback),
                      'var' => '$callback',
                      'paramnum' => 1));;
    }
    
    /**
     * Get an error message for Error_Util errors
     * @return string error message from error code
     * @param integer
     * @param array
     * @static
     */
    function genErrorMessage($code, $args = array(), $state = ERROR_RAISE_TEXT)
    {
        if (!is_array($args)) {
            return 'Error: $args passed to Error_Util::genErrorMessage is '.
                'not an array but a '.gettype($args);
        }
        $messages =
        array(
            ERROR_UTIL_ERROR_CLASS_DOESNT_EXIST =>
                'class "%cl%" does not exist',
            ERROR_UTIL_ERROR_INVALID_INPUT =>
                'invalid input, parameter #%paramnum% '
                    . '"%var%" was expecting '
                    . '"%expected%", instead got "%was%"',
            ERROR_UTIL_ERROR_METHOD_DOESNT_EXIST =>
                'method "%method%" doesn\'t exist in class "%class%"',
            ERROR_UTIL_ERROR_FUNCTION_DOESNT_EXIST =>
                'function "%function%" doesn\'t exist',
            ERROR_UTIL_ERROR_INTERNAL_FUNCTION =>
                'function "%function%" is an internal function, and '
                    . 'cannot be used as a callback',
             );
        if (is_int($code) && isset($messages[$code])) {
            $msg = $messages[$code];
            return Error_Raise::sprintfErrorMessageWithState($msg,
                $args, $state);
        } else {
            return 'Error: code ' . $code . ' not found';
        }
    }
}
?>