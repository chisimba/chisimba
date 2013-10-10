<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* http client Controller
*
* @author Paul Scott
* @copyright (c) 2004 University of the Western Cape
* @package httpclient
* @version 1
*/
class httpclient extends controller
{

    /**
	* Constructor method to instantiate objects and get variables
	*/
    function init()
    {
        try {
        	$this->objClient = $this->getObject('client');
        	//Get the activity logger class
        	$this->objLog=$this->newObject('logactivity', 'logger');
        	//Log this module call
        	$this->objLog->log();
        }
        catch (customException $e)
        {
        	echo customException::cleanUp();
        	die();
        }
    }

    /**
	* Method to process actions to be taken
    *
    * @param string $action String indicating action to be taken
	*/
    function dispatch($action=Null)
    {
        switch ($action)
        {
            default:
            	$url = $this->getParam('url');
            	$url = "http://www.google.co.za/";
                try {
                	//$proxy = array(
          			//  'host' => $host,
            		//  'port' => $port,
            		//  'user' => $user,
            		//  'password' => $pass
        			//);
        			//$proxy=array();


                	echo $this->objClient->getUrl($url, $proxy);
    			} catch (customException $e) {
        			echo $e->getMessage();
    			}
                break;
        }
    }
}
?>