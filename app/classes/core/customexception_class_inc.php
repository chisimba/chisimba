<?php

/**
 * Class that extends the SPL to handle exceptions in a custom way
 *
 * @author Paul Scott
 * @category Chisimba
 * @package core
 * @copyright AVOIR
 * @licence GNU/GPL
 */

class customException extends Exception
{
	public $uri;

	// constructor
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

    public function dbDeath($msg)
    {
    	$usrmsg = urlencode($msg[0]);
    	$devmsg = urlencode($msg[1]);
    	$this->uri = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'] . "?module=errors&action=dberror&usrmsg=".$usrmsg."&devmsg=".$devmsg;
    	header("Location: $this->uri");
    }

    function cleanUp()
    {
        // generic cleanup code here
        //for now, we can output a message?

    }

}
?>