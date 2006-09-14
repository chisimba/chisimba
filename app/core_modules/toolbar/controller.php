<?php
/**
* @package toolbar
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Controller class for toolbar module
* @author Megan Watson
* @copyright (c) 2004 UWC
* @version 1.0
*/

class toolbar extends controller
{
    /**
    * Method to construct the class.
    */
    function init()
    {
        $this->objPage =& $this->getObject('page');
        $this->objRegister =& $this->getObject('register');
        $this->objDbMenu =& $this->getObject('dbmenu');
        $this->objPerms =& $this->getObject('permissions_model', 'permissions');
        $this->objGroups =& $this->getObject('groupadminmodel', 'groupadmin');
        $this->objModules =& $this->getObject('modules', 'modulecatalogue');
        $this->objContext =& $this->getObject('dbcontext', 'context');
        if($this->objContext->isInContext()){
            $this->context = TRUE;
        }else{
            $this->context = FALSE;
        }
        $this->objLanguage =& $this->getObject('language', 'language');
    }

    /**
    * Standard dispatch function
    */
    function dispatch($action)
    {
    	 $this->setVar('pageSuppressXML',true);
        switch($action){
            case 'editlinks':
                $module = $this->getParam('modulename', NULL);
                if(!$module){
                    $module = 'toolbar';
                }
                return $this->editLinks($module);

            case 'restore':
                return $this->restoreDefaults();

            case 'addtool':
                $module = $this->getParam('modulename');
                $data = $this->objRegister->readModuleData($module);

                $this->setVarByRef('modData', $data);
                $this->setVarByRef('moduleName', $module);
                $this->setVar('mode', 'add');
                return 'addtool_tpl.php';

            case 'edittool':
                $id = $this->getParam('id');
                $data = $this->objDbMenu->getModuleLinks("id='$id'");
                $this->setVarByRef('data', $data[0]);
                $this->setVarByRef('moduleName', $data[0]['module']);
                $this->setVar('mode', 'edit');
                return 'addtool_tpl.php';

            case 'savetool':
                return $this->saveToolLink();

            case 'delete':
                $id = $this->getParam('id');
                $module = $this->getParam('modulename');
                $this->objDbMenu->delete('id', $id);
                return $this->nextAction('editlinks', array('modulename'=>$module));

            case 'addmenu':
                $module = $this->getParam('modulename');
                $data = $this->objRegister->readModuleData($module);
                $this->setVarByRef('modData', $data);
                $this->setVarByRef('moduleName', $module);
                $this->setVar('page', FALSE);
                $this->setVar('mode', 'add');
                return 'addmenu_tpl.php';

            case 'editmenu':
                $id = $this->getParam('id');
                $data = $this->objDbMenu->getModuleLinks("id='$id'");
                $this->setVarByRef('data', $data[0]);
                $this->setVarByRef('moduleName', $data[0]['module']);
                $this->setVar('page', FALSE);
                $this->setVar('mode', 'edit');
                return 'addmenu_tpl.php';

            case 'setperm':
                $module = $this->getParam('modulename');
                $this->setVarByRef('moduleName', $module);
                $this->setVar('setDefault', FALSE);
                return 'editperms_tpl.php';

            case 'restoreperms':
                return $this->restorePerms();

            case 'savemenu':
                return $this->saveMenuLink();

            case 'addpage':
                $module = $this->getParam('modulename');
                $data = $this->objRegister->readModuleData($module);
                $this->setVarByRef('modData', $data);
                $this->setVarByRef('moduleName', $module);
                $this->setVar('page', TRUE);
                $this->setVar('mode', 'add');
                return 'addmenu_tpl.php';

            case 'editpage':
                $id = $this->getParam('id');
                $data = $this->objDbMenu->getModuleLinks("id='$id'");
                $this->setVarByRef('data', $data[0]);
                $this->setVarByRef('moduleName', $data[0]['module']);
                $this->setVar('page', TRUE);
                $this->setVar('mode', 'edit');
                return 'addmenu_tpl.php';

            case 'savepage':
                return $this->savePageLink();

            case 'updatemenus':
                $this->objRegister->updateMenus();
                return $this->nextAction('');

            case 'updateperms':
                $this->objRegister->updatePermissions();
                return $this->nextAction('');

            default:
                $modules = $this->objPage->getPage('admin', $this->context);
                $this->setVarByRef('modules',$modules);
                return 'admin_tpl.php';
        }
    }

    /**
    * Method to edit the links displayed for a module.
    * The method checks the menu_category table and displays the places where links
    * are placed, toolbar, sidemenus, pages. A template is displayed with options to
    * add, edit or delete the links.
    * @return The template to display.
    */
    function editLinks($module)
    {
        $data = $this->objDbMenu->getModuleLinks("module='$module'");
        $moduleList = $this->objModules->getAll('WHERE isVisible = 1 ORDER BY module_id');

        $this->setVarByRef('moduleList', $moduleList);
        $this->setVarByRef('moduleName', $module);
        $this->setVarByRef('data', $data);
        return 'editlinks_tpl.php';
    }

    /**
    * Method to restore the default link settings for the module.
    * The method deletes the existing links, reads the register.conf and creates the
    * original links.
    */
    function restoreDefaults()
    {
        $module = $this->getParam('modulename');

        // Delete existing links
        if(isset($_POST['ids']) && !empty($_POST['ids'])){
            $exLinks = explode(',', $_POST['ids']);
            foreach($exLinks as $id){
                $this->objDbMenu->delete('id', $id);
            }
        }

        // Restore defaults from register.conf
        $this->objRegister->restoreDefaults($module);
        return $this->nextAction('editlinks', array('modulename'=>$module));
    }

    /**
    * Method to get the default permissions for a module from the register.conf.
    */
    function restorePerms()
    {
        // Get defaults from register.conf
        $module = $this->getParam('modulename');
        $aclList = $this->objRegister->restoreDefaultPerms($module);

        $this->setVarByRef('moduleName', $module);
        $this->setVarByRef('defaultList', $aclList);
        $this->setVar('setDefault', TRUE);
        return 'editperms_tpl.php';
    }

    /**
    * Method to save the new toolbar links to the menu category table.
    */
    function saveToolLink()
    {
        $mod = $_POST['moduleName'];
        if($_POST['save'] == $this->objLanguage->languageText('word_back','security', 'Back')){
            return $this->nextAction('editlinks', array('modulename'=>$mod));
        }

        return $this->save($_POST['category']);
    }

    /**
    * Method to save the new side menu links to the menu category table.
    */
    function saveMenuLink()
    {
        if($_POST['save'] == $this->objLanguage->languageText('word_back','security', 'Back')){
            return $this->nextAction('editlinks', array('modulename'=>$_POST['moduleName']));
        }
        $category = 'menu_'.$_POST['menu'].'-'.$_POST['position'];
        $category .= '||'.$_POST['actionName'].'|'.$_POST['icon'];
        $category .= '|'.$_POST['code'];

        $str = explode('|', $_POST['permissions']);
        if(isset($_POST['site']) && !empty($_POST['site'])){
            $str = '|site|';
            $_POST['permissions'] = $str;
        }else{
            $str[1] = str_replace('site', '', $str[1]);
            $_POST['permissions'] = implode('|', $str);
        }
        return $this->save($category);
    }

    /**
    * Method to save the new page links.
    */
    function savePageLink()
    {
        if($_POST['save'] == $this->objLanguage->languageText('word_back','security','Back')){
            return $this->nextAction('editlinks', array('modulename'=>$_POST['moduleName']));
        }
        $category = 'page_'.$_POST['menu'].'_'.$_POST['position'];
        $category .= '|'.$_POST['actionName'].'|'.$_POST['icon'];
        $category .= '|'.$_POST['code'];

        $str = explode('|', $_POST['permissions']);
        if(isset($_POST['site']) && !empty($_POST['site'])){
            $str = '|site|';
            $_POST['permissions'] = $str;
        }else{
            $str[1] = str_replace('site', '', $str[1]);
            $_POST['permissions'] = implode('|', $str);
        }
        return $this->save($category);
    }

    /**
    * Method to save the editted links to the menu_category table.
    * @param string $category The formatted string for the toolbar category or the menu links.
    */
    function save($category)
    {
        $id = '';
        if(isset($_POST['id']) && !empty($_POST['id'])){
            $id = $_POST['id'];
        }
        $module = $_POST['moduleName'];

        $fields = array();
        $fields['category'] = $category;
        $fields['module'] = $module;

        if(isset($_POST['adminOnly']) && !empty($_POST['adminOnly'])){
            $fields['adminOnly'] = 1;
        }else{
            $fields['adminOnly'] = 0;
        }
        $fields['permissions'] = $_POST['permissions'];

        if(isset($_POST['dependsContext']) && !empty($_POST['dependsContext'])){
            $fields['dependsContext'] = 1;
        }else{
            $fields['dependsContext'] = 0;
        }
        $this->objDbMenu->saveLinks($fields, $id);
        return $this->nextAction('editlinks', array('modulename'=>$module));
    }
}
?>