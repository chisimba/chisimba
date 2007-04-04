<?php
/* -------------------- stories class extends controller ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Module class to handle the tree
 * 
 * @author Wesley Nitsckie
 * 
 */
 
 

class tree extends controller 
{
    /*
    Variables for creating the user, language object, etc
    */
    var $objSkin;
	var $objConfig;
    
    /**
     * Intialiser for the adminGroups object
     * 
     * @param byref $ string $engine the engine object
     */
    function init()
    {
        $this->objSkin = & $this->getObject('skin','skin');
        $this->objConfig= & $this->getObject('config','config');
		$this->loadClass('folderbot', 'files');
		$this->loadClass('layer','htmlelements');
		$this->loadClass('treemenu','tree');
		$this->loadClass('treenode','tree');		
		$this->loadClass('dhtml','tree');		
		$this->loadClass('listbox','tree');		
    } 
    /**
     * *The standard dispatch method for the module. The dispatch() method must 
     * return the name of a page body template which will render the module 
     * output (for more details see Modules and templating)
     */
    function dispatch($action)
    { 
        
        switch ($action) {
            case null:
                $this->showtree();
				$this->showDirTree();
				//$this->setVar('dirtree','');		
                return "treedemo_tpl.php";
                break;
            
            default:
                echo $objLanguage->languageText("phrase_unrecognizedaction");
                break;
        } 
    }
    
    /**
    * Method to prepare and set the vars for the output template
    */

    
    
	function showtree()
	{
	
	  	$icon         = 'folder.gif';
		$expandedIcon = 'folder-expanded.gif';
	
			
		//Create a new tree
		$menu  = new treemenu();
	
		//create some  nodes
		$node1   = new treenode(array('text' => "First level", 'link' => "test.php", 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'expanded' => true), array('onclick' => "alert('foo'); return false", 'onexpand' => "alert('Expanded')"));
		$node1_1 = &$node1->addItem(new treenode(array('text' => "Second level", 'link' => "test.php", 'icon' => $icon, 'expandedIcon' => $expandedIcon)));
		$node1_1_1 = &$node1_1->addItem(new treenode(array('text' => "Third level", 'link' => "test.php", 'icon' => $icon, 'expandedIcon' => $expandedIcon)));
		$node1_1_1_1 = &$node1_1_1->addItem(new treenode(array('text' => "Fourth level", 'link' => "test.php", 'icon' => $icon, 'expandedIcon' => $expandedIcon)));
		$node1_1_1_1->addItem(new treenode(array('text' => "Fifth level", 'link' => "test.php", 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => 'treeMenuBold')));
	
		$node1->addItem(new treenode(array('text' => "Second level, item 2", 'link' => "test.php", 'icon' => $icon, 'expandedIcon' => $expandedIcon)));
		$node1->addItem(new treenode(array('text' => "Second level, item 3", 'link' => "test.php", 'icon' => $icon, 'expandedIcon' => $expandedIcon)));
	
		//Add nodes to the tree
		$menu->addItem($node1);
		$menu->addItem($node1_1);
		//Add anynode from anywhere to the tree
		$menu->addItem($node1_1_1);		
		
		// Create the presentation class
		
		$treeMenu = &new dhtml($menu, array('images' => $this->objSkin->getSkinURL().'treeimages/imagesAlt2', 'defaultClass' => 'treeMenuDefault'));
		$listBox  = &new listbox($menu, array('linkTarget' => '_self','submitText' => 'Go Somewhere','promoText' => 'Choose Tree Item...'));
	  
		$this->setVar('treemenu',$treeMenu->getMenu());
		$this->setVar('listbox',$listBox->getMenu());

	}
	
	/**
	* Method to show a directory tree
	* ...still working here
	*/
	
	function showDirTree()
	{
		$this->icon         = 'folder.gif';
		$this->expandedIcon = 'folder-expanded.gif';
		$menu  = new treemenu();
		$contentlink='';
		$basenode = new treenode(array('text' => '\\...', 'link' => $contentlink, 'icon' => 'base.gif', 'expandedIcon' => 'base.gif'));
		//create other nodes and add them to the base node
		$nodes =& $basenode->addItem($this->_setDirNodes($basenode,$this->objConfig->siteRootPath())) ;
		//add base node to the menu
		$menu->addItem($basenode);
		
		$treeMenu = &new dhtml($menu, array('images' => $this->objSkin->getSkinURL().'/treeimages/imagesAlt2', 'defaultClass' => 'treeMenuDefault'));

		$this->setVar('dirtree',$treeMenu->getMenu());		
		
		//echo $this->recurseDir($this->objConfig->siteRootPath());
	
	}
	
	function _setDirNodes($node,$dirpath)
	{
		if ($dir = opendir($dirpath)) 
		{
			$newnode=& $node->addItem(new treenode(array('text' => basename($dirpath), 'link' => '', 'icon' => $this->icon , 'expandedIcon' => $this->expandedIcon)));
		
			 while (($file = readdir($dir)) !== false)
			 {		 
	  			if (!strcmp(".", $file))continue; // this may not the most efficient way to detect the . and .. entries
	            if (!strcmp("..", $file))continue;
				if (!strcmp("cvs", strtolower($file)))continue;
				
			
              	if (is_dir($file)) {                    	
                    $node=&$newnode->addItem($this->_setDirNodes($newnode,$file));
			
               	} else {
                   	$node=&$newnode->addItem(new treenode(array('text' => basename($file), 'link' => '', 'icon' => 'paper.gif' , 'expandedIcon' => 'paper.gif')));
               	}										
 	         } 				
		}
			
		
		return $newnode;

	}
	
	function _createFiles($file,$node)
	{
		return $node->addItem(new treenode(array('text' => dirname($file), 'link' => '', 'icon' => $this->icon , 'expandedIcon' => $this->expandedIcon)));	
		
	}
}
?>