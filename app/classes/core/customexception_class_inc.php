<?php
/**
 * Class that extends the SPL to handle exceptions in a custom way
 *
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @category  Chisimba
 * @package   Core
 * @copyright 2007  AVOIR
 * @license   http://gnu.org GPL 
 */

class customException extends Exception
{
	public $uri;
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
     * @todo fix this function up for multilingual and prettiness
     * @access public
     * @param void
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
     * @param call stack $msg
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
     * @param void
     * @return void
     */
    public function cleanUp()
    {
        // generic cleanup code here
        //for now, we can output a message?

    }



}
?>