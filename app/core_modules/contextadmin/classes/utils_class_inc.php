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

            $this->_objContextModules =  $this->getObject('dbcontextmodules', 'context');
            $this->_objLanguage =  $this->getObject('language', 'language');
            $this->_objUser =  $this->getObject('user', 'security');
            $this->_objDBContext =  $this->getObject('dbcontext', 'context');
        
            // Load HTML Elements
            $this->loadClass('form', 'htmlelements');
            $this->loadClass('textinput', 'htmlelements');
            $this->loadClass('button', 'htmlelements');
            $this->loadClass('dropdown', 'htmlelements');
            $this->loadClass('htmlheading', 'htmlelements');
            $this->loadClass('checkbox', 'htmlelements');
            $this->loadClass('link', 'htmlelements');
            $this->loadClass('radio', 'htmlelements');

    }

    
    /**
       * Method to get the users context that he
       * is registered to
       * @return array
       * @access public
       */
      public function getContextList()
      {
        

        //if the user is an administrator of the site then show him all the courses
        
          if ($this->_objUser->isAdmin())
         {
            $newContexts = array();
            $contexts = $this->_objDBContext->getAll(' ORDER BY title');
            
            if (count($contexts) > 0) {
                foreach ($contexts as $context)
                {
                    if ($context['archive'] != '1') {
                        if ($context['archive'] == 0) {
                            $newContexts[] = $context;
                        }
                    }
                };
            }
            return  $newContexts;
        
        } else {
            $arr = array();
            
            $objGroups =  $this->newObject('managegroups', 'contextgroups');
            $contextCodes = $objGroups->usercontextcodes($this->_objUser->userId());
            
            foreach ($contextCodes as $code)
            {
                
                $context = $this->_objDBContext->getRow('contextcode',$code);
                
                if ($context != FALSE) {
                    if ($context['archive'] == 0) {
                        $arr[] = $context;
                    }
                }
            };
          
            return $arr;
        }
      }
      
      /**
      * Method to get the list of archived context
      * 
      * @return array
      */
      public function getArchivedContext()
      {
        $arr = array();

        //if the user is an administrator of the site then show him all the courses
        
        $arch = $this->_objDBContext->getAll("WHERE archive=1");
          if ($this->_objUser->isAdmin())
        {
            return  $arch;
        } 

          $objGroups =  $this->newObject('managegroups', 'contextgroups');
          $contextCodes = $objGroups->usercontextcodes($this->_objUser->userId());

        //if the user is lecturer in the archived context then show it
            
          foreach ($contextCodes as $code)
          {
               foreach($arch as $a)
               {
                if($a['contextcode'] == $code['contextcode'])
                {print $a['contextcode'];
                    $arr[] = $this->_objDBContext->getRow('contextcode',$code);        
                }
            }
           
          }
        //var_dump($contextCodes);
          return $arr;
      }

    /**
    * Method to get a list of courses that the user is an lecturer in
    * @return array
    * @access  public
    */
    public function getContextAdminList()
    {

        return NULL;
    }

      /**
       * Method to get the users context that he
       * is registered to
       * @return array
       * @access public
       */
      public function getOtherContextList()
      {

          $objGroups =  $this->newObject('managegroups', 'contextgroups');
          return NULL;//$objGroups->usercontextcodes($this->_objUser->userId());
      }

      /**
       * Method to get the left widgets
       * @return string
       * @access public
       */
      public function getLeftContent()
      {
          //Put a block to test the blocks module
        $objBlocks =  $this->newObject('blocks', 'blocks');
       //$userPic  = &$this->newObject('userutils', 'contextpostlogin');
       $leftSideColumn = $this->_objUser->getUserPic();//$userMenu->show();;
        //Add loginhistory block

        if($this->_objDBContext->isInContext())
        {
            //$objContextUtils =  $this->getObject('utilities','context');
            $cm =''; //$objContextUtils->getHiddenContextMenu('home','none');
        } else {
            $cm = '';
        }    
        $leftSideColumn .= $cm;
    

        $leftSideColumn .= $objBlocks->showBlock('latest', 'blog');

        $leftSideColumn .= $objBlocks->showBlock('loginstats', 'context');

        $leftSideColumn .= $objBlocks->showBlock('calendar', 'eventscalendar');

        $leftSideColumn .= $objBlocks->showBlock('latestpodcast', 'podcast');

        $leftSideColumn .= $objBlocks->showBlock('contextchat', 'messaging');


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
         $objBlocks =  $this->newObject('blocks', 'blocks');
        //Add the getting help block
        $rightSideColumn .= $objBlocks->showBlock('dictionary', 'dictionary');
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

              $arr = $objLeaf->getGroupUsers($leafId);

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
          $objIcon =  $this->newObject('geticon', 'htmlelements');
        $objLink = new link();
          $objModule =  $this->newObject('modules', 'modulecatalogue');
          if(is_array($arr))
          {
              foreach($arr as $plugin)
              {

                  $modInfo =$objModule->getModuleInfo($plugin);

                  $objIcon->setModuleIcon($plugin);
                  $objIcon->alt = $modInfo['name'];
                  //$str .= $objIcon->show().'   ';

                $objLink->href = $this->uri(array ('action' => 'gotomodule', 'moduleid' => $plugin, 'contextcode' => $contextCode), 'context');
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
      public function getPluginForm($contextCode = NULL)
      {

          if(empty($contextCode))
          {
              $contextCode = $this->_objDBContext->getContextCode();
          }
          $objForm = new form();
          $objFormMod = new form();
        $objH = new htmlheading();
        $inpContextCode =  new textinput();
        $inpMenuText = new textinput();
        $objDBContextParams =  $this->newObject('dbcontextparams', 'context');
        $featureBox = $this->getObject('featurebox', 'navigation');

        //list of modules for this context
        $arrContextModules = $this->_objContextModules->getContextModules($contextCode);

        $inpButton =  new button();
                  //setup the form
        $objForm->name = 'addfrm';
        $objForm->action = $this->uri(array('action' => 'savestep3'));        
        $objForm->displayType = 3;

        $objFormMod->name = 'modfrm';
        $objFormMod->action = $this->uri(array('action' => 'savedefaultmod'));
        $objFormMod->displayType = 1;

        $inpAbout->name = 'about';
        $inpAbout->id = 'about';
        $inpAbout->value = '';
        
        $inpAbout->width = '20px';


        $inpButton->setToSubmit();
        $inpButton->cssClass = 'f-submit';
        $inpButton->value = ucwords($this->_objLanguage->languageText("word_save"));


        //validation
        //$objForm->addRule('about','About is a required field!', 'required');


        //$objForm->addToForm('<div class="req"><b>*</b> Indicates required field</div>');

        $objForm->addToForm('<fieldset>');
        $objForm->addToForm($objH->show());
        $objForm->addToForm('<div id="resultslist-wrap"><ol>');

        $objModuleFile =  $this->newObject('modulefile', 'modulecatalogue');
        $objModules =  $this->newObject('modules', 'modulecatalogue');
        $arrModules = $objModules->getModules(2);


        foreach ($arrModules as $module)
        {
            if($objModuleFile->contextPlugin($module['module_id']))
            {
                $checkbox = new checkbox('mod_'.$module['module_id']);
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
                
                $objForm->addToForm('<ul><dt>'.$checkbox->show().'&nbsp;'.$icon->show().'&nbsp;'.ucwords($this->_objLanguage->code2Txt('mod_'.$module['module_id'].'_name',$module['module_id'],array('context' => 'Course'))).'</dt>');
                $objForm->addToForm('<dd  class="subdued">'.$this->_objLanguage->abstractText($module['description']).'</dd>');
                $objForm->addToForm('</ul>');
            }

        }


        $objForm->addToForm('</ol></div><div class="f-submit-wrap">'.$inpButton->show().'</div></fieldset>');

        $dropDefaultModule = new dropdown();

        $defaultmoduleid = $objDBContextParams->getParamValue($contextCode, 'defaultmodule');

        $drop = '<select id="defaultmodule" name="defaultmodule">';

        $drop .= '<option value="">'.$this->_objLanguage->languageText("mod_context_setdefaultmodule",'context').'</option>';
        
        //$inpButton->value = $this->_objLanguage->languageText("mod_context_setasdefaultmodule",'context');
    
        foreach($arrContextModules as $mod)
        {
            $modInfo = $objModules->getModuleInfo($mod['moduleid']);

            $drop .= '<option value="'.$mod['moduleid'].'"';
            $drop .= ($defaultmoduleid == $mod['moduleid']) ? ' selected="selected" ' : '';
            $drop .= '>'.ucwords($modInfo['name']).'</option>';
        }
        $drop .= '</select>';
        $drop ='<div style="width:270px">'.$drop.$inpButton->show().'</div>';
    
    
        
        $objFormMod->addToForm($drop);
        $objFormMod->addToForm('<span class="subdued">'.$this->_objLanguage->code2Txt("mod_contextadmin_defaultmodhelp",'context',array('context'=>'Course')).'</span>');

        return  $featureBox->show($this->_objLanguage->languageText("mod_context_setdefaultmodule",'context'), $objFormMod->show()).
                $featureBox->show('Plugin List',$objForm->show()).'<br/>';


      }

      /**
       * Get context edit form
       * @return string
       *
       */
      public function getEditContextForm($contextCode = NULL)
      {
          if(empty($contextCode))
          {
              $contextCode = $this->_objDBContext->getContextCode();
          }

              $context = $this->_objDBContext->getRow('contextcode' , $contextCode);

            $objH = new htmlheading();
            $objForm = new form();

            $inpContextCode =  new textinput();
            $inpMenuText = new textinput();
            $inpTitle = new textinput();
            $inpButton =  new button();
            $objIcon =  $this->newObject('geticon','htmlelements');
            $dropAccess = new dropdown();
            //$radioStatus = new radio();
            $objStartDate =   $this->newObject('datepicker', 'htmlelements');
            $objFinishDate =   $this->newObject('datepicker', 'htmlelements');

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
            $drop .=         '<label for="Open">
                            <input id="Open" type="radio" name="access" '.$checked.' value="Open" class="f-radio" tabindex="9" />
                            '.$this->_objLanguage->languageText("mod_context_open",'context').' <span class="caption">  -  '.$this->_objLanguage->code2Txt("mod_context_openhelp",'context',array('context'=>'Course')).'</span></label>';

            $checked = ($context['access'] == 'Private') ? ' checked = "checked" ' : '';
            $drop .='        <label for="Private">

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
            
            $objForm->addRule('menutext',$this->_objLanguage->languageText("mod_contextadmin_err_requiremenutext",'contextadmin'), 'required!');
            $objForm->addRule('title',$this->_objLanguage->languageText("mod_contextadmin_err_requiretitle",'contextadmin'), 'required!');

            $objForm->addToForm('<div class="req"><b>*</b>'.$this->_objLanguage->languageText("mod_context_required",'context').'</div>');
            $objForm->addToForm('<fieldset>');
            

            $objForm->addToForm('<label for="contextcode"><b><span class="req">*</span>'.ucwords($this->_objLanguage->code2Txt("mod_context_contextcode",'context',array('context'=>'Course'))).':</b> <span class="highlight">');
            $objForm->addToForm($this->_objDBContext->getContextCode().'</span><br /></label>');

            $objForm->addToForm('<label for="title"><b><span class="req">*</span>'.$this->_objLanguage->languageText("word_title").':</b>');
            $objForm->addToForm($inpTitle->show().'<br /></label>');

            $objForm->addToForm('<label for="menutext"><b><span class="req">*</span>'.$this->_objLanguage->languageText("mod_context_menutext",'context').':</b>');
            $objForm->addToForm($inpMenuText->show().'<br /></label>');

        
            $objForm->addToForm('<label for="access"><b><span class="req">*</span>'.$this->_objLanguage->languageText("mod_context_status",'context').':</b>');
            $objForm->addToForm($dropAccess->show().'<br /></label>');

            $objForm->addToForm($drop);
            $objForm->addToForm('<label>&nbsp;<br/></label>');
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
            $objH = new htmlheading();
            $objForm = new form();

            $inpContextCode =  new textinput();
            $inpMenuText = new textinput();
            $inpAbout =  $this->newObject('htmlarea','htmlelements');
            $inpButton =  new button();

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
            $objLink =  new link();
            $icon =   $this->newObject('geticon', 'htmlelements');
            $table =  $this->newObject('htmltable' , 'htmlelements');
            $objGroups =  $this->newObject('managegroups', 'contextgroups');
            $box =   $this->newObject('featurebox', 'navigation');
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
                    $tableRow = array($this->_objUser->fullname($lecture['userid']));
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
            $table =  $this->newObject('htmltable' , 'htmlelements');
            //table headings
            $table->width = '60%';
            $rowcount = 0;
            if(count($studentArr) > 0)
            {

                foreach($studentArr as $student)
                {
                    $oddOrEven = ($rowcount == 0) ? "even" : "odd";
                    $tableRow = array($this->_objUser->fullname($student['userid']));
                    $table->addRow($tableRow);
                    $rowcount = ($rowcount == 0) ? 1 : 0;
                }
            } else {
                $temp = array('<div align="center" style="font-size:small;font-weight:bold;color:#CCCCCC;font-family: Helvetica, sans-serif;">'.
                $this->_objLanguage->code2Txt('mod_groupadmin_nostuds','groupadmin',array('context'=>'course','readonlys' => 'students')).
                '</div>');
                 $table->addRow($temp);
            }
           $lnkStud = new link();
            $lnkStud->href = $this->uri( array( 'action'=>'manage_stud' ),'contextgroups' );
            $lnkStud->link = $this->_objLanguage->code2Txt('mod_contextgroups_managestuds','contextgroups',array('readonlys'=>''));


            $tableRow = array('<hr/>'.$lnkStud->show());
            $table->addRow($tableRow);

            $str .= $box->show(ucwords($this->_objLanguage->code2Txt('mod_contextgroups_ttlStudents','contextgroups')),$table->show());



            $table =  $this->newObject('htmltable' , 'htmlelements');
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
                    $tableRow = array($this->_objUser->fullname($guest['userid']));
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
      
          $objLink = $this->newObject('link','htmlelements');
          $objLink2 = $this->newObject('link','htmlelements');
          $objIcon = $this->newObject('geticon', 'htmlelements');
          $objIcon2 = $this->newObject('geticon', 'htmlelements');

          if($this->_objContextModules->isContextPlugin($this->_objDBContext->getContextCode(), 'contextcontent'))
          {
              $objLink->href = $this->uri(NULL,'contextdesigner');
              $objLink2->href = $this->uri(NULL,'contextcontent');

              $objIcon2->setModuleIcon('contextcontent');
              $objIcon->setModuleIcon('contextdesigner');

              $objLink->link = $objIcon->show(). '  '.ucwords($this->_objLanguage->code2Txt("mod_contextdesigner_name",'contextdesigner',array('context'=>'Course')));
              $objLink2->link = $objIcon2->show(). '  '.ucwords($this->_objLanguage->code2Txt("mod_contextcontent_about_title",'contextcontent',array('context'=>'Course')));

              $contentsection = '<div class="tab-page">
                <h2 class="tab">'.ucwords($this->_objLanguage->languageText('mod_contextcontent_contentmanager','contextcontent')).'</h2>'.
                      $objLink->show().
                '<br/>'.$objLink2->show().
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
            <!--div class="tab-page">
                <h2 class="tab">'.$this->_objLanguage->languageText('mod_contextadmin_communication','contextadmin').'</h2>


                Send Email to class

            </div-->

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
              $objAlphabet= $this->getObject('alphabet','navigation');
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
      
      /**
      * Method to delete a context
      * 
      * @param string $contextCode
      * @return boolean
      */
      public function deleteContext($contextCode)
      {
        try{
                    
            //achive the context
            $this->_objDBContext->archiveContext($contextCode);
            
        }
        catch (customException $e)
        {
            echo customException::cleanUp($e);
            die();
        }
        
            
      }
      
      
      /**
      * Method to delete a context
      * 
      * @param string $contextCode
      * @return boolean
      */
      public function undeleteContext($contextCode)
      {
        try{
                    
            //undelete the context
            $this->_objDBContext->undeleteContext($contextCode);
            
        }
        catch (customException $e)
        {
            echo customException::cleanUp($e);
            die();
        }
        
            
      }
}
?>