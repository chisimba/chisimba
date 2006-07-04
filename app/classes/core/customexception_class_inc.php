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
    }
}
}