<?php
/**
* Class cssmenu extends object.
* @package toolbar
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Elearn Toolbar Prototype - This is still in the development phase
* @author Tohir Solomons <tsolomons@uwc.ac.za>
*/

class toolbar_elearn extends object
{
    
    
    private $menuItems;
    
    private $default = 'home';
    
    /**
    * Method to construct the class
    */
    function init()
    {
        
        $this->loadClass('link','htmlelements');
        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->objUser = $this->getObject('user', 'security');
        $this->contextCode = $this->objContext->getContextCode();
        
        
        $this->objLanguage = $this->getObject('language', 'language');
        
        $this->objModule = $this->getObject('modules','modulecatalogue');
        
        $this->menuItems['home'] = array('text'=>$this->objLanguage->languageText('word_home', 'system', 'Home'), 'link'=>$this->uri(NULL, '_default'), 'class'=>'homelink');
    }
    
    public function getParams()
    {
        
    }
    
    /**
     * Method to display the elearn toolbar
     */
    public function show()
    {
        $objBreadcrumbs = $this->getObject('breadcrumbs');
        
        // Take Decision on this
        // Should something be shown to not logged in users
        if ($this->objUser->isLoggedIn()) {
            return $this->generateMenu().'<div id="breadcrumbs">'.$objBreadcrumbs->show().'</div>';
        } else {
            return '<div id="breadcrumbs">'.$objBreadcrumbs->show().'</div>';
        }
        
        
    }
    
    /**
     * Method to generate the elearn toolbar
     */
    private function generateMenu()
    {
        $isRegistered = $this->objModule->checkIfRegistered('personalspace');
        
        if ($isRegistered) {
            $this->menuItems['personalspace'] = array('text'=>$this->objLanguage->languageText('mod_personalspace_myworkspace', 'personalspace', 'My Work Space'), 'link'=>$this->uri(NULL, 'personalspace'));
        }
        
        if ($this->contextCode != '') {
            $this->menuItems['context'] = array('text'=>ucwords($this->objLanguage->code2Txt('mod_context_contexthome', 'context', NULL, '[-context-] Home')), 'link'=>$this->uri(NULL, 'context'), 'title'=>$this->objContext->getTitle());
        }
        
        $this->menuItems['contextadmin'] = array('text'=>ucwords($this->objLanguage->code2Txt('phrase_mycourses', 'system', NULL, 'My [-contexts-]')), 'link'=>$this->uri(NULL, 'contextadmin'));
        
        if ($this->objUser->isAdmin()) {
            $this->menuItems['admin'] = array('text'=>$this->objLanguage->languageText('category_admin', 'toolbar', 'Admin'), 'link'=>$this->uri(NULL, 'toolbar'));
        }
        
        
        
        $this->determineDefault($this->getParam('module', '_default'));
        return $this->generateOutput();
    }
    
    function addContextLogoutJS()
    {
        $contextUri = str_replace('&amp;', '&', $this->uri(array('action'=>'leavecontext'), 'context'));
        $homeUri = $this->uri(NULL, '_default');
        return "
<script type=\"text/javascript\">
// <![CDATA[
// Change Link to javascript
jQuery('.homelink').attr('href', 'javascript:;');
// Bind a confirmation to link
jQuery('.homelink').bind('click', function() {
        
        if (confirm('Would you like to log out of this course?')) {
            window.location.href = '{$contextUri}';
        } else {
            window.location.href = '{$homeUri}';
        }
    });

// ]]>
</script>
            ";
    }
    
    private function determineDefault($module)
    {
        switch ($module)
        {
            case 'sitemap':
                $this->default = 'sitemap';
                return;
            case 'contextadmin':
                $this->default = 'contextadmin';
                return;
            case 'context':
                $this->default = 'context';
                return;
            
            case 'blog':
            case 'buddies':
            case 'homepage':
            case 'kbookmark':
            case 'internalmail':
            case 'podcast':
            case 'personalspace':
                $this->default = 'personalspace';
                return;
            
            case 'context':
                $this->default = 'context';
                return;
            case 'contextcontent':
            case 'contextgroups':
                $this->default = 'context';
                return;
            case 'toolbar':
                $this->default = 'admin';
                return;
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
            case '_default':
                $this->default = 'home';
                return;
            case 'calendar':
            case 'filemanager':
                if ($this->contextCode == '') {
                    $this->default = 'personalspace';
                } else {
                    $this->default = 'context';
                }
                return;
            default:
                break;
        }
        
        if ($this->objModule->dependsContext($module)) {
            $this->default = 'context';
            return;
        }
        
        if ($this->objModule->isContextAware($module) && $this->contextCode != '') {
            $this->default = 'context';
            return;
        }
        
        return;
    }
    
    private function generateOutput()
    {
        //See if the site map module is registered
        
        $isRegistered = $this->objModule->checkIfRegistered('sitemap');
        
        if ($isRegistered) {
            $this->menuItems['sitemap'] = array('text'=>$this->objLanguage->languageText('phrase_sitemap', 'sitemap', 'Site Map'), 'link'=>$this->uri(NULL, 'sitemap'));
        }
        
        // Logout is always last
        $this->menuItems['logout'] = array('text'=>$this->objLanguage->languageText('word_logout', 'system', 'Logout'), 'link'=>$this->uri(array('action'=>'logoff'), 'security'));
        
        $str = '<ul class="glossytabs">';
        
        foreach ($this->menuItems as $menuItem=>$menuInfo)
        {
            $link = new link ($menuInfo['link']);
            $link->link = '<strong>'.$menuInfo['text'].'</strong>';
            
            if (isset($menuInfo['class'])) {
                $link->cssClass = $menuInfo['class'];
            }
            
            $css = ($this->default == $menuItem) ? ' class="current"' : '';
            $str .= '<li '.$css.'>'.$link->show().'</li>';
        }
        
        $str .= '</ul>';
        
        if ($this->contextCode != '') {
            $str .= $this->addContextLogoutJS();
        }
        return $str;
    }
    
}
?>