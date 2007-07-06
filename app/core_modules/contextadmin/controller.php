<?php
/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check

 /**
 * Class to access the ContextModules Tables 
 * @package context
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @author Wesley  Nitsckie
 * @version $Id$ 
 **/
class contextadmin extends controller 
{
   /*
    * @var object objExportContent
    */
    public $objExportContent;

    /**
     * Import object
     *
     * @var object
     */
    public $objImport;
 
     /**
     * File handler
     *
     * @var object
     */
    public $objConf;

     /**
     * 
     *
     * @var object
     */
    public $objImportIMSContent;

    /**
     * The constructor
     */
    public function init()
    {
        $this->_objDBContext = & $this->newObject('dbcontext', 'context');
        $this->_objDBContextModules = & $this->newObject('dbcontextmodules', 'context');
        $this->_objUser = & $this->newObject('user', 'security');
        $this->_objLanguage = & $this->newObject('language', 'language');
        $this->_objUtils = & $this->newObject('utils', 'contextadmin');
        $this->_objDBContextParams = & $this->newObject('dbcontextparams', 'context');
	//Load Export class
	$this->objExportContent = & $this->newObject('export','contextadmin');

	//Load Import IMS class
	$this->objImportIMSContent = & $this->newObject('importimspackage','contextadmin');
	//Load Import KNG class
	$this->objImportKNGContent = & $this->newObject('importkngpackage','contextadmin');
	//Load Export IMS class
	$this->objExportIMSContent = & $this->newObject('exportimspackage','contextadmin');
	//Load Import Export Utilities class
	$this->objIEUtils = & $this->newObject('importexportutils','contextadmin');
        $this->objConf = &$this->getObject('altconfig','config');
        $this->objModules = $this->getObject('modules', 'modulecatalogue');

    }
    

    /**
     * The standard dispatch function
     */
    public function dispatch()
    {
        $action = $this->getParam('action');
        
        if(!$this->hasPerms())
        {
         	$this->setLayoutTemplate('layout_tpl.php');
			return 'error_tpl.php';	
		}

        switch ($action)
        {
        	
            case '':
	        case 'default':
	            $this->setLayoutTemplate('main_layout_tpl.php');
	            $this->setVar('contextList', $this->_objUtils->getContextList());
	            $this->setVar('otherCourses', $this->_objUtils->getOtherContextList());
	            $this->setVar('filter', $this->_objUtils->getFilterList($this->_objUtils->getContextList()));
	            return 'main_tpl.php';
                
            //the following cases deals with adding a context
            
            //use a layout template for this wizard

            case 'addstep1':
                $this->setLayoutTemplate('layout_tpl.php');
                $this->setVar('error', $this->getParam('error'));
                
                return 'addstep1_tpl.php';
            case 'savestep1':
                 if($this->_objDBContext->createContext() != FALSE)
                 {
                     $this->_objDBContext->joinContext($this->getParam('contextcode'));
                     
                     return $this->nextAction('addstep2');
                 } else {
                     return $this->nextAction('addstep1', array('error' => $this->_objLanguage->languageText("mod_context_error_createcontext",'context') ));
                 }
            case 'addstep2':
                $this->setLayoutTemplate('layout_tpl.php');
                return 'addstep2_tpl.php';
            case 'savestep2':
                $this->_objDBContext->update('contextcode', $this->_objDBContext->getContextCode(), array('about' => $this->getParam('about')));
                return $this->nextAction('addstep3');
            case 'addstep3':
                $this->setLayoutTemplate('layout_tpl.php');
                return 'addstep3_tpl.php';
                
            case 'savestep3':
                $this->_objDBContextModules->save();
                $this->_objDBContext->setLastUpdated();
                return $this->nextAction('default');
           
                
            //the next steps deals with actions coming from the 
            //config page         
            case 'saveedit';
            	$this->_objDBContext->saveEdit();
            	 return $this->nextAction('default');
           	case 'saveaboutedit';
            	$this->_objDBContext->saveAboutEdit();
            	 return $this->nextAction('default');
            case 'savedefaultmod':
            	//if($this->getParam('defaultmodule') != '')
            	//{
            		$this->_objDBContextParams->setParam($this->_objDBContext->getContextCode(), 'defaultmodule',$this->getParam('defaultmodule'));
            	//}
            	return $this->nextAction('default');
           	case 'admincontext':
           		$this->_objDBContext->joinContext($this->getParam('contextcode'));
           		return $this->nextAction('default');
	/**
	Author : Jarrett Jordaan
	Update : Import and Export of IMS Content
	*/

	//Display Import Template
	case 'eduCommonsToChisimba':
		$this->setLayoutTemplate('eduCommonsToChisimba_tpl.php');
			
		return 'eduCommonsToChisimba_tpl.php';

	/**
	 * Executes the Uploading of IMS package into Chisimba
	*/
        case 'uploadIMS':
		$this->setLayoutTemplate('uploadstatus_tpl.php');
		//Instantiate the Import of IMS package
		$uploadStatus = $this->objImportIMSContent->importIMScontent($_FILES);
		$this->setVar('uploadStatus',$uploadStatus);
		$this->setSession('uploadStatus', $uploadStatus);
		if(!(strcmp($uploadStatus, '/error/')))
			return 'uploadstatus_tpl.php';
		else
			return 'errorreport_tpl.php';

	/**
	 * Executes the Uploading of KNG package into Chisimba
	*/
	case 'uploadKNG':
		$this->setLayoutTemplate('uploadstatus_tpl.php');
		$choice = $this->getParam('dropdownchoice');
		//Instantiate the Import of KNG package
		$uploadStatus = $this->objImportKNGContent->importKNGcontent($choice);
		$this->setVar('uploadStatus',$uploadStatus);

		return 'uploadstatus_tpl.php';

	//Display Export Template
	case 'chisimbaToIMS':
		$this->setLayoutTemplate('chisimbaToIMS_tpl.php');
			
		return 'chisimbaToIMS_tpl.php';

	/**
	 * Executes the Downloading of IMS package from Chisimba
	*/
        case 'downloadChisimba':
		$this->setLayoutTemplate('uploadstatus_tpl.php');
		$choice = $this->getParam('dropdownchoice');
		//Instantiate the Import of KNG package
		$uploadStatus = $this->objExportIMSContent->exportChisimbaContent($choice);
		$this->setVar('uploadStatus',$uploadStatus);

		return 'uploadstatus_tpl.php';

	/**
	 * Executes the Downloading of IMS package from KNG
	*/
        case 'downloadKNG':
		$this->setLayoutTemplate('uploadstatus_tpl.php');
		$choice = $this->getParam('dropdownchoice');
		//Instantiate the Import of KNG package
		$uploadStatus = $this->objExportIMSContent->exportKNGContent($choice);
		//$this->setVar('uploadStatus',$uploadStatus);

		return 'uploadstatus_tpl.php';

	/**
	 * 
	*/
        case 'debug':
		$this->setLayoutTemplate('debug_tpl.php');

		return 'debug_tpl.php';

	default:
		return $this->nextAction(null);
        }
    }
    

    /**
     * Method to load an HTML element's class.
     * @param string $name The name of the element
     * @return The element object
     */
     public function loadHTMLElement($name)
     {
         return $this->loadClass($name, 'htmlelements');
     }
 
    /**
     * Method to get the left widget
     * @return string
     * 
     */
    public function getLeftWidgets()
    {
    	
    	$str = $this->_objUtils->getLeftContent();
    	
    	return $str;
    }
    
    
    
    /**
     * Method to get right left widget
     * @return string
     * 
     */
    public function getRightWidgets()
    {
    	
    	$str = $this->_objUtils->getRightContent();
    	
    	return $str;
    }
    
    /**
    * Method to check the permissions
    * for a user. Only Lecturers and Administrators
    * are allowed here
    * @access public
    */
    public function hasPerms()
    {
     	
		if($this->_objUser->isAdmin() || $this->_objUser->isLecturer())
		{
			return TRUE;
		} else {
			return FALSE;
		}
			
		
	}
    

}
?>