<?PHP
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

class packages extends controller
{
	
	/**
     * Controller class for the packages module that extends the base controller
     *
     * @author Prince Mbekwa <pmbekwa@uwc.ac.za>
     * @copyright 2007 AVOIR
     * @package packages
     * @category chisimba
     * @license GPL
     */
    /**
     * Constructor method to instantiate objects and get variables
     * 
     * @since  1.0.0
     * @return string
     * @access public
     */
    public function init()
    {
        try {
           

        }
        catch(customException $e) {
            //oops, something not there - bail out
            echo customException::cleanUp();
            //we don't want to even attempt anything else right now.
            die();
        }
    }

    /**
     * Method to process actions to be taken from the querystring
     *
     * @param string $action String indicating action to be taken
     * @return string template
     */
    public function dispatch($action = Null){
    	
    	
    }

}
?>