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
		$arr = array();

		//if the user is an administrator of the site then show him all the courses
	  	if ($this->_objUser->isAdmin())
		{
			return  $this->_objDBContext->getAll();
		}

	  	$objGroups = & $this->newObject('managegroups', 'contextgroups');
	  	$contextCodes = $objGroups->usercontextcodes($this->_objUser->userId());
	  	
	  	
	  	foreach ($contextCodes as $code)
	  	{
	  		$arr[] = $this->_objDBContext->getRow('contextcode',$code); 
	  		
	  	}
	  	//print_r($arr);
	  	return $arr;
	  }

	/**
	* Method to get a list of courses that the user is an lecturer in
	* @return array
	* @access  public
	*/
	public function getContextAdminList()
	{
		
		return null;
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
       //$userPic  = &$this->newObject('userutils', 'contextpostlogin');
       $leftSideColumn = $this->_objUser->getUserPic();//$userMenu->show();;
        //Add loginhistory block
        
        
        
        $leftSideColumn .= $objBlocks->showBlock('latest', 'blog');
        
        $leftSideColumn .= $objBlocks->showBlock('loginstats', 'context');
        
        $leftSideColumn .= $objBlocks->showBlock('calendar', 'eventscalendar');

        $leftSideColumn .= $objBlocks->showBlock('latestpodcast', 'podcast');

        $leftSideColumn .= $objBlocks->showBlock('chat', 'chat');

/*
		//Add loginhistory block
		$leftSideColumn .= $objBlocks->showBlock('calendar', 'eventscalendar');
		$leftSideColumn .= $objBlocks->showBlock('loginstats', 'context');
		//Add the latest in blog as a a block
		$leftSideColumn .= $objBlocks->showBlock('latest', 'blog');
		//Add guestbook block
		$leftSideColumn .= $objBlocks->showBlock('guestinput', 'guestbook');
		//Add latest search block
		$leftSideColumn .= $objBlocks->showBlock('lastsearch', 'websearch');
		//Add the whatsnew block
		$leftSideColumn .= $objBlocks->showBlock('whatsnew', 'whatsnew');
		
		$leftSideColumn .= $objBlocks->showBlock('today_weather','weather');
*/
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
        //$rightSideColumn .= $objBlocks->showBlock('latest', 'blog');
        //Add the latest in blog as a a block
        //$rightSideColumn .= $objBlocks->showBlock('latestpodcast', 'podcast');
        //Add a block for chat
        //$rightSideColumn .= $objBlocks->showBlock('chat', 'chat');
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
        $objLink = & $this->newObject('link', 'htmlelements');
	  	$objModule = & $this->newObject('modules', 'modulecatalogue');
	  	if(is_array($arr))
	  	{
	  		foreach($arr as $plugin)
	  		{
	  			
	  			$modInfo =$objModule->getModuleInfo($plugin['moduleid']);
	  			
	  			$objIcon->setModuleIcon($plugin['moduleid']);
	  			$objIcon->alt = $modInfo['name'];
	  			//$str .= $objIcon->show().'   ';
                
                $objLink->href = $this->uri(array ('action' => 'gotomodule', 'moduleid' => $plugin['moduleid'], 'contextcode' => $contextCode), 'context');
                $objLink->link = $objIcon->show();
                $str .= $objLink->show().'   ';
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
	  	$objFormMod = & $this->newObject('form','htmlelements');
		$objH = & $this->newObject('htmlheading','htmlelements');
		$inpContextCode =  & $this->newObject('textinput','htmlelements');
		$inpMenuText = & $this->newObject('textinput','htmlelements');
		$objDBContextParams = & $this->newObject('dbcontextparams', 'context');
		
		
		//list of modules for this context
		$arrContextModules = $this->_objContextModules->getContextModules($contextCode);
		
		$inpButton =  $this->newObject('button','htmlelements');
			  	//setup the form
		$objForm->name = 'addfrm';
		$objForm->action = $this->uri(array('action' => 'savestep3'));
		$objForm->extra = 'class="f-wrap-1"';
		$objForm->displayType = 3;
		
		$objFormMod->name = 'modfrm';
		$objFormMod->action = $this->uri(array('action' => 'savedefaultmod'));
		// $objFormMod->extra = 'class="f-wrap-1"';
		$objFormMod->displayType = 3;
		
		$inpAbout->name = 'about';
		$inpAbout->id = 'about';
		$inpAbout->value = '';
		$inpAbout->cols = 4;
		$inpAbout->rows = 3;
		
		
		$inpButton->setToSubmit();
		$inpButton->cssClass = 'f-submit';
		$inpButton->value = ucwords($this->_objLanguage->languageText("word_save"));
		
		
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
		        
		        foreach ($arrContextModules as $arr)
		        {
		        	if($arr['moduleid'] == $module['module_id'] )
		        	{
		        		$checkbox->setChecked(TRUE);
		        		break 1;
		        	}
		        }
		        
		        $icon = $this->newObject('geticon', 'htmlelements');
		        $icon->setModuleIcon($module['module_id']);
		        //print $module['module_id'];
		        $objForm->addToForm('<li><dl><dt>'.$checkbox->show().'&nbsp;'.$icon->show().'&nbsp;'.ucwords($this->_objLanguage->code2Txt('mod_'.$module['module_id'].'_name',$module['module_id'],array('context' => 'Course'))).'</dt>');
		        $objForm->addToForm('<dd  class="desc">'.$module['description'].'</dd>');
		        $objForm->addToForm('</dl></li>');
		    }
		
		}
		
		
		$objForm->addToForm('</ol></div><div class="f-submit-wrap">'.$inpButton->show().'</div></fieldset>');
		
		$dropDefaultModule = $this->newObject('dropdown', 'htmlelements');
		
		$defaultmoduleid = $objDBContextParams->getParamValue($contextCode, 'defaultmodule');
		
		$drop = $this->_objLanguage->languageText("mod_context_selectdefaultmodule",'context').'<select id="defaultmodule" name="defaultmodule">';
		
		$drop .= '<option value="">'.$this->_objLanguage->languageText("mod_context_setdefaultmodule",'context').'</option>';
		
		foreach($arrContextModules as $mod)
		{
			$modInfo = $objModules->getModuleInfo($mod['moduleid']);
			
			$drop .= '<option value="'.$mod['moduleid'].'"';
			$drop .= ($defaultmoduleid == $mod['moduleid']) ? ' selected="selected" ' : '';
			$drop .= '>'.$modInfo['name'].'</option>';
		}
		$drop .= '</select>';
		$drop ='<div style="width:270px">'.$drop.'</div>';
		$objFormMod->addToForm($drop);
		$inpButton->value = $this->_objLanguage->languageText("mod_context_setasdefaultmodule",'context');
		$objFormMod->addToForm($inpButton->show());
		
		return  $objFormMod->show().$objForm->show().'<br/>';
		

	  }
	  
	  /**
	   * Get context edit form
	   * @return string
	   * 
	   */
	  public function getEditContextForm($contextCode = null)
	  {
	  	if(empty($contextCode))
	  	{
	  		$contextCode = $this->_objDBContext->getContextCode();
	  	}
	  	
	  	$context = $this->_objDBContext->getRow('contextcode' , $contextCode);
		$objH = & $this->newObject('htmlheading','htmlelements');
			$objForm = & $this->newObject('form','htmlelements');
			
			$inpContextCode =  & $this->getObject('textinput','htmlelements');
			$inpMenuText = & $this->getObject('textinput','htmlelements');
			$inpTitle = & $this->getObject('textinput','htmlelements');
			$inpButton =  $this->getObject('button','htmlelements');
			$objIcon =  $this->getObject('geticon','htmlelements');
			$dropAccess = $this->getObject('dropdown','htmlelements');
			$radioStatus = $this->getObject('radio','htmlelements');
			$objStartDate =  & $this->getObject('datepicker', 'htmlelements');
            $objFinishDate =  & $this->getObject('datepicker', 'htmlelements');
			
            $objIcon->setIcon('help');
            
            $objH->str = $this->_objLanguage->languageText("mod_context_step",'context').' 1: '.$this->_objLanguage->languageText("mod_context_addcontext",'context');
			$objH->type = 3;
			
			//setup the form
			$objForm->name = 'addfrm';
			$objForm->action = $this->uri(array('action' => 'saveedit'));
			$objForm->extra = 'class="f-wrap-1"';
			$objForm->displayType = 3;
			
            //contextcode
			$inpContextCode->name = 'contextcode';
			$inpContextCode->id = 'contextcode';
			$inpContextCode->value = '';
			$inpContextCode->cssClass = 'f-name';
			
            //title
			$inpTitle->name = 'title';
			$inpTitle->id = 'title';
			$inpTitle->value = $context['title'];
			$inpTitle->cssClass = 'f-name';
			
            //menu text
			$inpMenuText->value = $context['menutext'];
			$inpMenuText->name = 'menutext';
			$inpMenuText->id = 'menutext';
			$inpMenuText->cssClass = 'f-name';
			
			//status
			$dropAccess->name = 'status';
			$dropAccess->addOption('Published',$this->_objLanguage->languageText("mod_context_published",'context'));
			$dropAccess->addOption('Unpublished',$this->_objLanguage->languageText("mod_context_unpublished",'context'));
			$dropAccess->setSelected(trim($context['status']));
			
			
			//access
			$checked = ($context['access'] == 'Public') ? ' checked = "checked" ' : '';
			$drop = '<fieldset class="f-radio-wrap">
		
						<b>'.$this->_objLanguage->languageText("mod_context_access",'context').':</b>
			
						
							<fieldset>
							
			
							<label for="Public">
							<input id="Public" type="radio" name="access" '.$checked.'
							value="Public" class="f-radio" tabindex="8" />
							'.$this->_objLanguage->languageText("mod_context_public",'context').' <span class="caption">  -  '.$this->_objLanguage->code2Txt("mod_context_publichelp",'context',array('context'=>'Course')).'</span></label>';
			
			$checked = ($context['access'] == 'Open') ? ' checked = "checked" ' : '';			
			$drop .= 		'<label for="Open">
							<input id="Open" type="radio" name="access" '.$checked.' value="Open" class="f-radio" tabindex="9" />
							'.$this->_objLanguage->languageText("mod_context_open",'context').' <span class="caption">  -  '.$this->_objLanguage->code2Txt("mod_context_openhelp",'context',array('context'=>'Course')).'</span></label>';
			
			$checked = ($context['access'] == 'Private') ? ' checked = "checked" ' : '';										
			$drop .='		<label for="Private">
			
							<input id="Private" type="radio" name="access" '.$checked.' value="Private" class="f-radio" tabindex="10" />
							'.$this->_objLanguage->languageText("mod_context_private",'context').' <span class="caption">  -  '.$this->_objLanguage->code2Txt("mod_context_privatehelp",'context',array('context'=>'course')).'</span></label>
				
							</fieldset>
						
						</fieldset>';
            //start date
            $objStartDate->name = 'startdate';
            $objStartDate->value = $context['startdate'];

            //finish date
            $objFinishDate->name = 'finishdate';
            $objFinishDate->value = $context['finishdate'];

			//button
			$inpButton->setToSubmit();
			$inpButton->cssClass = 'f-submit';
			$inpButton->value = ucwords($this->_objLanguage->languageText("word_save"));
			
			
			//validation
			$objForm->addRule('contextcode','[-context-] Code is a required field!', 'required');
			$objForm->addRule('menutext','Menu Text is a required field', 'required!');
			$objForm->addRule('title','Title is a required field', 'required!');
			
			$objForm->addToForm('<div class="req"><b>*</b>'.$this->_objLanguage->languageText("mod_context_required",'context').'</div>');
			$objForm->addToForm('<fieldset>');
			//if($error)
			//{
			//    $objForm->addToForm('<p class="error">'.$error.'</p>');
			//}
			//$objForm->addToForm($objH->show());
			
			$objForm->addToForm('<label for="contextcode"><b><span class="req">*</span>'.$this->_objLanguage->code2Txt("mod_context_contextcode",'context',array('context'=>'Course')).':</b> <span class="highlight">');
			$objForm->addToForm($this->_objDBContext->getContextCode().'</span><br /></label>');
			
			$objForm->addToForm('<label for="title"><b><span class="req">*</span>'.$this->_objLanguage->languageText("word_title").':</b>');
			$objForm->addToForm($inpTitle->show().'<br /></label>');
			
			$objForm->addToForm('<label for="menutext"><b><span class="req">*</span>'.$this->_objLanguage->languageText("mod_context_menutext",'context').':</b>');
			$objForm->addToForm($inpMenuText->show().'<br /></label>');
			
			//$objForm->addToForm('&nbsp;<br/>');
			
			
			$objForm->addToForm('<label for="access"><b><span class="req">*</span>'.$this->_objLanguage->languageText("mod_context_status",'context').':</b>');
            $objForm->addToForm($dropAccess->show().'<br /></label>');

			$objForm->addToForm($drop);
            $objForm->addToForm('<label>&nbsp;<br/></label>');
			
            
            //$objForm->addToForm('<label for="access"><b>'.$this->_objLanguage->languageText("mod_context_startdate",'context').':</b>');
            //$objForm->addToForm($objStartDate->show().'<br /></label>');

         //   $objForm->addToForm('<label for="access"><b>'.$this->_objLanguage->languageText("mod_context_finishdate",'context').':</b>');
          //  $objForm->addToForm($objFinishDate->show().'<br /></label>');
			$objForm->addToForm('<br/><div class="f-submit-wrap">'.$inpButton->show().'</div></fieldset>');
			return  $objForm->show().'<br/>';
	  
	  }
	  /**
	   * Method to get the about form
	   * @return string
	   * @param string $contextCode
	   * @access public
	   */
	  public function getAboutForm($contextCode = '')
	  {
				
			if(empty($contextCode))
		  	{
		  		$contextCode = $this->_objDBContext->getContextCode();
		  	}
			
			//add step 1 template
			$objH = & $this->newObject('htmlheading','htmlelements');
			$objForm = & $this->newObject('form','htmlelements');
			
			$inpContextCode =  & $this->newObject('textinput','htmlelements');
			$inpMenuText = & $this->newObject('textinput','htmlelements');
			$inpAbout =  $this->newObject('htmlarea','htmlelements');
			$inpButton =  $this->newObject('button','htmlelements');
			
			$objH->str = $this->_objLanguage->code2Txt("mod_context_aboutthecontext",'context',array('context'=>'Course'));
			$objH->type = 3;
			
			//setup the form
			$objForm->name = 'addfrm';
			$objForm->action = $this->uri(array('action' => 'saveaboutedit'));
			//$objForm->extra = 'class="f-wrap-1"';
			$objForm->displayType = 3;
			
			$inpAbout->name = 'about';
			$inpAbout->id = 'about';
			$inpAbout->value = '';
			$inpAbout->cols = 1;
			$inpAbout->rows =1;
			
			$contextLine = $this->_objDBContext->getRow('contextcode', $this->_objDBContext->getContextCode());

			$inpAbout->setContent($contextLine['about']);
			//$inpAbout->cssClass = 'f-comments';
			
			$inpButton->setToSubmit();
			$inpButton->cssClass = 'f-submit';
			$inpButton->value = $this->_objLanguage->languageText("word_save");
			
			
			//validation
			//$objForm->addRule('about','About is a required field!', 'required');
			
		
			//$objForm->addToForm('<div class="req"><b>*</b> Indicates required field</div>');
			//$objForm->addToForm('<fieldset>');
			
			$objForm->addToForm($objH->show());
			
			//$objForm->addToForm('</fieldset><b><span class="req">*</span>About:</b>');
			$objForm->addToForm($inpAbout->show());
			
			
			$objForm->addToForm('<div class="f-submit-wrap">'.$inpButton->show().'<br /></div>');
			return $objForm->show().'<br/>';	
			//return $inpAbout->show();
				  	
	  }

        /**
        * Method to get the context users
        * @return string
        */
        public function getContextUsers()
        {

            //manage context users for the course that you are in only
            $objLink =  & $this->newObject('link', 'htmlelements');
            $icon =  & $this->newObject('geticon', 'htmlelements');
            $table = & $this->newObject('htmltable' , 'htmlelements');
            $objGroups = & $this->newObject('managegroups', 'contextgroups');
            $box =  & $this->newObject('featurebox', 'navigation');
            //lecturers
            $lecturerArr = $objGroups->contextUsers('Lecturers');
            $str = '';
            //table headings
            $table->width = '60%';
            $rowcount = 0;
            if(count($lecturerArr) > 0)
            {
                
                foreach($lecturerArr as $lecture)
                {
                    $oddOrEven = ($rowcount == 0) ? "even" : "odd";
                    $tableRow = array($lecture['fullname']);
                    $table->addRow($tableRow);
                    $rowcount = ($rowcount == 0) ? 1 : 0;
                }
            } else {
                $temp = array('<div align="center" style="font-size:small;font-weight:bold;color:#CCCCCC;font-family: Helvetica, sans-serif;">'.
                $this->_objLanguage->code2Txt('mod_groupadmin_nolects','groupadmin',array('context'=>'course','authors' => 'lecturers')).
                '</div>');
                 $table->addRow($temp);
            }
            $lnkLect = $this->newObject('link', 'htmlelements');
            $lnkLect->href = $this->uri( array( 'action'=>'manage_lect' ),'contextgroups' );
            $lnkLect->link = $this->_objLanguage->code2Txt('mod_contextgroups_managelects','contextgroups',array('authors'=>''));

            $tableRow = array('<hr/>'.$lnkLect->show());
            $table->addRow($tableRow);
        
            $str .= $box->show(ucwords(($this->_objLanguage->code2Txt('mod_contextgroups_ttlLecturers','contextgroups'))),$table->show());
            
            //students list
            $studentArr = $objGroups->contextUsers('Students');
            $table = & $this->newObject('htmltable' , 'htmlelements');
            //table headings
            $table->width = '60%';
            $rowcount = 0;
            if(count($studentArr) > 0)
            {
                
                foreach($studentArr as $student)
                {
                    $oddOrEven = ($rowcount == 0) ? "even" : "odd";
                    $tableRow = array($student['fullname']);
                    $table->addRow($tableRow);
                    $rowcount = ($rowcount == 0) ? 1 : 0;
                }
            } else {
                $temp = array('<div align="center" style="font-size:small;font-weight:bold;color:#CCCCCC;font-family: Helvetica, sans-serif;">'.
                $this->_objLanguage->code2Txt('mod_groupadmin_nostuds','groupadmin',array('context'=>'course','readonlys' => 'students')).
                '</div>');
                 $table->addRow($temp);
            }
           $lnkStud = $this->newObject('link', 'htmlelements');
            $lnkStud->href = $this->uri( array( 'action'=>'manage_stud' ),'contextgroups' );
            $lnkStud->link = $this->_objLanguage->code2Txt('mod_contextgroups_managestuds','contextgroups',array('readonlys'=>''));
          

            $tableRow = array('<hr/>'.$lnkStud->show());
            $table->addRow($tableRow);
        
            $str .= $box->show(ucwords($this->_objLanguage->code2Txt('mod_contextgroups_ttlStudents','contextgroups')),$table->show());



            $table = & $this->newObject('htmltable' , 'htmlelements');
            //lecturers
            $guestArr = $objGroups->contextUsers('Guest');
            
            //table headings
            $table->width = '60%';
            $rowcount = 0;
            if(count($guestArr) > 0)
            {
                
                foreach($guestArr as $guest)
                {
                    $oddOrEven = ($rowcount == 0) ? "even" : "odd";
                    $tableRow = array($guest['fullname']);
                    $table->addRow($tableRow);
                    $rowcount = ($rowcount == 0) ? 1 : 0;
                }
            } else {
                $temp = array('<div align="center" style="font-size:small;font-weight:bold;color:#CCCCCC;font-family: Helvetica, sans-serif;">'.
                $this->_objLanguage->code2Txt('mod_groupadmin_noguest','groupadmin',array('context'=>'course')).
                '</div>');
                 $table->addRow($temp);
            }
            $lnkGuest = $this->newObject('link', 'htmlelements');
            $lnkGuest->href = $this->uri( array( 'action'=>'manage_guest' ),'contextgroups' );
            $lnkGuest->link = $this->_objLanguage->languageText('mod_contextgroups_manageguests','contextgroups');

            $tableRow = array('<hr/>'.$lnkGuest->show());
            $table->addRow($tableRow);
        
            $str .= $box->show(ucwords($this->_objLanguage->languageText('mod_contextgroups_ttlGuest','contextgroups')),$table->show());
 

            //students
           
            return $str;
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
	  	$objLink = $this->getObject('link','htmlelements');
	  	$objIcon = $this->getObject('geticon', 'htmlelements');
	  	
	  	if($this->_objContextModules->isContextPlugin($this->_objDBContext->getContextCode(), 'contextcontent'))
	  	{
	  	    $objLink->href = $this->uri(null,'contextdesigner');	
	  	    $objIcon->setModuleIcon('contextdesigner');
	  	    $objLink->link = $objIcon->show(). '  '.$this->_objLanguage->code2Txt("mod_contextdesigner_name",'contextdesigner',array('context'=>'Course'));
	  	    $contentsection = '<div class="tab-page">
				<h2 class="tab">'.$this->_objLanguage->languageText('mod_contextcontent_contentmanager','contextcontent').'</h2>'.$objLink->show().
	  	    '</div>';
	  	} else {
	  	    $contentsection = '';
	  	}
	  	$str = '<div class="tab-page">
		

		
		<!-- id is not necessary unless you want to support multiple tabs with persistence -->
		<div class="tab-pane" id="tabPane3">

			<div class="tab-page">
				<h2 class="tab">'.$this->_objLanguage->languageText('mod_contextadmin_plugins','contextadmin').'</h2>
				
				'. $this->getPluginForm().'
				
			</div>
            <div class="tab-page">
                <h2 class="tab">'.$this->_objLanguage->languageText('mod_contextadmin_users','contextadmin').'</h2>
                '.$this->getContextUsers().'
                
            </div>
			<div class="tab-page">
				<h2 class="tab">'.$this->_objLanguage->languageText('mod_contextadmin_communication','contextadmin').'</h2>

				
				Send Email to class
				
			</div>
			
			'.$contentsection.'
			
			<!--div class="tab-page">
				<h2 class="tab">Assessment Tools</h2>
				Assessment Tools can go here
				
			</div>
			<div class="tab-page">
				<h2 class="tab">Personal</h2>
				my personal space can go here
				
			</div-->
			
			<div class="tab-page">
				<h2 class="tab">'.$this->_objLanguage->languageText('mod_contextadmin_configure','contextadmin').'</h2>
				'.$this->getEditContextForm().'
				
			</div>
			
			<div class="tab-page">
				<h2 class="tab">'.$this->_objLanguage->languageText('mod_contextadmin_about','contextadmin').'</h2>
				'.$this->getAboutForm().'
				
			</div>
            

		</div>
		
	</div>';
	  	$objFeatureBox = $this->newObject('featurebox', 'navigation');
	  	//$objFeatureBox->title = 'Tool Box';
	  	return $objFeatureBox->show($this->_objDBContext->getTitle().' Tool Box', $str);
	  	return $str;
	  }
	  
	  
	  
	   /**
	   * Method to get a filter list to filter the courses
	   * @param array $courseList the list of courses
	   * @return string
	   * @access public
	   */
	  public function getFilterList($courseList)
	  {
	  	
	  	try {
	  		$objAlphabet=& $this->getObject('alphabet','navigation');
	  		$linkarray=array('filter'=>'LETTER');
			$url=$this->uri($linkarray,'contextpostlogin');
	  		$str = $objAlphabet->putAlpha($url);
	  		return $str;
	  		
	  	}
	  	catch (Exception $e) {
    		echo customException::cleanUp('Caught exception: '.$e->getMessage());
    		exit();
    	}
	  }
}	
?>