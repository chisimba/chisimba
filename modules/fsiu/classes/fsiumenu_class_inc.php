<?php

/**
* Class to Generate Custom Menu for the Sanord Skin
* @author Tohir Solomons
*/
class fsiumenu extends object
{
    /**
    * @var array $menuItems List of Menu Items/Modules to be built into the system
    */
    public $menuItems;
    
    /**
    * Constructor
    */
    public function init()
    {
    	    	
        // List of Menu Items
        $this->menuItems = array(
            /**array('title'=>'About Us', 'module'=>'cms', 'action' => 'showsection','id' => 'gen9Srv59Nme5_1514_1188560676', 'sectionid' => 'gen9Srv59Nme5_1514_1188560676'),
            array('title'=>'Members', 'module'=>'cms', 'action' => 'showsection','id' => 'gen9Srv59Nme5_9007_118967971', 'sectionid' => 'gen9Srv59Nme5_9007_1189679711'),
            array('title'=>'Press Releases', 'module'=>'cms', 'action' => 'showsection ','id' => 'init_1', 'sectionid' => 'init_1'),*/
	   array('title'=>'Leadership','module'=>'cms','action'=>'showcontent','id'=>'gen22Srv38Nme10_5177_1213881863','sectionid'=>'gen8Srv56Nme56_1234_1207871796'),
           array('title'=>'Researchers','module'=>'cms','action'=>'showsection','id'=>'gen22Srv38Nme10_5805_1214398614','sectionid'=>'gen22Srv38Nme10_5805_1214398614'),
	   array('title'=>'Students','module'=>'cms','action'=>'showsection','id'=>'gen8Srv56Nme56_9859_1207871891','sectionid'=>'gen8Srv56Nme56_9859_1207871891'),	
           array('title'=>'Join SANORD', 'module'=>'userregistration'),
	    array('title'=>'Links'),	
        );
        
        $this->objUser = $this->getObject('user', 'security');
        $this->objConfig = $this->getObject('altconfig', 'config');
    }
    
    /**
    * Method to build and display the menu
    */
    public function show()
    {
    	
          // Start Tags
        $str = '<div id="sanordmenu">';
        
        /** SHow link to login
          if (!$this->objUser->isLoggedIn()) {
            $objIcon = $this->newObject('geticon', 'htmlelements');
            $objIcon->setIcon('loginlink');
            $objIcon->alt = 'Login';
            $objIcon->title = 'Login';
            $objIcon->align = 'top';
            $this->loadClass('link','htmlelements');
            $link = new link ($this->uri(array('action'=>'error', 'message'=>'needlogin'), 'security'));
            $link->link = $objIcon->show();
            
        }*/
        
        $str .= '<ul>';
        
        // Add Home Link
        $str .= $this->prepareModuleItem(array('title'=>'Home', 'module'=>'_default'));
        
        // Build Items into menu
        foreach ($this->menuItems as $menuItem)
        {
            $str .= $this->prepareModuleItem($menuItem);
        }
        
        
        // Add Logout link if user is logged in
        if ($this->objUser->isLoggedIn()) {
            $link = $this->uri(array('action'=>'logoff'), 'security');
            $str .= $this->prepareItem('Logout', $link);
        }
        
        // End Tags
        $str .= '</ul></div>';
        
        return $str;
    }
    
    /**
    * Method to build a module item into a link
    * @param string $title Title of the Item
    * @param string $module Name of the Module
    */
    private function prepareModuleItem($item)
    {
        // Get Current Module from URL
        $currentModule = $this->getParam('module');
        $isCurrent = FALSE;
        
        $title = $item['title'];
        $module = $item['module'];
        
        unset($item['title']);
        unset($item['module']);
        
        // Check if Current Module
        if ($module == $currentModule) {
            $isCurrent = TRUE; 
        }
        
        // Check if Home Module
        if ($currentModule == '' && $module == '_default') {
            $isCurrent = TRUE;
        }
        
        if (!$this->objUser->isLoggedIn() && $currentModule == $this->objConfig->getPrelogin() && $module == '_default') {
            $isCurrent = TRUE;
        }
        
        if ($this->objUser->isLoggedIn() && $currentModule == $this->objConfig->getdefaultModuleName() && $module == '_default') {
            $isCurrent = TRUE;
        }
        
        if ($isCurrent && isset($item['id']) && $item['id'] != $this->getParam('id')) {
            $isCurrent = FALSE;
        }
        
        // Create Link
        $link = $this->uri($item, $module);
        
        // Build Menu Item
        return $this->prepareItem($title, $link, $isCurrent);
    }
    
    /**
    * Method to Build a Menu Item
    * @param string $title Title of the Item
    * @param string $link Link of the Item
    * @param boolean $isCurrent Flag to set item as current
    */ 
    private function prepareItem($title, $link, $isCurrent=FALSE)
    {
        $str = '<li';
        
        if ($isCurrent) {
        	$cssid = str_replace(' ', '_', $title);
             $str .= ' id="'.$cssid.'"';
        }
        
        $str .= '><a href="'.$link.'" title="'.$title.'">'.$title.'</a></li>';
        
        return $str;
    }
    
    
}

?>
