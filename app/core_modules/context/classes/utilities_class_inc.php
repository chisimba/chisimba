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
        $this->objDBContextModules = & $this->newObject('dbcontextmodules', 'context');        
        $this->objDBContextParams = & $this->newObject('dbcontextparams', 'context');        
        $this->contextCode = $this->objDBContext->getContextCode();
        
    }
    
     /**
    * Method to create a link to the course home
    *@return string
    */
    function getContextLinks()
    {
        $this->objIcon->setIcon("home");
        $this->objIcon->alt=$this->objLanguage->languageText("mod_context_coursehome",'context');
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
        $this->objIcon->alt=$this->objLanguage->languageText("mod_context_coursecontent",'context');
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
        $this->objIcon->alt=$this->objLanguage->languageText("mod_context_courseadmin",'context');
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
    
    /**
    * Method to get the context menu
    * @return string
    * @param void
    * @access public
    */
    public function getContextMenu()
    {
    	
    	
    	
    	try {
			
			//initiate the objects
			$objSideBar = $this->newObject('sidebar', 'navigation');
			$objModules = & $this->newObject('modules', 'modulecatalogue');
			
			//get the contextCode
			$this->objDBContext->getContextCode();	
			
			//create the nodes array
			$nodes = array();
			
			//get the section id
			$section = $this->getParam('id');
			
			//create the home for the context
			$nodes[] = array('text' =>$this->objDBContext->getMenuText() . ' - Home Page ', 'uri' => $this->uri(null,"_default"));
						
			
			
			//get the registered modules for this context
			/*$arrContextModules = array(
			                         array('moduleid' => 'forum', 'title' => 'Disussion Forum'), 
			                         array('moduleid' => 'chat', 'title' =>  'Chat'),
			                         array('moduleid' => 'contextcmscontent', 'title' => 'Course Content'));
			                         */
			$arrContextModules = $this->objDBContextModules->getContextModules($this->contextCode);
			
			foreach($arrContextModules as $contextModule)
			{
				$modInfo = $objModules->getModuleInfo($contextModule['moduleid']);
				
				$nodes[] = array('text' => $modInfo['name'], 'uri' => $this->uri(array('action' => 'contenthome', 'moduleid' => $contextModule['moduleid'])),  'sectionid' => $contextModule['moduleid']);
			}
			/*
			//start looping through the sections
			foreach ($arrSections as $section)
			{
				
				//add the sections
		        if(($this->getParam('action') ==  'showsection') && ($this->getParam('id') == $section['id']) || $this->getParam('sectionid') == $section['id'])
		        {
		        	
		        	$pagenodes = array();
		        	$arrPages = $this->_objContent->getAll('WHERE sectionid = "'.$section['id'].'" AND published=1 ORDER BY ordering');
		        	
		        	foreach( $arrPages as $page)
		        	{
		        		$pagenodes[] = array('text' => $page['menutext'] , 'uri' =>$this->uri(array('action' => 'showfulltext', 'id' => $page['id'], 'sectionid' => $section['id']), 'cms'));
		        		
		        	}
		        	
		        	$nodes[] = array('text' =>$section['menutext'], 'uri' => $this->uri(array('action' => 'showsection', 'id' => $section['id']), 'cms'), 'sectionid' => $section['id'], 'haschildren' => $pagenodes);
		        	$pagenodes = null;
		        	
		        } else {
		        	$nodes[] = array('text' =>$section['menutext'], 'uri' => $this->uri(array('action' => 'showsection', 'id' => $section['id']), 'cms'), 'sectionid' => $section['id']);	
		        }
				
			}
			//add the admin link
			$nodes[] = array('text' => 'Administration', 'uri' =>$this->uri(null, 'cmsadmin'));
						*/
			return $objSideBar->show($nodes, $this->getParam('id'));
		}catch (Exception $e){
       		echo 'Caught exception: ',  $e->getMessage();
        	exit();
        }
    }
} 
?>
