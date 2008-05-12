<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Controller class for the blog module that extends the base controller
 *
 * @author Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright AVOIR
 * @package sitemap
 * @category chisimba
 * @licence GPL
 */
class sitemap extends controller
{

    /**
    * @var array $modulesWithLinks An Array containing the list of modules that integrates with the sitemap framework
    * @access private
    */
    private $modulesWithLinks;
    
    /**
    * @var object $objLanguage Object to render language texts
    * @access public
    */
    public $objLanguage;
    
    /**
    * Constructor
    */
    public function init()
    {
        // Provisional List of Modules with module links classes
        // Note: This will in future comes from keywords
        $this->modulesWithLinks = array('forum', 'podcast', 'cmsadmin', 'announcements');
        
        $this->objLanguage =& $this->getObject('language', 'language');
    }
    
    /**
     * Method to process actions to be taken from the querystring
     *
     * @param string $action String indicating action to be taken
     * @return string template
     */
    public function dispatch($action)
    {
        // Load the Modules Object to Check whether modules is registered
        $objModules =& $this->getObject('modules', 'modulecatalogue');
        $objClassCheck =& $this->getObject('checkobject', 'utilities');
        
        // Sort Modules in Alphabetical Order
        asort($this->modulesWithLinks);
        
        // Create New Array
        $modules = array();
        
        // Loop through modules
        foreach ($this->modulesWithLinks as $module)
        {
            // If registered, add to list
            if ($objModules->checkIfRegistered($module) == TRUE && $objClassCheck->objectFileExists('modulelinks_'.$module, $module)) {
                $modules[] = $module;
            }
        }
        
        // Send list to Template
        $this->setVarByRef('modules', $modules);
        
        switch ($action)
        {
            case 'fcklink':
                return 'sitemap_fckeditor_tpl.php';
            case 'visual':
                return 'sitemap_visual_tpl.php';
            case 'visualmap':
                return 'sitemap_visualmap_tpl.php';
            case 'text':
                return 'sitemap_text_tpl.php';
            default:
                return 'sitemap_visual_tpl.php';
        }

    }
    
    /**
    * Method to generate a form and drop down allowing users to view the sitemap of one module at a time
    * @param array $modules List of Modules to populate in the drop down
    * @param string $selected Module to set as the default selected in the dropdown
    * @param string $action Current View mode - either 'visual' or 'text'
    * @return string Form allowing users to choose a sitemap to view
    */
    protected function generateDropdownNavigation($modules, $selected, $action='visual')
    {
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        
        $form = new form ('getsitemap', $this->uri(array('action'=>$action)));
        $form->method = 'get';
        
        $form->addToForm($this->showModeLinks($selected, $action).' &nbsp; - ');
        
        $formModule = new hiddeninput('module', 'sitemap');
        $form->addToForm($formModule->show());
        
        $formAction = new hiddeninput('action', $action);
        $form->addToForm($formAction->show());
        
        $dropdown = new dropdown ('showmodule');
        $dropdown->addOption('all', $this->objLanguage->languageText('mod_sitemap_showsitemapforallmodules', 'sitemap'));
        
        foreach ($modules as $module)
        {
            $dropdown->addOption($module, $this->objLanguage->languageText('mod_'.$module.'_name', $module));
        }
        
        $dropdown->setSelected($selected);
        
        $form->addToForm($dropdown->show());
        
        $button = new button ('', $this->objLanguage->languageText('word_go'));
        $button->setToSubmit();
        $form->addToForm($button->show());
        
        return $form->show();
    }
    
    /**
    * This method provides the links allowing the user to switch between Visual and Text Site Map Modules
    * @param string $showModule Current Module Sitemap being viewed
    * @param string $mode Current viewing mode - either 'visual' or 'text'
    * @return string Navigation links to switch between view modes
    */
    protected function showModeLinks($showModule, $mode)
    {
        $this->loadClass('link', 'htmlelements');
        
        $visualLink = new link ($this->uri(array('action'=>'visual', 'showmodule'=>$showModule)));
        $visualLink->link = $this->objLanguage->languageText('mod_sitemap_visualsitemap', 'sitemap');
        
        $textLink = new link ($this->uri(array('action'=>'text', 'showmodule'=>$showModule)));
        $textLink->link = $this->objLanguage->languageText('mod_sitemap_textsitemap', 'sitemap');
        
        if ($mode == 'visual') {
            $visual = $this->objLanguage->languageText('mod_sitemap_visualsitemap', 'sitemap');
            $text = $textLink->show();
        } else {
            $visual = $visualLink->show();
            $text = $this->objLanguage->languageText('mod_sitemap_textsitemap', 'sitemap');
        }
        
        return $visual.' / '.$text;
    }
}


?>