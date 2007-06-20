<?php
/**
* Class flatmenu extends object.
* @package toolbar
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class for creating and displaying a flat style menu using css style sheets.
* @author Megan Watson
* @copyright (c)2007 University of the Western Cape
* @package toolbar
* @version 0.1
*/

class flatmenu extends object
{
    public $menu = array();

    /**
    * Method to construct the class
    */
    function init()
    {
        $this->objLanguage = $this->getObject('language','language');
        $this->objUser = $this->getObject('user','security');
        $this->objConfig = $this->getObject('altconfig','config');
        
        $this->loadClass('link','htmlelements');
    }

    /**
    * Method to build the menu in html for display purposes.
    * @param string $iconPath The path to the icons within the skins
    * icons folder. Default: false.
    * @return string $menu The menu
    */
    public function show()
    {
    	$homeLabel = $this->objLanguage->languageText('word_home');
    	$logoutLabel = $this->objLanguage->languageText('word_logout');
    	$confirmLabel = $this->objLanguage->languageText('phrase_confirmlogout');
        
        $postlogin = $this->objConfig->getdefaultModuleName();
        $home = $this->objConfig->getPrelogin();
        $showLogout = FALSE;
        
        // Check if the user is logged in
        if($this->objUser->isLoggedIn()){
            $home = $postlogin;
            $showLogout = TRUE;
        }
        
        $str = '<ul style="list-style: none; background-color:#fff;">';
        
        // Home link
        
        $objLink = new link($this->uri('', $home));
        $objLink->link = $homeLabel;
        $link = $objLink->show();
        
        $str .= '<li style="border-right: 1px #ead0cf dotted; background-image: none;">'.$link.'</li>';

        if(!empty($this->menu)){
            foreach($this->menu as $item){
                $actArr = !empty($item['action']) ? array('action' => $item['action']) : '';
                $objLink = new link($this->uri($actArr, $item['module']));
                $objLink->link = $item['text'];
                $link = $objLink->show();
                
                $str .= '<li style="border-right: 1px #ead0cf dotted; background-image: none;">'.$link.'</li>';
            }
        }

        if($showLogout){
            $url = $this->uri(array('action' => 'logoff'), 'security');
            $objLink = new link("javascript: if(confirm('{$confirmLabel}')) {document.location= '{$url}'};");
            $objLink->link = $logoutLabel;
            $link = $objLink->show();
            
            $str .= '<li style="background-image: none;">'.$link.'</li>';
        }

        $str .= '</ul><br />';
        
        return $str;
	}

    /**
    * Method to add a menu item.
    *
    * @access public
    * @param string $module The module name
    * @param string $text The link text
    * @param string $action The action to take
    * @return
    */
    public function addItem($module, $text, $action = '')
    {
        $this->menu[] = array('module' => $module, 'text' => $text, 'action' => $action);
    }
}
?>
