<?php
/**
 * Error Management for PEAR
 *
 * The Error_Raise static class contains tremendously simple and powerful
 * error management based on error codes.
 * @package Error_Raise
 * @author Greg Beaver <cellog@php.net>
 * @version 0.2.2
 */
/**
 * for PEAR_Error class
 */
require_once 'PEAR.php';
/**
 * Pre-defined Error classes for Error_Raise
 */
require_once 'HTML/Progress/Error/Raise/Error.php';
/**
 * Utility static class
 */
require_once 'HTML/Progress/Error/Util.php';

/**
 * Repository for error message generations
 * @see Error_Raise::setErrorMsgGenerator()
 * @global array $GLOBALS['_ERROR_RAISE_MSGS']
 * @name $_ERROR_RAISE_MSGS
 * @access private
 */
$GLOBALS['_ERROR_RAISE_MSGS'] =
    array(
        'error_raise' => array('Error_Raise', '_getErrorMessage'),
    );

/**
 * Repository for context file/line number generators
 *
 * @see Error_Raise::setContextGrabber()
 * @global array $GLOBALS['_ERROR_RAISE_CONTEXTGRABBER']
 * @name $_ERROR_RAISE_CONTEXTGRABBER
 * @access private
 */
$GLOBALS['_ERROR_RAISE_CONTEXTGRABBER'] =
    array(
        'error_raise' => array('Error_Util', '_getFileLine'),
    );

/**
 * Stack used to control error raising
 *
 * This stack contains disabled callbacks.  It is an array of either '*' for
 * all callbacks, or an array of package names
 * @see Error_Raise::disableErrorCallbacks()
 * @access private
 * @global array $GLOBALS['_ERROR_RAISE_DISABLED_CALLBACKS']
 * @name $_ERROR_RAISE_DISABLED_CALLBACKS
 */
$GLOBALS['_ERROR_RAISE_DISABLED_CALLBACKS'] = array();
/**#@+
 * Error message display constants
 */
/**
 * Plain text error messages
 */
define('ERROR_RAISE_TEXT', 1);
/**
 * html error messages
 */
define('ERROR_RAISE_HTML', 2);
/**
 * ansi (console) error messages
 */
define('ERROR_RAISE_ANSI', 3);
/**#@-*/

/**
 * Error Raise - central coordination for generation of error messages
 *
 * To use, simply call Error_Raise::initialize() with the package name
 * and a function name for generation of error messages.  There is no need
 * to instantiate the Error_Raise or define any error classes, unless you
 * need extra functionality in the error message context information (see
 * {@link setContextGrabber()} for details).
 *
 * To create an error, you must:
 * - define an error code
 * - create a function that maps this error code to a message
 * - call Error_Raise::initialize
 * - call one of the error creators with the package name and any additional
 *   parameters unique to this error instance
 *
 * If your package is named "mypackage", then create errors, warnings, notices,
 * or exceptions by error code using Error_Raise::error('MyPackage', CODE),
 * Error_Raise::warning('MyPackage', CODE), etc.  If you wish to use non-standard
 * error types for your own debugging purposes like "debug" or "information",
 * simply use {@link raise()} like Error_Raise::raise('MyPackage', CODE, 'debug');
 *
 * You can pass additional information in an associative array.
 *
 * <code>
 * Error_Raise::notice('MyPackage', CODE,
 *     array('name' => $aditional, 'otherstuff' => $another));
 * </code>
 *
 * With this system, you can use {@link Error_Raise::sprintfErrorMessage()} in
 * your error message generation function.
 *
 * The strength of this system is easy internationalization and other benefits.
 * Different functions can be defined to return error messages, and even an
 * end-user can define a different error-message generation function for their
 * own purposes, without modification to the original source.
 *
 * In addition, unique error messages are generated for every package,
 * guaranteed.  Used in conjunction with {@tutorial Error_Handler.pkg Error_Handler}, this can be
 * used in more complicated packages and applications to handle errors from
 * multiple packages, propagate errors and even transmute them, and log them or
 * do other advanced processing.
 *
 * For those concerned about performance, all of this extra functionality is
 * only 8% slower than the PEAR::raiseError() method.  In other words, the
 * difference is negligible, and allows a huge jump in error handling.
 * @see initialize()
 * @tutorial Error_Raise.pkg
 * @package Error_Raise
 * @version 0.2.2
 * @author Greg Beaver <cellog@php.net>
 * @static
 */
class Error_Raise {
    /**
     * Initialize error raising for a package
     *
     * Use this method to assign an error message handler to a package.  Note:
     * This is an alias to {@link set ErrorMsgGenerator()}
     *
     * As an example, here is a simple Error_Raise usage:
     *
     * <code>
     * <?php
     * // define error constants for package foo
     * define("FOO_ERROR_NOT_NUMBER", 1);
     * define("FOO_ERROR_NOT_STRING", 2);
     *
     * /**
     *  * Error message generator for package Foo
     *  {@*}
     * function getFooMsg($code, $args, $state)
     * {
     *     $msgs = array(
     *        FOO_ERROR_NOT_NUMBER => 'parameter %p% is a %type%, not a number',
     *        FOO_ERROR_NOT_STRING => 'parameter %p% is a %type%, not a string',
     *     );
     *     if (isset($msgs[$code])) {
     *         $message = $msgs[$code];
     *     } else {
     *         $message = 'Code ' . $code . ' is not a valid error code';
     *     }
     *     return Error_Raise::sprintfErrorMessageWithState(
     *        $message, $args, $state);
     * }
     *
     * // initialize error creation
     * Error_Raise::initialize('foo', 'getFooMsg');
     * // that's it!  Now you can do this any of these:
     * Error_Raise::warning('foo', FOO_ERROR_NOT_STRING,
     *    array('p' => 'bar', 'type' => 'object'));
     * Error_Raise::notice('foo', FOO_ERROR_NOT_STRING,
     *    array('p' => 'bar', 'type' => 'object'));
     * Error_Raise::exception('foo', FOO_ERROR_NOT_NUMBER,
     *    array('p' => 'bar', 'type' => 'object'));
     * $e = Error_Raise::error('foo', FOO_ERROR_NOT_NUMBER,
     *    array('p' => 'bar', 'type' => 'object'));
     * echo $e->getMessage();
     * // displays 'foo error : parameter bar is a object, not a number'
     * echo $e->getMessage(ERROR_RAISE_HTML);
     * // displays '<strong>foo error :</strong> parameter <strong>bar</strong>
     * // is a <strong>object</strong>, not a number
     * ?>
     * </code>
     * @param string package name
     * @param array|string call_user_func_array-compatible function/method
     *
     *                     This function name can be either be a
     *                     global function (string), static class method
     *                     array(classname, method), or object method
     *                     array(&$obj, method).  Be sure to assign by
     *                     reference in PHP 4
     * @static
     */
    function initialize($package, $errorMsgGenerator)
    {
        return Error_Raise::setErrorMsgGenerator($package, $errorMsgGenerator);
    }
    
    /**
     * Create a notice from the error code
     * @param string package name
     * @param integer error code
     * @param array associative array of error-specific data.  This can be
     *        anything.  When used with {@link sprintfErrorMessage()}, it will
     *        allow extremely configurable error messages.
     * @throws Error_Raise::ERROR_RAISE_ERROR_INVALID_INPUT
     * @static
     */
    function notice($package, $code, $args = array())
    {
        /*                 validate input                  */
        if (!is_int($code)) {
            return Error_Raise::exception('error_raise', ERROR_RAISE_ERROR_INVALID_INPUT,
                array('var' => '$code',
                      'was' => gettype($code),
                      'expected' => 'int',
                      'paramnum' => 2));
        }
        if (!is_string($package)) {
            return Error_Raise::exception('error_raise',
                ERROR_RAISE_ERROR_INVALID_INPUT,
                array('was' => gettype($package), 'expected' => 'string', 
                      'param' => '$package', 'paramnum' => 1));
        }
        /*                 done validate input                  */
        $packageclass = $package . '_error';
        // create the error class if necessary
        if (!class_exists($packageclass)) {
            $packageclass = 'error_raise_error';
        }
        $mode = 0;
        $extracallback = null;
        if (isset($GLOBALS['_PEAR_default_error_mode']) &&
              ($GLOBALS['_PEAR_default_error_mode'] & PEAR_ERROR_CALLBACK)) {
            $mode |= $GLOBALS['_PEAR_default_error_mode'];
            $extracallback = $GLOBALS['_PEAR_default_error_options'];
        }
        // verify that packages with disabled callbacks don't have
        // PEAR_ERROR_CALLBACK or PEAR_ERROR_TRIGGER as part of the mode
        if (!count($disabledCallbacks = $GLOBALS['_ERROR_RAISE_DISABLED_CALLBACKS'])) {
            if ($disabledCallbacks == '*') {
                $mode &= ~(PEAR_ERROR_CALLBACK | PEAR_ERROR_TRIGGER);
            } else {
                if (in_array(strtolower($package), $disabledCallbacks)) {
                    $mode &= ~(PEAR_ERROR_CALLBACK | PEAR_ERROR_TRIGGER);
                }
            }
        }
        $backtrace = null;
        if (function_exists('debug_backtrace')) {
            $backtrace = debug_backtrace();
        }
        
        return new $packageclass($package, 'notice', $code, $args, $mode,
            $extracallback, $backtrace, false);
    }

    /**
     * Create a warning from the error code
     * @param string package name
     * @param integer error code
     * @param array associative array of error-specific data.  This can be
     *        anything.  When used with {@link sprintfErrorMessage()}, it will
     *        allow extremely configurable error messages.
     * @throws Error_Raise::ERROR_RAISE_ERROR_INVALID_INPUT
     * @static
     */
    function warning($package, $code, $args = array())
    {
        /*                 validate input                  */
        if (!is_int($code)) {
            return Error_Raise::exception('error_raise', ERROR_RAISE_ERROR_INVALID_INPUT,
                array('var' => '$code',
                      'was' => gettype($code),
                      'expected' => 'int',
                      'paramnum' => 2));
        }
        if (!is_string($package)) {
            return Error_Raise::exception('error_raise',
                ERROR_RAISE_ERROR_INVALID_INPUT,
                array('was' => gettype($package), 'expected' => 'string', 
                      'param' => '$package', 'paramnum' => 1));
        }
        /*                 done validate input                  */
        $packageclass = $package . '_error';
        // use the existing error class if possible
        if (!class_exists($packageclass)) {
            $packageclass = 'error_raise_error';
        }
        $mode = 0;
        $extracallback = null;
        if (isset($GLOBALS['_PEAR_default_error_mode']) &&
              ($GLOBALS['_PEAR_default_error_mode'] & PEAR_ERROR_CALLBACK)) {
            $mode |= $GLOBALS['_PEAR_default_error_mode'];
            $extracallback = $GLOBALS['_PEAR_default_error_options'];
        }
        // verify that packages with disabled callbacks don't have
        // PEAR_ERROR_CALLBACK or PEAR_ERROR_TRIGGER as part of the mode
        if (!count($disabledCallbacks = $GLOBALS['_ERROR_RAISE_DISABLED_CALLBACKS'])) {
            if ($disabledCallbacks == '*') {
                $mode &= ~(PEAR_ERROR_CALLBACK | PEAR_ERROR_TRIGGER);
            } else {
                if (in_array(strtolower($package), $disabledCallbacks)) {
                    $mode &= ~(PEAR_ERROR_CALLBACK | PEAR_ERROR_TRIGGER);
                }
            }
        }
        $backtrace = null;
        if (function_exists('debug_backtrace')) {
            $backtrace = debug_backtrace();
        }
        
        return new $packageclass($package, 'warning', $code, $args, $mode,
            $extracallback, $backtrace, false);
    }

    /**
     * Create an error from the error code
     * @param string package name
     * @param integer error code
     * @param array associative array of error-specific data.  This can be
     *        anything.  When used with {@link sprintfErrorMessage()}, it will
     *        allow extremely configurable error messages.
     * @throws Error_Raise::ERROR_RAISE_ERROR_INVALID_INPUT
     * @static
     */
    function error($package, $code, $args = array())
    {
        /*                 validate input                  */
        if (!is_int($code)) {
            return Error_Raise::exception('error_raise', ERROR_RAISE_ERROR_INVALID_INPUT,
                array('var' => '$code',
                      'was' => gettype($code),
                      'expected' => 'int',
                      'paramnum' => 2));
        }
        if (!is_string($package)) {
            return Error_Raise::exception('error_raise',
                ERROR_RAISE_ERROR_INVALID_INPUT,
                array('was' => gettype($package), 'expected' => 'string', 
                      'param' => '$package', 'paramnum' => 1));
        }
        /*                 done validate input                  */
        $packageclass = $package . '_error';
        // use the existing error class if possible
        if (!class_exists($packageclass)) {
            $packageclass = 'error_raise_error';
        }
        $mode = 0;
        $extracallback = null;
        if (isset($GLOBALS['_PEAR_default_error_mode']) &&
              ($GLOBALS['_PEAR_default_error_mode'] & PEAR_ERROR_CALLBACK)) {
            $mode |= $GLOBALS['_PEAR_default_error_mode'];
            $extracallback = $GLOBALS['_PEAR_default_error_options'];
        }
        // verify that packages with disabled callbacks don't have
        // PEAR_ERROR_CALLBACK or PEAR_ERROR_TRIGGER as part of the mode
        if (!count($disabledCallbacks = $GLOBALS['_ERROR_RAISE_DISABLED_CALLBACKS'])) {
            if ($disabledCallbacks == '*') {
                $mode &= ~(PEAR_ERROR_CALLBACK | PEAR_ERROR_TRIGGER);
            } else {
                if (in_array(strtolower($package), $disabledCallbacks)) {
                    $mode &= ~(PEAR_ERROR_CALLBACK | PEAR_ERROR_TRIGGER);
                }
            }
        }
        $backtrace = null;
        if (function_exists('debug_backtrace')) {
            $backtrace = debug_backtrace();
        }
        
        return new $packageclass($package, 'error', $code, $args, $mode,
            $extracallback, $backtrace, false);
    }

    /**
     * Create an exception from the error code
     * @param string package name
     * @param integer error code
     * @param array associative array of error-specific data.  This can be
     *        anything.  When used with {@link sprintfErrorMessage()}, it will
     *        allow extremely configurable error messages.
     * @throws Error_Raise::ERROR_RAISE_ERROR_INVALID_INPUT
     * @static
     */
    function exception($package, $code, $args = array())
    {
        /*                 validate input                  */
        if (!is_int($code)) {
            return Error_Raise::exception('error_raise', ERROR_RAISE_ERROR_INVALID_INPUT,
                array('var' => '$code',
                      'was' => gettype($code),
                      'expected' => 'int',
                      'paramnum' => 2));
        }
        if (!is_string($package)) {
            return Error_Raise::exception('error_raise',
                ERROR_RAISE_ERROR_INVALID_INPUT,
                array('was' => gettype($package), 'expected' => 'string', 
                      'param' => '$package', 'paramnum' => 1));
        }
        /*                 done validate input                  */
        $packageclass = $package . '_error';
        // use the existing error class if possible
        if (!class_exists($packageclass)) {
            $packageclass = 'error_raise_error';
        }
        $mode = 0;
        $extracallback = null;
        if (isset($GLOBALS['_PEAR_default_error_mode']) &&
              ($GLOBALS['_PEAR_default_error_mode'] & PEAR_ERROR_CALLBACK)) {
            $mode |= $GLOBALS['_PEAR_default_error_mode'];
            $extracallback = $GLOBALS['_PEAR_default_error_options'];
        }
        // verify that packages with disabled callbacks don't have
        // PEAR_ERROR_CALLBACK or PEAR_ERROR_TRIGGER as part of the mode
        if (!count($disabledCallbacks = $GLOBALS['_ERROR_RAISE_DISABLED_CALLBACKS'])) {
            if ($disabledCallbacks == '*') {
                $mode &= ~(PEAR_ERROR_CALLBACK | PEAR_ERROR_TRIGGER);
            } else {
                if (in_array(strtolower($package), $disabledCallbacks)) {
                    $mode &= ~(PEAR_ERROR_CALLBACK | PEAR_ERROR_TRIGGER);
                }
            }
        }
        $backtrace = null;
        if (function_exists('debug_backtrace')) {
            $backtrace = debug_backtrace();
        }
        
        return new $packageclass($package, 'exception', $code, $args, $mode,
            $extracallback, $backtrace, false);
    }
    
    /**
     * Return an error object
     *
     * For more control, call this static function directly, otherwise use
     * one of the shorthand methods.
     * @param string package name throwing the error
     * @param integer error code
     * @param string error type
     * @param false|Error_Raise_Error parent error, for cascading
     * @param integer error mode, see {@link PEAR.php}
     * @param string|array callback function for PEAR_ERROR_CALLBACK mode
     * @return Error_Raise_Error
     * @throws ERROR_RAISE_ERROR_INVALID_INPUT
     * @static
     */
    function raise($package, $code, $errorType, $args, $mode = null,
        $extracallback = null, $parent = false)
    {
        /*                 validate input                  */
        if (!is_int($code)) {
            return Error_Raise::exception('error_raise', ERROR_RAISE_ERROR_INVALID_INPUT,
                array('var' => '$code',
                      'was' => gettype($code),
                      'expected' => 'int',
                      'paramnum' => 2));
        }
        if (!is_string($package)) {
            return Error_Raise::exception('error_raise', ERROR_RAISE_ERROR_INVALID_INPUT,
                array('var' => '$package',
                      'was' => gettype($package),
                      'expected' => 'string',
                      'paramnum' => 1));
        }
        if (!is_string($errorType)) {
            return Error_Raise::exception('error_raise', ERROR_RAISE_ERROR_INVALID_INPUT,
                array('paramnum' => 3,
                      'var' => '$errorType',
                      'was' => gettype($errorType),
                      'expected' => 'string'));
        }
        $errorType = strtolower($errorType);
        /*                 done validate input                  */
        // create the error class if necessary
        $errorClass = $package . '_error';
        // use the existing error class if possible
        if (!class_exists($errorClass)) {
            $errorClass = 'error_raise_error';
        }
        
        if (isset($GLOBALS['_PEAR_default_error_mode']) &&
              ($GLOBALS['_PEAR_default_error_mode'] & PEAR_ERROR_CALLBACK)) {
            $mode |= $GLOBALS['_PEAR_default_error_mode'];
            $extracallback = $GLOBALS['_PEAR_default_error_options'];
        }
        // verify that packages with disabled callbacks don't have
        // PEAR_ERROR_CALLBACK or PEAR_ERROR_TRIGGER as part of the mode
        if (count($disabledCallbacks = $GLOBALS['_ERROR_RAISE_DISABLED_CALLBACKS'])) {
            if ($disabledCallbacks == '*') {
                $mode &= ~(PEAR_ERROR_CALLBACK | PEAR_ERROR_TRIGGER);
            } else {
                if (in_array(strtolower($package), $disabledCallbacks)) {
                    $mode &= ~(PEAR_ERROR_CALLBACK | PEAR_ERROR_TRIGGER);
                }
            }
        }
        $backtrace = null;
        if (function_exists('debug_backtrace')) {
            $backtrace = debug_backtrace();
        }
        
        return new $errorClass($package, $errorType, $code, $args, $mode,
            $extracallback, $backtrace, false);
    }
    
    /**
     * Determine if an error was triggered by a package or group of packages
     *
     * This will trigger a PHP warning if parameters are invalid
     * @param Error_Raise_Error
     * @param string|array single package name, or list of package names
     * @return boolean
     */
    function isPackageError($error, $package)
    {
        if (is_string($package)) {
            $package = array(strtolower($package));
        }
        if (!is_a($error, 'Error_Raise_Error')) {
            return false;
        }
        if (array_walk($package, create_function('&$s','$s = strtolower($s);'))) {
            return in_array($error->_package, $package);
        }
        return false;
    }
    
    /**
     * Determine whether input is an error class with level "error"
     *
     * use PEAR::isError() to determine if a class is an error class.  This
     * static method is only for determining the error level of a class
     * @static
     * @param mixed
     * @return boolean true if $obj is an Error_Raise_Error and is type error
     */
    function isError($obj)
    {
        return is_a($obj, 'error_raise_error') && $obj->_type == 'error';
    }
    
    
    /**
     * Determine whether input is an error class with level "exception"
     *
     * use PEAR::isError() to determine if a class is an error class.  This
     * static method is only for determining the error level of a class
     * @static
     * @param mixed
     * @return boolean true if $obj is an Error_Raise_Error and is type exception
     */
    function isException($obj)
    {
        return is_a($obj, 'error_raise_error') && $obj->_type == 'exception';
    }
    
    
    /**
     * Determine whether input is an error class with level "notice"
     *
     * use PEAR::isError() to determine if a class is an error class.  This
     * static method is only for determining the error level of a class
     * @static
     * @param mixed
     * @return boolean true if $obj is an Error_Raise_Error and is type notice
     */
    function isNotice($obj)
    {
        return is_a($obj, 'error_raise_error') && $obj->_type == 'notice';
    }
    
    
    /**
     * Determine whether input is an error class with level "warning"
     *
     * use PEAR::isError() to determine if a class is an error class.  This
     * static method is only for determining the error level of a class
     * @static
     * @param mixed
     * @return boolean true if $obj is an Error_Raise_Error and is type warning
     */
    function isWarning($obj)
    {
        return is_a($obj, 'error_raise_error') && $obj->_type == 'warning';
    }
    
    /**
     * Get the list of packages for which callbacks are temporarily disabled
     *
     * @see disableErrorCallbacks
     * @return false|array|*
     */
    function getDisabledCallbacks()
    {
        if (!count($GLOBALS['_ERROR_RAISE_DISABLED_CALLBACKS'])) {
            return false;
        }
        return $GLOBALS['_ERROR_RAISE_DISABLED_CALLBACKS']
            [count($GLOBALS['_ERROR_RAISE_DISABLED_CALLBACKS']) - 1];
    }
    
    /**
     * Temporarily disable any error callbacks/trigger_error.
     *
     * To temporarily disable callback functions in order to suppress any
     * error reporting of internal errors, use this static method.  To
     * selectively disable a short list of packages, and allow others to inform
     * callbacks of their creation, pass in an array of the names of packages
     * to disable errors for.
     *
     * This is similar to PEAR::expectError(), except that only global
     * error-handling is affected
     * @param array list of packages to disable error callbacks for
     * @throws Error_Raise_Exception::ERROR_RAISE_ERROR_INVALID_INPUT
     * @return true|Error_Raise_Exception
     * @static
     */
    function disableErrorCallbacks($packages = array())
    {
        if (!is_array($packages) && !is_string($packages)) {
            return Error_Raise::exception('error_raise',
                ERROR_RAISE_ERROR_INVALID_INPUT,
                array('was' => gettype($packages), 'expected' => 'array|string',
                      'param' => '$packages', 'paramnum' => 1));
        }
        if (is_string($packages)) {
            $packages = array($packages);
        }
        $newpack = array();
        foreach($packages as $i => $package) {
            if (!is_string($package)) {
                return Error_Raise::exception('error_raise',
                ERROR_RAISE_ERROR_INVALID_INPUT,
                array('was' => gettype($package), 'expected' => 'string',
                      'param' => '$packages[' . $i . ']', 'paramnum' => 1));
            }
            $newpack[] = strtolower($package);
        }
        if (!count($newpack)) {
            array_push($GLOBALS['_ERROR_RAISE_DISABLED_CALLBACKS'], '*');
        } else {
            array_push($GLOBALS['_ERROR_RAISE_DISABLED_CALLBACKS'], $newpack);
        }
        return true;
    }
    
    /**
     * Re-enable callbacks that were temporarily disabled with
     * {@link disableErrorCallbacks()}
     */
    function reenableErrorCallbacks()
    {
        array_pop($GLOBALS['_ERROR_RAISE_DISABLED_CALLBACKS']);
    }
    
    /**
     * Change the error message generation method from one set in the
     * constructor
     *
     * If later logic requires the use of different error messages for a
     * package, use this static method to change the message generator.
     * @return true|Error_Raise_Error
     * @param string package name
     * @param string|array callback
     * @static
     */
    function setErrorMsgGenerator($package, $errorMsgGenerator)
    {
        if ($errorMsgGenerator) {
            $package = strtolower($package);
            Error_Raise::disableErrorCallbacks();
            $e = Error_Util::_validCallback($errorMsgGenerator);
            Error_Raise::reenableErrorCallbacks();
            if (PEAR::isError($e)) {
                return $e->rePackageError('Error_Raise', 'error',
                    ERROR_RAISE_ERROR_INVALID_ERRMSGGEN,
                    array('package' => $package));
            }
            $GLOBALS['_ERROR_RAISE_MSGS'][$package] = $errorMsgGenerator;
            return true;
        }
    }
    
    /**
     * Set the function/method used to retrieve context information
     * for an error, like the file and line number that generated an error.
     *
     * In most cases, the file or line number is the file/line returned from
     * the debug_backtrace() called at the error's creation.  In some cases,
     * the error/warning/notice occurs when processing another file, and this
     * function can be used to set the callback that should be used to
     * retrieve this information.
     *
     * A function passed to setContextGrabber() must return an associative
     * array of values.  These values will be assigned to properties of a new
     * error object created by {@link raise()}.  In this way, the properties can
     * be used by {@link Error_Raise_Error::getErrorPrefix()} to generate
     * context-specific information for each error.
     *
     * An example of a custom context grabber:
     *
     * <code>
     * class doesDB {
     *     /**
     *      * We ignore the backtrace and instead use DB-specific information
     *      * @param array debug_backtrace() output
     *      * @param integer backtrace frame to use
     *      {@*}
     *     function grabContext($trace, $frame)
     *     {
     *         $return = array(
     *            '_db' => $this->_databaseName,
     *            '_table' => $this->_tableName,
     *            '_queryPos' => $this->_queryPos,);
     *     }
     * }
     * </code>
     *
     * Then, you can use this context information in a custom implementation
     * of {@link Error_Raise_Error::getErrorPrefix()}
     *
     * <code>
     * $db = &new doesDB;
     * Error_Raise::setContextGrabber('myDB', array(&$db, 'grabContext'));
     * ...
     * class myDB_Error extends Error_Raise_Error {
     *     function getErrorPrefix($state = ERROR_RAISE_TEXT)
     *     {
     *         $prefix = $this->getPackage() . ' ' . $this->getErrorType();
     *         if (isset($this->_db)) {
     *             $prefix .= ' in database "' . $this->_db . '"';
     *         }
     *         if (isset($this->_table)) {
     *             $prefix .= ', ' . $this->_table;
     *         }
     *         if (isset($this->_queryPos)) {
     *             $prefix .= ' query position ' . $this->_queryPos;
     *         }
     *         $prefix .= ' : ';
     *     }
     * }
     * </code>
     * @param string package name
     * @param string|array
     * @static
     */
    function setContextGrabber($package, $fileline)
    {
        $package = strtolower($package);
        $GLOBALS['_ERROR_RAISE_CONTEXTGRABBER'][$package] = $fileline;
        return true;
    }
    
    /**
     * Get an Error_Raise error message from a code
     *
     * This is the error message generator for the Error_Raise package, and
     * should not be used by other packages.  It can be used as an example
     * of one way to generate an error message
     * @access private
     * @return string error message from error code
     * @param integer
     * @param array
     * @static
     */
    function _getErrorMessage($code, $args = array(), $state = ERROR_RAISE_TEXT)
    {
        if (!is_array($args)) {
            return 'Error: $args passed to Error_Raise::_getErrorMessage is '.
                'not an array but a '.gettype($args);
        }
        $messages =
        array(
            ERROR_RAISE_ERROR_CLASS_DOESNT_EXIST =>
                'class "%cl%" does not exist',
            ERROR_RAISE_ERROR_CODENOMSG =>
                'package "%package%" has no registered error message '
                    . 'generator',
            ERROR_RAISE_ERROR_ALREADY_INITIALIZED =>
                'package "%package%" has already been initialized',
            ERROR_RAISE_ERROR_INVALID_INPUT =>
                'invalid input, parameter #%paramnum% '
                    . '"%var%" was expecting '
                    . '"%expected%", instead got "%was%"',
            ERROR_RAISE_ERROR_INVALID_ERROR_CLASS =>
                'error class "%class%" in package '
                    . '"%package%," error type '
                    . '"%type%" does not descend from '
                    . 'Error_Raise_Error.  Use '
                    . 'PEAR::raiseError() for normal PEAR_Error classes',
            ERROR_RAISE_ERROR_INVALID_ERRMSGGEN =>
                'Invalid callback passed to setErrorMsgGenerator() for package'
                    . ' %package%',
            ERROR_RAISE_ERROR_INVALID_CONTEXT_GRABBER =>
                'An invalid callback was passed as a context grabber for '
                    . 'package %package%',
            ERROR_RAISE_ERROR_INVALID_CONTEXTGRABBER_RETURN =>
                'The context grabber for package %package% did not return an '
                    . 'array, but instead returned a %type%',
             );
        if (is_int($code) && isset($messages[$code])) {
            $msg = $messages[$code];
            return Error_Raise::sprintfErrorMessageWithState($msg,
                $args, $state);
        } else {
            return 'Error: code ' . $code . ' not found';
        }
    }
    
    /**
     * Simple utility function for replacing variables with values in an error
     * message template
     *
     * This simple str_replace-based function can be used to have an
     * order-independent sprintf, so error messages can be passed in
     * with different grammar ordering, or other possibilities without
     * changing the source code.
     *
     * In addition, if the argument is an object and has a toString() method,
     * it will be called in order to transform the object into viewable text.
     * If it is an array, the values will be joined together by commas
     *
     * Variables should simply be surrounded by % as in %varname%
     * @param string error message template
     * @param array associative array of template var -> message text
     * @see sprintfErrorMessageWithState
     * @static
     */
    function sprintfErrorMessage($msg, $args)
    {
        if (is_array($args)) {
            foreach($args as $name => $value) {
                $val = $value;
                if (is_object($value) && method_exists($value, 'tostring')) {
                    $val = $value->toString();
                }
                if (is_array($value)) {
                    $i = 0;
                    $val = '';
                    foreach($value as $item) {
                        if ($i++ > 0) {
                            $val .= ', ';
                        }
                        $val .= $item;
                    }
                }
                $msg = str_replace("%$name%", $val, $msg);
            }
        }
        return $msg;
    }
    
    /**
     * Simple utility function for replacing variables with values in an error
     * message template, with special formatting for state.
     *
     * This simple str_replace-based function can be used to have an
     * order-independent sprintf, so error messages can be passed in
     * with different grammar ordering, or other possibilities without
     * changing the source code.
     *
     * In addition, if the argument is an object and has a toString() method,
     * it will be called in order to transform the object into viewable text.
     * If it is an array, the values will be joined together by commas
     *
     * Variables should simply be surrounded by % as in %varname%
     * @param string error message template
     * @param array associative array of template var -> message text
     * @param ERROR_RAISE_TEXT|ERROR_RAISE_ANSI|ERROR_RAISE_HTML
     * @see sprintfErrorMessage
     * @static
     */
    function sprintfErrorMessageWithState($msg, $args, $state)
    {
        if (is_array($args)) {
            foreach($args as $name => $value) {
                $val = $value;
                if (is_object($value) && method_exists($value, 'tostring')) {
                    $val = $value->toString();
                }
                if (is_array($value)) {
                    $i = 0;
                    $val = '';
                    foreach($value as $item) {
                        if ($i++ > 0) {
                            $val .= ', ';
                        }
                        $val .= $item;
                    }
                }
                if ($state == ERROR_RAISE_ANSI) {
                    $msg = str_replace("%$name%",
                        '%r%U%%' . $val . '%%U%r', $msg);
                } elseif ($state == ERROR_RAISE_HTML) {
                    $msg = str_replace("%$name%", 
                        '<strong>' . $val . '</strong>', $msg);
                } else {
                    $msg = str_replace("%$name%", $val, $msg);
                }
            }
        }
        if ($state == ERROR_RAISE_HTML) {
            return nl2br($msg);
        }
        return $msg;
    }
}

if (!function_exists('is_a')) {
/**
 * @ignore
 */
function is_a($obj, $classname)
{
    return (get_class($obj) == strtolower($classname)) ||
        is_subclass_of($obj, $classname);
}
}
?>
