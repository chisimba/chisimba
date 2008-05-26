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
        
        $this->menuItems['home'] = array('text'=>'Home', 'link'=>$this->uri(NULL, '_default'));
    }
    
    public function getParams()
    {
        
    }

    public function show()
    {
        $objBreadcrumbs = $this->getObject('breadcrumbs');
        
        if ($this->objUser->isLoggedIn()) {
            return $this->generateMenu().$objBreadcrumbs->show();
        } else {
            return $objBreadcrumbs->show();
        }
        
        
    }
    
    
    private function generateMenu()
    {
        $this->menuItems['personalspace'] = array('text'=>'My Home Space', 'link'=>$this->uri(NULL, 'personalspace'));
        
        
        if ($this->contextCode != '') {
            $this->menuItems['context'] = array('text'=>'Course Home', 'link'=>$this->uri(NULL, 'context'));
            //$this->menuItems['resources'] = array('text'=>'Resources', 'link'=>$this->uri(array('action'=>'resources'), 'context'));
            $this->menuItems['contextadmin'] = array('text'=>'My Courses', 'link'=>$this->uri(NULL, 'contextadmin'));
        } else {
            $this->menuItems['contextadmin'] = array('text'=>'My Courses', 'link'=>$this->uri(NULL, 'contextadmin'));
            //$this->menuItems['resources'] = array('text'=>'Resources', 'link'=>$this->uri(array('action'=>'resources'), 'toolbar'));
        }
        
        if ($this->objUser->isAdmin()) {
            $this->menuItems['admin'] = array('text'=>'Admin', 'link'=>$this->uri(NULL, 'toolbar'));
        }
        
        
        
        $this->determineDefault();
        return $this->generateOutput();
    }
    
    private function determineDefault()
    {
        switch ($this->getParam('module'))
        {
            case 'sitemap':
                $this->default = 'sitemap';
                break;
            case 'blog':
            case 'podcast':
            case 'personalspace':
                $this->default = 'personalspace';
                break;
            case 'contextadmin':
                $this->default = 'contextadmin';
                break;
            case 'context':
                $this->default = 'context';
                break;
            case 'contextcontent':
            case 'forum':
            case 'contextgroups':
                $this->default = 'context';
                break;
            case 'forum':
            case 'blog':
            case 'wiki':
                $this->default = 'resources';
                break;
            case 'toolbar':
                if ($this->getParam('action') == '') {
                    $this->default = 'admin';
                } else {
                    $this->default = 'resources';
                }
                break;
            case 'modulecatalogue':
            case 'sysconfig':
            case 'stories':
            case 'prelogin':
            case 'systext':
                $this->default = 'admin';
                break;
            default:
                break;
        }
    }
    
    private function generateOutput()
    {
        // Logout is always last
        $this->menuItems['sitemap'] = array('text'=>'Site Map', 'link'=>$this->uri(NULL, 'sitemap'));
        $this->menuItems['logout'] = array('text'=>'Logout', 'link'=>$this->uri(array('action'=>'logoff'), 'security'));
        
        $str = '<ul class="glossytabs">';
        
        foreach ($this->menuItems as $menuItem=>$menuInfo)
        {
            $link = new link ($menuInfo['link']);
            $link->link = '<strong>'.$menuInfo['text'].'</strong>';
            
            $css = ($this->default == $menuItem) ? ' class="current"' : '';
            $str .= '<li '.$css.'>'.$link->show().'</li>';
        }
        
        $str .= '</ul>';
        
        return $str;
    }
    
}
?>