<?php

/**
 * Custom Exception Handler
 * 
 * CustomException extends the built in SPL Exception Class
 * 
 * PHP version 5
 * 
 * This program is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License 
 * along with this program; if not, write to the 
 * Free Software Foundation, Inc., 
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 * @category  Chisimba
 * @package   core
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */

/**
 * Custom exception handler
 * 
 * Custom exception handler that extends the built in PHP5 Exception class (SPL)
 * 
 * @category  Chisimba
 * @package   core
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class customException extends Exception
{

    /**
     * URI
     * @var    string
     * @access public
     */
	public $uri;

    /**
     * Config Object
     * @var    unknown
     * @access public 
     */
	public $_objConfig;

	/**
	 * Constructor method
	 *
	 * @param call stack $m
	 */
    function __construct($m)
    {
    	$msg = urlencode($m);
    	//log the exception
    	log_debug($m);
    	//do the cleanup
        $this->cleanUp();
        //send out the pretty error page
		$this->diePage($msg);
    }

    /**
     * Method to return a nicely formatted error page for DB errors
     *
     * @access public
     * @param  void  
     * @return string
     */
    public function diePage($msg)
    {
    	$this->uri = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'] . "?module=errors&action=syserr&msg=".$msg;
    	header("Location: $this->uri");
    }

    /**
     * Database error handler
     *
     * @param  call stack $msg
     * @return url 
     */
    public function dbDeath($msg)
    {
    	$usrmsg = urlencode($msg[0]);
    	$devmsg = urlencode($msg[1]);
    	$this->uri = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'] . "?module=errors&action=dberror&usrmsg=".$usrmsg."&devmsg=".$devmsg;
    	header("Location: $this->uri");
    }

    /**
     * Generic clean up function
     *
     * @param  void
     * @return void
     */
    public function cleanUp()
    {
        // generic cleanup code here
        //for now, we can output a message?

    }



}
?>