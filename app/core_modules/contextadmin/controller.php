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
        $this->objLanguage = & $this->newObject('language', 'language');
        //$this->_objUtilsContent = & $this->newObject('utils', 'contextpostlogin');
        $this->_objUtils = & $this->newObject('utils', 'contextadmin');
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
                     return $this->nextAction('addstep1', array('error' =>  'An error has occured while trying to create the [-context-]'));
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
                return $this->nextAction('default');
            
            
        }
        
        
        
    }
    
    /**
     * Method to get the wizard steps
     * @return string
     * @access public
     */
    public function wizardSteps()
    {
        $action = $this->getParam('action');
        
        switch($action)
        {
            case 'addstep1';
                return 'Step 1';
            case 'addstep2';
                return 'Step 2';
            case 'addstep3';
                return 'Step 3';
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