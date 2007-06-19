<?php
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
    * @var $contextCode The current context code
    * @access private
    */
    private $contextCode = '';
    
    /**
    * Method to construct the class.
    **/
    function init()
    {
        $this->cssMenu =& $this->getObject('cssmenu');
        $this->flatMenu = $this->getObject('flatmenu');
        $this->dbmenu =& $this->getObject('dbmenu');
        $this->tools =& $this->getObject('tools');

        $this->objLanguage =& $this->getObject('language','language');
        $this->objSkin =& $this->newObject('skin','skin');
        $this->objUser =& $this->getObject('user','security');
        $this->objSysConfig =& $this->getObject('dbsysconfig','sysconfig');
        $this->objModule =& $this->getObject('modules', 'modulecatalogue');
        $this->objTable =& $this->newObject('htmltable','htmlelements');
        $this->objLayer =& $this->getObject('layer','htmlelements');

        $this->objContext =& $this->getObject('dbcontext','context');
        $this->objDbConMod =& $this->getObject('dbcontextmodules','context');
        $this->contextCode = $this->objContext->getContextCode();
        $this->context = FALSE; $this->im = FALSE;
        // First check if the user is in a context
        if(!empty($this->contextCode)){
            $this->context = TRUE;
        }
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
        $access = 2;
        
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
        $i=0; $menu=array(); $visModules = array();
        
        if($this->context){
            $visibleMod = $this->objDbConMod->getContextModules($this->contextCode);
            foreach($visibleMod as $vis){
                $visModules[] = $vis['moduleid'];
            }
        }
        
        foreach($data as $item){
            if($this->tools->checkPermissions($item, $this->context)){
                if(!empty($item['category'])){
                    /*switch($item['contextplugin']){
                        case '1':
                            // Check if module is visible in context
                            if($this->context){
                                //if($this->objDbConMod->isVisible($item['module'],$this->contextCode)){
                                if(!empty($visModules) && in_array(strtolower($item['module']), $visModules)){
                                    $menu[$item['category']][] = $item['module'];
                                }
                            }else{
                                $menu[$item['category']][]=$item['module'];
                            }
                            break;
                        
                        default:
                            $menu[$item['category']][]=$item['module'];
                    }
                    $i++;*/
                    $menu[$item['category']][]=$item['module'];
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
                $this->cssMenu->addMenuItem($this->objLanguage->languageText('category_'.$category, 'toolbar',ucwords($category)), ucwords($text),$v);
            }
        }
        return $this->cssMenu->show();
    }

    /**
    * Method to build up the menu from the list of categories and modules.
    * @param array $items Array of categories and modules.
    * @return string $menu The menu for display.
    */
    function buildFlatMenu($modules)
    {
        // build menu
        if(!empty($modules)){
            foreach($modules as $key=>$item){
                $category = strtolower($item['module']);
                $text = $this->objLanguage->code2Txt('mod_'.$category.'_name', $category);
                $this->flatMenu->addItem($category, $text);
            }
        }
        return $this->flatMenu->show();
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
            
            $toolbarType = $this->objSysConfig->getValue('TOOLBAR_TYPE', 'toolbar');
            switch(strtolower($toolbarType)){
                case 'flat':
                    return $this->createFlatToolbar();
                default:
                    return $this->createToolbar();
            }
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
    * Method to create a flat toolbar
    * 
    * @access private
    */
    private function createFlatToolbar()
    {
        $access = 2;
        
        if($this->objUser->isAdmin()){
            $access = 1;
        }
        
        $modules = $this->dbmenu->getFlatModules($access, $this->context);
        
        $menu = $this->buildFlatMenu($modules);
        
        $navbar = '<div id="menu">'.$menu.'</div>';
        return $navbar;
    }
    
    /**
    * Method to create the standard toolbar
    */
    function createToolbar()
    {
        $im = ''; $menu = FALSE; $iconList = '';

        // get slide out menus
        $modules = $this->menuBar();
        $menu = $this->buildMenu($modules);

        if(!$menu)
            $menu='';
        // get breadcrumbs
        $crumbs=$this->tools->navigation();

        $im = $this->tools->addIM();
        if($im){
            //$im .= '&nbsp;&nbsp;';
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
        $logout='';//<nobr>'.$iconList.$im.'&nbsp;'.'</nobr>';
		//return $menu;
        // Display data in a table
        $this->objTable->width="100%";
        $this->objTable->startRow();
        $this->objTable->addCell($menu, '90%', 'middle','left','menuhead');
        $this->objTable->addCell($im, '10%', 'middle','right','menuhead');
        $this->objTable->endRow();

        $this->objLayer->str = $crumbs;
        $this->objLayer->cssClass = 'menuhead';
        $this->objLayer->border = ";border-top: 1px solid #555555; padding: 5px; padding-left: 14px;";

        $navbar = '<div id="menu" style="float:left;">'.$menu.'</div><div style="float:right; padding-right: 10px;">'.$im.'</div><div id="breadcrumbs" style="clear:both">'.$crumbs.'</div>';
//        $navbar = '<div id="menu">'.$this->objTable->show().'</div><div id="breadcrumbs">'.$crumbs.'</div>';

        return $navbar;
    }
    
    /**
    * Method to get extra parameters
    *
    * @access public
    * @param array $headerParams The array of parameters added to the header
    * @param array $bodyOnload The array of parameters for body onload
    * @return
    */
    public function getParams($headerParams = array(), $bodyOnLoad = array())
    {
        // get from the tools class
        $params = $this->tools->params;
        
        if(!empty($params)){
            foreach($params as $key => $item){
                // append new parameter
                switch($key){
                    case 'headerParams':
                        if(!is_array($headerParams) || empty($headerParams)){
                            $headerParams = array();
                            $headerParams[] = $item;
                            break;
                        }
                        if (!in_array($item, $headerParams)){
                            $headerParams[] = $item;
                        }
                        break;
                        
                    case 'bodyOnLoad':
                        if(!is_array($bodyOnLoad) || empty($bodyOnLoad)){
                            $bodyOnLoad = array();
                            $bodyOnLoad[] = $item;
                            break;
                        }
                        if (!in_array($item, $bodyOnLoad)){
                            $bodyOnLoad[] = $item;
                        }
                        break;
                }
            }
        }
        return '';
    }

    /**
     * Create the Menu navigation
     *
     * @access public
     * @return string
     */
    public function navigationMenu()
    {
    	$str = '<ul id="nav">
				<li class="first"><a href="#">Home</a></li>
				<li class="active"><a href="#">User</a>
					<ul>
					<li class="first"><a href="#">Blog</a></li>
					<li class="active"><a href="#">Chat</a></li>
					<li><a href="#">Photo Gallery</a></li>
					<li><a href="#">Mailing List</a></li>
					<li><a href="#">Discussion Forum</a></li>

					<li class="last"><a href="#">Internal Email</a></li>
					</ul>
				</li>
				<li><a href="#">Resources</a>
					<ul>
					<li class="first"><a href="#">Discussion Forum</a></li>
					<li class="last"><a href="#">Wiki</a></li>
					</ul>
				</li>
				<li><a href="#">Admin</a>
					<ul>
					<li class="first"><a href="#">Maecenas</a></li>
					<li><a href="#">Phasellus</a></li>
					<li><a href="#">Mauris sollicitudin</a></li>
					<li><a href="#">Phasellus</a></li>
					<li><a href="#">Mauris sollicitudin</a></li>
					<li><a href="#">Phasellus</a></li>
					<li><a href="#">Mauris sollicitudin</a></li>
					<li><a href="#">Phasellus</a></li>
					<li><a href="#">Mauris sollicitudin</a></li>
					<li><a href="#">Phasellus</a></li>
					<li><a href="#">Mauris sollicitudin</a></li>
					<li class="last"><a href="#">Mauris at enim</a></li>
					</ul>
				</li>
				<li class="last"><a href="#">About</a>
					<ul>

					<li class="last"><a href="#">Credits</a></li>
					</ul>
				</li>
				</ul>';

    	return $str;

    }
}
?>
