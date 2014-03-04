<?php
/* -------------------- keepsessionalive class extends controller ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check
                                                                                                                                             
/**
* Controller class for the module to prevent session timeout
* @copyright 2004 KEWL.NextGen
* @author James Scoble
*
* $Id: controller.php
*/

class keepsessionalive extends controller
{

    public function init()
    {
        $this->objConfig= $this->getObject('altconfig', 'config');
        $this->objUser= $this->getObject('user', 'security');
        $this->userId=$this->objUser->userId();
        $this->objLanguage=  $this->getObject('language','language');
    }
    /**
    * This is the main method in the class
    * It calls other functions depending on the value of $action
    * @param string $action
    */
    public function dispatch($action=Null)
    {
        switch ($action)
        {
            case 'stayonline':
            $this->pageSettings();
            //$this->setPageTemplate('stayonline_page_tpl.php');
            return ('index_tpl.php');
        case 'ajaxreload':
            $this->ajaxReload();
        default:
            return ('menu_tpl.php');
        }
    }


    /** 
    * Method to determine if the user has to be logged in or not
    */
    public function requiresLogin() // overides that in parent class
    {
        $action=$this->getParam('action','NULL');
        if ($action=='stayonline'){
            return FALSE;
        }
        return TRUE;
    }
    
    /**
    * Method to disable the normal contents of the page
    */
    public function pageSettings()
    {
        $this->setVar('pageSuppressContainer', TRUE);
        $this->setVar('pageSuppressBanner', TRUE);
        $this->setVar('pageSuppressToolbar', TRUE);
        $this->setVar('pageSuppressMetaData', TRUE);
        $this->setVar('suppressFooter', TRUE);
        $this->setVar('pageSuppressIM', TRUE); 
        $this->setVar('overRideTitle',$this->objLanguage->languageText('help_keepsessionalive_about_title','keepsessionalive' )); 
        $timeOut=$this->objConfig->getsystemTimeout();
        $reload=(($timeOut-1)*60);
        // Avoid session timeout
        if ($reload>300){
            $reload=300;
        }
        $headerParam = '<meta http-equiv="refresh" content="'.$reload .'; URL='.$this->uri(array('action'=>'stayonline')).'"/>';
        $this->appendArrayVar('headerParams',$headerParam);
        
        if ($this->getParam('loadwindow', NULL) == 'yes') {
            $this->setVar('bodyParams', 'onLoad = "window.focus()";');
        }
    }


    function ajaxReload()
    {



        // Instantiate Class - Parameter MUST be the URL with the current action
       // $xajaxTest = new xajax($this->uri(NULL));
        //$xajaxTest->registerFunction(array($this,"ajaxRefresh")); // Register another function in this controller
        //$xajaxTest->processRequests(); // XAJAX method to be called
                                
    }

    function ajaxRefresh()
    {
        $this->loadClass('xajaxresponse', 'htmlelements');
        $xajaxResponse = new xajaxresponse();
        $xajaxResponse->addAlert('sadasddas');
        return $this->xajaxResponse->getXML();
    }
}
?>
