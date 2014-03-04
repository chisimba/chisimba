<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
* 
* Controller class for Chisimba for the module framesite, which allows the inclusion
* of an external site inside an IFRAME within a Chisimba application.
*
* @author Derek Keats
* @package framesite
*
*/
class framesite extends controller
{
    
    /**
    * @var $objLanguage String object property for holding the 
    * language object
    */
    var $objLanguage;
    /**
    * @var $objLog String object property for holding the 
    * logger object for logging user activity
    */
    var $objLog;

    /**
     * Intialiser for the stories controller
     *
     * @param byref $ string $engine the engine object
     */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
    }
    
    
    /**
     * 
     * Call the appropriate template depending on some parameters.
     * If there is a URL, then it is loaded, but if there is no URL then
     * an input box is provided to enter a URL.
     * 
     */
    public function dispatch()
    {
        // retrieve the mode (edit/add/translate) from the querystring
        $site = $this->getParam("site", NULL);
		if ($site !== NULL) {
		    $objIframe = $this->newObject('iframe', 'htmlelements');
		    $objIframe->width=$this->getParam('width', 800);
		    $objIframe->height=$this->getParam('height', 600);
		    $objIframe->border=$this->getParam('border', '0');
		    $id=$this->getParam('id', NULL);
		    if ($id!==NULL) {
				$objIframe->id=$id;		        
		    }
		    $objIframe->src = $site;
		    $str = $objIframe->show();
		    $this->setVarByRef('str', $str);
		    return "iframe_tpl.php";
		} else {
		    $str = "Working here";
		    $this->setVarByRef('str', $str);
		    return "dump_tpl.php";
		}
        
    }


    /**
    *
    * This is a method to determine if the user has to 
    * be logged in or not. Note that this is an example, 
    * and if you use it view will be visible to non-logged in 
    * users. Delete it if you do not want to allow annonymous access.
    * It overides that in the parent class
    *
    * @return boolean TRUE|FALSE
    *
    */
    public function requiresLogin()
    {
        $action=$this->getParam('action','NULL');
        switch ($action)
        {
            case 'view':
                return FALSE;
                break;
            default:
                return TRUE;
                break;
        }
     }
}
?>