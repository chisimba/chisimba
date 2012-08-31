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
	// constructor
    function __construct($m)
    {
        //log the exception
    	log_debug($m);
    	//do the cleanup
        $this->cleanUp();
        //send out the pretty error page
		$this->diePage();
    }

    /**
     * Method to return a nicely formatted error page for DB errors
     *
     * @todo fix this function up for multilingual and prettiness
     * @access public
     * @param void
     * @return string
     */
    public function diePage()
    {
        $uri = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
        $message = '<style type="text/css" media="screen">
                    @import url("skins/echo/main.css");
                 </style>

                <div class="featurebox"><h1> An Error has been encountered</h1>
                 Please email your system log file to the Chisimba developers near you ';
        $message .= '<a href='.$uri.'>Back</a></div>';
        print $message;
    }

    function cleanUp()
    {
        // generic cleanup code here
        //for now, we can output a message?

    }

}
?>