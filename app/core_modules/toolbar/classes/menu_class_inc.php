<?
/**
* Class menu extends object.
* @package toolbar
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class for building the toolbar for KEWL.nextgen.
*
* The class builds a css style menu from the list of modules based on which modules
* the user has premission to access.
*
* @author Megan Watson
* @copyright (c)2004 UWC
* @package toolbar
* @version 0.9
*/

class menu extends object
{
    /**
    * Method to construct the class.
    **/
    function init()
    {
        $this->cssMenu =& $this->getObject('cssmenu');
        $this->dbmenu =& $this->getObject('dbmenu');
        $this->tools =& $this->getObject('tools');

        $this->objLanguage =& $this->getObject('language','language');
        $this->objSkin =& $this->newObject('skin','skin');
        $this->objUser =& $this->getObject('user','security');
        $this->objModule =& $this->getObject('modulesadmin', 'modulelist');
        $this->objTable =& $this->getObject('htmltable','htmlelements');
        $this->objLayer =& $this->getObject('layer','htmlelements');

        $this->objContext =& $this->getObject('dbcontext','context');
        $this->objDbConMod =& $this->getObject('dbcontextmodules','context');
        $this->context = FALSE; $this->im = FALSE;
    }

    /**
    * Method to get the list of modules for building the menu.
    * The method gets a list of modules from the database based on whether
    * they're context dependent or admin only. A function is called to check
    * which modules the user has permission to access. The menu is then built using
    * the cssmenu class.
    * @return string $menu The menu.
    */
    function menuBar()
    {
        $access = 2; $this->context = FALSE;
        // First check if the user is in a context
        if($this->objContext->isInContext()){
            $this->context = TRUE;
        }
        if($this->objUser->isAdmin()){
            $access = 1;
        }

        // get category and module data
        $rows = $this->dbmenu->getModules($access, $this->context);

        if(!empty($rows)){
            // Check user permissions and get a nested array of categories and modules
            $rows = $this->isVisible($rows);

            // put admin and about menus at the end
            $admin = array(); $about = array();
            foreach($rows as $key=>$item){
                if(strtolower($key) == 'about')
                    $about = array_shift($rows);
                if(strtolower($key) == 'admin')
                    $admin = array_shift($rows);
            }
            if(!empty($admin)){
                $rows['Admin'] = $admin;
            }
            if(!empty($about)){
                $rows['About'] = $about;
            }

            return $rows;
        }
        return FALSE;
    }

    /**
    * Method to check Module visibility and permissions.
    * The users permission to access each module is checked.
    * If in a context, the context aware modules are checked to determine
    * whether they are visible.
    * The method then creates an array of categories with a nested array of
    * the visible modules for each category.
    * @param array $data Array of all registered modules
    * @return array $menu Array of visible modules
    */
    function isVisible($data)
    {
        $i=0; $menu=array();
        foreach($data as $item){
            if($this->tools->checkPermissions($item, $this->context)){
                if(!empty($item['category'])){
                    switch($item['module']){
                        default:
                            /* Check if module is visible in context
                            if($this->context){
                                if($this->objDbConMod->isVisible($item['module'],$this->contextCode)){
                                    $menu[$item['category']][]=$item['module'];
                                }
                            }else{*/
                            $menu[$item['category']][]=$item['module'];
                    }
                    $i++;
                }
            }
        }
        return $menu;
    }

    /**
    * Method to build up the menu from the list of categories and modules.
    * @param array $items Array of categories and modules.
    * @return string $menu The menu for display.
    */
    function buildMenu($modules)
    {
        // build menu
        foreach($modules as $key=>$item){
            $category = strtolower($key);
            $this->cssMenu->addHeader($this->objLanguage->languageText('category_'.$category,'toolbar', ucwords($category)));
            foreach($item as $k=>$v){
                if(!(strpos(strtolower($v), 'context')===FALSE)){
                    $array = array('context'=>'course');
                    $text = $this->objLanguage->code2Txt('mod_'.$v.'_toolbarname',$v);
                }else{
                    $text = $this->objLanguage->code2Txt('mod_'.$v.'_name',$v);
                }
                $this->cssMenu->addMenuItem($this->objLanguage->languageText('category_'.$category, ucwords($category)), ucwords($text),$v);
            }
        }
        return $this->cssMenu->show();
    }

    /**
    * Method to display the toolbar.
    * @return string $navbar The toolbar.
    */
    function show()
    {
        if($this->tools->check()){
            // check session for a custom toolbar
            $toolBar = $this->getSession('toolbar', NULL);
            // if set call class toolbar from module obtained from the session variable
            if($toolBar){
                $objToolMod = $this->getObject('newtoolbar', $toolBar);
                return $objToolMod->createToolbar();
            }
            return $this->createToolbar();
        }
        return '';
    }

    /**
    * Method to set or unset a session variable.
    * The session variable contains the name of a module. The module then creates a method called
    * createToolbar() in a class called newtoolbar. The method creates a modified toolbar to replace
    * the standard toolbar.
    * @param string $module The module containing the newtoolbar class.
    * @param bool $set Determines whether to set or unset the session.
    */
    function setToolbar($module, $set = TRUE)
    {
        if($set){
           $this->setSession('toolbar', $module);
        }else{
            $this->unsetSession('toolbar');
        }
    }

    /**
    * Method to create the standard toolbar
    */
    function createToolbar()
    {
        $im = ''; $menu = FALSE; $iconList = '';

        // get slide out menus
        $modules = $this->menuBar();
        if(!empty($modules)){
            $menu = $this->buildMenu($modules);
        }
        if(!$menu)
            $menu='';
        // get breadcrumbs
        $crumbs=$this->tools->navigation();

        $im = $this->tools->addIM();
        if($im){
            $im .= '&nbsp;&nbsp;';
        }

        $helpBtn = $this->tools->getHelp();
        if($helpBtn){
            $iconList .= $helpBtn.'&nbsp;&nbsp;';
        }

        $pause = $this->tools->addPause();
        if($pause){
            $iconList .= $pause.'&nbsp;&nbsp;';
        }

        // get logout button
        $logout='<nobr>'.$iconList.$im.$this->objSkin->putLogout().'&nbsp;'.'</nobr>';

        // Display data in a table
        $this->objTable->width="100%";
        $this->objTable->startRow();
        $this->objTable->addCell($menu, '90%', 'center','left','menuhead');
        $this->objTable->addCell($logout, '10%', 'center','right','menuhead');
        $this->objTable->endRow();

        $this->objLayer->str = $crumbs;
        $this->objLayer->cssClass = 'menuhead';
        $this->objLayer->border = ";border-top: 1px solid #555555; padding: 5px; padding-left: 14px;";

        $navbar = $this->objTable->show().$this->objLayer->show();

        return $navbar;
    }
}
?>