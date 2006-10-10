<?php

/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2005 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author:          Ondrej Jombik <nepto@platon.sk>                     |
// | Original author: Gavin Brown <gavin.brown@uk.com>                    |
// +----------------------------------------------------------------------+
//
// $Id$
//
// Identification Protocol implementation
//

require_once 'PEAR.php';

/**
 * Net_Ident default values
 * @const   NET_IDENT_DEFAULT_TIMEOUT   Default ident query network timeout
 * @const   NET_IDENT_DEFAULT_PORT      Default ident protocol port
 */
define('NET_IDENT_DEFAULT_TIMEOUT',      30);
define('NET_IDENT_DEFAULT_PORT',        113);

/**
 * Net_Ident object states
 * @const   NET_IDENT_STATUS_UNDEF      Undefined Net_Ident object state
 * @const   NET_IDENT_STATUS_OK         Net_Ident object did successful query
 * @const   NET_IDENT_STATUS_ERROR      Net_Ident object query failed
 */
define('NET_IDENT_STATUS_UNDEF',          0);
define('NET_IDENT_STATUS_OK',             1);
define('NET_IDENT_STATUS_ERROR',          2);

/**
 * Net_Ident - Identification Protocol implementation according to RFC 1413
 *
 * The Identification Protocol (a.k.a., "ident", a.k.a., "the Ident Protocol")
 * provides a means to determine the identity of a user of a particular TCP
 * connection.  Given a TCP port number pair, it returns a character string
 * which identifies the owner of that connection on the server's system.
 *
 * You can find out more about the Ident protocol at
 *
 *      http://www.ietf.org/rfc/rfc1413.txt
 *
 * Usage:
 *   <?php
 *       require_once 'Net/Ident.php';
 *       $ident   = new Net_Ident;
 *       $user    = $ident->getUser();
 *       $os_type = $ident->getOsType();
 *       echo "user: $user, operating system: $os_type\n";
 *   ?>
 *
 * @author      Ondrej Jombik <nepto@platon.sk>
 * @package     Net_Ident
 * @version     1.1.0
 * @access      public
 */
class Net_Ident
{
    /**
     * Current object state (undef, ok, error)
     *
     * @var     enum
     * @access  private
     */
    var $_status;

    /**
     * Error message string;
     *   if $_status is "error", $_error contains system error message;
     *   if $_status is "ok", $_error contains ident error message
     * or it is empty
     *
     * @var     string
     * @access  private
     */
    var $_error;

    /**
     * Properties array (contains remote host, remote port, ident port, etc.)
     *
     * @var     array
     * @access  private
     */
    var $_props;

    /**
     * Data array (contains ident username, ident operating system type, and
     * raw line returned from ident server)
     *
     * @var     array
     * @access  private
     */
    var $_data;

    /**
     * Net_Ident object constructor
     *
     * Initializes class properties. Use empty string '' for any string
     * parameter and value of 0 for any int parameter to set default value.
     *
     * @param   string  $remote_addr    Remote host address (IP or hostname)
     * @param   int     $remote_port    Remote port (default $REMOTE_PORT)
     * @param   int     $local_port     Local port  (default $SERVER_PORT)
     * @param   int     $ident_port     Ident port  (default 113)
     * @param   int     $timeout        Socket timeout (default 30 seconds)
     * @return  none
     * @access  public
     */
    function Net_Ident(
            $remote_addr = '',
            $remote_port = 0,
            $local_port  = 0,
            $ident_port  = 0,
            $timeout     = 0)
    {
        $this->_status = NET_IDENT_STATUS_UNDEF;
        $this->setRemoteAddr($remote_addr);
        $this->setRemotePort($remote_port);
        $this->setLocalPort($local_port);
        $this->setIdentPort($ident_port);
        $this->setTimeout($timeout);
    }

    /**
     * Sets remote host address (IP or hostname)
     *
     * @param   string  $remote_addr    Remote host address (IP or hostname)
     * @return  none
     * @access  public
     */
    function setRemoteAddr($remote_addr)
    {
        strlen($remote_addr) <= 0 && $remote_addr = $_SERVER['REMOTE_ADDR'];
        $this->_props['remote_addr'] = $remote_addr;
    }

    /**
     * Sets remote port
     *
     * @param   int     $remote_port    Remote port (default $REMOTE_PORT)
     * @return  none
     * @access  public
     */
    function setRemotePort($remote_port)
    {
        $remote_port = intval($remote_port);
        $remote_port <= 0 && $remote_port = $_SERVER['REMOTE_PORT'];
        $this->_props['remote_port'] = $remote_port;
    }

    /**
     * Sets local port
     *
     * @param   int     $local_port     Local port  (default $SERVER_PORT)
     * @return  none
     * @access  public
     */
    function setLocalPort($local_port)
    {
        $local_port = intval($local_port);
        $local_port <= 0 && $local_port = $_SERVER['SERVER_PORT'];
        $this->_props['local_port'] = $local_port;
    }

    /**
     * Sets ident port
     *
     * @param   int     $ident_port     Ident port  (default 113)
     * @return  none
     * @access  public
     */
    function setIdentPort($ident_port)
    {
        $ident_port = intval($ident_port);
        $ident_port <= 0 && $ident_port = NET_IDENT_DEFAULT_PORT;
        $this->_props['ident_port'] = $ident_port;
    }

    /**
     * Sets socket timeout
     *
     * @param   int     $timeout        Socket timeout (default 30 seconds)
     * @return  none
     * @access  public
     */
    function setTimeout($timeout)
    {
        $timeout = intval($timeout);
        $timeout <= 0 && $timeout = NET_IDENT_DEFAULT_TIMEOUT;
        $this->_props['timeout'] = $timeout;
    }

    /**
     * Performs network socket ident query
     *
     * @return  mixed   PEAR_Error on connection error
     *                  rawdata read from socket on success
     * @access  public
     */
    function query()
    {
        // query forced, clean current result
        if ($this->_status == NET_IDENT_STATUS_OK) {
            unset($this->_data['username']);
            unset($this->_data['os_type']);
            $this->_status = NET_IDENT_STATUS_UNDEF;
        }
        while (1) {
            if ($this->_status == NET_IDENT_STATUS_ERROR) {
                return new PEAR_Error($this->_error);
            }

            if ($socket = @fsockopen(
                        $this->_props['remote_addr'],
                        $this->_props['ident_port'],
                        $errno, $errstr,
                        $this->_props['timeout'])) {
                break;
            }
            $this->_status = NET_IDENT_STATUS_ERROR;
            $this->_error  = 'Error connecting to ident server ('
                    .$this->_props['remote_addr'].':'
                    .$this->_props['ident_port']."): $errstr ($errno)"; // )
        }

        $line = $this->_props['remote_port'].','
            .$this->_props['local_port']."\r\n";
        @fwrite($socket, $line);
        $line = @fgets($socket, 1000); // 1000 octets according to RFC 1413
        fclose($socket);

        $this->_status = NET_IDENT_STATUS_OK;
        $this->_data['rawdata'] = $line;
        $this->_parseIdentResponse($line);

        return $line;
    }

    /**
     * Returns ident username
     *
     * @return  mixed   PEAR_Error on connection error
     *                  false boolean on ident protocol error
     *                  username string on success
     * @access  public
     */
    function getUser()
    {
        $this->_status == NET_IDENT_STATUS_UNDEF && $this->query();
        if ($this->_status == NET_IDENT_STATUS_ERROR) {
            return new PEAR_Error($this->_error);
        }
        return $this->_data['username'];
    }

    /**
     * Returns ident operating system type
     *
     * @return  mixed   PEAR_Error on connection error
     *                  false boolean on ident protocol error
     *                  operating system type string on success
     * @access  public
     */
    function getOsType()
    {
        $this->_status == NET_IDENT_STATUS_UNDEF && $this->query();
        if ($this->_status == NET_IDENT_STATUS_ERROR) {
            return new PEAR_Error($this->_error);
        }
        return $this->_data['os_type'];
    }

    /**
     * Returns ident protocol error
     *
     * @return  mixed   error string if ident protocol error had occured
     *                  false otherwise
     * @access  public
     */
    function identError()
    {
        if ($this->_status == NET_IDENT_STATUS_OK
                && isset($this->_error)) {
            return $this->_error;
        }
        return false;
    }

    /**
     * Parses response from indent server and sets internal data structures
     * with ident username and ident operating system type
     *
     * @param   string  $string     ident server response
     * @return  boolean true if no ident protocol error had occured
     *                  false otherwise
     * @access  private
     */
    function _parseIdentResponse($string)
    {
        $this->_data['username'] = false;
        $this->_data['os_type']  = false;
        $array = explode(':', $string, 4);
        if (count($array) > 1 && ! strcasecmp(trim($array[1]), 'USERID')) {
            isset($array[2]) && $this->_data['os_type']  = trim($array[2]);
            isset($array[3]) && $this->_data['username'] = trim($array[3]);
            return true;
        } elseif (count($array) > 1 && ! strcasecmp(trim($array[1]), 'ERROR')) {
            isset($array[2]) && $this->_error = trim($array[2]);
        } else {
            $this->_error = 'Invalid ident server response';
        }
        return false;
    }
}

?>
