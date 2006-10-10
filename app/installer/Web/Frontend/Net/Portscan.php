<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Martin Jansen <mj@php.net>                                  |
// |                                                                      |
// +----------------------------------------------------------------------+
//
// $Id$

define("NET_PORTSCAN_SERVICE_FOUND", true);
define("NET_PORTSCAN_NO_SERVICE", false);

/**
 * Portscan class
 *
 * This class provides methods to scan ports on machines,
 * that are connected to the internet. See README for more
 * information on how to use it.
 *
 * @author  Martin Jansen <mj@php.net>
 * @package Net_Portscan
 * @category Net
 * @version $Revision$
 */
class Net_Portscan
{
    // {{{ checkPort()

    /**
     * Check if there is a service available at a certain port.
     *
     * This function tries to open a connection to the port
     * $port on the machine $host. If the connection can be
     * established, there is a service listening on the port.
     * If the connection fails, there is no service.
     *
     * @access public
     * @param  string  Hostname
     * @param  integer Portnumber
     * @param  integer Timeout for socket connection in seconds (default is 30).
     * @return string
     */
    function checkPort($host, $port, $timeout = 30)
    {
        $socket = @fsockopen($host, $port, $errorNumber, $errorString, $timeout);

        if (!$socket) {
            return NET_PORTSCAN_NO_SERVICE;
        }

        @fclose($socket);
        return NET_PORTSCAN_SERVICE_FOUND;
    }

    // }}}
    // {{{ checkPortRange()

    /**
     * Check a range of ports at a machine
     *
     * This function can scan a range of ports (from $minPort
     * to $maxPort) on the machine $host for running services.
     *
     * @access public
     * @param  string Hostname
     * @param  integer Lowest port
     * @param  integer Highest port
     * @param  integer Timeout for socket connection in seconds (default is 30).
     * @return array  Associative array containing the result
     */
    function checkPortRange($host, $minPort, $maxPort, $timeout = 30)
    {
        for ($i = $minPort; $i <= $maxPort; $i++) {
            $retVal[$i] = Net_Portscan::checkPort($host, $i, $timeout);
        }

        return $retVal;
    }

    // }}}
    // {{{ getService()
    
    /**
     * Get name of the service that is listening on a certain port.
     *
     * @access public
     * @param  integer Portnumber
     * @param  string  Protocol (Is either tcp or udp. Default is tcp.)
     * @return string  Name of the Internet service associated with $service
     */
    function getService($port, $protocol = "tcp")
    {
        return @getservbyport($port, $protocol);
    }

    // }}}
    // {{{ getPort()

    /**
     * Get port that a certain service uses.
     *
     * @access public
     * @param  string  Name of the service
     * @param  string  Protocol (Is either tcp or udp. Default is tcp.)
     * @return integer Internet port which corresponds to $service
     */
    function getPort($service, $protocol = "tcp")
    {
        return @getservbyname($service, $protocol);
    }

    // }}}
}
?>
