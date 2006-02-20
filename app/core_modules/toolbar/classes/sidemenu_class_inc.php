<?
/**
* sidemenu extends object
* @package toolbar
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Sidemenu class dynamically creates and displays a side navigation menu.
* The different menus are context, user and postlogin.
* The menu is created dynamically using table tbl_menu_category. Modules set to
* go into the postlogin menu are given the menu category menu_postlogin-num. Where
* num indicates the position of the item in the menu: 1 = top, 2 = middle,
* 3 = bottom.
*
* The permission of the user to access a module is checked before displaying the link.
*
* The functions available in the class:
*
* menuContext() - returns the context side menu.
* menuUser() - returns the user side menu.
* menuPostLogin() - returns the postlogin side menu.
*
* @author Megan Watson
* @author Tohir Solomons
* @copyright (c)2004 UWC
* @package toolbar
* @version 1.1
*/


class sidemenu extends object
{
    /**
    * Method to construct the class.
    */
    function init()
    {
        $this->dbMenu =& $this->newObject('dbmenu', 'toolbar');
        $this->objTools =& $this->newObject('tools', 'toolbar');

        $this->objLanguage= $this->getObject('language','language');
        $this->objUser =& $this->getObject('user', 'security');
        $this->objUserPic =& $this->getObject('imageupload', 'useradmin');
        $this->objIcon =& $this->newObject('geticon', 'htmlelements');
        $this->objLink =& $this->getObject('link','htmlelements');

        $this->objHead =& $this->newObject('htmlheading','htmlelements');
        $this->objHead->type=3;

        // Create a global table - Other methods are allowed to add to this table
        $this->globalTable=$this->newObject('htmltable','htmlelements');
        $this->globalTable->cellpadding=5;
        $this->globalTable->width='99%';

        // get images from icons/modules folder
        $objSkin = & $this->getObject('skin','skin');
        $this->iconModFolder = $objSkin->getSkinLocation()."icons/modules/";
        $this->iconFolder = $objSkin->getSkinLocation()."icons/";

        // Get Context Code & Title
        $this->objContext =& $this->getObject('dbcontext', 'context');
        if($this->objContext->isInContext()){
            $this->contextTitle = $this->objContext->getTitle();
            $this->contextcode = $this->objContext->getcontextcode();
            $this->context = TRUE;
        }else{
            $this->contextTitle = $this->objLanguage->languageText('word_inlobby');
            $this->contextcode = '';
            $this->context = FALSE;
        }
    }

    /**
    * Method to build a side menu for context.
    * The method calls a function to check if the user is in a context and whether
    * the user has admin rights. This information is used to determine which modules
    * to get from the database. The module permissions are then checked to determine
    * whether the user has permission to access the module. Another function is
    * then called to build the menu.
    */
    function menuContext()
    {
        $access = $this->checkAccess();
        $menus = $this->dbMenu->getSideMenus('context', $access, $this->context);
        $menus = $this->checkPerm($menus);

        $this->objHead->str = $this->objContext->getField('menuText');
        $menu = $this->objHead->show();
        if(!empty($this->contextcode)){
            $menu .= $this->joinInterestGroup();
        }
        $menu .= '<p>'.$this->getMenuList($menus).'</p>';

        return $menu;
    }

    /**
    * Method to build the side menu for user.
    * The method calls a function to check if the user is in a context and whether
    * the user has admin rights. This information is used to determine which modules
    * to get from the database. The module permissions are then checked to determine
    * whether the user has permission to access the module. Another function is
    * then called to build the menu.
    * @return string $menu The finished menu
    */
    function menuUser()
    {
        $access = $this->checkAccess();
        $menus = $this->dbMenu->getSideMenus('user', $access, $this->context);
        $menus = $this->checkPerm($menus);

        $this->objHead->str = $this->objUser->fullName();
        $menu = $this->objHead->show();

        $menu .= '<p align="center"><img src="'.$this->objUserPic->userpicture($this->objUser->userId() ).'" /></p>';

        $menu .= $this->getMenuList($menus);
        return $menu;
    }

    /**
    * Method to build the side menu for postlogin.
    * The method calls a function to check if the user is in a context and whether
    * the user has admin rights. This information is used to determine which modules
    * to get from the database. The module permissions are then checked to determine
    * whether the user has permission to access the module. Another function is
    * then called to build the menu.
    * @return string $menu The finished menu
    */
    function menuPostLogin()
    {
        $access = $this->checkAccess();
        $menus = $this->dbMenu->getSideMenus('postlogin', $access, $this->context);
        $menus = $this->checkPerm($menus);

        $this->objHead->str = $this->objUser->fullName();
        $menu =  $this->objHead->show();

        $menu .= '<p align="center"><img src="'.$this->objUserPic->userpicture($this->objUser->userId() ).'" /></p>';

        $menu .= $this->joinContext();

        $menu .= $this->getMenuList($menus);
        return $menu;

    }

    /**
    * Method to check the permissions for a module and determine whether the user
    * has permission to access the module.
    * @param array $modules A list of modules and their permissions.
    * @param array $menus The list of accessible modules.
    */
    function checkPerm($modules)
    {
        $menus = array();
        foreach($modules as $module){
            if(!empty($module['permissions'])){
                if($this->objTools->checkPermissions($module, $this->context)){
                    $menus[]=$module;
                }
            }else{
                $menus[]=$module;
            }
        }
        return $menus;
    }

    /**
    * Method to check whether the user has admin rights.
    */
    function checkAccess()
    {
        $access = 2;
        if($this->objUser->isAdmin()){
            $access = 1;
        }
        return $access;
    }

    /**
    * Method to get a list of registered modules to build the side menu.
    * The link in the side menu is specified in the register.conf file using
    * the form SIDEMENU: menu-1.
    * If an action is required it takes the form
    * SIDEMENU: menu-1|permissions|linkaction|icon|language code
    * @param array $modules The list of modules.
    */
    function getMenuList($modules)
    {
        if(!empty($modules)){
            foreach($modules as $line){
                $actions = explode('|', $line['category']);
                // Check if there is an action and insert a array for a link.
                if(isset($actions[2]) && !empty($actions[2])){
                    $linkArray = array('action'=>$actions[2]);
                }else{
                    $linkArray = null;
                }
                if(isset($actions[3]) && !empty($actions[3])){
                    $icon = $actions[3];
                }else{
                    $icon = $line['module'];
                }
                if(isset($actions[4]) && !empty($actions[4])){
                    $name = ucwords($this->objLanguage->code2Txt($actions[4]));
                }else{
                    $name = ucwords($this->objLanguage->code2Txt('mod_'.$line['module'].'_name'));
                }

                if($line['module'] == 'email'){
                    // Add new email count if the module is email
                    $kngmail =& $this->getObject('kngmail', 'email');
                    $emails = $kngmail->listMail($this->objUser->userId(), 'new');
                    $count = count($emails);
                    $name .= ' ('.$count.' ' .$this->objLanguage->languageText('word_new').')';
                }
                $this->addNavigationRow($name, $line['module'], $icon, $linkArray);
            }
        }
        return $this->globalTable->show();
    }

    /**
    * This method adds rows to the global table.
    *
    * @param string $moduleName: Name to be displayed
    * @param string $moduleId: Module URI
    * @param string $icon: Icon to be displayed.
    * @param array $linkArray: Additional parameters for URI
    */
    function addNavigationRow($moduleName, $module, $icon, $linkArray=null)
    {
        $this->loadClass('link', 'htmlelements');

        $this->globalTable->startRow();

        // Replace icon with the default icon if it can't be found.
        if(file_exists($this->iconModFolder.$icon.'.gif')){
            $this->objIcon->setModuleIcon($icon);
        }else if(file_exists($this->iconFolder.$icon.'.gif')){
            $this->objIcon->setIcon($icon);
        }else{
            $this->objIcon->setModuleIcon('default');
        }
        $this->objIcon->alt = $moduleName;
        $this->objIcon->title= $moduleName;

        $this->globalTable->addCell($this->objIcon->show(), 20, 'absmiddle', 'center');

        $moduleLink = new link($this->uri($linkArray, $module));
        $moduleLink->link = $moduleName;

        $this->globalTable->addCell($moduleLink->show(), null, 'absmiddle');

        $this->globalTable->endRow();
    }

    /**
    * Method to add the dropdown for joining or leaving a course.
    */
    function joinContext()
    {
        $objModule =& $this->getObject('modulesadmin','modulelist');
        $contextAdminUtils =& $this->getObject('contextadminutils','contextadmin');
        $objForm =& $this->newObject('form','htmlelements');
        $objButton =& $this->newObject('button','htmlelements');
        $objDrop =& $this->newObject('dropdown','htmlelements');

        $joinCourse = ucwords($this->objLanguage->code2Txt('mod_context_joincontext',array('context'=>'course')));
        $leaveCourse = ucwords($this->objLanguage->code2Txt('mod_toolbar_leavecontext'));
        $go = $this->objLanguage->languageText('word_go');
        $inCourse = $this->objLanguage->languageText('mod_postlogin_currentlyincontext');
        $str = '';

        if($objModule->checkIfRegistered('context','context')){

            $this->objHead->type = 4;
            $this->objHead->str = $joinCourse;

            $str .= $this->objHead->show();

            //The Course that you are currently in
            $this->objIcon->setIcon('leavecourse');
            $this->objIcon->alt = $leaveCourse;
            $this->objIcon->title = $leaveCourse;
            $objLeaveButton = $this->objIcon->show();

            $objLink = new link($this->uri(array('action'=>'leavecontext'),'_default'));
            $objLink->link = $objLeaveButton;
            $objLeaveLink = $objLink->show();

            $contextObject =& $this->getObject('dbcontext', 'context');
            $contextcode = $contextObject->getcontextcode();

            $objLink = new link($this->uri(null, 'context'));
            $objLink->link = $this->contextTitle;
            $contextLink = $objLink->show();

            // Set Context Code to 'root' if not in context
            if ($this->contextcode == ''){
                $contextTitle = $this->contextTitle;
            } else {
                $contextTitle = $contextLink.' '.$objLeaveLink;
            }

            $contextTitle = str_replace('{context}', '<strong>'.$contextTitle.'</strong>',
            $inCourse);

            $str .= '<p>'.$contextTitle.'</p>';

            // get number of courses available
            $numberofcontexts = count($this->objContext->getAll());

            // dont show course drop down if no courses are available
            if ($numberofcontexts > 0) {
                $objForm = new form('joincontext',
                $this->uri(array('action'=>'joincontext'),'context'));
                $objForm->setDisplayType(3);

                $objDrop = new dropdown('context_dropdown');
                $objDrop->cssClass = 'coursechooser';
                $objDrop->addFromDB($contextAdminUtils->getUserContext(),'menutext','contextcode',
                $this->contextcode);

                $objButton = new button();
                $objButton->setToSubmit();
                $objButton->setValue($go);

                $objForm->addToForm('<p>'.$objDrop->show().'</p>');
                $objForm->addToForm('<p>'.$objButton->show().'</p>');

                $str .= $objForm->show();
            }
        }
        return $str;
    }

    /**
    * Method to create a dropdown list on interest groups (workgroups).
    * @param string $filter Determines if users should be filtered by context or not (alumni users).
    * @return the html string.
    */
    function joinInterestGroup($filter = 'context')
    {
        // Check if workgroup is registered and active for the context
        $objModule =& $this->getObject('modulesadmin','modulelist');
        $objCondition =& $this->getObject('contextcondition','contextpermissions');
        $objForm =& $this->newObject('form','htmlelements');
        $objButton =& $this->newObject('button','htmlelements');
        $objDrop =& $this->newObject('dropdown','htmlelements');

        $notaMember = $this->objLanguage->code2Txt('phrase_notamemberofanyworkgroup');
        $leaveGroup = $this->objLanguage->code2Txt('mod_workgroup_leavegroup');
        $join = ucwords($this->objLanguage->code2Txt('phrase_joinworkgroup'));
        $notInGroup = $this->objLanguage->code2Txt('phrase_notinworkgroup');
        $inGroup = $this->objLanguage->code2Txt('phrase_currentlyinworkgroup');
        $go = $this->objLanguage->languageText('word_go');

        $str = '';
        if ($objModule->checkIfRegistered('workgroup', 'workgroup')) {
            $objDBWorkgroup =& $this->getObject('dbworkgroup', 'workgroup');
            $this->objHeading = &$this->newObject('htmlheading', 'htmlelements');
            $this->objHeading->str = $join;
            $this->objHeading->type = 4;
            $str = $this->objHeading->show();

            if($filter == 'context'){
                // Get available workgroups. Lecturers - all in context
                if($objCondition->isContextMember('Lecturers')){
                    $workgroups = $objDBWorkgroup->getAll($this->contextcode);
                }else{
                    $workgroups = $objDBWorkgroup->getAllForUser($this->contextcode, $this->objUser->userId());
                }
            }else{
                $workgroups = $objDBWorkgroup->getAllForUser(NULL, $this->objUser->userId());
            }

            // No workgroups are available.
            if (count($workgroups) == 0) {
                $str .= $notaMember;
            }
            else {
                $workgroupId = $objDBWorkgroup->getWorkgroupId();
                if ($workgroupId == NULL){
                    $workgroupTitle = '';//"<strong>".$notInGroup."</strong>";
                } else {
                    $objLink = new link($this->uri(null, 'workgroup'));
                    $objLink->link = $objDBWorkgroup->getDescription($workgroupId);
                    $workGroupLink = $objLink->show();

                    $this->objIcon->setIcon('leavecourse');
                    $this->objIcon->alt = $leaveGroup;
                    $this->objIcon->title = $leaveGroup;

                    $objLink = new link($this->uri(array('action'=>'leaveworkgroup'),'workgroup'));
                    $objLink->link = $this->objIcon->show();
                    $workgroupTitle = $workGroupLink.' '.$objLink->show();
                    $workgroupTitle = str_replace('{workgroup}', '<strong>'.$workgroupTitle.'</strong>', $inGroup);
                }
                $str .= '<p>'.$workgroupTitle.'</p>';

                $objForm = new form('joinworkgroup', $this->uri(array('action'=>'joinworkgroup'),'workgroup'));
                $objForm->setDisplayType(3);

                $dropdown = new dropdown('workgroup');
                $dropdown->cssClass = 'coursechooser';
                $dropdown->addFromDB($workgroups, 'description', 'id', $workgroupId);

                $button = new button('save', $go);
                $button->setToSubmit();
                $objForm->addToForm('<p>'.$dropdown->show().'</p>');
                $objForm->addToForm('<p>'.$button->show().'</p>');
                $str .= $objForm->show();
            }
        }
        return $str;
    }
}
?>