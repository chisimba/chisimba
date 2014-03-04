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
class pdf extends controller
{

    public $objPdf;

	/**
	* Constructor method to instantiate objects and get variables
	*/
    public function init()
    {
        try {
        	$this->objPdf = $this->getObject('zpdf');
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
    public function dispatch($action=Null)
    {
        switch ($action)
        {
            default:
            case 'genpdf':
            	try {
            		$this->objPdf->newPdf();
            		$this->objPdf->setupPages();
            		$this->objPdf->newPdfPage();



            		//save
            		$this->objPdf->savePdf('/var/www/chi/test123.pdf', TRUE, $newFileName = '');


				} catch (customException $e) {
        			echo $e->getMessage();
    			}
                break;

    	}
    }
}
?>