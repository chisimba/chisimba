<?php
/**
 * HTML loading bar with only PHP and JS interface.
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   HTML
 * @package    HTML_Progress
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/HTML_Progress
 * @since      File available since Release 1.0
 */

require_once 'HTML/Progress/DM.php';
require_once 'HTML/Progress/UI.php';

/**#@+
 * Progress Bar shape types
 *
 * @var        integer
 * @since      0.6
 */
define ('HTML_PROGRESS_BAR_HORIZONTAL', 1);
define ('HTML_PROGRESS_BAR_VERTICAL',   2);
/**#@-*/

/**#@+
 * Progress Bar shape types
 *
 * @var        integer
 * @since      1.2.0RC1
 */
define ('HTML_PROGRESS_POLYGONAL',      3);
define ('HTML_PROGRESS_CIRCLE',         4);
/**#@-*/

/**
 * Basic error code that indicate a wrong input
 *
 * @var        integer
 * @since      1.0
 */
define ('HTML_PROGRESS_ERROR_INVALID_INPUT',   -100);

/**
 * Basic error code that indicate a wrong callback definition.
 * Allows only function or class-method structure.
 *
 * @var        integer
 * @since      1.1
 */
define ('HTML_PROGRESS_ERROR_INVALID_CALLBACK',-101);

/**
 * Basic error code that indicate a deprecated method
 * that may be removed at any time from a future version
 *
 * @var        integer
 * @since      1.2.0RC1
 */
define ('HTML_PROGRESS_DEPRECATED',            -102);

/**#@+
 * One of five possible return values from the error Callback
 *
 * @see        HTML_Progress::_handleError
 * @var        integer
 * @since      1.2.0
 */
/**
 * If this is returned, then the error will be both pushed onto the stack
 * and logged.
 */
define('HTML_PROGRESS_ERRORSTACK_PUSHANDLOG', 1);
/**
 * If this is returned, then the error will only be pushed onto the stack,
 * and not logged.
 */
define('HTML_PROGRESS_ERRORSTACK_PUSH', 2);
/**
 * If this is returned, then the error will only be logged, but not pushed
 * onto the error stack.
 */
define('HTML_PROGRESS_ERRORSTACK_LOG', 3);
/**
 * If this is returned, then the error is completely ignored.
 */
define('HTML_PROGRESS_ERRORSTACK_IGNORE', 4);
/**
 * If this is returned, then the error will only be logged, but not pushed
 * onto the error stack because will halt script execution.
 */
define('HTML_PROGRESS_ERRORSTACK_LOGANDDIE', 5);
/**#@-*/


/**#@+
 * Log types for PHP's native error_log() function
 *
 * @see        HTML_Progress::_errorHandler
 * @var        integer
 * @since      1.2.0
 */
/**
 * Use PHP's system logger
 */
define('HTML_PROGRESS_LOG_TYPE_SYSTEM',  0);
/**
 * Use PHP's mail() function
 */
define('HTML_PROGRESS_LOG_TYPE_MAIL',    1);
/**
 * Append to a file
 */
define('HTML_PROGRESS_LOG_TYPE_FILE',    3);
/**#@-*/

/**
 * Global error message callback.
 * This will be used to generate the error message
 * from the error code.
 *
 * @global     false|string|array      $GLOBALS['_HTML_PROGRESS_CALLBACK_MESSAGE']
 * @since      1.2.0
 * @access     private
 * @see        HTML_Progress::_initErrorHandler
 */
$GLOBALS['_HTML_PROGRESS_CALLBACK_MESSAGE'] = false;

/**
 * Global error context callback.
 * This will be used to generate the error context for an error.
 *
 * @global     false|string|array      $GLOBALS['_HTML_PROGRESS_CALLBACK_CONTEXT']
 * @since      1.2.0
 * @access     private
 * @see        HTML_Progress::_initErrorHandler
 */
$GLOBALS['_HTML_PROGRESS_CALLBACK_CONTEXT'] = false;

/**
 * Global error push callback.
 * This will be called every time an error is pushed onto the stack.
 * The return value will be used to determine whether to allow
 * an error to be pushed or logged.
 *
 * @global     false|string|array      $GLOBALS['_HTML_PROGRESS_CALLBACK_PUSH']
 * @since      1.2.0
 * @access     private
 * @see        HTML_Progress::_initErrorHandler
 */
$GLOBALS['_HTML_PROGRESS_CALLBACK_PUSH'] = false;

/**
 * Global error handler callback.
 * This will handle any errors raised by this package.
 *
 * @global     false|string|array      $GLOBALS['_HTML_PROGRESS_CALLBACK_ERRORHANDLER']
 * @since      1.2.0
 * @access     private
 * @see        HTML_Progress::_initErrorHandler
 */
$GLOBALS['_HTML_PROGRESS_CALLBACK_ERRORHANDLER'] = false;

/**
 * Global associative array of key-value pairs
 * that are used to specify any handler-specific settings.
 *
 * @global     array                   $GLOBALS['_HTML_PROGRESS_ERRORHANDLER_OPTIONS']
 * @since      1.2.0
 * @access     private
 * @see        HTML_Progress::_initErrorHandler
 */
$GLOBALS['_HTML_PROGRESS_ERRORHANDLER_OPTIONS'] = array();

/**
 * Global error stack for this package.
 *
 * @global     array                   $GLOBALS['_HTML_PROGRESS_ERRORSTACK']
 * @since      1.2.0
 * @access     private
 * @see        HTML_Progress::raiseError
 */
$GLOBALS['_HTML_PROGRESS_ERRORSTACK'] = array();


/**
 * HTML loading bar with only PHP and JS interface.
 *
 * The HTML_Progress class allow you to add a loading bar
 * to any of your xhtml document.
 * You should have a browser that accept DHTML feature.
 *
 * @category   HTML
 * @package    HTML_Progress
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: 1.2.5
 * @link       http://pear.php.net/package/HTML_Progress
 * @since      Class available since Release 1.0
 */

class HTML_Progress
{
    /**
     * Whether the progress bar is in determinate or indeterminate mode.
     * The default is false.
     * An indeterminate progress bar continuously displays animation indicating
     * that an operation of unknown length is occuring.
     *
     * @var        boolean
     * @since      1.0
     * @access     private
     * @see        setIndeterminate(), isIndeterminate()
     */
    var $_indeterminate;

    /**
     * Whether to display a border around the progress bar.
     * The default is false.
     *
     * @var        boolean
     * @since      1.0
     * @access     private
     * @see        setBorderPainted(), isBorderPainted()
     */
    var $_paintBorder;

    /**
     * Whether to textually display a string on the progress bar.
     * The default is false.
     * Setting this to true causes a textual display of the progress to be rendered
     * on the progress bar. If the $_progressString is null, the percentage of completion
     * is displayed on the progress bar. Otherwise, the $_progressString is rendered
     * on the progress bar.
     *
     * @var        boolean
     * @since      1.0
     * @access     private
     * @see        setStringPainted(), isStringPainted()
     */
    var $_paintString;

    /**
     * An optional string that can be displayed on the progress bar.
     * The default is null.
     * Setting this to a non-null value does not imply that the string
     * will be displayed.
     *
     * @var        string
     * @since      1.0
     * @access     private
     * @see        getString(), setString()
     */
    var $_progressString;

    /**
     * The data model (HTML_Progress_DM instance or extends)
     * handles any mathematical issues arising from assigning faulty values.
     *
     * @var        object
     * @since      1.0
     * @access     private
     * @see        getDM(), setDM()
     */
    var $_DM;

    /**
     * The user interface (HTML_Progress_UI instance or extends)
     * handles look-and-feel of the progress bar.
     *
     * @var        object
     * @since      1.0
     * @access     private
     * @see        getUI(), setUI()
     */
    var $_UI;

    /**
     * The label that uniquely identifies this progress object.
     *
     * @var        string
     * @since      1.0
     * @access     private
     * @see        getIdent(), setIdent()
     */
    var $_ident;

    /**
     * Holds all HTML_Progress_Observer objects that wish to be notified of new messages.
     *
     * @var        array
     * @since      1.0
     * @access     private
     * @see        getListeners(), addListener(), removeListener()
     */
    var $_listeners;

    /**
     * Delay in milisecond before each progress cells display.
     * 1000 ms === sleep(1)
     * <strong>usleep()</strong> function does not run on Windows platform.
     *
     * @var        integer
     * @since      1.1
     * @access     private
     * @see        setAnimSpeed()
     */
    var $_anim_speed;

    /**
     * Callback, either function name or array(&$object, 'method')
     *
     * @var        mixed
     * @since      1.2.0RC3
     * @access     private
     * @see        setProgressHandler()
     */
    var $_callback = null;


    /**
     * Constructor Summary
     *
     * o Creates a natural horizontal progress bar that displays ten cells/units
     *   with no border and no progress string.
     *   The initial and minimum values are 0, and the maximum is 100.
     *   <code>
     *   $bar = new HTML_Progress();
     *   </code>
     *
     * o Creates a natural progress bar with the specified orientation, which can be
     *   either HTML_PROGRESS_BAR_HORIZONTAL or HTML_PROGRESS_BAR_VERTICAL
     *   By default, no border and no progress string are painted.
     *   The initial and minimum values are 0, and the maximum is 100.
     *   <code>
     *   $bar = new HTML_Progress($orient);
     *   </code>
     *
     * o Creates a natural horizontal progress bar with the specified minimum and
     *   maximum. Sets the initial value of the progress bar to the specified
     *   minimum, and the maximum that the progress bar can reach.
     *   By default, no border and no progress string are painted.
     *   <code>
     *   $bar = new HTML_Progress($min, $max);
     *   </code>
     *
     * o Creates a natural horizontal progress bar with the specified orientation,
     *   minimum and maximum. Sets the initial value of the progress bar to the
     *   specified minimum, and the maximum that the progress bar can reach.
     *   By default, no border and no progress string are painted.
     *   <code>
     *   $bar = new HTML_Progress($orient, $min, $max);
     *   </code>
     *
     * o Creates a natural horizontal progress that uses the specified model
     *   to hold the progress bar's data.
     *   By default, no border and no progress string are painted.
     *   <code>
     *   $bar = new HTML_Progress($model);
     *   </code>
     *
     *
     * @param      object    $model         (optional) Model that hold the progress bar's data
     * @param      int       $orient        (optional) Orientation of progress bar
     * @param      int       $min           (optional) Minimum value of progress bar
     * @param      int       $max           (optional) Maximum value of progress bar
     * @param      array     $errorPrefs    (optional) Always last argument of class constructor.
     *                                       hash of params to configure PEAR_ErrorStack and loggers
     *
     * @since      1.0
     * @access     public
     * @throws     HTML_PROGRESS_ERROR_INVALID_INPUT
     * @see        setIndeterminate(),
     *             setBorderPainted(), setStringPainted(), setString(),
     *             setDM(), setUI(), setIdent()
     */
    function HTML_Progress()
    {
        $args = func_get_args();
        $num_args = func_num_args();

        if ($num_args > 0) {
            $errorPrefs = func_get_arg($num_args - 1);
            if (!is_array($errorPrefs)) {
                $errorPrefs = array();
            } else {
                $num_args--;
            }
            HTML_Progress::_initErrorHandler($errorPrefs);
        } else {
            HTML_Progress::_initErrorhandler();
        }

        $this->_listeners = array();          // none listeners by default

        $this->_DM = new HTML_Progress_DM();  // new instance of a progress DataModel
        $this->_UI = new HTML_Progress_UI();  // new instance of a progress UserInterface

        switch ($num_args) {
         case 1:
            if (is_object($args[0]) && (is_a($args[0], 'html_progress_dm'))) {
                /*   object html_progress_dm extends   */
                $this->_DM = &$args[0];

            } elseif (is_int($args[0])) {
                /*   int orient   */
                $this->_UI->setOrientation($args[0]);

            } else {
                return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                    array('var' => '$model | $orient',
                          'was' => (gettype($args[0]) == 'object') ?
                                    get_class($args[0]).' object' : gettype($args[0]),
                          'expected' => 'html_progress_dm object | integer',
                          'paramnum' => 1));
            }
            break;
         case 2:
            /*   int min, int max   */
            if (!is_int($args[0])) {
                return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                    array('var' => '$min',
                          'was' => $args[0],
                          'expected' => 'integer',
                          'paramnum' => 1));

            } elseif (!is_int($args[1])) {
                return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                    array('var' => '$max',
                          'was' => $args[1],
                          'expected' => 'integer',
                          'paramnum' => 2));
            } else {
                $this->_DM->setMinimum($args[0]);
                $this->_DM->setMaximum($args[1]);
            }
            break;
         case 3:
            /*   int orient, int min, int max   */
            if (!is_int($args[0])) {
                return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                    array('var' => '$orient',
                          'was' => $args[0],
                          'expected' => 'integer',
                          'paramnum' => 1));

            } elseif (!is_int($args[1])) {
                return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                    array('var' => '$min',
                          'was' => $args[1],
                          'expected' => 'integer',
                          'paramnum' => 2));

            } elseif (!is_int($args[2])) {
                return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                    array('var' => '$max',
                          'was' => $args[2],
                          'expected' => 'integer',
                          'paramnum' => 3));
            } else {
                $this->_UI->setOrientation($args[0]);
                $this->_DM->setMinimum($args[1]);
                $this->_DM->setMaximum($args[2]);
            }
            break;
         default:
        }
        $this->setString(null);
        $this->setStringPainted(false);
        $this->setBorderPainted(false);
        $this->setIndeterminate(false);
        $this->setIdent();
        $this->setAnimSpeed(0);

        // to fix a potential php config problem with PHP 4.2.0 : turn 'implicit_flush' ON
        ob_implicit_flush(1);
    }

    /**
     * Returns the current API version
     *
     * @return     float
     * @since      0.1
     * @access     public
     */
    function apiVersion()
    {
        return 1.2;
    }

    /**
     * Returns mode of the progress bar (determinate or not).
     *
     * @return     boolean
     * @since      1.0
     * @access     public
     * @see        setIndeterminate()
     */
    function isIndeterminate()
    {
        return $this->_indeterminate;
    }

    /**
     * Sets the $_indeterminate property of the progress bar, which determines
     * whether the progress bar is in determinate or indeterminate mode.
     * An indeterminate progress bar continuously displays animation indicating
     * that an operation of unknown length is occuring.
     * By default, this property is false.
     *
     * @param      boolean   $continuous    whether countinuously displays animation
     *
     * @return     void
     * @since      1.0
     * @access     public
     * @throws     HTML_PROGRESS_ERROR_INVALID_INPUT
     * @see        isIndeterminate()
     */
    function setIndeterminate($continuous)
    {
        if (!is_bool($continuous)) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$continuous',
                      'was' => gettype($continuous),
                      'expected' => 'boolean',
                      'paramnum' => 1));
        }
        $this->_indeterminate = $continuous;
    }

    /**
     * Determines whether the progress bar border is painted or not.
     * The default is false.
     *
     * @return     boolean
     * @since      1.0
     * @access     public
     * @see        setBorderPainted()
     */
    function isBorderPainted()
    {
        return $this->_paintBorder;
    }

    /**
     * Sets the value of $_paintBorder property, which determines whether the
     * progress bar should paint its border. The default is false.
     *
     * @param      boolean   $paint         whether the progress bar should paint its border
     *
     * @return     void
     * @since      1.0
     * @access     public
     * @throws     HTML_PROGRESS_ERROR_INVALID_INPUT
     * @see        isBorderPainted()
     */
    function setBorderPainted($paint)
    {
        if (!is_bool($paint)) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$paint',
                      'was' => gettype($paint),
                      'expected' => 'boolean',
                      'paramnum' => 1));
        }

        $this->_paintBorder = $paint;
    }

    /**
     * Determines whether the progress bar string is painted or not.
     * The default is false.
     * The progress bar displays the value returned by getPercentComplete() method
     * formatted as a percent such as 33%.
     *
     * @return     boolean
     * @since      1.0
     * @access     public
     * @see        setStringPainted(), setString()
     */
    function isStringPainted()
    {
        return $this->_paintString;
    }

    /**
     * Sets the value of $_paintString property, which determines whether the
     * progress bar should render a progress string. The default is false.
     *
     * @param      boolean   $paint         whether the progress bar should render a string
     *
     * @return     void
     * @since      1.0
     * @access     public
     * @throws     HTML_PROGRESS_ERROR_INVALID_INPUT
     * @see        isStringPainted(), setString()
     */
    function setStringPainted($paint)
    {
        if (!is_bool($paint)) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$paint',
                      'was' => gettype($paint),
                      'expected' => 'boolean',
                      'paramnum' => 1));
        }
        $this->_paintString = $paint;
    }

    /**
     * Returns the current value of the progress string.
     * By default, the progress bar displays the value returned by
     * getPercentComplete() method formatted as a percent such as 33%.
     *
     * @return     string
     * @since      1.0
     * @access     public
     * @see        setString(), isStringPainted()
     */
    function getString()
    {
        if ($this->isStringPainted() && !is_null($this->_progressString)) {
            return $this->_progressString;
        } else {
            return sprintf("%s", $this->getPercentComplete(false)).' %';
        }
    }

    /**
     * Sets the current value of the progress string. By default, this string
     * is null. If you have provided a custom progress string and want to revert
     * to the built-in-behavior, set the string back to null.
     * The progress string is painted only if the isStringPainted() method
     * returns true.
     *
     * @param      string    $str           progress string
     *
     * @return     void
     * @since      1.0
     * @access     public
     * @see        getString(), isStringPainted(), setStringPainted()
     */
    function setString($str)
    {
        $this->_progressString = $str;
    }

    /**
     * Returns the data model used by this progress bar.
     *
     * @return     object
     * @since      1.0
     * @access     public
     * @see        setDM()
     */
    function &getDM()
    {
        return $this->_DM;
    }

    /**
     * Sets the data model used by this progress bar.
     *
     * @param      string    $model         class name of a html_progress_dm extends object
     *
     * @return     void
     * @since      1.0
     * @access     public
     * @throws     HTML_PROGRESS_ERROR_INVALID_INPUT
     * @see        getDM()
     */
    function setDM($model)
    {
        if (!class_exists($model)) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'error',
                array('var' => '$model',
                      'was' => 'class does not exists',
                      'expected' => $model.' class defined',
                      'paramnum' => 1));
        }

        $_dm = new $model();

        if (!is_a($_dm, 'html_progress_dm')) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'error',
                array('var' => '$model',
                      'was' => $model,
                      'expected' => 'HTML_Progress_DM extends',
                      'paramnum' => 1));
        }
        $this->_DM =& $_dm;
    }

    /**
     * Returns the progress bar's minimum value stored in the progress bar's data model.
     * The default value is 0.
     *
     * @return     integer
     * @since      1.0
     * @access     public
     * @see        setMinimum(),
     *             HTML_Progress_DM::getMinimum()
     */
    function getMinimum()
    {
        return $this->_DM->getMinimum();
    }

    /**
     * Sets the progress bar's minimum value stored in the progress bar's data model.
     * If the minimum value is different from previous minimum, all change listeners
     * are notified.
     *
     * @param      integer   $min           progress bar's minimal value
     *
     * @return     void
     * @since      1.0
     * @access     public
     * @see        getMinimum(),
     *             HTML_Progress_DM::setMinimum()
     */
    function setMinimum($min)
    {
        $oldVal = $this->getMinimum();

        $this->_DM->setMinimum($min);

        if ($oldVal != $min) {
            $this->_announce(array('log' => 'setMinimum', 'value' => $min));
        }
    }

    /**
     * Returns the progress bar's maximum value stored in the progress bar's data model.
     * The default value is 100.
     *
     * @return     integer
     * @since      1.0
     * @access     public
     * @see        setMaximum(),
     *             HTML_Progress_DM::getMaximum()
     */
    function getMaximum()
    {
        return $this->_DM->getMaximum();
    }

    /**
     * Sets the progress bar's maximum value stored in the progress bar's data model.
     * If the maximum value is different from previous maximum, all change listeners
     * are notified.
     *
     * @param      integer   $max           progress bar's maximal value
     *
     * @return     void
     * @since      1.0
     * @access     public
     * @see        getMaximum(),
     *             HTML_Progress_DM::setMaximum()
     */
    function setMaximum($max)
    {
        $oldVal = $this->getMaximum();

        $this->_DM->setMaximum($max);

        if ($oldVal != $max) {
            $this->_announce(array('log' => 'setMaximum', 'value' => $max));
        }
    }

    /**
     * Returns the progress bar's increment value stored in the progress bar's data model.
     * The default value is +1.
     *
     * @return     integer
     * @since      1.0
     * @access     public
     * @see        setIncrement(),
     *             HTML_Progress_DM::getIncrement()
     */
    function getIncrement()
    {
        return $this->_DM->getIncrement();
    }

    /**
     * Sets the progress bar's increment value stored in the progress bar's data model.
     *
     * @param      integer   $inc           progress bar's increment value
     *
     * @return     void
     * @since      1.0
     * @access     public
     * @see        getIncrement(),
     *             HTML_Progress_DM::setIncrement()
     */
    function setIncrement($inc)
    {
        $this->_DM->setIncrement($inc);
    }

    /**
     * Returns the progress bar's current value, which is stored in the
     * progress bar's data model. The value is always between the minimum
     * and maximum values, inclusive.
     * By default, the value is initialized to be equal to the minimum value.
     *
     * @return     integer
     * @since      1.0
     * @access     public
     * @see        setValue(), incValue(),
     *             HTML_Progress_DM::getValue()
     */
    function getValue()
    {
        return $this->_DM->getValue();
    }

    /**
     * Sets the progress bar's current value stored in the progress bar's data model.
     * If the new value is different from previous value, all change listeners
     * are notified.
     *
     * @param      integer   $val           progress bar's current value
     *
     * @return     void
     * @since      1.0
     * @access     public
     * @see        getValue(), incValue(),
     *             HTML_Progress_DM::setValue()
     */
    function setValue($val)
    {
        $oldVal = $this->getValue();

        $this->_DM->setValue($val);

        if ($oldVal != $val) {
            $this->_announce(array('log' => 'setValue', 'value' => $val));
        }
    }

    /**
     * Updates the progress bar's current value by adding increment value.
     * All change listeners are notified.
     *
     * @return     void
     * @since      1.0
     * @access     public
     * @see        getValue(), setValue(),
     *             HTML_Progress_DM::incValue()
     */
    function incValue()
    {
        $this->_DM->incValue();
        $this->_announce(array('log' => 'incValue', 'value' => $this->_DM->getValue() ));
    }

    /**
     * Returns the percent complete for the progress bar. Note that this number is
     * between 0.00 and 1.00 or 0 and 100.
     *
     * @param      boolean   $float         (optional) float or integer format
     *
     * @return     mixed
     * @since      1.0
     * @access     public
     * @see        getValue(), getMaximum(),
     *             HTML_Progress_DM::getPercentComplete()
     */
    function getPercentComplete($float = true)
    {
        return $this->_DM->getPercentComplete($float);
    }

    /**
     * Returns the look-and-feel object that renders the progress bar.
     *
     * @return     object
     * @since      1.0
     * @access     public
     * @see        setUI()
     */
    function &getUI()
    {
        return $this->_UI;
    }

    /**
     * Sets the look-and-feel object that renders the progress bar.
     *
     * @param      string    $ui            class name of a html_progress_ui extends object
     *
     * @return     void
     * @since      1.0
     * @access     public
     * @throws     HTML_PROGRESS_ERROR_INVALID_INPUT
     * @see        getUI()
     */
    function setUI($ui)
    {
        if (!class_exists($ui)) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'error',
                array('var' => '$ui',
                      'was' => 'class does not exists',
                      'expected' => $ui.' class defined',
                      'paramnum' => 1));
        }

        $_ui = new $ui();

        if (!is_a($_ui, 'html_progress_ui')) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'error',
                array('var' => '$ui',
                      'was' => $ui,
                      'expected' => 'HTML_Progress_UI extends',
                      'paramnum' => 1));
        }
        $this->_UI =& $_ui;
    }

    /**
     * Sets the look-and-feel model that renders the progress bar.
     *
     * @param      string    $file          file name of model properties
     * @param      string    $type          type of external ressource (phpArray, iniFile, XML ...)
     *
     * @return     void
     * @since      1.0
     * @access     public
     * @throws     HTML_PROGRESS_ERROR_INVALID_INPUT
     * @see        setUI()
     */
    function setModel($file, $type)
    {
        if (!file_exists($file)) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'error',
                array('var' => '$file',
                      'was' => $file,
                      'expected' => 'file exists',
                      'paramnum' => 1));
        }

        include_once 'Config.php';

        $conf = new Config();

        if (!$conf->isConfigTypeRegistered($type)) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'error',
                array('var' => '$type',
                      'was' => $type,
                      'expected' => implode (" | ", array_keys($GLOBALS['CONFIG_TYPES'])),
                      'paramnum' => 2));
        }

        $data = $conf->parseConfig($file, $type);

        $structure = $data->toArray(false);
        $progress =& $structure['root'];

        $ui = new HTML_Progress_UI();

        if (isset($progress['core']['speed'])) {
            $this->setAnimSpeed(intval($progress['core']['speed']));
        }

        if (isset($progress['core']['indeterminate'])) {
            $mode = (strtolower(trim($progress['core']['indeterminate'])) == 'true');
            $this->setIndeterminate($mode);
        }

        if (isset($progress['core']['increment'])) {
            $this->setIncrement(intval($progress['core']['increment']));
        }

        if (isset($progress['core']['javascript']) && file_exists($progress['core']['javascript'])) {
            $ui->setScript($progress['core']['javascript']);
        }

        if (isset($progress['orientation']['shape'])) {
            $ui->setOrientation(intval($progress['orientation']['shape']));
        }

        if (isset($progress['orientation']['fillway'])) {
            $ui->setFillWay($progress['orientation']['fillway']);
        }

        if (isset($progress['cell']['count'])) {
            $ui->setCellCount(intval($progress['cell']['count']));
        }

        if (isset($progress['cell']['font-family'])) {
            if (is_array($progress['cell']['font-family'])) {
                $progress['cell']['font-family'] = implode(",", $progress['cell']['font-family']);
            }
        }
        if (isset($progress['cell'])) {
            $ui->setCellAttributes($progress['cell']);
        }

        if (isset($progress['border'])) {
            $this->setBorderPainted(true);
            $ui->setBorderAttributes($progress['border']);
        }

        if (isset($progress['string']['font-family'])) {
            if (is_array($progress['string']['font-family'])) {
                $progress['string']['font-family'] = implode(",", $progress['string']['font-family']);
            }
        }
        if (isset($progress['string'])) {
            $this->setStringPainted(true);
            $ui->setStringAttributes($progress['string']);
        }

        if (isset($progress['progress'])) {
            $ui->setProgressAttributes($progress['progress']);
        }

        $this->_UI = $ui;
    }

    /**
     * Returns delay execution of the progress bar
     *
     * @return     integer
     * @since      1.2.0RC1
     * @access     public
     * @see        setAnimSpeed()
     */
    function getAnimSpeed()
    {
        return $this->_anim_speed;
    }

    /**
     * Set the delays progress bar execution for the given number of miliseconds.
     *
     * @param      integer   $delay         Delay in milisecond.
     *
     * @return     void
     * @since      1.1
     * @access     public
     * @throws     HTML_PROGRESS_ERROR_INVALID_INPUT
     * @see        getAnimSpeed()
     */
    function setAnimSpeed($delay)
    {
        if (!is_int($delay)) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$delay',
                      'was' => gettype($delay),
                      'expected' => 'integer',
                      'paramnum' => 1));

        } elseif ($delay < 0) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'error',
                array('var' => '$delay',
                      'was' => $delay,
                      'expected' => 'greater than zero',
                      'paramnum' => 1));

        } elseif ($delay > 1000) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'error',
                array('var' => '$delay',
                      'was' => $delay,
                      'expected' => 'less or equal 1000',
                      'paramnum' => 1));
        }
        $this->_anim_speed = $delay;
    }

    /**
     * Get the cascading style sheet to put inline on HTML document
     *
     * @return     string
     * @since      1.0
     * @access     public
     * @see        HTML_Progress_UI::getStyle()
     */
    function getStyle()
    {
        $ui = $this->getUI();
        $lnEnd = $ui->_getLineEnd();

        $style = $lnEnd . $ui->getStyle();
        $style = str_replace('{%pIdent%}', '.'.$this->getIdent(), $style);

        if (!$this->isBorderPainted()) {
            $style = ereg_replace('border-width: [0-9]+px;', 'border-width: 0;', $style);
        }
        return $style;
    }

    /**
     * Get the javascript code to manage progress bar.
     *
     * @return     string                   JavaScript URL or inline code to manage progress bar
     * @since      1.0
     * @access     public
     * @see        HTML_Progress_UI::getScript()
     */
    function getScript()
    {
        $ui = $this->getUI();
        $js = $ui->getScript();
        return $js;
    }

    /**
     * Returns the progress bar structure in an array.
     *
     * @return     array of progress bar properties
     * @since      1.0
     * @access     public
     */
    function toArray()
    {
        $ui =& $this->getUI();
        $dm =& $this->getDM();

        $_structure = array();
        $_structure['id'] = $this->getIdent();
        $_structure['indeterminate'] = $this->isIndeterminate();
        $_structure['borderpainted'] = $this->isBorderPainted();
        $_structure['stringpainted'] = $this->isStringPainted();
        $_structure['string'] = $this->_progressString;
        $_structure['animspeed'] = $this->getAnimSpeed();
        $_structure['ui']['classID'] = get_class($ui);
        $_structure['ui']['orientation'] = $ui->getOrientation();
        $_structure['ui']['fillway'] = $ui->getFillWay();
        $_structure['ui']['cell'] = $ui->getCellAttributes();
        $_structure['ui']['cell']['count'] = $ui->getCellCount();
        $_structure['ui']['border'] = $ui->getBorderAttributes();
        $_structure['ui']['string'] = $ui->getStringAttributes();
        $_structure['ui']['progress'] = $ui->getProgressAttributes();
        $_structure['ui']['script'] = $ui->getScript();
        $_structure['dm']['classID'] = get_class($dm);
        $_structure['dm']['minimum'] = $dm->getMinimum();
        $_structure['dm']['maximum'] = $dm->getMaximum();
        $_structure['dm']['increment'] = $dm->getIncrement();
        $_structure['dm']['value'] = $dm->getValue();
        $_structure['dm']['percent'] = $dm->getPercentComplete(false);

        return $_structure;
    }

    /**
     * Returns the progress structure as HTML.
     *
     * @return     string                   HTML Progress bar
     * @since      0.2
     * @access     public
     */
    function toHtml()
    {
        $strHtml = '';
        $ui =& $this->_UI;
        $tabs = $ui->_getTabs();
        $tab = $ui->_getTab();
        $lnEnd = $ui->_getLineEnd();
        $comment = $ui->getComment();
        $orient = $ui->getOrientation();
        $progressAttr = $ui->getProgressAttributes();
        $borderAttr = $ui->getBorderAttributes();
        $stringAttr = $ui->getStringAttributes();
        $valign = strtolower($stringAttr['valign']);

        /**
         *  Adds a progress bar legend in html code is possible.
         *  See HTML_Common::setComment() method.
         */
        if (strlen($comment) > 0) {
            $strHtml .= $tabs . "<!-- $comment -->" . $lnEnd;
        }

        $strHtml .= $tabs . "<div id=\"".$this->getIdent()."_progress\" class=\"".$this->getIdent()."\">" . $lnEnd;
        $strHtml .= $tabs . "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">" . $lnEnd;
        $progressId = $this->getIdent().'_';

        /**
         *  Creates all cells of progress bar in order
         *  depending of the FillWay and Orientation.
         */
        if ($orient == HTML_PROGRESS_BAR_HORIZONTAL) {
            $progressHtml = $this->_getProgressHbar_toHtml();
        }

        if ($orient == HTML_PROGRESS_BAR_VERTICAL) {
            $progressHtml = $this->_getProgressVbar_toHtml();
        }

        if ($orient == HTML_PROGRESS_POLYGONAL) {
            $progressHtml = $this->_getProgressPolygonal_toHtml();
        }

        if ($orient == HTML_PROGRESS_CIRCLE) {
            $cellAttr = $ui->getCellAttributes();
            if (!isset($cellAttr[0]['background-image']) || !file_exists($cellAttr[0]['background-image'])) {
                // creates default circle segments pictures :
                // 'c0.png'->0% 'c1.png'->10%, 'c2.png'->20%, ... 'c10.png'->100%
                $ui->drawCircleSegments();
            }
            $progressHtml = $this->_getProgressCircle_toHtml();
        }

        /**
         *  Progress Bar (2) alignment rules:
         *  - percent / messsage area (1)
         *
         *  +---------------------------------------+
         *  |         +t1---+                       |
         *  |         | (1) |                       |
         *  |         +-----+                       |
         *  | +t2---+ +-------------------+ +t3---+ |
         *  | | (1) | | | | | (2) | | | | | | (1) | |
         *  | +-----+ +-------------------+ +-----+ |
         *  |         +t4---+                       |
         *  |         | (1) |                       |
         *  |         +-----+                       |
         *  +---------------------------------------+
         */
        if (($valign == 'left') || ($valign == 'right')) {
            $tRows = 1;
            $tCols = 2;
            $ps = ($valign == 'left') ? 0 : 1;
        } else {
            $tRows = 2;
            $tCols = 1;
            $ps = ($valign == 'top')  ? 0 : 1;
        }

        for ($r = 0 ; $r < $tRows ; $r++) {
            $strHtml .= $tabs . "<tr>" . $lnEnd;
            for ($c = 0 ; $c < $tCols ; $c++) {
                if (($c == $ps) || ($r == $ps)) {
                    $id = $stringAttr['id'];
                    $strHtml .= $tabs . $tab . "<td class=\"$id\" id=\"$progressId$id\">" . $lnEnd;
                    $strHtml .= $tabs . $tab . $tab . $this->getString() . $lnEnd;
                    $ps = -1;
                } else {
                    $class = $progressAttr['class'];
                    $strHtml .= $tabs . $tab ."<td class=\"$class\">" . $lnEnd;
                    $strHtml .= $tabs . $tab . $tab . "<div class=\"".$borderAttr['class']."\">" . $lnEnd;
                    $strHtml .= $progressHtml;
                    $strHtml .= $tabs . $tab . $tab . "</div>" . $lnEnd;
                }
                $strHtml .= $tabs . $tab ."</td>" . $lnEnd;
            }
            $strHtml .= $tabs . "</tr>" . $lnEnd;
        }

        $strHtml .= $tabs . "</table>" . $lnEnd;
        $strHtml .= $tabs . "</div>" . $lnEnd;

        return $strHtml;
    }

    /**
     * Renders the new value of progress bar.
     *
     * @return     void
     * @since      0.2
     * @access     public
     */
    function display()
    {
        static $lnEnd;
        static $cellAmount;
        static $determinate;

        if(!isset($lnEnd)) {
            $ui =& $this->_UI;
            $lnEnd = $ui->_getLineEnd();
            $cellAmount = ($this->getMaximum() - $this->getMinimum()) / $ui->getCellCount();
        }

        if (function_exists('ob_get_clean')) {
            $bar  = ob_get_clean();      // use for PHP 4.3+
        } else {
            $bar  = ob_get_contents();   // use for PHP 4.2+
            ob_end_clean();
        }
        $bar .= $lnEnd;

        $progressId = $this->getIdent().'_';

        if ($this->isIndeterminate()) {
            if (isset($determinate)) {
                $determinate++;
                $progress = $determinate;
            } else {
                $progress = $determinate = 1;
            }
        } else {
            $progress = ($this->getValue() - $this->getMinimum()) / $cellAmount;
            $determinate = 0;
    }
        $bar .= '<script type="text/javascript">self.setprogress("'.$progressId.'",'.((int) $progress).',"'.$this->getString().'",'.$determinate.'); </script>';

        echo $bar;
        ob_start();
    }

    /**
     * Hides the progress bar.
     *
     * @return     void
     * @since      1.2.0RC3
     * @access     public
     */
    function hide()
    {
        $ui = $this->getUI();
        $lnEnd = $ui->_getLineEnd();
        $progressId = $this->getIdent().'_';

        if (function_exists('ob_get_clean')) {
            $bar  = ob_get_clean();      // use for PHP 4.3+
        } else {
            $bar  = ob_get_contents();   // use for PHP 4.2+
            ob_end_clean();
        }
        $bar .= $lnEnd;
        $bar .= '<script type="text/javascript">self.hideProgress("'.$progressId.'"); </script>';
        echo $bar;
    }

    /**
     * Default user callback when none are defined.
     * Delay execution of progress meter for the given number of milliseconds.
     *
     * NOTE: The function {@link http://www.php.net/manual/en/function.usleep.php}
     *       did not work on Windows systems until PHP 5.0.0
     *
     * @return     void
     * @since      1.2.0RC3
     * @access     public
     * @see        getAnimSpeed(), setAnimSpeed(), process()
     */
    function sleep()
    {
        // convert delay from milliseconds to microseconds
        $usecs = $this->getAnimSpeed()*1000;

        if ((substr(PHP_OS, 0, 3) == 'WIN') && (substr(PHP_VERSION,0,1) < '5') ){
            for ($i=0; $i<$usecs; $i++) { }
        } else {
            usleep($usecs);
    }
    }

    /**
     * Sets the user callback function that execute all actions pending progress
     *
     * @param      mixed     $handler       Name of function or a class-method.
     *
     * @return     void
     * @since      1.2.0RC3
     * @access     public
     * @throws     HTML_PROGRESS_ERROR_INVALID_CALLBACK
     * @see        process()
     */
    function setProgressHandler($handler)
    {
        if (!is_callable($handler)) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_CALLBACK, 'warning',
                array('var' => '$handler',
                      'element' => 'valid Class-Method/Function',
                      'was' => 'element',
                      'paramnum' => 1));
        }
        $this->_callback = $handler;
    }

    /**
     * Performs the progress actions
     *
     * @return     void
     * @since      1.2.0RC3
     * @access     public
     * @see        sleep()
     */
    function process()
    {
        if (!$this->_callbackExists($this->_callback)) {
            // when there is no valid user callback then default is to sleep a bit ...
            $this->sleep();
        } else {
            call_user_func_array($this->_callback, array($this->getValue(), &$this));
        }
    }

    /**
     * Runs the progress bar (both modes: indeterminate and determinate),
     * and execute all actions defined in user callback identified by
     * method setProgressHandler.
     *
     * @return     void
     * @since      1.2.0RC3
     * @access     public
     * @see        process(), setProgressHandler()
     */
    function run()
    {
        do {
            $this->display();
            $this->process();
            if ($this->getPercentComplete() == 1) {
                if ($this->isIndeterminate()) {
                    $this->setValue(0);
                } else {
                    return;
                }
            }
            $this->incValue();
        } while(1);
    }

    /**
     * Returns the current identification string.
     *
     * @return     string                   current Progress instance's identification string
     * @since      1.0
     * @access     public
     * @see        setIdent()
     */
    function getIdent()
    {
        return $this->_ident;
    }

    /**
     * Sets this Progress instance's identification string.
     *
     * @param      mixed     $ident         (optional) the new identification string.
     *
     * @since      1.0
     * @access     public
     * @see        getIdent()
     */
    function setIdent($ident = null)
    {
        if (is_null($ident)) {
            $this->_ident = 'p_' . substr(md5(microtime()), 0, 6);
        } else {
            $this->_ident = $ident;
    }
    }

    /**
     * Returns an array of all the listeners added to this progress bar.
     *
     * @return     array
     * @since      1.0
     * @access     public
     * @see        addListener(), removeListener()
     */
    function getListeners()
    {
        return $this->_listeners;
    }

    /**
     * Adds a HTML_Progress_Observer instance to the list of observers
     * that are listening for messages emitted by this HTML_Progress instance.
     *
     * @param      object    $observer      The HTML_Progress_Observer instance
     *                                      to attach as a listener.
     *
     * @return     boolean                  True if the observer is successfully attached.
     * @since      1.0
     * @access     public
     * @see        getListeners(), removeListener()
     */
    function addListener($observer)
    {
        if (!is_a($observer, 'HTML_Progress_Observer') &&
            !is_a($observer, 'HTML_Progress_Monitor')) {
            return false;
        }
        $this->_listeners[$observer->_id] = &$observer;
        return true;
    }

    /**
     * Removes a HTML_Progress_Observer instance from the list of observers.
     *
     * @param      object    $observer      The HTML_Progress_Observer instance
     *                                      to detach from the list of listeners.
     *
     * @return     boolean                  True if the observer is successfully detached.
     * @since      1.0
     * @access     public
     * @see        getListeners(), addListener()
     */
    function removeListener($observer)
    {
        if ((!is_a($observer, 'HTML_Progress_Observer') &&
             !is_a($observer, 'HTML_Progress_Monitor')
             ) ||
            (!isset($this->_listeners[$observer->_id]))  ) {

            return false;
        }
        unset($this->_listeners[$observer->_id]);
        return true;
    }

    /**
     * Notifies all listeners that have registered interest in $event message.
     *
     * @param      mixed     $event         A hash describing the progress event.
     *
     * @since      1.0
     * @access     private
     * @see        setMinimum(), setMaximum(), setValue(), incValue()
     */
    function _announce($event)
    {
        foreach ($this->_listeners as $id => $listener) {
            $this->_listeners[$id]->notify($event);
        }
    }

    /**
     * Returns a horizontal progress bar structure as HTML.
     *
     * @return     string                   Horizontal HTML Progress bar
     * @since      1.0
     * @access     private
     */
    function _getProgressHbar_toHtml()
    {
        $ui =& $this->_UI;
        $tabs = $ui->_getTabs();
        $tab = $ui->_getTab();
        $lnEnd = $ui->_getLineEnd();
        $way_natural = ($ui->getFillWay() == 'natural');
        $cellAttr = $ui->getCellAttributes();
        $cellCount = $ui->getCellCount();

        $progressId = $this->getIdent().'_';
        $progressHtml = "";

        if ($way_natural) {
            // inactive cells first
            $pos = $cellAttr['spacing'];
            for ($i=0; $i<$cellCount; $i++) {
                $progressHtml .= $tabs . $tab . $tab;
                $progressHtml .= "<div id=\"". $progressId . sprintf($cellAttr['id'],$i) ."I\"";
                $progressHtml .= " class=\"".$cellAttr['class']."I\"";
                $progressHtml .= " style=\"position:absolute;top:".$cellAttr['spacing']."px;left:".$pos."px;\"";
                $progressHtml .= ">&nbsp;</div>" . $lnEnd;
                $pos += ($cellAttr['width'] + $cellAttr['spacing']);
            }
            // then active cells
            $pos = $cellAttr['spacing'];
            for ($i=0; $i<$cellCount; $i++) {
                $progressHtml .= $tabs . $tab . $tab;
                $progressHtml .= "<div id=\"". $progressId . sprintf($cellAttr['id'],$i) ."A\"";
                $progressHtml .= " class=\"".$cellAttr['class']."A\"";
                $progressHtml .= " style=\"position:absolute;top:".$cellAttr['spacing']."px;left:".$pos."px;";
                if (isset($cellAttr[$i])) {
                    $progressHtml .= "color:".$cellAttr[$i]['color'].";\"";
                } else {
                    $progressHtml .= "\"";
                }
                $progressHtml .= ">&nbsp;</div>" . $lnEnd;
                $pos += ($cellAttr['width'] + $cellAttr['spacing']);
            }
        } else {
            // inactive cells first
            $pos = $cellAttr['spacing'];
            for ($i=$cellCount-1; $i>=0; $i--) {
                $progressHtml .= $tabs . $tab . $tab;
                $progressHtml .= "<div id=\"". $progressId . sprintf($cellAttr['id'],$i) ."I\"";
                $progressHtml .= " class=\"".$cellAttr['class']."I\"";
                $progressHtml .= " style=\"position:absolute;top:".$cellAttr['spacing']."px;left:".$pos."px;\"";
                $progressHtml .= ">&nbsp;</div>" . $lnEnd;
                $pos += ($cellAttr['width'] + $cellAttr['spacing']);
            }
            // then active cells
            $pos = $cellAttr['spacing'];
            for ($i=$cellCount-1; $i>=0; $i--) {
                $progressHtml .= $tabs . $tab . $tab;
                $progressHtml .= "<div id=\"". $progressId . sprintf($cellAttr['id'],$i) ."A\"";
                $progressHtml .= " class=\"".$cellAttr['class']."A\"";
                $progressHtml .= " style=\"position:absolute;top:".$cellAttr['spacing']."px;left:".$pos."px;";
                if (isset($cellAttr[$i])) {
                    $progressHtml .= "color:".$cellAttr[$i]['color'].";\"";
                } else {
                    $progressHtml .= "\"";
                }
                $progressHtml .= ">&nbsp;</div>" . $lnEnd;
                $pos += ($cellAttr['width'] + $cellAttr['spacing']);
            }
        }
        return $progressHtml;
    }

    /**
     * Returns a vertical progress bar structure as HTML.
     *
     * @return     string                   Vertical HTML Progress bar
     * @since      1.0
     * @access     private
     */
    function _getProgressVbar_toHtml()
    {
        $ui =& $this->_UI;
        $tabs = $ui->_getTabs();
        $tab = $ui->_getTab();
        $lnEnd = $ui->_getLineEnd();
        $way_natural = ($ui->getFillWay() == 'natural');
        $cellAttr = $ui->getCellAttributes();
        $cellCount = $ui->getCellCount();

        $progressId = $this->getIdent().'_';
        $progressHtml = "";

        if ($way_natural) {
            // inactive cells first
            $pos = $cellAttr['spacing'];
            for ($i=$cellCount-1; $i>=0; $i--) {
                $progressHtml .= $tabs . $tab . $tab;
                $progressHtml .= "<div id=\"". $progressId . sprintf($cellAttr['id'],$i) ."I\"";
                $progressHtml .= " class=\"".$cellAttr['class']."I\"";
                $progressHtml .= " style=\"position:absolute;left:".$cellAttr['spacing']."px;top:".$pos."px;\"";
                $progressHtml .= ">&nbsp;</div>" . $lnEnd;
                $pos += ($cellAttr['height'] + $cellAttr['spacing']);
            }
            // then active cells
            $pos = $cellAttr['spacing'];
            for ($i=$cellCount-1; $i>=0; $i--) {
                $progressHtml .= $tabs . $tab . $tab;
                $progressHtml .= "<div id=\"". $progressId . sprintf($cellAttr['id'],$i) ."A\"";
                $progressHtml .= " class=\"".$cellAttr['class']."A\"";
                $progressHtml .= " style=\"position:absolute;left:".$cellAttr['spacing']."px;top:".$pos."px;";
                if (isset($cellAttr[$i])) {
                    $progressHtml .= "color:".$cellAttr[$i]['color'].";\"";
                } else {
                    $progressHtml .= "\"";
                }
                $progressHtml .= ">&nbsp;</div>" . $lnEnd;
                $pos += ($cellAttr['height'] + $cellAttr['spacing']);
            }
        } else {
            // inactive cells first
            $pos = $cellAttr['spacing'];
            for ($i=0; $i<$cellCount; $i++) {
                $progressHtml .= $tabs . $tab . $tab;
                $progressHtml .= "<div id=\"". $progressId . sprintf($cellAttr['id'],$i) ."I\"";
                $progressHtml .= " class=\"".$cellAttr['class']."I\"";
                $progressHtml .= " style=\"position:absolute;left:".$cellAttr['spacing']."px;top:".$pos."px;\"";
                $progressHtml .= ">&nbsp;</div>" . $lnEnd;
                $pos += ($cellAttr['height'] + $cellAttr['spacing']);
            }
            // then active cells
            $pos = $cellAttr['spacing'];
            for ($i=0; $i<$cellCount; $i++) {
                $progressHtml .= $tabs . $tab . $tab;
                $progressHtml .= "<div id=\"". $progressId . sprintf($cellAttr['id'],$i) ."A\"";
                $progressHtml .= " class=\"".$cellAttr['class']."A\"";
                $progressHtml .= " style=\"position:absolute;left:".$cellAttr['spacing']."px;top:".$pos."px;";
                if (isset($cellAttr[$i])) {
                    $progressHtml .= "color:".$cellAttr[$i]['color'].";\"";
                } else {
                    $progressHtml .= "\"";
                }
                $progressHtml .= ">&nbsp;</div>" . $lnEnd;
                $pos += ($cellAttr['height'] + $cellAttr['spacing']);
            }
        }
        return $progressHtml;
    }

    /**
     * Returns a polygonal progress structure as HTML.
     *
     * @return     string                   Polygonal HTML Progress
     * @since      1.2.0RC1
     * @access     private
     */
    function _getProgressPolygonal_toHtml()
    {
        $ui =& $this->_UI;
        $tabs = $ui->_getTabs();
        $tab = $ui->_getTab();
        $lnEnd = $ui->_getLineEnd();
        $way_natural = ($ui->getFillWay() == 'natural');
        $cellAttr = $ui->getCellAttributes();
        $cellCount = $ui->getCellCount();
        $coord = $ui->_coordinates;

        $progressId = $this->getIdent().'_';
        $progressHtml = "";

        if ($way_natural) {
            // inactive cells first
            for ($i=0; $i<$cellCount; $i++) {
                $top  = $coord[$i][0] * $cellAttr['width'];
                $left = $coord[$i][1] * $cellAttr['height'];
                $progressHtml .= $tabs . $tab . $tab;
                $progressHtml .= "<div id=\"". $progressId . sprintf($cellAttr['id'],$i) ."I\"";
                $progressHtml .= " class=\"".$cellAttr['class']."I\"";
                $progressHtml .= " style=\"position:absolute;top:".$top."px;left:".$left."px;\"";
                $progressHtml .= ">&nbsp;</div>" . $lnEnd;
            }
            // then active cells
            for ($i=0; $i<$cellCount; $i++) {
                $top  = $coord[$i][0] * $cellAttr['width'];
                $left = $coord[$i][1] * $cellAttr['height'];
                $progressHtml .= $tabs . $tab . $tab;
                $progressHtml .= "<div id=\"". $progressId . sprintf($cellAttr['id'],$i) ."A\"";
                $progressHtml .= " class=\"".$cellAttr['class']."A\"";
                $progressHtml .= " style=\"position:absolute;top:".$top."px;left:".$left."px;\"";
                if (isset($cellAttr[$i])) {
                    $progressHtml .= "color:".$cellAttr[$i]['color'].";\"";
                } else {
                    $progressHtml .= "\"";
                }
                $progressHtml .= ">&nbsp;</div>" . $lnEnd;
            }
        } else {
            $c = count($coord) - 1;
            // inactive cells first
            for ($i=0; $i<$cellCount; $i++) {
                $top  = $coord[$c-$i][0] * $cellAttr['width'];
                $left = $coord[$c-$i][1] * $cellAttr['height'];
                $progressHtml .= $tabs . $tab . $tab;
                $progressHtml .= "<div id=\"". $progressId . sprintf($cellAttr['id'],$i) ."I\"";
                $progressHtml .= " class=\"".$cellAttr['class']."I\"";
                $progressHtml .= " style=\"position:absolute;top:".$top."px;left:".$left."px;\"";
                $progressHtml .= ">&nbsp;</div>" . $lnEnd;
            }
            // then active cells
            for ($i=0; $i<$cellCount; $i++) {
                $top  = $coord[$c-$i][0] * $cellAttr['width'];
                $left = $coord[$c-$i][1] * $cellAttr['height'];
                $progressHtml .= $tabs . $tab . $tab;
                $progressHtml .= "<div id=\"". $progressId . sprintf($cellAttr['id'],$i) ."A\"";
                $progressHtml .= " class=\"".$cellAttr['class']."A\"";
                $progressHtml .= " style=\"position:absolute;top:".$top."px;left:".$left."px;\"";
                if (isset($cellAttr[$i])) {
                    $progressHtml .= "color:".$cellAttr[$i]['color'].";\"";
                } else {
                    $progressHtml .= "\"";
                }
                $progressHtml .= ">&nbsp;</div>" . $lnEnd;
            }
        }
        return $progressHtml;
    }

    /**
     * Returns a circle progress structure as HTML.
     *
     * @return     string                   Circle HTML Progress
     * @since      1.2.0RC1
     * @access     private
     */
    function _getProgressCircle_toHtml()
    {
        $ui =& $this->_UI;
        $tabs = $ui->_getTabs();
        $tab = $ui->_getTab();
        $lnEnd = $ui->_getLineEnd();
        $way_natural = ($ui->getFillWay() == 'natural');
        $cellAttr = $ui->getCellAttributes();
        $cellCount = $ui->getCellCount();

        $progressId = $this->getIdent().'_';
        $progressHtml = "";

        if ($way_natural) {
            // inactive cells first
            for ($i=0; $i<$cellCount; $i++) {
                $progressHtml .= $tabs . $tab . $tab;
                $progressHtml .= "<div id=\"". $progressId . sprintf($cellAttr['id'],$i) ."I\"";
                $progressHtml .= " class=\"".$cellAttr['class']."I\"";
                $progressHtml .= " style=\"position:absolute;top:0;left:0;\"";
                $progressHtml .= ">&nbsp;</div>" . $lnEnd;
            }
            // then active cells
            for ($i=0; $i<$cellCount; $i++) {
                $progressHtml .= $tabs . $tab . $tab;
                $progressHtml .= "<div id=\"". $progressId . sprintf($cellAttr['id'],$i) ."A\"";
                $progressHtml .= " class=\"".$cellAttr['class']."A\"";
                $progressHtml .= " style=\"position:absolute;top:0;left:0;\"";
                $progressHtml .= "><img src=\"".$cellAttr[$i+1]['background-image']."\" border=\"0\" /></div>" . $lnEnd;
            }
        } else {
            // inactive cells first
            for ($i=0; $i<$cellCount; $i++) {
                $progressHtml .= $tabs . $tab . $tab;
                $progressHtml .= "<div id=\"". $progressId . sprintf($cellAttr['id'],$i) ."I\"";
                $progressHtml .= " class=\"".$cellAttr['class']."I\"";
                $progressHtml .= " style=\"position:absolute;top:0;left:0;\"";
                $progressHtml .= ">&nbsp;</div>" . $lnEnd;
            }
            // then active cells
            for ($i=0; $i<$cellCount; $i++) {
                $progressHtml .= $tabs . $tab . $tab;
                $progressHtml .= "<div id=\"". $progressId . sprintf($cellAttr['id'],$i) ."A\"";
                $progressHtml .= " class=\"".$cellAttr['class']."A\"";
                $progressHtml .= " style=\"position:absolute;top:0;left:0;\"";
                $progressHtml .= "><img src=\"".$cellAttr[$i+1]['background-image']."\" border=\"0\" /></div>" . $lnEnd;
            }
        }
        return $progressHtml;
    }

    /**
     * Checks for callback function existance
     *
     * @param      mixed     $callback      a callback, like one used by call_user_func()
     *
     * @return     boolean
     * @since      1.2.0RC3
     * @access     private
     */
    function _callbackExists($callback)
    {
        if (is_string($callback)) {
            return function_exists($callback);
        } elseif (is_array($callback) && is_object($callback[0])) {
            return method_exists($callback[0], $callback[1]);
        } else {
            return false;
        }
    }

    /**
     * Initialize Error Handler
     *
     * Parameter '$prefs' contains a hash of options to define the error handler.
     * You may find :
     *  'message_callback'  A callback to generate message body.
     *                      Default is:  HTML_Progress::_msgCallback()
     *  'context_callback'  A callback to generate context of error.
     *                      Default is:  HTML_Progress::_getBacktrace()
     *  'push_callback'     A callback to determine whether to allow an error
     *                      to be pushed or logged.
     *                      Default is:  HTML_Progress::_handleError()
     *  'error_handler'     A callback to manage all error raised.
     *                      Default is:  HTML_Progress::_errorHandler()
     *  'handler'           Hash of params to configure all handlers (display, file, mail ...)
     *                      There are only a display handler by default with options below:
     *  <code>
     *  array('display' => array('conf' => $options));
     *  // where $options are:
     *  $options = array(
     *      'lineFormat' => '<b>%1$s</b>: %2$s %3$s',
     *      'contextFormat' => ' in <b>%3$s</b> (file <b>%1$s</b> at line <b>%2$s</b>)'
     *  );
     *  </code>
     *
     * @param      array     $prefs         hash of params to configure error handler
     *
     * @return     void
     * @since      1.2.0
     * @access     private
     * @static
     */
    function _initErrorHandler($prefs = array())
    {
        // error message mapping callback
        if (isset($prefs['message_callback']) && is_callable($prefs['message_callback'])) {
            $GLOBALS['_HTML_PROGRESS_CALLBACK_MESSAGE'] = $prefs['message_callback'];
        } else {
            $GLOBALS['_HTML_PROGRESS_CALLBACK_MESSAGE'] = array('HTML_Progress', '_msgCallback');
        }

        // error context mapping callback
        if (isset($prefs['context_callback']) && is_callable($prefs['context_callback'])) {
            $GLOBALS['_HTML_PROGRESS_CALLBACK_CONTEXT'] = $prefs['context_callback'];
        } else {
            $GLOBALS['_HTML_PROGRESS_CALLBACK_CONTEXT'] = array('HTML_Progress', '_getBacktrace');
        }

        // determine whether to allow an error to be pushed or logged
        if (isset($prefs['push_callback']) && is_callable($prefs['push_callback'])) {
            $GLOBALS['_HTML_PROGRESS_CALLBACK_PUSH'] = $prefs['push_callback'];
        } else {
            $GLOBALS['_HTML_PROGRESS_CALLBACK_PUSH'] = array('HTML_Progress', '_handleError');
        }

        // default error handler will use PEAR_Error
        if (isset($prefs['error_handler']) && is_callable($prefs['error_handler'])) {
            $GLOBALS['_HTML_PROGRESS_CALLBACK_ERRORHANDLER'] = $prefs['error_handler'];
        } else {
            $GLOBALS['_HTML_PROGRESS_CALLBACK_ERRORHANDLER'] = array('HTML_Progress', '_errorHandler');
        }

        // only a display handler is set by default with specific settings
        $conf = array('lineFormat' => '<b>%1$s</b>: %2$s %3$s',
                      'contextFormat' => ' in <b>%3$s</b> (file <b>%1$s</b> at line <b>%2$s</b>)'
                      );

        $optionsHandler['display'] = array('conf' => $conf);

        if (isset($prefs['handler'])) {
            $optionsHandler = array_merge($optionsHandler, $prefs['handler']);
        }
        $GLOBALS['_HTML_PROGRESS_ERRORHANDLER_OPTIONS'] = $optionsHandler;
    }

    /**
     * Default callback to generate error messages for any instance
     *
     * @param      array     $err           current error structure with context info
     *
     * @return     string
     * @since      1.2.0RC1
     * @access     private
     * @static
     */
    function _msgCallback($err)
    {
        $messages = HTML_Progress::_getErrorMessage();
        $mainmsg = $messages[$err['code']];

        if (count($err['params'])) {
            foreach ($err['params'] as $name => $val) {
                if (is_array($val)) {
                    $val = implode(', ', $val);
                }
                $mainmsg = str_replace('%' . $name . '%', $val,  $mainmsg);
            }
        }
        return $mainmsg;
    }

    /**
     * Standard file/line number/function/class context callback
     *
     * @return     false|array
     * @since      1.2.0
     * @access     private
     * @static
     */
    function _getBacktrace()
    {
        if (function_exists('debug_backtrace')) {
            $backtrace = debug_backtrace();     // PHP 4.3+
            $backtrace = $backtrace[count($backtrace)-1];
        } else {
            $backtrace = false;                 // PHP 4.1.x, 4.2.x (no context info available)
        }
        return $backtrace;
    }

    /**
     * Standard callback, this will be called every time an error
     * is pushed onto the stack.  The return value will be used to determine
     * whether to allow an error to be pushed or logged.
     * Dies if the error is an exception (and would have died anyway)
     *
     * @param      array     $err           current error structure with context info
     *
     * @return     null|HTML_PROGRESS_ERRORSTACK_* constant
     * @since      1.2.0RC2
     * @access     private
     * @static
     * @see        HTML_PROGRESS_ERRORSTACK_PUSHANDLOG, HTML_PROGRESS_ERRORSTACK_PUSH,
     *             HTML_PROGRESS_ERRORSTACK_LOG, HTML_PROGRESS_ERRORSTACK_IGNORE,
     *             HTML_PROGRESS_ERRORSTACK_LOGANDDIE
     *
     */
    function _handleError($err)
    {
        if ($err['level'] == 'exception') {
            return HTML_PROGRESS_ERRORSTACK_LOGANDDIE;
        }
    }

    /**
     * Standard error handler that will use PEAR_Error object
     *
     * To improve performances, the PEAR.php file is included dynamically.
     * The file is so included only when an error is triggered. So, in most
     * cases, the file isn't included and perfs are much better.
     *
     * @param      array     $err           current error structure with context info
     *
     * @return     PEAR_Error
     * @since      1.2.0
     * @access     private
     * @static
     */
    function _errorHandler($err)
    {
        include_once 'PEAR.php';
        $e = PEAR::raiseError($err['message'], $err['code'], PEAR_ERROR_RETURN, E_USER_ERROR,
                              $err['context']);

        if (isset($err['context'])) {
            $file  = $err['context']['file'];
            $line  = $err['context']['line'];
            $func  = $err['context']['class'];
            $func .= $err['context']['type'];
            $func .= $err['context']['function'];
        }

        $display_errors = ini_get('display_errors');
        $log_errors = ini_get('log_errors');

        $display = $GLOBALS['_HTML_PROGRESS_ERRORHANDLER_OPTIONS']['display'];

        if ($display_errors) {
            $lineFormat = $display['conf']['lineFormat'];
            $contextFormat = $display['conf']['contextFormat'];

            $context = sprintf($contextFormat, $file, $line, $func);

            printf($lineFormat."<br />\n", ucfirst($err['level']), $err['message'], $context);
        }

        if ($log_errors) {
            if (isset($GLOBALS['_HTML_PROGRESS_ERRORHANDLER_OPTIONS']['error_log'])) {
                $error_log = $GLOBALS['_HTML_PROGRESS_ERRORHANDLER_OPTIONS']['error_log'];
                $message_type = $error_log['name'];
                $destination = '';
                $extra_headers = '';
                $send = true;

                switch ($message_type) {
                    case HTML_PROGRESS_LOG_TYPE_SYSTEM:
                        break;
                    case HTML_PROGRESS_LOG_TYPE_MAIL:
                        $destination = $error_log['conf']['destination'];
                        $extra_headers = $error_log['conf']['extra_headers'];
                        break;
                    case HTML_PROGRESS_LOG_TYPE_FILE:
                        $destination = $error_log['conf']['destination'];
                        break;
                    default:
                        $send = false;
                }
                if ($send) {
                    /**
                     * String containing the format of a log line.
                     * Position arguments are:
                     *  $1 -> timestamp
                     *  $2 -> ident
                     *  $3 -> level
                     *  $4 -> message
                     *  $5 -> context
                     */
                    if (isset($error_log['conf']['lineFormat'])) {
                        $lineFormat = $error_log['conf']['lineFormat'];
                    } else {
                        $lineFormat = '%1$s %2$s [%3$s] %4$s %5$s';
                    }

                    /**
                     * String containing the timestamp format
                     */
                    if (isset($error_log['conf']['timeFormat'])) {
                        $timeFormat = $error_log['conf']['timeFormat'];
                    } else {
                        $timeFormat = '%b %d %H:%M:%S';
                    }

                    /**
                     * String containing the error execution context format
                     */
                    if (isset($error_log['conf']['contextFormat'])) {
                        $contextFormat = $error_log['conf']['contextFormat'];
                    } else {
                        $contextFormat = strip_tags($display['conf']['contextFormat']);
                    }

                    /**
                     * String containing the end-on-line character sequence
                     */
                    if (isset($error_log['conf']['eol'])) {
                        $eol = $error_log['conf']['eol'];
                    } else {
                        $eol = "\n";
                    }

                    $context = sprintf($contextFormat, $file, $line, $func);
                    $message = sprintf($lineFormat,
                                       strftime($timeFormat, $err['time']),
                                       $error_log['ident'],
                                       $err['level'],
                                       $err['message'],
                                       $context);

                    error_log($message.$eol, $message_type, $destination, $extra_headers);
                }
            }
        }
        return $e;
    }

    /**
     * Error Message Template array
     *
     * @return     string
     * @since      1.0
     * @access     private
     */
    function _getErrorMessage()
    {
        $messages = array(
            HTML_PROGRESS_ERROR_INVALID_INPUT =>
                'invalid input, parameter #%paramnum% '
                    . '"%var%" was expecting '
                    . '"%expected%", instead got "%was%"',
            HTML_PROGRESS_ERROR_INVALID_CALLBACK =>
                'invalid callback, parameter #%paramnum% '
                    . '"%var%" expecting %element%,'
                    . ' instead got "%was%" does not exists',
            HTML_PROGRESS_DEPRECATED =>
                'method is deprecated '
                    . 'use %newmethod% instead of %oldmethod%'

        );
        return $messages;
    }

    /**
     * Add an error to the stack
     *
     * @param      integer   $code       Error code.
     * @param      string    $level      The error level of the message.
     * @param      array     $params     Associative array of error parameters
     *
     * @return     NULL|PEAR_Error       PEAR_Error instance,
     *                                   with context info if PHP 4.3.0+
     * @since      1.2.0RC1
     * @access     public
     * @static
     * @see        hasErrors(), getError()
     */
    function raiseError($code, $level, $params)
    {
        // obey at protocol
        if (error_reporting() == 0) {
            return;
        }

        // grab error context
        $context = call_user_func($GLOBALS['_HTML_PROGRESS_CALLBACK_CONTEXT']);

        // save error
        $time = explode(' ', microtime());
        $time = $time[1] + $time[0];
        $err = array(
                'code' => $code,
                'params' => $params,
                'package' => 'HTML_Progress',
                'level' => $level,
                'time' => $time,
                'context' => $context
               );

        // set up the error message, if necessary
        $err['message'] = call_user_func($GLOBALS['_HTML_PROGRESS_CALLBACK_MESSAGE'], $err);

        $push = $log = true;
        $die = false;
        $action = call_user_func($GLOBALS['_HTML_PROGRESS_CALLBACK_PUSH'], $err);

        switch($action){
            case HTML_PROGRESS_ERRORSTACK_IGNORE:
                $push = $log = false;
                break;
            case HTML_PROGRESS_ERRORSTACK_PUSH:
                $log = false;
                break;
            case HTML_PROGRESS_ERRORSTACK_LOG:
                $push = false;
            break;
            case HTML_PROGRESS_ERRORSTACK_LOGANDDIE:
                $push = false;
                $die = true;
            break;
            // anything else returned has the same effect as pushandlog
        }

        $e = false;
        if ($push) {
            array_unshift($GLOBALS['_HTML_PROGRESS_ERRORSTACK'], $err);
        }
        if ($log) {
            // default callback returns a PEAR_Error object
            $e = call_user_func($GLOBALS['_HTML_PROGRESS_CALLBACK_ERRORHANDLER'], $err);
        }
        if ($die) {
            die();
        }
        return $e;
    }

    /**
     * Determine whether there are errors into the HTML_Progress stack
     *
     * @return     integer
     * @since      1.2.0RC3
     * @access     public
     * @static
     * @see        getError(), raiseError()
     */
    function hasErrors()
    {
        return count($GLOBALS['_HTML_PROGRESS_ERRORSTACK']);
    }

    /**
     * Pop an error off of the HTML_Progress stack
     *
     * @return     false|array
     * @since      1.2.0RC3
     * @access     public
     * @static
     * @see        hasErrors(), raiseError()
     */
    function getError()
    {
        return @array_shift($GLOBALS['_HTML_PROGRESS_ERRORSTACK']);
    }
}
?>