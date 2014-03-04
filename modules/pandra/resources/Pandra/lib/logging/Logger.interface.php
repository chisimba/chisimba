<?php
/**
 * PandraLogger
 *
 * Logger interface.  Static 'Log' class expects loggers to use this interface.
 *
 * @author Michael Pearson <pandra-support@phpgrease.net>
 * @copyright 2010 phpgrease.net
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @version 0.2.1
 * @package pandra
 * @abstract
 */
interface PandraLogger {

    /**
     * Class constructor
     * @access public
     * @param array $params parameters to child constructor
     */
    public function __construct(array $params);

    /**
     * Logger is open and available
     * @access public
     * @return bool Logger constructed OK, is ready
     */
    public function isOpen();

    /**
     * Child will log for priority level
     * @access public
     * @param int $priority priority to question for
     * @return bool priority is OK
     */
    public function isPriorityLogger($priority);

    /**
     * Execute the logging function
     * @access public
     * @param int $priority priority to log for
     * @param string $message Message to log
     */
    public function execute($priority, $message);
}
?>