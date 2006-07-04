<?php

/**
 * Class that extends the SPL to handle exceptions in a custom way
 * 
 * @author Paul Scott
 * @package core
 * @copyright AVOIR GNU/GPL
 */

class customException extends Exception 
{
	// constructor
    function __construct($m)
    {
        //log the exception
    	log_debug($m);
    	//do the cleanup
        $this->cleanUp();
    }

    function cleanUp()
    {
        // generic cleanup code here
        //for now, we can output a message?
        return "An exception has been encountered, please email your system log file to the Chisimba developers near you...";
    }
}
?>