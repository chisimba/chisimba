<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Tabs Menu
 *
 * Class to quickly generate a glossy tabs menu. Simply tell this class
 * which modules you want in the menu, and it will generate it for you.
 *
 * Also adds Admin if user is admin, and logout if user is logged in
 *
 * @author Tohir Solomons
 * @filesource
 * @copyright AVOIR
 * @package toolbar
 * @category chisimba
 * @access public
 * @example
 *      $menu = $this->getObject('tabsmenu','toolbar');
 *      $menu->modules = array('wiki', 'blog', 'faq');
 *      echo $menu->show();
 */
class tabsmenu extends object
{
    
    /**
     * @var array $modules List of Modules to be include in menu
     */
    public $modules;
    
    /**
     * @var array $menuItems List of Items that goes into menu
     * Even though the user provides a list, still check that:
     *  1) Module exists
     *  2) Is registered
     */
    private $menuItems;
    
    /**
     * @var string $default Default Tab to be highlighted
     */
    private $default = 'home';
    
    
    
    /**
    * Method to construct the class
    */
    function init()
    {
        // Load Classes Required
        $this->loadClass('link','htmlelements');
        $this->objUser = $this->getObject('user', 'security');
        
        $this->objLanguage = $this->getObject('language', 'language');
        
        $this->objModule = $this->getObject('modules','modulecatalogue');
        
        // Home will always be the first menu
        $this->menuItems['home'] = array('text'=>$this->objLanguage->languageText('word_home', 'system', 'Home'), 'link'=>$this->uri(NULL, '_default'), 'class'=>'homelink');
    }
    
    /**
     * Method to send any params the toolbar may need to send to the header
     * This toolbar doesn't need to, so it does nothing
     */
    public function getParams()
    {}
    
    /**
     * Method to display the menu / toolbar
     */
    public function show()
    {
        $objBreadcrumbs = $this->getObject('breadcrumbs');
        
        return $this->generateMenu().'<div id="breadcrumbs">'.$objBreadcrumbs->show().'</div>';
        
    }
    
    /**
     * Method to generate the elearn toolbar menu items
     */
    private function generateMenu()
    {
        // Loop through list, check if registered, and add
        foreach ($this->modules as $module)
        {
            $isRegistered = $this->objModule->checkIfRegistered($module);
            
            if ($isRegistered) {
                $this->menuItems[$module] = array('text'=>$this->objLanguage->languageText('mod_'.$module.'_name', $module), 'link'=>$this->uri(NULL, $module));
            }
        }
        
        // Add Admin Module
        if ($this->objUser->isAdmin()) {
            $this->menuItems['admin'] = array('text'=>$this->objLanguage->languageText('category_admin', 'toolbar', 'Admin'), 'link'=>$this->uri(NULL, 'toolbar'));
        }
        
        $this->determineDefault($this->getParam('module', '_default'));
        
        return $this->generateOutput();
    }
    
    
    /**
     * Method to determine which tab should be highlighted dependent on the current module being viewed
     * @param string $module Name of the Current Module
     */
    private function determineDefault($module)
    {
        // Check for one of given items
        if (array_key_exists($module, $this->menuItems)) {
            $this->default = $module;
            return;
        }
        
        // Cater for admin
        switch ($module)
        {
            case 'toolbar':
            case 'modulecatalogue':
            case 'sysconfig':
            case 'stories':
            case 'prelogin':
            case 'systext':
            case 'useradmin':
            case 'groupadmin':
            case 'permissions':
                $this->default = 'admin';
                return;
        }
    }
    
    /**
     * Method to generate the actual toolbar
     */
    private function generateOutput()
    {
        
        // Logout is always last
        if ($this->objUser->isLoggedIn()) {
            $this->menuItems['logout'] = array('text'=>$this->objLanguage->languageText('word_logout', 'system', 'Logout'), 'link'=>$this->uri(array('action'=>'logoff'), 'security'));
        }
        
        $str = '<ul class="glossytabs">';
        
        foreach ($this->menuItems as $menuItem=>$menuInfo)
        {
            $link = new link ($menuInfo['link']);
            $link->link = '<strong>'.$menuInfo['text'].'</strong>';
            
            if (isset($menuInfo['title'])) {
                $link->title = $menuInfo['title'];
            }
            
            if (isset($menuInfo['class'])) {
                $link->cssClass = $menuInfo['class'];
            }
            
            $css = ($this->default == $menuItem) ? ' class="current"' : '';
            $str .= '<li '.$css.'>'.$link->show().'</li>';
        }
        
        $str .= '</ul>';
        
        return $str;
    }
    
}
?>