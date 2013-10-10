<?php
/**
 * PandraLoggerSTDOUT
 *
 * Sends all logging events to standard output (unbuffered)
 *
 * @author Michael Pearson <pandra-support@phpgrease.net>
 * @copyright 2010 phpgrease.net
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @version 0.2.1
 * @package pandra
 */
class PandraLoggerSTDOUT implements PandraLogger {

    private $_isOpen = true;

    private $_htmlMode = FALSE;

    public function __construct(array $params) {
        if (array_key_exists('htmlMode', $params)) {
            $this->_htmlMode = (bool) $params['htmlMode'];
        }
        return $this->_isOpen;
    }

    public function isOpen() {
        return $this->_isOpen;
    }

    /**
     * Log everything
     * @param int $priority requested priority log
     * @return boolean this logger will log for priority
     */
    public function isPriorityLogger($priority) {
        return TRUE;
    }

    public function execute($priority, $message) {
        $out = PandraLog::$priorityMap[$priority] . ": " . $message . "\n";

        if ($this->_htmlMode) $out = nl2br($out);

        if ($priority == PandraLog::LOG_CRIT) {
            throw new RuntimeException($out);
        } else {
            echo $out;
            return TRUE;
        }
    }
}
?>