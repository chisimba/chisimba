<?
/**
* Class to show the File Manager Navigation as a tree
* @author Tohir Solomons
*/
class treefilter extends object
{

    
    /**
    * Constructor
    */
    public function init()
    {
        $this->objFile =& $this->getObject('dbfile');
        $this->objMediaFileInfo =& $this->getObject('dbmediafileinfo');
        $this->objUser =& $this->getObject('user', 'security');
		$this->loadClass('treemenu','tree');
		$this->loadClass('treenode','tree');
        $this->loadClass('htmllist','tree');
        $this->objLanguage =& $this->getObject('language', 'language');
        $this->loadClass('dropdown', 'htmlelements');
    }
    
    /**
    * Method to show the Navigation
    */
    public function show()
    {
        $icon         = 'folder.gif';
		$expandedIcon = 'folder-expanded.gif';
        
        $userId = $this->objUser->userId();
        $fullname = $this->objUser->fullname();
        
		//Create a new tree
		$menu  = new treemenu();
        
        $numFiles = $this->objFile->getNumUniqueFiles($userId);
		
		$allFilesNode = new treenode(array('text' => 'All Files ('.$numFiles.')', 'link' => $this->uri(NULL)));
        
        $categories = $this->objFile->getUserCategories($userId);
        
        foreach ($categories as $category)
        {
            $node =& new treenode(array('text' => ucfirst($category['category']), 'link' => $this->uri(array('category'=>$category['category']))));
            
            $allFilesNode->addItem($node);
        }
        
        $menu->addItem($allFilesNode);
        
        $uploadItem = new treenode(array('text' => 'Upload File', 'link' => $this->uri(array('action'=>'uploadfiles'))));
        
        $menu->addItem($uploadItem);
        
        $indexItem = new treenode(array('text' => 'File Indexer', 'link' => $this->uri(array('action'=>'indexfiles'))));
        
        $menu->addItem($indexItem);
        
        $treeMenu = &new htmllist($menu);
        
        //$this->appendArrayVar('headerParams', '<script src="modules/tree/resources/TreeMenu.js" language="JavaScript" type="text/javascript"></script>');
        
        // Check if there are any temporary files
        $objCheckOverwrite = $this->getObject('checkoverwrite');
        
        $title = '<h1>'.htmlentities($fullname."'s Files").'</h1>';
        
        return $title.'<p>'.$objCheckOverwrite->showLink().'</p>'.$treeMenu->getMenu();
    }
    
    function showDropDown()
    {
        $dropDown = new dropdown('asfas');
        $dropDown->addOption('asfsa', 'Fix Me - List of FIlters');
        return $dropDown->show();
    }

}

?>