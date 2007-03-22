<?
/**
* Class to handle interaction with table tbl_files_folders
* This table lists all folders that are created on the system
*
* @author Tohir Solomons
*/
class dbfolder extends dbTable
{
    
    
    /**
    * Constructor
    */
    public function init()
    {
        parent::init('tbl_files_folders');
        
        $this->objUser =& $this->getObject('user', 'security');
        $this->objConfig =& $this->getObject('altconfig', 'config');
        
		$this->loadClass('treemenu', 'tree');
		$this->loadClass('treenode', 'tree');
        $this->loadClass('htmllist', 'tree');
        $this->loadClass('htmldropdown', 'tree');
        $this->loadClass('dhtml', 'tree');
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
    }
    
    
    public function indexFolder($folder, $isFullPath=TRUE)
    {
        // Convert all backslashes to forward slashes
        // Convert multiple forward slashes to single
        $folder = preg_replace('/(\/|\\\)+/', '/', $folder);
        
        if ($isFullPath) {
            // Remove the path upto userfiles
            // Eg. removes /htdocs/chisima_framework/app/usrfiles/
            $folder = preg_replace('/\\A(.)*?usrfiles\//', '', $folder);
        }
        
        // Remove the Slash at the end if there is one
        $folder = preg_replace('/\/$/', '', $folder);
        
        if (!$this->valueExists('folderpath', $folder)) {
            return $this->addFolder($folder);
        } else {
            return $folder['id'];
        }
    }
    
    private function addFolder($folder)
    {
        return $this->insert(array('folderpath'=> $folder, 'folderlevel'=>count(explode('/', $folder))));
    }
    
    function showUserFolders($default='')
    {
        //Create a new tree
		$menu  = new treemenu();
        
        
        $icon         = 'folder.gif';
		$expandedIcon = 'folder-expanded.gif';
        
        $allFilesNode = new treenode(array('text' => 'My Files', 'link' => $this->uri(NULL), 'icon' => $icon, 'expandedIcon' => $expandedIcon));
        
        
        
        $refArray = array();

        $refArray['/users/1'] =& $allFilesNode;
        
        $folders = $this->getUserFolders($this->objUser->userId());
        
        if (count($folders) > 0) {
            foreach ($folders as $folder)
            {
                $folderText = basename($folder['folderpath']);
                
                if ($folder['id'] == $default) {
                    $folderText = '<strong>'.$folderText.'</strong>';
                    $cssClass = 'confirm';
                } else {
                    $cssClass = '';
                }
                
                $node =& new treenode(array('text' => $folderText, 'link' => $this->uri(array('action'=>'viewfolder', 'folder'=>$folder['id'])), 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass'=>$cssClass));
                
                $parent = '/'.dirname($folder['folderpath']);
                
                //echo $folder['folderpath'].' - '.$parent.'<br />';
                if (array_key_exists($parent, $refArray)) {
                    $refArray['/'.dirname($folder['folderpath'])]->addItem($node);
                }
                
                $refArray['/'.$folder['folderpath']] =& $node;
            }
        }
        
        $menu->addItem($allFilesNode);
        
        $this->appendArrayVar('headerParams', $this->getJavascriptFile('TreeMenu.js', 'tree'));
        $this->setVar('pageSuppressXML', TRUE);
        
        $objSkin =& $this->getObject('skin', 'skin');
        $treeMenu = &new dhtml($menu, array('images' => $objSkin->getSkinURL().'treeimages/imagesAlt2', 'defaultClass' => 'treeMenuDefault'));
        return $treeMenu->getMenu();
    }
    
    function getUserFolders($userId)
    {
        return $this->getAll(' WHERE folderpath LIKE \'users/'.$userId.'/%\' ORDER BY folderlevel, folderpath');
    }
    
    
    function getFolder($id)
    {
        return $this->getRow('id', $id);
    }
    
    function getFolderId($path)
    {
        $folder = $this->getRow('folderpath', $path);
        
        // print_r($folder);
        // echo '<br /><br /><br />';
        if ($folder == FALSE) {
            return FALSE;
        } else {
            return $folder['id'];
        }
    }
    
    public function getSubFolders($id)
    {
        $folder = $this->getFolder($id);
        
        if ($folder == FALSE) {
            return FALSE;
        } else {
            return $this->_getSubFolders($folder['folderpath'], $folder['folderlevel']);
        }
    }
    
    public function getSubFoldersFromPath($folderPath)
    {
        $folder = $this->getFolder($this->getFolderId($folderPath));
        if ($folder == FALSE) {
            return FALSE;
        } else {
            return $this->_getSubFolders($folder['folderpath'], $folder['folderlevel']);
        }
    }
    
    private function _getSubFolders($path, $level)
    {
        return $this->getAll(' WHERE folderpath LIKE \''.$path.'/%\' AND folderlevel = '.($level+1).' ORDER BY folderpath');
    }
    
    public function generateBreadcrumbsFromUserPath($userId, $path)
    {
        // users/1/archives/error_log/error_log
        $userPath = 'users/'.$userId;
        
        $regex = '/\\Ausers\/'.$userId.'\/';
        
        $remainderPath = preg_replace($regex.'/', '', $path);
        
        $homeLink = new link ($this->uri(NULL));
        $homeLink->link = 'My Files';
        
        $breadcrumbs = $homeLink->show();
        
        $items = explode('/', $remainderPath);
        
        $itemCount = count($items);
        
        if ($itemCount > 0) {
            $counter = 1;
            foreach ($items as $item)
            {
                $userPath .= '/'.$item;
                
                if ($counter == $itemCount) {
                    $breadcrumbs .= ' &gt; '.$item;
                } else {
                
                    $itemLink = new link ($this->uri(array('action'=>'viewfolder', 'folder'=>$this->getFolderId($userPath))));
                    $itemLink->link = $item;
                    
                    $breadcrumbs .= ' &gt; '.$itemLink->show();
                }
                
                
                $counter++;
            }
        }
        
        return $breadcrumbs;
    }
    
    function getTreedropdown($selected = '')
    {
        //Create a new tree
		$menu  = new treemenu();
        
        $allFilesNode = new treenode(array('text' => 'My Files', 'link' => 'ROOT'));
        
        $refArray = array();

        $refArray['/users/1'] =& $allFilesNode;
        
        $folders = $this->getUserFolders($this->objUser->userId());
        
        if (count($folders) > 0) {
            foreach ($folders as $folder)
            {
                $node =& new treenode(array('text' => basename($folder['folderpath']), 'link' => $folder['id']));
                
                $parent = '/'.dirname($folder['folderpath']);
                
                //echo $folder['folderpath'].' - '.$parent.'<br />';
                if (array_key_exists($parent, $refArray)) {
                    $refArray['/'.dirname($folder['folderpath'])]->addItem($node);
                }
                
                $refArray['/'.$folder['folderpath']] =& $node;
            }
        }
        
        $menu->addItem($allFilesNode);
        
        $this->appendArrayVar('headerParams', $this->getJavascriptFile('TreeMenu.js', 'tree'));
        $this->setVar('pageSuppressXML', TRUE);
        
        $objSkin =& $this->getObject('skin', 'skin');
        $treeMenu = &new htmldropdown($menu, array('inputName'=> 'parentfolder', 'id'=>'input_parentfolder','selected'=>$selected));
        return $treeMenu->getMenu();
    }
    
    function showCreateFolderForm($folderId)
    {
        $form = new form ('createfolder', $this->uri(array('action'=>'createfolder')));
        
        $label = new label ('Create a subfolder in: ', 'input_parentfolder');
        
        $form->addToForm($label->show().$this->getTreedropdown($folderId));
        
        
        // $objInputMasks = $this->getObject('inputmasks', 'htmlelements');
        // echo $objInputMasks->show();
 
        $textinput = new textinput('foldername');
        //$textinput->setCss('text input_mask anytext');
        
        $label = new label ('Name of Folder: ', 'input_foldername');
        
        $form->addToForm(' &nbsp; '.$label->show().$textinput->show());
        
        $button = new button ('create', 'Create Folder');
        $button->setToSubmit();
        
        $form->addToForm(' '.$button->show());
        
        return $form->show();
    }

    
    


}

?>