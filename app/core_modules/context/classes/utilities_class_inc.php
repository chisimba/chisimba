<?php
/* ----------- data class extends dbTable for tbl_context_usernotes------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }

 /**
 * Utilities class for context
 * @package context
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @author Wesley  Nitsckie
 * @version $Id$ 
 **/
class utilities extends object
{
    
    /**
    * @var object $objDBContext
    */
    var $objDBContext;
    
    /**
    * @var object $objConfig
    */
    var $objConfig;
    
    /**
    * @var object $objDBContext
    */
    var $contextCode;
    
    /**
    * Constructor method to define the table
    */
    function init() {
        $this->objDBContext = & $this->newObject('dbcontext', 'context');        
        $this->objLink = & $this->newObject('link', 'htmlelements');        
        $this->objIcon = & $this->newObject('geticon', 'htmlelements');        
        $this->objConfig = &$this->getObject('config', 'config');
        
        $this->contextCode = $this->objDBContext->getContextCode();
        
    }
    
     /**
    * Method to create a link to the course home
    *@return string
    */
    function getContextLinks()
    {
        $this->objIcon->setIcon("home");
        $this->objIcon->alt=$this->objLanguage->languageText("mod_context_coursehome");
        $this->objIcon->align = "absmiddle";
        
        $this->objLink->href=$this->URI(null,'context');
        $this->objLink->link=$this->objIcon->show();
        $str = $this->objLink->show();
        
        return $str;       
    }
    
     /**
    * Method to create links to the contents
    * and to the course
    *@return string
    */
    function getContentLinks()
    {
        $this->objIcon->setModuleIcon("content");
        $this->objIcon->alt=$this->objLanguage->languageText("mod_context_coursecontent");
        $this->objIcon->align = "absmiddle";
        
        $params = array('nodeid' => $this->getParam('nodeid'), 'action' => 'content');
        $this->objLink->href=$this->URI($params,'context');
        $this->objLink->link=$this->objIcon->show();
        $str = $this->objLink->show();
        
        return $str;       
    }
    
   /**
    * Method to create links to the course admin
    * 
    *@return string
    */
    function getCourseAdminLink()
    {
        $this->objIcon->setModuleIcon("contextadmin");
        $this->objIcon->alt=$this->objLanguage->languageText("mod_context_courseadmin");
        $this->objIcon->align = "absmiddle";
        
        $params = array( 'action' => 'courseadmin');
        $this->objLink->href=$this->URI($params,'contextadmin');
        $this->objLink->link=$this->objIcon->show();
        $str = $this->objLink->show();
        
        return $str;       
    }
    
    /**
    * Method used to get the path to the course folder
    * @param string $contextCode The context code
    * @return string 
    */
    function getContextFolder($contextCode=NULL){
        if($contextCode==NULL){
            $contextCode = $this->contextCode;
        } 
        $str = $this->objConfig->siteRootPath().'usrfiles/content/'.$contextCode.'/';
        
       return $str; 
          
    }
    
    /**
    * Method used to get the path to the images  folder
    * for a given course code
    * @param string $contextCode The context code
    * @return string 
    */
    function getImagesFolder($contextCode=NULL){
        return $this->getContextFolder($contextCode).'images/';
    }

    /**
    * Method used to get the path to the maps  folder
    * for a given course code
    * @param string $contextCode The context code
    * @return string 
    */
    function getMapsFolder($contextCode=NULL){
        return $this->getContextFolder($contextCode).'maps/';
    }
} 
?>
