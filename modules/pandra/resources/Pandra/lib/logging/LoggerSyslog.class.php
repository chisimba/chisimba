<?php
/**
 * PandraLoggerSyslog
 *
 * Syslog implementation of PandraLogger.  Handles all log messages err -> emergency
 * Syslog is part of PHP core, there are no other dependencies.
 *
 * @author Michael Pearson <pandra-support@phpgrease.net>
 * @copyright 2010 phpgrease.net
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @version 0.2.1
 * @package pandra
 */
class PandraLoggerSyslog implements PandraLogger {

    private $_isOpen = FALSE;

    private $_maxPriority = PandraLog::LOG_ERR;

    /* @var int default syslog open 'option' */
    const DEFAULT_OPTION = LOG_ODELAY;

    /* @var int default syslog open 'facility' */
    const DEFAULT_FACILITY = LOG_SYSLOG;

    public function __construct(array $params) {
        if (empty($params)) {
            $params = array(
                                'ident' => 'pandra',
                                'option' => self::DEFAULT_OPTION,
                                'facility' => self::DEFAULT_FACILITY);
        }
        if (function_exists('openlog')) {
            return $this->open($params['ident'],
                                $params['option'],
                                $params['facility']);
        }
        return FALSE;
    }

    public function isOpen() {
        return $this->_isOpen;
    }

    public function open($ident,
                            $option = self::DEFAULT_OPTION,
                            $facility = self::DEFAULT_FACILITY) {

        $this->_isOpen = openlog($ident, $option, $facility);
        return $this->_isOpen;
    }

    /**
     * Syslog shouldn't handle notices, info's etc.
     * @param int $priority requested priority log
     * @return boolean this logger will log for priority
     */
    public function isPriorityLogger($priority) {
        return ($priority <= $this->_maxPriority);
    }

    public function execute($priority, $message) {
        if ($this->isPriorityLogger($priority) &&
                !empty($message)
                && $this->_isOpen) {

            if (is_array($message)) {
                foreach ($message as $msg) {
                    syslog($priority, $message);
                }
            } else {
                syslog($priority, $message);
            }
            return TRUE;
        }
        return FALSE;
    }

    public function close() {
        if ($this->_isOpen) return closelog();
        return FALSE;
    }

    public function  __destruct() {
        $this->close();
    }
}
?>