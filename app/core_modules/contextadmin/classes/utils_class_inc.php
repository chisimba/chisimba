<?php
// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
    die( "You cannot view this page directly" );
} 
// end security check
/**
 * The context postlogin controls the information 
 * of courses that a user is registered to and the tools
 * that goes courses
 * 
 * @author Wesley Nitsckie
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @package context
 */

class utils extends object 
{
    
    /**
     * The constructor
     */
    public function init()
    {
        
          $this->_objContextModules = & $this->newObject('dbcontextmodules', 'context');
	      $this->_objLanguage = & $this->newObject('language', 'language');
	      $this->_objUser = & $this->newObject('user', 'security');
	      $this->_objDBContext = & $this->newObject('dbcontext', 'context');
    }
    
    /**
     * Method to get the widgets
     * 
     */
    public function getWidgets()
    {
        
        
    }
    
    /**
     * Method to get the context for this user
     * 
     */
    public function getContexts($userId)
    {
        
        
    }
    
    /**
	   * Method to get the users context that he
	   * is registered to
	   * @return array
	   * @access public
	   */
	  public function getContextList()
	  {
	  	
	  	$objGroups = & $this->newObject('managegroups', 'contextgroups');
	  	$contextCodes = $objGroups->usercontextcodes($this->_objUser->userId());
	  	
	  	$arr = array();
	  	foreach ($contextCodes as $code)
	  	{
	  		$arr[] = $this->_objDBContext->getRow('contextcode',$code); 
	  		
	  	}
	  	//print_r($arr);
	  	return $arr;
	  }
	  
	  /**
	   * Method to get the users context that he
	   * is registered to
	   * @return array
	   * @access public
	   */
	  public function getOtherContextList()
	  {
	  	
	  	$objGroups = & $this->newObject('managegroups', 'contextgroups');
	  	return null;//$objGroups->usercontextcodes($this->_objUser->userId());
	  }
	  
	  /**
	   * Method to get the left widgets
	   * @return string
	   * @access public
	   */
	  public function getLeftContent()
	  {
	  	//Put a block to test the blocks module
		$objBlocks = & $this->newObject('blocks', 'blocks');
		$leftSideColumn = '';
		//Add loginhistory block
		$leftSideColumn .= $objBlocks->showBlock('calendar', 'eventscalendar');
		$leftSideColumn .= $objBlocks->showBlock('loginstats', 'context');
		//Add guestbook block
		$leftSideColumn .= $objBlocks->showBlock('guestinput', 'guestbook');
		//Add latest search block
		$leftSideColumn .= $objBlocks->showBlock('lastsearch', 'websearch');
		//Add the whatsnew block
		$leftSideColumn .= $objBlocks->showBlock('whatsnew', 'whatsnew');
		//Add random quote block
		$leftSideColumn .= $objBlocks->showBlock('rquote', 'quotes');
		$leftSideColumn .= $objBlocks->showBlock('today_weather','weather');
	      return $leftSideColumn;
	  }
	   
	  
	  /**
	   * Method to get the right widgets
	   * @return string
	   * @access public
	   */
	  public function getRightContent()
	  {
	     $rightSideColumn = "";
	     $objBlocks = & $this->newObject('blocks', 'blocks');
		//Add the getting help block
		$rightSideColumn .= $objBlocks->showBlock('dictionary', 'dictionary');
		//Add the latest in blog as a a block
		$rightSideColumn .= $objBlocks->showBlock('latest', 'blog');
		//Add the latest in blog as a a block
		$rightSideColumn .= $objBlocks->showBlock('latestpodcast', 'podcast');
		//Add a block for chat
		$rightSideColumn .= $objBlocks->showBlock('chat', 'chat');
		//Add a block for the google api search
		$rightSideColumn .= $objBlocks->showBlock('google', 'websearch');
		//Put the google scholar google search
		$rightSideColumn .= $objBlocks->showBlock('scholarg', 'websearch');
		//Put a wikipedia search
		$rightSideColumn .= $objBlocks->showBlock('wikipedia', 'websearch');
		//Put a dictionary lookup
		
		return $rightSideColumn;
	  } 
	  
	  
	  /**
	   * Method to get the Lectures for a course
	   * @param string $contextCode The context code
	   * @return array
	   * @access public
	   */
	  public function getContextLecturers($contextCode)
	  {
	  		$objLeaf = $this->newObject('groupadminmodel', 'groupadmin');
	  		$leafId = $objLeaf->getLeafId(array($contextCode,'Lecturers'));
	  		
	  		$arr = $objLeaf->getSubGroupUsers($leafId);
	  		
	  		return $arr;
	  		
	  }
	  
	  /**
	   * Method to get a plugins for a context 
	   * @param string $contextCode The Context Code
	   * @return string 
	   * @access public
	   * 
	   */
	  public function getPlugins($contextCode)
	  {
	  	$str = '';
	  	$arr = $this->_objContextModules->getContextModules($contextCode);
	  	$objIcon = & $this->newObject('geticon', 'htmlelements');
	  	$objModule = & $this->newObject('modules', 'modulecatalogue');
	  	if(is_array($arr))
	  	{
	  		foreach($arr as $plugin)
	  		{
	  			
	  			$modInfo =$objModule->getModuleInfo($plugin['moduleid']);
	  			
	  			$objIcon->setModuleIcon($plugin['moduleid']);
	  			$objIcon->alt = $modInfo['name'];
	  			$str .= $objIcon->show().'   ';
	  		}
	  		
	  		return $str;
	  	} else {
	  		return '';
	  	}
	  	
	  }
	  
	  /**
	   * Method to generate a form with the 
	   * plugin modules on
	   * @param string $contextCode
	   * 
	   * @return string
	   */
	  public function getPluginForm($contextCode = null)
	  {
	  	
	  	if(empty($contextCode))
	  	{
	  		$contextCode = $this->_objDBContext->getContextCode();
	  	}
	  	$objForm = & $this->newObject('form','htmlelements');
		$objH = & $this->newObject('htmlheading','htmlelements');
		$inpContextCode =  & $this->newObject('textinput','htmlelements');
		$inpMenuText = & $this->newObject('textinput','htmlelements');
		
		$inpButton =  $this->newObject('button','htmlelements');
			  	//setup the form
		$objForm->name = 'addfrm';
		$objForm->action = $this->uri(array('action' => 'savestep3'));
		$objForm->extra = 'class="f-wrap-1"';
		$objForm->displayType = 3;
		
		$inpAbout->name = 'about';
		$inpAbout->id = 'about';
		$inpAbout->value = '';
		$inpAbout->cols = 4;
		$inpAbout->rows = 3;
		
		
		$inpButton->setToSubmit();
		$inpButton->cssClass = 'f-submit';
		$inpButton->value = 'Save';
		
		
		//validation
		//$objForm->addRule('about','About is a required field!', 'required');
		
		
		//$objForm->addToForm('<div class="req"><b>*</b> Indicates required field</div>');
		
		$objForm->addToForm('<fieldset>');
		$objForm->addToForm($objH->show());
		$objForm->addToForm('<div id="resultslist-wrap"><ol>');
		
		$objModuleFile = & $this->newObject('modulefile', 'modulecatalogue');
		$objModules = & $this->newObject('modules', 'modulecatalogue');
		$arrModules = $objModules->getModules(2);
		
		
		foreach ($arrModules as $module)
		{
		    if($objModuleFile->contextPlugin($module['module_id']))
		    {
		        $checkbox = $this->newObject('checkbox', 'htmlelements');
		        $checkbox->value=$module['module_id'];
		        $checkbox->cssId = 'mod_'.$module['module_id'];
		        $checkbox->name = 'mod_'.$module['module_id'];
		        $checkbox->cssClass = 'f-checkbox';
		        
		        $icon = $this->newObject('geticon', 'htmlelements');
		        $icon->setModuleIcon($module['module_id']);
		        print $module['module_id'];
		        $objForm->addToForm('<li><dl><dt>'.$checkbox->show().'&nbsp;'.$icon->show().'&nbsp;'.$module['title'].'</dt>');
		        $objForm->addToForm('<dd  class="desc">'.$module['description'].'</dd>');
		        $objForm->addToForm('</dl></li>');
		    }
		
		}
		$objForm->addToForm('</ol></div><div class="f-submit-wrap">'.$inpButton->show().'</div></fieldset>');
		return  $objForm->show().'<br/>';
		

	  }
	  
	  /**
	   * Method to generate the toolbox for the 
	   * the lecturer
	   */
	  public function getContextAdminToolBox()
	  {
	  	/*$str = 'asdfasdfasdfasdfdsafadsf';
	  	
	  	$tabBox = & $this->newObject('tabpane', 'htmlelements');
	  	$tabBox->name = 'toolbox';
	  	$tabBox->addTab(array('name'=>'Configure Course','content' => $str, 'luna-tab-style-sheet'));
	  	$tabBox->addTab(array('name'=>'Manage Users','content' => $str, 'luna-tab-style-sheet'));
	  	return $tabBox->show();
	  	*/
	  	$str = '<div class="tab-page">
		

		
		<!-- id is not necessary unless you want to support multiple tabs with persistence -->
		<div class="tab-pane" id="tabPane3">

			<div class="tab-page">
				<h2 class="tab">Plugins</h2>
				
				'. $this->getPluginForm().'
				
			</div>

			<div class="tab-page">
				<h2 class="tab">Communication</h2>

				
				Send Email to class
				
			</div>
			
			<div class="tab-page">
				<h2 class="tab">Content Managment</h2>
				Link to content management goes here. I dont think we can put the content managment in here as it will be too big 
			</div>
			
			<div class="tab-page">
				<h2 class="tab">Assessment Tools</h2>
				Assessment Tools can go here
				
			</div>
			<div class="tab-page">
				<h2 class="tab">Personal</h2>
				my personal space can go here
				
			</div>
			
			<div class="tab-page">
				<h2 class="tab">Configure</h2>
				Configuration of the course goes here
				
			</div>

		</div>
		
	</div>';
	  	$objFeatureBox = $this->newObject('featurebox', 'navigation');
	  	//$objFeatureBox->title = 'Tool Box';
	  	return $objFeatureBox->show('Tool Box', $str);
	  	return $str;
	  }
}	
?>