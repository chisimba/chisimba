<?php
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author: Laurent Laville <pear@laurent-laville.org>                   |
// | Credit: Tomas V.V.Cox <cox@vulcanonet.com>                           |
// |         to reuse part of his code and idea from package HTTP_upload  |
// |         see: http://pear.php.net/package/HTTP_Upload                 |
// +----------------------------------------------------------------------+
//
// $Id$

require_once 'PEAR.php';

/**
 * The FTP_Upload class provides an easy and secure managment 
 * of files to upload to your ftp server.
 *
 * @version    1.1
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @author     Tomas V.V.Cox <cox@vulcanonet.com>
 * @access     public
 * @category   HTML
 * @package    HTML_Progress
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 */

class FTP_Upload
{
    /**
     * It's a common security risk in pages who has the upload dir
     * facility. You should restrict file kind to upload.
     *
     * @var        array
     * @since      1.1
     * @access     private
     * @see        setValidExtensions()
     */
    var $_extensions_check = array('jpg', 'jpeg', 'gif', 'png', 'pdf', 'tar', 'zip', 'gz');

    /**
     * A list of files ready to upload on a ftp server.
     *
     * @var        array
     * @since      1.1
     * @access     private
     * @see        setFiles()
     */
    var $_files = array();

    /**
     * FTP stream resource used for communications
     *
     * @var        resource
     * @since      1.1
     * @access     private
     */
    var $_conn;


    /**
     * The FTP upload class constructor
     *
     * @since      1.1
     * @access     public
     */
    function FTP_Upload()
    {
        if (function_exists('version_compare') && version_compare(phpversion(), '4.3', 'ge')) {
            // some ftp functions requires PHP 4.3+
        } else {
            trigger_error('PHP version 4.3 or better is required', E_USER_ERROR);
	}
	
	if (!in_array('ftp', get_loaded_extensions())) {
            trigger_error('extension ftp is unavailable', E_USER_ERROR);
	}
    }

    /**
     * Restricts the valid extensions on file uploads.
     *
     * @param      mixed     $exts          File extensions to validate
     *
     * @return     void
     * @since      1.1
     * @access     public
     */
    function setValidExtensions($exts)
    {
        if (is_array($exts)) {
            $this->_extensions_check = $exts;
        } else {
            $this->_extensions_check = array();
            array_push($this->_extensions_check, $exts);
        }
    }

    /**
     * Set a list of files to upload on the ftp server.
     *
     * @param      mixed     $files         List of files to transfer to FTP server.
     * @param      boolean   $check         (optional) Restrict files to valid extensions only.
     *
     * @return     void
     * @since      1.1
     * @access     public
     */
    function setFiles($files, $check = true)
    {
        $this->_files = $inputs = array();
        if (is_array($files)) {
            $inputs = $files;
        } else {
            array_push($inputs, $files);
        }

        foreach ($inputs as $file) {
            if ($check) {
                $info = pathinfo($file);
                if (in_array($info['extension'], $this->_extensions_check) && file_exists($file)) {
                    $this->_files[] = $file;
                }
            } else {
                $this->_files[] = $file;
	    }
        }
    }

    /**
     * Connect on a remote FTP server and login as $user.
     *
     * @param      string    $host          FTP server to connect to.
     * @param      string    $user          Username.
     * @param      string    $pass          Password.
     *
     * @return     mixed                    TRUE on success, and PEAR_Error on failure
     * @since      1.1
     * @access     public
     */
    function logon($user, $pass, $host, $port = 21, $timeout = 90)
    {
        $conn = $this->_connect($host, $port, $timeout);
        if (PEAR::isError($conn)) {
            return $conn;
        }
        $this->_conn = $conn;

        $logs = $this->_login($conn, $user, $pass);
        if (PEAR::isError($logs)) {
            $this->logoff();            
            return $logs;
        }
    }

    /**
     * Disconnect from a remote FTP server.
     * (Timeout is default set to 90 sec.)
     *
     * @return     void
     * @since      1.1
     * @access     public
     */
    function logoff()
    {
        @ftp_close($this->_conn);
        unset($this->_conn);
    }

    /**
     * Uploads the files synchronously into directory $dest on remote host.
     *
     * @param      string    $dest          Changes from current to the specified directory.
     *
     * @return     mixed                    PEAR_Error on failure, array (null) of untransfered files
     * @since      1.1
     * @access     public
     */
    function moveTo($dest)
    {
        $dir = $this->_changeDir($dest);
        if (PEAR::isError($dir)) {
            return $dir;
        }
        $nomove = array();   // files not transfered on remote host

        foreach ($this->_files as $file) {
        	
            $ret = ftp_put($this->_conn, basename($file), $file, FTP_BINARY);
            if (!$ret) {
                $nomove[] = $file;
            }
        }
        return $nomove;
    }

    /**
     * Changes directories on a FTP server.
     *
     * @param      string    $dest          Changes from current to the specified directory.
     *
     * @return     mixed                    TRUE on success, and PEAR_Error on failure
     * @since      1.1
     * @access     private
     */
    function _changeDir($dest)
    {
        if (!isset($this->_conn)) {
            return PEAR::raiseError('You should logs in fisrt'); 
        }
        $chg = ftp_chdir($this->_conn, $dest);
        if (!$chg) {
            return PEAR::raiseError('Couldn\'t change to directory ' . $dest); 
        }
        return true;
    }

    /**
     * Opens an FTP connection on remote host.
     *
     * @param      string    $host          Hostname.
     * @param      integer   $port          (optional) an alternate port to connect to.
     * @param      integer   $timeout       (optional) the timeout for all subsequent network operations.
     *
     * @return     mixed                    FTP stream on success, and PEAR_Error on failure
     * @since      1.1
     * @access     private
     */
    function _connect($host, $port = 21, $timeout = 90)
    {
        $conn = ftp_connect($host, $port, $timeout);
        if ($conn === false) {
            return PEAR::raiseError('Couldn\'t connect to ' . $host); 
        }
        return $conn;
    }

    /**
     * Logs in to an FTP connection.
     *
     * @param      resource  $conn          FTP stream resource.
     * @param      string    $user          Username.
     * @param      string    $pass          Password.
     *
     * @return     mixed                    TRUE on success, and PEAR_Error on failure
     * @since      1.1
     * @access     private
     */
    function _login($conn, $user, $pass)
    {
        $logs = ftp_login($conn, $user, $pass);
        if (!$logs) {
            return PEAR::raiseError('Couldn\'t connect as ' . $user); 
        }
        return true;
    }
}
?>