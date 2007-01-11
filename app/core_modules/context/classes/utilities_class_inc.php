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
        $this->objDBContext = & $this->getObject('dbcontext', 'context');        
        $this->objLink = & $this->getObject('link', 'htmlelements');        
        $this->objIcon = & $this->getObject('geticon', 'htmlelements');        
        $this->objConfig = &$this->getObject('config', 'config');
        $this->objDBContextModules = & $this->getObject('dbcontextmodules', 'context');        
        $this->objDBContextParams = & $this->getObject('dbcontextparams', 'context');        
        $this->objLanguage = & $this->getObject('language', 'language');        
        $this->contextCode = $this->objDBContext->getContextCode();
        $this->_objContextModules = & $this->getObject('dbcontextmodules', 'context');
        
    }
    
    
    /**
     * Method to get the sliding context menu
     * 
     * @return string
     */
    public function getHiddenContextMenu($selectedModule, $showOrHide = 'none')
    {
        //apparently document.write is not xhtml compliant, so
        // seeing that I dont an other alternative to that I will
        // suppress the xhtml
        $this->setVar('pageSuppressXML',true);
        $icon = $this->getObject('geticon', 'htmlelements');
        $icon->setIcon('up');
        $scripts = '<script src="modules/htmlelements/resources/script.aculos.us/lib/prototype.js" type="text/javascript"></script>
                      <script src="modules/htmlelements/resources/script.aculos.us/src/scriptaculous.js" type="text/javascript"></script>
                      <script src="modules/htmlelements/resources/script.aculos.us/src/unittest.js" type="text/javascript"></script>';
        $this->appendArrayVar('headerParams',$scripts);
        $str = "<a href=\"#\" onclick=\"Effect.SlideUp('contextmenu',{queue:{scope:'myscope', position:'end', limit: 1}});\">".$icon->show()."</a>";
        $icon->setIcon('down');
        $str .="<a href=\"#\" onclick=\"Effect.SlideDown('contextmenu',{queue:{scope:'myscope', position:'end', limit: 1}});\">".$icon->show()."</a>";    
        
        $str .='<div id="contextmenu"  style="width:170px;overflow: hidden;display:'.$showOrHide.';"> ';
        $str .= $this->getPluginNavigation($selectedModule);
        $str .= '</div>';
        
        return $str;
    }
    
    /**
     * Method to get the left Navigation
     * with the context plugins
     * 
     * @param string $contextCode
     * @access public
     * @return string
     */
    public function getPluginNavigation($selectedModule = null)
    {
    	
    	
    	$objSideBar = $this->newObject('sidebar' , 'navigation');
    	$objModule = & $this->newObject('modules', 'modulecatalogue');
    	$objContentLinks = $this->getObject('dbcontextdesigner','contextdesigner');
    	$objIcon = & $this->getObject('geticon', 'htmlelements');
    	
    	$arr = $this->_objContextModules->getContextModules($this->objDBContext->getContextCode());
    	
    	//create the nodes array
		$nodes = array();
	    $children = array();
	    $nodes[] = array('text' => $this->objDBContext->getMenuText() .' - Home', 'uri' => $this->uri(null,'context'),  'nodeid' => 'context');
    	if(is_array($arr))
	  	{
	  		foreach($arr as $contextModule)
	  		{
	  			
	  			//$modInfo =$objModule->getModuleInfo($plugin['moduleid']);
	  			if($contextModule['moduleid'] == 'contextcontent')
	  			{
	  			    $isregistered = true;
	  			} else {
    	  			
    	  			$modInfo = $objModule->getModuleInfo($contextModule['moduleid']);
    	  			
    				$moduleLink = $this->uri(null,$contextModule['moduleid']);//$this->uri(array('action' => 'contenthome', 'moduleid' => $contextModule['moduleid']));
    				
    				$nodes[] = array('text' => $modInfo['name'], 'uri' => $moduleLink,  'nodeid' => $contextModule['moduleid']);
    				
	  			}

	  			
	  		}
	  		
	  		if($isregistered)
	  			{
	  			    
	  			    $linksArr = $objContentLinks->getPublishedContextLinks();
	  			    foreach($linksArr as $link)
	  			    {
	  			        $objIcon->setModuleIcon($link['moduleid']);
	  			        
	  			        $params = array();
	  			        $temp = spliti(',',$link['params']);
	  			       
	  			        foreach($temp as $value)
	  			        {
	  			            if(!$value=='')
	  			            {
    	  			            
    	  			            $fel = spliti('=>', $value);
    	  			            
    	  			            $params[$fel[0]] = $fel[1];
    	  			           
	  			            }
	  			        }
	  			       
	  			        $children[] = array('text' => $objIcon->show().' '.$link['menutext'], 'uri' => $this->uri($params,$link['moduleid']),  'sectionid' => 'contextcontent');
	  			        
	  			    }
	  			    $nodes[] = array('text' => $this->objLanguage->languageText("mod_context_content",'context'), 'uri' => $moduleLink,  'sectionid' => $contextModule['moduleid'], 'haschildren' => $children);
	  			    $isregistered = false;
	  			}
	  			
	  		return $objSideBar->show($nodes, $selectedModule);
	  	} else {
	  		return '';
	  	}
    }
    
    
    /**
     * Method to check if a user can join a 
     * context
     * @param string $contextCode The context Code
     * @return boolean
     * @access public
     * @author Wesley Nitsckie
     */
    public function canJoin($contextCode)
    {
    	//TODO
    	
    	//check if the user is logged in to access an open context
    	
    	//check if the user is registered to the context and he is logged in
    	
    	//if the context is public then the user can access the context , but only limited access
    	
    	
    	return true;
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
			$nodes[] = array('text' =>$this->objDBContext->getMenuText() . ' -  '.$this->objLanguage->languageText("word_home"), 'uri' => $this->uri(null,"_default"));
						
			
			
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