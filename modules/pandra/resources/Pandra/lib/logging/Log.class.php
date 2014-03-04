<?php
/**
 * Log
 *
 * Log is the static controller to handle logging access for the Pandra
 * suit of classes for a given priority level
 *
 * @author Michael Pearson <pandra-support@phpgrease.net>
 * @copyright 2010 phpgrease.net
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @version 0.2.1
 * @package pandra
 */
class PandraLog {

    /**
     * Constants normalise against syslog LOG_ types
     */
    const LOG_EMERG = 0;
    const LOG_ALERT = 1;
    const LOG_CRIT = 2;
    const LOG_ERR = 3;
    const LOG_WARNING = 4;
    const LOG_NOTICE = 5;
    const LOG_INFO = 6;
    const LOG_DEBUG = 7;

    /**
     * Array of registered loggers (keyed to class name)
     * @static
     * @access private
     * @var array $_loggers
     */
    static private $_loggers = array();

    /**
     * Array of internal log priorities to english
     * @static
     * @access public
     * @var array $priorityMap
     */
    static public $priorityMap = array(
            self::LOG_EMERG => 'emergency',
            self::LOG_ALERT => 'alert',
            self::LOG_CRIT => 'critical',
            self::LOG_ERR => 'error',
            self::LOG_WARNING => 'warning',
            self::LOG_NOTICE => 'notice',
            self::LOG_INFO => 'info',
            self::LOG_DEBUG => 'debug',
    );

    /**
     * Returns the class name of a logger implementing PandraLogger interface
     * @static
     * @access private
     * @param string $loggerName name of the logger we're deriving a class for
     * @param bool $verify ensure the class exists and implements PandraLogger interface
     * @return string name of the logger class we're looking for
     */
    static private function getClassFromName($loggerName, $verify = FALSE) {
        $classPfx = 'PandraLogger';

        // Strip the loggername, incase the entire class has been passed
        $class = $classPfx.ucfirst(preg_replace("/$classPfx/", '', $loggerName));

        if (!$verify) {
            return $class;
        } else {
            // Make sure the class exists
            if (class_exists($class)) {
                $c = new $class(array());
                if ($c instanceof PandraLogger) {
                    unset($c);
                    return $class;
                }
            }
            throw new RuntimeException("Logger $class could not be found");
        }
    }

    /**
     * Retrieves a named logger from the local register
     * @static
     * @access public
     * @param string $loggerName name of the logger
     * @return PandraLogger registered instance of the logger names
     */
    static public function getLogger($loggerName) {
        $lc = self::getClassFromName($loggerName);
        if (array_key_exists($lc, self::$_loggers)) return self::$_loggers[$lc];
        return NULL;
    }

    /**
     * Creates a new instance of the logger class, keyed to logger name
     *
     * @static
     * @access public
     * @param string $loggerName name of the logger (minus namespaced class prefix)
     * @param array $params parameters to pass through to logger construct
     * @return bool registered OK
     */
    static public function register($loggerName, $params = array()) {
        $lc = self::getClassFromName($loggerName, TRUE);
        if (!array_key_exists($lc, self::$_loggers)) {
            $lObj = new $lc($params);
            self::$_loggers[$lc] = &$lObj;
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Gets all registered logger names
     * @static
     * @access public
     * @return array array of logger names (strings)
     */
    static public function getRegisteredLoggers() {
        return array_values(self::$_loggers);
    }

    /**
     * Returs whether a logger has already been registerd
     * @static
     * @access public
     * @param string $loggerName name of the logger
     * @return bool logger name is registered
     */
    static public function isRegistered($loggerName) {
        $lc = self::getClassFromName($loggerName);
        return array_key_exists($lc, self::$_loggers);
    }

    /**
     * Returns whether any loggers are registered, which can log for prioritys
     * @static
     * @access public
     * @param int $priority priority level to interrogate
     * @return bool Logger for $priority has been registered
     */
    static public function hasPriorityLogger($priority) {
        foreach (self::$_loggers as $logger) {
            if ($logger->isPriorityLogger($priority)) return TRUE;
        }
        return FALSE;
    }

    /**
     * Sends a message to all loggers for priority
     * @static
     * @access public
     * @param int $priority priority level to log for
     * @param string $message message to log
     */
    static public function logPriorityMessage($priority, $message) {
        foreach (self::$_loggers as $logger) {
            if ($logger->isPriorityLogger($priority)) $logger->execute($priority, $message);
        }
    }

    /* ------- Priority Helper */
    /*
     * @todo 5.3, strip the priority helpers and use callStatic
    static public function __callStatic($class, $args) {
        $priority = array_search(strtolower($class), self::$priorityMap);
        if (!empty($priority)) {
            self::logPriorityMessage($priority, array_pop($args));
        }
    }
    */

    /**
     * Logs for priority Emergency
     * @static
     * @access public
     * @param string $message
     */
    static public function emerg($message, $trace = TRUE) {
        if ($trace) {
            $message = array_merge($message, debug_backtrace());
        }
        self::logPriorityMessage(self::LOG_EMERG, $message);
    }

    /**
     * Logs for priority Alert
     * @static
     * @access public
     * @param string $message
     */
    static public function alert($message) {
        self::logPriorityMessage(self::LOG_ALERT, $message);
    }

    /**
     * Logs for priority Crit(ical)
     * @static
     * @access public
     * @param string $message
     */
    static public function crit($message) {
        self::logPriorityMessage(self::LOG_CRIT, $message);
    }

    /**
     * Logs for priority Err(or)
     * @static
     * @access public
     * @param string $message
     */
    static public function err($message) {
        self::logPriorityMessage(self::LOG_ERR, $message);
    }

    /**
     * Logs for priority Warning
     * @static
     * @access public
     * @param string $message
     */
    static public function warning($message) {
        self::logPriorityMessage(self::LOG_WARNING, $message);
    }

    /**
     * Logs for priority Notice
     * @static
     * @access public
     * @param string $message
     */
    static public function notice($message) {
        self::logPriorityMessage(self::LOG_NOTICE, $message);
    }

    /**
     * Logs for priority Info
     * @static
     * @access public
     * @param string $message
     */
    static public function info($message) {
        self::logPriorityMessage(self::LOG_INFO, $message);
    }

    /**
     * Logs for priority Debug
     * @static
     * @access public
     * @param string $message
     */
    static public function debug($message, $trace = FALSE) {
        if ($trace) {
            $message = array_merge(array($message), debug_backtrace());
        }
        self::logPriorityMessage(self::LOG_DEBUG, $message);
    }
}
?>