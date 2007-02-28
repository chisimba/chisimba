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
     * The constructor
     */
    public function init()
    {
        
        $this->_objDBContext = & $this->newObject('dbcontext', 'context');
        $this->_objDBContextModules = & $this->newObject('dbcontextmodules', 'context');
        $this->_objUser = & $this->newObject('user', 'security');
        $this->_objLanguage = & $this->newObject('language', 'language');
        //$this->_objUtilsContent = & $this->newObject('utils', 'contextpostlogin');
        $this->_objUtils = & $this->newObject('utils', 'contextadmin');
        $this->_objDBContextParams = & $this->newObject('dbcontextparams', 'context');
	$this->objExportContent = & $this->newObject('export','contextadmin');
	$this->objImport = &$this->getObject('blogimporter','blog');
    }
    
    
    /**
     * The standard dispatch function
     */
    public function dispatch()
    {
        $action = $this->getParam('action');
        
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
                    // $this->_objDBContext->joinContext($this->getParam('contextcode'));
                     
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
           
            case 'delete':
                $this->_objDBContext->deleteContext($this->getParam('contextcode'));
                return $this->nextAction(null);
                
                
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
		case 'exporttoxml':
                      $this->objExportContent->doXMLExport();
                      return $this->nextAction(null);
		case 'passcourse':
			echo "asdf";
			return $this->nextAction(null);
 	        case 'importcourse' :
			$this->setLayoutTemplate('importcoures_tpl.php');
			$this->setVar('dbData',$this->accessCourseList());
			return 'importcourse_tpl.php';
		case 'submitcourse':
			echo "asdf";
			echo $this->getParam('contextcode');
			echo $this->contextcode;
			echo $this->getParam('cc');
			echo $this->cc;
			return $this->nextAction(null);
		case 'writetonew':
			$this->objExportContent->doXMLWrite();
			return $this->nextAction(null);		
		default:
			return $this->nextAction(null);
        }
        
        
        
    }
    
    public function accessCourseList($dsn)
	{
					                
                //$dsn = "localhost";
                $table = "tbl_context";
                //$filter = "SELECT contextcode from tbl_context";
		$filter = "SELECT * from tbl_context";

                //set up to connect to the server
                $dsn1 = $this->objImport->setup($dsn);
                //connect to the remote db
                $dbobj = $this->objImport->_dbObject();
                $datas = $this->objImport->queryTable($table,$filter);
		return $datas;
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
 
	public function arrayKeys()
	{
	$arrayC[0] = "1";
	$arrayC[1] = "2";
	$arrayC[2] = "3";

	return $arrayC;
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
}
