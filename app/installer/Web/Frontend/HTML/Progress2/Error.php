<?php
/**
 * This class creates a progress error object, extending the PEAR_Error class.
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
 * @package    HTML_Progress2
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/HTML_Progress2
 */

require_once 'PEAR.php';

/**
 * This class creates a progress error object, extending the PEAR_Error class.
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
 * @package    HTML_Progress2
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: 2.0.0
 * @link       http://pear.php.net/package/HTML_Progress2
 */

class HTML_Progress2_Error extends PEAR_Error
{

    /**
     * Constructor (ZE1)
     *
     * @since      2.0.0
     * @access     public
     */
    function HTML_Progress2_Error($message = null,
                                  $code = null,
                                  $mode = null, $options = null,
                                  $userinfo = null)
    {
        $this->__construct($message, $code, $mode, $options, $userinfo);
    }

    /**
     * Constructor (ZE2)
     *
     * @since      2.0.0
     * @access     public
     */
    function __construct($message = null,
                         $code = null,
                         $mode = null, $options = null,
                         $userinfo = null)
    {
        if ($mode === null) {
            $mode = PEAR_ERROR_RETURN;
        }
        $this->message   = $message;
        $this->code      = $code;
        $this->mode      = $mode;
        $this->userinfo  = $userinfo;
        if (function_exists('debug_backtrace')) {
            $this->backtrace = debug_backtrace();
        }

        if ($mode & PEAR_ERROR_CALLBACK) {
            $this->level = E_USER_NOTICE;
            $this->callback = $options;
        } else {
            if ($options === null) {
                switch ($userinfo['level']) {
                    case 'exception':
                    case 'error':
                        $options = E_USER_ERROR;
                        break;
                    case 'warning':
                        $options = E_USER_WARNING;
                        break;
                    default:
                        $options = E_USER_NOTICE;
                }
            }
            $this->level = $options;
            $this->callback = null;
        }
        if ($this->mode & PEAR_ERROR_PRINT) {
            echo $this->_display($userinfo);
        }
        if ($this->mode & PEAR_ERROR_TRIGGER) {
            trigger_error($this->getMessage(), $this->level);
        }
        if ($this->mode & PEAR_ERROR_DIE) {
            $this->log();
            die();
        }
        if ($this->mode & PEAR_ERROR_CALLBACK) {
            if (is_callable($this->callback)) {
                call_user_func($this->callback, $this);
            } else {
                $this->log();
            }
        }
    }
    
    /**
     * Get error level from an error object
     *
     * @return     int                      error level
     * @since      2.0.0
     * @access     public
     */
    function getLevel()
    {
       return $this->level;
    }

    /**
     * Default callback function/method from an error object
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     */
    function log()
    {
        $userinfo = $this->getUserInfo();

        $display_errors = ini_get('display_errors');
        $log_errors = ini_get('log_errors');

        if ($display_errors) {
            echo $this->_display($userinfo);
        }

        if ($log_errors) {
            $this->_log($userinfo);
        }
    }

    /**
     * Returns the context of execution formatted.
     *
     * @param      string    $format        the context of execution format 
     *
     * @return     string
     * @since      2.0.0
     * @access     public
     */
    function sprintContextExec($format)
    {
        $userinfo = $this->getUserInfo();

        if (isset($userinfo['context'])) {
            $context = $userinfo['context'];
        } else {
            $context = $this->getBacktrace();
            $context = @array_pop($context);
        }
   
        if ($context) {
            $file  = $context['file'];
            $line  = $context['line'];
            
            if (isset($context['class'])) {
                $func  = $context['class'];
                $func .= $context['type'];
                $func .= $context['function'];
            } elseif (isset($context['function'])) {
                $func  = $context['function'];
            } else {
                $func  = '';
            }
            return sprintf($format, $file, $line, $func);
        }
        return '';
    }

    /**
     * Print an error message 
     *
     * @param      array     $userinfo      has of parameters 
     *
     * @return     void
     * @since      2.0.0
     * @access     private
     */
    function _display($userinfo)
    {
        $displayDefault = array(
            'eol' => "<br/>\n",
            'lineFormat' => '<b>%1$s</b>: %2$s %3$s',
            'contextFormat' => 'in <b>%3$s</b> (file <b>%1$s</b> on line <b>%2$s</b>)'
        );
        $displayConf = $userinfo['display'];
        $display = array_merge($displayDefault, $displayConf);

        $contextExec = $this->sprintContextExec($display['contextFormat']);
        
        return sprintf($display['lineFormat'] . $display['eol'], ucfirst($userinfo['level']), $this->getMessage(), $contextExec);
    }

    /**
     * Send an error message somewhere
     *
     * @return     void
     * @since      2.0.0
     * @access     private
     */
    function _log($userinfo)
    {
        $logDefault = array(
            'eol' => "\n",
            'lineFormat' => '%1$s %2$s [%3$s] %4$s %5$s',
            'contextFormat' => 'in %3$s (file %1$s on line %2$s)',
            'timeFormat' => '%b %d %H:%M:%S',
            'ident' => $_SERVER['REMOTE_ADDR'],
            'message_type' => 3,
            'destination' => get_class($this) . '.log',
            'extra_headers' => ''
        );
        $logConf = $userinfo['log'];
        $log = array_merge($logDefault, $logConf);

        $message_type = $log['message_type'];
        $destination = '';
        $extra_headers = '';
        $send = true;
                
        switch ($message_type) {
            case 0:  // LOG_TYPE_SYSTEM:
                break;
            case 1:  // LOG_TYPE_MAIL:
                $destination = $log['destination'];
                $extra_headers = $log['extra_headers'];
                break;
            case 3:  // LOG_TYPE_FILE:
                $destination = $log['destination'];
                break;
            default:
                $send = false;
        }

        if ($send) {
            $time = explode(' ', microtime());
            $time = $time[1] + $time[0];
            $timestamp = isset($userinfo['time']) ? $userinfo['time'] : $time;

            $contextExec = $this->sprintContextExec($log['contextFormat']);
            $message = sprintf($log['lineFormat'] . $log['eol'], 
                           strftime($log['timeFormat'], $timestamp),
                           $log['ident'],
                           $userinfo['level'],
                           $this->getMessage(),
                           $contextExec);

            error_log(strip_tags($message), $message_type, $destination, $extra_headers);
        }
    }

    /**
     * Default internal error handler
     * Dies if the error is an exception (and would have died anyway)
     *
     * @param      int       $code          a numeric error code. 
     *                                      Valid are HTML_PROGRESS_ERROR_* constants
     * @param      string    $level         error level ('exception', 'error', 'warning', ...)
     *
     * @return     mixed
     * @since      2.0.0
     * @access     private
     */
    function _handleError($code, $level)
    {
        if ($level == 'exception') {
            return PEAR_ERROR_DIE;
        } else {
            return null;
        }
    }

    /**
     * User callback to generate error messages for any instance
     *
     * @param      int       $code          a numeric error code. 
     *                                      Valid are HTML_PROGRESS_ERROR_* constants
     * @param      mixed     $userinfo      if you need to pass along parameters 
     *                                      for dynamic messages
     *
     * @return     string
     * @since      2.0.0
     * @access     private
     */
    function _msgCallback($code, $userinfo)
    {
        $errorMessages = HTML_Progress2_Error::_getErrorMessage();
        
        if (isset($errorMessages[$code])) {
            $mainmsg = $errorMessages[$code];
        } else {
            $mainmsg = $errorMessages[HTML_PROGRESS_ERROR_UNKNOWN];
        }

        if (is_array($userinfo)) {
            foreach ($userinfo as $name => $val) {
                if (is_array($val)) {
                    // @ is needed in case $val is a multi-dimensional array
                    $val = @implode(', ', $val);
                }
                if (is_object($val)) {
                    if (method_exists($val, '__toString')) {
                        $val = $val->__toString();
                    } else {
                        continue;
                    }
                }
                $mainmsg = str_replace('%' . $name . '%', $val, $mainmsg);
            }
        }

        return $mainmsg;
    }

    /**
     * Error Message Template array
     *
     * @return     string
     * @since      2.0.0
     * @access     private
     */
    function _getErrorMessage()
    {
        $messages = array(
            HTML_PROGRESS2_ERROR_UNKNOWN =>
                'unknown error',
            HTML_PROGRESS2_ERROR_INVALID_INPUT =>
                'invalid input, parameter #%paramnum% '
                    . '"%var%" was expecting '
                    . '"%expected%", instead got "%was%"',
            HTML_PROGRESS2_ERROR_INVALID_CALLBACK =>
                'invalid callback, parameter #%paramnum% '
                    . '"%var%" expecting %element%,'
                    . ' instead got "%was%" does not exists',
            HTML_PROGRESS2_ERROR_DEPRECATED => 
                'method is deprecated '
                    . 'use %newmethod% instead of %oldmethod%',
            HTML_PROGRESS2_ERROR_INVALID_OPTION =>
                '%element% option "%prop%" is not allowed'
        );
        return $messages;
    }
}
?>