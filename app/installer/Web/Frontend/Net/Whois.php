<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// | Portions Copyright (c) 1980, 1993 The Regents of the University of   |
// |   California.  All rights reserved.                                  |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author: Seamus Venasse <seamus.venasse@polaris.ca>                   |
// +----------------------------------------------------------------------+
//
// $Id$
//
// Whois Class
//
require_once('PEAR.php');

/**
 * Looks up records in the databases maintained by several Network Information
 * Centres (NICs).  This class uses PEAR's Net_Socket:: class.
 *
 * @version 1.0
 * @author Seamus Venasse <seamus.venasse@polaris.ca>
 * @package Net
 * @access public
 */
class Net_Whois extends PEAR {

    // {{{ properties

    /**
     * List of NICs to query
     *
     * @var array
     * @access private
     */
    var $_nicServers = array (
        "NICHOST"           => "whois.crsnic.net",
        "INICHOST"          => "whois.networksolutions.com",
        "DNICHOST"          => "whois.nic.mil",
        "GNICHOST"          => "whois.nic.gov",
        "ANICHOST"          => "whois.arin.net",
        "RNICHOST"          => "whois.ripe.net",
        "PNICHOST"          => "whois.apnic.net",
        "RUNICHOST"         => "whois.ripn.net",
        "MNICHOST"          => "whois.ra.net",
        "QNICHOST_TAIL"     => ".whois-servers.net",
        "SNICHOST"          => "whois.6bone.net",
        "BNICHOST"          => "whois.registro.br"
    );

    /**
     * Search string of server to search on
     *
     * @var string
     * @access private
     */
    var $_whoisServerID = "Whois Server: ";

    /**
     * Server to search for IP address lookups
     *
     * @var array
     * @access private
     */
    var $_ipNicServers = array ("RNICHOST", "PNICHOST", "BNICHOST");

    /**
     * List of error codes and text
     *
     * @var array
     * @access private
     */
    var $_errorCodes = array (
        010 => 'Unable to create a socket object',
        011 => 'Unable to open socket',
        012 => 'Write to socket failed',
        013 => 'Read from socket failed'
    );
    // }}}

    // {{{ constructor
    /**
     * Constructs a new Net_Whois object
     *
     * @access public
     */
    function Net_Whois() {
        $this->PEAR();
    }
    // }}}

    // {{{ query()
    /**
     * Connect to the necessary servers to perform a domain whois query.  Prefix
     * queries with a "!" to lookup information in InterNIC handle database.
     * Add a "-arin" suffix to queries to lookup information in ARIN handle
     * database.
     *
     * @param $domain string IP address or host name
     * @param $userWhoisServer string server to query (optional)
     * @access public
     * @return mixed returns a PEAR_Error on failure, or a string on success
     */
    function query($domain, $userWhoisServer = null) {
        $domain = trim($domain);

        if (isset($userWhoisServer)) {
            $whoisServer = $userWhoisServer;
        } elseif (preg_match("/^!.*/", $domain)) {
            $whoisServer = $this->_nicServers["INICHOST"];
        } elseif (preg_match("/.*?-arin/i", $domain)) {
            $whoisServer = $this->_nicServers["ANICHOST"];
        } elseif (preg_match('/\.gov$/i', $domain)) {
            $whoisServer = $this->_nicServers["GNICHOST"];
        } elseif (preg_match('/\.mil$/i', $domain)) {
            $whoisServer = $this->_nicServers["DNICHOST"];
        } else {
            $whoisServer = $this->_chooseServer($domain);
        }

        $whoisData = $this->_connect($whoisServer, $domain);
        if (PEAR::isError($whoisData)) {
            return $whoisData;
        }

        return $whoisData;
    }
    // }}}

    // {{{ queryAPNIC()
    /**
     * Use the Asia/Pacific Network Information Center (APNIC) database.
     * It contains network numbers used in East Asia, Australia, New
     * Zealand, and the Pacific islands.
     *
     * @param $ipAddress string IP address
     * @access public
     * @return mixed returns a PEAR_Error on failure, or a string on success
     */
    function queryAPNIC($domain) {
        return $this->query($domain, $this->_nicServers["PNICHOST"]);
    }
    // }}}

    // {{{ queryIPv6()
    /**
     * Use the IPv6 Resource Center (6bone) database.  It contains network
     * names and addresses for the IPv6 network.
     *
     * @param $domain string IP address or host name
     * @access public
     * @return mixed returns a PEAR_Error on failure, or a string on success
     */
    function queryIPv6($domain) {
        return $this->query($domain, $thiis->_nicServers["SNICHOST"]);
    }
    // }}}

    // {{{ queryRADB()
    /**
     * Use the Route Arbiter Database (RADB) database.  It contains
     * route policy specifications for a large number of operators'
     * networks.
     *
     * @param $ipAddress string IP address
     * @access public
     * @return mixed returns a PEAR_Error on failure, or a string on success
     */
    function queryRADB($ipAddress) {
        return $this->query($ipAddress, $this->_nicServers["MNICHOST"]);
    }
    // }}}

    // {{{ _chooseServer()
    /**
     * Determines the correct server to connect to based upon the domin
     *
     * @param $domain string IP address or host name
     * @access private
     * @return string whois server host name
     */
    function _chooseServer($domain) {
        if (!strpos($domain, ".")) {
            return $this->_nicServers["NICHOST"];
        }

        $pieces = explode(".", $domain);
        $TLD = $pieces[count($pieces)-1];
        if (is_numeric($TLD)) {
            $whoisServer = $this->_nicServers["ANICHOST"];
        } else {
            $whoisServer = $TLD . $this->_nicServers["QNICHOST_TAIL"];
        }

        return $whoisServer;
    }
    // }}}

    // {{{ _connect()
    /**
     * Connects to the whois server and retrieves domain information
     *
     * @param $nicServer string FQDN of whois server to query
     * @param $domain string domain name to query
     * @access private
     * @return mixed returns a PEAR_Error on failure, string of whois data on success
     */
    function _connect($nicServer, $domain) {
        include_once 'Net/Socket.php';

        if (PEAR::isError($socket = new Net_Socket())) {
            return new PEAR_Error($this->_errorCodes[010]);
        }
        if (PEAR::isError($socket->connect($nicServer, getservbyname('whois', 'tcp')))) {
            return new PEAR_Error($this->_errorCodes[011]);
        }
        $socket->setBlocking(false);
        if (PEAR::isError($socket->writeLine($domain))) {
            return new PEAR_Error($this->_errorCodes[012]);
        }

        $nHost = null;
        $whoisData = $socket->readAll();
        if (PEAR::isError($whoisData)) {
            return new PEAR_Error($this->_errorCodes[013]);
        }

        $data = explode("\n", $whoisData);
        foreach ($data as $line) {
            $line = rtrim($line);

            // check for whois server redirection
            if (!isset($nHost)) {
                if (preg_match("/" . $this->_whoisServerID . "(.*)/", $line, $matches)) {
                    $nHost = $matches[1];
                } elseif ($nicServer == $this->_nicServers["ANICHOST"]) {
                    foreach ($this->_ipNicServers as $ipNicServer) {
                        if (strstr($line, $ipNicServer)) {
                            $nHost = $ipNicServer;
                        }
                    }
                }
            }
        }

        // this should fail, but we'll call it anyway and ignore the error
        $socket->disconnect();

        if ($nHost) {
            $tmpBuffer = $this->_connect($nHost, $domain);
            if (PEAR::isError($tmpBuffer)) {
                return $tmpBuffer;
            }
            $whoisData .= $tmpBuffer;
        }

        return $whoisData;
    }
    // }}}
}
?>