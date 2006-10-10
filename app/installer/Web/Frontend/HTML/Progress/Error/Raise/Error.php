<?php
/**
 * Error class for the Error_Raise package.
 * @package Error_Raise
 * @version 0.2.2
 * @author Greg Beaver <cellog@php.net>
 */
/**#@+
 * Error constants for Error_Raise
 */
/**
 * Error used if a class passed to initialize hasn't been defined yet
 *
 * {@link initialize()} allows
 */
define('ERROR_RAISE_ERROR_CLASS_DOESNT_EXIST', 1);
/**
 * Error used if a package raises an error, but has no registered code->message
 * mapping function.
 */
define('ERROR_RAISE_ERROR_CODENOMSG', 3);
/**
 * Error used if a package already has a package_Raise class and
 * Error_Raise::initialize('package') is called
 */
define('ERROR_RAISE_ERROR_ALREADY_INITIALIZED', 4);
/**
 * Error used if a method doesn't exist in a callback passed in
 */
define('ERROR_RAISE_ERROR_METHOD_DOESNT_EXIST', 5);
/**
 * Error used if a function doesn't exist in a callback passed in
 */
define('ERROR_RAISE_ERROR_FUNCTION_DOESNT_EXIST', 6);
/**
 * Error used when parameters to functions don't match expected types
 */
define('ERROR_RAISE_ERROR_INVALID_INPUT', 7);
/**
 * Error used when a new error class is not a descendant of Error_Raise_Error
 */
define('ERROR_RAISE_ERROR_INVALID_ERROR_CLASS', 8);
/**
 * Error used when an internal function is passed as a callback - this is never
 * allowed.
 */
define('ERROR_RAISE_ERROR_INTERNAL_FUNCTION', 9);
/**
 * Error used when an invalid callback is passed as an error message generator
 */
define('ERROR_RAISE_ERROR_INVALID_ERRMSGGEN', 10);
/**
 * Error used when an invalid callback is passed as a context grabber
 */
define('ERROR_RAISE_ERROR_INVALID_CONTEXT_GRABBER', 11);
/**
 * Error used when a context grabber does not return an array
 */
define('ERROR_RAISE_ERROR_INVALID_CONTEXTGRABBER_RETURN', 12);
/**#@-*/

/**
 * Extended error class with support for extensive user info
 *
 * Enhancements to PEAR_Error include:
 *
 * - Error messages are generated dynamically at runtime, preserving specific
 *   error data that would otherwise need to be extracted from an error message
 *   using a regexp hack.  This allows greater control and automatic error
 *   processing
 * - {@link getMessage()} accepts a parameter indicating what display environment
 *   the message exists in.  Legal values are ERROR_RAISE_TEXT, ERROR_RAISE_ANSI,
 *   and ERROR_RAISE_HTML
 * - {@link getPackage()} returns the package associated with an error.  No
 *   longer is there any guesswork involved with hacking getType()
 * - {@link getErrorType()} allows for the first time, error levels.  Not all
 *   errors are simply fatal errors, and this uses PHP's default error levels
 *   and allows notice/warning/error/exception levels.
 * - Customizable automatic context information.  {@link getErrorPrefix()}
 *   and {@link getErrorSuffix()} are used in conjunction with
 *   {@link Error_Raise::setContextGrabber()} to display things like File/Line
 *   number, class and function of calling context, and optionally, a stack
 *   trace (this is only possible in PHP 4.3.0 and above)
 * - Cascading errors.  If another error was transformed into this one,
 *   {@link getParent()} can be used to retrieve the error object that
 *   originally caused the current one to be thrown.  An example might be when
 *   a database error occurs in a CMS.  The common user shouldn't see database
 *   errors, but administrators should.  By accessing getParent() for admins,
 *   and just the CMS error for users, this is easy to implement.
 * @version 0.2.2
 * @author Greg Beaver <cellog@php.net>
 * @package Error_Raise
 * @todo Implement pretty stack dump for those who want it
 */
class Error_Raise_Error extends PEAR_Error {
    /**
     * Parameters passed to the constructor to be formatted into User Info
     * @var array
     * @access private
     */
    var $_params;
    /**
     * File in which this error originated
     * @var string|null
     * @access private
     */
    var $_file;
    /**
     * Line number on which this error originated
     * @var integer|null
     * @access private
     */
    var $_line;
    /**
     * Class in which this error originated
     * @var string|null
     * @access private
     */
    var $_class;
    /**
     * Function/method on which this error originated
     * @var string|null
     * @access private
     */
    var $_function;
    /**
     * Parent error, if this is a cascaded error
     * @see getParent()
     * @var Error_Raise_Error|false
     * @access private
     */
    var $_parent = false;
    /**
     * Used for determining package name
     * @access private
     * @var string
     */
    var $_type = 'error';
    /**
     * Used for determining how man lines of code on either side of an error
     * to include in a context code listing
     * @var integer
     * @access private
     */
    var $_contextLines = 5;
    /**
     * @var string
     * @access private
     */
    var $_package = 'error_raise';
    
    /**
     * Note that error objects should not be created directly, but with the
     * help of {@link Error_Raise::raise()} or the easy helper functions listed
     * below.
     * @see Error_Raise::exception()
     * @see Error_Raise::error()
     * @see Error_Raise::warning()
     * @see Error_Raise::notice()
     * @param string error type (notice/warning/error/exception)
     * @param integer error code
     * @param array extra information to be passed to the error message
     *        generation
     * @param integer|null mode, see {@link PEAR.php}
     * @param string|array|null callback function/method for PEAR_ERROR_CALLBACK
     */
    function Error_Raise_Error($package, $type, $code, $extrainfo = array(),
        $mode = null, $extracallback = null, $backtrace = null, $parent = null)
    {
        $this->_type = $type;
        $this->_parent = $parent;
        $this->_package = strtolower($package);
        $this->backtrace = $backtrace;
        $this->userinfo = $extrainfo;
        $this->code = $code;
        if ($mode === null) {
            $mode = 0;
        }
        $this->mode = $mode;
        if ($type == 'exception') {
            $this->mode |= PEAR_ERROR_EXCEPTION;
        }
        if ($type == 'warning') {
            $this->level = E_USER_WARNING;
        }
        if ($type == 'notice') {
            $this->level = E_USER_NOTICE;
        }
        if ($type == 'error'
              || $type == 'exception') {
            $this->level = E_USER_ERROR;
        }
        $this->callback = $extracallback;
        if ($this->mode & PEAR_ERROR_TRIGGER) {
            trigger_error($this->getMessage(), $this->level);
        }
        if ($this->mode & PEAR_ERROR_DIE) {
            die($this->getMessage());
        }
        if ($this->mode & PEAR_ERROR_CALLBACK) {
            if (is_string($this->callback) && strlen($this->callback)) {
                call_user_func($this->callback, $this);
            } elseif (is_array($this->callback) &&
                      sizeof($this->callback) == 2 &&
                      is_object($this->callback[0]) &&
                      is_string($this->callback[1]) &&
                      strlen($this->callback[1])) {
                      call_user_func($this->callback, $this);
            }
        }
        if (PEAR_ZE2 && $this->mode & PEAR_ERROR_EXCEPTION) {
            eval('throw $this;');
        }
    }
    
    /**
     * Process a debug_backtrace.  This method should be called by
     * either getErrorPrefix() or getErrorSuffix() in situations where it is
     * needed.
     * @param array output from debug_backtrace
     * @param Error_Raise_Error
     * @param string error type (warning, error, exception, notice)
     * @access protected
     */
    function _grabBacktrace()
    {
        if (isset($GLOBALS['_ERROR_RAISE_CONTEXTGRABBER']
              [$this->_package])) {
            list($frame, $functionframe) = Error_Util::_parseBacktrace(
                $this->backtrace, $this->_type, $this);
            $func = $GLOBALS['_ERROR_RAISE_CONTEXTGRABBER']
                [$this->_package];
            $fileline = call_user_func($func, $this->backtrace,
                $frame, $functionframe);
            if ($fileline) {
                if (!is_array($fileline)) {
                    if ($this->_package == 'error_raise') {
                        die('EXCEPTION: Error_Raise context grabber '
                        . 'did NOT return an array in '
                        . 'Error_Raise::_grabBacktrace().  You should NEVER'
                        . 'see this message');
                    }
                    return Error_Raise::exception('error_raise',
                        ERROR_RAISE_ERROR_INVALID_CONTEXTGRABBER_RETURN,
                        array('type' => gettype($fileline),
                              'package' => $this->_package));
                }
                foreach($fileline as $var => $value) {
                    // for future extensibility
                    $this->$var = $value;
                }
                if (!isset($this->_context) && isset($this->_file) &&
                      isset($this->_line)) {
                    $this->_context =
                        Error_Util::getErrorContext($this->_file, $this->_line);
                }
            }
        }
    }
    
    /**
     * Retrieve the package name of this error object
     * @return string
     */
    function getPackage()
    {
        return $this->_package;
    }
    
    /**
     * Retrieve the error message for this object
     *
     * Error messages are dynamically generated from the callback
     * set by {@link Error_Raise::initialize()}, or passed to
     * {@link Error_Raise::setErrorMsgGenerator()}.  The function is passed the
     * error code, user information that was set in the constructor, and the
     * display state, and must return a string.
     *
     * An example error message generator is Error_Raises's own generator
     * found at {@link Error_Raise::_getErrorMessage()}
     * @uses getErrorPrefix() grab the error prefix generated dynamically
     *       from context information
     * @return string
     * @throws ERROR_RAISE_ERROR_CODENOMSG if no error message generator
     *         function has been registered for this package
     * @param ERROR_RAISE_TEXT|ERROR_RAISE_HTML|ERROR_RAISE_ANSI format state
     *        that error message should be in
     * @param boolean determines whether source code context is displayed with
     *        the message.  By default, the presence of a context grabbing
     *        function sets this to true.  Pass in true to turn off context
     *        display
     */
    function getMessage($state = ERROR_RAISE_TEXT, $context = false)
    {
        $package = $this->_package;
        if (isset($GLOBALS['_ERROR_RAISE_MSGS'][$package])) {
            if (function_exists('debug_backtrace')) {
                $e = $this->_grabBacktrace($this->backtrace, $this, $this->_type);
                if (PEAR::isError($e)) {
                    return $e;
                }
            }
            $this->error_message_prefix = $this->getErrorPrefix($state, $context);
            $this->message = $this->getBareMessage($state);
            $this->error_message_suffix = $this->getErrorSuffix($state, $context);
            return $this->error_message_prefix . $this->message .
                $this->error_message_suffix;
        } else {
            $e = Error_Raise::warning('error_Raise', ERROR_RAISE_ERROR_CODENOMSG,
                array('package' => $package));
            if (strlen($this->message)) {
                return parent::getMessage();
            }
            return $e->getMessage($state);
        }
    }
    
    /**
     * Return the error message without prefix or suffix
     * @param ERROR_RAISE_TEXT|ERROR_RAISE_HTML|ERROR_RAISE_ANSI format state
     *        that error message should be in
     */
    function getBareMessage($state = ERROR_RAISE_TEXT)
    {
        $info = $this->getUserInfo();
        if (!is_array($info)) {
            $info = array();
        }
        $args = array_merge(array($this->code), array($info), array($state));
        return call_user_func_array(
                $GLOBALS['_ERROR_RAISE_MSGS'][$this->getPackage()], $args);
    }
    
    /**
     * Returns default error prefix
     *
     * The default error prefix is:
     *
     * Packagename exception|error|warning|notice[ in file "filename"][
     * on line ##]:
     *
     * If the Console_Color package is present, ERROR_RAISE_ANSI will return
     * a colored error message for console display.
     * @param ERROR_RAISE_TEXT|ERROR_RAISE_HTML|ERROR_RAISE_ANSI format state
     *        that error message should be in
     * @param boolean If true, then return any context information.  By default,
     *                this is ignored in getErrorPrefix(), and handled in
     *                {@link getErrorSuffix()}
     * @see Error_Raise::setContextGrabber()
     * @return string
     */
    function getErrorPrefix($state = ERROR_RAISE_TEXT, $context = false)
    {
        if (isset($this->backtrace)) {
            $this->_grabBacktrace();
        }
        $vars = array('package' => $this->getPackage(),
                      'errortype' => $this->getErrorType());
        $prefix = '%package% %errortype%';
        if (isset($this->_file)) {
            $vars['file'] = $this->_file;
            $prefix .= ' in file "%file%"';
        }
        if (isset($this->_function)) {
            $prefix .= ', ';
            if (isset($this->_class)) {
                $vars['class'] = $this->_class;
                $prefix .= '%class%::';
            }
            $vars['function'] = $this->_function;
            $prefix .= '%function%';
        }
        if (isset($this->_line)) {
            $vars['line'] = $this->_line;
            $prefix .= ' on line %line%';
        }
        $prefix .= " :\n";
        return Error_Raise::sprintfErrorMessageWithState($prefix, $vars, $state);
    }
    
    /**
     * Returns default error suffix
     * @param ERROR_RAISE_TEXT|ERROR_RAISE_HTML|ERROR_RAISE_ANSI format state
     *        that error message should be in
     * @param boolean If true, then return any context information.  By default,
     *                the source code is highlighted as PHP in HTML and returned
     *                or just returned as is in other states
     */
    function getErrorSuffix($state = ERROR_RAISE_TEXT, $context = false)
    {
        if ($context) {
            if (isset($this->backtrace)) {
                $this->_grabBacktrace();
            }
            if (isset($this->_file) && isset($this->_line)) {
                $context = Error_Util::getErrorContext($this->_file, $this->_line,
                                                       $this->_contextLines);
                if ($state == ERROR_RAISE_HTML) {
                    return @highlight_string(trim($context['source']), true);
                } else {
                    $vars['context'] = trim($context['source']);
                }
                $suffix = "\n%context%\n";
                return Error_Raise::sprintfErrorMessageWithState($suffix, $vars,
                    $state);
            }
        } else {
            return '';
        }
    }
    
    /**
     * Get the name of this error, one of error, exception, warning, or notice.
     * @return string
     */
    function getErrorType()
    {
        return $this->_type;
    }
    
    /**
     * Get parent error, if this is a cascaded error
     *
     * If this error object is a result of an error from another package,
     * it is a cascaded error.  An example might be a database error in a CMS.
     * The CMS most likely will not want to display to the user an error that
     * states "this table does not have that field", but instead re-package
     * the error into format "your request failed because ...".  However, a
     * CMS administrator would want to see the database error in order to
     * pass on debug information to developers (or fix it himself/herself).
     * This variable stores the parent object, making this possible.  Cascading
     * is unlimited, and can be accessed through getParent()
     * @return false|Error_Raise_Error parent error object
     */
    function &getParent()
    {
        return $this->_parent;
    }
    
    /**
     * Set the parent error object, for error cascading
     * @see getParent()
     * @param Error_Raise_Error|false
     */
    function setParent(&$parent)
    {
        $this->_parent = &$parent;
    }
    
    /**
     * Set the number of lines of source code to use for errors that contain
     * file and line number information.
     *
     * This setting should be considered 1/2 of the number of lines - for
     * instance, if this is set to 5, then there will be up to 5 lines of code
     * from before and 5 lines of code from after the error code line.
     *
     * If $lines > 10 we reset it to 10
     * @param integer
     */
    function setContextLines($lines)
    {
        if (!is_int($lines)) {
            // no errors for the dummies
            $lines = 5;
        }
        if ($lines > 10) {
            $lines = 10;
        }
        $this->_contextLines = $lines;
    }
    
    /**
     * Re-assign an error to this error's package
     *
     * If an error is returned that needs to be re-defined for another package,
     * this method should be called from the pre-existing error in order to
     * transform it into another package's error
     * @param string package name of new error object
     * @param error|warning|notice|exception error severity
     * @param integer error code
     * @param array|null|false any new information for the error message,
     *        otherwise existing information will be used.  If passed false,
     *        the new information will be set to null
     * @throws ERROR_RAISE_ERROR_INVALID_INPUT
     */
    function &rePackageError($package, $type, $code, $extrainfo = null)
    {
        if (!is_string($type)) {
            return Error_Raise::exception('error_raise',
                ERROR_RAISE_ERROR_INVALID_INPUT,
                array('was' => gettype($type), 'expected' => 'string',
                'param' => '$type', 'paramnum' => 2));
        }
        $type = strtolower($type);
        if (!in_array($type, array('error', 'warning', 'notice', 'exception'))) {
            return Error_Raise::exception('error_raise',
                ERROR_RAISE_ERROR_INVALID_INPUT,
                array('was' => $type, 'expected' => array('error', 'warning',
                 'notice', 'exception'), 'param' => '$type', 'paramnum' => 2));
        }
        if (!is_string($package)) {
            return Error_Raise::exception('error_raise',
                ERROR_RAISE_ERROR_INVALID_INPUT,
                array('was' => gettype($package), 'expected' => 'string',
                'param' => '$package', 'paramnum' => 1));
        }
        if (!class_exists($errorclass = $package . '_error')) {
            $errorclass = 'error_raise_error';
        }
        $backtrace = false;
        if (function_exists('debug_backtrace')) {
            $backtrace = debug_backtrace();
        }
        $e = new $errorclass($package, $type, $code, $extrainfo, $this->mode,
            $this->callback, $backtrace, $this);
        return $e;
    }
}
?>