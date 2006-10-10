<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
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
// |          Tomas V.V.Cox <cox@idecnet.com>                             |
// |          Jan Lehnardt  <jan@php.net>                                 |
// |          Kai Schröder <k.schroeder@php.net>                          |
// +----------------------------------------------------------------------+
//
// $Id$

require_once "PEAR.php";
require_once "OS/Guess.php";

define('NET_PING_FAILED_MSG',                     'execution of ping failed'        );
define('NET_PING_HOST_NOT_FOUND_MSG',             'unknown host'                    );
define('NET_PING_INVALID_ARGUMENTS_MSG',          'invalid argument array'          );
define('NET_PING_CANT_LOCATE_PING_BINARY_MSG',    'unable to locate the ping binary');
define('NET_PING_RESULT_UNSUPPORTED_BACKEND_MSG', 'Backend not Supported'           );

define('NET_PING_FAILED',                     0);
define('NET_PING_HOST_NOT_FOUND',             1);
define('NET_PING_INVALID_ARGUMENTS',          2);
define('NET_PING_CANT_LOCATE_PING_BINARY',    3);
define('NET_PING_RESULT_UNSUPPORTED_BACKEND', 4);


/* TODO
 *
 * - add Net_Ping_Result parser for:
 *   + IRIX64
 *   + OSF1
 *   + BSD/OS
 *   + OpenBSD
 * - fix Net_Ping::checkHost()
 * - reset result buffer
 */

/**
* Wrapper class for ping calls
*
* Usage:
*
* <?php
*   require_once "Net/Ping.php";
*   $ping = Net_Ping::factory();
*   if(PEAR::isError($ping)) {
*     echo $ping->getMessage();
*   } else {
*     $ping->setArgs(array('count' => 2));
*     var_dump($ping->ping('example.com'));
*   }
* ?>
*
* @author   Jan Lehnardt <jan@php.net>
* @version  $Revision$
* @package  Net
* @access   public
*/
class Net_Ping
{
    /**
    * Location where the ping program is stored
    *
    * @var string
    * @access private
    */
    var $_ping_path = "";

    /**
    * Array with the result from the ping execution
    *
    * @var array
    * @access private
    */
    var $_result = array();

    /**
    * OS_Guess instance
    *
    * @var object
    * @access private
    */
    var $_OS_Guess = "";

    /**
    * OS_Guess->getSysname result
    *
    * @var string
    * @access private
    */
    var $_sysname = "";

    /**
    * Ping command arguments
    *
    * @var array
    * @access private
    */
    var $_args = array();

    /**
    * Indicates if an empty array was given to setArgs
    *
    * @var boolean
    * @access private
    */
    var $_noArgs = true;

    /**
    * Contains the argument->option relation
    *
    * @var array
    * @access private
    */
    var $_argRelation = array();

    /**
    * Constructor for the Class
    *
    * @access private
    */
    function Net_Ping($ping_path, $sysname)
    {
        $this->_ping_path = $ping_path;
        $this->_sysname   = $sysname;
        $this->_initArgRelation();
    } /* function Net_Ping() */

    /**
    * Factory for Net_Ping
    *
    * @access public
    */
    function factory()
    {
        $ping_path = '';

        $sysname = Net_Ping::_setSystemName();

        if (($ping_path = Net_Ping::_setPingPath($sysname)) == NET_PING_CANT_LOCATE_PING_BINARY) {
            return PEAR::throwError(NET_PING_CANT_LOCATE_PING_BINARY_MSG, NET_PING_CANT_LOCATE_PING_BINARY);
        } else {
            return new Net_Ping($ping_path, $sysname);
        }
    } /* function factory() */

    /** 
     * Resolve the system name
     *
     * @access private
     */
    function _setSystemName()
    {
        $OS_Guess  = new OS_Guess;
        $sysname   = $OS_Guess->getSysname();

        /* Nasty hack for Debian, as it uses a custom ping version */
        if ('linux' == $sysname) {
            if (file_exists('/etc/debian_version')) {
                $sysname = 'debian';
            }
        }

        return $sysname;
        
    } /* function _setSystemName */

    /**
    * Set the arguments array
    *
    * @param array $args Hash with options
    * @return mixed true or PEAR_error
    * @access public
    */
    function setArgs($args)
    {
        if (!is_array($args)) {
            return PEAR::throwError(NET_PING_INVALID_ARGUMENTS_MSG, NET_PING_INVALID_ARGUMENTS);
        }

        $this->_setNoArgs($args);

        $this->_args = $args;

        return true;
    } /* function setArgs() */

    /**
    * Set the noArgs flag
    *
    * @param array $args Hash with options
    * @return void
    * @access private
    */
    function _setNoArgs($args)
    {
        if (0 == count($args)) {
            $this->_noArgs = true;
        } else {
            $this->_noArgs = false;
        }
    } /* function _setNoArgs() */

    /**
    * Sets the system's path to the ping binary
    *
    * @access private
    */
    function _setPingPath($sysname)
    {
        $status    = '';
        $output    = array();
        $ping_path = '';

        if ("windows" == $sysname) {
            return "ping";
        } else {
            $ping_path = exec("which ping", $output, $status);
            if (0 != $status) {
                return NET_PING_CANT_LOCATE_PING_BINARY;
            } else {
                return $ping_path;
            }
        }
    } /* function _setPingPath() */

    /**
    * Creates the argument list according to platform differences
    *
    * @return string Argument line
    * @access private
    */
    function _createArgList()
    {
        $retval     = array();

        $timeout    = "";
        $iface      = "";
        $ttl        = "";
        $count      = "";
        $quiet      = "";
        $size       = "";
        $seq        = "";
        $deadline   = "";

        foreach($this->_args AS $option => $value) {
            if(!empty($option) && isset($this->_argRelation[$this->_sysname][$option]) && NULL != $this->_argRelation[$this->_sysname][$option]) {
                ${$option} = $this->_argRelation[$this->_sysname][$option]." ".$value." ";
             }
        }

        switch($this->_sysname) {

        case "sunos":
             if ($size || $count || $iface) {
                 /* $size and $count must be _both_ defined */
                 $seq = " -s ";
                 if ($size == "") {
                     $size = " 56 ";
                 }
                 if ($count == "") {
                     $count = " 5 ";
                 }
             }
             $retval['pre'] = $iface.$seq.$ttl;
             $retval['post'] = $size.$count;
             break;

        case "freebsd":
             $retval['pre'] = $quiet.$count.$ttl.$timeout;
             $retval['post'] = "";
             break;

        case "darwin":
             $retval['pre'] = $count.$timeout.$size;
             $retval['post'] = "";
             break;

        case "netbsd":
             $retval['pre'] = $quiet.$count.$iface.$size.$ttl.$timeout;
             $retval['post'] = "";
             break;

        case "linux":
             $retval['pre'] = $quiet.$deadline.$count.$ttl.$size.$timeout;
             $retval['post'] = "";
             break;

        case "debian":
             $retval['pre'] = $quiet.$count.$ttl.$size.$timeout;
             $retval['post'] = "";

             /* undo nasty debian hack*/
             $this->_sysname = 'linux';
             break;

        case "windows":
             $retval['pre'] = $count.$ttl.$timeout;
             $retval['post'] = "";
             break;

        case "hpux":
             $retval['pre'] = $ttl;
             $retval['post'] = $size.$count;
             break;

        case "aix":
            $retval['pre'] = $count.$timeout.$ttl.$size;
            $retval['post'] = "";
            break;

        default:
             $retval['pre'] = "";
             $retval['post'] = "";
             break;
        }
        return($retval);
    }  /* function _createArgList() */

    /**
    * Execute ping
    *
    * @param  string    $host   hostname
    * @return mixed  String on error or array with the result
    * @access public
    */
    function ping($host)
    {
        
        if($this->_noArgs) {
            $this->setArgs(array('count' => 3));
        }

        $argList = $this->_createArgList();
        $cmd = $this->_ping_path." ".$argList['pre']." ".$host." ".$argList['post'];
        exec($cmd, $this->_result);

        if (!is_array($this->_result)) {
            return PEAR::throwError(NET_PING_FAILED_MSG, NET_PING_FAILED);
        }

        if (count($this->_result) == 0) {
            return PEAR::throwError(NET_PING_HOST_NOT_FOUND_MSG, NET_PING_HOST_NOT_FOUND);
        } else {
            return Net_Ping_Result::factory($this->_result, $this->_sysname);
        }
    } /* function ping() */

    /**
    * Check if a host is up by pinging it
    *
    * @param string $host   The host to test
    * @param bool $severely If some of the packages did reach the host
    *                       and severely is false the function will return true
    * @return bool True on success or false otherwise
    *
    */
    function checkHost($host, $severely = true)
    {
    	$matches = array();
    	
        $this->setArgs(array("count" => 10,
                             "size"  => 32,
                             "quiet" => null,
                             "deadline" => 10
                             )
                       );
        $res = $this->ping($host);
        if (PEAR::isError($res)) {
            return false;
        }
        if (!preg_match_all('|\d+|', $res[3], $matches) || count($matches[0]) < 3) {
            ob_start();
            $rep = ob_get_contents();
            ob_end_clean();
            trigger_error("Output format seems not to be supported, please report ".
                          "the following to pear-dev@lists.php.net, including your ".
                          "version of ping:\n $rep");
            return false;
        }
        if ($matches[0][1] == 0) {
            return false;
        }
        // [0] => transmitted, [1] => received
        if ($matches[0][0] != $matches[0][1] && $severely) {
            return false;
        }
        return true;
    } /* function checkHost() */

    /**
    * Output errors with PHP trigger_error(). You can silence the errors
    * with prefixing a "@" sign to the function call: @Net_Ping::ping(..);
    *
    * @param mixed $error a PEAR error or a string with the error message
    * @return bool false
    * @access private
    * @author Kai Schröder <k.schroeder@php.net>
    */
    function _raiseError($error)
    {
        if (PEAR::isError($error)) {
            $error = $error->getMessage();
        }
        trigger_error($error, E_USER_WARNING);
        return false;
    }  /* function _raiseError() */

    /**
    * Creates the argument list according to platform differences
    *
    * @return string Argument line
    * @access private
    */
    function _initArgRelation()
    {
        $this->_argRelation["sunos"] = array(
                                             "timeout"   => NULL,
                                             "ttl"       => "-t",
                                             "count"     => " ",
                                             "quiet"     => "-q",
                                             "size"      => " ",
                                             "iface"     => "-i"
                                             );

        $this->_argRelation["freebsd"] = array (
                                                "timeout"   => "-t",
                                                "ttl"       => "-m",
                                                "count"     => "-c",
                                                "quiet"     => "-q",
                                                "size"      => NULL,
                                                "iface"     => NULL
                                                );

        $this->_argRelation["netbsd"] = array (
                                               "timeout"   => "-w",
                                               "iface"     => "-I",
                                               "ttl"       => "-T",
                                               "count"     => "-c",
                                               "quiet"     => "-q",
                                               "size"      => "-s"
                                               );

        $this->_argRelation["openbsd"] = array (
                                                "timeout"   => "-w",
                                                "iface"     => "-I",
                                                "ttl"       => "-t",
                                                "count"     => "-c",
                                                "quiet"     => "-q",
                                                "size"      => "-s"
                                                );

        $this->_argRelation["darwin"] = array (
                                               "timeout"   => "-t",
                                               "iface"     => NULL,
                                               "ttl"       => NULL,
                                               "count"     => "-c",
                                               "quiet"     => "-q",
                                               "size"      => NULL
                                               );

        $this->_argRelation["linux"] = array (
                                              "timeout"   => "-t",
                                              "iface"     => NULL,
                                              "ttl"       => "-m",
                                              "count"     => "-c",
                                              "quiet"     => "-q",
                                              "size"      => "-s",
                                              "deadline"  => "-w"
                                              );

        $this->_argRelation["debian"] = array (
                                              "timeout"   => "-t",
                                              "iface"     => NULL,
                                              "ttl"       => "-m",
                                              "count"     => "-c",
                                              "quiet"     => "-q",
                                              "size"      => "-s",
                                              );

        $this->_argRelation["windows"] = array (
                                                "timeout"   => "-w",
                                                "iface"     => NULL,
                                                "ttl"       => "-i",
                                                "count"     => "-n",
                                                "quiet"     => NULL,
                                                "size"      => "-l"
                                                 );

        $this->_argRelation["hpux"] = array (
                                             "timeout"   => NULL,
                                             "iface"     => NULL,
                                             "ttl"       => "-t",
                                             "count"     => "-n",
                                             "quiet"     => NULL,
                                             "size"      => " "
                                             );

        $this->_argRelation["aix"] = array (
                                            "timeout"   => "-i",
                                            "iface"     => NULL,
                                            "ttl"       => "-T",
                                            "count"     => "-c",
                                            "quiet"     => NULL,
                                            "size"      => "-s"
                                            );
    }  /* function _initArgRelation() */
} /* class Net_Ping */

/**
* Container class for Net_Ping results
*
* @author   Jan Lehnardt <jan@php.net>
* @version  $Revision$
* @package  Net
* @access   private
*/
class Net_Ping_Result
{
    /**
    * ICMP sequence number and associated time in ms
    *
    * @var array
    * @access private
    */
    var $_icmp_sequence = array(); /* array($sequence_number => $time ) */

    /**
    * The target's IP Address
    *
    * @var string
    * @access private
    */
    var $_target_ip;

    /**
    * Number of bytes that are sent with each ICMP request
    *
    * @var int
    * @access private
    */
    var $_bytes_per_request;

    /**
    * The total number of bytes that are sent with all ICMP requests
    *
    * @var int
    * @access private
    */
    var $_bytes_total;

    /**
    * The ICMP request's TTL
    *
    * @var int
    * @access private
    */
    var $_ttl;

    /**
    * The raw Net_Ping::result
    *
    * @var array
    * @access private
    */
    var $_raw_data = array();

    /**
    * The Net_Ping::_sysname
    *
    * @var int
    * @access private
    */
    var $_sysname;

    /**
    * Statistical information about the ping
    *
    * @var int
    * @access private
    */
    var $_round_trip = array(); /* array('min' => xxx, 'avg' => yyy, 'max' => zzz) */


    /**
    * Constructor for the Class
    *
    * @access private
    */
    function Net_Ping_Result($result, $sysname)
    {
        $this->_raw_data = $result;
        $this->_sysname  = $sysname;

        $this->_parseResult();
    } /* function Net_Ping_Result() */

    /**
    * Factory for Net_Ping_Result
    *
    * @access public
    * @param array $result Net_Ping result
    * @param string $sysname OS_Guess::sysname
    */
    function factory($result, $sysname)
    {
        if (!Net_Ping_Result::_prepareParseResult($sysname)) {
            return PEAR::throwError(NET_PING_RESULT_UNSUPPORTED_BACKEND_MSG, NET_PING_RESULT_UNSUPPORTED_BACKEND);
        } else {
            return new Net_Ping_Result($result, $sysname);
        }
    }  /* function factory() */

	/**
	* Preparation method for _parseResult
	*
	* @access private
	* @param string $sysname OS_Guess::sysname
	* $return bool
	*/
	function _prepareParseResult($sysname)
	{
        $parse_methods = array_values(array_map('strtolower', get_class_methods('Net_Ping_Result')));

		return in_array('_parseresult'.$sysname, $parse_methods);
	} /* function _prepareParseResult() */

    /**
    * Delegates the parsing routine according to $this->_sysname
    *
    * @access private
    */
    function _parseResult()
    {
        call_user_func(array(&$this, '_parseResult'.$this->_sysname));
    } /* function _parseResult() */

    /**
    * Parses the output of Linux' ping command
    *
    * @access private
    * @see _parseResultlinux
    */
    function _parseResultlinux()
    {
        $raw_data_len   = count($this->_raw_data);
        $icmp_seq_count = $raw_data_len - 4;

        /* loop from second elment to the fifths last */
        for($idx = 1; $idx < $icmp_seq_count; $idx++) {
                $parts = explode(' ', $this->_raw_data[$idx]);
                $this->_icmp_sequence[substr(@$parts[4], 9, strlen(@$parts[4]))] = substr(@$parts[6], 5, strlen(@$parts[6]));
            }
        $this->_bytes_per_request = $parts[0];
        $this->_bytes_total       = (int)$parts[0] * $icmp_seq_count;
        $this->_target_ip         = substr($parts[3], 0, -1);
        $this->_ttl               = substr($parts[5], 4, strlen($parts[3]));

        $stats = explode(',', $this->_raw_data[$raw_data_len - 2]);
        $transmitted = explode(' ', $stats[0]);
        $this->_transmitted = $transmitted[0];

        $received = explode(' ', $stats[1]);
        $this->_received = $received[1];

        $loss = explode(' ', $stats[2]);
        $this->_loss = (int)$loss[1];

        $round_trip = explode('/', str_replace('=', '/', substr($this->_raw_data[$raw_data_len - 1], 0, -3)));

        /* if mdev field exists, shift input one unit left */
        if (strpos($this->_raw_data[$raw_data_len - 1], 'mdev')) {
            /* do not forget the rtt field */
            $this->_round_trip['min']    = ltrim($round_trip[5]);
            $this->_round_trip['avg']    = $round_trip[6];
            $this->_round_trip['max']    = $round_trip[7];
        } else {
            $this->_round_trip['min']    = ltrim($round_trip[4]);
            $this->_round_trip['avg']    = $round_trip[5];
            $this->_round_trip['max']    = $round_trip[6];
        }
    } /* function _parseResultlinux() */

    /**
    * Parses the output of NetBSD's ping command
    *
    * @access private
    * @see _parseResultfreebsd
    */
    function _parseResultnetbsd()
    {
        $this->_parseResultfreebsd();
    } /* function _parseResultnetbsd() */
  
    /**
    * Parses the output of Darwin's ping command
    *
    * @access private
    */
    function _parseResultdarwin()
    {
        $raw_data_len   = count($this->_raw_data);
        $icmp_seq_count = $raw_data_len - 5;

        /* loop from second elment to the fifths last */
        for($idx = 1; $idx < $icmp_seq_count; $idx++) {
            $parts = explode(' ', $this->_raw_data[$idx]);
            $this->_icmp_sequence[substr($parts[4], 9, strlen($parts[4]))] = substr($parts[6], 5, strlen($parts[6]));
        }

        $this->_bytes_per_request = (int)$parts[0];
        $this->_bytes_total       = (int)($this->_bytes_per_request * $icmp_seq_count);
        $this->_target_ip         = substr($parts[3], 0, -1);
        $this->_ttl               = (int)substr($parts[5], 4, strlen($parts[3]));

        $stats = explode(',', $this->_raw_data[$raw_data_len - 2]);
        $transmitted = explode(' ', $stats[0]);
        $this->_transmitted = (int)$transmitted[0];

        $received = explode(' ', $stats[1]);
        $this->_received = (int)$received[1];

        $loss = explode(' ', $stats[2]);
        $this->_loss = (int)$loss[1];

        $round_trip = explode('/', str_replace('=', '/', substr($this->_raw_data[$raw_data_len - 1], 0, -3)));

        $this->_round_trip['min']    = (float)ltrim($round_trip[3]);
        $this->_round_trip['avg']    = (float)$round_trip[4];
        $this->_round_trip['max']    = (float)$round_trip[5];
        $this->_round_trip['stddev'] = NULL; /* no stddev */
    } /* function _parseResultdarwin() */

    /**
    * Parses the output of HP-UX' ping command
    *
    * @access private
    */   
    function _parseResulthpux()
    {
        $parts          = array();
        $raw_data_len   = count($this->_raw_data);
        $icmp_seq_count = $raw_data_len - 5;

        /* loop from second elment to the fifths last */
        for($idx = 1; $idx <= $icmp_seq_count; $idx++) {
            $parts = explode(' ', $this->_raw_data[$idx]);
            $this->_icmp_sequence[(int)substr($parts[4], 9, strlen($parts[4]))] = (int)substr($parts[5], 5, strlen($parts[5]));
        }
        $this->_bytes_per_request = (int)$parts[0];
        $this->_bytes_total       = (int)($parts[0] * $icmp_seq_count);
        $this->_target_ip         = NULL; /* no target ip */
        $this->_ttl               = NULL; /* no ttl */

        $stats = explode(',', $this->_raw_data[$raw_data_len - 2]);
        $transmitted = explode(' ', $stats[0]);
        $this->_transmitted = (int)$transmitted[0];

        $received = explode(' ', $stats[1]);
        $this->_received = (int)$received[1];

        $loss = explode(' ', $stats[2]);
        $this->_loss = (int)$loss[1];

        $round_trip = explode('/', str_replace('=', '/',$this->_raw_data[$raw_data_len - 1]));

        $this->_round_trip['min']    = (int)ltrim($round_trip[3]);
        $this->_round_trip['avg']    = (int)$round_trip[4];
        $this->_round_trip['max']    = (int)$round_trip[5];
        $this->_round_trip['stddev'] = NULL; /* no stddev */
    } /* function _parseResulthpux() */

    /**
    * Parses the output of AIX' ping command
    *
    * @access private
    */   
    function _parseResultaix()
    {
        $parts          = array();
        $raw_data_len   = count($this->_raw_data);
        $icmp_seq_count = $raw_data_len - 5;

        /* loop from second elment to the fifths last */
        for($idx = 1; $idx <= $icmp_seq_count; $idx++) {
            $parts = explode(' ', $this->_raw_data[$idx]);
            $this->_icmp_sequence[(int)substr($parts[4], 9, strlen($parts[4]))] = (int)substr($parts[6], 5, strlen($parts[6]));
        }
        $this->_bytes_per_request = (int)$parts[0];
        $this->_bytes_total       = (int)($parts[0] * $icmp_seq_count);
        $this->_target_ip         = substr($parts[3], 0, -1);
        $this->_ttl               = (int)substr($parts[5], 4, strlen($parts[3]));

        $stats = explode(',', $this->_raw_data[$raw_data_len - 2]);
        $transmitted = explode(' ', $stats[0]);
        $this->_transmitted = (int)$transmitted[0];

        $received = explode(' ', $stats[1]);
        $this->_received = (int)$received[1];

        $loss = explode(' ', $stats[2]);
        $this->_loss = (int)$loss[1];

        $round_trip = explode('/', str_replace('=', '/',$this->_raw_data[$raw_data_len - 1]));

        $this->_round_trip['min']    = (int)ltrim($round_trip[3]);
        $this->_round_trip['avg']    = (int)$round_trip[4];
        $this->_round_trip['max']    = (int)$round_trip[5];
        $this->_round_trip['stddev'] = NULL; /* no stddev */
    } /* function _parseResultaix() */

    /**
    * Parses the output of FreeBSD's ping command
    *
    * @access private
    */
    function _parseResultfreebsd()
    {
        $raw_data_len   = count($this->_raw_data);
        $icmp_seq_count = $raw_data_len - 5;

        /* loop from second elment to the fifths last */
        for($idx = 1; $idx < $icmp_seq_count; $idx++) {
           $parts = explode(' ', $this->_raw_data[$idx]);
           $this->_icmp_sequence[substr($parts[4], 9, strlen($parts[4]))] = substr($parts[6], 5, strlen($parts[6]));
        }

        $this->_bytes_per_request = (int)$parts[0];
        $this->_bytes_total       = (int)($parts[0] * $icmp_seq_count);
        $this->_target_ip         = substr($parts[3], 0, -1);
        $this->_ttl               = (int)substr($parts[5], 4, strlen($parts[3]));

        $stats = explode(',', $this->_raw_data[$raw_data_len - 2]);
        $transmitted = explode(' ', $stats[0]);
        $this->_transmitted = (int)$transmitted[0];

        $received = explode(' ', $stats[1]);
        $this->_received = (int)$received[1];

        $loss = explode(' ', $stats[2]);
        $this->_loss = (int)$loss[1];

        $round_trip = explode('/', str_replace('=', '/', substr($this->_raw_data[$raw_data_len - 1], 0, -3)));

        $this->_round_trip['min']    = (float)ltrim($round_trip[4]);
        $this->_round_trip['avg']    = (float)$round_trip[5];
        $this->_round_trip['max']    = (float)$round_trip[6];
        $this->_round_trip['stddev'] = (float)$round_trip[7];
    } /* function _parseResultfreebsd() */

    /**
    * Parses the output of Windows' ping command
    *
    * @author Kai Schröder <k.schroeder@php.net>
    * @access private
    */
    function _parseResultwindows()
    {
        $raw_data_len   = count($this->_raw_data);
        $icmp_seq_count = $raw_data_len - 8;

        /* loop from fourth elment to the sixths last */
        for($idx = 1; $idx <= $icmp_seq_count; $idx++) {
            $parts = explode(' ', $this->_raw_data[$idx + 2]);
            $this->_icmp_sequence[$idx - 1] = (int)substr(end(split('=', $parts[4])), 0, -2);

            $ttl = (int)substr($parts[5], 4, strlen($parts[3]));
            if ($ttl > 0 && $this->_ttl == 0) {
                $this->_ttl = $ttl;
            }
        }

       
        $parts = explode(' ', $this->_raw_data[1]);
        $this->_bytes_per_request = (int)$parts[4];
        $this->_bytes_total       = $this->_bytes_per_request * $icmp_seq_count;
        $this->_target_ip         = substr(trim($parts[2]), 1, -1);

        $stats = explode(',', $this->_raw_data[$raw_data_len - 3]);
        $transmitted = explode('=', $stats[0]);
        $this->_transmitted = (int)$transmitted[1];

        $received = explode('=', $stats[1]);
        $this->_received = (int)$received[1];

        $loss = explode('=', $stats[2]);
        $this->_loss = (int)$loss[1];

        $round_trip = explode(',', str_replace('=', ',', $this->_raw_data[$raw_data_len - 1]));
        $this->_round_trip['min'] = (int)substr(trim($round_trip[1]), 0, -2);
        $this->_round_trip['max'] = (int)substr(trim($round_trip[3]), 0, -2);
        $this->_round_trip['avg'] = (int)substr(trim($round_trip[5]), 0, -2);
    } /* function _parseResultwindows() */

    /**
    * Returns a Ping_Result property
    *
    * @param string $name property name
    * @return mixed property value
    * @access public
    */
    function getValue($name)
    {
        return isset($this->$name)?$this->$name:'';
    } /* function getValue() */

    /**
    * Accessor for $this->_target_ip;
    *
    * @return string IP address
    * @access public
    * @see Ping_Result::_target_ip
    */
    function getTargetIp()
    {
    	return $this->_target_ip;
    } /* function getTargetIp() */

    /**
    * Accessor for $this->_icmp_sequence;
    *
    * @return array ICMP sequence
    * @access private
    * @see Ping_Result::_icmp_sequence
    */
    function getICMPSequence()
    {
    	return $this->_icmp_sequence;
    } /* function getICMPSequencs() */

    /**
    * Accessor for $this->_bytes_per_request;
    *
    * @return int bytes per request
    * @access private
    * @see Ping_Result::_bytes_per_request
    */
    function getBytesPerRequest()
    {
    	return $this->_bytes_per_request;
    } /* function getBytesPerRequest() */

    /**
    * Accessor for $this->_bytes_total;
    *
    * @return int total bytes
    * @access private
    * @see Ping_Result::_bytes_total
    */
    function getBytesTotal()
    {
    	return $this->_bytes_total;
    } /* function getBytesTotal() */

    /**
    * Accessor for $this->_ttl;
    *
    * @return int TTL
    * @access private
    * @see Ping_Result::_ttl
    */
    function getTTL()
    {
    	return $this->_ttl;
    } /* function getTTL() */

    /**
    * Accessor for $this->_raw_data;
    *
    * @return array raw data
    * @access private
    * @see Ping_Result::_raw_data
    */
    function getRawData()
    {
    	return $this->_raw_data;
    } /* function getRawData() */

    /**
    * Accessor for $this->_sysname;
    *
    * @return string OS_Guess::sysname
    * @access private
    * @see Ping_Result::_sysname
    */
    function getSystemName()
    {
    	return $this->_sysname;
    } /* function getSystemName() */

    /**
    * Accessor for $this->_round_trip;
    *
    * @return array statistical information
    * @access private
    * @see Ping_Result::_round_trip
    */
    function getRoundTrip()
    {
    	return $this->_round_trip;
    } /* function getRoundTrip() */

    /**
    * Accessor for $this->_round_trip['min'];
    *
    * @return array statistical information
    * @access private
    * @see Ping_Result::_round_trip
    */
    function getMin()
    {
    	return $this->_round_trip['min'];
    } /* function getMin() */

    /**
    * Accessor for $this->_round_trip['max'];
    *
    * @return array statistical information
    * @access private
    * @see Ping_Result::_round_trip
    */
    function getMax()
    {
    	return $this->_round_trip['max'];
    } /* function getMax() */

    /**
    * Accessor for $this->_round_trip['stddev'];
    *
    * @return array statistical information
    * @access private
    * @see Ping_Result::_round_trip
    */
    function getStddev()
    {
    	return $this->_round_trip['stddev'];
    } /* function getStddev() */

    /**
    * Accessor for $this->_round_tripp['avg'];
    *
    * @return array statistical information
    * @access private
    * @see Ping_Result::_round_trip
    */
    function getAvg()
    {
    	return $this->_round_trip['avg'];
    } /* function getAvg() */

    /**
    * Accessor for $this->_transmitted;
    *
    * @return array statistical information
    * @access private
    */
    function getTransmitted()
    {
    	return $this->_transmitted;
    } /* function getTransmitted() */

    /**
    * Accessor for $this->_received;
    *
    * @return array statistical information
    * @access private
    */
    function getReceived()
    {
    	return $this->_received;
    } /* function getReceived() */

    /**
    * Accessor for $this->_loss;
    *
    * @return array statistical information
    * @access private
    */
    function getLoss()
    {
    	return $this->_loss;
    } /* function getLoss() */

} /* class Net_Ping_Result */
?>
