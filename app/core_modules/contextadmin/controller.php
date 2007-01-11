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
                     $this->_objDBContext->joinContext($this->getParam('contextcode'));
                     
                     return $this->nextAction('addstep2');
                 } else {
                     return $this->nextAction('addstep1', array('error' => $this->objLanguage->languageText("mod_context_error_createcontext",'context') ));
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
		default:
			return $this->nextAction(null);
        }
        
        
        
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