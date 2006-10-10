<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Sebastian Nohn <sebastian@nohn.net>                         |
// +----------------------------------------------------------------------+
//
// $Id$
//

require_once 'PEAR.php';
require_once 'Net/Socket.php';

/**
 * PEAR's Net_Finger:: interface.
 *
 * Provides functions useful for Finger-Queries.
 *
 * @version $Revision$
 * @author Sebastian Nohn <sebastian@nohn.net>
 * @package Net
 */
class Net_Finger
{

    /**
     * Implements Net_Finger::query() function using PEAR's socket functions
     *
     * @param 	string	$server The finger-server to query
     * @param 	string  $query	The finger database object to lookup
     * @return 	mixed  			The data returned from the finger-server as string
     *                          or a PEAR_Error ( see Net_Socket for error codes)
     */   
    function query($server, $query)
    {
        $socket = new Net_Socket;
        if( PEAR::isError( $sockerror = $socket->connect($server, 79))) {
            $data = new PEAR_Error( "Error connecting to $server ( Net_Socket says: ".
                                    $sockerror->getMessage().")", $sockerror->getCode());
        } else { 
            $query .= "\n"; 
            $socket->write($query); 
            $data = $socket->read(16384); 
            $socket->disconnect();
        }         
        return $data; 
    } 
} 
?>
